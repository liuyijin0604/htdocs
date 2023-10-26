<?php

// 地址和邮编列表
$address = '151 Milner Rd, High Wycombe WA 6057';
$postcodes = [
    "6060","6061","6068","6069","6102","6103","6104","6109","6123","6124","6125","6126","6148","6160","6161","6165","6167","6168","6169","6170","6171","6172","6173","6174","6176"];
// 初始化距离和区域数组
$distances = array();
$zones = array('0-15km' => array(), '15-30km' => array(), '30-60km' => array(), '60-90km' => array(), '>90km' => array());

// 获取地址的经纬度
$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address)."&key=AIzaSyCEyTNyyfwHnkGYb9P1xtA68HdJ5jxcdbk";
$resp_json = file_get_contents($url);
$resp = json_decode($resp_json, true);

if($resp['status']=='OK'){

    // 解析地址的经纬度
    $lat = $resp['results'][0]['geometry']['location']['lat'];
    $lng = $resp['results'][0]['geometry']['location']['lng'];

    // 循环邮编列表，计算距离并分配到相应区域
    foreach($postcodes as $postcode){

        // 获取邮编的经纬度
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($postcode)."&key=AIzaSyCEyTNyyfwHnkGYb9P1xtA68HdJ5jxcdbk";
        $resp_json = file_get_contents($url);
        $resp = json_decode($resp_json, true);

        if($resp['status']=='OK'){

            // 解析邮编的经纬度
            $postcode_lat = $resp['results'][0]['geometry']['location']['lat'];
            $postcode_lng = $resp['results'][0]['geometry']['location']['lng'];

            // 计算距离
            $theta = $lng - $postcode_lng;
            $dist = sin(deg2rad($lat)) * sin(deg2rad($postcode_lat)) +  cos(deg2rad($lat)) * cos(deg2rad($postcode_lat)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $km = $miles * 1.609344;

            // 分配到相应区域
            if($km <= 15){
                if(!in_array($postcode, $zones['0-15km'])){
                    $zones['0-15km'][] = $postcode;
                }
            } elseif($km <= 30) {
                if(!in_array($postcode, $zones['15-30km'])){
                    $zones['15-30km'][] = $postcode;
                }
            } elseif($km <= 60) {
                if(!in_array($postcode, $zones['30-60km'])){
                    $zones['30-60km'][] = $postcode;
                }
            } elseif($km <= 90) {
                 if(!in_array($postcode, $zones['60-90km'])){
                    $zones['60-90km'][] = $postcode;
                }
            } else{
                 if(!in_array($postcode, $zones['>90km'])){
                    $zones['>90km'][] = $postcode;
                }
            }

            // 记录距离
            $distances[$postcode] = $km;
        }
    }

    print_r($zones['0-15km']);
    print_r($zones['15-30km']);
    print_r($zones['30-60km']);
    print_r($zones['60-90km']);
    print_r($zones['>90km']);
}