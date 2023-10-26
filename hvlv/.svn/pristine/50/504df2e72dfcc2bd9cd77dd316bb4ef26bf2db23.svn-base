<?php

/**
 * This is the model class for table "zone_rate".
 *
 * The followings are the available columns in table 'zone_rate':
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
class ZoneRateBk extends CActiveRecord{

	public static $gsts = array(
		1 => 'Inc GST',
		2 => 'Ex GST',
		3 => 'GST Free',
	);
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'zone_rate_bk';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('rate_id, chargecode_id,zone, zone_name, weight_lo, weight_hi, base, item, perkg, nkg, minimum,min_incl, levy', 'safe'),
			array('gst', 'numerical', 'integerOnly'=>true),
			array('rate_id,chargecode_id', 'length', 'max'=>11),
			array('zone', 'length', 'max'=>20),
			array('weight_lo, weight_hi, base, item, perkg, minimum,min_incl, levy', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, rate_id,chargecode_id, zone, zone_name, weight_lo, weight_hi, base, item, perkg, nkg, minimum,min_incl, gst, levy', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'orgrate' => array(self::BELONGS_TO, 'OrgRate', 'rate_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
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
			'min_incl' => 'Minimum Include',
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
	public function search(){
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('rate_id',$this->rate_id);
		$criteria->compare('zone',$this->zone,true);
		$criteria->compare('zone_name',$this->zone_name,true);
		$criteria->compare('weight_lo',$this->weight_lo,true);
		$criteria->compare('weight_hi',$this->weight_hi,true);
		$criteria->compare('base',$this->base,true);
		$criteria->compare('item',$this->item,true);
		$criteria->compare('perkg',$this->perkg,true);
		$criteria->compare('nkg',$this->nkg,true);
		$criteria->compare('minimum',$this->minimum,true);
		$criteria->compare('min_incl',$this->min_incl,true);
		$criteria->compare('gst',$this->gst);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}



    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZoneRate the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
