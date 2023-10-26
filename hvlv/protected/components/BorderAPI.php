<?php
class BorderAPI
{
	const TRACKING_DIRECT_URL = 'https://www.fastway.com.au/courier-services/track-your-parcel?l=';
	public $result;
	public $rescode;
	public $err;
	public $is_test;
	private $prefix ="";
	private $debug = false;

	//Old API compatibility
	private $clientId = "71b09838.ba60.449d.96b5.f5280ff705ef";
	private $secret = "FJcN40Q5KTd3Tr4LUrx1jfn4d7Foxx7Z";
	public $origin = 'SYD';
	public $senderInfo = [];
	private static $apiUrl = 'https://au.api.fastway.org/v4/';

	private static $scope = 'fw-fl2-api-au';
	private static $url = 'https://api.myfastway.com.au';
	private static $token_url = 'https://api.borderexpress.com.au/token';

	private $token;
	public $prefixArray = ["SYD"=>"TLDB","MEL"=>"TLDB6","BNE"=>"TLDB7"];

	public function getAccessToken()
	{
		$service = new Service();
		$tokenData = $service->getCacheData("borderTokenData");
		if(!empty($tokenData))
		{
			return $tokenData['access_token'];
		}else
		{
			$tokenData = $this->getToken();
			$service->setCacheData("borderTokenData",$tokenData,3000);
			return $tokenData->access_token;
		}
	}

	public static function getDirectTrackingUrl($no)
	{
		return self::TRACKING_DIRECT_URL . $no;
	}

	function __construct($test = "TEST",$orgRateId = ImportChargeCode::MYBORDER_SYD_ID)
    {
       $this->orgRate = OrgRate::model()->findByPk($orgRateId);
       $type = $this->orgRate->mdata['facility'];
       $this->log = new APILog('Border',$type);

       $this->fromInfo=$this->log->fromInfo;
       $this->prefix = $this->getPrefix($type);
    }

    public function getPrefix($type)
    {
    	return $this->prefixArray[$type];
    }
    
    public static function getAPIInstance($courierId)
    {
        return new BorderAPI(false,$courierId);
    }
		
	public function findTheFacility($orgRate = false)
	{
		if($orgRate!=null)
		{
			return $orgRate->mdata['facility'];
		}else
		{
			return $this->orgRate->mdata['facility'];
		}
	}


	public function createShipment()
	{
		$ref = $this->genNumber($this->prefix);
		if(!empty($ref))
		{
			return ['success'=>true,'consignment_no'=>$ref];
		}
		return ['success'=>false,'error'=>'Without Ref Range'];
	}

	public static function canDeliver($shipment,$orgRate)
	{
		if(empty($shipment->packs)) return false;
		foreach ($shipment->packs as $key => $package)
		{
			if($package['weight']>40||$package['length']>240||$package['width']>240||$package['height']>240)
			{
				return false;
			}
		}
		$or = OrgRate::model()->findByPk($orgRate);
		$o = new stdClass();
		$o->code = 0;
		$o->msg="";
		$o->data = 0;

		$price = 0;
		$postcode = $shipment->cnee->postcode;
		$suburb = $shipment->cnee->suburb;
		$weight = $shipment->weight;

		$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code AND suburb=:suburb', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode,':suburb' => $suburb]);
		$chargeCode = 'xxxxxxxxx';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$chargeCode = $zoneMap['z1'];
		}
		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate]);
		if (!empty($zrs))
		{
			return true;
		}else
		{
			return false;
		}
	}

	public static function getCost($orgRate, $suburb, $postcode, $weight,$isApi = false)
	{
		$or = OrgRate::model()->findByPk($orgRate);
		$o = new stdClass();
		$o->code = 0;
		$o->msg="";
		$o->data = 0;

		$price = 0;
		$postcode = intval($postcode)+0;
		if(!empty($suburb))
		{
			$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code AND suburb=:suburb', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode,':suburb' => $suburb]);
		}else
		{
			$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$or->org_id ,':zoneid' => $or->zone_id,':code' => $postcode]);
		}
		$chargeCode = 'xxxxxxxxx';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$chargeCode = $zoneMap['z1'];
		}
		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate]);
		if (!empty($zrs)) {
			// get maximum one
			foreach ($zrs as $zr) {
				$temp = $zr['base'] + $zr['item'];
				if ($zr['nkg'] > 0) {
					$wl = $weight;
					$temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
				} else {
					$temp +=  $weight * $zr['perkg'];
				}
				if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
					$temp = $zr['minimum'];
				}
				
				if($isApi)
				{
					$temp = $temp*0.85;
				}

				$price = max($price, $temp);

				if(!empty($or->mdata["fuel"]))
				{
					$price=$price+($price*$or->mdata["fuel"]);
				}
			}

		}else
		{
			$o->code = 1;
			$o->msg="no rate for this serivce";
			return $o;
		}

		$o->msg = "success";
		$o->data = $price;
		return $o;
	}

	public static function getBorderRouteCode($shipment)
	{
		$borderRouteCode = BorderRouteCode::model()->find("suburb =:suburb and state = :state and postcode = :postcode",[":suburb"=>$shipment->cnee->suburb,":state"=>$shipment->cnee->state,":postcode"=>$shipment->cnee->postcode]);
		if(!empty($borderRouteCode))
		{
			return $borderRouteCode->route_code;
		}else
		{
			return "";
		}
	}

	public function removeSpecialChars($s){
		return preg_replace(['/[^\d\w\'\"\,\;\.\/\(\):&# \-]+/', '/[\’]+/'], ['', '\''] , $s);
	}

	public function prepareData($shipment,$isUpdate = false,$serviceCode,$consignmentId = 0,$consignmentData = false,$itemsForBorder = false)
	{
		$predata=new stdClass();
		if($isUpdate)
		{
			$predata->orderId = $shipment->mdata['etower_shipment_orderid'];
		}
		if(!empty($shipment->mdata['article_id'])) $predata->trackingNo = $shipment->mdata['article_id'];
		$predata->referenceNo=$shipment->id;
		if($this->is_test)
		{
			$predata->referenceNo=$shipment->id.ceil(rand(1, 100));
		}
		if(empty($serviceCode))
		{
			$orgRateId= $shipment->mdata['org_rate_id'];
			$orgRate = OrgRate::model()->findByPk($orgRateId);
			$serviceCode = "";
			$predata->facility= $this->findTheFacility($orgRate);
		}else
		{
			$predata->facility= $this->findTheFacility();
		}

		$predata->recipientName=substr($this->removeSpecialChars($shipment->cnee->name." ".$shipment->cnee->company), 0, 50);
		$predata->recipientCompany=substr($this->removeSpecialChars($shipment->cnee->company), 0, 49);
		if(!empty($itemsForBorder))
		{
			$predata->email= empty($shipment->cnee->email)?"imports@toplogistics.com.au":substr($shipment->cnee->email, 0, 50);
		}else
		{
			$predata->email= substr($shipment->cnee->email, 0, 50);
		}
		$predata->phone=substr($shipment->cnee->tel, 0, 20);
		$cnee_addr = $this->removeSpecialChars($shipment->cnee->address);
		$predata->addressLine1= substr($cnee_addr, 0, 40);
		if (strlen($cnee_addr) > 40) {
			$predata->addressLine2= substr($cnee_addr, 40, 100);
		}
		$predata->city=$shipment->cnee->suburb;
		$predata->state=$shipment->cnee->state;
		$predata->postcode=$shipment->cnee->postcode;
		$predata->country='AU';
		$predata->weight= $this->getBreakWeight($shipment->weight);
		if(!empty($consignmentId))
		{
			if(!empty($predata->length))
			{
				$predata->length = $consignmentData->length;
				$predata->width	=	$consignmentData->width;
				$predata->height =	$consignmentData->height;
			}
			$predata->weight= $this->getBreakWeight($consignmentData->weight);
			$predata->consignmentId = $consignmentId;
		}
		// $predata->volume=$shipment->cbm*($shipment->pkg);
		$predata->invoiceValue=$shipment->dvalue;
		$predata->invoiceCurrency='AUD';
		$predata->description=substr($shipment->getGoods(), 0, 50);
		$predata->serviceCode=$serviceCode;
		$invoiceValue = 0;
		if(!empty($itemsForBorder))
		{
			$predata->orderItems = [];
			foreach ($itemsForBorder as $key => $item) {
				$itemObj = new stdClass();
				$itemObj->itemCount = 1;
				$itemObj->weight = $item->weight;
				$itemObj->length = $item->length;
				$itemObj->width = $item->width;
				$itemObj->height = $item->height;
				$itemObj->originCountry=$item->originCountry;
				$itemObj->description=$item->description;
				$itemObj->unitValue=$item->unitValue;
				$predata->orderItems[] = $itemObj;
				$invoiceValue+=$item->unitValue;
			}
			$predata->invoiceValue=$invoiceValue;

		}

		$name=$shipment->agent_id;
		$shipper = Org::model()->findByPk($this->orgRate->mdata['ddpt_id']);
		$org=$shipment->agent;
		if (!empty($org->extra['delivery_label_name'])) {
			$name=$org->extra['delivery_label_name'];
		}
		if($shipper->suburb=='BANKSTOWN AERODROME')
		{
			$shipper->postcode = '2200';
		}

		$predata->shipperName=$name;
		$predata->shipperAddressLine1=substr($this->fromInfo['address'], 0, 80);
		$predata->shipperCity=substr($this->fromInfo['suburb'], 0, 80);
		$predata->shipperState=substr($this->fromInfo['state'], 0, 80);
		$predata->shipperPostcode=substr($this->fromInfo['postcode'], 0, 80);
		$predata->shipperCountry='Australia';
		$predata->shipperPhone=substr($shipper->phone, 0, 20);
		$predata->returnName= $name;
		$predata->returnAddressLine1=$this->fromInfo['address'];
		$predata->returnCity=$this->fromInfo['suburb'];
		$predata->returnState = $this->fromInfo['state'];
		$predata->returnPostcode = $this->fromInfo['postcode'];
		$predata->returnCountry = 'Australia';
		return $predata;
	}

	public function prepareDataBorder($shipment,$isUpdate = false,$serviceCode,$isSave = true)
	{
		$preDataArr = [];
		$pkg = $shipment->pkg;
		$consignmentDataArr = [];
		$cbm = $shipment->cbm*$shipment->pkg*1000000;
		$x = pow($cbm/(3*3*4), 1/3);
		$consignmentData = new stdClass();
		$consignmentData->qty = 1;
		$consignmentData->weight = $shipment->weight;
		$consignmentData->length = 0;
		$consignmentData->width = 0;
		$consignmentData->height = 0;
		$consignmentDataArr[] = $consignmentData;

		$itemsForBorder = $this->getBorderItems($shipment);


		$predata = $this->prepareData($shipment,false,$serviceCode,1,$consignmentDataArr[0],$itemsForBorder);
		$preDataArr[] = $predata;
		if($isSave)
		{
			$shipment->mdata['ubi_border'] = $consignmentDataArr;
			$shipment->updateMeta();
		}
		return $preDataArr;
	}

	public function getBorderItems($shipment)
	{
		$itemsForBorder = [];
		if(!empty($shipment->packs))
		{
			foreach ($shipment->packs as $key => $package) {
				$itemsForBorderObj = new stdClass();
				$itemsForBorderObj->qty = 1;
				$itemsForBorderObj->weight = $package['weight'];
				$itemsForBorderObj->length = $package['length'];
				$itemsForBorderObj->width = $package['width'];
				$itemsForBorderObj->height = $package['height'];
				$itemsForBorderObj->originCountry="AU";
				$itemsForBorderObj->description="Carton";
				$itemsForBorderObj->unitValue=number_format($shipment->getValue()/$shipment->pkg,2,".","");
				$itemsForBorder[] = $itemsForBorderObj;
			}
		}else
		{
			if(!empty($shipment->cbm))
			{
				$thisCBM = (($shipment->weight/$shipment->pkg)/rand(251,280));
				$cbm = $thisCBM*1000000;
				$x = pow($cbm/(3*3*4), 1/3);
				for ($i=0; $i < $shipment->pkg; $i++) { 
					$itemsForBorderObj = new stdClass();
					$itemsForBorderObj->qty = 1;
					$itemsForBorderObj->weight = number_format($shipment->weight/$shipment->pkg);
					if(4*$x>=120)
					{
						$x = pow($thisCBM/(3*3*3), 1/3);
						$itemsForBorderObj->length = number_format(3*$x,0,'.','');
						$itemsForBorderObj->width = number_format(3*$x,0,'.','');
						$itemsForBorderObj->height = number_format(3*$x,0,'.','');
					}else
					{
						$itemsForBorderObj->length = number_format(3*$x,0,'.','');
						$itemsForBorderObj->width = number_format(3*$x,0,'.','');
						$itemsForBorderObj->height = number_format(4*$x,0,'.','');
					}
					$itemsForBorderObj->originCountry="AU";
					$itemsForBorderObj->description="Carton";
					$itemsForBorderObj->unitValue=number_format($shipment->getValue()/$shipment->pkg,2,".","");
					$itemsForBorder[] = $itemsForBorderObj;
				}
			}else
			{
				$this->log2file('Time: '.date('Y-m-d H:i:s')."ref:".$shipment->hbn." empty cbm dim and packages");
				return [];
			}
		}
		return $itemsForBorder;
	}

	public function getBreakWeight($weight)
	{
		return  round($weight * 100) / 100;
	}

	/**
	* list all available users
	* @return bool
	* @throws Exception
	*/
	public function getUsers()
	{
		// http://au.api.fastway.org/v2/fastlabel/listusers?api_key=c17c64585c67180170dd504b75ca44ad
		$action = 'fastlabel/listusers?api_key=' . $this->apikey;
		$result = $this->request($action);
		if ($result) {
			return $result->result;
		}
		return false;
	}

	public function getToken()
	{
		$r = $this->mfRequest(self::$token_url, 'POST', ['grant_type' => 'client_credentials', 'client_id' => $this->clientId, 'client_secret' => $this->secret]);
		return $r;
	}


	public function mfRequest($url, $method = 'GET', $data = [])
	{
		$this->result = false;
		$c = new curl($url);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 600);
		$hdr = [];
		$hdr[] = 'Content-Type: application/x-www-form-urlencoded';
		if (is_array($data)) {
			$data = $c->asPostString($data);
		}
		if (!empty($data)) {
			$c->setopt(CURLOPT_POSTFIELDS, $data);
			$hdr[] = 'Content-Length: ' . strlen($data);
			$c->setopt(CURLOPT_HTTPHEADER, $hdr);
		}

		if (!$c->exec()) {
			throw new Exception('cUrl Error: '.$c->err);
		}
		$this->rescode = $c->rcode;

		if ($this->debug) {
			$this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".$data."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result."\n\n");
		}
		$this->result = json_decode($c->result);

		if (!empty($this->result->error)) {
			$this->err = $this->result->error;
			return false;
		}
		return $this->result;
	}

	public function request($url, $method = 'GET', $data = [])
	{
		$this->result = false;
		$c = new curl($url);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 600);
		$hdr = [];
		if (!empty($this->getAccessToken())) {
			$hdr[] = 'Authorization: bearer '.$this->getAccessToken();
			$hdr[] = 'Content-Type: application/json';
		}
		if (is_array($data)) {
			$data = $c->asPostString($data);
		}
		if (!empty($data)) {
			$c->setopt(CURLOPT_POSTFIELDS, $data);
			$hdr[] = 'Content-Length: ' . strlen($data);
		}
		$c->setopt(CURLOPT_HTTPHEADER, $hdr);

		if (!$c->exec()) {
			throw new Exception('cUrl Error: '.$c->err);
		}
		$this->rescode = $c->rcode;

		if ($this->debug) {
			$this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".$data."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result."\n\n");
		}
		$this->result = json_decode($c->result);

		if (!empty($this->result->error)) {
			$this->err = $this->result->error;
			return false;
		}
		return $this->result;
	}

	public function getTracking($ref)
	{
		$url = "https://api.borderexpress.com.au/api/v1/consignments/".$ref."/Statuses";
		$r = $this->request($url, 'GET', []);
		return $r;
	}
	public function prepareManifestData($p)
	{
		$name=$p->agent_id;
		$shipper = Org::model()->findByPk($this->orgRate->mdata['ddpt_id']);
		$org=$p->agent;
		if (!empty($org->extra['delivery_label_name'])) {
			$name=$org->extra['delivery_label_name'];
		}
		if($shipper->suburb=='BANKSTOWN AERODROME')
		{
			$shipper->postcode = '2200';
		}



		$predata = [];
		$predata['Consignment']=[];
		$predata['Consignment']['ConsignmentNumber'] = $p->ref;
		$predata['Consignment']['ConsignmentDate'] = date('Y-m-d');
		$predata['Consignment']['SenderName'] = $name;
		$predata['Consignment']['SenderStreetAddress'] = substr($this->fromInfo['address'], 0, 40);
		if (strlen($p->cnor->address) > 40) {
			$predata['Consignment']['SenderStreetAddress1'] =  substr($this->fromInfo['address'], 40, 80);
		}
		$predata['Consignment']['SenderSuburb'] = $this->fromInfo['suburb'];
		$predata['Consignment']['SenderState'] = $this->fromInfo['state'];
		$predata['Consignment']['SenderPostcode'] = $this->fromInfo['postcode'];
		$predata['Consignment']['SenderReference'] = $p->cref;
		$predata['Consignment']['Pickup'] = 'Yes';
		$predata['Consignment']['Delivery'] = 'Yes';
		$predata['Consignment']['DangerousGoods'] = 'NO';
		$predata['Consignment']['ReceiverName'] = substr($this->removeSpecialChars($p->cnee->name." ".$p->cnee->company), 0, 50);
			
		$predata['Consignment']['ReceiverStreetAddress'] = substr($p->cnee->address, 0, 40);
		if (strlen($p->cnee->address) > 40) {
			$predata['Consignment']['ReceiverStreetAddress1'] =  substr($p->cnee->address, 40, 80);
		}

		$predata['Consignment']['ReceiverSuburb'] = $p->cnee->suburb;
		$predata['Consignment']['ReceiverState'] = $p->cnee->state;
		$predata['Consignment']['ReceiverPostcode'] = $p->cnee->postcode;
		$predata['Consignment']['ReceiverPhone'] = $p->cnee->tel;
		if(!empty($p->cnee->email))
		{
			$predata['Consignment']['ReceiverEmail'] = $p->cnee->email;
		}
		$predata['Consignment']['Comments'] = "No split deliveries;ATL;";
		if(!empty($p->mdata['amazon_po']))
		{
			$predata['Consignment']['Comments'] .=";amazon_po:".$p->mdata['amazon_po'];
		}
		if(!empty($p->mdata['amazon_shipment_ids']))
		{
			$predata['Consignment']['Comments'] .=";amazon_shipment_ids:".$p->mdata['amazon_shipment_ids'];
		}
		
		$predata['Consignment']['ChargeTo'] = "TOP10";

		$weight = 0;
		foreach ($p->packs as $key => $pack)
		{
			$weight+=$pack['weight'];
		}

		$predata['Consignment']['TotalWeight'] = $weight;
		$predata['Consignment']['CarrierService'] = "BEP";
		foreach ($p->packs as $key => $pack)
		{
			$predata['Consignment']['LoadDetails'.$key] = [];
			$predata['Consignment']['LoadDetails'.$key]['ItemReference'] = $p->hbn;
			$predata['Consignment']['LoadDetails'.$key]['NumberOfUnits'] = '1';
			$predata['Consignment']['LoadDetails'.$key]['LogisticUnit'] = 'CARTON';
			$predata['Consignment']['LoadDetails'.$key]['Weight'] = $pack['weight'];
			$predata['Consignment']['LoadDetails'.$key]['Length'] = number_format($pack['length']/100,2,'.','');
			$predata['Consignment']['LoadDetails'.$key]['Width'] =  number_format($pack['width']/100,2,'.','');
			$predata['Consignment']['LoadDetails'.$key]['Height'] = number_format($pack['height']/100,2,'.','');
			$predata['Consignment']['LoadDetails'.$key]['Cubic'] = number_format(($pack['length']*$pack['width']*$pack['height'])/1000000,3,'.','');
			$weight+=$pack['weight'];
		}

		$predata['Consignment']['LogisticUnitDetails']=[];
		$barcodes = $p->getParcelLongRef($p->mdata['org_id'],1);
		foreach ($barcodes as $key => $barcode) {
			$predata['Consignment']['LogisticUnitDetails']['Barcode'.$key] = $barcode;
		}
		

		return $predata;
	}

	private function getFileName($index)
	{
		return 'TLA_BORDER_'.date('YmdHis').$index.'.xml';
	}

	public function xmlManifest($s,$index)
	{
		try {
			$data=$this->prepareManifestData($s);
			$filename = $this->getFileName($index);
			$path = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'MYBORDER' . DIRECTORY_SEPARATOR;
			$xmlCreator = new XMLCreator(['LoadDetails','Barcode']);
			$fullFilename = $path.$filename;
			$xmlCreator->create($fullFilename,$data);
			return [$fullFilename,$filename];
		} catch (Exception $ex) {
			throw $ex;
			return array("status"=>false);
		}
	}

	public static function getSFTP(){
		require_once Yii::app()->basePath . '/vendor/autoload.php';
		$sftp = new phpseclib\Net\SFTP('sftp.borderexpress.com.au');
		$loginResult = $sftp->login('toplogistics', 'veS!fZKslGMh');
		return [$sftp,$loginResult];
	}

	public function ftpManifest($s,$index,$sftp,$loginResult){
		require_once Yii::app()->basePath . '/vendor/autoload.php';

		[$fullFilename,$mfn] = $this->xmlManifest($s,$index);
		$man_no = date('YmdHis').$index;
		$xml = file_get_contents($fullFilename);
		// Check SFTP Connection$sftp->login('toplogistics', 'veS!fZKslGMh')
		if ($loginResult) {

			if($sftp->put($mfn, $xml)){
				file_put_contents($lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'MYBORDER' . DIRECTORY_SEPARATOR. 'MYBORDER_Manifest' . DIRECTORY_SEPARATOR.$mfn, $xml);
				unlink($fullFilename);
				return $man_no;
			}
		}
		return false;
	}

	public function prepAddrLines($addr, $split = false){
		$addr = str_ireplace(['&#xd', '&#xa', '&#39', ' ', '–', '"'], ["\n", "\n", "'", ' ', '-', ''], $addr);
		if(!$split) return $addr;
		$ls = preg_split('/[\n\r;]+/', trim($addr));
		if (sizeof($ls) == 1 && mb_strlen($addr) > 40) {
			$ls = AppHelper::mbstr_split($addr, 40);
		}
		return $ls;
	}


	public function genNumber($pfx = 'EEEEE')
	{
		return ConnoteRange::newNumber('Org', Org::ORGID_COURIER_BORDER, $pfx);
	}
	
	protected function log2file($m, $file='ELabel')
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'MYBORDER' . DIRECTORY_SEPARATOR;
		$lf .= 'MYBORDER_api_'.$file. date('Y-m-d') . '.log';
		return file_put_contents($lf, $m, FILE_APPEND);
	}

	//end of class
}
