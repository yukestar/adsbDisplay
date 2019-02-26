<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Tasks;
use \Jawish\FlightRadar24\FlightRadar24;
use \Geokit\Math;
use \DB;

/**
 * Robot example task
 *
 * Ruthlessly stolen from the beareded Canadian sexy symbol:
 *
 *		Derek Allard: http://derekallard.com/
 *
 * @package		Fuel
 * @version		1.0
 * @author		Phil Sturgeon
 */

class Test
{
	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r robots
	 *
	 * or
	 *
	 * php oil r robots "Kill all Mice"
	 *
	 * @return string
	 */
	public static function run()
	{
        $fl = new FlightRadar24();

        // $airports = $fl->getAirports();

        // [0] => europe
    //         [1] => poland
    //         [2] => germany
    //         [3] => uk
    //         [4] => london
    //         [5] => ireland
    //         [6] => spain
    //         [7] => france
    //         [8] => ceur
    //         [9] => scandinavia
    //         [10] => italy
    //         [11] => northamerica
    //         [12] => na_n
    //         [13] => na_c
    //         [14] => na_cny
    //         [15] => na_cla
    //         [16] => na_cat
    //         [17] => na_cse
    //         [18] => na_nw
    //         [19] => na_ne
    //         [20] => na_sw
    //         [21] => na_se
    //         [22] => na_cc
    //         [23] => na_s
    //         [24] => southamerica
    //         [25] => oceania
    //         [26] => asia
    //         [27] => japan
    //         [28] => africa
    //         [29] => atlantic
    //         [30] => maldives
    //         [31] => northatlantic
        // print_r($zones);
        // print_r($fl->getLoadBalancers(true));
        $fl->selectZone('asia');
        $asia_aircrafts = $fl->getAircrafts(true);
        $fl->selectZone('japan');
        $japan_aircrafts = $fl->getAircrafts(true);
        $aircrafts = array_merge($japan_aircrafts, $asia_aircrafts);
        
        $ports = $fl->getAirports(true);
        $airports = array();
        foreach($ports as $k => $v) {
            $airports[$v['iata']] = $v;
        }
        // print_r($airports);exit;

        // HND 北緯35度33分12秒 東経139度46分52秒
        // NRT 北緯35度45分55秒 東経140度23分08秒
        $math = new \Geokit\Math();

        foreach($aircrafts as $a) {
            if (is_array($a)) {
                if ((strlen($a['destination']) > 0)
                    && (strlen($a['origin']) > 0)
                    && (isset($airports[$a['destination']]) == true)
                    && (isset($airports[$a['origin']]) == true)) {

                    // 現在地など設定
                    $position = new \Geokit\LatLng($a['latitude'], $a['longitude']);
                    $origin = new \Geokit\LatLng($airports[$a['origin']]['lat'],
                                               $airports[$a['origin']]['lon']);
                    $dest = new \Geokit\LatLng($airports[$a['destination']]['lat'],
                                               $airports[$a['destination']]['lon']);

                    $allDistance = $math->distanceHaversine($dest , $position);
                    $toGo = $math->distanceHaversine($origin, $dest);

                    
                    // 速度からETAを求める
                    if ($a['speed'] > 0) {
                        $eta = ($toGo->kilometers() / ($a['speed'] * 1.852)); // knots -> kilometers
                    } else {
                        $eta = 0;
                    }

                    \Log::warning(implode("\t", array($a['carrier'],
                                              $a['callsign'],
                                              $a['registration'],
                                              $a['type'],
                                              $a['swquawk'],
                                              $a['speed'],
                                              $a['altitude'],
                                              $a['origin'],
                                              $a['destination'],
                                              $allDistance->kilometers(),
                                                      $toGo->kilometers(),
                                              $eta,
                    )));
                    
                    // DBに記録
                    try {
                        \DB::insert('flightdata')->set(array('carrier' => $a['carrier'],
                                                             'callsign' => $a['callsign'],
                                                             'registration' => $a['registration'],
                                                             'aircraft' => $a['type'],
                                                             'squawk' => $a['swquawk'],
                                                             'speed' => $a['speed'],
                                                             'altitude' => $a['altitude'],
                                                             'origin_iana' => $a['origin'],
                                                             'origin' => $airports[$a['origin']]['name'],
                                                             'destination_iana' => $a['destination'],
                                                             'destination' => $airports[$a['destination']]['name'],
                                                             'length' => $allDistance->kilometers(),
                                                             'togo' => $toGo->kilometers(),
                                                             'eta' => $eta,
                                                             'created' => date('Y-m-d H:i:s')))->execute();
                    } catch (\Exception $e) {
                        \Log::error('failed to insert');
                        continue;
                    }
                    // exit;
                }
            }
        }

            
        
	}

	/**
	 * An example method that is here just to show the various uses of tasks.
	 *
	 * Usage (from command line):
	 *
	 * php oil r robots:protect
	 *
	 * @return string
	 */
	public static function protect()
	{
        $fl = new FlightRadar24();
        $fl->selectZone('asia');
        $aircrafts = $fl->getAircrafts(true);
        print_r(count($aircrafts));
        foreach($aircrafts as $a) {
            if ((strlen($a['destination']) > 0)
                && (strlen($a['origin']) > 0)) {
                print_r($a);
            }
        }

	}
}

/* End of file tasks/robots.php */
