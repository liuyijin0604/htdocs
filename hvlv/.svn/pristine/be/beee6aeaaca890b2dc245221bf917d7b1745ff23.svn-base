<?php

/**
 * This is the model class for table "ledger".
 *
 * The followings are the available columns in table 'ledger':
 * @property string $id
 * @property string $from_id
 * @property string $to_id
 * @property string $cra
 * @property string $dea
 * @property string $fid
 * @property string $model
 * @property integer $type
 * @property string $chgcode
 * @property integer $currency
 * @property string $exchange_rate
 * @property integer $status
 * @property string $no
 * @property string $ref
 * @property string $date
 * @property string $due
 * @property string $total
 * @property string $accrual_amount
 * @property string $lines
 * @property string $bwf
 * @property string $meta
 * @property string $notes
 */
class Ledger extends CActiveRecord
{

	public $supplierId;
	public $mdata = array();

	public static $currencies = array(
		'1' => 'AUD',
		'2' => 'USD',
		'3' => 'RMB',
		'4' => 'HKD',
		'5' => 'EUR',
		'6' => 'GBP',
	);
	
	public static $types = array(
		'10' => 'AR Inv.',
		'30' => 'AP Inv.',
		'40' => 'Import Def',
		'50' => 'Export Def',
		'70' => 'Rev.',
		'90' => 'Credit Note',
	);
	const TYPE_IMPORT_DEF = 40;
	const TYPE_EXPORT_DEF = 50;
	const TYPE_AP_INV = 30;


	public static $states = array(
		'1' => 'Pending',
		'6' => 'Posted',
		'9' => 'Approved',
		'10' => 'Deleted',
	);

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ledger';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('from_id, to_id, cra, dea, fid, model, type, chgcode, currency, status, no, ref, date, due, total, lines, bwf, meta, notes', 'safe'),
			array('type, currency, status', 'numerical', 'integerOnly'=>true),
			array('from_id, to_id, cra, dea, fid, bwf', 'length', 'max'=>11),
			array('model, no, ref', 'length', 'max'=>50),
			array('total,accrual_amount', 'length', 'max'=>12),
			array('chgcode', 'length', 'max'=>20),
			array('exchange_rate', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, from_id, to_id, cra, dea, fid, model, type, chgcode, currency,exchange_rate, status, no, ref, date, due, total, accrual_amount,lines, bwf, meta, notes', 'safe', 'on'=>'search'),
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
			'creditor' => array(self::BELONGS_TO,'Org','to_id'),
			'chargecode' => array(self::BELONGS_TO,'Chargecode','chgcode')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'from_id' => 'Creditor',
			'to_id' => 'To',
			'cra' => 'Cra',
			'dea' => 'Dea',
			'fid' => 'Fid',
			'model' => 'Model',
			'type' => 'Type',
			'chgcode' => 'Charge Code',
			'currency' => 'Currency',
			'exchange_rate' => 'Exchange Rate',
			'status' => 'Status',
			'no' => 'Invoice',
			'ref' => 'Ref',
			'date' => 'Date',
			'due' => 'Due',
			'total' => 'Local Amount',
			'accrual_amount' => 'Estimated Amount',
			'lines' => 'Lines',
			'bwf' => 'Bwf',
			'meta' => 'Meta',
			'notes' => 'Description',
		);
	}


	public function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		return true;
	}

	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return true;
	}

	public function updateMeta($nolog=true){
		$this->nolog = $nolog;
		$this->meta = json_encode($this->mdata);
		$this->update(['meta']);
	}

	public static function getAllChargeCodes(){
		$chargeCodes = array();
		$mChargeCodes = Chargecode::model()->findAll('status = 1 AND id > 0');
		foreach ( $mChargeCodes as  $chargeCode ) {
			$chargeCodes[$chargeCode->code] =  '(' . $chargeCode->code .')'.  $chargeCode['description'];
		}
		return $chargeCodes;
	}


	/**
	 * @return array
	 */
	public function getAllSuppliers(){
		// we think all Courier CN(10), Courier AU(15), Supplier(70) as all supplier type
		$criteria = new CDbCriteria();
		$criteria->addInCondition('type',array(10,15,70));
		$orgs = Org::model()->findAll($criteria);
		$suppliers = array();
		foreach ( $orgs as $org ) {
			$suppliers[$org->id] = $org->code . ':' . $org->name;
		}
		return $suppliers;
	}

	public function getStatus(){
		return Yii::t(strtolower(__CLASS__), self::$states[$this->status]);
	}

	public function getCCodeDesc(){
		if (!isset($this->chgcode) ) return '';
		$desc = $this->chgcode;
		$ccodeModel = Chargecode::model()->find('status = 1 AND code = :ccode',[':ccode' => $this->chgcode]);
		if ( !empty($ccodeModel) ) $desc .=  ':' . $ccodeModel->name;
		return $desc;
	}

	/**
	 * @param $rs
	 * @param $key
	 * @return int
	 */
	public function getTotal(&$rs, $key){
		$total = 0;
		foreach ($rs as $r) {
			switch ( $key ) {
				case 'total' :
					$total += $r->total;
					break;
				case 'accrual_amount':
					$total += $r->accrual_amount;
					break;
			}
		}
		return $total;
	}

	/**
	 * add defaut cost items for specified console
	 * @param $cosoleId
	 * @param int $type default 40 means for import parcels
	 *    type 40 means all default cost for import parcels
	 *    type 50 means all default cost for export parcels
	 */
	public static function createDefCostConsole($cosoleId,$type = 40){
		// get all default cost items
		$defCost = Ledger::model()->findAll('type = :ntype' , [':ntype' => $type]);
		foreach ( $defCost as $cost ) {
			$newCost = new Ledger();
			$newCost->attributes = $cost->getAttributes();
			$newCost->fid = $cosoleId;
			$newCost->type = self::TYPE_AP_INV; // set as AP inv. type
			$newCost->date = date('Y-m-d');
			$newCost->save();
		}
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
		$criteria->compare('from_id',$this->from_id,true);
		$criteria->compare('to_id',$this->to_id,true);
		$criteria->compare('cra',$this->cra,true);
		$criteria->compare('dea',$this->dea,true);
		$criteria->compare('fid',$this->fid);
		$criteria->compare('model',$this->model);
		$criteria->compare('type',$this->type);
		$criteria->compare('chgcode',$this->chgcode);
		$criteria->compare('currency',$this->currency);
		$criteria->compare('status',$this->status);
		$criteria->compare('exchange_rate',$this->exchange_rate);
		$criteria->compare('no',$this->no,true);
		$criteria->compare('ref',$this->ref,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('due',$this->due,true);
//		$criteria->compare('total',$this->total);
  //      $criteria->compare('accrual_amount',$this->accrual_amount);
		$criteria->compare('lines',$this->lines,true);
		$criteria->compare('bwf',$this->bwf,true);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('notes',$this->notes,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Ledger the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
