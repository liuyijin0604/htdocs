<?php
class TlaCustomerDisputeService extends Service
{
	public function importCustomerDisputeTemplate($orgId,$userId,$file,$files,$modelName)
	{
		Yii::app()->name = "TLA";
		$data = $this->getFileData($file);
		$data = $data[0];
		unset($data[1]);
		$checkData = [];
		foreach ($data as $key => $d) {
			if(empty($d[1])) continue;
			$shipment = null;
			$inv = Invoice::model()->find('no = :no and status <10',[":no"=>$d[1]]);
			if(empty($inv))
			{
				$this->warns[] = "Line ".$key.": inv number".$d[1]." is not existed";
				continue;
			}

			if(!empty($d[2]))
			{
				$shipment = Shipment::model()->find('(hbn = :ref or ref = :ref or cref = :ref) and status <100',[":ref"=>$d[2]]);
				// if(empty($shipment))
				// {
				// 	$this->warns[] = "Line ".$key.": Shipment ".$d[2]." is not existed";
				// 	continue;
				// }
			}

				if(!is_numeric($d[3]))
				{
					$this->warns[] = "Line ".$key.": Shipment ".$d[2]." invoice amount ".$d[3]." is not numeric";
					continue;
				}

				if(!is_numeric($d[4]))
				{
					$this->warns[] = "Line ".$key.": Shipment ".$d[2]." customer amount ".$d[4]." is not numeric";
					continue;
				}

				if(!is_numeric($d[5]))
				{
					$this->warns[] = "Line ".$key.": Shipment ".$d[2]." diff ".$d[5]." is not numeric";
					continue;
				}

				if($d[5]<10)
				{
					$this->warns[] = "Line ".$key.": Shipment ".$d[2]." only dispute diff greater than 10 dollars can be disputed ";
					continue;
				}

			$d['inv_id'] = $inv->id;
			if(!empty($shipment))
			{
				$d['pid'] = $shipment->id;
			}else
			{
				$d['pid'] = 0;
			}

			$disputeLine = TlaCustomerDisputeLine::model()->find(["condition"=>"inv_id=:inv_id and inv_no=:inv_no and ref=:ref and dispute_type=:dispute_type","params"=>[":ref"=>$d[2],":inv_id"=>$d['inv_id'],":inv_no"=>$d[1],":dispute_type"=>$d[7]],"order"=>"id desc"]);
			if(!empty($disputeLine))
			{
				$this->warns[] = "Line ".$key.": Shipment ".$d[2]." was submitted at ".$disputeLine->created;
					continue;
			}

			$checkData[] = $d;

		}
		if(!empty($this->warns))
		{
			return [false,$this->warns];
		}

		if(!empty($checkData))
		{
			$tlaCustomerDispute = new TlaCustomerDispute();
			$tlaCustomerDispute->user_id = $userId;
			$tlaCustomerDispute->org_id = $orgId;
			$tlaCustomerDispute->created = date("Y-m-d H:i:s");
			if($tlaCustomerDispute->save())
			{
				$tlaCustomerDispute->refresh();
				foreach ($checkData as $key => $d) {
					$tlaCustomerDisputeLine = new TlaCustomerDisputeLine();
					$tlaCustomerDisputeLine->parent_id = $tlaCustomerDispute->id;
					$tlaCustomerDisputeLine->inv_id = $d['inv_id'];
					$tlaCustomerDisputeLine->inv_no = $d[1];
					$tlaCustomerDisputeLine->ref = $d[2];
					$tlaCustomerDisputeLine->pid = $d['pid'];
					$tlaCustomerDisputeLine->invoice_amount = $d[3];
					$tlaCustomerDisputeLine->customer_amount = $d[4];
					$tlaCustomerDisputeLine->diff = $d[5];
					$tlaCustomerDisputeLine->comment = $d[2].":".$d[6];
					$tlaCustomerDisputeLine->dispute_type = $d[7];
					$tlaCustomerDisputeLine->tla_op_comment = "";
					$tlaCustomerDisputeLine->credit_note_no = "";
					$tlaCustomerDisputeLine->credit_note_amount = 0;
					$tlaCustomerDisputeLine->save();
				}
				$this->saveFiles($files,$modelName,$tlaCustomerDispute->id,FileRepo::TLACUSTOMERDISPUTEFILETYPE);
				return [true,[],$tlaCustomerDispute];
			}
			return [false,["save failure"]];
		}

		return [false,["empty data"]];
	}


	public function updateStatus($disputeLine, $status)
	{
		$disputeLine->status  = $status;
		if($status==TlaCustomerDisputeLine::APPROVED_STATUS||$status==TlaCustomerDisputeLine::REJECTED_STATUS)
		{
			$disputeLine->handle_status = $status;
			$disputeLine->getTask()->done();
		}

		if($status==TlaCustomerDisputeLine::CLOSE_STATUS)
		{
			$disputeLine->getTask()->close();
		}
		$disputeLine->save();
	}

	public function exportDisputeTaskWithTemplate($disputeTasks)
	{
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['INV number','Shipment ref','账单金额','应收金额','差异','备注','Dispute 类型','TLA 处理意见','Credit Note number','Credit Note amount']);

		foreach ($disputeTasks as $d) {
			if($d->type==DisputeTask::$my_type)
			{
				$r = $d->getLinkObj();
				$xls->addRow($i++, [$r->inv_no, $r->ref,$r->invoice_amount,$r->customer_amount,$r->diff,$r->comment,$r->dispute_type,$r->tla_op_comment, $r->credit_note_no, $r->credit_note_amount]);
			}

		}
		$xls->output('dispute_task_current_search_export_'.time().'.xlsx');

	}
}
