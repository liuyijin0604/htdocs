<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AccountsController extends Controller
{
	public function actionIndex()
	{
		$this->render('list');
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		if (Yii::app()->user->grp >= 50 && !in_array($model->id, User::getOrgIds())) {
			Acl::denied403();
		}
		$own = $model->id == Yii::app()->user->org;
		$user = User::model()->findByPk(Yii::app()->user->id);

		$_GET['tabid'] = 12321112;
		if (isset($_POST['Org']) && isset($_POST['User'])) {
			if ($own) {
				unset($_POST['Org']['status']);
				unset($_POST['Org']['type']);
			}
			$model->attributes = $_POST['Org'];
			$model->sync_xero = false;
			$model->save();
			if (empty($_POST['User']['password'])) {
				unset($_POST['User']['password']);
			} else {
				$_POST['User']['password'] = md5($_POST['User']['password']);
			}
			$user->attributes = $_POST['User'];
			if ($user->isNewRecord) {
				$user->type = 70;
				$user->by_id = Yii::app()->user->org;
				$user->org_id = $id;
			}
			$user->save();
			$errors = $user->getErrors();
			$msg = '';
			foreach ($errors as $e) {
				$msg .= implode(':', $e);
			}
			if (!empty($msg)) {
				$model->addError('User update Failed', $msg);
			}
			$this->ajaxResult($model);
		}

		$this->render('update', ['model' => $model, 'own' => $own, 'user' => $user]);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new Org;
		$user = new User;
		$_GET['tabid'] = 12321113;
		if (isset($_POST['Org'])) {
			$model->attributes = $_POST['Org'];
			$model->type = 30;
			$model->by = Yii::app()->user->org;
			$model->status = 1;
			$model->extra = empty($_POST['extra']) ? [] : $_POST['extra'];
			$model->sync_xero = false;
			$model->save();
			$this->ajaxResult($model, ['id', 'code']);
		} else {
			// set default value for Client or Agent organization
			$model->by = Yii::app()->user->org;
			$model->extra['wthreshold'] = 5;
			$model->extra['credit_init_date'] = date('Y-m-d');
			$model->extra['creditlimit'] = 10000;
			$model->extra['creditterms'] = 14; // credit
		}
		$model->status = 1;
		$model->country = 'Australia';
		$this->render('create', [
			'model' => $model,
			'own' => false,
			'user' => $user,
		]);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = Org::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	public function actionOwnerSuggest()
	{
		$org_id = Yii::app()->user->org;
		$a = [];
		if ($org_id > 0) {
			$users = User::model()->findAll('org_id=:org_id OR by_id=:org_id', [':org_id' => $org_id]);
			foreach ($users as $u) {
				$rs = Org::model()->findAll([
					'condition' => 'status=1 AND (name LIKE :n OR CODE LIKE :n) AND id=:oid',
					'params' => [':n' => '%' . $_GET['term'] . '%', ':oid' => $u->org_id],
					'limit' => 10,
				]);
				foreach ($rs as $r) {
					$a[] = [
						'value' => $r->id,
						'label' => $r->code . ':' . $r->name,
					];
				}
			}
		}
		echo json_encode($a);
	}

	public function actionInvoice()
	{
		$this->render('invoice_list');
	}

	public function actionInvView()
	{
		$this->forward('/invoice/print');
	}

	public function actionInvDetail()
	{
		$this->forward('/invoice/detail');
	}
}
