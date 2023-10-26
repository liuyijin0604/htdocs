<?php

/**
 * This is the model class for table "cnl_doc".
 *
 * The followings are the available columns in table 'cnl_doc':
 * @property string $id
 * @property string $order_id
 * @property string $file_id
 * @property integer $type
 * @property integer $status
 * @property string $meta
 */
class CnlDoc extends CActiveRecord
{

	public static $types = [
		10 => 'CI',
		20 => 'PL',
		30 => 'MSDS',
		40 => 'FUMIGATION_CERT',
		50 => 'CONTAINER_MANIFEST',
		60 => 'BILL_OF_LADING',
		65 => 'DRAFT_BL',
		90 => 'OTHER',
	];

	public static $states = [
		10 => 'Received',
		20 => 'Uploaded',
		40 => 'Attached',
		50 => 'Sent',
		100 => 'Deleted',
	];

	public $mdata = [];
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'cnl_doc';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, file_id, type, status, meta', 'safe'),
			array('type, status', 'numerical', 'integerOnly'=>true),
			array('order_id, file_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, file_id, type, status, meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'file' => array(self::BELONGS_TO, 'FileRepo', 'file_id'),
		);
	}

	public function beforeValidate(){
		foreach(['type'] as $k){
			if(is_string($this->{$k})) $this->_mapAttrVal($k);
		}
		if($this->isNewRecord && self::model()->count('order_id = :oid AND file_id = :fid AND type = :t', [':oid' => $this->order_id, ':fid' => $this->file_id, ':t' => $this->type]) > 0){
			$this->addError('id', 'Duplicate Entry');
			return false;
		}
		return parent::beforeValidate();
	}

	private function _mapAttrVal($attr){
		$map = ['status' => 'states'];
		$v = in_array($attr, $map)? $map[$attr] : $attr.'s';
		if(!isset(self::$$v)) return false;
		foreach(self::$$v as $k => $v){
			if($v === $this->{$attr}){
				$this->{$attr} = $k;
				return true;
				break;
			}
		}
		return false;
	}

	public function getType(){
		if(empty($this->type)) return '';
		return isset(static::$types[$this->type])? Yii::t(strtolower(__CLASS__), static::$types[$this->type]) : $this->type;
	}

	public function getStatus(){
		return isset(static::$states[$this->status])? Yii::t(strtolower(__CLASS__), static::$states[$this->status]) : $this->status;
	}

	
	public function beforeSave(){
		$this->meta = empty($this->mdata)? '' : json_encode($this->mdata);
		return parent::beforeSave();
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return parent::afterFind();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'order_id' => 'Order',
			'file_id' => 'File',
			'type' => 'Type',
			'status' => 'Status',
			'meta' => 'Meta',
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
	public function search($pgn=true, $ps = 30, $ec = false){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('file_id',$this->file_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('status',$this->status);
		$criteria->compare('meta',$this->meta,true);
		$with = [];

		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'t.id DESC',
 			),
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CnlDoc the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
