<?php

class PaymentBillingController extends Controller
{

	protected $nonAjax = [];
	protected $skipAcl = [];

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id),
		));
	}

	/**
	 * show invoice related logs
	 * @param $id
	 * @throws CHttpException
	 */
	public function actionLog() {
		$model = $this->loadModel($_GET['fid']);
		$this->render('log', array(
			'model' => $model,
		));
	}

	public function actionNotes($id) {
		$model = $this->loadModel($id);
		Log::add($model, Log::LOG_TYPE_NOTES, ['notes' => $_POST['notes']]);
		$this->ajaxResult($model);
	}


	/**
	 *  pull all payments from xero to HVLV
	 */
	public function actionSyncxero() {
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate() {
		$model = new PaymentBilling;

		if (isset($_POST['PaymentBilling'])) {
			$model->attributes = $_POST['PaymentBilling'];
			$model->transaction_date = date('Y-m-d');
			$model->status = PaymentBilling::PAYMENT_STATUS_PENDING;
			if (!empty($model->org_id)) {
				$org = Org::model()->findByPk($model->org_id);
				$model->mdata['name'] = $org->name;
				$model->mdata['address'] = $org->getAddress();
			}
			$model->mdata['diff'] = $_POST['diff'];
			$model->save();

			if (!empty($_POST['alloc'])) {
				foreach ($_POST['alloc'] as $i => $t) {
					if (empty($t)) continue;
					$pb = new PayBill;
					$pb->bill_id = $i;
					$pb->pay_id = $model->id;
					$pb->amount = $t;
					$pb->save();
				}
			}

			if (!empty($_POST['billing_diff'])) {
				foreach ($_POST['billing_diff'] as $i => $t) {
					if (empty($t)) continue;
					$billing = Billing::model()->findByPk($i);
					$billing->mdata['diff'] = $_POST['diff'] / count($_POST['billing_diff']);
					$billing->save();
				}
			}

			$model->updateAta();
			$this->ajaxResult($model);
		}

		$model->bank = 'WestPac AUD';

		if ( !empty($_GET['bs_date']) && !empty($_GET['bs_amount']) ) {
			$model->date = $_GET['bs_date'];
			$model->amount = $_GET['bs_amount'];
			$model->type = 1;
		}

		$this->render('create', array(
			'model' => $model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id) {
		$model = $this->loadModel($id);

		if (isset($_POST['PaymentBilling'])) {
			$model->attributes = $_POST['PaymentBilling'];
			$model->save();

			if (!empty($_POST['alloc'])) {
				foreach ($_POST['alloc'] as $i => $t) {
					if (empty($t)) continue;

					$billing = Billing::model()->findByPk($i);
					$balance = $billing->getBalance();
					if ($t > $balance) $t = $balance;

					$pb = new PayBill;
					$pb->bill_id = $i;
					$pb->pay_id = $model->id;
					$pb->amount = $t;
					$pb->save();
				}
				$model->updateAta();
			}
			$this->ajaxResult($model);
		}

		$this->render('update', array(
			'model' => $model,
		));
	}

	public function actionBtn($id) {
		$model = $this->loadModel($id);

		$this->ajaxResult($model);
	}

	/**
	 * Lists and search.
	 */
	public function actionList() {
		$model = new PaymentBilling('search');
		$model->unsetAttributes();  // clear any default values
		unset($_GET['PaymentBilling']['gst']);
		if (isset($_GET['PaymentBilling']))
			$model->attributes = $_GET['PaymentBilling'];

		$this->render('list', array(
			'model' => $model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id) {
		$model = PaymentBilling::model()->findByPk($id);
		if ($model === null)
			throw new CHttpException(404, 'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model) {
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'payment-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

}