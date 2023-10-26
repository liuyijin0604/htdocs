<?php
class ApiWmsLinkAction extends CAction
{
	public $ctlr;
	public $debug;
	public $user;


	public function run()
	{
		$this->ctlr = $this->getController();
		$this->debug = !empty($_POST['test']);
		$this->user = empty($this->ctlr->user)? false : User::model()->findByPk($this->ctlr->user);

		if (!empty($_POST['method']) && method_exists($this, $_POST['method'])) {
			if (!in_array($_POST['method'], ['get'])) {
				$this->log(json_encode($_POST));
			}
			$this->{$_POST['method']}();
		} else {
			throw new CHttpException(400, 'API method not found!');
		}
	}

	public function log($l)
	{
		$tmp = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR . 'shipmentapi' . DIRECTORY_SEPARATOR ;
		file_put_contents($tmp.'shipment_api_'  . date('Y-m-d') . '.log', date('Y-m-d H:i:s').' '.$l."\n", FILE_APPEND);
	}
	
	function funcToShipment(){
		$objData = $this->ctlr->data;
		
		$obj = WmsJob::model()->find('no=:no',[':no'=>$objData->job_id]);
		
		$strJobId = $obj->id;
		// $obj = WmsJob::model()->findAllByPk($strJobId);
		
		$objTaskMain = new WmsTask;
		// $objTaskMain->ref= $data[$i][1];
		$objTaskMain->job_id = $strJobId;
		// $objTaskMain->type = WmsTask::TYPE_Split_Delivery_SF;
		$objTaskMain->type = WmsTask::TYPE_PICK_UNIT; //todo
		// $objTaskMain->status = WmsTask::STATUS_NEW;
		$objTaskMain->status = $objData->task_main->status; //todo
		$objTaskMain->is_request = 1;
		// $objTaskMain->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
		$objTaskMain->dpt_id = $objData->task_main->depot_id;
		$objTaskMain->save();

		$objTaskPack = null;
		$objSubTasks = $objTaskMain->subTasks;
		foreach($objSubTasks as $iKey=>$objSubTask){
			if($objSubTask->type == WmsTask::TYPE_PACK_ORDER){
				$objTaskPack = $objSubTask;
			}
		}
		
		$objTaskPack->mdata = json_decode($objData->task_pack->meta,true); 
		// $objTaskPack->ref = $objTaskMain->ref;
		$objTaskPack->save();
		
		
		$objWmsTaskDelivery = WmsTask::model()->find('link_id = :id AND type IN (2120)', [':id' => $objTaskMain ->id]);
		if (empty($objWmsTaskDelivery)) {
			$objWmsTaskDelivery = new WmsTask;
			$objWmsTaskDelivery->type = WmsTask::TYPE_DELIVERY;
			$objWmsTaskDelivery->status = WmsTask::STATUS_NEW;
			$objWmsTaskDelivery->link_id = $objTaskMain ->id;
			$objWmsTaskDelivery->job_id = $objTaskMain->job_id;
			$objWmsTaskDelivery->dpt_id = $objTaskMain->dpt_id;
			//todo
			// $objSubTask->mdata['cnee'] = $objCnee;
			// $objSubTask->mdata['courier'] = Org::ORGID_COURIER_SF;
			// $objSubTask->ref = $objTaskMain->ref;
			$objWmsTaskDelivery->mdata = json_decode($objData->task_delivery->meta,true); 
			unset($objWmsTaskDelivery->mdata['shipment_id']);
			unset($objWmsTaskDelivery->mdata['shipment_courier_id']);
			
			// json_decode($this->meta, true); 
			$objWmsTaskDelivery->save();
		}
		
		
		
		// $pt = WmsTask::model()->find('type = 3210 AND link_id = :t', [':t' => $objTaskMain ->id]);
		$objTaskPack = $objTaskPack;
		$pkg = 0;
		$tw = 0;
		$fw_wt = [];
		$rs = [];
		if (!empty($objTaskPack) && !empty($objTaskPack->mdata['pkg'])) {
			foreach (json_decode($objTaskPack->mdata['pkg'], true) as $pk) {
				if (empty($pk['wt'])) {
					continue;
				}

				$fw_wt[] = $pk['wt']; //store each pack's weight;
				$tw += $pk['wt'];
				$pkg++;
			}
		} else {
			$pkg = 1;
		}
		
							// toShipment
							$listShipment = [];
							if (empty($objWmsTaskDelivery->mdata['shipment_id']) || empty($objWmsTaskDelivery->mdata['shipment_courier_id']) || $objWmsTaskDelivery->mdata['shipment_courier_id'] != $objWmsTaskDelivery->mdata['courier'] ||
							($objWmsTaskDelivery->mdata['courier'] == Org::ORGID_COURIER_FASTWAY && sizeof($objWmsTaskDelivery->mdata['shipment_id']) != $pkg)) {
								$objImParcel = $objWmsTaskDelivery->toShipment()[0];
								$objImParcel->ot_id = WmsTask::OT_ID_3PL;//ot_id = 10
								$objImParcel->save(); 
								$listShipment[] = $objImParcel;
							} else {
								foreach ($objWmsTaskDelivery->mdata['shipment_id'] as $sid) {
									$p = Shipment::model()->find('id=:id', array(':id' => $sid));
									if (!empty($p)) {
										$listShipment[] = $p;
									}
			
								}
							}
					
							
					$o=['status' => 1,
					'task_main_id'=>$objTaskMain->id,
					'shipment_id' => $listShipment[0]->id,
					'shipment_number'=>$listShipment[0]->ref,
					// 'listShipment' =>$listShipment,
					 'msg' => 'successed'];
	
					echo json_encode($o);
		
		
		
	}
	
	
	function funcSyncStatus(){
		
		// $objData = $this->ctlr->data;
		// $listId2Status = $objData->listId2Status;

		$objData = json_decode($_POST['data'],true);
		$listId2Status = $objData['listId2Status'];
		
		$listId = [];
		foreach($listId2Status as $id=>$status){
			$listId[]=$id;
		}
		
		
		$strWhere = 'id in (';
		foreach($listId as $i=>$strId){
			$strWhere.= $strId;
			if($i != sizeof($listId)-1){
				$strWhere.=',';
			}
		}
		$strWhere.=')';
		
		
		// $listTaskMain = WmsTask::model()->findAll('id in :ids',[':ids'=>$listId]);
		$listTaskMain = WmsTask::model()->findAll($strWhere);
		
		foreach($listTaskMain as $i=>$objTaskMain){
			// Yii::app()->user->grp = 0;
			if($objTaskMain->status == WmsTask::STATUS_COMPLETED&&$listId2Status[$objTaskMain->id] == WmsTask::STATUS_CANCELLED){
				$objTaskMain->status = WmsTask::STATUS_NEW;
				$objTaskMain->save();
			}
			else{
				$objTaskMain->status = $listId2Status[$objTaskMain->id];
				$objTaskMain->save();
				$status = $objTaskMain->status;
			}
		}
		
		$o=['status' => 1,
		 'msg' => 'successed'];

		echo json_encode($o);
		
	}
	
	
	function funcSyncManifest(){
		$objData = json_decode($_POST['data'],true);
		$listShipmentId = $objData['listShipmentId'];
		
		
		$strWhere = 'id in (';
		foreach($listShipmentId as $i=>$strId){
			$strWhere.= $strId;
			if($i != sizeof($listShipmentId)-1){
				$strWhere.=',';
			}
		}
		$strWhere.=')';
		
		$listShipment = ImParcel::model()->findAll($strWhere);
		$listShipmentId2IsManifest = [];
		foreach($listShipment as $i=>$objShipment){
			$strStatus = $objShipment->getStatus();
			if(strpos($strStatus,'[M]')){
				$listShipmentId2IsManifest[$objShipment->id]= 1;
			}
			else{
				$listShipmentId2IsManifest[$objShipment->id]= 0;
			}
		}
		
		$o=[
			'status' => 1,
			'listShipmentId2IsManifest' => $listShipmentId2IsManifest,
			'msg' => 'successed'];

		echo json_encode($o);
		
	}
	
	
	

}




