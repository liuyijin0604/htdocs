<?php
class TlaTaskService extends Service
{
	public function updateStatus($tlaTask, $status)
	{
		$tlaTask = $tlaTask->getInstance();
		$update=false;
		if($tlaTask->status!=TlaTask::CLOSE&&$status==TlaTask::CLOSE)
		{
			$tlaTask->status = $status;
			$tlaTask->close();
		}

		if($tlaTask->status!=TlaTask::REJECT&&$status==TlaTask::REJECT)
		{
			$tlaTask->status = $status;
			$tlaTask->reject();
		}

		if (key_exists($status, TlaTask::$processTypes)) {
			$tlaTask->status=$status;
			$update=true;
		}

		if ($update) {
			$tlaTask->save();
			$tlaTask->refresh();
		}
	}

	public function confirmTask($tlaTask)
	{
		$tlaTaskUsers = TlaTaskUser::model()->findAll('task_id=:id',[":id"=>$tlaTask->id]);
		if(!empty($tlaTaskUsers))
		{
			foreach ($tlaTaskUsers as $key => $tlaTaskUser) {
				$tlaTaskUser->is_confirm = 1;
				$tlaTaskUser->save();
			}
		}
	}

	public function changeTag($tlaTask,$tag)
	{
		$tlaTaskUsers = TlaTaskUser::model()->count('task_id=:id and user_id=:userId',[":id"=>$tlaTask->id,":userId"=>User::currentUserID()]);
		if($tlaTaskUsers>0)
		{
			return false;
		}else
		{
			$tlaTask->tag = $tag;
			$tlaTask->save();
		}
		return true;
	}

	public function rejectClosedTask($tlaTask)
	{
		$tlaTask->rejectClosed();
	}

	public function assignTlaTaskToUser($tlaTask, $userId)
	{
		$tlaTask = $tlaTask->getInstance();
		return $tlaTask->assignUser($userId);
	}

	public function saveTlaTaskToMessage($tlaTask)
	{
		$tlaTask = $tlaTask->getInstance();
		foreach ($tlaTask->tlaTaskUsers as $key => $tlaTaskUser) {
			$model = new Message();
			$model->msg=$tlaTask->getTaskContent();
			$model->to_id = $tlaTaskUser->user_id;
			$model->from_id = Yii::app()->user->id;
			$model->status = 0;
			$model->save();
			$tlaTaskUser->is_notice = 1;
		}
	}

	public static function getUserMessageProvide()
	{
		$userId = User::currentUserID();
		$sql="SELECT (@i :=@i + 1) AS id,status,count(*) as number, (SELECT count(*) FROM `message` m WHERE DATEDIFF(NOW(),time)<=1 and m.status=mg.status and to_id = {$userId} and status!=".Message::VIEWED." ) as day1
			, (SELECT count(*) FROM `message` m WHERE  DATEDIFF(NOW(),time)<3 and DATEDIFF(NOW(),time)>1 and m.status=mg.status and to_id = {$userId} and status!=".Message::VIEWED." ) as day2
			, (SELECT count(*) FROM `message` m WHERE DATEDIFF(NOW(),time)>=3 and m.status=mg.status and to_id = {$userId} and status!=".Message::VIEWED." ) as day3
			FROM `message` mg,
			(SELECT @i := 0) AS it  WHERE to_id = {$userId} and status!=".Message::VIEWED." group by status";


		$provide=Yii::app()->db->createCommand($sql)->queryAll();
		return $provide;
	}


	public function createNewTlaTask($type,$taskNo = "",$model,$fid,$dptId,$userId,$comment,$extra=[],$files = [],$toId=false,$ete = null,$tag = null)
	{
		$tlaTask = null;
		if(!empty($taskNo))
		{
			$tlaTask = TlaTask::model()->find('task_no = :taskNo',[":taskNo"=>$taskNo]);
		}
		if(empty($tlaTask))
		{
			$tlaTask = new TlaTask();
		}
		$tlaTask->type = $type;
		$tlaTask->status = TlaTask::NEWTASK;
		$tlaTask->create = date("Y-m-d H:i:s");
		$tlaTask->task_no = $taskNo;
		$tlaTask->model = $model;
		$tlaTask->fid = $fid;
		$tlaTask->dpt_id = $dptId;
		$tlaTask->user_id = $userId;
		$tlaTask->comment = $comment;
		$tlaTask->mdata = $extra;
		if(!empty($ete))
		{
			$tlaTask->ete = $ete;
		}
		if(!empty($tag))
		{
			$tlaTask->tag = $tag;
		}
		if(!empty($extra['reoccuring']))
		{
			$tlaTask->mdata['assigned_user'] = $toId;
			$tlaTask->status = TlaTask::SCHEDULED;
			$tlaTask->save();
			return $tlaTask;
		}
		
		if($tlaTask->save())
		{
			$this->saveFiles($files,'TlaTask',$tlaTask->id,FileRepo::TLATASKFILETYPE);
			if(!empty($toId))
			{
				$this->assignTlaTaskToUser($tlaTask, $toId);
			}else
			{
				$this->assignTlaTaskByFilter($tlaTask);
			}
		}

		return $tlaTask;
	}
	public function getMyTlaTaskProvide($userId,$types=[])
	{
		$sql="SELECT (@i :=@i + 1) AS id,tg.type,tg.status,count(*) as number, (SELECT count(*) FROM `tla_task_user` m left join tla_task t on m.task_id = t.id WHERE DATEDIFF(NOW(),ete)>=-1 and t.status=tg.status and t.type=tg.type and m.user_id = {$userId} and t.status<".TlaTask::CLOSE." ) as day1
			, (SELECT count(*) FROM `tla_task_user` m left join tla_task t on m.task_id = t.id WHERE  DATEDIFF(NOW(),ete)>-3 and DATEDIFF(NOW(),ete)<-1 and t.status=tg.status and t.type=tg.type and m.user_id = {$userId} and t.status<".TlaTask::CLOSE." and m.status<100 ) as day2
			, (SELECT count(*) FROM `tla_task_user` m left join tla_task t on m.task_id = t.id WHERE DATEDIFF(NOW(),ete)<=-3 and t.status=tg.status and t.type=tg.type and m.user_id = {$userId} and t.status<".TlaTask::CLOSE." and m.status<100 ) as day3
			FROM `tla_task_user` mg left join tla_task tg on mg.task_id = tg.id ,
			(SELECT @i := 0) AS it  WHERE tg.type in (20,60,70,90) and mg.user_id = {$userId} and tg.status<".TlaTask::CLOSE." and mg.status<100 group by tg.type,tg.status order by tg.type,tg.status";

		$provide=Yii::app()->db->createCommand($sql)->queryAll();
		if(!empty($types))
		{
			foreach ($provide as $key => $value) {
				if(!in_array($value['type'], $types))
				{
					unset($provide[$key]);
				}
			}
		}
		return $provide;
	}

	public function getMyTlaTaskTabProvide($userId,$types=[],$timestamp="")
	{
		$closeStatus = TlaTask::CLOSE;
		$timestamp1 = "";
		$timestamp2 = "";
		if(!empty($timestamp))
		{
			$timestamp1 = " and tg.create > '{$timestamp}'";
			$timestamp2 = " and mg.close_time > '{$timestamp}'";
		}

		$sql="SELECT (@i :=@i + 1) AS id, (select count(tg.id) FROM `tla_task_user` mg left join tla_task tg on mg.task_id = tg.id ,
			(SELECT @i := 0) AS it  WHERE type in (20,60,70,90) and mg.user_id = {$userId} and tg.status<{$closeStatus} {$timestamp1} group by mg.user_id) as received_task, 

(SELECT count(tg.id)
			FROM `tla_task_user` mg left join tla_task tg on mg.task_id = tg.id WHERE type in (20,60,70,90) and mg.user_id = {$userId} and tg.status={$closeStatus} and mg.is_confirm=0 and mg.close_time>'2022-05-23' {$timestamp2} group by mg.user_id) as closed_task_need_confirm,

(SELECT count(tg.id)
			FROM `tla_task_user` mg left join tla_task tg on mg.task_id = tg.id   WHERE type in (20,60,70,90) and mg.user_id = {$userId} and tg.status={$closeStatus} and mg.is_confirm=1 and mg.close_time>'2022-05-23' {$timestamp2} group by mg.user_id) as closed_and_confirmed,

(SELECT count(tg.id)
			FROM `tla_task_user` mg left join tla_task tg on mg.task_id = tg.id   WHERE type in (20,60,70,90) and tg.user_id = {$userId} and tg.status={$closeStatus} and mg.is_confirm=0 and mg.close_time>'2022-05-23' {$timestamp2} group by tg.user_id) as task_need_your_confirm
            from tla_task where id>0 limit 1";
		$provide=Yii::app()->db->createCommand($sql)->queryAll();
		$mtd=$this->getTlaTaskDashBoardKPI();
		$provide[0]['id'] = $userId;
		$provide[0]['mtd'] = isset($mtd[$userId]['month_to_date'])?$mtd[$userId]['month_to_date']:0;
		$provide[0]['received_task'] = empty($provide[0]['received_task'])?0:$provide[0]['received_task'];
		$provide[0]['closed_task_need_confirm'] = empty($provide[0]['closed_task_need_confirm'])?0:$provide[0]['closed_task_need_confirm'];
		$provide[0]['closed_and_confirmed'] = empty($provide[0]['closed_and_confirmed'])?0:$provide[0]['closed_and_confirmed'];
		$provide[0]['task_need_your_confirm'] = empty($provide[0]['task_need_your_confirm'])?0:$provide[0]['task_need_your_confirm'];

		return $provide;
	}


	public function getTlaTaskDashBoardKPI($refresh = false)
	{
		$kpiService = new KpiService();
		$cache = $this->getCacheData("TodayTaskData");
		if(!empty($cache)&&$refresh==false)
		{
			return $cache;
		}

		$sql="select distinct(user_id) as user_id from tla_task_user";
		$userArr=Yii::app()->db->createCommand($sql)->queryAll();
		if(empty($userArr))
		{
			return [];
		}
		$userIds = array_column($userArr, 'user_id');

		$provide = [];
		$index=0;
		$kpiResult = $kpiService->calculateMonthTaskKPI(date("Y-m"));
		foreach ($userIds as $key => $userId)
		{
			$result = [];
			$user = User::model()->find("id = :id and active =1",[":id"=>$userId]);
			if(empty($user)) continue;
			$log = Log::model()->find('model = "User" AND lid = :lid AND type = 3', [':lid' => $userId]);
			if(!empty($log))
			{
				$user_create = $log->time;
			}else
			{
				$user_create = '2000-01-01 00:00:00';
			}

			$str1 = " and (m.create_time>= '{$user_create}' and t.type != 10) ";
			$str2 = " and (mg.create_time>= '{$user_create}' and tg.type != 10)";

			$sql="SELECT (SELECT count(*) as today_left FROM `tla_task_user` mg left join tla_task tg on mg.task_id = tg.id left join user u on mg.user_id = u.id WHERE tg.status not in (90,100,101) {$str2} and mg.user_id={$userId}  group by mg.user_id) as today_left,

			(SELECT count(*) as number FROM `tla_task_user` mg left join tla_task tg on mg.task_id = tg.id left join user u on mg.user_id = u.id WHERE tg.status not in (101) {$str2} and mg.user_id={$userId}  group by mg.user_id) as `total_task`,

			(SELECT count(*) as number FROM `tla_task_user` mg left join tla_task tg on mg.task_id = tg.id left join user u on mg.user_id = u.id WHERE tg.status in (90,100) {$str2} and mg.user_id={$userId}  group by mg.user_id) as `close`,

			(SELECT count(tg.id)
			FROM `tla_task_user` mg left join tla_task tg on mg.task_id = tg.id WHERE type in (20,60,70,90) and mg.user_id = {$userId} and tg.status=100 and mg.is_confirm=0 and mg.close_time>'2022-05-23' group by mg.user_id) as closed_task_need_confirm,

			(SELECT count(tg.id)
						FROM `tla_task_user` mg left join tla_task tg on mg.task_id = tg.id   WHERE type in (20,60,70,90) and mg.user_id = {$userId} and tg.status=100 and mg.is_confirm=1 and mg.close_time>'2022-05-23' group by mg.user_id) as closed_and_confirmed,

			(SELECT count(tg.id)
						FROM `tla_task_user` mg left join tla_task tg on mg.task_id = tg.id   WHERE type in (20,60,70,90) and tg.user_id = {$userId} and tg.status=100 and mg.is_confirm=0 and mg.close_time>'2022-05-23' group by tg.user_id) as task_need_your_confirm


			FROM `tla_task_user` where user_id={$userId} limit 1";

			$records=Yii::app()->db->createCommand($sql)->queryAll();
			$warehouse = Org::model()->findByPk($user->dpt_id);
			$name = @$user->fname." ".@$user->lname;
			if(!empty($warehouse))
			{
				$warehouseName = explode(" ", $warehouse->name)[0];
				$name = @$user->fname." ".@$user->lname."(".$warehouseName.")";
			}
			// 总数量，还有多少没做，做完了多少个。    本月完成率
			$result = ["id"=>$userId,"user_id"=>$userId,"name"=>$name,"total_task"=>0,"today_left"=>0,"close"=>0,"close_percent"=>0,"month_to_date"=>0,"closed_task_need_confirm"=>0,"closed_and_confirmed"=>0,"task_need_your_confirm"=>0];
			if(!empty($records))
			{
				$result['total_task'] = empty($records[0]['total_task'])?0:$records[0]['total_task'];
				$result['today_left'] = empty($records[0]['today_left'])?0:$records[0]['today_left'];
				$result['close'] = empty($records[0]['close'])?0:$records[0]['close'];
				if(!empty($result['total_task']))
				{
					$result['close_percent'] = $result['close']/$result['total_task'];
				}else
				{
					$result['close_percent'] = 0;
				}

				$result['closed_task_need_confirm'] = empty($records[0]['closed_task_need_confirm'])?0:$records[0]['closed_task_need_confirm'];
				$result['closed_and_confirmed'] = empty($records[0]['closed_and_confirmed'])?0:$records[0]['closed_and_confirmed'];
				$result['task_need_your_confirm'] = empty($records[0]['task_need_your_confirm'])?0:$records[0]['task_need_your_confirm'];
			}
			$result['month_to_date'] = isset($kpiResult[$userId])?$kpiResult[$userId][2]:0;
			$provide[$userId] = $result;
		}

		$this->setCacheData("TodayTaskData",$provide,600);
		return $provide;


		// $sql="SELECT (@i :=@i + 1) AS id,mg.user_id,CONCAT(u.fname,' ',u.lname) as name,tg.type,mg.status,count(*) as number, (SELECT count(*) FROM `tla_task_user` m left join tla_task t on m.task_id = t.id WHERE DATEDIFF(NOW(),create_time)<=1 and m.status=mg.status and t.type=tg.type and m.user_id = mg.user_id and m.status!=".TlaTask::CLOSE." ) as day1
		// 	, (SELECT count(*) FROM `tla_task_user` m left join tla_task t on m.task_id = t.id WHERE  DATEDIFF(NOW(),create_time)<3 and DATEDIFF(NOW(),create_time)>1 and m.status=mg.status and t.type=tg.type and m.user_id = mg.user_id and m.status!=".TlaTask::CLOSE." ) as day2
		// 	, (SELECT count(*) FROM `tla_task_user` m left join tla_task t on m.task_id = t.id WHERE DATEDIFF(NOW(),create_time)>=3 and m.status=mg.status and t.type=tg.type and m.user_id = mg.user_id and m.status!=".TlaTask::CLOSE." ) as day3
		// 	FROM `tla_task_user` mg left join tla_task tg on mg.task_id = tg.id left join user u on mg.user_id = u.id,
		// 	(SELECT @i := 0) AS it  WHERE mg.status!=".TlaTask::CLOSE." group by mg.user_id,tg.type,mg.status order by tg.type,mg.status";

		// $provide=Yii::app()->db->createCommand($sql)->queryAll();
		// if(!empty($types))
		// {
		// 	foreach ($provide as $key => $value) {
		// 		if(!in_array($value['type'], $types))
		// 		{
		// 			unset($provide[$key]);
		// 		}
		// 	}
		// }
		// return $provide;
	}

	public function getMyCreateTlaTaskProvide($userId)
	{
		$userId = User::currentUserID();
		$sql="SELECT 0 as id,t.* from (SELECT mg.type,mg.status,count(*) as number, (SELECT count(*) FROM tla_task m WHERE DATEDIFF(NOW(),`create`)<=1 and m.status=mg.status and m.type=mg.type and m.user_id = {$userId} and m.status!=".TlaTask::CLOSE." ) as day1
			, (SELECT count(*) FROM `tla_task` m WHERE  DATEDIFF(NOW(),`create`)<3 and DATEDIFF(NOW(),`create`)>1 and m.status=mg.status and m.type=mg.type and m.user_id = {$userId} and m.status!=".TlaTask::CLOSE." ) as day2
			, (SELECT count(*) FROM `tla_task` m WHERE DATEDIFF(NOW(),`create`)>=3 and m.status=mg.status and m.type=mg.type and m.user_id = {$userId} and m.status!=".TlaTask::CLOSE." ) as day3
			FROM `tla_task` mg WHERE mg.user_id = {$userId} and mg.status!=".TlaTask::CLOSE." group by mg.type,mg.status) t";
		$provide=Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($provide as $key => $value) {
			$provide[$key]['id'] = $key+1;
		}
		return $provide;
	}

	public function getTlaTaskFilter($userId)
	{
		
	}

	public function assignTlaTaskByFilter($tlaTask)
	{
		$tlaTask->getInstance()->assignUser();
	}

	public function saveEmailToBeEmailTask($email)
	{
		foreach ($email->mail_users as $key => $mailUser) {
			$this->createNewTlaTask(EmailTask::$my_type,$email->no,get_class($email),$email->id,$mailUser->user->dpt_id,User::currentUserID(),"",[],[],$mailUser->user->id);
		}
	}

	public function getMyTlaTaskListTabNotice($userId,$timestamp)
	{
		$provide = $this->getMyTlaTaskTabProvide($userId,[],$timestamp);
		$timestamp = date("Y-m-d H:i:s");
		if(empty($provide))
		{
			return [0,0,0,$timestamp];
		}

		return [$provide[0]['received_task'],$provide[0]['task_need_your_confirm'],$provide[0]['closed_task_need_confirm'],$timestamp];
	}

	public function transferMqProcessToBeReportTask($mqProcess)
	{
		$this->createNewTlaTask(ReportTask::$my_type,"","MqProcess",$mqProcess->id,Org::TLA_DEPARTMENT_SYDNEY,0,"",[],[],User::currentUserID(),"0000-00-00 00:00:00");
	}

	public function createDisputeType($content,$dptId,$agentId,$invoiceType,$shipmentType,$userId)
	{
		$csFaq = new CsFaq();
		$csFaq->status=CsFaq::dispute_type;
		$csFaq->type=CsFaq::dispute_type_type;
		$csFaq->content = $content;
		$csFaq->frontend_content = $content;
		$csFaq->dpt_id = $dptId;
		$csFaq->agent_id = $agentId;
		$csFaq->invoice_type = $invoiceType;
		$csFaq->shipment_type = $shipmentType;
		$csFaq->user_id = User::currentUserID();
		$csFaq->save();
		$cs = new CsFaqUserMap();
		$cs->cs_faq_id = $csFaq->id;
		$cs->user_id = $userId;
		$cs->save();
		return $csFaq;
	}

	public function assignDisputeTypeToUser($csFaq,$userId)
	{
		if(empty($csFaq->assignUser))
		{
			$cs = new CsFaqUserMap();
			$cs->cs_faq_id = $csFaq->id;
			$cs->user_id = $userId;
			$cs->save();
		}else
		{
			$csFaq->assignUser->user_id = $userId;
			$csFaq->assignUser->save();
		}
	}

}
