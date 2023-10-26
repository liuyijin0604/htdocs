<?php

/**
 * This is the model class for table "parcel_priority".
 *
 * The followings are the available columns in table 'parcel_priority':
 * @property integer $id
 * @property integer $parcel_type
 * @property integer $priority
 * @property integer $area_type
 * @property integer $is_oversize
 */
class ParcelPriority extends CActiveRecord
{
	const HELD_PARCEL_TYPE = 16;
	const B2C_PARCEL_TYPE = 2;
	const B2B_PARCEL_TYPE = 4;
	const FBA_PARCEL_TYPE = 8;
	const IS_OVERSIZE = 1;
	const GROUND_LEVEL = 2;
	const LEVEL_ONE = 2;
	const LEVEL_TWO = 4;
	const LEVEL_THREE = 8;
	const LEVEL_FOUR = 16;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'parcel_priority';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parcel_type, priority, area_type, is_oversize', 'required'),
			array('parcel_type, priority, area_type, is_oversize', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, parcel_type, priority, area_type, is_oversize', 'safe', 'on'=>'search'),
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
			'parcel_type' => 'Parcel Type',
			'priority' => 'Priority',
			'area_type' => 'Area Type',
			'is_oversize' => 'Is Oversize',
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
		$criteria->compare('parcel_type',$this->parcel_type);
		$criteria->compare('priority',$this->priority);
		$criteria->compare('area_type',$this->area_type);
		$criteria->compare('is_oversize',$this->is_oversize);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ParcelPriority the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
