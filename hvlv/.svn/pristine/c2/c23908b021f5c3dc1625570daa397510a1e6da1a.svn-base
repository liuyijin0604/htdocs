<?php

class ReturnController extends Controller
{

	public function actionList()
	{
		$job = WmsJob::model()->find('org_id = :oid and status = 10 and week(`created`,1) = :week', array(':oid' => Yii::app()->user->org, ':week' => intval(date('W'))));
		if (empty($job)) {
			$job = new WmsJob();
			$job->org_id = Yii::app()->user->org;
			$job->status = 10;
			$job->type = 90;
			$job->created = date('Y-m-d');
			$job->ref = '3PL_' . date('Y-m-d');
			$job->save();
		}

		$this->render('list', array('job' => $job));
	}

	public function actionCreate($jid)
	{
		if (!is_numeric($jid)) {
			$jid = WmsJob::model()->find(['condition' => 't.org_id = :org_id', 'params' => [':org_id' => Yii::app()->user->org], 'order' => 't.id DESC'])->id;
		}

		$job = WmsJob::model()->findByPk($jid);
		$model = new WmsTask;
		$model->job_id = $jid;
		$model->type = 7010;
		$model->is_request = 1;
		$model->status = 10;

		if (empty($_POST)) {
			$this->render('create', ['model' => $model]);
		} else {
			if (empty($_POST['item'])) {
				$model->addError('id', 'Product Information is empty');
				$this->ajaxResult($model);
			}

			$count = 0;
			foreach ($_POST['item'] as $item) {
				if (empty($item['sn']) && empty($item['sku']) && empty($item['uq'])) continue;

				if (empty($item['sn']) && empty($item['sku'])) {
					$model->addError('id', 'Product Name & Barcode is empty');
					$this->ajaxResult($model);
				}

				if (empty($item['uq'])) {
					$model->addError('id', 'Qty is empty');
					$this->ajaxResult($model);
				}

				$count++;
			}
			if ($count == 0) {
				$model->addError('id', 'Product Information is empty');
				$this->ajaxResult($model);
			}

			$model->attributes = $_POST['WmsTask'];
			$model->mdata['weight'] = $_POST['mdata']['weight'];
			if ($model->save()) {
				foreach ($_POST['item'] as $item) {
					$wti = new WmsTaskItem;
					$wti->task_id = $model->id;
					$wti->mdata = $item;
					$wti->save();
				}

				if (!empty($_POST['mdata']['cnee'])) {
					if (!Postcode::validateAddress($_POST['mdata']['cnee']['suburb'], $_POST['mdata']['cnee']['state'], $_POST['mdata']['cnee']['postcode'])) {
						$model->addError('id', 'Address is incorrect');
						$this->ajaxResult($model);
					}

					$delivery = $model->deliveryTask;
					$delivery->mdata['cnee'] = $_POST['mdata']['cnee'];
					$delivery->save();

					if ($model->mdata['return_option'] == 2) {
						$model->generateRedeliveryTask($_POST['mdata']['cnee']);
					}
				}
			}

			$this->ajaxResult($model, ['id']);
		}
	}

	public function actionUpdate($id)
	{
		if (!is_numeric($id)) {
			return;
		}

		$model = $this->loadModel($id);

		if ($model->getReturnType() == 'Customer Return') {
			$this->actionUpdateCustomerReturn($id);
		} else if ($model->getReturnType() == 'Courier RTS') {
			$this->actionUpdateCourierRTS($id);
		}
	}

	public function actionUpdateCustomerReturn($id)
	{
		$model = $this->loadModel($id);

		if (empty($_POST)) {
			$this->render('update_customer_return', ['model' => $model]);
		} else {
			if (empty($_POST['item'])) {
				$model->addError('id', 'Product Information is empty');
				$this->ajaxResult($model);
			}

			$count = 0;
			foreach ($_POST['item'] as $item) {
				if (empty($item['sn']) && empty($item['sku']) && empty($item['uq'])) continue;

				if (empty($item['sn']) && empty($item['sku'])) {
					$model->addError('id', 'Product Name & Barcode is empty');
					$this->ajaxResult($model);
				}

				if (empty($item['uq'])) {
					$model->addError('id', 'Qty is empty');
					$this->ajaxResult($model);
				}

				$count++;
			}
			if ($count == 0) {
				$model->addError('id', 'Product Information is empty');
				$this->ajaxResult($model);
			}

			$model->attributes = $_POST['WmsTask'];
			$model->mdata['weight'] = $_POST['mdata']['weight'];
			if ($model->save()) {
				foreach ($model->items as $item) {
					$item->del = 1;
					$item->save();
				}
				foreach ($_POST['item'] as $item) {
					$wti = new WmsTaskItem;
					$wti->task_id = $model->id;
					$wti->mdata = $item;
					$wti->save();
				}

				if (!empty($_POST['mdata']['cnee'])) {
					if (!Postcode::validateAddress($_POST['mdata']['cnee']['suburb'], $_POST['mdata']['cnee']['state'], $_POST['mdata']['cnee']['postcode'])) {
						$model->addError('id', 'Address is incorrect');
						$this->ajaxResult($model);
					}

					$delivery = $model->deliveryTask;
					$delivery->mdata['cnee'] = $_POST['mdata']['cnee'];
					$delivery->save();

					if ($model->mdata['return_option'] == 2) {
						$model->generateRedeliveryTask($_POST['mdata']['cnee']);
					}
				}
			}

			$this->ajaxResult($model, ['id']);
		}
	}

	public function actionUpdateCourierRTS($id)
	{
		$model = $this->loadModel($id);

		if (empty($_POST)) {
			$this->render('update_courier_rts', ['model' => $model]);
		} else {
			if (!empty($_POST['WmsTask']['mdata']['return_option'])) {
				$model->mdata['return_option'] = $_POST['WmsTask']['mdata']['return_option'];
				$model->update('meta');

				if ($model->mdata['return_option'] == 2) {
					$model->generateRedeliveryTask($_POST['mdata']['cnee']);
				}
			}

			$this->ajaxResult($model);
		}
	}

	public function actionLabel($id)
	{
		$model = $this->loadModel($id);

		if (!Postcode::validateAddress($model->deliveryTask->mdata['cnee']['suburb'], $model->deliveryTask->mdata['cnee']['state'], $model->deliveryTask->mdata['cnee']['postcode'])) {
			echo 'Address is incorrect';
		} else {
			if (empty($model->deliveryTask->mdata['return_shipment_id'])) {
				$model->deliveryTask->createReturnLabel(explode(',', $model->mdata['weight']));
			}

			if (!empty($model->deliveryTask->mdata['return_shipment_id'])) {
				$rs = [];
				foreach ($model->deliveryTask->mdata['return_shipment_id'] as $rsi) {
					$r = Shipment::model()->findByPk($rsi);
					$rs[] = $r;
				}
				oPDF::renderPDF('label_A6', array('rs' => $rs), 1, 'label.pdf');
			} else {
				echo 'Return Label currently not available, coming soon';
			}
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = WmsTask::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

}
