<?php

/**
 * This is the model class for table "au_postcode".
 *
 * The followings are the available columns in table 'au_postcode':
 * @property string $postcode
 * @property string $suburb
 * @property string $state
 * @property string $dc
 * @property string $type
 * @property double $lat
 * @property double $lon
 */
class AuPostcode extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'au_postcode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('postcode, suburb, state, type', 'required'),
			array('lat, lon', 'numerical'),
			array('postcode, state', 'length', 'max'=>4),
			array('suburb, type', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('postcode, suburb, state, type, lat, lon', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'postcode' => 'Postcode',
			'suburb' => 'Suburb',
			'state' => 'State',
			'dc' => 'Dc',
			'type' => 'Type',
			'lat' => 'Lat',
			'lon' => 'Lon',
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
	public function search(){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('postcode',$this->postcode,true);
		$criteria->compare('suburb',$this->suburb,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('dc',$this->dc,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('lat',$this->lat);
		$criteria->compare('lon',$this->lon);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AuPostcode the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
