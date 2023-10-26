<?php

/**
 * This is the model class for table "hs_poc".
 *
 * The followings are the available columns in table 'hs_poc':
 * @property string $id
 * @property string $hsid
 * @property string $poc
 * @property double $price
 * @property integer $inactive
 */
class HsPoc extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hs_poc';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hsid, poc', 'required'),
			array('price', 'safe'),
			array('inactive', 'numerical', 'integerOnly'=>true),
			array('price', 'numerical'),
			array('hsid', 'length', 'max'=>11),
			array('poc', 'length', 'max'=>5),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, hsid, poc, price, inactive', 'safe', 'on'=>'search'),
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
			'hs' => array(self::BELONGS_TO, 'HS', 'hsid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'hsid' => 'Hsid',
			'poc' => 'Poc',
			'price' => 'Price',
			'inactive' => 'Inactive',
		);
	}

	public static function isActive($hs, $poc){
		$c = self::model()->with('hs')->count('hs.hs = :hs AND poc = :poc AND inactive = 1', [':hs' => $hs, ':poc' => $poc]);
		return empty($c);
	}

	public static function getTariff($hs, $exc, $v, $wt){
		if(!self::isActive($hs, $exc->code)) return false;
		$r = self::model()->with('hs')->find('hs.hs = :hs AND poc = :poc AND inactive = 0', [':hs' => $hs, ':poc' => $exc->code]);
		$v = floatval($v);
		if(empty($r)){
			$h = HS::model()->find('hs = :hs', [':hs' => $hs]);
			if(empty($h)) return [$v, 0.15, $v * 0.15];
			$p = $h->price;
		}else{
			$h = $r->hs;
			$p = $r->price;
		}
		
		if($exc->mdata['dtype'] == 'cc' || empty($hs)) return [$v, $h->rate, $v * $h->rate];

		if($h->uc == '035' && !($hs == '01010700' && !empty($exc->mdata['ftc']) && $exc->mdata['ftc'] == 2)) $p = $p * $wt; // KG

		if((!empty($exc->mdata['ftc']) || $hs != '01010700') && $p > 0  && $v > ($p / 2) && $v < ($p * 2)){
			$v = $p;
		}

		return [$v, $h->rate, $v * $h->rate];
	}

	public static function getQuota($hs, $poc){
		$r = self::model()->with('hs')->find('hs.hs = :hs AND poc = :poc AND inactive = 0', [':hs' => $hs, ':poc' => $poc]);
		if(empty($r)){
			$h = HS::model()->find('hs = :hs', [':hs' => $hs]);
			if(empty($h)) return 0;
			return $h->price;
		}
		return $r->price;
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('hsid',$this->hsid,true);
		$criteria->compare('poc',$this->poc,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('inactive',$this->inactive);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return HsPoc the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
