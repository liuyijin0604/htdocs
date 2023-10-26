<?php
class WinitAPI
{
	private static $api_test = [
		'user' => 'rebecca',
		'password' => '888',
		'token' => '89435277FA3BA272DE795559998E-',
		'url' => 'http://openapi.sandbox.winit.com.cn/openapi/service',
	];
	
	private static $api_prod = [
		'user' => 'gero@toplogistics.com.au',
		'password' => 'pca168',
		'token' => 'CEFDC9D7981448181259790BD32B09B4',
		'url' => 'http://openapi.winit.com.cn/openapi/service',
	];
	
	private $account;
	public $result;
	public $err;
	public $is_test;

	public function __construct($test = false)
	{
		$this->is_test = $test;
		$this->account = $test? self::$api_test : self::$api_prod;
	}

	public function createShipment($p, $prodCode = 'OSF711002525', $pickupCode = 'AULMA001'){
		$addrs = str_split($p->cnee->address, 35);
		$parcelList = [];
		for($i = 1; $i <= $p->pkg; $i++){
			$parcel = $this->getVirtualWeightDim($p);
			$parcel['parcelNo'] = $i;
			$parcel['merchandiseList'] = [[
				// 'eBayTransactionId' => '12341',
				// 'eBayItemId' => '1234',
				'merchandiseName' => $p->eitems['g'][0],
				'merchandiseCode' => 'G-1',
				// 'specifications' => '1dfa',
				'quantity' => $p->eitems['q'][0],
				'salePrice' => $p->eitems['v'][0],
				'declaredValue' => $p->eitems['v'][0],
				'declaredCurrency' => 'USD',
				// 'declaredNameCn' => 'SHDF',
				'declaredNameEn' => $p->eitems['g'][0],
			]];
			$parcelList[] = $parcel;
		}
		$data = [
			'sellerOrderNo' => $p->hbn,
			'shipDate' => date('Y-m-d H:i:s'),
			'parcelList' => $parcelList,
			'productInfo' => [
				'winitProductCode' => $prodCode,
			],
			'receiver' => [
				'receiverName' => $p->cnee->name,
				'addressLine1' => $addrs[0], //max 35
				'addressLine2' => empty($addrs[1])? '' : $addrs[1], //optional max 35
				'city' => $p->cnee->suburb,
				'state' => $p->cnee->state,
				'postCode' => $p->cnee->postcode,
				'countryCode' => 'AU',
				'email' => $p->cnee->email,
				'phoneNo' => $p->cnee->tel,
			],
			'shipper' => [
				'pickupAddressCode' => $pickupCode,
			],
			'totalParcelsNumber' => $p->pkg,
			'totalParcelsWeight' => $p->weight,
		];
		return $this->request('lma.shipment.create', $data);
	}

	private function getVirtualWeightDim($p){
		$weight = round($p->weight / ($p->pkg > 0 ? $p->pkg : 1) * 70) / 100; // shrink to 70% firstly
		$averageCube = floor((Utility::croot3($weight / 250) * 100)); // divide by 250 to get virtual cube
		if ($averageCube <= 5) { // aupost can accept cube with minimum 5cm
			$dim = [5,5,5];
		} else {
			$adjustValue = rand(1, $averageCube - 5);
			$dim = [$averageCube - $adjustValue, $averageCube, $averageCube + $adjustValue];
		}
		shuffle($dim);
		list($width, $height, $length) = $dim;

		return [
			'weight' => sprintf('%.02f', $weight),
			'width' => sprintf('%.1f', $width),
			'height' => sprintf('%.1f', $height),
			'length' => sprintf('%.1f', $length),
		];
	}

	public function sign($d){
		ksort($d);
		$ss = $this->account['token'];
		foreach($d as $k=>$v){
			if(is_array($v)) $v = json_encode($v, JSON_UNESCAPED_UNICODE );
			$ss .= $k.$v;
		}
		$ss .= $this->account['token'];
		$d['sign'] = strtoupper(md5($ss));
		// $d['language'] = 'zh_CN';
		ksort($d);
		return $d;
	}

	public function ksortRecursive(&$array, $sort_flags = SORT_REGULAR) {
		if (!is_array($array)) return false;
		ksort($array, $sort_flags);
		foreach ($array as &$arr) {
			$this->ksortRecursive($arr, $sort_flags);
		}
		return true;
	}

	public function request($action, $data=null, $method='POST')
	{
		$this->ksortRecursive($data);
		$d = $this->sign([
			'action' => $action,
			'app_key' => $this->account['user'],
			'data' => $data,
			'format' => 'json',
			'platform' => 'ECPP',
			'sign_method' => 'md5',
			'timestamp' => date('Y-m-d H:i:s'),
			'version' => '1.0',
		]);

		$this->result=false;
		$ds = json_encode($d, JSON_UNESCAPED_UNICODE);
		$c=new curl($this->account['url']);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 600);
		$c->setopt(CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=UTF-8', 'Content-Length: ' . strlen($ds)]);
		$c->setopt(CURLOPT_POSTFIELDS, $ds);
		 
		if (!$c->exec()) throw new Exception('cUrl Error: '.$c->err);

		$this->result = json_decode($c->result);
		$this->log2file('Time: '.date('Y-m-d H:i:s')."\nAction: ".$action."\nRequest: ".$ds."\nResponse: ".$c->result);
		return $this->result;
	}
	 
	protected function log2file($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'winit' . DIRECTORY_SEPARATOR . 'winit_api_'. date('Y-m-d') . '.log';

		return file_put_contents($lf, $m.PHP_EOL, FILE_APPEND);
	}
}
