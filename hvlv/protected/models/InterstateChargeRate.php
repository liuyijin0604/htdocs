<?php

/**
 * This is the model class for table "interstate_charge_rate".
 *
 * The followings are the available columns in table 'interstate_charge_rate':
 * @property integer $id
 * @property integer $departure_depot
 * @property integer $destination_depot
 * @property integer $org_id
 * @property string $weight_low
 * @property string $weight_high
 * @property string $base
 * @property string $item
 * @property string $perkg
 * @property string $per_pallet
 * @property string $minimum
 * @property integer $type
 * @property string $start_date
 * @property string $gst
 */
class InterstateChargeRate extends CActiveRecord
{
	const CHARGE_BY_WEIGHT = 1;
	const CHARGE_BY_PALLET = 2;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'interstate_charge_rate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('weight_low, weight_high, start_date', 'required'),
			array('departure_depot, destination_depot, org_id, type, gst', 'numerical', 'integerOnly'=>true),
			array('weight_low, weight_high, base, item, perkg, per_pallet, minimum', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, departure_depot, destination_depot, org_id, weight_low, weight_high, base, item, perkg, per_pallet, minimum, type, start_date, gst', 'safe', 'on'=>'search'),
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
			'departure_depot' => 'Departure Depot',
			'destination_depot' => 'Destination Depot',
			'org_id' => 'Org',
			'weight_low' => 'Weight Low',
			'weight_high' => 'Weight High',
			'base' => 'Base',
			'item' => 'Item',
			'perkg' => 'Perkg',
			'per_pallet' => 'Per Pallet',
			'minimum' => 'Minimum',
			'type' => 'Type',
			'start_date' => 'Start Date',
			'gst' => 'Gst',
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
		$criteria->compare('departure_depot',$this->departure_depot);
		$criteria->compare('destination_depot',$this->destination_depot);
		$criteria->compare('org_id',$this->org_id);
		$criteria->compare('weight_low',$this->weight_low,true);
		$criteria->compare('weight_high',$this->weight_high,true);
		$criteria->compare('base',$this->base,true);
		$criteria->compare('item',$this->item,true);
		$criteria->compare('perkg',$this->perkg,true);
		$criteria->compare('per_pallet',$this->per_pallet,true);
		$criteria->compare('minimum',$this->minimum,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('gst', $this->gst, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InterstateChargeRate the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
