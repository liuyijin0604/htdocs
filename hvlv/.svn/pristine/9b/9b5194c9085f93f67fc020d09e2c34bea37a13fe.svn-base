<?php

/**
 * This is the model class for table "cnl_party".
 *
 * The followings are the available columns in table 'cnl_party':
 * @property string $id
 * @property string $hash
 * @property string $ediCode
 * @property string $companyName
 * @property string $uscc
 * @property string $contactName
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string $zipcode
 * @property string $phone
 * @property string $email
 * @property string $meta
 */
class CnlParty extends CActiveRecord
{
	public $mdata = [];
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'cnl_party';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hash, ediCode, companyName, uscc, contactName, address, city, state, country, zipcode, phone, email, meta', 'safe'),
			array('hash', 'length', 'max'=>40),
			array('ediCode', 'length', 'max'=>10),
			array('companyName', 'length', 'max'=>200),
			array('uscc, zipcode, phone', 'length', 'max'=>50),
			array('contactName, city, state, country, email', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, hash, ediCode, companyName, uscc, contactName, address, city, state, country, zipcode, phone, email, meta', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}
	
	public function beforeSave(){
		$this->meta = empty($this->mdata)? '' : json_encode($this->mdata);
		return parent::beforeSave();
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
			'hash' => 'Hash',
			'ediCode' => 'Edi Code',
			'companyName' => 'Company Name',
			'uscc' => 'Uscc',
			'contactName' => 'Contact Name',
			'address' => 'Address',
			'city' => 'City',
			'state' => 'State',
			'country' => 'Country',
			'zipcode' => 'Zipcode',
			'phone' => 'Phone',
			'email' => 'Email',
			'meta' => 'Meta',
		);
	}

	public static function addParty($data){
		unset($data['type']);
		$hash = sha1(json_encode($data));
		$p = self::model()->find('hash = :h', [':h' => $hash]);
		if(empty($p)){
			$p = new CnlParty;
			$p->hash = $hash;
			foreach($data as $k => $v){
				if($p->hasAttribute($k)){
					$p->{$k} = $v;
				}else{
					$p->mdata[$k] = $v;
				}
			}
			$p->save();
		}

		return $p;
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('hash',$this->hash,true);
		$criteria->compare('ediCode',$this->ediCode,true);
		$criteria->compare('companyName',$this->companyName,true);
		$criteria->compare('uscc',$this->uscc,true);
		$criteria->compare('contactName',$this->contactName,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('zipcode',$this->zipcode,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('meta',$this->meta,true);
		$with = [];

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
	 * @return CnlParty the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
