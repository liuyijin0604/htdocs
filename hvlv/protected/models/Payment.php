<?php

/**
 * This is the model class for table "invoice_payment".
 *
 * The followings are the available columns in table 'invoice_payment':
 * @property string $id
 * @property string $org_id
 * @property integer $bank_transaction_id
 * @property string $date
 * @property string $dpmt
 * @property string $no
 * @property string $transaction_date
 * @property integer $status
 * @property integer $bank
 * @property integer $currency
 * @property string $amount
 * @property integer $op_id  record the op for who manually create the credit Note
 * @property string $ata
 * @property string $refund
 * @property integer $type
 * @property integer $flag
 * @property string $ref
 * @property string $note
 */
class Payment extends oActiveRecord
{
	const PAYMENT_TYPE_EFT = 1;
	const PAYMENT_TYPE_CHEQUE = 2;
	const PAYMENT_TYPE_CASH = 3;
	const PAYMENT_TYPE_CREDIT_CARD = 4;
	const PAYMENT_TYPE_CREDIT_NOTE = 5;
	const PAYMENT_TYPE_POLI = 6;
	const PAYMENT_TYPE_PAYPAL = 7;
	const PAYMENT_TYPE_SUPAY = 8;
	const PAYMENT_TYPE_OFFSET = 9;
	const PAYMENT_TYPE_FASTWAY = 10;
	const PAYMENT_TYPE_COURIER_CN = 999;
	const RESEND_EMAIL = 4;
	public static $types = array(
		'1' => 'EFT',
		'2' => 'Cheque',
		'3' => 'Cash',
		'4' => 'Credit Card',
		'5' => 'Credit Note',
		'6' => 'Poli',
		'7' => 'PayPal',
		'8' => 'Supay',
		'9' => 'OffSet',
		'10' => 'Fastway',
	);

	const PAYMENT_STATUS_PENDING = 1;
	const PAYMENT_STATUS_POSTED = 6;
	const PAYMENT_STATUS_DELETED = 9;
	const PAYMENT_STATUS_PARTIALLY_USED = 7;
	const PAYMENT_STATUS_USED = 8;
	public static $states = array(
		'1' => 'Pending',
		'6' => 'Posted',
		'7' =>	'Partially Used',
		'8' =>	'Used',
		'9' => 'Deleted',
	);

	public static $postedStatus = [6,7,8];

	const PAYMENT_MODE_PREPAYMENT = 1;
	const PAYMENT_MODE_CREDITNOTE = 2;
	const PAYMENT_MODE_CN_FW = 3;
	const PAYMENT_MODE_OFFSET = 4;
	const PAYMENT_MODE_PREPAYMENT_BEFORE_7D = 5;
	const PAYMENT_MODE_PREPAYMENT_WITHIN_7D = 6;
	const PAYMENT_MODE_CREDITNOTE_BEFORE_7D = 7;
	const PAYMENT_MODE_CN_FW_BEFORE_7D = 8;
	const PAYMENT_MODE_OFFSET_BEFORE_7D = 9;
	const PAYMENT_MODE_ALL = 99;
	public static $modes = array(
		'1' => 'Prepayment',
		'2' => 'Credit Note',
		'3' => 'Credit Note & Fastway',
		'4' => 'Offset',
		'5' => 'Prepayment Before 7D',
		'6' => 'Prepayment Within 7D',
		'7' => 'Credit Note Before 7D',
		'8' => 'Creidt Note & Fastway Before 7D',
		'9' => 'Offset Before 7D',
		'99' => 'All',
	);

	public static $banks = array(
		'10' => 'Westpac AUD',
		'11' => 'Westpac USD',
		'20' => 'RMB',
		self::PAYMENT_BANK_SYSTEM_CREDIT_NOTE => 'System Credit Note',
		'91' => 'Manual Credit Note',
	);
	const PAYMENT_BANK_SYSTEM_CREDIT_NOTE = 90;

	public static $thedpmts = [
		10 => 'Import',
		20 => 'Export',
		30 => 'Air/Sea',
		40 => '3PL',
	];

	public static $flags = [
		1 => 'QUE', //waiting to send to client;
		2 => 'SEND', //already sent to the
		4 => 'RESEND'
	];

	public $client, $invnos, $opids, $op_name, $mdata, $dpmts, $gst, $diff, $rate, $line_gst, $suborg_name,$fromInvoice;
	public $nolog = false;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return InvoicePayment the static model class
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
		return 'payment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, date, amount, type', 'required'),
			array('ata, status, bank, currency, ref, note,transaction_date, refund, mdata', 'safe'),
			array('type,bank_transaction_id', 'numerical', 'integerOnly' => true),
			array('amount', 'length', 'max' => 12),
			array('ref', 'length', 'max' => 100),
			array('xero_id,no', 'length', 'max' => 45),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, org_id, client, date,transaction_date,no,dpmt,flag,status,meta,opids,dpmts,op_name, bank_transaction_id,bank,op_id, currency, amount, ata, type, ref, note,xero_id ,invnos, gst, diff, refund, suborg_name,fromInvoice', 'safe', 'on' => 'search'),
		);
	}

		// for TLA
	public function getDbConnection(){
		return self::getTlaConnection();
	}
	
	public function updateAta()
	{
		$this->isNewRecord = false;
		$rs = PayInv::model()->findAll('pay_id = :id', [':id' => $this->id]);
		$tt = 0;
		$tat = 0;
		foreach ($rs as $r) {
			$tt += $r->amount / (empty($r->exrate) ? 1 : $r->exrate);
			if($r->sync_xero==1)
			{
				$tat += $r->amount / (empty($r->exrate) ? 1 : $r->exrate);
			}
		}
		$this->ata = $this->amount - $tt - (!empty($this->mdata['diff']) ? $this->mdata['diff'] : 0) - floatval($this->refund);
		
		if (($this->type==5&&$this->bank==91)) {
			if($this->ata>0&&$tat>0)
			{
				$this->status=self::PAYMENT_STATUS_PARTIALLY_USED;
				$this->update(['ata','status']);
			}elseif($this->ata<=0&&$tat>0)
			{
				$this->status=self::PAYMENT_STATUS_USED;
				$this->update(['ata','status']);
			}else
			{
				$this->update(['ata']);
			}
  		}else
  		{
		
			$this->update(['ata']);
		}
	}

	protected function beforeSave()
	{
		if (empty($this->transaction_date)) {
			$this->transaction_date = date('Y-m-d');
		}

		if (!empty($this->mdata)) {
			$this->meta = json_encode($this->mdata);
		}

		if (Yii::app()->name != 'PEP' && (strtotime($this->date) < strtotime(date('Y-m-01', strtotime('-1 month'))) || (date('j') > 5 && strtotime($this->date) < strtotime(date('Y-m-01')))) && $this->isNewRecord) {
			$this->addError('date', 'No back date recepit after 5th');
			return false;
		}

		if (!$this->currency) {
			$this->currency = 1;
		}

		return true;
	}

	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata = json_decode($this->meta, true);
		}

		$this->genNo();

		$this->line_gst = 0;
		foreach ($this->credit_lines as $line) {
			$this->line_gst += $line->gst;
		}

		if ($this->id > 500000) {
			[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
			$this->credit_lines = CreditLine::model()->findAll('pid = :pid', [':pid' => $this->id]);
			Yii::app()->name = $app_name;
		}
	}

	public function genNo()
	{
		if (empty($this->no)) {
			if (Yii::app()->name != 'TLA') {
				if ($this->type == 5) {
					$this->no = 'CRN' . sprintf("%05s", substr($this->id, -5));
				} else {
					$this->no = strtoupper(substr($this->getType(), 0, 2)) . sprintf("%05s", substr($this->id, -5));
				}
			} else {
				if ($this->type == 5) {
					$this->no = 'CRN' . sprintf("%06s", substr($this->id, -6));
				} else {
					$this->no = strtoupper(substr($this->getType(), 0, 2)) . sprintf("%06s", substr($this->id, -6));
				}
			}
			//$this->save();
			$this->saveAttributes(['no']);
		}
	}

	public static function typeIdx($n)
	{
		foreach (self::$types as $i => $t) {
			if ($t == $n) {
				return $i;
			}

		}
		return 0;
	}

	public function getStatus()
	{
		return Yii::t(strtolower(__CLASS__), self::$states[$this->status]);
	}

	public function getType()
	{
		return Yii::t(strtolower(__CLASS__), self::$types[$this->type]);
	}

	public function getBank()
	{
		return Yii::t(strtolower(__CLASS__), self::$banks[$this->bank]);
	}

	public function getCurrency()
	{
		return Yii::t(strtolower(__CLASS__), Invoice::$currencies[$this->currency]);
	}

	public function getDpmt()
	{
		if (isset(Invoice::$dpmts[$this->dpmt])) {
			return Yii::t(strtolower(__CLASS__), Invoice::$dpmts[$this->dpmt]);
		} else {
			return 0;
		}
	}

	/**
	 * @param $fromDate
	 * @param $endDate
	 * @return mixed
	 */
	public static function getOccurringAmountByDatespan($fromDate, $endDate)
	{
		$sql = 'SELECT SUM(amount) AS t FROM payment WHERE status != ' . Payment::PAYMENT_STATUS_DELETED;
		$sql .= " AND transaction_date > '" . $fromDate . "' AND transaction_date <= '" . $endDate . "'";
		$c = Yii::app()->db_tla->createCommand($sql);
		return $c->queryScalar();

	}

	/**
	 * @param $fromDate
	 * @param $endDate
	 * @return mixed
	 */
	public static function getOccurringAmountByDatespanByOrg($fromDate, $endDate, $orgId)
	{
		$sql = 'SELECT SUM(amount) AS t FROM payment WHERE status != ' . Payment::PAYMENT_STATUS_DELETED;
		$sql .= " AND transaction_date > '" . $fromDate . "' AND transaction_date <= '" . $endDate . "'";
		$sql .= " AND org_id = " . $orgId;
		$c = Yii::app()->db_tla->createCommand($sql);
		return $c->queryScalar();

	}

	/**
	 * get occurring payment amount based on month last date
	 * @param $monthLastDate
	 * @return int
	 */
	public static function getOccurringAmountByMonth($monthLastDate)
	{

		// based on month last date get first date
		$monthFirstDate = date('Y-m-01', strtotime($monthLastDate));
		$sql = 'SELECT SUM(amount) AS t FROM payment WHERE status != ' . Payment::PAYMENT_STATUS_DELETED;
		$sql .= " AND transaction_date >= '" . $monthFirstDate . "' AND transaction_date <= '" . $monthLastDate . "'";
		$c = Yii::app()->db_tla->createCommand($sql);
		return $c->queryScalar();

	}

	/**
	 * get total occurred  paid amount end of specified month
	 * @param $monthLastDate
	 * @return mixed
	 */
	public static function getPaidAmountByMonth($monthLastDate)
	{

		// based on month last date get first date
		$sql = 'SELECT SUM(pi.amount) AS t FROM pay_inv as pi LEFT JOIN payment as p ON p.id = pi.pay_id LEFT JOIN invoice as i ON i.id = pi.inv_id WHERE ';
		$sql .= ' p.status != ' . Payment::PAYMENT_STATUS_DELETED . ' AND i.status != ' . Invoice::INVOICE_STATUS_CACELLED;
		$sql .= " AND (pi.transaction_date <= '" . $monthLastDate . "'";

		// currently in order to support old date, we need to take care NULL transaction date
		$sql .= " OR pi.transaction_date is NULL)";

		$c = Yii::app()->db_tla->createCommand($sql);

		$linkedAmount = $c->queryScalar();

		// calculate all payment amount which is not linked with any invoice
		$sql = 'SELECT SUM(p.amount) AS t FROM payment as p WHERE p.id not in ( SELECT pay_id FROM pay_inv WHERE pay_id > 0)  AND';
		$sql .= ' p.status != ' . Payment::PAYMENT_STATUS_DELETED;
		$sql .= " AND (p.transaction_date <= '" . $monthLastDate . "'";
		$sql .= " OR p.transaction_date is NULL)";
		$c = Yii::app()->db_tla->createCommand($sql);

		$allAmount = $linkedAmount + $c->queryScalar();

		return $allAmount;

	}

	/**
	 * get all payment amount which are not linked with any invoice
	 * @param $orgid
	 * @param $monthLastDate
	 * @return mixed
	 */
	public static function orgTotalUnallocPaymentAmount($orgid, $monthLastDate)
	{
		// calculate all payment amount which is not linked with any invoice
		$sql = 'SELECT SUM(p.amount) AS t FROM payment as p WHERE p.id not in ( SELECT pay_id FROM pay_inv WHERE pay_id > 0)  AND';
		$sql .= ' p.org_id = ' . $orgid . ' AND';
		$sql .= ' p.status != ' . Payment::PAYMENT_STATUS_DELETED;
		$sql .= " AND (p.transaction_date <= '" . $monthLastDate . "'";
		$sql .= " OR p.transaction_date is NULL)";
		$c = Yii::app()->db_tla->createCommand($sql);
		return $c->queryScalar();
	}

	/**
	 * @param $monthLastDate
	 * @return array
	 */
	public static function getTotalUnallocPaymentAmountList($monthLastDate)
	{
		// calculate all payment amount which is not linked with any invoice
		$sql = 'SELECT p.org_id,SUM(p.amount) AS t FROM payment as p WHERE p.id not in ( SELECT pay_id FROM pay_inv WHERE pay_id > 0)  AND';
		$sql .= ' p.status != ' . Payment::PAYMENT_STATUS_DELETED;
		$sql .= " AND (p.transaction_date <= '" . $monthLastDate . "'";
		$sql .= ' OR p.transaction_date is NULL)';
		$sql .= ' GROUP by p.org_id';
		$c = Yii::app()->db_tla->createCommand($sql);
		$rt = $c->queryAll();

		$results = array();
		foreach ($rt as $r) {
			$results[$r['org_id']] = $r['t'];
		}
		return $results;
	}

	public static function getTotalAtaPaymentAmountList($monthLastDate)
	{
		// calculate all payment amount which is not linked with any invoice
		$sql = 'SELECT p.org_id,SUM(p.ata) AS t FROM payment as p WHERE ';
		$sql .= ' p.status != ' . Payment::PAYMENT_STATUS_DELETED . ' AND ata > 0 ';
		$sql .= " AND (p.transaction_date <= '" . $monthLastDate . "'";
		$sql .= ' OR p.transaction_date is NULL)';
		$sql .= ' GROUP by p.org_id';
		$c = Yii::app()->db_tla->createCommand($sql);
		$rt = $c->queryAll();

		$results = array();
		foreach ($rt as $r) {
			$results[$r['org_id']] = $r['t'];
		}
		return $results;
	}

	public static function orgTotalAta($oid)
	{
		$sql = 'SELECT SUM(ata) AS t FROM payment WHERE status in (6,7) AND org_id = ' . $oid;
		$c = Yii::app()->db_tla->createCommand($sql);
		return $c->queryScalar();
	}

	public static function orgTotalCreditAta($oid)
	{
		$sql = 'SELECT SUM(ata) AS t FROM payment WHERE type = 5 and status in (6,7) AND org_id = ' . $oid;
		$c = Yii::app()->db_tla->createCommand($sql);
		return $c->queryScalar();
	}

	public static function orgAtaDetails($oid)
	{
		$ps = Payment::model()->findAll('ata > 0 AND status = 6 AND org_id = :oid ', [':oid' => $oid]);
		return $ps;
	}

	public static function orgAtaBefore($oid, $date, $mode, $currency = 1)
	{
		$amt = 0;

		// allocated
		$sql = 'select sum(pi.amount / pi.exrate) as t from pay_inv pi join invoice i on pi.inv_id = i.id join payment p on pi.pay_id = p.id where i.to_id = ' . $oid . ' and p.org_id = ' . $oid . ' and p.date <= "' . $date . '" and p.status = 6 and (pi.transaction_date > "' . $date . '" or i.date > "' . $date . '") and p.currency = ' . $currency;
		// $sql = 'select sum(pi.amount) as t from pay_inv pi join invoice i on pi.inv_id = i.id join payment p on pi.pay_id = p.id where i.to_id = ' . $oid . ' and i.date > "' . $date . '" and p.org_id = ' . $oid . ' and p.date <= "' . $date . '" and p.status = 6 and p.currency = ' . $currency;
		if ($mode == 1) {
			$sql .= ' and p.type != 5 and p.type != 9 and p.ref not REGEXP "FWY[[:digit:]]{5}"';
		}
		if ($mode == 2) {
			$sql .= ' and p.type = 5 and p.bank = 91';
		}
		if ($mode == 3) {
			$sql .= ' and ((p.type = 5 and p.bank = 91) or p.ref REGEXP "FWY[[:digit:]]{5}")';
		}
		if ($mode == 4) {
			$sql .= ' and p.type = 9';
		}
		if ($mode == 5) {
			$sql .= ' and p.type != 5 and p.type != 9 and p.ref not REGEXP "FWY[[:digit:]]{5}" and (pi.transaction_date > "' . date('Y-m-d', strtotime($date . ' + 7 day')) . '" or i.date > "' . $date . '")';
		}
		if ($mode == 6) {
			$sql .= ' and false';
		}
		if ($mode == 7) {
			$sql .= ' and p.type = 5 and p.bank = 91 and (pi.transaction_date > "' . date('Y-m-d', strtotime($date, ' + 7 day')) . '" or i.date > "' . $date . '")';
		}
		if ($mode == 8) {
			$sql .= ' and ((p.type = 5 and p.bank = 91) or p.ref REGEXP "FWY[[:digit:]]{5}") and (pi.transaction_date > "' . date('Y-m-d', strtotime($date . ' + 7 day')) . '" or i.date > "' . $date . '")';
		}
		if ($mode == 9) {
			$sql .= ' and p.type = 9 and (pi.transaction_date > "' . date('Y-m-d', strtotime($date . ' + 7 day')) . '" or i.date > "' . $date . '")';
		}
		$amt += Yii::app()->db_tla->createCommand($sql)->queryScalar();

		// not allocated
		$sql = 'select sum(ata) as t from payment where org_id = ' . $oid . ' and date <= "' . $date . '" and status = 6 and ata > 0 and currency = ' . $currency;
		if ($mode == 1) {
			$sql .= ' and type != 5 and type != 9 and ref not REGEXP "FWY[[:digit:]]{5}"';
		}
		if ($mode == 2 || $mode == 7) {
			$sql .= ' and type = 5 and bank = 91';
		}
		if ($mode == 3 || $mode == 8) {
			$sql .= ' and ((type = 5 and bank = 91) or ref REGEXP "FWY[[:digit:]]{5}")';
		}
		if ($mode == 4 || $mode == 9) {
			$sql .= ' and type = 9';
		}
		if ($mode == 5) {
			$sql .= ' and type != 5 and type != 9 and ref not REGEXP "FWY[[:digit:]]{5}" and date <= "' . date('Y-m-d', strtotime($date . ' - 7 day')) . '"';
		}
		if ($mode == 6) {
			$sql .= ' and type != 5 and type != 9 and ref not REGEXP "FWY[[:digit:]]{5}" and date > "' . date('Y-m-d', strtotime($date . ' - 7 day')) . '"';
		}
		$amt += Yii::app()->db_tla->createCommand($sql)->queryScalar();

		// refund
		$sql = '';
		if ($mode == 1) {
			$sql .= ' and type != 5 and type != 9 and ref not REGEXP "FWY[[:digit:]]{5}"';
		}
		if ($mode == 2 || $mode == 7) {
			$sql .= ' and type = 5 and bank = 91';
		}
		if ($mode == 3 || $mode == 8) {
			$sql .= ' and ((type = 5 and bank = 91) or ref REGEXP "FWY[[:digit:]]{5}")';
		}
		if ($mode == 4 || $mode == 9) {
			$sql .= ' and type = 9';
		}
		if ($mode == 5) {
			$sql .= ' and type != 5 and type != 9 and ref not REGEXP "FWY[[:digit:]]{5}" and date <= "' . date('Y-m-d', strtotime($date . ' - 7 day')) . '"';
		}
		if ($mode == 6) {
			$sql .= ' and type != 5 and type != 9 and ref not REGEXP "FWY[[:digit:]]{5}" and date > "' . date('Y-m-d', strtotime($date . ' - 7 day')) . '"';
		}
		$payments = Payment::model()->findAll('org_id = :oid and refund > 0 and currency = :currency and date <= :date' . $sql, array(':oid' => $oid, ':currency' => $currency, ':date' => $date));
		foreach ($payments as $payment) {
			$bank = BankStatement::model()->findByPk($payment->mdata['refund_bank_transaction_id']);
			if ($bank->reconciled_date > $date) {
				$amt += $payment->refund;
			}
		}

		return $amt;
	}

	public function orgAtaDetailsBefore($oid, $date, $mode, $currency = 1)
	{
		if (empty($mode)) {
			return [];
		}

		// allocated
		$sql = 'select p.*, (pi.amount / pi.exrate) as total from pay_inv pi join invoice i on pi.inv_id = i.id join payment p on pi.pay_id = p.id where i.to_id = ' . $oid . ' and p.org_id = ' . $oid . ' and p.date <= "' . $date . '" and p.status in (6,7) and (pi.transaction_date > "' . $date . '" or i.date > "' . $date . '") and p.currency = ' . $currency;
		if ($mode == 1) {
			$sql .= ' and p.type != 5 and p.type != 9 and p.ref not REGEXP "FWY[[:digit:]]{5}"';
		}
		if ($mode == 2) {
			$sql .= ' and p.type = 5 and p.bank = 91';
		}
		if ($mode == 3) {
			$sql .= ' and ((p.type = 5 and p.bank = 91) or p.ref REGEXP "FWY[[:digit:]]{5}")';
		}
		if ($mode == 4) {
			$sql .= ' and p.type = 9';
		}
		if ($mode == 5) {
			$sql .= ' and p.type != 5 and p.type != 9 and p.ref not REGEXP "FWY[[:digit:]]{5}" and (pi.transaction_date > "' . date('Y-m-d', strtotime($date . ' + 7 day')) . '" or i.date > "' . $date . '")';
		}
		if ($mode == 6) {
			$sql .= ' and false';
		}
		if ($mode == 7) {
			$sql .= ' and p.type = 5 and p.bank = 91 and (pi.transaction_date > "' . date('Y-m-d', strtotime($date . ' + 7 day')) . '" or i.date > "' . $date . '")';
		}
		if ($mode == 8) {
			$sql .= ' and ((p.type = 5 and p.bank = 91) or p.ref REGEXP "FWY[[:digit:]]{5}") and (pi.transaction_date > "' . date('Y-m-d', strtotime($date . ' + 7 day')) . '" or i.date > "' . $date . '")';
		}
		if ($mode == 9) {
			$sql .= ' and p.type = 9 and (pi.transaction_date > "' . date('Y-m-d', strtotime($date . ' + 7 day')) . '" or i.date > "' . $date . '")';
		}
		$ps = Yii::app()->db_tla->createCommand($sql)->queryAll();

		// not allocated
		$sql = 'select *, ata as total from payment where org_id = ' . $oid . ' and date <= "' . $date . '" and status in (6,7) and ata > 0 and currency = ' . $currency;
		if ($mode == 1) {
			$sql .= ' and type != 5 and type != 9 and ref not REGEXP "FWY[[:digit:]]{5}"';
		}
		if ($mode == 2 || $mode == 7) {
			$sql .= ' and type = 5 and bank = 91';
		}
		if ($mode == 3 || $mode == 8) {
			$sql .= ' and ((type = 5 and bank = 91) or ref REGEXP "FWY[[:digit:]]{5}")';
		}
		if ($mode == 4 || $mode == 9) {
			$sql .= ' and type = 9';
		}
		if ($mode == 5) {
			$sql .= ' and type != 5 and type != 9 and ref not REGEXP "FWY[[:digit:]]{5}" and date <= "' . date('Y-m-d', strtotime($date . ' - 7 day')) . '"';
		}
		if ($mode == 6) {
			$sql .= ' and type != 5 and type != 9 and ref not REGEXP "FWY[[:digit:]]{5}" and date > "' . date('Y-m-d', strtotime($date . ' - 7 day')) . '"';
		}
		$ps = array_merge($ps, Yii::app()->db_tla->createCommand($sql)->queryAll());

		// refund
		$sql = '';
		if ($mode == 1) {
			$sql .= ' and type != 5 and type != 9 and ref not REGEXP "FWY[[:digit:]]{5}"';
		}
		if ($mode == 2 || $mode == 7) {
			$sql .= ' and type = 5 and bank = 91';
		}
		if ($mode == 3 || $mode == 8) {
			$sql .= ' and ((type = 5 and bank = 91) or ref REGEXP "FWY[[:digit:]]{5}")';
		}
		if ($mode == 4 || $mode == 9) {
			$sql .= ' and type = 9';
		}
		if ($mode == 5) {
			$sql .= ' and type != 5 and type != 9 and ref not REGEXP "FWY[[:digit:]]{5}" and date <= "' . date('Y-m-d', strtotime($date . ' - 7 day')) . '"';
		}
		if ($mode == 6) {
			$sql .= ' and type != 5 and type != 9 and ref not REGEXP "FWY[[:digit:]]{5}" and date > "' . date('Y-m-d', strtotime($date . ' - 7 day')) . '"';
		}
		$payments = Payment::model()->findAll('org_id = :oid and refund > 0 and currency = :currency and date <= :date' . $sql, array(':oid' => $oid, ':currency' => $currency, ':date' => $date));
		foreach ($payments as $payment) {
			$bank = BankStatement::model()->findByPk($payment->mdata['refund_bank_transaction_id']);
			if ($bank->reconciled_date > $date) {
				$ps[] = array('no' => $payment->no, 'ref' => $payment->ref, 'note' => $payment->note, 'date' => $payment->date, 'total' => $payment->refund);
			}
		}

		$rs = [];
		foreach ($ps as $p) {
			$rs[] = $p;
		}

		return $rs;
	}

	/**
	 * get Xero xml data from single payment object
	 * we only sync to xero for credit notes now
	 * @param $data
	 * @return mixed
	 */
	public static function getXeroXmlData(&$data)
	{

		$creditNotes = new XeCreditNote();
		$creditNotes->creditNoteNumber = 'PCN-' . $data->id;
		$creditNotes->type = XeCreditNote::TYPE_RECEIVABLE;
		$creditNotes->status = XeCreditNote::ST_AUTHORISED;
		$creditNotes->lineAmountTypes = 'Exclusive';
		$creditNotes->date = $data->date;

		$contact = new XeContact();
		$contact->contactNumber = 'PORG-' . $data->org_id;
		$creditNotes->contact = $contact;
		$desc = $data->ref;
		if (empty($desc)) {
			$desc = 'hvlv credit notes';
		}

		$data = array(
			'description' => $desc,
			'quantity' => 1,
			'unitAmount' => $data->ata,
			'accountCode' => AppHelper::getXeroSetting('creditnote_glcode'),
		);
		$creditNotes->addLineItem($data);

		return $creditNotes->getXmlData();
	}

	/**
	 * for returned shipment we need to do following things
	 * 1. get returned amount from invoice
	 * 2. create a payment with credit note
	 * 3. accounting will do the following things manually
	 * @param $shipment
	 */
	public static function createReturnedCreditNote(&$shipment)
	{
		// get manifest id
		// 1. find manifest from mani_map by fid = shipment id and model = ExParcel
		$manis = ManiMap::model()->with('manifest')->findAll('manifest.type = 40 AND fid = :id AND model = :exm', array(':id' => $shipment->id, ':exm' => 'ExParcel'));

		// try to create a credit note for related invoice
		foreach ($manis as $mani) {
			if (Payment::checkReturnedItemInManifest($mani->mani_id, $shipment)) {
				break;
			}
		}

	}

	public static function checkReturnedItemInManifest($manifestId, &$shipment)
	{

		$bFound = false;

		$amount = 0;
		// get related invoice
		// 1. try to find this from invline table by manifest id
		// status 10 : means canceled
		$invoiceLine = InvLine::model()->with('invoice')->find('fid = :id AND invoice.status != 10', array(':id' => $manifestId));
		if (!empty($invoiceLine)) {
			// try to find the returned item
			foreach ($invoiceLine->mdata['items'] as $si => $r) {
				if ($r[0] == $shipment->hbn) {
					if (isset($r[7])) {
						$amount = $r[7];
						$bFound = true;
					}
					break;
				}
			}

			// get invoice
			$invoice = $invoiceLine->invoice;
			// create a payment
			if (isset($invoice) && $amount > 0) {
				$payment = new Payment();
				$payment->org_id = $invoice->to_id;
				$payment->amount = $amount;
				$payment->ata = $amount;
				$payment->ref = 'for returned item : ' . $shipment->hbn;
				$payment->date = date('Y-m-d');
				$payment->transaction_date = $payment->date;
				$payment->type = Payment::PAYMENT_TYPE_CREDIT_NOTE; // default credit note
				$payment->bank = self::PAYMENT_BANK_SYSTEM_CREDIT_NOTE; // credit note
				$payment->currency = 1; // AUD
				$payment->status = Payment::PAYMENT_STATUS_POSTED; // post status
				$payment->save();

				// just create credit note only
				// let Michael AC do the next thing manually

				/*
			// in case there are existing invoices , we just
			// link this payment to not paid invoice directly
			$criteria = new CDbCriteria();
			$criteria->condition = 'to_id=:aid';
			$criteria->params = array(':aid'=>$invoice->to_id);
			$criteria->addInCondition('status', array(2,3,7)); // search for : overdue, posted, partially paid
			$waitingInvoice = Invoice::model()->findAll($criteria);

			// try to pay for each invoice until no more money
			$leftMoney = $amount;
			foreach ( $waitingInvoice as $inv ) {
			if ( $leftMoney <= 0 ) break;
			$balance = $inv->getTotal() - $inv->paid();
			if ( $balance <= 0 ) continue;

			$pi = new PayInv();
			$pi->inv_id = $inv->id;
			$pi->pay_id = $payment->id;
			if ( $leftMoney >= $balance ) {
			$pi->amount = $balance;
			$leftMoney -= $balance;
			} else {
			$pi->amount = $leftMoney;
			$leftMoney = 0;
			}
			$pi->save();
			$inv->checkPaid();
			$inv->save();
			}

			// in case paid money still left
			if ( count($waitingInvoice) > 0 ) {
			$payment->ata = $leftMoney;
			$payment->status = 6;
			$payment->save();
			}
			 */
			}
		}
		return $bFound;
	}

	public function prepXdata()
	{
		$xdata = new stdClass;
		$xdata->no = explode('-', $this->no)[0];
		$xdata->currency = $this->currency;
		$xdata->cust = $this->cust;
		$xdata->date = $this->date;
		$xdata->due = $this->date;
		$xdata->gst = 1;
		$xdata->dpt_id = 106;

		// xero account
		if ($this->dpmt == 10) {
			$code = 83000;
		} else if ($this->dpmt == 20) {
			$code = 81000;
		} else if ($this->dpmt == 30) {
			$code = 82000;
		} else if ($this->dpmt == 40) {
			if ($this->type == 60) {
				$code = 85000;
			} else {
				$code = 85050;
			}
		}else if ($this->dpmt == 50) {
			$code = 84000;
		}

		foreach ($this->credit_lines as $line) {
			// check every credit line for gst
			if($line->gst>0)
			{
				if ($this->dpmt == 10) {
					$code = 83001;
				}else if ($this->dpmt == 40) {
					if ($this->type == 60) {
						$code = 85001;
					} else {
						$code = 85051;
					}
				}else if ($this->dpmt == 50) {
					$code = 84001;
				}
			}

			if (empty($costs[$line->tax . $code . $this->dpmt])) {
				$costs[$line->tax . $code . $this->dpmt] = array(
					'qty' => 1,
					'det' => $this->ref,
					'ccode' => $code,
					'taxType' => $line->tax,
					'dept' => BillingLine::$xero_segments[$this->dpmt / 10],
					'amount' => 0,
					'region' =>$this->getMyRegion()
				);
			}

			$costs[$line->tax . $code . $this->dpmt]['amount'] += $line->amount;
		}

		return [$xdata, $costs];
	}

	// public function saveCreditNote2Xero()
	// {
	// 	if (yii::app()->name == 'TLA') return;

	// 	[$xdata, $costs] = $this->prepXdata();

	// 	if (!empty($costs)) {

	// 		$xdata->lines = array_values($costs);

	// 		$creditNote = new XeCreditNote();

	// 		$creditNote->type = 'ACCRECCREDIT';
	// 		$creditNote->status = 'AUTHORISED'; // approved , waiting for pay
	// 		$creditNote->creditNoteNumber = explode('-', $xdata->no)[0];

	// 		$curIndex = $xdata->currency;
	// 		$curCode = 'AUD';
	// 		if (isset(Invoice::$currencies[$curIndex])) {
	// 			$curCode = Invoice::$currencies[$curIndex];
	// 		}
	// 		$creditNote->currencyCode = $curCode;

	// 		$contact = new XeContact();
	// 		$contact->name = $xdata->cust->name;
	// 		$contact->accountNumber = 'PORG-' . $xdata->cust->id;
	// 		$creditNote->contact = $contact;
	// 		$creditNote->date = $xdata->date;

	// 		// Exclusive - exclude GST
	// 		// Inclusive - include GST
	// 		// NoTax
	// 		if ($xdata->gst > 0) {
	// 			$creditNote->lineAmountTypes = 'Inclusive';
	// 		} else {
	// 			$creditNote->lineAmountTypes = 'NoTax';
	// 		}

	// 		// get all items
	// 		foreach ($xdata->lines as $line) {
	// 			$itemData = array();
	// 			$itemData['quantity'] = $line['qty'];
	// 			$itemData['accountCode'] = $line['ccode'];
	// 			$itemData['description'] = '【' . $line['ccode'] . '】 ' . $line['det'];
	// 			$itemData['unitAmount'] = $line['amount'];
	// 			$itemData['taxType'] = $line['taxType'];

	// 			$tracking = Invoice::getTrackingInfo($xdata, isset($line['dept']) ? $line['dept'] : '');
	// 			$itemData['trackingName'] = $tracking['name'];
	// 			$itemData['trackingValue'] = $tracking['value'];

	// 			$creditNote->addLineItem($itemData);
	// 		}

	// 		try {
	// 			$rt = $creditNote->save();
	// 			if ($rt && $creditNote->statusAttributeString == 'OK') {
	// 				// set sync to Xero successful flag
	// 				$model = Payment::model()->find('no = :no', [':no' => $xdata->no]);
	// 				if (!empty($model)) {
	// 					$model->xero_id = $creditNote->creditNoteID;
	// 					$model->update('xero_id');
	// 				}
	// 			} else if (!empty($creditNote->creditNoteID)) {
	// 				$model = Payment::model()->find('no = :no', [':no' => $xdata->no]);
	// 				if (!empty($model)) {
	// 					$model->xero_id = $creditNote->creditNoteID;
	// 					$model->update('xero_id');
	// 				}
	// 			} else {
	// 				// log error message
	// 				Yii::app()->xero->log('failed to save invoice to xero for : ' . $xdata->no, Xero::LOG_LEVEL_ERR);
	// 			}
	// 		} catch (Exception $mye) {
	// 			$msg = 'failed to save invoice - ' . $mye->getMessage();
	// 			$msg .= PHP_EOL;
	// 			$msg .= 'Invoice Data : ' . json_encode($creditNote, JSON_PRETTY_PRINT);
	// 			Yii::app()->xero->log($msg, Xero::LOG_LEVEL_ERR);
	// 		}
	// 	}
	// }

	public function saveCreditNote2Xero()
	{
		$amount = 0;
		
		foreach ($this->credit_lines as $line)
		{
			$amount += $line->amount;
		}

		if($amount>0&&$amount!=$this->amount)
		{
			$this->amount = $amount;
			$this->update(['amount']);
		}

		[$xdata, $costs] = $this->prepXdata();

		if (yii::app()->name != 'TLA') {
			$xero = new XeroAPI('xero_token_pcaex');
		} else {
			$xero = new XeroAPI('xero_token_toplog');
		}

		if (!empty($costs)) {

			$xdata->lines = array_values($costs);

			$creditNote = $xero->new('Accounting\CreditNote');

			$creditNote->setType('ACCRECCREDIT');
			$creditNote->setStatus('AUTHORISED'); // approved , waiting for pay
			$creditNote->setCreditNoteNumber($xdata->no);

			$curIndex = $xdata->currency;
			$curCode = 'AUD';
			if (isset(Invoice::$currencies[$curIndex])) {
				$curCode = Invoice::$currencies[$curIndex];
			}
			$creditNote->setCurrencyCode($curCode);

			$contact = $xero->new('Accounting\Contact');
			if (!empty($xdata->cust->extra[$xero->key])) {
				$contact->setGUID($xdata->cust->extra[$xero->key]);
			} else {
				$contact->setName($xdata->cust->name);
				$contact->setAccountNumber('PORG-' . $xdata->cust->id);
			}
			$creditNote->setContact($contact);
			$creditNote->setDate(new DateTime($xdata->date));

			// Exclusive - exclude GST
			// Inclusive - include GST
			// NoTax
			if ($xdata->gst > 0) {
				$creditNote->setLineAmountType('Inclusive');
			} else {
				$creditNote->setLineAmountType('NoTax');
			}

			// get all items
			foreach ($xdata->lines as $line) {
				$itemData = $xero->new('Accounting\LineItem');
				$itemData->setQuantity($line['qty']);
				$itemData->setAccountCode($line['ccode']);
				$itemData->setDescription('【' . $line['ccode'] . '】 ' . $line['det']);
				$itemData->setUnitAmount($line['amount']);
				$itemData->setTaxType($line['taxType']);

				$t = Invoice::getTrackingInfo($line, isset($line['dept']) ? $line['dept'] : '');
				foreach ($t['name'] as $k => $name) {
					$tracking = $xero->new('Accounting\TrackingCategory');
					$tracking->setName($t['name'][$k]);
					$tracking->setOption($t['value'][$k]);
					$itemData->addTracking($tracking);
				}
				$creditNote->addLineItem($itemData);
			}

			try {
				$guid = Payment::model()->find('no = :no AND xero_id != ""', [':no' => $xdata->no]);
				if (!empty($guid)) {
					$creditNote->setGUID($guid->xero_id);
				}

				$rt = $creditNote->save();
				Yii::app()->xero->log(json_encode($rt->getElements()), Xero::LOG_LEVEL_TRACE);
				if ($creditNote->hasGUID()) {
					// set sync to Xero successful flag
					$this->xero_id = $creditNote->getGUID();
					$this->update('xero_id');
				} else {
					// log error message
					Yii::app()->xero->log('failed to save credit note to xero for : ' . $xdata->no, Xero::LOG_LEVEL_ERR);
				}
			} catch (Exception $mye) {
				$msg = 'failed to save credit note - ' . $mye->getMessage();
				$msg .= PHP_EOL;
				$msg .= 'Credit Note Data : ' . json_encode($creditNote, JSON_PRETTY_PRINT);
				Yii::app()->xero->log($msg, Xero::LOG_LEVEL_ERR);
			}
		}
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'cust' => array(self::BELONGS_TO, 'Org', 'org_id'),
			'invoices' => array(self::MANY_MANY, 'Invoice', 'pay_inv(pay_id, inv_id)'),
			'pays' => array(self::HAS_MANY, 'PayInv', 'pay_id'),
			'ops' => array(self::BELONGS_TO, 'User', 'op_id'),
			'credit_lines' => array(self::HAS_MANY, 'CreditLine', 'pid'),
			'from_invoice' => array(self::HAS_MANY, 'CreditNoteFromInvoice', 'payment_id'),
		);
	}

	public function getFromInvNos()
	{
		$nos = [];
		foreach ($this->from_invoice as $i => $inv) {
			$nos[] = @$inv->invoice->no;
		}

		return "<div class='gridtext gridtext_small'>".implode(',', $nos)."</div>";
	}

	public function getInvNos($l = 0)
	{
		$nos = [];
		foreach ($this->invoices as $i => $inv) {
			if ($l > 0 && $i + 1 > $l) {
				break;
			}

			$nos[] = $inv->no;
		}

		return implode(',', $nos) . ($l > 0 && sizeof($this->invoices) > $l ? '..(' . sizeof($this->invoices) . ')' : '');
	}

	public function subOrgName($short = 0)
	{
		if (empty($this->mdata['suborg'])) return '';
		$subo = Org::model()->findByPk($this->mdata['suborg']);
		if (empty($subo)) return '';
		return $short > 0 ? $subo->shortName($short) : $subo->name;
	}

	public function afterSave()
	{
		if (!$this->nolog && !empty($this)) {
			$extra = empty($this->custom_log_note) ? array() : array('note' => $this->custom_log_note);
			$opname = 'Cron Or API';
			if (isset(Yii::app()->user)) {
				$opname = Yii::app()->user->name;
			}

			if ($this->isNewRecord) {
				Log::add($this, Log::LOG_TYPE_CREATE, array_merge(['notes' => $opname . ' create'], $extra));
			} else {
				Log::add($this, Log::LOG_TYPE_UPDATE, array_merge(['notes' => $opname . ' update status is :' . $this->getStatus()], $extra));
			}
		}

		if ($this->xero_id != '' && $this->status == self::PAYMENT_STATUS_DELETED) {
			$this->delete2xero();
		}
	}

	public function getAwb()
	{
		if(!empty($this->mdata['consol']))
		{
			return @Consol::model()->find("no = :no",[":no"=>$this->mdata['consol']])->awb;
		}
		return "";
	}

	public function delete2xero()
	{
		if (yii::app()->name != 'TLA') {
			$xero = new XeroAPI('xero_token_pcaex');
		} else {
			$xero = new XeroAPI('xero_token_toplog');
		}
		$results = $xero->get('Accounting\CreditNote', ['Type' => 'ACCRECCREDIT', 'creditNoteNumber' => explode('-', $this->no)[0]]);
		try {
			if (!empty($results) && count($results) == 1) {
				$xero->delete('Accounting\CreditNote', $results[0]['CreditNoteID']);
			} else {
				// log error message
				Yii::app()->xero->log('failed to delete creditnote to xero for : ' . $this->no, Xero::LOG_LEVEL_ERR);
			}
		} catch (Exception $mye) {
			$msg = 'failed to delete creditnote - ' . $mye->getMessage();
			$msg .= PHP_EOL;
			Yii::app()->xero->log($msg, Xero::LOG_LEVEL_ERR);
		}
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'org_id' => 'Client',
			'client' => 'Client',
			// 'date' => 'Date',
			'date' => 'Bank Trans Date',
			// 'transaction_date' => 'Transaction Date',
			'transaction_date' => 'Hvlv Create Date',
			'suborg_name' => 'Sub A/C',
			'status' => 'Status',
			'bank' => 'Account',
			'currency' => 'Currency',
			'amount' => 'Amount',
			'invnos' => 'Inv#',
			'ata' => 'Avl. Amt',
			'refund' => 'Refund',
			'type' => 'Type',
			'ref' => 'Ref',
			'bank_transaction_id' => 'Bank Transaction ID',
			'note' => 'Note',
			'meta' => 'Meta',
			'gst' => 'Inv GST',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($pgn = true, $ps = 30, $ec = false)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;
		$with = [];
		if (!empty($this->client)) {
			$with[] = 'cust';
			//$criteria->compare('cust.name', $this->client, true);
			$criteria->addCondition('cust.name LIKE :cust_name OR t.org_id = :org_id');
			$criteria->params[':cust_name'] = '%' . $this->client . '%';
			$criteria->params[':org_id'] = $this->client;
		}

		$criteria->compare('t.status', $this->status, true);
		$criteria->compare('t.dpmt', $this->dpmt, true);
		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('t.no', $this->no, true);
		$criteria->compare('t.op_id', $this->op_id, true);
		$criteria->compare('t.date', $this->date, true);
		$criteria->compare('t.transaction_date', $this->transaction_date, true);
		$criteria->compare('t.bank', $this->bank, true);
		$criteria->compare('t.currency', $this->currency, true);
		$criteria->compare('t.amount', $this->amount, true);
		$criteria->compare('t.ata', $this->ata, true);
		$criteria->compare('t.type', $this->type);
		$criteria->compare('t.ref', $this->ref, true);
		$criteria->compare('t.note', $this->note, true);
		$criteria->compare('t.xero_id', $this->xero_id, true);
		$criteria->compare('t.bank_transaction_id', $this->bank_transaction_id, false);

		if (!empty($this->opids)) {
			$criteria->addInCondition('t.op_id', $this->opids);
		}

		if (!empty($this->dpmts)) {
			$criteria->addInCondition('t.dpmt', $this->dpmts);
		}

		if (!empty($this->flag)) {
			$criteria->addCondition('t.flag & ' . $this->flag . ' > 0');
		}

		if (!empty($this->invnos)) {
			$with[] = 'invoices';
			$criteria->compare('invoices.no', $this->invnos, true);
		}

		if (!empty($this->op_name)) {
			$with[] = 'ops';
			$criteria->compare('ops.fname', $this->op_name, true);
		}

		if (!empty($this->fromInvoice)) {
			$with[] = 'from_invoice';
			$with[] = 'from_invoice.invoice';
			$criteria->compare('invoice.no', $this->fromInvoice, true);
		}

		if (!empty($this->suborg_name)) {
			$criteria->addCondition('JSON_VALUE(t.meta, "$.suborg") = :suborg_id OR JSON_VALUE(t.meta, "$.suborg") IN (SELECT id FROM org WHERE name LIKE :suborg_name)');
			$criteria->params[':suborg_name'] = '%'.$this->suborg_name.'%';
			$criteria->params[':suborg_id'] = $this->suborg_name;
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
				'defaultOrder' => 't.status ASC, t.id DESC',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	public function searchForReconciled($pgn = true, $ps = 30, $ec = false)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;
		$criteria->addCondition('t.status < :td AND t.bank_transaction_id > 0 AND t.date > :ldate ');
		$criteria->params = array(
			':td' => Payment::PAYMENT_STATUS_DELETED,
			':ldate' => '2017-07-01',
		);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.status ASC, t.id DESC',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	public function getInvoiceGsts()
	{
		$gst = 0;
		$invoices = array();
		foreach ($this->invoices as $invoice) {
			$invoices[] = $invoice->id;
		}
		foreach ($this->pays as $pay) {
			if (!empty($pay->invoice->gst) && $pay->invoice->gst > 0 && in_array($pay->invoice->id, $invoices)) {
				$pay->invoice->total = $pay->invoice->total ? $pay->invoice->total : 1;
				$gst += $pay->invoice->total > 0 ? $pay->amount / $pay->invoice->total * $pay->invoice->gst : 0;
			}
		}
		return number_format($gst, 2);
	}

	public function getInvoiceOrgName()
	{
		$orgName = !empty($this->cust)?$this->cust->name:"";
		$subName = "";
		if(!empty($this->mdata['suborg']))
		{
			$sorg = Org::model()->findByPk($this->mdata['suborg']);
			$subName = !empty($sorg)?"|".$sorg->name:"";
		}
		return $orgName.$subName;
	}

	public function exportExcel()
	{
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, [$this->no]);
		if (!empty($this->mdata['suborg'])) {
			$org = Org::model()->findByPk($this->mdata['suborg']);
			$this->mdata['name'] = $org->name;
		}
		$xls->addRow($i++, [$this->mdata['name']]);
		$xls->addRow($i++, []);
		$xls->addRow($i++, ['No', 'Description', 'QTY', 'Rate', 'Amount AUD']);
		$gst = 0;
		foreach ($this->credit_lines as $k => $il) {
			$xls->addRow($i++, [($k+1), $il->description, $il->qty, $il->rate, $il->amount - $il->gst]);
			$gst += $il->gst;
		}
		$xls->addRow($i++, ['', '', '', 'Subtotal:', $this->getCurrency() . ' ' . AppHelper::money_format('%i', $this->amount - $gst)]);
		if ($gst > 0) {
			$xls->addRow($i++, ['', '', '', 'GST:', AppHelper::money_format('%i', ($gst))]);
		}
		$xls->addRow($i++, ['', '', '', 'Total:', $this->getCurrency() . AppHelper::money_format('%i',$this->amount)]);
		$xls->output($this->no . '.xls');
	}


	private function getMyRegion()
	{
		$regions =  SystemSetting::getInvoiceRegions();
		$region = $regions[106];

		return $region;
	}

	public function getFromInvoiceNumbers()
	{
		$cs = CreditNoteFromInvoice::model()->findAll("payment_id = :paymentId",[":paymentId"=>$this->id]);
		if(!empty($cs))
		{
			$invoices = Invoice::model()->findAll("id in (".join(",",array_column($cs,'invoice_id')).")");
			if(!empty($invoices))
			{
				return join(",",array_column($invoices,'no'));
			}
		}
		return "";
	}

	public function getGst()
	{
		$gst = 0;
		foreach ($this->credit_lines as $key => $value) {
			$gst+= $value->gst;
		}
		return $gst;
	}
}
