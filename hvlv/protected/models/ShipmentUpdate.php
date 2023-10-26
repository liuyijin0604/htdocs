<?php

/**
 * This is the model class for table "shipment_update".
 *
 * The followings are the available columns in table 'shipment_update':
 * @property integer $id
 * @property integer $shipment_id
 * @property integer $type
 * @property string $created
 * @property integer $num_value
 * @property string $text_value
 */
class ShipmentUpdate extends CActiveRecord
{
	const BWF_UPDATE = 1;
	const DONE = 1;
	const SCHEDULED = 0;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shipment_update';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('shipment_id, type, created', 'required'),
			array('shipment_id, type, num_value', 'numerical', 'integerOnly'=>true),
			array('text_value', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, shipment_id, type, created, num_value, text_value', 'safe', 'on'=>'search'),
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
			'imparcel' => [self::BELONGS_TO, 'ImParcel', 'shipment_id'],
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
			'type' => 'Type',
			'created' => 'Created',
			'num_value' => 'Num Value',
			'text_value' => 'Text Value',
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
		$criteria->compare('type',$this->type);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('num_value',$this->num_value);
		$criteria->compare('text_value',$this->text_value,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentUpdate the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
