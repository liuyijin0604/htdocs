<?php

/**
 * This is the model class for table "wms_charge_rate".
 *
 * The followings are the available columns in table 'wms_charge_rate':
 * @property string $id
 * @property string $chargecode_id
 * @property string $weight_lo
 * @property string $weight_hi
 * @property string $base
 * @property string $perkg
 * @property string $minimum
 */
class WmsChargeRate extends CActiveRecord
{

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_charge_rate';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('chargecode_id, weight_lo, weight_hi, base, perkg, minimum', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, chargecode_id, weight_lo, weight_hi, base, perkg, minimum', 'safe', 'on' => 'search'),
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
			'chargecode_id' => 'Charge Code',
			'weight_lo' => 'Wt. Lo',
			'weight_hi' => 'Wt. Hi',
			'base' => 'Base',
			'perkg' => 'Perkg',
			'minimum' => 'Minimum',
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
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.weight_lo',$this->weight_lo,true);
		$criteria->compare('t.weight_hi',$this->weight_hi,true);
		$criteria->compare('t.base',$this->base,true);
		$criteria->compare('t.perkg',$this->perkg,true);
		$criteria->compare('t.minimum',$this->minimum,true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ZoneRate the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}