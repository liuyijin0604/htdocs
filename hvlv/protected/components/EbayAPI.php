<?php
include_once(Yii::app()->basePath.'/vendor2/sdk-ebay-rest-fulfillment-master/vendor/autoload.php');
Use macropage\SDKs\ebay\rest\fulfillment\Configuration;
Use macropage\SDKs\ebay\rest\fulfillment\API\OrderApi;
Use macropage\SDKs\ebay\rest\fulfillment\Model\Order;
Use macropage\SDKs\ebay\rest\fulfillment\Model\OrderSearchPagedCollection;
Use macropage\SDKs\ebay\rest\fulfillment\Model\LineItem;
Use macropage\SDKs\ebay\rest\fulfillment\API\ShippingFulfillmentApi;
Use macropage\SDKs\ebay\rest\fulfillment\Model\ShippingFulfillmentDetails;
    
class EbayAPI{

    public $apiInstance;
    public $org_id = 3841;
    public $fulfillmentPolicyRequest;
    public $grantType = 'refresh_token';
    public $tokenUrl = 'https://api.ebay.com/identity/v1/oauth2/token';
    public $authCode = 'v^1.1%23i^1%23f^0%23I^3%23r^1%23p^3%23t^Ul4xMF8wOjRCNUM4QjVDMEZFMEI2QkJFQTgxNTVEMTRDQkQ5REJFXzFfMSNFXjI2MA==';
    public $ruName = 'https://api.ebay.com/oauth/api_scope/sell.fulfillment';
	public $clientID = 'toplogis-Toplogis-PRD-90fa8089d-605aaa81';
	public $certID = 'PRD-0fa8089da7f4-325d-403f-94b9-c99d';

    public function __construct()
	{
        
    }

    public function getOrderInformation(){
        $userToken = json_decode(json_encode($this->httpPost()));
        
        $config = Configuration::getDefaultConfiguration()->setAccessToken($userToken->access_token);
        $config->setAccessToken='Bearer';

        $apiInstance = new OrderApi(
            new GuzzleHttp\Client(),
            $config
        );
        $filter = "orderfulfillmentstatus:{NOT_STARTED|IN_PROGRESS}"; 
        $limit = null; 
        $offset = null; 
        $orderIds = null; 

        try {
            $result = $apiInstance->getOrders($filter, $limit, $offset, $orderIds);
            
            return $result;

        } catch (Exception $e) {

        }
    }

	public function getOrder($orderid){
		$userToken = json_decode(json_encode($this->httpPost()));
		$config = Configuration::getDefaultConfiguration()->setAccessToken($userToken->access_token);
		$config->setAccessToken='Bearer';
		$apiInstance = new OrderApi(
			new GuzzleHttp\Client(),
			$config
		);
		$orderId = $orderid;

		try {
			$result = $apiInstance->getOrder($orderId);
			return $result;
		} catch (Exception $e) {
			echo 'Exception when calling OrderApi->getOrder: ', $e->getMessage(), PHP_EOL;
		}		
	}

	public function createShippingFulfillmentDetail($orderid,$couriercode,$trackingno){
		$userToken = json_decode(json_encode($this->httpPost()));
		$config = Configuration::getDefaultConfiguration()->setAccessToken($userToken->access_token);
		$config->setAccessToken='Bearer';

		$apiInstance = new ShippingFulfillmentApi(
			new GuzzleHttp\Client(),
			$config
		);

		$orderId = $orderid; 
		$objOrder = $this-> getOrder($orderId);
		$shippingFulfillmentDetails = new ShippingFulfillmentDetails(); 
		
		try {
			$shippingFulfillmentDetails->setLineItems($objOrder->getLineItems());
			$shippingFulfillmentDetails->setShippingCarrierCode($couriercode);
			$shippingFulfillmentDetails->setTrackingNumber($trackingno);

			$result = $apiInstance->createShippingFulfillment($orderId, $shippingFulfillmentDetails);
			return $result;
		} catch (Exception $e) {
			echo 'Exception when calling ShippingFulfillmentApi->createShippingFulfillment: ', $e->getMessage(), PHP_EOL;
		}		
	}

    public function httpPost()
	{
        $codeAuth = base64_encode($this->clientID.':'.$this->certID);
        $ch = curl_init($this->tokenUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '.$codeAuth
        ));
		//curl_setopt($ch, CURLHEADER_SEPARATE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=".$this->grantType."&refresh_token=".$this->authCode."&scope=".$this->ruName);

		$httpResponse = curl_exec($ch);
		if(curl_errno($ch)){
			$data = curl_errno($ch);
		}

		curl_close($ch);

		if (!$httpResponse) {
			return ['done' => false];
		}

		// Extract the response details
		$httpParsedResponseAr = json_decode($httpResponse, true);

		if (sizeof($httpParsedResponseAr) == 0) {
			return ['done' => false];
		}

		//$this->log2file(json_encode($httpParsedResponseAr));
		return array_merge($httpParsedResponseAr, ['done' => true]);
	}

    protected function log2file($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'ebay' . DIRECTORY_SEPARATOR;
		$lf .= 'shopify_' . date('Y-m-d H') . '.log';
		return file_put_contents($lf, date('Y-m-d H:i:s') . ' ' . $m . "\n", FILE_APPEND);
	}
}
?>