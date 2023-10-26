<?php

/**
 * This is the model class for table "air_freight_invoice_reconciliation_line".
 *
 * The followings are the available columns in table 'air_freight_invoice_reconciliation_line':
 * @property string $id
 * @property integer $invoice_id
 * @property string $desc
 * @property string $gst
 * @property string $glcode
 * @property string $amount
 */
class AFInvoiceReconciliationLine extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'air_freight_invoice_reconciliation_line';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('invoice_id', 'numerical', 'integerOnly'=>true),
			array('desc', 'length', 'max'=>200),
			array('gst, glcode, amount', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, invoice_id, desc, gst, glcode, amount', 'safe', 'on'=>'search'),
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
			'af' => array(self::BELONGS_TO, 'AFInvoiceReconciliation', 'invoice_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'invoice_id' => 'Invoice',
			'desc' => 'Description',
			'gst' => 'GST',
			'glcode' => 'Gl Code',
			'amount' => 'Amount',
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
		$criteria->compare('invoice_id',$this->invoice_id);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('gst',$this->gst,true);
		$criteria->compare('glcode',$this->glcode,true);
		$criteria->compare('amount',$this->amount,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AFInvoiceReconciliationLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
