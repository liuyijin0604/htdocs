<?php

/**
 * This is the model class for table "delivery_record".
 *
 * The followings are the available columns in table 'delivery_record':
 * @property int $id
 * @property string $rego
 * @property int $plt
 * @property string $note
 * @property int status
 * @property int damage
 * @property int op_id
 * @property string created
 */
class DeliveryRecord extends MetaModel
{

	public $detail;
	const NEWSTATE = 1;
	const DONESTATE = 9;
	const CANCELSTATE = 10;
	public static $states = array(
		1 => 'New',
		9 => 'Done',
		10 => 'Cancelled',
	);

	public static $damage_states = array(
		1 => 'Yes',
		0 => 'No',
	);

	public static $booking_type = array(
		0 => 'Delivery',
		1 => 'Pickup',
	);

	public function tableName()
    {
        return 'delivery_record';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('rego, plt, status, damage, op_id, created, booking_time, mobile, name, pkg, uuid', 'required'),
            array('plt, status, damage, op_id, pkg', 'numerical', 'integerOnly'=>true),
            array('rego, task', 'length', 'max'=>20),
            array('no, mobile, name', 'length', 'max'=>45),
            array('uuid', 'length', 'max'=>125),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, rego, plt, note, status, damage, op_id, created, task, no, booking_time, mobile, name, pkg, uuid', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'rego' => 'Rego',
            'plt' => 'Plt',
            'note' => 'Note',
            'status' => 'Status',
            'damage' => 'Damage',
            'op_id' => 'Op',
            'created' => 'Created',
            'task' => 'Task',
            'no' => 'No',
            'booking_time' => 'Booking Time',
            'mobile' => 'Mobile',
            'name' => 'Name',
            'pkg' => 'Pkg',
            'uuid' => 'Unique User Id',
        );
    }

	public function relations()
	{
		return array(
			'op' => array(self::BELONGS_TO, 'User', 'op_id'),
		);
	}

	public function getStatus()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$states[$this->status]) ? '' : self::$states[$this->status]);
	}

	public function getDamage()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$damage_states[$this->damage]) ? '' : self::$damage_states[$this->damage]);
	}

	public function getBookingType()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$booking_type[@$this->mdata['booking_type']]) ? '&nbsp;' : self::$booking_type[@$this->mdata['booking_type']]);
	}

	public function afterFind()
	{
		$this->genNo();
		parent::afterFind();
	}
	private function getType()
	{
		return "DB";
	}
	public function genNo()
	{
		if (empty($this->no)) {
			$this->no = $this->getType().$this->id;
			$this->save();
		}
	}
	public function beforeSave()
	{
		if (empty($this->damage)) {
			$this->damage = 0;
		}
		if (empty($this->status)) {
			$this->status = 1;
		}

		$this->checkAttributes();

		parent::beforeSave();
		return true;
	}

	public function checkAttributes()
	{
		if (empty($this->created)) {
			$this->created = date('Y-m-d H:i:s');
			}
			
		if (empty($this->note)) {
			$this->note = "none";
			}

			if (empty($this->task)) {
				$this->task = "none";
			}

			if (empty($this->mobile)) {
				$this->mobile = "none";
			}

			if (empty($this->name )) {
				$this->name = "none";
			}
			if (empty($this->uuid)) {
				$this->uuid = "none";
			}
	}

	public function getBookingDate()
	{
		return explode(' ', $this->booking_time)[0];
	}

	public function getBookingTime()
	{
		return explode(' ', $this->booking_time)[1];
	}
	public function afterSave()
	{
		Log::add($this, $this->isNewRecord ? 3 : 4, array('status' => $this->getStatus()));
	}

	public function search($pgn = true, $ps = 30,$order ="DESC",$m=false)
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('rego', $this->rego, true);
		$criteria->compare('mobile', $this->mobile, true);
		$criteria->compare('plt', $this->plt);
		$criteria->compare('note', $this->note, true);
		$criteria->compare('status', $this->status);
		$criteria->compare('damage', $this->damage);
		$criteria->compare('op_id', $this->op_id);
		$criteria->compare('created', $this->created, true);
		$criteria->compare('booking_time', $this->booking_time, true);
		$criteria->compare('uuid', $this->uuid);
		$criteria->compare('no', $this->no,true);

		if(empty($this->rego)&&!$m)// only the front-end app will filter the data without booking_time
		{
			$criteria->addCondition('booking_time != "0000-00-00 00:00:00"');
		}

		if(empty($this->status))
		{
			// if(!empty($this->uuid))
			// {
				$criteria->addCondition('status != '.self::CANCELSTATE);
			// }
		}
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'status ASC, t.booking_time '.$order,
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function getListInfo()
	{
		/**/
		$num =0;
		// search the booking before this record in the same day
		$bookingDay = date("Y-m-d",strtotime($this->booking_time));
		$criteria = new CDbCriteria();
		$criteria->addCondition(" to_days(booking_time) = to_days('".$bookingDay."') ");
		$criteria->addCondition(" booking_time < '{$this->booking_time}' ");
		$criteria->addCondition(" rego != '{$this->rego}' ");
		$criteria->addCondition(" status = '".self::NEWSTATE."' ");

		$records = self::model()->findAll($criteria);
		$num = sizeof($records);
		return "Booking Before you: {$num}";
	}

	public function getTimeSelectArr($checkDate,$type = false)
	{
		$criteria = new CDbCriteria();
		if(is_array($checkDate))
		{
			$checkDate = $checkDate[0];
		}
		$checkDate = date('Y-m-d',strtotime($checkDate));
		$criteria->addCondition(" to_days(booking_time) = to_days('".$checkDate."') ");
		$criteria->addCondition(" status = '".self::NEWSTATE."' ");
		$records = self::model()->findAll($criteria);
		$allBookingTime = array_column($records, 'booking_time');
		$startTime = date('Y-m-d',strtotime($checkDate)).' 09:00:00';
		$endTime = date('Y-m-d',strtotime($checkDate)).' 16:00:00';
		$currentTimestamp = strtotime($startTime);
		$endTimestamp = strtotime($endTime);
		$provide = [];
		$i = 0;
		while($currentTimestamp<$endTimestamp)
		{
			$repeatTime = 0;
			foreach ($allBookingTime as $key => $value) 
			{
				if($value == date('Y-m-d H:i:s',$currentTimestamp))
				{
					$repeatTime++;
				}
			}

			if($type)
			{
				if($repeatTime>0)
				{
					$currentTime = date('Y-m-d H:i:s',$currentTimestamp);
					$provide[] = ['id'=>$i,'datetime'=>$currentTime,'num'=>$repeatTime];
					$i++;
				}
			}else
			{
				if($repeatTime<2)
				{
					$currentTime = date('Y-m-d H:i:s',$currentTimestamp);
					$provide[] = ['id'=>$i,'datetime'=>explode(' ', $currentTime)[1]];
					$i++;
				}
			}
			$currentTimestamp =strtotime('+30 minute',$currentTimestamp);
		}
		return $provide;
	}

}