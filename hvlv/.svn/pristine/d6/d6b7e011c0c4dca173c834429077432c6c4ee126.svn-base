<?php

/**
 * This is the model class for table "pay_inv".
 *
 * The followings are the available columns in table 'pay_inv':
 * @property string $pay_id
 * @property string $inv_id
 * @property string $amount
 * @property string $exrate
 * @property string $transaction_date
 */
class PayInv extends oActiveRecord
{
	public $inv_no, $inv_total, $inv_stat, $inv_org, $inv_suborg;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pay_inv';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pay_id, inv_id, amount', 'required'),
			array('inv_no, inv_total, exrate, inv_stat', 'safe'),
			array('pay_id, inv_id', 'length', 'max' => 11),
			array('amount', 'length', 'max' => 10),
			array('sync_xero', 'numerical', 'integerOnly' => true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('pay_id, inv_id, amount, exrate, sync_xero,transaction_date, inv_no, inv_total, inv_stat, inv_org, inv_suborg', 'safe', 'on' => 'search'),
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
			'payment' => array(self::BELONGS_TO, 'Payment', 'pay_id'),
			'invoice' => array(self::BELONGS_TO, 'Invoice', 'inv_id'),
		);
	}

		// for TLA
	public function getDbConnection(){
		return self::getTlaConnection();
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'pay_id' => 'Pay',
			'inv_id' => 'Inv',
			'amount' => 'Amount',
			'exrate' => 'Exchange Rate',
			'inv_no' => 'Inv #',
			'inv_total' => 'Total',
			'inv_stat' => 'Status',
			'sync_xero' => 'Sync Xero',
			'transaction_date' => 'Transaction Date',
			'inv_org' => 'Org',
			'inv_suborg' => 'Sub',
		);
	}

	public function afterFind()
	{
		if ($this->inv_id > 500000) {
			[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
			$this->payment = Payment::model()->findByPk($this->pay_id);
			Yii::app()->name = $app_name;
		} else {
			[$app_name, Yii::app()->name] = [Yii::app()->name, 'HVLV APP'];
			$this->payment = Payment::model()->findByPk($this->pay_id);
			Yii::app()->name = $app_name;
		}
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

		$criteria = new CDbCriteria;

		$criteria->compare('pay_id', $this->pay_id);
		$criteria->compare('inv_id', $this->inv_id);
		$criteria->compare('t.amount', $this->amount, true);
		$criteria->compare('exrate', $this->exrate, true);
		$criteria->compare('t.sync_xero', $this->sync_xero, true);
		$criteria->compare('t.transaction_date', $this->transaction_date, true);

		$with = array();
		if (!empty($this->inv_no)) {
			$with[] = 'invoice';
			$criteria->compare('invoice.no', $this->inv_no, true);
		}
		if (!empty($this->inv_total)) {
			$with[] = 'invoice';
			$criteria->compare('invoice.total', $this->inv_total, true);
		}
		if (!empty($this->inv_stat)) {
			$with[] = 'invoice';
			$criteria->compare('invoice.status', $this->inv_stat);
		}
		if (!empty($this->inv_org)) {
			$with[] = 'invoice.cust';
			$criteria->compare('cust.name', $this->inv_org);
		}
		if (!empty($this->inv_suborg)) {
			$with[] = 'invoice.subcust';
			$criteria->compare('subcust.name', $this->inv_suborg);
		}
		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if ($ec) {
			$criteria->mergeWith($ec);
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.pay_id DESC',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * save transaction date
	 * @return bool
	 */
	protected function beforeSave()
	{
		if (empty($this->transaction_date) || $this->transaction_date == '0000-00-00') {
			$this->transaction_date = date('Y-m-d');
		} else if ($this->transaction_date < $this->payment->transaction_date) {
			$this->transaction_date = $this->payment->transaction_date;
		}

		if (empty($this->payment->dpmt) && !empty($this->invoice->dpmt)) {
			$this->payment->nolog = true;
			$this->payment->dpmt = $this->invoice->dpmt;
			$this->payment->save();
		}
		if (empty($this->exrate) || $this->exrate == 0) {
			$this->exrate = 1;
		}

		return true;
	}

	/**
	 * get all paid amount
	 * @return mixed
	 */
	public function totalAmount()
	{
		$sql = 'SELECT SUM(amount) AS t FROM pay_inv  WHERE inv_id = ' . $this->inv_id;
		$cmd = Yii::app()->db->createCommand($sql);
		return $cmd->queryScalar();
	}

	/**
	 * get Xero xml data from single payment object
	 * we only sync to xero for all linked to one invoiced payments
	 * @param $data
	 * @return mixed
	 */
	public static function getXeroXmlData(&$data)
	{
		$localInvoice = Invoice::model()->findByPk($data->inv_id);
		if (empty($localInvoice)) {
			return '';
		}

		$localPayment = Payment::model()->findByPk($data->pay_id);
		if (empty($localPayment)) {
			return '';
		}

		$payment = new XePayment();
		$invoice = new XeInvoice();
		$invoice->invoiceNumber = $localInvoice->no;
		$account = new XeAccount();

		$paymentAccountCode = AppHelper::getXeroSetting('import_payment_glcode');
		if (strtoupper(substr($localInvoice->no, 0, 2)) == 'EX') {
			// in case export invoice payment
			$paymentAccountCode = AppHelper::getXeroSetting('export_payment_glcode');
		}

		$account->code = $paymentAccountCode;
		$payment->invoice = $invoice;
		$payment->account = $account;
		$payment->date = $localPayment->date;
		$payment->amount = $data->amount;
		// we just use reference to map to local pay_inv table
		$payment->reference = $data->pay_id . '-' . $data->inv_id;
		return $payment->getXmlData();
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PayInv the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
