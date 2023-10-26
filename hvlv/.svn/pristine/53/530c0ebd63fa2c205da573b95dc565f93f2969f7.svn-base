<?php

class InterstateChargeRateController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new InterstateChargeRate;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['InterstateChargeRate']))
		{
			$model->attributes=$_POST['InterstateChargeRate'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['InterstateChargeRate']))
		{
			$model->attributes=$_POST['InterstateChargeRate'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('InterstateChargeRate');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new InterstateChargeRate('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['InterstateChargeRate']))
			$model->attributes=$_GET['InterstateChargeRate'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return InterstateChargeRate the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=InterstateChargeRate::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param InterstateChargeRate $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='interstate-charge-rate-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionImport()
	{
		if (!empty($_POST)) {
			$res = new stdClass;
			$res->success = 1;
			$res->errorMessage = "";
			$orgId = !empty($_POST['org_id']) ? $_POST['org_id'] : 0;
			if (!empty($_FILES['import_file'])) {
				$template_file = $_FILES['import_file'];
				$xls = new oExcel;
				$xls->load($template_file['tmp_name']);
				$data = $xls->getAll();
				$dataCount = count($data);
				for ($i = 2; $i <= $dataCount; $i++) {
					$departure = !empty($data[$i][1]) ? $data[$i][1] : 0;
					$destination = !empty($data[$i][2]) ? $data[$i][2] : 0;
					$weightLow = !empty($data[$i][3]) ? $data[$i][3] : 0;
					$weightHigh = !empty($data[$i][4]) ? $data[$i][4] : 0;
					$base = !empty($data[$i][5]) ? $data[$i][5] : 0;
					$item = !empty($data[$i][6]) ? $data[$i][6] : 0;
					$perKg = !empty($data[$i][7]) ? $data[$i][7] : 0;
					$perPallet = !empty($data[$i][8]) ? $data[$i][8] : 0;
					$minimum = !empty($data[$i][9]) ? $data[$i][9] : 0;
					$gst = !empty($data[$i][10]) ? 1 : 0;
					$chargeType = !empty($perKg) ? InterstateChargeRate::CHARGE_BY_WEIGHT : InterstateChargeRate::CHARGE_BY_PALLET;

					if (empty($departure) || empty($destination)) {
						$res->errorMessage .= "<p><span style=\"color:red;\">Error on line " . $i . "</span>: Departure and Destination cannot be empty</p>";
						continue;
					}
					$interstateRate = InterstateChargeRate::model()->findByAttributes(array('departure_depot' => $departure, 'destination_depot' => $destination, 'org_id' => $orgId, 'weight_low' => $weightLow, 'weight_high' => $weightHigh, 'type' => $chargeType));
					if (empty($interstateRate)) {	// create a new rate
						$interstateRate = new InterstateChargeRate();
						$interstateRate->departure_depot = $departure;
						$interstateRate->destination_depot = $destination;
						$interstateRate->org_id = $orgId;
						$interstateRate->weight_low = $weightLow;
						$interstateRate->weight_high = $weightHigh;
						$interstateRate->base = $base;
						$interstateRate->item = $item;
						$interstateRate->perkg = $perKg;
						$interstateRate->per_pallet = $perPallet;
						$interstateRate->minimum = $minimum;
						$interstateRate->type = $chargeType;
						$interstateRate->start_date = date('Y-m-d H:i:s');
						$interstateRate->gst = $gst;
						$interstateRate->save();
					} else {	// update existing rate
						$interstateRate->base = $base;
						$interstateRate->item = $item;
						$interstateRate->perkg = $perKg;
						$interstateRate->per_pallet = $perPallet;
						$interstateRate->minimum = $minimum;
						$interstateRate->start_date = date('Y-m-d H:i:s');
						$interstateRate->gst = $gst;
						$interstateRate->save();
					}
					
				}
			}
			echo json_encode($res);
		} else {
			$this->render('import');
		}
	}
}
