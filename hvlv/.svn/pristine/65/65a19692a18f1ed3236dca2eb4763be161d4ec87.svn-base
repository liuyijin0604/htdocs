<?php

class trackingCommand extends CConsoleCommand
{
	private $db;
	private $args;
	private $cts;
	private $hod;
	private $moh;
	private $mmod;
	private $min;
	private $mn;
	public $debug = false;
	private $rrobin = true;
	private $linkchain = [];
	private $apApi_lrt, $apApi_rc;
	private $errorRefs = [];

	public function __construct($name, $runner)
	{
		$this->db = Yii::app()->getDb();
		return parent::__construct($name, $runner);
	}

	public function run($args)
	{
		$this->args = $args;
		foreach ($this->args as $ag) {
			if ($ag == '-d') {
				$this->debug = true;
			}
			if ($ag == '-a') {
				$this->rrobin = false;
			}
		}
		$this->hod = date('G');
		$this->moh = ltrim(date('i'), 0);
		$this->mn = intval(date('i'));
		if (empty($this->moh)) {
			$this->moh = 0;
		}
		$this->mmod = round($this->moh / 10);
		$this->min= floor($this->moh / 10);

		if (!empty($args[0]) && method_exists($this, $args[0])) {
			$this->{$args[0]}();
		} else {
			$pid = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'tracking.pid';
			if (is_file($pid) && filectime($pid) > time() - 1800 && !$this->debug) {
				return false;
			}
			file_put_contents($pid, '1');
			if (($this->hod > 4 && $this->hod < 20) || $this->debug) {
				$this->_updateImport();
			}
			// if (($this->hod > 4 && $this->hod < 20) || $this->debug) {

			// }
			// if ($this->mn % 15 == 0) 
			// {
			// 	$this->syncSFTrackingData();
			// }
			/*if (($this->hod > 7 && $this->hod < 23) || $this->debug) {
				$this->_updateExport();
			}
			if (($this->hod > 7 && $this->hod < 23) || $this->debug) {
				// $this->_updateFWD();
			 	$this->auspostCourier();
			}*/
			unlink($pid);
		}
	}

	public function trackAll()
	{
		$this->_updateImportTracking('3447,4429');
		$this->_updateImportTracking('102,858,1017,976,3466,140,3079,3189,3752,3701,4015');
	}

	public function poc2cc($poc, $rn='')
	{
		$orate = OrgRate::model()->find('type = 10 AND code = :c AND vfrom <= CURRENT_DATE() AND (vto IS NULL OR vto >= CURRENT_DATE())', [':c' => $poc]);
		if (empty($orate)) {
			return [128, 'yzpy'];
		} else {
			return [$orate->org_id, $orate->org->code];
		}

		$r = [128, 'yzpy'];
		switch ($poc) {
			case 'CNKMG':
			case 'CNTAO':
			case 'CNTA2':
			case 'AUPOST':
			//case 'CNCS2':
			//case 'CNCSX':
				$r = [128, 'ems'];
			break;
			case 'CNCTU':
			case 'CNTSN':
			case 'CNJNA':
			case 'FWDEMS':
			case 'CNJMN':
			case 'CNJM2':
			//case 'CNXIA':
			case 'CNSJA':
			/*case 'CNHFI':
				$pc = substr($rn, 0, 3);
				if($pc == '770'){
					$r = [382, 'yd'];
				}else{
					$r = [495, 'yto'];
				}
			break;*/
			/*case 'CNCHQ':
				$r = [382, 'yd'];
			break;
			case 'CNJMN':
				$r = [382, 'yd'];
			break;*/
			break;
			case 'HKHKG':
				$pc = substr($rn, 0, 4);
				$r = [488, 'zto'];
				switch ($pc) {
					case '7569':
						$r = [494, 'sto'];
					break;
					case '9727':
						$r = [496, 'sf'];
					break;
				}
			break;
			case 'CNSZX':
				$pc = substr($rn, 0, 6);
				if ($pc == '359495') {
					$r = [488, 'zto'];
				}
			break;
			//case 'CNJM2':
			case 'CNCA2':
			case 'SFDIR':
				$r = [496, 'sf'];
			break;
			case 'STO':
				$r = [494, 'sto'];
			break;
			default:
			break;
		}
		if ($r[0] == 128 && preg_match('/^611\d+/', $rn)) {
			$r = [496, 'sf'];
		}
		return $r;
	}

	protected function _updateImport()
	{
		//update flight info
		$rs = ImcoConsol::model()->findAll("status <= 60 AND `etd` < NOW() AND `etd` > DATE_SUB(NOW(), INTERVAL 30 DAY) AND flight != '' AND awb != ''");
		if ($this->debug) {
			echo '=== Found '.sizeof($rs)." import consol to update flight info. ===\n";
		}
		$dconsols=[];
		$ds= DmawbConsol::model()->findAll("status<= 25 AND `eta`< NOW() AND `etd` >DATE_SUB(NOW(), INTERVAL 30 DAY) AND flight != '' AND awb !=''");
		foreach ($ds as $dc) {
			if (!preg_match("/\d{3}-\d{8}/", $dc->awb)) {
				continue;
			}
			$dconsols[]=$dc;
		}
		$rs= array_merge($rs,$dconsols);
		foreach($rs as $r){
			echo $r->no;
			if(!empty($r->mdata['flt']) && $r->mdata['flt'] > 1)
			{
				if(!empty($r->mdata['updatedEtd']))
				{
					$r->updateFlight();
					continue;
				}else
				{
					continue;
				}
			}
			$d = $this->flight($r->flight, $r->etd, false, true);
			if(!empty($d)){
				$trans = Yii::app()->db->beginTransaction();
				try {
					if (empty($r->mdata['flt']) && $d[7] > 0) { //dispatched
						$r->mdata['flt'] = 1;
						$r->save();
						foreach ($r->shipments as $p) {
							if ($p->agent_id==1303&&!preg_match("/HONG KONG/i", $d[2])) {
								continue;
							}
							$p->addTracking(38, 'Flight dispatched', $d[2], $d[4]);
							if ($p->status < 38&&$p->status!=31) {
								$p->status = 38;
								$sql = " update shipment set status = 38 where id = {$p->id} and status<38 and status != 31";
								$result = Yii::app()->db->createCommand($sql)->execute();
								if(!empty($result))
								{
									Log::add($p, $p->isNewRecord? 3 : 4, array_merge(['status' => ImParcel::$states[38]], []));
								}
							}
						}
					}

					if (!empty($r->mdata['flt']) && $d[7] == 2) { //arrival
						$r->mdata['flt'] = 2;
						$r->save();
						foreach ($r->shipments as $p) {
							$p->addTracking(40, 'Flight arrived', $d[3], $d[5]);
							if ($p->status < 40&&$p->status!=31) {
								$p->status = 40;
								$sql = " update shipment set status = 40 where id = {$p->id} and status<40 and status != 31";
								$result = Yii::app()->db->createCommand($sql)->execute();
								if(!empty($result))
								{
									Log::add($p, $p->isNewRecord? 3 : 4, array_merge(['status' => ImParcel::$states[40]], []));
								}
							}
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
			}
		}



			//update sea info
		$rs = ImcoConsol::model()->findAll("status <= 60 AND `etd` > DATE_SUB(NOW(), INTERVAL 30 DAY) AND flight != '' AND awb != ''");
		if($this->debug) echo '=== Found '.sizeof($rs)." import consol to update sea info. ===\n";
		$dconsols=[];
		$ds= DmawbConsol::model()->findAll("status<= 25 AND `etd` >DATE_SUB(NOW(), INTERVAL 30 DAY) AND flight != '' AND awb !=''");
		foreach($ds as $dc){
			if($dc->service==Consol::SEACONSOL) $dconsols[]=$dc;
		}

		foreach($rs as $r){
			if($r->service==Consol::SEACONSOL) $dconsols[]=$r;
		}

		foreach($dconsols as $r)
		{
			$oShipmentNum = count($r->shipments);//reset consol
			if(!empty($r->mdata['oShipmentNum']))
			{
				if($r->mdata['oShipmentNum']!=$oShipmentNum)
				{
					$r->mdata['slt'] = 0;
				}
			}
			if(!empty($r->mdata['slt']) && $r->mdata['slt'] > 1) continue;
			$trans = Yii::app()->db->beginTransaction();
			$polInfor = SystemSetting::getPodSetting(substr($r->pol,0,2),$r->pol);
			$podInfor = SystemSetting::getPodSetting(substr($r->pod,0,2),$r->pod);
			$vesselInfo = " ".@$r->mdata['sea_vessel']."/".@$r->flight;
			try{
				if(empty($r->mdata['slt']))
				{ //dispatched
					$r->mdata['slt'] = 1;
					$r->mdata['oShipmentNum'] = $oShipmentNum;
					$r->save();
					foreach($r->shipments as $p)
					{
						$p->addTracking(38, 'Sea vessel dispatched'.$vesselInfo, $polInfor[0], $r->etd);
						if($p->status < 38&&$p->status!=31)
						{
							$p->status = 38;
							$sql = " update shipment set status = 38 where id = {$p->id} and status<38 and status != 31";
							$result = Yii::app()->db->createCommand($sql)->execute();
							if(!empty($result))
							{
								Log::add($p, $p->isNewRecord? 3 : 4, array_merge(['status' => ImParcel::$states[38]], []));
							}
						}
					}
				}

				if(strtotime($r->eta)<=strtotime(Date("Y-m-d")))
				{
					if(!empty($r->mdata['slt'])&&$r->mdata['slt']==1)
					{ //arrival
						$r->mdata['slt'] = 2;
						$r->mdata['oShipmentNum'] = $oShipmentNum;
						$r->save();
						foreach($r->shipments as $p)
						{
							$p->addTracking(40, 'Sea arrived'.$vesselInfo, $podInfor[0], $r->eta);
							if($p->status < 39&&$p->status!=31)
							{
								$p->status = 39;
								$sql = " update shipment set status = 39 where id = {$p->id} and status<39 and status != 31";
								$result = Yii::app()->db->createCommand($sql)->execute();
								if(!empty($result))
								{
									Log::add($p, $p->isNewRecord? 3 : 4, array_merge(['status' => ImParcel::$states[39]], []));
								}
							}
						}
					}
				}
				$trans->commit();
				echo '=== SeaConsol '.$r->no." , Size ".sizeof($rs)." import consol to update sea info. ===\n";
			} catch (Exception $ex) {
				$trans->rollback();
				throw $ex;
			}
			continue;
		}
//
//		//update tracking
//		$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN (101,102,103,115,858,1017) AND `time` > DATE_SUB(NOW(), INTERVAL 30 DAY) AND `time` < DATE_SUB(NOW(), INTERVAL 2 HOUR)');
//		if($this->debug) echo '=== Found '.sizeof($rs)." import shipments for updating. ===\n";
//		$c = 0;
//
//		foreach($rs as $i=>$r){
//			if($r->skipTrack() || ($this->rrobin && $r->id % 6 != $this->min)) continue;
//                        Log::log2file($r->id.'==>'.$r->connote, "import_tracking_log");
//			@$this->_upImTrack($r);
//			$c++;
//		}
//		if($this->debug) echo '=== Total '.$c." import shipments updated. ===\n";
	}

	public function _updateImportTracking($ids)
	{
		//auspost on separate thread;
		// $dir = Yii::app()->basePath.DIRECTORY_SEPARATOR;
		// $cmd = '_updateAuspost';
		// AppHelper::exec($dir.'yiic '.$cmd.' >/dev/null 2>/dev/null &');

		//update tracking
		$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN ('.$ids.') AND `time` > DATE_SUB(NOW(), INTERVAL 30 DAY)  AND (HOUR(NOW())+1) % LEAST(CEIL(POWER(TIMESTAMPDIFF(DAY, `time`, NOW())+1, 2) / 6), 12) = 0');
		if ($this->debug) {
			echo '=== Found '.sizeof($rs)." import shipments for updating. ===\n";
		}
		$c = 0;
		$tracking = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'trackingInfo.pid';
		if (file_exists($tracking) && preg_match("/tracking:(\d+)/i", file_get_contents($tracking), $match)) {
			$d = ($match[1]+1)%12;
		} else {
			$d=0;
		}
		file_put_contents($tracking, 'tracking:'.$d.PHP_EOL);

		// $bfw = [];
		$ubi = [];
		$dfe = [];
		$other = [];
		$tollIPEC = [];
		$allied = [];

		foreach ($rs as $i=>$r) {
			// if (($this->rrobin && $r->id % 12 != $d) || @$r->skipTrack()) {
			// 	continue;
			// }
			if (empty($r->shipment)) {
				continue;
			}
			if (in_array($r->org_id, [Org::ORGID_COURIER_STARTRACK, Org::ORGID_COURIER_AUPOST, Org::ORGID_COURIER_D2Z,Org::ORGID_COURIER_UBI,Org::ORGID_COURIER_EIZ,Org::ORGID_COURIER_TOLL_IPEC,Org::ORGID_COURIER_BORDER,Org::ORGID_COURIER_ALLIED_TOP]) && empty($r->mdata['oid'])) {
				continue;
			} //only tracking after manifest;

			// if (in_array($r->org_id, [ Org::ORGID_COURIER_AUPOST, Org::ORGID_COURIER_D2Z])) {
			// 	$aup[] = $r;
			// 	continue;
			// }

			// if($r->org_id == Org::ORGID_COURIER_FASTWAY){
			// 	$bfw[] = $r;
			// 	continue;
			// }

			if (in_array($r->org_id, [ Org::ORGID_COURIER_UBI])) {
				if(preg_match('/^8/', $r->connote))
				{
					$tollIPEC[] = $r;
					continue;
				}
				$ubi[] = $r;
				// print_r($ubi);
				continue;
			}

			if (in_array($r->org_id, [ Org::ORGID_COURIER_TOLL_IPEC,Org::ORGID_COURIER_EIZ,Org::ORGID_COURIER_AUK])) {
				if(preg_match('/AOE/', $r->connote))
				{
					continue;
				}

				$tollIPEC[] = $r;
				continue;
			}

			if (in_array($r->org_id, [ Org::ORGID_COURIER_BORDER])) {

				$border[] = $r;
				continue;
			}

			if (in_array($r->org_id, [ Org::ORGID_COURIER_ALLIED_TOP])) {

				$allied[] = $r;
				continue;
			}

			if (in_array($r->org_id, [Org::ORGID_COURIER_DFE,Org::ORGID_COURIER_DFE_TOP])) {
				if(!empty($r->mdata['expired'])&&$r->mdata['expired']==1)
				{
					continue;
				}

				if(!empty($r->mdata['request_number_today'])&&$r->mdata['request_number_today']>=5&&$r->mdata['request_date']==date('Y-m-d'))
				{
					continue;
				}
				$dfe[] = $r;
				continue;
			}
			$other[] = $r;
			$c++;
		}

		/*if(!empty($bfw)){
			if ($this->debug) {
				echo '=== Total '.count($bfw)." fastway shipments. ===\n";
			}
			$ppg = 100;
			$pgs = ceil(count($bfw) / $ppg);
			for($i=0; $i<$pgs; $i++){
				$ts = array_slice($bfw, $i*$ppg, $ppg);
				$rs = $this->fastway($ts);
				$trans = Yii::app()->db->beginTransaction();
				try {
					foreach($ts as $t){
						if(isset($rs[$t->connote])){
							$this->cts = &$t;
							$this->_upImTrack($t, $this->_prepFastwayEvents($rs[$t->connote]));
							$c++;
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
			}
		}*/
		foreach($other as $r){
			@$this->_upImTrack($r);
		}

		if(!empty($ubi)){ //batch update aupost
			if ($this->debug) {
				echo '=== Total '.count($ubi)." ubi shipments. ===\n";
			}

			$ppg = 10;
			$pgs = ceil(count($ubi) / $ppg);
			for($i=0; $i<$pgs; $i++){
				$ts = array_slice($ubi, $i*$ppg, $ppg);
				$rs = $this->ubiApi($ts);
				$trans = Yii::app()->db->beginTransaction();
				try {
					foreach($ts as $t){
						if(isset($rs[$t->mdata['barcodeLabelNumber']])){
							$this->cts = &$t;
							$this->_upImTrack($t, $this->_prepUbiEvents($rs[$t->mdata['barcodeLabelNumber']]));
							$c++;
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
			}
		}

		if(!empty($tollIPEC)){ //batch update aupost
			if ($this->debug) {
				echo '=== Total '.count($tollIPEC)." toll IPEC shipments. ===\n";
			}

			$ppg = 5;
			$pgs = ceil(count($tollIPEC) / $ppg);
			for($i=0; $i<$pgs; $i++){
				$ts = array_slice($tollIPEC, $i*$ppg, $ppg);
				$rs = $this->myTollApi($ts);
				$trans = Yii::app()->db->beginTransaction();
				try {
					foreach($ts as $t){
						if(isset($rs[$t->connote])){
							$this->cts = &$t;
							$this->_upImTrack($t, $this->_prepTollIPECEvents($rs[$t->connote]));
							$c++;
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
			}
		}

		if(!empty($border)){ //batch update aupost
			if ($this->debug) {
				echo '=== Total '.count($border)." border shipments. ===\n";
			}

			$ppg = 1;
			$pgs = ceil(count($border) / $ppg);
			for($i=0; $i<$pgs; $i++){
				$ts = array_slice($border, $i*$ppg, $ppg);
				$rs = $this->borderApi($ts);
				$trans = Yii::app()->db->beginTransaction();
				try {
					foreach($ts as $t){
						if(isset($rs[$t->connote])){
							$this->cts = &$t;
							$this->_upImTrack($t, $this->_prepBorderEvents($rs[$t->connote]));
							$c++;
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
			}
		}

		if(!empty($allied)){ //batch update aupost
			if ($this->debug) {
				echo '=== Total '.count($allied)." allied shipments. ===\n";
			}

			$ppg = 5;
			$pgs = ceil(count($allied) / $ppg);
			for($i=0; $i<$pgs; $i++){
				$ts = array_slice($allied, $i*$ppg, $ppg);
				$rs = $this->alliedApi($ts);
				$trans = Yii::app()->db->beginTransaction();
				try {
					foreach($ts as $t){
						if(isset($rs[$t->connote])){
							$this->cts = &$t;
							$this->_upImTrack($t, $this->_prepAlliedEvents($rs[$t->connote]));
							$c++;
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
			}
		}



		//DFE
		// if(!empty($dfe)){ //batch update dfe
		// 	if ($this->debug) {
		// 		echo '=== Total '.count($dfe)." dfe shipments. ===\n";
		// 	}

		// 	$ppg = 10;
		// 	$pgs = ceil(count($dfe) / $ppg);
		// 	for($i=0; $i<$pgs; $i++){
		// 		$ts = array_slice($dfe, $i*$ppg, $ppg);
		// 		$dfeReuqestTimes = SystemSetting::getDFERequestTimes();
		// 		if($dfeReuqestTimes>=5000)
		// 		{
		// 			break;
		// 		}
		// 		SystemSetting::setDFERequestTimes($dfeReuqestTimes+1);
		// 		$rs = $this->dfeApi($ts);
		// 		$trans = Yii::app()->db->beginTransaction();
		// 		try {
		// 			foreach($ts as $t){
		// 				if(isset($rs[$t->connote])){
		// 					$this->cts = &$t;
		// 					$requestResult = $this->_prepDfeEvents($rs[$t->shipment->ref]);
		// 					if($requestResult===false) continue;
		// 					$requestNumberToday = (empty($t->mdata['request_number_today'])||$t->mdata['request_date']!=date('Y-m-d'))?0:$t->mdata['request_number_today'];
		// 					$t->mdata['request_number_today'] = $requestNumberToday + 1;
		// 					$t->mdata['request_date'] = date('Y-m-d');
		// 					$this->_upImTrack($t, $requestResult);
		// 					$c++;
		// 				}
		// 			}
		// 			$trans->commit();
		// 		} catch (Exception $ex) {
		// 			$trans->rollback();
		// 			throw $ex;
		// 		}
		// 	}
		// }

		if ($this->debug) {
			echo '=== Total '.$c." import shipments updated. ===\n";
		}
	}
	private function myTollApi($ts)
	{
		$ps = [];
		foreach ($ts as $key => $s) {
			$ps[$key] = new stdClass();
			$ps[$key]->ref = $s->connote;
			$ps[$key]->barcode = $s->shipment->mdata['barcode'];
		}

		$myTollAPI = new MyTollAPI('syd');
		$data = $myTollAPI->trackingShipment($ps);
		return $data;
	}

	private function alliedApi($ts)
	{
		$ps = [];
		$s = $ts[0];

		$orgRate = OrgRate::model()->findByPk($s->shipment->mdata['org_rate_id']);
		$javaAPI = new HvlvJavaAPI(HvlvJavaAPI::RATE_TYPE[$orgRate->mdata['ddpt_id']],$orgRate->mdata['courier']);
		$data = $javaAPI->trackShipment($s->shipment);
		if(!empty($data['code'])&&$data['code']=='60000')
		{
			$data = $data['data']['tracking_data'];
		}
		return [$ts[0]->connote=>$data];
	}

	private function  _prepTollIPECEvents($data){
        $finalData = [];
        foreach($data as $key => $d){
            $thisData = [];
            $thisData[0] = $d[2];
            $thisData[1] = $d[1];
            $thisData[2] = '';
            if(preg_match('/delivered/i', $thisData[1]))
            {
                $this->cts->status = 99;
                $this->cts->mdata['inTransit'] = 0;
                $thisData['delivered'] = 1;
            }

            if(preg_match('/Shipment Created/i', $thisData[1]))
            {
                continue;
            }

            if(preg_match('/(Pick)|(Transit)|(IN DEPOT)/i', $thisData[1]))
            {
                $this->cts->mdata['inTransit'] = 1;
            }
            $finalData[] = $thisData;
        }
        return $finalData;
    }

    private function _prepAlliedEvents($data)
    {
    	$finalData = [];
        foreach($data as $key => $d){
        	if(empty($d['scanDateStr']))
        	{
        		continue;
        	}
            $thisData = [];
            $thisData[0] = $d['scanDateStr'];
            $thisData[1] = $d['batchID'];
            $thisData[2] = $d['location'];

            if($thisData[1]=='DEL')
            {
                $this->cts->status = 99;
                $this->cts->mdata['inTransit'] = 0;
                $thisData['delivered'] = 1;
                $thisData[1] = 'Delivered';
                $finalData[] = $thisData;
                continue;
            }

            if($thisData[1]=='PICKED UP')
            {
                $this->cts->mdata['inTransit'] = 1;
                $finalData[] = $thisData;
                continue;
            }

            if(preg_match('/labelsent/i', $thisData[1]))
            {
                continue;
            }

            $this->cts->mdata['inTransit'] = 1;
            $thisData[1] = 'In transit';


            $finalData[] = $thisData;
        }
        return $finalData;
    }

    private function borderApi($ts)
	{
		$trackingNumber = $ts[0]->connote;

		$borderAPI = BorderAPI::getAPIInstance($ts[0]->shipment->mdata['org_rate_id']);
		$data = $borderAPI->getTracking($trackingNumber);
		return [$trackingNumber=>$data];
	}
	
	private function  _prepBorderEvents($data){
        $finalData = [];
        $checkingData = $data->Statuses;
        foreach($checkingData as $key => $d){
            $thisData = [];
            $dateStr = str_replace("T", " ", $d->DateOccurred);
            $dateStr = str_replace("+10:00", "", $dateStr);
            $thisData[0] = $dateStr;
            $thisData[1] = $d->Status;
            $thisData[2] = $d->Suburb.", ".$d->State;
            if(preg_match('/Shipment Data Received/i', $thisData[1]))
            {
                continue;
            }elseif(preg_match('/unsaved/i', $thisData[1]))
            {
                continue;
            }else
            {
            	$this->cts->mdata['inTransit'] = 1;
            }


            if(preg_match('/delivered/i', $thisData[1]))
            {
                $this->cts->status = 99;
                $this->cts->mdata['inTransit'] = 0;
                $thisData['delivered'] = 1;
            }
            $finalData[] = $thisData;
        }
        return $finalData;
    }



	// public function _updateDFE()//only for testing
	// {
	// 	$this->debug = true;
	// 	$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN (3189) and json_value(meta,"$.expired") is null');
	// 	if ($this->debug) {
	// 		echo '=== Found '.sizeof($rs)." import shipments for updating. ===\n";
	// 	}
	// 	$c = 0;
	// 	$tracking = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'trackingInfo.pid';
	// 	if (file_exists($tracking) && preg_match("/tracking:(\d+)/i", file_get_contents($tracking), $match)) {
	// 		$d = ($match[1]+1)%12;
	// 	} else {
	// 		$d=0;
	// 	}
	// 	file_put_contents($tracking, 'tracking:'.$d.PHP_EOL);

	// 	// $bfw = [];
	// 	$dfeSyd = [];

	// 	foreach ($rs as $i=>$r) {


	// 		if (in_array($r->org_id, [ Org::ORGID_COURIER_DFE])) {
	// 			$orgRate = OrgRate::model()->findByPk($r->imparcel->mdata['org_rate_id']);
	// 			$code = $orgRate->code;
	// 			switch ($code) {
	// 				case ImportChargeCode::DFE_SYD_CODE:
	// 				    $dfeSyd[] = $r;
	// 					break;
	// 				default:
	// 					 $dfeSyd[] = $r;
	// 					break;
	// 			}
	// 		}
	// 		$c++;
	// 	}

	// 	if(!empty($dfeSyd)){ //batch update dfe
	// 		if ($this->debug) {
	// 			echo '=== Total '.count($dfeSyd)." dfe shipments. ===\n";
	// 		}

	// 		$ppg = 10;
	// 		$pgs = ceil(count($dfeSyd) / $ppg);
	// 		for($i=0; $i<$pgs; $i++){
	// 			if($i>0 && $i%5 == 0)
	// 			{
	// 				sleep(80);//stop to run 1 minute
	// 			}
	// 			$ts = array_slice($dfeSyd, $i*$ppg, $ppg);
	// 			$rs = $this->dfeApi($ts,'syd');
	// 			$dfeReuqestTimes = SystemSetting::getDFERequestTimes();
	// 			if($dfeReuqestTimes>=5000)
	// 			{
	// 				break;
	// 			}
	// 			SystemSetting::setDFERequestTimes($dfeReuqestTimes+1);
	// 			$trans = Yii::app()->db->beginTransaction();
	// 			try {
	// 				foreach($ts as $t){
	// 					if(isset($rs[$t->connote])){
	// 						$this->cts = &$t;
	// 						$this->_upImTrack($t, $this->_prepDfeEvents($rs[$t->shipment->ref]));
	// 						$c++;
	// 					}
	// 				}
	// 				$trans->commit();
	// 			} catch (Exception $ex) {
	// 				$trans->rollback();
	// 				throw $ex;
	// 			}
	// 		}
	// 	}

	// 	if ($this->debug) {
	// 		echo '=== Total '.$c." import shipments updated. ===\n";
	// 	}
	// }

	// public function _updateDFETOP()//only for testing
	// {
	// 	$this->debug = true;
	// 	$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN (3460) and json_value(meta,"$.expired") is null');
	// 	if ($this->debug) {
	// 		echo '=== Found '.sizeof($rs)." import shipments for updating. ===\n";
	// 	}
	// 	$c = 0;
	// 	$tracking = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'trackingInfo.pid';
	// 	if (file_exists($tracking) && preg_match("/tracking:(\d+)/i", file_get_contents($tracking), $match)) {
	// 		$d = ($match[1]+1)%12;
	// 	} else {
	// 		$d=0;
	// 	}
	// 	file_put_contents($tracking, 'tracking:'.$d.PHP_EOL);

	// 	// $bfw = [];
	// 	$ubi = [];
	// 	$dfeSyd = [];
	// 	$dfeMel = [];
	// 	$dfeBne = [];
	// 	$other = [];

	// 	foreach ($rs as $i=>$r) {


	// 		if (in_array($r->org_id, [ Org::ORGID_COURIER_DFE_TOP])) {
	// 			$orgRate = OrgRate::model()->findByPk($r->imparcel->mdata['org_rate_id']);
	// 			$code = $orgRate->code;
	// 			switch ($code) {
	// 				case ImportChargeCode::DFE_TOP_SYD_CODE:
	// 				    $dfeSyd[] = $r;
	// 					break;
	// 				case ImportChargeCode::DFE_TOP_MEL_CODE:
	// 					$dfeMel[] = $r;
	// 					break;
	// 				case ImportChargeCode::DFE_TOP_BNE_CODE:
	// 					$dfeBne[] = $r;
	// 					break;
	// 				default:
	// 					 $dfeSyd[] = $r;
	// 					break;
	// 			}
	// 		}
	// 		$c++;
	// 	}

	// 	if(!empty($dfeSyd)){ //batch update dfe
	// 		if ($this->debug) {
	// 			echo '=== Total '.count($dfeSyd)." dfe shipments. ===\n";
	// 		}

	// 		$ppg = 10;
	// 		$pgs = ceil(count($dfeSyd) / $ppg);
	// 		for($i=0; $i<$pgs; $i++){
	// 			if($i>0 && $i%5 == 0)
	// 			{
	// 				sleep(80);//stop to run 1 minute
	// 			}
	// 			$ts = array_slice($dfeSyd, $i*$ppg, $ppg);
	// 			$rs = $this->dfeApi($ts,'syd_top');
	// 			$dfeReuqestTimes = SystemSetting::getDFERequestTimes('top_syd');
	// 			if($dfeReuqestTimes>=5000)
	// 			{
	// 				break;
	// 			}
	// 			SystemSetting::setDFERequestTimes($dfeReuqestTimes+1,'top_syd');
	// 			$trans = Yii::app()->db->beginTransaction();
	// 			try {
	// 				foreach($ts as $t){
	// 					if(isset($rs[$t->connote])){
	// 						$this->cts = &$t;
	// 						$this->_upImTrack($t, $this->_prepDfeEvents($rs[$t->shipment->ref]));
	// 						$c++;
	// 					}
	// 				}
	// 				$trans->commit();
	// 			} catch (Exception $ex) {
	// 				$trans->rollback();
	// 				throw $ex;
	// 			}
	// 		}
	// 	}

	// 	if(!empty($dfeMel)){ //batch update dfe
	// 		if ($this->debug) {
	// 			echo '=== Total '.count($dfeMel)." dfe shipments. ===\n";
	// 		}

	// 		$ppg = 10;
	// 		$pgs = ceil(count($dfeMel) / $ppg);
	// 		for($i=0; $i<$pgs; $i++){
	// 			if($i>0 && $i%5 == 0)
	// 			{
	// 				sleep(80);//stop to run 1 minute
	// 			}
	// 			$ts = array_slice($dfeMel, $i*$ppg, $ppg);
	// 			$rs = $this->dfeApi($ts,'mel_top');
	// 			$dfeReuqestTimes = SystemSetting::getDFERequestTimes('top_mel');
	// 			if($dfeReuqestTimes>=5000)
	// 			{
	// 				break;
	// 			}
	// 			SystemSetting::setDFERequestTimes($dfeReuqestTimes+1,'top_mel');
	// 			$trans = Yii::app()->db->beginTransaction();
	// 			try {
	// 				foreach($ts as $t){
	// 					if(isset($rs[$t->connote])){
	// 						$this->cts = &$t;
	// 						$this->_upImTrack($t, $this->_prepDfeEvents($rs[$t->shipment->ref]));
	// 						$c++;
	// 					}
	// 				}
	// 				$trans->commit();
	// 			} catch (Exception $ex) {
	// 				$trans->rollback();
	// 				throw $ex;
	// 			}
	// 		}
	// 	}

	// 	if(!empty($dfeBne)){ //batch update dfe
	// 		if ($this->debug) {
	// 			echo '=== Total '.count($dfeBne)." dfe shipments. ===\n";
	// 		}

	// 		$ppg = 10;
	// 		$pgs = ceil(count($dfeBne) / $ppg);
	// 		for($i=0; $i<$pgs; $i++){
	// 			if($i>0 && $i%5 == 0)
	// 			{
	// 				sleep(80);//stop to run 1 minute
	// 			}
	// 			$ts = array_slice($dfeBne, $i*$ppg, $ppg);
	// 			$rs = $this->dfeApi($ts,'bne_top');
	// 			$dfeReuqestTimes = SystemSetting::getDFERequestTimes('top_bne');
	// 			if($dfeReuqestTimes>=5000)
	// 			{
	// 				break;
	// 			}
	// 			SystemSetting::setDFERequestTimes($dfeReuqestTimes+1,'top_bne');
	// 			$trans = Yii::app()->db->beginTransaction();
	// 			try {
	// 				foreach($ts as $t){
	// 					if(isset($rs[$t->connote])){
	// 						$this->cts = &$t;
	// 						$this->_upImTrack($t, $this->_prepDfeEvents($rs[$t->shipment->ref]));
	// 						$c++;
	// 					}
	// 				}
	// 				$trans->commit();
	// 			} catch (Exception $ex) {
	// 				$trans->rollback();
	// 				throw $ex;
	// 			}
	// 		}
	// 	}

	// 	if ($this->debug) {
	// 		echo '=== Total '.$c." import shipments updated. ===\n";
	// 	}
	// 	$this->_updateChukou1();
	// 	$this->_updateSF();
	// }

	public function _updateChukou1()//only for testing
	{
		$this->debug = false;
		$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN (3557) and json_value(meta,"$.expired") is null');
		if ($this->debug) {
			echo '=== Found '.sizeof($rs)." import shipments for updating. ===\n";
		}
		$c = 0;
		$tracking = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'trackingInfo.pid';
		if (file_exists($tracking) && preg_match("/tracking:(\d+)/i", file_get_contents($tracking), $match)) {
			$d = ($match[1]+1)%12;
		} else {
			$d=0;
		}
		file_put_contents($tracking, 'tracking:'.$d.PHP_EOL);

		// $bfw = [];
		$ubi = [];
		$syd = [];
		$dfeMel = [];
		$dfeBne = [];
		$other = [];

		foreach ($rs as $i=>$r) {
			$syd[] = $r;
			$c++;
		}

		if(!empty($syd)){ //batch update dfe
			if ($this->debug) {
				echo '=== Total '.count($syd)." chukou1 shipments. ===\n";
			}

			$ppg = 10;
			$pgs = count($syd);
			for($i=0; $i<$pgs; $i++){
				$t = $syd[$i];
				if($i>0 && $i%20 == 0)
				{
					sleep(10);//stop to run 1 minute
				}
				if ($this->debug) {
					print_r($t);
				}
				$rs = $this->ck1Api($t);
				if ($this->debug) {
					echo "all--------------------rs";
					print_r($rs);
				}
				$ck1ReuqestTimes = SystemSetting::getDFERequestTimes('top_syd_ck1');
				if($ck1ReuqestTimes>=5000)
				{
					break;
				}
				SystemSetting::setDFERequestTimes($ck1ReuqestTimes+1,'top_syd_ck1');
				$trans = Yii::app()->db->beginTransaction();
				try {
					if(isset($rs[$t->connote])){
						$this->cts = &$syd[$i];
						$this->_upImTrack($t, $this->_prepChukou1Events($rs[$t->connote]));
						$c++;
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
			}
		}

		if ($this->debug) {
			echo '=== Total '.$c." import shipments updated. ===\n";
		}
	}

	public function _updateSF()//only for testing
	{
		$this->debug = false;
		$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN (3590) and json_value(meta,"$.expired") is null');
		if ($this->debug) {
			echo '=== Found '.sizeof($rs)." import shipments for updating. ===\n";
		}
		$c = 0;
		$tracking = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'trackingInfo.pid';
		if (file_exists($tracking) && preg_match("/tracking:(\d+)/i", file_get_contents($tracking), $match)) {
			$d = ($match[1]+1)%12;
		} else {
			$d=0;
		}
		file_put_contents($tracking, 'tracking:'.$d.PHP_EOL);

		// $bfw = [];
		$ubi = [];
		$syd = [];
		$dfeMel = [];
		$dfeBne = [];
		$other = [];

		foreach ($rs as $i=>$r) {
			$syd[] = $r;
			$c++;
		}

		if(!empty($syd)){ //batch update dfe
			if ($this->debug) {
				echo '=== Total '.count($syd)." chukou1 shipments. ===\n";
			}

			$ppg = 10;
			$pgs = count($syd);
			for($i=0; $i<$pgs; $i++){
				$t = $syd[$i];
				if($i>0 && $i%20 == 0)
				{
					sleep(60);//stop to run 1 minute
				}
				if ($this->debug) {
					print_r($t);
				}
				$rs = $this->sfApi($t);
				if ($this->debug) {
					echo "all--------------------rs";
					print_r($rs);
				}
				$ck1ReuqestTimes = SystemSetting::getDFERequestTimes('top_syd_sf');
				if($ck1ReuqestTimes>=5000)
				{
					break;
				}
				SystemSetting::setDFERequestTimes($ck1ReuqestTimes+1,'top_syd_sf');
				$trans = Yii::app()->db->beginTransaction();
				try {
					if(isset($rs[$t->connote])){
						$this->cts = &$syd[$i];
						$this->_upImTrack($t, $this->_prepSFEvents($rs[$t->connote]));
						$c++;
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
			}
		}

		if ($this->debug) {
			echo '=== Total '.$c." import shipments updated. ===\n";
		}
	}

	public function _updateFastwayFTP()
	{
		require_once Yii::app()->basePath . '/vendor/autoload.php';
		$sftp = new phpseclib\Net\SFTP('sftp.fastway.org');
		$rootPath = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'fastway'.DIRECTORY_SEPARATOR.'fastwayTracking';
		// Check SFTP Connection
		if ($sftp->login('292107', 'eGv0&4KS6*qfVPXOGwI7Qs')) {
			// $lists = $sftp->rawlist('/Manifests');
			$sftp->chdir('/Tracking');
			$get_path = $sftp->pwd();
			$filepaths = $sftp->nlist();
			foreach ($filepaths as $key => $filepath) {
				if($filepath=="."||$filepath=="..") continue;
				$fileCalculator = explode('.', $filepath)[0];
				if($fileCalculator>'1')
				{
					$oldRecord = FastwayTrackingFileImport::model()->find('content = :content',[":content"=>$filepath]);
					if(!empty($oldRecord))
					{
						$sftp->delete($filepath);
						continue;
					}

					$result = $sftp->get($filepath);
					$fileFullName = $rootPath.DIRECTORY_SEPARATOR.$filepath;
					file_put_contents($fileFullName, $result);
					$data = oExcel::getFileData($fileFullName);
					$data = $data[0];
					foreach ($data as $key => $row) {
						if(empty($row[2])) continue;
						$ref = $row[2];
						$scanType = $row[4];
						$scanSmallType = $row[3];
						$descriptionSmall = $row[6];
						$description = $row[7];
						$this->cts = null;
						$imparcel = ImParcel::model()->find("ref = :ref",[":ref"=>$ref]);
						if(empty($imparcel)) continue;
						if(!empty($imparcel->trans))
						{
							foreach ($imparcel->trans as $key => $t) {
								if($t->org_id==Org::ORGID_COURIER_FASTWAY&&in_array($t->status,[11,19]))
								{
									$this->cts = $t;
									break;
								}
							}
						}
						if(empty($this->cts)) continue;
						$data = [];
						if ($scanType === 'D' && !empty($this->cts) && !preg_match('/Unable to Deliver|Onboard|Parcel Connect Agent|Calling Card Left|Parcel Connect CCL|Undeliverable|En route to collection point|Parcel Connect collection point/i',trim($descriptionSmall))&&$scanSmallType!='UND') {
							$this->cts->status = 99;
							$data['delivered'] = 1;
						}

						if ($scanType === 'T' && !empty($this->cts) && !preg_match('/Consignment Information Submitted/i',trim($descriptionSmall))) {
							$data['isTransit'] = 1;
						}
						$date = explode('/', $row[5]);
						$yearAndTime = explode(' ', $date[2]);

						$date = $yearAndTime[0].'-'.$date[1].'-'.$date[0].' '.$yearAndTime[1];
						$data[0] = $date;
						$data[1] = $description;
						$data[2] = $row[9];
						$data[3] = $row[3];
						@$this->_upImTrack($this->cts, [$data]);
					}
					$record = new FastwayTrackingFileImport();
					$record->content = $filepath;
					$record->created = date('Y-m-d H:i:s');
					$record->save();
				}


			}
		}
		return false;
	}

	public function updateMissedFastway()
	{
		$rootPath = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'fastway'.DIRECTORY_SEPARATOR.'fastwayTracking';
		$files = ['637551238061252277.csv'];
		foreach ($files as $key => $fname) {
				$fileFullName = $rootPath.DIRECTORY_SEPARATOR.$fname;
					$data = oExcel::getFileData($fileFullName);
					$data = $data[0];
					foreach ($data as $key => $row) {
						if(empty($row[2])) continue;
						$ref = $row[2];
						$scanType = $row[4];
						$scanSmallType = $row[3];
						$descriptionSmall = $row[6];
						$description = $row[7];
						$this->cts = null;
						$imparcel = ImParcel::model()->find("ref = :ref",[":ref"=>$ref]);
						if(empty($imparcel)) continue;
						if(!empty($imparcel->trans))
						{
							foreach ($imparcel->trans as $key => $t) {
								if($t->org_id==Org::ORGID_COURIER_FASTWAY&&in_array($t->status,[11,19]))
								{
									$this->cts = $t;
									break;
								}
							}
						}
						if(empty($this->cts)) continue;
						$data = [];
						if ($scanType === 'D' && !empty($this->cts) && !preg_match('/Unable to Deliver|Onboard|Parcel Connect Agent|Calling Card Left|Parcel Connect CCL|Undeliverable|En route to collection point|Parcel Connect collection point/i',trim($descriptionSmall))&&$scanSmallType!='UND') {
							$this->cts->status = 99;
							$data['delivered'] = 1;
						}

						if ($scanType === 'T' && !empty($this->cts) && !preg_match('/Consignment Information Submitted/i',trim($descriptionSmall))) {
							$data['isTransit'] = 1;
						}
						$date = explode('/', $row[5]);
						$yearAndTime = explode(' ', $date[2]);

						$date = $yearAndTime[0].'-'.$date[1].'-'.$date[0].' '.$yearAndTime[1];
						$data[0] = $date;
						$data[1] = $description;
						$data[2] = $row[9];
						$data[3] = $row[3];
						@$this->_upImTrack($this->cts, [$data]);
					}
		}
	
	}
	public function _updateFastwayFTPTesting()
	{
		echo "Iam in";
		require_once Yii::app()->basePath . '/vendor/autoload.php';
		$rootPath = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'fastway'.DIRECTORY_SEPARATOR.'fastwayTracking';
			$fileFullName = $rootPath.DIRECTORY_SEPARATOR.'637455381938557865.csv';
			$data = oExcel::getFileData($fileFullName);
			$data = $data[0];
			foreach ($data as $key => $row) {
				if(empty($row[2])) continue;
				$ref = $row[2];
				if($ref =="2X0005459202")
				{
					echo "2X0005459202";
				}
				$scanType = $row[4];
				$scanSmallType = $row[3];
				$descriptionSmall = $row[6];
				$description = $row[7];
				$this->cts = null;
				$imparcel = ImParcel::model()->find("ref = :ref",[":ref"=>$ref]);
				if(empty($imparcel)) continue;
				if(!empty($imparcel->trans))
				{
					foreach ($imparcel->trans as $key => $t) {
						if($t->org_id==Org::ORGID_COURIER_FASTWAY&&in_array($t->status,[11,19]))
						{
							$this->cts = $t;
							break;
						}
					}
				}
				if(empty($this->cts)) continue;
				$data = [];
				if ($scanType === 'D' && !empty($this->cts) && !preg_match('/Unable to Deliver|Onboard|Parcel Connect Agent|Calling Card Left|Parcel Connect CCL|Undeliverable/i',trim($descriptionSmall))&&$scanSmallType!='UND') {
					$this->cts->status = 99;
					$data['delivered'] = 1;
				}

				if ($scanType === 'T' && !empty($this->cts) && !preg_match('/Consignment Information Submitted/i',trim($descriptionSmall))) {
					$data['isTransit'] = 1;
				}
				$date = explode('/', $row[5]);
				$yearAndTime = explode(' ', $date[2]);

				$date = $yearAndTime[0].'-'.$date[1].'-'.$date[0].' '.$yearAndTime[1];
				$data[0] = $date;
				$data[1] = $description;
				$data[2] = $row[9];
				$data[3] = $row[3];
				// print_r($data);
				@$this->_upImTrack($this->cts, [$data]);
			}
		return false;
	}

	public function _updateAuspost($rs = false){

		if(empty($rs))
		{
			$day_shift = ($this->hod > 5 && $this->hod < 21);
			$cycle_base = $day_shift? 36 : 108;
			$tracking = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'_tracking_updateAupost.pid';
			if (file_exists($tracking) && preg_match("/tracking:(\d+)/i", file_get_contents($tracking), $match)) {
				$d = ($match[1]+1)%$cycle_base;
			} else {
				$d=0;
			}
			file_put_contents($tracking, 'tracking:'.$d.PHP_EOL);

			if($day_shift){
				$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN (101,1426) AND `time` > DATE_SUB(NOW(), INTERVAL 10 DAY) AND id % :cb = :r AND connote not like "349PR%"', [':cb' => $this->rrobin? $cycle_base : 1, ':r' => $d]);
			}else{
				$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN (101,1426) AND `time` > DATE_SUB(NOW(), INTERVAL 60 DAY) AND `time` < DATE_SUB(NOW(), INTERVAL 10 DAY) AND id % :cb = :r AND connote not like "349PR%"', [':cb' => $this->rrobin? $cycle_base : 1, ':r' => $d]);
			}
			$aup = [];
			foreach ($rs as $i=>$r)
			{
				if (empty($r->shipment) || @$r->skipTrack()) {
					continue;
				}
				$aup[] = $r;
			}
		}else
		{
			$d = 0;
			$cycle_base = 0;
			$aup = [];
			foreach ($rs as $i=>$r) {
				if (empty($r->shipment)) {
					continue;
				}
				$aup[] = $r;
			}
		}

		$c = 0;
		if(!empty($aup)){ //batch update aupost
			$msg = '=== Total '.count($aup).' aupost shipments. '.$d.'/'.$cycle_base.' ===';
			$timer = time();
			if ($this->debug) echo $msg, PHP_EOL;
			$this->log($msg);
			shuffle($aup);
			$ppg = 10;
			$pgs = ceil(count($aup) / $ppg);
			for($i=0; $i<$pgs; $i++){
				$ts = array_slice($aup, $i*$ppg, $ppg);
				$rs = $this->auPostApi($ts);

				$trans = Yii::app()->db->beginTransaction();
				try {
					foreach($ts as $t){
						$articleId = $t->imparcel->getParcelLongRef(Org::ORGID_COURIER_AUPOST)[0];
						if(isset($rs[$articleId])){
							$this->cts = &$t;
							$this->_upImTrack($t, $this->_prepAupostEvents($rs[$articleId]));
							$c++;
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
				sleep(10);
			}

			$td = time() - $timer;
			$tspan = '';
			if($td > 60){
				$tspan = floor($td / 60).'m';
			}
			$tspan .= ($td%60).'s';

			$msg = '===  '.$c.'/'.count($aup).' aupost updated in '.$tspan.'. '.$d.'/'.$cycle_base.' ===';
			if ($this->debug) echo $msg, PHP_EOL;
			$this->log($msg);
		}
	}


	public function _updateGVAuspost($rs = false){

		if(empty($rs))
		{
			$day_shift = ($this->hod > 5 && $this->hod < 21);
			$cycle_base = $day_shift? 36 : 108;
			$tracking = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'_tracking_GV_updateAupost.pid';
			if (file_exists($tracking) && preg_match("/tracking:(\d+)/i", file_get_contents($tracking), $match)) {
				$d = ($match[1]+1)%$cycle_base;
			} else {
				$d=0;
			}
			file_put_contents($tracking, 'tracking:'.$d.PHP_EOL);

			if($day_shift){
				$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN (3937) AND `time` > DATE_SUB(NOW(), INTERVAL 10 DAY) AND id % :cb = :r', [':cb' => $this->rrobin? $cycle_base : 1, ':r' => $d]);
			}else{
				$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN (3937) AND `time` > DATE_SUB(NOW(), INTERVAL 60 DAY) AND `time` < DATE_SUB(NOW(), INTERVAL 10 DAY) AND id % :cb = :r', [':cb' => $this->rrobin? $cycle_base : 1, ':r' => $d]);
			}
			$aup = [];
			foreach ($rs as $i=>$r)
			{
				if (empty($r->shipment) || @$r->skipTrack()) {
					continue;
				}
				$aup[] = $r;
			}
		}else
		{
			$aup = [];
			foreach ($rs as $i=>$r) {
				if (empty($r->shipment)) {
					continue;
				}
				$aup[] = $r;
			}
		}

		$c = 0;
		if(!empty($aup)){ //batch update aupost
			$msg = '=== Total '.count($aup).' aupost shipments. '.$d.'/'.$cycle_base.' ===';
			$timer = time();
			if ($this->debug) echo $msg, PHP_EOL;
			$this->log($msg);
			shuffle($aup);
			$ppg = 10;
			$pgs = ceil(count($aup) / $ppg);
			for($i=0; $i<$pgs; $i++){
				$ts = array_slice($aup, $i*$ppg, $ppg);
				$rs = $this->gvAuPostApi($ts);

				$trans = Yii::app()->db->beginTransaction();
				try {
					foreach($ts as $t){
						$articleId = $t->imparcel->getParcelLongRef(Org::ORGID_COURIER_AUPOST)[0];
						if(isset($rs[$articleId])){
							$this->cts = &$t;
							$this->_upImTrack($t, $this->_prepAupostEvents($rs[$articleId]));
							$c++;
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
				sleep(10);
			}

			$td = time() - $timer;
			$tspan = '';
			if($td > 60){
				$tspan = floor($td / 60).'m';
			}
			$tspan .= ($td%60).'s';

			$msg = '===  '.$c.'/'.count($aup).' GV aupost updated in '.$tspan.'. '.$d.'/'.$cycle_base.' ===';
			if ($this->debug) echo $msg, PHP_EOL;
			$this->log($msg);
		}
	}



	public function updateAuspost()
	{
		$refs =$this->prompt('refs ');
		$rs = Tranship::model()->with(['imparcel'])->findAll('t.org_id IN (101,1426) AND imparcel.ref in ('.$refs.') and imparcel.status !=90');
		$this->debug = true;
		$this->_updateAuspost($rs);
		print_r($this->errorRefs);
	}

	public function updateAuspostSF()
	{
		$rs = Tranship::model()->with(['imparcel'])->findAll('t.org_id = 101 AND imparcel.agent_id = 3333 and json_value(t.meta,"$.oid") is not null and (imparcel.cbwf &524288)>0 and t.status !=99');
		$this->debug = true;
		$this->_updateAuspost($rs);
	}

	public function _updateFastway(){
		$day_shift = ($this->hod > 5 && $this->hod < 21);
		$cycle_base = $day_shift? 36 : 108;
		$tracking = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'_tracking_updateFastway.pid';
		if (file_exists($tracking) && preg_match("/tracking:(\d+)/i", file_get_contents($tracking), $match)) {
			$d = ($match[1]+1)%$cycle_base;
		} else {
			$d=0;
		}
		file_put_contents($tracking, 'tracking:'.$d.PHP_EOL);

		if($day_shift){
			$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN (115) AND `time` > DATE_SUB(NOW(), INTERVAL 5 DAY) AND id % :cb = :r', [':cb' => $this->rrobin? $cycle_base : 1, ':r' => $d]);
		}else{
			$rs = Tranship::model()->findAll('status IN (11,19) AND org_id IN (115) AND `time` > DATE_SUB(NOW(), INTERVAL 60 DAY) AND `time` < DATE_SUB(NOW(), INTERVAL 5 DAY) AND id % :cb = :r', [':cb' => $this->rrobin? $cycle_base : 1, ':r' => $d]);
		}

		$bfw = [];
		foreach ($rs as $i=>$r) {
			if (empty($r->shipment) || @$r->skipTrack()) {
				continue;
			}
			$bfw[] = $r;
		}

		$c = 0;
		if(!empty($bfw)){
			$msg = '=== Total '.count($bfw).' fastway shipments. '.$d.'/'.$cycle_base.' ===';
			$timer = time();
			if ($this->debug) echo $msg, PHP_EOL;
			$this->log($msg);
			shuffle($bfw);

			$ppg = 100;
			$pgs = ceil(count($bfw) / $ppg);
			for($i=0; $i<$pgs; $i++){
				$ts = array_slice($bfw, $i*$ppg, $ppg);
				$rs = $this->fastway($ts);
				$trans = Yii::app()->db->beginTransaction();
				try {
					foreach($ts as $t){
						if(isset($rs[$t->connote])){
							$this->cts = &$t;
							$this->_upImTrack($t, $this->_prepFastwayEvents($rs[$t->connote]));
							$c++;
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
			}
			$td = time() - $timer;
			$tspan = '';
			if($td > 60){
				$tspan = floor($td / 60).'m';
			}
			$tspan .= ($td%60).'s';

			$msg = '===  '.$c.'/'.count($bfw).' fastway updated in '.$tspan.'. '.$d.'/'.$cycle_base.' ===';
			if ($this->debug) echo $msg, PHP_EOL;
			$this->log($msg);
		}
	}

	public function _updateFastwayTesting(){
		$rs = Tranship::model()->with(["shipment","shipment.consol"])->findAll("t.org_id = 115 and t.status !=99 and t.status >=10 and consol.id in (63279)");
		$bfw = [];
		foreach ($rs as $i=>$r) {
			if (empty($r->shipment) || @$r->skipTrack()) {
				continue;
			}
			$bfw[] = $r;
		}

		$c = 0;
		if(!empty($bfw)){
			$msg = '=== Total '.count($bfw).' fastway shipments. '.$d.'/'.$cycle_base.' ===';
			$timer = time();
			if ($this->debug) echo $msg, PHP_EOL;
			$this->log($msg);
			shuffle($bfw);

			$ppg = 100;
			$pgs = ceil(count($bfw) / $ppg);
			for($i=0; $i<$pgs; $i++){
				$ts = array_slice($bfw, $i*$ppg, $ppg);
				$rs = $this->fastway($ts);
				$trans = Yii::app()->db->beginTransaction();
				try {
					foreach($ts as $t){
						if(isset($rs[$t->connote])){
							 echo "1";
							$this->cts = &$t;
							$this->_upImTrack($t, $this->_prepFastwayEvents($rs[$t->connote]));
							$c++;
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
			}
			$td = time() - $timer;
			$tspan = '';
			if($td > 60){
				$tspan = floor($td / 60).'m';
			}
			$tspan .= ($td%60).'s';

			$msg = '===  '.$c.'/'.count($bfw).' fastway updated in '.$tspan.'. '.$d.'/'.$cycle_base.' ===';
			if ($this->debug) echo $msg, PHP_EOL;
			$this->log($msg);
		}
	}

	public function _upImTrack($r, $data=null)
	{
		if($r->shipment->status==ImParcel::STATE_HELD)
		{
			return;
		}
		$this->cts = &$r;
		if(empty($data)){
			switch ($r->org_id) {
				case Org::ORGID_COURIER_AUPOST: //aus post
				case Org::ORGID_COURIER_D2Z: // D2z
					$data = $this->auPostApi($r->connote);
					$data = $this->_prepAupostEvents($data);
				break;
				case Org::ORGID_COURIER_GV_AUPOST:
					$data = $this->gvAuPostApi($r->connote);
					$data = $this->_prepAupostEvents($data);
				break;
				case Org::ORGID_COURIER_FASTWAY: // Fastway
					$data = $this->fastway($r->connote);
					$data = $this->_prepFastwayEvents($data);
				break;
				case 102: //couriers please
					$data = $this->couriersPlease($r->connote);
				break;
				case Org::ORGID_COURIER_STARTRACK: //star track
					$data = $this->starTrackApi($r->connote);
				break;
				case Org::ORGID_COURIER_TOLL:
					$data = $this->tollTrack($r->connote);
				break;
				case Org::ORGID_COURIER_TNT:
					$data = $this->tntTrack($r->connote);
				break;
				case ORG::ORGID_COURIER_HUNTER:
					$data = $this->hunterTrack($r->connote);
				break;
				case Org::ORGID_COURIER_UBI: // UBI
					if(preg_match('/^8/', $r->connote))
					{
						$rs = $this->myTollApi([$r]);
						$data = !empty($rs[$r->connote])?$this->_prepTollIPECEvents($rs[$r->connote]):[];
					}else
					{
						$data = $this->ubiApi([$r]);
						$data = !empty($data[$t->mdata['barcodeLabelNumber']])?$this->_prepUbiEvents($data[$t->mdata['barcodeLabelNumber']]):[];
					}
				break;
				case Org::ORGID_COURIER_AUK:
				case Org::ORGID_COURIER_TOLL_IPEC: // TOLL IPEC
					$rs = $this->myTollApi([$r]);
					$data = !empty($rs[$r->connote])?$this->_prepTollIPECEvents($rs[$r->connote]):[];
				break;
				case Org::ORGID_COURIER_DFE: // DFE
					$data = $this->dfeApi($r);
					$data = $this->_prepDfeEvents($data);
				break;
				case Org::ORGID_COURIER_CHUKOU1: // CK1
					$data = $this->ck1Api($r);
					$data = $this->_prepChukou1Events($data[$r->connote]);
				break;
				case Org::ORGID_COURIER_BORDER: // BORDER
					$data = $this->borderApi([$r]);
					$data = $this->_prepBorderEvents($data[$r->connote]);
				break;
			}
		}
		if (empty($data)) {
			if ($r->status == 0) {//tranship cancelled
				$r->save();
				if ($this->debug) {
					echo "Tanshipment cancelled.\n";
				}
			} elseif ($this->debug) {
				echo "No data found!\n";
			}
			return false;
		} else {
			if (in_array($r->org_id, [858,115,Org::ORGID_COURIER_DFE_TOP,Org::ORGID_COURIER_TNT_TOP])&&!empty($r->shipment)) {
				// $r->refresh();
				$r->shipment->msgNotice();
			}
		}

		foreach ($data as $key => $t) {
			if (empty($t[1]) || empty($t[0])) {
				continue;
			}
			if (empty($r->shipment)) {
				continue;
			}
			
			if($r->org_id==Org::ORGID_COURIER_UBI)
			{
				if (!empty($t[3])){		// data is captured from ubi api
					if($t[3] == 'CCD')
					{	
						$r->shipment->addTracking(60, $t[1], $t[2], $t[0], $r->id);
					}else if($t[3] == 'HLD')
					{
						$r->shipment->addTracking(55, $t[1], $t[2], $t[0], $r->id);
					}else
					{
						$r->shipment->addTracking(70, $t[1], $t[2], $t[0], $r->id);
					}
				}else		// data is captured from MyTollApi
				{
					if($r->status > 90&&!empty($t['delivered']))
					{
						$r->shipment->addTracking(90, $t[1], $t[2], $t[0], $r->id);
					}else
					{
						$r->shipment->addTracking(70, $t[1], $t[2], $t[0], $r->id);
					}
				}

			}else
			{
				if($r->status > 90&&!empty($t['delivered']))
				{
					$r->shipment->addTracking(90, $t[1], $t[2], $t[0], $r->id);
				}else
				{
					$r->shipment->addTracking(70, $t[1], $t[2], $t[0], $r->id);
				}
			}
		}

		//update status
		if($r->org_id==Org::ORGID_COURIER_UBI && !empty($t['3']))
		{
			$t = $data[0];
			$r->shipment->refresh();
			if($t['3'] == 'CCD')
			{	
				if($r->shipment->status<ImParcel::STATE_CLEAR)
				{
					$r->shipment->status = ImParcel::STATE_CLEAR;
					$r->shipment->update(['status']);
				}
			}else if($t['3'] == 'HLD')
			{
				if($r->shipment->status<ImParcel::STATE_HELD)
				{
					$r->shipment->status = ImParcel::STATE_HELD;
					$r->shipment->update(['status']);
				}
			}else if(in_array($t['3'],['BSE','DLV','CRD','SCN']))
			{
				if(($r->shipment->status<ImParcel::STATE_COURIER&& ($r->shipment->scan_no < $r->shipment->pkg) && $r->shipment->scanAfter8h())||!empty($r->shipment->getTranshipImparcel()))
				{
					$r->shipment->status = ImParcel::STATE_COURIER;
					$r->shipment->update(['status']);
				}
			}

			if($r->status > 90) {
				if ($r->shipment->status<80 && $r->shipment->status > 40) {
					$r->shipment->status = 90;
					$r->shipment->update(['status']);
				}
			}
			$r->save();
			return;
		}

		//update status
		if($r->org_id==Org::ORGID_COURIER_FASTWAY)//TODO this is redundent code, only for fastway
		{
			$t = end($data);
			$r->shipment->refresh();
			//update status
			if ($r->status > 90) {
				if ($r->shipment->status<80 && in_array($r->shipment->status, [42,60,70])) {
					$r->shipment->status = 90;
					$r->shipment->update(['status']);
				}
			} elseif (in_array($r->shipment->status, [42,60]) && ($r->shipment->scan_no == 0)&&!empty($t['isTransit'])&& $r->shipment->scanAfter8h()) {
				$r->shipment->status = 70;
				$r->shipment->update(['status']);
			}
		}else
		{
			//update status
			if ($r->status > 90) {
				if ($r->shipment->status<80 && (in_array($r->shipment->status, [42,60,70])||$r->shipment->status==25&&!empty($r->shipment->getTranshipImparcel()))) {
					$r->shipment->status = 90;
					$r->shipment->update(['status']);
				}
			} elseif (($r->shipment->status == 60) && ($r->shipment->scan_no == 0) && $r->shipment->scanAfter8h()) {
				$r->shipment->status = 70;
				$r->shipment->update(['status']);
			} else {
				// in case local delivery parcel
				// if tranship status existing
				// which means parcel is in courier
				if (!empty($data)) {
					if ($r->shipment->status < 70 && !in_array($r->shipment->status, [50,55,57,58]) && ($r->shipment->cbwf & 32) == 0 && empty($r->shipment->mdata['direct_courier']) && !in_array($r->shipment->agent_id, [1427, 1656, 1689])&& ($r->shipment->scan_no < $r->shipment->pkg)&& $r->shipment->scanAfter8h()) {
						$r->shipment->status = 70;
						$r->shipment->update(['status']);
					}
				}
			}
		}

		$lt = $r->getLastTrack();
		if (!empty($lt) && strtotime($lt->dt) < time() - 864000) {
			$r->time = $lt->dt;
		} else {
			$r->time = date('Y-m-d H:i:s');
		}
		$r->save();
	}

	public function _updateExport()
	{
		//update flight info
		$rs = ExcoConsol::model()->findAll("status IN (20,40,70)");
		if ($this->debug) {
			echo '=== Found '.sizeof($rs)." export consol to update flight info. ===\n";
		}

		$trans = Yii::app()->db->beginTransaction();
		try {
			foreach ($rs as $r) {
				if ($r->created == date('Y-m-d') && date('G') < 18) {
					continue;
				}
				$rup = false;
				if (!empty($r->mdata['subc'])) {//sub consols
					$scc = sizeof($r->mdata['subc']);
					$comp = 0;
					$dp = false;
					foreach ($r->mdata['subc'] as $i => $sc) {
						if (empty($sc['flight']) || empty($sc['etd'])) {
							continue;
						}
						if (strtotime($sc['etd']) > time() || strtotime($sc['etd']) < strtotime('-15 day')) {
							continue;
						}
						if (!empty($sc['status']) && $sc['status'] == 20) {
							$comp++;
							continue;
						}
						$d = $this->flight($sc['flight'], $sc['etd']);
						if (!empty($d)) {
							$ms = Manifest::model()->findAll('id IN ('.$sc['pids'].')');

							if (empty($sc['status']) && $d[7] > 0) {//departure
								$r->mdata['subc'][$i]['status'] = 10;
								$rup = true;
								$dp = true;
								foreach ($ms as $m) {
									foreach ($m->lines as $l) {
										$p = $l->mm();
										if ($p->status >= 60) {
											continue;
										}
										$dpt = $d[2];
										if ($p->odpt_id == 218) {
											$dpt = 'Melbourne Tullamarine (YMML / MEL)';
										}
										if ($p->odpt_id == 529) {
											$dpt = 'Adelaide Int\'l (YPAD / ADL)';
										}
										if ($p->odpt_id == 530) {
											$dpt = 'Brisbane (YBBN / BNE)';
										}
										$p->addTracking(38, '空运航班飞离始发港', $dpt, $d[4]);
										$p->status = 60;
										$p->update(['status']);
									}
								}
							} elseif ($d[7] == 2) {//arrival
								$r->mdata['subc'][$i]['status'] = 20;
								$rup = true;
								$comp++;
								foreach ($ms as $m) {
									foreach ($m->lines as $l) {
										$p = $l->mm();
										if ($p->status >= 70) {
											continue;
										}
										$p->addTracking(40, '空运航班抵达目的港', '', $d[5]);
										$p->status = 70;
										$p->update(['status']);
									}
								}
							}
						}
					}

					if ($comp == $scc) {
						if ($r->status == 70) {
							$r->status = 72;
						}
						$rup = true;
					} elseif ($dp) {
						if ($r->status < 70) {
							$r->status = 70;
						}
						$rup = true;
					}
					if ($rup) {
						$r->save();
					}

					continue;
				}

				if (empty($r->flight) || empty($r->etd) || $r->etd == '0000-00-00') {
					continue;
				}
				if (strtotime($r->etd) > time() || strtotime($r->etd) < strtotime('-30 day')) {
					continue;
				}

				$d = $this->flight($r->flight, $r->etd, true);
				if (!empty($d)) {
					if ($r->status < 70 && $d[7] > 0) { //dispatched
						$r->status = 70;
						$r->save();
						foreach ($r->shipments as $p) {
							$p->addTracking(38, '空运航班飞离始发港', $d[2], $d[4]);
							$p->status = 60;
							$p->update(['status']);
						}
					} elseif ($r->status == 70 && $d[7] == 2) { //arrival
						$r->status = 72;
						$r->save();
						foreach ($r->shipments as $p) {
							$p->addTracking(40, '空运航班抵达目的港', '', $d[5]);
							$p->status = 70;
							$p->update(['status']);
						}
					}
				}
			}
			$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}

		//update clearance
		$rs = ExParcel::model()->findAll('status = 70 AND consol_id > 0');
		if ($this->debug) {
			echo '=== Found '.sizeof($rs)." export shipments in arrived. ===\n";
		}
		$stclrs = [];
		$pocs = [];
		$ccs = [];
		foreach ($rs as $p) {
			if (empty($pocs[$p->consol_id])) {
				$pocs[$p->consol_id] = $p->consol->poc;
			}
			//if($pocs[$p->consol_id] == 'CNXMN' && preg_match('/^DG16\d+/', $p->ref)) continue;
			foreach ($p->tracks as $t) {
				if ($t->type != 40) {
					continue;
				}
				// if($pocs[$p->consol_id] == 'CNCAN'){ //46-50 hours
				// $tlo = 165600;
				// $thi = 180000;
				// }else{ //22-26 hours
				$tlo = 79200;
				$thi = 93600;
				// }
				if (strtotime($t->dt) <= time() - $thi) { //after 50 hours of arrival
					$cc = $this->poc2cc($pocs[$p->consol_id], $p->ref);
					if ($cc[1] == 'sto' && preg_match('/^81\d+/', $p->ref)) {
						$cc[1] = 'yto';
					}
					if (!isset($ccs[$p->consol_id])) {
						$ccs[$p->consol_id] = $p->consol;
					}
					$stclrs[$cc[1]][$p->ref] = [strtotime($t->dt) + rand($tlo, $thi), $p];
					break;
				}
			}
		}

		$c = 0;
		foreach ($stclrs as $cc => $trs) {
			$cs = ceil(sizeof($trs) / 100);
			for ($j = 0; $j < $cs; $j++) {
				$rs = array_slice($trs, $j*100, 100, true);
				$trans = Yii::app()->db->beginTransaction();
				try {
					if ($this->KdniaoSubs(array_keys($rs), $cc)) {
						foreach ($rs as $p) {
							$p[1]->addTracking(50, '到库分拣完成，开始清关', '', date('Y-m-d H:i:s', $p[0]));
							$p[1]->status = 80;
							$p[1]->save();
							$c++;
						}
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
			}
		}

		$trans = Yii::app()->db->beginTransaction();
		try {
			foreach ($ccs as $csl) {
				if ($csl->status < 75) {
					$csl->status = 75;
					$csl->save();
				}
			}
			$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}
		if ($this->debug) {
			echo '=== Total '.$c." shipments started clearance. ===\n";
		}

		//re sub kdn
		if ($this->debug) {
			echo "=== KDN resubscription ===\n";
		}
		$rt = ExParcel::model()->count('status = 80 AND created > DATE_SUB(NOW(), INTERVAL 60 DAY)');
		$ppg = 2000;
		$tpg = ceil($rt / $ppg);
		$c = 0;

		$trans = Yii::app()->db->beginTransaction();
		try {
			for ($i = 0; $i < $tpg; $i++) {
				$rs = ExParcel::model()->findAll(['condition' => 'status = 80 AND created > DATE_SUB(NOW(), INTERVAL 60 DAY)', 'limit' => $ppg, 'offset' => $i * $ppg]);
				$stclrs = [];
				$pocs = [];
				foreach ($rs as $p) {
					if (empty($p->ref)) {
						continue;
					}
					if (empty($p->consol_id)) {
						$pocs[0] = 'FWDEMS';
					}
					if (empty($pocs[$p->consol_id])) {
						$pocs[$p->consol_id] = $p->consol->poc;
					}
					if ($pocs[$p->consol_id] == 'CNXMN' && preg_match('/^DG16\d+/', $p->ref)) {
						continue;
					}
					if ($pocs[$p->consol_id] == 'CNCA3' && preg_match('/^8800\d+/', $p->ref)) {
						continue;
					}
					$lt = $p->getLastTrack();
					if ($lt->type != 50) {
						continue;
					}
					$lst = empty($lt->mdata['kdnts'])? strtotime($lt->dt) : $lt->mdata['kdnts'];
					if ($lst <= time() - 172800) { //after 48 hours
						$cc = $this->poc2cc($pocs[$p->consol_id], $p->ref);
						if ($cc[1] == 'sto' && preg_match('/^81\d+/', $p->ref)) {
							$cc[1] = 'yto';
						}
						$stclrs[$cc[1]][$p->ref] = $lt;
					}
				}

				foreach ($stclrs as $cc => $trs) {
					$cs = ceil(sizeof($trs) / 100);
					for ($j = 0; $j < $cs; $j++) {
						$rs = array_slice($trs, $j*100, 100, true);
						if ($this->KdniaoSubs(array_keys($rs), $cc)) {
							foreach ($rs as $lt) {
								$lt->mdata['kdnts'] = time();
								$lt->update('meta');
								$c++;
							}
						}
					}
				}
			}
			$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}
		if ($this->debug) {
			echo '=== Total '.$c." subscibed again. ===\n";
		}
		
		$this->exConsolUpdate();
		//$this->fixClearance();
		//check courier dispatch
		/*
		$rs = ExParcel::model()->findAll('status = 80 AND created > DATE_SUB(NOW(), INTERVAL 60 DAY)');
		$this->_exCourierDispatch($rs);

		//update tracking
		$rs = Tranship::model()->findAll("status IN (11,19) AND org_id IN (128,382,488,494,495) AND `type` = 80 AND `time` > DATE_SUB(NOW(), INTERVAL 20 DAY) AND `time` < DATE_SUB(NOW(), INTERVAL 2 HOUR)");
		if($this->debug) echo '=== Found '.sizeof($rs)." export shipments for updating.===\n";
		$c = 0;
		foreach($rs as $i=>$r){
			if($r->skipTrack() || ($this->rrobin && $i % 7 != $this->mmod)) continue;
			$this->_upExTrack($r);
			$c++;
		}
		if($this->debug) echo '=== Total '.$c." export shipments updated. ===\n";
		*/
	}

	protected function _exCourierDispatch($rs)
	{
		if ($this->debug) {
			echo '=== Found '.sizeof($rs)." export shipments in clearance. ===\n";
		}
		$scc = [];
		$pgs = ceil(count($rs) / 10);

		for ($j = 0; $j < $pgs; $j++) {
			$trans = Yii::app()->db->beginTransaction();
			try {
				foreach (array_slice($rs, $j*10, 10) as $i=>$p) {
					if (empty($p->ref) || (isset($scc[$p->consol_id]) && $scc[$p->consol_id] > 9) || ($this->rrobin && $i % 7 != $this->mmod)) {
						continue;
					}
					//if(isset($p->mdata['ems_last_pending']) && $p->mdata['ems_last_pending'] > time() - 7200) continue;
					//$lt = $p->getLastTrack();
					//if(!$lt) continue;
					//if(strtotime($lt->dt) > time() - 86400) continue;
					//if($this->debug) echo 'Looking for '.$p->ref."\n";
					$d = $this->exTracking($p->ref, $p->consol->poc);
					/*if(empty($d) && strtotime($lt->dt) < time() - 864000){// > 10 days
						$cc = $this->poc2cc($p->consol->poc, $p->ref);
						if($cc[1] == 'ems')	$d = $this->kdcom($p->ref);
					}*/
					if (!empty($d)) {
						//$p->mdata['ems_last_pending'] = time();
						//$p->updateMeta();
						/*$c = ExParcel::model()->count('consol_id = :cid AND status > 80', [':cid' => $p->consol_id]);
						if($c == 0){
							if(!isset($scc[$p->consol_id])) $scc[$p->consol_id] = 0;
							$scc[$p->consol_id]++;
						}
					}else{*/
						$p->status = 90;
						$p->addTracking(60, '清关完成开始派送', @$d[0][2], $d[0][0]);
						if (!empty($p->consol_id) && $p->consol->status < 80) {
							$p->consol->status = 80;
							$p->consol->save();
						}
						$c = Tracking::model()->find("type = 50 AND pid = ".$p->id);
						if (strtotime($c->dt) > strtotime($d[0][0])) { //quick clearance
							$a = Tracking::model()->find("type = 40 AND pid = ".$p->id);
							$dt = strtotime($c->dt) - strtotime($a->dt);
							if ($dt > 0) {
								$dt = floor($dt / 2) - 7200 + rand(0, 14400) + strtotime($a->dt);
								$c->dt = date('Y-m-d H:i:s', $dt);
								$c->save();
							}
						}
						unset($p->mdata['ems_last_pending']);
						$p->save();
						$ts = new Tranship;
						$ts->type = 80;
						$ts->pid = $p->id;
						$cc = $this->poc2cc($p->consol->poc, $p->ref);
						$ts->org_id = $cc[0];
						$ts->connote = $p->ref;
						$ts->status = 19;
						$ts->time = date('Y-m-d H:i:s');
						$ts->save();
						foreach ($d as $t) {
							if (empty($t[1]) || empty($t[0])) {
								continue;
							}
							$p->addTracking($t[3], $t[1], @$t[2], $t[0], $ts->id);
							if ($t[3] == 90) {
								$p->status = 99;
								$p->save();
								$ts->status = 99;
								$ts->save();
							}
						}
					}
				}
				$trans->commit();
			} catch (Exception $ex) {
				$trans->rollback();
				throw $ex;
			}
		}
	}

	protected function _upExTrack($r, $poc = false)
	{
		$this->cts = &$r;
		$data = $this->exTracking($r->connote, !$poc? $r->shipment->consol->poc: $poc);
		if (empty($data)) {
			return false;
		}

		foreach ($data as $t) {
			if (empty($t[1]) || empty($t[0])) {
				continue;
			}
			$r->shipment->addTracking($t[3], $t[1], @$t[2], $t[0], $r->id);
		}

		//update status
		if ($r->status > 90) {//delivered
			$r->shipment->status = 99;
			$r->shipment->save();
		} elseif ($r->shipment->status < 90) {
			$r->shipment->status = 90;
			$r->shipment->save();
		}

		$r->time = date('Y-m-d H:i:s');
		$r->save();
	}

	public function _updateDirect()
	{
		$rs = ExDirect::model()->findAll('status = 50');
		if ($this->debug) {
			echo '=== Found '.sizeof($rs)." direct shipments in processing. ===\n";
		}
		$scc = [];
		foreach ($rs as $i=>$p) {
			if (empty($p->ref) || $scc[$p->consol_id] > 9 || ($this->rrobin && $i % 7 != $this->mmod)) {
				continue;
			}
			$lt = $p->getLastTrack();
			if (!$lt) {
				continue;
			}
			$d = $this->apix2($p->ref, '顺丰速运');
			if (empty($d)) {
				$p->mdata['ems_last_pending'] = time();
				$p->updateMeta();
				$c = ExParcel::model()->count('consol_id = :cid AND status > 80', [':cid' => $p->consol_id]);
				if ($c == 0) {
					if (!isset($scc[$p->consol_id])) {
						$scc[$p->consol_id] = 0;
					}
					$scc[$p->consol_id]++;
				}
			} else {
				$p->status = 90;
				unset($p->mdata['ems_last_pending']);
				$p->save();
				$ts = new Tranship;
				$ts->type = 80;
				$ts->pid = $p->id;
				$ts->org_id = 496;
				$ts->connote = $p->ref;
				$ts->status = 19;
				$ts->time = date('Y-m-d H:i:s');
				$ts->save();
				foreach ($d as $t) {
					if (empty($t[1]) || empty($t[0])) {
						continue;
					}
					$p->addTracking($t[3], $t[1], $t[2], $t[0], $ts->id);
					if ($t[3] == 90) {
						$p->status = 99;
						$p->save();
						$ts->status = 99;
						$ts->save();
					}
				}
			}
		}

		//update tracking
		$rs = Tranship::model()->findAll("status IN (11,19) AND org_id IN (496) AND `type` = 80 AND `time` > DATE_SUB(NOW(), INTERVAL 20 DAY) AND `time` < DATE_SUB(NOW(), INTERVAL 2 HOUR)");
		if ($this->debug) {
			echo '=== Found '.sizeof($rs)." direct shipments for updating.===\n";
		}
		$c = 0;
		foreach ($rs as $i=>$r) {
			if ($r->skipTrack() || ($this->rrobin && $i % 7 != $this->mmod)) {
				continue;
			}
			$this->_upExTrack($r, 'SFDIR');
			$c++;
		}
		if ($this->debug) {
			echo '=== Total '.$c." direct shipments updated. ===\n";
		}
	}

	protected function _updateFWD()
	{
		$aids = [672];
		$rs = ExParcel::model()->findAll('agent_id IN ('.implode(',', $aids).') AND status IN (10, 15, 18, 20, 25, 60, 70)');
		foreach ($rs as $r) {
			$lt = $r->getLastTrack();
			$lt_ts = strtotime($lt->dt);
			$lt_date = date('Y-m-d', $lt_ts);
			$lt_hr = date('G', $lt_ts);
			switch ($r->status) {
				case 10: //new
					$nts = strtotime($lt_date) + rand(64800, 72000) + ($lt_hr > 16? 86400 : 0);
					$dow = date('N', $nts);
					if ($dow > 5) {
						$nts += (8 - $dow) * 86400;
					}
					$tsc = 15;
				break;
				case 15: //rcvd
					$nts = strtotime($lt_date) + rand(172800, 432000);
					$tsc = 18;
				break;
				case 18: //info ready
					$nts = strtotime($lt_date) + rand(34200, 37800) + ($lt_hr < 9? 0 : 86400);
					$dow = date('N', $nts);
					if ($dow > 5) {
						$nts += (8 - $dow) * 86400;
					}
					$tsc = 20;
				break;
				case 20: //consol
					$nts = $lt_ts + rand(1800, 7200);
					$tsc = 25;
				break;
				case 25: //confirmed
				case 60: //dispatched
					$nts = time() + 3600;
					if (empty($r->mdata['consol'])) {
						$cs = ExcoConsol::model()->findAll('created >= :d AND created < DATE_ADD(:d, INTERVAL 4 DAY) AND status > 20 AND pol = :pol', [':d' => $lt_date, ':pol' => 'AUSYD']);
						foreach ($cs as $c) {
							if (!empty($c->flight)) {
								$r->mdata['consol'] = [
									'flight' => $c->flight,
									'etd' => $c->etd,
								];
							} else {
								if (!empty($c->mdata['subc'])) {
									foreach ($c->mdata['subc'] as $k=>$sc) {
										if (!empty($sc['flight'])) {
											$r->mdata['consol'] = [
												'flight' => $sc['flight'],
												'etd' => $sc['etd'],
											];
											break;
										}
									}
								}
							}
							if (!empty($r->mdata['consol'])) {
								$r->mdata['consol']['id'] = $c->id;
								$r->mdata['consol']['poc'] = $c->poc;
								$r->save();
								break;
							}
						}
					} else {
						$d = $this->flight($r->mdata['consol']['flight'], $r->mdata['consol']['etd'], true);
						if (!empty($d)) {
							if ($r->status < 60 && $d[7] > 0) { //dispatched
								$r->addTracking(38, '空运航班飞离始发港', $d[2], $d[4]);
								$r->status = 60;
								$r->save();
							} elseif ($r->status < 70 && $d[7] == 2) { //arrival
								$r->addTracking(40, '空运航班抵达目的港', '', $d[5]);
								$r->status = 70;
								$r->save();
							}
						}
					}
				break;
				case 70: //arrived
					$nts = $lt_ts + rand(165600, 180000);
					$tsc = 80;
				break;
			}
			if (time() >= $nts) {
				$r->status = $tsc;
				if ($tsc == 15) {
					$r->checkInfoReady();
					$r->odpt_id = 106;
				}
				$dpt = empty($r->odepot)? '' : $r->odepot->suburb;
				$dt = date('Y-m-d H:i:s', $nts);
				switch ($r->status) {
					case 15:
						$r->addTracking(15, '运单信息已输入，身份证信息未上传，留库待发', $dpt, $dt);
					break;
					case 18:
						$r->addTracking(18, '身份证信息已匹配成功，等待发运', $dpt, $dt);
					break;
					case 20:
						$r->addTracking(20, '中国口岸进口预申报已完成，等待清关处理', $dpt, $dt);
					break;
					case 25:
						$r->addTracking(25, '理货打板已毕，发离仓库', $dpt, $dt);
					break;
					case 80:
						$r->addTracking(50, '到库分拣完成，开始清关', '', $dt);
					break;
				}
				$r->save();
			}
			if (empty($r->ref) && !empty($r->cref)) {
				$r->ref = $r->cref;
				$r->save();
			}
		}
	}

	public function update($tn = false)
	{
		$tn = !$tn? $this->args[1] : $tn;
		$s = Shipment::model()->find('hbn = :hbn', [':hbn' => $tn]);
		if (empty($s)) {
			return;
		}
		$this->rrobin = false;
		if ($s->type == 20 && $s->status == 80) {
			return $this->_exCourierDispatch([$s]);
		}
		if (!$s) {
			die($tn.", not found\n");
		}
		foreach ($s->trans as $r) {
			if($r->skipTrack()) continue;
			if (in_array($s->type, [10,30])) { //import
				$this->_upImTrack($r);
			} elseif ($s->type == 20) { //export
				$this->_upExTrack($r);
			}
		}
//		echo "Done\n";
	}

	public function exConsolUpdate()
	{
		$this->rrobin = false;
		$rs = ExcoConsol::model()->findAll('status = 80 AND etd > DATE_SUB(NOW(), INTERVAL 2 MONTH)');
		foreach ($rs as $r) {
			$s90r = round(ExParcel::model()->count('status > 80 AND consol_id = :cid', [':cid' => $r->id]) / count($r->shipments) * 100);
			if ($s90r > 75 && $s90r < 99) {
				if ($this->debug) {
					echo $r->no.PHP_EOL;
				}
				$ss = [];
				foreach ($r->shipments as $s) {
					if ($s->status > 80) {
						continue;
					}
					if (empty($s->mdata['ems_last_pending']) || $s->mdata['ems_last_pending'] < time() - 10800) {
						$ss[] = $s;
						$s->mdata['ems_last_pending'] = time();
						$s->updateMeta();
					}
				}
				$this->_exCourierDispatch($ss);
			}
		}
		echo 'Done', PHP_EOL;
	}

	public function fixAgentClearance()
	{
		$rs = ExParcel::model()->with('consol')->findAll('t.agent_id = :aid AND t.status = 80 AND consol.status = 80 AND consol.etd < DATE_SUB(NOW(), INTERVAL 7 DAY) AND consol.etd > DATE_SUB(NOW(), INTERVAL 90 DAY)', [':aid' => $this->args[1]]);
		$this->rrobin = false;
		$this->_exCourierDispatch($rs);

		$rs = ExParcel::model()->with('consol')->findAll('t.agent_id = :aid AND t.status = 90 AND consol.status = 80 AND consol.etd < DATE_SUB(NOW(), INTERVAL 7 DAY) AND consol.etd > DATE_SUB(NOW(), INTERVAL 90 DAY)', [':aid' => $this->args[1]]);
		if ($this->debug) {
			echo '=== Found '.sizeof($rs)." courier shipments for updating.===\n";
		}
		foreach ($rs as $i=>$r) {
			foreach ($r->trans as $t) {
				if (in_array($t->status, [11, 19])) {
					$this->_upExTrack($t);
				}
			}
		}
	}

	public function exConsolCourier()
	{
		$this->rrobin = false;

		$rs = ExParcel::model()->findAll('status = 80 AND consol_id = :cid', [':cid' => $this->args[1]]);
		$this->_exCourierDispatch($rs);

		$ols = [];

		foreach ($rs as $r) {
			$ols[] = $r->id;
		}

		return;
		$rs = ExParcel::model()->findAll('status = 90 AND consol_id = :cid', [':cid' => $this->args[1]]);
		if ($this->debug) {
			echo '=== Found '.sizeof($rs)." export shipments for updating.===\n";
		}
		foreach ($rs as $i=>$r) {
			if (in_array($r->id, $ols)) {
				continue;
			}
			foreach ($r->trans as $t) {
				if (in_array($t->status, [11, 19])) {
					$this->_upExTrack($t);
				}
			}
		}
	}

	public function auspostCourier()
	{
		$rs = ExParcel::model()->findAll("status >= 25 AND status < 99 AND ref LIKE 'ES9%AU'");
		foreach ($rs as $r) {
			if ($this->debug) {
				echo 'updating '. $r->ref."\n";
			}
			if (empty($r->trans)) {
				$ts = new Tranship;
				$ts->type = 80;
				$ts->pid = $r->id;
				$ts->org_id = 101;
				$ts->connote = $r->ref;
				$ts->status = 19;
				$ts->time = date('Y-m-d H:i:s');
				$ts->save();
				$r->refresh();
			}
			foreach ($r->tracks as $k) {
				if ($k->type == 25) {
					$k->delete();
				}
			}
			foreach ($r->trans as $t) {
				if (in_array($t->status, [11, 19])) {
					$this->_upExTrack($t, 'AUPOST');
				}
			}
		}
	}

	public function exConsolResubKDN()
	{
		$rs = ExParcel::model()->with('consol')->together()->findAll('t.status IN (80,90) AND consol.no = :cid', [':cid' => $this->args[1]]);
		$stclrs = [];
		$c = 0;
		$poc = $rs[0]->consol->poc;
		var_dump($poc);
		foreach ($rs as $p) {
			if ($poc == 'CNXMN' && preg_match('/^DG16\d+/', $p->ref)) {
				continue;
			}
			if ($poc == 'CNCA3' && preg_match('/^8800\d+/', $p->ref)) {
				continue;
			}
			$lt = $p->getLastTrack();
			if ($lt->type < 50) {
				echo $p->hbn,' ',$lt->type,"\n";
			}
			$cc = $this->poc2cc($poc, $p->ref);
			if ($cc[1] == 'sto' && preg_match('/^81\d+/', $p->ref)) {
				$cc[1] = 'yto';
			}
			$stclrs[$cc[1]][$p->ref] = $lt;
		}

		$trans = Yii::app()->db->beginTransaction();
		try {
			foreach ($stclrs as $cc => $trs) {
				$ts = sizeof($trs);
				if ($this->debug) {
					echo 'Resub. '.$cc.' '.$ts."\n";
				}
				$cs = ceil($ts / 100);
				for ($j = 0; $j < $cs; $j++) {
					$rs = array_slice($trs, $j*100, 100, true);
					if ($this->KdniaoSubs(array_keys($rs), $cc)) {
						foreach ($rs as $lt) {
							$lt->mdata['kdnts'] = time();
							$lt->update('meta');
							$c++;
						}
					}
				}
			}
			$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}
		if ($this->debug) {
			echo '=== Total '.$c." subscibed again. ===\n";
		}
	}

	/**
	* get fastway tracking events
	* @param string $tn
	* @return array
	*/
	public function fastway($tn='')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;

		$multi = false;
		if(is_array($tn) && !empty($tn[0]->connote)){
			$ts = [];
			foreach($tn as $t){
				$ts[] = $t->connote;
			}
			$tn = implode(',', $ts);
			$multi = true;
		}

		if ($this->debug) {
			echo 'Fastway Tracking - '.$tn."\n";
		}
		$data = [];

		$fastway = new FastwayAPI();
		if($multi){
			$scans = $fastway->massTracking($ts);
		}else{
			$scans = $fastway->getTracking($tn);
		}
		if (!empty($scans)) {
			foreach ($scans as $n=>$scan) {
				if($multi){
					$data[$n] = [];
					foreach($scan as $s){
						$data[$n][] = [
							$s['date'],
							$s['desc'],
							$s['where'],
							$s['type'],
							$s['desc1'],
						];
					}
				}else{
					$data[] = [
						$scan['date'],
						$scan['desc'],
						$scan['where'],
						$scan['type'],
						$scan['desc1'],
					];
				}
			}
		}
		return $data;
	}

	private function _prepFastwayEvents($data){
		foreach($data as $key =>&$d){
			// check to see if shipment was deliveried
			if ($d[3] === 'D' && !empty($this->cts) && !in_array(trim($d[4]), ['Unable to Deliver','Onboard','Parcel Connect Agent','Calling Card Left','Parcel Connect CCL','Undeliverable'])) {
				$this->cts->status = 99;
				$d['delivered'] = 1;
			}

			if ($d[3] === 'T' && !empty($this->cts) && !in_array(trim($d[4]), ['Consignment Information Submitted'])) {
				$d['isTransit'] = 1;
			}
		}
		return $data;
	}

	private function _prepAupostEvents($data){
		$amp = ['/by Australia Post/', '/Shipping information/', '/Australia Post facility/', '/With Australia Post/'];
		$amr = ['', 'Courier dispatch information', 'delivery depot', 'With driver'];
		foreach($data as $key=>&$d){
			$d[1] = preg_replace($amp, $amr, $d[1]);//|Customer enquiry lodged
			if (preg_match('/^(Delivered|Returned|Returning to sender)/i', $d[1]) && !empty($this->cts)) {
			 	$this->cts->status = 99;
			 	$d['delivered'] = 1;
			}
		}
		return $data;
	}

	public function auPost($tn='')//defunt!!!
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'AuPost Tracking - '.$tn."\n";
		}
		$data = [];

		$c = new curl('https://digitalapi.auspost.com.au/track/v3/search?q='.$tn);
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_REFERER, 'http://auspost.com.au/parcels-mail/track.html');
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:44.0) Gecko/20100101 Firefox/44.0', 'Cache-Control: no-cache', 'Origin: http://auspost.com.au', 'Authorization: Basic cHJvZF90cmFja2FwaTpXZWxjb21lQDEyMw==']);

		if (!$c->exec()) {
			return $data;
		}

		$jd = json_decode($c->result);
		if ($this->debug) {
			var_dump($jd);
		}
		if (empty($jd) || $jd->status != 'Success') {
			return $data;
		}

		$amp = ['/by Australia Post/', '/Shipping information/', '/Australia Post facility/'];
		$amr = ['', 'Courier dispatch information', 'delivery depot'];
		if (!empty($jd->QueryTrackEventsResponse->TrackingResults[0]->ReturnMessage->Description) && $jd->QueryTrackEventsResponse->TrackingResults[0]->ReturnMessage->Description=="Product Not Trackable") {
			$tracking=new Tracking();
			$data=$tracking->auPost($tn);
			if (!empty($data[1])&&in_array($data[1], ['Delivered', 'Returned']) &&!empty($this->cts)) {
				$this->cts->status = 99;
			}
			return $data[0];
		}
		if (empty($jd->QueryTrackEventsResponse->TrackingResults[0]->Consignment->Articles[0]->Events)) {
			return $data;
		}

		$evts = $jd->QueryTrackEventsResponse->TrackingResults[0]->Consignment->Articles[0]->Events;
		//var_dump($evts);
		if (empty($evts)) {
			return $data;
		}

		foreach (array_reverse($evts) as $e) {
			$dr = &$data[];
			$dr[0] = date('Y-m-d H:i:s', strtotime($e->EventDateTime));
			$t = preg_replace($amp, $amr, $e->EventDescription);
			$dr[1] = $t;
			$dr[2] = empty($e->Location)? '' : $e->Location;
			if (in_array($e->Status, ['Delivered', 'Returned']) && !empty($this->cts)) {
				$this->cts->status = 99;
			}
		}

		//if($this->debug && !empty($this->args[1])) print_r($data);
		return $data;
	}

	public function auPost2($tn='')//defunt!!!
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'AuPost Tracking - '.$tn."\n";
		}
		$data = [];

		$c = new curl('https://digitalapi.auspost.com.au/shipmentsgatewayapi/watchlist/shipments?trackingIds='.$tn);
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_REFERER, 'https://auspost.com.au/mypost/track/');
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:44.0) Gecko/20100101 Firefox/44.0', 'Cache-Control: no-cache', 'Origin: http://auspost.com.au', 'api-key: d11f9456-11c3-456d-9f6d-f7449cb9af8e']);
		if (!$c->exec()) {
			return $data;
		}

		$jd = json_decode($c->result);
		if ($this->debug) {
			var_dump($jd);
		}
		if (isset($jd[0])) {
			$jd = $jd[0];
		}

		if (empty($jd->shipment) || $jd->shipment->status != 'Success') {
			// var_dump($c->result);
			return $data;
		}

		$amp = ['/by Australia Post/', '/Shipping information/', '/Australia Post facility/', '/With Australia Post/'];
		$amr = ['', 'Courier dispatch information', 'delivery depot', 'With driver'];

		foreach ($jd->shipment->articles as $art) {
			if (empty($art->details[0]->events)) {
				continue;
			}
			foreach (array_reverse($art->details[0]->events) as $e) {
				$dr = &$data[];
				$dr[0] = date('Y-m-d H:i:s', strtotime($e->localeDateTime));
				$t = preg_replace($amp, $amr, $e->description);
				$dr[1] = $t;
				$dr[2] = empty($e->location)? '' : $e->location;
				if (!empty($t)) {
					if (stripos($t, 'Returning to sender') !== false) {
						$stopAddVirtualEvent = true;
					} elseif (stripos($t, 'Customer Enquiry lodged') !== false) {
						$stopAddVirtualEvent = true;
					}
				}
			}

			if (in_array($art->trackStatusOfArticle, ['Delivered', 'Returned']) && !empty($this->cts)) {
				$this->cts->status = 99;
			}
		}

		//if($this->debug && !empty($this->args[1])) print_r($data);
		return $data;
	}

	public function auPostApi($tn, $cc=0)
	{
		// if(!isset($this->apApi_lrt) || time() - $this->apApi_lrt > 60){
		// 	$this->apApi_lrt = time();
		// 	$this->apApi_rc = 0;
		// }

		// $this->apApi_rc++;

		// if($this->apApi_rc > 10){
		// 	return $this->puppet17Track($tn, $cc+1);
		// }
		
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		$multi = false;
		if(is_array($tn) && !empty($tn[0]->connote)){
			$ts = [];
			foreach($tn as $t){
				$ts[] = $t->imparcel->getParcelLongRef(Org::ORGID_COURIER_AUPOST)[0];
			}
			$otn = $tn;
			$tn = implode(',', $ts);
			$multi = true;
		}

		if($cc > 5){
			$this->log('Failed tracking: '.$tn.PHP_EOL);
			return [];
		}

		if ($this->debug) {
			echo 'AuPost Tracking - '.$tn."\n";
		}

		$aa = ['stone', 'syd', 'mel', 'bne'];
		shuffle($aa);
		$api = new AusPostAPI($aa[0], false);
		$res = $api->trackItems($tn);
		if ($this->debug) {
		print_r($res);
		}
		$data = [];
		$hasRef = [];

		if (empty($res->tracking_results)) {
			return [];
		}

		foreach ($res->tracking_results as $g) {
			if((empty($g->trackable_items) || empty($g->trackable_items[0]->items) || !is_array($g->trackable_items[0]->items[0]->events))&&empty($g->trackable_items[0]->events))  continue;
			if(!empty($g->trackable_items[0]->events))
			{
				$thisItem = $g->trackable_items[0];
			}else
			{
				$thisItem = $g->trackable_items[0]->items[0];
			}

			$hasRef[] = $g->tracking_id;

			foreach ($thisItem->events as $e) {
				if($multi){
					$dr = &$data[$g->tracking_id][];
				}else{
					$dr = &$data[];
				}
				$dr[0] = date('Y-m-d H:i:00', strtotime($e->date));
				$dr[1] = $e->description;
				$dr[2] = empty($e->location)? '' : $e->location;
			}

			// if (in_array($art->status, ['Delivered', 'Returned', 'Customer Enquiry lodged', 'Returning to sender']) && !empty($this->cts)) {
			// 	$this->cts->status = 99;
			// }
		}
		if(!empty($ts))
		{
			foreach ($ts as $key => $thisRef) {
				if(!in_array($thisRef, $hasRef))
				{
					$this->errorRefs[] = $thisRef;
				}
			}
		}

		// if($this->debug && !empty($this->args[1])) var_dump($data);
		if($this->debug){
			if($multi){
				$msg = 'Aupost API: '.count($data).'/'.count($ts);
				if($this->debug) echo $msg, PHP_EOL;
			}else{
				$msg = 'Aupost API: '.$tn;
			}
			$this->log($msg);
		}

		return $data;
	}

	public function gvAuPostApi($tn, $cc=0)
	{
		// if(!isset($this->apApi_lrt) || time() - $this->apApi_lrt > 60){
		// 	$this->apApi_lrt = time();
		// 	$this->apApi_rc = 0;
		// }

		// $this->apApi_rc++;

		// if($this->apApi_rc > 10){
		// 	return $this->puppet17Track($tn, $cc+1);
		// }

		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		$multi = false;
		if(is_array($tn) && !empty($tn[0]->connote)){
			$ts = [];
			foreach($tn as $t){
				$ts[] = $t->imparcel->getParcelLongRef(Org::ORGID_COURIER_AUPOST)[0];
			}
			$otn = $tn;
			$tn = implode(',', $ts);
			$multi = true;
		}

		if($cc > 5){
			$this->log('Failed tracking: '.$tn.PHP_EOL);
			return [];
		}

		if ($this->debug) {
			echo 'AuPost Tracking - '.$tn."\n";
		}

		$aa = ['GVSYD','GVPER','GVMEL','GVBNE'];
		shuffle($aa);
		$api = new GvAusPostAPI($aa[0], false);
		$res = $api->trackItems($tn);
		if ($this->debug) {
		print_r($res);
		}
		$data = [];
		$hasRef = [];

		if (empty($res->tracking_results)) {
			return [];
		}

		foreach ($res->tracking_results as $g) {
			if((empty($g->trackable_items) || empty($g->trackable_items[0]->items) || !is_array($g->trackable_items[0]->items[0]->events))&&empty($g->trackable_items[0]->events))  continue;
			if(!empty($g->trackable_items[0]->events))
			{
				$thisItem = $g->trackable_items[0];
			}else
			{
				$thisItem = $g->trackable_items[0]->items[0];
			}

			$hasRef[] = $g->tracking_id;

			foreach ($thisItem->events as $e) {
				if($multi){
					$dr = &$data[$g->tracking_id][];
				}else{
					$dr = &$data[];
				}
				$dr[0] = date('Y-m-d H:i:00', strtotime($e->date));
				$dr[1] = $e->description;
				$dr[2] = empty($e->location)? '' : $e->location;
			}

			// if (in_array($art->status, ['Delivered', 'Returned', 'Customer Enquiry lodged', 'Returning to sender']) && !empty($this->cts)) {
			// 	$this->cts->status = 99;
			// }
		}
		if(!empty($ts))
		{
			foreach ($ts as $key => $thisRef) {
				if(!in_array($thisRef, $hasRef))
				{
					$this->errorRefs[] = $thisRef;
				}
			}
		}

		// if($this->debug && !empty($this->args[1])) var_dump($data);
		if($this->debug){
			if($multi){
				$msg = 'GV Aupost API: '.count($data).'/'.count($ts);
				if($this->debug) echo $msg, PHP_EOL;
			}else{
				$msg = 'GV Aupost API: '.$tn;
			}
			$this->log($msg);
		}

		return $data;
	}


	public function puppetAupost($tn='', $cc=0)
	{	
		if($cc > 5){
			$this->log('Failed tracking: '.$tn.PHP_EOL);
			return [];
		}
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		$multi = false;
		if(is_array($tn) && !empty($tn[0]->connote)){
			$ts = [];
			foreach($tn as $t){
				$ts[] = $t->connote;
			}
			$tn = implode(',', $ts);
			$multi = true;
		}
		if ($this->debug) {
			echo 'AuPost Tracking - '.$tn."\n";
		}
		$data = [];

		$c = new curl('http://172.27.156.21:8081/aupost?tn='.$tn);
		$c->setopt(CURLOPT_TIMEOUT, 20);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);

		if (!$c->exec()) {
			return $data;
		}

		$rs = @json_decode($c->result);

		if (empty($rs)) return $data;

		foreach($rs as $r){
			if(empty($r->shipment->articles)) continue;
			if($multi){
				$data[$r->shipment->consignmentId] = [];
			}
			foreach($r->shipment->articles as $art){
				if (empty($art->details[0]->events)) continue;
				foreach (array_reverse($art->details[0]->events) as $e) {
					if($multi){
						$dr = &$data[$r->shipment->consignmentId][];
					}else{
						$dr = &$data[];
					}
					
					$dr[0] = date('Y-m-d H:i:s', strtotime($e->localeDateTime));
					$dr[1] = $e->description;
					$dr[2] = empty($e->location)? '' : $e->location;
				}
			}
		}
	
		if($this->debug){
			if($multi){
				$msg = 'AP Puppet: '.count($data).'/'.count($ts). PHP_EOL;
				if($this->debug) echo $msg;
			}else{
				$msg = 'AP Puppet: '.$tn. PHP_EOL;
			}
			$this->log($msg);
		}
		// if($this->debug && !empty($this->args[1])) print_r($data);
		return $data;
	}

	public function puppet17Track($tn='', $cc=0)
	{
		return [];
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		$multi = false;
		if(is_array($tn) && !empty($tn[0]->connote)){
			$ts = [];
			foreach($tn as $t){
				$ts[] = $t->connote;
			}
			$otn = $tn;
			$tn = implode(',', $ts);
			$multi = true;
		}

		if(!is_array($tn))
		{
			$otn = $tn;
		}

		if($cc > 5){
			$this->log('Failed tracking: '.$tn.PHP_EOL);
			return [];
		}
		if ($this->debug) {
			echo '17Tracking - '.$tn."\n";
		}
		$data = [];

		$c = new curl('http://172.27.156.21:8082/track?tn='.$tn);
		$c->setopt(CURLOPT_TIMEOUT, 20);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);

		if (!$c->exec()) {
			sleep(5);
			return $this->auPostApi($otn, $cc+1);
		}

		$rs = @json_decode($c->result, true);

		if (empty($rs)) {
			sleep(5);
			return $this->auPostApi($otn, $cc+1);
		}

		foreach($rs as $k => $r){
			if(empty($r) || !is_array($r)) continue;
			if($multi){
				$data[$k] = [];
			}
			foreach (array_reverse($r) as $e) {
				if($multi){
					$dr = &$data[$k][];
				}else{
					$dr = &$data[];
				}
				
				$dr[0] = date('Y-m-d H:i:s', strtotime($e['a']));
				$dr[1] = $e['z'];
				$dr[2] = empty($e['c'])? '' : $e['c'];
			}
		}

		if($this->debug){
			if($multi){
				$msg = '17T Puppet: '.count($data).'/'.count($ts);
				if($this->debug) echo $msg, PHP_EOL;
			}else{
				$msg = '17T Puppet: '.$tn;
			}

			$this->log($msg);
		}

		// if($this->debug && !empty($this->args[1])) print_r($data);
		return $data;
	}

	public function couriersPlease($tn='')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		$this->linkchain[] = $tn;
		$data = [];
		if ($this->debug) {
			echo 'CP Tracking - '.$tn."\n";
		}
		$c = new curl('https://www.couriersplease.com.au/DesktopModules/EzyTrack/EzyTrackHandler/CPPL_EzyTrackHandler.ashx?Type=trakingJsonServiceUser');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_SSL_VERIFYPEER, 0);
		$c->setopt(CURLOPT_REFERER, 'https://www.couriersplease.com.au/Tools/Track');
		$c->setopt(CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0', 'Cache-Control: no-cache', 'Content-Type: application/json; charset=UTF-8', 'X-Requested-With: XMLHttpRequest']);
		$c->setopt(CURLOPT_POST, true);
		$postr = json_encode([
			'strClientCode' => 'CPPLW',
			'strServiceCode' => '52',
			'strCode' => $tn,
		]);

		$c->setopt(CURLOPT_POSTFIELDS, $postr);
		if (!$c->exec()) {
			return $data;
		}

		$res = json_decode($c->result);
		if (!empty($res->Root)) {
			$pdt = '';
			foreach ($res->Root->TrakingInfo as $i=>$t) {
				if ($i == 0) {
					continue;
				}
				$dt = trim($t->Date.' '.$t->time);
				$dr = &$data[$i];

				if (strpos($t->Action, ' been delivered') > 0) {
					if (!empty($this->cts)) {
						$this->cts->status = 99;
					}
					if (empty($dt) && !empty($res->Root->TrakingInfo[$i+1])) {
						$nr = $res->Root->TrakingInfo[$i+1];
						$dt = trim($nr->Date.' '.$nr->time);
					}
				}

				$dr[0] = date('Y-m-d H:i:s', strtotime($dt));

				if ($dt == $pdt || empty($dt)) { //merge same time activity
					$data[$i-1][1] .= "\n".$t->Action;
					unset($data[$i]);
				} else {
					$dr[1] = $t->Action;
				}
				$pdt = $dt;

				/*if(preg_match('/^Linked to (.+)$/', $t->Action, $m)){	//linked tracking as the last
					if(in_array($m[1], $this->linkchain)) break;
					if($this->debug) echo "Linked to: ";
					$linked = $this->couriersPlease($m[1]);
					if(!empty($linked)){
						foreach ($linked as $k => $v) {
							$data[$r++] = $v;
						}
					}
				}*/
			}
		}
		//if(!empty($this->args[1])) print_r($data);
		return $data;
	}

	/**
	* get star track information
	* @param string $tn
	* @return array
	*/
	public function tollTrack($tn='')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'Toll Tracking - '.$tn."\n";
		}
		$data = [];

		$toll = new TollAPI();
		$scans = $toll->getTracking($tn);
		if (!empty($scans)) {
			foreach ($scans as $scan) {
				// data[0] -> scan date time
				// data[1] -> scan status description
				// data[2] -> scan shipment currently location
				$datestr = str_replace('/', '-', $scan['date']);
				$data[] = [
					date('Y-m-d H:i:s', strtotime($datestr)),
					$scan['desc'],
					$scan['where']
				];

				// check to see if shipment was deliveried
				if ($scan['type'] === 'D'&& !empty($this->cts)) {
					$this->cts->status = 99;
				}
			}
		}

		return $data;
	}

	public function tntTrack($tn='')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'TNT Tracking - '.$tn."\n";
		}
		$tnt= TntAPI::getTntInterface();
		$data=$tnt->trackingTnt(trim($tn));
		if (!empty($data)) {
			foreach ($data as &$d) {
				if (preg_match("/DLV|DLA|ATL|IMG|DOH/i", $d[3])) {
					$this->cts->status=99;
					$d['delivered'] = 1;
				}
			}
		}

		// var_dump($data);
		return $data;
	}


	public function starTrack($tn='')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'Startrack Tracking - '.$tn."\n";
		}
		$url = "https://msto.startrack.com.au/track-trace/?id=" . $tn;
		$c = new curl($url);
		$data = [];
		if (!$c->exec()) {
			return $data;
		}
		$b = $c->result;
		preg_match_all('/<fieldset class="generalDetails".*>(.*)<\/fieldset>/Ums', $b, $m);
		preg_match('/<table class="gvTable" id="lvFreightItemGrid".*>.*<\/table>/Umsi', $b, $m4);
		preg_match('/<table class="gvTable" id="lvTrackingGrid".*>.*<\/table>/Umsi', $b, $m2);
		//fetch the status at first
		if (isset($m[1])) {
			$m[1] = array_reverse($m[1]);
		}
		if (isset($m[1][0])) {
			$status='';
			$status_scan='';
			$status_dpot='';
			preg_match('/status.*<span id="__c1_lblStatus">(.*)<\/span>.*scan/Umsi', $m[1][0], $m1);
			preg_match('/status.*<span id="__c1_lblScanDateTime">(.*)<\/span>/Umsi', $m[1][0], $m1_scan);
			preg_match('/status.*<span id="__c1_lblScanDepot">(.*)<\/span>/Umsi', $m[1][0], $m1_dpot);
			if (isset($m1[1])) {
				$status = $m1[1];
			}
			if (isset($m1_scan[1])) {
				$status_scan=trim($m1_scan[1]);
			}
			if (isset($m1_dpot[1])) {
				$status_dpot=$m1_dpot[1];
			}
		}

		if (!isset($m2[0])) {
			if (!isset($m4[0])) {
				if (!empty($status)&&!empty($status_scan)) {
					$data[]=[date_format(date_create_from_format("d/m/Y H:i", trim($status_scan)), 'Y-m-d H:i:s'),
						$status,
						$status_dpot];
					return $data;
				}
			} else {
				preg_match('/<tr>(.*)<\/tr>/Umsi', $m4[0], $m41);
				if (!isset($m41[1])) {
					if (!empty($status)&&!empty($status_scan)) {
						$data[]=[date_format(date_create_from_format("d/m/Y H:i", trim($status_scan)), 'Y-m-d H:i:s'),
							$status,
							$status_dpot
						];
						return $data;
					}
				} else {
					if (preg_match_all('/<td>(.*)<\/td>/Umsi', $m41[1], $mt2)) {
						if (empty(trim($mt2[1][1]))||empty(trim($mt2[1][3]))) {
							if (!empty($status)&&!empty($status_scan)) {
								$data[]=[date_format(date_create_from_format("d/m/Y H:i", trim($status_scan)), 'Y-m-d H:i:s'),
									$status,
									$status_dpot
								];
								return $data;
							}
						} else {
							$data[] = [
								date_format(date_create_from_format("d/m/Y H:i", trim($mt2[1][1])), 'Y-m-d H:i:s'),
								trim($mt2[1][3]),
								trim($mt2[1][2]),
							];
						}
					}
				}
			}
		} else {
			preg_match_all('/<tr>(.*)<\/tr>/Umsi', $m2[0], $m3);
			if (!isset($m3[1])) {
				return $data;
			}
			foreach ($m3[1]as $i => $mt) {
				if (preg_match_all('/<td>(.*)<\/td>/Umsi', $mt, $mt1)) {
					$data[] = [
						date_format(date_create_from_format("d/m/Y H:i", trim($mt1[1][2])), 'Y-m-d H:i:s'),
						trim($mt1[1][4]),
						trim($mt1[1][3]),
					];
				}
			}
		}
		if (preg_match('/Successfull Delivery|Delivered in Full|^Delivered$|Returned$/i', trim($status))&& !empty($this->cts)) {
			$this->cts->status = 99;
			$data['delivered'] = 1;
		}
		$data= array_reverse($data);
		// if (!empty($data)) {
		// 	Log::log2file("No: ".$tn."=>".json_encode($data), "startrack_tracking", "tracking");
		// }
		return $data;
	}

	public function starTrackApi($tn='')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'StarTrack Tracking - '.$tn."\n";
		}
		$data = [];

		$c = new curl('https://api.startrack.com.au/shipping/v1/consignmentsummary?searchType=1&consignmentId='.$tn);
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 120);
		$c->setopt(CURLOPT_REFERER, 'https://startrack.com.au/');
		$c->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$c->setopt(CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:44.0) Gecko/20100101 Firefox/44.0', 'Cache-Control: no-cache', 'Origin: https://startrack.com.au', 'X-ApiKey:Fabric']);
		if (!$c->exec()) {
			return $data;
		}
		$jd = json_decode($c->result);
		if (empty($jd) || empty($jd->consignmentDetail)) {
			return $data;
		}
		foreach ($jd->consignmentDetail[0]->trackingEvents as $trackingDetail) {
			$data[]=[
				date_format(date_create_from_format("d/m/Y H:i", trim($trackingDetail->eventDateTime)), 'Y-m-d H:i:s'),
				$trackingDetail->scanDescription,
				$trackingDetail->scanDepot,
			];
		}

		if (preg_match('/Successfull Delivery|Delivered in Full|^Delivered$|Returned$/i', trim($jd->consignmentDetail[0]->consignmentStatus))&& !empty($this->cts)) {
			$this->cts->status = 99;
			$data['delivered'] = 1;
		}
		$data= array_reverse($data);
		// var_dump($jd->consignmentDetail[0]->consignmentStatus);
		// var_dump($jd->consignmentDetail[0]->lineItems[0]->freightItems[0]->trackingEvents);
		//        var_dump($data);
		// if (!empty($data)) {
		// 	Log::log2file("No: ".$tn."=>".json_encode($data), "startrack_tracking", "tracking");
		// }
		return $data;
	}

//	public function starTrack($tn=''){
//		$data = [];
//		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
//		if($this->debug) echo 'StarTrack Tracking - '.$tn."\n";
//		$URLBase = 'http://www.startrackexpress.com.au/scripts/webtracktrace.dll/conget';
//		$c = new curl($URLBase);
//		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
//		$c->setopt(CURLOPT_REFERER, $URLBase);
//
//		//ajax request
//		$c->setopt(CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0', 'Cache-Control: no-cache', 'Content-Type: application/x-www-form-urlencoded; charset=utf-8'));
//		$c->setopt(CURLOPT_POST, true);
//		$postr = $c->asPostString([
//			'StartLabel' =>	1,
//			'Connote' => $tn,
//			'SessionID' => '']);
//		$c->setopt(CURLOPT_POSTFIELDS, $postr);
//		if(!$c->exec()) return $data;
//
//		$doc = new DOMDocument();
//		libxml_use_internal_errors(true);
//		$doc->loadHTML($c->result, LIBXML_NOWARNING);
//		$gs = $doc->getElementsByTagName('table');
//		foreach($gs as $g){
//			if($g->getAttribute('cellspacing') == 1 && $g->getAttribute('cellpadding') == 3){
//				foreach($g->getElementsByTagName('tr') as $i => $tr){
//					if($i == 0) continue;
//					$dr = &$data[];
//					$dr[3] = 70;
//					foreach($tr->getElementsByTagName('td') as $j => $c){
//						$c = $c->nodeValue;
//						switch($j){
//							case 1:
//								$dr[0] = date('Y-m-d H:i:s', strtotime($c));
//							break;
//							case 2:
//								$dr[2] = $c;
//							break;
//							case 3:
//								$dr[1] = $c;
//								if(preg_match('/DELIVERED[\.\s]+/i', $c)){
//									if(!empty($this->cts)) $this->cts->status = 99;
//									$dr[3] = 90;
//								}
//							break;
//						}
//					}
//					ksort($dr);
//				}
//			}
//		}
//
//		//if(!empty($this->args[1])) print_r($data);
//		return $data;
//	}

	public function hunterTrack($tn = '')
	{
		$tn = empty($tn) && !empty($this->args[1]) ? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'Hunter Tracking - ' . $tn . "\n";
		}
		$data = [];

		$hunter = new HunterAPI(false);
		$result = $hunter->tracking($tn);
		if (!empty($result['events'])) {
			foreach ($result['events'] as $event) {
				$data[] = [
					date('Y-m-d H:i:s', strtotime($event['when'])),
					$event['what'],
					'',
				];
				if (preg_match("/Delivered/i", $event['what'])) {
					$this->cts->status=99;
				}
			}
		}
		// if ($this->debug) echo json_encode($data) . PHP_EOL;
		return $data;
	}

	public function exTracking($tn='', $poc='CNCAN')
	{
		$cc = $this->poc2cc($poc, $tn);
		$d = $cc[1];
		if (strtolower($this->args[0]) == 'extracking') {
			$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
			$d = !empty($this->args[2])? $this->args[2] : $d;
		}

		if ($d == 'sto' && preg_match('/^81\d+/', $tn)) {
			$d = 'yto';
		}

		if ($d == 'yto') {
			//return $this->kdniao($tn,'yto');
			return $this->kdpt($tn, 'yuantong');
		} elseif ($d == 'sto') {
			return $this->kdapi($tn, 'shentong');
		//return $this->kdpt($tn, 'shentong');
		} elseif ($d == 'zto') {
			return $this->apix($tn, 'zt');
		} elseif ($d == 'ems') {
			return $this->kdpt($tn, 'ems');
		} elseif ($d == 'yzpy') {
			//return $this->kdpt($tn, 'yzpy');
			return $this->Post163($tn);
		} elseif ($d == 'sf') {
			return $this->kdniao($tn, 'SF');
		} elseif ($d == 'yd') {
			return $this->gosYD($tn);
		}

		return [];
	}

	public function Kd100($tn='', $typ='ems')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'Kd100 '.$typ.' - '.$tn."\n";
		}
		$data = [];

		if (in_array($typ, ['ems', 'shunfeng', 'shentong'])) { //html api
			$c = new curl('http://www.kuaidi100.com/applyurl?key=487f23c3d11ccb27&com='.$typ.'&nu='.$tn.'&order=asc');
			$c->setopt(CURLOPT_FOLLOWLOCATION, true);
			$c->setopt(CURLOPT_TIMEOUT, 5);
			if (!$c->exec()) {
				return $data;
			}
			$referrer = $c->result;
			//$c = new curl('http://www.kuaidi100.com/query?id=1&type='.$typ.'&postid='.$tn.'&valicode=&temp='.time());
			$c = new curl('http://hk.pca168.com/kd100.php?d='.$typ.'&tn='.$tn);
			$c->setopt(CURLOPT_FOLLOWLOCATION, true);
			$c->setopt(CURLOPT_TIMEOUT, 5);
			$c->setopt(CURLOPT_REFERER, $referrer);
			//$c->setopt(CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0', 'Cache-Control: no-cache', 'Content-Type: application/x-www-form-urlencoded; charset=utf-8', 'X-Requested-With: XMLHttpRequest'));
			if (!$c->exec()) {
				return $data;
			}
			$jd = json_decode($c->result);
			//var_dump($jd);
			if (empty($jd) || $jd->status != 200) {
				if ($this->debug) {
					echo "Tracking pending.\n";
				}
				return $data;
			}
			$jd->data = array_reverse($jd->data);
		} else { //json api
			$c = new curl('http://api.kuaidi100.com/api?id=487f23c3d11ccb27&com='.$typ.'&nu='.$tn.'&order=asc');
			if (!$c->exec()) {
				return $data;
			}
			$jd = json_decode($c->result);
			if (empty($jd) || $jd->status != 1) {
				if ($this->debug) {
					echo "Tracking pending.\n";
				}
				return $data;
			}
		}

		foreach ($jd->data as $t) {
			$dr = &$data[];
			$dr[0] = $t->time;
			$dr[1] = $t->context;
			$dr[2] = '';
			$dr[3] = 70;
			if ($jd->state == 3 && preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点)/', $dr[1])) {
				if (!empty($this->cts)) {
					$this->cts->status = 99;
				}
				$dr[3] = 90;
			}
		}

		//if($this->debug) print_r($data);

		return $data;
	}

	public function Ickd($tn='', $type='ems')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'Ickd '.strtoupper($type).' - '.$tn."\n";
		}
		$data = [];
		sleep(1);
		$c = new curl('http://api.ickd.cn/?id=112070&secret=e36dd9261139bedb4e93097e9ea9cc15&com='.$type.'&nu='.$tn.'&ord=asc&type=json&encode=utf8&ver=2');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_REFERER, 'http://www.ickd.cn/');
		$c->setopt(CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0', 'Cache-Control: no-cache', 'Content-Type: application/x-www-form-urlencoded; charset=utf-8', 'X-MicrosoftAjax: Delta=true', 'X-Requested-With: XMLHttpRequest']);
		if (!$c->exec()) {
			return $data;
		}
		$jd = json_decode($c->result);
		//var_dump($c->result);

		if (empty($jd) || $jd->status == 0) {
			if ($this->debug) {
				echo "Tracking pending.\n";
			}
			return $data;
		}

		foreach ($jd->data as $i=>$g) {
			$dr = &$data[];
			$t = trim(str_replace(chr(0xC2).chr(0xA0), ' ', $g->context));
			$t = str_replace('：', ':', $t);
			$t = preg_replace('/\s+/', ' ', $t);
			//$ps = explode(':', $t);
			$dr[0] = $g->time;
			$dr[1] = $t;
			$dr[2] = '';
			$dr[3] = 70;
			if (preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点)/', $dr[1])) {
				if (!empty($this->cts)) {
					$this->cts->status = 99;
				}
				$dr[3] = 90;
			}
		}

		//if($this->debug) print_r($data);

		return $data;
	}

	public function kdpt($tn='', $type='ems', $retry=0)
	{
		sleep(1);
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		$type = strtolower($type);
		if ($type == 'shentong' && preg_match('/^81\d+/', $tn)) {
			$type = 'yuantong';
		}
		$tmap = ['yto' => 'yuantong', 'yzpy' => 'chinapost', 'sto' => 'shentong'];
		if (isset($tmap[$type])) {
			$type = $tmap[$type];
		}
		if ($this->debug) {
			echo 'kdpt '.$type.' - '.$tn."\n";
		}
		$data = [];
		$apis = ['XDB2gzsjbsssdqtwapANo0I_3162692881', 'XDB2gzsjbsssdqtwapAN01I_3162692881', 'XDB29zsjbs6sdqtwapAN01I_3162692881'];

		$c = new curl('http://q.kdpt.net/api?id='.array_rand($apis).'&com='.$type.'&nu='.$tn.'&show=json&order=asc');//&format=kuaidi100
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		if (!$c->exec()) {
			return $data;
		}
		$jd = json_decode($c->result);
		//var_dump($jd);

		if (empty($jd) || $jd->status != 1) {
			if ($this->debug) {
				echo "Tracking pending.\n";
			}
			if ($retry < 1) {
				return $this->kdpt($tn, $type, $retry+1);
			}
			return $data;
		}

		foreach ($jd->data as $t) {
			$dr = &$data[];
			$dr[0] = $t->time;
			$dr[1] = $t->context;
			$dr[2] = '';
			$dr[3] = 70;
			if ($jd->state == 3 && preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点|delivered)/i', $dr[1])) {
				if (!empty($this->cts)) {
					$this->cts->status = 99;
				}
				$dr[3] = 90;
			}
		}

		//if($this->debug) print_r($data);

		return $data;
	}

	public function akd($tn='', $type='ems')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'AKD '.strtoupper($type).' - '.$tn."\n";
		}
		$data = [];
		$c = new curl('http://www.aikuaidi.cn/rest/?key=2b5826d1fc034b449c45988fd67bfcdb&order='.$tn.'&id='.$type.'&ord=asc&show=json');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_HTTPHEADER, ['Cache-Control: no-cache', 'Content-Type: application/json; charset=utf-8', 'Accept: application/json']);
		if (!$c->exec()) {
			return $data;
		}
		$jd = json_decode($c->result);
		//var_dump($jd);

		if (empty($jd) || !empty($jd->errCode)) {
			if ($this->debug) {
				echo "Tracking pending.\n";
			}
			return $data;
		}

		foreach ($jd->data as $t) {
			$dr = &$data[];
			$dr[0] = $t->time;
			$dr[1] = $t->content;
			$dr[2] = '';
			$dr[3] = 70;
			if ($jd->status == 4 && preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点)/', $dr[1])) {
				if (!empty($this->cts)) {
					$this->cts->status = 99;
				}
				$dr[3] = 90;
			}
		}
		//if($this->debug) print_r($data);

		return $data;
	}

	public function kdapi($tn='', $type='ems')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'AKD '.strtoupper($type).' - '.$tn."\n";
		}
		$data = [];
		$c = new curl('http://www.kuaidiapi.cn/rest/?uid=53270&key=f628a27741354d4bbacef4345ef6371b&order='.$tn.'&id='.$type.'&ord=asc&show=json');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_HTTPHEADER, ['Cache-Control: no-cache', 'Content-Type: application/json; charset=utf-8', 'Accept: application/json']);
		if (!$c->exec()) {
			return $data;
		}
		$jd = json_decode($c->result);
		//var_dump($jd);

		if (empty($jd) || $jd->status < 2) {
			if ($this->debug) {
				echo "Tracking pending.\n";
			}
			return $data;
		}

		foreach ($jd->data as $t) {
			$dr = &$data[];
			$dr[0] = $t->time;
			$dr[1] = $t->content;
			$dr[2] = '';
			$dr[3] = 70;
			if ($jd->status == 4 && preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点)/', $dr[1])) {
				if (!empty($this->cts)) {
					$this->cts->status = 99;
				}
				$dr[3] = 90;
			}
		}
		// if($this->debug) print_r($data);

		return $data;
	}

	public function Kdniao($tn='', $type='EMS')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		$type = strtoupper($type);
		if ($this->debug) {
			echo 'KDN '.$type.' - '.$tn."\n";
		}
		$data = [];
		$c = new curl('http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		//$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_HTTPHEADER, ['Cache-Control: no-cache', 'Content-Type: application/x-www-form-urlencoded;charset=utf-8', 'Accept: application/json']);
		$c->setopt(CURLOPT_POST, true);

		$d = json_encode(["OrderCode" => '', "ShipperCode" => $type, "LogisticCode" => $tn]);
		$pd = [
			'RequestData' => urlencode($d),
			'EBusinessID' => '1256523',
			'RequestType' => '1002',
			'DataSign' => '',
			'DataType' => 2,
		];

		$key = 'b51188ce-2ae4-438a-afe3-7ba7a1dc0dd7';

		$pd['DataSign'] = urlencode(base64_encode(MD5($d.$key)));
		$c->setopt(CURLOPT_POSTFIELDS, $c->asPostString($pd));
		if (!$c->exec()) {
			return $data;
		}
		$jd = json_decode($c->result);

		if (empty($jd) || empty($jd->State) || empty($jd->Traces)) {
			if ($this->debug) {
				echo "Tracking pending.\n";
			}
			return $data;
		}

		foreach ($jd->Traces as $t) {
			$dr = &$data[];
			$dr[0] = $t->AcceptTime;
			$dr[1] = $t->AcceptStation;
			$dr[2] = '';
			$dr[3] = 70;
			if ($jd->State == 3 && preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点)/', $dr[1])) {
				if (!empty($this->cts)) {
					$this->cts->status = 99;
				}
				$dr[3] = 90;
			}
		}
		//if($this->debug) print_r($data);

		return $data;
	}

	public function KdniaoSubs($ns='', $type='EMS')
	{
		$ns = (empty($ns) && !empty($this->args[1]))? [$this->args[1]] : $ns;
		if (empty($ns)) {
			return false;
		}
		$type = strtoupper($type);
		$c = new curl('http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		//$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_HTTPHEADER, ['Cache-Control: no-cache', 'Content-Type: application/x-www-form-urlencoded;charset=utf-8', 'Accept: application/json']);
		$c->setopt(CURLOPT_POST, true);

		$rs = ["Code" => $type, "Item" => []];
		foreach ($ns as $n) {
			$n = trim($n);
			if (empty($n) || strlen($n) < 6 || strlen($n) > 50) {
				continue;
			}
			$rs['Item'][] = ['No' => $n, 'Bk' => hash('crc32b', $n.'**KdN2PCA**')];
		}
		$d = json_encode([$rs]);

		$pd = [
			'RequestData' => urlencode($d),
			'EBusinessID' => '1256523',
			'RequestType' => '1005',
			'DataSign' => '',
			'DataType' => 2,
		];

		$key = 'b51188ce-2ae4-438a-afe3-7ba7a1dc0dd7';

		$pd['DataSign'] = urlencode(base64_encode(MD5($d.$key)));
		$c->setopt(CURLOPT_POSTFIELDS, $c->asPostString($pd));
		if (!$c->exec()) {
			return false;
		}
		$jd = json_decode($c->result);
		if ($this->debug && (empty($jd) || !$jd->Success)) {
			file_put_contents(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'kdn_api.log', date('Y-m-d H:i:s').' '.print_r($ns, true)."\n".print_r($pd, true)."\n", FILE_APPEND);
		}
		if ($this->debug) {
			var_dump($c->result);
		}
		if (empty($jd)) {
			return false;
		}
		return $jd->Success;
	}

	public function kdcom($tn='', $type='ems')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'KDCOM '.strtoupper($type).' - '.$tn."\n";
		}
		$data = [];
		$c = new curl('http://www.kuaidi.com/index-ajaxselectcourierinfo-'.$tn.'-'.$type.'.html');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_REFERER, 'http://www.kuaidi.com/all/ems.html');
		$c->setopt(CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0', 'Cache-Control: no-cache', 'X-Requested-With: XMLHttpRequest']);
		if (!$c->exec()) {
			return $data;
		}
		$jd = json_decode($c->result);
		if (empty($jd) || empty($jd->status) || empty($jd->data)) {
			if ($this->debug) {
				echo "Tracking pending.\n";
			}
			return $data;
		}

		foreach (array_reverse($jd->data) as $t) {
			$dr = &$data[];
			$dr[0] = $t->time;
			$dr[1] = $t->context;
			$dr[2] = '';
			$dr[3] = 70;
			if ($jd->status == 6 && preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点)/', $dr[1])) {
				if (!empty($this->cts)) {
					$this->cts->status = 99;
				}
				$dr[3] = 90;
			}
		}
		//if($this->debug) print_r($data);

		return $data;
	}

	public function cnPost($tn='')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'CnPost - '.$tn."\n";
		}
		$data = [];
		$c = new curl('http://intmail.11185.cn/zdxt/gjyjqcgzcx/gjyjqcgzcx_NewgjyjqcgzcxLzxxQueryPage.action');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_REFERER, 'http://cx.11185.cn/trackmail.a');
		$c->setopt(CURLOPT_HTTPHEADER, ['Accept: application/json', 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0', 'Cache-Control: no-cache', 'Origin: http://intmail.11185.cn', 'Referer: http://intmail.11185.cn/zdxt/jsp/zhdd/gjyjgzcx/gjyjqcgzcx/gjyjqcgzcx_new.jsp', 'X-Requested-With: XMLHttpRequest']);
		$c->setopt(CURLOPT_POST, true);
		$pd = [
			'vYjhm' => $tn,
			'vTdbz' => '1',
			'vGjbz' => 'GN',
			'cgsyj' => 'null',
		];
		$c->setopt(CURLOPT_POSTFIELDS, $c->asPostString($pd));
		if (!$c->exec()) {
			return $data;
		}
		$jd = json_decode($c->result);
		if (empty($jd) || empty($jd->rdata)) {
			if ($this->debug) {
				echo "Tracking pending.\n";
			}
			return $data;
		}

		foreach ($jd->rdata as $t) {
			$dr = &$data[];
			$dr[0] = $t->D_SJSJ;
			$dr[1] = preg_replace('/<a>|<\/a>/', '', $t->V_ZT);
			$dr[3] = 70;
			if ($t->V_HJDM == 'TT' || preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点)/', $dr[1])) {
				if (!empty($this->cts)) {
					$this->cts->status = 99;
				}
				$dr[3] = 90;
			}
		}

		//if($this->debug) print_r($data);

		return $data;
	}

	public function Post163($tn='', $type='ems')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		if ($this->debug) {
			echo 'P163 '.strtoupper($type).' - '.$tn."\n";
		}
		$data = [];
		sleep(1);
		if (preg_match('/^BE4/', $tn)) {
			$type = 'gdems';
		}
		//$c = new curl('http://hk.pca168.com/post163.php?d='.$type.'&tn='.$tn);
		//$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		//$c->setopt(CURLOPT_TIMEOUT, 5);
		
		$c = new curl('http://www.post163.com/query/');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_REFERER, 'http://www.post163.com/ems/');
		$c->setopt(CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0', 'Cache-Control: no-cache', 'Content-Type: application/x-www-form-urlencoded; charset=utf-8', 'X-MicrosoftAjax: Delta=true', 'X-Requested-With: XMLHttpRequest']);
		$c->setopt(CURLOPT_POST, true);
		$postr = $c->asPostString([
			'id' => $type,
			'order' => $tn,
		]);
		$c->setopt(CURLOPT_POSTFIELDS, $postr);
		
		if (!$c->exec()) {
			return $data;
		}
		$jd = json_decode(iconv('gb2312', 'utf-8', $c->result));
		//echo $c->result;

		if (empty($jd) || $jd->status == 1) {
			if ($this->debug) {
				echo "Tracking pending.\n";
			}
			return $data;
		}

		foreach ($jd->data as $i=>$g) {
			$dr = &$data[];
			$t = trim(str_replace(chr(0xC2).chr(0xA0), ' ', $g->content));
			$t = str_replace('：', ':', $t);
			$t = preg_replace('/\s+/', ' ', $t);
			//$ps = explode(':', $t);
			$dr[0] = $g->time;
			$dr[1] = $t;
			$dr[2] = '';
			$dr[3] = 70;
			if (preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点)/', $dr[1])) {
				if (!empty($this->cts)) {
					$this->cts->status = 99;
				}
				$dr[3] = 90;
			}
		}

		//if($dr[3] == 70 && strtotime($dr[0]) < time() - 604800) return $this->kdcom($tn);

		//if($this->debug) print_r($data);

		return $data;
	}

	public function gosYD($tn='')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		$tns = explode(",", $tn);
		$api_account = '8104007';
		$api_key = 'D41D8CD98F00B204';

		$xml = '<scan_infos>';
		foreach ($tns as $n) {
			$xml .= '<scan_info><mailno>'.$n.'</mailno><mailno_type>2</mailno_type></scan_info>';
		}
		$xml .= '</scan_infos>';
		$xml = base64_encode($xml);

		$c = new curl('http://gos.yundasys.com:45109/ydgos/other/showlist.jspx');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_TIMEOUT, 5);
		$c->setopt(CURLOPT_POST, true);
		$pd = [
			'account' => $api_account,
			'version' => '1.0',
			'data' => $xml,
			'validation' => md5($xml.$api_key),
		];

		$c->setopt(CURLOPT_POSTFIELDS, $c->asPostString($pd));

		$data = [];
		if (!$c->exec()) {
			return $data;
		}
		//echo $c->result;
		$xml = simplexml_load_string($c->result);
		foreach ($xml->response as $r) {
			if ($this->debug) {
				echo "YD: ",$r->mail_no,"\n";
			}
			if (empty($r->scan_infos->scan_info)) {
				continue;
			}
			foreach ($r->scan_infos->scan_info as $si) {
				$dr = &$data[];
				$dr[0] = (string) $si->time;
				$dr[1] = (string) $si->remark;
				$dr[2] = '';
				$dr[3] = 70;
				if (preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点)/', $dr[1])) {
					if (!empty($this->cts)) {
						$this->cts->status = 99;
					}
					$dr[3] = 90;
				}
			}
		}
		//var_dump($data);
		return $data;
	}

	public function cngg($tn='', $type='YTO')
	{
		$tn = empty($tn) && !empty($this->args[1])? $this->args[1] : $tn;
		$c = new curl('http://api.wap.guoguo-app.com/h5/mtop.cnwireless.cncainiaoappservice.getlogisticscompanylist/1.0/?v=1.0&api=mtop.cnwireless.CNCainiaoAppService.getLogisticsCompanyList&appKey=12574478&t=1496741493353&callback=mtopjsonp1&type=jsonp&sign=b0f7376271effd90e311f998ad3a3efb&data=%7B%22version%22%3A0%2C%22cptype%22%3A%22all%22%7D');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_REFERER, 'http://www.guoguo-app.com');
		$c->setopt(CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0', 'Cache-Control: no-cache', 'X-Requested-With: XMLHttpRequest']);
		$data = [];
		if (!$c->exec()) {
			return $data;
		}
		$cookie = empty($c->header['Set-Cookie'])? '' : $c->header['Set-Cookie'];
		$key = preg_match('/(?:^|;\s*)_m_h5_tk\=([^_;]+)/', $cookie, $m)? $m[1] : '';
		$tick = time();
		$sign = strtolower(md5($key.'&'.$tick.'&12574478&{"mailNo":"'.$tn.'","cpcode":"'.$type.'"}'));

		$c->setopt(CURLOPT_URL, 'http://api.wap.guoguo-app.com/h5/mtop.cnwireless.cnlogisticdetailservice.wapquerylogisticpackagebymailno/1.0/?v=1.0&api=mtop.cnwireless.CNLogisticDetailService.wapqueryLogisticPackageByMailNo&appKey=12574478&t='.$tick.'&callback=mtopjsonp&type=jsonp&sign='.$sign.'&data={"mailNo":"'.$tn.'","cpcode":"'.$type.'"}');
		$c->setopt(CURLOPT_COOKIE, $cookie);
		if (!$c->exec()) {
			return $data;
		}
		$r = json_decode(substr($c->result, 10, -1));
		if (empty($r->data) || empty($r->data->transitList)) {
			return $data;
		}
		//echo $r->data->partnerCode;
		foreach ($r->data->transitList as $si) {
			$dr = &$data[];
			$dr[0] = (string) $si->time;
			$dr[1] = (string) $si->message;
			$dr[2] = '';
			$dr[3] = 70;
			if (preg_match('/(?<!未)(妥投|签收|已投到|已领取|自提点)/', $dr[1])) {
				if (!empty($this->cts)) {
					$this->cts->status = 99;
				}
				$dr[3] = 90;
			}
		}
		//var_dump($data);
		return $data;
	}
	

	protected function httpGet($url)
	{
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$httpResponse = curl_exec($ch);
		curl_close($ch);

		if (!$httpResponse) {
			return ['done' => false];
		}

		return $httpResponse;
	}


	/**
	* @param string $fn
	* @param string $fd
	* @param bool $uld
	* @return array|bool
	*/
	public function flight($fn='', $fd='', $uld = false, $byEta = false)
	{
		$fn = empty($fn) && !empty($this->args[1])? $this->args[1] : $fn;
		$fn = trim($fn);
		$fd = empty($fd) && !empty($this->args[2])? $this->args[2] : $fd;
		$fd = empty($fd)? date('Y-m-d') : date('Y-m-d', strtotime($fd));

		$alcm = include(Yii::app()->basePath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'airline_codes.php');

		$ac = strtoupper(substr($fn, 0, 2));
		if (!isset($alcm[$ac])) {
			echo 'Airline code '.$ac." unknown.\n";
			return false;
		}
		$ep = $this->httpGet('https://uk.flightaware.com/live/flight/'.$alcm[$ac].ltrim(substr($fn, 2), 0));
		if(is_array($ep))
		{
			return false;
		}

		preg_match('/<script>var trackpollBootstrap = (.+);<\/script>/Ums', $ep, $m);
		$ed = empty($m[1])? false : json_decode($m[1]);

		if (empty($ed)) {
			if ($this->debug) {
				echo " not found\n";
			}
			return $this->flightStat($fn, $fd);
		}
		$getData = function ($f) use ($fd) {
			$tft = $f->takeoffTimes->scheduled;
			$ldt = $f->landingTimes->scheduled;
			$status = 0; // on schedule
			if (!empty($f->takeoffTimes->actual)) {
				$tft = $f->takeoffTimes->actual;
				$status = 1; // on flying
			}
			if (!empty($f->landingTimes->actual)) {
				$ldt = $f->landingTimes->actual;
				$status = 2; // arrived
			}
			$tft = new DateTime('@'.$tft);
			$tft->setTimeZone(new DateTimeZone(substr($f->origin->TZ, 1)));

			$ldt = new DateTime('@'.$ldt);
			$ldt->setTimeZone(new DateTimeZone(substr($f->destination->TZ, 1)));
			return [$fd, $f->aircraftType, $f->origin->friendlyLocation, $f->destination->friendlyLocation, $tft->format('Y-m-d H:i:s'), $ldt->format('Y-m-d H:i:s'), null, $status];
		};
		$data = [];
		if ($this->debug) {
			echo 'Looking for flight '.$fn.' on '.$fd;
		}
		foreach ($ed->flights as $v => $k) {
			if (isset($k->activityLog)) {
				foreach ($k->activityLog->flights as $f) {
					// in case some flight will show one day in advance
					// so we need to avoid the day in advance
					// for example: http://uk.flightaware.com/live/flight/SIA221
					if ($byEta) { // check flight by ETA time
						if(empty($f->landingTimes->scheduled)) continue;
						$etaGuess = new DateTime('@'.$f->landingTimes->scheduled);
						$etaGuess->setTimeZone(new DateTimeZone(substr($f->destination->TZ, 1)));
						$schedule = $etaGuess->format('Y-m-d');
					} else {
						$schedule = gmdate('Y-m-d', $f->takeoffTimes->scheduled);
					}

					$today = date('Y-m-d');
					if ($schedule <= $today &&  $schedule == $fd) {
						$data = $getData($f);
						if (strpos($data[2], 'Australia') > 0) {
							break;
						}
					}
				}
				break;
			}
		}

		if (empty($data)) {
			if ($uld && strtotime($fd) < time() - 172800) {
				$data = $getData($f);
				$dd = strtotime($fd) - strtotime(substr($data[4], 0, 10));
				if ($dd > 0) {
					$data[4] = date('Y-m-d H:i:s', strtotime($data[4].' +'.$dd.' seconds'));
					$data[5] = date('Y-m-d H:i:s', strtotime($data[5].' +'.$dd.' seconds'));
				}
			} else {
				if ($this->debug) {
					echo " not found\n";
				}
				return $this->flightStat($fn, $fd);
			}
		}

		if ($this->debug) {
			echo " found\n";
		}

		//if($this->debug) print_r($data);

		return $data;
	}

	public function flightStat($fn='', $fd='')
	{
		$fn = empty($fn) && !empty($this->args[1])? $this->args[1] : $fn;
		$fn = trim($fn);
		$fd = empty($fd) && !empty($this->args[2])? $this->args[2] : $fd;
		$fd = empty($fd)? date('Y-m-d') : date('Y-m-d', strtotime($fd));
		$data = [];
		$ac = strtoupper(substr($fn, 0, 2));

		$c = new curl('http://www.flightstats.com/go/FlightTracker/flightTracker.do');
		$c->setopt(CURLOPT_FOLLOWLOCATION, true);
		$c->setopt(CURLOPT_REFERER, 'http://www.flightstats.com');
		$c->setopt(CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0', 'Cache-Control: no-cache', 'Content-Type: application/x-www-form-urlencoded; charset=utf-8', 'X-MicrosoftAjax: Delta=true', 'X-Requested-With: XMLHttpRequest']);
		$c->setopt(CURLOPT_POST, true);
		$postr = $c->asPostString([
			'airline' => $ac,
			'departureDate' => $fd,
			'flightNumber' => ltrim(substr($fn, 2), 0),
		]);
		$c->setopt(CURLOPT_POSTFIELDS, $postr);
		if (!$c->exec()) {
			return $data;
		}
		$data[0] = $fd;

		$doc = new DOMDocument();
		libxml_use_internal_errors(true);

		$doc->loadHTML($c->result, LIBXML_NOWARNING);
		libxml_clear_errors();
		$c = $doc->getElementById('flightTable');

		if ($this->debug) {
			echo 'Looking for flight '.$fn.' on '.$fd;
		}
		if (empty($c)) {
			if ($this->debug) {
				echo " not found\n";
			}
			return false;
		}
		if ($this->debug) {
			echo " found\n";
		}

		$cs = $c->getElementsByTagName('table');

		foreach ($cs as $c) {
			$tds = $c->getElementsByTagName('td');
			$cc = 0;
			foreach ($tds as $i=>$td) {
				$d = trim($td->nodeValue);
				if ($d == 'Status:') {
					if (strpos(trim($tds->item($i+1)->nodeValue), 'Scheduled') === 0) {
						return [];
					}
				} elseif ($d == 'Departure:') {
					$cc = 1;
					$data[2] = trim($tds->item($i+1)->nodeValue);
				} elseif ($d == 'Arrival:') {
					$cc = 2;
					$data[3] = trim($tds->item($i+1)->nodeValue);
				} elseif ($d == 'Actual:') {
					$di = $cc == 1? 4 : 5;
					$data[$di] = date('Y-m-d H:i:s', strtotime($fd.' '.trim(str_replace('(Estimated)', '', $tds->item($i+1)->nodeValue))));
				}
			}
		}

		if (empty($data[4])) {
			$data[7] = 0;
		} elseif (empty($data[5])) {
			$data[7] = 1;
		} else {
			if (strtotime($data[5]) < strtotime($data[4])) {// +1 day
				$data[5] = date('Y-m-d H:i:s', strtotime($data[5].' +1 day'));
			}
			$data[7] = 2;
		}

		ksort($data);

		//if($this->debug) print_r($data);

		return $data;
	}

	public function checkingFastway()
	{
		$consol_no = $this->prompt('Consol No: ');
		$consol = Consol::model()->find("no = :no",[":no"=>$consol_no]);
		foreach ($consol->shipments as $key => $value) {
					$this->update($value->hbn);
		}
	}

	public function checkingFastwayTranship()
	{
		$ref = $this->prompt('Shipment Ref: ');
		print_r($this->fastway($ref));
	}

	private function _prepUbiEvents($data){
		$amp = ['/BY AUSTRALIA POST/', '/SHIPPING INFORMATION/', '/AUSTRALIA POST FACILITY/', '/WITH AUSTRALIA POST/','/AT AUSTRALIA POST/'];
		$amr = ['', 'COURIER DISPATCH INFORMATION', 'DELIVERY DEPOT', 'WITH DRIVER','AT COURIER'];
		$finalData = [];
		foreach($data as &$d){
			$thisData = [];
			$d->activity = preg_replace($amp, $amr, $d->activity);//'Customer Enquiry lodged'_
			if (in_array($d->activity, ['Delivered', 'Returned', 'Returning to sender']) && !empty($this->cts)){
			 	$this->cts->status = 99;
			}else if(in_array($d->eventCode, ['DLD']))
			{
				$this->cts->status = 99;
			}
			$thisData[0] = $d->eventTime;
			$thisData[1] = $d->activity;
			$thisData[2] = $d->location;
			$thisData[3] = $d->eventCode;
			$finalData[] = $thisData;
		}
		return $finalData;
	}

	private function _prepDfeEvents($data){
		$finalData = [];
		if(!empty($data['status']))
		{
			if($data['status']=='410')
			{
				$this->cts->mdata['expired'] = 1;
			}elseif($data['status']=='408'||$data['status']=='406')
			{
				$this->cts->mdata['request_number_today'] = 5;
				$this->cts->mdata['request_date'] = date('Y-m-d');
			}
			$this->cts->save();
			return false;
		}

		foreach($data as &$d){
			$thisData = [];
			if (preg_match('/Delivered|RTS|Redirect/i',$d->Status)&&!preg_match('/Not Delivered/i', $d->Status) && !empty($this->cts)){
			 	$this->cts->status = 99;
			 	$thisData['delivered'] = 1;
			}
			$thisData[0] = $d->Date;
			$thisData[1] = $d->Status;
			$thisData[2] = $d->Location;
			$finalData[] = $thisData;
		}
		return $finalData;
	}

	private function  _prepChukou1Events($data){
		if ($this->debug) {
					echo "prepData--------------------";
					print_r($data);
		}

		$finalData = [];
		foreach($data as $key => &$d){
			if ($this->debug) {
				echo $key;
				print_r($d);
			}

			if($key==='status') continue;
			$thisData = [];
			$thisData[0] = $d->DateTime;
			$thisData[1] = $d->Message;
			$thisData[2] = $d->Location;
			if(!empty($d->TrackingStatus)&&$d->TrackingStatus=='Delivered')
			{
				$this->cts->status = 99;
				$thisData['delivered'] = 1;
			}
			if ($this->debug) {
				echo "thisData----------------";
				print_r($thisData);
			}
			$finalData[] = $thisData;
		}
		if ($this->debug) {
			echo "final-----------";
			print_r($finalData);
		}
		return $finalData;
	}

	private function  _prepSFEvents($data){
		if ($this->debug) {
					echo "prepData--------------------";
					print_r($data);
		}

		$finalData = [];
		foreach($data as $key => &$d){
			if ($this->debug) {
				echo $key;
				print_r($d);
			}
			$thisData = [];
			$thisData[0] = $d[0];
			$thisData[1] = $d[2];
			$thisData[2] = $d[1];
			if(in_array($d[3],[80,8000]))
			{
				$this->cts->status = 99;
				$thisData['delivered'] = 1;
			}
			if ($this->debug) {
				echo "thisData----------------";
				print_r($thisData);
			}
			$finalData[] = $thisData;
		}
		if ($this->debug) {
			echo "final-----------";
			print_r($finalData);
		}
		return $finalData;
	}

	public function ubiApi($tn='')
	{
		$sn = [];
		foreach ($tn as $t) {
			if(!empty($t->mdata['barcodeLabelNumber'])||!empty($t->mdata['article_id'])||preg_match('/SIZE/i', $t->connote))
			{
				$sn[]=$t->shipment;
			}
		}

		if ($this->debug) {
			echo 'UbiAPI Tracking - '.$tn."\n";
		}

		$api = new UbiAPI();
		$res = $api->getTrackingEvents($sn);
		$data = [];

		if(in_array($res->status,["Success","Partial Success"]))
		{

			foreach ($res->data as $reobj) {
				if (empty($reobj->events))  continue;

				foreach ($reobj->events as $e) {
					if(empty($data[$e->trackingNo]))
					{
						$data[$e->trackingNo] = [$e];
					}else
					{
						$data[$e->trackingNo][] = $e;
					}
				}
			}
		}
		return $data;
	}

	public function dfeApi($tn='',$type = 'syd')
	{
		return [];
		$sn = [];
		foreach ($tn as $t) {
			if(!empty($t->mdata['oid']))
			{
				$sn[]=$t->shipment;
			}
		}

		if ($this->debug) {
			echo 'DfeAPI Tracking - '.$t->connote."\n";
		}

		$api = new DFEAPI($type);
		$res = $api->getTrackingEvents($sn);
		$data = [];
		if(in_array($res->ResponseCode,["300"]))
		{
			foreach ($res->TrackingResults as $reobj) {
				if($reobj->ResponseCode!="300")
				{
					$data[$reobj->Connote]['status'] = $reobj->ResponseCode;
					continue;
				}

				if (!empty($reobj->ConsignmentTrackingDetails)) {
				foreach ($reobj->ConsignmentTrackingDetails as $e) {
					if(empty($data[$reobj->Connote]))
					{
						$data[$reobj->Connote] = [$e];
					}else
					{
						$data[$reobj->Connote][] = $e;
					}
				}
				}
			}
		}
		return $data;
	}

	public function ck1Api($tn='',$type = 'syd')
	{
		$sn = $tn->connote;

		if ($this->debug) {
			echo 'ck1API Tracking - '.$tn->connote."\n";
		}

		$api = new CK1API();
		$res = $api->getTrackingEvents($sn);
		$data = [];
		if(in_array($res['rescode'],["200","400"]))
		{
			$res = $res['msg'];
			if ($this->debug) {
			print_r($res);
			}
			if(isset($res->Checkpoints))
			{
				foreach ($res->Checkpoints as $reobj) {
					if(empty(($data[$sn])))
					{
						$data[$sn] = [$reobj];
					}else
					{
						$data[$sn][] = $reobj;
					}
				}
				$data[$sn]['status'] = $res->TrackingStatus;
			}else
			{
				$data[$sn] = [];
			}
		}
		return $data;
	}

	public function sfApi($tn='',$type = 'syd')
	{
		$sn = $tn->connote;

		if ($this->debug) {
			echo 'skAPI Tracking - '.$tn->connote."\n";
		}

		$api = new SFAPI(false);
		$res = $api->getTrackingEvents([$sn]);
		$data = [];
		if(!empty($res))
		{
			if ($this->debug) {
				print_r($res);
			}
			foreach ($res as $reobj) {
				if(empty($data[$sn]))
				{
					$data[$sn] = [$reobj];
				}else
				{
					$data[$sn][] = $reobj;
				}
			}
		}
		return $data;
	}

	public function syncSFTrackingData()
	{
		$ps = ImParcel::model()->with(['tracks','trans'])->findAll('agent_id = :agentId and json_value(tracks.meta,"$.sync_sf") is null and json_value(trans.meta,"$.oid") is not null and (cbwf &524288)>0  and tracks.dt !="0000-00-00 00:00:00" and DATE_SUB(CURDATE(), INTERVAL 150 DAY) <= date(tracks.dt) ',[":agentId"=>Org::ORGID_CLIENT_SF]);
		foreach ($ps as $key => $p) 
		{
			$sfAPI = new SFAPI(false,true,$p->ddpt_id);
			$sfAPI->pushTrackingDataGTS($p,$p->tracks);
		}
	}

	public function syncSFCustomsData()
	{
		$ps = ImParcel::model()->with(['tracks','trans'])->findAll('agent_id = :agentId and (json_value(tracks.meta,"$.sync_sf") is null or json_value(tracks.meta,"$.sync_sf") = 0) and trans.id is null and (cbwf &524288)=0 and t.status>=55 AND tracks.type in (61,55,60) and tracks.dt !="0000-00-00 00:00:00" and DATE_SUB(CURDATE(), INTERVAL 150 DAY) <= date(tracks.dt) ',[":agentId"=>Org::ORGID_CLIENT_SF]);
		foreach ($ps as $key => $p) 
		{
			$sfAPI = new SFAPI(false,true,$p->consol->dpt_id);
			$sfAPI->pushCustomsDataGTS($p,$p->tracks);
		}
	}

	public function syncUBICustomsData()
	{
		$this->syncFuncUBICustomsData();
	}

	public function syncFuncUBICustomsData()
	{
		$trackings = TrackingProcess::model()->findAllBySql("SELECT t.* from `tracking_process` `t` where agent_id in (3025) and status = 1 limit 1000");
		if(empty($trackings)) return;
		$ps = ImParcel::model()->findAllBySql("SELECT t.* from `shipment` `t` where id in (SELECT pid from tracking where id in (".join(',',array_column($trackings,"tracking_id")).") and status in (30,31,35,38,39,40,42,50,53,55,57,58,59,60,62,64,66) )  and status !=100 limit 1000");
		if(empty($ps))
		{
			$trans = Yii::app()->db->beginTransaction();
			try {
				foreach ($trackings as $key => $t) {
					$t->status = 2;
					$t->update(['status']);
				}
				$trans->commit();
			} catch (Exception $ex) {
				$trans->rollback();
				throw $ex;
			}
		}
		//$ps = ImParcel::model()->findAllBySql("SELECT t.* FROM `shipment` `t`  LEFT OUTER JOIN `consol` `consol` ON (`t`.`consol_id`=`consol`.`id`) LEFT OUTER JOIN `tracking` `tracks` ON (`tracks`.`pid`=`t`.`id`) LEFT OUTER JOIN `tranship` `trans` ON (`trans`.`pid`=`t`.`id`) WHERE ((`t`.`type`=10) AND (agent_id = 3025 and tracks.id is not null and trans.id is null and t.status in (30,31,35,38,39,40,42,50,53,55,57,58,59,60,62,64,66) and tracks.dt !='0000-00-00 00:00:00' )) and t.created>='{$fd}' and t.created<='{$td}' and json_value(tracks.meta,'$.sync_ubi') is null ORDER BY tracks.dt ASC limit 1000");

		if(!empty($ps))
		{
			$ubiAPI = new UbiAPI();
			$ubiAPI->ftpTracking($ps);
		}
	}

	public function syncFuncDaipostCustomsData()
	{
		$trackings = TrackingProcess::model()->findAllBySql("SELECT t.* from `tracking_process` `t` where agent_id in (4669) and status = 1 limit 1000");
		if(empty($trackings)) return;
		$ps = ImParcel::model()->findAllBySql("SELECT t.* from `shipment` `t` where id in (SELECT pid from tracking where id in (".join(',',array_column($trackings,"tracking_id")).")  and type in (55,68,60))  and status !=100 limit 1000");
		if(empty($ps))
		{
			$trans = Yii::app()->db->beginTransaction();
			try {
				foreach ($trackings as $key => $t) {
					$t->status = 2;
					$t->update(['status']);
				}
				$trans->commit();
			} catch (Exception $ex) {
				$trans->rollback();
				throw $ex;
			}
		}
		// $h = intval(date("H"));
		// $i = intval(date("i"));
		// if(($h>=6 && $h<=15)||($h>=17 && $h<=20)||($h>=16 && $h<=17 && $i!=0) )
		// {
		// 	$fromDay = date('Y-m-d', strtotime('-15 day'));
		// 	$ps = ImParcel::model()->findAllBySql("SELECT t.* FROM `shipment` `t`  LEFT OUTER JOIN `consol` `consol` ON (`t`.`consol_id`=`consol`.`id`) LEFT OUTER JOIN `tracking` `tracks` ON (`tracks`.`pid`=`t`.`id`) LEFT OUTER JOIN `tranship` `trans` ON (`trans`.`pid`=`t`.`id`) WHERE `t`.`created`>={$fromDay} AND ((`t`.`type`=10) AND (agent_id = 4669 and tracks.type in (55,68,60) and tracks.id is not null and trans.id is null and t.status>=30 and tracks.dt !='0000-00-00 00:00:00' ))  and json_value(tracks.meta,'$.sync_daipost') is null group by `t`.`id` ORDER BY tracks.dt ASC limit 1000");
		// }else
		// {
		// 	$ps = ImParcel::model()->findAllBySql("SELECT t.* FROM `shipment` `t`  LEFT OUTER JOIN `consol` `consol` ON (`t`.`consol_id`=`consol`.`id`) LEFT OUTER JOIN `tracking` `tracks` ON (`tracks`.`pid`=`t`.`id`) LEFT OUTER JOIN `tranship` `trans` ON (`trans`.`pid`=`t`.`id`) WHERE ((`t`.`type`=10) AND (agent_id = 4669 and tracks.type in (55,68,60) and tracks.id is not null and trans.id is null and t.status>=30 and tracks.dt !='0000-00-00 00:00:00' ))  and json_value(tracks.meta,'$.sync_daipost') is null group by `t`.`id` ORDER BY tracks.dt ASC limit 1000");
		// }
		

		if(!empty($ps))
		{
			$ubiAPI = new DaipostAPI();
			$ubiAPI->ftpTracking($ps);
		}
	}

	public function syncFuncEweCustomsData()
	{
		// $h = intval(date("H"));
		// $i = intval(date("i"));
		// if(($h>=6 && $h<=15)||($h>=17 && $h<=20)||($h>=16 && $h<=17 && $i!=0) )
		// {
		// 	$fromDay = date('Y-m-d', strtotime('-15 day'));
		// 	$ps = ImParcel::model()->findAllBySql("SELECT t.* FROM `shipment` `t`  LEFT OUTER JOIN `consol` `consol` ON (`t`.`consol_id`=`consol`.`id`) LEFT OUTER JOIN `tracking` `tracks` ON (`tracks`.`pid`=`t`.`id`) LEFT OUTER JOIN `tranship` `trans` ON (`trans`.`pid`=`t`.`id`) WHERE `t`.`created`>={$fromDay} AND ((`t`.`type`=10) AND (agent_id in (4618,4660,4657) and tracks.type in (55,68,60) and tracks.id is not null and trans.id is null and t.status>=30 and tracks.dt !='0000-00-00 00:00:00' ))  and json_value(tracks.meta,'$.sync_ewe') is null group by `t`.`id` ORDER BY tracks.dt ASC limit 1000");
		// }else
		// {
		// 	$ps = ImParcel::model()->findAllBySql("SELECT t.* FROM `shipment` `t`  LEFT OUTER JOIN `consol` `consol` ON (`t`.`consol_id`=`consol`.`id`) LEFT OUTER JOIN `tracking` `tracks` ON (`tracks`.`pid`=`t`.`id`) LEFT OUTER JOIN `tranship` `trans` ON (`trans`.`pid`=`t`.`id`) WHERE  ((`t`.`type`=10) AND (agent_id in (4618,4660,4657) and tracks.type in (55,68,60) and tracks.id is not null and trans.id is null and t.status>=30 and tracks.dt !='0000-00-00 00:00:00' ))  and json_value(tracks.meta,'$.sync_ewe') is null group by `t`.`id` ORDER BY tracks.dt ASC limit 1000");
		// }
		$trackings = TrackingProcess::model()->findAllBySql("SELECT t.* from `tracking_process` `t` where agent_id in (4618,4660,4657) and status = 1 limit 1000");
		if(empty($trackings)) return;
		$ps = ImParcel::model()->findAllBySql("SELECT t.* from `shipment` `t` where id in (SELECT pid from tracking where id in (".join(',',array_column($trackings,"tracking_id")).") and type in (55,68,60)) and status !=100 limit 2000");
		if(empty($ps))
		{
			$trans = Yii::app()->db->beginTransaction();
			try {
				foreach ($trackings as $key => $t) {
					$t->status = 2;
					$t->update(['status']);
				}
				$trans->commit();
			} catch (Exception $ex) {
				$trans->rollback();
				throw $ex;
			}
		}

		if(!empty($ps))
		{
			$newEWE = new EweAPI("EWE","SYD");
			$newEWE->ftpTracking($ps);
		}
	}

	protected function log($m){
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'track_cmd.log';
		return file_put_contents($lf, date('Y-m-d H:i:s')."\t".$m."\n", FILE_APPEND);
	}
}

