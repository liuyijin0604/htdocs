<?php
class ABMAPI
{

	public $test_url = 'http://sc.idanchuang.com/openwarehouse/wms/api/pca/callback';
	public $prod_url = '';
	public $url = '';

	public function __construct($test = false)
	{
		$this->url = $test ? $this->test_url : $this->prod_url;
	}

	public function httpPost($params = [])
	{
		// Set the curl parameters
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
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

		$this->log2file(json_encode($params));
		$this->log2file($httpResponse);
		if (!is_array($httpParsedResponseAr) || sizeof($httpParsedResponseAr) == 0) {
			return ['done' => false];
		} else {
			return array_merge($httpParsedResponseAr, ['done' => true]);
		}
	}
	
	protected function log2file($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'abm' . DIRECTORY_SEPARATOR;
		$lf .= 'abm_' . date('Y-m-d') . '.log';
		return file_put_contents($lf, date('Y-m-d H:i:s') . ' ' . $m . "\n", FILE_APPEND);
	}

}