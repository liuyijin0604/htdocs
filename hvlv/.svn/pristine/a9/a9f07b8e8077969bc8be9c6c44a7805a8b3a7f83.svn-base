<?php

/**
 * This is the model class for table "cnl_party2order".
 *
 * The followings are the available columns in table 'cnl_party2order':
 * @property string $id
 * @property string $order_id
 * @property string $party_id
 * @property integer $type
 * @property string $meta
 */
class CnlParty2order extends CActiveRecord
{

	public static $types = [
		10 => 'SHIPPER',
		20 => 'CONSIGNEE',
		30 => 'PICKUP',
		40 => 'FIRST_NOTIFIER',
		50 => 'SECOND_NOTIFIER',
	];
	
	public $mdata = [];
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'cnl_party2order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, party_id, type, meta', 'safe'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('order_id, party_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, party_id, type, meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'party' => array(self::BELONGS_TO, 'CnlParty', 'party_id'),
		);
	}

	public function beforeValidate(){
		foreach(['type'] as $k){
			if(is_string($this->{$k})) $this->_mapAttrVal($k);
		}
		if(self::model()->count('order_id = :oid AND party_id = :pid AND type = :t AND id != :id', [':oid' => $this->order_id, ':pid' => $this->party_id, ':id' => empty($this->id)? 0 : $this->id, ':t' => $this->type]) > 0){
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

	private static function _as2v($attr, $s){
		$map = ['status' => 'states'];
		$v = in_array($attr, $map)? $map[$attr] : $attr.'s';
		if(!isset(self::$$v)) return false;
		foreach(self::$$v as $k => $v){
			if($v === $s){
				return $k;
				break;
			}
		}
		return false;
	}

	public static function link($p, $o, $t){
		$l = CnlParty2order::model()->find('order_id = :oid AND type = :t', [':oid' => $o->id, ':t' => self::_as2v('type', $t)]);
		if(empty($l)){
			$l = new CnlParty2order;
			$l->order_id = $o->id;
			$l->type = $t;
		}

		$l->party_id = $p->id;
		$l->save();
	}

	public function getType(){
		return isset(static::$types[$this->type])? Yii::t(strtolower(__CLASS__), static::$types[$this->type]) : $this->type;
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
			'party_id' => 'Party',
			'type' => 'Type',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('order_id',$this->order_id,true);
		$criteria->compare('party_id',$this->party_id,true);
		$criteria->compare('type',$this->type);
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
	 * @return CnlParty2order the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
