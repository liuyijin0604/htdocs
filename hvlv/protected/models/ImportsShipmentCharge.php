<?php

/**
 * This is the model class for table "imports_shipment_charge".
 *
 * The followings are the available columns in table 'imports_shipment_charge':
 * @property integer $id
 * @property integer $shipment_id
 * @property string $delivery_fee
 * @property string $mh_fee
 * @property string $os_fee
 * @property string $rsd_fee
 * @property string $fuel_fee
 * @property string $levy_fee
 * @property string $remote_fee
 * @property string $kp_discount
 * @property string $surcharge_amount
 */
class ImportsShipmentCharge extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'imports_shipment_charge';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('shipment_id', 'required'),
			array('shipment_id', 'numerical', 'integerOnly'=>true),
			array('value', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, shipment_id, code, type, value', 'safe', 'on'=>'search'),
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

	public static function cleanShipmentChargeRecord($shipmentId)
	{
		self::model()->deleteAll("shipment_id = :shipmentId",[':shipmentId'=>$shipmentId]);
	}

	public static function saveSurchargeArrIntoCharge($codeArr,$chargeArr,$shipmentId)
	{
		if(!empty($codeArr))
		{
			foreach ($codeArr as $key => $code)
			{
				self::createShipmentChargeRecord($shipmentId,$code->code,$code->type,$chargeArr[$key]);
			}
		}
	}

	public static function createShipmentChargeRecord($shipmentId,$code,$type,$value)
	{
		$imsc = new ImportsShipmentCharge();
		$imsc->shipment_id = $shipmentId;
		$imsc->code = $code;
		$imsc->type = $type;
		$imsc->value = $value;
		$imsc->save();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'shipment_id' => 'Shipment',
			'delivery_fee' => 'Delivery Fee',
			'mh_fee' => 'Mh Fee',
			'os_fee' => 'Os Fee',
			'rsd_fee' => 'Rsd Fee',
			'fuel_fee' => 'Fuel Fee',
			'levy_fee' => 'Levy Fee',
			'remote_fee' => 'Remote Fee',
			'kp_discount' => 'Kp Discount',
			'surcharge_amount' => 'Surcharge Amount',
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
		$criteria->compare('shipment_id',$this->shipment_id);
		$criteria->compare('delivery_fee',$this->delivery_fee,true);
		$criteria->compare('mh_fee',$this->mh_fee,true);
		$criteria->compare('os_fee',$this->os_fee,true);
		$criteria->compare('rsd_fee',$this->rsd_fee,true);
		$criteria->compare('fuel_fee',$this->fuel_fee,true);
		$criteria->compare('levy_fee',$this->levy_fee,true);
		$criteria->compare('remote_fee',$this->remote_fee,true);
		$criteria->compare('kp_discount',$this->kp_discount,true);
		$criteria->compare('surcharge_amount',$this->surcharge_amount,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ImportsShipmentCharge the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
