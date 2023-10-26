<?php

/**
 * This is the model class for table "mani_map".
 *
 * The followings are the available columns in table 'mani_map':
 * @property string $id
 * @property string $mani_id
 * @property string $model
 * @property string $fid
 * @property string $bwf
 * @property string $meta
 * @property integer $status
 */
class ManiMap extends CActiveRecord{

	public $mdata = [];

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'mani_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('mani_id, model, fid', 'required'),
			array('bwf, meta, status', 'safe'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('mani_id, fid', 'length', 'max'=>11),
			array('model', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, mani_id, model, fid, bwf, meta, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'manifest' => array(self::BELONGS_TO, 'Manifest', 'mani_id'),
			'shipment' => array(self::BELONGS_TO, 'Shipment', 'fid'),
		);
	}
	
	public function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		return parent::beforeSave();
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'mani_id' => 'Mani',
			'model' => 'Model',
			'fid' => 'Fid',
			'status' => 'Status',
		);
	}

	public function mm(){
		$r = new $this->model;
		return $r::model()->findByPk($this->fid);
	}

	public static function belong2($m){
		return self::model()->findAll('model = :m AND fid = :id', [':m' => get_class($m), ':id' => $m->id]);
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
		$criteria->compare('mani_id',$this->mani_id,true);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('fid',$this->fid,true);
		$criteria->compare('status',$this->status);
		if(!empty($this->bwf)){
			$criteria->addCondition('t.bwf & ' . $this->bwf . ' > 0');
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ManiMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
