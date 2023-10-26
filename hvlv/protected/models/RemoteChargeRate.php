<?php

/**
 * This is the model class for table "remote_charge_rate".
 *
 * The followings are the available columns in table 'remote_charge_rate':
 * @property string $id
 * @property string $rate_id
 * @property integer $chargecode_id
 * @property string $zone
 * @property string $zone_name
 * @property string $postcode
 * @property string $suburb
 * @property string $weight_lo
 * @property string $weight_hi
 * @property string $base
 * @property string $item
 * @property string $perkg
 * @property double $nkg
 * @property string $minimum
 * @property integer $gst
 * @property string $levy
 * @property string $min_incl
 */
class RemoteChargeRate extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'remote_charge_rate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('chargecode_id, gst', 'numerical', 'integerOnly'=>true),
			array('nkg', 'numerical'),
			array('rate_id', 'length', 'max'=>11),
			array('zone', 'length', 'max'=>20),
			array('zone_name, postcode, suburb', 'length', 'max'=>255),
			array('weight_lo, weight_hi, base, item, perkg, minimum, levy, min_incl', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, rate_id, chargecode_id, zone, zone_name, postcode, suburb, weight_lo, weight_hi, base, item, perkg, nkg, minimum, gst, levy, min_incl', 'safe', 'on'=>'search'),
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
			'rate_id' => 'Rate',
			'chargecode_id' => 'Chargecode',
			'zone' => 'Zone',
			'zone_name' => 'Zone Name',
			'postcode' => 'Postcode',
			'suburb' => 'Suburb',
			'weight_lo' => 'Weight Lo',
			'weight_hi' => 'Weight Hi',
			'base' => 'Base',
			'item' => 'Item',
			'perkg' => 'Perkg',
			'nkg' => 'Nkg',
			'minimum' => 'Minimum',
			'gst' => 'Gst',
			'levy' => 'Levy',
			'min_incl' => 'Min Incl',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('rate_id',$this->rate_id,true);
		$criteria->compare('chargecode_id',$this->chargecode_id);
		$criteria->compare('zone',$this->zone,true);
		$criteria->compare('zone_name',$this->zone_name,true);
		$criteria->compare('postcode',$this->postcode,true);
		$criteria->compare('suburb',$this->suburb,true);
		$criteria->compare('weight_lo',$this->weight_lo,true);
		$criteria->compare('weight_hi',$this->weight_hi,true);
		$criteria->compare('base',$this->base,true);
		$criteria->compare('item',$this->item,true);
		$criteria->compare('perkg',$this->perkg,true);
		$criteria->compare('nkg',$this->nkg);
		$criteria->compare('minimum',$this->minimum,true);
		$criteria->compare('gst',$this->gst);
		$criteria->compare('levy',$this->levy,true);
		$criteria->compare('min_incl',$this->min_incl,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RemoteChargeRate the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
