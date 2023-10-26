<?php
class UbiAPI
{
	const TEST_TOKEN='test5AdbzO5OEeOpvgAVXUFE0A';
	const TEST_KEY='79db9e5OEeOpvgAVXUFWSD';
	const TEST_URL='http://qa.etowertech.com';
	const PRODUCTION_KEY="2dgoDRmLu6inSJTOWMifeQ";
	const PRODUCTION_TOKEN="pclOquXiP3-95uJTGMVmuD";
	const PRODUCTION_URL="https://cn.etowertech.com";
	const CLIENTID = 7139;
	public $result;
	public $err;
	public $is_test;
	public $debug;
	public $serviceCode;
	public $orgRate;
	private $fromInfo;

	public function __construct($debug = false, $test = false,$orgRateId=0)
	{
		$this->is_test = $test;
		$this->debug = $debug;
		$this->orgRate = OrgRate::model()->findByPk($orgRateId);
		if(!empty($this->orgRate))
		{
			$this->serviceCode = $this->orgRate->mdata['service_code'];
			$facility = $this->findTheFacility($this->orgRate);
			$type = str_replace('WW', '', $facility);
			if(empty($type)||$type=='SYD')
			{
				$this->fromInfo=[
					'suburb'=>"BANKSTOWN AERODROME",
					'postcode'=>"2200",
					'address'=>"1/233 Milperra Rd",
					'state'=>"NSW"
				];
			}elseif($type=='MEL')
			{
				$this->fromInfo=[
					'suburb'=>"Sunshine",
					'postcode'=>"3020",
					'address'=>"3B/8 Judge St",
					'state'=>"VIC"
				];
			}elseif($type=='BNE')
			{
				$this->fromInfo=[
					'suburb'=>"Coopers Plains",
					'postcode'=>"4108",
					'address'=>"6/55 Musgrave Rd",
					'state'=>"QLD"
				];
			}elseif($type=='PER')
			{
				$this->fromInfo=[
					'suburb'=>"Welshpool",
					'postcode'=>"6106",
					'address'=>"Warehouse, 8 Glenferrie Road",
					'state'=>"WA"
				];
			}
		}


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
	 
	 
	private function build_headers($method, $path, $acceptType='application/json')
	{
		$walltech_date=date(DATE_RSS);
		$auth = $method."\n".$walltech_date."\n".$path;
		$hash=base64_encode(hash_hmac('sha1', $auth, $this->is_test?self::TEST_KEY : self::PRODUCTION_KEY, true));
		//  echo $walltech_date."<br>".$auth."<br>".$hash."<br>";
		return [ 'Content-Type: application/json',
			'Accept: '.$acceptType,
			'X-WallTech-Date: '.$walltech_date,
			'Authorization: WallTech '.($this->is_test?self::TEST_TOKEN : self::PRODUCTION_TOKEN).':'.$hash
		];
	}
	
	public function getBreakWeight($weight)
	{
		// if ($weight <= 0.500) {
		//     return $weight;
		// } elseif ($weight <= 1.0) {
		//     return $this->doTheWeight($weight, 0.5, 1.0, 0.4, 0.5);
		// } elseif ($weight <= 2) {
		//     return $this->doTheWeight($weight, 1, 2, 0.8, 1.0);
		// } elseif ($weight <= 3) {
		//     return $this->doTheWeight($weight, 2, 3, 1.8, 2.0);
		// } elseif ($weight <= 4) {
		//     return $this->doTheWeight($weight, 3, 4, 2.5, 3.0);
		// } elseif ($weight <= 5) {
		//     return $this->doTheWeight($weight, 4, 5, 3.5, 4.0);
		// } elseif ($weight <= 7) {
		//     return $this->doTheWeight($weight, 5, 7, 4.5, 5.0);
		// } elseif ($weight <= 10) {
		//     return round($weight * rand(80, 100) / 100, 3);
		// } else {
		//     return round($weight, 3);
		// }
		return  round($weight * 100) / 100;
	}
	public function doTheWeight($w, $x1, $x2, $y1, $y2)
	{
		return round(($y1-abs($y1-$y2)/abs($x1-$x2)*$x1)+abs($y1-$y2)/abs($x1-$x2)*$w, 3);
	}
	 
	/**
	 * create shipment Order;
	 * @param  $ss is an Array of shipment
	 */
	public function createShipment($ss)
	{
		$data=[];
		foreach ($ss as $shipment) {
			if(!empty($shipment->mdata['etower_shipment_orderid'])){
				continue;
			}
			$data[]= $this->prepareData($shipment,false,$this->serviceCode);
		}
		if(empty($data)) return false;
		$result=$this->request('/services/shipper/orders', 'POST', $data);
		return $result;
	}

	/**
	 * update shipment Order;
	 * @param  $ss is an Array of shipment
	 */
	public function updateShipment($ss)
	{
		$data=[];
		foreach ($ss as $shipment) {
			if(empty($shipment->mdata['etower_shipment_orderid'])){
				continue;
			}
			$data[]= $this->prepareData($shipment,true,$this->serviceCode);
		}
		if(empty($data)) return false;
		$result=$this->request('/services/shipper/update-order', 'POST', $data);
		return $result;
	}

	/**
	 * update shipment weight;
	 * @param  $ss is an Array of shipment
	 */
	public function updateShipmentWeight($ss)
	{
		$data=[];
		foreach ($ss as $shipment) {
			if(empty($shipment->mdata['etower_shipment_orderid'])){
				continue;
			}
			 $thisObj=new stdClass();
			 $thisObj->operatingAccount="frank.liu@toplogistics.com.au";
			 $thisObj->weight="".$shipment->weight;
			 $thisObj->barcode=$shipment->mdata["article_id"];
			 $data[]= $thisObj;
		}
		if(empty($data)) return false;
		$result=$this->request('/services/integration/carrier/weighed-event', 'POST', $data[0]);
		return $result;
	}

	/**
	 * update shipment weight;
	 * @param  $ss is an Array of shipment
	 */
	public function manifestAU($ss)
	{
		$data=[];
		foreach ($ss as $shipment) {
			if(empty($shipment->mdata['etower_shipment_orderid'])){
				continue;
			}
			$thisObj=new stdClass();
			$thisObj->barcode=$shipment->mdata["article_id"];
			$data[]= $thisObj;
		}
		if(empty($data)) return false;
		$result=$this->request('/services/integration/carrier/despatched-event', 'POST', $data[0]);
		return $result;
	}
	 
	public function manifest($ss)
	{
		$data=[];
		foreach ($ss as $s) {
			$orderIds = explode(',', $s->mdata['etower_shipment_orderid']);
			foreach ($orderIds as $key => $orderId) {
				$data[]=$orderId;
			}
		}
		$result=$this->request('/services/shipper/manifests', 'POST', $data);
		return $result;
	}
	 
	public function getLabelInfo($ss)
	{
		$data=[];
		foreach ($ss as $s) {
			$data[]=$s->mdata['etower_shipment_orderid'];
		}
		$result=$this->request('/services/shipper/labelSpecs', 'POST', $data);
		return $result;
	}

	public function getTrackingEvents($ss)
	{
		$data=[];
		foreach ($ss as $s) {
			if(preg_match('/SIZE/i', $s->ref))
			{
				$data[]=$s->ref;
			}else
			{
				if(!empty($s->mdata['article_id']))
				{
					$data[]=$s->mdata['article_id'];
				}
			}
		}
		$result=$this->request('/services/shipper/trackingEvents', 'POST', $data,false);
		print_r($result);
		return $result;
	}
	
	public function getCost($shipment)
	{
		$orgRate = OrgRate::model()->findByPk($shipment->mdata['org_rate_id']);
		$price = 0;
		$zoneMap = ZoneMap::model()->find('org_id = :oid AND zone_id = :zoneid AND pc_lo <= :code AND pc_hi >= :code', [':oid' =>$orgRate->org_id   ,':zoneid' => $orgRate->zone_id,':code' => $shipment->postcode]);
		$zone = 'N1';
		if (!empty($zoneMap) && !empty($zoneMap['z1'])) {
			$zone = $zoneMap['z1'];
		}
		$weight=$shipment->weight;
		$zrs = ZoneRate::model()->findAll("zone = :s AND (weight_lo < :w AND weight_hi >= :w) AND rate_id = :rateid AND base+item+perkg > 0 ", [':s' => $zone, ':w' => $shipment->weight, ':rateid' => $orgRate->id]);
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

	public function removeSpecialChars($s){
		return preg_replace(['/[^\d\w\'\"\,\;\.\/\(\):&# \-]+/', '/[\’]+/'], ['', '\''] , $s);
	}

	public function prepareData($shipment,$isUpdate = false,$serviceCode)
	{
		$predata=new stdClass();
		if($isUpdate)
		{
			$predata->orderId = $shipment->mdata['etower_shipment_orderid'];
		}
		//if(!empty($shipment->mdata['article_id'])) $predata->trackingNo = $shipment->mdata['article_id'];
		$predata->referenceNo=$shipment->hbn;
		if($this->is_test)
		{
			$predata->referenceNo=$shipment->hbn.ceil(rand(1, 100));
		}
		if(empty($serviceCode))
		{
			$orgRateId= $shipment->mdata['org_rate_id'];
			$orgRate = OrgRate::model()->findByPk($orgRateId);
			$serviceCode = $orgRate->mdata['service_code'];
			$predata->facility= $this->findTheFacility($orgRate);
		}else
		{
			$predata->facility= $this->findTheFacility();
		}

		$predata->recipientName=substr($this->removeSpecialChars($shipment->cnee->name), 0, 35);
		$predata->recipientCompany=substr($this->removeSpecialChars($shipment->cnee->company), 0, 49);
		$predata->email= substr($shipment->cnee->email, 0, 50);
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
		$shipment->mdata['manifest_weight']=$predata->weight;
		$shipment->updateMeta();
		// $predata->volume=$shipment->cbm*($shipment->pkg);
		$predata->invoiceValue=$shipment->dvalue;
		$predata->invoiceCurrency='AUD';
		$predata->description=substr($shipment->getGoods(), 0, 50);
		$predata->serviceCode=$serviceCode;

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


		$returnAddress = $shipment->getReturnAddress(true,false,true);
		$predata->returnName= $returnAddress['name'];
		$predata->returnAddressLine1=$returnAddress['address'];
		$predata->returnCity=$returnAddress['suburb'];
		$predata->returnState = $returnAddress['state'];
		$predata->returnPostcode = $returnAddress['postcode'];
		$predata->returnCountry = 'Australia';
		return $predata;
	}

	public function request($action, $method='POST', $data=null,$log = true)
	{
		$this->result=false;
		
		$url= ($this->is_test?self::TEST_URL:self::PRODUCTION_URL).$action;
		$c=new curl($url);
		$c->setopt(CURLOPT_CUSTOMREQUEST, strtoupper($method));
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_TIMEOUT, 600);
		$hdr = $this->build_headers($method, $url);
		if (!empty($data)) {
			$data = AppHelper::safeJsonEncode($data);
			$c->setopt(CURLOPT_POSTFIELDS, $data);
		}
		$c->setopt(CURLOPT_HTTPHEADER, $hdr);
		 
		if (!$c->exec()) {
			throw new Exception('cUrl Error: '.$c->err);
		}
		$this->rescode=$c->rcode;
		$this->result = json_decode($c->result);
		if($log)
		{
			$this->log2file('Time: '.date('Y-m-d H:i:s')."\nURL: ".$url."\nRequest: ".json_encode($data)."\nResponse Code: ".$this->rescode."\nResponse: ".$c->result.PHP_EOL.PHP_EOL);
		}
		return $this->result;
	}

	public function getFtpTracking($ss,$fileName)
	{
		$xlsave = new oExcel('CSV');
		$xlsave->addRow(1, ["MAWB","TRACKING","SCAN_TIME","CODE"]);
		$start = 2;
		foreach ($ss as $key => $s) 
		{
			if(empty($s->ref)) continue;
			if(!empty($s->tracks))
			{
				foreach ($s->tracks as $k2 => $track)
				{
					if(!empty($track->mdata['sync_ubi'])||(!empty($track->process->status)&&$track->process->status==2)) continue;
					if(!empty($s->consol->container_no))
					{
						$xlsave->addRow($start, [$s->consol->container_no,$s->ref,$track->dt,$track->activity]);
					}else
					{
						$xlsave->addRow($start, [$s->consol->awb,$s->ref,$track->dt,$track->activity]);
					}
					$start++;
				}
			}
		}
		$xlsave->output($fileName, null, false);
		if($start==2) return false;
		return file_get_contents($fileName);
	}

	public function ftpTracking($ss)
	{
		require_once Yii::app()->basePath . '/vendor/autoload.php';
		$man_no = date('YmdHis');
		$mfn = 'TLA_UBI_'.$man_no.rand(0,1000).'_event.csv';
		$fileName=Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR  . 'UBI' . DIRECTORY_SEPARATOR.$mfn;
		$sftp = new phpseclib\Net\SFTP('54.222.221.250');
		// Check SFTP Connection
		if ($sftp->login('TopLogistics', 'w52U1TXJyNVhN23fJ3Fs')) {
			// $lists = $sftp->rawlist('/Manifests');
			$sftp->chdir('/event');
			$ftpFileData = $this->getFtpTracking($ss,$fileName);
			if(empty($ftpFileData)||$sftp->put($mfn, $ftpFileData)){
				//update tranship
				$trans = Yii::app()->db->beginTransaction();
				try{
					foreach($ss as $s){
						if(empty($s->ref)) continue;
						foreach ($s->tracks as $k2 => $track)
						{
							// $track->mdata['sync_ubi'] = 1;
							// $track->save();
							if(!empty($track->process)&&$track->process->status==1)
							{
								$track->process->status = 2;
								$track->process->save();
							}
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					unlink($fileName);
					throw $ex;
				}

				return true;
			}else
			{
				echo "failed";
				unlink($fileName);
			}
		}
		return false;
	}
	 
	protected function log2file($m, $file='ELabel')
	{
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'UBI' . DIRECTORY_SEPARATOR;
		$lf .= 'ubi_api_'.$file. date('Y-m-d') . '.log';
		return file_put_contents($lf, $m, FILE_APPEND);
	}

}
