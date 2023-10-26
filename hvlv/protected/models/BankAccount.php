<?php

/**
 * This is the model class for table "bank_account".
 *
 * The followings are the available columns in table 'bank_account':
 * @property string $id
 * @property string $name
 * @property string $code // for xero
 * @property string $created
 * @property string $account_number // for import
 * @property string $xero_id
 */
class BankAccount extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'bank_account';
	}

	public static $pcae_pay = array(
		self::WESTPAC_AUD,
		self::WESTPAC_USD,
		self::IMPORTS,
	);

	const WESTPAC_AUD = 9;
	const WESTPAC_USD = 13;
	const IMPORTS = 192;

	public static $tla_pay = array(
		self::TLA,
	);

	const TLA = 193;

	public function getAllForBilling()
	{
		$rs = [];
		$models = self::model()->findAll('id IN (' . implode(',', Yii::app()->name != 'TLA' ? self::$pcae_pay : self::$tla_pay) . ')');
		foreach ($models as $model) {
			$rs[$model->id] = $model->name;
		}
		return $rs;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, code, created, account_number', 'required'),
			array('name, account_number', 'length', 'max'=>45),
			array('code', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, code, created, account_number', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'code' => 'Code',
			'created' => 'Created',
			'account_number' => 'Account Number',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('account_number',$this->account_number,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BankAccount the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public static function getByXeroID($id)
	{
		$model = BankAccount::model()->find('xero_id = :id', array(':id' => $id));
		return @$model;
	}

}
