<?php
class StarTrackAPI {

	const API_TEST_URL = 'https://digitalapi.auspost.com.au/test/shipping/v1/'; //Test URL
	const API_URL = 'https://digitalapi.auspost.com.au/shipping/v1/'; //Production URL
	const API_GETORDER_SUMMARY_URL = 'https://digitalapi.auspost.com.au/api/shipping/v1/accounts/%s/orders/%s/summary';

	const API_PRODUCT_ID = 'EXP';
	
	private static $api_accounts = [
		'syd' => ['id' => '7RFZ', 'no' => '10148335', 'key' => 'd07de577-da80-43c7-ae4d-aca200020851', 'password' => 'xfd88af63b76122f1cc8'],
		'mel' => ['id' => '4XHZ', 'no' => '10153277', 'key' => '8f760adc-dcce-482a-8a5f-ada7fa5ec04a', 'password' => 'x635687251ece537341a'],
		'test' => ['id' => 'TEST', 'no' => '00447357', 'key' => '72139770-540f-4b2e-b399-9e0172656659', 'password' => 'x72df654730fc6870607'],
	];

	private $api_id, $api_key, $api_pwd, $is_test, $debug;
	public $result, $rescode, $err;

	public function __construct($o = 'syd', $debug = true, $test = false){
		if(isset(self::$api_accounts[$o])){
			$this->api_id = self::$api_accounts[$o]['no'];
			$this->api_key = self::$api_accounts[$o]['key'];
			$this->api_pwd =self::$api_accounts[$o]['password'];
		}
		$this->is_test = $test;
		$this->debug = $debug;
	}

	public static function aidChkDgt($aid){
		$mlid = substr($aid,0,3);
		$st = '';
		for($i = 0; $i < strlen($mlid); $i++){
			$st .= preg_match('/[A-Z]/', $mlid[$i])? substr(ord($mlid[$i]),-1) : $mlid[$i];
		}
		$st .= substr($aid, 3);
		$st = strrev($st);
		$dt = 0;
		for($i = 0; $i < strlen($st); $i++){
			if($i % 2 == 0){
				$dt += $st[$i] * 3;
			}else{
				$dt += $st[$i];
			}
		}
		return substr(10 - ($dt % 10), -1);
	}

	public static function getAccountNo($o = 'syd'){
		return self::$api_accounts[$o]['no'];
	}

	public function getAccounts($id=''){
		return $this->request('accounts/'.(empty($id)? $this->api_id : $id));
	}

	public function getItemPrices($pc_from, $pc_to, $weight, $dim=array()){
		$d = new stdClass;
		$d->from['postcode'] = $pc_from;
		$d->to['postcode'] = $pc_to;
		$itm = new stdClass;
		$itm->weight = $weight;
		if(!empty($dim[0])) $itm->length = floatval($dim[0]);
		if(!empty($dim[1])) $itm->height = floatval($dim[1]);
		if(!empty($dim[2])) $itm->width = floatval($dim[2]);
		$d->items = [$itm];

		return $this->request('prices/items', 'POST', $d);
	}

	public function getShipments($ids, $offset=null, $nos=null, $status=null){
		$qs = [];
		if(!empty($ids)) $qs[] = 'shipment_ids='.$ids;
		if(!empty($offset)) $qs[] = 'offset='.$offset;
		if(!empty($nos)) $qs[] = 'number_of_shipments='.$nos;
		if(!empty($status)) $qs[] = 'status='.$status;

		return $this->request('shipments'.(empty($qs)? '' : '?'.implode('&', $qs)));
	}

	protected function prepAddrLines($addr){
		$ls = preg_split('/[\n\r;]+/', $addr);
		if(sizeof($ls) == 1 && strlen($addr) > 40) $ls = str_split($addr, 40);
		$ls = array_slice($ls, 0, 3);
		$ls[0] = substr($ls[0], 0, 40);
		if(!empty($ls[1])) $ls[1] = substr($ls[1], 0, 40);
		if(!empty($ls[2])) $ls[2] = substr($ls[2], 0, 40);
		return $ls;
	}

	protected function log2file($m){
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'startrack' . DIRECTORY_SEPARATOR;
		$lf .= 'startrack_api_' . date('Y-m-d') . '.log';
		return file_put_contents($lf, $m, FILE_APPEND);
	}

	protected function prepShipmentData($p){
		if(get_class($p) == 'stdClass') return $p;
		$s = new stdClass;
		$s->shipment_reference = $p->hbn;
		$s->customer_reference_1 = substr($p->cref, 0, 20);


		// This is our consolidation.
		// Items travelling to the same receiver on the same service will be added to the same consignment number.
		// It is  a feature that is enabled by default to limit tracking requirements and reduce despatch costs.
		// https://developers.auspost.com.au/apis/shipping-and-tracking/reference/shipment-consolidation
		// If set true, the system attempts to search for existing pending shipments to consolidate this shipment with.
		// If set to false, a new shipment is created as per normal workflow. For further information on consolidation,
		// please refer to Shipment Consolidation.
		$s->consolidate = false;

		$s->from = new stdClass;
		$s->from->name = substr('PCA Express', 0, 40);
		$s->from->lines = $this->prepAddrLines("6C The Crescent");
		$s->from->suburb = 'Kingsgrove';
		$s->from->state = 'NSW';
		$s->from->postcode = '2208';
		$s->from->phone = '0299257111';

		$s->to = new stdClass;
		$s->to->name = substr($p->cnee->name, 0, 35);

		// will make Startrack server side issue, so just comment it now
		//  $s->to->business_name = '';

		$s->to->lines = $this->prepAddrLines($p->cnee->address);
		$s->to->suburb = $p->cnee->suburb;
		$s->to->state = strtoupper($p->cnee->state);
		$s->to->postcode = sprintf('%04d', $p->cnee->postcode);
		if(!empty($p->mdata['amazon_po'])) $s->to->delivery_instructions=['Amazon PO:'.substr($p->mdata['amazon_po'],0,15)];
		if(!empty($p->cnee->tel)) $s->to->phone = $p->cnee->auTel();
		$cnee_email = filter_var($p->cnee->email, FILTER_VALIDATE_EMAIL);
		if($cnee_email !== false){
			$s->to->email = $cnee_email;
			$s->email_tracking_enabled = true;
		}

		$insurance = round($p->insurance / ( $p->pkg > 0 ? $p->pkg : 1),2);

		$pkgCount =  $p->pkg > 0 ? $p->pkg : 1;
		// for startrack we need round weight to integer
//        $p->bwf=$p->bwf|1;
//        $p->update(['bwf']);
		$singleWeight = sprintf('%d', max(round($p->weight/$pkgCount),1))*0.75;
		$p->mdata['manifest_weight']= $singleWeight*$pkgCount;
		$p->updateMeta();
		for($i = 1; $i <= $p->pkg; $i++){
			$itm = new stdClass;
			$itm->item_reference = $p->hbn.'-'.$i;
			$itm->product_id = self::API_PRODUCT_ID;

			$itm->weight = sprintf('%.02f', $singleWeight);
			if(!empty($p->mdata['ss_shipment_items'][$i-1]))
			$itm->item_id=$p->mdata['ss_shipment_items'][$i-1];

			// try to a small one dim
			$needUpdateDim = false;
			$vol = $p->cbm * 1000000;
			if ( !empty($p->mdata['dim']) ){

				// sometimes we got dim is stdClass not array
				if ( !is_array( $p->mdata['dim']) ) {
					$p->mdata['dim'] = array('w' => 0,'d' => 0,'h' => 0);
				}
				$realVol = floatval($p->mdata['dim']['w']) * floatval($p->mdata['dim']['d']) * floatval($p->mdata['dim']['h']);
				if ( $vol > 0 && ( round($vol * 1000) < round($realVol * 1000) || $realVol <= 0.0 ) ) {
					$needUpdateDim = true;
				}
			} else {
				$p->mdata['dim'] = array();
				if ( $vol > 0 ) {
					$needUpdateDim = true;
				}
			}

			// update dim with new value
			if ( $needUpdateDim ) {
				$p->mdata['dim']['w'] = round($vol / 1000, 1);
				$p->mdata['dim']['d'] = round(sqrt($vol / $p->mdata['dim']['w']) * rand(10, 20) / 10, 1);
				$p->mdata['dim']['h'] = round($vol / $p->mdata['dim']['w'] / $p->mdata['dim']['d'], 1);
			}

			$itm->width = sprintf('%.1f',  floatval($p->mdata['dim']['w']));
			$itm->height = sprintf('%.1f', floatval($p->mdata['dim']['h']));
			$itm->length = sprintf('%.1f', floatval($p->mdata['dim']['d'])*0.8);
			$itm->authority_to_leave = false;
			$itm->partial_delivery_allowed = false;

			// Mandatory for StarTrack only
			// The packaging type for this item. Some example values are: CTN, PAL, SAT, BAG, ENV, ITM, JIF, SKI
			// Note, for StarTrack products, the packaging type (refereed to as a unit type by StarTrack) is mandatory and has a maximum length of 3.
			$itm->packaging_type = 'ITM';

			// in case insurance
			if ( $insurance > 0 ) {
				$itm->features = new stdClass();
				$itm->features->TRANSIT_COVER = new stdClass();
				$itm->features->TRANSIT_COVER->attributes = new stdClass();
				$itm->features->TRANSIT_COVER->attributes->cover_amount = $insurance;
			}

			if(!empty($p->ref) && preg_match('/^(7RFZ|4XHZ)\d{8}/i', $p->ref)){
				$aid = $p->ref.'EXP'.sprintf('%05d', $i);
				$itm->tracking_details = [
					'consignment_id' => $p->ref,
					'article_id' => $aid,
					'barcode_id' => $aid,
				];
			}

			$s->items[] = $itm;
		}

		return $s;
	}

	// refer to
	// https://developers.auspost.com.au/apis/shipping-and-tracking/reference/create-shipments
	public function createShipments($p){
		$d = new stdClass;
		if(is_array($p)){
			foreach($p as $s){
				$d->shipments[] = $this->prepShipmentData($s);
			}
		}else{
			$d->shipments[] = $this->prepShipmentData($p);
		}
		return $this->request('shipments', 'POST', $d);
	}

	public function updateShipment($id, $p){
	   $theResult=$this->request('shipments/'.$id, 'PUT', $this->prepShipmentData($p));
	   if($theResult){
			$result = $this->getShipments($id);
			$result_item=[];
		  if ( $result && isset($result->shipments) && is_array($result->shipments) ) {
				$result = $result->shipments[0];
			  foreach ($result->items as $item) {
				if (!empty($item->tracking_details->article_id)) {
					if (preg_match('/(?<=7RFZ|4XHZ)\d{8}EXP(\d{5})/i', $item->tracking_details->article_id,$match)) {
						 $result_item[intval($match[1]-1)]=$item->item_id;
					}
				}
			  }
			  ksort($result_item);
			  $p->mdata['ss_shipment_items']=$result_item;
			  $p->updateMeta();
		  }
	   }
	  return $theResult;
	}

	public function updateItems($id, $p){
		$s = $this->getShipments($id);
		if(empty($s->shipments)) return $s;
		$ns = $this->prepShipmentData($p);
		foreach($ns->items as $k=>$itm){
			if(isset($s->shipments[0]->items[$k])) $itm->item_id = $s->shipments[0]->items[$k]->item_id;
		}
		return $this->request('shipments/'.$id.'/items', 'PUT', ['items' => $ns->items]);
	}

	public function updateItemsById($id){
		$p = ImParcel::model()->findByPk($id);
		$id = $p->trans[0]->mdata['ss_shipment_id'];
		$ns = $this->prepShipmentData($p);
		return $this->request('shipments/'.$id.'/items', 'PUT', ['items' => $ns->items]);
	}

	public function deleteShipment($ids){
		return $this->request('shipments?shipment_ids='.$ids, 'DELETE');
	}

	public function deleteItem($id, $item_id){
		return $this->request('shipments/'.$id.'/items/'.$item_id, 'DELETE');
	}

	public function getOrder($id){
		return $this->request('orders/'.$id);
	}

	/**
	 * If you want to print labels using the API you need to follow this flow:
	•	Create shipment(s) – returns shipment id
	•	Create Labels – using shipment id, returns request id
	•	Get labels – using request id, returns URL to pdf labels

	•	Create order including all shipments for that day – using a list of shipment ids, returns order id
	•	Get order summary(manifest) – using order id

	 * @param $ss
	 * @param null $ref
	 * @return bool|mixed
	 * @throws Exception
	 */
	public function createOrderFromShipments($ss, $ref=null){
		$d = new stdClass;
		if ( !empty($ref) ) $d->order_reference = $ref;
		foreach($ss as $s){
			if ( isset($s->mdata['ss_shipment_id']) ) {
				$d->shipments[] = ['shipment_id' => $s->mdata['ss_shipment_id']];
			}elseif(isset($s->trans[0]->mdata['ss_shipment_id'])){
				$d->shipments[] = ['shipment_id' => $s->trans[0]->mdata['ss_shipment_id']];
			}
		}
		return $this->request('orders', 'PUT', $d);
	}


	protected function prepOrderShipmentData($p){
		if ( isset($p->mdata['ss_shipment_id']) ) {
			$s = new stdClass();
			$s->shipment_id = $p->mdata['ss_shipment_id'];
			return $s;
		} else {
			return null;
		}
	}

	public function createOrderIncludingShipments($ss, $ref=null){
		$d = new stdClass;
		if(!empty($ref)) $d->order_reference = $ref;
		foreach($ss as $p){
			if($p->status == 100) continue;
			$d->shipments[] = $this->prepShipmentData($p);
		}
		return $this->request('orders', 'POST', $d);
	}

	/**
	 * @param $orderId
	 * @return bool|mixed
	 * @throws Exception
	 */
	public function getOrderSummary($orderIds){

		if ( count($orderIds) == 1 ) {
			foreach ( $orderIds as $k => $orderId) {
				return $this->requestOrderSummary($orderId);
			}
		} else {
			$pdfSummaries = array();
			foreach ( $orderIds as $k => $orderId) {
				$pdfSummaries[] = oPDF::output($this->requestOrderSummary($orderId,true), 2,'');
			}

			// merge all pdfs
			oPDF::mergePDF($pdfSummaries,1, true);
		}
	}


	/**
	 * refer to
	 * https://developers.auspost.com.au/apis/shipping-and-tracking/reference/get-label
	 * @param $reqId
	 * @return bool|mixed
	 * @throws Exception
	 */
	public function getLabel($reqId){
		return $this->request('labels', 'GET', $reqId);
	}


	/**
	 *  refer to
	// https://developers.auspost.com.au/apis/shipping-and-tracking/reference/create-labels
	 * @param $ids
	 * @param string $layout
	 * @return bool|mixed
	 * @throws Exception
	 */
	public function createLabels($ids, $layout="A6"){
		$d = new stdClass;
		$d->preferences = ['type' => 'PRINT',
			'groups' =>[
				[
					'group' => "StarTrack",
					'layout' => $layout == 'A4'? 'A4-4pp' : 'A6-1pp',
					'branded' => $layout == 'A4'? false : true,
					'left_offset' => 0,
					'top_offset' => 0,

				] ]
		];

		foreach($ids as $id){
			$d->shipments[] = ['shipment_id' => $id];
		}
		return $this->request('labels', 'POST', $d);
	}

	public function validateAddress($suburb, $state, $postcode){
		$r = $this->request('address?suburb='.$suburb.'&state='.$state.'&postcode='.$postcode);
		return $r->found;
	}

	public function trackItems($ids){
		return $this->request('track?tracking_ids='.$ids);
	}

	public function createPayment($order_id, $ref=null){
		$d = new stdClass;
		$d->order_id = $order_id;
		$d->payments_method = 'CHARGE_TO_ACCOUNT';
		if(!empty($ref)) $d->payment_reference = $ref;
		return $this->request('payments', 'POST', $d);
	}

	protected  function requestOrderSummary($orderId,$returnPdf = false){
		$this->result = false;

		$url = sprintf(self::API_GETORDER_SUMMARY_URL, $this->api_id,$orderId);

		if ( $this->debug )  {
			$this->log2file('Time: '.date('Y-m-d H:i:s')."\nGet order summary URL: ".$url . "\n");
		}

		$c = new curl($url);
		$c->setopt(CURLOPT_USERPWD, $this->api_key . ":" . $this->api_pwd);
		$c->setopt(CURLOPT_CUSTOMREQUEST, 'GET');
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 600);

		if(!$c->exec()) throw new Exception('cUrl Error: '.$c->err);

		if ( $c->rcode == 200 ) {
			// startrack will return order summary with pdf format
			if ( $this->debug )  {
				$this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: order summary successfully \n\n");
			}

			if ( $returnPdf ) {
				return $c->result;
			} else {
				header("Cache-Control: maxage=1");
				header("Pragma: public");
				header('Content-Type: application/pdf');
				header('Content-Disposition: inline; filename=sslable.pdf');
				echo $c->result;
				Yii::app()->end();
			}
		} else {

			$this->result = json_decode($c->result);

			if(!empty($this->result->errors)){
				$this->err = $this->result->errors;
				$this->log2file('Time: '.date('Y-m-d H:i:s') . "\nERROR: " . "\n code : " . $this->err[0]->code, "\n name : " . $this->err[0]->name . "\n message : " . json_encode($this->err) . "\n\n");
				return false;
			}
		}

		return $this->result;
	}


	protected function request($action, $method = 'GET', $data = null){
		$this->result = false;
		$url = ($this->is_test? self::API_TEST_URL : self::API_URL).$action;
		if ( $method == 'GET' ) {
			if ( !empty($data) ) $url .= '/' . $data;
		}
		$c = new curl($url);
		$c->setopt(CURLOPT_USERPWD, $this->api_key . ":" . $this->api_pwd);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 600);
		$hdr = ['Content-Type: application/json','Account-Number: ' . $this->api_id];
		if(!empty($data) && ( $method == 'POST' || $method == 'PUT' ) ){
			$data = json_encode($data);
			$c->setopt(CURLOPT_POSTFIELDS, $data);
			$hdr[] = 'Content-Length: ' . strlen($data);
		}
		$c->setopt(CURLOPT_HTTPHEADER, $hdr);
		if(!$c->exec()) throw new Exception('cUrl Error: '.$c->err);
		$this->rescode = $c->rcode;

		if($this->debug) $this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nMethod: ".$method."\nRequest: ".json_encode($data)."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result."\n\n");

		if(preg_match('/^application\/json/i', $c->header['Content-Type'])){
			$this->result = json_decode($c->result);
			if(!empty($this->result->errors)){
				$this->err = $this->result->errors;
				return false;
			}
		}else{
			$this->result = $c->result;
		}

		return $this->result;
	}

//end of class
}
