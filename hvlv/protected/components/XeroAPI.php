<?php

class XeroAPI {

	public $callback = 'http://localhost/';
	public $consumer_key = 'XAP9LTZ5R43RQQ060XYVV5NELHXNBF';
	public $consumer_secret = 'YH7LTXUC1JCQTDXSYPWYRGA79I2EDJ';
	public $xero = null;
	public $provider = null;
	public $key = null;

	// public function __construct($callback = null)
	// {
	// 	include_once(Yii::app()->basePath.'/vendor/autoload.php');
	// 	$config = [
	// 		'oauth' => [
	// 			'callback' => !empty($callback) ? $callback : $this->callback,
	// 			'consumer_key' => $this->consumer_key,
	// 			'consumer_secret' => $this->consumer_secret,
	// 			'rsa_private_key' => 'file://' . Yii::app()->basePath . '/data/xero_key.pem',
	// 		],
	// 		'curl' => [
	// 			CURLOPT_SSL_VERIFYPEER => false,
	// 		],
	// 	];

	// 	$this->xero = new \XeroPHP\Application\PrivateApplication($config);

	// 	return $this->xero;
	// }

	public function __construct($key = 'xero_token_pcaex', $initialToken = false)
	{
		$this->key = $key;
		include_once(Yii::app()->basePath.'/vendor/autoload.php');

		$setting = SystemSetting::model()->find('t.key = :key', [':key' => $key]);

		$provider = new Calcinai\OAuth2\Client\Provider\Xero([
			'clientId'          => $setting->mdata['clientId'],
			'clientSecret'      => $setting->mdata['clientSecret'],
			'redirectUri'       => $setting->mdata['redirectUri'],
		]);
		$this->provider = $provider;

		if ($initialToken) return;

		$accessToken = new League\OAuth2\Client\Token\AccessToken($setting->mdata['token']);
		$accessToken = $provider->getAccessToken('refresh_token', [
			'refresh_token' => $accessToken->getRefreshToken()
		]);
		$setting->mdata['token'] = json_decode(json_encode($accessToken), true);
		$setting->save();

		$tenants = $provider->getTenants($accessToken);

		$this->xero = new \XeroPHP\Application($accessToken, $tenants[0]->tenantId);

		return $this->xero;
	}

	public function new($class)
	{
		$class = '\XeroPHP\Models\\' . $class;
		return new $class($this->xero);
	}

	public function get($class, $params = array(), $page = false)
	{
		$query = $this->xero->load($class);
		foreach ($params as $k => $v) {
			if ($k === 'ModifiedAfter') {
				$query = $query->modifiedAfter(new DateTime($v));
			} else if ($k === 'FromDate') {
				$query = $query->where(sprintf('Date >= DateTime(%s)', date('Y,m,d', strtotime($v))));
			} else if ($k === 'ToDate') {
				$query = $query->where(sprintf('Date <= DateTime(%s)', date('Y,m,d', strtotime($v))));
			} else {
				$query = $query->where($k, $v);
			}
		}
		if($page !== false){
			$query->page($page);
		}
		$result = $query->execute();

		return $result;
	}

	public function getByID($class, $id)
	{
		sleep(2);
		try {
			$result = $this->xero->loadByGUID($class, $id);
			return $result;
		} catch (Exception $ex) {
			return null;
		}
	}

	public function delete($class, $id)
	{
		$invoice = $this->xero->loadByGUID($class, $id);
		$invoice->setStatus('VOIDED');
		$invoice->save();
	}

}