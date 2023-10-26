<?php

/**
 * This is the model class for table "pay_bill".
 *
 * The followings are the available columns in table 'pay_bill':
 * @property string $pay_id
 * @property string $bill_id
 * @property string $amount
 * @property string $transaction_date
 */
class PayBill extends oActiveRecord
{
	public $billing_no, $billing_total, $billing_stat;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pay_bill';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pay_id, bill_id, amount', 'required'),
			array('billing_no, billing_total, billing_stat', 'safe'),
			array('pay_id, bill_id', 'length', 'max'=>11),
			array('amount', 'length', 'max'=>10),
			array('sync_xero', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('pay_id, bill_id, amount,sync_xero,transaction_date, billing_no, billing_total, billing_stat', 'safe', 'on'=>'search'),
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
			'payment' => array(self::BELONGS_TO, 'PaymentBilling', 'pay_id'),
			'billing' => array(self::BELONGS_TO, 'Billing', 'bill_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'pay_id' => 'Pay',
			'bill_id' => 'Billing',
			'amount' => 'Amount',
			'billing_no' => 'Billing #',
			'billing_total' => 'Total',
			'billing_stat' => 'Status',
			'sync_xero' => 'Sync Xero',
			'transaction_date' => 'Transaction Date'
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

		$criteria->compare('pay_id',$this->pay_id);
		$criteria->compare('bill_id',$this->bill_id);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('sync_xero',$this->sync_xero,true);
		$criteria->compare('transaction_date',$this->transaction_date,true);

		$with = array();
		if(!empty($this->billing_no)){
			$with[] = 'billing';
			$criteria->compare('billing.no',$this->billing_no,true);
		}
		if(!empty($this->billing_total)){
			$with[] = 'billing';
			$criteria->compare('billing.total',$this->billing_total,true);
		}
		if(!empty($this->billing_stat)){
			$with[] = 'billing';
			$criteria->compare('billing.status',$this->billing_stat);
		}
		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * save transaction date
	 * @return bool
	 */
	protected function beforeSave(){
		if ( empty($this->transaction_date) || $this->transaction_date == '0000-00-00') $this->transaction_date = date('Y-m-d');
		if(empty($this->payment->dpmt) && !empty($this->billing->dpmt)){
			$this->payment->nolog = true;
			$this->payment->dpmt = $this->billing->dpmt;
			$this->payment->save();
		}
		return true;
	}

	/**
	 * get all paid amount
	 * @return mixed
	 */
	public function totalAmount(){
		$sql = 'SELECT SUM(amount) AS t FROM pay_bill  WHERE bill_id = '.$this->bill_id;
		$cmd = Yii::app()->db->createCommand($sql);
		return $cmd->queryScalar();
	}

	/**
	 * get Xero xml data from single payment object
	 * we only sync to xero for all linked to one billing payments
	 * @param $data
	 * @return mixed
	 */
	public static function getXeroXmlData(&$data){
		$localBilling = Billing::model()->findByPk($data->bill_id);
		if ( empty( $localBilling ) ) return '';
		$localPayment = Payment::model()->findByPk($data->pay_id);
		if ( empty( $localPayment ) ) return '';

		$payment = new XePayment();
		$billing = new XeBilling();
		$billing->billingNumber = $localBilling->no;
		$account = new XeAccount();

		$paymentAccountCode = AppHelper::getXeroSetting('import_payment_glcode');
		if ( strtoupper(substr($localBilling->no,0,2)) == 'EX' ) { // in case export billing payment
			$paymentAccountCode = AppHelper::getXeroSetting('export_payment_glcode');
		}

		$account->code = $paymentAccountCode;
		$payment->billing = $billing;
		$payment->account = $account;
		$payment->date = $localPayment->date;
		$payment->amount = $data->amount;
		// we just use reference to map to local pay_bill table
		$payment->reference = $data->pay_id . '-' . $data->bill_id;
		return  $payment->getXmlData();
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PayBill the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
