<?php

class CartageController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';
	protected $nonAjax = ['viewDetail', 'update'];

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow all users to perform 'index' and 'view' actions
				'actions' => array('index', 'view'),
				'users' => array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions' => array('create', 'update'),
				'users' => array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions' => array('admin', 'delete'),
				'users' => array('admin'),
			),
			array('deny', // deny all users
				'users' => array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view', array(
			'model' => $this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new Cartage;
		if (isset($_POST['Cartage'])) {
			$model->attributes = $_POST['Cartage'];
			$model->op_id = Yii::app()->user->id;
			$model->mdata['notes']['op'] = empty($_POST['cartage_note']) ? '' : $_POST['cartage_note'];
			$user = User::model()->find('id=:id', [':id' => Yii::app()->user->id]);
			$model->mdata['log']['create'] = $user->id . '+' . $user->fname . ' ' . $user->lname . ' create task at ' . date('Y-m-d h:i:s');
			$model->status = 10; //new
			$model->save();
			$this->ajaxResult($model);
		}
		if (!empty($_GET['type'])) {
			$org = Org::model()->findByPk(106);
			if ($_GET['type'] == 'pick_terminal') {
				$model->to_org = $org;
				$model->to_id = $org->id;
				$model->to_addr = $org->address;
				$model->to_contact = $org->phone;
				$model->type = 40;
			} else if ($_GET['type'] == 'pick_customer') {
				$model->to_org = $org;
				$model->to_id = $org->id;
				$model->to_addr = $org->address;
				$model->to_contact = $org->phone;
				$model->type = 10;
			} else if ($_GET['type'] == 'send_terminal') {
				$model->from_org = $org;
				$model->from_id = $org->id;
				$model->from_addr = $org->getAddress(true, false);
				$model->to_contact = $org->phone;
				if (!empty($_GET['jobid'])) {
					$job = EdiJob::model()->findByPk($_GET['jobid']);
					$awb = EdiAwbConsol::model()->find('awb = :awb', [':awb' => $job->awb]);
					if (!empty($awb)) {
						$route = Route::model()->find('flight_no LIKE :flight_no AND code = :code', [':flight_no' => '% ' . $awb->flight . ' %', ':code' => substr($awb->pod, 2)]);
						if (!empty($route)) {
							$flights = explode(' ', $route->flight_no);
							foreach ($flights as $k => $flight) {
								if (empty($flight)) {
									unset($flights[$k]);
								}
							}
							$flights = array_values($flights);
							foreach ($flights as $k => $flight) {
								if ($flight == $awb->flight) {
									$route->deptime = @explode(' ', $route->mdata['deptimes'])[$k];
									break;
								}
							}
						}
						$airline = Airline::model()->find('code = :code', [':code' => $awb->airline]);
					}
					$model->job = $job;
					$model->ref = $model->getWmsTaskRef();
					$model->plt = $model->getWmsTaskPlt();
					$model->job_id = $job->id;
					$model->scd_time = @$awb->etd . ' ' . @$route->deptime;
					$model->to_id = @$airline->tid;
					if (!empty($airline->tid)) {
						$model->to_addr = $airline->terminal->getAddress(true, false);
						$model->to_contact = $airline->terminal->phone;
					}
				}
				$model->type = 30;
			} else if ($_GET['type'] == 'send_customer') {
				$model->from_org = $org;
				$model->from_id = $org->id;
				$model->from_addr = $org->address;
				$model->to_contact = $org->phone;
				$model->type = 20;
			}
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
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		if (isset($_POST['Cartage'])) {
			$model->attributes = $_POST['Cartage'];
			$model->op_id = Yii::app()->user->id;
			$model->mdata['notes']['op'] = empty($_POST['cartage_note']) ? '' : $_POST['cartage_note'];
			$user = User::model()->find('id=:id', [':id' => Yii::app()->user->id]);
			$model->mdata['log']['update'] = $user->id . '+' . $user->fname . ' ' . $user->lname . ' update task at ' . date('Y-m-d h:i:s');
			$model->status = 10; //new
			$model->save();
			$this->ajaxResult($model);
		}

		if (isset($_GET['tab'])) {
			Acl::hasAccess($this->CaName . '/' . $_GET['tab'], true);
			$this->render('tab_' . $_GET['tab'], array('model' => $model, 'actab' => @$_GET['actab']));
		} else {
			$this->render('update', array('model' => $model));
		}
	}

	public function actionAdmin()
	{
		$model = new Cartage('search');
		$model->unsetAttributes(); // clear any default values
		$model->status = 10;
		if (isset($_GET['Cartage'])) {
			$model->attributes = $_GET['Cartage'];
		}

		$this->render('admin', array(
			'model' => $model,
		));
	}

	public function actionArrange()
	{
		if (empty($_SESSION['cartage-arrange-status'])) {
			$_SESSION['cartage-arrange-status'] = 10;
		}
		if (!empty($_GET['Cartage']['status'])) {
			$_SESSION['cartage-arrange-status'] = $_GET['Cartage']['status'];
		}
		$model = new Cartage('search');
		$model->unsetAttributes();
		if (isset($_GET['Cartage'])) {
			$model->attributes = $_GET['Cartage'];
		}
		if ($_SESSION['cartage-arrange-status'] == 10) {
			$model->status = [10,65];
		} else if ($_SESSION['cartage-arrange-status'] == 110) {
			$model->status = [10,65,70];
		} else {
			$model->status = $_SESSION['cartage-arrange-status'];
		}
		$this->render('arrange', array(
			'model' => $model,
		));
	}

	public function actionComplete($id)
	{
		$model = $this->loadModel($id);
		if ($model->ifXray() && $model->status < 65) {
			$model->status = 65;
		} else {
			$model->status = 70;
		}
		$model->update('status');
		$this->ajaxResult($model);
	}

	public function actionRetrieve($id)
	{
		$model = $this->loadModel($id);
		$model->status = 10;
		$model->update('status');
		$this->ajaxResult($model);
	}

	public function actionPrint($id)
	{
		$model = $this->loadModel($id);
		if (empty($_POST)) {
			$_GET['actab'] = 1;
			$this->render('update', array('model' => $model));
		} else {
			$model->mdata['printed'] = true;
			$model->update('meta');
			$this->ajaxResult($model);
		}
	}

	public function actionCancel($id)
	{
		$model = $this->loadModel($id);
		if (Acl::hasAccess('B:cartage/cancel')) {
			$model->status = 100;
			$model->update('status');
		}
		$this->ajaxResult($model);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Cartage the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = Cartage::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Cartage $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'cartage-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	public function actionViewDetail()
	{
		echo '<h4>Task ' . $_GET['id'] . ' Details:</h4>';
		if (!empty($_GET['id'])) {
			$r = Cartage::model()->find('id=:id', array(':id' => $_GET['id']));
			echo '<b>Start From:</b> ' . (empty($r->from_org->name) ? '' : $r->from_org->name) . '&nbsp <b>Contact Detail:</b> ' . $r->from_contact . '<br>';
			echo '<b>Delivery To:</b> ' . (empty($r->to_org->name) ? '' : $r->to_org->name) . '&nbsp <b>Contact Detail:</b>' . $r->to_contact . '<br><hr>';
			echo '<h4>Goods Information:</h4>';
			echo '<b>Pallet Number:</b>' . $r->plt . '&nbsp &nbsp &nbsp <b>Cubic Meters(M<sup>3</sup>): </b>' . $r->cbm . '&nbsp &nbsp &nbsp <b>Weight(Kg): </b>' . $r->kg . '<br><hr>';
			echo '<b>Assign To:</b>' . (empty($r->org->name) ? '' : $r->org->name) . '<br>';
			if (!empty($r->mdata) && is_array($r->mdata)) {
				foreach ($r->mdata as $key => $value) {
					if ($key == 'notes') {
						echo '<b>op Notes:</b>';
						if (is_array($value)) {
							echo (empty($value['op']) ? '' : $value['op']) . '<br>';
						}
						echo '<b>Driver Notes:</b>';
						if (is_array($value)) {
						}
					}echo (empty($value['driver']) ? '' : $value['driver']) . '<br>';

				}
			}
		}
	}

}
