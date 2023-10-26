<?php 
class RtsProcessService extends WarehouseService
{

	public function __construct()
	{
		
	}
	public function scanRts($postData,$op)
	{
		if (!empty($postData))
		{
			// get scan result
			if (isset($postData['barcode'])) {
				$r = new StdClass;
				$r->barcode = @$postData['barcode'];
				$r->location = @$postData['location'];
				$r->color = 'red';
				$r->sound = 'not_found';
				$r->amazon = '';
				$r->blueLabel = '';
				$r->dg = '';
				$r->area = 'sydney';
				$r->stop = 0;
				$r->msg = 'NOT FOUND';
				$r->status = 'UNKNOWN';
				$r->nosound = 0;
				$r->found = 0;
				$r->id = 0;
				$r->label = '';
				$r->print = 0;
				$r->pkg = 0;
				$r->cfire = '';
				$r->log = "";

				$status_suffix = '';
			}else
			{
				$r->color = 'red';
				$r->sound = 'not_found';
				$r->stop = 0;
				$r->msg = 'Empty Barcode';
				$r->status = 'Empty Barcode';
				$r->nosound = 0;
				$r->found = 0;
				$this->groupSound($r);
				echo json_encode($r);
				Yii::app()->end();
			}
		}
		if($op=="rtsscan")
		{
			$this->scanRtsCheckIn($postData,$op,$r);
		}if($op=="rtsscan_kp")
		{
			$this->scanKpRtsCheckIn($postData,$op,$r);
		}elseif($op=="discard")
		{
			$this->scanRtsDiscard($postData,$op,$r);
		}elseif($op=="rtsresend"||$op=="rtsresend_new")
		{
			$this->scanRtsResend($postData,$op,$r);
		}elseif($op=="rtsstatus")
		{
			$this->scanRtsStatus($postData,$op,$r);
		}
	}

	public function scanRtsCheckIn($postData,$op,$r)
	{
		if (!empty($postData))
		{
				$courier_id = 0;
				$sn = 0;
				$isHbn = false;
				$changedShipment = null;
				$changed_label = false;
				$scaned = false;
				$barcode = $r->barcode;
				$location = $r->location;
				$rtsType = @$postData['rts_type'];
				if(empty($location))
				{
					$r->color = 'red';
					$r->sound = 'not_found';
					$r->stop = 0;
					$r->msg = $barcode.' Empty Location';
					$r->status = $barcode.' Empty Location';
					$r->nosound = 0;
					$r->found = 0;
					$this->groupSound($r);
					echo json_encode($r);
					Yii::app()->end();
				}else
				{
					$locationObj = WmsLocation::model()->find('name = :code OR code = :code', [':code' => $location]);
					if(empty($locationObj))
					{
						$r->color = 'red';
						$r->sound = 'not_found';
						$r->stop = 0;
						$r->msg = $barcode.' Location '.$location.'Not Found';
						$r->status = $barcode.' Location '.$location.'Not Found';
						$r->nosound = 0;
						$r->found = 0;
						$this->groupSound($r);
						echo json_encode($r);
						Yii::app()->end();
					}
				}

				$containerNo = empty($postData['search_container_no'])?"":$postData['search_container_no'];
				$r->msg = 'Not Found ' . $barcode;

				$shipment = ShipmentScan::getShipmentByJavaAPI($barcode, $sn, $courier_id, false, 0, $changed_label,$containerNo);


				if (empty($shipment)) {
					$this->groupSound($r);
					echo json_encode($r);
					Yii::app()->end();
				}

				if($shipment->checkIsCargoProcessWithRef())// if it is cargo process
				{
					$this->scanKpRtsCheckIn($postData,$op,$r);
					return;
				}

				if(($barcode==$shipment->ref||$barcode==$shipment->hbn)&&$shipment->pkg==1)
				{
					$sn = 1;
				}

				if(($barcode==$shipment->ref||$barcode==$shipment->hbn)&&$shipment->pkg>1)
				{
					$r->found = -1;
					$r->color = 'red';
					$r->sound = 'not_found';
					$r->stop = 0;
					$r->msg = $barcode.' NOT FOUND';
					$r->status = $barcode.' This is HBN/Ref, Please scan correct barcode';
					$r->nosound = 0;
					$this->groupSound($r);
					echo json_encode($r);
					Yii::app()->end();
				}

				if($shipment->status==ImParcel::STATE_RECEIVED)
				{
					Tracking::imTrack($shipment->ref);
					$shipment->refresh();
					if($shipment->status==ImParcel::STATE_RECEIVED)
					{
						$r->found = -1;
						$r->color = 'red';
						$r->sound = 'not_found';
						$r->stop = 0;
						$r->msg = $barcode.' Waiting Resend';
						$r->status = $barcode.' This shipment is a new shipment waiting for resend. Please check with OP';
						$r->nosound = 0;
						$this->groupSound($r);
						echo json_encode($r);
						Yii::app()->end();
					}
				}

				$r->found = -1;
				$r->id = $shipment->id;
				$r->sn = $sn;
				$check = ShipmentRtsRecord::model()->find("shipment_id=:shipment_id and sn =:sn and type = :type",[":shipment_id"=>$shipment->id,":sn"=>$sn,":type"=>ShipmentRtsRecord::KNOWN]);

				$withoutCheckIn = ImParcelService::isWithoutCheckIn($shipment,$sn,$barcode);
				if($withoutCheckIn)
				{
					$sc = ShipmentRtsRecordConfirm::model()->count("new_id=:shipment_id",[":shipment_id"=>$shipment->id]);// when a shipment which is not RTS new shipment havn't been checkin, record this log and show error
					if(empty($sc))
					{
						Log::add($shipment, 6, ['notes' => "RTS without check in"]);
						$r->color = 'red';
						$r->sound = 'without_checkin';
						$r->stop = 0;
						$r->msg = 'not check in, please report to office';
						$r->status = 'WITHOUT CHECKIN';
						$r->nosound = 0;
						$r->found = -1;
						$this->groupSound($r);
						echo json_encode($r);
						Yii::app()->end();
					}
				}

				// if ($shipment->checkIsCargoProcessWithRef()) {
				// 	$r->status = 'WARNING';
				// 	$r->msg = 'TLD Service, Please Check With OP';
				// 	$this->groupSound($r);
				// 	echo json_encode($r);
				// 	Yii::app()->end();
				// }
				if(!empty($check))
				{
					switch ($check->status) {
						case ShipmentRtsRecord::KNOWN_DISCARD_DONE:{
							$r->msg  = $shipment->ref.' rts_discard '.$shipment->agent->name;
							$r->status = 'rts_discard';
							$r->sound = 'rts_discard';
							$this->groupSound($r);
							echo json_encode($r);
							Yii::app()->end();
						}
						break;
						case ShipmentRtsRecord::RTS_DONE: {
							$r->msg  = $shipment->ref.' rts_done '.$shipment->agent->name;
							$r->status = 'rts_done';
							$r->sound = 'rts_done';
							$this->groupSound($r);
							echo json_encode($r);
							Yii::app()->end();
						}
						break;

					}
				}

				switch ($shipment->status) {
					case 80: {
						if (preg_match('/^T(\d{6,7})$/i', $shipment->cref, $matches)) {
							$this->check3PL($matches[1], $shipment,$r);
						} else {
							$ssRe = ShipmentScan::genShipmentScan($shipment, ShipmentScan::RTS_TYPE, $sn, $this->getDptId(),3600,$barcode);
							if($ssRe)
							{
								$r->color="green";
							}
							$r->found = 1;
							$r->msg  = $shipment->ref.' rts_received '.$shipment->agent->name;
							$r->status = 'rts_received';
							$r->sound = 'rts_received';
						}
					}
					break;
					case 85: {
						$r->msg  = $shipment->ref.' rts_waiting_resend '.$shipment->agent->name;
						$r->status = 'rts_waiting_resend';
						$r->sound = 'rts_waiting_resend';
					}
					break;
					case 86: {
						$r->msg  = $shipment->ref.' rts_discard '.$shipment->agent->name;
						$r->status = 'rts_discard';
						$r->sound = 'rts_discard';
					}
					break;
					default: {
						if($shipment->status==55)
						{
							$r->msg  = $shipment->ref.' held '.$shipment->agent->name;
							$r->status = 'held';
							$this->groupSound($r);
							echo json_encode($r);
							Yii::app()->end();
						}
					}
				}

				if(empty($check)||!empty($rtsType))
				{
					if(empty($rtsType))
					{
						$shipment->status = 80;
						$shipment->mdata['rts_scan_date'] = date('Y-m-d');
						$shipment->cbwf = $shipment->cbwf|256;
						$shipment->mdata['rts_warehouse'] = Yii::app()->session['scan_warehouse'];
						$shipment->update('status', 'meta', 'cbwf');
						ShipmentScan::genShipmentScan($shipment, ShipmentScan::RTS_TYPE, $sn, $this->getDptId(),3600,$barcode);
						if (preg_match('/^T(\d{6,7})$/i', $shipment->cref, $matches)) {
							$r->found = 1;
							$this->check3PL($matches[1], $shipment,$r);
						} else {
							$r->found = 1;
							$r->msg  = $shipment->ref.' rts_received '.$shipment->agent->name;
							$r->status = 'rts_received';
							$r->sound = 'rts_received';
							$r->color="green";
						}
					}else
					{
						$r->found = 1;
						$r->msg  = $shipment->ref.' rts_wrong_courier_received '.$shipment->agent->name;
						$r->status = 'rts_wrong_courier_received';
						$r->sound = 'rts_received';
						$r->color="green";
					}
				}

				$this->groupSound($r);
				echo json_encode($r);
				Yii::app()->end();
		}
	}

	public function scanKpRtsCheckIn($postData,$op,$r)
	{
		if (!empty($postData))
		{
				$courier_id = 0;
				$sn = 0;
				$isHbn = false;
				$changedShipment = null;
				$changed_label = false;
				$scaned = false;
				$barcode = $r->barcode;
				$location = $r->location;

				if(empty($location))
				{
					$r->color = 'red';
					$r->sound = 'not_found';
					$r->stop = 0;
					$r->msg = $barcode.' Empty Location';
					$r->status = $barcode.' Empty Location';
					$r->nosound = 0;
					$r->found = 0;
					$this->groupSound($r);
					echo json_encode($r);
					Yii::app()->end();
				}else
				{
					$locationObj = WmsLocation::model()->find('name = :code OR code = :code', [':code' => $location]);
					if(empty($locationObj))
					{
						$r->color = 'red';
						$r->sound = 'not_found';
						$r->stop = 0;
						$r->msg = $barcode.' Location '.$location.'Not Found';
						$r->status = $barcode.' Location '.$location.'Not Found';
						$r->nosound = 0;
						$r->found = 0;
						$this->groupSound($r);
						echo json_encode($r);
						Yii::app()->end();
					}
				}

				$containerNo = empty($postData['search_container_no'])?"":$postData['search_container_no'];
				$r->msg = 'Not Found ' . $barcode;

				$shipment = ShipmentScan::getShipmentByJavaAPI($barcode, $sn, $courier_id, false, 0, $changed_label,$containerNo);

				if (empty($shipment)||empty($shipment->cargo_process)) {
					$this->groupSound($r);
					echo json_encode($r);
					Yii::app()->end();
				}

				// when the status of cargo process is not RTS DELIVERY, we process the data;
				if($shipment->cargo_process->status!=CargoProcess::RTSDELIVERY)
				{
					$shipment->cargo_process->status=CargoProcess::RTSDELIVERY;
					$shipment->cargo_process->update(['status']);

					$wrs = WmsRackShipment::model()->find('shipment_id = :sid ', [':sid' => $shipment->id]);
					if(!empty($wrs)){
						$wrs->status=WmsRackShipment::REMOVED;
						$wrs->save();
					}

					$wrs = new WmsRackShipment('create');
					$wrs->shipment_id = $shipment->id;
					$wrs->sno = $sn;
					$wrs->barcode = $barcode;
					// $wrs->sno = 0;
					$wrs->rack_id = $locationObj->id;
					$wrs->save();

					$gp = GatepassShipment::model()->find("fid = :fid and connote_no = :no and parent_id!=0",[":fid"=>$shipment->id,":no"=>$shipment->ref]);

					if(!empty($gp))
					{
						$t = date("Y-m-d H:i:s");
						$gps = GatepassShipment::model()->findAll("fid = :fid and connote_no = :no and parent_id=:parentId",[":fid"=>$shipment->id,":no"=>$shipment->ref,":parentId"=>$gp->parent_id]);
						foreach ($gps as $key => $gpo)
						{
							$gpo->scan_time = $t;
							$gpo->gate_pass_time = "0000-00-00 00:00:00";
							$gpo->parent_id = 0;
							$gpo->status = 0;
							$gpo->id = null;
							$gpo->isNewRecord = true;
							$gpo->save();
						}
						

					}
				}

				$r->found = 1;
				$r->msg  = $shipment->ref.' rts_received ';
				$r->status = 'rts_received';
				$r->sound = 'rts_received';
				$r->color="green";

				$this->groupSound($r);
				echo json_encode($r);
				Yii::app()->end();
		}
	}

	public function scanRtsDiscard($postData,$op,$r)
	{
		if (!empty($postData))
		{
				$barcode = $r->barcode;
				$record = ShipmentRtsRecord::model()->find("barcode = :barcode and (status=:status or status = :status2)",[":barcode"=>$barcode,":status"=>ShipmentRtsRecord::WAITING_UNKNOWN_DISCARD,":status2"=>ShipmentRtsRecord::WAITING_KNOWN_DISCARD]);
				if(empty($record))
				{
					$record = ShipmentRtsRecord::model()->find("barcode = :barcode and (status=:status or status = :status2)",[":barcode"=>$barcode,":status"=>ShipmentRtsRecord::UNKNOWN_DISCARD_DONE,":status2"=>ShipmentRtsRecord::KNOWN_DISCARD_DONE]);
					if(!empty($record))
					{
						$r->color = 'green';
						$r->sound = 'rts_discard';
						$r->stop = 0;
						$r->msg = $barcode.' Rts Discard';
						$r->status = $barcode.' Rts Discard';
						$r->nosound = 0;
						$r->found = 0;
						$this->groupSound($r);
						echo json_encode($r);
						Yii::app()->end();
					}

					$r->color = 'red';
					$r->sound = 'not_found';
					$r->stop = 0;
					$r->msg = 'Not Found '.$barcode.' Record';
					$r->status = 'Not Found '.$barcode.' Record';
					$r->nosound = 0;
					$r->found = 0;
					$this->groupSound($r);
					echo json_encode($r);
					Yii::app()->end();
				}else
				{
					if($record->type==ShipmentRtsRecord::KNOWN)
					{
						$record->status = ShipmentRtsRecord::KNOWN_DISCARD_DONE;
						$record->shipment->status = ImParcel::RTS_DISCARDED;
						$record->shipment->update(['status']);
					}else
					{
						$record->status = ShipmentRtsRecord::UNKNOWN_DISCARD_DONE;
					}
					$record->discard_time = date('Y-m-d H:i:s');
					$record->discard_user_id = User::currentUserID();
					$record->update(['status','discard_user_id','discard_time']);
					$r->color = 'green';
					$r->sound = 'rts_discard';
					$r->stop = 0;
					$r->msg = $barcode.' Rts Discard';
					$r->status = $barcode.' Rts Discard';
					$r->nosound = 0;
					$r->found = 0;
					$this->groupSound($r);
					echo json_encode($r);
					Yii::app()->end();
				}

				$this->groupSound($r);
				echo json_encode($r);
				Yii::app()->end();
		}
	}


	public function scanRtsResend($postData,$op,$r)
	{
		if (!empty($postData))
		{
			$barcode = $r->barcode;
			$sn = 0;
			$courier_id = 0;
			$changed_label = false;
			$containerNo = "";
			$shipment = ShipmentScan::getShipmentByJavaAPI($barcode, $sn, $courier_id, false, 0, $changed_label,$containerNo);
			if (empty($shipment)) {
				$this->groupSound($r);
				echo json_encode($r);
				Yii::app()->end();
			}

			$rtsSubmit = ShipmentRtsRecordConfirm::model()->find(["condition"=>"original_id = :pid or new_id = :pid","params"=>[":pid"=>$shipment->id],"order"=>"id desc"]);
			if (empty($rtsSubmit)) {
				$this->groupSound($r);
				echo json_encode($r);
				Yii::app()->end();
			}

			if($rtsSubmit->original_id==$shipment->id&&$op=="rtsresend")
			{
				$r->color = 'green';
				$r->sound = 'rts_received';
				$r->stop = 0;
				$r->msg = 'print new shipment label';
				$r->status = $barcode.' Rts Received';
				$r->print = 1;
				$r->id = $rtsSubmit->new_id;
				$r->nosound = 0;
				$r->found = 1;
				$r->sn =0;
				$this->groupSound($r);
				echo json_encode($r);
				Yii::app()->end();
			}elseif($rtsSubmit->original_id!=$shipment->id&&$op=="rtsresend_new")
			{
				$this->manifestRTSSubmit($rtsSubmit);
				$r->color = 'green';
				$r->sound = 'rts_done';
				$r->stop = 0;
				$r->msg = $barcode.' Rts Done';
				$r->status = $barcode.' Rts Done';
				$r->nosound = 0;
				$r->found = 1;
				$r->sn =0;
				$this->groupSound($r);
				echo json_encode($r);
				Yii::app()->end();
			}else
			{
				$this->groupSound($r);
				echo json_encode($r);
				Yii::app()->end();
			}
			
		}
	}

	public function scanRtsStatus($postData,$op,$r)
	{
		if (!empty($postData))
		{
				$courier_id = 0;
				$sn = 0;
				$isHbn = false;
				$changedShipment = null;
				$changed_label = false;
				$scaned = false;
				$barcode = $r->barcode;
				$containerNo="";

				$r->msg = "";

				$shipment = ShipmentScan::getShipmentByJavaAPI($barcode, $sn, $courier_id, false, 0, $changed_label,$containerNo);

				if(!empty($shipment))
				{
					$records = ShipmentRtsRecord::model()->findAll("shipment_id=:shipment_id",[":shipment_id"=>$shipment->id]);
					if(empty($records))
					{
						$confirm = ShipmentRtsRecordConfirm::model()->find("new_id=:new_id",[":new_id"=>$shipment->id]);
						if(!empty($confirm))
						{
							$records = $confirm->records;
						}
					}

				}else
				{
					$records = ShipmentRtsRecord::model()->findAll("barcode=:barcode",[":barcode"=>$barcode]);
				}

				// if ($shipment->checkIsCargoProcessWithRef()) {
				// 	$r->status = 'WARNING';
				// 	$r->msg = 'TLD Service, Please Check With OP';
				// 	$this->groupSound($r);
				// 	echo json_encode($r);
				// 	Yii::app()->end();
				// }
				if(!empty($records))
				{
					$r->found = 1;
					foreach ($records as $key => $record) {
						$r->msg  .= $record->barcode." ".$record->getStatus()."</br>";
						$r->status .= $record->barcode." ".$record->getStatus()."</br>";
					}
					
					$r->color="green";
				}else
				{
					$r->found = 0;
					$r->msg="Not Found";
					$r->status="Not Found";
					
					$r->color="red";
				}

				$this->groupSound($r);
				echo json_encode($r);
				Yii::app()->end();
		}
	}
	
	private function check3PL($taskid, $shipment,$r)
	{
		$task = WmsTask::model()->findByPk($taskid);
		if (!empty($task) && $task->job->org_id == $shipment->agent_id) {
			if (!empty($task->deliveryTask->mdata['shipment_id']) && in_array($shipment->id, $task->deliveryTask->mdata['shipment_id'])) {
				// mark rts detect
				if (($task->bwf & 128) == 0) $task->bwf |= 128;
				// mark arrive warehouse
				if (empty($task->mdata['return_status']) || $task->mdata['return_status'] < WmsTask::WMS_TASK_RETURN_STATUS_ARRIVE_WAREHOUSE) $task->mdata['return_status'] = WmsTask::WMS_TASK_RETURN_STATUS_ARRIVE_WAREHOUSE;
				$task->update('bwf', 'meta');
				$r->msg  = $shipment->ref.' 3pl_rts_received ';
				$r->status = '3pl_rts_received';
				$r->found = 1;
				$r->sound = 'rts_received';
			} else if (!empty($task->deliveryTask->mdata['return_shipment_id']) && in_array($shipment->id, $task->deliveryTask->mdata['return_shipment_id'])) {
				// mark rts detect
				if (($task->bwf & 128) == 0) $task->bwf |= 128;
				// mark arrive warehouse
				if ($task->status < WmsTask::WMS_TASK_RETURN_STATUS_ARRIVE_WAREHOUSE) $task->status = WmsTask::WMS_TASK_RETURN_STATUS_ARRIVE_WAREHOUSE;
				$task->update('bwf', 'status');
				$r->msg  = $shipment->ref.' 3pl_rts_received ';
				$r->status = '3pl_rts_received';
				$r->found = 1;
				$r->sound = 'rts_received';
			} else {
				$r->msg  = $shipment->ref.' rts_received ';
				$r->status = 'rts_received';
				$r->found = 1;
				$r->sound = 'rts_received';
			}
		} else {
			$r->msg  = $shipment->ref.' rts_received ';
			$r->status = 'rts_received';
			$r->found = 1;
			$r->sound = 'rts_received';
		}

		$this->groupSound($r);
		echo json_encode($r);
		Yii::app()->end();
	}


	public function updateShipmentRtsProcess($model,$ref,$sno)
	{
		$shipment = Shipment::model()->find("hbn = :ref or ref=:ref or cref = :ref and status<100",[":ref"=>$ref]);
		if(empty($shipment))
		{
			return "Empty Shipment";
		}
		$model->adjust_shipment_id = $shipment->id;
		$model->adjust_sn = $sno;
		$model->status = ShipmentRtsRecord::UNKNOWN_NEED_PRINT;
		$model->unknown_edit_time = date("Y-m-d H:i:s");
		$result = $model->save();
		$model->log("link shipment");
		return $result;
	}

	/**
	 * create a new shipment for returned item
	 * @param $orgShipment
	 * @return string
	 */
	public function createNewShipment($orgShipment, $rt)
	{
		$newShipment = new ImParcel('create');

		// copy basic attributes
		$newShipment->attributes = $orgShipment->attributes;
		$cnor = new Addr;
		$cnor->attributes = $orgShipment->cnor->attributes;
		$cnor->save();
		$newShipment->cnor_id = $cnor->id;
		$cnee = new Addr;
		$cnee->attributes = $orgShipment->cnee->attributes;

		$cnee->save();
		$newShipment->packages = $orgShipment->packages;
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
		$newShipment->hbn = ''; //  in order to create a new one in case
		$newShipment->ref = '';
		$newShipment->status = 25; // set as Received status
		$newShipment->consol_id = 0;
		$newShipment->created = date('Y-m-d');

		// save original RTS shipment No.
		$newShipment->mdata['rts_org_no'] = $orgShipment->hbn;
		$newShipment->note .= $orgShipment->ref;
		$newShipment->bwf = 0;

		$newShipment->mdata['etower_shipment_orderid'] = "";
		$newShipment->mdata['ubi_toll'] = "";
		$newShipment->mdata['barcode'] = "";
		$newShipment->packs = $orgShipment->packs;

		$o = new stdClass();
		$o->error = [];
        $reply = ChooseShipment::funcAfterSelectServiceByChargeCode($o,$newShipment,$rt,false);

        if($reply->success)
        {
        	//handling RTS record
        	$shipmentRecords = ShipmentRTSRecord::model()->findAll("shipment_id = :oid and status=:status",[":oid"=>$orgShipment->id,":status"=>ShipmentRtsRecord::NEW]);
        	$shipmentRtsRecordConfirm = new ShipmentRtsRecordConfirm();
        	$shipmentRtsRecordConfirm->agent_id = $orgShipment->agent_id;
        	$shipmentRtsRecordConfirm->user_id = User::currentUserID();
        	$shipmentRtsRecordConfirm->name = $newShipment->cnee->name;
        	$shipmentRtsRecordConfirm->address = $newShipment->cnee->address;
        	$shipmentRtsRecordConfirm->tel = $newShipment->cnee->tel;
        	$shipmentRtsRecordConfirm->suburb = $newShipment->cnee->suburb;
        	$shipmentRtsRecordConfirm->state = $newShipment->cnee->state;
        	$shipmentRtsRecordConfirm->postcode = $newShipment->cnee->postcode;
        	$shipmentRtsRecordConfirm->country = $newShipment->cnee->country;
        	$shipmentRtsRecordConfirm->email = $newShipment->cnee->email;
        	$shipmentRtsRecordConfirm->status = ShipmentRtsRecordConfirm::RTS_WAITING_RESEND;
        	$shipmentRtsRecordConfirm->original_id = $orgShipment->id;
        	$shipmentRtsRecordConfirm->new_id = $newShipment->id;
        	$shipmentRtsRecordConfirm->save();

        	foreach($shipmentRecords AS $k1=>$shipmentRecord)
        	{
        		$shipmentRecord->status = ShipmentRTSRecord::RTS_WAITING_RESEND;
        		$shipmentRecord->confirm_id = $shipmentRtsRecordConfirm->id;
        		$shipmentRecord->update(['confirm_id','status']);
        	}

        	$orgShipment->refresh();
			$orgShipment->note .= $newShipment->ref;
			$orgShipment->update(['note']);
			// add tracking information for new one
			$newShipment->addTracking(95, 'Shipment tranship from : ' . $orgShipment->hbn);

			// add new tranship
			// in order to link original one with new one
			$ts = new Tranship;
			$ts->pid = $orgShipment->id;
			$ts->org_id = $rt->courier->id; // default as Australia Post Office
			$ts->man_id = 0;
			$ts->type = 90; // RTS Delivery Courier
			$ts->status = 19; // Final moving
			$ts->connote = $newShipment->hbn;
			$ts->time = date('Y-m-d H:i:s');
			$ts->save();

			// add tracking information for original one
			$orgShipment->addTracking(95, 'Shipment tranship with : ' . $newShipment->hbn);
		}else
		{
			$newShipment->addError(join(',',$reply->error),join(',',$reply->error));
		}
		$newShipment->refresh();
		$newShipment->save();
		return $newShipment;
	}

	private function manifestRTSSubmit($rtsSubmit)
	{
		$action = "RTS NEW";
		$courier_id = null;
        if(empty($courier_id))
        {
            $courier_id=$rtsSubmit->newShipment->getCourierId();
        }
        GatepassShipment::gatepassScan($rtsSubmit->newShipment, $courier_id, 1, $rtsSubmit->newShipment->ddpt_id,$action);// let the new shipment can go to gatepass

		$trans = $rtsSubmit->newShipment->trans;
		$check = true;
		if(!empty($trans))
		{
			$tran = end($trans);
			if(!empty($tran->mdata['oid']))
			{
				$check = false;
			}
		}

		if($check)
		{
			$shipmentIds = [$rtsSubmit->new_id];
		    $imcoConsolService = new ImConsolService();
		    $imcoConsolService->gatepassShipmentsForManifest($shipmentIds);
		}
		
		$this->markRtsDone($rtsSubmit->newShipment);

		if($rtsSubmit->status!=ShipmentRtsRecordConfirm::RTS_DONE)
		{
			$this->createRtsList([$rtsSubmit->originalShipment],$rtsSubmit->originalShipment->ref,"",$rtsSubmit->newShipment->ddpt_id);

			foreach ($rtsSubmit->records as $key => $record) {
				$record->status = ShipmentRtsRecord::RTS_DONE;
				$record->update(['status']);
			}
		    $rtsSubmit->status = ShipmentRtsRecordConfirm::RTS_DONE;
		    $rtsSubmit->rts_done_time = date("Y-m-d H:i:s");
		    $rtsSubmit->rts_done_user_id = User::currentUserID();
			$rtsSubmit->update(['status','rts_done_time','rts_done_user_id']);
		}
		
	}

		/**
	 * create RTS ready list all shipments in list will be ready for pickup again
	 * @param $shipments
	 * @param $note
	 * @return string
	 */
	private function createRtsList($shipments, $note, $company, $dptid)
	{

		// create new Rts shipments List
		$modelRtsList = new RtsList('create');
		$modelRtsList->status = 10;
		$modelRtsList->ref = $note;
		$modelRtsList->mdata['rts_company'] = $company;
		$modelRtsList->dpt_id = $dptid; // default as Sydney
		$modelRtsList->save();

		// save related all shipment
		foreach ($shipments as $shipment) {
			$attr = [
				'mani_id' => $modelRtsList->id,
				'fid' => $shipment->id,
				'model' => 'ImParcel',
			];
			$mm = new ManiMap;
			$mm->attributes = $attr;
			$mm->status = 10;
			$mm->save();
		}
		return $modelRtsList->id;
	}


	private function markRtsDone($s)
	{
		if (!isset($s->mdata['rts_org_no'])) {
			return;
		}

		$orgShipmentNo = $s->mdata['rts_org_no'];
		$orgShipment = Shipment::model()->find('hbn = :hbn', [':hbn' => $orgShipmentNo]);
		$orgShipment->status = ImParcel::RTS_DONE; // set as reshipping done
		$orgShipment->mdata['rts_reshipping_time'] = date('Y-m-d'); // recored RTS reshipping date
		$orgShipment->update('status');
		$orgShipment->updateMeta();
	}

	/**
	 * create RTS resend fee invoice
	 * @param $parcel
	 * @param $amount
	 */
	public function createRTSResendInvoice(&$parcel, $amount)
	{
		$oParcel = ImParcel::model()->findByPk($parcel->getRTSOrgShipNoId());
		if(!empty($oParcel->consol_id))
		{
			$oConsol = $oParcel->consol;
			$oConsol->isTLA();
		}
		// check to see if related pending invoice existing
		$invLines = InvLine::model()->findAll('model = :model AND fid = :fid', [':model' => 'ImParcel', 'fid' => $parcel->id]);
		$invoice = null;
		if (!empty($invLines)) {
			// try to check related invoice belongs to RTS fee or not
			foreach ($invLines as $invLine) {
				$invoiceRts = Invoice::model()->findByPk($invLine->inv_id);
				if (!empty($invoiceRts) && $invoiceRts->type == Invoice::INVOICE_TYPE_RTS_RESEND_FEE) {
					// delete all old invoice line
					InvLine::model()->deleteAll('inv_id = :invid', [':invid' => $invoiceRts->id]);
					$invoice = $invoiceRts;
				}
			}
		}
		if (empty($invoice)) {
			// not existing yet , just create a new one
			$invoice = new Invoice();
			$invoice->type = Invoice::INVOICE_TYPE_RTS_RESEND_FEE; // for RTS fee invoice
			$invoice->to_id = $parcel->agent_id;
			$invoice->man_id = 0; // in case manifest id means nothing
			$invoice->dpt_id = $parcel->ddpt_id;
			$invoice->dpmt = Invoice::DPMT_IMPORT;

			// if department not set , we set as Sydney warehouse
			if (empty($invoice->dpt_id)) {
				$invoice->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
			}
			$invoice->status = Invoice::INVOICE_STATUS_PENDING;
			$invoice->date = date('Y-m-d'); // here just dummy date , once accounting change to posted , should create new date for the invoice

			// get invoice currency
			$invoiceCurrency = 1; // default as AUD
			$orgRate = OrgRate::model()->find('org_id = :oid AND type = 40', [':oid' => $parcel->agent_id]);
			if (!empty($orgRate)) {
				$invoiceCurrency = $orgRate['currency'];
			}
			$invoice->currency = $invoiceCurrency;

			// set invoice name , address and pay terms information
			$owner = $parcel->agent;
			$invoice->mdata['name'] = $owner->name;
			$invoice->mdata['address'] = $owner->getAddress();
			$invoice->mdata['payterm'] = empty($owner->extra['payterm']) ? '2 days' : $owner->extra['payterm'] . ' days';

			// here just dummy date , once accounting change to posted , should create new date for the invoice
			$invoice->due = Invoice::calcDue($invoice->date, $invoice->mdata['payterm']);
		}

		$rtsFee = $amount;
		// check to see if we should including GST (default 10%)
		$gst = 0;
		if (isset($parcel->agent->extra['incl_gst']) && $parcel->agent->extra['incl_gst'] == 1) {
			$gst = round($rtsFee * 10 / 100, 2);
			$rtsFee += $gst;
			$invoice->gst = $gst;
		}

		$invoice->total = $rtsFee;
		$invoice->save();

		// create related invoice line and attached it to the invoice
		$il = new InvLine;
		$il->inv_id = $invoice->id;
		$il->amount = $rtsFee;
		$il->gst = $gst;
		$il->mdata['items'] = [[$parcel->note, 'Returned to Sender Reshipping Fee', $il->amount - $il->gst, $parcel->ref]];
		$il->model = 'ImParcel';
		$il->fid = $parcel->id;
		$il->qty = 1;
		$il->save();

		// add storage fee
		if (isset($parcel->mdata['rts_org_no'])) {
			$orgShipment = Shipment::model()->find('hbn = :hbn', [':hbn' => $parcel->mdata['rts_org_no']]);

			// set original shipment as all done status
			$orgShipment->status = ImParcel::RTS_WAITING_RESHIPPING;
			$orgShipment->update('status');

			// calculate RTS storage fee invoice
			if (!empty($orgShipment) && isset($orgShipment->mdata['rts_scan_date'])) {
				$receivedDate = $orgShipment->mdata['rts_scan_date'];
				$inTime = new DateTime($receivedDate, new DateTimeZone('Australia/Sydney'));
				$outTime = new DateTime('now', new DateTimeZone('Australia/Sydney'));
				$storageDays = $outTime->diff($inTime)->format("%a");
				$freeStorageDays = $this->getRtsStorageTerms($orgShipment->agent->id);
				$storageDays = $storageDays - $freeStorageDays;
				if ($storageDays > 0) {
					$storageCharge = $this->getRtsStorageRate($orgShipment->agent->id) * $orgShipment->weight * $storageDays;
					if ($storageCharge > 0) {
						$il = new InvLine;
						$il->inv_id = $invoice->id;
						$il->amount = $storageCharge;
						$il->mdata['items'] = [[$parcel->hbn, 'Returned to Sender Storage Fee', $il->amount]];
						$il->model = 'ImParcel';
						$il->fid = $parcel->id;
						$il->save();

						if (isset($parcel->agent->extra['incl_gst']) && $parcel->agent->extra['incl_gst'] == 1) {
							$gst = round($storageCharge * 10 / 100, 2);
							$storageCharge += $gst;
							$invoice->gst += $gst;
							$il->gst = $gst;
							$il->amount += $gst;
							$il->update(['amount', 'gst']);
						}

						$invoice->total += $storageCharge;
						$invoice->update(['total', 'gst']);
					}
				}
			}
		}
	}

	/**
	 * get RTS storage terms
	 * @param $orgId
	 * @return int
	 */
	private function getRtsStorageTerms($orgId)
	{

		// get organizaton price rate
		$orgRate = OrgRate::model()->find('org_id = :oid AND type = 40', [':oid' => $orgId]);
		if (empty($orgRate)) {
			return 0;
		}

		// get org rate details
		$rateInfo = json_decode($orgRate['meta']);
		$code = 'AU'; // RTS fee belongs to Australia local
		$rtsStrTerms = 0;
		if (isset($rateInfo->$code->rts_strterms)) {
			$rtsStrTerms = intval($rateInfo->$code->rts_strterms);
		}
		return $rtsStrTerms;
	}

	/**
	 * get RTS storage rate
	 * @param $orgId
	 * @return float|int
	 */
	private function getRtsStorageRate($orgId)
	{

		// get organizaton price rate
		$orgRate = OrgRate::model()->find('org_id = :oid AND type = 40', [':oid' => $orgId]);
		if (empty($orgRate)) {
			return 0;
		}

		// get org rate details
		$rateInfo = json_decode($orgRate['meta']);
		$code = 'AU'; // RTS fee belongs to Australia local
		$rtsStrFee = 0;
		if (isset($rateInfo->$code->rts_strrate)) {
			$rtsStrFee = floatval($rateInfo->$code->rts_strrate);
		}
		return $rtsStrFee;
	}


	public function discardKnownRTS($id)
	{
		$org = Org::model()->findByPk(User::currentUserOrgId());
		$rtsRecords = ShipmentRtsRecord::model()->findAll("shipment_id = :shipment_id and status = :status",[":shipment_id"=>$id,":status"=>ShipmentRtsRecord::NEW]);
		$orgShipment = Shipment::model()->findByPk($id);
		$orgShipment->status = ImParcel::RTS_DISCARDED;
		$orgShipment->update(['status']);
		$shipmentRtsRecordConfirm = new ShipmentRtsRecordConfirm();
        $shipmentRtsRecordConfirm->agent_id = $orgShipment->agent_id;
        $shipmentRtsRecordConfirm->user_id = User::currentUserID();
        $shipmentRtsRecordConfirm->status = ShipmentRtsRecordConfirm::RTS_DISCARDED;
        $shipmentRtsRecordConfirm->original_id = $orgShipment->id;
        $result = $shipmentRtsRecordConfirm->save();

		foreach($rtsRecords as $key => $rtsRecord)
		{
			$rtsRecord->confirm_id = $shipmentRtsRecordConfirm->id;
			$rtsRecord->status = ShipmentRtsRecord::WAITING_KNOWN_DISCARD;
			$rtsRecord->update(['status','confirm_id']);
		}
		return $result;
	}

	public function discardUnknownRTS($id)
	{
		$rtsRecords = ShipmentRtsRecord::model()->findAll("id =:id",[":id"=>$id]);
		$shipmentRtsRecordConfirm = new ShipmentRtsRecordConfirm();
        $shipmentRtsRecordConfirm->agent_id = 0;
        $shipmentRtsRecordConfirm->user_id = User::currentUserID();
        $shipmentRtsRecordConfirm->status = ShipmentRtsRecordConfirm::RTS_DISCARDED;
        $shipmentRtsRecordConfirm->original_id = 0;
        $result = $shipmentRtsRecordConfirm->save();
		foreach($rtsRecords as $key => $rtsRecord)
		{
			$rtsRecord->confirm_id = $shipmentRtsRecordConfirm->id;
			$rtsRecord->status = ShipmentRtsRecord::WAITING_UNKNOWN_DISCARD;
			$rtsRecord->update(['status','confirm_id']);
		}
		return $result;
	}


	public function submitRTCList($shipmentIds)
	{
		$strId  = join(',',$shipmentIds);
		if(empty($shipmentIds)) return false;

		$shipments = Shipment::model()->findAll('id in  ('.$strId.') and status != 55');
		$rtsRecords = ShipmentRtsRecord::model()->findAll(' shipment_id in ('.$strId.') and status = :status',[":status"=>ShipmentRtsRecord::NEW]);
		if(empty($rtsRecords))
		{
			return "RTC Records were submitted";
		}

		$orgName = Org::model()->findByPk($shipments[0]->agent_id)->name;
		$gp = new Manifest(); 
		$gp->company = $orgName;
		$gp->ref = "RTC To ".$orgName;
		$gp->dpt_id = $rtsRecords[0]->warehouse_id;
		$gp->type = 80;
		$result = $gp->save();
		foreach($shipments as $k1 =>$shipment)
		{
			$shipment->status = ImParcel::RTC_WAITING;
			$shipment->update(['status']);
			$maniMap = new ManiMap();
			$maniMap->mani_id = $gp->id;
			$maniMap->model = 'Shipment';
			$maniMap->fid = $shipment->id;
			$maniMap->save();
		}

		$shipmentRtsRecordConfirm = new ShipmentRtsRecordConfirm();
        $shipmentRtsRecordConfirm->agent_id = $shipments[0]->agent_id;
        $shipmentRtsRecordConfirm->user_id = User::currentUserID();
        $shipmentRtsRecordConfirm->status = ShipmentRtsRecordConfirm::RTC_WAITING;
        $shipmentRtsRecordConfirm->rtc_gp_id = $gp->id;
        $shipmentRtsRecordConfirm->save();
		foreach($rtsRecords as $key => $rtsRecord)
		{
			$rtsRecord->confirm_id = $shipmentRtsRecordConfirm->id;
			$rtsRecord->status = ShipmentRtsRecord::RTC_WAITING;
			$rtsRecord->update(['status','confirm_id']);
		}

		return $result;
	}

	public function importFileForRtcGp($file,$id)
	{
        $shipmentRtsRecordConfirm = ShipmentRtsRecordConfirm::model()->findByPk($id);
        $shipmentRtsRecordConfirm->status = ShipmentRtsRecordConfirm::RTC_DONE;
        $shipmentRtsRecordConfirm->update(['status']);
        $imconsolService = new ImConsolService();
        $check = [];
        $result = $imconsolService->importFileForGP($file,$shipmentRtsRecordConfirm->rtc_gp_id);
        foreach ($shipmentRtsRecordConfirm->records as $key => $record) {
        	if($record->shipment->status==ImParcel::RTC_WAITING&&!in_array($record->shipment->id,$check))
        	{
        		$record->shipment->status = ImParcel::RTC_DONE;
        		$record->shipment->update(['status']);
        		$check[] = $record->shipment->id;
        	}
        }
        return $result;
	}

	public function getRtsProcessReportProvide($refresh = false,$isOp = 1)
	{
		$kpiService = new KpiService();
		$cache = $this->getCacheData("TodayRTSProcessData".$isOp);
		// if(!empty($cache)&&$refresh==false)
		// {
		// 	return $cache;
		// }
		$provide = [];
		if($isOp==1)
		{
			$unknownNumbers = ShipmentRtsRecord::model()->count(" type = :type and adjust_shipment_id=0 and status<:status",[":type"=>ShipmentRtsRecord::UNKNOWN,":status"=>ShipmentRtsRecord::WAITING_UNKNOWN_DISCARD]);

			$status = ShipmentRtsRecord::NEW;
			$records = ShipmentRtsRecord::model()->findAll("type=:type and status=:status",[':status'=>ShipmentRtsRecord::NEW,':type'=>ShipmentRtsRecord::KNOWN]);
			$ids = array_column($records,'shipment_id');
			$ids[] = -1;
			$receivedNumbers = Shipment::model()->count(" id in (".join(',',$ids).")");
			$recordNumbers = ShipmentRtsRecordConfirm::model()->count(" status = :status",[":status"=>ShipmentRtsRecordConfirm::RTC_WAITING]);
			$provide[ShipmentRtsRecord::UNKNOWN]=["id"=>ShipmentRtsRecord::UNKNOWN,"status"=>ShipmentRtsRecord::UNKNOWN,"today_left"=>$unknownNumbers];
			$provide[ShipmentRtsRecord::KNOWN]=["id"=>ShipmentRtsRecord::KNOWN,"status"=>ShipmentRtsRecord::KNOWN,"today_left"=>$receivedNumbers];
			$provide[ShipmentRtsRecordConfirm::RTC_WAITING]=["id"=>ShipmentRtsRecordConfirm::RTC_WAITING,"status"=>ShipmentRtsRecordConfirm::RTC_WAITING,"today_left"=>$recordNumbers];

		}else
		{
			$unknownNumbers = ShipmentRtsRecord::model()->count(" warehouse_id = :warehouseId and type = :type and adjust_shipment_id!=0",[":warehouseId"=>$this->getDptId(),":type"=>ShipmentRtsRecord::UNKNOWN]);
			$resendNumbers = ShipmentRtsRecordConfirm::model()->with(['records'])->count("records.warehouse_id=:warehouseId and t.status = :status",[":warehouseId"=>$this->getDptId(),":status"=>ShipmentRtsRecordConfirm::RTS_WAITING_RESEND]);
			$discardNumbers = ShipmentRtsRecord::model()->count(" status in (".join(',',[ShipmentRtsRecord::WAITING_UNKNOWN_DISCARD,ShipmentRtsRecord::WAITING_KNOWN_DISCARD]).") and warehouse_id = :warehouseId",[":warehouseId"=>$this->getDptId()]);
			$provide[ShipmentRtsRecord::UNKNOWN]=["id"=>ShipmentRtsRecord::UNKNOWN,"status"=>ShipmentRtsRecord::UNKNOWN,"today_left"=>$unknownNumbers];
			$provide[ShipmentRtsRecordConfirm::RTS_WAITING_RESEND]=["id"=>ShipmentRtsRecordConfirm::RTS_WAITING_RESEND,"status"=>ShipmentRtsRecordConfirm::RTS_WAITING_RESEND,"today_left"=>$resendNumbers];
			$provide[ShipmentRtsRecord::WAITING_UNKNOWN_DISCARD]=["id"=>ShipmentRtsRecord::WAITING_UNKNOWN_DISCARD,"status"=>ShipmentRtsRecord::WAITING_UNKNOWN_DISCARD,"today_left"=>$discardNumbers];
		}

		$this->setCacheData("TodayRTSProcessData".$isOp,$provide,600);
		return $provide;


	}

	public function getWhscanRTSResendProvide()
	{
		$leftNumbers = ShipmentRtsRecordConfirm::model()->with(['records'])->count("records.warehouse_id=:warehouseId and t.status = :status",[":warehouseId"=>$this->getDptId(),":status"=>ShipmentRtsRecordConfirm::RTS_WAITING_RESEND]);
		$doneNumbers = ShipmentRtsRecordConfirm::model()->with(['records'])->count("records.warehouse_id=:warehouseId and t.status = :status and to_days(rts_done_time) = to_days(NOW()) ",[":warehouseId"=>$this->getDptId(),":status"=>ShipmentRtsRecordConfirm::RTS_DONE]);
		return [date("Y-m-d H:i:s"),$leftNumbers+$doneNumbers,$doneNumbers,$leftNumbers];
	}

	public function getWhscanRTSUnknownProvide($op = false)
	{
		if($op)
		{
			$leftNumbers = ShipmentRtsRecord::model()->count("type = :type and adjust_shipment_id=0 and status<:status",[":type"=>ShipmentRtsRecord::UNKNOWN,":status"=>ShipmentRtsRecord::WAITING_UNKNOWN_DISCARD]);
			$doneNumbers = ShipmentRtsRecord::model()->count("adjust_shipment_id!=0 and to_days(unknown_edit_time) =to_days(NOW())",[":type"=>ShipmentRtsRecord::UNKNOWN]);
			return [date("Y-m-d H:i:s"),$leftNumbers+$doneNumbers,$doneNumbers,$leftNumbers];
		}else
		{
			$leftNumbers = ShipmentRtsRecord::model()->count(" warehouse_id = :warehouseId and type = :type and adjust_shipment_id!=0",[":warehouseId"=>$this->getDptId(),":type"=>ShipmentRtsRecord::UNKNOWN]);
			$doneNumbers = ShipmentRtsRecord::model()->count(" warehouse_id = :warehouseId and type = :type and adjust_shipment_id!=0 and shipment_id!=0 and is_print=1",[":warehouseId"=>$this->getDptId(),":type"=>ShipmentRtsRecord::KNOWN]);
			return [date("Y-m-d H:i:s"),$leftNumbers+$doneNumbers,$doneNumbers,$leftNumbers];
		}
	}



}
?>