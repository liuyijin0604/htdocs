<?php

/**
 * This is the model class for table "scanner".
 *
 * The followings are the available columns in table 'scanner':
 * @property string $id
 * @property integer $user_id
 * @property integer $wh_id
 * @property integer $status
 * @property string $manuf
 * @property string $model
 * @property string $serial
 * @property string $ref
 * @property string $registered
 */
class Scanner extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'scanner';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, wh_id, status, manuf, model, serial, ref, registered', 'safe'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('manuf, model, serial', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, wh_id, status, manuf, model, serial, ref, registered', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'warehouse' => array(self::BELONGS_TO, 'Org', 'wh_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'wh_id' => 'Warehouse',
			'status' => 'Status',
			'manuf' => 'Manuf',
			'model' => 'Model',
			'serial' => 'Serial',
			'ref' => 'Reference',
			'registered' => 'Registered',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('wh_id',$this->wh_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('manuf',$this->manuf,true);
		$criteria->compare('model',$this->model);
		$criteria->compare('serial',$this->serial);
		$criteria->compare('ref',$this->ref);
		$criteria->compare('registered',$this->registered,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Scanner the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
