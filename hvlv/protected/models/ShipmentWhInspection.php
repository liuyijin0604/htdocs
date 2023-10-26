<?php

/**
 * This is the model class for table "shipment_wh_inspection".
 *
 * The followings are the available columns in table 'shipment_wh_inspection':
 * @property integer $pid
 * @property integer $process_status
 * @property string $meta
 * @property string $created
 */
class ShipmentWhInspection extends MetaModel
{
	const NEW_STATUS = 10;
	const UPLOAD_STATUS = 20;
	const DONE_STATUS = 100;
	public $hbn,$ref,$consol_no,$status,$photos;

	public static $states = [
		self::NEW_STATUS=>"NEW",
		self::UPLOAD_STATUS=>"UPLOADED",
		self::DONE_STATUS=>"DONE",

	];
	public $scan_time = "";
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'shipment_wh_inspection';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('process_status', 'required'),
			array('process_status', 'numerical', 'integerOnly'=>true),
			array('meta', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, process_status, meta, created,scan_time,hbn,ref,consol_no,status,photos,isCombine,upload_time', 'safe', 'on'=>'search'),
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
			'consol' => [self::BELONGS_TO, 'Consol', 'consol_id'],
			'relations' => [self::HAS_MANY, 'ShipmentWhInspectionRelations', 'inspection_id'],
			'courier' => [self::BELONGS_TO, 'Org', 'courier_id'],
		);
	}

	public function getStatus()
	{
		return isset(static::$states[$this->process_status])? Yii::t(strtolower(__CLASS__), static::$states[$this->process_status]) : $this->process_status;
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'id',
			'process_status' => 'Process Status',
			'meta' => 'Meta',
			'created' => 'Created',
			'scan_time' => 'Scan Time',
			'hbn'=>'HBN',
			'ref'=>'REF',
			'consol_no'=>'Consol No./AWB',
			'status'=>'Status',
			'upload_time'=>'Upload Time'
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
	public function search($pgn=true, $ps = 30, $ec = false, $defaultOrder=true)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		if(!empty($this->process_status))
		{
			$criteria->compare('t.process_status',$this->process_status);
		}else
		{
			$criteria->compare('t.process_status',[ShipmentWhInspection::NEW_STATUS,ShipmentWhInspection::UPLOAD_STATUS]);
		}
		$criteria->compare('t.dpt_id',$this->dpt_id);
		$criteria->compare('t.meta',$this->meta,true);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.upload_time',$this->upload_time,true);
		$with = ['relations'];

		$criteria->compare('relations.scan_time',$this->scan_time,true);
		if(!empty($this->hbn)||!empty($this->ref)||!empty($this->consol_no)||!empty($this->status))
		{
			$with[] = 'relations.shipment';
		}

		if(!empty($this->hbn))
		{
			$criteria->compare('shipment.hbn',$this->hbn);
		}

		if(!empty($this->ref))
		{
			$criteria->compare('shipment.ref',$this->ref);
		}

		if(!empty($this->status))
		{
			$criteria->compare('shipment.status',$this->status);
		}

		if(!empty($this->consol_no))
		{
			$with[] = 'consol';
			$criteria->addCondition('consol.no = "'.$this->consol_no.'" or consol.awb = "'.$this->consol_no.'" or  consol.meta like "%'.$this->consol_no.'%"');
		}
		$criteria->with = $with;
		$pagerparams = $_GET;
		$sort = new CSort(get_called_class());
		if ($defaultOrder) {
			$sort->defaultOrder = 't.process_status ASC,t.consol_id, t.created ASC';
		} else {
			$sort->defaultOrder = 't.process_status ASC,t.consol_id, t.created ASC';
		}

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
			'sort'=>$sort,
			'pagination'=> $pgn? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ShipmentWhInspection the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
