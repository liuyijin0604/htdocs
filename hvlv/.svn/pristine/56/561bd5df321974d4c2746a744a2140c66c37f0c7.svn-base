<?php

/**
 * This is the model class for table "message".
 *
 * The followings are the available columns in table 'message':
 * @property string $id
 * @property string $from_id
 * @property string $to_id
 * @property integer $type
 * @property string $msg
 * @property integer $status
 * @property string $time
 */
class Message extends CActiveRecord
{
	const VIEWED = 2;
	const SHOWN = 1;
	const NEWMESSAGE = 0;
	public $isNoAfter = false;
	public static $states = array(
		'0' => 'New',
		'1' => 'Shown',
		'2' => 'Viewed',
		'3' => 'Pushed',
	);

	public static $types = array(
		0 => 'normal',
		1 => 'TaskNotice'
	);

	public static $processTypesColor = [
		0 => 'red',
		1 => 'orange',
		2 =>'green',
		3 =>'black'
	];
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Message the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'message';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('from_id, to_id, msg, status', 'required'),
			array('type, time', 'safe'),
			array('type, status', 'numerical', 'integerOnly'=>true),
			array('from_id, to_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, from_id, to_id, type, msg, status, time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'from' => array(self::BELONGS_TO, 'User', 'from_id'),
			'to' => array(self::BELONGS_TO, 'User', 'to_id'),
		);
	}
	
	public function beforeSave(){
		if(empty($this->time))	$this->time = date('Y-m-d H:i:s');
		return true;
	}
	

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'from_id' => 'From',
			'to_id' => 'To',
			'type' => 'Type',
			'msg' => 'Message',
			'status' => 'Status',
			'time' => 'Time',
		);
	}
	
	public function updateShown()
	{
		if($this->status==0)
		{
			$this->status =1;
			$this->save();
		}
		if($this->status==1)
		{
			$this->status =2;
			$this->save();
		}
		return "";

	}
	public function getFrom(){
		if(empty($this->from_id)) return 'System';
		return $this->from->getName();
	}
	
	public function getTo(){
		if(empty($this->to)) return "";
		return $this->to->getName();
	}
	
	public function getStatus(){
		return empty(self::$states[$this->status])? '' : self::$states[$this->status];
	}

	public function getStatusForList()
	{
		return "<font style='font-size:2em;color:".static::$processTypesColor[$this->status]."'>●</font>";
	}

	public function isTask()
	{
		if(preg_match('/(NT|DIS|IT|WH)\d{8}/', $this->msg))
		{
			return true;
		}
		return false;
	}

	public function getTaskNo()
	{
		if(preg_match('/(NT|DIS|IT|WH)\d{8}/', $this->msg,$m))
		{
			return $m[0];
		}
		return "";
	}
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($pgn = true, $ps = 30, $ec = false, $defaultOrder = true){
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('from_id',$this->from_id,true);
		$criteria->compare('to_id',$this->to_id,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('msg',$this->msg,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('time',$this->time,true);
		$sort = new CSort(get_called_class());
		$sort->attributes = [
			'*',
		];
		if ($defaultOrder) {
			$sort->defaultOrder = 't.time DESC';
		}
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
}