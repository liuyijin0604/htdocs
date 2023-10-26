<?php

/**
 * This is the model class for table "org_rate".
 *
 * The followings are the available columns in table 'org_rate':
 * @property string $id
 * @property string $org_id
 * @property string $name
 * @property string $code
 * @property integer $type
 * @property string $vfrom
 * @property string $vto
 * @property integer $zone_id
 * @property string $currency
 * @property string $meta
 * @property string $updated
 */
class OrgRate extends CActiveRecord
{

	// in the org_rate table there is a field called : zone_id
	// because one Org maybe has multiple zone map rules
	// so we use zone_id to differentiate the cost zone
	// and we use zone_id = 0 to set our price zone for our client
	public static $types = array(
		5 => 'Cost Rate',  // all price rate which client gives to PCAE
		10 => 'Parcel Rate',
		20 => 'Satchel Rate',
		30 => 'Sell Rate',
		40 => 'PCAE Rate',
		50 => 'PCAE eParcel',
		60 => 'PCAE BPA',
		70 => 'PCAE Letters',
		80 => 'PCAE Sell Zone Rate', // PCAE sell delivery rate based on specified zone
		55 => 'Cost Rate Test',
		90 => 'CBM Cost Rate'// CBM cost Rate for austway
	);

	// define service  type
	public static $PCAE_SERVICE_TYPE = array(
		'CN' => '中国操作', // service in china
		'HK' => '港本地',  // service in Hongkong
		'FR' => '空运',      // service for freight
		'AU' => '澳本地',  // service in Australia
		'DR' => '澳洲派送'    // service for delivery in Australia
	);

	//
	public static $ReconcileRates = array(
		'FWSYD2020' => 'Fastway Syd 2020', // service in china
		'FWSYD2022' => 'Fastway Syd 2022',
		'FWSYD' => 'Fastway Syd',
		'TNT2020'=>'TNT2020',
		'STARTRACK'=>'STARTRACK'
	);

	public static $TemplateTypes = array(
		'FWSYD2022' => 'Fastway Syd 2022',
		// 'FWSYD2020' => 'Fastway Syd 2020',
		// 'FWSYDOLD' => 'Fastway Syd Old',
		// 'TNT2020'=>'TNT2020',
		'STARTRACK'=>'STARTRACK',
		// 'AUSPOST' => 'AUSPOST',
		'AUSPOST-item' => 'AUSPOST-item',
		// 'Eiz-toll' => 'Eiz-toll',
		'UBI-toll' => 'UBI-toll',
		'UBI-AUPOST' => 'UBI-AUPOST',
		// 'my-toll' => 'My toll',
		'FL-hunter' => 'FL-hunter',
		'my-toll-CSV' => 'My toll CSV',
		'my-border-CSV' => 'My border CSV',
		'UBI-toll-surcharge' => 'UBI-toll-surcharge',
		'TLD-supplier' => 'TLD-supplier',
		'GV-Aupost' => 'GV-Aupost',
		'Aupost-Weight-Check' => 'Aupost-Weight-Check',
		'Allied' => 'Allied',
		'OTHER' => 'OTHER'
	);
	public static $BrokerTemplateTypes = array(
		'Master' =>'Master',
		'Autumn' =>'Autumn',
		'FYN' => 'FYN'

	);
	public static $ManualTemplateTypes = array(
		'consol_manual' => 'consol_manual',
		'consol_manual_multi' => 'consol_manual_multi',
		'consol_manual_weight' => 'consol_manual_weight',
		'consol_manual_brownways' => 'consol_manual_brownways'

	);
	public static $TerminalTemplateTypes = array(
		'Qantas' => 'Qantas',
		'Menzies' => 'Menzies',
		'AMI' => 'AMI',
		'Dnata' => 'Dnata'

	);
	public static $TemplateTypesOrg = array(
		'FWSYD2020' => 115,
		'FWSYD2022' => 115,
		'FWSYDOLD' => 115,
		'TNT2020' => 3466,
		'STARTRACK' => 858,
		'AUSPOST' => 101,
		'AUSPOST-item' => 101,
		'Aupost-Weight-Check'=>101,
		'Menzies' => 939,
		'Qantas' => 955,
		'Dnata' => 977,
		'AMI' => 1363,
		'Eiz-toll' => 3701,
		'UBI-toll' => 3079,
		'UBI-AUPOST' => 3079,
		'my-toll' => 3752,
		'my-toll-CSV' => 3752,
		'my-border-CSV' => 3447,
		'UBI-toll-surcharge' => 3079,
		'FL-hunter' =>Org::ORGID_COURIER_FL,
		'TLD-supplier'=>999999999,
		'GV-Aupost'=>3937,
		'Allied'=>Org::ORGID_COURIER_ALLIED_TOP,
		'OTHER' => 999999999
	);

	public $mdata = [];
	public $nolog = false;
	public $custom_log_note = '';
	public $priority = 0;
	const COSTRATETYPE = 5;
	const COSTRATETESTTYPE = 55;
	const CBM_COST_RATE = 90;
	const B2C_TYPE = 2;
	const B2B_TYPE = 1;
	public static function getServiceTypes(){
		return self::$PCAE_SERVICE_TYPE;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'org_rate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, name, code, type, vfrom, currency, updated', 'required'),
			array('vto, currency, meta', 'safe'),
			array('type,zone_id', 'numerical', 'integerOnly'=>true),
			array('org_id', 'length', 'max'=>11),
			array('name', 'length', 'max'=>50),
			array('code', 'length', 'max'=>20),
			array('vfrom, vto', 'default', 'setOnEmpty' => true, 'value' => null),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, org_id, zone_id,name, code, type, vfrom, vto, currency, meta, updated', 'safe', 'on'=>'search'),
		);
	}

	public function isCurrent(){
		return empty($this->vto) || strtotime($this->vto) > time();
	}

	public function getStatus()
	{
		"";
	}
	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'org' => array(self::BELONGS_TO, 'Org', 'org_id'),
			'zoneMapTest' => array(self::HAS_MANY, 'ZoneMapTest', 'rate_id'),
			'zoneRate' => array(self::HAS_MANY, 'ZoneRate', 'rate_id'),
			'surcharge' => [self::HAS_MANY, 'ImportsFlexibleSurcharge', 'rate_id','on'=>'status!=100 and start<=NOW() and end>=NOW()'],
			'courier_surcharge' => [self::HAS_MANY, 'ImportsFlexibleSurcharge', 'rate_id','on'=>'status!=100 and type!="fuel_surcharge" and courier_type="org_rate"'],
		);
	}
	
	public function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		return parent::beforeSave();
	}
	
	public function afterSave(){
		if(!$this->nolog && !empty($this)){
			$extra = empty($this->custom_log_note)? array() : array('note' => $this->custom_log_note);
			Log::add($this, $this->isNewRecord? 3 : 4, $extra);
		}
		return parent::afterSave();
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return parent::afterFind();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'org_id' => 'Org',
			'zone_id' => 'Zone',
			'name' => 'Name',
			'code' => 'Code',
			'type' => 'Type',
			'vfrom' => 'Valid From',
			'vto' => 'Valid To',
			'currency' => 'Currency',
			'meta' => 'Meta',
			'updated' => 'Updated',
		);
	}

	public function copyZonesFrom($oid){
		$rs = ZoneRate::model()->findAll('rate_id = :id', [':id' => $oid]);
		foreach($rs as $r){
			$r->id = null;
			$r->isNewRecord = true;
			$r->rate_id = $this->id;
			$r->save();
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
	public function search(){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('org_id',$this->org_id,true);
		$criteria->compare('zone_id',$this->zone_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('vfrom',$this->vfrom,true);
		$criteria->compare('vto',$this->vto,true);
		$criteria->compare('currency',$this->currency,true);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('updated',$this->updated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


	/**
	 * get org zone rate , if not existing create a new one
	 * @param $orgId
	 * @return OrgRate|static
	 */
	public static function getOrgZoneRate($orgId){
		$zoneRate = OrgRate::model()->find('org_id = :oid AND type = 80',array( ':oid' => $orgId));
		// if not existing create a new one
		if ( empty($zoneRate) ) {
			$zoneRate = new OrgRate();
			$zoneRate->org_id = $orgId;
			$zoneRate->name = 'Sell Zone Rate';
			$zoneRate->code = 'sell_zone_rate';
			$zoneRate->type = 80;
			$zoneRate->vfrom = date('Y-m-d');
			$zoneRate->vto = '0000-00-00'; // currently we only support one rate based on time span
			$zoneRate->currency = 1 ; // default as AUD
			$zoneRate->meta = 'sell_zone_rate';
			$zoneRate->updated = date('Y-m-d h:i:s');
			$zoneRate->save();
		}
		return $zoneRate;
	}

	/**
	 * get org cost zone rate , if not existing create a new one
	 * @param $orgId
	 * @return OrgRate|static
	 */
	public static function getOrgCostZoneRate($orgId){
		$zoneRate = OrgRate::model()->find('org_id = :oid AND type = 5',array( ':oid' => $orgId));
		// if not existing create a new one
		if ( empty($zoneRate) ) {
			$zoneRate = new OrgRate();
			$zoneRate->org_id = $orgId;
			$zoneRate->name = 'ORG cost zone rate';
			$zoneRate->code = 'org_cost_zone_rate';
			$zoneRate->type = 5;
			$zoneRate->vfrom = date('Y-m-d');
			$zoneRate->vto = '0000-00-00'; // currently we only support one rate based on time span
			$zoneRate->currency = 1 ; // default as AUD
			$zoneRate->meta = 'org_cost_zone_rate';
			$zoneRate->updated = date('Y-m-d h:i:s');
			$zoneRate->save();
		}
		return $zoneRate;
	}

	public function isOrgRateMyToll()
	{
		if($this->org_id == Org::ORGID_COURIER_TOLL_IPEC)
		{
			return true;
		}
		return false;
	}

	public function isOrgRateUbiToll()
	{
		if($this->org_id == Org::ORGID_COURIER_UBI&&preg_match('/TOLL/i', $this->code))
		{
			return true;
		}
		return false;
	}


	/**
	 * get customer's applied service plans
	 * we save all selected service price setting in OrgRate table with meta field
	 * @param $orgId
	 */
	public function getCustomerServicePlans($orgId){

		$findServices = OrgRate::model()->find('org_id = :oid AND type = 40',array( ':oid' => $orgId));
		if ( empty($findServices) || empty($findServices->meta) ) {
			// not setting yet, we use the default values
			$realDefServices =  array(
				'CN' => array(    // operation service in China
					'selected' => 0,  // selected or not
					'rate' => 0  // price rate by kg
				),
				'HK' => array( // operation service in HongKong
					'selected' => 0,
					'rate' => 0
				),
				'FR' => array(   // operation service for Freight
					'selected' => 0,
					'rate' => 0
				),
				'AU' => array(  // operation service in Australia
					'selected' => 0,
					'rate' => 0,
					'showdetails' => 0,
					'doc_rate' => 0, // document fee
					'thc_rate' => 0, // terminal handling charge
					'dlv_rate' => 0, // Delivery charge
					'sac_rate' => 0, // Self assistant clear
					'sac_prate' => 0, // Self assistant clear
					'str_rate' => 0, // storage fee
					'str_terms' => 0, // storage free terms
					'str_oth_terms' => 0, // storage free terms for not our warehouse
					'rts_rate' => 0, // for RTS fee
					'rts_moderate' => 0, // for RTS Mod fee
					'prate' => 0  //  price rate by piece
				),
				'DR' => array( // delivery service in Australia
					'selected' => 0,
					'eparcel_extra_rate' => 0,
					'bpa_extra_rate' => 0,
					'letters_extra_rate' => 0,
					'st_rate' => 0,
					'st_prate' => 0,
					'ft_rate' => 0,
					'ft_prate' => 0,
					'tracking_mode' => 2, // default cost schedule 3
					'dl_mode' => 1 // default as eParcel
				),
			);
			$services = array(
				'id' => 0,
				'currency' => 1,
				'vfrom' => date('Y-m-d'),
				'vto' => '',
				'services' => json_decode(json_encode($realDefServices))
			);
		} else {
			$services = array (
				'currency' => $findServices->currency,
				'vfrom' => $findServices->vfrom,
				'vto' => $findServices->vto,
				'id' => $findServices->id,
				'services' => json_decode($findServices->meta)
			);
		}

		return $services;
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrgRate the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
