<?php

class WarehouseGroupController extends Controller{

	protected $skipAcl = ['profile'];

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
	 * Lists all models.
	 */
	public function actionList(){
        $model=new UserWarehouseGroup('search');
        $model->unsetAttributes();
        if(isset($_GET['UserWarehouseGroup']))$model->attributes=$_GET['UserWarehouseGroup'];

        $rule=new UserWarehouseGroupRule('search');
        $rule->unsetAttributes();
        if(isset($_GET['UserWarehouseGroupRule']))$rule->attributes=$_GET['UserWarehouseGroupRule'];

        $this->render('list',array(
            'model'=>$model,
            'rule'=>$rule
        ));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionUpdateRule()
	{
		$id = $_GET["id"];
		$model = UserWarehouseGroupRule::model()->findByPk($id);
		$this->render('operation_tab',['model'=>$model]);
	}

	public function actionSaveStatus()
	{
		$id = $_GET["id"];
		$model = UserWarehouseGroupRule::model()->findByPk($id);
		if($model->status==UserWarehouseGroupRule::ACTIVE)
		{
			$model->status = UserWarehouseGroupRule::INACTIVE;
			if($model->other_agent==1)
			{
				$rule = UserWarehouseGroupRule::model()->find("`from` = :from and id != :id and dpt_id=:dpt_id",[":from"=>$model->from,":id"=>$model->id,":dpt_id"=>$model->dpt_id]);
				$rule->other_agent = 1;
				$rule->save();
				$model->other_agent=0;
			}
		}else
		{
			$model->status = UserWarehouseGroupRule::ACTIVE;
			$rule = UserWarehouseGroupRule::model()->find("`from` = :from and id != :id and `group` < :group and status =0  and dpt_id=:dpt_id",[":from"=>$model->from,":id"=>$model->id,":group"=>$model->group,":dpt_id"=>$model->dpt_id]);
			if(!empty($rule))
			{
				$rule->other_agent =1;
				$rule->save();
			}else
			{
				$model->other_agent =1;
				$rule = UserWarehouseGroupRule::model()->find("`from` = :from and id != :id and `group` > :group  and dpt_id=:dpt_id",[":from"=>$model->from,":id"=>$model->id,":group"=>$model->group,":dpt_id"=>$model->dpt_id]);
				if(!empty($rule))
				{
					$rule->other_agent = 0;
					$rule->save();
				}
			}
		}
		$model->save();
		echo "Success";
	}
}
