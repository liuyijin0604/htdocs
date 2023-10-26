<?php
class BaiduAPI {

	private static $ak = 'pRvf3sqiKFECFfwyNGGQrYXY';
	private static $sk = 'HoWqzRfmMp36htwdWH4svE0SXxPENY2d';

	public static function Geocoding($addr, $city = '', $output = 'json'){
		$host = "http://api.map.baidu.com";
		$uri = '/geocoder/v2/';

		$qsa = array (
			'address' => $addr,
			'city' => $city,
			'output' => $output,
			'ak' => self::$ak,
		);

		$qsa['sn'] = self::genAkSn($uri, $qsa);
		$target = $host.$uri.'?'.http_build_query($qsa);
 		return json_decode(file_get_contents($target));
	}

	private static function genAkSn($uri, $qsa, $method = 'GET'){  
		if ($method === 'POST')	ksort($qsa);
		$qs = http_build_query($qsa);
		return md5(urlencode($uri.'?'.$qs.self::$sk));
	}

//end of class
}