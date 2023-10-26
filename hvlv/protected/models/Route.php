<?php

/**
 * This is the model class for table "route".
 *
 * The followings are the available columns in table 'route':
 * @property integer $id
 * @property integer $airline_id
 * @property string $flight_no
 * @property integer $status
 * @property string $destination
 * @property string $departure
 * @property string $code
 * @property string $stops
 * @property string $days
 * @property string $info
 * @property string $cca_charge
 * @property string $awb_p
 * @property string $security_fuel
 * @property string $deptime
 * @property string $meta
 */
class Route extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'route';
	}

	public static $states = array(
		0 => 'Inactive',
		1 => 'Active',
	);

	public $mdata = [];

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, destination, departure, code', 'required'),
			array('id, airline_id, flight_no, status, destination, departure, code, stops, days, info, cca_charge, awb_p, security_fuel, deptime', 'safe'),
			array('airline_id, status', 'numerical', 'integerOnly' => true),
			array('cca_charge, awb_p, security_fuel', 'length', 'max' => 10),
			array('destination, departure, days', 'length', 'max' => 45),
			array('code', 'length', 'max' => 5),
			array('stops', 'length', 'max' => 20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, airline_id, flight_no, status, destination, departure, code, stops, days, info, cca_charge, awb_p, security_fuel, deptime', 'safe', 'on' => 'search'),
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
			'quotes' => array(self::HAS_MANY, 'Quotes', 'route_id', 'order' => 'wt_lo ASC'),
			'airline' => array(self::BELONGS_TO, 'Airline', 'airline_id'),
		);
	}

	public function getStatus()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$states[$this->status]) ? '' : self::$states[$this->status]);
	}

	public function beforeSave()
	{
		$this->meta = json_encode($this->mdata);
		return true;
	}

	public function afterFind()
	{
		$this->mdata = json_decode($this->meta, true);
		return true;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'airline_id' => 'Airline',
			'flight_no' => 'Flight No',
			'status' => 'Status',
			'destination' => 'Destination',
			'departure' => 'Departure',
			'code' => 'Code',
			'stops' => 'Stops',
			'days' => 'Days',
			'info' => 'Info',
			'cca_charge' => 'Cca Charge',
			'awb_p' => 'Awb P',
			'security_fuel' => 'Security Fuel',
			'deptime' => 'Departure Time',
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
	public function search($pgn = true, $ps = 20)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('airline_id', $this->airline_id);
		$criteria->compare('flight_no', $this->flight_no, true);
		$criteria->compare('status', $this->status);
		$criteria->compare('destination', $this->destination, true);
		$criteria->compare('departure', $this->departure, true);
		$criteria->compare('code', $this->code, true);
		$criteria->compare('stops', $this->stops, true);
		$criteria->compare('days', $this->days, true);
		$criteria->compare('info', $this->info, true);
		$criteria->compare('cca_charge', $this->cca_charge, true);
		$criteria->compare('awb_p', $this->awb_p, true);
		$criteria->compare('security_fuel', $this->security_fuel, true);
		$criteria->compare('deptime', $this->deptime, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.id DESC',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Route the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
