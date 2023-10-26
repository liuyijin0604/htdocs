<?php
class ShunfengAPI {
	private static $test_url = 'http://218.17.248.244:11080/bsp-oisp/ws/sfexpressService?wsdl';

	private static $url = 'http://bsp-oisp.sf-express.com/bsp-oisp/ws/sfexpressService?wsdl'; //Production URL

	private $api_id, $api_key, $cust_id, $is_test, $debug;

	public $result, $err;

	public $logging = true;

	public function __construct($id, $key, $cid = '', $debug = false, $test = false){
		$this->api_id = $id;
		$this->api_key = $key;
		$this->is_test = $test;
		$this->cust_id = empty($cid)? $id : $cid;
		$this->debug = $debug;
	}

	public function addOrder($p, $ja='', $et=11){
		$ja = empty($ja)? (empty($p->cnor->address)? 'U12, 1801 Botany Road' : $p->cnor->address) : $ja;
		$xml = '<Order
orderid="'.$p->hbn.'" 
custid="'.$this->cust_id.'"
j_company="PCA Express"
j_contact="'.htmlspecialchars($p->cnor->name).'"
j_tel="'.(empty($p->cnor->tel)? '1800518000' : $p->cnor->tel).'"
j_address="'.$ja.'"
d_company="个人"
d_contact="'.$p->cnee->name.'"
d_tel="'.$p->cnee->tel.'"
d_address="'.$p->cnee->getCnFullAddress().'"
express_type="'.$et.'"
pay_method="1"
parcel_quantity="1"
cargo_total_weight="'.$p->shipWeight().'"
remark="">';
	foreach($p->eitems['g'] as $gi=>$g){
		$xml .= '<Cargo
name="'.$g.'"
count="'.$p->eitems['q'][$gi].'"
unit="个"></Cargo>';
	}
		$xml .= '</Order>';

		$this->request('OrderService', $xml);
		if(empty($this->err)){
			$p->ref = (string) $this->result->OrderResponse->attributes()->mailno;
			$p->mdata['dtb'] = (string) $this->result->OrderResponse->attributes()->destcode;
			if($p->status == 20) $p->status = 25;
			$p->save();
		}
	}

	public function track($tn){
		$xml = '<RouteRequest tracking_type="1" method_type="1" tracking_number="'.$tn.'"/>';
		$this->request('RouteService', $xml);
		return $this->result;
	}

	protected function request($service, $xml){
		$this->result = false;

		try{
			$data = '<Request service="'.$service.'" lang="zh-CN">
<Head>'.$this->api_id.'</Head>
<Body>'.$xml.'</Body>
</Request>';
			$v = base64_encode(md5($data.$this->api_key, true));
			$client = new SoapClient($this->is_test? self::$test_url : self::$url, array('trace' => 1));
			$res = $client->sfexpressService(['arg0' => $data, 'arg1' => $v]);
			//echo $res->return;
			if(!empty($res->return)){
				$this->log($res->return);
				$xml = simplexml_load_string($res->return);
				if($xml->Head == 'ERR'){
					$this->err[] = [(string) $xml->ERROR->attributes()->code => (string) $xml->ERROR];
				}else{
					$this->result = $xml->Body;
				}
			}
		}catch(SoapFault $fault) {
			Yii::log($fault, 'error');
			return false;
		}

		return true;
	}

	public function log($m){
		if(!$this->logging) return false;
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'shunfeng_api.log';
		@file_put_contents($lf, date('Y-m-d H:i:s').' '.$m."\n", FILE_APPEND);
	}

//end of class
}
