<?php

class ErpVendorsController extends Controller
{
	public function actionVendorList()
	{
        $model = new ErpVendors('search');

         $this->render('list',array(
            'model' => $model,
        ));
	}

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate(){

        $model = new ErpVendors();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if ( isset($_POST['ErpVendors'])) {
            $model->attributes = $_POST['ErpVendors'];
            $model->save();
            $this->ajaxResult($model);
        }

        $this->render('create',array(
            'model'=>$model,
        ));

    }

    /**
     * update vendor information
     * @param $id
     */
    public function actionUpdate($id){

        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['ErpVendors'])){
            $model->attributes=$_POST['ErpVendors'];
            $model->save();
            $this->ajaxResult($model);
        }

        $this->render('update',array(
            'model'=> $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id){
        $model = ErpVendors::model()->findByPk($id);
        if ( $model === null )
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}