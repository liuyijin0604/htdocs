<?php

/**
 * This is the model class for table "supplier_invoice_line".
 *
 * The followings are the available columns in table 'supplier_invoice_line':
 * @property string $id
 * @property string $inv_id
 * @property string $ref
 * @property string $item_code
 * @property string $postcode
 * @property string $amount
 * @property string $gst
 * @property string $det
 * @property string $weight
 * @property string $qty
 * @property string $meta
 * @property string $amount_ex_gst
 */
class SupplierInvoiceLine extends OMetaModel
{
	public function getDbConnection(){
		return self::getTlaConnection();
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'supplier_invoice_line';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('inv_id, item_code, amount,  qty,  amount_ex_gst', 'required'),
			array('inv_id, qty', 'length', 'max'=>10),
			array('amount, amount_ex_gst,weight', 'length', 'max'=>20),
			array('postcode', 'length', 'max'=>45),
			array('ref', 'length', 'max'=>50),
			array('item_code', 'length', 'max'=>100),
			array('gst', 'length', 'max'=>12),
			array('det', 'length', 'max'=>300),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array(' inv_id, ref, item_code, postcode, amount, gst, det, weight, qty, meta, amount_ex_gst', 'safe', 'on'=>'search'),
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
			'parent'=>[self::BELONGS_TO,'SupplierInvoice','inv_id']
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'inv_id' => 'Inv',
			'ref' => 'Ref',
			'item_code' => 'Item Code',
			'postcode' => 'Postcode',
			'amount' => 'Amount',
			'gst' => 'Gst',
			'det' => 'Det',
			'weight' => 'Weight',
			'qty' => 'Qty',
			'meta' => 'Meta',
			'amount_ex_gst' => 'Amount Ex Gst',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('inv_id',$this->inv_id,true);
		$criteria->compare('ref',$this->ref,true);
		$criteria->compare('item_code',$this->item_code,true);
		$criteria->compare('postcode',$this->postcode,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('gst',$this->gst,true);
		$criteria->compare('det',$this->det,true);
		$criteria->compare('weight',$this->weight,true);
		$criteria->compare('qty',$this->qty,true);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('amount_ex_gst',$this->amount_ex_gst,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SupplierInvoiceLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
