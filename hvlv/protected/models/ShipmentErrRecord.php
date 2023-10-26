<?php

/**
 * This is the model class for table "shipment_err_record".
 *
 * The followings are the available columns in table 'shipment_err_record':
 * @property integer $id
 * @property integer $fid
 * @property integer $record
 * @property string $err_time
 * @property string $create_time
 * @property integer $err_op
 */
class ShipmentErrRecord extends CActiveRecord
{
	 public $nolog=false;
         public $custom_log_note;
        /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shipment_err_record';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fid,create_time', 'required'),
			array('id, fid, record, err_op', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fid, record, err_time, create_time, err_op', 'safe', 'on'=>'search'),
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
			'fid' => 'Fid',
			'record' => 'Record',
			'err_time' => 'Err Time',
			'create_time' => 'Create Time',
			'err_op' => 'Err Op',
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
		$criteria->compare('fid',$this->fid);
		$criteria->compare('record',$this->record);
		$criteria->compare('err_time',$this->err_time,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('err_op',$this->err_op);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
        public function afterSave(){
             if(!$this->nolog && !empty($this)){
			$extra = empty($this->custom_log_note)? array() : array('note' => $this->custom_log_note);
			Log::add($this, $this->isNewRecord? 3 : 4, array_merge(array('status' => $this->isNewRecord ? 'Create':'Update'), $extra));
		}
        }
        /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentErrRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
