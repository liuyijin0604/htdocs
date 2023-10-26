<?php

class OrgContactController extends Controller
{
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($id){
		$model=new OrgContact;
		$model->org_id = $id;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['OrgContact'])){
			$model->attributes=$_POST['OrgContact'];
			$model->type = 10;
			$model->func = empty($_POST['func'])? 0 : array_sum($_POST['func']);
			$model->save();
			$this->ajaxResult($model);
		}
		$model->status = 1;

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id){
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['OrgContact'])){
			$model->attributes=$_POST['OrgContact'];
			$model->func = empty($_POST['func'])? 0 : array_sum($_POST['func']);
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}
	
	public function actionApproval($id){
		$model=$this->loadModel($id);
		if($model && !empty($_POST['status'])){
			if(!empty($_POST['reason'])){
				$model->logExtra['reason'] = $_POST['reason'];
			}
			$model->status = $_POST['status'];
			$model->save();
			$this->ajaxResult($model);
		}else{
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new OrgContact('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['OrgContact']))
			$model->attributes=$_GET['OrgContact'];

		$this->render('list',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=OrgContact::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='org-contact-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
