<?php

class Cin7API
{

	public $domain = 'https://api.cin7.com/api/';
	public $user, $key, $org_id;

	public function __construct($config = array())
	{
		if (!empty($config['user'])) {
			$this->user = $config['user'];
		}

		if (!empty($config['key'])) {
			$this->key = $config['key'];
		}

		if (!empty($config['org_id'])) {
			$this->org_id = $config['org_id'];
		}
	}

	public function getProducts()
	{
		$endpoint = 'v1/Products';

		$params = array(
			'Status' => 'Public',
		);

		return $this->httpGet($endpoint, $params);
	}

	public function getContacts()
	{
		$endpoint = 'v1/Contacts';

		$params = array(
			'Type' => 'Customer',
		);

		return $this->httpGet($endpoint, $params);
	}

	public function getContact($id)
	{
		$endpoint = 'v1/Contacts';

		$params = array(
			'id' => $id,
		);

		return $this->httpGet($endpoint, $params);
	}

	public function getProductOptions($id = false)
	{
		$endpoint = 'v1/ProductOptions';

		if ($id) {
			$endpoint .= '/' . $id;
			$params = [];
		} else {
			$params = array(
				'Status' => 'Public',
			);
		}

		return $this->httpGet($endpoint, $params);
	}

	public function getSaleOrder($id)
	{
		$endpoint = 'v1/SalesOrders';

		$params = array(
			'id' => $id,
		);

		return $this->httpGet($endpoint, $params);
	}

	public function getSalesOrders()
	{
		$endpoint = 'v1/SalesOrders';

		$params = array(
			'Stage' => 'Processing',
			'BranchId' => 8570,
		);

		return $this->httpGet($endpoint, $params);
	}

	public function getPurchaseOrders()
	{
		$endpoint = 'v1/PurchaseOrders';

		$params = array(
			'Stage' => 'New',
			'BranchId' => 8570,
		);

		return $this->httpGet($endpoint, $params);
	}

	public function completeOrder($order_id, $courier, $tracking_nos)
	{
		$endpoint = 'v1/SalesOrders';

		// $order = $this->getOrder($order_id);

		// $params = $order['items'];

		// $params[0]['trackingCode'] = $tracking_nos;

		// $order = self::getSaleOrder($order_id);
		// if (!empty($order['items'][0])) {
		// 	$order = $order['items'][0];
		// } else {
		// 	return false;
		// }

		// $lineItems = [];
		// foreach ($order['lineItems'] as $item) {
		// 	$item['qtyShipped'] = $item['qty'];
		// 	$lineItems[] = $item;
		// }

		$params = array(
			array(
				'id' => $order_id,
				'logisticsCarrier' => $courier,
				'logisticsStatus' => 9,
				'internalComments' => 'Picked by 3PL',
				'stage' => 'Dispatched',
				'dispatchedDate' => date('Y-m-d') . 'T' . date('H:i:s') . 'Z',
				'trackingCode' => implode(',', $tracking_nos),
			)
		);

		return $this->httpPut($endpoint, $params);
	}

	public function errorOrder($order_id)
	{
		$endpoint = 'v1/SalesOrders';

		$params = array(
			array(
				'id' => $order_id,
				'logisticsCarrier' => '',
				'logisticsStatus' => 2,
				'internalComments' => 'Transfer to 3PL - Backorder',
				'stage' => 'On Hold',
				'trackingCode' => '',
			)
		);

		return $this->httpPut($endpoint, $params);
	}

	public function receiverOrder($order_id)
	{
		$endpoint = 'v1/SalesOrders';

		$params = array(
			array(
				'id' => $order_id,
				'logisticsCarrier' => '',
				'logisticsStatus' => 2,
				'internalComments' => 'Transfer to 3PL',
				'stage' => 'Processing',
				'trackingCode' => '',
			)
		);

		return $this->httpPut($endpoint, $params);
	}

	public function getRequestUrl($endpoint, $params = array())
	{
		if ($params) {
			$endpoint .= '?where=';
			foreach ($params as $k => $v) {
				if (!is_numeric($v)) {
					$v = '\'' . $v . '\'';
				}
				$endpoint .= $k . '=' . $v . '+and+';
			}
			$endpoint = substr($endpoint, 0, strlen($endpoint) - 5);
		}

		$url = $this->domain;

		return $url . $endpoint;
	}

	public function httpGet($endpoint, $params = array())
	{
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl($endpoint, $params));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$header = array();
		$header[] = 'Authorization: Basic ' . base64_encode($this->user . ':' . $this->key);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$httpResponse = curl_exec($ch);
		curl_close($ch);

		if (!$httpResponse) {
			return ['done' => false];
		}

		// Extract the response details
		$httpParsedResponseAr = json_decode($httpResponse, true);

		if (!is_array($httpParsedResponseAr) || sizeof($httpParsedResponseAr) == 0) {
			return ['done' => false];
		}

		$this->log2file(json_encode($httpParsedResponseAr));
		return array_merge(['items' => $httpParsedResponseAr], ['done' => true]);
	}

	public function httpPost($endpoint, $params = array())
	{
		// Set the curl parameters
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl($endpoint));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$header = array();
		$header[] = 'Authorization: Basic ' . base64_encode($this->user . ':' . $this->key);
		$header[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$httpResponse = curl_exec($ch);
		curl_close($ch);

		if (!$httpResponse) {
			return ['done' => false];
		}

		// Extract the response details
		$httpParsedResponseAr = json_decode($httpResponse, true);

		if (sizeof($httpParsedResponseAr) == 0) {
			return ['done' => false];
		}

		$this->log2file(json_encode($httpParsedResponseAr));
		return array_merge(['items' => $httpParsedResponseAr], ['done' => true]);
	}

	public function httpPut($endpoint, $params = array())
	{
		// Set the curl parameters
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl($endpoint));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$header = array();
		$header[] = 'Authorization: Basic ' . base64_encode($this->user . ':' . $this->key);
		$header[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$httpResponse = curl_exec($ch);
		curl_close($ch);

		if (!$httpResponse) {
			return ['done' => false];
		}

		// Extract the response details
		$httpParsedResponseAr = json_decode($httpResponse, true);

		if (sizeof($httpParsedResponseAr) == 0) {
			return ['done' => false];
		}
		$this->log2file(json_encode($params));
		$this->log2file(json_encode($httpParsedResponseAr));
		return array_merge(['items' => $httpParsedResponseAr], ['done' => true]);
	}
	
	protected function log2file($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'cin7' . DIRECTORY_SEPARATOR;
		$lf .= 'cin7_' . date('Y-m-d') . '.log';
		return file_put_contents($lf, date('Y-m-d H:i:s') . ' ' . $m . "\n", FILE_APPEND);
	}

}