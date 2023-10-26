<?php

/**
 * This is the model class for table "wms_billing".
 *
 * The followings are the available columns in table 'wms_billing':
 * @property string $id
 * @property string $task_id
 * @property integer $status
 * @property string $code
 * @property string $desc
 * @property string $date
 * @property double $price
 * @property double $qty
 * @property string $meta
 */
class WmsBilling extends CActiveRecord
{
	public $mdata = [];

	public static $codes = [
		'STK/H' => [WmsOrgQuote::QUOTE_STOCKTAKE_HOUR, 'Stocktake / Hour'],
		'STK/D' => [WmsOrgQuote::QUOTE_STOCKTAKE_DAY, 'Stocktake / Day'],
		'CPL' => [WmsOrgQuote::QUOTE_MISC_CHANGE_PALLET, 'Change Pallet'],
		'PLT/S' => [WmsOrgQuote::QUOTE_MISC_STANDARD_PALLET, 'Standard Pallet'],
		'PLT/F' => [WmsOrgQuote::QUOTE_MISC_FUMIGATED_WOODEN_PALLET, 'Fumigated Wooden Pallet'],
		'PLT/P' => [WmsOrgQuote::QUOTE_MISC_PLASTIC_PALLET, 'Plastic Pallet'],
		'WRP' => [WmsOrgQuote::QUOTE_MISC_PALLET_WRAP, 'Pallet Wrap'],
		'PLH' => [WmsOrgQuote::QUOTE_MISC_PALLET_HIRE_DAY, 'Pallet Hire / Day'],
		'PDP' => [WmsOrgQuote::QUOTE_MISC_PICKUP_DELIVERY_PALLET, 'Pickup or Delivery / Pallet'],
		'DTP' => [WmsOrgQuote::QUOTE_MISC_DELIVERY_AIRPORT_PALLET, 'Delivery to Airport / Pallet'],
		'LBR/H' => [WmsOrgQuote::QUOTE_MISC_LABOUR_HOUR, 'Labour / Hr'],
		'LBR/O' => [WmsOrgQuote::QUOTE_MISC_OVERTIME_LABOUR_HOUR, 'Overtime Labour / Hr'],
		'FLT/H' => [WmsOrgQuote::QUOTE_MISC_FORK_TRUCK_HOUR, 'Fork/Reach Truck / Hr'],
		'FLT/O' => [WmsOrgQuote::QUOTE_MISC_OVERTIME_FOR_TRUCK_HOUR, 'Overtime Fork/Reach Truck / Hr'],
		'LBL' => [WmsOrgQuote::QUOTE_MISC_LABELLING, 'Labelling'],
	];

    // define invoice all status
	public static $states = array(
		'1' => 'Auto',
		'2' => 'Manual',
	);

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'wms_billing';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_id, code', 'required'),
			array('desc, price, qty', 'safe'),
			array('date, meta', 'safe'),
			array('price, qty', 'numerical'),
			array('task_id', 'length', 'max'=>11),
			array('code', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, task_id, code, desc, date, price, qty, status, meta', 'safe', 'on'=>'search'),
		);
	}

	public static function cndList(){
		$r = [];
		foreach(self::$codes as $c => $a){
			$r[$c] = $a[1];
		}
		return $r;
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
		if(empty($this->date)) $this->date = date('Y-m-d');
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
			'task_id' => 'Task',
			'code' => 'Code',
			'desc' => 'Desc',
			'date' => 'Date',
			'price' => 'Price',
			'qty' => 'Qty',
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
		$criteria->compare('task_id',$this->task_id);
		if(empty($this->status)){
			$criteria->compare('status', '<10');
		}else{
			$criteria->compare('status',$this->status);
		}
		$criteria->compare('code',$this->code,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('qty',$this->qty);
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
	 * @return WmsBilling the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
