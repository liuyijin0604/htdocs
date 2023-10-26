<?php
include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR.'vendor/autoload.php');

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

class PaypalClient
{
	//@Author:Nero @Date:2021/6/3
	private const CLIENT_ID = 'AVL9VKfCUXcM4engbDS_5i4Lb0T5nJXo9inEBBh5XJJtS4Gl0EZ6y7sglZYqEwRSrGIrZACU7TFQoQAp';
	private const SECRET = 'EDaCZhOc3XfJyHhu0A89F8obqZRopRreAQdYa8w8HrY78uxy2A8_0PS4tSwCbc5nE5pPtTtRTa_ioofT';
	private const SB_CLIENT_ID = 'ATqFWVmknm3aWYMvRYPDW8sgQUVDoBGcf8ytju7jhs-ZBbKaY2LlRVrgHKzzgIyk3tj1qhwdv8k_DQDO';
	private const SB_SECRET = 'EHhI7r4tnna6fYZpF6kWpWthoj5Xd2JwCVeqQaqE8i_gZ5ymdL5S1HvmHnPdbczZFfoMMgG_rQY0fN_F';

	public $debug = false;
	public $sandbox = false;

	public function __construct($debug = false, $sandbox = false){
		$this->debug = $debug;
		$this->sandbox = $sandbox;
	}

	public function createOrder($data)
	{
		$request = new OrdersCreateRequest();
		$request->prefer('return=representation');
		$request->body = [
			'intent' => 'CAPTURE',
			'purchase_units' => $data,
		];

		return $this->request($request);
	}

	public function captureOrder($orderId)
	{
		return $this->request(new OrdersCaptureRequest($orderId));
	}

	public function getOrder($orderId){
		return $this->request(new OrdersGetRequest($orderId));
	}

	public function request($request)
	{
		if($this->sandbox){
			$client = new PayPalHttpClient(new PayPalCheckoutSdk\Core\SandboxEnvironment(self::SB_CLIENT_ID, self::SB_SECRET));
		}else{
			$client = new PayPalHttpClient(new PayPalCheckoutSdk\Core\ProductionEnvironment(self::CLIENT_ID, self::SECRET));
		}

		$response = $client->execute($request);

		if ($this->debug) {
			$l = 'Time: '.date('Y-m-d H:i:s'). PHP_EOL .
				json_encode($response). PHP_EOL;
			$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'paypalclient.log';
			file_put_contents($lf, $l, FILE_APPEND);
		}

		return $response;
	}
}
