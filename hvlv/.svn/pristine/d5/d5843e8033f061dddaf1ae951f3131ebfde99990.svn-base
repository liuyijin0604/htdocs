<?php
class ReturnController extends Controller
{

	public function actionCheck()
	{
		if (empty($_POST)) {
			$this->render('check');
		} else {
			$shipment = ShipmentScan::getShipmentByBarcode($_POST['barcode']);
			if (empty($shipment)) {
				echo json_encode(['done' => false, 'msg' => 'Not found', 'sound' => 'not_found']);
				Yii::app()->end();
			}

			$task = WmsTask::model()->findByPk(ltrim($shipment->cref, 'T'));
			if (empty($task)) {
				echo json_encode(['done' => false, 'msg' => 'Not found', 'sound' => 'not_found']);
				Yii::app()->end();
			}

			if ($task->getReturnType() == 'Courier RTS') {
				$sound = 'return_restock';
			} else if ($task->getReturnType() == 'Customer Return') {
				if (empty($task->mdata['return_option']) || $task->mdata['return_option'] == WmsTask::CUSTOMER_RETURN_CHECK) {
					$sound = 'return_check';
				} else if ($task->mdata['return_option'] == WmsTask::CUSTOMER_RETURN_RESTOCK) {
					$sound = 'return_restock';
				} else if ($task->mdata['return_option'] == WmsTask::CUSTOMER_RETURN_REDELIVERY) {
					$sound = 'return_restock';
				} else if ($task->mdata['return_option'] == WmsTask::CUSTOMER_RETURN_REPAIR) {
					$sound = 'return_repair';
				} else if ($task->mdata['return_option'] == WmsTask::CUSTOMER_RETURN_DISCARD) {
					$sound = 'return_discard';
				} else {
					$sound = 'not_found';
				}
			}

			if ($sound == 'return_restock') {
				$task->generateRestockTask();
			}

			echo json_encode(['done' => true, 'msg' => 'Successfully', 'sound' => $sound]);
			Yii::app()->end();
		}
	}

}