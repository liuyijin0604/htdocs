<?php

class RouteController extends Controller
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
		$model=new Route;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Route']))
		{
			$model->attributes=$_POST['Route'];
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

		if(isset($_POST['Route']))
		{
			$model->attributes=$_POST['Route'];
			if (sizeof(explode('-', $model->destination)) == 2) {
				$model->code = explode('-', $model->destination)[0];
			}
			if (!empty($_POST['flight'])) {
				$model->flight_no = ' ' . implode(' ', $_POST['flight']) . ' ';
				$model->mdata['deptimes'] = implode(' ', $_POST['deptime']);
			}
			if($model->save()) {
				// $this->redirect(array('view','id'=>$model->id));

				foreach ($model->quotes as $quote) {
					$quote->delete();
				}

				if (!empty($_POST['uld_type']) && $_POST['uld_type'] == 'pallet') {
					$range = $_POST['range'];
					$price = $_POST['price'];
					$sec_fuel = $_POST['sec_fuel'];
					$min = @$_POST['min'];

					$temp = $range;
					asort($temp);
					$temp = array_values($temp);
					if ($temp != $range) {
						$range = array_values(array_reverse($range));
						$price = array_values(array_reverse($price));
						$sec_fuel = array_values(array_reverse($sec_fuel));
					}

					$n = sizeof($range);
					for ($i = 0; $i < $n; $i++) {
						$modelQ = new Quotes;
						// $modelQ->attributes = $_POST['Quotes'];
						$modelQ->route_id = $id;
						$modelQ->uld_type = $_POST['uld_type'];
						$modelQ->wt_lo = $range[$i];
						$modelQ->wt_hi = empty($range[$i + 1]) ? null : $range[$i + 1];
						$modelQ->pkg = $price[$i];
						$modelQ->min = $min;
						$modelQ->sec_fuel = $sec_fuel[$i];
						$modelQ->date_eff = $_POST['date_eff'];
						$quotesExist = Quotes::model()->find('wt_lo=:wt_lo AND (wt_hi=:wt_hi OR wt_hi is NULL)AND route_id=:route_id AND uld_type =:uld_type And date_eff =:date_eff',
							array(':route_id' => $modelQ->route_id, ':uld_type' => $modelQ->uld_type, ':date_eff' => $modelQ->date_eff, ':wt_lo' => $modelQ->wt_lo, ':wt_hi' => $modelQ->wt_hi));
						if (!$quotesExist) {
							$modelQ->save();
						}
					}
				}
			}
			$this->ajaxResult($model);
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Route');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Route('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Route']))
			$model->attributes=$_GET['Route'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Route the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Route::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Route $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='route-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
