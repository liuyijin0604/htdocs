<?php

/**
 * This is the model class for table "pickup_booking_slot".
 *
 * The followings are the available columns in table 'pickup_booking_slot':
 * @property integer $id
 * @property integer $depot
 * @property string $slot_time
 * @property integer $status
 */
class PickupBookingSlot extends CActiveRecord
{
	const DEPOT_SYDNEY = 106;
	const DEPOT_MELBOURNE = 218;
	const DEPOT_BRISBANE = 530;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pickup_booking_slot';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('depot, slot_time, status', 'required'),
			array('depot, status', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, depot, slot_time, status', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'depot' => 'Depot',
			'slot_time' => 'Slot Time',
			'status' => 'Status',
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
		$criteria->compare('depot',$this->depot);
		$criteria->compare('slot_time',$this->slot_time,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PickupBookingSlot the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
