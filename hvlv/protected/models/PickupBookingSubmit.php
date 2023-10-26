<?php

/**
 * This is the model class for table "pickup_booking_submit".
 *
 * The followings are the available columns in table 'pickup_booking_submit':
 * @property string $id
 * @property string $submit_no
 * @property string $created
 */
class PickupBookingSubmit extends MetaModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pickup_booking_submit';
	}

	public function generateSubmitNumber($booking_time,$depot)
	{
		if(empty($this->submit_no))
		{
			$today = date('Y-m-d');
			$today = $today . ' 00:00:00';
			$todayBookings = PickupBooking::model()->findAllBySql('SELECT * FROM pickup_booking WHERE create_date >= "'. $today . '"');
			$bookingCount = count($todayBookings)+1;
			$s = date('ymd');
			switch ($depot) {
				case "Sydney":
					$this->submit_no = "SBK" . $s . $bookingCount;
					break;
				case "Melbourne":
					$this->submit_no = "MBK" . $s . $bookingCount;
					break;
				case "Brisbane":
					$this->submit_no = "BBK" . $s . $bookingCount;
					break;
			}
		}
	}


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('submit_no, created', 'required'),
			array('submit_no', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, submit_no, created', 'safe', 'on'=>'search'),
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
			'booking' => [self::HAS_MANY, 'PickupBooking', 'submit_id'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'submit_no' => 'Submit No',
			'created' => 'Created',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('submit_no',$this->submit_no,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PickupBookingSubmit the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
