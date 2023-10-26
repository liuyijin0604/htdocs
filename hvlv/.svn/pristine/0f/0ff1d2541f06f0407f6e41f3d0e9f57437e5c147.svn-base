<?php

/**
 * This is the model class for table "postcode".
 *
 * The followings are the available columns in table 'postcode':
 * @property integer $id
 * @property string $postcode
 * @property string $suburb
 * @property integer $state
 * @property string  $country
 * @property double $lat
 * @property double $lon
 */
class Postcode extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'postcode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('postcode, suburb, state, country', 'required'),
			array('lat,long', 'safe'),
			array('postcode', 'length', 'max' => 16),
			array('suburb', 'length', 'max' => 40),
			array('country', 'length', 'max' => 10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, postcode, suburb, state, country, lat, lon', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'postcode' => 'Postcode',
			'suburb' => 'Suburb',
			'state' => 'State',
			'lat' => 'Latitude',
			'lon' => 'Longitude',
		);
	}

	public static function countryList()
	{
		$rs = self::model()->findAll(['group' => 'country']);
		$a = [];
		foreach ($rs as $r) {
			$a[$r['country']] = $r['country'];
		}
		return $a;
	}

	public static function stateList()
	{
		$rs = self::model()->findAll(['group' => 'country, state', 'order' => 'country, state']);
		$a = [];
		foreach ($rs as $r) {
			$a[$r['state']] = $r['state'];
		}
		return $a;
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
	public function search($pgn = true, $ps = 30, $ec = false, $paidonly = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;
		$criteria->compare('id', $this->id);
		$criteria->compare('postcode', $this->postcode);
		$criteria->compare('suburb', $this->suburb, true);
		$criteria->compare('state', $this->state);
		$criteria->compare('country', $this->country);
		$criteria->compare('lat', $this->lat);
		$criteria->compare('lon', $this->lon);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'suburb',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	public static function validateAddress($suburb, $state, $postcode, $country = 'AU', $apapi = true)
	{
		//abbrevs
		$suburb = preg_replace(['/\bMT\b\.*/i', '/\bNTH\b\.*/i', '/\bSTH\b\.*/i', '/^PT\b\.*/i', '/^(SAINT\b|ST\.)/i'], ['MOUNT', 'NORTH', 'SOUTH', 'PORT', 'ST'], $suburb);
		$c = Postcode::model()->count('suburb=:suburb AND state=:state AND postcode=:postcode AND country=:country', ['suburb' => $suburb, ':state' => $state, ':postcode' => $postcode, ':country' => $country]);
		if ($c > 0) return true;
		if ($country == 'AU' && $apapi) {
			$apa = new AusPostAPI('syd');
			if ($apa->validateAddress($suburb, $state, $postcode) === true) {
				// $pc = new Postcode;
				// $pc->country = 'AU';
				// $pc->suburb = strtoupper($suburb);
				// $pc->state = strtoupper($state);
				// $pc->postcode = $postcode;
				// $pc->save();
				return true;
			}
		}
		return false;
	}

	public static function autoSuburbByPostcode($suburb, $state, $postcode, $country = 'AU')
	{
		//abbrevs
		$suburb = preg_replace(['/\bMT\b\.*/i', '/\bNTH\b\.*/i', '/\bSTH\b\.*/i', '/^PT\b\.*/i', '/^(SAINT\b|ST\.)/i'], ['MOUNT', 'NORTH', 'SOUTH', 'PORT', 'ST'],  trim($suburb));
		$c = Postcode::model()->count('suburb=:suburb AND state=:state AND postcode=:postcode AND country=:country', ['suburb' => $suburb, ':state' => $state, ':postcode' => $postcode, ':country' => $country]);

		if (empty($c)) {
			$match = false;
			$rs = Postcode::model()->findAll('state=:state AND postcode=:postcode AND country=:country', [':state' => $state, ':postcode' => $postcode, ':country' => $country]);
			if (empty($rs)) {
				$r = Postcode::model()->find('state=:state AND suburb=:suburb AND country=:country', [':state' => $state, ':suburb' => $suburb, ':country' => $country]);
				if ($r) {
					return ['suburb' => $suburb, 'state' => $state, 'postcode' => $r->postcode, 'country' => $country];
				}
			}
			$ss = [];
			foreach ($rs as $r) {
				$ss[] = $r->suburb;
			}
			if (preg_match('/^(SOUTH|NORTH|EAST|WEST) (.+)$/i', $suburb, $m)) { // sawp words
				if (in_array(strtoupper($m[2] . ' ' . $m[1]), $ss)) {
					$match = true;
					$suburb = strtoupper($m[2] . ' ' . $m[1]);
				}
			}
			if (!$match) { // words by words
				$ws = explode(' ', strtoupper($suburb));
				foreach ($ws as $w) {
					$w = trim($w);
					if (empty($w) || in_array(strtoupper($w), ['SOUTH', 'NORTH', 'EAST', 'WEST'])) continue;
					foreach ($ss as $s) {
						if (strpos($s, $w) !== false) {
							$match = true;
							$suburb = $s;
							break 2;
						}
					}
				}
			}
			if (!$match && !empty($ss)) { //last resort, lucky random
				$suburb = $ss[array_rand($ss)];
			}
		}

		return ['suburb' => $suburb, 'state' => $state, 'postcode' => $postcode, 'country' => $country];
	}

	public static function suburb($term)
	{
		$c = new curl('https://geo.pcaex.com/site/suburb');
		$d['key'] = "pcaexpress";
		$d['suburb'] = 1;
		$d['term'] = $term;
		$data_string = $c->asPostString($d);
		$c->setopt(CURLOPT_CUSTOMREQUEST, "POST");
		$c->setopt(CURLOPT_POSTFIELDS, $data_string);
		$c->setopt(CURLOPT_RETURNTRANSFER, true);
		$c->exec();
		$result = $c->result;
		return $result;
	}

	public static function getStateByPostcode($postcode)
	{
		$ranges = array(
			'NSW' => array(
				1000, 1999,
				2000, 2599,
				2619, 2898,
				2921, 2999
			),
			'ACT' => array(
				200, 299,
				2600, 2618,
				2900, 2920
			),
			'VIC' => array(
				3000, 3999,
				8000, 8999
			),
			'QLD' => array(
				4000, 4999,
				9000, 9999
			),
			'SA' => array(
				5000, 5999
			),
			'WA' => array(
				6000, 6797,
				6800, 6999
			),
			'TAS' => array(
				7000, 7999
			),
			'NT' => array(
				800, 999
			)
		);
		$exceptions = array(
			4825 => 'QLD',
			872 => 'NT',
			2406 => 'NSW',
			2540 => 'NSW',
			2611 => 'ACT',
			2620 => 'NSW',
			3585 => 'VIC',
			3586 => 'VIC',
			3644 => 'VIC',
			3691 => 'VIC',
			3707 => 'VIC',
			4380 => 'QLD',
			4377 => 'QLD',
			4383 => 'QLD',
			4385 => 'QLD'
		);
		$postcode = intval($postcode);
		if (array_key_exists($postcode, $exceptions)) {
			return $exceptions[$postcode];
		}
		foreach ($ranges as $state => $range) {
			$c = count($range);
			for ($i = 0; $i < $c; $i += 2) {
				$min = $range[$i];
				$max = $range[$i + 1];
				if ($postcode >= $min && $postcode <= $max) {
					return $state;
				}
			}
		}
		return "";
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Postcode the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
