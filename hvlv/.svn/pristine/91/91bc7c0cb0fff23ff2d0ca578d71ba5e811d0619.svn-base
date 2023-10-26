<?php

/**
 * This is the model class for table "org_contact".
 *
 * The followings are the available columns in table 'org_contact':
 * @property string $id
 * @property string $org_id
 * @property integer $type
 * @property string $name
 * @property string $position
 * @property string $address
 * @property string $suburb
 * @property string $state
 * @property string $postcode
 * @property string $country
 * @property string $email
 * @property string $phone
 * @property string $direct
 * @property string $mobile
 * @property string $fax
 * @property string $desc
 * @property string $accode
 * @property integer $func
 * @property integer $status
 * @property string $meta
 */
class OrgContact extends CActiveRecord
{
	public static $funcs = array(
		1 => 'Billing Address',
		2 => 'Delivery Address',
		4 => 'Consignor Address',
		8 => 'RTS Address',
		16 => 'Enquiry Email',
	);
	const ENQUIRYEMAIL = 16;

	public static $types = array(
		'10' => 'Person',
		'20' => 'Store',
	);
	
	public static $states = array(
		'0' => 'Inactive',
		'1' => 'Active',
	);
	
	public $mdata = array(), $org_name;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrgContact the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'org_contact';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, name', 'required'),
			array('type, position, address, suburb,city, state, postcode, country, email, phone, direct, mobile, fax, desc, accode, func, meta', 'safe'),
			array('status, func', 'numerical', 'integerOnly'=>true),
			array('org_id', 'length', 'max'=>11),
			array('name, position, address, email', 'length', 'max'=>255),
			array('suburb,city,country', 'length', 'max'=>100),
			array('state, phone, fax', 'length', 'max'=>50),
			array('postcode', 'length', 'max'=>10),
			array('accode', 'length', 'max'=>30),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, org_id, org_name, type, name, position, address, suburb, city,state, postcode, country, email, phone, direct, mobile, fax, desc, accode, func, status, meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'org' => array(self::BELONGS_TO, 'Org', 'org_id'),
		);
	}
	
	public function getStatus(){
		return Yii::t(strtolower(__CLASS__), self::$states[$this->status]);
	}
	
	public function getType(){
		return Yii::t(strtolower(__CLASS__), empty(self::$types[$this->type])? 'System' : self::$types[$this->type]);
	}
	
	protected function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		return true;
	}
	
	protected function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return true;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		$al = array(
			'id' => 'ID',
			'org_id' => 'Org',
			'type' => 'Type',
			'name' => 'Name',
			'position' => 'Position',
			'address' => 'Address',
			'suburb' => 'Suburb',
            'city' => 'City',
			'state' => 'State',
			'postcode' => 'Postcode',
			'country' => 'Country',
			'email' => 'Email',
			'phone' => 'Phone',
			'direct' => 'Direct No.',
			'mobile' => 'Mobile',
			'fax' => 'Fax',
			'desc' => 'Notes',
			'accode' => 'A/C Code',
			'func' => 'Functions',
			'status' => 'Status',
			'meta' => 'Meta',
		);
		foreach($al as $k=>$l){
			$al[$k] = Yii::t(strtolower(__CLASS__), $l);
		}
		return $al;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search(){
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('org_id',$this->org_id);
		$criteria->compare('t.type',$this->type,true);
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('position',$this->position,true);
		$criteria->compare('t.address',$this->address,true);
		$criteria->compare('t.suburb',$this->suburb,true);
        $criteria->compare('t.city',$this->city,true);
		$criteria->compare('t.state',$this->state,true);
		$criteria->compare('t.postcode',$this->postcode,true);
		$criteria->compare('t.country',$this->country,true);
		$criteria->compare('t.email',$this->email,true);
		$criteria->compare('t.phone',$this->phone,true);
		$criteria->compare('direct',$this->direct,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('accode',$this->accode,true);
		$criteria->compare('t.status',$this->status);

		if(!empty($this->func)){
			$criteria->addCondition('t.func & ' . $this->func . ' > 0');
		}

		if(!empty($this->org_name)){
			$with[] = 'org';
			$criteria->compare('org.name',$this->org_name, true);
		}
		
		if(Yii::app()->name == 'PEP'){
			$with[] = 'org';
			$criteria->addInCondition('org.type', [60, 65]);
		}

		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
    			'defaultOrder'=>'t.name ASC',
  			),
			'pagination'=>array(
				'pageSize'=>'30',
            ),
		));
	}
}