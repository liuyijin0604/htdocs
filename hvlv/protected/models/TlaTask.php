<?php
/**
 * This is the model class for table "tla_task".
 *
 * The followings are the available columns in table 'tla_task':
 * @property integer $id
 * @property integer $type
 * @property string $task_no
 * @property string $comment
 * @property integer $dpt_id
 * @property integer $status
 * @property integer $user_id
 * @property string $create
 * @property string $meta
 */
class TlaTask extends MetaModel
{
	public $mdata = [];
	public static $my_type = 0;
	const NEWTASK = 10;
	const REJECT = 5;
	const CLOSE = 100;
	const DONE = 90;
	const PENDING = 90;
	const PROCESSING = 20;
	const WAREHOUSETASK = 60;
	const SCHEDULED = 110;
	const WEEKLY = 1;
	const MONTHLY = 2;
	const FORTNIGHTLY = 3;
	const DAILY = 4;
	const DELETED = 101;
	public $thisObj = null;
	public $files;
	public $linkObj = null;
	public $nolog = false;
	public $searchingAssigned = false;
	public $assignedUser = false;
	public $isIms = false;
	public $noNotice = false;
	public $assignStatus;
	public $isTotal = false;
	public $closedByAssignedUser = false;
	public $assignedUserName = null;
	public $createdUserName = null;
	public $customer = null;
	public $no_type = null;
	public static $types = [
		10 => 'EmailTask',
		20 => 'NoteTask',
		30 => 'CargoProcessTask',
		40 => 'CustomProcessTask',
		50 => 'CustomerServiceTask',
		60 => 'WarehouseTask',
		70 => 'ITTask',
		80 => 'FinanceTask',
		90 => 'DisputeTask',
		100 => 'DisputeSupplierTask',
		110 => 'ProcessTask',
		120 => 'ReportTask'
	];

	public static $reoccuringList = [
		1 => "weekly",
		2 => "monthly",
		3 => "fortnightly",
		4 => "daily"
	];

	public static $typesForCreate = [
		20 => 'NoteTask',
		60 => 'WarehouseTask',
		70 => 'ITTask'
	];

	public static $typesForTask = [
		10 => 'EM',
		20 => 'NT',
		30 => 'CAP',
		40 => 'CUP',
		50 => 'CUS',
		60 => 'WH',
		70 => 'IT',
		80 => 'FI',
		90 => 'DIS',
		100 => 'DSP',
		110 => 'PS',
		120 => 'RP'
	];

	public static $processTypes = [
		5=>'Reject',
		10 => 'New',
		20 => 'Processing',
		90 => 'Pending',
		100 =>'Close',
		101 =>'Deleted',
		110 =>'Scheduled'
	];

	public static $processTypesColor = [
		5 => 'black',
		10 => 'red',
		20 => 'orange',
		90 => 'blue',
		100 =>'green',
		110 =>'black'
	];

	public static $tags = [
		0=>'',
		2 => 'Important',
		4 => 'Urgent(<4 Hours)',
		8 => 'Normal'
	];

	public static $tag_point = [
		0=>1,
		2 => 40,
		4 => 2,
		8 => 1
	];

	public static $tags2 = [
		0=>'',
		4 => 'Urgent(<4 Hours)',
		8 => 'Normal'
	];

	public function __construct($scenario='insert')
	{
		parent::__construct($scenario);
		if (static::$my_type > 0) {
			$this->type = static::$my_type;
		}
	}


	public function unsetAttributes($names = null)
	{
		parent::unsetAttributes($names = null);
		if (static::$my_type > 0) {
			$this->type = static::$my_type;
		}
	}

	public function getLinkObj()
	{
		if(!empty($this->linkObj))
		{
			return $this->linkObj;
		}else
		{
			if(!empty($this->model)&&!empty($this->fid))
			{
				$this->linkObj = $this->model::model()->findByPk($this->fid);
				return $this->linkObj;
			}
		}
	}

	public function getTitle()
	{
		return "Task Obj ";
	}

	public function getStatus()
	{
		return isset(static::$processTypes[$this->status])? Yii::t(strtolower(__CLASS__), static::$processTypes[$this->status]) : $this->status;
	}

	public function getTag()
	{
		return isset(static::$tags[$this->tag])? Yii::t(strtolower(__CLASS__), static::$tags[$this->tag]) : $this->tag;
	}

	public function getStatusForList()
	{
		return "<font style='font-size:2em;color:".static::$processTypesColor[$this->status]."'>●</font>";
	}

	public function assignUser($userIds)
	{
		$status = TlaTask::NEWTASK;
		if(!empty($this->assignStatus))
		{
			$status = $this->assignStatus;
		}
		if(empty($this->user_id)&&!empty(User::currentUserID()))
		{
			$this->user_id = User::currentUserID();
			$this->save();
		}
		TlaTaskUser::model()->deleteAll('task_id = :taskId and status!=100',[":taskId"=>$this->id]);
		if(empty($userIds))
		{
			return true;
		}
		if(!is_array($userIds))
		{
			$userIds = [$userIds];
		}

		foreach ($userIds as $key => $userId) {
			
			$check = TlaTaskUser::model()->count('user_id = :userId and task_id = :taskId',[":userId"=>$userId,":taskId"=>$this->id]);
			if($check==0)
			{
				$tlaTaskUser = new TlaTaskUser();
				$tlaTaskUser->user_id = $userId;
				$tlaTaskUser->task_id = $this->id;
				$tlaTaskUser->create_time = date("Y-m-d H:i:s");
				$tlaTaskUser->status = $status;
				$tlaTaskUser->create_user_id = User::currentUserID();
				$tlaTaskUser->save();
				$u1= User::model()->findByPk(User::currentUserID());
				$u1name = empty(User::currentUserID())?"System":$u1->fname." ".$u1->lname;
				$u2= User::model()->findByPk($userId);
				TlaLog::add(TlaTask::model()->findByPk($this->id), $this->isNewRecord? 3 : 4, array_merge(['status' => $this->getStatus()], ["assign"=>"from ".$u1name." to ".$u2->fname." ".$u2->lname]));
			}

		}
		return true;
	}
	
	public function assignUserMore($userId,$ete)
	{
		$status = TlaTask::NEWTASK;
		if(!empty($this->assignStatus))
		{
			$status = $this->assignStatus;
		}
		if(empty($this->user_id)&&!empty(User::currentUserID()))
		{
			$this->user_id = User::currentUserID();
			$this->save();
		}

		$tlaTaskUser = TlaTaskUser::model()->find('task_id = :taskId and user_id =:userId and status!=100',[":taskId"=>$this->id,":userId"=>$userId]);
		if(empty($userId))
		{
			return true;
		}

		if(empty($tlaTaskUser))
		{
			$tlaTaskUser = new TlaTaskUser();
			$tlaTaskUser->user_id = $userId;
			$tlaTaskUser->task_id = $this->id;
			$tlaTaskUser->create_time = date("Y-m-d H:i:s");
			$tlaTaskUser->status = $status;
			$tlaTaskUser->create_user_id = User::currentUserID();
			$tlaTaskUser->ete_bk = $ete;
			$tlaTaskUser->save();
			$u1= User::model()->findByPk(User::currentUserID());
			$u1name = empty(User::currentUserID())?"System":$u1->fname." ".$u1->lname;
			$u2= User::model()->findByPk($userId);
			TlaLog::add(TlaTask::model()->findByPk($this->id), $this->isNewRecord? 3 : 4, array_merge(['status' => $this->getStatus()], ["assign"=>"from ".$u1name." to ".$u2->fname." ".$u2->lname]));
		}

		return true;
	}
	
	public function close()
	{
		if($this->status!=TlaTask::CLOSE)
		{
			$this->status = TlaTask::CLOSE;
			$this->save();
		}
		foreach ($this->tlaTaskUsers as $key => $tlaTaskUser) {
			if($tlaTaskUser->status !=TlaTask::CLOSE)
			{
				$tlaTaskUser->close_time = date("Y-m-d H:i:s");
				$tlaTaskUser->status = TlaTask::CLOSE;
				$tlaTaskUser->save();
			}
		}
		if(!empty($this->user_id)&&$this->noNotice==false)
		{
			$model=new Message;
			$model->msg = "Task ".$this->task_no." was closed. Comment:".$this->comment.".";
			$model->from_id = User::currentUserID();
			$model->to_id = $this->user_id;
			$model->status = 0;
			$model->save();
		}
	}

	public function rejectClosed()
	{
		if($this->status!=TlaTask::PROCESSING)
		{
			$this->status = TlaTask::PROCESSING;
			$this->save();
		}

		foreach ($this->tlaTaskUsers as $key => $tlaTaskUser) {
			$tlaTaskUser->user_id = $this->user_id;
			$tlaTaskUser->status = TlaTask::PROCESSING;
			$tlaTaskUser->save();
		}

		if(!empty($this->user_id)&&$this->noNotice==false)
		{
			$model=new Message;
			$model->msg = "Task ".$this->task_no." was not confirmed. Reject Reason:".$this->mdata['reject_reason'].".";
			$model->from_id = User::currentUserID();
			$model->to_id = $this->user_id;
			$model->status = 0;
			$model->save();
		}
	}

	public function reject()
	{
		if($this->status!=TlaTask::REJECT)
		{
			$this->status = TlaTask::REJECT;
			$this->save();
		}

		foreach ($this->tlaTaskUsers as $key => $tlaTaskUser) {
			$tlaTaskUser->user_id = $this->user_id;
			$tlaTaskUser->status = TlaTask::REJECT;
			$tlaTaskUser->save();
		}

		if(!empty($this->user_id)&&$this->noNotice==false)
		{
			$model=new Message;
			$model->msg = "Task ".$this->task_no." was reject. Reject Reason:".$this->mdata['reject_reason'].".";
			$model->from_id = User::currentUserID();
			$model->to_id = $this->user_id;
			$model->status = 0;
			$model->save();
		}
	}


	public function getType()
	{
		return isset(static::$types[$this->type])? Yii::t(strtolower(__CLASS__), static::$types[$this->type]) : $this->type;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tla_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, dpt_id, status, user_id, create', 'required'),
			array('type, dpt_id, status, user_id', 'numerical', 'integerOnly'=>true),
			array('task_no', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, task_no, comment, dpt_id, status, user_id, create, meta,ets,ete,assignedUserName,createdUserName,tag,task_content,customer', 'safe', 'on'=>'search'),
		);
	}

	public function beforeSave()
	{
		if (!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		return parent::beforeSave();
	}

	public function afterFind()
	{
		if(empty($this->task_no))
		{
			$this->task_no = strtoupper(self::$typesForTask[$this->type]).sprintf('%08s', $this->id);
			$this->nolog = true;
			if (!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
			$this->update(['task_no']);
		}

		if(static::$my_type==0&&$this->getInstance()->getTaskContent()!=$this->task_content)
		{
			$this->task_content = $this->getInstance()->getTaskContent();
			if (!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
			$this->update(['task_content']);
		}
		
		return parent::afterFind();
	}

	public function afterSave()
	{
		//add log
		if (!empty($this)&&!$this->nolog) {
			$extra=[];

			
			TlaLog::add($this, $this->isNewRecord? 3 : 4, array_merge(['status' => $this->getStatus()], $extra));
		}
		return 	parent::afterSave();
	}
	public function log($action)
	{
		$extra =["action"=>$action];
		TlaLog::add($this, $this->isNewRecord? 3 : 4, array_merge(['status' => $this->getStatus()], $extra));
	}



	public function getInstance()
	{
		if(empty($this->thisObj))
		{
			$this->thisObj = self::$types[$this->type]::model()->findByPk($this->id);

		}
		return $this->thisObj;
	}


	public function getAssignUsers()
	{
		$userName = [];
		foreach ($this->tlaTaskUsers as $key => $tlaTaskUser) {
			$userName[$tlaTaskUser->user_id]=empty($tlaTaskUser->user_id)?"System":($tlaTaskUser->user->fname." ".$tlaTaskUser->user->lname);
		}
		return join(',',$userName);
	}

	public function getGoToLinkText()
	{
		return "";
	}
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'createUser' => [self::BELONGS_TO, 'User', 'user_id'],
			'tlaTaskUsers' => [self::HAS_MANY, 'TlaTaskUser', 'task_id'],
			'myTlaTaskUser' => [self::HAS_ONE, 'TlaTaskUser', 'task_id','on' => "myTlaTaskUser.user_id = ".User::currentUserID()],
			'agent' => [self::BELONGS_TO, 'Org', 'agent_id']
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'task_no' => 'Task No',
			'comment' => 'comment',
			'dpt_id' => 'Dpt',
			'status' => 'Status',
			'user_id' => 'Created By',
			'create' => 'Create',
			'meta' => 'Meta',
			'ets' => 'Start Date',
			'ete' => 'Deadline',
			'assignedUserName'=>'Assign User',
			'createdUserName'=>'Create User',
			'tag'=>'Tag'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search($pgn = true, $ps = 30, $ec = false, $defaultOrder = true,$taskOrder = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.type',$this->type);
		$criteria->compare('task_no',$this->task_no,true);
		$criteria->compare('ets',$this->ets,true);
		$criteria->compare('t.dpt_id',$this->dpt_id);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.tag',$this->tag);
		$criteria->compare('t.user_id',$this->user_id);
		$criteria->compare('t.create',$this->create,true);
		$criteria->compare('t.meta',$this->meta,true);
		$criteria->compare('t.task_content',$this->task_content,true);
		$with = [];

		if($this->type==DisputeSupplierTask::$my_type)
		{
			$criteria->join = ' join `hvlv_top`.si_reconcile `siReconcile` on `siReconcile`.id = `t`.`fid` and `t`.`model`="siReconcile" join `hvlv_top`.supplier_invoice `parent` on `siReconcile`.supplier_invoice_id = `parent`.`id` ';

			$criteria->addCondition("siReconcile.meta like '%{$this->comment}%' or parent.inv_no like '%{$this->comment}%' or t.comment like '%{$this->comment}%'");
		}else
		{
			$criteria->compare('t.comment',$this->comment,true);
		}
		if($this->searchingAssigned)
		{
			$with[] = 'tlaTaskUsers';
			$criteria->compare('tlaTaskUsers.user_id',User::currentUserID());
			if($this->isIms)
			{
				if(empty($this->status))
				{
					$criteria->addCondition('t.status<100 and (tlaTaskUsers.status<100 or tlaTaskUsers.id is null)');
				}
			}else
			{
				$criteria->addCondition('t.status<100 and (tlaTaskUsers.status<100 or tlaTaskUsers.id is null)');
			}

			if(!$this->isTotal)
			{
				$log = Log::model()->find('model = "User" AND lid = :lid AND type = 3', [':lid' => User::currentUserID()]);
				if(!empty($log))
				{
					$user_create = $log->time;
				}else
				{
					$user_create = '2000-01-01 00:00:00';
				}

				$criteria->addCondition(" ((tlaTaskUsers.create_time>= '{$user_create}' and t.type = 10) or t.type !=10) ");
			}


		}
		if($this->closedByAssignedUser!==false)
		{
			$criteria->compare('t.type',[NoteTask::$my_type,ITTask::$my_type,WarehouseTask::$my_type]);
			$with[] = 'tlaTaskUsers';
			$criteria->addCondition('tlaTaskUsers.is_confirm = 0 and tlaTaskUsers.close_time>"2022-05-23"');
		}
		if($this->assignedUser!==false)
		{
			$with[] = 'tlaTaskUsers';
			if($this->assignedUser==0)
			{
				$criteria->addCondition('tlaTaskUsers.user_id is null');
			}else
			{
				$criteria->compare('tlaTaskUsers.user_id',$this->assignedUser);
				if(!$this->isTotal)
				{
					$log = Log::model()->find('model = "User" AND lid = :lid AND type = 3', [':lid' => $this->assignedUser]);
					if(!empty($log))
					{
						$user_create = $log->time;
					}else
					{
						$user_create = '2000-01-01 00:00:00';
					}

					$criteria->addCondition(" ((tlaTaskUsers.create_time>= '{$user_create}' and t.type = 10) or t.type !=10) ");
				}

			}

			if($this->isIms)
			{
				if(empty($this->status))
				{
					$criteria->addCondition('t.status<100  and (tlaTaskUsers.status<100 or tlaTaskUsers.id is null)');
				}
			}else
			{
				$criteria->addCondition('t.status<100 and (tlaTaskUsers.status<100 or tlaTaskUsers.id is null)');
			}


		}

		if($this->assignedUserName!==false)
		{
			$with[] = 'tlaTaskUsers';
			$with[] = 'tlaTaskUsers.user';
			$criteria->compare('user.fname',$this->assignedUserName,true);
		}

		if($this->createdUserName!==false)
		{
			$with[] = 'createUser';
			$criteria->compare('createUser.fname',$this->createdUserName,true);
		}

		if(!empty($this->ete))
		{
			$dateRange = explode('~', $this->ete);
			if(count($dateRange)>1)
			{

					$c2 = count(explode("-",$dateRange[1]));
					if($c2==1)
					{
						$dateRange[1] = date("Y",strtotime('+1 year',strtotime($dateRange[1])));
					}elseif($c2==2)
					{
						$dateRange[1] = date("Y-m",strtotime('+1 month',strtotime($dateRange[1])));
					}
					else
					{
						$dateRange[1] = date("Y-m-d",strtotime('+1 day',strtotime($dateRange[1])));
					}
			}

			if(in_array('tlaTaskUsers', $with))
			{
				if(count($dateRange)>1)
				{
					$criteria->addCondition('(t.ete >= "'.$dateRange[0].'" and t.ete <= "'.$dateRange[1].'") or (tlaTaskUsers.ete_bk >= "'.$dateRange[0].'" and tlaTaskUsers.ete_bk <= "'.$dateRange[1].'")');
				}else
				{
					$criteria->addCondition('t.ete like "%'.$this->ete.'%" or tlaTaskUsers.ete_bk like "%'.$this->ete.'%"');
				}
			}else
			{
				$dateRange = explode('~', $this->ete);
				if(count($dateRange)>1)
				{
					$criteria->addCondition('t.ete >= "'.$dateRange[0].'" and t.ete <= "'.$dateRange[1].'"');
				}else
				{
					$criteria->addCondition('t.ete like "%'.$this->ete.'%"');
				}

			}
		}

		if(!empty($this->customer))
		{
			$with[] = 'agent';
			$criteria->compare('agent.name',$this->customer,true);
		}

		if(!empty($this->no_type))
		{
			$criteria->addCondition('t.type not in ('.join(',',$this->no_type).')');
		}

		$criteria->addCondition("t.type != 10");
		$sort = new CSort(get_called_class());
		$sort->attributes = [

			'*',
			'tlaTaskUsers.close_time' => [
				'asc' => 'tlaTaskUsers.close_time ASC',
				'desc' => 'tlaTaskUsers.close_time DESC',
			]
		];

		if($this->searchingAssigned||$this->assignedUser)
		{
			if ($defaultOrder) {
				$sort->defaultOrder = '(IF(tlaTaskUsers.task_order>0,1,0)) DESC,IF(t.ete="0000-00-00 00:00:00", 999,(to_days(t.ete)-to_days(NOW()))) ASC';
			} else {
				$sort->defaultOrder = '(IF(tlaTaskUsers.task_order>0,1,0)) DESC, tlaTaskUsers.task_order ASC';
			}
		}else
		{
			$sort->defaultOrder = 't.create DESC';
		}

		if($taskOrder)
		{
			$sort->defaultOrder = '(IF(tlaTaskUsers.task_order>0,1,0)) DESC,IF(t.ete="0000-00-00 00:00:00", 999,(to_days(t.ete)-to_days(NOW()))) ASC';
		}

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}
		// in case sub-gridview, we need consol_id set by parent grid view
		$pagerparams = $_GET;

		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
			'sort' => $sort,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		]);
	}

	public function getCustomerName(){
		if(empty($this->mdata['agent_id'])){
			return "";
		}else{
			return Org::model()->findByPk($this->mdata['agent_id'])->name;
		}
	}

	public function showComment()
	{

		return "<div class='gridtext gridtext_medium'>".preg_replace("/\r/i", "</br>", $this->comment)."<div>";
	}

	public function getMyEte($isHtml = false)
	{
		foreach ($this->tlaTaskUsers as $key => $u) {
			if(!empty($u->ete_bk)&&$u->ete_bk!="0000-00-00 00:00:00")
			{
				if($isHtml)
				{
					if(strtotime(date("Y-m-d",strtotime($u->ete_bk)))-strtotime(date("Y-m-d"))<=0)
					{
						return "<span class='warn_red'>".$u->ete_bk."</span>";
					}else
					{
						return $u->ete_bk;
					}
				}else
				{
					return $u->ete_bk;
				}
			}
		}
		if($isHtml)
		{
			if(strtotime(date("Y-m-d",strtotime($this->ete)))-strtotime(date("Y-m-d"))<=0)
			{
				return "<span class='warn_red'>".$this->ete."</span>";
			}else
			{
				return $this->ete;
			}
		}else
		{
			return $this->ete;
		}
	}

	public function getPoints()
	{
		if($this->type==ITTask::$my_type)
		{
			if($this->tag==2)//Important IT task
			{
				self::$tag_point[$this->tag];
			}else
			{
				return 8;
			}
			
		}

		if($this->type==NoteTask::$my_type)
		{
			if($this->tag==4)//Urgent Note task
			{
				return self::$tag_point[$this->tag];
			}
			return 1;
		}
		return self::$tag_point[$this->tag];

	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TlaTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
