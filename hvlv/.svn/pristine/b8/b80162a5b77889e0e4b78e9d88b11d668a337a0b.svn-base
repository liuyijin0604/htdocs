<?php
class SendleAPI
{
	const API_ID_TEST = 'jerry_fang_pcaexpres';
	const API_KEY_TEST = 'wfWdRGmK8Gcjjf9wK6jMsCvW';
	const TEST_URL = 'https://sandbox.sendle.com/api/';

	const API_ID_LIVE = 'frank_pcaexpress_com';
	const API_KEY_LIVE = 'xgddNRZNV5DmTVWWSqQ8XfZP';
	const LIVE_URL = 'https://api.sendle.com/api/';

	private $api_id, $api_key, $url;

	public function __construct($test = false)
	{
		if ($test == true) {
			$this->api_id = self::API_ID_TEST;
			$this->api_key = self::API_KEY_TEST;
			$this->url = self::TEST_URL;
		} else {
			$this->api_id = self::API_ID_LIVE;
			$this->api_key = self::API_KEY_LIVE;
			$this->url = self::LIVE_URL;
		}
	}

	public function createInternationalOrder(&$shipment)
	{
		$action = 'orders';
		$data = [];
		// $data['pickup_date'] = '';
		$task = WmsTask::model()->findByPk(substr($shipment->cref, 1));
		$data['description'] = $shipment->cref . ' ' . $task->ref . ' ' . $task->job->customer->name;
		$data['kilogram_weight'] = $this->roundWeight($shipment->weight);
		$data['customer_reference'] = $shipment->cref;

		// sender
		$data['sender']['contact']['name'] = 'Eva Liu';

		$data['sender']['address']['address_line1'] = '6C The Crescent';
		$data['sender']['address']['suburb'] = 'Kingsgrove';
		$data['sender']['address']['postcode'] = '2208';
		$data['sender']['address']['state_name'] = 'NSW';
		$data['sender']['address']['country'] = 'AU';

		// receiver
		$data['receiver']['instructions'] = 'signature required on delivery';

		$data['receiver']['contact']['name'] = $shipment->cnee->name;
		$data['receiver']['contact']['phone'] = $shipment->cnee->tel;
		$data['receiver']['contact']['email'] = $shipment->cnee->email;

		$data['receiver']['address']['address_line1'] = $shipment->cnee->address;
		$data['receiver']['address']['suburb'] = $shipment->cnee->suburb;
		$data['receiver']['address']['postcode'] = $shipment->cnee->postcode;
		$data['receiver']['address']['state_name'] = $shipment->cnee->state;
		$data['receiver']['address']['country'] = $shipment->cnee->country;

		// content
		$data['contents']['description'] = $shipment->eitems['g'][0];
		$data['contents']['description'] = $data['contents']['description'];
		$data['contents']['value'] = $shipment->eitems['v'][0];
		$data['contents']['country_of_origin'] = $shipment->eitems['coo'][0];

		$data = json_encode($data);

		$data = preg_replace('/\\\\u[0-9|a-z|A-Z]{4}/', '', $data);

		$response = $this->_post($action, $data);
		$this->log2file('Request: ' . $data . "\n");
		$this->log2file('Response: ' . json_encode($response) . "\n");
		return $response;
	}

	public function roundWeight($wt){
		if($wt <= 0.25){
			return $wt;
		}else{
			$wt = floor($wt * 0.8 / 0.05) * 0.05 - (rand(0, 5) / 100);
		}
		$m = ($wt*100) % 25;
		if($m < 7){
			$wt -= $m / 100;
		}
		return $wt;
	}

	public function getLabel(&$shipment)
	{
		$action = 'orders/' . $shipment->trans[sizeof($shipment->trans)-1]->mdata['order_id'] . '/labels/cropped.pdf';
		$response = $this->_get($action);
		preg_match('/<a href="(.*)">redirected<\/a>/', $response, $matches);
		$url = str_replace('&amp;', '&', $matches[1]);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode($this->api_id . ':' . $this->api_key), 'Content-Type: application/json', 'Accept: application/json'));
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);

		$this->log2file('Request: label' . "\n");
		$this->log2file('Response: ' . $url . "\n");
		return $response;
	}

	public function getOrder($shipment)
	{
		$action = 'orders/' . $shipment->trans[sizeof($shipment->trans)-1]->mdata['order_id'];
		$response = $this->_get($action);
		$this->log2file('Request: order' . "\n");
		$this->log2file('Response: ' . $url . "\n");
		return $response;
	}

	public function _post($action, $data = [])
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url . $action);
		curl_setopt($curl, CURLOPT_POST, 1);

		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode($this->api_id . ':' . $this->api_key), 'Content-Type: application/json', 'Accept: application/json'));
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

	public function _get($action, $data = [])
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url . $action . $this->parse($data));

		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode($this->api_id . ':' . $this->api_key), 'Content-Type: application/json', 'Accept: application/json'));
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);

		if (!empty($response)) {
			if (is_array($response)) {
				return json_decode($response, true);
			} else {
				return $response;
			}
		} else {
			return false;
		}
	}

	public function parse($data)
	{
		$result = !empty($data) ? '?' : '';
		foreach ($data as $k => $v) {
			$result .= $k . '=' . $v . '&';
		}

		return $result;
	}

	protected function log2file($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'sendle' . DIRECTORY_SEPARATOR;
		$lf .= 'sendle_' . date('Y-m-d') . '.log';
		return file_put_contents($lf, $m, FILE_APPEND);
	}

}