<?php

/**
 * This is the model class for table "hs".
 *
 * The followings are the available columns in table 'hs':
 * @property string $id
 * @property string $hs
 * @property string $name
 * @property string $unit
 * @property string $uc
 * @property double $price
 * @property double $rate
 */
class HS extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hs, name', 'required'),
			array('unit, uc, price, rate', 'safe'),
			array('price, rate', 'numerical'),
			array('hs', 'length', 'max'=>8),
			array('name', 'length', 'max'=>100),
			array('unit', 'length', 'max'=>20),
			array('uc', 'length', 'max'=>3),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, hs, name, unit, uc, price, rate', 'safe', 'on'=>'search'),
		);
	}

	public static function getUpr($hs, $c=false){
		$r = self::model()->find('hs = :hs', [':hs' => $hs]);
		if(empty($r)) return false;
		return !$c? ['unit' => $r->unit, 'uc' => $r->uc, 'price' => $r->price, 'rate' => $r->rate] : $r->{$c};
	}

	public static function getUC($u){
		$r = self::model()->find('unit = :u', [':u' => $u]);
		if(empty($r)) return false;
		return $r->uc;
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
			'hs' => 'Hs',
			'name' => 'Name',
			'unit' => 'Unit',
			'uc' => 'Uc',
			'price' => 'Price',
			'rate' => 'Rate',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('hs',$this->hs,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('unit',$this->unit,true);
		$criteria->compare('uc',$this->uc,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('rate',$this->rate);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HS the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
