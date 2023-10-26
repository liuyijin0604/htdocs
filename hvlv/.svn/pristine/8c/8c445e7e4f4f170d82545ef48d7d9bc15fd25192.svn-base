<?php

/**
 * This is the model class for table "ex_channel".
 *
 * The followings are the available columns in table 'ex_channel':
 * @property string $id
 * @property string $code
 * @property string $name
 * @property string $pod
 * @property integer $status
 * @property string $rules
 * @property string $meta
 */
class ExChannel extends CActiveRecord
{

	public static $states = array(
		10 => 'Pending',
		50 => 'Active',
		90 => 'Inactive',
	);
	
	public $mdata = [];
	public $nolog = false;
	public $custom_log_note = '';
	public $_mkv = [];

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ex_channel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, name, pod, status', 'required'),
			array('rules, meta', 'safe'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('code, pod', 'length', 'max'=>5),
			array('code', 'unique', 'on'=>'create'),
			array('name', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, code, name, pod, status, rules, meta', 'safe', 'on'=>'search'),
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
		);
	}
	
	public function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		return parent::beforeSave();
	}
	
	public function afterSave(){
		if(!$this->nolog && !empty($this)){
			$extra = empty($this->custom_log_note)? array() : array('note' => $this->custom_log_note);
			Log::add($this, $this->isNewRecord? 3 : 4, array_merge(array('status' => $this->getStatus()), $extra));
		}
		return parent::afterSave();
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return parent::afterFind();
	}

	public function getStatus(){
		return isset(static::$states[$this->status])? Yii::t(strtolower(__CLASS__), static::$states[$this->status]) : $this->status;
	}

	public function customCheck($type, $qty, $goods, $weight, &$parcel){
		if(empty($this->mdata['ccf'])) return true;
		$v = eval($this->mdata['ccf']);
		return $v === false? false: true;
	}

	public static function getSuffix($code){
		$r = self::model()->find('code = :c', [':c' => $code]);
		return empty($r)? 'xx' : $r->mdata['suffix'];
	}

	public static function getPocs($inact = false){
		$rs = self::model()->findAll(['condition' => ($inact? '' : 'status = 50'), 'order' => 'status, name']);
		$l = [];
		foreach($rs as $r){
			$l[$r->code] = $r->name;
		}
		return $l;
	}

	public static function getNames($inact = false){
		$rs = self::model()->findAll(['condition' => ($inact? '' : 'status = 50'), 'order' => 'status, name']);
		$l = [];
		foreach($rs as $r){
			$l[$r->name] = $r->name;
		}
		return $l;
	}

	public static function getName($code){
		$r = self::model()->find('code = :c', [':c' => $code]);
		return empty($r)? $code : $r->name;
	}

	public static function getDuty($code, $date) {
		$org_rate = OrgRate::model()->find('code = :c AND vfrom >= :date', [':c' => $code, ':date' => $date]);
		if (isset($org_rate->mdata['duty_ppk'])) {
			return $org_rate->mdata['duty_ppk'];
		} else {
			$sql = "select SUM(bl.actual_amount) from consol c join billing_line bl on bl.link_id = c.id where c.poc = 'CNXI2' AND c.etd >= '2018-01-13' AND bl.type = 2 AND bl.dpmt = 20 AND bl.desc = 'channel_duty_cost'";
			$c = Yii::app()->db->createCommand($sql);
			$tot_duty = floatval($c->queryScalar());

			$sql = "select SUM(ec.amount) from consol c join exconsol_cost ec on ec.consol_id = c.id where c. poc = 'CNXI2' AND c.etd >= '2018-01-13' and ec.type = 1";
			$c = Yii::app()->db->createCommand($sql);
			$tot_ppk = floatval($c->queryScalar());
			return $tot_duty / ($tot_ppk ? $tot_ppk : 1);
		}
	}

	public static function getCode($name) {
		$r = self::model()->find('name = :n', [':n' => $name]);
		return empty($r) ? $name : $r->code;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'code' => 'Code',
			'name' => 'Name',
			'pod' => 'Pod',
			'status' => 'Status',
			'rules' => 'Rules',
			'meta' => 'Meta',
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
	public function search($pgn=true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('code',$this->code);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('pod',$this->pod);
		$criteria->compare('status',$this->status);
		$criteria->compare('rules',$this->rules,true);
		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=> ['defaultOrder' => 't.status ASC, t.name ASC'],
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExChannel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
