<?php

class DeliveryRecordController extends Controller
{
	protected $nonAjax=['scanInfo','export','generateCargoReceipt','printGatePass','uploadPOD','uploadSignatureFile','viewDriverJobs'];
	private $joinSql = "";

	public function actionList()
	{	
		$filtersForm=new FiltersForm;
		if (isset($_GET['FiltersForm'])) {
			$filtersForm->filters=$_GET['FiltersForm'];
		}
		$joinSql = $this->joinSql;
		$model = new DeliveryRecord('search');
		$model->unsetAttributes();
		if(!empty($_GET['DeliveryRecord']))
		{
			$model->setAttributes($_GET['DeliveryRecord']);
		}
		if(!empty($_GET['status']))
		{
			$model->status = $_GET['status'];
		}

		if(!empty($_GET['DeliveryRecord']))
		{
			$this->render("record_list",['model'=>$model]);
			return "";
		}

		$sql =" SELECT (@i :=@i + 1) AS id,cg.status,count(*) as number
				, (SELECT count(*) FROM `delivery_record` c ${joinSql} WHERE DATEDIFF(NOW(),booking_time)=0 and c.status=cg.status and to_days(booking_time)>=to_days(NOW())) as Today
				, (SELECT count(*) FROM `delivery_record` c ${joinSql} WHERE DATEDIFF(NOW(),booking_time)=1 and c.status=cg.status and to_days(booking_time)>=to_days(NOW())) as day2
				, (SELECT count(*) FROM `delivery_record` c ${joinSql} WHERE DATEDIFF(NOW(),booking_time)>=2 and c.status=cg.status and to_days(booking_time)>=to_days(NOW())) as day3
				FROM (SELECT @i := 0) AS it ,`delivery_record` cg 
				 WHERE cg.status<".DeliveryRecord::DONESTATE." and DATEDIFF(NOW(),booking_time)>=0 and to_days(booking_time)>=to_days(NOW()) group by cg.status ";

		$provide=Yii::app()->db->createCommand($sql)->queryAll();
		$filteredData=$filtersForm->filter($provide);
		$dataprovider=new CArrayDataProvider($filteredData);
		$dataprovider->pagination=['pageSize' =>10,];
		$sort=new CSort();
		$sort->attributes=[
			'number'=>[
				'asc'=>'number ASC',
				'desc'=>'number DESC',
			],
			'status'=>[
				'asc'=>'status ASC',
				'desc'=>'status DESC',
			],
		];
		$sort->defaultOrder = "status ASC";
		$dataprovider->sort=$sort;
		
		$this->render("list",['dataProvider'=>[$dataprovider,$filtersForm],'model'=>$model]);
	}

	public function actionUpdate($id)
	{	
		$model = $this->loadModel($id);
		if(!empty($_POST))
		{
			$model->setAttributes($_POST);
			$model->op_id = User::getCurrentUser()->id;
			$model->checkAttributes();
			$model->save();
			$this->ajaxResult($model);
			return "";
		}

		$this->render("_form",['model'=>$model,'type'=>'update']);
		
	}

	public function actionCreate()
	{	
		$model = new DeliveryRecord('create');
		if(!empty($_POST))
		{
			$model->setAttributes($_POST);
			$model->op_id = User::getCurrentUser()->id;
			$model->checkAttributes();
			$model->status = DeliveryRecord::NEWSTATE;
			$model->save();
			$this->ajaxResult($model);
			return "";
		}

		$this->render("_form",['model'=>$model,'type'=>'create']);
		
	}

	public function actionOperation($id)
	{	
		$model = $this->loadModel($id);

		if(!empty($_POST))
		{
				$model->status =$_POST['status'];
				$model->checkAttributes();
				$model->save();
				$this->ajaxResult($model);
				return "";
		}	

		if(!empty($_GET['yt0'])||!empty($_GET['yt1']))
		{
			if(!empty($_GET['yt0'])&&$_GET['yt0']=='confirm')
			{
				$model->status = DeliveryRecord::DONESTATE;
			}


			if(!empty($_GET['yt1'])&&$_GET['yt1']=='cancel')
			{
				$model->status = DeliveryRecord::CANCELSTATE;
			}	

			$model->checkAttributes();

			$model->save();
			$this->ajaxResult($model);
			return "";
		}
		$this->render("operation_tab",['model'=>$model]);
		
	}

	public function loadModel($id)
	{
		$model= DeliveryRecord::model()->findByPk($id);
		if ($model===null)
		{
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}


}
