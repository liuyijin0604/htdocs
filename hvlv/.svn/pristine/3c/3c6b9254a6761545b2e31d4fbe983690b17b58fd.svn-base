<?php 
class LabelService extends Service
{
	public function changeCourierLabel($refs,$orgRates,$onlyCheckCost = false)
	{
		if(empty($refs)||empty($orgRates))
		{
			return $this->getFailResult('need hbns/refs and couriers');
		}

		$orgRateService = new OrgRateService();
		foreach ($refs as $key => $ref) {
			$shipment = ImParcel::model()->find(' (ref = :ref or hbn = :ref) and status <100',[':ref'=>$ref]);
			if(empty($shipment))
			{
				$this->warns[]= ["Line ".$key.":".$ref." failure,not found"];
				continue;
			}
			$oToll = $orgRateService->calculateCheaperCourierCost($shipment,$orgRates);
			//$oCP = $orgRateService->calculateCheaperCourierCost($shipment,[70]);

			$cost1 = empty($oToll->status)?0:$oToll->minCost;
			$TOLLCourier = empty($oToll->status)?"":$oToll->courier->code;
			//$cost2 = empty($oCP->status)?0:$oCP->minCost;
			$pref = "";
			$pcan = "";
			$pnote = "";
			$pmdata = [];
			if(!empty($cost1))
			{	
				if($onlyCheckCost)
				{
					$this->warns[]= ["Line ".$key.":".$ref." Cost ".$cost1."|Courier:".$TOLLCourier];
					continue;
				}

				$pref = $shipment->ref;
				$pcan = $shipment->can;
				$pnote = $shipment->note;
				$pmdata = $shipment->mdata;
				$shipment->can = $shipment->ref;
				$shipment->note = $shipment->note."</br>".$shipment->ref;
				$shipment->ref = "";
				if(!empty($shipment->mdata['cargo_delivery']))
				{
					unset($shipment->mdata['cargo_delivery']);
				}
				if(!empty($shipment->mdata['aupost_exp']))
				{
					unset($shipment->mdata['aupost_exp']);
				}
				$shipment->save();
				$status = $shipment->createCourierLabel($oToll->courier);
				if($status)
				{
					$changeShipmentLabel = new ChangeShipmentLabel();
					$changeShipmentLabel->newref = $shipment->ref;
					$changeShipmentLabel->pid = $shipment->id;
					$changeShipmentLabel->pref = $shipment->can;
					$changeShipmentLabel->phbn = $shipment->hbn;
					$changeShipmentLabel->save();

					if(!empty($shipment->trans))
					{
						foreach ($shipment->trans as $key => $tran)
						{
							if($tran->connote!=$shipment->ref&&$tran->type==80)
							{
								$tran->delete();
							}
						}
					}

					$gatepassShipments = GatepassShipment::model()->findAll('fid = :fid',[':fid'=>$shipment->id]);
					foreach ($gatepassShipments as $key => $gatepassShipment) {
						$gatepassShipment->connote_no = $shipment->ref;
						$gatepassShipment->courier_id = $oToll->courier->org_id;
						$gatepassShipment->status = 0;
						$gatepassShipment->parent_id = 0;
						$gatepassShipment->gate_pass_time = '0000-00-00 00:00:00';
						$gatepassShipment->save();
					}

					if(!empty($shipment->cargo_process))
					{
						$shipment->cargo_process->status = CargoProcess::DELETED;
						$shipment->cargo_process->save();
					}
					$shipment->save();
					$this->warns[]= ["Line ".$key.":".$ref." success,new ref:".$shipment->ref];
				}else
				{
					$shipment->ref = $pref;
					$shipment->can = $pcan;
					$shipment->note = $pnote;
					$shipment->mdata = $pmdata;
					$shipment->save();
					if(!empty($shipment->tempErrors))
					{
						$this->warns[]= ["Line ".$key.":".$ref." failure ".$shipment->tempErrors];
					}
				}
			}else
			{
				$this->warns[]= ["Line ".$key.":".$ref." failure undeliverable".join(',',$oToll->error)];
			}
		}

		return $this->getSuccessResultWithWarns();
	}

	public function importParcelThirdPartyLabelInfo($file,$orgRateId)
	{
		if(empty($file)||empty($orgRateId))
		{
			return $this->getFailResult('need file and couriers');
		}
		$data = $this->getFileData($file);
		$data = $data[0];
		unset($data[1]);

		foreach ($data as $key => $d) {
			$checkShipment = ImParcel::model()->find(' (ref = "'.$d[2].'" or hbn = "'.$d[2].'") and status <100',[':ref'=>$d[2]]);
			if(!empty($checkShipment))
			{
				$this->warns[]= ["Line ".$key.":".$d[2]." failure, ref existed"];
				continue;
			}
			$shipment = ImParcel::model()->find(' (ref = "'.$d[1].'" or hbn = "'.$d[1].'") and status <100');
			if(empty($shipment))
			{
				$this->warns[]= ["Line ".$key.":".$d[1]." failure,not found"];
				continue;
			}
			$orgRate = OrgRate::model()->findByPk($orgRateId);
			if($shipment->ref!=$d[2])
			{
				$shipment->can = $shipment->ref;
				$shipment->ref = $d[2];
			}
			$shipment->mdata['org_id'] = $orgRate->org_id;
			$shipment->mdata['org_rate_id'] = $orgRate->id;
			$barcodes = explode(';', $d[3]);
			$saveBarcodes = [];
			$transaction=Yii::app()->db->beginTransaction();
			try {

				foreach ($barcodes as $key2 => $barcode) {
					if(empty($barcode)) continue;
					$pb = new ParcelBarcode();
					$pb->main_ref = $shipment->ref;
					$pb->fid = $shipment->id;
					$pb->sub_ref = $barcode;
					$pb->sn = $key+1;
					$pb->save();
					$saveBarcodes[] = $barcode;
				}
				$shipment->mdata['barcode'] = join(',',$saveBarcodes);
				$shipment->save();
				foreach ($shipment->trans as $key2 => $tran) {
					if($tran->type == Tranship::THIRD_PARTY_TYPE)
					{
						$tran->delete();
					}else
					{
						$tran->mdata['non_manifest'] = 1;
						$tran->save();
					}
				}
				$ts = new Tranship;
				$ts->pid = $shipment->id;
				$ts->org_id = $orgRate->org_id;
				$ts->type = Tranship::THIRD_PARTY_TYPE;
				$ts->status = 10;
				$ts->connote = $shipment->ref;
				$ts->time = date('Y-m-d H:i:s');
				$ts->cost = 0;
				$ts->mdata['oid'] = $orgRate->org_id;
				$ts->mdata['non_manifest'] = 1;
				$ts->save();

				$changeShipmentLabel = new ChangeShipmentLabel();
				$changeShipmentLabel->newref = $shipment->ref;
				$changeShipmentLabel->pid = $shipment->id;
				$changeShipmentLabel->pref = $shipment->can;
				$changeShipmentLabel->phbn = $shipment->hbn;
				$changeShipmentLabel->save();
				$transaction->commit();
				$this->warns[]= ["Line ".$key.":".$d[1]." Success"];
			} catch (Exception $ex) {
				$transaction->rollback();
				throw $ex;
			}
		}

		return $this->getSuccessResultWithWarns();
	}

	public function printLabel($id,$helper=false,$sn = 0)
	{

		$model = Shipment::model()->findByPk($id);
		if (empty($model)) {
			throw new CHttpException(404, 'Page not found!');
		}
		if(!empty($helper)){
			$type = 'pdf';
			if(!empty($model->trans)&&$model->trans[0]->org_id == 115){
				$file_data = $this->renderPartial('//_pdf/_label_fastway_zpl2020', ['label' => $model], true);
				$type = 'zpl';
			}else{
				if(ImParcelService::isOtherLabel($model))
	            {
					$imparcelService = new ImParcelService();
					$pdf = $imparcelService->getImparcelLabel([$model],$sn,3);
					$file_data = base64_encode($pdf);
				}else
				{
					$pdf = oPDF::renderPDF('label_A6', ['tpl' => '_label_1', 'empty' => false, 'rs' => [$model], 'sn' => empty($sn)? 0 : $sn], 0);
					$file_data = base64_encode($pdf);
				}
			}

			$files = [['printer' => 'label', 'type' => $type, 'file' => $file_data]];
			echo json_encode(array("result" => true, "file" => json_encode($files)));
			Yii::app()->end();

		}

		if(ImParcelService::isOtherLabel($model))
		{
			$imparcelService = new ImParcelService();
			$imparcelService->getImparcelLabel([$model],$sn);
			return;
		}

		oPDF::renderPDF('label_A6', ['tpl' => '_label_1', 'empty' => false, 'rs' => [$model], 'sn' => empty($sn)? 0 : $sn]);
	}

	public static function createUbiBorderLabel(&$imParcel,$orgRate, $facility = false)
	{
		if ($imParcel->id <= 0) {
			return false;
		}
		$ubi=new EtowerAPI(false,false,$orgRate->id);
		$result=$ubi->createBorderShipment([$imParcel]);
		if(is_object($result)){
			if($result->status=="Failure")
			{
				$imParcel->tempErrors = @json_encode($result->errors);
				return false;
			}else
			{
				$result=$result->data;
				if($result[0]->status=="Success"){
					$orderIds = [];
					$barcodes = [];
					$ref = "";
					foreach ($result as $key => $rdata) {
						if($rdata->status=="Success"){
							$refNo = $rdata->referenceNo;
							$refNoArr = explode('-', $refNo);
							$orderIds[intval($refNoArr[1])] = $rdata->orderId;
							$ref =  $rdata->trackingNo;
						}
					}
					ksort($orderIds);
					$imParcel->ref = $ref;
					$imParcel->nolog = true;
					$imParcel->mdata['facility'] = $facility;
					$imParcel->mdata['org_rate_id'] = $orgRate->id;
					$imParcel->mdata['etower_shipment_orderid'] = join(',',$orderIds);
					$imParcel->update('ref', 'note', 'meta');
					$labelResult = $ubi->getLabels([$imParcel]);
					$barcodes = $labelResult[3];
					$imParcel->mdata['borderLabelTags'] = [$labelResult[0],$labelResult[1],$labelResult[2]];
					$imParcel->mdata['barcode'] = join(',',$barcodes);
					$imParcel->update('meta');
					$imParcel->nolog = false;
					foreach ($barcodes as $key => $barcode) {
						$pb = new ParcelBarcode();
						$pb->main_ref = $imParcel->ref;
						$pb->fid = $imParcel->id;
						$pb->sub_ref = $barcode;
						$pb->sn = $key+1;
						$pb->save();
					}

					$ts = new Tranship;
					$ts->pid = $imParcel->id;
					$ts->org_id = Org::ORGID_COURIER_UBI_TOLL;
					$ts->type = 80;
					$ts->status = 19;
					$ts->connote = $imParcel->ref;
					$ts->time = date('Y-m-d H:i:s');
					$ts->cost = 0;
					if ($ts->save()) {
						return true;
					}
					return true;
				}
			}
		}else {
			$imParcel->tempErrors = @json_encode($result['errors']);
			return false;
		}
		return false;
	}


	public static function createUbiTollLabel(&$imParcel,$orgRate, $facility = false)
	{
		if ($imParcel->id <= 0) {
			return false;
		}
		$ubi=new EtowerAPI(false,false,$orgRate->id);
		$result=$ubi->createShipment([$imParcel]);
		if(is_object($result)){
			if($result->status=="Failure")
			{
				$imParcel->tempErrors = @json_encode($result->errors);
				return false;
			}else
			{
				$result=$result->data;
				if($result[0]->status=="Success"){
					$orderIds = [];
					$barcodes = [];
					$ref = "";
					foreach ($result as $key => $rdata) {
						if($rdata->status=="Success"){
							$refNo = $rdata->referenceNo;
							$refNoArr = explode('-', $refNo);
							$orderIds[intval($refNoArr[1])] = $rdata->orderId;
							$barcodes[intval($refNoArr[1])] = $rdata->trackingNo;
							$ref =  $rdata->connoteId;
						}

						if(!empty($rdata->piecesResult))
						{
							foreach ($rdata->piecesResult as $key => $rPieceData) {
								if($rPieceData->status=="Success"){
									$refNo = $rPieceData->referenceNo;
									$refNoArr = explode('-', $refNo);
									$orderIds[intval($refNoArr[1])] = $rPieceData->orderId;
									$barcodes[intval($refNoArr[1])] = $rPieceData->trackingNo;
									$ref =  $rPieceData->connoteId;
								}
							}
						}
					}

					ksort($orderIds);
					ksort($barcodes);
					$imParcel->ref = $ref;
					$imParcel->nolog = true;
					$imParcel->mdata['facility'] = $facility;
					$imParcel->mdata['org_rate_id'] = $orgRate->id;
					$imParcel->mdata['etower_shipment_orderid'] = join(',',$orderIds);
					$imParcel->mdata['barcode'] = join(',',$barcodes);
					$imParcel->update('ref', 'note', 'meta');
					$imParcel->nolog = false;
					foreach ($barcodes as $key => $barcode) {
						$pb = new ParcelBarcode();
						$pb->main_ref = $imParcel->ref;
						$pb->fid = $imParcel->id;
						$pb->sub_ref = $barcode;
						$pb->sn = $key;
						$pb->save();
					}

					$ts = new Tranship;
					$ts->pid = $imParcel->id;
					$ts->org_id = Org::ORGID_COURIER_UBI_TOLL;
					$ts->type = 80;
					$ts->status = 19;
					$ts->connote = $imParcel->ref;
					$ts->time = date('Y-m-d H:i:s');
					$ts->cost = 0;
					if ($ts->save()) {
						return true;
					}
					return true;
				}
			}
		}else {
			$imParcel->tempErrors = @json_encode($result['errors']);
			return false;
		}
		return false;
	}

	public static function createUbiAlliedLabel(&$imParcel,$orgRate, $facility = false)
	{
		if ($imParcel->id <= 0) {
			return false;
		}
		$ubi=new EtowerAPI(false,false,$orgRate->id);
		$result=$ubi->createShipment([$imParcel]);
		if(is_object($result)){
			if($result->status=="Failure")
			{
				$imParcel->tempErrors = @json_encode($result->errors);
				return false;
			}else
			{
				$result=$result->data;
				if($result[0]->status=="Success"){
					$orderIds = [];
					$barcodes = [];
					$ref = "";
					foreach ($result as $key => $rdata) {
						if($rdata->status=="Success"){
							$refNo = $rdata->referenceNo;
							$refNoArr = explode('-', $refNo);
							$orderIds[intval($refNoArr[1])] = $rdata->orderId;
							$barcodes[intval($refNoArr[1])] = $rdata->trackingNo;
							$ref =  $rdata->connoteId;
						}

						if(!empty($rdata->piecesResult))
						{
							foreach ($rdata->piecesResult as $key => $rPieceData) {
								if($rPieceData->status=="Success"){
									$refNo = $rPieceData->referenceNo;
									$refNoArr = explode('-', $refNo);
									$orderIds[intval($refNoArr[1])] = $rPieceData->orderId;
									$barcodes[intval($refNoArr[1])] = $rPieceData->trackingNo;
									$ref =  $rPieceData->connoteId;
								}
							}
						}
					}
					ksort($orderIds);
					ksort($barcodes);
					$imParcel->ref = $ref;
					$imParcel->nolog = true;
					$imParcel->mdata['facility'] = $facility;
					$imParcel->mdata['org_rate_id'] = $orgRate->id;
					$imParcel->mdata['etower_shipment_orderid'] = join(',',$orderIds);
					$imParcel->mdata['barcode'] = join(',',$barcodes);
					$imParcel->update('ref', 'note', 'meta');
					$imParcel->nolog = false;
					foreach ($barcodes as $key => $barcode) {
						$pb = new ParcelBarcode();
						$pb->main_ref = $imParcel->ref;
						$pb->fid = $imParcel->id;
						$pb->sub_ref = $barcode;
						$pb->sn = $key;
						$pb->save();
					}

					$ts = new Tranship;
					$ts->pid = $imParcel->id;
					$ts->org_id = Org::ORGID_COURIER_UBI;
					$ts->type = 80;
					$ts->status = 19;
					$ts->connote = $imParcel->ref;
					$ts->time = date('Y-m-d H:i:s');
					$ts->cost = 0;
					if ($ts->save()) {
						return true;
					}
					return true;
				}
			}
		}else {
			$imParcel->tempErrors = @json_encode($result['errors']);
			return false;
		}
		return false;
	}

	public static function createUbiAuspostLabel(&$imParcel,$orgRate, $facility = false)
	{
		if ($imParcel->id <= 0) {
			return false;
		}
		if ($imParcel->pkg>1) {
			return false;
		}
		if (empty($facility)) {
			$facility = $orgRate->mdata['facility'];
		} else {
			$facility = strtoupper($facility);
			$orgRate = OrgRate::model()->find("org_id = :org_id AND json_value(meta,'$.facility') = :facility  and json_value(meta,'$.service_code')='UBI.AU2AU.AUPOST' ", [":org_id"=>Org::ORGID_COURIER_UBI,":facility"=>$facility]);
		}

		// if (empty($this->ref)) {
		// 	switch ($facility) {
		// 		case 'SYDWW':
		// 			$this->ref = self::genEparcelNo('3079', '33G7K');
		// 		break;
		// 		case 'MELWW':
		// 			$this->ref = self::genEparcelNo('3079', '33G7L');
		// 		break;
		// 		case 'BNEWW':
		// 			$this->ref = self::genEparcelNo('3079', '33G7M');
		// 		break;
		// 		case 'ADLWW':
		// 			$this->ref = self::genEparcelNo('3079', '33G7N');
		// 		break;
		// 		case 'PERWW':
		// 			$this->ref = self::genEparcelNo('3079', '33G7P');
		// 		break;
		// 	}
		// }

		// $this->mdata['facility'] = $facility;
		// $this->mdata['org_rate_id'] = $orgRate->id;

		// if (!empty($orgRate->mdata['sort_code'])) {
		// 	$this->mdata['sort_code'] = $orgRate->mdata['sort_code'];
		// 	$this->mdata['isUbiCN2AU'] = 1;
		// }

		// $aid = $this->ref.sprintf('%02s', 1).'00093'.'51'.'0';
		// $aid .= AusPostAPI::aidChkDgt($aid);


		// if (empty($this->mdata['article_id'])) {
		// 	$this->note = $aid;
		// 	$this->mdata['article_id'] = $aid;
		// }

		// $this->nolog = true;
		// $this->update('ref', 'note', 'meta');
		// $this->nolog = false;

		// $ts = new Tranship;
		// $ts->pid = $this->id;
		// $ts->org_id = Org::ORGID_COURIER_UBI;
		// $ts->type = 80;
		// $ts->status = 10;
		// $ts->connote = $this->ref;
		// $ts->time = date('Y-m-d H:i:s');
		// $ts->mdata['barcodeLabelNumber'] = $aid;
		// $ts->cost = 0;
		// return $ts->save();

		$ubi=new UbiAPI(false,false,$orgRate->id);
		$result=$ubi->createShipment([$imParcel]);
		if(is_object($result)){
			if($result->status=="Failure"){
				$imParcel->tempErrors = @json_encode($result->errors);
				return false;
			}else{
				$result=$result->data[0];
				if($result->status=="Success"){
					$orderId = $result->orderId;
					$aid = $result->trackingNo;
					$ref = substr($aid,0,-11);
					$imParcel->ref = $ref;
					if(!empty($orgRate->mdata['sort_code']))
					{
						$imParcel->mdata['sort_code'] = $orgRate->mdata['sort_code'];
						$imParcel->mdata['isUbiCN2AU'] = 1;
					}

					// $aid = $this->ref.sprintf('%02s', 1).'00093'.'51'.'0';
					// $aid .= AusPostAPI::aidChkDgt($aid);


					$imParcel->note = $aid;
					$imParcel->mdata['article_id'] = $aid;
					
					$imParcel->nolog = true;
					$imParcel->mdata['facility'] = $facility;
					$imParcel->mdata['org_rate_id'] = $orgRate->id;
					$imParcel->mdata['etower_shipment_orderid'] = $orderId;
					$imParcel->update('ref', 'note', 'meta');
					$imParcel->nolog = false;

					$ts = new Tranship;
					$ts->pid = $imParcel->id;
					$ts->org_id = Org::ORGID_COURIER_UBI;
					$ts->type = 80;
					$ts->status = 19;
					$ts->connote = $imParcel->ref;
					$ts->time = date('Y-m-d H:i:s');
					$ts->mdata['barcodeLabelNumber'] = $aid;
					$ts->cost = 0;
					if ($ts->save()) {
						return true;
					}
				}
			}
		}else {
			$imParcel->tempErrors = @json_encode($result['errors']);
			return false;
		}
	}

	public static function getAlliedDepotCode($shipment)
	{
		$postcode = $shipment->cnee->postcode;
		$suburb = $shipment->cnee->suburb;
		$state = $shipment->cnee->state;
		$zoneMap = AlliedDepot::model()->find('postcode = :postcode and state =:state and suburb=:suburb', [':postcode' =>$postcode ,':state' => $state,':suburb' => trim(strtoupper($suburb))]);
		return empty($zoneMap)?"":$zoneMap->depot;
	}

	public function changeCourierLabelPallet($refs,$pallets,$orgRates,$reTransNum,$onlyCheckCost = false)
	{
		if(empty($refs)||empty($orgRates))
		{
			return $this->getFailResult('need hbns/refs and couriers');
		}

		$orgRateService = new OrgRateService();
		foreach ($refs as $key => $ref)
		{
			if(empty($pallets[$key]))
			{
				$this->warns[]= ["Line ".$key.": ".$ref." failure,new pallets"];
				continue;
			}

			$reTransNum = $pallets[$key];
			$oldShipment = ImParcel::model()->find(' (ref = :ref or hbn = :ref) and status <100',[':ref'=>$ref]);
			if(empty($oldShipment))
			{
				$this->warns[]= ["Line ".$key.": ".$ref." failure,not found"];
				continue;
			}

			$shipment = $this->copyShipment($ref,$reTransNum,true);

			if(empty($shipment))
			{
				$this->warns[]= ["Line ".$key.":copy shipment failure"];
				continue;
			}

			$oToll = $orgRateService->calculateCheaperCourierCost($shipment,$orgRates);
			$cost1 = empty($oToll->status)?0:$oToll->minCost;
			$TOLLCourier = empty($oToll->status)?"":$oToll->courier->code;
			$pref = "";
			$pcan = "";
			$pnote = "";
			$pmdata = [];
			if(!empty($cost1))
			{	
				if($onlyCheckCost)
				{
					$this->warns[]= ["Line ".$key.": ".$ref." Cost ".$cost1."|Courier:".$TOLLCourier];
					continue;
				}

				$pref = $shipment->ref;
				$pcan = $shipment->can;
				$pnote = $shipment->note;
				$pmdata = $shipment->mdata;
				$shipment->can = $shipment->ref;
				$shipment->note = $shipment->note."</br>".$shipment->ref;
				$shipment->ref = "";
				if(!empty($shipment->mdata['cargo_delivery']))
				{
					unset($shipment->mdata['cargo_delivery']);
				}
				$shipment->save();
				$status = $shipment->createCourierLabel($oToll->courier);
				if($status)
				{
					$oldShipment->can = "已换单-C";
					$oldShipment->mdata['copy_shipment_id'] = $shipment->id;
					$oldShipment->update(['can','meta']);
					if(!empty($oldShipment->cargo_process))
					{
						$oldShipment->cargo_process->status = CargoProcess::PROCESSDONE;
						$oldShipment->cargo_process->update(['status']);
					}	

					$changeShipmentLabel = new ChangeShipmentLabel();
					$changeShipmentLabel->newref = $shipment->ref;
					$changeShipmentLabel->pid = $shipment->id;
					$changeShipmentLabel->pref = $shipment->can;
					$changeShipmentLabel->phbn = $shipment->hbn;
					$changeShipmentLabel->save();

					$changeShipmentLabel = new ChangeShipmentLabel();
					$changeShipmentLabel->newref = $shipment->ref;
					$changeShipmentLabel->pid = $shipment->id;
					$changeShipmentLabel->pref = $oldShipment->ref;
					$changeShipmentLabel->phbn = $oldShipment->hbn;
					$changeShipmentLabel->is_copy = 1;
					$changeShipmentLabel->save();

					if(!empty($shipment->trans))
					{
						foreach ($shipment->trans as $key => $tran)
						{
							if($tran->connote!=$shipment->ref&&$tran->type==80)
							{
								$tran->delete();
							}
						}
					}

					// $gatepassShipments = GatepassShipment::model()->findAll('fid = :fid',[':fid'=>$shipment->id]);
					// foreach ($gatepassShipments as $key => $gatepassShipment) {
					// 	$gatepassShipment->connote_no = $shipment->ref;
					// 	$gatepassShipment->courier_id = $oToll->courier->org_id;
					// 	$gatepassShipment->status = 0;
					// 	$gatepassShipment->parent_id = 0;
					// 	$gatepassShipment->gate_pass_time = '0000-00-00 00:00:00';
					// 	$gatepassShipment->save();
					// }

					if(!empty($shipment->cargo_process))
					{
						$shipment->cargo_process->status = CargoProcess::DELETED;
						$shipment->cargo_process->save();
					}
					$shipment->save();
					$this->warns[]= ["Line ".$key.": ".$ref." success, Copy HBN:".$shipment->hbn.", new ref:".$shipment->ref];
				}else
				{
					$shipment->ref = $pref;
					$shipment->can = $pcan;
					$shipment->note = $pnote;
					$shipment->mdata = $pmdata;
					$shipment->save();
					if(!empty($shipment->tempErrors))
					{
						$this->warns[]= ["Line ".$key.": ".$ref." failure ".$shipment->tempErrors];
					}
				}
			}else
			{
				$this->warns[]= ["Line ".$key.": ".$ref." failure undeliverable".join(',',$oToll->error)];
			}

		}

		return $this->getSuccessResultWithWarns();
	}

	public function copyShipment($ref,$reTransNum,$isChangePallet=false)
	{
		$oldRecord = ImParcel::model()->find(' (ref = :ref or hbn = :ref) and status <100',[":ref"=>$ref."-C"]);
		if(!empty($oldRecord))
		{
			return $oldRecord;
		}

		$orgShipment = ImParcel::model()->find(' (ref = :ref or hbn = :ref) and status <100',[":ref"=>$ref]);
		$newShipment = new ImParcel('create');
		$newShipment->attributes = $orgShipment->attributes;
		$newShipment->hbn = $newShipment->hbn.'-C';
		$newShipment->cref = $newShipment->cref.'-C';
		$newShipment->ot_id = 10;
		$cnor = new Addr;
		$cnor->attributes = $orgShipment->cnor->attributes;
		$cnor->save();
		$newShipment->cnor_id = $cnor->id;
		$cnee = new Addr;
		$cnee->attributes = $orgShipment->cnee->attributes;

		$cnee->save();
		$newShipment->cnee_id = $cnee->id;
		$newShipment->eitems = $orgShipment->eitems;
		$newShipment->mdata = $orgShipment->mdata;
		unset($newShipment->mdata['direct_courier']);
		unset($newShipment->mdata['rts_scan_date']);
		unset($newShipment->mdata['ss_lbl_request_id']);

		unset($newShipment->mdata['import_billing_id']);
		unset($newShipment->mdata['old_import_billing_id']);
		unset($newShipment->mdata['location']);
		unset($newShipment->mdata['used_location']);
		unset($newShipment->mdata['eiz']);
		unset($newShipment->mdata['eiz_id']);
		unset($newShipment->mdata['chargecode']);
		unset($newShipment->mdata['cargo_delivery']);
		unset($newShipment->mdata['cargo_pickup']);
		unset($newShipment->mdata['copy_shipment_id']);

		$newShipment->mdata['oref'] = $orgShipment->ref;
		$newShipment->ref = '';
		$newShipment->status = 42;//set as local arrival
		$newShipment->consol_id = 0;
		$newShipment->created = date('Y-m-d');
		$newShipment->weight = round(($orgShipment->weight/$orgShipment->pkg)*$reTransNum,2);
		$newShipment->pkg = $reTransNum;
		$newShipment->mdata['total_cbm'] = round($newShipment->cbm*$reTransNum,2);
		// save original RTS shipment No.
		$newShipment->note .= $orgShipment->ref;
		$newShipment->bwf = 0;
		$newShipment->scan_data=[50=>[]];
		$newShipment->scan_no = 0;
		for ($i=0; $i < $reTransNum; $i++)
		{ 
			$newShipment->scan_data[50][$newShipment->hbn."-".($i+1)] = date("Y-m-d H:i:s");
		}


		if($isChangePallet)
		{
			$newShipment->mdata['chargecode'] = @$orgShipment->mdata['chargecode'];
			$totalCBM = $orgShipment->cbm*$orgShipment->pkg;
			$newShipment->cbm = round($totalCBM/$reTransNum,3);
			$newShipment->mdata['total_cbm'] = $totalCBM;
			$newShipment->weight = $orgShipment->weight;
			$height = ($newShipment->cbm*1000000)/(110*110);
			$packages = [];
			for ($i=0; $i < $reTransNum; $i++) { 
				$packages[] = ["weight"=>number_format($newShipment->weight/$newShipment->pkg,3,'.',''),"length"=>110,"width"=>110,"height"=>number_format($height,0,'.','')];
			}
			$newShipment->packs = $packages;
			$newShipment->packages = json_encode($packages);

		}

		if ($newShipment->save()) {
			if($isChangePallet)
			{
				$rackRecords = $orgShipment->rack_record;
				foreach ($rackRecords as $key => $eR) {
					$eR->id = null;
					$eR->isNewRecord = true;
					$eR->shipment_id = $newShipment->id;
					$eR->save();
				}

				$trackRecords = $orgShipment->tracks;
				foreach ($trackRecords as $key => $tR) {
					$tR->id = null;
					$tR->isNewRecord = true;
					$tR->pid = $newShipment->id;
					$tR->save();
				}
			}


			return $newShipment;

		}else
		{
			return null;
		}
	}


	
}
?>