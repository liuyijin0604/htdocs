<?php

/**
 * This is the model class for table "salesfunnel_requirements_submission".
 *
 * The followings are the available columns in table 'salesfunnel_requirements_submission':
 * @property integer $id
 * @property string $company_name
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property integer $contact_number
 * @property integer $status
 * @property string $password
 * @property string $category
 * @property integer $forwarder_shippingagent
 * @property integer $customer_type
 * @property integer $sub_status
 * @property integer $sub_status_ignore
 */
class SalesfunnelRequirementsSubmission extends CActiveRecord
{
	const STATUSSUBMITTED = 1;
	const STATUSNOTBUBMITED = 0;

	const FORWARDERAGENTYES = 1;
	const FORWARDERAGENTNO = 0;

	const CUSTOMERTYPESINGLE = 1;
	const CUSTOMERTYPEREPEAT = 2; 

	const STAT_PRC = 4;
	const STAT_INV = 32768;
	const STAT_APV = 8;
	const STAT_DEP = 16;
	const STAT_SYS = 32;
	const STAT_CON = 64;
	/**
	 * @return string the associated database table name
	 */

	public static $statusType = [
		1  => 'Submitted',
		0  => 'Not Submitted yet'
	];

	public static $forwarderAgent = [
		1  => 'Yes',
		0  => 'No'
	];

	public static $customerType = [
		1  => 'Single',
		2  => 'Repeat'
	];

	public static $singleClientStatuses=[
		4=>'PRC',       //price
		32768=>'INV',
		64=>'CON'       //console
	];

	public static $repeatClientStatuses=[
		4=>'PRC',
		8=>'APN',
		16=>'DEP',
		32=>'SYS',
		64=>'CON'
	];

	public function tableName()
	{
		return 'salesfunnel_requirements_submission';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('company_name, first_name, last_name, email, contact_number, forwarder_shippingagent', 'required'),
			array('contact_number, status, forwarder_shippingagent, customer_type, sub_status, sub_status_ignore', 'numerical', 'integerOnly'=>true),
			array('company_name, first_name, last_name, email, password', 'length', 'max'=>200),
			array('category', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, company_name, first_name, last_name, email, contact_number, status, password, category, forwarder_shippingagent, customer_type, sub_status, sub_status_ignore', 'safe', 'on'=>'search'),
		);
	}

	public function getProcessStatus($isHtml = true)
	{
		$str = "";
		$changeline = 0;
		$thisStatus = [-1];
		switch ($this->customer_type) {
			case self::CUSTOMERTYPESINGLE:
				$thisStatus = self::$singleClientStatuses;
				break;
			case self::CUSTOMERTYPEREPEAT:
				$thisStatus = self::$repeatClientStatuses;
				break;			
			default:
				// code...
				break;
		}

		// if(!empty($this->consol->mdata["is_fak"]))
		// {
		// 	$thisStatus = ConsolProcess::$seaFakDocumentationStatuses;
		// }

		// foreach (ConsolProcess::$seaDocumentationStatusesArr as $key => $value) {
		// 	if($this->status==$key)
		// 	{
		// 		$thisStatus =$value;
		// 		break;
		// 	}
		// }

		foreach ($thisStatus as $key => $processStatus) {
			if($changeline>0&&$changeline>30)
			{
				$changeline=0;
				$str .=  "</br></br>";
			}
			if(($this->sub_status&$key)>0&&($this->sub_status_ignore&$key)>0)
			{
				$color = "req_do_blue";
			}elseif(($this->sub_status&$key)>0&&($this->sub_status_ignore&$key)==0)
			{
				$color = "req_do_green";
			}else
			{
				$color = "req_do_red";
			}
			$str .= CHtml::link($processStatus,Yii::app()->createURL("salesFunnel/requirementProcessOperation",array("id"=>$this->id,'process_type'=>$key)),["class"=>"jqm_link req_do_block {$color}"]);
			$changeline+=strlen($processStatus)+2;
		}
		return $str;
	}

	public function checkSalesRequCanBeToNextStatus()
	{
		if($this->customer_type==Consol::SEACONSOL&&in_array($this->status,[ConsolProcess::STATE_NEW,ConsolProcess::SEA_ARRIVAL_NOTICE,ConsolProcess::SEA_WAITING_MANI,ConsolProcess::SEA_PORT_CHARGES_INVOICE]))
		{
			$allPass = true;
			$thisStates = $this->getNeedFinishDocumentationStatus();
			foreach ($thisStates as $status => $value)
			{
				if((($status&$this->sub_status)==0)&&(($status&$this->sub_status_ignore)==0))
				{
					$allPass = false;
					break;
				}
			}
			if($allPass)// push to next status
			{
				foreach (self::$newSeaStates as $status => $value) {
					if($this->status<$status)
					{
						$this->status = $status;
						$this->update(["status"]);
						break;
					}
				}
			}
			return $allPass;
		}
	}

	public function getSubStatus($isHtml = true)
	{
		$str = "";
		$changeline = 0;
		$subStatus = "";
		switch ($this->customer_type) {
			case self::CUSTOMERTYPESINGLE:
				$subStatus = self::$singleClientStatuses;
				break;
			case self::CUSTOMERTYPEREPEAT:
				$subStatus = self::$repeatClientStatuses;
				break;			
			default:
				// code...
				break;
		}

		return $subStatus;
	}

	public function showCustomerType()
	{
		$customerTypeStr = '';
		switch ($this->customer_type) {
			case self::CUSTOMERTYPESINGLE:
				$customerTypeStr.="Single";
				break;
			case self::CUSTOMERTYPEREPEAT:
				$customerTypeStr.="Repeat";
				break;
			
			default:
				// code...
				break;
		}
		return $customerTypeStr;
	}

	public function showStatusType()
	{
		$statusTypeStr = '';
		switch ($this->status) {
			case self::STATUSSUBMITTED:
				$statusTypeStr.=$this->status.":[Submitted]";
				break;
			case self::STATUSNOTBUBMITED:
				$statusTypeStr.=$this->status.":[Not Submitted yet]";
				break;
			
			default:
				// code...
				break;
		}
		return $statusTypeStr;
	}

	public function showForwarderAgent()
	{
		$forwarderAngentStr = '';
		switch ($this->forwarder_shippingagent) {
			case self::FORWARDERAGENTYES:
				$forwarderAngentStr.="Yes";
				break;
			case self::FORWARDERAGENTNO:
				$forwarderAngentStr.="No";
				break;
			
			default:
				// code...
				break;
		}
		return $forwarderAngentStr;
	}

	public function showServiceType()
	{
		$serviceTypeStr = '';
		if (!empty($this->category) && isset($this->category)){
			$categoryIds = explode(',',$this->category);
			foreach ($categoryIds as $key => $category) {
				switch ($category) {
					case SalesfunnelRequirementsQuestions::IMPORTSERVICE:
						$serviceTypeStr.=" Import Service ";
						break;
					case SalesfunnelRequirementsQuestions::SAMECITYDELIVERY:
						$serviceTypeStr.=" Same City ";
						break;
					case SalesfunnelRequirementsQuestions::_3PLFULFILMENT:
						$serviceTypeStr.=" 3PL Service ";
						break;
					
					default:
						// code...
						break;
				}
			}
		}
		return $serviceTypeStr;
	}

	public function auTel()
	{
		return self::auTelValid($this->contact_number);
	}
	
	public static function auTelValid($no)
	{
		$tel = preg_replace('/[^\d]+/', '', $no);
		$tel = trim($tel);
		$realTel = $tel;
		if (preg_match('/^1300(\d{6})$/', $tel, $m)) {
			// in order to avoid error
			// "message":"Phone number longer than 10 characters: 01300255533.","field":"shipments[0].to.phone"}
			$realTel = $tel;
		} elseif (preg_match('/61(\d{10})$/', $tel, $m)) {
			// in case +61 04 3274 3955
			$realTel = $m[1];
		} elseif (preg_match('/61(\d{9})$/', $tel, $m)) {
			$realTel = '0'.$m[1];
		} elseif (preg_match('/^0\d{9}$/', $tel)) {
			$realTel = $tel;
		} elseif (preg_match('/^\d{9}$/', $tel, $m)) {
			$realTel = '0'.$tel;
		} else {
			return '';
		}
		if (strlen($realTel) > 10) {
			$realTel = substr($realTel, 0, 10);
		}
		return $realTel;
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

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'company_name' => 'Company Name',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'email' => 'Email',
			'contact_number' => 'Contact Number',
			'status' => 'Status',
			'password' => 'Password',
			'category' => 'Category',
			'forwarder_shippingagent' => 'Forwarder Shipping Agent',
			'customer_type' => 'Customer Type',
			'sub_status' => 'Sub Status',
			'sub_status_ignore' => 'Sub Status Ignore',
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
	public function search($pgn = true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('company_name',$this->company_name,true);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('contact_number',$this->contact_number);
		$criteria->compare('status',$this->status);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('category',$this->category,true);
		$criteria->compare('forwarder_shippingagent',$this->forwarder_shippingagent);
		$criteria->compare('customer_type',$this->customer_type);
		$criteria->compare('sub_status',$this->sub_status);
		$criteria->compare('sub_status_ignore',$this->sub_status_ignore);
		$with = [];

		if($this->status==null)
		{
			$criteria->compare('status',self::STATUSSUBMITTED);
		}else
		{
			$criteria->compare('status',$this->status);
		}

		$criteria->with = $with;
		$criteria->together = true;
		$sort = new CSort(get_called_class());
		$sort->defaultOrder = 't.id ASC';
		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
			'sort' => $sort,
			'pagination' => [
			'pageSize' => 30,
			],
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SalesfunnelRequirementsSubmission the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
