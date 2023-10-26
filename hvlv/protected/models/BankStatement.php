<?php

/**
 * This is the model class for table "bank_statement".
 *
 * The followings are the available columns in table 'bank_statement':
 * @property string $id
 * @property integer $bank_account_id
 * @property integer $currency
 * @property string $created
 * @property string $desc
 * @property string $debits
 * @property string $credits
 * @property string $balance
 * @property string $org_id
 * @proterty string $hash
 * @property integer $reconciled
 * @property string $reconciled_date
 */
class BankStatement extends CActiveRecord
{
	public static $states = array(
		1 => 'Reconciled',
		0 => 'Unreconciled',
		11 => 'Deleted',
	);

	public $cust_name;

	public function getStatus(){
		$statusTag = Yii::t(strtolower(__CLASS__), empty(self::$states[$this->reconciled])? '' : self::$states[$this->reconciled]);
		if ( $this->reconciled == 1 ) {
			$statusTag = '<span style="color:#008000">' . $statusTag . '</span>';
		} else {
			$statusTag = '<span style="color:#ff0000">' . $statusTag . '</span>';
		}
		return $statusTag;
	}

	public function getShortOrgName() {
		if (!empty($this->org->name)) {
			return (strlen($this->org->name) > 30) ? (mb_substr($this->org->name, 0, 30) . "..") : $this->org->name;
		} else {
			return '';
		}
	}

	public function getShortDesc() {
		return (strlen($this->desc) > 30) ? (mb_substr($this->desc, 0, 15) . "..") : $this->desc;
	}

	public function getLastNote() {
		$log = Log::getLast1($this, 6);
		if ( !empty($log) ) {
			return $log->getExtra();
		}
		return '';
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'bank_statement';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bank_account_id', 'required'),
			array('bank_account_id, currency, reconciled, org_id', 'numerical', 'integerOnly'=>true),
			array('desc', 'length', 'max'=>250),
			array('hash', 'length', 'max'=>33),
			array('debits, credits, balance', 'length', 'max'=>20),
			array('created, cust_name', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, bank_account_id, currency, reconciled_date, created, desc, debits, credits, hash, org_id, cust_name, balance, reconciled', 'safe', 'on'=>'search'),
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
			'org' => array(self::BELONGS_TO, 'Org', 'org_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'bank_account_id' => 'Bank Account',
			'currency' => 'Currency',
			'created' => 'Created',
			'desc' => 'Desc',
			'debits' => 'Spent',
			'credits' => 'Received',
			'balance' => 'Balance',
			'org' => 'Customer',
			'hash' => 'Hash',
			'reconciled' => 'Reconciled',
			'reconciled_date' => 'Reconciled Date',
		);
	}

	public function getBalance() {
		$model = BankStatement::model()->find(array(
			'condition' => 'id > 0 AND balance != 0 AND credits = 0 AND debits = 0',
			'order' => 't.id DESC'
		));

		$sql = 'SELECT SUM(debits) as spent, SUM(credits) as received FROM bank_statement WHERE reconciled != 11';
		$c = Yii::app()->db->createCommand($sql);
		$c = $c->queryAll();

		if ( empty($model) ) {
			$balance = $c[0]['received'] - $c[0]['spent'];
		} else {
			$balance = $model->balance + $c[0]['received'] - $c[0]['spent'];
		}

		return $balance;
	}

	public function getRecAmount() {
		$model = BankStatement::model()->find(array(
			'condition' => 'id > 0 AND balance != 0 AND credits = 0 AND debits = 0',
			'order' => 't.id DESC'
		));

		$sql = 'SELECT SUM(debits) as spent, SUM(credits) as received FROM bank_statement WHERE reconciled = 1';
		$c = Yii::app()->db->createCommand($sql);
		$c = $c->queryAll();

		if ( empty($model) ) {
			$balance = $c[0]['received'] - $c[0]['spent'];
		} else {
			$balance = $model->balance + $c[0]['received'] - $c[0]['spent'];
		}

		return $balance;
	}

	public function getUnrecAmount() {
		$sql = 'SELECT SUM(debits) as spent, SUM(credits) as received FROM bank_statement WHERE reconciled = 0';
		$c = Yii::app()->db->createCommand($sql);
		$c = $c->queryAll();

		return $c[0]['received'] - $c[0]['spent'];
	}

	public function getOpening() {
		$model = BankStatement::model()->find(array(
			'condition' => 'id > 0 AND balance != 0 AND credits = 0 AND debits = 0',
			'order' => 't.id DESC'
		));

		if ( empty($model) ) {
			$balance = ['amount' => 0, 'date' => date('Y-m-d')];
		} else {
			$balance = ['amount' => $model->balance, 'date' => $model->created];
		}

		return $balance;
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
	public function search($ec = null)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('bank_account_id',$this->bank_account_id);
		$criteria->compare('currency',$this->currency);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('hash',$this->hash,false);
		$criteria->compare('debits',$this->debits,true);
		$criteria->compare('credits',$this->credits,true);
		$criteria->compare('balance',$this->balance,true);
		$criteria->compare('org',$this->org,true);
		$criteria->compare('reconciled',$this->reconciled);
		$criteria->compare('reconciled_date',$this->reconciled_date);
		$criteria->addCondition('credits != 0 || debits != 0');
		$criteria->addCondition('reconciled != 11');

		$with = array();
		if ( !empty($this->cust_name) ) {
			$with[] = 'org';
			$criteria->compare('org.name', $this->cust_name, true);
		}

		if ( !empty($with) ) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if ($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=> 't.id DESC',
			),
			'pagination'=>array(
				'pageSize' => 20,
			)
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BankStatement the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function afterSave() {
		Log::add($this, $this->isNewRecord? 3 : 4, array('status' => BankStatement::$states[$this->reconciled]));
	}
}
