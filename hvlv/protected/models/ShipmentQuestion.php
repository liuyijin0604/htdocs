<?php

/**
 * This is the model class for table "shipment_question".
 *
 * The followings are the available columns in table 'shipment_question':
 * @property integer $id
 * @property integer $shipment_id
 * @property integer $submit_id
 * @property string $c_note
 * @property string $s_note
 * @property integer $c_read
 * @property integer $s_read
 * @property string $faq
 * @property string $faq_answer
 * @property string $answer_date
 * @property string $cr_date
 * @property integer $process_type
 */
class ShipmentQuestion extends MetaModel
{

	public $org_id;
	public $ticket;
	public $ref;
	public $submitType;
	public static $processTypes =[
		0=>"New",
		10=>'Responsed',
		20=>'Received',
		100=>"Done"
	];
	public static $readTypes = [
		true=>"Has Been Read",
		false=>"Mark Read"
	];

	public static $readTypes2 = [
		true=>"Has Been Read",
		false=>"Need Read"
	];
	public $date;

	const TYPE_UNFINISHED = 0;
	const TYPE_FINISHED = 100;
	const CREAD = true;
	const TYPE_RESPONSED = 10;
	const TYPE_RECEIVED = 20;

	public static $courierList = [
		"Startrack",
		"D2Z",
		"Fastway",
	];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shipment_question';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('shipment_id, submit_id, faq', 'required'),
			array('shipment_id, submit_id, c_read, s_read, process_type, finish_user_id', 'numerical', 'integerOnly'=>true),
			array('faq, faq_answer', 'length', 'max'=>255),
			array('cr_date, c_note,s_note,faq_answer', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, shipment_id, submit_id, c_note, s_note, c_read, s_read, faq, faq_answer, answer_date, cr_date, process_type, date,ticket,ref, finish_user_id', 'safe', 'on'=>'search'),
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
			"ShipmentQuestionLog"=>[self::HAS_MANY,'ShipmentQuestionLog','fid'],
			"ShipmentQuestionSubmit"=>[self::BELONGS_TO,'ShipmentQuestionSubmit','submit_id'],
			"Shipment"=>[self::BELONGS_TO,'Shipment','shipment_id'],
			"shipment"=>[self::BELONGS_TO,'Shipment','shipment_id'],
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return AppHelper::tArray(strtolower(__CLASS__), [
			'id' => 'ID',
			'shipment_id' => 'Shipment',
			'submit_id' => 'Submit',
			'c_note' => 'C note',
			's_note' => 'S note',
			'c_read' => 'C Read',
			's_read' => 'S Read',
			'faq' => 'Faq',
			'faq_answer' => 'Faq Answer',
			'answer_date' => 'Answer Date',
			'cr_date' => 'Cr Date',
			'date' => 'Submit Date',
			'ticket' => 'Ticket',
			'process_type' => 'Process Type',
			'ref' => 'Reference Number',
			'finish_user_id'=>'Finish User Id'
		]);
	}

	public function getDate()
	{
		if(isset($this->ShipmentQuestionSubmit))
		{
			$this->date = $this->ShipmentQuestionSubmit->date;
		}
		return $this->date;
	}

	public function setDate($myDate)
	{
		$this->date = $myDate;
	}

	public function setTicket($ticket)
	{
		$this->ticket = $ticket;
	}
	public function getRef()
	{
		if(isset($this->Shipment))
		{
			$this->ref = $this->Shipment->ref;
		}
		return $this->ref;
	}

	public function setRef($ref)
	{
		$this->ref = $ref;
	}

	public function getFaqAnswerList()
	{
		$fl=CsFaqAnswer::model()->findAll(" status=:status ",[":status"=>CsFaq::active]);
		$results=[];
		foreach ($fl as $key => $value) {
			$results[$value["content"]] = $value["content"];
		}
		return $results;
	}

	public function afterSave()
	{
		if(!empty($this)) {
			$extra = empty($this->custom_log_note)? [] : ['note' => 'new shipment question'];
			Log::add($this, $this->isNewRecord? 3 : 4, array_merge(['process_type' => ShipmentQuestion::$processTypes[$this->process_type]], $extra));
		}

		ShipmentQuestionLog::addLog($this->id, $this->faq);
	}

	public function checkIsSendCourierEmail()
	{
		if(isset($this->mdata)&&isset($this->mdata["sendEmailCourier"]))
		{
			return true;
		}else
		{
			return false;
		}
	}

	public function checkIsSendCustomerEmail()
	{
		if(isset($this->mdata)&&isset($this->mdata["sendEmailCustomer"]))
		{
			return true;
		}else
		{
			return false;
		}
	}

	public function checkIsSendPhone()
	{
		if(isset($this->mdata)&&isset($this->mdata["sendSmsCustomer"]))
		{
			return true;
		}else
		{
			return false;
		}
	}

	public function sendEmail()
	{

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
	public function search($pgn = true, $ps = 30, $ec = false, $defaultOrder = true)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('shipment_id',$this->shipment_id);
		$criteria->compare('submit_id',$this->submit_id);
		$criteria->compare('c_note',$this->c_note,true);
		$criteria->compare('s_note',$this->s_note,true);
		$criteria->compare('c_read',$this->c_read);
		$criteria->compare('s_read',$this->s_read);
		$criteria->compare('faq_answer',$this->faq_answer,true);
		$criteria->compare('answer_date',$this->answer_date,true);
		$criteria->compare('cr_date',$this->cr_date,true);
		$criteria->compare('process_type',$this->process_type);
		$sort = new CSort();

		$criteria->with =array(
				    'ShipmentQuestionSubmit','Shipment'
				);

		if($this->org_id !=null)
		{
			$criteria->addCondition("org_id = ".$this->org_id);
		}

		if($this->faq !=null)
		{
			$criteria->addCondition("t.faq like '%{$this->faq}%' ");
		}

		if($this->submitType !=null)
		{
			$criteria->addCondition("ShipmentQuestionSubmit.type = {$this->submitType}");
		}


		if($this->ref !=null)
		{
			$refStr ="";
			$ns = preg_split('/[\s,;]+/', trim($this->ref));
			if (sizeof($ns) > 200) {
				$ns = array_slice($ns, 0, 200);
			}
			foreach ($ns as $key => $value) {
				if($key==0)
				{
					$refStr="'".$value."'";
				}else
				{
					$refStr=$refStr.",'".$value."'";
				}
			}
			$criteria->addCondition("Shipment.ref in ({$refStr}) ");
		}


		if($this->date !=null)
		{
			$criteria->addCondition("ShipmentQuestionSubmit.date like '%{$this->date}%' ");
		}

		if($this->ticket !=null)
		{
			$criteria->addCondition("ShipmentQuestionSubmit.ticket like '%{$this->ticket}%' ");
		}


		$sort->attributes = [
			 	'date' => [
					'asc' => 'ShipmentQuestionSubmit.date',
			 		'desc' => 'ShipmentQuestionSubmit.date DESC',
			 	]
		 	];
		$sort->defaultOrder ='ShipmentQuestionSubmit.date DESC';


		$pagerparams = $_GET;
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => $sort,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentQuestion the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
