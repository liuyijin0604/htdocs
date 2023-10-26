<?php

/**
 * This is the model class for table "unloco".
 *
 * The followings are the available columns in table 'unloco':
 * @property string $id
 * @property string $status
 * @property string $country
 * @property string $port
 * @property string $name
 * @property integer $flag
 * @property string $iata
 * @property string $geo
 * @property string $meta
 */
class Unloco extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'unloco';
	}

	public static $flags = array(
		1 => 'Sea Port',
		2 => 'Air Port',
	);

	public static $states = array(
		0 => 'Inactive',
		1 => 'Active',
		9 => 'Deleted',
	);

	public $mdata = [];

	public static $austates = ['NSW' => 'new south wales', 'QLD' => 'queensland', 'SA' => 'south australia', 'TAS' => 'tasmania', 'VIC' => 'victoria', 'WA' => 'western australia', 'ACT' => 'australian capital territory', 'NT' => 'northern territory'];

	public static $countries = ["AF" => "Afghanistan", "AX" => "Åland Islands", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia (Plurinational State of)", "BQ" => "Bonaire, Sint Eustatius and Saba", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "IO" => "British Indian Ocean Territory", "BN" => "Brunei Darussalam", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "CV" => "Cabo Verde", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos (Keeling) Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo", "CD" => "Congo, Democratic Republic of the", "CK" => "Cook Islands", "CR" => "Costa Rica", "CI" => "Côte d'Ivoire", "HR" => "Croatia", "CU" => "Cuba", "CW" => "Curaçao", "CY" => "Cyprus", "CZ" => "Czech Republic", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "SZ" => "Eswatini", "ET" => "Ethiopia", "FK" => "Falkland Islands (Malvinas)", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island and McDonald Islands", "VA" => "Holy See", "HN" => "Honduras", "HK" => "Hong Kong", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran (Islamic Republic of)", "IQ" => "Iraq", "IE" => "Ireland", "IM" => "Isle of Man", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JE" => "Jersey", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KP" => "Korea (Democratic People's Republic of)", "KR" => "Korea, Republic of", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Lao People's Democratic Republic", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macao", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "MX" => "Mexico", "FM" => "Micronesia (Federated States of)", "MD" => "Moldova, Republic of", "MC" => "Monaco", "MN" => "Mongolia", "ME" => "Montenegro", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "MK" => "North Macedonia", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestine, State of", "PA" => "Panama", "PG" => "Papua New Guinea", "PY" => "Paraguay", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RE" => "Réunion", "RO" => "Romania", "RU" => "Russian Federation", "RW" => "Rwanda", "BL" => "Saint Barthélemy", "SH" => "Saint Helena, Ascension and Tristan da Cunha", "KN" => "Saint Kitts and Nevis", "LC" => "Saint Lucia", "MF" => "Saint Martin (French part)", "PM" => "Saint Pierre and Miquelon", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino", "ST" => "Sao Tome and Principe", "SA" => "Saudi Arabia", "SN" => "Senegal", "RS" => "Serbia", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SX" => "Sint Maarten (Dutch part)", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia and the South Sandwich Islands", "SS" => "South Sudan", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard and Jan Mayen", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syrian Arab Republic", "TW" => "Taiwan, Province of China[a]", "TJ" => "Tajikistan", "TZ" => "Tanzania, United Republic of", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks and Caicos Islands", "TV" => "Tuvalu", "UG" => "Uganda", "UA" => "Ukraine", "AE" => "United Arab Emirates", "GB" => "United Kingdom", "US" => "United States of America", "UM" => "United States Minor Outlying Islands", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VE" => "Venezuela (Bolivarian Republic of)", "VN" => "Viet Nam", "VG" => "Virgin Islands (British)", "VI" => "Virgin Islands (US)", "WF" => "Wallis and Futuna", "EH" => "Western Sahara", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe"];

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, country, port, name, flag, iata, geo', 'safe'),
			array('flag', 'numerical', 'integerOnly'=>true),
			array('country', 'length', 'max'=>2),
			array('port, iata', 'length', 'max'=>3),
			array('name', 'length', 'max'=>100),
			array('geo', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, country, port, name, flag, iata, geo', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}
	
	public function beforeSave(){
		if(!empty($this->meta)) $this->meta = json_encode($this->mdata);
		return parent::beforeSave();
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return parent::afterFind();
	}

	public static function autocomp($t, $f=0){
		$tl = strlen($t);

		if($tl > 3){
			$q = $f & 2 > 0? 't.country = :c AND (t.port LIKE :p OR t.iata LIKE :p)' : 't.country = :c AND t.port LIKE :p';
			$c = substr($t, 0, 2);
			$p = substr($t, 2).'%';
			$ps = [':c' => $c, ':p' => $p];
		}elseif($tl == 3){
			$q = $f & 2 > 0? '((t.country = :c AND (t.port LIKE :p OR t.iata LIKE :p)) OR (t.port = :t OR t.iata LIKE :t))' : '(t.country = :c AND t.port LIKE :p) OR t.port = :t';
			$c = substr($t, 0, 2);
			$p = substr($t, 2).'%';
			$ps = [':c' => $c, ':p' => $p, ':t' => $t];
		}else{
			$q = $f & 2 > 0? '(t.country LIKE :t OR t.port LIKE :t OR t.iata LIKE :t)' : '(t.country LIKE :t OR t.port LIKE :t)';
			$c = $t.'%';
			$ps = [':c' => $c];
		}

		$rs = self::model()->findAll(array(
				'condition' => $q.($f > 0? ' AND t.flag & '.$f.' = '.$f : ''),
				'params' => $ps,
				'limit'=>20,'together'=>true,
				));
		if(empty($rs)){
			$rs = self::model()->findAll(array(
				'condition' => 't.name LIKE :s'.($f > 0? ' AND t.flag & '.$f.' = '.$f : ''),
				'params' => [':t' => '%'.$t.'%'],
				'limit'=>20,'together'=>true,
				));
		}
		$a = array();
		foreach($rs as $r){
			$a[] = array(
				'value' => $r->country.$r->port,
				'label' => $r->country.$r->port.': '.$r->name,
			);
		}

		return $a;
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'status' => 'Status',
			'country' => 'Country',
			'port' => 'Port',
			'name' => 'Name',
			'flag' => 'Flag',
			'iata' => 'Iata',
			'geo' => 'Geo',
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
	public function search($pgn=true, $ps = 30, $ec = false){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('status',$this->status);
		$criteria->compare('country',$this->country);
		$criteria->compare('port',$this->port,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('iata',$this->iata,true);
		$criteria->compare('geo',$this->geo,true);

		if(!empty($this->flag)){
			$criteria->addCondition('t.flag & ' . $this->flag . ' > 0');
		}

		$with = [];

		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'t.id DESC',
 			),
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Unloco the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
