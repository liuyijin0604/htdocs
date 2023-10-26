<?php 
class CustomerServiceService extends Service
{
	public function transferCustomerServiceCaseToBeEmail($ticket = false)
	{
		// if(!empty($ticket))
		// {
			$shipmentQuestionSubmits = ShipmentQuestionSubmit::model()->findAll(' ticket = :ticket and  json_value(meta,"$.is_sync_email") is null ',[':ticket'=>$ticket]);
		// }else
		// {
		// 	$shipmentQuestionSubmits = ShipmentQuestionSubmit::model()->findAll(' json_value(meta,"$.is_sync_email") is null ');
		// }

		foreach ($shipmentQuestionSubmits as $key => $q)
		{
			if(!empty($q->shipmentQuestion))
			{
				$sq = $q->shipmentQuestion[0];
				$p = $sq->shipment;
			}
			$shipmentRef="";
			if(!empty($p))
			{
				$shipmentRef = $p->ref;
			}
			$fromEmail = 'importscs@toplogistics.com.au';
			$fromEmailName = 'TLA Imports Customer Service';
			$toEmail = 'importscs@toplogistics.com.au';
			$toEmailName = 'TLA Imports Customer Service';

			if(!empty($q->email))
			{
				$toEmail = $q->email;
				$toEmailName = explode('@', $fromEmail)[0];
			}

			$items = "";
			$shipments = "";
			$phone ="";
			foreach ($q->shipmentQuestion as $key => $mysq) {
				if(!empty($mysq->mdata["items"]))
				{
					$items.=$mysq->mdata["items"]."</br>";
				}
				if(!empty($mysq->shipment))
				{
					$shipments.=$mysq->shipment->ref." ";
				}
			}
			if(!empty($shipments))
			{
				$shipments  = "<p>".$shipments." Shipments </p>";
			}

			if(!empty($q->phone))
			{
				$phone  = "<p> Phone Number:".$q->phone."</p>";
			}
			$subType = '';
			$is3PL = 0;

			$symbol = '#';

			if(!empty($p))
			{
				$is3PL = $p->is3PL();
				if(preg_match('/SKP/i', $p->ref))
				{
					$subType = $symbol.'A';
				}else if(preg_match('/(BKP|PKP)/i', $p->ref))
				{
					$subType = $symbol.'B';
				}else if(preg_match('/MKP/i', $p->ref))
				{
					$subType = $symbol.'C';
				}
				if(empty($subType))
				{
					if($is3PL)
					{
						if(!empty($p->consol_id)&&preg_match('/WDT/i',$p->consol->no))
						{
							$subType = $symbol.'E';
						}else
						{
							$subType = $symbol.'D';
						}
					}
				}
			}

			if($q->type==ShipmentQuestionSubmit::FRONTENDGENERATE)
			{
				$subType .= ' #F';
			}

			$subject = "Customer Service Ticket ".$q->ticket.": ".$shipmentRef." (".$q->faq." ".$subType.")";
			$body = "<p>Case Details:</p>".$shipments.$phone."<p>".$q->c_note."</p><p>".$items."</p>";
			$emailLog = new Emailog();
			$emailLog->fid = $q->id;
			$emailLog->type= Emailog::CUSTOMER_SERVICE;
			$emailLog->dt = date('Y-m-d H:i:s');
			$emailLog->status = 10;
			$emailLog->prepTemplate();
			$emailLog->tpl->assignThese([
				'SUBJECT'=>$subject,
				'BODY' => $body,
				'TICKET' => $q->ticket
			]);

			$emailLog->tpl->assignSubject('SUBJECT',$subject);
			$emailLog->subject = $emailLog->tpl->subject;
			$emailLog->body = $emailLog->tpl->getContent();
			$emailLog->mdata['to']=$toEmail;
			$emailLog->mdata['fromName']=$toEmailName;
			$emailLog->mdata['from']=$fromEmail;

			if(!empty($q->email))
			{
				//$emailLog->save();
				$o=$emailLog->sendEmail();
			}


			$model = new ImportsMail();
			$model->subject = $emailLog->subject;
			$model->plain_body = $emailLog->body;
			$model->mdata['to_email'] = $toEmail;
			$model->to_email = $toEmail;
			$model->to_name = $toEmailName;
			$model->create_time = $q->date;
			$model->status =  ImportsMail::STATE_NEW;
			$model->ticket = $q->ticket;
			if(!empty($p))
			{
				if($p->is3PL())
				{
					$fromEmail = '3pl@toplogistics.com.au';
					$fromEmailName = 'TLA 3PL';
					 $is3PL =1;
				}
			}
			$model->from_email = $fromEmail;
			$model->from_name = $fromEmailName;

			$model->checkTheType($is3PL);
			$model->is3pl = $is3PL;
			if($model->save())
			{
				$model->refresh();
				$model->newMailUser($is3PL);
				$q->mdata['is_sync_email'] = 1;
				$q->save();
				foreach ($q->shipmentQuestion as $key => $shipmentQuestion)
				{
					$files = FileRepo::model()->findAll('fid = :fid and type =:type',[':fid'=>$shipmentQuestion->id,":type"=>FileRepo::CUSTOMERSERVICEFILE]);
					foreach ($files as $key => $file) {
						$fileRepo = new FileRepo();
						$fileRepo->name = $file->name;
						$fileRepo->hash = $file->hash;
						$fileRepo->mime = $file->mime;
						$fileRepo->size = $file->size;
						$fileRepo->date = $file->date;
						$fileRepo->meta = $file->meta;
						$fileRepo->status = $file->status;
						$fileRepo->type = FileRepo::FILE_TYPE_IMPORT_ATTACHMENT;
						$fileRepo->fid = $model->id;
						$fileRepo->save();
					}
				}
			}else
			{
				print_r($model);
			}
		}
		return true;
	}

	public function saveContactUsToBeTicket($data)
	{
		if(empty($data['Email'])) return false;
		if(empty($data['Message'])) return false;

		$model=new ShipmentQuestionSubmit();
		$model->date = date("Y-m-d H:i:s");
		$model->faq = $data['Type'];
		$model->email = $data['Email'];
		$model->phone = $data['Phone'];
		$model->type = ShipmentQuestionSubmit::USERGENERATE;
		$model->c_note = $data['Name']." ". $data['Tracking'] ." ". $data['Message'];
		$model->save();
		$model->refresh();

		$shipmentQuestion = new ShipmentQuestion();
		$shipmentQuestion->shipment_id= 0;
		// $shipmentQuestion->c_note= "none";
		// $shipmentQuestion->s_note= "none";
		$shipmentQuestion->submit_id= $model->id;
		$shipmentQuestion->faq = $data['Type'];
		$shipmentQuestion->mdata["items"] = "";//ShipmentQuestionSubmit::getItems($_POST['ShipmentQuestion']['mdata']['items'],$model,$shipment);
		// $shipmentQuestion->faq_answer = "none";
		$shipmentQuestion->save();

		$this->transferCustomerServiceCaseToBeEmail($model->ticket);
		return true;
	}
	
}
?>