<?php

/**
 * This is the model class for table "cargo_process_bidding".
 *
 * The followings are the available columns in table 'cargo_process_bidding':
 * @property integer $id
 * @property integer $shipment_id
 * @property integer $cargo_process_id
 * @property integer $status
 * @property integer $op_cost
 * @property string $str_date
 * @property string $end_date
 * @property string $str_time
 * @property string $end_time
 * @property string $questions
 * @property string $note
 * @property string $meta
 * @property string $createtime
 * @property integer $creater
 * @property integer $dpt_id
 */
class CargoProcessBidding extends CActiveRecord
{
	public $isFBA,$isB2B,$isB2C,$isInterState;
	public $deliverydate;
	public $driver_id;

	public static $statusTypes=[
		10=>'Active',
		100=>'Delete'
	];

	public static $hasBidding=[
		0 => 'All',
		1 => 'No',
		2 => 'Yes'
	];

	public static $unloading_Types=[
		1=>'With Forklift',
		2=>'Without Forklift'
	];

	public static $address_Types=[
		1=>'Commercial',
		2=>'Residential'
	];

	public static $delivery_Ranges=[
		1=>'Local',
		2=>'Interstate'
	];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cargo_process_bidding';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('shipment_id, cargo_process_id, status, creater, dpt_id, address_type, delivery_range, unload_type', 'numerical', 'integerOnly'=>true),
            array('op_cost', 'numerical'),
            array('note', 'length', 'max'=>500),
            array('str_date, end_date, str_time, end_time, questions, meta, createtime', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, shipment_id, cargo_process_id, status, op_cost, str_date, end_date, str_time, end_time, questions, note, meta, createtime, creater, dpt_id, address_type, delivery_range, unload_type,$driver_id', 'safe', 'on'=>'search'),
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
			'shipment' => [self::BELONGS_TO, 'ImParcel', 'shipment_id'],
			'cargo_process' => [self::BELONGS_TO, 'CargoProcess','cargo_process_id'],
			'cargo_process_bidding_detail' => [self::HAS_ONE,'CargoProcessBiddingDetail','bid_id'],
			'cargo_process_bidding_details' => [self::HAS_MANY,'CargoProcessBiddingDetail','bid_id'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Order ID',
			'hbn' => 'Connote',
			'ref' => 'Ref',
			'shipment_id' => 'Ref Number',
			'cargo_process_id' => 'Cargo Process',
			'status' => 'Status',
			'op_cost' => 'TLA Price',
			'str_date' => 'Str Date',
			'end_date' => 'End Date',
			'str_time' => 'Str Time',
			'end_time' => 'End Time',
			'questions' => 'Questions',
			'note' => 'Note',
			'meta' => 'Meta',
			'createtime' => 'Createtime',
			'creater' => 'Creater',
			'deliverydate' => 'Delivery Date',
			'dpt_id' => 'Dpt',
			'isFBA' => 'FBA',
			'isB2B' => 'B2B',
			'isB2C' => 'B2C',
			'isInterState' => 'InterState',
			'address_type' => 'Address Type',
            'delivery_range' => 'Delivery Range',
            'unload_type' => 'Unload Type',
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
	public function search($pgn = true,$ps = 50,$ec = false, $defaultOrder = true)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		$with = [];
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('cargo_process_id',$this->cargo_process_id);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('op_cost',$this->op_cost);

		if(!empty($this->deliverydate)){
			$criteria->addCondition("t.str_date <= '{$this->deliverydate}' and t.end_date >= '{$this->deliverydate}'");
		}
		
		$criteria->compare('str_time',$this->str_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('questions',$this->questions,true);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('createtime',$this->createtime,true);
		$criteria->compare('creater',$this->creater);
		$criteria->compare('t.dpt_id',$this->dpt_id);
		if(!empty($this->address_type)){
			$criteria->compare('address_type',$this->address_type);
		}
		if(!empty($this->unload_type)){
			$criteria->compare('unload_type',$this->unload_type);
		}
		if(!empty($this->delivery_range)){
			$criteria->compare('delivery_range',$this->delivery_range);
		}

		if(!empty($this->driver_id)){
			$with[]='cargo_process_bidding_detail';
			$criteria->addCondition("cargo_process_bidding_detail.driver_id = {$this->driver_id}");
		}

		if(!empty($this->shipment_id)){
			$with[]='shipment';
			$criteria->addCondition("shipment.hbn like '%{$this->shipment_id}%'");
		}

		if (!empty($this->ref)) {
			$with[]='shipment';
			$criteria->addCondition("shipment.ref like '%{$this->ref}%'");
		}


		if ($this->isFBA == 1 || $this->isB2B == 1 || $this->isB2C == 1 || $this->isInterState == 1) {
			$strType = "";
			if ($this->isFBA == 1) {
				$strType .= "2,";
			}
			if ($this->isB2B == 1) {
				$strType .= "4,";
			}
			if ($this->isB2C == 1) {
				$strType .= "1,";
			}
			if ($this->isInterState == 1) {
				$strType .= "6,";
			}
			$strType = rtrim($strType, ",");
			$with[]='cargo_process';
			$criteria->addCondition('cargo_process.type in (' . $strType . ')');
		}
		//$with[]='cargo_process_bidding_detail';
		$criteria->with = array_unique($with);
		$sort = new CSort();
		$sort->defaultOrder = 't.id DESC';
		$pagerparams = $_GET;
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>$sort,
			'pagination'=>$pgn?[
				'pageSize'=>$ps,
				'params'=>$pagerparams,
			]:false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CargoProcessBidding the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getReservePrice(){
		$objCargoProcessBiddingDetails = CargoProcessBiddingDetail::model()->findAll('bid_id = :bid_id',[':bid_id'=>$this->id]);
		$numTempCost = $this->op_cost;
		foreach($objCargoProcessBiddingDetails as $objCargoProcessBiddingDetail){
			if(!empty($objCargoProcessBiddingDetail->cost)){
				if($objCargoProcessBiddingDetail->cost < $numTempCost){
					$numTempCost = $objCargoProcessBiddingDetail->cost;
				}
			}
		}
		return $numTempCost;
	}

	public function getCargoType(){
		return CargoProcess::$cargoTypeList[$this->cargo_process->type];
	}

	public function getFromString(){
		$srtFromString = "";
		if(!empty($this->shipment->consol->dpt_id)){
			$objOrg = Org::model()->findByPk($this->shipment->consol->dpt_id);
			if(!Empty($objOrg)){
				$srtFromString = $objOrg->suburb." ".$objOrg->postcode;
			}
		}
		if(!empty($this->shipment->cnor->country)){
			if($this->shipment->cnor->country == 'Australia' or $this->shipment->cnor->country == 'AU' or $this->shipment->cnor->country == 'Au'){
				if(!empty($this->shipment->cnor->suburb)||$this->shipment->cnor->postcode){
					return $this->shipment->cnor->suburb." ".$this->shipment->cnor->postcode;
				}
			}
		}
		return $srtFromString;
	}

	public function getToString(){
		$strToString = "";
		if(!empty($this->shipment->cnee)){
			return $this->shipment->cnee->suburb." ".$this->shipment->cnee->postcode;
		}
	}

	public function getBiddingDate(){
		return date('Y-m-d',strtotime($this->str_date))." to ".date('Y-m-d',strtotime($this->end_date));
	}

	public function getReceivbleTime(){
		return $this->str_time." to ".$this->end_time;
	}

	public function getBiddingTabName(){
		return "Biddings ".$this->id;
	}

	public function getDay(){
		$dt_start = strtotime($this->str_date);
        $dt_end = strtotime($this->end_date);
 
        $day[date('Y-m-d',$dt_start)] = date('Y-m-d',$dt_start);
 
        while ($dt_start<$dt_end){
            $dt_start = strtotime('+1 day',$dt_start);
            $day[date('Y-m-d',$dt_start)] = date('Y-m-d',$dt_start);
        }
        return $day;
	}

	public function getMyCost($driver_id){
		$strState = "";
		$numCost = self::getReservePrice();
		$objCargoProcessBiddingDetail = CargoProcessBiddingDetail::model()->find('driver_id = :driver_id and bid_id =:bid_id and status = 1',[':driver_id'=>$driver_id,':bid_id'=>$this->id]);
		if(!empty($objCargoProcessBiddingDetail)){
			$objCargoProcessBidding = CargoProcessBidding::model()->findByPk($this->id);
			if(!empty($objCargoProcessBidding)){
				if(!empty($objCargoProcessBidding->cargo_process->job_relation->job)){
					if($objCargoProcessBidding->cargo_process->job_relation->job->driver_id==$driver_id){
						$strState = " (Successful)";
					}else{
						$strState = " (Fail)";
					}
				}else{
					if(!empty($numCost)&&$numCost == $objCargoProcessBiddingDetail->cost){
						$strState = " (Best Price)";
					}
				}
			}
			return $objCargoProcessBiddingDetail->cost.$strState;
		}
		return "";
	}

	public function getBiddingForlift(){
		if(!empty($this->unload_type==1)){
			return "✓";
		}else{
			return "✕";
		}
	}

	public function getBestBiddingDetailCost(){
		if(!empty($this->cargo_process_bidding_details)){
			$numTempCost = 0;
			$objCargoProcessBiddingDetails = $this->cargo_process_bidding_details;
			foreach($objCargoProcessBiddingDetails as $objCargoProcessBiddingDetail){
				if(!empty($objCargoProcessBiddingDetail->cost)){
					if($objCargoProcessBiddingDetail->cost < $numTempCost){
						$numTempCost = $objCargoProcessBiddingDetail->cost;
					}
				}
			}
			return $numTempCost;
		}
	}
}
