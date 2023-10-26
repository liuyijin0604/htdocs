<?php

/**
 * This is the model class for table "cnl_order".
 *
 * The followings are the available columns in table 'cnl_order':
 * @property string $id
 * @property integer $cargoType
 * @property integer $bizType
 * @property integer $status
 * @property integer $incoterm
 * @property string $orderCode
 * @property string $refCode
 * @property string $focOrderCode
 * @property string $couponCode
 * @property string $sellerName
 * @property string $pol
 * @property string $pod
 * @property string $targetEta
 * @property string $targetEtd
 * @property string $cargoReadyDate
 * @property integer $containerLoad
 * @property integer $transportMode
 * @property string $gmtModified
 * @property string $feature
 * @property string $remark
 * @property string $meta
 */
class CnlOrder extends CActiveRecord
{

	public $mdata = [];
	public $nolog = false;
	public $custom_log_note = '';

	public static $cargoTypes = [
		10 => 'General',
		20 => 'Dangerous',
		30 => 'Reefer',
	];

	public static $bizTypes = [
		10 => 'TMALL_DIRECT',
		20 => 'TMALL_HK',
	];

	public static $incoterms = [
		10 => 'CIF',
		20 => 'FOB',
		30 => 'EXW',
		40 => 'FCA',
	];

	public static $containerLoads = [
		10 => 'FCL',
		20 => 'LCL',
	];

	public static $transportModes = [
		10 => 'OCEAN',
		20 => 'AIR',
		30 => 'RAILWAY',
		40 => 'TRUCK',
	];

	public static $states = [
		10 => 'New',
		15 => 'Processing',
		20 => 'Confirmed',
		90 => 'Completed',
		100 => 'Cancelled',
	];

	public static $services = [
		1 => 'ORIGIN_PICKUP,CFS_RECEIVING',
		2 => 'ORIGIN_DECLARATION',
		4 => 'TALLY_BY_PIECE',
		8 => 'TALLY_BY_CARTON',
		16 => 'TALLY_BY_PALLET',
		32=> 'LABELING',
		64 => 'SHIPPING_MARK',
		128 => 'QC',
		256 => 'HSCODE_FILING',
		512 => 'SHELFLIFE_CHECK',
		1024 => 'DOCUMENTATION',
	];

	public static $wmsgs = [
		131 => 'CFM',
		134 => 'CI',
		136 => 'ATD',
		137 => 'ATA',
		140 => 'PRA',
	];

	public $inv_no, $awb, $wmsg;

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'cnl_order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cargoType, bizType, status, incoterm, orderCode, refCode, focOrderCode, couponCode, sellerName, pol, pod, targetEta, targetEtd, cargoReadyDate, containerLoad, transportMode, gmtModified, feature, remark, meta', 'safe'),
			array('cargoType, bizType, status, incoterm, containerLoad, transportMode', 'numerical', 'integerOnly'=>true),
			array('orderCode', 'length', 'max'=>32),
			array('refCode', 'length', 'max'=>64),
			array('pol, pod', 'length', 'max'=>5),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, cargoType, bizType, status, incoterm, orderCode, refCode, focOrderCode, couponCode, sellerName, pol, pod, targetEta, targetEtd, cargoReadyDate, containerLoad, transportMode, gmtModified, feature, remark, meta, inv_no, awb, wmsg', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'items' => array(self::HAS_MANY, 'CnlItem', 'order_id'),
			'oCargos' => array(self::HAS_MANY, 'CnlCargo', 'order_id', 'condition' => 'oCargos.type = 10'),
			'bCargos' => array(self::HAS_MANY, 'CnlCargo', 'order_id', 'condition' => 'bCargos.type = 20'),
		);
	}

	public function getStatus(){
		return isset(static::$states[$this->status])? Yii::t(strtolower(__CLASS__), static::$states[$this->status]) : $this->status;
	}

	public function getCargoType(){
		return isset(static::$cargoTypes[$this->cargoType])? Yii::t(strtolower(__CLASS__), static::$cargoTypes[$this->cargoType]) : $this->cargoType;
	}

	public function getBizType(){
		return isset(static::$bizTypes[$this->bizType])? Yii::t(strtolower(__CLASS__), preg_replace('/^TMALL_/', '', static::$bizTypes[$this->bizType])) : $this->bizType;
	}

	public function getIncoterm(){
		return isset(static::$incoterms[$this->incoterm])? Yii::t(strtolower(__CLASS__), static::$incoterms[$this->incoterm]) : $this->incoterm;
	}

	public function getTransportMode(){
		return isset(static::$transportModes[$this->transportMode])? Yii::t(strtolower(__CLASS__), static::$transportModes[$this->transportMode]) : $this->transportMode;
	}

	public function getContainerLoad(){
		return isset(static::$containerLoads[$this->containerLoad])? Yii::t(strtolower(__CLASS__), static::$containerLoads[$this->containerLoad]) : $this->containerLoad;
	}

	public function getParties(){
		$rs = CnlParty2order::model()->findAll('order_id = :id', [':id' => $this->id]);
		$ds = [];
		foreach($rs as $r){
			$ds[$r->getType()] = $r->party;
		}
		return $ds;
	}

	private function _mapAttrVal($attr){
		$map = ['status' => 'states'];
		$v = in_array($attr, $map)? $map[$attr] : $attr.'s';
		if(!isset(self::$$v)) return false;
		foreach(self::$$v as $k => $v){
			if($v === $this->{$attr}){
				$this->{$attr} = $k;
				return true;
				break;
			}
		}
		return false;
	}

	public function attDocs(){
		$rs = CnlDoc::model()->findAll('status = 40 AND order_id = :oid', [':oid' => $this->id]);
		$dl = [];

		foreach($rs as $r){
			$pi = pathinfo($r->file->name);
			$dl[] = ['type' => $r->getType(), 'fileType' => strtoupper($pi['extension']), 'fileData' => '#CNLDOC:'.$r->id.'#', 'remark' => $r->file->name];
		}

		return json_encode($dl);
	}

	public function msgWarn(){
		if($this->status > 90) return '';
		$ws = [];
		if($this->status < 20 && empty($this->mdata['msg'][131])){
			$ws[] = ['CFM', 'Confirm'];
		}
		
		if(!empty($this->mdata['bk']['etd']) && $this->status < 90 && empty($this->mdata['msg'][136]) && time() >= strtotime($this->mdata['bk']['etd'])){
			$ws[] = ['ATD', 'ATD'];
		}

		if(!empty($this->mdata['bk']['eta']) && $this->status < 90 && empty($this->mdata['msg'][137]) && time() >= strtotime($this->mdata['bk']['eta'])){
			$ws[] = ['ATA', 'ATA'];
		}

		if(!empty($this->mdata['bk']['eta']) && empty($this->mdata['msg'][134]) && time() >= strtotime($this->mdata['bk']['eta'])){
			$ws[] = ['CI', 'Cargo Info'];
		}

		if(!empty($this->mdata['msg'][136]) && empty($this->mdata['msg'][140]) && time() >= strtotime($this->mdata['bk']['atd'])){
			$ws[] = ['PRA', 'Pre-Alert'];
		}

		$h = [];
		foreach($ws as $w){
			$h[] = '<span class="warn" title="'.$w[1].'">'.$w[0].'</span>';
		}
		return implode('', $h);
	}

	public function AwbTracking($txt='')
	{
		if (empty($this->mdata['bk']['bln'])) {
			return '';
		}

		if (AwbTracking::canTrack($this->mdata['bk']['bln'])) {
			return '<a class="jqm_link" href="tracking/awb/' . $this->mdata['bk']['bln'] . '">' . (empty($txt)? $this->mdata['bk']['bln'].' &#8599' : $txt). '</a>';
		} elseif(preg_match('/^(OOLU|HDMU|EGLV|SUDU|COSU|ALAU)([A-Z\d]+)/', $this->awb, $m)) {
			$sfc = function($c){
				$m = ['OOLU' => 'oocl','HDMU' => 'hmm21', 'EGLV' => 'evergreen', 'SUDU' => 'hamburgsudline', 'COSU' => 'cosco', 'ALAU' => 'zim'];
				return isset($m[$c])? $m[$c] : '';
			};
			return '<a href="https://wheremy.com/bill-of-lading-tracking/show-tracking-info/'.$sfc($m[1]).'/'.$this->awb.'" target="_blank">' . (empty($txt)? $this->mdata['bk']['bln'].' &#8599' : $txt) . '</a>';
		} else {
			return $this->mdata['bk']['bln'];
		}
	}

	public function getInvoicesString()
	{
		if (!empty($this->mdata['edi_job'])) {
			$job = EdiJob::model()->findByPk($this->mdata['edi_job']);
		}elseif(!empty($this->mdata['bk']['bln'])){
			$job = EdiJob::model()->find('REGEXP_REPLACE(awb, "[^0-9A-z]+", "") LIKE :bl' , [':bl' => '%'.preg_replace('/[^\d\w]+/', '', $this->mdata['bk']['bln']).'%']);
		}
		if (!empty($job)) {
			return $job->getInvoicesString();
		}
		return '<span class="warn">No Invoice</span>';
	}

	public function beforeValidate(){
		foreach(['cargoType', 'bizType', 'incoterm', 'transportMode', 'containerLoad'] as $k){
			if(is_string($this->{$k})) $this->_mapAttrVal($k);
		}
		$this->refCode = mb_substr($this->refCode, 0, 64);
		return parent::beforeValidate();
	}
	
	public function beforeSave(){
		$this->meta = empty($this->mdata)? '' : json_encode($this->mdata);
		return parent::beforeSave();
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return parent::afterFind();
	}
	
	public function afterSave()
	{
		if (!$this->nolog && !empty($this)) {
			$this->addLog(empty($this->custom_log_note)? [] : ['note' => $this->custom_log_note]);
		}
	}

	public function addLog($note){
		Log::add($this, $this->isNewRecord? 3 : 4, array_merge(['status' => $this->getStatus()], $note));
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'cargoType' => 'Cargo Type',
			'bizType' => 'Biz Type',
			'status' => 'Status',
			'incoterm' => 'Incoterm',
			'orderCode' => 'Order Code',
			'refCode' => 'Ref Code',
			'focOrderCode' => 'Foc Code',
			'couponCode' => 'Coupon',
			'sellerName' => 'Seller',
			'pol' => 'Pol',
			'pod' => 'Pod',
			'targetEtd' => 'Target ETD',
			'targetEta' => 'Target ETA',
			'cargoReadyDate' => 'Ready Date',
			'containerLoad' => 'Container Load',
			'transportMode' => 'Transport',
			'gmtModified' => 'GMT Modified',
			'feature' => 'Feature',
			'remark' => 'Remark',
			'inv_no' => 'Invoice',
			'wmsg' => 'Msg',
			'awb' => 'Bill',
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
	public function search($pgn=true, $ps = 30, $ec = false){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('cargoType',$this->cargoType);
		$criteria->compare('bizType',$this->bizType);
		$criteria->compare('incoterm',$this->incoterm);
		$criteria->compare('orderCode',$this->orderCode,true);
		$criteria->compare('refCode',$this->refCode,true);
		$criteria->compare('focOrderCode',$this->focOrderCode,true);
		$criteria->compare('couponCode',$this->couponCode,true);
		$criteria->compare('sellerName',$this->sellerName,true);
		$criteria->compare('pol',$this->pol,true);
		$criteria->compare('pod',$this->pod,true);
		$criteria->compare('targetEtd',$this->targetEtd,true);
		$criteria->compare('targetEta',$this->targetEta,true);
		$criteria->compare('cargoReadyDate',$this->cargoReadyDate,true);
		$criteria->compare('containerLoad',$this->containerLoad);
		$criteria->compare('transportMode',$this->transportMode);
		$criteria->compare('gmtModified',$this->gmtModified,true);
		$criteria->compare('feature',$this->feature,true);
		$criteria->compare('remark',$this->remark,true);
		// $criteria->compare('meta',$this->meta,true);
		$with = [];

		if(empty($this->status)){
			$criteria->compare('t.status', '<100');
		}else{
			$criteria->compare('t.status', $this->status);
		}

		if(!empty($this->wmsg)){
			$criteria->addCondition("!JSON_CONTAINS(t.`meta`, 1, '$.msg.".$this->wmsg."')");
		}

		if(!empty($this->awb)){
			$criteria->addCondition("JSON_VALUE(t.`meta`, '$.bk.bln') LIKE :awb");
			$criteria->params[':awb'] = '%'.$this->awb.'%';
		}

		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'t.id DESC',
 			),
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CnlOrder the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
