<?php

/**
 * This is the model class for table "imports_unknown_shipment".
 *
 * The followings are the available columns in table 'imports_unknown_shipment':
 * @property integer $id
 * @property string $barcode
 * @property string $temp_barcode
 * @property integer $user_id
 * @property string $create_time
 */
class ImportsUnknownShipment extends CActiveRecord
{
	const SENT =1;
	const APPROVED = 2;
	const NORMAL = 0;
	const SURPLUS = 1;
	const NEWSTATUS = 1;
	const CANCELLED = 100;
	const PROCESSDONE = 80;
	const PROCESSING = 60;
	public $service,$shipmentStatus,$orgId,$nolog,$agentId;
	public static $states=[
		1=>'New',
		60=>'Processing',
		80=>'Process done',
		100=>'Cancelled',
	];
	public static $sentStatus=[
		1=>'Y',
		
		2=>'Y[D]',
		0=>''
	];

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'imports_unknown_shipment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, create_time', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('barcode, temp_barcode', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, barcode, temp_barcode, user_id, create_time,status,container_no,ground_label,service,shipmentStatus,dpt_id,exist_shipment_id,sent_surplus_outturn,sent_unpacking_outturn', 'safe', 'on'=>'search'),
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
			'shipment' => [self::BELONGS_TO, 'ImParcel', 'shipment_id'],
			'consol'=>[self::BELONGS_TO,'Consol','consol_id'],
			'depot' => [self::BELONGS_TO, 'Org', 'dpt_id'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'barcode' => 'Barcode',
			'temp_barcode' => 'Temp Barcode',
			'user_id' => 'User',
			'create_time' => 'TLA List Date',
			'client_upload_time' => 'Client uploaded Date',
			'customer_comment' => 'Customer Notes',
			'comment' => 'TLA Notes',
			'sent_surplus_outturn' => 'Sent Surplus Outturn',
			'sent_unpacking_outturn' => 'Sent Unpacking Outturn',
			'exist_shipment_id'=>'Not Belong'

		);
	}

	public function getStatus()
	{
		return isset(self::$states[$this->status]) ? Yii::t(strtolower(__CLASS__), self::$states[$this->status]) : $this->status;
	}

	public function generateTempBarcode()
	{
		if(empty($this->temp_barcode))
		{
			if(!empty($this->container_no))
			{
				$this->temp_barcode=$this->container_no."-UN".$this->id;
			}else
			{
				$this->temp_barcode=date("Ymd")."-UN".$this->id;
			}
		}
	}

	public function getConsolUnknownBarcodes()
	{
		return ImportsUnknownShipment::model()->count("container_no = :containerNo and shipment_id = 0",[":containerNo"=>$this->container_no]);
	}
	public function getBarcodes($html = false)
	{
		$arr = ImportsUnknownShipment::model()->findAll("container_no = :containerNo",[":containerNo"=>$this->container_no]);
		if($html)
		{
			return join("</br>",array_column($arr,"barcode"));
		}else
		{
			return join(",",array_column($arr,"barcode"));
		}
	}

	public function getClosedUnknownBarcodes()
	{
		return ImportsUnknownShipment::model()->count("container_no = :containerNo and status = :status",[":containerNo"=>$this->container_no,":status"=>self::PROCESSDONE]);
	}

	public function getEmptyConsolShipment()
	{
		return ImportsUnknownShipment::model()->count(["condition"=>"container_no = :containerNo and shipment_id != 0","params"=>[":containerNo"=>$this->container_no]]);
	}

	public function getConsolUnknownShipments()
	{
		return ImportsUnknownShipment::model()->findAll("container_no = :containerNo and shipment_id = 0",[":containerNo"=>$this->container_no]);
	}

	public function getConsolNotBelongShipments()
	{
		return ImportsUnknownShipment::model()->with(['shipment','shipment.consol'])->count("t.container_no = :containerNo and shipment_id != 0 and consol.awb !=t.container_no",[":containerNo"=>$this->container_no]);
	}


	public function beforeSave()
	{
		//add log
		if (!empty($this)&&!$this->nolog) {
			$extra=[];
			$action="";
			$oldRecord = ImportsUnknownShipment::model()->findByPk($this->id);
			if(!empty($this->sent_surplus_outturn)&&$this->sent_surplus_outturn!=$oldRecord->sent_surplus_outturn)
			{
				$extra["action"] = "Send Surplus Outturn-".self::$sentStatus[$this->sent_surplus_outturn];
			}

			if(!empty($this->sent_unpacking_outturn)&&$this->sent_unpacking_outturn!=$oldRecord->sent_unpacking_outturn)
			{
				$extra["action"] = "Send Unpacking Outturn-".self::$sentStatus[$this->sent_unpacking_outturn];
			}

			TlaLog::add($this, $this->isNewRecord? 3 : 4, array_merge(['status' => $this->getStatus()], $extra));
		}
		return 	parent::beforeSave();
	}

	public function log($action)
	{
		$extra =["action"=>$action];
		TlaLog::add($this, $this->isNewRecord? 3 : 4, array_merge(['status' => $this->getStatus()], $extra));
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
	public function search($pgn = true, $ps = 30,$orderBy="")
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.barcode',$this->barcode,true);
		$criteria->compare('t.temp_barcode',$this->temp_barcode,true);
		$criteria->compare('t.user_id',$this->user_id);
		$criteria->compare('t.create_time',$this->create_time,true);
		$criteria->compare('t.consol_id',$this->consol_id);
		$criteria->compare('t.container_no',$this->container_no);
		$criteria->compare('t.ground_label',$this->ground_label);
		$criteria->compare('t.sent_unpacking_outturn',$this->sent_unpacking_outturn);
		$criteria->compare('t.sent_surplus_outturn',$this->sent_surplus_outturn);
		if(!empty($this->dpt_id)&&$this->dpt_id>0)
		{
			$criteria->compare('t.dpt_id',$this->dpt_id);
		}
		if(!empty($this->exist_shipment_id))
		{
			if($this->exist_shipment_id=="<1")
			{
				$criteria->addCondition("t.exist_shipment_id = 0");
			}else
			{
				$criteria->addCondition("t.exist_shipment_id != 0");
			}
		}
		
		$with = [];
		if(empty($this->type))
		{
			$criteria->addCondition("t.type = 0");
		}else
		{
			$criteria->compare('t.type',$this->type);
		}

		if(!empty($this->service))
		{
			$with[] = "consol";
			$criteria->compare('consol.service',$this->service);
		}

		if(!empty($this->shipmentStatus))
		{
			$with[] = "shipment";
			$criteria->compare('shipment.status',$this->shipmentStatus);
		}

		if(!empty($this->orgId))
		{
			$sql = 'select distinct(consol_id) from shipment where consol_id in (select distinct(consol_id) as consol_id from imports_unknown_shipment where consol_id!=0 and type=1) and agent_id='.$this->orgId;
			$consolArr = Yii::app()->getDb()->createCommand($sql)->queryAll();
			$consolArr[]=['consol_id'=>-1];
			$criteria->compare('consol_id',array_column($consolArr,'consol_id'));
		}

		if(!empty($this->agentId))
		{
			if($this->agentId==Org::ORGID_CLIENT_DAIPOST)
			{
				$criteria->addCondition('barcode like "0039%" or barcode like "M%" or barcode like "33Y%" or barcode like "0019%"');
			}elseif($this->agentId==-1)
			{
				$criteria->addCondition('barcode not like "0039%" and barcode not like "M%" and barcode not like "33Y%" and barcode not like "0019%"');
			}
		}


		if(empty($this->status))
		{
			$criteria->addCondition('t.status !='.self::CANCELLED .' and t.status !='.self::PROCESSDONE);
		}else
		{
			$criteria->compare('t.status',$this->status);
		}

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		$pagerparams = $_GET;

		$sort = new CSort(get_called_class());
		$sort->attributes = [
			'*',
		];
		$sort->defaultOrder = 't.shipment_id DESC';
		if(!empty($orderBy))
		{
			$sort->defaultOrder = $orderBy.' DESC';
		}

		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
			'sort' => $sort,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		]);
	}

	public function getUnpackingStatus()
	{
		if($this->sent_unpacking_outturn!=self::APPROVED)
		{
			return ImportsUnknownShipment::$sentStatus[$this->sent_unpacking_outturn];
		}
		elseif(!empty($this->shipment)&&!empty($this->shipment->mdata['aco']&&$this->sent_unpacking_outturn==self::APPROVED))
		{
			return ImportsUnknownShipment::$sentStatus[$this->sent_unpacking_outturn];
		}else
		{
			return 'Y';
		}
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ImportsUnknownShipment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
