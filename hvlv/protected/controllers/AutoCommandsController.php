<?php

class AutoCommandsController extends Controller
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
		/* $model=new AutoCommands;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['AutoCommands']))
		{
			$model->attributes=$_POST['AutoCommands'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		)); */
		if (empty($_POST)) {
			$this->render('create');
		} else {
			$commandName = $_POST['command_name'];
			$funcName = $_POST['func_name'];
			$day = !empty($_POST['day']) ? $_POST['day'] : 0;
			$weekDay = !empty($_POST['week_day']) ? $_POST['week_day'] : 0;
			$commandExecutionType = ($_POST['execution_type'] == 'time_interval') ? AutoCommands::TYPE_TIME_INTERVAL : AutoCommands::TYPE_TIME_FIXED;
			if ($commandExecutionType == AutoCommands::TYPE_TIME_FIXED) {
				// for fixed time commands
				/* for ($h = 0; $h < 24; $h++) {
					if (isset($_POST['hour_'.$h])) {
						array_push($selectedHours, $h);
					}
				}
	
				for ($m = 0; $m < 60; $m+=5) {
					if (isset($_POST['minute_'.$m])) {
						array_push($selectedMinutes, $m);
					}
				} */
				$hour = !empty($_POST['hour']) ? intval($_POST['hour']) : 0;
				$minute = !empty($_POST['minute']) ? intval($_POST['minute']) : 0;
				$command = new AutoCommands();
				$command->command_name = $commandName;
				$command->func_name = $funcName;
				$command->day = $day;
				$command->week_day = $weekDay;
				$command->hour = $hour;
				$command->minute = $minute;
				$command->type = AutoCommands::TYPE_TIME_FIXED;
				$command->save();
			} else {
				// time interval executed commands
				$hourInterval = !empty($_POST['hour_interval']) ? intval($_POST['hour_interval']) : 1; // default: run every hour
				$minuteInterval = !empty($_POST['minute_interval']) ? intval($_POST['minute_interval']) : 1; // default: run every minute
				$command = new AutoCommands();
				$command->command_name = $commandName;
				$command->func_name = $funcName;
				$command->day = $day;
				$command->week_day = $weekDay;
				$command->type = AutoCommands::TYPE_TIME_INTERVAL;
				$command->hour_interval = $hourInterval;
				$command->minute_interval = $minuteInterval;
				$command->save();
			}
			

			/* foreach ($selectedHours as $hour) {
				foreach ($selectedMinutes as $minute) {
					$command = new AutoCommands();
					$command->command_name = $commandName;
					$command->func_name = $funcName;
					$command->day = $day;
					$command->week_day = $weekDay;
					$command->hour = $hour;
					$command->minute = $minute;
					$command->save();
				}
			} */
			echo 'Done';
		}
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

		if(isset($_POST['AutoCommands']))
		{
			$model->attributes=$_POST['AutoCommands'];
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
		$dataProvider=new CActiveDataProvider('AutoCommands');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new AutoCommands('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['AutoCommands']))
			$model->attributes=$_GET['AutoCommands'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return AutoCommands the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=AutoCommands::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param AutoCommands $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='auto-commands-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
