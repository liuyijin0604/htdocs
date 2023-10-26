<?php

/**
 * This is the model class for table "outturn".
 *
 * The followings are the available columns in table 'outturn':
 * @property string $id
 * @property string $dt
 * @property integer $status
 * @property string $meta
 */
class Outturn extends CActiveRecord
{
	public static $states = array(
		10 => 'New',
		20 => 'Scanning',
		90 => 'Finished',
	);
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'outturn';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dt, status, meta', 'safe'),
			array('status', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, dt, status, meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'shipments' => array(self::HAS_MANY, 'Shipment', 'ot_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'dt' => 'Time',
			'status' => 'Status',
			'meta' => 'Meta',
		);
	}
	
	public function getParcels(){
		return sizeof($this->shipments);
	}
	
	public function getStatus(){
		return Yii::t(strtolower(__CLASS__), self::$states[$this->status]);
	}
	
	public function beforeSave(){
		if(empty($this->dt)) $this->dt = date('Y-m-d H:i:s');
		if(empty($this->status)) $this->status = 10;
		
		return true;
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
		$criteria->compare('dt',$this->dt,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
    			'defaultOrder'=>'t.id DESC',
  			),
			'pagination'=>array(
				'pageSize'=>'30',
            ),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Outturn the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
