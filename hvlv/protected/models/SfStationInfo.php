<?php

/**
 * This is the model class for table "sf_station_info".
 *
 * The followings are the available columns in table 'sf_station_info':
 * @property integer $id
 * @property string $country
 * @property string $city_code
 * @property string $business_station
 * @property string $op_station
 * @property string $op_id
 * @property string $state
 * @property string $city
 * @property string $suburb
 * @property string $pc_lo
 * @property string $pc_hi
 * @property string $unknow
 * @property string $region
 * @property integer $weekdays
 * @property string $surcharge
 * @property string $supplier
 * @property integer $is_end
 * @property integer $is_active
 */
class SfStationInfo extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sf_station_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('country, city_code, business_station, op_station, op_id, state, city, suburb, pc_lo, pc_hi, unknow, region, weekdays, surcharge, supplier, is_end, is_active', 'required'),
			array('weekdays, is_end, is_active', 'numerical', 'integerOnly'=>true),
			array('country, city_code, business_station, op_station, op_id, state, city, suburb, pc_lo, pc_hi, unknow, region, supplier', 'length', 'max'=>45),
			array('surcharge', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, country, city_code, business_station, op_station, op_id, state, city, suburb, pc_lo, pc_hi, unknow, region, weekdays, surcharge, supplier, is_end, is_active', 'safe', 'on'=>'search'),
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
			'country' => 'Country',
			'city_code' => 'City Code',
			'business_station' => 'Business Station',
			'op_station' => 'Op Station',
			'op_id' => 'Op',
			'state' => 'State',
			'city' => 'City',
			'suburb' => 'Suburb',
			'pc_lo' => 'Pc Lo',
			'pc_hi' => 'Pc Hi',
			'unknow' => 'Unknow',
			'region' => 'Region',
			'weekdays' => 'Weekdays',
			'surcharge' => 'Surcharge',
			'supplier' => 'Supplier',
			'is_end' => 'Is End',
			'is_active' => 'Is Active',
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
		$criteria->compare('country',$this->country,true);
		$criteria->compare('city_code',$this->city_code,true);
		$criteria->compare('business_station',$this->business_station,true);
		$criteria->compare('op_station',$this->op_station,true);
		$criteria->compare('op_id',$this->op_id,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('suburb',$this->suburb,true);
		$criteria->compare('pc_lo',$this->pc_lo,true);
		$criteria->compare('pc_hi',$this->pc_hi,true);
		$criteria->compare('unknow',$this->unknow,true);
		$criteria->compare('region',$this->region,true);
		$criteria->compare('weekdays',$this->weekdays);
		$criteria->compare('surcharge',$this->surcharge,true);
		$criteria->compare('supplier',$this->supplier,true);
		$criteria->compare('is_end',$this->is_end);
		$criteria->compare('is_active',$this->is_active);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SfStationInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
