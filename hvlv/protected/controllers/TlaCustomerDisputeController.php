<?php

class TlaCustomerDisputeController extends Controller
{
	protected $nonAjax = [];

	public function actionOperation()
	{
		$id = $_GET["id"];
		$result =[];
		$result['model'] = $this->loadModel($id);
		$result['tab'] = @$_GET['tab'];
		$this->render('operation_tab', $result);
	}

	public function actionUpdate()
	{
		$id = $_GET["id"];
		$this->updateCsDispute($id);
		echo "done";
	}

	private function updateCsDispute($id)
	{
		$model= $this->loadModel($id);
		if(isset($_POST["tla_op_comment"]))
		{
			$model->tla_op_comment = $_POST["tla_op_comment"];
		}
		if(isset($_POST["credit_note_no"]))
		{
			$model->credit_note_no = $_POST["credit_note_no"];
		}
		if(isset($_POST["credit_note_amount"]))
		{
			$model->credit_note_amount = $_POST["credit_note_amount"];
		}
		$model->save();
	}
	
	public function actionClose()
	{
		$tlaCustomerDisputeService = new TlaCustomerDisputeService();
		$id = $_GET['id'];
		$model=$this->loadModel($id);
		$tlaCustomerDisputeService->updateStatus($model,TlaCustomerDisputeLine::CLOSE_STATUS);
		echo 'done';
	}

	public function actionApproveStatus()
	{
		$tlaCustomerDisputeService = new TlaCustomerDisputeService();
		$id = $_GET['id'];
		$model=$this->loadModel($id);
		$tlaCustomerDisputeService->updateStatus($model,TlaCustomerDisputeLine::APPROVED_STATUS);
		echo 'done';
	}

	public function actionRejectStatus()
	{
		$tlaCustomerDisputeService = new TlaCustomerDisputeService();
		$id = $_GET['id'];
		$model=$this->loadModel($id);
		$tlaCustomerDisputeService->updateStatus($model,TlaCustomerDisputeLine::REJECTED_STATUS);
		echo 'done';
	}

	public function actionGenerateCreditNote()
	{
		$tlaCustomerDisputeService = new TlaCustomerDisputeService();
		$id = $_GET['id'];
		$model=$this->loadModel($id);
		$amount = $_POST['credit_note_amount'];
		$pass  = $_POST['pass'];
		$task = DisputeTask::model()->find("model = 'TlaCustomerDisputeLine' and fid = :fid",[":fid"=>$model->id]);
		$comment = "<p>DisputeTask ".$task->task_no.":".$model->comment."</p><p>TLA OP comment:".$model->tla_op_comment."</p>";


		$result = PaymentService::autoGenerateCreditNote($model->inv_id,$amount,$comment,$pass);
		if($result['done'])
		{
			$model->credit_note_no = $result['no'];
			$model->update(['credit_note_no']);
		}
		echo json_encode($result);
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = TlaCustomerDisputeLine::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'invoice-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
