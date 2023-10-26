<?php
class ShopifyAPI
{

	// only elekzon now, probably need table to store more api info
	public $store_domain = 'https://toplogisticsnero.myshopify.com/';
	public $api_key = 'c8b70c46d613ccd101254e87032fd186';
	public $api_secret = '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';
	public $org_id = 1;

	// test api info
	// public $store_domain = 'https://pcaexpress.myshopify.com';
	// public $api_key = 'ded91eb1d28b189c5011c8375a9a3367';
	// public $api_secret = '4ed59305708f484a5eb7a8d575f2cf05';
	// public $org_id = 114;

	public $api_version = '/api/2021-07';

	public function __construct($config = array())
	{
		if (!empty($config['store_domain'])) {
			$this->store_domain = $config['store_domain'];
		}

		if (!empty($config['api_key'])) {
			$this->api_key = $config['api_key'];
		}

		if (!empty($config['api_secret'])) {
			$this->api_secret = $config['api_secret'];
		}

		if (!empty($config['org_id'])) {
			$this->org_id = $config['org_id'];
		}
	}

	public function getRequestUrl($endpoint, $params = array())
	{
		if ($params) {
			$endpoint .= '?';
			foreach ($params as $k => $v) {
				$endpoint .= $k . '=' . $v . '&';
			}
		}

		$store_url = explode('//', $this->store_domain)[0] . '//' . $this->api_key . ':' . $this->api_secret . '@' . explode('//', $this->store_domain)[1];

		return $store_url . $endpoint;
	}

	public function httpGet($endpoint, $params = array())
	{
		sleep(1);
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl($endpoint, $params));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, false);
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
		return array_merge($httpParsedResponseAr, ['done' => true]);
	}

	public function httpPost($endpoint, $params = array())
	{
		sleep(1);
		// Set the curl parameters
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl($endpoint));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$header = array();
		$header[] = 'Content-Type:application/json';
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
		return array_merge($httpParsedResponseAr, ['done' => true]);
	}

	public function httpPut($endpoint, $params = array())
	{
		sleep(1);
		// Set the curl parameters
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getRequestUrl($endpoint));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$header = array();
		$header[] = 'Content-Type:application/json';
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

		$this->log2file(json_encode($httpParsedResponseAr));
		return array_merge($httpParsedResponseAr, ['done' => true]);
	}

	public function getAccessToken()
	{
		$endpoint = '/admin' . $this->api_version . '/oauth/access_token';

		return null;
	}

	public function countProducts()
	{
		$endpoint = '/admin' . $this->api_version . '/products/count.json';

		$params = array(
			// 'updated_at_min' => date('c', strtotime('-1440min -10second')),
		);

		return $this->httpGet($endpoint);
	}

	public function getProducts($page = 1)
	{
		$endpoint = '/admin' . $this->api_version . '/products.json';

		$params = array(
			'limit' => 250,
			// 'page' => $page,
			// 'updated_at_min' => date('c', strtotime('-1440min -10second')),
		);

		return $this->httpGet($endpoint, $params);
	}

	public function getVariant($id)
	{
		$endpoint = '/admin' . $this->api_version . '/variants/' . $id . '.json';

		return $this->httpGet($endpoint, $params);
	}

	public function getInventoryLevels($item_id)
	{
		$endpoint = '/admin' . $this->api_version . '/inventory_levels.json';

		$params = array(
			'inventory_item_ids' => $item_id,
		);

		return $this->httpGet($endpoint, $params);
	}

	public function editInventoryLevel($item_id, $location_id, $qty)
	{
		$endpoint = '/admin' . $this->api_version . '/inventory_levels/adjust.json';

		$params = array(
			'inventory_item_id' => $item_id,
			'location_id' => $location_id,
			'available_adjustment' => $qty,
		);

		return $this->httpPost($endpoint, $params);
	}

	public function getOrder($id)
	{
		$endpoint = '/admin' . $this->api_version . '/orders/' . $id . '.json';

		return $this->httpGet($endpoint);
	}

	public function getOrderSince($id)
	{
		$endpoint = '/admin' . $this->api_version . '/orders.json';

		$params = array(
			'limit' => 2,
			'since_id' => $id,
		);

		return $this->httpGet($endpoint, $params);
	}

	public function getOrderByRef($ref)
	{
		$endpoint = '/admin' . $this->api_version . '/orders.json';

		$params = array(
			'limit' => 250,
			'status' => 'open',
			'fulfillment_status' => 'unfulfilled',
		);

		$orders = $this->httpGet($endpoint, $params);

		if ($orders['done'] == true) {
			foreach ($orders['orders'] as $order) {
				if ($order['order_number'] == $ref) {
					return $order['order_id'];
				}
			}
		}

		return 0;
	}

	public function countOrders($status = 'any', $financial_status = 'any', $time = '-30000min -10second')
	{
		$endpoint = '/admin' . $this->api_version . '/orders/count.json';

		$params = array(
			'updated_at_min' => date('c', strtotime($time)),
			'status' => $status,
			'fulfillment_status' => 'null',
			'financial_status' => $financial_status,
		);

		return $this->httpGet($endpoint, $params);
	}

	public function getOrders($page = 1, $status = 'any', $financial_status = 'any', $time = '-30000min -10second')
	{
		$endpoint = '/admin' . $this->api_version . '/orders.json';

		$params = array(
			'limit' => 250,
			// 'page' => $page,
			'updated_at_min' => date('c', strtotime($time)),
			'status' => $status,
			'fulfillment_status' => 'null',
			'financial_status' => $financial_status,
		);

		return $this->httpGet($endpoint, $params);
	}

	public function postFulfillment($id, $tracking_number, $tracking_urls = null, $shopifyLineID = null)
	{
		$endpoint = '/admin' . $this->api_version . '/orders/' . $id . '/fulfillments.json';

		$location = $this->getTLALocation();

		if (!empty($location)) {
			$params = array(
				'fulfillment' => array(
					'location_id' => $location['id'],
					'tracking_number' => $tracking_number,
					'tracking_urls' => $tracking_urls,
					'notify_customer' => true,
				),
			);

			return $this->httpPost($endpoint, $params);
		} else {
			return false;
		}
	}

	public function cancelFulfillment($id)
	{
		$endpoint = '/admin' . $this->api_version . '/fulfillments/' . $id . '/cancel.json';

		$params = [];

		return $this->httpPost($endpoint, $params);
	}

	public function completeFulfillment($orderid, $id)
	{
		$endpoint = '/admin' . $this->api_version . '/orders/' . $orderid . '/fulfillments/' . $id . '/complete.json';

		$params = [];

		return $this->httpPost($endpoint, $params);
	}

	public function openFulfillment($orderid, $id)
	{
		$endpoint = '/admin' . $this->api_version . '/orders/' . $orderid . '/fulfillments/' . $id . '/open.json';

		$params = [];

		return $this->httpPost($endpoint, $params);
	}

	public function updateFulfillment($id, $tracking_number, $tracking_urls = null)
	{
		$endpoint = '/admin' . $this->api_version . '/fulfillments/' . $id . '/update_tracking.json';

		$location = $this->getTLALocation();

		if (!empty($location)) {
			$params = array(
				'fulfillment' => array(
					'tracking_info' => array(
						'number' => $tracking_number,
						'url' => $tracking_urls[0],
					),
					// 'notify_customer' => true,
				),
			);

			return $this->httpPost($endpoint, $params);
		} else {
			return false;
		}
	}

	public function successFulfillment($orderid, $id)
	{
		$endpoint = '/admin' . $this->api_version . '/orders/' . $orderid . '/fulfillments/' . $id . '.json';

		$params = array(
			'fulfillment' => array(
				'id' => $id,
				'status' => 'success',
			),
		);

		return $this->httpPut($endpoint, $params);
	}

	public function getTLALocation()
	{
		$endpoint = '/admin' . $this->api_version . '/locations.json';

		$locations = $this->httpGet($endpoint);

		foreach ($locations['locations'] as $location) {
			// return $location;
			preg_match('/233MilperraRoad/i', str_replace(' ', '', $location['address1']), $match);
			if (!empty($match)) {
				return $location;
			}
		}

		return null;
	}
	
	protected function log2file($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'shopify' . DIRECTORY_SEPARATOR;
		$lf .= 'shopify_' . date('Y-m-d H') . '.log';
		return file_put_contents($lf, date('Y-m-d H:i:s') . ' ' . $m . "\n", FILE_APPEND);
	}

}
