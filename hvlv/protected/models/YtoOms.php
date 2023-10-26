<?php

/**
 * This is the model class for table "yto_oms".
 *
 * The followings are the available columns in table 'yto_oms':
 * @property string $id
 * @property string $api_id
 * @property string $md
 * @property integer $status
 * @property string $hbn
 * @property string $ref
 * @property string $cnors
 * @property string $cnees
 * @property string $swt
 * @property string $items
 * @property string $tpl
 * @property string $meta
 */
class YtoOms extends CActiveRecord
{
	public $mdata = [];
	public $cnor, $cnee;

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'yto_oms';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('api_id, md, status, hbn, ref, cnors, cnees, swt, items, tpl, meta', 'safe'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('api_id', 'length', 'max'=>11),
			array('hbn, ref', 'length', 'max'=>50),
			array('swt', 'length', 'max'=>10),
			array('tpl', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, api_id, md, status, hbn, ref, cnors, cnees, swt, items, tpl, meta', 'safe', 'on'=>'search'),
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

	
	
	public function beforeSave(){
		$this->meta = empty($this->mdata)? '' : json_encode($this->mdata);
		return parent::beforeSave();
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		if(!empty($this->cnors)) $this->cnor = json_decode($this->cnors);
		if(!empty($this->cnees)) $this->cnee = json_decode($this->cnees);
		return parent::afterFind();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'api_id' => 'Api',
			'md' => 'Md',
			'status' => 'Status',
			'hbn' => 'Hbn',
			'ref' => 'Ref',
			'cnor' => 'Cnor',
			'cnee' => 'Cnee',
			'swt' => 'Swt',
			'items' => 'Items',
			'tpl' => 'Tpl',
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
		$criteria->compare('api_id',$this->api_id,true);
		$criteria->compare('md',$this->md,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('hbn',$this->hbn,true);
		$criteria->compare('ref',$this->ref,true);
		$criteria->compare('cnor',$this->cnor,true);
		$criteria->compare('cnee',$this->cnee,true);
		$criteria->compare('swt',$this->swt,true);
		$criteria->compare('items',$this->items,true);
		$criteria->compare('tpl',$this->tpl,true);
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
	 * @return YtoOms the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
