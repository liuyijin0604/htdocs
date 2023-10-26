<?php

/**
 * This is the model class for table "wms_charge_code".
 *
 * The followings are the available columns in table 'wms_charge_code':
 * @property string $id
 * @property string $chargecode
 * @property string $created
 * @property integer $status
 * @property integer $type
 * @property string $v_from
 * @property string $v_to
 * @property string $note
 * @property string $meta
 */
class WmsChargeCode extends CActiveRecord
{

	public static $types = [
		1 => 'Pick',
		2 => 'Pack',
	];

	public static $states = [
		0 => 'Inactive',
		1 => 'Active',
	];

	public function getType()
	{
		return isset(static::$types[$this->type])? Yii::t(strtolower(__CLASS__), static::$types[$this->type]) : $this->type;
	}

	public function getStatus()
	{
		return isset(static::$states[$this->status])? Yii::t(strtolower(__CLASS__), static::$states[$this->status]) : $this->status;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_charge_code';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['type, v_from, v_to', 'required'],
			['chargecode, created, status, v_from, type, v_to, note, meta', 'safe'],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, chargecode, created, status, v_from, type, v_to, note, meta', 'safe', 'on' => 'search'],
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
			'rates' => array(self::HAS_MANY, 'WmsChargeRate', 'chargecode_id'),
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

	public function getWeightRangeByArrayByChargecode()
	{
		$rates = [];
		foreach ($this->rates as $rate) {
			$rates[] = array(
				'weight_lo' => $rate->weight_lo,
				'weight_hi' => $rate->weight_hi,
				'base' => $rate->base,
				'perkg' => $rate->perkg,
				'minimum' => $rate->minimum,
			);
		}

		return $rates;
	}

	public function getChargeValue($weight = 0)
	{
		$rate = WmsChargeRate::model()->find('weight_lo < :w AND weight_hi >= :w AND chargecode_id = :id', [':w' => $weight,':id'=>$this->id]);
		// if (!empty($rate)) return $rate->perkg;
		if (!empty($rate)) return $rate->base;
		else return 0;
	}

	/**
	 * if chargecode is empty , we create it now
	 * @return bool
	 */
	public function beforeSave()
	{
		if (empty($this->created)) $this->created = date('Y-m-d');
		if (empty($this->v_from)) $this->v_from = date('Y-m-d');
		if (empty($this->v_to)) $this->v_to = date('Y-m-d');
		$this->meta = empty($this->mdata)? '' : json_encode($this->mdata);
		return true;
	}

	public function afterFind()
	{
		if (!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
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
			$query = WmsChargeCode::model()->find('chargecode = :ccode', [':ccode' => $chargecode]);
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
			'created' => 'Created',
			'status' => 'Status',
			'v_from' => 'Valid From',
			'v_to' => 'Valid To',
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
		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('t.chargecode', $this->chargecode, true);
		$criteria->compare('t.created', $this->created, true);
		$criteria->compare('t.status', $this->status);
		$criteria->compare('t.type', $this->type);
		$criteria->compare('t.v_from', $this->v_from, true);
		$criteria->compare('t.v_to', $this->v_to, true);
		$criteria->compare('t.note', $this->note, true);

		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
			'sort' => [
				'defaultOrder' => 't.id DESC',
			],
			'pagination' => [
				'pageSize' => 30,
			],
		]);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZoneRate the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}