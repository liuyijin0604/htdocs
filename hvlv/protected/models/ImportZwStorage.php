<?php

/**
 * This is the model class for table "import_zw_storage".
 *
 * The followings are the available columns in table 'import_zw_storage':
 * @property integer $id
 * @property string $putcode
 * @property string $tlano
 * @property string $memberid
 * @property string $store_code
 * @property string $arrival_city
 * @property integer $transport_type
 * @property string $channel_code
 * @property string $volume
 * @property string $weight
 * @property string $arrival_date
 * @property string $created
 * @property string $updated
 * @property integer $org_id
 * @property integer $client_id
 * @property string $cutday
 * @property integer $goods_count
 * @property string $remark
 * @property string $meta
 * @property integer $status
 */
class ImportZwStorage extends MetaModel
{
	const STATE_NEW = 0;
	const STATE_PUSHED = 1;
	const STATE_MANUAL = 2;
	const STATE_RECEIVED = 20;
	const STATE_SHIPPING = 30;
	const STATE_CANCEL = 99;
	const STATE_CANCEL_NOTICED = 100;
	public $hbns = '';
	public $consols = '';
	public $org_name = '';
	public $eitems = "";

	public static $storeCode = array(
		"SYD"=>'Sydney',
		"MEL"=>'Melbourne'
	);
	public static $memberid = 10006;

	public static $channelCode = array(
		100=>'AC21'
		// 11=>'AU-AIR-MY01',
		// 12=>'AU-AIR-MY10',
		// 0=>'AU-SEA-MY12',
		// 13=>'AU-AIR-MY12K',  
		// 14=>'AU-SEA-MY12H'
	);
	public static $transportType = array(
		100=>'AC21'
		// 1=>'AIR-电-22KG/0.1CBM以上',
		// 11=>'AIR-没电-22KG/0.1CBM以上',
		// 12=>'AIR-22KG/0.1CBM以下',
		// 0=>'SEA',
		// 13=>'AIR-1KG 以上',
		// 14=>'SEA-1KG 以上'
	);

	public static $transportTypeShows = array(
		// 13=>'AIR-1KG 以上',
		// 14=>'SEA-1KG 以上'
		100=>'散货拼箱到港'
	);

	public static $transportTypeShowsLabel = array(
		// 13=>'AIR-1KG 以上',
		// 14=>'SEA-1KG 以上'
		"AC21"=>'散货拼箱到港'
	);

	public static $transportTypeRe = array(
		100=>0
		// 1=>1,
		// 11=>1,
		// 12=>1,
		// 0=>0,
		// 13=>1,
		// 14=>0
	);

	public static $states = [
		0=>'New',
		1=>'Pushed',
		2=>'Manual',
		20=>'Received',
		30=>'Shipping',
		99=>'Cancel',
		100=>'Cancel_Noticed'
	];
	public static $outStatesArr = [
		'cancel'
	];
	public static $outStates = [
		'New'=>0,
		'Pushed'=>1,
		'Received'=>20,
		'Shipping'=>30,
		'Cancel'=>99,
		'Cancel_Noticed'=>100
	];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'import_zw_storage';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tlano, memberid, store_code, arrival_city, transport_type, channel_code, volume, weight, created, updated, org_id, client_id, goods_count, status', 'required'),
			array('transport_type, org_id, client_id, goods_count, status', 'numerical', 'integerOnly'=>true),
			array('putcode, tlano, memberid, store_code, arrival_city, channel_code', 'length', 'max'=>45),
			array('volume, weight', 'length', 'max'=>10),
			array('remark', 'length', 'max'=>255),
			array('arrival_date, cutday', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, putcode, tlano, memberid, store_code, arrival_city, transport_type, channel_code, volume, weight, arrival_date, created, updated, org_id, client_id, cutday, goods_count, remark, status,org_name,hbns,consols', 'safe', 'on'=>'search'),
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
			'org' => [self::BELONGS_TO, 'Org', 'org_id'],
			'relations' => [self::HAS_MANY, 'ImportZwStorageRelation', 'parent_id'],
			'log' => [self::HAS_MANY, 'ImportZwStorageLog', 'lid'],
		);
	}

	public function beforeSave()
	{
		if (!empty($this->eitems)) {
			$this->items = json_encode($this->eitems);
		}
			$oldObj=ImportZwStorage::model()->findByPk($this->id);
			$extra =[];
			if(!$this->isNewRecord)
			{
				if($oldObj->putcode!=$this->putcode)
				{
					$extra['putcode'] = $this->putcode;
					$extra['oputcode'] = $oldObj->putcode;
				}
				if($oldObj->cutday!=$this->cutday)
				{
					$extra['cutday'] = $this->cutday;
					$extra['ocutday'] = $oldObj->cutday;
				}
				if($oldObj->volume!=$this->volume)
				{
					$extra['volume'] = $this->volume;
					$extra['ovolume'] = $oldObj->volume;
				}
				if($oldObj->goods_count!=$this->goods_count)
				{
					$extra['goods_count'] = $this->goods_count;
					$extra['ogoods_count'] = $oldObj->goods_count;
				}
				if($oldObj->weight!=$this->weight)
				{
					$extra['weight'] = $this->weight;
					$extra['oweight'] = $oldObj->weight;
				}
				if((empty($oldObj->mdata['shipdate'])&&!empty($this->mdata['shipdate']))||!empty($oldObj->mdata['shipdate'])&&!empty($this->mdata['shipdate'])&&$oldObj->mdata['shipdate']!=$this->mdata['shipdate'])
				{
					$extra['shipdate'] = $this->mdata['shipdate'];
					$extra['oshipdate'] = @$oldObj->mdata['shipdate'];
				}
			}
			ImportZwStorageLog::add($this, $this->isNewRecord? 3 : 4, array_merge(['status' => $this->status], $extra));
			return parent::beforeSave();
	}
	public function afterFind()
	{
		if (!empty($this->items)) {
			$this->eitems = json_decode($this->items, true);
		}
		if(empty($this->mdata['service']))$this->mdata['service']='sea';

		return parent::afterFind();
	}

	public function afterSave()
	{
		if(!empty($this->relations))
		{
			$imcoConsol = $this->relations[0]->imParcel->consol;
			if(empty($imcoConsol))
			{
				return parent::afterSave();
			}
			$org_id = $this->relations[0]->imParcel->agent_id;
			if(!isset($imcoConsol->mdata["org_".$org_id]))
			{
				$imcoConsol->mdata["org_".$org_id] = [];
			}
			if(empty($imcoConsol->mdata["org_".$org_id])||$imcoConsol->mdata["org_".$org_id]["awb_wt"]!=$this->weight)
			{
				$imcoConsol->mdata["org_".$org_id]["awb_wt"] = $this->weight;
				$imcoConsol->mdata["org_".$org_id]["cgb_wt"] = max($this->volume*167,$this->weight);
				$imcoConsol->save();
			}
		}
		return parent::afterSave();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'putcode' => 'Putcode',
			'tlano' => 'tlano',
			'memberid' => 'Memberid',
			'store_code' => 'Store Code',
			'arrival_city' => 'Arrival City',
			'transport_type' => 'Transport Type',
			'channel_code' => 'Channel Code',
			'volume' => 'Volume',
			'weight' => 'Weight',
			'arrival_date' => 'Arrival Date',
			'created' => 'Created',
			'updated' => 'Updated',
			'org_id' => 'Org',
			'client_id' => 'Client',
			'cutday' => 'Cutday',
			'goods_count' => 'packages',
			'remark' => 'Note',
			'status' => 'Status',
			'hbns' => 'Shipments',
			'org_name'=>'Org Name',
			'consols'=>'Consols'
		);
	}

	public static function generateTlaNo()
	{
		return "TLANO".strtotime(date('Y-m-d H:i:s')).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
	}

	public function getConsol($type = false,$isFront = false)
	{
		$consolArr = [];
		if(!empty($this->relations))
		{
			$sql = "SELECT c.id,c.no,c.eta from import_zw_storage_relation t join shipment s on t.shipment_id = s.id join consol c on s.consol_id = c.id where t.parent_id = {$this->id} group by c.id";
			$data = Yii::app()->db->createCommand($sql)->queryAll();


			if(!empty($data))
			{
				$result = "";
				foreach ($data as $key => $c) 
				{
					if($isFront)
					{
						$result .='<scan>'.$c['no'].':'.$c['eta'].'</scan></br>';
					}else
					{
						$result .='<a href="'.Yii::app()->createURL(Consol::getTheConsolType($key)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $c['id']))."\" class=\"tab_link\" title=\"".@$c['no']."\">".@$c['no'].'</a></br>';
					}
				}
				return $result;
			}
			return join(',',$consolArr);
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
	public function search($pgn = true, $ps = 50, $ec = false, $defaultOrder = true)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.putcode',$this->putcode,true);
		$criteria->compare('t.tlano',$this->tlano,true);
		$criteria->compare('t.memberid',$this->memberid,true);
		$criteria->compare('t.store_code',$this->store_code,true);
		$criteria->compare('t.arrival_city',$this->arrival_city,true);
		$criteria->compare('t.transport_type',$this->transport_type);
		$criteria->compare('t.channel_code',$this->channel_code,true);
		$criteria->compare('t.volume',$this->volume,true);
		$criteria->compare('t.weight',$this->weight,true);
		$criteria->compare('t.arrival_date',$this->arrival_date,true);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.updated',$this->updated,true);
		$criteria->compare('t.org_id',$this->org_id);
		$criteria->compare('t.client_id',$this->client_id);
		$criteria->compare('t.cutday',$this->cutday,true);
		$criteria->compare('t.goods_count',$this->goods_count);
		$criteria->compare('t.remark',$this->remark,true);
		$criteria->compare('t.meta',$this->meta,true);
		$criteria->compare('t.status',$this->status);
		if(empty($this->status)||$this->status<self::STATE_CANCEL)
		{
			$criteria->addCondition('t.status<99');
		}
		$criteria->with = ['org'];
		if(!empty($this->org_name))
		{
			$criteria->addCondition('org.name like "%'.$this->org_name.'%"');
		}

		if(!empty($this->hbns))
		{
			$criteria->addCondition('json_value(t.meta,"$.hbns") like "%'.$this->hbns.'%"');
		}

		if(!empty($this->consols))
		{
			$criteria->addCondition('c.no like "%'.$this->consols.'%"');
		}

		$sort = new CSort();
		$sort->defaultOrder ='t.id DESC';
		$pagerparams = $_GET;
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => $sort,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		));
	}
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ImportZwStorage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
