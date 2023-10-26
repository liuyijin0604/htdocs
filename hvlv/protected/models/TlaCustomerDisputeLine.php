<?php

/**
 * This is the model class for table "tla_customer_dispute_line".
 *
 * The followings are the available columns in table 'tla_customer_dispute_line':
 * @property integer $id
 * @property integer $parent_id
 * @property integer $inv_id
 * @property string $inv_no
 * @property string $ref
 * @property integer $pid
 * @property string $invoice_amount
 * @property string $customer_amount
 * @property string $diff
 * @property string $dispute_type
 * @property string $tla_op_comment
 * @property string $credit_note_no
 * @property string $credit_note_amount
 */
class TlaCustomerDisputeLine extends CActiveRecord
{
	const APPROVED_STATUS = 20;
	const REJECTED_STATUS = 30;
	const CLOSE_STATUS = 100;
	const NEW_STATUS = 10;
	private $task;
	public static $status = [
		0 => 'New',
		10 => 'New',
		20 => 'Approved',
		30 => 'Rejected',
		100 =>'Close'
	];

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tla_customer_dispute_line';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_id, inv_id, inv_no, pid, invoice_amount, customer_amount, diff, dispute_type', 'required'),
			array('parent_id, inv_id, pid', 'numerical', 'integerOnly'=>true),
			array('inv_no, ref, dispute_type, tla_op_comment, credit_note_no', 'length', 'max'=>45),
			array('invoice_amount, customer_amount, diff, credit_note_amount', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, parent_id, inv_id, inv_no, ref, pid, invoice_amount, customer_amount, diff, dispute_type, tla_op_comment, credit_note_no, credit_note_amount', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'dispute' => array(self::BELONGS_TO, 'TlaCustomerDispute', 'parent_id'),
			'shipment' => array(self::BELONGS_TO, 'Shipment', 'pid'),
		);
	}

	public function getDisplayInfo()
	{
		$ds =  "Invoice Number:".$this->inv_no."</br>".
				"Shipment:".@$this->shipment->ref."</br>".
				"Invoice Amount:".$this->invoice_amount."</br>".
				"Customer Amount:".$this->customer_amount."</br>".
				"Diff:".$this->diff."</br>".
				"Customer Comment:".$this->comment."</br>"."</br>";
		if(!empty($this->tla_op_comment))
		{
			$ds = $ds."</br>Tla OP Comment:".$this->tla_op_comment;
			return $ds;
		}
		return $ds;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'parent_id' => 'Parent',
			'inv_id' => 'Inv',
			'inv_no' => 'Inv No',
			'ref' => 'Ref',
			'pid' => 'Pid',
			'invoice_amount' => 'Invoice Amount',
			'customer_amount' => 'Customer Amount',
			'diff' => 'Diff',
			'dispute_type' => 'Dispute Type',
			'tla_op_comment' => 'Tla Op Comment',
			'credit_note_no' => 'Credit Note No',
			'credit_note_amount' => 'Credit Note Amount'
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('inv_id',$this->inv_id);
		$criteria->compare('inv_no',$this->inv_no,true);
		$criteria->compare('ref',$this->ref,true);
		$criteria->compare('pid',$this->pid);
		$criteria->compare('invoice_amount',$this->invoice_amount,true);
		$criteria->compare('customer_amount',$this->customer_amount,true);
		$criteria->compare('diff',$this->diff,true);
		$criteria->compare('dispute_type',$this->dispute_type,true);
		$criteria->compare('tla_op_comment',$this->tla_op_comment,true);
		$criteria->compare('credit_note_no',$this->credit_note_no,true);
		$criteria->compare('credit_note_amount',$this->credit_note_amount,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function getAssignUserIds($shipment)
	{
		$dptId = 0;
		$agentId = 0;
		$isTopCourierService = false;
		if(!empty($disputeLine->shipment)&&!empty($disputeLine->shipment->consol->dpt_id))
		{
			$dptId = $disputeLine->shipment->consol->dpt_id;
		}

		if(!empty($disputeLine->shipment))
		{
			$agentId = $shipment->agent_id;
			$shipment->checkIsCargoProcessWithRef();
		}

		$shipmentType = CsFaq::other;
		if($isTopCourierService)
		{
			$shipmentType = CsFaq::top_courier_service;
		}
		$invoice = Invoice::model()->findByPk($this->inv_id);
		$cs = CsFaq::model()->find("status = 2 and type = 3 and content = :content and dpt_id = :dptId and agent_id = :agentId and invoice_type like '%".$invoice->id."%' and shipment_type like '%".$shipmentType."%'",[":content"=>$this->dispute_type,":dptId"=>$dptId,":agentId"=>$agentId]);

		if(empty($cs))
		{
			$cs = CsFaq::model()->find("status = 2 and type = 3 and content = :content and dpt_id = :dptId and agent_id = :agentId and invoice_type like '%".$invoice->id."%'",[":content"=>$this->dispute_type,":dptId"=>$dptId,":agentId"=>$agentId]);

			if(empty($cs))
			{
				$cs = CsFaq::model()->find("status = 2 and type = 3 and content = :content and dpt_id = :dptId and agent_id = :agentId",[":content"=>$this->dispute_type,":dptId"=>$dptId,":agentId"=>$agentId]);

				if(empty($cs))
				{
					$cs = CsFaq::model()->find("status = 2 and type = 3 and content = :content and dpt_id = :dptId",[":content"=>$this->dispute_type,":dptId"=>$dptId]);

					if(empty($cs))
					{
						$cs = CsFaq::model()->find("status = 2 and type = 3 and content = :content",[":content"=>$this->dispute_type]);
					}
				}
			}
		}

		if(!empty($cs))
		{
			
			$occupation = CsFaqUserMap::model()->find("cs_faq_id = :cid",[":cid"=>$cs->id]);
			return [$occupation->user_id];
		}
		return [];
	}

	public function getTask()
	{
		if(empty($this->task))
		{
			$tlaTask = DisputeTask::model()->find("model='TlaCustomerDisputeLine' and fid = :fid",[":fid"=>$this->id]);
		}
		return $tlaTask;
	}

	public function getTaskId($getObj = false)
	{
		$tlaTask = DisputeTask::model()->find("model='TlaCustomerDisputeLine' and fid = :fid",[":fid"=>$this->id]);
		return $tlaTask->id;
	}

	public function getHandleStatus()
	{
		if($this->getTask()->status==TlaTask::CLOSE&&$this->handle_status==self::APPROVED_STATUS)
		{
			return "Completed";
		}else
		{
			return self::$status[$this->handle_status];
		}
	}
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TlaCustomerDisputeLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
