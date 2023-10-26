<?php

class ParcelStatusController extends PController
{
	public function actionIndex()
	{
		$shipment=ImParcel::model()->find(['condition'=>'(hbn=:hbn and ref=:ref) and status !=100 and consol_id!=0', 'params'=>[':hbn'=>$_GET['hbn'],':ref'=>$_GET['ref']],'order'=>'t.status desc']);
		if (empty($_GET['r'])||!in_array($_GET['r'], ImParcel::$custTypes)) {
			throw new CHttpException(400, "Method not found!");
		}
		if (!empty($shipment)) {
			$hash=isset($_GET['h'])?$_GET['h']:'';
			if (HashVerify::verify($hash, $shipment->id)) {
				//render the upload view
				$_GET['tabid']=11112222;
				//
				$this->render('parcel_status', ['model'=>$shipment]);
			} else {
				throw new CHttpException(400, "Visit Limit!");
			}
		} else {
			throw new CHttpException(400, "Parcel Not Found!");
		}
	}

	public function actionConfirmAqis()
	{
		$shipment=ImParcel::model()->find(['condition'=>'(hbn=:hbn and ref=:ref) and status !=100 and consol_id!=0', 'params'=>[':hbn'=>$_GET['hbn'],':ref'=>$_GET['ref']],'order'=>'t.status desc']);
		if (empty($shipment)) {
			throw new CHttpException(400, "Parcel not found!");
		}
		if ($_POST['action']=="confirm inspection"||$_POST['action']=="confirm disposal") {
			$hash=isset($_GET['h'])?$_GET['h']:'';
			if (HashVerify::verify($hash, $shipment->id)) {
				if($_POST['action']=="confirm inspection")
				{
					$shipment->process->status = ShipmentProcess::STATE_CONFIRM_INS;
					$shipment->process->mdata['customer_confirm'] = "Inspection";
					$shipment->process->mdata['customer_confirm_date'] = date("Y-m-d H:i:s");
					$shipment->process->custom_log_note="Customer Confirm AQIS Status:Inspection";
					$shipment->process->update(['status','meta']);
					$shipment->bwf  = $shipment->bwf|ImParcel::INSCODE;
					$shipment->update('bwf');
				}else
				{
					$shipment->process->status = ShipmentProcess::STATE_CONFIRM_DIS;
					$shipment->process->mdata['customer_confirm'] = "Disposal";
					$shipment->process->mdata['customer_confirm_date'] = date("Y-m-d H:i:s");
					$shipment->process->custom_log_note="Customer Confirm AQIS Status:Disposal";
					$shipment->process->update(['status','meta']);
					$shipment->bwf  = $shipment->bwf|ImParcel::DISCODE;
					$shipment->update('bwf');
				}
				echo '{"done":true}';
			} else {
				throw new CHttpException(400, "Visit Limit!");
			}
		} else {
			throw new CHttpException(400, "Parcel Not Found!");
		}
	}
		
	/*
	 * change the shipmentProcess status
	 */
	public function actionProcess()
	{
		if (empty($_GET['id'])||empty($_GET['status'])) {
			return false;
		} else {
			$process= ShipmentProcess::model()->find('pid=:pid', [':pid'=>$_GET['id']]);
			if (empty($process)) {
				return false;
			}
			$process->changeStatus($_GET['status']);
			if ($_GET['status']== ShipmentProcess::ENTRY_CONFIRM) {
				$shipment= ImParcel::model()->findByPk($process->pid);
				$shipment->mdata['event_msg']="<b>Entry Confirmed at ".date("Y-m-d H:i:s")." !</b>";
				$shipment->updateMeta();
				ShipmentProcess::sendOpNotice($shipment);
			}
			echo 'done';
			return true;
		}
	}
		
	public function actionDeleteFile($id)
	{
		$fr = FileRepo::model()->findByPk($id);
		$fr->status = 0;
		$fr->save();
		echo 'done';
		return true;
	}
		
	public function actionLoa($id)
	{
		$model= ImParcel::model()->findByPk($id);
		$process= ShipmentProcess::model()->find('pid=:pid', [':pid'=>$id]);
		if (empty($process)) {
			return false;
		}
		foreach ($_POST as $k=>$v) {
			$process->mdata[$k]=$v;
		}
		$ip=$this->getRealIpAddr();
		if (!empty($ip)) {
			$process->mdata['client_ip']=$ip;
		}
		$process->mdata['sig_date']=date('Y-m-d');
		$process->updateMeta();
					 
		$tempDirectory = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		if (!file_exists($tempDirectory)) {
			mkdir($tempDirectory);
		}
		$fileName=$tempDirectory.DIRECTORY_SEPARATOR.'loa_'.$model->hbn.'.pdf';
		oPDF::renderPDF('loa_sig_v2', ['model' => $process], 2, $fileName);
		FileRepo::storeFile($fileName, basename($fileName), 18, $model->id);
		AppHelper::unlinkRecursive($tempDirectory);

		echo 'done';
		return true;
	}
	public function getRealIpAddr()
	{
		$ip='';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	//           public function actionTestLabel($id){
	//                 $process= ShipmentProcess::model()->find('pid=:pid',array(':pid'=>$id));
//
	//                 oPDF::renderPDF('loa_sig_v2',array('model' => $process), 1, $process->pid);
	//           }
			 
	public function actionGen()
	{
		$model= $process= ShipmentProcess::model()->find('pid=3286432');
		oPDF::renderPDF('loa_sig', ['model'=>$model], 1, 'loa.pdf');
	}
			 
	public function actionPrint($id)
	{
		if($id >= 500000){
			Yii::app()->name = 'TLA';
		}
		$model= Invoice::model()->findByPk($id);
		if (empty($model)) {
			throw new CHttpException(400, "Invoice Not Found!");
		}
		if ($model->pid!=$_GET['pid']) {
			throw new CHttpException(400, "Invoice Not Found!");
		}
		oPDF::renderPDF('invoice', ['inv'=>$model], 1, 'Invoice_'.$model->no.'.pdf');
	}
}
