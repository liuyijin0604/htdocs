<?php

class InvoiceTemplateController extends Controller {

	protected $nonAjax = [];

	public function actionCreate()
	{
		if (empty($_POST)) {
			$model = new InvoiceTemplate('create');
			$model->dpt_id = 106;
			$this->render('create', array('model' => $model));
		} else {
			$model = new InvoiceTemplate;
			$model->attributes = $_POST['InvoiceTemplate'];
			$model->status = 1;

			if (empty($_POST['InvTempLine']['ccode'][0]) || !is_array($_POST['InvTempLine']['ccode'])) {
				$model->addError('id', 'Please input at least one invoice line');
			} else {
				$model->save();
				foreach ($_POST['InvTempLine']['ccode'] as $k => $v) {
					$line = new InvTempLine;
					$line->temp_id = $model->id;
					$line->ccode = $v;
					$line->desc = $_POST['InvTempLine']['desc'][$k];
					$line->rate = $_POST['InvTempLine']['rate'][$k];
					$line->qty = $_POST['InvTempLine']['qty'][$k];
					$line->gst = $_POST['InvTempLine']['gst'][$k];
					$line->save();
				}
			}
			$this->ajaxResult($model);
		}
	}

	public function actionUpdate($id)
	{
		if (empty($_POST)) {
			$model = $this->loadModel($id);
			$this->render('update', array('model' => $model));
		} else {
			$model = $this->loadModel($id);
			$model->attributes = $_POST['InvoiceTemplate'];
			$model->save();

			if (!empty($_POST['InvTempLine']['ccode'][0]) && is_array($_POST['InvTempLine']['ccode'])) {
				foreach ($_POST['InvTempLine']['ccode'] as $k => $v) {
					$line = new InvTempLine;
					$line->temp_id = $model->id;
					$line->ccode = $v;
					$line->desc = $_POST['InvTempLine']['desc'][$k];
					$line->rate = $_POST['InvTempLine']['rate'][$k];
					$line->qty = $_POST['InvTempLine']['qty'][$k];
					$line->gst = $_POST['InvTempLine']['gst'][$k];
					$line->save();
				}
			}
			$this->ajaxResult($model);
		}
	}

	public function actionLog()
	{

	}

	public function actionList()
	{
		$model = new InvoiceTemplate('search');
		$model->unsetAttributes();

		$this->render('list', array(
			'model' => $model,
		));
	}

	public function actionLineGrid()
	{
		if (isset($_POST['InvTempLine'])) {
			if (!empty($_POST['InvTempLine']['id'])) {
				$model = InvTempLine::model()->findByPk($_POST['InvTempLine']['id']);
				unset($_POST['InvTempLine']['id']);
				$model->attributes = $_POST['InvTempLine'];
				$model->save();
			} else {
				$model = new InvTempLine;
				$model->temp_id = $_GET['id'];
				$model->attributes = $_POST['InvTempLine'];
				$model->save();
			}
		} else {
			$model = InvTempLine::model()->findByPk($_GET['id']);
			$model->delete();
		}
		$this->ajaxResult($model);
	}

	public function actionOrgGrid()
	{
		if (isset($_POST['InvTempOrg'])) {
			if (!empty($_POST['InvTempOrg']['id'])) {
				$model = InvTempOrg::model()->findByPk($_POST['InvTempOrg']['id']);
				unset($_POST['InvTempOrg']['id']);
				$model->attributes = $_POST['InvTempOrg'];
				$model->save();
			} else {
				$model = new InvTempOrg;
				$model->temp_id = $_GET['id'];
				$model->attributes = $_POST['InvTempOrg'];
				$model->save();
			}
		} else {
			$model = InvTempOrg::model()->findByPk($_GET['id']);
			$model->delete();
		}
		$this->ajaxResult($model);
	}

	private function loadModel($id)
	{
		$model = InvoiceTemplate::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

}