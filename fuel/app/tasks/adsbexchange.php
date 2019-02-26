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
use \Model\Flights;

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

class adsbexchange
{
    public static function run() {

    }

    public static function cleanup() {
        // ２４時間以上昔のデータは削除する。
        Flights::cleanup(strtotime('-2 day'));
    }

    public static function get() {
        $source = 'http://public-api.adsbexchange.com/VirtualRadar/AircraftList.json';
        $math = new \Geokit\Math();
        
        // 空港一覧の取得
        $airports = Flights::getAirports();

        $data = json_decode(file_get_contents($source));
        foreach($data->acList as $k => $v) {
            $aircraft = (array)$v;
            
            if ((isset($aircraft['From']) == true)
                && (isset($aircraft['To']) == true)
                && (isset($aircraft['Reg']) == true)
                && (isset($aircraft['Alt']) == true)
                && (isset($aircraft['Lat']) == true)
                && (isset($aircraft['Long']) == true)
                && (isset($aircraft['Spd']) == true)
                && (isset($aircraft['Mdl']) == true)
                && (isset($aircraft['OpIcao']) == true)) {

                try {
                    // 発着空港のICAOコードを取得する。
                    $tmp = explode(' ', $aircraft['From']);
                    $icaoFrom = array_shift($tmp);
                    $tmp = explode(' ', $aircraft['To']);
                    $icaoTo = array_shift($tmp);

                    // 現在の飛行機の位置
                    $position = new \Geokit\LatLng($aircraft['Lat'], $aircraft['Long']);
                    // 飛行機の行き先の位置
                    $origin = new \Geokit\LatLng($airports[$icaoFrom]['latitude'],
                                                 $airports[$icaoFrom]['longitude']);
                    // 飛行機の行き先の位置
                    $dest = new \Geokit\LatLng($airports[$icaoTo]['latitude'],
                                               $airports[$icaoTo]['longitude']);
                    // 残り距離を計算
                    $remaining = $math->distanceHaversine($position, $dest);
                    $flighted = $math->distanceHaversine($position, $origin);

                    // \Log::warning(print_r(array('carrier' => $aircraft['OpIcao'],
                    //                             'callsign' => $aircraft['Call'],
                    //                             'registration' => $aircraft['Reg'],
                    //                             'aircraft' => $aircraft['Mdl'],
                    //                             'squawk' => $aircraft['Sqk'],
                    //                             'altitude' => $aircraft['Alt'],
                    //                             'origin' => $aircraft['From'],
                    //                             'origin_icao' => $icaoFrom,
                    //                             'destination' => $aircraft['To'],
                    //                             'destination_icao' => $icaoTo,
                    //                             'togo' => $remaining->kilometers()), true));

                    Flights::regist(array('carrier' => $aircraft['OpIcao'],
                                          'callsign' => $aircraft['Call'],
                                          'registration' => $aircraft['Reg'],
                                          'aircraft' => $aircraft['Mdl'],
                                          'squawk' => $aircraft['Sqk'],
                                          'altitude' => $aircraft['Alt'],
                                          'origin' => $aircraft['From'],
                                          'origin_icao' => $icaoFrom,
                                          'destination' => $aircraft['To'],
                                          'destination_icao' => $icaoTo,
                                          'togo' => $remaining->kilometers(), // 目的地までの距離
                                          'length' => $flighted->kilometers(), // 出発地からの距離
                                          'created' => date('Y-m-d H:i:s')));
                } catch (\Exception $e) {
                    \Log::error(print_r($aircraft, true));
                    continue;
                }
            } else {
                Flights::registOther(array('data' => json_encode($aircraft),
                                           'created' => date('Y-m-d H:i:s')));
            }
        }
        // print_r($data);
    }

    // 空港マスター更新
    public static function makeAirportMaster() {
        $fl = new FlightRadar24();
        Flights::deleteAirport();

        $ports = $fl->getAirports(true);

        $airports = array();
        foreach($ports as $k => $a) {
            Flights::registAirport(array('name' => addslashes($a['name']),
                                         'iata' => $a['iata'],
                                         'icao' => $a['icao'],
                                         'latitude' => $a['lat'],
                                         'longitude' => $a['lon'], 
                                         'country' => $a['country'],
                                         'altitude' => $a['alt'],
                                         'created' => date('Y-m-d H:i:s')));
            
        }

        return $airports;
    }

    public static function testrun($dest) {
        $ret = Flights::getMilitary();
        print_r($ret);
    }
}
