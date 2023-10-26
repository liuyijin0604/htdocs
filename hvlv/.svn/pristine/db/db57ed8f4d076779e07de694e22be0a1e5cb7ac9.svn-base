<?php

/**
 * This is the model class for table "currency".
 *
 * The followings are the available columns in table 'currency':
 * @property integer $id
 * @property string $currency
 * @property string $date
 * @property string $type
 * @property integer $valid
 * @property string $meta
 */
class Currency extends CActiveRecord
{
	const AUD =1;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'currency';
	}
	public static $currency_type=[
		1=>'AUD',
		2 => 'USD',
		3 => 'CNY',
		4 => 'HKD',
		5 => 'EUR',
		6 => 'GBP',
		7 => 'NZD',
		8 => 'CAD',
		9 => 'NOK',
		10 => 'SGD',
		11 => 'DKK',
		12 => 'INR',
		13 => 'JPY'
	];
	public static $currency_type_re=[
		'AUD'=>1,
		'AU'=>1,
		'USD'=>2,
		'CNY'=>3,
		'HKD'=>4,
		'EUR'=>5,
		'GBP'=>6,
		'NZD'=>7,
		'CAD'=>8,
		'NOK'=>9,
		'SGD'=>10,
		'DKK'=>11,
		'INR'=>12,
		'JPY'=>13
	];

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['currency, date', 'required'],
			['valid,type', 'numerical', 'integerOnly'=>true],
			['currency', 'length', 'max'=>10],
			['meta', 'safe'],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['id, currency, date, valid,type, meta', 'safe', 'on'=>'search'],
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
			'currency' => 'Currency',
			'date' => 'Date',
			'valid' => 'Valid',
			'meta' => 'Meta',
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
		$criteria->compare('currency', $this->currency, true);
		$criteria->compare('date', $this->date, true);
		$criteria->compare('valid', $this->valid);
		$criteria->compare('type', $this->type);
		$criteria->compare('meta', $this->meta, true);

		return new CActiveDataProvider($this, [
			'sort'=>[
				'defaultOrder'=>'date DESC',
			],
			'criteria'=>$criteria,
		]);
	}

	public static function updateExrate()
	{
		$url = 'https://www.ccf.border.gov.au/reference/production/change/today/';
		;
		if (!$c = @file_get_contents($url)) return false;

		if (preg_match('/(XCHGRATE\-P1\-EDCHNG\-\d+\.txt)/', $c, $m)) {
			preg_match_all('/(USD|CNY|HKD|EUR|GBP|NZD|CAD|NOK|SGD|DKK|INR|JPY) \d+ \d+\s+([\d\.]+)\s+(\d{8})\s+I/', file_get_contents($url . $m[1]), $rs);
			if(!empty($rs[1])){
				$date = date('Y-m-d');
				$c2k = array_flip(self::$currency_type);
				foreach($rs[1] as $k => $v){
					if(isset($c2k[$v])){
						if(Currency::model()->count('date=:date AND type=:t', [':date' => $date, ':t' => $c2k[$v]]) == 0){
							$model = new Currency;
							$model->currency = $rs[2][$k];
							$model->type = $c2k[$v];
							$model->date = $date;
							$model->save();
						}
					}
				}
			}
		}
	}
	
	public static function getExrate($date = '', $type=2)
	{
		if(empty($date)) $date = date('Y-m-d');
		$r = Currency::model()->find('`valid` = 1 AND `type` = :type AND `date` >= DATE_SUB(:d, INTERVAL 3 DAY) AND `date` <= :d ORDER BY `date` DESC', [':d' => $date,':type'=>$type]);
		if(empty($r)){
			switch($type){
				case 1:
					return [1, '0000-00-00'];
				case 2:
					return [0.66, '0000-00-00'];
				break;
				case 3:
					return [5, '0000-00-00'];
				break;
				case 4:
					return [5.6, '0000-00-00'];
				break;
				case 5:
					return [0.65, '0000-00-00'];
				break;
				case 6:
					return [0.55, '0000-00-00'];
				break;
				case 7:
					return [1.1, '0000-00-00'];
				break;
				case 8:
					return [0.9, '0000-00-00'];
				break;
				case 9:
					return [6.66, '0000-00-00'];
				break;
				case 10:
					return [1.1, '0000-00-00'];
				break;
				case 11:
					return [4.5, '0000-00-00'];
				case 12:
					return [54.40, '0000-00-00'];
				case 13:
					return [94.12, '0000-00-00'];
				break;
			}
		}else{
			return [$r->currency, $r->date];
		}
	}

	public static function rbaExrate($cur = 'USD'){
		$xml = simplexml_load_string(file_get_contents('https://www.rba.gov.au/rss/rss-cb-exchange-rates.xml'));
			foreach($xml->item as $k => $v){
			if(preg_match('/AU:\s+([\d\.]+)\s+(\w{3})\s+\=\s+1\s+AUD\s+(\d{4}-\d{2}-\d{2})/', $v->title, $r)){
				if($cur == $r[2]){
					return [$cur, $r[1], $r[3]];
				}
			}
		}
		return false;
	}
		
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Currency the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
