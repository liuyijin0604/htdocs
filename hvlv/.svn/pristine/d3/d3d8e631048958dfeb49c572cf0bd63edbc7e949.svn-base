<?php
//include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR.'vendor/autoload.php');
include_once(Yii::app()->basePath.'/vendor2/autoload.php');
use Mirakl\MMP\Shop\Client\ShopApiClient as MiraklApiClient;
use Mirakl\MMP\Shop\Request\Offer\GetOfferRequest;
use Mirakl\MMP\Shop\Request\Order\Get\GetOrdersRequest;
use Mirakl\MMP\FrontOperator\Domain\Order as Ordera;
use Mirakl\MMP\Shop\Client\ShopApiClient;
use Mirakl\MMP\Shop\Request\Order\Tracking\UpdateOrderTrackingInfoRequest;

class MiraklAPI{
    //Subemo 
    public $apiUrl = 'https://marketplace.catch.com.au/api';
	public $apiKey = '40d2e621-eaf0-478b-bd84-0f0d971f97b0';
    public $org_id = '3841';

    public function __construct($config = array())
	{
		if (!empty($config['apiUrl'])) {
			$this->apiUrl = $config['apiUrl'];
		}

		if (!empty($config['apiKey'])) {
			$this->apiKey = $config['apiKey'];
		}

        if(!empty($config['org_id'])){
            $this->org_id = $config['org_id'];
        }
	}

    public function getClient(){
        try {
            $client = new MiraklApiClient($this->apiUrl, $this->apiKey);
            return $client;
        } catch (\Exception $e) {
            var_dump($e);
        }
    
    }

    public function getOrders(){
        try{
            $client = new MiraklApiClient($this->apiUrl, $this->apiKey);
            $request = new GetOrdersRequest();
            $request -> setPaginate(false);
            return $client->getOrders($request);
        }catch (\Exception $e) {
            var_dump($e);
        }
    }

    public function setTracking($id,$courier_code,$couride_name,$ref,$Url){
        try{
            $client = new MiraklApiClient($this->apiUrl, $this->apiKey);
            $request = new UpdateOrderTrackingInfoRequest($id, [
                'carrier_code'       => $courier_code,
                'carrier_name'       => $couride_name,
                'carrier_url'        => $Url,
                'tracking_number'    => $ref,
            ]);
            $client->updateOrderTrackingInfo($request);

        }catch (\Exception $e) {
            var_dump($e);
        }
    }
}