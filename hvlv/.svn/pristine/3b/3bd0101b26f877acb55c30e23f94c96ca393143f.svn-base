<?php

/**
 * This is the model class for table "intl_charge_code".
 *
 * The followings are the available columns in table 'intl_charge_code':
 * @property string $id
 * @property string $chargecode
 * @property string $couriers
 * @property string $created
 * @property integer $status
 * @property integer $type
 * @property integer $charge_wt   // 0 means chargeWeight based on the max of weight based on cbm and gross weigth........ 1  based on gross weight.
 * @property string  meta   json format
 * @property string $v_from
 * @property string $v_to
 * @property string $note
 */
class IntlChargeCode extends CActiveRecord
{
	public $owner_name;
	public $mdata = [];

	public static $states = [
		0 => 'Disable',
		1 => 'Active',
	];

	public static $charge_weight = [
		0 => 'CBM Weight',
		1 => 'Gross Weight',
	];

	public static $charge_code_types = [
		0 => 'self use',
		1 => 'Client Use',
	];

	public static $cubic_factors = [
		0 => '4000',
		1 => '6000',
		2 => '5000',
	];

	public function getStatus()
	{
		return isset(static::$states[$this->status]) ? Yii::t(strtolower(__CLASS__), static::$states[$this->status]) : $this->status;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'intl_charge_code';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['chargecode,org_id', 'required'],
			['status,charge_wt,type', 'numerical', 'integerOnly' => true],
			['chargecode', 'length', 'max' => 6],
			['couriers', 'length', 'max' => 500],
			['note', 'length', 'max' => 500],
			['created, v_from, v_to,meta', 'safe'],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, chargecode,org_id, couriers, created,oids,status,charge_wt, v_from,type, v_to, note,owner_name,meta', 'safe', 'on' => 'search'],
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
			'owner' => [self::BELONGS_TO, 'Org', 'org_id'],
		];
	}

	/**
	 * if chargecode is empty , we create it now
	 * @return bool
	 */
	public function beforeValidate()
	{
		if (empty($this->chargecode)) {
			$this->chargecode = $this->genChargeCode();
		}
		return true;
	}

	/**
	 * if chargecode is empty , we create it now
	 * @return bool
	 */
	public function beforeSave()
	{
		if (empty($this->chargecode)) {
			$this->chargecode = $this->genChargeCode();
		}
		// if (!empty($this->couriersObj)) {
		// 	$this->couriers = json_encode($this->couriersObj);
		// }
		$this->meta = empty($this->mdata) ? '' : json_encode($this->mdata);
		return true;
	}

	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata = json_decode($this->meta, true);
		}
		// if (!empty($this->couriers)) {
		// 	$this->couriersObj = json_decode($this->couriers, true);
		// }
	}

	/**
	 * create random 4 digitals charge code
	 * @return string
	 */
	private function genChargeCode()
	{
		$digits = 4;
		$chargecode = '';
		while (true) {
			$chargecode = str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
			$query = IntlChargeCode::model()->find('chargecode = :ccode', [':ccode' => $chargecode]);
			if (empty($query)) {
				break;
			}
		}
		return $chargecode;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'chargecode' => 'Chargecode',
			'couriers' => 'Couriers',
			'created' => 'Created',
			'status' => 'Status',
			'v_from' => 'V From',
			'v_to' => 'V To',
			'note' => 'Note',
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

		$criteria = new CDbCriteria;

		$criteria->with = ['owner'];
		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('chargecode', $this->chargecode, true);
		$criteria->compare('couriers', $this->couriers, true);
		$criteria->compare('created', $this->created, true);
		$criteria->compare('t.status', $this->status);
		$criteria->compare('t.type', $this->type);
		$criteria->compare('owner.name', $this->owner_name, true);
		$criteria->compare('v_from', $this->v_from, true);
		$criteria->compare('v_to', $this->v_to, true);
		$criteria->compare('note', $this->note, true);
		if (!empty($this->oids)) {
			$criteria->addInCondition('t.org_id', $this->oids);
		}

		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
			'sort' => [
				'attributes' => [
					'owner_name' => [
						'asc' => 'owner.name',
						'desc' => 'owner.name DESC',
					],
					'*',
				],
			],
			'pagination' => [
				'pageSize' => 50,
			],
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return IntlChargeCode the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
