<?php

class HunterPortcode extends CActiveRecord
{

	public function tableName()
	{
		return 'hunter_portcode';
	}

	public function rules()
	{
		return array(
			array('suburb, postcode, portcode, zonecode', 'required'),
			array('suburb, postcode, portcode, zonecode', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return array(
			'suburb' => 'Suburb',
			'postcode' => 'Postcode',
			'portcode' => 'Portcode',
			'zonecode' => 'Zonecode',
		);
	}

	public static function getPortcode($suburb, $postcode)
	{
		$hp = HunterPortcode::model()->find('suburb = :suburb AND postcode = :postcode', [':suburb' => $suburb, ':postcode' => $postcode]);
		return $hp->portcode;
	}

	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('suburb', $this->suburb);
		$criteria->compare('postcode', $this->postcode);
		$criteria->compare('portcode', $this->portcode);
		$criteria->compare('zonecode', $this->zonecode);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}