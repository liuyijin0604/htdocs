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
class DisputeSupplierTask extends FinanceTask
{
	public $invNo,$ref;
	public static $my_type = 100;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return parent::relations() + array(
			'siReconcile' => [self::BELONGS_TO, 'SiReconcile', 'fid']
		);
	}

	public function assignUser($userId=null)
	{
		parent::assignUser([$userId]);
	}

	public function getGoToLinkText()
	{
		return "Check Si Reconcile";
	}

	public function getTaskContent()
	{
		$taskContent = "Dispute Supplier Invoice: ".$this->getLinkObj()->parent()->inv_no;
		$creditnotes = "";
		if(!empty($this->getLinkObj()->mdata['disputeCreditNote']))
		{
			$creditnotes = "</br>Credit Notes:";
			foreach ($this->getLinkObj()->mdata['disputeCreditNote'] as $no => $value) {
				foreach ($value as $vk => $v) {
					$creditnotes .= "</br>{$vk}: \${$v}";
				}
			}
		}
		return $taskContent.$creditnotes;
	}

	public function getOperationLink()
	{
		return Yii::app()->createURL('siReconcile/getReconcileDpmt')."?id=".$this->fid;
	}

	public function close()
	{
		parent::close();
	}

	public function done()
	{
		foreach ($this->tlaTaskUsers as $key => $tlaTaskUser) {
			$tlaTaskUser->close();
			$model=new Message;
			$model->msg = "Dispute Supplier Task ".$this->task_no." was ".TlaTask::$processTypes[$this->status];
			$model->from_id = User::currentUserID();
			$model->to_id = $tlaTaskUser->user_id;
			$model->status = 0;
			$model->save();
		}
	}

	

	public function getTitle()
	{
		return "Cus Dispute ";
	}

	public function getDisputeAmount()
	{
		if(isset($this->mdata['disputeAmount']))
		{
			return $this->mdata['disputeAmount'];
		}else
		{
			$amount = 0;
			$dls = DisputeLine::model()->findAll('dispute_id = :dispute_id', [':dispute_id' => $this->mdata['dispute_id']]);
			if(!empty($dls))
			{
				foreach ($dls as $dl) {
					$amount += $dl->dispute_amount_ex_gst;
				}
				$this->mdata['disputeAmount'] = $amount;
				$this->updateMeta();
			}
			return $amount;
		}
	}

	public function getCreditAmount()
	{
		if(isset($this->mdata['disputeLeast']))
		{
			return number_format($this->getDisputeAmount()-$this->mdata['disputeLeast'],4,'.','');
		}else
		{
			return 0;
		}
	}

	public function getSupplierName()
	{
		return $this->getLinkObj()->supplier->name;
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
