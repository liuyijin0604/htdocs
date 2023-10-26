<?php

class SystemSettingController extends Controller
{

	public function actionCargoProcessSetting()
	{
		$model = $this->loadModel('cargoProcess');
		if(!empty($_POST))
		{
			$model->key='cargoProcess';
			$model->uid=User::currentUserID();
			$model->date=date('Y-m-d H:i:s');
			$model->mdata=[];
			$model->meta="[]";
			$model->savePOSTMeta($_POST);
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render("cargo_process_setting",["model"=>$model]);
	}
	public function loadModel($key)
	{
		$model= SystemSetting::model()->find(' `key` = :key ',[":key"=>$key]);
		if ($model===null) {
			$model = new SystemSetting();
		}
		return $model;
	}

}
