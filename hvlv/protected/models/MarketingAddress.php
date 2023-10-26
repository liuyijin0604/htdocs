<?php
class MarketingAddress extends CActiveRecord
{
	const status_active = 1;
	const status_inactive = 0;

    public const listYesNo = [
        1=>'Yes',
        0=>'No',
    ];

    public const listStatus = [
        1=>'Active',
        0=>'Inactive',
    ];

	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'marketing_address';
	}

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			array('id,email,company,country,contact,tel,imp,tpl,tld,status,meta,group', 'safe'),
			// @todo Please remove those attributes that should not be searched.
			array('id,email,company,country,contact,tel,imp,tpl,tld,status,meta,group', 'safe', 'on' => 'search'),
		);
	}

	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'email' => 'email',
			'company' => 'company',
			'country' => 'country',
			'contact' => 'contact',
			'tel' => 'tel',
            'imp' => 'imp',
			'tpl' => 'tpl',
			'tld' => 'tld',
			'status' => 'status',
			'meta' => 'meta',
			'group' => 'group',
		);
	}
	

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search(){
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);

		$criteria->compare('email',$this->email,true);
		$criteria->compare('company',$this->company,true);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('contact',$this->contact,true);
		$criteria->compare('tel',$this->tel,true);
        $criteria->compare('imp',$this->imp,true);
		$criteria->compare('tpl',$this->tpl,true);
		$criteria->compare('tld',$this->tld,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('group',$this->group,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>[
				'defaultOrder'=>'t.id desc',
			],
			'pagination'=>[
				'pageSize'=>'30',
			],
		));
	}



}