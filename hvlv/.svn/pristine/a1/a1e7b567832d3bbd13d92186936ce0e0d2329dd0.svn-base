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
		}elseif(isset($_POST['extra'])){
			$org = $model->org;
			$org->extra['always_last_shipper'] = empty($_POST['extra']['always_last_shipper'])? 0 : 1;
			$org->extra['edo_margin'] = empty($_POST['extra']['edo_margin'])? 1 : $_POST['extra']['edo_margin'];
			$org->extra['pager_size'] = empty($_POST['extra']['pager_size'])? 1 : $_POST['extra']['pager_size'];
			$org->extra['new_multi'] = empty($_POST['extra']['new_multi'])? 0 : 1;
			$org->extra['pickup_when_weigh'] = empty($_POST['extra']['pickup_when_weigh'])? 0 : 1;
			$org->sync_xero = false;
			$org->save();
			$this->ajaxResult($org);
		}
		$this->render('settings', array('model' => $model));
	}
}