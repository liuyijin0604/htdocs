<?php

/**
 * This is the model class for table "cargo_process_plan".
 *
 * The followings are the available columns in table 'cargo_process_plan':
 * @property integer $id
 * @property string $ref
 * @property integer $agent_id
 * @property integer $dpt_id
 * @property integer $ot_id
 * @property integer $type
 * @property string $schedule_time
 * @property string $address_id
 * @property integer $pallets
 * @property double $pallets_rate
 * @property string $pallets_note
 * @property string $note
 * @property integer $driver_id
 * @property string $vehicle
 * @property integer $status
 * @property string $meta
 * @property integer $creater
 * @property string $create_time
 * @property integer $modifyer
 * @property string $modify_time
 * @property string $invoice_by
 * @property float $weight
 * @property float $cbm
 */
class CargoProcessPlan extends CActiveRecord
{
	public $mdata = [];

	public static $dpmts = [
		10 => 'Import',
		20 => 'Export',
		30 => 'Air/Sea',
		40 => '3PL',
		50 => 'TopCourierService',
	];

	public static $cargoplan_states = array(
		10 => 'New',
		20 => 'Confirmed',
		30 => 'Synchronized',
		100 => 'Cancel',
	);

	public static $cargoplanlist_states = array(
		10 => 'New',
		20 => 'Confirmed',
		//30 => 'Synchronized',
		30 => 'Processing',
		100 => 'Cancel',
	);

	public static $cargoplan_types = array(
		// 10 => 'Delivery',
		// 20 => 'Pick Up',
		30 => 'Pick Up & Delivery',
		// 40 => 'Interstate',
	);

	public static $address_default = [
		0 =>'Choose One',
		106 => 'Sydney',
		218 => 'Melbourne',
		530 => 'Brisbane',
		"4PX" => '4PX',
		"AP" => 'AP',
		"Punchbowl" => 'Punchbowl',
		"BWU1" => 'BWU1',
		"BWU2" => 'BWU2',
	];

	public static $countries = array(
		'AU' => 'Australia',
	);
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cargo_process_plan';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dpt_id,ot_id,agent_id,type, pallets, driver_id,  status, creater, modifyer, invoice_id', 'numerical', 'integerOnly' => true),
			array('pallets_rate', 'numerical'),
			array('ref,address_id,vehicle,invoice_by,need_invoice', 'length', 'max' => 50),
			array('pallets_note', 'length', 'max' => 1000),
			array('schedule_time,due_time, note, meta, create_time, modify_time', 'safe'),
			array('weight,cbm','length','max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, ref, dpt_id,ot_id,type,agent_id, schedule_time,due_time, address_id, pallets, pallets_note, note, driver_id, vehicle, status, meta, creater, create_time, modifyer, modify_time,invoice_by,need_invoice,numb,weight,cbm', 'safe', 'on' => 'search'),
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
			'assigned' => array(self::BELONGS_TO, 'User', 'creater'),
			'agent' => array(self::BELONGS_TO, 'Org', 'agent_id'),
			'branch' => array(self::BELONGS_TO, 'Org', 'dpt_id'),
			'shipment' => array(self::BELONGS_TO,'ImParcel','meta["shipment_id_no"]'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'numb'=>'Ref No.',
			'ref' => 'Shipment No.',
			'dpt_id' => 'Depot',
			'ot_id' => 'Department',
			'type' => 'Type',
			'agent_id' => 'Charge Account',
			'schedule_time' => 'Request Date',
			'due_time,'=>'Due Date',
			'address_id' => 'Address',
			'pallets' => 'No. Plts',
			'weight' => 'Weight',
			'cbm' => 'CBM',
			'pallets_rate' => 'Pallets Rate',
			'pallets_note' => 'Pallets Note',
			'note' => 'Note',
			'driver_id' => 'Driver',
			'vehicle' => 'Vehicle',
			'status' => 'Status',
			'meta' => 'Meta',
			'creater' => 'From',
			'create_time' => 'Create Time',
			'modifyer' => 'Modifyer',
			'modify_time' => 'Modify Time',
			'invoice_by' => 'Invoice No',
			'invoice_id' => 'Invoice Id',
			'need_invoice' => 'Need Create Invoice',
		);
	}

	public function beforeSave()
	{
		if (!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		return parent::beforeSave();
	}

	public function afterFind()
	{
		if (!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return parent::afterFind();
	}

	public function updateMeta($nolog = true)
	{
		$this->nolog = $nolog;
		$this->meta = json_encode($this->mdata);
		$this->update(['meta']);
	}

	public function getNo(){
		return "PltReq ".$this->id;
	}

	public function getBranch()
	{
		return empty($this->dpt_id) ? '' : $this->branch->shortName(1);
	}

	public function getCreaterName()
	{
		return empty($this->creater) ? '' : $this->assigned->fname . " " . $this->assigned->lname;
	}

	public function getStatus()
	{
		return empty($this->status) ? '' : $this::$cargoplan_states[$this->status];
	}

	public function getCjobStatus(){
		$result = empty($this->status) ? '' : $this::$cargoplan_states[$this->status];
		if(!empty($this->mdata['shipment_id_no'])){
			$objShipment = ImParcel::model()->findByPk($this->mdata['shipment_id_no']);
			if(!empty($objShipment)){
				if(!empty($objShipment->cargo_process)){
					if(!empty($objShipment->cargo_process->job_relation)){
						if(!empty($objShipment->cargo_process->job_relation->job)){
							$result = CargoProcessJob::$processTypes[$objShipment->cargo_process->job_relation->job->status];
						}
					}
				}
			}
		}
		return $result;
	}

	public function getCjobTime(){
		$result = "";
		if(!empty($this->mdata['shipment_id_no'])){
			$objShipment = ImParcel::model()->findByPk($this->mdata['shipment_id_no']);
			if(!empty($objShipment)){
				if(!empty($objShipment->cargo_process)){
					if(!empty($objShipment->cargo_process->job_relation)){
						if(!empty($objShipment->cargo_process->job_relation->job)){
							$result = $objShipment->cargo_process->job_relation->job->created;
						}
					}
				}
			}
		}
		return $result;
	}

	public function getType()
	{
		return empty($this->type) ? '' : $this::$cargoplan_types[$this->type];
	}

	public function getOrgName()
	{
		return empty($this->agent_id) ? '' : Org::model()->findByPk($this->agent_id)->name;
	}

	public function getPalletRate()
	{
		return empty($this->pallets_rate) ? '25' : $this->pallets_rate;
	}
	
	public function getCjobDriver(){
		$result = "";
		if(!empty($this->mdata['shipment_id_no'])){
			$objShipment = ImParcel::model()->findByPk($this->mdata['shipment_id_no']);
			if(!empty($objShipment)){
				if(!empty($objShipment->cargo_process)){
					if(!empty($objShipment->cargo_process->job_relation)){
						if(!empty($objShipment->cargo_process->job_relation->job)){
							$result = Org::model()->findByPk($objShipment->cargo_process->job_relation->job->driver_id)->name;
						}
					}
				}
			}
		}
		return $result;
	}

	public function getInvoiceNo()
	{
		if(!empty($this->invoice_id)){
			[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
			return Invoice::model()->findByPk($this->invoice_id)->no;
			Yii::app()->name = $app_name;
		}
		return "";
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
	public function search($pgn = true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		$with = [];
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('ref', $this->ref, true);
		$criteria->compare('dpt_id', $this->dpt_id);
		$criteria->compare('ot_id', $this->ot_id);
		$criteria->compare('type', $this->type);
		$criteria->compare('agent_id', $this->agent_id);
		$criteria->compare('schedule_time', $this->schedule_time, true);
		$criteria->compare('due_time', $this->due_time, true);
		$criteria->compare('address_id', $this->address_id);
		$criteria->compare('pallets', $this->pallets);
		$criteria->compare('pallets_rate',$this->pallets_rate);
		$criteria->compare('pallets_note', $this->pallets_note, true);
		$criteria->compare('note', $this->note, true);
		$criteria->compare('driver_id', $this->driver_id);
		$criteria->compare('vehicle', $this->vehicle);
		$criteria->compare('status', $this->status);
		$criteria->compare('meta', $this->meta, true);
		$criteria->compare('creater', $this->creater);
		$criteria->compare('create_time', $this->create_time, true);
		$criteria->compare('modifyer', $this->modifyer);
		$criteria->compare('modify_time', $this->modify_time, true);
		$criteria->compare('invoice_by', $this->invoice_by, true);
		$criteria->compare('invoice_id', $this->invoice_id);
		$criteria->compare('weight', $this->weight);
		$criteria->compare('cbm', $this->cbm);

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}
		
		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.id DESC',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CargoProcessPlan the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function getConsolNumber()
	{
		if (!empty($this->mdata['shipment_id_no'])) {
			$shipment = ImParcel::model()->findByPk($this->mdata['shipment_id_no']);
			if (!empty($shipment) && !empty($shipment->consol_id)) {
				$consol = Consol::model()->findByPk($shipment->consol_id);
				if (!empty($consol)) {
					return $consol->no;
				}
			}
		}
	}
}
