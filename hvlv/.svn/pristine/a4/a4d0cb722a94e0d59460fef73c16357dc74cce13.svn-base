<?php

/**
 * This is the model class for table "chargecode".
 *
 * The followings are the available columns in table 'chargecode':
 * @property string $id
 * @property string $code
 * @property string $name
 * @property string $description
 * @property string $type
 * @property string $dpmt
 * @property string $class
 * @property string $tax_code
 */
class Chargecode extends CActiveRecord
{
	public static $switch = [
		0 => 'Disable',
		1 => 'Enable',
	];

	public static $types = [
		'CURRENT' => 'Current Asset',
		'FIXED' => 'Fixed Asset',
		'LIABILITY' => 'Liability',
		'CURRLIAB' => 'Current Liability',
		'TERMLIAB' => 'Non-current Liability',
		'SALES' => 'Sales',
		'EQUITY' => 'Equity',
		'OTHERINCOME' => 'Other Income',
		'DIRECTCOSTS' => 'Direct Costs',
		'EXPENSE' => 'Expense',
		'OVERHEADS' => 'Overhead'
	];

	public static $classes = [
		'ASSET' => 'Asset',
		'LIABILITY' => 'Liability',
		'REVENUE' => 'Revenue',
		'EXPENSE' => 'Expense',
		'EQUITY' => 'Equity'
	];

	// public static $types = array(
	// 	'ASSET' => 'Asset',
	// 	'LIABILITY' => 'Liability',
	// 	'EQUITY' => 'Equity',
	// 	'REVENUE' => 'Revenue',
	// 	'EXPENSE' => 'Expense',
	// );

	public static $taxTypes = [
		'BASEXCLUDED' => 'BAS Excluded',
		'EXEMPTCAPITAL' => 'GST Free Capital',
		'EXEMPTEXPENSES' => 'GST Free Expenses',
		'EXEMPTEXPORT' => 'GST Free Exports',
		'EXEMPTOUTPUT' => 'GST free on Sales',
		'CAPEXINPUT' => 'GST on Capital',
		'GSTONCAPIMPORTS' => 'GST on Capital Imports',
		'GSTONIMPORTS' => 'GST on Imports',
		'INPUT' => 'GST on Purchases',
		'OUTPUT' => 'GST on Sales',
		'INPUTTAXED' => 'Input Taxed',
	];
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'chargecode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['code, name', 'required'],
			['code', 'length', 'max'=>20],
			['name, description', 'length', 'max'=>300],
			['type', 'length', 'max'=>30],
			['tax_code', 'length', 'max'=>45],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, code, name, description, type, dpmt, tax_code, class', 'safe', 'on'=>'search'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return [
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'code' => 'Code',
			'name' => 'Name',
			'dpmt' => 'Department',
			'description' => 'Description',
			'type' => 'Type',
			'tax_code' => 'Tax Code',
			'class' => 'Class',
		];
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

		$criteria->compare('id', $this->id);
		$criteria->compare('code', $this->code, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('type', $this->type, true);
		$criteria->compare('type', $this->dpmt);
		$criteria->compare('tax_code', $this->tax_code, true);
		$criteria->compare('class', $this->class, true);

		return new CActiveDataProvider($this, [
			'criteria'=>$criteria,
			'pagination'=> [
				'pageSize' => 30,
			]
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Chargecode the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
