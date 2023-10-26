<?php

/**
 * This is the model class for table "shipment_scan_mq_process".
 *
 * The followings are the available columns in table 'shipment_scan_mq_process':
 * @property integer $id
 * @property integer $user_id
 * @property string $barcode
 * @property integer $pid
 * @property integer $pno
 * @property integer $warehouse
 * @property integer $type
 * @property string $scan_time
 */
class ShipmentScanMqProcess extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shipment_scan_mq_process';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, pid, pno, warehouse, type', 'numerical', 'integerOnly'=>true),
			array('barcode', 'length', 'max'=>128),
			array('scan_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, barcode, pid, pno, warehouse, type, scan_time', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'barcode' => 'Barcode',
			'pid' => 'Pid',
			'pno' => 'Pno',
			'warehouse' => 'Warehouse',
			'type' => 'Type',
			'scan_time' => 'Scan Time',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('barcode',$this->barcode,true);
		$criteria->compare('pid',$this->pid);
		$criteria->compare('pno',$this->pno);
		$criteria->compare('warehouse',$this->warehouse);
		$criteria->compare('type',$this->type);
		$criteria->compare('scan_time',$this->scan_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentScanMqProcess the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
