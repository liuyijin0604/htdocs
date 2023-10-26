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
			$org->extra['pager_size'] = empty($_POST['extra']['pager_size'])? 1 : $_POST['extra']['pager_size'];
            $org->extra['tracking'] = empty($_POST['extra']['tracking'])? 0 : $_POST['extra']['tracking'];
			$org->save();
			$this->ajaxResult($org);
		} elseif ( isset($_POST['OrgContact']) ) {
            // save related org contact - here only shipper address
            if ( empty($model->org->contacts) ) {
                $orgContact = new OrgContact();
                $orgContact->org_id = $model->org->id;
            } else {
                $orgContact = $model->org->contacts[0];
            }
            $orgContact->attributes = $_POST['OrgContact'];
            $orgContact->setAttribute('position','Shipper'); // save as shipper used address
            $orgContact->save();
            $this->ajaxResult($model);
        }
		$this->render('settings', array('model' => $model));
	}
}