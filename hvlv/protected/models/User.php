<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property integer $type
 * @property integer $dpmt   this is used to record the internal user types, like 1 manager 1&2 Import Manager, 1&32 all manager..
 * @property string $title
 * @property string $fname
 * @property string $lname
 * @property string $user
 * @property string $email
 * @property string $password
 * @property string $phone
 * @property string $fax
 * @property string $mobile
 * @property string $since
 * @property integer $active
 * @property string $meta
 * @property integer $org_id
 * @property integer $by_id
 * @property integer $dpt_id
 *
 * The followings are the available model relations:
 * @property Herdinspection[] $herdinspections
 * @property Log[] $logs
 * @property Skininspection[] $skininspections
 * @property Skininspection[] $skininspections1
 * @property Skininspection[] $skininspections2
 * @property Org $org
 */
class User extends CActiveRecord
{
	const INACTIVE = 0;
	public $group;
	public static $types = array(
		0 => 'System Admin',
		10 => 'Manager',
		30 => 'Import Operator',
		53 => 'Import Internship',
		60 => 'Import Client',
		70 => 'Import Sub Client',
	    200 => 'Import Air',
	    201 => 'Import Sea',
	    202 => 'Import Account Manager',
	    40 => 'AU Warehouse Operator',
	    54 => 'Top Courier Service Internship',
	    56 => 'Top Courier Service Operator',
        230=>'Customs Clearance',
	    240=>'Customer Service',
		72 => 'External Broker',
		100 => 'Driver',
	    210=> 'Finance',
	    220=>'IT',
	    //20 => 'Custom Broker',
		//35 => 'PEP Export',
		//45 => 'Origin Warehouse Operator',
		//50 => 'Import Agent Manager',
		//52 => 'Import Agent Operator',
		//80 => 'Export Agent Manager',
		//82 => 'Export Agent Operator',
		//90 => 'PCA Cainiao',
		//120 => 'Data Entry',
		//130 => 'Import Truck Delivery Agent',
		//131 => 'Amazon Operator',
		//55 => 'Top Courier Service Operator',
	);

	public static $occupations = array(
		0=>'None',
		2 => '清关',
		4 => '清关 AQIS+HV',
		8 => '客户经理',
		16 => 'OP manager',
		32 => '进口 AIR OP后',
		64 => '进口 SEA OP后',
		134217728=>'进口 AIR OP后副',
		268435456=>'进口 SEA OP后副',
		128 => '供应商管理',
		256 => '卡派OP',
		512 => '3PL OP',
		1024 => '3PL 库管',
		2048 => '财务 AP',
		4096 => '财务 AR',
		8192 => '客服',
		16384 => 'IT leader',
		32768 => 'IT',
		65536 => '仓库held、备货',
		131072 => '仓库 扫货',
		262144 => '仓库 出库',
		524288 => 'KPI 管理员',
		2097152=>'仓库 验货',
		4194304=>'仓库 入库',
		536870912=>'仓库 签字',
		1073741824=>'仓库 TodayHeld',
		2147483648=>'UB',
		4294967296=>'SCR',
		8388608=>'进口 AIR OP前主',
		16777216=>'进口 SEA OP前主',
		33554432=>'进口 AIR OP前副',
		67108864=>'进口 SEA OP前副',
		1048576 => 'Not Yet',
		8589934592 => 'Shenzhen OP Manager'

	);

	public static $occupationList = array(
		'General'=>[
			0=>'None',
			8 => '客户经理',
			128 => '供应商管理',
			8192 => '客服',
			16384 => 'IT leader',
			32768 => 'IT',
			524288 => 'KPI 管理员',
			1048576 => 'Not Yet',
			8589934592 => 'Shenzhen OP Manager'
		],
		'Customs'=>[
			2 => '清关',
			4 => '清关 AQIS HV'
		],
		'Consol OP'=>[
			16 => 'OP manager',
			32 => '进口 AIR OP后',
			64 => '进口 SEA OP后',
			134217728=>'进口 AIR OP后副',
			268435456=>'进口 SEA OP后副',
			8388608=>'进口 AIR OP前主',
			16777216=>'进口 SEA OP前主',
			33554432=>'进口 AIR OP前副',
			67108864=>'进口 SEA OP前副',
			2147483648=>'UB',
			4294967296=>'SCR',

		],

		'TLD' =>[
			256 => '卡派OP',
			512 => '3PL OP',
			1024 => '3PL 库管'
		],
		'Accounting'=>[
			2048 => '财务 AP',
			4096 => '财务 AR'
		],
		'Warehouse'=>[
			65536 => '仓库held、备货',
			131072 => '仓库 扫货',
			262144 => '仓库 出库',
			2097152=>'仓库 验货',
			4194304=>'仓库 入库',
			536870912=>'仓库 签字',
			1073741824=>'仓库 TodayHeld'
		]
	);

	public static $consolOpOccupation = array(
		16 => [0,0,0],
		32 => [10,2,1],// 10 means air 2 means last,1 means major
		64 => [20,2,1],
		134217728=>[10,2,2],
		268435456=>[20,2,2],
		8388608=> [10,1,1],
		16777216=>[20,1,1],
		33554432=>[10,1,2],
		67108864=>[20,1,2]
	);

	public static $occupation_tree = array(
		2 => [],
		4 => [],
		8 => [],
		16 => [32,64,134217728,268435456,8388608,16777216,33554432,67108864,2147483648,4294967296],
		32 => [134217728,8388608,33554432],
		64 => [268435456,16777216,67108864],
		128 => [],
		256 => [],
		512 => [],
		1024 => [],
		2048 => [],
		4096 => [],
		8192 => [],
		16384 => [2,4,8,16,32,64,128,256,512,1024,2048,4096,8192,16384,32768,65536,131072,262144,524288,1048576,2097152,4194304,536870912,1073741824,2147483648,4294967296],
		32768 => [],
		65536 => [],
		131072 => [],
		262144 => [],
		2097152 => [],
		4194304 => [],
		134217728 =>[],
		268435456=>[],
		536870912=>[],
		33554432=>[],
		67108864=>[],
		1048576=>[],
		1073741824=>[],
		2147483648=>[],
		4294967296=>[],
		8388608 => [33554432],
		16777216 => [67108864],
		524288 => [2,4,8,16,32,64,128,256,512,1024,2048,4096,8192,16384,32768,65536,131072,262144,524288,1048576,2097152,4194304,536870912,1073741824,2147483648,4294967296],
		8589934592 => [4,8192,2147483648,4294967296]
	);

	Const CUSTOM_CLEARENCE = 2;
	Const CUSTOM_AQIS_HV = 4;
	Const CUSTOMER_MANAGER = 8;
	Const IM_AIR_OP = 32;
	Const IM_SEA_OP = 64;
	Const SUPPLIER_MANAGER = 128;
	Const TLD_OP = 256;
	Const TPL_OP = 512;
	Const TPL_STORAGE = 1024;
	Const ACCOUNTING_AP = 2048;
	Const ACCOUNTING_AR = 4096;
	Const CUSTOMER_SERVICE = 8192;
	Const IT_LEADER = 16384;
	Const IT = 32768;
	Const WH_HELD_PREPARATION = 65536;
	Const WH_CHECK_IN = 131072;
	Const WH_CHECK_OUT = 262144;
	Const WH_INSPECTION = 2097152;
	Const WH_SORTHELD_PUTAWAY = 4194304;
	Const IM_AIR_OP_BACK_2 = 134217728;
	Const IM_SEA_OP_BACK_2 = 268435456;
	
	Const IM_AIR_OP_FRONT_1 = 8388608;
	Const IM_SEA_OP_FRONT_1 = 16777216;
	Const IM_AIR_OP_FRONT_2 = 33554432;
	Const IM_SEA_OP_FRONT_2 = 67108864;
	Const WH_GATEPASS = 536870912;
	Const WH_TODAY_HELD = 1073741824;
	Const CONSOL_UB = 2147483648;
	Const CONSOL_SCR_ACR = 4294967296;

	
	const Type_Export_Agent_Manager = 80;
	const AU_WAREHOUSE_OPERATOR = 40;

	public static $titles = array(
		'Mr' => 'Mr',
		'Ms' => 'Ms',
		'Mrs' => 'Mrs',
		'Miss' => 'Miss',
	);

	public static $dpmts = array(
//            1=>'Manager',
		2 => 'Import',
		4 => 'Export',
		8 => 'Air/Sea',
		16 => '3PL',
		32 => 'Top Courier Service',
		64 => 'IT'
	);

	const IT_DPMT = 64;
	const IMPORT_TRUCK_DELIVERY = 130;
	const EXTERNAL_BROKER_TYPE = 72;
	const DRIVER = 100;
	public $org_search;

	public $extra = array();

	public $dash = array();

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, title, fname, lname, email, active, org_id', 'required'),
			array('phone, fax, mobile, since, user, meta, dashboard, password, by_id, dpt_id', 'safe'),
			array('password', 'required', 'on' => 'register'),
			array('type, occupation, active, org_id', 'numerical', 'integerOnly' => true),
			array('title, email, phone, fax, mobile', 'length', 'max' => 255),
			array('fname, lname', 'length', 'max' => 40),
			array('password', 'length', 'max' => 32),
			array('email, user', 'unique'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, occupation, title, fname, lname, email, password,dpmt,phone, fax, mobile, since, active, org_id, by_id, org_search, dpt_id, abf', 'safe', 'on' => 'search'),
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
			'logs' => array(self::HAS_MANY, 'Log', 'user_id'),
			'org' => array(self::BELONGS_TO, 'Org', 'org_id'),
			'by' => array(self::BELONGS_TO, 'Org', 'by_id'),
			'branch' => array(self::BELONGS_TO, 'Org', 'dpt_id'),
			'files' => array(self::HAS_MANY, 'FileRepo', 'fid'),
			'abf' => array(self::HAS_MANY, 'FileRepo', 'fid', 'on' => 'abf.type = 220 or abf.type = 221'),
			'userWarehouseGroup' => array(self::HAS_ONE, 'UserWarehouseGroup', 'user_id'),
			'actionLogs' => array(self::HAS_MANY, 'Log', 'lid', 'on' => 'actionLogs.model  = "User"')
		);
	}

	public function setPassword($v)
	{
		$this->password = md5($v);
	}

	public function getName()
	{
		return $this->fname . ' ' . $this->lname;
	}

	public static function currentUserID()
	{
		if (empty(Yii::app()->user)) {
			return 0;
		} elseif (empty(Yii::app()->user->id)) {
			if (Yii::app()->controller->id == 'api') {
				$controller = Yii::app()->getController();
				return $controller->user;
			} else {
				return 0;
			}
		}

		return Yii::app()->user->id;
	}
	public static function getCurrentUserType()
	{
		$user = self::getCurrentUser();
		return $user->type;
	}

	public static function currentUserDptId()
	{
		if (empty(Yii::app()->user)) {
			return 999;
		} elseif (empty(Yii::app()->user->id)) {
			if (Yii::app()->controller->id == 'api') {
				return 999;
			} else {
				return 999;
			}
		}
		return Yii::app()->user->dpt_id;
	}

	public static function currentUserOrgId()
	{
		if (empty(Yii::app()->user)) {
			return 999;
		} elseif (empty(Yii::app()->user->id)) {
			if (Yii::app()->controller->id == 'api') {
				return 999;
			} else {
				return 999;
			}
		}
		return Yii::app()->user->org;
	}

	public static function getCurrentUser()
	{
		$user = User::model()->findByPk(self::currentUserID());
		return $user;
	}

	public static function checkIsNotTruckUser()
	{
		return (self::getCurrentUserType() != self::IMPORT_TRUCK_DELIVERY && self::getCurrentUserType() != self::DRIVER) ? true : false;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$al = array(
			'id' => 'ID',
			'type' => 'Type',
			'title' => 'Title',
			'fname' => 'First Name',
			'lname' => 'Surname',
			'user' => 'User Name',
			'email' => 'Email',
			'password' => 'Password',
			'pwd_conf' => 'Confirm Password',
			'phone' => 'Phone',
			'fax' => 'Fax',
			'mobile' => 'Mobile',
			'since' => 'Since',
			'active' => 'Active',
			'meta' => 'Meta',
			'org_id' => 'Organisation',
			'org_search' => 'Organisation',
			'dpt_id' => 'Branch',
			'group' => 'Warehouse Group',
			'occupation'=>'Occupation'
		);
		foreach ($al as $k => $l) {
			$al[$k] = Yii::t(strtolower(__CLASS__), $l);
		}
		return $al;
	}

	public function getSubUserName()
	{
		$users = self::model()->count('org_id = :oid AND type = 60', array(':oid' => $this->org_id)) + 1;
		return $this->org_id . '-' . sprintf('%03s', 100 + $users);
	}

	public function getType()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$types[$this->type]) ? '' : self::$types[$this->type]);
	}

	public function getActive()
	{
		return Yii::t(strtolower(__CLASS__), $this->active == 1 ? 'Yes' : 'No');
	}

	public function getFullName()
	{
		return $this->fname . ' ' . $this->lname;
	}

	public function getAbfStatus()
	{
		$b1555 = 0;
		$id = 0;
		if (!empty($this->abf)) {
			foreach ($this->abf as $key => $abf) {
				if ($abf->type == FileRepo::USER_B1555_FILE) {
					$b1555 = 1;
				}
				elseif ($abf->type == FileRepo::USER_ID_FILE) {
					$id = 1;
				}
			}

			if (!empty($b1555) && !empty($id)) {
				return "Yes";
			}
			else{
				return "No";
			}
		}
		else{
			return "No";
		}
	}

	public static function managerList($id)
	{
		$rs = User::model()->findAll('active = 1 OR id = :id ORDER BY fname', array(':id' => $id));
		$l = array();
		foreach ($rs as $r) {
			$l[$r->id] = $r->getFullName();
		}
		return $l;
	}

	public static function userList()
	{
		$rs = User::model()->findAll('active = 1 ORDER BY fname');
		$l = array();
		foreach ($rs as $r) {
			$l[$r->id] = $r->getFullName();
		}
		return $l;
	}

	public static function getUserByEmail($email)
	{
		$r = User::model()->find('email = :email', array(':email' => $email));
		return $r;
	}

	public function getBranch()
	{
		return empty($this->dpt_id) ? '' : $this->branch->shortName(1);
	}

	public function beforeSave()
	{
		if (empty($this->dpt_id)) {
			$this->dpt_id = 106;
		}

		$this->dashboard = json_encode($this->dash);
		$this->meta = json_encode($this->extra);
		if (empty($this->since)) {
			$this->since = date('Y-m-d H:i:s');
		}

		return true;
	}

	public function afterFind()
	{
		if (!empty($this->dashboard)) {
			$this->dash = json_decode($this->dashboard, true);
		}

		if (!empty($this->meta)) {
			$this->extra = json_decode($this->meta, true);
		}

		return true;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;
		$criteria->with = array('org');

		$criteria->compare('t.type', $this->type);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('fname', $this->fname, true);
		$criteria->compare('lname', $this->lname, true);
		$criteria->compare('t.email', $this->email, true);
		$criteria->compare('password', $this->password, true);
		$criteria->compare('t.phone', $this->phone, true);
		$criteria->compare('t.fax', $this->fax, true);
		$criteria->compare('mobile', $this->mobile, true);
		$criteria->compare('since', $this->since, true);
		$criteria->compare('t.active', $this->active);
		$criteria->compare('org_id', $this->org_id);
		$criteria->compare('by_id', $this->by_id);
		$criteria->compare('org.name', $this->org_search, true);
		$criteria->compare('t.dpt_id', $this->dpt_id);
		if (!empty($this->dpmt)) {
			$criteria->addCondtion('t.dpmt &' . $this->dpmt . ">0");
		}

		if (Yii::app()->name == 'PEP') {
			$criteria->addInCondition('t.type', [35, 80, 82, 100, 120]);
		}

		if (!empty($this->abf)) {					
			if ($this->abf == "Yes") {	
				$criteria->addCondition('t.id IN (SELECT fid FROM `filerepo` WHERE type IN (220, 221))');
			}
			elseif ($this->abf == "No") {
				$criteria->addCondition('t.id NOT IN (SELECT fid FROM `filerepo` WHERE type IN (220, 221))');
				$criteria->addCondition('t.type IN (0,10,30,53,200,201,202,40,54,56,230,240,210,220)');
			}			
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.id ASC',
			),
			'pagination' => array(
				'pageSize' => '30',
			),
		));
	}

	public static function getTruckUsers()
	{
		$userList = User::model()->findAll(" type = :type ", [":type" => self::IMPORT_TRUCK_DELIVERY]);
		$result = [];
		foreach ($userList as $key => $user) {
			$result[$user->id] = $user['fname'];
		}
		return $result;
	}
	protected function afterSave()
	{
		Log::add($this, $this->isNewRecord ? 3 : 4);
		return true;
	}
	public static function isManager()
	{
		if (isset(Yii::app()->user->id)) {
			$user = User::model()->findByPk(Yii::app()->user->id);
			if ($user->dpmt & 1 > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function groupUsers()
	{
		$group = [];
		if (isset(Yii::app()->user->id)) {
			$user = User::model()->findByPk(Yii::app()->user->id);
			$dpmt = [2, 4, 8, 16, 32];
			foreach ($dpmt as $d) {
				if ($user->dpmt & $d) {
					$rs = User::model()->findAll('dpmt&' . $d . '>0');
					if (!empty($rs)) {
						foreach ($rs as $r) {
							$group[] = $r->id;
						}
					}
				}
			}
		}
		return $group;
	}
	public static function getDeparts()
	{
		$group = [];
		if (isset(Yii::app()->user->id)) {
			$user = User::model()->findByPk(Yii::app()->user->id);
			$dpmt = [2 => 10, 4 => 20, 8 => 30, 16 => 40,32=>50];
			foreach ($dpmt as $d => $v) {
				if (($user->dpmt & $d) > 0) {
					$group[$v] = Invoice::$dpmts[$v];
				}
			}
		}
		return $group;
	}

	//get all the org and  by org ids(the org belong to this org)
	public static function getOrgIds()
	{
		$a = [];
		if (Yii::app()->user->org == 1) {
			$a[] = 1;
		} else {
			$sql = 'SELECT id FROM org WHERE (id=:oid OR `by`=:oid)';
			$rs = Yii::app()->db->createCommand($sql)->bindValues([':oid' => Yii::app()->user->org])->queryAll();
			foreach ($rs as $r) {
				$a[] = $r['id'];
			}

		}
		return $a;
	}

	public static function getParentOrgIds()
	{
		$a = [];
		if (Yii::app()->user->org == 1) {
			$a[] = 1;
		} else {
			$sql = 'SELECT id FROM org WHERE (id=:oid OR `by`=:oid)';
			$rs = Yii::app()->db->createCommand($sql)->bindValues([':oid' => Org::model()->findByPk(Yii::app()->user->org)->by])->queryAll();
			foreach ($rs as $r) {
				$a[] = $r['id'];
			}

		}
		return $a;
	}

	public static function getUserName($id)
	{
		$user = self::model()->findByPk($id);
		if (!empty($user)) {
			return $user->getName();
		} else {
			return '';
		}

	}
	/*
	 * @return array();
	 */
	public static function getDataEntryUser()
	{
		$data = [];
		$sql = "SELECT id FROM `user` WHERE type=120";
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rs as $r) {
			$data[] = $r['id'];
		}
		$data[] = 379;
		return $data;
	}

	public static function isFBADriver()
	{
		return self::isSomeDriver(CargoProcess::FBA_CARGO);
	}

	public static function isB2BDriver()
	{
		return self::isSomeDriver(CargoProcess::B2B_CARGO);
	}

	public static function isNormalDriver()
	{
		return self::isSomeDriver(CargoProcess::NORMAL_CARGO);
	}

	public static function isSomeDriver($type)
	{
		if (!empty(self::getCurrentUser())&&self::getCurrentUser()->org_id < 2) {
			return true;
		}
		$record = CargoProcess::model()->find("type = :type and driver_id = :org", [":type" => $type, ":org" => self::getCurrentUser()->org_id]);
		return !empty($record) ? true : false;
	}

	public static function getITUsers($frontEnd = false)
	{
		$users = User::model()->findAll('(dpmt&64)>0 and active=1');
		if($frontEnd)
		{
			$ITTaskTabList= [0=>"IT Plan"];
			foreach ($users as $key => $user) {
				$ITTaskTabList[$user->id] = $user->fname;
			}
			return $ITTaskTabList;
		}else
		{
			return $users;
		}
	}

	public function logLogin()
	{
		Log::add($this, 1, array(
			'ip' => $_SERVER['REMOTE_ADDR'],
			'proxy' => empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? '' : $_SERVER['HTTP_X_FORWARDED_FOR'],
			'agent' => empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'],
		));
	}

	public function logLogout()
	{
		Log::add($this, 255, []);
	}

	public static function isShowCostUser()
	{
		if (!empty(Yii::app()->user->org)) {
			$org = Org::model()->findByPk(Yii::app()->user->org);
			if (!empty($org)) {
				$mdata = json_decode($org->meta);
				if (!empty($mdata->is_show_driver_cost)) {
					return true;
				}
			}
		}
		return false;
	}
}
