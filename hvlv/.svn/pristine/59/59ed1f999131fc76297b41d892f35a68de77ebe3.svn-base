<?php

/**
 * This is the model class for table "imports_shipment_relations".
 *
 * The followings are the available columns in table 'imports_shipment_relations':
 * @property integer $id
 * @property integer $cid
 * @property integer $pid
 * @property integer $label_lo
 * @property integer $label_hi
 * @property integer $blue_label
 * @property string $created
 */
class ImportsShipmentRelations extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'imports_shipment_relations';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cid, pid, label_lo, label_hi, blue_label, created', 'required'),
			array('cid, pid, label_lo, label_hi, blue_label', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, cid, pid, label_lo, label_hi, blue_label, created', 'safe', 'on'=>'search'),
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
			'sub_shipment'=>[self::BELONGS_TO,'ImParcel','cid'],
			'original'=>[self::BELONGS_TO,'ImParcel','pid']
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'cid' => 'Cid',
			'pid' => 'Pid',
			'label_lo' => 'Label Lo',
			'label_hi' => 'Label Hi',
			'blue_label' => 'Blue Label',
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
		$criteria->compare('cid',$this->cid);
		$criteria->compare('pid',$this->pid);
		$criteria->compare('label_lo',$this->label_lo);
		$criteria->compare('label_hi',$this->label_hi);
		$criteria->compare('blue_label',$this->blue_label);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ImportsShipmentRelations the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
