<?php

/**
 * This is the model class for table "exconsol_cost".
 *
 * The followings are the available columns in table 'exconsol_cost':
 * @property integer $id
 * @property string $consol_id
 * @property string $billing_id
 * @property integer $type
 * @property integer $status
 * @property double $amount
 * @property string $meta
 */
class ExconsolCost extends CActiveRecord
{

	public static $types = array(
		'1' => 'Pcs',
		'2' => 'Weight',
		'3' => 'Air Freight',
		'4' => 'Clearance',
		'5' => 'Delivery',
		'6' => 'Duty',
		'9' => 'Others'
	);

	public static $states = array(
		1 => 'Pending',
		5 => 'Approved',
		9 => 'Posted',
		10 => 'Archived',
	);

	public $mdata = [], $billing_no, $consol_no, $invoice_no, $consol_poc;
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'exconsol_cost';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('billing_id, type, status, amount', 'required'),
			array('consol_id, meta', 'safe'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('consol_id, billing_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, consol_id, billing_id, type, status, amount, meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'bill' => array(self::BELONGS_TO, 'Billing', 'billing_id'),
			'consol' => array(self::BELONGS_TO, 'ExcoConsol', 'consol_id'),
		);
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
			'consol_id' => 'Consol',
			'billing_id' => 'Billing',
			'type' => 'Type',
			'status' => 'Status',
			'amount' => 'Amount',
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
		$criteria->compare('consol_id',$this->consol_id);
		$criteria->compare('billing_id',$this->billing_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('amount',$this->amount);
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

	public function search1($pgn = true, $ps = 30, $ec = false) {
		$criteria = new CDbCriteria;
		$criteria->select = 'billing_id, consol_id, status';
		$criteria->group = 'billing_id, consol_id';
		$criteria->compare('bill.no', $this->billing_no, true);
		$criteria->compare('bill.billing_cref', $this->invoice_no, true);
		$criteria->compare('consol.no', $this->consol_no, true);
		$criteria->compare('consol.poc', ExChannel::getCode($this->consol_poc), true);
		$criteria->compare('t.status', $this->status);
		$with = ['consol', 'bill'];

		if (!empty($with)) {
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

	public function getStatus() {
		return Yii::t(strtolower(__CLASS__), empty(self::$states[$this->status])? '' : self::$states[$this->status]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExconsolCost the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
