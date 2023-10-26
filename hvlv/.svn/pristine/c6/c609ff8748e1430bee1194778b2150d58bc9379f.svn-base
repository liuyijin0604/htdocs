<?php

/**
 * This is the model class for table "job_line".
 *
 * The followings are the available columns in table 'job_line':
 * @property string $id
 * @property string $job_id
 * @property string $ccode
 * @property string $desc
 * @property string $qty
 * @property string $rate
 * @property integer $inv_gst
 * @property string $supplier_id
 * @property string $cost_amount
 * @property integer $cost_gst
 * @property string $invline_id
 */
class JobLine extends CActiveRecord
{

    public $invAmount,$awb_no;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'job_line';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		//	array('job_id, ccode, desc, qty, rate, inv_gst, cost_amount,cost_gst', 'required'),
            array('job_id, ccode, desc, qty, rate, inv_gst', 'required'),
			array('id, job_id, supplier_id, invline_id', 'length', 'max'=>11),
			array('ccode,inv_gst, cost_gst', 'length', 'max'=>20),
			array('desc', 'length', 'max'=>120),
            array('invoice_ref', 'length', 'max'=>25),
			array('qty, rate, cost_amount', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, job_id, ccode, desc, qty, rate, invoice_ref , invoice_date,invoice_due_date, inv_gst, supplier_id, cost_amount, cost_gst, invline_id', 'safe', 'on'=>'search'),
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
            'job' => array(self::BELONGS_TO, 'Job', 'job_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'job_id' => 'Job',
			'ccode' => 'Ccode',
			'desc' => 'Desc',
			'qty' => 'Qty',
			'rate' => 'Rate',
			'inv_gst' => 'Inv Gst',
			'supplier_id' => 'Supplier',
			'cost_amount' => 'Cost Amount',
			'cost_gst' => 'Cost Gst',
			'invline_id' => 'Invline',
            'invoice_ref' => 'Billing Ref.',
            'invoice_date' => 'Billing Date',
            'invoice_due_date' => 'Billing Due'
		);
	}

    public function getTaxType(){
        return Yii::t(strtolower(__CLASS__), Invoice::$TaxType[$this->tax]);
    }


    public  function hasInvoice(){
        return !empty($this->invline_id) ? 1 : 0;
    }

    public function getSupplierName(){
        if ( !isset($this->supplier_id) ) return '';
        $supplierName = '';
        $supplierMode = Org::model()->findByPk($this->supplier_id);
        if ( !empty($supplierMode) ) $supplierName = $supplierMode->name;
        return $supplierName;
    }

    public function getCCodeDesc(){
        if (!isset($this->ccode) ) return '';
        $desc = '';
       // $ccodeModel = Chargecode::model()->find('status = 1 AND code = :ccode',[':ccode' => $this->ccode]);
       // if ( !empty($ccodeModel) ) $desc .=  ':' . $ccodeModel->name;

        $ccodeKeys = EdiJob::getChargeItemTypes();
        if ( isset($ccodeKeys[$this->ccode]) ) $desc =  $ccodeKeys[$this->ccode];

        return $desc;
    }

    public function getInvAmount(){
        return $this->qty * $this->rate;
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
		$criteria->compare('job_id',$this->job_id);
		$criteria->compare('ccode',$this->ccode,true);
		$criteria->compare('desc',$this->desc,true);
        $criteria->compare('invoice_ref',$this->invoice_ref,true);
		$criteria->compare('qty',$this->qty);
		$criteria->compare('rate',$this->rate);
		$criteria->compare('inv_gst',$this->inv_gst);
		$criteria->compare('supplier_id',$this->supplier_id);
		$criteria->compare('cost_amount',$this->cost_amount,true);
		$criteria->compare('cost_gst',$this->cost_gst);
		$criteria->compare('invline_id',$this->invline_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize' => 100,
            )
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return JobLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
