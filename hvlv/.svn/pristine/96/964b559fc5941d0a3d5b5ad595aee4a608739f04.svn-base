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
class DisputeTask extends FinanceTask
{
	public $invNo,$ref,$org_id;
	public static $my_type = 90;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return parent::relations() + array(
			'tlaCustomerDisputeLine' => [self::BELONGS_TO, 'TlaCustomerDisputeLine', 'fid']
		);
	}

	public function assignUser($userId=null)
	{
		if($userId!=null)
		{
			parent::assignUser([$userId]);
			return;
		}
		$disputeLine = $this->getLinkObj();
		
		$userIds = $disputeLine->getAssignUserIds($disputeLine->shipment);

		if(empty($userIds))
		{
			$user = User::model()->find('(occupation & :occupation)>0 and active = 1',[":occupation"=>User::ACCOUNTING_AR]);
			$userIds = [$user->id];
		}
		if(!empty($userIds))
		{
			parent::assignUser($userIds);
		}
	}

	public function getGoToLinkText()
	{
		return "Handling Dispute";
	}

	public function getTaskContent()
	{
		return "<div class='gridtext gridtext_small'>".$this->getLinkObj()->getDisplayInfo()."<div>";
		$file = Filerepo::model()->find("fid = :fid and type = :type",[":fid"=>$this->id,":type"=>Filerepo::TLATASKFILETYPE]);
		return "Dispute File: ".$file->name."</br>".$this->getLinkObj()->getDisplayInfo();
	}

	public function getOperationLink()
	{
		return Yii::app()->createURL('tlaCustomerDispute/operation')."?id=".$this->fid;
	}

	public function close()
	{
		$this->noNotice= true;
		parent::close();
	}

	public function done()
	{
		if($this->status!=TlaTask::PENDING)
		{
			$this->status = TlaTask::PENDING;
			$this->save();
		}
		foreach ($this->tlaTaskUsers as $key => $tlaTaskUser) {
			$tlaTaskUser->close();
		}

		$this->assignStatus = TlaTask::PENDING;
		$user = User::model()->find('(occupation & :occupation)>0 and active = 1',[":occupation"=>User::ACCOUNTING_AR]);
		$after7Days = date("Y-m-d 18:00:00",strtotime('+7 day',strtotime($this->ete)));
		parent::assignUserMore($user->id,$after7Days);
		$model=new Message;
		$model->msg = "Dispute Task ".$this->task_no." was ".TlaCustomerDisputeLine::$status[$this->getLinkObj()->status];
		$model->from_id = User::currentUserID();
		$model->to_id = $user->id;
		$model->status = 0;
		$model->save();
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
		$criteria->compare('t.task_no',$this->task_no,true);
		$criteria->compare('t.ets',$this->ets,true);
		$criteria->compare('t.ete',$this->ete,true);
		$criteria->compare('t.comment',$this->comment,true);
		$criteria->compare('t.dpt_id',$this->dpt_id);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.user_id',$this->user_id);
		$criteria->compare('t.create',$this->create,true);
		$criteria->compare('t.meta',$this->meta,true);
		$with = [];
		$with[] = 'tlaCustomerDisputeLine';
		$criteria->compare('tlaCustomerDisputeLine.inv_no',$this->invNo,true);
		$criteria->compare('tlaCustomerDisputeLine.ref',$this->ref,true);

		if($this->org_id)
		{
			$with = ['createUser'];
			$criteria->compare('createUser.org_id',$this->org_id);
		}

		if($this->searchingAssigned)
		{
			$with[] = 'tlaTaskUsers';
			$criteria->compare('tlaTaskUsers.user_id',User::currentUserID());
			if($this->isIms)
			{
				if(empty($this->status))
				{
					$criteria->addCondition('t.status<100');
				}
			}else
			{
				$criteria->addCondition('t.status<100');
			}

		}

		if($this->assignedUser!==False)
		{
			$with[] = 'tlaTaskUsers';
			if($this->assignedUser==0)
			{
				$criteria->addCondition('tlaTaskUsers.user_id is null');
			}else
			{
				$criteria->compare('tlaTaskUsers.user_id',$this->assignedUser);
			}

			if($this->isIms)
			{
				if(empty($this->status))
				{
					$criteria->addCondition('t.status<100');
				}
			}else
			{
				$criteria->addCondition('t.status<100');
			}
		}

		$sort = new CSort(get_called_class());
		$sort->attributes = [

			'*',
		];

		if($this->searchingAssigned)
		{
			if ($defaultOrder) {
				$sort->defaultOrder = '(IF(tlaTaskUsers.task_order>0,1,0)) DESC, tlaTaskUsers.task_order ASC, t.create DESC';
			} else {
				$sort->defaultOrder = '(IF(tlaTaskUsers.task_order>0,1,0)) DESC, tlaTaskUsers.task_order ASC';
			}
		}else
		{
			$sort->defaultOrder = 't.create DESC';
		}

		if($taskOrder)
		{
			$sort->defaultOrder = '(IF(tlaTaskUsers.task_order>0,1,0)) DESC, tlaTaskUsers.task_order ASC, t.create DESC';
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

	public function getTitle()
	{
		return "Cus Dispute ";
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
