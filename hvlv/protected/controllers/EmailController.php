<?php

class EmailController extends Controller{

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$model = $this->loadModel($id);
		$this->render('view',array(
			'model'=>$model,
		));
	}
	
	public function actionAttachment($id){
		$model = $this->loadModel($id);
		$model->showAttachment(substr($_GET['file'],0,-4));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate(){
		$model=new Emailog;
		$model->type = $_GET['type'];
		$model->fid = empty($_GET['fid'])? 0 : $_GET['fid'];
		if(!empty($_GET['ret'])) $model->mdata['ret'] = $_GET['ret'];
		if(!empty($_GET['invoice'])) $model->mdata['invoice'] = $_GET['invoice'];

		if(isset($_POST['Emailog'])){
			$model->attributes=$_POST['Emailog'];
			foreach($_POST['extra'] as $k => $v){
				$model->mdata[$k] = $v;
			}
			$model->status = 10;
			$model->save();
			
			if(!empty(Yii::app()->session['uploads'][$_POST['ppupload']][2])){
				foreach(Yii::app()->session['uploads'][$_POST['ppupload']][2] as $fid){
					$fr = FileRepo::model()->findByPk($fid);
					$fr->fid = $model->id;
					$fr->save();
				}
			}
			
			if(empty($model->scheduled)) $model->send();
			$this->ajaxResult($model, array('id'), 'Email Sent Successfully');
		}

		$model->prepTemplate();
		$model->subject = $model->tpl->subject;
		$model->body = $model->tpl->getContent();
		
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

		if(isset($_POST['Email'])){
			$model->attributes=$_POST['Email'];
			$model->to = empty($_POST['to'])? '' : implode(',', $_POST['to']);
			$model->save();
			$this->ajaxResult($model);
		}
		
		switch($model->type){
			case 2:
				$q = Quote::model()->findByPk($model->fid);
				$tos = $q->cust->contactList('Quote');
			break;
		}

		$this->render('update',array(
			'model'=>$model,
			'tos'=>$tos,
		));
	}
	
	public function actionUpdateSchedule($id){
		$model=Emailog::model()->find('scid = :id', array(':id' => $id));

		if(!empty($_POST['schedule'])){
			if($_POST['submit'] == 'Delete Schedule'){
				$cmd = Yii::app()->db->createCommand()->update('emailog', array('status'=>95), 'status = 10 AND scid = :id', array(':id' => $id));
			}else{
				if(strlen($_POST['schedule']) == 10) $_POST['schedule'] .= ' 10:00:00';
				$cmd = Yii::app()->db->createCommand()->update('emailog', array('scheduled' => $_POST['schedule'], 'ctno' => $_POST['ctno']), 'status = 10 AND scid = :id', array(':id' => $id));
			}
			//$cmd->execute();
			$this->ajaxResult($model);
		}

		$this->render('updateSchedule',array(
			'model'=>$model,
		));
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new Email('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Email']))
			$model->attributes=$_GET['Email'];

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
		$model=Email::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='email-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
