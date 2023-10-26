<?php

/**
 * This is the model class for table "lf_hunter_label_zone".
 *
 * The followings are the available columns in table 'lf_hunter_label_zone':
 * @property integer $id
 * @property integer $zone_id
 * @property string $postcode
 * @property string $suburb
 * @property string $state
 * @property string $label_zone
 * @property string $label_sub_zone
 */
class LfHunterLabelZone extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'lf_hunter_label_zone';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('zone_id', 'numerical', 'integerOnly'=>true),
			array('postcode, state, label_zone, label_sub_zone', 'length', 'max'=>10),
			array('suburb', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, zone_id, postcode, suburb, state, label_zone, label_sub_zone', 'safe', 'on'=>'search'),
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
			'zone_id' => 'Zone',
			'postcode' => 'Postcode',
			'suburb' => 'Suburb',
			'state' => 'State',
			'label_zone' => 'Label Zone',
			'label_sub_zone' => 'Label Sub Zone',
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
		$criteria->compare('zone_id',$this->zone_id);
		$criteria->compare('postcode',$this->postcode,true);
		$criteria->compare('suburb',$this->suburb,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('label_zone',$this->label_zone,true);
		$criteria->compare('label_sub_zone',$this->label_sub_zone,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return LfHunterLabelZone the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
