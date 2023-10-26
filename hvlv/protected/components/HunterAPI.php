<?php
class HunterAPI
{

	private static $test_url = 'https://sandbox.hunterexpress.com.au/sandbox/rest/hxws/';
	private static $live_url = 'https://api.hunterexpress.com.au/live/rest/hxws/';

	const TEST_HUNTER_USERNAME = 'hxws';
	const TEST_HUNTER_PASSWORD = 'hxws';
	const TEST_HUNTER_ACCOUNT_CODE = 'DUMMY';

	const LIVE_HUNTER_USERNAME = 'PCAKIL';
	const LIVE_HUNTER_PASSWORD = 'pcaexp';
	const LIVE_HUNTER_ACCOUNT_CODE = 'PCAKIL';

	private $username, $password, $account_code, $url;

	public function __construct($test = true)
	{
		if ($test == true) {
			$this->username = HunterAPI::TEST_HUNTER_USERNAME;
			$this->password = HunterAPI::TEST_HUNTER_PASSWORD;
			$this->account_code = HunterAPI::TEST_HUNTER_ACCOUNT_CODE;
			$this->url = self::$test_url;
		} else {
			$this->username = HunterAPI::LIVE_HUNTER_USERNAME;
			$this->password = HunterAPI::LIVE_HUNTER_PASSWORD;
			$this->account_code = HunterAPI::LIVE_HUNTER_ACCOUNT_CODE;
			$this->url = self::$live_url;
		}
	}

	public function bookingJob(&$shipment)
	{
		$action = 'booking/book-job';
		$data = [];
		$data['customerCode'] = $this->account_code;
		$data['reference1'] = $shipment->hbn;
		$data['reference2'] = $shipment->ref;
		$data['primaryService'] = 'RF';
		$data['goods'] = array(
			array(
				'pieces' => $shipment->pkg,
				'weight' => round($shipment->weight * 100) / 100 / $shipment->pkg,
				'width' => floatval(@$shipment->mdata['dim']['w']),
				'height' => floatval(@$shipment->mdata['dim']['h']),
				'depth' => floatval(@$shipment->mdata['dim']['d']),
				'typeCode' => 'CTN',
			),
		);
		$dow = date('N');
		$hr = date('G');
		$pu_time = '09:00:00';
		if($dow < 5 || ($dow == 5 && $hr < 14)){// same day
			$plusdays = 0;
			$pu_time = '14:00:00';
		}elseif(($dow < 5 && $hr > 13) || $dow == 7){ //next day
			$plusdays = 1;
		}elseif($dow == 5){ //skip weekend
			$plusdays = 3;
		}elseif($dow == 6){ //skip sunday
			$plusdays = 2;
		}

		$data['stops'] = array(
			array(
				'name' => 'Top Logistics',
				'suburbName' => 'Kingsgrove',
				'addressLine1' => '6C The Crescent',
				'addressLine2' => '',
				'postCode' => '2208',
				'state' => 'NSW',
				'contact' => array(
					'name' => 'Michelle',
					'phone' => '02-99257111',
				),
				'timeWindow' => array(
					'earliest' => date('Y-m-d '.$pu_time, strtotime('+'.$plusdays.' day')),
					'latest' => date('Y-m-d 18:00:00', strtotime('+'.$plusdays.' day')),
				),
			),
			array(
				'name' => $shipment->cnee->name,
				'suburbName' => $shipment->cnee->suburb,
				'addressLine1' => $shipment->cnee->address,
				'addressLine2' => '',
				'postCode' => $shipment->cnee->postcode,
				'state' => $shipment->cnee->state,
				'contact' => array(
					'name' => $shipment->cnee->name,
					'phone' => $shipment->cnee->tel,
					'email' => $shipment->cnee->email,
				),
			),
		);
		$data = json_encode($data);

		$response = $this->_post($action, $data);
		$this->log2file('Request: ' . $data . "\n");
		$this->log2file('Response: ' . json_encode($response) . "\n");
		return $response;
	}

	public function tracking($trackingNumber)
	{
		$action = 'booking/get-job-statuses';
		$data = [];
		$data['customerCode'] = $this->account_code;
		$data['trackingNumber'] = $trackingNumber;

		$response = $this->_get($action, $data);
		$this->log2file('Request: ' . json_encode($data) . "\n");
		$this->log2file('Response: ' . json_encode($response) . "\n");
		return $response;
	}

	public function _post($action, $data)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url . $action);
		curl_setopt($curl, CURLOPT_POST, 1);

		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json', 'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password)));
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);

		if (!empty($response)) {
			return json_decode($response, true);
		} else {
			return false;
		}
	}

	public function _get($action, $data)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url . $action . $this->parse($data));

		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json', 'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password)));
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);

		if (!empty($response)) {
			return json_decode($response, true);
		} else {
			return false;
		}
	}

	public function parse($data)
	{
		$result = '?';
		foreach ($data as $k => $v) {
			$result .= $k . '=' . $v . '&';
		}

		return $result;
	}

	protected function log2file($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'hunter' . DIRECTORY_SEPARATOR;
		$lf .= 'hunter_' . date('Y-m-d') . '.log';
		return file_put_contents($lf, $m, FILE_APPEND);
	}

}