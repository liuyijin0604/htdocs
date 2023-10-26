<?php

class AppController extends Controller{
	
	protected $nonAjax=array();

	public function actionSettings(){
		$model = User::model()->findByPk(Yii::app()->user->id);

		if(!empty($_POST['User'])){
			if(empty($_POST['User']['password'])){
				unset($_POST['User']['password']);
			}else{
				$_POST['User']['password'] = md5($_POST['User']['password']);
			}
			$model->attributes=$_POST['User'];
			$model->save();
			$this->ajaxResult($model);
		}
		$this->render('settings', array('model' => $model));
	}
}