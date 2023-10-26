<?php

/**
 * This is the model class for table "shipment_wh_inspection_relations".
 *
 * The followings are the available columns in table 'shipment_wh_inspection_relations':
 * @property integer $id
 * @property integer $pid
 * @property string $comment
 * @property integer $check_status
 * @property integer $process_status
 * @property integer $file_id
 */
class ShipmentWhInspectionRelations extends CActiveRecord
{
	public static $check_status_list = [
		1=>'Yes',
		2=>'No'
	];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shipment_wh_inspection_relations';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid, process_status', 'required'),
			array('pid, check_status, process_status, file_id', 'numerical', 'integerOnly'=>true),
			array('comment', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pid, comment, check_status, process_status, file_id', 'safe', 'on'=>'search'),
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
			'shipment' => [self::BELONGS_TO, 'Shipment', 'pid'],
			'inspection' => [self::BELONGS_TO, 'ShipmentWhInspection', 'inspection_id'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pid' => 'Pid',
			'comment' => 'Comment',
			'check_status' => 'Check Status',
			'process_status' => 'Process Status',
			'file_id' => 'File',
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
		$criteria->compare('pid',$this->pid);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('check_status',$this->check_status);
		$criteria->compare('process_status',$this->process_status);
		$criteria->compare('file_id',$this->file_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentWhInspectionRelations the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
