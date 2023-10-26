<?php

class ImportsAmazonController extends Controller
{
	protected $nonAjax=[];
    

	public function actionCreateAmazonPlan()
	{
		$model = ImportsAmazonPlan::model();
		$dptId = $_GET['pod_id'];
		$model->dpt_id = $dptId;
		if(!empty($_POST['amazon_booking_time']))
		{
			$iap = ImportsAmazonPlan::model()->find('amazon_booking_time =:a and dpt_id=:dptId',[':a'=>$_POST['amazon_booking_time'],':dptId'=>$_POST['dpt_id']]);
			if(empty($iap))
			{
				$iap = new ImportsAmazonPlan();
			}
			$iap->amazon_booking_time = $_POST['amazon_booking_time'];
			$iap->pallet = $_POST['pallet'];
			$iap->dpt_id = $_POST['dpt_id'];
			$iap->create = date('Y-m-d H:i:s');
			$iap->user_id = User::currentUserID();
			$iap->save();
			$this->ajaxResult($iap);
		}
		$this->render('create_amazon_plan',['model'=>$model,'dptId'=>$dptId]);
	}

}
