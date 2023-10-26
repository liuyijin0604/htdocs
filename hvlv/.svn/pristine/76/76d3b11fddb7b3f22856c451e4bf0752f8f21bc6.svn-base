<?php
libxml_disable_entity_loader(false);
class SFAPI
{
	/************************Service Type***********/
	const SERVICE_SYD = "TNT_SYDNEY";
	const SERVICE_MEL = "TNT_MELBOURNE";
	const SERVICE_BNE = "TNT_BRISBANE";
	const SERVICE_SYD_TOP = "TNT_SYDNEY_TOP";
	const SERVICE_MEL_TOP = "TNT_MELBOURNE_TOP";
	const SERVICE_BNE_TOP = "TNT_BRISBANE_TOP";

	const SERVICE_TEST = "TNT_TEST";
	/*************************************************/
	const API_USER_ID="CIT00000000000110801";
	const API_USER_PASSWORD="pcatnt2018";

	const ITEM_UNIQUE_NUMBER='12'; //assigned by TNT for own system
	
	const ROAD_SERVICE_CODE=76;
	const OVERNIGHT_EXPRESS_CODE=75;
	
	const PRODUCTION_URL='http://osms.sf-express.com/osms/services/OrderWebService?wsdl'; //web API production
	const TEST_URL='http://osms.sit.sf-express.com:2080/osms/services/OrderWebService?wsdl';    //Web API Test API
	const TRACKING_URL='http://www.tntexpress.com.au/link/Cnmhx2.asp?';   //Tracking API
	const ROUTING_URL='http://osms.sit.sf-express.com:2080/osms/services/OrderWebService?wsdl';   //GET ROUTING INFORMATION for SELF OWN SYSTEM;
	const GET_SERVICE_URL='https://www.tntexpress.com.au/Rtt/inputRequest.asp';   //RTT
	const PUSH_TRACKING_URL_TEST ='http://113.105.119.26:45579/gts-tc/api/track/receive';
	const PUSH_TRACKING_URL_PRODUCTION ='https://ibu-gts.sf-express.com/gts-tc/api/track/receive';
	const CHECKWORD = 'fc34c561a34f';

	/********************************************************/
	public $is_test=true;
	
	public $user_id;
	
	public $user_passwd;
	
	private $select_service_code='76';

	private $pushConfig = [];
	
	const collection_suburb="Bankstown Aerodrome";
	
	const collection_postcode="2200";
	
	const collection_address="1/233 Milperra Rd";
	
	const collection_state="NSW";
	private $working_url = '';
	private $working_push_url = '';
	private $thisCheckWord = '';
	private $OSMS = '';
	private $production_pushing_key = '351cde0df584ef55241b21d2f7d1b1a9228593fb93c048cffbf85332f6eef965';
	private $dev_pushing_key = '0096ba20413b9cc747e5b5ebd2cc70071b75801b8bb6f42d0537eb6f8a2e18e1';
	
	const CONSIGNMENT_FINAL_YES='Y';
	const CONSIGNMENT_FINAL_NO='N';

	public function __construct($is_test=true,$isPushing = false,$pushType='',$orgRate= false,$type = 'syd')
	{
		if($is_test)
		{
			$this->working_url = self::TEST_URL;
			$this->working_push_url = self::PUSH_TRACKING_URL_TEST;
			$this->thisCheckWord = 'fc34c561a34f';
			$this->custid = '0010002117';
			$this->OSMS = 'OSMS_1';
			$this->is_test = $is_test;
		}else
		{
			$this->working_url = self::PRODUCTION_URL;
			$this->working_push_url = self::PUSH_TRACKING_URL_PRODUCTION;

			if(empty($type)||$type!='syd_truck')
			{
				$this->thisCheckWord = 'c31bc0ba2d4a4cef';
				$this->custid = '0610100229';
				$this->OSMS = 'OSMS_8285';
			}else
			{
				$this->thisCheckWord = '63a03467e13540be';
				$this->custid = '0610100321';
				$this->OSMS = 'OSMS_10217';
			}
			$this->is_test = $is_test;
		}

		if($isPushing)
		{
			$pushingKey = "";
			if($is_test)
			{
				$pushingKey = $this->dev_pushing_key;
			}else
			{
				$pushingKey = $this->production_pushing_key;
			}
			if($pushType==Org::PCAE_DEPARTMENT_SYDNEY)
			{
				$this->pushConfig = [
					'state'=>'NEW SOUTH WALES',
					'country'=>'AU',
					'sysCode'=>'TLA-AU',
					'sign'=>$pushingKey,
					'billNoType'=>1

				];
			}elseif($pushType==Org::PCAE_DEPARTMENT_MELBOURNE)
			{
				$this->pushConfig = [
					'state'=>'VICTORIA',
					'country'=>'AU',
					'sysCode'=>'TLA-AU',
					'sign'=>$pushingKey,
					'billNoType'=>1
				];
			}elseif($pushType==Org::PCAE_DEPARTMENT_BRISBANE)
			{
				$this->pushConfig = [
					'state'=>' QUEENSLAND',
					'country'=>'AU',
					'sysCode'=>'TLA-AU',
					'sign'=>$pushingKey,
					'billNoType'=>1

				];
			}
		}
	}
	
	/**
	 * create shipment for self own system;
	 * @param type $shipment
	 */
	public function createOneShipment($shipment,$isUpdate = false)
	{
		$reply=['status'=>true,'msg'=>'','data'=>[]];
		$xml = $this->prepareOrderData($shipment,$isUpdate);
		$data=base64_encode($xml);
		$validateStr = base64_encode(md5($xml.$this->thisCheckWord, false));
		$pmsLoginAction = $this->working_url;
		$client = new SoapClient ($pmsLoginAction);
		$client->__getFunctions();
		$client->__getTypes();
		$this->log2file(date('Y-m-d H:i:s').'_Request:'.$xml."\n");
		$result=$client->sfexpressService(array('data'=>$data,'validateStr'=>$validateStr,'customerCode'=>$this->OSMS));
		$this->log2file(date('Y-m-d H:i:s').'_Response:'.json_encode($result)."\n");
		$return = $result->Return;
		$m = [];
		if(preg_match('/<customerOrderNo>(.*)<\/customerOrderNo>/i', $return,$m))
		{
			if(isset($m[1])&&$m[1]==$shipment->hbn)
			{
				$n = [];
				if(preg_match('/<mailNo>(.*)<\/mailNo>/i', $return,$n))
				{
					$ref = $n[1];
					$barcode = $ref;
					if($shipment->pkg>1)
					{
						$ref = explode(',', $ref)[0];
					}
					$l = [];
					preg_match('/<printUrl>(.*)<\/printUrl>/i', $return,$l);
					$printUrl = $l[1];
					$inv = [];
					preg_match('/<invoiceUrl>(.*)<\/invoiceUrl>/i', $return,$inv);
					$invoiceUrl = $inv[1];
					$data = ['ref'=>$ref,'barcode'=>$barcode,'printUrl'=>$printUrl,'invoiceUrl'=>$invoiceUrl];
					$reply['data'] = $data;
				}else
				{
					$reply['status'] = false;
					$reply['msg'] = json_encode($result);
				}
			}else
			{
				$reply['status'] = false;
				$reply['msg'] = json_encode($result);
			}
		}else
		{
			$reply['status'] = false;
			$reply['msg'] = json_encode($result);
		}
		if($this->is_test)
		{
			print_r($result);
		}
		// node2array
		// <Response service="OrderWebService"><Head>OK</Head><Body><OrderResponse><customerOrderNo>TCN1478035825</customerOrderNo><mailNo>070050206177,001003395124</mailNo><originCode>SYD</originCode><destCode>SYD</destCode><printUrl>http://osms.sit.sf-express.com:2080/osms/wbs/print/printOrder.pub?mailno=joZnecBN1szcwT0/FLdlXRDpWbyu51rZw0eB5E51M7E=</printUrl><invoiceUrl>http://osms.sit.sf-express.com:2080/osms/wbs/print/printInvoice.pub?mailno=joZnecBN1szcwT0/FLdlXRDpWbyu51rZw0eB5E51M7E=</invoiceUrl></OrderResponse></Body></Response>
		return $reply;
	}	

	public function create_guid($namespace = '') {  
		  $guid = '';
		  $uid = uniqid("", true);
		  $data = $namespace;
		  $data .= $_SERVER['REQUEST_TIME'];
		  $data .= $_SERVER['HTTP_USER_AGENT'];
		  $data .= $_SERVER['REMOTE_ADDR'];
		  $data .= $_SERVER['REMOTE_PORT'];
		  $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
		  $guid = substr($hash, 0, 8) .
		      '-' .
		      substr($hash, 8, 4) .
		      '-' .
		      substr($hash, 12, 4) .
		      '-' .
		      substr($hash, 16, 4) .
		      '-' .
		      substr($hash, 20, 12);
		  return $guid;
	}

	public function getSFRef($type)
	{
		$v = $this->create_guid();
		Yii::app()->db->createCommand('UPDATE imports_ref_pool SET status ="'.$v.'" where org_id = 3590 and type = '.$type.' and status = 0 limit 1')->execute();
		$ref = ImportsRefPool::model()->find("status = '{$v}'");
		$ref->status = "1";
		$ref->save();
		if(!empty($ref))
		{
			return $ref->ref;
		}else
		{
			return "";
		}
	}

	public function getShipmentRefs($pkg)
	{
		$mainRef = "";
		$subRef = [];
		$count = 0;
		for ($i=0; $i < $pkg; $i++) { 
			if($i==0)
			{
				$mr = $this->getSFRef(2);
				if(!empty($mr))
				{
					$count++;
					$mainRef = $mr;
				}
			}else
			{
				$sr = $this->getSFRef(1);
				if(!empty($sr))
				{
					$count++;
					$subRef[] = $sr;
				}
			}
		}

		return ["mainRef"=>$mainRef,"subRef"=>$subRef,"count"=>$count];
	}
	/**
	 * create shipment for self own system;
	 * @param type $shipment
	 */
	public function updateShipment($shipment)
	{
		return $this->createOneShipment($shipment,1);
	}

	public function prepareOrderData($shipment,$isUpdate = false)
	{
		try
		{
		$parameters = [];
		$body=[];
		$body['Order'] = ['myAttributes'=>["order_id"=>$shipment->hbn,"mailno"=>$shipment->mdata['barcode'],"is_gen_bill_no"=>"0","reference_no1"=>$shipment->hbn,"express_type"=>"606","custid"=>$this->custid,"j_contact"=>'Top Logistics',"j_tel"=>"0299257111","j_address"=>self::collection_address,"j_province"=>self::collection_state,"j_city"=>self::collection_suburb,"j_county"=>"","j_country"=>"AU","j_post_code"=>self::collection_postcode,"d_company"=>$shipment->cnee->company."", "d_contact"=>$shipment->cnee->name."","d_tel"=>$shipment->cnee->tel."","d_mobile"=>$shipment->cnee->tel."","d_address"=>$shipment->cnee->address."","d_province"=>$shipment->cnee->state."","d_city"=>$shipment->cnee->suburb."","d_county"=>"","d_country"=>"AU","d_post_code"=>$shipment->cnee->postcode."","currency"=>"AUD","realweightqty"=>"".$shipment->weight,"parcel_quantity"=>"".$shipment->pkg,"pay_method"=>"1","tax"=>'DDU',"tax_pay_type"=>"2","printSize"=>"1"]];
		foreach ($shipment->eitems['g'] as $key => $item) {
			$cargo = ["name"=>$shipment->eitems['g'][$key],"count"=>$shipment->eitems['q'][$key]."","currency"=>"AUD","amount"=>round($shipment->eitems['q'][$key]*$shipment->eitems['v'][$key],2)."","unit"=>"pcs","source_area"=>"AU"];
			$body['Order']['Cargo|'.$key] = ['myAttributes'=>$cargo];
		}

		if(!empty($isUpdate))
		{
			$body['Order']['myAttributes']['operate_type'] = 2;
		}

		$parameters['Request'] = ['myAttributes'=>["service"=>"apiOrderService","lang"=>"en"],"Head"=>$this->OSMS,"Body"=>$body];
		$xmlCreator = new XMLCreator();
		$xml = $xmlCreator->createSF($parameters);
		if($this->test)
		{
			echo $xml;
		}
			return $xml;
		}catch(Exception $e)
		{
			$this->log2file("Error:".$e->getMessage());
			return false;
		}
	}

	public function prepareTrackingData($refs)
	{
		$ref = "";
		$ref = join(',',$refs);
		$parameters = [];
		$body=[];
		$body['Route'] = ['myAttributes'=>["tracking_type"=>1,"tracking_number"=>$ref]];
		$parameters['Request'] = ['myAttributes'=>["service"=>"RouteService","lang"=>"en"],"Head"=>$this->OSMS,"Body"=>$body];
		$xmlCreator = new XMLCreator();
		$xml = $xmlCreator->createSF($parameters);
		return $xml;
	}

	public function getTrackingEvents($refs)
	{
		$reply=['status'=>true,'msg'=>'','data'=>[]];
		$xml = $this->prepareTrackingData($refs);
		$data=base64_encode($xml);
		$validateStr = base64_encode(md5($xml.$this->thisCheckWord, false));
		$pmsLoginAction = $this->working_url;
		$client = new SoapClient ($pmsLoginAction);
		$client->__getFunctions();
		$client->__getTypes();
		$this->log2file(date('Y-m-d H:i:s').'_Request:'.$xml."\n");
		$result=$client->sfexpressService(array('data'=>$data,'validateStr'=>$validateStr,'customerCode'=>$this->OSMS));
		$this->log2file(date('Y-m-d H:i:s').'_Response:'.json_encode($result)."\n");
		$return = $result->Return;
		$return = str_replace("\"", '"', $return);
		$return = str_replace("\t", ' ', $return);
		$return = str_replace("\/", '/', $return);
		$dom = DOMDocument::loadXML($return);
		$elements = $dom->getElementsByTagName("Route");
		$element = 1;
		$trackingData = [];
		$i = 0;
		while(!empty($element))
		{
			$element = $elements->item($i);
			if(empty($element))
			{
				break;
			}
			if ($element->hasAttributes()){
		        $acceptTime = $element->getAttribute("accept_time");
		        $acceptAddress = $element->getAttribute("accept_address");
		        if(empty($acceptAddress))
		        {
		        	$acceptAddress = "";
		        }
		        $remark = $element->getAttribute("remark");
		        $opcode = $element->getAttribute("opcode");
		        $trackingData[] = [$acceptTime,$acceptAddress,$remark,$opcode];
		    }
			$i++;
		}
		return $trackingData;
	}

	public function pushTrackingEvents($refs)
	{
		$reply=['status'=>true,'msg'=>'','data'=>[]];
		$xml = $this->prepareTrackingData($refs);
		$data=base64_encode($xml);
		$validateStr = base64_encode(md5($xml.$this->thisCheckWord, false));
		$pmsLoginAction = $this->working_url;
		$client = new SoapClient ($pmsLoginAction);
		$client->__getFunctions();
		$client->__getTypes();
		$this->log2file(date('Y-m-d H:i:s').'_Request:'.$xml."\n");
		$result=$client->sfexpressService(array('data'=>$data,'validateStr'=>$validateStr,'customerCode'=>$this->OSMS));
		$this->log2file(date('Y-m-d H:i:s').'_Response:'.json_encode($result)."\n");
		$return = $result->Return;
		$return = str_replace("\"", '"', $return);
		$return = str_replace("\t", ' ', $return);
		$return = str_replace("\/", '/', $return);
		$dom = DOMDocument::loadXML($return);
		$elements = $dom->getElementsByTagName("Route");
		$element = 1;
		$trackingData = [];
		$i = 0;
		while(!empty($element))
		{
			$element = $elements->item($i);
			if(empty($element))
			{
				break;
			}
			if ($element->hasAttributes()){
		        $acceptTime = $element->getAttribute("accept_time");
		        $acceptAddress = $element->getAttribute("accept_address");
		        if(empty($acceptAddress))
		        {
		        	$acceptAddress = "";
		        }
		        $remark = $element->getAttribute("remark");
		        $opcode = $element->getAttribute("opcode");
		        $trackingData[] = [$acceptTime,$acceptAddress,$remark,$opcode];
		    }
			$i++;
		}
		return $trackingData;
	}
	
	public function getCost($rate_id=ImportChargeCode::SF_SYDNEY_ID,$shipment)
	{
		$orgRate = OrgRate::model()->findByPk($rate_id);
		$price = 0;
		$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>Org::ORGID_COURIER_SF ,':zoneid' => $orgRate->zone_id,':code' => $shipment->postcode]);
		$chargeCode = 'xxxxxx';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$chargeCode = $zoneMap['z1'];
		}
		$weight=$shipment->weight;
		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $shipment->weight, ':rateid' => $rate_id]);
		if (!empty($zrs)) {
			// get maximum one
			foreach ($zrs as $zr) {
				$temp = $zr['base'] + $zr['item'];
				if ($zr['nkg'] > 0) {
					$wl = $weight- ($zr['base'] > 0 ? $zr['nkg'] : 0);
					$temp += ceil($wl / $zr['nkg']) * $zr['perkg'];
				} else {
					$temp +=  $weight * $zr['perkg'];
				}
				if ($zr['minimum'] > 0 && $temp < $zr['minimum']) {
					$temp = $zr['minimum'];
				}
				$price = max($price, $temp);
			}
		}
		return $price;
	}

	public static function getCode($shipment)
	{
		$orgRate = OrgRate::model()->findByPk(ImportChargeCode::SF_CODE_ID);
		$price = 0;
		$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>Org::ORGID_COURIER_SF ,':zoneid' => $orgRate->zone_id,':code' => $shipment->postcode]);
		$chargeCode = '';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$chargeCode = $zoneMap['z1'];
		}
		return $chargeCode;
	}
	
	private function moveFile($fileName)
	{
		$lf=Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'tntapi' . DIRECTORY_SEPARATOR.'manifest_hitory'.DIRECTORY_SEPARATOR;
		if (!file_exists($lf)) {
			mkdir($lf);
		}
		rename($fileName, $lf.basename($fileName));
	}

	public function request($action, $method='POST', $data=null)
	{
		$this->result=false;
		$url= $action;
		$c=new curl($url);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_TIMEOUT, 600);
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		if(empty($data))
        {
          $hdr = $this->build_headers($method, $url,'application/json',null);
        }else
        {
          $hdr = $this->build_headers($method, $url,'application/json',AppHelper::safeJsonEncode($data)); 
        }
		if (!empty($data)) {
			$data = AppHelper::safeJsonEncode($data);
			$c->setopt(CURLOPT_POSTFIELDS, $data);
		}
		// var_dump($hdr);
		$c->setopt(CURLOPT_HTTPHEADER, $hdr);
		if (!$c->exec()) {
			throw new Exception('cUrl Error: '.$c->err);
		}
		$this->rescode=$c->rcode;
		$this->result = json_decode($c->result);
		$this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".urldecode($data)."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result.PHP_EOL.PHP_EOL);
		return $this->result;
	}

	private function build_headers($method, $path, $acceptType='application/json',$data)
    {
        return [
            'Content-Length:'.strlen($data),
            'Content-Type:application/json; charset=utf-8',
        ];
    }
     

	/**
		 * Log the Request and Response to the file
		 *
		 */
	private function log2file($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'sfapi' . DIRECTORY_SEPARATOR;
		if (!file_exists($lf)) {
			mkdir($lf);
		}
		$lf .= 'sfapi_'.date('Y_m_d').'.log';
		return file_put_contents($lf, $m, FILE_APPEND);
	}

	private function log2filerout($m)
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'sfapi' . DIRECTORY_SEPARATOR;
		if (!file_exists($lf)) {
			mkdir($lf);
		}
		$lf .= 'sf_routing_' . date('Ym') . '.log';
		return file_put_contents($lf, $m, FILE_APPEND);
	}

	public function getSFStationInfo($suburb,$state,$cneeAddr=false)
	{
		$obj = null;
		$stateArr = ['NSW'=>'NEW SOUTH WALES','VIC'=>'VICTORIA','QLD'=>'QUEENSLAND','NT'=>'NORTHERN TERRITORY','WA'=>'WESTERN AUSTRALIA','SA'=>'SOUTH AUSTRALIA','TAS'=>'TASMANIA','ACT'=>'AUSTRALIAN CAPITAL TERRITORY'];
		if(!empty($suburb))
		{
			$obj = SfStationInfo::model()->find('country=:country and state=:state and suburb=:suburb',[":country"=>$this->pushConfig['country'],":state"=>$this->pushConfig['state'],":suburb"=>$suburb]);
			if(empty($obj))
			{
				$state = $stateArr[$state];
				$obj = SfStationInfo::model()->find('country=:country and state=:state and suburb=:suburb',[":country"=>$this->pushConfig['country'],":state"=>$state,":suburb"=>$suburb]);
			}
		}

		if(empty($obj))
		{
			$state = $stateArr[$cneeAddr->state];
			$obj = SfStationInfo::model()->find('country=:country and state=:state and suburb=:suburb',[":country"=>$this->pushConfig['country'],":state"=>$state,":suburb"=>$cneeAddr->suburb]);
		}
		return $obj;
	}

	private function preparePushTrackingData($shipment,$trackings)
	{
		$obj = new stdClass();
		$obj->sysCode = $this->pushConfig['sysCode'];
		$obj->sign = $this->pushConfig['sign'];
		$obj->billNoType = $this->pushConfig['billNoType'];
		$obj->data = [];
		$ddpt = Org::model()->findByPk($shipment->ddpt_id);
		foreach ($trackings as $key => $tracking) {
			$suburb = "";

			if(in_array(trim($tracking->activity),['Courier dispatch information received','Courier dispatch information approved','Received and ready for processing']))
			{
				$tracking->depot = strtoupper($ddpt->suburb." ".$ddpt->state);
			}

			if(trim($tracking->activity)=='Courier dispatch information received')
			{
				$tracking->activity = 'Shipping information received by Australia Post';
			}

			if(trim($tracking->activity)=='Courier dispatch information approved')
			{
				$tracking->activity = 'Shipping information approved by Australia Post';
			}

			if(empty($tracking->depot))
			{
				if(preg_match('/In transit to next facility in /', $tracking->activity))
				{
					$suburb = explode('In transit to next facility in ', $tracking->activity);
					if(!empty($suburb[1]))
					{
						$suburb = $suburb[1];
					}
				}
			}
			$suburb = empty($suburb)?$tracking->depot:$suburb;
			$oSuburb = $suburb;
			$suburb = $suburb."|";
			$suburb = str_replace(' NSW|', '', $suburb);
			$suburb = str_replace(' VIC|', '', $suburb);
			$suburb = str_replace(' QLD|', '', $suburb);
			$suburb = str_replace(' NT|', '', $suburb);
			$suburb = str_replace(' WA|', '', $suburb);
			$suburb = str_replace(' SA|', '', $suburb);
			$suburb = str_replace(' TAS|', '', $suburb);
			$suburb = str_replace('|', '', $suburb);
			if(empty($suburb)&&$tracking->type==90) $suburb = $shipment->cnee->suburb;
			$stationInfo = $this->getSFStationInfo($suburb,$shipment->cnee->state,$shipment->cnee);
			// echo  $tracking->activity;
			$orgRate = OrgRate::model()->findByPk($shipment->mdata['org_rate_id']);
			$sss = SfTrackingRelationInfo::model()->findAll('courier_id = :orgId',[":orgId"=>$orgRate->org_id]);
			$stRelation = false;
			$opCode = 0;
			$reasonCode = '';
			foreach ($sss as $key => $sr)
			{
				$checkTrackingCode = str_replace("'", "\'", $sr->tracking_code);
				$checkTrackingCode = str_replace("/", "\/",$checkTrackingCode);
				$checkActivity = str_replace("'", "\'",$tracking->activity);
				$checkActivity = str_replace("/", "\/",$checkActivity);
				if(preg_match('/'.$checkTrackingCode.'/i', $checkActivity)||preg_match('/'.$checkActivity.'/i', $checkTrackingCode))
				{
					$stRelation = $sr;
					break;
				}
			}

			if($tracking->type!=70&&empty($stRelation)) continue;
			if(!empty($stRelation))
			{
				$opCode =$stRelation->op_code;
				if($stRelation->reason_code!='')
				{
					$reasonCode = $stRelation->reason_code;
				}
			}else
			{
				$opCode = 98;
			}
			$d = new stdClass();
			$d->opCode = $opCode;
			if($reasonCode!='')
			{
				$d->reasonCode = $reasonCode;
			}
			$d->sfWaybillNo = $shipment->cref;
			if(empty($stationInfo))
			{
				continue;
			}
			$d->zoneCode = $stationInfo->op_station;
			if(empty($stationInfo->op_id)) print_r($stationInfo);
			$d->barOprCode = $stationInfo->op_id;
			$d->localTm = $tracking->dt;
			$d->gmt = "GMT+10";
			$d->agentTrackRemark = $tracking->activity;
			$d->trackAddr=$oSuburb;
			if($d->opCode==70&&$d->reasonCode==222)
			{
				$station = explode('Awaiting collection at ', $tracking->activity);
				if(!empty($station[1]))
				{
					$station = $station[1];
				}else
				{
					$station = "";
				}
				$d->Location = $station;
			}

			if(in_array($d->opCode,[208,139]))
			{
				$d->agentWayBillNo = $shipment->ref;
			}
			$obj->data[] = $d;
		}
		return $obj;
	}

	private function preparePushCustomsData($shipment,&$trackings)
	{
		$obj = new stdClass();
		$obj->sysCode = $this->pushConfig['sysCode'];
		$obj->sign = $this->pushConfig['sign'];
		$obj->billNoType = $this->pushConfig['billNoType'];
		$obj->data = [];
		$ddpt = Org::model()->findByPk($shipment->consol->dpt_id);
		foreach ($trackings as $key => $tracking) {
			$tracking->mdata['no_sync'] = 1;
			if($obj->data ===1) break;
			$suburb = "";
			$tracking->depot = strtoupper($ddpt->suburb." ".$ddpt->state);
			$suburb = empty($suburb)?$tracking->depot:$suburb;
			$oSuburb = $suburb;
			$suburb = $suburb."|";
			$suburb = str_replace(' NSW|', '', $suburb);
			$suburb = str_replace(' VIC|', '', $suburb);
			$suburb = str_replace(' QLD|', '', $suburb);
			$suburb = str_replace(' NT|', '', $suburb);
			$suburb = str_replace(' WA|', '', $suburb);
			$suburb = str_replace(' SA|', '', $suburb);
			$suburb = str_replace(' TAS|', '', $suburb);
			$suburb = str_replace('|', '', $suburb);
			if(empty($suburb)&&$tracking->type==90) $suburb = $shipment->cnee->suburb;
			$stationInfo = new stdClass();
			$stationInfo->op_station = 'SYD02A';
			$stationInfo->op_id = '93341216';
			// echo  $tracking->activity;
			$sss = SfTrackingRelationInfo::model()->findAll('courier_id = :orgId',[":orgId"=>0]);
			$stRelationPrimary = false;
			$stRelationOther = false;
			$opCode = 0;
			$reasonCode = '';
			$dt = '';
			$customsCases = [31=>[1=>'unpacking'],14=>[1=>'cleared'],15=>[1=>'border'],16=>[301=>'high value',309=>'Deficient Info(高价值清关资料不齐)'],612=>[309=>'Deficient Info (申报价值1000AUD以下包裹资料不齐)'],18=>[350=>'AQIS',312=>'EMPP',309=>'要求提供其他的清关资料;'],613=>[330=>'清关已完成，待客户支付税金']];

			if($tracking->type==Tracking::TYPE_DECONSOLIDATION)
			{
				$stRelationPrimary = new stdClass();
				$stRelationPrimary->op_code = 31;
				$dt = $tracking->dt;
				$tracking->mdata['dt'] = $dt;
			}
			else
			{
				if(preg_match('/Customs held/i',$tracking->activity))
				{
					$clStatus = Tracking::model()->find('pid =:pid and type=:type and id>:id',[":pid"=>$shipment->id,':type'=>Tracking::TYPE_CLEARED,':id'=>$tracking->id]);
					$stRelationPrimary = new stdClass();
					$m = [];
					preg_match('/Customs held\((.*)\)/i', $tracking->activity,$m);
					$reason = $m[1];
					$rsm = explode(';', $reason);
					if(strtoupper($rsm[0])=='HIGH VALUE')
					{
						$stRelationPrimary = new stdClass();
						$stRelationPrimary->op_code = 16;
						$stRelationPrimary->reason_code = 309;

						if(count($rsm)>1&&!empty($rsm[1]))
						{
							$stRelationOther = new stdClass();
							$stRelationOther->op_code = 16;
							$stRelationOther->reason_code = 309;
						}
					}

					if(strtoupper($rsm[0])=='AQIS')
					{
						$stRelationPrimary = new stdClass();
						$stRelationPrimary->op_code = 18;
						$stRelationPrimary->reason_code = 350;

						if(count($rsm)>1&&!empty($rsm[1]))
						{
							$stRelationOther = new stdClass();
							$stRelationOther->op_code = 18;
							$stRelationOther->reason_code = 309;
						}
					}

					if(strtoupper($rsm[0])=='BORDER')
					{
						$stRelationPrimary = new stdClass();
						$stRelationPrimary->op_code = 15;
					}

					if(strtoupper($rsm[0])=='EMPP')
					{
						$stRelationPrimary = new stdClass();
						$stRelationPrimary->op_code = 18;
						$stRelationPrimary->reason_code = 312;

						if(count($rsm)>1&&!empty($rsm[1]))
						{
							$stRelationOther = new stdClass();
							$stRelationOther->op_code = 18;
							$stRelationOther->reason_code = 309;
						}
					}

					if(!empty($clStatus))
					{
						$stRelationPrimary->op_code = null;
					}

				}

				if(preg_match('/Clearance processing complete/i',$tracking->activity))
				{
					$stRelationPrimary = new stdClass();
					$stRelationPrimary->op_code = 14;
					$handOverStatus = Tracking::model()->find('pid =:pid and type=:type',[":pid"=>$shipment->id,':type'=>Tracking::TYPE_HAND_OVER]);
					if(empty($handOverStatus))// if there is not hand over status, then don't sync
					{
						$tracking->mdata['no_sync'] = 1;
						continue;
					}else
					{
						$tracking->dt = $handOverStatus->dt;
					}


				}

				$deStatus = Tracking::model()->find('pid =:pid and type=:type and json_value(meta,"$.sync_sf")=1 and json_value(meta,"$.sync_time") is not null',[":pid"=>$shipment->id,':type'=>Tracking::TYPE_DECONSOLIDATION]);
				if($tracking->type==Tracking::TYPE_CLEARED)
				{
					$heldStatus = Tracking::model()->find('pid =:pid and type=:type ',[":pid"=>$shipment->id,':type'=>Tracking::TYPE_HELD]);
					if(!empty($heldStatus))
					{
						if(empty($heldStatus->mdata['sync_time']))
						{
							$tracking->mdata['no_sync'] = 1;
							continue;
						}else
						{
							$deStatus = $heldStatus;
						}
					}
				}elseif ($tracking->type==Tracking::TYPE_HELD) {
					if(empty($deStatus))// means when there is not deconsolidation, then don't sync held status
					{
						$tracking->mdata['no_sync'] = 1;
						continue;
					}
				}
				if(!empty($deStatus))
				{
					$deSyncTime = $deStatus->mdata['sync_time'];
					$gap = HolidayHelper::checkBetweenMinutes(date('Y-m-d H:i:s'),$deStatus->mdata['sync_time']);
					if($gap<5)//is the deconcolidation time between now is less than 5 hours, don't sync
					{
						$tracking->mdata['no_sync'] = 1;
						continue;
					}

					//if larger than 5 hours
					$dt = date("Y-m-d H:i:s",strtotime($deStatus->mdata['dt']." +5 minutes"));
					if(strtotime($dt)<strtotime($tracking->dt))
					{
						$dt = $tracking->dt;
					}
					$tracking->mdata['dt'] = $dt;
				}else
				{
					continue;
				}
			}

			$stRelations = [$stRelationPrimary,$stRelationOther];
			$opCode = 0;
			$reasonCode = 0;
			foreach ($stRelations as $key => $stRelation)
			{
				if(empty($stRelation))
				{
					continue;
				}else
				{
					if(empty($stRelation->op_code))
					{
						$obj->data = 1;
						$tracking->mdata['break'] = 1;
						$tracking->mdata['no_sync'] = 0;
						break;
					}

					$opCode =$stRelation->op_code;
					if($stRelation->reason_code!='')
					{
						$reasonCode = $stRelation->reason_code;
					}
				}

				$pbs = ParcelBarcode::model()->findAll('fid = :fid',[":fid"=>$shipment->id]);
				$d = new stdClass();
				$d->opCode = $opCode;
				if($reasonCode!='')
				{
					$d->reasonCode = $reasonCode;
				}
				$d->sfWaybillNo = $shipment->hbn;
				$d->zoneCode = $stationInfo->op_station;
				$d->barOprCode = $stationInfo->op_id;
				$d->localTm = $dt;
				$d->gmt = "GMT+10";
				$d->agentTrackRemark = $tracking->activity;
				$d->trackAddr=$oSuburb;
				$obj->data[] = $d;
				$tracking->mdata['no_sync'] = 0;

				foreach ($pbs as $key => $pb)
				{
					if($pb->sub_ref==$shipment->hbn) continue;
					$d = new stdClass();
					$d->opCode = $opCode;
					if($reasonCode!='')
					{
						$d->reasonCode = $reasonCode;
					}
					$d->sfWaybillNo = $pb->sub_ref;
					$d->zoneCode = $stationInfo->op_station;
					$d->barOprCode = $stationInfo->op_id;
					$d->localTm = $dt;
					$d->gmt = "GMT+10";
					$d->agentTrackRemark = $tracking->activity;
					$d->trackAddr=$oSuburb;
					$obj->data[] = $d;
				}

			}
		}
		return $obj;
	}

	public function pushTrackingDataGTS($shipment,$trackingData)
	{
		$action = $this->working_push_url;
		$da = $this->preparePushTrackingData($shipment,$trackingData);
		if(empty($da->data))
		{
			return false;
		}
		$result = $this->request($action,'POST', $da);
		if($result->success)
		{
				$trans = Yii::app()->db->beginTransaction();
				try 
				{
					foreach ($trackingData as $key => $track) {
						//if(!empty($track->mdata['sync_sf'])) continue;
						$track->mdata['sync_sf'] = 1;
						$track->updateMeta();
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
		}
		return $result;
	}

	public function pushCustomsDataGTS($shipment,$trackingData)
	{
		$action = $this->working_push_url;
		$da = $this->preparePushCustomsData($shipment,$trackingData);
		if(empty($da->data))
		{
			return false;
		}
		if($da->data!==1)
		{
			$result = $this->request($action,'POST', $da);
		}
		if($result->success||$da->data===1)
		{
				$trans = Yii::app()->db->beginTransaction();
				try 
				{
					foreach ($trackingData as $key => $track) {
						if($track->mdata['no_sync']) continue;
						//if(!empty($track->mdata['sync_sf'])) continue;
						$track->mdata['sync_sf'] = 1;
						$track->mdata['sync_time'] = date('Y-m-d H:i:s');
						$track->updateMeta();
						if($track->mdata['break']) break;
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
		}
		return $result;
	}
	
	
}
