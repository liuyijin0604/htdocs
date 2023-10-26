<?php

/**
 * This is the model class for table "job".
 *
 * The followings are the available columns in table 'job':
 * @property string $id
 * @property string $no
 * @property integer $dpt_id
 * @property integer $user_id // op id
 * @property ingeger $sp_id // sales person id
 * @property integer $dpmt
 * @property integer $owner_id
 * @property string $created
 * @property integer $type
 * @property integer $status
 * @property string $meta
 */
class Job extends CActiveRecord
{
	public static $types = array(
		10 => 'EdiJob',
	);

	//define departments
	const DPMT_AIRSEA = 30;
	const DPMT_3PL = 40;

	public static $dpmts = [
		30 => 'Air/Sea',
		40 => '3PL',
	];

	public static $my_type = 0;
	public $nolog = false;
	public $custom_log_note = '';
	public $mdata = array();

	public $owner_name;
	public $user_name;
	public $InvoiceNo;
	public $plt, $etd, $ata, $transit_type, $result, $create_user;

	public function __construct($scenario='insert'){
		parent::__construct($scenario);
		if(static::$my_type > 0) $this->type = static::$my_type;
	}

	protected function instantiate($attr){
		$cls = isset(self::$types[$attr['type']])? self::$types[$attr['type']] : get_class($this);
		return new $cls(null);
	}

	public function defaultScope(){
		return empty(static::$my_type)? [] : ['condition' => $this->dbConnection->quoteColumnName($this->getTableAlias(false, false).'.type').'='.static::$my_type];
	}

	public function unsetAttributes($names = NULL){
		parent::unsetAttributes($names);
		if(static::$my_type > 0) $this->type = static::$my_type;
	}

	public function afterSave(){
		if(!$this->nolog && !empty($this)){
			$extra = empty($this->custom_log_note)? array() : array('note' => $this->custom_log_note);
			Log::add($this, $this->isNewRecord? 3 : 4, array_merge(array('status' => $this->getStatus()), $extra));
		}
	}

	public function beforeSave(){
		if ($this->isNewRecord) $this->mdata['create'] = Yii::app()->user->id;
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		if (empty($this->created) || $this->created == '0000-00-00') $this->created = date('Y-m-d');
		return true;
	}

	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return true;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'job';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('no, dpt_id, owner_id, created, due,type, dpmt,status', 'required'),
			array('dpt_id, owner_id,user_id, type,dpmt, status,currency', 'numerical', 'integerOnly'=>true),
			array('no', 'length', 'max'=>20),
			array('dpt_id, owner_id', 'length', 'max'=>10),
			array('awb', 'length', 'max'=>45),
			array('owner_id', 'checkCredit', 'on'=>'create'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, no, dpt_id, user_id, sp_id, owner_id,awb,created,currency, due,type, status,dpmt, meta,owner_name,user_name,InvoiceNo, plt, etd, ata, transit_type, result, inv_type, create_user', 'safe', 'on'=>'search'),
		);
	}

	public function checkCredit(){
		$org = Org::model()->findByPk($this->owner_id);
		if(empty($org)){
			$this->addError('owner_id', 'Client invalid');
			return false;
		}
		// if($org->overCreditLimit()){
		// 	$this->addError('owner_id', 'Client '.$org->name.' credit limit exceeded, please contact accounts!');
		// 	return false;
		// }
		return true;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'depot' => array(self::BELONGS_TO, 'Org', 'dpt_id'),
			'owner' => array(self::BELONGS_TO, 'Org', 'owner_id'),
			'lines' => array(self::HAS_MANY, 'JobLine', 'job_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'logs' => array(self::HAS_MANY, 'Log', 'lid', 'on' => "logs.model = '".get_called_class()."'", 'order' => 'logs.time ASC'),
			'sales' => array(self::BELONGS_TO, 'User', 'sp_id'),
			'awbconsol' => array(self::HAS_ONE, 'EdiAwbConsol', ['awb' => 'awb']),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'no' => 'No',
			'dpt_id' => 'Depot',
			'owner_id' => 'Owner',
			'user_id' => 'User',
			'created' => 'Created',
			'due' => 'Due Date',
			'type' => 'Type',
			'status' => 'Status',
			'currency' => 'Currency',
			'dpmt' => 'Type',
			'meta' => 'Meta',
			'sp_id' => 'Sales Person',
			'create_user' => 'Create By',
		);
	}

	protected function totCost(){
		return 0;
	}

	protected function totRevenue(){
		return 0;
	}

	protected function totProfit(){
		return 0;
	}

	/**
	 * @return int|string
	 */
	public function getStatus(){
		$t = isset(self::$types[$this->type])? self::$types[$this->type] : self;
		$states = empty($t::$states)? self::$states : $t::$states;
		return isset($states[$this->status])? Yii::t(strtolower(__CLASS__), $states[$this->status]) : $this->status;
	}

	/**
	 * @return string
	 */
	public function getType(){
		return Yii::t(strtolower(__CLASS__), static::$types[$this->type]);
	}

	public function getInvType()
	{
		if (isset(EdiJob::$inv_types[$this->inv_type]) && $this->inv_type != 0) {
			return Yii::t(strtolower(__CLASS__), EdiJob::$inv_types[$this->inv_type]);
		} else {
			return '';
		}
	}

	/**
	 * @return bool
	 */
	public function hasInvoice(){
		return Invoice::model()->count('job_id = :jid AND status NOT IN (8, 10)',[':jid' => $this->id]) > 0;
	}

	public function getCreateUser()
	{
		if (!empty($this->mdata['create'])) {
			$user = User::model()->findByPk($this->mdata['create']);
			return $user->fname;
		} else {
			return '';
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
	public function search($page = true, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.no',$this->no,true);
		$criteria->compare('t.dpt_id',$this->dpt_id);
		$criteria->compare('t.owner_id',$this->owner_id);
		$criteria->compare('t.dpmt',$this->dpmt);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('t.awb',$this->awb,true);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.due',$this->due);
		$criteria->compare('t.type',$this->type);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.currency',$this->currency);
		$criteria->compare('t.inv_type', $this->inv_type);
	//	$criteria->compare('t.meta',$this->meta,true);

		$with = array();

		if(!empty($this->owner_name)){
			$with[] = 'owner';
			//$criteria->compare('cust.name',$this->to_name,true);
			$criteria->addCondition('owner.name LIKE :owner_name OR t.owner_id = :to_id');
			$criteria->params[':owner_name'] = '%'.$this->owner_name.'%';
			$criteria->params[':to_id'] = $this->owner_name;
		}

		if(!empty($this->user_name)){
			$with[] = 'user';
			$criteria->compare('user.fname', $this->user_name, true);
		}

		if (!empty($this->sp_id)) {
			$with[] = 'sales';
			$criteria->addCondition('sales.fname LIKE :sale_name OR sales.lname LIKE :sale_name OR t.sp_id = :sp_id');
			$criteria->params[':sale_name'] = '%' . $this->sp_id . '%';
			$criteria->params[':sp_id'] = $this->sp_id;
		}

		if ( !empty($this->InvoiceNo) ) {
			$with[] = 'invoice';
			if($this->InvoiceNo == '=0'){
				$criteria->addCondition('invoice.id IS NULL');
			}else{
				$criteria->compare('invoice.no',$this->InvoiceNo,true);
			}
		}

		if (!empty($this->create_user)) {
			$criteria->addCondition('JSON_VALUE(t.meta, "$.create") = :create_id OR JSON_VALUE(t.meta, "$.create") IN (SELECT id FROM user WHERE fname LIKE :create)');
			$criteria->params[':create'] = '%' . $this->create_user . '%';
			$criteria->params[':create_id'] = $this->create_user;
		}

		if (!empty($this->plt)) {
			$criteria->compare('JSON_VALUE(meta, "$.plt")', $this->plt);
		}

		if (!empty($this->etd)) {
			$with[] = 'awbconsol';
			$criteria->compare('awbconsol.etd', $this->etd, true);
		}

		if (!empty($this->ata)) {
			$criteria->compare('JSON_VALUE(meta, "$.ata")', $this->ata, true);
		}

		if (!empty($this->transit_type)) {
			$criteria->compare('JSON_VALUE(meta, "$.transit_type")', $this->transit_type);
		}

		if (!empty($this->result)) {
			$criteria->compare('JSON_VALUE(meta, "$.result")', $this->result);
		}

		if ($ec) {
			if (is_array($ec->with)) {
				$with = array_merge($with, $ec->with);
			} else {
				$with[] = $ec->with;
			}
			$criteria->mergeWith($ec);
		}

		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		$criteria->order = 't.created DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>$page ? array(
				'pageSize' => 30,
			) : false
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Job the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
