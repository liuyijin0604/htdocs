<?php

class CustomerServiceController extends Controller
{
	protected $nonAjax=[];
	public function actionFaqList()
	{
		$model=new CsFaq('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['csFaq'])) {
			$model->attributes=$_GET['csFaq'];
		}

		$this->render('faqList', [
			'model'=>$model,
		]);
	}


	public function actionFaqAnswerList()
	{
		$model=new CsFaqAnswer('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['csFaqAnswer'])) {
			$model->attributes=$_GET['csFaqAnswer'];
		}

		$this->render('faqAnswerList', [
			'model'=>$model,
		]);
	}

	public function actionIndex()
	{
		Yii::import('application.controllers.ImportsMailController');
		$modelQuestion = new ShipmentQuestion('search');
		$modelQuestion->unsetAttributes();
		$modelQuestion->process_type = ShipmentQuestion::TYPE_UNFINISHED;
		if(isset($_GET["ShipmentQuestion"]))
		{
			$modelQuestion->setAttributes($_GET["ShipmentQuestion"]);
			if(isset($_GET["ShipmentQuestion"]["date"]))
			{
				$modelQuestion->setDate($_GET["ShipmentQuestion"]["date"]);
			}
			if(isset($_GET["ShipmentQuestion"]["ticket"]))
			{
				$modelQuestion->setTicket($_GET["ShipmentQuestion"]["ticket"]);
			}
		}

		if(isset($_GET["ShipmentQuestionSubmit"]))
		{
			$modelQuestion->submitType = @$_GET["ShipmentQuestionSubmit"]["type"];
		}


		if (isset($_POST['org_id']) || isset($_GET['org_id'])) {
			if (isset($_POST['org_id'])) {
				if (!empty($_POST['org_id'])&&$_POST['org_id']!=0) {
					$modelQuestion->org_id =$_POST['org_id'];
				}
				$orgId = $_POST['org_id'];
			} else {
				if (!empty($_GET['org_id'])&&$_GET['org_id']!=0) {
					$modelQuestion->org_id =$_GET['org_id'];
				}
				$orgId = $_GET['org_id'];
			}

			$this->renderPartial('_sub_questions', [
				'modelQuestion' => $modelQuestion,
				'orgId' => $orgId,
			]);
			return;
		}

		$model=new CsFaq('search');
		$model->unsetAttributes();  // clear any default values
		$filtersForm=new FiltersForm;
		if (isset($_GET['FiltersForm'])) {
			$filtersForm->filters=$_GET['FiltersForm'];
		}
		$provide=[];
		$sql="SELECT (@i :=@i + 1) AS id, cg.org_id, o.name,count(*) as number, (SELECT count(*) FROM `shipment_question_submit` c  join shipment_question s2  on c.id = s2.submit_id
             join org o2 on c.org_id = o2.id WHERE DATEDIFF(NOW(),date)<=1 and cg.org_id=c.org_id and s2.process_type!= ".ShipmentQuestion::TYPE_FINISHED." ) as day1
			, (SELECT count(*) FROM `shipment_question_submit` c join shipment_question s2  on c.id = s2.submit_id
             join org o2 on c.org_id = o2.id WHERE DATEDIFF(NOW(),date)<=2 and DATEDIFF(NOW(),date)>1 and cg.org_id=c.org_id  and s2.process_type!= ".ShipmentQuestion::TYPE_FINISHED." ) as day2
			, (SELECT count(*) FROM `shipment_question_submit` c join shipment_question s2  on c.id = s2.submit_id
             join org o2 on c.org_id = o2.id WHERE DATEDIFF(NOW(),date)>2 and cg.org_id=c.org_id  and s2.process_type!= ".ShipmentQuestion::TYPE_FINISHED.") as day3 from
			(SELECT @i := 0) AS it,`shipment_question_submit` cg
             join shipment_question s  on cg.id = s.submit_id
             join org o on cg.org_id = o.id
            WHERE s.process_type!= ".ShipmentQuestion::TYPE_FINISHED." group by cg.org_id order by id";

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
			'name' => [
				'asc' => 'name ASC',
				'desc' => 'name DESC',
			],
		];
		$sort->defaultOrder = "status ASC";
		$dataprovider->sort=$sort;
		
		$tableTwoInfo = [];
		$totalShipmentQuestion = 0;
		$sql = 'SELECT COUNT(*) as number, user_id, type FROM `shipment_question_log` WHERE time<=SUBDATE(CURDATE(),INTERVAL -1080 MINUTE) AND time>=SUBDATE(CURDATE(),INTERVAL 360 MINUTE) AND status=1 GROUP BY user_id,type';
		$open_info = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($open_info as $oneOpenInfo) {
			$tableTwoInfo[$oneOpenInfo['user_id']][$oneOpenInfo['type']] = ['type' => $oneOpenInfo['type'], 'open' => $oneOpenInfo['number'], 'close' => 0, 'total' => 0, 'close_avg' => 0, 'close_avg_percent' => 0];
		}

		$sql = 'SELECT COUNT(*) as number,p.type,t.finish_user_id as user_id FROM `shipment_question` t 
					INNER JOIN `shipment_question_submit` sqs on t.submit_id = sqs.id 
					INNER JOIN `shipment_question_log` p ON t.id=p.fid and  t.finish_user_id = p.user_id
					WHERE sqs.date<=SUBDATE(CURDATE(),INTERVAL -1080 MINUTE)
					 AND sqs.date>=SUBDATE(CURDATE(),INTERVAL 360 MINUTE) 
					 AND t.process_type=100 
					GROUP by p.type,t.finish_user_id';

		$close_info = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($close_info as $oneCloseInfo) {
			$tableTwoInfo[$oneCloseInfo['user_id']][$oneCloseInfo['type']]['close'] = $oneCloseInfo['number'];
		}

		$sql = "SELECT count(*) as number, p.user_id,p.type FROM `shipment_question` t INNER JOIN `shipment_question_log` p ON t.id=p.fid WHERE t.process_type<100 AND p.status=1 GROUP by p.user_id , p.type";

		$totalHeldInfo = Yii::app()->db->createCommand($sql)->queryAll();
		$totalHelshipments = 0;

		$this->filteringJobs($tableTwoInfo,$totalHeldInfo,$totalHelshipments);

		$faqList = CsFaq::getFaqList();
		$closeAvgArr = [];
		foreach ($faqList as $key => $faq) {
			$closeAvgArr[$key.'close_avg'] = 1;
		}

		$sql = 'SELECT COUNT(*) FROM `shipment_question_submit` t INNER JOIN `shipment_question` s ON t.id=s.submit_id WHERE t.date>=SUBDATE(CURDATE(),INTERVAL 29 DAY) AND t.date<=CURDATE()';

		$totalShipmentQuestion = Yii::app()->db->createCommand($sql)->queryScalar();
		$totalShipmentQuestion = ceil($totalShipmentQuestion / 30);

		foreach ($tableTwoInfo as $user_id => $typeInfo) {
			foreach ($faqList as $key => $name) {
				if (key_exists($key, $typeInfo)) 
				{
					$tableTwoInfo[$user_id][$key]['close_avg'] = $closeAvgArr[$key . 'close_avg'];

					$tableTwoInfo[$user_id][$key]['close_avg_percent'] = ($closeAvgArr[$key . 'close_avg'] > 0 ? number_format(@intval($tableTwoInfo[$user_id][$key]['close']) / $closeAvgArr[$key . 'close_avg'] * 100, 2, '.', '') : 0);
					$tableTwoInfo[$user_id][$key]['totalPercent'] = ($totalShipmentQuestion > 0 ? number_format(@$tableTwoInfo[$user_id][$key]['total'] / $totalShipmentQuestion * 100, 2, '.', '') : 0);
					$tableTwoInfo[$user_id][$key]['percent'] = ($totalShipmentQuestion > 0 ? number_format(@$tableTwoInfo[$user_id][$key]['total'] / $totalHelshipments * 100, 2, '.', '') : 0);
				}
			}
		}



		$info = ['tableTwoInfo' => $tableTwoInfo, 'totalShipmentQuestion' => $totalShipmentQuestion];



		$this->render('index', [
			'dataProvider'=>[$dataprovider,$filtersForm],
			'model'=>$model,
			'info' => $info,
			'modelQuestion' => $modelQuestion,
			'orgId' => null,
		]);

		Yii::app()->runController('importsMail/listSimple');

	}

	private function filteringJobs(&$tableTwoInfo,$totalHeldInfo,&$totalHelshipments)
	{
				//below is the filter for filtering staff's assigned job
		$mapUsersArr=[];
        $rs =TypeMapUser::model()->findAll('type=:type and status=1',array(':type'=> TypeMapUser::TYPE_CUSTOMER_SERVICE));
        $faqList = CsFaq::getFullFaqList();
        foreach($rs as $oneMap){
       		foreach ($totalHeldInfo as $oneHeldInfo) 
       		{
       			if($faqList[$oneMap['map_type']][0]==$oneHeldInfo['type']&&$oneMap['user_id']==$oneHeldInfo['user_id'])
	       		{
	       			$totalHelshipments += intval($oneHeldInfo['number']);
					$tableTwoInfo[$oneHeldInfo['user_id']][$oneHeldInfo['type']]['total'] = $oneHeldInfo['number'];
	       		}
			}
        }
        //above is the filter for filtering staff's assigned job
        return $tableTwoInfo;
	}
		
	public function actionAssignUser()
	{
		if (!empty($_POST)) {
			$model = new TypeMapUser();
			TypeMapUser::model()->deleteAll(" type=:type ",[':type' =>TypeMapUser::TYPE_CUSTOMER_SERVICE]);
			foreach ($_POST as $key => $value) {
				$thisKey = explode("_", $key)[0];
				$r = new TypeMapUser();
				$r->type = TypeMapUser::TYPE_CUSTOMER_SERVICE;
				$r->map_type = $thisKey;
				$r->user_id = $value;
				$r->status = 1;
				$r->save();
			}
			$this->ajaxResult($model);
		}

		$this->render('assign_user');
	}

	public function actionAddFaq()
	{
		if (!empty($_POST)) {
			$faq = new CsFaq();
			$faq->setAttributes($_POST["faq"]);
			$faq->user_id= User::currentUserID();
			$faq->order=999;
			$faq->save();
		}
		echo "done";
	}

	public function actionInactiveFaq()
	{
		$id = $_GET["id"];
		$type = $_GET["type"];
		$faq = CsFaq::model()->findByPk($id);
		if($type=="active")
		{
			$faq->status = CsFaq::active;
		}else
		{
			$faq->status = CsFaq::inactive;
		}
		$faq->update(["status"]);
		echo "done";
	}

	public function actionInactiveFaqAnswer()
	{
		$id = $_GET["id"];
		$type = $_GET["type"];
		$faqAnswer = CsFaqAnswer::model()->findByPk($id);
		if($type=="active")
		{
			$faqAnswer->status = CsFaq::active;
		}else if($type=="email")
		{
			$faqAnswer->status = CsFaq::EMAIL;
		}else 
		{
			$faqAnswer->status = CsFaq::inactive;
		}
		$faqAnswer->update(["status"]);
		echo "done";
	}


	public function actionAddFaqAnswer()
	{
		if (!empty($_POST)) {
			$faqAnswer = new CsFaqAnswer();
			$faqAnswer->setAttributes($_POST["faqAnswer"]);
			$faqAnswer->user_id= User::currentUserID();
			$faqAnswer->save();
		}
		echo "done";
	}


	public function actionUpdate($id)
	{
		$model = ShipmentQuestion::model()->findByPk($id);
		if(empty($model->s_read))
		{
			$model->s_read = 1;
			$model->save();
		}
		$emailTpl = EmailTpl::model()->find("slug = :slug ",[":slug"=>EmailTpl::courierParcelSearch]);
		if($emailTpl==null)
		{
			$emailTpl = new EmailTpl();
		}else
		{
			$ref = empty($model->Shipment)?"":$model->Shipment->ref;
			$emailTpl->assignSubject('SHIPMENT_NO', $ref);
			$emailTpl->assignThese([
					'SHIPMENT_NO'=> $ref,
					'ITEMS'=> @$model->mdata['items']
				]);
		}

		if(!empty($_POST))
		{
			$model->savePOSTMeta($_POST);
			$model->s_note = $_POST["s_note"];
			$model->save();
			echo "done";
			return null;
		}

		$this->render('operation_tab',["model"=>$model,"emailTpl"=>$emailTpl]);
	}

	public function actionFinish($id)
	{
		$model = ShipmentQuestion::model()->findByPk($id);
		$model->process_type = ShipmentQuestion::TYPE_FINISHED;
		$model->finish_user_id = User::currentUserID();
		$model->update(["process_type","finish_user_id"]);
		echo "done";
	}

	public function actionUnfinish($id)
	{
		$model = ShipmentQuestion::model()->findByPk($id);
		$model->process_type = ShipmentQuestion::TYPE_UNFINISHED;
		$model->update(["process_type"]);
		echo "done";
	}


	public function actionAddTicket()
	{
		if(isset($_POST['ShipmentQuestionSubmit']))
		{
			if(isset($_POST['ShipmentQuestionSubmit']['mhbns']))
			{
				$shipments= [];
				$model = new ShipmentQuestionSubmit();
				$model->attributes=$_POST['ShipmentQuestionSubmit'];
				$model->mhbns = $_POST['ShipmentQuestionSubmit']["mhbns"];

				if (!empty($model->mhbns)) {
						$ns = preg_split('/[\s,;]+/', trim($model->mhbns));
						if (sizeof($ns) > 200) {
							$ns = array_slice($ns, 0, 200);
						}
						$sc1 = new CDbCriteria;
						$sc1->addInCondition("hbn", $ns);
						$sc1->addInCondition("ref", $ns, 'OR');
						$sc1->addInCondition("cref", $ns, 'OR');
						$shipments = ImParcel::model()->findAll($sc1);
				}
				$model->org_id = Yii::app()->user->org;
				$model->date = date("Y-m-d H:i:s");
				$model->faq = $_POST['faq'];
				$model->type = ShipmentQuestionSubmit::SERVICEGENERATE;
				if(isset($_POST['ShipmentQuestionSubmit']['email']))
				{
					$model->email= $_POST['ShipmentQuestionSubmit']['email'];
				}
				if(isset($_POST['ShipmentQuestionSubmit']['phone']))
				{
					$model->phone= $_POST['ShipmentQuestionSubmit']['phone'];
				}

				$model->save();
				$model->refresh();
				
				if(!empty($shipments))
				{
					foreach ($shipments as $key => $shipment) {


						$shipmentQuestion = new ShipmentQuestion();
						$shipmentQuestion->shipment_id= $shipment->id;
						$shipmentQuestion->submit_id= $model->id;
						$shipmentQuestion->faq = $_POST['faq'];
						$shipmentQuestion->mdata["items"] = "";
						$shipmentQuestion->save();
						ShipmentQuestionSubmit::saveFiles($_FILES,$shipmentQuestion,true);
					}
				}else
				{
					$shipmentQuestion = new ShipmentQuestion();
					$shipmentQuestion->shipment_id= 0;
					$shipmentQuestion->submit_id= $model->id;
					$shipmentQuestion->faq =$_POST['faq'];
					$shipmentQuestion->mdata["items"] = "";
					$shipmentQuestion->save();
					ShipmentQuestionSubmit::saveFiles($_FILES,$shipmentQuestion,true);
				}



				$customerServiceService = new CustomerServiceService();
				$customerServiceService->transferCustomerServiceCaseToBeEmail($model->ticket);
				echo "done";
			}else
			{
				echo "enter required info";
			}
		}else
		{
			$model = new ShipmentQuestionSubmit();
			$this->render('add_ticket',["model"=>$model]);
		}
	}

	public function actionFaqAnswer()
	{
		if (!empty($_POST)) {
			$id = $_POST["id"];
			$faqAnswer = $_POST["faqAnswer"];
			$shipmentQuestion = ShipmentQuestion::model()->findByPk($id);
			$shipmentQuestion->faq_answer = $faqAnswer;
			$shipmentQuestion->answer_date = date("Y-m-d H:i:s");
			$shipmentQuestion->save();
		}
		echo "done";
	}		
	
	public function actionSendEmail()
	{
		$id = $_POST["id"];
		$shipmentQuestion = ShipmentQuestion::model()->findByPk($id);
		$type = $_POST["type"];
		$toEmail="";
		$files = [];
		$model = new Emailog();
		$signature = "<br>";
		if(ImportsMailSignature::haveDefaultSignature())
		{
			$signature = ImportsMailSignature::getDefaultSignature()->sig_body;
		}else
		{
			echo "setup your default signature";
			return;
		}

		if($type =="courier")
		{
			if(isset($shipmentQuestion->Shipment->trans)&&count($shipmentQuestion->Shipment->trans)>0)
			{
				$cInfo = $shipmentQuestion->Shipment->getCourierInfo();
				if(!empty($cInfo)&&!empty($cInfo[3]))
				{
					$toEmail = @Org::model()->findByPk($cInfo[3])->extra['cs_email'];
					$model->to_id = $cInfo[3];
				}

				if(empty($toEmail))
				{
					echo "this courier is not support email checking";
					return;
				}
				if(!empty($_POST['with_files']))
				{
					$fileRepos = FileRepo::model()->findAll(" type = :type and fid=:fid and status!=:status ",[":type"=>FileRepo::CUSTOMERSERVICEFILE,":fid"=>$shipmentQuestion->id,":status"=>FileRepo::DELETED]);

					foreach ($fileRepos as $key => $fr) 
					{
						$files[] = [$fr->getFile(),$fr->name];
					}
				}

			}else
			{
				echo "haven't pick up infor, please check the status of this parcel";
				return;
			}
			
		}else if($type == "customer")
		{
			$model->to_id = 0;
			if(isset($shipmentQuestion->ShipmentQuestionSubmit->email))
			{
				$toEmail = $shipmentQuestion->ShipmentQuestionSubmit->email;
			}else
			{
				if($shipmentQuestion->ShipmentQuestionSubmit->Org->email!=yii::app()->user->org->email)
				{
					$toEmail =$shipmentQuestion->ShipmentQuestionSubmit->Org->email;
				}else
				{
					return "havn't customer email";
				}
			}

			if(!empty($_POST['with_files']))
			{
				$fileRepos = FileRepo::model()->findAll(" type = :type and fid=:fid and status!=:status ",[":type"=>FileRepo::CUSTOMERSERVICEFILE,":fid"=>$shipmentQuestion->id,":status"=>FileRepo::DELETED]);

				foreach ($fileRepos as $key => $fr) 
				{
					$files[] = [$fr->getFile(),$fr->name];
				}
			}
		}else if($type =="sms")
		{
			$sms = new Sms;
			$sms->type = 30;
			$sms->no = $shipmentQuestion->ShipmentQuestionSubmit->phone;
			$sms->msg = "TLA Customer Service notice:  The status of your ticket {$shipmentQuestion->ShipmentQuestionSubmit->ticket} (parcel {$shipmentQuestion->Shipment->ref}) is updated. Please check the detail in https://ims.toplogistics.com.au/customerService/cneeShipmentQuery.app?ref={$shipmentQuestion->Shipment->ref}&&ticket={$shipmentQuestion->ShipmentQuestionSubmit->ticket}&&type=2";
			$sms->sendLocal();
			echo "done";
			return;
		}

		// $emailTemplate = EmailTpl::model()->find("slug = :slug ",[":slug"=>EmailTpl::courierParcelSearch]);
		// if($emailTemplate==null)
		// {
		// 	echo "set email template which slug equals courierParcelSearch";
		// 	return null;
		// }

		// $emailTemplate->assignSubject('SHIPMENT_NO', $shipmentQuestion->Shipment->ref);
		// $emailTemplate->assignThese([
		// 		'SHIPMENT_NO'=> $shipmentQuestion->Shipment->ref,
		// 	]);

		// $body = $emailTemplate->getContent();
		// $subject = $emailTemplate->subject;

		$iam = User::model()->findByPk(Yii::app()->user->id);
		$model->subject = $_POST['EmailTpl']['subject'];
		$model->body =$_POST['EmailTpl']['content'].$signature;
		$model->mdata['to'] = $toEmail;
		$model->mdata['from'] = 'importscs@toplogistics.com.au';
		$model->type= Emailog::SEARCH_PARCEL_EMAIL;
		$model->fid=$shipmentQuestion->Shipment->id;
		$model->scheduled = "0000-00-00 00:00:00";
		// $model->mdata['cc'] = 'importscs@toplogistics.com.au';
		$model->mdata['shipmentQuestionId'] = $shipmentQuestion->id;
		$model->mdata['user_send_email_id'] = $iam->id;
		$result = $model->customerServiceSend($files);

		$modelClone = clone $model;
		$modelClone->mdata['to'] = 'importscs@toplogistics.com.au';
		$resultClone = $modelClone->customerServiceSend($files);//fastway don't allow the email which cc itself
		if($result['status'])
		{
			$shipmentQuestion->mdata['sendEmailCourier']=true;
			$shipmentQuestion->update(["meta"]);
			// ImportsMail::createNewTicket(["from_email"=>$model->mdata['from'],"from_name"=>$model->mdata['from'],"subject"=>$model->subject,"to_email"=>$model->mdata['to'],"to_name"=>$model->mdata['to'],"plain_body"=>$model->body,"cc_email"=>"importscs@toplogistics.com.au"]);
			echo "done";
			if($resultClone['status'])
			{
				echo ",clone done";
			}
		}else
		{
			echo $result['msg'];
		}
	}

	public function actionGetImages($id)
	{
		$model=ShipmentQuestion::model()->findByPk($id);
		$this->render("images",["model"=>$model]);
	}

	public function actionGetEmails($id)
	{
		$emailIds = [0];
		$model = new ImportsMail('search');
		$model->unsetAttributes();
		if(!empty($_GET['ImportsMail']))
		{
			$model->setAttributes($_GET['ImportsMail']);
		}
		$shipmentQuestion = ShipmentQuestion::model()->findByPk($id);
		$sendEmails = Emailog::model()->findAll(' json_value(meta,"$.shipmentQuestionId") = :shipmentQuestionId ',[":shipmentQuestionId"=>$shipmentQuestion->id]);
		foreach ($sendEmails as $key => $sendEmail) {
			$relatedEmails = ImportsMail::model()->findAll(" subject like :subject",[":subject"=>"%".trim($sendEmail->subject)."%"]);
			$ids = array_column($relatedEmails, "id");
			$emailIds = array_merge($ids,$emailIds);
		}
		if(!empty($shipmentQuestion->Shipment))
		{
			$relatedEmails = ImportsMail::model()->findAll(" json_value(meta,'$.cs_ref') = :ref ",[":ref"=>$shipmentQuestion->Shipment->ref]);
			$ids = array_column($relatedEmails, "id");
			$emailIds = array_merge($ids,$emailIds);
		}

		$relatedEmails = ImportsMail::model()->findAll(" ticket = :ticket ",[":ticket"=>$shipmentQuestion->ShipmentQuestionSubmit->ticket]);
		$ids = array_column($relatedEmails, "id");
		$emailIds = array_merge($ids,$emailIds);

		$model->emailIds = $emailIds;
		$this->render("emails",["model"=>$model]);
	}

	public function actionGetEmailRelatedEmails()
	{
		$ticket = $_GET['ticket'];
		$emailIds = [0];
		$model = new ImportsMail('search');
		$model->unsetAttributes();
		if(!empty($_GET['ImportsMail']))
		{
			$model->setAttributes($_GET['ImportsMail']);
		}
		$q = ShipmentQuestionSubmit::model()->find("ticket = :ticket",[":ticket"=>$ticket]);
		$shipmentQuestions = $q->shipmentQuestion;
		foreach ($shipmentQuestions as $key => $shipmentQuestion)
		{
			$sendEmails = Emailog::model()->findAll(' json_value(meta,"$.shipmentQuestionId") = :shipmentQuestionId ',[":shipmentQuestionId"=>$shipmentQuestion->id]);
			foreach ($sendEmails as $key => $sendEmail) {
				$relatedEmails = ImportsMail::model()->findAll(" subject like :subject",[":subject"=>"%".trim($sendEmail->subject)."%"]);
				$ids = array_column($relatedEmails, "id");
				$emailIds = array_merge($ids,$emailIds);
			}

			$relatedEmails = ImportsMail::model()->findAll(" json_value(meta,'$.cs_ref') = :ref",[":ref"=>$shipmentQuestion->Shipment->ref]);
			$ids = array_column($relatedEmails, "id");
			$emailIds = array_merge($ids,$emailIds);
		}

		$relatedEmails = ImportsMail::model()->findAll(" ticket = :ticket",[":ticket"=>$q->ticket]);
		$ids = array_column($relatedEmails, "id");
		$emailIds = array_merge($ids,$emailIds);


		$model->emailIds = $emailIds;
		$this->render("emails",["model"=>$model]);
	}

	public function actionNotes($id)
	{
		$model = ShipmentQuestion::model()->findByPk($id);
		Log::add($model, Log::LOG_TYPE_NOTES, ['notes' => $_POST['notes']]);
		$this->ajaxResult($model);
	}

	public function actionLog($id)
	{
		$model = ShipmentQuestion::model()->findByPk($id);
		if ($model==null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		$this->render('log', [
			'model'=>$model,
		]);
	}

}
