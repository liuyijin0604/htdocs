<?php

/**
 * This is the model class for table "container_quotation".
 *
 * The followings are the available columns in table 'container_quotation':
 * @property string $id
 * @property string $user_id
 * @property string $date
 * @property string $address
 * @property string $suburb
 * @property string $state
 * @property string $postcode
 * @property string $standard_trailer
 * @property string $quotation_num
 */
class ContainerQuotation extends CActiveRecord
{
	public $customer_name;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'container_quotation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id', 'length', 'max'=>11),
			array('suburb, state, standard_trailer, quotation_num', 'length', 'max'=>100),
			array('postcode', 'length', 'max'=>20),
			array('date, address', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, date, address, suburb, state, postcode, standard_trailer, quotation_num', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
			'user' => [self::BELONGS_TO, 'User', 'user_id'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'date' => 'Date',
			'address' => 'Address',
			'suburb' => 'Suburb',
			'state' => 'State',
			'postcode' => 'Postcode',
			'standard_trailer' => 'Standard Trailer',
			'quotation_num' => 'Quotation Num',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('suburb',$this->suburb,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('postcode',$this->postcode,true);
		$criteria->compare('standard_trailer',$this->standard_trailer,true);
		$criteria->compare('quotation_num',$this->quotation_num,true);
		$criteria->compare('distance',$this->distance,true);
		$criteria->compare('amount',$this->amount,true);

		$with=[];
		if (!empty($this->customer_name)) {
			$with[]='user';
			$criteria->addNotInCondition('user.fname'.' '.'user.lname', $this->customer_name);
		}
		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		$sort = new CSort(get_called_class());
		$sort->defaultOrder='t.id DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>$sort,
		));
	}

	public function getCustomerName()
	{
		if (empty($this->user_id)) {
			return 'Casual';
		}
		else{
			return $this->user->fname." ".$this->user->lname;
		}
	}

	public function getAddress()
	{
		return $this->address.' '.$this->suburb.' '.$this->state.' '.$this->postcode;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ContainerQuotation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
