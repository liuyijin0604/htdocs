<?php

/**
 * This is the model class for table "seized_shipment".
 *
 * The followings are the available columns in table 'seized_shipment':
 * @property integer $id
 * @property integer $shipment_id
 * @property integer $consol_id
 * @property string $create_time
 * @property integer $type
 * @property integer $shipment_status
 */
class SeizedShipment extends CActiveRecord
{
	public static $types = [
		1 => 'Fully Seized',
		2 => 'Partially Seized',
		3 => 'LCL Inspection',
		4 => 'LCL Return'
	];

	const FULLY_SEIZED = 1;
	const PARTIALLY_SEIZED = 2;
	const LCL_INSPECTION = 3;
	const LCL_RETURN = 4;

	public $hbn, $consol_no, $agent_id;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seized_shipment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('shipment_id, consol_id, create_time, type, shipment_status, user_id', 'required'),
			array('shipment_id, consol_id, type, shipment_status, user_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, shipment_id, consol_id, create_time, type, shipment_status, user_id, hbn, consol_no, agent_id', 'safe', 'on'=>'search'),
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
			'consol' => [self::BELONGS_TO, 'Consol', 'consol_id'],
			'user' => [self::BELONGS_TO, 'User', 'user_id'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'shipment_id' => 'Shipment',
			'consol_id' => 'Consol',
			'create_time' => 'Create Time',
			'type' => 'Type',
			'shipment_status' => 'Shipment Status',
			'user_id' => 'User',
			'hbn' => 'Connote',
			'consol_no' => 'Consol No',
			'agent_id' => 'Agent',
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
		$criteria->compare('consol_id',$this->consol_id);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('shipment_status',$this->shipment_status);

		$with = [
			'shipment',
			'consol',
			'user'
		];

		if (!empty($this->hbn)) {
			$criteria->addCondition("shipment.hbn like '%{$this->hbn}%'");
		}

		if (!empty($this->consol_no)) {
			$criteria->addCondition("consol.no like '%{$this->consol_no}%'");
		}

		if (!empty($this->agent_id)) {
			$criteria->addCondition("shipment.agent_id = {$this->agent_id}");
		}

		$criteria->with = $with;
		$sort = new CSort();
		$sort->defaultOrder = 'create_time DESC';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => $sort,
			'pagination' => array('pageSize' => 30),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SeizedShipment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getInvoiceId()
	{
		$invoice = Invoice::model()->findByAttributes(array('pid' => $this->shipment_id, 'type' => Invoice::INVOICE_TYPE_CUSTOMS_SEIZURE), '`status` != ' . Invoice::INVOICE_STATUS_CACELLED);
		return !empty($invoice) ? $invoice->id : 0;
	}
}
