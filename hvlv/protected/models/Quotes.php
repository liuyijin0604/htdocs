<?php

/**
 * This is the model class for table "quotes".
 *
 * The followings are the available columns in table 'quotes':
 * @property integer $id
 * @property integer $route_id
 * @property string $uld_type
 * @property integer $wt_lo
 * @property integer $wt_hi
 * @property string $pkg
 * @property string $sec_fuel
 * @property string $min
 * @property string $date_eff
 * @property string $date_exp
 */
class Quotes extends CActiveRecord
{

	public static $types = array(
		'pallet' => 'Pallet',
		'AKE' => 'AKE',
		'PMC' => 'PMC',
	);

	public $route_destination, $route_code, $route_departure, $route_airline_name, $route_flight_no, $route_days, $route_deptime, $departure, $destination;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'quotes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uld_type, wt_lo, pkg, min, date_eff', 'required'),
			array('route_id, wt_lo', 'numerical', 'integerOnly' => true),
			array('uld_type', 'length', 'max' => 45),
			array('pkg, sec_fuel, min', 'length', 'max' => 10),
			array('date_exp', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, route_id, uld_type, wt_lo, wt_hi, pkg, sec_fuel, min, date_eff, date_exp, route_destination, route_code, route_departure, route_airline_name, route_flight_no, route_days, route_deptime, departure, destination', 'safe', 'on' => 'search'),
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
			'route' => array(self::BELONGS_TO, 'Route', 'route_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'route_id' => 'Route',
			'uld_type' => 'Uld Type',
			'wt_lo' => 'Wt Lo',
			'wt_hi' => 'Wt Hi',
			'pkg' => 'Pkg',
			'sec_fuel' => 'Security Fuel',
			'min' => 'Min',
			'date_eff' => 'Date Eff',
			'date_exp' => 'Date Exp',
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

		$criteria = new CDbCriteria;
		$criteria->with = array('route' => array('together' => true));
		$criteria->together = true;
		// $criteria->compare('route_id',$this->route_id);
		// $criteria->condition='wt_lo<=:wt_lo AND wt_hi>=:wt_lo';
		// $criteria->compare('wt_lo',$this->wt_lo);
		// $criteria->compare('wt_hi',$this->wt_hi);

		$criteria->condition = 'date_eff <= :date_eff AND (date_exp >= :date_eff OR date_exp = "0000-00-00") AND route.departure like :dep AND uld_type like :uld_type
		AND (route.code like :des OR route.destination like :des)';
		if (!empty($this->wt_lo)) {
			$criteria->addCondition('wt_lo <= :wt_lo AND (wt_hi IS NULL OR wt_hi > :wt_lo)');
		}

		$destination = explode('-', $this->destination)[0];
		$departure = empty(explode('-', $this->departure)[1]) ? explode('-', $this->departure)[0] : explode('-', $this->departure)[1];

		$criteria->params = array(
			':date_eff' => $this->date_eff,
			':wt_lo' => $this->wt_lo,
			':dep' => "$departure%",
			':uld_type' => "$this->uld_type%",
			':des' => "$destination%",
		);

		$criteria->compare('pkg', $this->pkg, true);
		// $criteria->compare('sec_fuel', $this->sec_fuel, true);
		$criteria->compare('min', $this->min, true);
		$criteria->compare('date_exp', $this->date_exp, true);

		$with = [];
		if (!empty($this->route_destination)) {
			$criteria->addCondition('route.destination like "%' . $this->route_destination . '%"');
		}
		if (!empty($this->route_code)) {
			$criteria->addCondition('route.code like "%' . $this->route_code . '%"');
		}
		if (!empty($this->route_departure)) {
			$criteria->addCondition('route.departure like "%' . $this->route_departure . '%"');
		}
		if (!empty($this->route_airline_name)) {
			$with[] = 'route.airline';
			$criteria->addCondition('airline.name like "%' . $this->route_airline_name . '%"');
		}
		if (!empty($this->route_flight_no)) {
			$criteria->addCondition('route.flight_no like "%' . $this->route_flight_no . '%"');
		}
		if (!empty($this->route_days)) {
			$criteria->addCondition('route.days like "%' . $this->route_days . '%"');
		}
		if (!empty($this->route_deptime)) {
			$criteria->addCondition('route.deptime like "%' . $this->route_deptime . '%"');
		}

		if (!empty($with)) {
			$criteria->with = array_merge($criteria->with, array_unique($with));
		}

		return new CActiveDataProvider($this, array(
			'pagination' => array(
				'pageSize' => 20,
			),
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.id DESC',
			),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Quotes the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**/
	public static function airlineList()
	{
		$rs = Airline::model()->findAll(['condition' => 'id > 0', 'order' => 'name ASC']);
		$a = array();
		foreach ($rs as $v) {
			$a[$v['id']] = $v['name'];
		}
		return $a;
	}

	public static function destinationList()
	{
		$rs = Route::model()->findAll('id > 0');
		$a = array();
		foreach ($rs as $v) {
			$a[] = $v['destination'];
		}
		return $a;
	}

	public function getType()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$types[$this->uld_type]) ? '' : self::$types[$this->uld_type]);
	}

}
