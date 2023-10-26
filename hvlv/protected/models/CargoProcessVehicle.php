<?php

/**
 * This is the model class for table "cargo_process_vehicle".
 *
 * The followings are the available columns in table 'cargo_process_vehicle':
 * @property integer $id
 * @property integer $type
 * @property string $vehicle_name
 * @property double $min_cbm
 * @property double $max_cbm
 * @property integer $top_width
 * @property integer $bottom_width
 * @property integer $height
 * @property integer $length
 * @property integer $status
 * @property string $meta
 * @property string $org_id
 * @property string $user_id
 * @property string $plate_number
 * @property string $dpt_id
 */
class CargoProcessVehicle extends CActiveRecord
{
	const enum_type_small_vehicle = 1;
	const enum_type_big_vehicle = 2;
	const dicEnum2Type = [
		self::enum_type_small_vehicle => 'small_vehicle',
		self::enum_type_big_vehicle => 'big_vehicle',
	];

	const enum_status_active = 1;
	const enum_status_inactive = 2;
	const dicEnum2Status = [
		self::enum_status_active => 'active',
		self::enum_status_inactive => 'inactive',
	];


	public $region,$dptId;
	public $deliveryZone=[];
	public $myCargos = [];
	const ACTIVE=1;


	public static $arrayDriversPhone=[
		1=>"",
		2=>"",
		3=>"",
		4=>"",
		5=>"",
		6=>"",
		28=>"",
		31=>"",
	];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cargo_process_vehicle';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, vehicle_name, min_cbm, max_cbm, top_width, bottom_width, height, length, status,org_id,user_id,plate_number,dpt_id', 'required'),
			array('type, top_width, bottom_width, height, length, status', 'numerical', 'integerOnly'=>true),
			array('min_cbm, max_cbm', 'numerical'),
			array('vehicle_name', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, vehicle_name, min_cbm, max_cbm, top_width, bottom_width, height, length, status, meta,org_id,user_id,plate_number,dpt_id', 'safe', 'on'=>'search'),
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
			'type' => 'Type',
			'vehicle_name' => 'Vehicle Name',
			'min_cbm' => 'Min Cbm',
			'max_cbm' => 'Max Cbm',
			'top_width' => 'Top Width',
			'bottom_width' => 'Bottom Width',
			'height' => 'Height',
			'length' => 'Length',
			'status' => 'Status',
			'meta' => 'Meta',
			'org_id'=>'org_id',
			'user_id'=>'user_id',
			'plate_number'=>'plate_number',
			'dpt_id'=>'dpt_id',
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
		$criteria->compare('type',$this->type);
		$criteria->compare('vehicle_name',$this->vehicle_name,true);
		$criteria->compare('min_cbm',$this->min_cbm);
		$criteria->compare('max_cbm',$this->max_cbm);
		$criteria->compare('top_width',$this->top_width);
		$criteria->compare('bottom_width',$this->bottom_width);
		$criteria->compare('height',$this->height);
		$criteria->compare('length',$this->length);
		$criteria->compare('status',$this->status);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('org_id',$this->org_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('plate_number',$this->plate_number);
		$criteria->compare('dpt_id',$this->dpt_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CargoProcessVehicle the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getCargosCBM()
	{
		$volume = 0;
		foreach ($this->myCargos as $key => $c) {
			$volume+=$c[1]->getTotalCBM();
		}
		return $volume;
	}
	public function isFull($cargo)
	{
		$volume = $this->getCargosCBM();
		if($volume+$cargo->getTotalCBM()>$this->max_cbm)
		{
			return true;
		}else
		{
			return false;
		}
	}

	public function canDeliver($cargo)
	{
		if(in_array($cargo->shipment->postcode,$this->deliveryZone))
		{
			return true;
		}
		return false;
	}

	public function canBeSaveToJob()
	{
		$volume = $this->getCargosCBM();
		if($volume>=$this->min_cbm)
		{
			return true;
		}
		return false;
	}
	
}

