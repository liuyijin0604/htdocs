<?php

/**
 * This is the model class for table "shipment_rts_record".
 *
 * The followings are the available columns in table 'shipment_rts_record':
 * @property integer $id
 * @property integer $shipment_id
 * @property string $barcode
 * @property string $record_time
 * @property integer $type
 * @property integer $sn
 */
class ShipmentRtsRecord extends CActiveRecord
{
	const WRONG_RTS = 2;
	const KNOWN = 1;
	const UNKNOWN = 0;
	const NEW = 10;
	const UNKNOWN_NEED_PRINT = 50;
	const PRINTED_UNKNOWN = 60;
	const WAITING_UNKNOWN_DISCARD = 75;
	const UNKNOWN_DISCARD_DONE = 80;
	const RTS_DONE = 90;
	const KNOWN_DISCARD_DONE = 95;
	const WAITING_KNOWN_DISCARD = 78;
	const RTS_WAITING_RESEND = 85;
	const RTC_WAITING = 83;
	const RTC_DONE = 84;
	const RTS_TYPE_NORMAL = 0;
	const RTS_TYPE_WEONG_COURIER = 1;

	public static $states = [
		10 => 'NEW',
		50 => 'UnKnown RTS Need Print',
		60 => 'Printed UnKnown',
		75 => 'UnKnown RTS Waiting Discard',
		78 => 'Known RTS Waiting Discard',
		80 => 'UnKnown RTS Discard Done',
		83 => 'RTC Waiting',
		84 => 'RTC Done',
		85 => 'Known RTS Waiting Resend',
		90 => 'RTS Done',
		95 => 'Known RTS Discard',
		100 => 'Cancelled'
	];

	public static $ims_states = [
		10=>'RTS Received',
		90=>'RTS Done'
	];

	public static $opProcessType = [
		self::UNKNOWN=>'UnKnown RTS',
		self::KNOWN=>'RTS/RTC List',
		self::RTC_WAITING=>'RTC Record',
	];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shipment_rts_record';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('barcode', 'required'),
			array('shipment_id, type, sn', 'numerical', 'integerOnly'=>true),
			array('barcode', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, shipment_id, barcode, record_time, type, sn, status, location_id, warehouse_id', 'safe', 'on'=>'search'),
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
			'shipment' => [self::BELONGS_TO, 'Shipment', 'shipment_id'],
			'location' => [self::BELONGS_TO, 'WmsLocation', 'location_id'],
			'warehouse' => [self::BELONGS_TO, 'Org', 'warehouse_id'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'shipment_id' => 'Shipment',
			'barcode' => 'Barcode',
			'record_time' => 'RTS Record Time',
			'type' => 'Type',
			'sn' => 'Sn',
			'location_id' => 'Location'
		);
	}

	public function getUrl()
	{
		if($this->type==self::UNKNOWN)
		{
			$fileRepos = FileRepo::model()->findAll(["condition"=>"fid =:fid and type = :type","params"=>[":fid"=>$this->id,":type"=>Filerepo::UNKNOWN_RTS],'order'=>'id desc','limit'=>'1']);
		}else
		{
			$fileRepos = FileRepo::model()->findAll(["condition"=>"fid =:fid and type = :type","params"=>[":fid"=>$this->shipment_id,":type"=>Filerepo::KNOWN_RTS],'order'=>'id desc','limit'=>'1']);
		}
		$url = "";
		foreach ($fileRepos as $key => $fileRepo) {
			$url.="<a href=\"{$fileRepo->getOutUrl()}\"  target= \"_blank\">{$fileRepo->name}</a>";
			break;
		}
		return $url;
	}

	public function getBarcode()
	{
		if(!empty($this->adjust_shipment_id))
		{
			$shipment = Shipment::model()->findByPk($this->adjust_shipment_id);
			return $this->barcode."("."<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $this->adjust_shipment_id))."\" class=\"tab_link\" title=\"".$shipment->ref."\">".$shipment->ref.'-'.$this->adjust_sn."</a>".")";
		}
		return $this->barcode;
	}

	public function beforeSave()
	{
		// $thisCopy = self::model()->findByPk($this->id);
		// if($thisCopy->is_print==0&&$this->is_print==1)
		// {
		// 	$this->log("print unknown label");
		// }
		return true;
	}

	public function afterSave()
	{
		if (!empty($this)) {
			Log::add($this, $this->isNewRecord? 3 : 4, array_merge(['status' => $this->getStatus()],[]));
		}
		return true;
	}

	public function getStatus()
	{
		$status = isset(static::$states[$this->status]) ? Yii::t(strtolower(__CLASS__), static::$states[$this->status]) : $this->status;
		return $status;
	}


	public function log($action)
	{
		$extra =["action"=>$action];
		Log::add($this, $this->isNewRecord? 3 : 4, array_merge(['status' => $this->getStatus()], $extra));
	}

	public function getShipmentCourier()
	{
		foreach($this->shipment->trans as $ts){
			return explode(":",$ts->infoLink())[0];
		}
	}

	public function getScanUser()
	{
		$ss = ShipmentScan::model()->find("pid = :pid and pno = :pno",[":pid"=>$this->shipment_id,":pno"=>$this->sn]);
		if(!empty($ss))
		{
			return $ss->user->fname." ".$ss->user->lname;
		}
		return "";
	}

	public function getScanTime()
	{
		$ss = ShipmentScan::model()->find("pid = :pid and pno = :pno",[":pid"=>$this->shipment_id,":pno"=>$this->sn]);
		if(!empty($ss))
		{
			return $ss->scan_time;
		}
		return "";
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
	public function search($pgn = true, $ps = 30, $ec = false, $defaultOrder = true)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.barcode',$this->barcode,true);
		$criteria->compare('t.record_time',$this->record_time,true);
		$criteria->compare('t.type',$this->type);
		$criteria->compare('t.sn',$this->sn);
		$criteria->compare('t.warehouse_id',$this->warehouse_id);
		$criteria->compare('t.status',$this->status);

		$with = [];

		if(!empty($this->shipment_id))
		{
			$with = ['shipment'];
			$criteria->compare('shipment.ref',$this->shipment_id);
		}

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		$sort = new CSort(get_called_class());

		if ($defaultOrder) {
			$sort->defaultOrder = 't.record_time desc';
		} else {
			$sort->defaultOrder = 't.record_time desc';
		}

		// in case sub-gridview, we need consol_id set by parent grid view
		$pagerparams = $_GET;

		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
			'sort' => $sort,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		]);

	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentRtsRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
