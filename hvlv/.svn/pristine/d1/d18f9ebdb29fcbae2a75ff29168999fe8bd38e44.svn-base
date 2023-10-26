<?php
class DFEAPI
{
	const ACCOUNT_NUMBER='24683';
	const ACCOUNT_NUMBER_TOP='24751';
	const ACCOUNT_NUMBER_TOP_MEL='34586';
	const ACCOUNT_NUMBER_TOP_BNE='43572';
	const CONSIGNMENT_PREFIX='24683'.'1';
	const CONSIGNMENT_PREFIX_TOP='24751'.'1';
	const CONSIGNMENT_PREFIX_TOP_MEL='34586'.'1';
	const CONSIGNMENT_PREFIX_TOP_BNE='43572'.'1';
	const API_KEY = '40FE79EC-CF5B-4222-AA33-8D41B0C6AB6D';
	const API_KEY_TOP = '30FA6177-CCEB-4DD5-87CF-0379278A0049';
	const API_KEY_TOP_MEL = '1945B3B3-1D46-4BBD-AB1C-2421697F1E36';
	const API_KEY_TOP_BNE = '8CE3431D-4ADE-4EF7-BDA8-3BC2179902E2';
	public $ACCOUNT_NUMBER = '';
	public $CONSIGNMENT_PREFIX = '';
	public $API_KEY = '';
	public $ORG_ID = 0;
	const TRACKING_URL = 'https://webservices.directfreight.com.au/Dispatch/api/TrackConsignment/';

	public function __construct($type=false)
	{
		if(empty($type)||$type=='syd')
		{
			$this->ACCOUNT_NUMBER = self::ACCOUNT_NUMBER;
			$this->CONSIGNMENT_PREFIX = self::CONSIGNMENT_PREFIX;
			$this->API_KEY = self::API_KEY;
			$this->ORG_ID = Org::ORGID_COURIER_DFE;
		}

		if(!empty($type)&&$type=='syd_top')
		{
			$this->ACCOUNT_NUMBER = self::ACCOUNT_NUMBER_TOP;
			$this->CONSIGNMENT_PREFIX = self::CONSIGNMENT_PREFIX_TOP;
			$this->API_KEY = self::API_KEY_TOP;
			$this->ORG_ID = Org::ORGID_COURIER_DFE_TOP;
		}elseif(!empty($type)&&$type=='mel_top')
		{
			$this->ACCOUNT_NUMBER = self::ACCOUNT_NUMBER_TOP_MEL;
			$this->CONSIGNMENT_PREFIX = self::CONSIGNMENT_PREFIX_TOP_MEL;
			$this->API_KEY = self::API_KEY_TOP_MEL;
			$this->ORG_ID = Org::ORGID_COURIER_DFE_TOP;
		}elseif(!empty($type)&&$type=='bne_top')
		{
			$this->ACCOUNT_NUMBER = self::ACCOUNT_NUMBER_TOP_BNE;
			$this->CONSIGNMENT_PREFIX = self::CONSIGNMENT_PREFIX_TOP_BNE;
			$this->API_KEY = self::API_KEY_TOP_BNE;
			$this->ORG_ID = Org::ORGID_COURIER_DFE_TOP;
		}

	}
		
		
	public function genDFEConsignmentNo()
	{
		return ConnoteRange::newNumber("Org", $this->ORG_ID, $this->CONSIGNMENT_PREFIX);
	}
	 
	private function getFileName()
	{
		return 'MANIFEST_'.$this->ACCOUNT_NUMBER."_".date('YmdHis').'.xml';
	}

	public function manifest($ss)
	{
		try {
			$data=$this->prepareData($ss);

			$filename = $this->getFileName();
			$path = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'DFE' . DIRECTORY_SEPARATOR;
			$xmlCreator = new XMLCreator();
			$fullFilename = $path.$filename;
			$xmlCreator->create($fullFilename,$data);
			$result = $this->sendManifestEmail($fullFilename,$filename);
			$result['filename'] = $filename;
			return $result;
		} catch (Exception $ex) {
			throw $ex;
			return array("status"=>false);
		}
	}

	public function sendManifestEmail($fullFilename,$filename)
	{
		$email = 'sydcons@directfreight.com.au';
		$emailLog = new Emailog();
		$emailLog->to_id = $this->ORG_ID;
		$emailLog->fid = 0;
		$emailLog->type= Emailog::DFE_API_TEMPLATE;
		$emailLog->dt = date('Y-m-d H:i:s');
		$emailLog->status = 10;
		$emailLog->prepTemplate();
		$emailLog->tpl->assignSubject('TITLE', $filename);
		// $emailLog->tpl->assignThese([
		// 	'FILENAME'=>$filename,
		// ]);
		$emailLog->subject = $emailLog->tpl->subject;
		$emailLog->body = $emailLog->tpl->getContent();
		$emailLog->mdata['to']=$email;
		$emailLog->mdata['fromName']='TLA Imports';
		$emailLog->mdata['from']='imports@toplogistics.com.au';
		$emailLog->mdata['cc']='imports@toplogistics.com.au';
		$o=$emailLog->sendEmail([[$fullFilename,$filename]]);
		return $o;
	}
	public function canDeliver($shipment,$orgRateId = 710)
	{
		$zoneMap = DfeZone::model()->find('postcode = :postcode AND state = :state AND postcodename = :suburb', [':postcode' =>$shipment->cnee->postcode ,':state' => strtoupper($shipment->cnee->state),':suburb' => strtoupper($shipment->cnee->suburb)]);
		if (!empty($zoneMap)) {
			$chargeCode = $zoneMap->carrierzone;
			$postcodename = $zoneMap->postcodename;
		}else{
			$this->log2file('Time: '.date('Y-m-d H:i:s')."check Can Deliver Cnee address-".$shipment->cnee->address.",".$shipment->cnee->suburb.",".$shipment->cnee->state.",".$shipment->cnee->postcode.","."\nResponse: no service for this postcode".PHP_EOL.PHP_EOL);
			return false;
		}


		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode."_".$postcodename, ':w' => $shipment->weight, ':rateid' => $orgRateId]);
		if(empty($zrs))
		{
			$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $shipment->weight, ':rateid' => $orgRateId]);
		}

		if(empty($zrs))
		{
			$this->log2file('Time: '.date('Y-m-d H:i:s')."check Can Deliver Cnee address-".$shipment->cnee->address.",".$shipment->cnee->suburb.",".$shipment->cnee->state.",".$shipment->cnee->postcode.","."\nResponse: no rate for this service".PHP_EOL.PHP_EOL);
			return false;
		}

		if(preg_match('/'.$shipment->cnee->postcode.'/i', CargoProcess::PUREFBAPOSTCODE))
		{
			if(TntAPI::isMatchAmazon($shipment->cnee->postcode,$shipment->cnee->suburb,$shipment->cnee->address))
			{
				$this->log2file('Time: '.date('Y-m-d H:i:s')."check Can Deliver Cnee address-".$shipment->cnee->address.",".$shipment->cnee->suburb.",".$shipment->cnee->state.",".$shipment->cnee->postcode.","."\nResponse: amazon address".PHP_EOL.PHP_EOL);
				return false;
			}
		}

		$this->log2file('Time: '.date('Y-m-d H:i:s')."check Can Deliver Cnee address-".$shipment->cnee->address.",".$shipment->cnee->suburb.",".$shipment->cnee->state.",".$shipment->cnee->postcode.","."\nResponse: success".PHP_EOL.PHP_EOL);

		return true;
	}

	public static function getCost($orgRate, $suburb, $postcode, $weight)
	{
		$o = new stdClass();
		$o->code = 0;
		$o->msg="";
		$o->data = 0;

		$price = 0;
		$postcode = intval($postcode)+0;
		$zoneMap = DfeZone::model()->find('postcode = :postcode AND postcodename = :suburb', [':postcode' =>$postcode ,':suburb' => strtoupper($suburb)]);
		$chargeCode = 'N1';
		$postcodename = '';
		if (!empty($zoneMap)) {
			$chargeCode = $zoneMap->carrierzone;
			$postcodename = $zoneMap->postcodename;
		}else
		{
			$o->code = 1;
			$o->msg="no service for this postcode";
			return $o;
		}

		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode."_".$postcodename, ':w' => $weight, ':rateid' => $orgRate]);
		if(empty($zrs))
		{
			$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate]);
		}
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
				$price = max($price, $temp);
			}
		}else
		{
			$o->code = 1;
			$o->msg="no rate for this serivce";
			return $o;
		}
		$DFECost = $price;
		$forwardingFee = DfeForwardingFee::model()->find('postcode = :postcode AND suburb = :suburb AND org_id = 3460', [':postcode' =>$postcode ,':suburb' => strtoupper($suburb)]);
		if(empty($forwardingFee))
		{
			$forwardingFee = DfeForwardingFee::model()->find('postcode = :postcode AND suburb like "ALL %"  AND org_id = 3460', [':postcode' =>$postcode]);
		}
		$thisForwardingFee = 0;
		if(!empty($forwardingFee))
		{
			$thisForwardingFee = $forwardingFee->cost;
		}

		$o->msg = "success";
		$o->data = $thisForwardingFee+$DFECost;
		return $o;
	}

	public static function getRevenue($importChargecode, $suburb, $postcode, $weight)
	{
		$o = new stdClass();
		$o->code = 0;
		$o->msg="";
		$o->data = 0;

		$price = 0;
		$postcode = intval($postcode)+0;
		$zoneMap = DfeZone::model()->find('postcode = :postcode AND postcodename = :suburb', [':postcode' =>$postcode ,':suburb' => strtoupper($suburb)]);
		$chargeCode = 'N1';
		$postcodename = '';
		if (!empty($zoneMap)) {
			$chargeCode = $zoneMap->carrierzone;
			$postcodename = $zoneMap->postcodename;
		}else
		{
			$o->code = 1;
			$o->msg="no service for this postcode";
			return $o;
		}

		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND chargecode_id = :chargecode_id AND base+item+perkg > 0 ", [':s' => $chargeCode."_".$postcodename, ':w' => $weight, ':chargecode_id' => $importChargecode->id]);
		if(empty($zrs))
		{
			$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND chargecode_id = :chargecode_id AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':chargecode_id' => $importChargecode->id]);
		}
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
				$price = max($price, $temp);
			}
		}else
		{
			$o->code = 2;
			$o->msg="no rate for this serivce";
			return $o;
		}
		$DFECost = $price;
		$forwardingFee = DfeForwardingFee::model()->find('postcode = :postcode AND suburb = :suburb  AND org_id = 3460', [':postcode' =>$postcode ,':suburb' => strtoupper($suburb)]);
		if(empty($forwardingFee))
		{
			$forwardingFee = DfeForwardingFee::model()->find('postcode = :postcode AND suburb like "ALL %"  AND org_id = 3460', [':postcode' =>$postcode]);
		}
		$thisForwardingFee = 0;
		if(!empty($forwardingFee))
		{
			$thisForwardingFee = $forwardingFee->cost;
		}

		$o->msg = "success";
		$o->data = $thisForwardingFee+$DFECost;
		return $o;
	}

	public static function getCostForBorder($orgRate, $suburb, $postcode, $weight)
	{
		$o = new stdClass();
		$o->code = 0;
		$o->msg="";
		$o->data = 0;

		$price = 0;
		$zoneMap = SuburbPostcodeZone::model()->find('postcode = :postcode AND suburb = :suburb and org_id = 3447 ', [':postcode' =>$postcode ,':suburb' => strtoupper($suburb)]);
		$chargeCode = 'N1';
		$postcodename = '';
		if (!empty($zoneMap)) {
			$chargeCode = $zoneMap->bigZone;
		}else
		{
			$o->code = 1;
			$o->msg="no service for this postcode";
			return $o;
		}

		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $chargeCode, ':w' => $weight, ':rateid' => $orgRate]);
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
		}else
		{
			$o->code = 1;
			$o->msg="no rate for this serivce";
			return $o;
		}
		$DFECost = $price;
		$o->code = 0;
		$o->msg = "success";
		$o->data = $DFECost;
		return $o;
	}

	public static function getSortcode($shipment)
	{
		$zoneMap = DfeZone::model()->find('postcode = :postcode AND state = :state AND postcodename = :suburb', [':postcode' =>$shipment->cnee->postcode ,':state' => strtoupper($shipment->cnee->state),':suburb' => strtoupper($shipment->cnee->suburb)]);
		$chargeCode = 'N1';
		$postcodename = '';
		if (!empty($zoneMap)) {
			$sortcode = $zoneMap->sortcode;
			$sortcodeArr = explode(' ', $sortcode);
			$tmp = strip_tags($sortcodeArr[0]);
			$temLen = mb_strlen($tmp);
			$head = mb_substr($tmp,0,2,'utf-8');
			$withoutHead = explode($head, $sortcodeArr[0]);
			unset($withoutHead[0]);
			$middle = join($head,$withoutHead);
			$tail = $sortcodeArr[1];
			return ["head"=>$head,"middle"=>$middle,"tail"=>$tail];

		}else
		{
			return null;
		}
	}

	public function removeSpecialChars($s){
		return preg_replace(['/[^\d\w\'\"\,\;\.\/\(\):&# \-]+/', '/[\’]+/'], ['', '\''] , $s);
	}

	public function prepareData($shipments)
	{
		$predata = [];
		$predata['NewDataSet'] = [];
		foreach ($shipments as $key => $shipment) 
		{
			$cname = preg_replace('/&#x(d|a0)/i', ' ', $shipment->cnee->name);
			$caddress = preg_replace('/&#x(d|a0)/i', ' ', $shipment->cnee->address);
			$csuburb = preg_replace('/&#x(d|a0)/i', ' ', $shipment->cnee->suburb);
			$cbm = $shipment->getTotalCBM()<0.001?0.001:$shipment->getTotalCBM();
			$cbm = round($cbm,3);
			$predata['NewDataSet']['table'.$key] = [
				'ReceiverName' => $cname,
				'ReceiverContactName' => $cname,
				'ReceiverPhone' => $shipment->cnee->tel,
				'ReceiverEmail' => $shipment->cnee->email,
				'ReceiverAddress1' => $caddress,
				'ReceiverAddress2' => '',
				'ReceiverAddress3' => '',
				'ReceiverCity' => $csuburb,
				'ReceiverState' => $shipment->cnee->state,
				'ReceiverPostcode' => $shipment->cnee->postcode,
				'AccountNumber' => $this->ACCOUNT_NUMBER,
				'SiteID' => '1',
				'Connote' => $shipment->ref,
				'Special' => 'Please ring receiver before delivery',
				'ConnoteDate' => date('Y-m-d')."T00:00:00".date('O'),
				'Items' => $shipment->pkg,
				'KGS' => ceil($shipment->weight),
				'Cubic' => $cbm,
				'Service' => '',
				'CustomerReference' => $shipment->hbn,
				'Class' => '',
				'InsuranceAmount' => 0.00,
				'StartDate' =>'',
				'StopDate' =>'',
				'DangerousGoods' => !empty($shipment->mdata['is_dg'])? 'True' : 'False',
			];

			$predata['NewDataSet']['details'.$key] = [
				'ConNoteDetailsID' => $key+1,
				'Connote' => $shipment->ref,
				'SenderReference' => $shipment->hbn,
				'PackageType' => 'CARTON',
				'Items' => $shipment->pkg,
				'KGS' => ceil($shipment->weight),
				'Length' => !empty($shipment->mdata['dim'])? $shipment->mdata['dim']['d'] : '',
				'Width' => !empty($shipment->mdata['dim'])? $shipment->mdata['dim']['w'] : '',
				'Height' => !empty($shipment->mdata['dim'])? $shipment->mdata['dim']['h'] : '',
				'Quantity' => !empty($shipment->mdata['dim']['d'])? 1 : "",
				'Cubic' => $cbm,
				'Type' => 'ITEM',
			];

			$longRef = $shipment->getParcelLongRef(null);
			for($pkg_num=0;$pkg_num<$shipment->pkg;$pkg_num++)
			{
				$predata['NewDataSet']['DDDD'.$key.'_'.$pkg_num] = [
					'ConNoteDetailsID' => $key+1,
					'Connote' =>  $shipment->ref,
					'LabelTrackCode' => $longRef[$pkg_num],
				];
			}

		}
	
		return $predata;
	}

	public function getTrackingEvents($ss)
	{
		$data=["ConnoteList"=>[]];
		foreach ($ss as $s) {
			if(!empty($s->ref))
			{
				$data["ConnoteList"][]=["Connote"=>$s->ref];
			}
		}
		$result=$this->request(self::TRACKING_URL, 'POST', $data);
		return $result;
	}

	public function request($action, $method='POST', $data=null)
	{
		return [];
		$this->result=false;
		
		$url= $action;
		$c=new curl($url);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_TIMEOUT, 600);
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$hdr = $this->build_headers($method, $url);
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

	private function build_headers($method, $path, $acceptType='application/json')
	{
		//  echo $walltech_date."<br>".$auth."<br>".$hash."<br>";
		return [ 'Content-Type: application/json',
			'Accept: '.$acceptType,
			'Authorisation: '.$this->API_KEY,
			'AccountNumber: '.$this->ACCOUNT_NUMBER
		];
	}
	 
	protected function log2file($m, $file='ELabel')
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'DFE' . DIRECTORY_SEPARATOR;
		$lf .= 'dfe_api_'.$file. date('Y-m-d') . '.log';
		return file_put_contents($lf, $m, FILE_APPEND);
	}

	

}
