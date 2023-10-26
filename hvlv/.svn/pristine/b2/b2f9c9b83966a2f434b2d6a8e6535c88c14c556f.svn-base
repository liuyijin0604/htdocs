<?php

class SalesFunnelController extends Controller
{
	 /**
	 * Declares class-based actions.
	 */
    protected $nonAjax = array('showallquestion','createQuestionAnswersForm','updateQuestionAnswersForm','showAllQuestionAnswer');
    protected $skipAcl = array('showallquestion','createQuestionAnswersForm','updateQuestionAnswersForm','showAllQuestionAnswer');
    protected $skipLogin = array('showallquestion','createQuestionAnswersForm','updateQuestionAnswersForm','showAllQuestionAnswer');


    public function actionShowAllQuestionAnswer(){
    	// $questionModel = SalesfunnelRequirementsQuestions::model()->with('this_question','next_question');
    	$questionModel = new SalesfunnelRequirementsQuestions('search');
    	if (!empty($_GET['SalesfunnelRequirementsQuestions'])) {
			$questionModel->attributes = $_GET['SalesfunnelRequirementsQuestions'];
		}

    	$this->render('list_question_answer', [
            'questionModel' => $questionModel
        ]);

    }

    public function actionQueAnswer()
	{
		if (!empty($_POST)) {
			$id = $_POST["id"];
			$nextQuestion = $_POST["nextQuestion"];
			$requirementsAnswer = SalesfunnelRequirementsAnswer::model()->findByPk($id);
			$requirementsAnswer->next_question_id = $nextQuestion;
			$requirementsAnswer->save();
		}
		echo "done";
	}

	public function actionChangeCustType()
	{
		// echo json_encode($_POST);
		// Yii::app()->end();
		if (!empty($_POST)) {			
			$id = $_POST["id"];
			$customerType = $_POST["customer_type"];
			$requirementsSubmission = SalesfunnelRequirementsSubmission::model()->findByPk($id);
			$requirementsSubmission->customer_type = $customerType;			
			$requirementsSubmission->save();
			// print_r($requirementsSubmission);
			// Yii::app()->end();			
		}
		echo "done";
	}

	public function actionCreateQuestionAnswersForm()
	{
		$model = new SalesfunnelRequirementsQuestions('create');
		$this->renderPartial('question_answer_storage_form',['model'=>$model]);
	}

	public function actionSaveQuestionAnswers()
	{
		$id = @$_GET['id'];
		// echo(json_encode($id));
		//echo(json_encode($_POST));
		// Yii::app()->end();

		$questionModel = new SalesfunnelRequirementsQuestions();
		if(!empty($id))
		{
			$questionModel = SalesfunnelRequirementsQuestions::model()->findByPk($id);
		}		
		if(!empty($_POST['SalesfunnelRequirementsQuestions']['question_num']))
		{
			$questionModel->question_num = $_POST['SalesfunnelRequirementsQuestions']['question_num'];
		}
		if(!empty($_POST['SalesfunnelRequirementsQuestions']['question']))
		{
			$questionModel->question = $_POST['SalesfunnelRequirementsQuestions']['question'];
		}
		if(!empty($_POST['SalesfunnelRequirementsQuestions']['question_type']))
		{
			$questionModel->question_type = $_POST['SalesfunnelRequirementsQuestions']['question_type'];
		}
		if(!empty($_POST['SalesfunnelRequirementsQuestions']['question_category']))
		{
			$questionModel->question_category = $_POST['SalesfunnelRequirementsQuestions']['question_category'];
		}
		$re = $questionModel->save();
		// echo($questionModel->id);

		if(!empty($_POST['items']['anid']) && $_POST['items']['anid'][0]>0)
		{
			foreach ($_POST['items']['anid'] as $key => $value) 
			{
				$oan = SalesfunnelRequirementsAnswer::model()->findByPk($value);
				$oan->answer = $_POST['items']['an_answer'][$key];
				$oan->question_id = $_POST['items']['pid'][$key];
				$oan->next_question_id = $_POST['items']['nextQueId'][$key];
				$oan->save();
				// echo($oan);
			} 
		}else{
			// $len = count($_POST['items']['an_answer']);
			// $answerModel = [];
			foreach ($_POST['items']['an_answer'] as $key => $value) 
			{
				$oan = new SalesfunnelRequirementsAnswer();
				$oan->answer = $value;
				$oan->question_id = $questionModel->id;
				$oan->next_question_id = $_POST['items']['nextQueId'][$key];
				$oan->save();
				// echo($oan);
			}
		}
		if($re)
		{
			echo "done";
		}else
		{
			echo "failure";
		}




		// echo(json_encode($_POST));
  		//Yii::app()->end();
	}

	public function actionUpdateQuestionAnswersForm()
	{
		$id = @$_GET['id'];
		$model = SalesfunnelRequirementsQuestions::model()->findByPk($id);
		$answers = [];
		$questionType = $model->question_type;
		if($questionType == SalesfunnelRequirementsQuestions::QUESTIONTYPERADIOBUTTON || $questionType == SalesfunnelRequirementsQuestions::QUESTIONTYPECHECKBOX)
		{
			$ansjs = SalesfunnelRequirementsAnswer::model()->findAll(["condition" => 'question_id = '.$id]);
			// echo($ansjs[0]->id);
			// Yii::app()->end();
			foreach ($ansjs as $key => $value) {
				$answers['anid'][] = $value->id;
				$answers['an_answer'][] = $value->answer;
				$answers['pid'][] = $value->question_id;
				$answers['nextQueId'][] = $value->next_question_id;
			}
			$this->render('question_answer_storage_form',['model' => $model,'answers'=>$answers]);
			reutrn ;
		}
		
		$this->renderPartial('question_answer_storage_form',['model' => $model]);

	}

	public function actionShowCustomersDetail(){
		$submissionModel = new SalesfunnelRequirementsSubmission('search');
		if (!empty($_GET['sid'])){
			$sid = $_GET["sid"];
			$submissionModel->id = $sid;
		}    	
    	
    	$recordModel = new SalesfunnelRequirementsRecords('search');
    	if (!empty($_GET['SalesfunnelRequirementsSubmission'])) {
			$submissionModel->attributes = $_GET['SalesfunnelRequirementsSubmission'];
		}


    	$this->render('show_requirements', [
            'submissionModel' => $submissionModel,
            'recordModel' => $recordModel
        ]);

    }

    public function actionShowIndividualRequirement(){
    	$submit_id = $_GET['id'];
        $submissionModel = SalesfunnelRequirementsSubmission::model()->findbypk($submit_id);
        $requirementRecordModels = SalesfunnelRequirementsRecords::model()->with('question','answer')->findAll(["condition"=>'submit_id =  '.($submit_id),"order"=>"t.question_id ASC"]);
        $questionModels = new SalesfunnelRequirementsQuestions('search');
        $answerModels = new SalesfunnelRequirementsAnswer('search');
        if (!empty($_GET['SalesfunnelRequirementsQuestions'])) {
            $questionModel->attributes = $_GET['SalesfunnelRequirementsQuestions'];
        }
        if (!empty($_GET['SalesfunnelRequirementsAnswer'])) {
            $answerModel->attributes = $_GET['SalesfunnelRequirementsAnswer'];
        }

    	$this->renderPartial('individual_requirement',[ 'submissionModel' => $submissionModel,
            'requirementRecordModels' =>$requirementRecordModels]);
    }

    public function actionCreateEmail()
	{
		$submit_id = $_GET['id'];
		$submissionModel = SalesfunnelRequirementsSubmission::model()->findbypk($submit_id);
		$emailService = new EmailService();
		[$model,$data] = $emailService->createEmailContent($_POST,$_GET, null, null, null,null,$submissionModel->email);
		if($data===1)
		{
			$this->ajaxResult($model);
		}
		
		$this->render('//importsmail/create_email', ['model' => $model,'data'=>$data]);
	}

    public function actionGetRelatedEmails()
	{
		$submit_id = $_GET['id'];
		$submissionModel = SalesfunnelRequirementsSubmission::model()->findbypk($submit_id);		
		$model = new ImportsMail('search');
		$model->unsetAttributes();
		$ids = [-1];
		// echo $submissionModel->email;
		// Yii::app()->end();

		$relatedEmails = ImportsMail::model()->findAll(" from_email = :from_email OR to_email = :to_email",[":from_email"=>$submissionModel->email, ":to_email"=>$submissionModel->email]);
		if (!empty($relatedEmails)&&isset($relatedEmails)){
			$ids = array_column($relatedEmails, "id");
		}
		// echo json_encode($relatedEmails);
		// Yii::app()->end();

		$model->emailIds = $ids;
		$this->render("//customerservice/emails",["model"=>$model]);
	}

	public function actionRequirementProcessOperation()
	{
		$id = $_GET['id'];
		$processType = $_GET['process_type'];
		$model = $this->loadModel($id);
		$this->render("requirement_process_operation",["processType"=>$processType,'model'=>$model,'customer_type'=>$model->customer_type]);
	}

	public function loadModel($id)
	{
		$model= SalesfunnelRequirementsSubmission::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	public function actionReqOperationUpdate($id)
	{
		$id = $_GET["id"];
		$requirementsSubmission = new SalesfunnelRequirementsSubmission();
		$ids = [];
		$result =[];
		$ids = explode(Process::IP_SEPERATOR,$id);

		foreach ($ids as $key => $id) {
			$requirementsSubmission = SalesfunnelRequirementsSubmission::model()->findByPk($id);
			$this->saveReqOperationUpdate($requirementsSubmission);
		}

		echo 'done';
	}

	private function saveReqOperationUpdate($requirementsSubmission)
	{
		if(!empty($_POST['submission']['sub_status']))
		{
			$requirementsSubmission->sub_status = $_POST['submission']['sub_status'];
		}

		$requirementsSubmission->update('sub_status');
	}

	public function actionEditProcessEmail()
	{
		$id = $_GET["id"];
		$ids = [];
		if(strpos($id,Process::IP_SEPERATOR))
		{
			$ids = explode(Process::IP_SEPERATOR,$id);
		}else
		{
			$ids[] = $id;
		}
		// print_r(json_encode($_GET));
		// Yii::app()->end();

		$salesFunnelService = new SalesfunnelRequirementsService();
		[$model,$data] = $salesFunnelService->editProcessEmail($_POST,$_GET,$ids);
		if($data===1)
		{
			$this->ajaxResult($model);
		}
		$this->render('//importsMail/create_email', ['model' => $model,'data'=>$data]);

	}

	public function actionRequirementProcessUpdate()
	{
		$id = $_POST["id"];
		$submission = $this->loadModel($id);
		$processType = $_POST['processType'];
		$isIgnore = @$_POST['isIgnore'];
		// print_r(json_encode($_POST));
		// Yii::app()->end();
		if($isIgnore=="false") $isIgnore = 0;
		$submissionService = new SalesfunnelRequirementsService();
		$result = $submissionService->updateRequirementProcessStatus($submission,$processType,$isIgnore,$_POST);
		echo json_encode($result);
	}
      
}
