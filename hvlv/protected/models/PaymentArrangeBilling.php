<?php

/**
 * This is the model class for table "payment_arrange_billing".
 *
 * The followings are the available columns in table 'payment_arrange_billing':
 * @property string $id
 * @property string $payment_arrange_id
 * @property string $billing_id
 * @property string $amount
 */
class PaymentArrangeBilling extends oActiveRecord
{
	const PAYMENT_ARRANGE_STATUS_REQURING_BOSS_APPROVE = 99;
	public $searchBillingForBossApprove = false;
	public $currency = "";

	public function getStatus()
	{
		return Yii::t(strtolower(__CLASS__), PaymentArrange::$states[$this->status]);
	}

	public function getCurrency()
	{
		return Yii::t(strtolower(__CLASS__), Invoice::$currencies[$this->payment_arrange->currency]);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrgBankAccount the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'payment_arrange_billing';
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'payment_arrange_id' => 'Payment Arrange',
			'billing_id' => 'Billing',
			'amount' => 'amount',
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('payment_arrange_id, billing_id, amount', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, payment_arrange_id, billing_id, amount', 'safe', 'on'=>'search'),
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
			'payment_arrange' => array(self::BELONGS_TO, 'PaymentArrange', 'payment_arrange_id'),
			'billing' => array(self::BELONGS_TO, 'Billing', 'billing_id'),
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
	public function search($pgn = true, $ps = 30, $odr = 't.id ASC', $ec = null)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;
		$criteria->compare('t.payment_arrange_id', $this->payment_arrange_id, true);
		$with = [];


		if($this->searchBillingForBossApprove)
		{
			$with[] = "billing";
			$with[] = "payment_arrange";
			$criteria->compare('t.status',PaymentArrange::PAYMENT_ARRANGE_STATUS_REQURING_BOSS_APPROVE);
			$criteria->compare('payment_arrange.status',PaymentArrange::PAYMENT_ARRANGE_STATUS_REQURING_BOSS_APPROVE);
			$criteria->compare('billing.status',Billing::BILLING_STATUS_ARRANGED_PAYMENT);

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
				'defaultOrder' => $odr,
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

}