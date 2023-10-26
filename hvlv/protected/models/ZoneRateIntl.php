<?php

/**
 * This is the model class for table "zone_rate_intl".
 *
 * The followings are the available columns in table 'zone_rate_intl':
 * @property string $id
 * @property string $rate_id
 * @property string $zone
 * @property string $zone_name
 * @property string $weight_lo
 * @property string $weight_hi
 * @property string $base
 * @property string $item
 * @property string $perkg
 * @property string $nkg
 * @property string $minimum
 * @property integer $gst
 */
class ZoneRateIntl extends CActiveRecord
{

	public static $gsts = array(
		1 => 'Inc GST',
		2 => 'Ex GST',
		3 => 'GST Free',
	);
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'zone_rate_intl';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('rate_id, chargecode_id,zone, zone_name, weight_lo, weight_hi, base, item, perkg, nkg, minimum, levy', 'safe'),
			array('gst', 'numerical', 'integerOnly' => true),
			array('rate_id,chargecode_id', 'length', 'max' => 11),
			array('zone', 'length', 'max' => 20),
			array('weight_lo, weight_hi, base, item, perkg, minimum, levy', 'length', 'max' => 10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, rate_id,chargecode_id, zone, zone_name, weight_lo, weight_hi, base, item, perkg, nkg, minimum, gst, levy', 'safe', 'on' => 'search'),
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
			'orgrate' => array(self::BELONGS_TO, 'OrgRate', 'rate_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'rate_id' => 'Rate',
			'chargecode_id' => 'Charge Code',
			'zone' => 'Zone',
			'zone_name' => 'Zone Name',
			'weight_lo' => 'Wt. Lo',
			'weight_hi' => 'Wt. Hi',
			'base' => 'Base',
			'item' => 'Item',
			'perkg' => 'Perkg',
			'nkg' => 'nkg',
			'minimum' => 'Minimum',
			'gst' => 'GST',
			'levy' => 'Levy',
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

		$criteria->compare('id', $this->id);
		$criteria->compare('rate_id', $this->rate_id);
		$criteria->compare('zone', $this->zone, true);
		$criteria->compare('zone_name', $this->zone_name, true);
		$criteria->compare('weight_lo', $this->weight_lo, true);
		$criteria->compare('weight_hi', $this->weight_hi, true);
		$criteria->compare('base', $this->base, true);
		$criteria->compare('item', $this->item, true);
		$criteria->compare('perkg', $this->perkg, true);
		$criteria->compare('nkg', $this->nkg, true);
		$criteria->compare('minimum', $this->minimum, true);
		$criteria->compare('gst', $this->gst);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * @param $zoneRateId
	 * @return CActiveDataProvider
	 */
	public function getZoneRateWeightRange($zoneRateId)
	{

		$criteria = new CDbCriteria;
		$criteria->condition = 'rate_id = :rid';
		$criteria->params = array(':rid' => $zoneRateId);
		$criteria->group = 'weight_lo';
		$criteria->order = 'weight_lo ASC';

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * @param $zoneRateId
	 * @return CActiveDataProvider
	 */
	public function getZoneRateWeightRangeByArray($zoneRateId)
	{
		$rt = ZoneRateIntl::model()->findAll('rate_id = :rid group by weight_lo,weight_hi ORDER BY weight_lo ASC', array(':rid' => $zoneRateId));
		$rates = array();
		foreach ($rt as $rate) {
			$oneRate = array(
				'weight_lo' => $rate['weight_lo'],
				'weight_hi' => $rate['weight_hi'],
				'data' => array(),
			);

			// get related zone weight span price setting
			$zonePrices = ZoneRateIntl::model()->findAll('rate_id = :rid and weight_lo = :wlo and weight_hi = :whi ORDER BY zone',
				array(':rid' => $zoneRateId, ':wlo' => $rate['weight_lo'], ':whi' => $rate['weight_hi']));
			foreach ($zonePrices as $price) {
				$oneRate['data'][] = array(
					'code' => $price['zone'],
					'name' => $price['zone_name'],
					'ppc' => isset($price['item']) ? $price['item'] : 0,
					'pkg' => isset($price['perkg']) ? $price['perkg'] : 0,
					'minimum' => isset($price['minimum']) ? $price['minimum'] : 0,
					'base' => isset($price['base']) ? $price['base'] : 0,
					'nkg' => isset($price['nkg']) ? $price['nkg'] : 0,
				);
			}
			$rates[] = $oneRate;
		}
		return $rates;
	}

	/**
	 * get price rate weight ranges based on charge code ID
	 * @param $chargeCodeId
	 * @return array
	 */
	public function getZoneRateWeightRangeByArrayByChargecode($chargeCodeId)
	{
		$rt = ZoneRateIntl::model()->findAll('chargecode_id = :rid group by weight_lo,weight_hi ORDER BY weight_lo ASC', array(':rid' => $chargeCodeId));
		$rates = array();
		foreach ($rt as $rate) {
			$oneRate = array(
				'weight_lo' => $rate['weight_lo'],
				'weight_hi' => $rate['weight_hi'],
				'data' => array(),
			);

			// get related zone weight span price setting
			$zonePrices = ZoneRateIntl::model()->findAll('chargecode_id = :cid and weight_lo = :wlo and weight_hi = :whi ORDER BY zone',
				array(':cid' => $chargeCodeId, ':wlo' => $rate['weight_lo'], ':whi' => $rate['weight_hi']));
			foreach ($zonePrices as $price) {
				$oneRate['data'][] = array(
					'code' => $price['zone'],
					'name' => $price['zone_name'],
					'ppc' => isset($price['item']) ? $price['item'] : 0,
					'pkg' => isset($price['perkg']) ? $price['perkg'] : 0,
					'minimum' => isset($price['minimum']) ? $price['minimum'] : 0,
					'base' => isset($price['base']) ? $price['base'] : 0,
					'nkg' => isset($price['nkg']) ? $price['nkg'] : 0,
				);
			}
			$rates[] = $oneRate;
		}
		return $rates;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZoneRateIntl the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
