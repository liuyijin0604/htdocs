<?php

/**
 * This is the model class for table "supplier_invoice".
 *
 * The followings are the available columns in table 'supplier_invoice':
 * @property string $id
 * @property string $org_id
 * @property string $created
 * @property string $inv_date
 * @property integer $type
 * @property integer $status
 * @property string $inv_no
 * @property integer $currency
 * @property string $total
 * @property string $gst
 * @property string $meta
 * @property string $total_ex_gst
 */
class SupplierInvoice extends OMetaModel
{

	public function getDbConnection(){
		return self::getTlaConnection();
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'supplier_invoice';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, type, status, inv_no, currency, total_ex_gst', 'required'),
			array('type, status, currency', 'numerical', 'integerOnly'=>true),
			array('org_id,, total, gst, total_ex_gst', 'length', 'max'=>20),
			array('inv_no', 'length', 'max'=>45),
			array('created, inv_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, org_id, created, inv_date, type, status, inv_no, currency, total, gst, total_ex_gst', 'safe', 'on'=>'search'),
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
			'lines' => array(self::HAS_MANY, 'SupplierInvoiceLine', 'inv_id'),
			'rec' => array(self::HAS_ONE, 'SiReconcile', 'supplier_invoice_id'),
			'billing'=> array(self::HAS_ONE,'Billing',['billing_cref' => 'inv_no'],'on'=>'status!=11')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'org_id' => 'Org',
			'created' => 'Created',
			'inv_date' => 'Inv Date',
			'type' => 'Type',
			'status' => 'Status',
			'inv_no' => 'Inv No',
			'currency' => 'Currency',
			'total' => 'Total',
			'gst' => 'Gst',
			'total_ex_gst' => 'Total Ex Gst',
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
		$criteria->compare('org_id',$this->org_id,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('inv_date',$this->inv_date,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('status',$this->status);
		$criteria->compare('inv_no',$this->inv_no,true);
		$criteria->compare('currency',$this->currency);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('gst',$this->gst,true);
		$criteria->compare('total_ex_gst',$this->total_ex_gst,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SupplierInvoice the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
