<?php

/**
 * This is the model class for table "inv_line_check".
 *
 * The followings are the available columns in table 'inv_line_check':
 * @property string $id
 * @property string $booking_number
 * @property string $inv_line_id
 */

 class InvLineCheck extends oActiveRecord {

    public function tableName()
	{
		return 'inv_line_check';
	}

    // for TLA
	public function getDbConnection(){
		//return isset($this->inv_id) && $this->inv_id >= 500000? self::getTlaConnection() : parent::getDbConnection();
		//2022-06-17 
		return self::getTlaConnection();
	}

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('booking_number, inv_line_id', 'required'),
			array('booking_number', 'safe'),
			array('inv_line_id', 'length', 'max'=>11),
			array('booking_number', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, inv_line_id, booking_number', 'safe', 'on'=>'search'),
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
			'invLine' => array(self::BELONGS_TO, 'InvLine', 'inv_line_id'),
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
	public function search($ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('inv_line_id',$this->inv_line_id);
		$criteria->compare('booking_number',$this->booking_number,true);
		if($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InvLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
 }