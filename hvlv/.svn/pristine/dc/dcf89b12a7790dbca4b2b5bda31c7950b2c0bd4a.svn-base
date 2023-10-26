<?php

/**
 * This is the model class for table "payment_b".
 *
 * The followings are the available columns in table 'payment_b':
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
 * @property integer $type
 * @property integer $flag
 * @property string $ref
 * @property string $note
 */
class PaymentBilling extends oActiveRecord
{
	const PAYMENT_TYPE_EFT = 1;
	const PAYMENT_TYPE_CHEQUE = 2;
	const PAYMENT_TYPE_CASH = 3;
	const PAYMENT_TYPE_CREDIT_CARD = 4;
	const PAYMENT_TYPE_CREDIT_NOTE = 5;
	const PAYMENT_TYPE_POLI = 6;
	const PAYMENT_TYPE_PAYPAL = 7;
	const PAYMENT_TYPE_SUPAY = 8;
	const PAYMENT_TYPE_OFFSET=9;
	const PAYMENT_TYPE_FASTWAY = 10;
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
	public static $states = array(
		'1' => 'Pending',
		'6' => 'Posted',
		'9' => 'Deleted',
	);

	public static $banks = array(
		'10' => 'Westpac AUD',
		'11' => 'Westpac USD',
		'20' => 'RMB',
		'90' => 'Credit Note',
		'91' => 'Manual Credit Note',
	);
	
	public static $thedpmts = [
		10 => 'Import',
		20 => 'Export',
		30 => 'Air/Sea',
		40 => '3PL',
	];

	public static $flags = [
		 1 => 'QUE',    //waiting to send to client;
		 2 => 'SEND',   //already sent to the 
	];

	public $client, $billingnos,$opids,$op_name,$mdata,$dpmts, $gst;
	public $nolog = false;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PaymentBilling the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'payment_b';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, date, amount, type', 'required'),
			array('ata, status, bank, currency, ref, note,transaction_date', 'safe'),
			array('type,bank_transaction_id', 'numerical', 'integerOnly'=>true),
			array('amount', 'length', 'max'=>12),
			array('ref', 'length', 'max'=>100),
			array('xero_id,no', 'length', 'max'=>45),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('billingnos', 'safe'),
			array('id, org_id, client, date,transaction_date,no,dpmt,flag,status,meta,opids,dpmts,op_name, bank_transaction_id,bank,op_id, currency, amount, ata, type, ref, note,xero_id ,billingnos', 'safe', 'on'=>'search'),
		);
	}

	public function updateAta(){
		$rs = PayBill::model()->findAll('pay_id = :id', [':id' => $this->id]);
		$tt = 0;
		foreach($rs as $r){
			$tt += $r->amount;
		}
		$this->ata = $this->amount - $tt - (!empty($this->mdata['diff']) ? $this->mdata['diff'] : 0);
		$this->update(['ata']);
	}

	protected function beforeSave(){
		if(empty($this->transaction_date)) $this->transaction_date = date('Y-m-d');
		if(!empty($this->mdata)) $this->meta= json_encode ($this->mdata);
		return true;
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata= json_decode($this->meta,true);
		if(empty($this->no)){
			if($this->type==5){
				$this->no='CRN'.sprintf("%05s", substr($this->id, -5));
			}else{
			 $this->no = strtoupper(substr($this->getType(),0,2)).sprintf("%05s", substr($this->id, -5));
			}
			$this->saveAttributes(['no']);
		}
	}
	
	public function genNo(){
	   if(empty($this->no)){
			if($this->type==5){
				$this->no='CRN'.sprintf("%05s", substr($this->id, -5));
			}else{
				$this->no = strtoupper(substr($this->getType(),0,2)).sprintf("%05s", substr($this->id, -5));   
			}
			//$this->save();
			$this->saveAttributes(['no']);
		}
	}

	public static function typeIdx($n){
		foreach(self::$types as $i=>$t){
			if($t == $n) return $i;
		}
		return 0;
	}
	
	public function getStatus(){
		return Yii::t(strtolower(__CLASS__), self::$states[$this->status]);
	}
		
	public function getType(){
		return Yii::t(strtolower(__CLASS__), self::$types[$this->type]);
	}
		
	public function getBank(){
		return Yii::t(strtolower(__CLASS__), self::$banks[$this->bank]);
	}
		
	public function getCurrency(){
		return Yii::t(strtolower(__CLASS__), Invoice::$currencies[$this->currency]);
	}

	/**
	 * @param $fromDate
	 * @param $endDate
	 * @return mixed
	 */
	public static function getOccurringAmountByDatespan($fromDate,$endDate){
		$sql = 'SELECT SUM(amount) AS t FROM payment_b WHERE status != ' . PaymentBilling::PAYMENT_STATUS_DELETED ;
		$sql .= " AND transaction_date > '" . $fromDate . "' AND transaction_date <= '" . $endDate . "'";
		$c = Yii::app()->db->createCommand($sql);
		return $c->queryScalar();

	}

	/**
	 * @param $fromDate
	 * @param $endDate
	 * @return mixed
	 */
	public static function getOccurringAmountByDatespanByOrg($fromDate,$endDate,$orgId){
		$sql = 'SELECT SUM(amount) AS t FROM payment_b WHERE status != ' . PaymentBilling::PAYMENT_STATUS_DELETED ;
		$sql .= " AND transaction_date > '" . $fromDate . "' AND transaction_date <= '" . $endDate . "'";
		$sql .= " AND org_id = " . $orgId;
		$c = Yii::app()->db->createCommand($sql);
		return $c->queryScalar();

	}


	/**
	 * get occurring payment amount based on month last date
	 * @param $monthLastDate
	 * @return int
	 */
	public static function getOccurringAmountByMonth($monthLastDate){

		// based on month last date get first date
		$monthFirstDate = date('Y-m-01',strtotime($monthLastDate));
		$sql = 'SELECT SUM(amount) AS t FROM payment_b WHERE status != ' . PaymentBilling::PAYMENT_STATUS_DELETED ;
		$sql .= " AND transaction_date >= '" . $monthFirstDate . "' AND transaction_date <= '" . $monthLastDate . "'";
		$c = Yii::app()->db->createCommand($sql);
		return $c->queryScalar();

	}

	/**
	 * get total occurred  paid amount end of specified month
	 * @param $monthLastDate
	 * @return mixed
	 */
	public static function getPaidAmountByMonth($monthLastDate){

		// based on month last date get first date
		$sql = 'SELECT SUM(pi.amount) AS t FROM pay_bill as pi LEFT JOIN payment_b as p ON p.id = pi.pay_id LEFT JOIN billing as i ON i.id = pi.bill_id WHERE ';
		$sql .= ' p.status != ' . PaymentBilling::PAYMENT_STATUS_DELETED . ' AND i.status != ' . 11;
		$sql .= " AND (pi.transaction_date <= '" . $monthLastDate . "'";

		// currently in order to support old date, we need to take care NULL transaction date
		$sql .= " OR pi.transaction_date is NULL)";

		$c = Yii::app()->db->createCommand($sql);

		$linkedAmount = $c->queryScalar();

		// calculate all payment amount which is not linked with any billing
		$sql = 'SELECT SUM(p.amount) AS t FROM payment_b as p WHERE p.id not in ( SELECT pay_id FROM pay_bill WHERE pay_id > 0)  AND';
		$sql .= ' p.status != ' . PaymentBilling::PAYMENT_STATUS_DELETED;
		$sql .= " AND (p.transaction_date <= '" . $monthLastDate . "'";
		$sql .= " OR p.transaction_date is NULL)";
		$c = Yii::app()->db->createCommand($sql);

		$allAmount = $linkedAmount + $c->queryScalar();

		return $allAmount;

	}

	/**
	 * get all payment amount which are not linked with any billing
	 * @param $orgid
	 * @param $monthLastDate
	 * @return mixed
	 */
	public static function orgTotalUnallocPaymentAmount($orgid,$monthLastDate){
		// calculate all payment amount which is not linked with any billing
		$sql = 'SELECT SUM(p.amount) AS t FROM payment_b as p WHERE p.id not in ( SELECT pay_id FROM pay_bill WHERE pay_id > 0)  AND';
		$sql .= ' p.org_id = ' . $orgid . ' AND';
		$sql .= ' p.status != ' . PaymentBilling::PAYMENT_STATUS_DELETED;
		$sql .= " AND (p.transaction_date <= '" . $monthLastDate . "'";
		$sql .= " OR p.transaction_date is NULL)";
		$c = Yii::app()->db->createCommand($sql);
		return $c->queryScalar();
	}

	/**
	 * @param $monthLastDate
	 * @return array
	 */
	public static function getTotalUnallocPaymentAmountList($monthLastDate){
		// calculate all payment amount which is not linked with any billing
		$sql = 'SELECT p.org_id,SUM(p.amount) AS t FROM payment_b as p WHERE p.id not in ( SELECT pay_id FROM pay_bill WHERE pay_id > 0)  AND';
		$sql .= ' p.status != ' . PaymentBilling::PAYMENT_STATUS_DELETED;
		$sql .= " AND (p.transaction_date <= '" . $monthLastDate . "'";
		$sql .= ' OR p.transaction_date is NULL)';
		$sql .= ' GROUP by p.org_id';
		$c = Yii::app()->db->createCommand($sql);
		$rt = $c->queryAll();

		$results = array();
		foreach ( $rt as $r ) {
			$results[$r['org_id']] = $r['t'];
		}
		return $results;
	}

	public static function getTotalAtaPaymentAmountList($monthLastDate){
		// calculate all payment amount which is not linked with any billing
		$sql = 'SELECT p.org_id,SUM(p.ata) AS t FROM payment_b as p WHERE ';
		$sql .= ' p.status != ' . PaymentBilling::PAYMENT_STATUS_DELETED . ' AND ata > 0 ';
		$sql .= " AND (p.transaction_date <= '" . $monthLastDate . "'";
		$sql .= ' OR p.transaction_date is NULL)';
		$sql .= ' GROUP by p.org_id';
		$c = Yii::app()->db->createCommand($sql);
		$rt = $c->queryAll();

		$results = array();
		foreach ( $rt as $r ) {
			$results[$r['org_id']] = $r['t'];
		}
		return $results;
	}

	public static function orgTotalAta($oid){
		$sql = 'SELECT SUM(ata) AS t FROM payment_b WHERE status = 6 AND org_id = '.$oid;
		$c = Yii::app()->db->createCommand($sql);
		return $c->queryScalar();
	}

	public static function orgAtaDetails($oid){
		$ps = PaymentBilling::model()->findAll ('ata > 0 AND status = 6 AND org_id = :oid ',[':oid' => $oid]);
		return $ps;
	}

	public static function orgAtaBefore($oid, $date, $mode) {
		$amt = 0;

		$sql = 'select sum(pi.amount) as t from pay_bill pi join billing i on pi.bill_id = i.id join payment_b p on pi.pay_id = p.id where i.to_id = ' . $oid . ' and i.date > "' . $date . '" and p.org_id = ' . $oid .' and p.date <= "' . $date . '"';
		if ($mode == 1) $sql .= ' and p.type != 5 and p.type != 10';
		$amt += Yii::app()->db->createCommand($sql)->queryScalar();

		$sql = 'select sum(ata) as t from payment_b where org_id = ' . $oid . ' and date <= "' . $date . '" and status = 6 and ata > 0';
		if ($mode == 1) $sql .= ' and type != 5 and type != 10';
		$amt += Yii::app()->db->createCommand($sql)->queryScalar();

		return $amt;
	}

	public function orgAtaDetailsBefore($oid, $date, $mode) {
		if (empty($mode)) return [];
		$sql = 'select p.*, pi.amount as total from pay_bill pi join billing i on pi.bill_id = i.id join payment_b p on pi.pay_id = p.id where i.to_id = ' . $oid . ' and i.date > "' . $date . '" and p.org_id = ' . $oid .' and p.date <= "' . $date . '"';
		if ($mode == 1) $sql .= ' and p.type != 5 and p.type != 10';
		$ps = Yii::app()->db->createCommand($sql)->queryAll();

		$sql = 'select *, ata as total from payment_b where org_id = ' . $oid . ' and date <= "' . $date . '" and status = 6 and ata > 0';
		if ($mode == 1) $sql .= ' and type != 5 and type != 10';
		$ps = array_merge($ps, Yii::app()->db->createCommand($sql)->queryAll());

		$ids = [];
		$rs = [];
		foreach ($ps as $p) {
			if (!in_array($p['id'], $ids)) {
				$ids[] = $p['id'];
				$rs[] = $p;
			}
		}

		return $rs;
	}

	/**
	 * get Xero xml data from single payment object
	 * we only sync to xero for credit notes now
	 * @param $data
	 * @return mixed
	 */
	public static function getXeroXmlData(&$data){

		$creditNotes = new XeCreditNote();
		$creditNotes->creditNoteNumber = 'PCN-' . $data->id;
		$creditNotes->type = XeCreditNote::TYPE_RECEIVABLE;
		$creditNotes->status = XeCreditNote::ST_AUTHORISED;
		$creditNotes->lineAmountTypes = 'Exclusive';
		$creditNotes->date = $data->date;

		$contact = new XeContact();
		$contact->contactNumber = 'PORG-'. $data->org_id;
		$creditNotes->contact = $contact;
		$desc = $data->ref;
		if ( empty($desc) ) $desc = 'hvlv credit notes';
		$data = array(
			'description' => $desc,
			'quantity' => 1,
			'unitAmount' => $data->ata,
			'accountCode' => AppHelper::getXeroSetting('creditnote_glcode')
		);
		$creditNotes->addLineItem($data);

		return  $creditNotes->getXmlData();
	}

	/**
	 * for returned shipment we need to do following things
	 * 1. get returned amount from billing
	 * 2. create a payment with credit note
	 * 3. accounting will do the following things manually
	 * @param $shipment
	 */
	public static function createReturnedCreditNote(&$shipment)
	{
		// get manifest id
		// 1. find manifest from mani_map by fid = shipment id and model = ExParcel
		$manis = ManiMap::model()->with('manifest')->findAll('manifest.type = 40 AND fid = :id AND model = :exm', array(':id' => $shipment->id,':exm' => 'ExParcel'));

		// try to create a credit note for related billing
		foreach ( $manis as $mani ) {
			if (PaymentBilling::checkReturnedItemInManifest($mani->mani_id, $shipment)) {
				break;
			}
		}

	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'cust' => array(self::BELONGS_TO, 'Org', 'org_id'),
			'billings' => array(self::MANY_MANY, 'Billing', 'pay_bill(pay_id, bill_id)'),
			'pays' => array(self::HAS_MANY, 'PayBill', 'pay_id'),
			'ops'=>array(self::BELONGS_TO,'User','op_id'),
			'credit_lines'=>array(self::HAS_MANY,'CreditLine','pid'),
		);
	}

	public function getBillingNos($l = 0){
		$nos = [];
		foreach ($this->billings as $i => $billing) {
			if($l > 0 && $i+1 > $l) break;
			$nos[] = $billing->billing_cref;
		}

		return implode(',', $nos).($l > 0 && sizeof($this->billings) > $l? '..('.sizeof($this->billings).')' : '');
	}
	
	public function afterSave(){
		if( !$this->nolog && !empty($this)){
			$extra = empty($this->custom_log_note)? array() : array('note' => $this->custom_log_note);
			$opname = 'Cron Or API';
			if ( isset(Yii::app()->user) ) $opname = Yii::app()->user->name;
			if ( $this->isNewRecord ) {
				Log::add($this, Log::LOG_TYPE_CREATE, array_merge(['notes' => $opname . ' create'],$extra) );
			} else {
				Log::add($this, Log::LOG_TYPE_UPDATE, array_merge(['notes' => $opname . ' update status is :' .$this->getStatus() ],$extra) );
			}
		}
	   }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'org_id' => 'Client',
			'client' => 'Client',
			'date' => 'Date',
			'transaction_date' => 'Transaction Date',
			'status' => 'Status',
			'bank' => 'Account',
			'currency' => 'Currency',
			'amount' => 'Amount',
			'billingnos' => 'Billing#',
			'ata' => 'Avl. Amt',
			'type' => 'Type',
			'ref' => 'Ref',
			'bank_transaction_id' => 'Bank Transaction ID',
			'note' => 'Note',
			'meta'=>'Meta',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($pgn=true, $ps=30, $ec=false){
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		$with = [];
		if(!empty($this->client)){
			$with[] = 'cust';
			//$criteria->compare('cust.name', $this->client, true);
			$criteria->addCondition('cust.name LIKE :cust_name OR t.org_id = :org_id');
			$criteria->params[':cust_name'] = '%'.$this->client.'%';
			$criteria->params[':org_id'] = $this->client;
		}

		$criteria->compare('t.status', $this->status, true);
		$criteria->compare('t.dpmt', $this->dpmt, true);
		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('t.no', $this->no, true);
		$criteria->compare('t.op_id', $this->op_id, true);
		$criteria->compare('t.date', $this->date, true);
		$criteria->compare('t.transaction_date', $this->transaction_date, true);
		$criteria->compare('bank', $this->bank, true);
		$criteria->compare('currency', $this->currency, true);
		$criteria->compare('amount', $this->amount, true);
		$criteria->compare('ata', $this->ata, true);
		$criteria->compare('t.type', $this->type);
		$criteria->compare('ref', $this->ref, true);
		$criteria->compare('note', $this->note, true);
		$criteria->compare('xero_id',$this->xero_id,true);
		$criteria->compare('bank_transaction_id',$this->bank_transaction_id,false);
		
		if(!empty($this->opids)){
				$criteria->addInCondition('t.op_id', $this->opids);
		}
		
		if(!empty($this->dpmts)){
			$criteria->addInCondition('t.dpmt', $this->dpmts);
		}
			   
		if(!empty($this->flag)){
			$criteria->addCondition('t.flag & ' . $this->flag . ' > 0');
		}

		if(!empty($this->billingnos)){
			$with[] = 'billings';
			$criteria->compare('billings.billing_cref', $this->billingnos, true);
		}

		if(!empty($this->op_name)){
			$with[]='ops';
			$criteria->compare('ops.fname',$this->op_name,true);
		}

		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if($ec){
			$criteria->mergeWith($ec);
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'t.status ASC, t.date DESC',
			),
			'pagination'=>$pgn? array(
				'pageSize'=>$ps,
			) : false,
		));
	}

	public function searchForReconciled($pgn=true, $ps=30, $ec=false){
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;
		$criteria->addCondition('t.status < :td AND t.bank_transaction_id > 0 AND t.date > :ldate ');
		$criteria->params = array(
			':td' => PaymentBilling::PAYMENT_STATUS_DELETED,
			':ldate' => '2017-07-01'
		);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'t.status ASC, t.id DESC',
			),
			'pagination'=>$pgn? array(
				'pageSize'=>$ps,
			) : false,
		));
	}

	public function getBillingGsts() {
		$gst = 0;
		$billings = array();
		foreach ($this->billings as $billing) {
			$billings[] = $billing->id;
		}
		foreach ($this->pays as $pay) {
			if (!empty($pay->billing->gst) && $pay->billing->gst > 0 && in_array($pay->billing->id, $billings)) {
				$gst += $pay->amount / ($pay->billing->total + $pay->billing->gst) * $pay->billing->gst;
			}
		}
		return number_format($gst, 2);
	}

	public function updateAmount()
	{
		$this->amount = 0;
		$pays = PayBill::model()->findAll('pay_id = :pay_id', [':pay_id' => $this->id]);
		foreach ($pays as $pay) {
			$this->amount += $pay->amount;
		}
		$this->update('amount');
	}

}