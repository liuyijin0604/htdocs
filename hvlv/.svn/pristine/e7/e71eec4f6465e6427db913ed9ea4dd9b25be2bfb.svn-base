<?php

/**
 * This is the model class for table "cn_area".
 *
 * The followings are the available columns in table 'cn_area':
 * @property string $id
 * @property string $name
 * @property string $init
 * @property string $city_id
 * @property string $zip
 * @property string $code
 */
class CnArea extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'cn_area';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, name, city_id, zip, code', 'safe'),
			array('id, city_id', 'length', 'max'=>11),
			array('name', 'length', 'max'=>50),
			array('zip, code', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, city_id, zip, code', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'city' => array(self::BELONGS_TO, 'CnCity', 'city_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'init' => 'Initial',
			'city_id' => 'City',
			'zip' => 'Zip',
			'code' => 'Code',
		);
	}

	public static function getZip($p, $c){
		$a = self::model()->with(['city', 'city.province'])->find([
			'condition' => 'province.name LIKE :p AND city.name = :c',
			'order' => 't.id',
			'params' => [':p' => $p.'%', ':c' => $c],
			]);
		return ($a)? $a->zip : '';
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('init',$this->init,true);
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('zip',$this->zip,true);
		$criteria->compare('code',$this->code,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CnArea the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
