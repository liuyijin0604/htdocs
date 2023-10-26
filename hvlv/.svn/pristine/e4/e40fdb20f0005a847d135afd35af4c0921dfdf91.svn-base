<?php

/**
 * This is the model class for table "driver_tracking".
 *
 * The followings are the available columns in table 'driver_tracking':
 * @property integer $id
 * @property integer $driver_id
 * @property string $tracking_time
 * @property string $longitude
 * @property string $latitude
 */
class DriverTracking extends CActiveRecord
{
	const TRACKING_REALTIME = 1;
	const TRACKING_DELIVERED = 2;
	const TRACKING_DELIVERY_ERROR = 3;
	public static $type = [
		1 => 'Realtime',
		2 => 'Delivered',
		3 => 'Delivery Error'
	];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'driver_tracking';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'driver_id' => 'Driver ID',
			'tracking_time' => 'Tracking Time',
			'longitude' => 'Longitude',
			'latitude' => 'Latitude',
			'type' => 'Type',
			'cargo_process_id' => 'Cargo Process ID'
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

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('driver_id', $this->driver_id);
		$criteria->compare('tracking_time', $this->tracking_time);
		$criteria->compare('longitude', $this->longitude);
		$criteria->compare('latitude', $this->latitude);
		$criteria->comapre('type', $this->type);
		$criteria->compare('cargo_process_id', $this->cargo_process_id);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GoogleReview the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
