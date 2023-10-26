<?php
class D2zShipAPI
{
	private static $test_url = 'http://18.220.140.225:8080/v1/d2z/api/';
	private static $test_username = 'PCAC';
	private static $test_auth = 'Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJjaHJpcyIsImlhdCI6MTU1Nzc2NDM1MX0.QxxhcEh-v_Zso32QKMD6kLth8HiVJLpfTSdE-VvVr96TKBqRFWvoPlldnBk1aRuFX4fAYmfUp85MHfPcFimVHg';

	private static $prod_url = 'https://www.d2z.com.au/v1/d2z/api/';
	private static $prod_username = 'PCAC';
	private static $prod_auth = 'Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJQQ0FDIiwiaWF0IjoxNTU4NDA4MzgyfQ.8O_ZbL1kWJEFiwl0LKEVk8dhRJjZOY7Nyzgtr5MADDBOyOZB51jNpAl510H1Ecm2lVJBhZNL-_QYBPmsOkbk4A';

	private static $services = [
		'syd' => '1PS4',
		'api-mel' => '1PM4',
		'bne' => '1PB4',
		'per' => '1PP4',
		'auto' => '4P4',
		'local' => '1PS5',
		'mel' => '1PM5',
		'local-mel' => '1PM5',
	];

	private static $ship = [
		'shipperName' => 'Test company',
		'shipperAddr1' => '1 fake st',
		'shipperCity' => 'Petaling Jaya',
		'shipperState' => 'Selangor',
		'shipperPostcode' => '47301',
		'shipperCountry' => 'MY',
	];

	private $username;
	private $auth;
	private $service;
	private $url;

	public function __construct($type = 'auto', $test = false)
	{
		if ($test) {
			$this->username = self::$test_username;
			$this->auth = self::$test_auth;
			$this->url = self::$test_url;
		} else {
			$this->username = self::$prod_username;
			$this->auth = self::$prod_auth;
			$this->url  = self::$prod_url;
		}

		$this->service = isset(self::$services[$type])? self::$services[$type] : false;
	}

	public function createConsignments($rs, $barcode=false)
	{
		$data = [
			'userName' => 'PCAC',
			'consignmentData' => [],
		];

		if(!is_array($rs)) $rs = [$rs];

		foreach($rs as $r){
			$addrs = $this->prepAddrLines($r->cnee->address);
			$sp = Postcode::autoSuburbByPostcode($r->cnee->suburb, $r->cnee->state, $r->cnee->postcode, 'AU');
			//fixing d2z suburb issues
			if($sp['postcode'] == '4385' && strtoupper($sp['suburb']) == 'TEXAS'){
				$r->cnee->state = 'NSW';
			}
			$shipdata = [
				'referenceNumber' => $r->hbn,
				'consigneeCompany' => $r->cnee->company,
				'consigneeName' => mb_substr(AppHelper::semiAngle($r->cnee->name), 0, 40),
				'consigneeAddr1' => $addrs[0],
				'consigneeAddr2' => empty($addrs[1])? '' : $addrs[1],
				'consigneeSuburb' => $sp['suburb'],
				'consigneeState' => $r->cnee->state,
				'consigneePostcode' => $sp['postcode'],
				'consigneePhone' => $r->cnee->tel,
				'productDescription' => @$r->eitems['g'][0],
				'value' => max(@$r->eitems['v'][0] * $r->eitems['q'][0], 1),
				'currency' => 'AUD',
				'weight' => round($r->weight * 100) / 100,
				'shippedQuantity' => $r->pkg,
				'dimensionsLength' => min(max(floatval(@$r->mdata['dim']['d']), 1), 40),
				'dimensionsHeight' => min(max(floatval(@$r->mdata['dim']['h']), 1), 40),
				'dimensionsWidth' => min(max(floatval(@$r->mdata['dim']['w']), 1), 40),
				'serviceType' => (empty($r->mdata['d2ztype']) || !isset(self::$services[$r->mdata['d2ztype']]))? $this->service : self::$services[$r->mdata['d2ztype']],
			];
			if($barcode){
				$aid = $r->ref.sprintf('%02s', 1).'00093'.'51'.'0';
				$aid .= AusPostAPI::aidChkDgt($aid);
				$shipdata['barcodeLabelNumber'] = '019931265009991891'.$aid;
				$shipdata['datamatrix'] = '[01]99312650099918[91]'.$aid.'[420]'.$r->postcode.'[8008]'.date('Ymd', strtotime($r->created)).'120000';
			}
			$data['consignmentData'][] =  array_merge($shipdata, self::$ship,
			);
		}

		return $this->_request('consignments-create', 'POST', json_encode($data));
	}

	protected function prepAddrLines($addr)
	{
		$addr = str_ireplace(['&#xd', '&#xa', '&#39', ' ', '–'], ["\n", "\n", "'", ' ', '-'], AppHelper::semiAngle($addr));
		$ls = preg_split('/[\n\r;]+/', trim($addr));
		if (sizeof($ls) == 1 && mb_strlen($addr) > 50) {
			$ls = AppHelper::mbstr_split($addr, 50);
		}
		$ls = array_slice($ls, 0, 2);
		$ls[0] = mb_substr($ls[0], 0, 50);
		if (!empty($ls[1])) {
			$ls[1] = mb_substr($ls[1], 0, 50);
		}

		return $ls;
	}

	public function allocate_shipment($shipment)
	{
		return $this->_request('consignments/' . $shipment->hbn . '/shipment/' . $shipment->ref, 'GET');
	}

	public function allocate_shipments($shipments, $no)
	{
		$hbns = [];
		foreach ($shipments as $shipment) {
			$hbns[] = $shipment->hbn;
		}
		return $this->_request('consignments/shipment/' . $no, 'PUT', implode(',', $hbns), ['Content-Type: text/plain']);
	}

	public function updateConsignments($shipments){
		//update weight only
		$data = [];
		foreach($shipments as $s){
			$data[] = ['referenceNumber' => $s->hbn, 'weight' => $s->weight];
		}
		return $this->_request('consignments', 'PUT', json_encode($data));
	}

	public function _request($action, $method='POST', $data = '', $hdrs = ['Content-type: application/json'])
	{
		$hdrs[] = 'Authorization: ' . $this->auth;
		$c = new curl($this->url . $action);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_HTTPHEADER, $hdrs);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 600);
		if (!empty($data)) {
			$c->setopt(CURLOPT_POSTFIELDS, $data);
		}
		$st = microtime(true);
		$ld = $method.': ' . $this->url . $action. "\nRequest Time: ".date('Y-m-d H:i:s').(empty($data)? '' : "\nData: " . $data);

		if (!$c->exec()) {
			$this->log2file($ld."\nError: " . $c->err."\nRequest Took: ".(microtime(true) - $st).'s');
			throw new Exception('cUrl Error: '.$c->err);
		}

		$this->log2file($ld."\nResponse: " . trim($c->result)."\nRequest Took: ".(microtime(true) - $st).'s');

		return empty($c->result)? false : json_decode($c->result, true);
	}

	protected function log2file($m)
	{
		$lf = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR  . 'd2z' . DIRECTORY_SEPARATOR . 'd2z_' . date('Y-m-d') . '.log';
		return file_put_contents($lf, date('Y-m-d H:i:s').' '.$m."\n", FILE_APPEND);
	}
}
