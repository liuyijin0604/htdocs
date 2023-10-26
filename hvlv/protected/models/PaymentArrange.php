<?php

/**
 * This is the model class for table "payment_arrange".
 *
 * The followings are the available columns in table 'payment_arrange':
 * @property string $id
 * @property string $no
 * @property string $created
 * @property int $user_id
 * @property int $status
 * @property string $total
 * @property string $approved
 * @property int $currency
 * @property string $meta
 */
class PaymentArrange extends oActiveRecord
{
	public $searchInPay = false;
	public $mdata;

	public static $states = array(
		self::PAYMENT_ARRANGE_STATUS_NEW => 'New',
		self::PAYMENT_ARRANGE_STATUS_BOSS_APPROVED => 'Admin Approved',
		// self::PAYMENT_ARRANGE_STATUS_PARTIALLY_APPROVED => 'Partially Approved',
		self::PAYMENT_ARRANGE_STATUS_APPROVED => 'Approved',
		self::PAYMENT_ARRANGE_STATUS_REJECTED => 'Rejected',
		self::PAYMENT_ARRANGE_STATUS_REQURING_BOSS_APPROVE => 'Requiring Admin Approve',
		self::PAYMENT_ARRANGE_STATUS_ADMIN_REJECTED => 'Admin Rejected'
	);
	const PAYMENT_ARRANGE_STATUS_NEW = 1;
	const PAYMENT_ARRANGE_STATUS_PARTIALLY_APPROVED = 5;
	const PAYMENT_ARRANGE_STATUS_APPROVED = 6;
	const PAYMENT_ARRANGE_STATUS_REJECTED = 10;
	const PAYMENT_ARRANGE_STATUS_REQURING_BOSS_APPROVE = 99;
	const PAYMENT_ARRANGE_STATUS_BOSS_APPROVED = 2;
	const PAYMENT_ARRANGE_STATUS_BOSS_PARTIALLY_APPROVED = 3;
	const PAYMENT_ARRANGE_STATUS_ADMIN_REJECTED = 12;

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
		return 'payment_arrange';
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'no' => 'No',
			'created' => 'Created',
			'user_id' => 'Submit',
			'total' => 'Total',
			'approved' => 'Approved',
			'status' => 'Status',
			'currency' => 'Currency',
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
			array('created, user_id, status, currency', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('no, total', 'safe'),
			array('id, no, created, user_id, status, total, approved, currency', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'links' => array(self::HAS_MANY, 'PaymentArrangeBilling', 'payment_arrange_id'),
			'signature' => array(self::HAS_ONE, 'PaymentArrangeSignature', 'payment_arrange_id'),
		);
	}

	public function getCurrency()
	{
		return Yii::t(strtolower(__CLASS__), Invoice::$currencies[$this->currency]);
	}

	public function getStatus()
	{
		return Yii::t(strtolower(__CLASS__), self::$states[$this->status]);
	}

	/**
	 * generate billing number
	 * @return string
	 */
	public function genNo()
	{
		$n = 'PA' . date('ymd', strtotime($this->created));
		$s = self::model()->count('no LIKE :n', [':n' => $n . '%']) + 1;
		return $n . sprintf('%02d', $s);
	}

	public function getUser()
	{
		if (!empty($this->user)) {
			return $this->user->getName();
		} else {
			return '';
		}
	}

	public function getSummary()
	{
		$data = [];
		foreach ($this->links as $link) {
			$billing = $link->billing;
			if (empty($data[$billing->org_id])) $data[$billing->org_id] = ['id' => $billing->org_id, 'name' => $billing->cust->name, 'currency' => $billing->getCurrency(), 'total' => 0, 'gst' => 0, 'subtotal' => 0, 'dispute_paid' => 0, 'balance' => 0, 'amount' => 0, 'no' => [], 'date' => '1970-01-01'];
			$data[$billing->org_id]['total'] += $billing->total;
			$data[$billing->org_id]['gst'] += $billing->gst;
			$data[$billing->org_id]['subtotal'] += $billing->total - $billing->gst;
			$data[$billing->org_id]['bank'] = $billing->cust->getBankAccount(true, $billing->currency);
			$data[$billing->org_id]['dispute_paid'] += $billing->total - $billing->getBalance();
			$data[$billing->org_id]['balance'] += $billing->getBalance();
			$data[$billing->org_id]['bankaccount'] = $billing->cust->getBankAccount(false, $billing->currency);
			$data[$billing->org_id]['amount'] += $link->amount;
			$data[$billing->org_id]['no'][] = $billing->billing_cref;
			$data[$billing->org_id]['date'] = strtotime($data[$billing->org_id]['date']) > strtotime($billing->date) ? $data[$billing->org_id]['date'] : $billing->date;
		}

		foreach ($data as $org => $item) {
			$data[$org]['total'] = number_format($item['total'], 2, '.', '');
			$data[$org]['gst'] = number_format($item['gst'], 2, '.', '');
			$data[$org]['subtotal'] = number_format($item['subtotal'], 2, '.', '');
			$data[$org]['dispute_paid'] = number_format($item['dispute_paid'], 2, '.', '');
			$data[$org]['balance'] = number_format($item['balance'], 2, '.', '');
			$data[$org]['amount'] = number_format($item['amount'], 2, '.', '');
		}

		if (!empty($this->mdata['desc'])) {
			foreach ($data as $org => $item) {
				if (!empty($this->mdata['desc'][$org])) {
					$data[$org]['desc'] = $this->mdata['desc'][$org];
				}
			}
		}

		return $data;
	}

	public function updateTotal()
	{
		$this->refresh();
		$this->total = 0;
		foreach ($this->links as $link) {
			$this->total += $link->amount;
		}
		$this->update('total');
	}

	public function createAba()
	{
		include_once(Yii::app()->basePath.DIRECTORY_SEPARATOR.'vendor/autoload.php');

		$bank = BankAccount::model()->findByPk($this->mdata['bank_account']);

		$generator = new AbaFileGenerator\Generator\AbaFileGenerator(
			substr($bank->code, 0, 3) . '-' . substr($bank->code, 3, 3), // bsb
			substr($bank->code, 6), // account number
			'WBC',
			'PCAExpress',
			$this->user->fname,
			'000000',
			'Payment',
		);

		$transactions = [];
		$data = $this->getSummary();
		foreach ($data as $org_id => $line) {
			$org = OrgBankAccount::model()->find('org_id = :org_id AND currency = :currency', [':org_id' => $line['id'], ':currency' => $this->currency]);
			$transaction = new AbaFileGenerator\Model\Transaction();
			$transaction->setAccountName(substr($org->bank_name, 0, 32));
			$transaction->setAccountNumber($org->bank_number);
			$transaction->setBsb($org->bank_bsb);
			$transaction->setTransactionCode('50');
			if (!empty($this->mdata['desc'][$org_id])) {
				$transaction->setReference($this->mdata['desc'][$org_id]);
			} else {
				$transaction->setReference(substr(sizeof($line['no']) == 1 ? preg_replace('/\/|\-/', '', $line['no'][0]) : 'up to ' . date('Ymd', strtotime($line['date'])), 0, 18));
			}
			$transaction->setAmount($line['amount'] * 100);
			$transactions[] = $transaction;
		}
		$abaString = $generator->generate($transactions);
		$abaString = substr($abaString, 0, 1) . implode(' ', array_fill(0, 18, '')) . substr($abaString, 18);

		return $abaString;
	}

	public function beforeSave()
	{
		if (empty($this->no)) {
			$this->no = $this->genNo();
		}

		$this->meta = empty($this->mdata) ? '' : json_encode($this->mdata);

		return true;
	}

	public function afterSave()
	{
		if ($this->status == self::PAYMENT_ARRANGE_STATUS_REJECTED||$this->status == self::PAYMENT_ARRANGE_STATUS_ADMIN_REJECTED) {
			foreach ($this->links as $link) {
				$link->billing->checkPaid();
				$link->billing->update('status');
			}
		}
	}

	public function afterFind()
	{
		$this->mdata = empty($this->meta) ? [] : json_decode($this->meta, true);
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
	public function search($pgn = true, $ps = 30, $odr = 't.id DESC', $ec = null)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;
		$criteria->compare('t.no', $this->no, true);
		$criteria->compare('t.created', $this->created, true);
		$criteria->compare('t.status', $this->status);
		$criteria->compare('t.total', $this->total, true);
		$criteria->compare('t.approved', $this->approved, true);
		$criteria->compare('t.currency', $this->currency, true);

		if (!empty($this->user_id)) {
			$with[] = 'user';
			$criteria->addCondition('user.id = "' . $this->user_id . '" OR user.fname LIKE "' . $this->user_id . '" OR user.lname LIKE "' . $this->user->id . '"');
		}

		if($this->searchInPay)
		{
			$criteria->compare('t.status',[PaymentArrange::PAYMENT_ARRANGE_STATUS_NEW,PaymentArrange::PAYMENT_ARRANGE_STATUS_BOSS_APPROVED,PaymentArrange::PAYMENT_ARRANGE_STATUS_REJECTED,PaymentArrange::PAYMENT_ARRANGE_STATUS_ADMIN_REJECTED,PaymentArrange::PAYMENT_ARRANGE_STATUS_APPROVED]);

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