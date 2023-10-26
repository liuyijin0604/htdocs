<?php

/**
 * This is the model class for table "org_bank_account".
 *
 * The followings are the available columns in table 'org_bank_account':
 * @property string $id
 * @property string $org_id
 * @property string $bank_name
 * @property string $bank_bsb
 * @property string $bank_number
 * @property int $currency
 * @property string $meta
 */
class OrgBankAccount extends CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrgBankAccount the static model class
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
		return 'org_bank_account';
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'org_id' => 'Org',
			'currency' => 'Currency',
			'bank_name' => 'Account Name',
			'bank_bsb' => 'BSB',
			'bank_number' => 'Account Number',
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, bank_name, bank_bsb, bank_number, currency', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, org_id, bank_name, bank_bsb, bank_number, currency, meta', 'safe', 'on'=>'search'),
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
			'org' => array(self::BELONGS_TO, 'Org', 'org_id'),
		);
	}

	public function getCurrency()
	{
		return Yii::t(strtolower(__CLASS__), Invoice::$currencies[$this->currency]);
	}

	public function beforeSave()
	{
		$this->bank_bsb = str_replace('-', '', $this->bank_bsb);
		$this->bank_bsb = substr($this->bank_bsb, 0, 3) . '-' . substr($this->bank_bsb, 3, 3);

		return true;
	}

}