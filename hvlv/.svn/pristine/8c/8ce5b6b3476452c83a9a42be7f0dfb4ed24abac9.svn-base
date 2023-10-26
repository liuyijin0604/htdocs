<?php

/**
 * This is the model class for table "sf_tracking_relation_info".
 *
 * The followings are the available columns in table 'sf_tracking_relation_info':
 * @property integer $id
 * @property integer $courier_id
 * @property string $tracking_code
 * @property integer $status
 * @property string $op_code
 * @property string $op_description
 * @property string $reason_code
 * @property string $reason_description
 * @property string $created
 */
class SfTrackingRelationInfo extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sf_tracking_relation_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('courier_id, tracking_code, status, op_code, op_description, reason_code, reason_description, created', 'required'),
			array('courier_id, status', 'numerical', 'integerOnly'=>true),
			array('tracking_code', 'length', 'max'=>100),
			array('op_code, reason_code', 'length', 'max'=>45),
			array('op_description, reason_description', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, courier_id, tracking_code, status, op_code, op_description, reason_code, reason_description, created', 'safe', 'on'=>'search'),
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
			'courier_id' => 'Courier',
			'tracking_code' => 'Tracking Code',
			'status' => 'Status',
			'op_code' => 'Op Code',
			'op_description' => 'Op Description',
			'reason_code' => 'Reason Code',
			'reason_description' => 'Reason Description',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('courier_id',$this->courier_id);
		$criteria->compare('tracking_code',$this->tracking_code,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('op_code',$this->op_code,true);
		$criteria->compare('op_description',$this->op_description,true);
		$criteria->compare('reason_code',$this->reason_code,true);
		$criteria->compare('reason_description',$this->reason_description,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SfTrackingRelationInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
