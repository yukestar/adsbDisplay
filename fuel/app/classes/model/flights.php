<?php

namespace Model;

class Flights extends \Model {

    public static function allDest() {
        $allDest = array('Haneda' => 'RJTT',
                         'Narita' => 'RJAA',
                         'Osaka(Itami)' => 'RJOO',
                         'Osaka(Kansai)' => 'RJBB',
                         'Nagoya' => 'RJNA',
                         'Nagoya(Centrair)' => 'RJGG',
                         'New Chitose' => 'RJCC',
                         'Hiroshima' => 'RJOA',
                         'Fukuoka' => 'RJFF',
                         'Kitakyushu' => 'RJFR',
                         // 'Naha' => 'ROAH',
        ); // ICAO
        return $allDest;
    }

    // flightradar24版
    public static function getFlights($dest) {
        // $threshold = date('Y-m-d H:i:s', strtotime('-20 min'));
        // $data = \DB::query("SELECT * FROM flightdata WHERE (destination_iana = '".$dest."' OR origin_iana = '".$dest."') AND length < 1000 AND created > '".$threshold."' ORDER BY callsign, created ASC")
        // // $data = \DB::query("SELECT * FROM flightdata WHERE origin_iana = '".$dest."' AND length < 1000 AND created > '".$threshold."' ORDER BY callsign, created ASC")
        //       ->execute()->as_array();
        // $result = array();
        // foreach($data as $k => $v) {
            
        //     // if (($v[''] < 3) && ($dest == $v['origin_iana'])) {continue;}
        //     // if (($v['length'] > 1000) && ($dest == $v['origin_iana'])) {continue;}
        //     if (isset($result[$v['callsign']]) == true) {
        //         // update
        //         $result[$v['callsign']] = $v;
        //     } else {
        //         // new flight
        //         $result[$v['callsign']] = $v;
        //     }
        // }
        // // altitudeを最初のほうに持ってくる
        // $res = usort($result, function($a, $b) {
        //     if ($a['length'] == $b['length']) { return true; }
        //     if ($a['length'] > $b['length']) { return true; }
        //     if ($a['length'] < $b['length']) { return false; }
        // });

        // return $result;
    }

    // adsbexchange版
    public static function getFlightsFromAdsb($dest) {
        $threshold = date('Y-m-d H:i:s', strtotime('-20 min'));
        // $data = \DB::query("SELECT * FROM adsb WHERE (destination_icao = '".$dest."' OR origin_icao = '".$dest."') AND created > '".$threshold."' ORDER BY callsign, created ASC")
        $data = \DB::query("SELECT * FROM adsb WHERE destination_icao = '".$dest."' AND created > '".$threshold."' ORDER BY callsign, created ASC")
              ->execute()->as_array();
        $result = array();
        foreach($data as $k => $v) {
            if ($dest == $v['origin_icao']) { $v['flightLength'] = $v['length']; } // 出発地を指定した場合は、すでに飛んだ距離を使う
            if ($dest == $v['destination_icao']) { $v['flightLength'] = $v['togo']; } // 到着地を指定した場合は、到着地までの距離を使う

            if (($v['togo'] < 3) && ($dest == $v['origin_icao'])) {continue;}
            $v['length'] = $v['flightLength'];
            if ($v['length'] > 1000) {continue;}
            // if (($v['length'] > 1000) && ($dest == $v['origin_icao'])) {continue;}

            $tmp = trim(str_replace($v['origin_icao'], '', $v['origin']));
            $no = strpos($tmp, ',');
            $v['origin_name'] = substr($tmp, 0, $no);
            $tmp = trim(str_replace($v['destination_icao'], '', $v['destination']));
            $no = strpos($tmp, ',');
            $v['destination_name'] = substr($tmp, 0, $no);

            if (isset($result[$v['callsign']]) == true) {
                // update
                $result[$v['callsign']] = $v;
            } else {
                // new flight
                $result[$v['callsign']] = $v;
            }
            
        }
        
        // altitudeを最初のほうに持ってくる
        $res = usort($result, function($a, $b) {
            if ($a['flightLength'] == $b['flightLength']) { return true; }
            if ($a['flightLength'] > $b['flightLength']) { return true; }
            if ($a['flightLength'] < $b['flightLength']) { return false; }
        });

        $misc = static::getPlanesByLatLong($dest);

        return array('result' => $result, 'misc' => $misc);
    }

    public static function getMilitary($flg = true) {
        $data = static::getMiscAc();

        
        $result = array();
        
        foreach($data as $k => $aircraft) {
            $vehicle = (array)json_decode($aircraft['data']);

            if ($flg == true) {
                if ($vehicle['Mil'] == 1) {
                    $result[$k] = static::code($vehicle);
                }
            } else {
                $result[$k] = static::code($vehicle);
            }
            
        }

        return $result;
    }

    public static function getMiscAc() {
        $time = intval(time()/300);
        $base = date('Y-m-d H:i', ($time * 300));
        $time = \DB::query('SELECT max(created) as base FROM miscadsb LIMIT 1')
              ->execute()->current();
        // print_r($time);
        $from = date('Y-m-d h:i:s', strtotime($time['base']) - 30);
        $to = date('Y-m-d h:i:s', strtotime($time['base']) + 30);
        
        // $data = \DB::query('SELECT * FROM miscadsb WHERE data LIKE \'%Mil":true%\' AND created LIKE "'.$base.'%" ORDER BY data')
        //       ->execute()->as_array();
        $data = \DB::query('SELECT * FROM miscadsb WHERE created BETWEEN "'.$from.'" AND "'.$to.'" ORDER BY data ASC')
              ->execute()->as_array();
        return $data;
    }

    public static function getPlanesByLatLong($airport) {

        // 空港の緯度経度による検索範囲
        $coordinates = array('RJTT' => array('lat' => array('from' => 33,
                                                            'to' => 38),
                                             'long' => array('from' => 137,
                                                             'to' => 142)
        ),
                             'RJAA' => array('lat' => array('from' => 33,
                                                            'to' => 38),
                                             'long' => array('from' => 138,
                                                             'to' => 143)
                             ),
                             'RJOO' => array('lat' => array('from' => 32,
                                                            'to' => 36),
                                             'long' => array('from' => 133,
                                                             'to' => 138)
                             ),
                             'RJBB' => array('lat' => array('from' => 32,
                                                            'to' => 36),
                                             'long' => array('from' => 133,
                                                             'to' => 138)
                             ),
                             'RJNA' => array('lat' => array('from' => 32,
                                                            'to' => 36),
                                             'long' => array('from' => 134,
                                                             'to' => 138)
                             ),
                             'RJGG' => array('lat' => array('from' => 32,
                                                            'to' => 36),
                                             'long' => array('from' => 134,
                                                             'to' => 138)
                             ),
                             'RJCC' => array('lat' => array('from' => 40,
                                                            'to' => 44),
                                             'long' => array('from' => 139,
                                                             'to' => 143)
                             ),
                             'RJOA' => array('lat' => array('from' => 32,
                                                            'to' => 36),
                                             'long' => array('from' => 134,
                                                             'to' => 138)
                             ),
                             'RJFF' => array('lat' => array('from' => 32,
                                                            'to' => 36),
                                             'long' => array('from' => 130,
                                                             'to' => 134)
                             ),
                             'RJFR' => array('lat' => array('from' => 31,
                                                            'to' => 35),
                                             'long' => array('from' => 129,
                                                             'to' => 133)
                             ),
                             'ROAH' => array('lat' => array('from' => 24,
                                                            'to' => 28),
                                             'long' => array('from' => 125,
                                                             'to' => 129)
                             ),
        );

        // その他扱いの飛行機を取得する
        $miscAc = static::getMiscAc();

        // 指定された空港により、検索する範囲を設定する。
        $lat = $coordinates[$airport]['lat'];
        $long = $coordinates[$airport]['long'];

        $result = array();
        foreach($miscAc as $plane) {
            $air = static::code((array)json_decode($plane['data']));

            if (($air['latitude'] == null)
                && ($air['longitude'] == null)) { continue; }
            
            if (($air['latitude'] > $lat['from'])
                && ($air['latitude'] < $lat['to'])
                && ($air['longitude'] > $long['from'])
                && ($air['longitude'] < $long['to'])) {
                // print_r($air);
                $result[] = $air;
            }
        }
        return $result;
    }

    static function code($in) {
        $out = array('aircraft' => null,
                     'maker' => null,
                     'country' => null,
                     'altitude' => null,
                     'latitude' => null,
                     'longitude' => null,
                     'speed' => null,
                     'operator' => null,
                     'registration' => null,
                     'from' => null,
                     'to' => null,
        );
        if (isset($in['Mdl'])) { $out['aircraft'] = $in['Mdl']; }
        else {$out['aircraft'] = null; }
        if (isset($in['Man'])) { $out['maker'] = $in['Man']; }
        else {$out['maker'] = null; }
        if (isset($in['Cou'])) { $out['country'] = $in['Cou']; }
        else {$out['country'] = null; }
        if (isset($in['Alt'])) { $out['altitude'] = $in['Alt']; }
        else {$out['altitude'] = null; }
        if (isset($in['Lat'])) { $out['latitude'] = $in['Lat']; }
        else {$out['latitude'] = null; }
        if (isset($in['Long'])) { $out['longitude'] = $in['Long']; }
        else {$out['longitude'] = null; }
        if (isset($in['Spd'])) { $out['speed'] = $in['Spd']; }
        else {$out['speed'] = null; }
        if (isset($in['Op'])) { $out['operator'] = $in['Op']; }
        else {$out['operator'] = null; }
        if (isset($in['Reg'])) { $out['registration'] = $in['Reg']; }
        else {$out['registration'] = null; }
        if (isset($in['From'])) { $out['from'] = $in['From']; }
        else {$out['from'] = null; }
        if (isset($in['To'])) { $out['to'] = $in['To']; }
        else {$out['to'] = null; }
        if (isset($in['Callsign'])) { $out['callsign'] = $in['Callsign']; }
        else {$out['callsign'] = null; }


        foreach(array_keys($in) as $k => $v) {
            $out[$v] = $in[$v];
        }

        return $out;
    }

    // 空港マスター取得
    public static function getAirports($country = 'Japan') {
        $res = \DB::query('SELECT * FROM airports')->execute()->as_array();
        // print_r($res);

        $return = array();
        foreach($res as $k => $v) {
            $return[$v['icao']] = $v;
        }

        return $return;
    }

    // 航空機登録
    public static function regist($data) {
        \DB::insert('adsb')->set($data)->execute();
        return true;
    }

    // その他登録
    public static function registOther($data) {
        \DB::insert('miscadsb')->set($data)->execute();
        return true;
    }

    public static function icao2iana($icao) {
        $data = \DB::query('SELECT * FROM airports WHERE icao = "'.$icao.'"')->execute()->as_array();
    }

    public static function cleanup($day) {
        \DB::query('DELETE FROM adsb WHERE created < "'.date('Y-m-d', $day).' 00:00:00"')->execute();
        \DB::query('DELETE FROM miscadsb WHERE created < "'.date('Y-m-d', $day).' 23:45:00"')->execute();
    }

    public static function deleteAirport() {
        return \DB::query('TRUNCATE airports')->execute();
    }
    
    public static function registAirport($data) {
        \DB::insert('airports')->set($data)->execute();
        return true;
    }
}