<?php

class PickupBookingSlotController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';

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
		$model=new PickupBookingSlot;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['PickupBookingSlot']))
		{
			$model->attributes=$_POST['PickupBookingSlot'];
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

		if(isset($_POST['PickupBookingSlot']))
		{
			$model->attributes=$_POST['PickupBookingSlot'];
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
		$dataProvider=new CActiveDataProvider('PickupBookingSlot');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new PickupBookingSlot('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PickupBookingSlot']))
			$model->attributes=$_GET['PickupBookingSlot'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Slot Management
	 */
	public function actionManage()
	{
		$depotList = [0=>'Sydney', 1=>'Melbourne', 2=>'Brisbane'];
		$this->render('slot_management', ['depotList' => $depotList]);
	}

	/**
	 * Get booking slots status
	 */
	public function actionGetSlots($id)
	{
		switch($id) {
			case 0:
				$sql = "SELECT * FROM pickup_booking_slot WHERE depot = 106 ORDER BY slot_time ASC";
				$slots = PickupBookingSlot::model()->findAllBySql($sql);
				break;
			case 1:
				$sql = "SELECT * FROM pickup_booking_slot WHERE depot = 218 ORDER BY slot_time ASC";
				$slots = PickupBookingSlot::model()->findAllBySql($sql);
				break;
			case 2:
				$sql = "SELECT * FROM pickup_booking_slot WHERE depot = 530 ORDER BY slot_time ASC";
				$slots = PickupBookingSlot::model()->findAllBySql($sql);
				break;
		}
		$this->render('_sub_booking_slots', ['model' => $slots]);
	}

	/**
	 * Set booking slots' status
	 */
	public function actionSetSlots()
	{
		if (!empty($_POST)){
			switch ($_POST['depot']) {
				case 'Sydney':
					$sql = "SELECT * FROM pickup_booking_slot WHERE depot = 106 ORDER BY slot_time ASC";
					$slots = PickupBookingSlot::model()->findAllBySql($sql);
					break;
				case 'Melbourne':
					$sql = "SELECT * FROM pickup_booking_slot WHERE depot = 218 ORDER BY slot_time ASC";
					$slots = PickupBookingSlot::model()->findAllBySql($sql);
					break;
				case 'Brisbane':
					$sql = "SELECT * FROM pickup_booking_slot WHERE depot = 530 ORDER BY slot_time ASC";
					$slots = PickupBookingSlot::model()->findAllBySql($sql);
					break;
			}
			for ($i = 0; $i < 66; $i++){
				if (!empty($_POST[$i]) && $_POST[$i] == "active") {
					$slots[$i]->status = 1;
					$slots[$i]->save();
				} else {
					$slots[$i]->status = 0;
					$slots[$i]->save();
				}
			}
		}
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return PickupBookingSlot the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=PickupBookingSlot::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param PickupBookingSlot $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='pickup-booking-slot-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
