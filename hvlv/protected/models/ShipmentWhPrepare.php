<?php

/**
 * This is the model class for table "shipment_wh_prepare".
 *
 * The followings are the available columns in table 'shipment_wh_prepare':
 * @property integer $id
 * @property integer $shipment_id
 * @property integer $status
 * @property string $operate_time
 * @property integer $uid
 */
class ShipmentWhPrepare extends CActiveRecord
{
	public static $states = [
		0=>"not prepared",
		100=>"prepared",
		101=>"canceled",
	];

	const NOTPREPARED = 0;
	const PREPARED = 100;
	const CANCELED = 101;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shipment_wh_prepare';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('shipment_id, status, operate_time, uid', 'required'),
			array('shipment_id, status, uid', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, shipment_id, status, operate_time, uid', 'safe', 'on'=>'search'),
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
			'shipment_id' => 'Shipment',
			'status' => 'Status',
			'operate_time' => 'Operate Time',
			'uid' => 'Uid',
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
		$criteria->compare('shipment_id',$this->shipment_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('operate_time',$this->operate_time,true);
		$criteria->compare('uid',$this->uid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentWhPrepare the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
