<?php

/**
 * This is the model class for table "vehicle".
 *
 * The followings are the available columns in table 'vehicle':
 * @property string $id
 * @property integer $org_id
 * @property string $plate_no
 * @property string $vehicle_model
 * @property string $load_capacity
 * @property integer $has_tailgate
 * @property string $tailgate_width
 * @property string $tailgate_length
 * @property string $tailgate_weight
 * @property string $inner_length
 * @property string $inner_width
 * @property string $inner_height
 * @property string $inner_volume
 */
class Vehicle extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'vehicle';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, plate_no, vehicle_model, load_capacity, has_tailgate', 'required'),
			array('org_id, has_tailgate', 'numerical', 'integerOnly'=>true),
			array('plate_no', 'length', 'max'=>45),
			array('vehicle_model', 'length', 'max'=>50),
			array('load_capacity, tailgate_width, tailgate_length, tailgate_weight, inner_length, inner_width, inner_height', 'length', 'max'=>6),
			array('inner_volume', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, org_id, plate_no, vehicle_model, load_capacity, has_tailgate, tailgate_width, tailgate_length, tailgate_weight, inner_length, inner_width, inner_height, inner_volume', 'safe', 'on'=>'search'),
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
			'org' => [self::BELONGS_TO, 'Org', 'org_id']
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'org_id' => 'Org',
			'plate_no' => 'Plate No',
			'vehicle_model' => 'Vehicle Model',
			'load_capacity' => 'Load Capacity',
			'has_tailgate' => 'Has Tailgate',
			'tailgate_width' => 'Tailgate Width',
			'tailgate_length' => 'Tailgate Length',
			'tailgate_weight' => 'Tailgate Weight',
			'inner_length' => 'Inner Length',
			'inner_width' => 'Inner Width',
			'inner_height' => 'Inner Height',
			'inner_volume' => 'Inner Volume',
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
		$criteria->compare('org_id',$this->org_id);
		$criteria->compare('plate_no',$this->plate_no,true);
		$criteria->compare('vehicle_model',$this->vehicle_model,true);
		$criteria->compare('load_capacity',$this->load_capacity,true);
		$criteria->compare('has_tailgate',$this->has_tailgate);
		$criteria->compare('tailgate_width',$this->tailgate_width,true);
		$criteria->compare('tailgate_length',$this->tailgate_length,true);
		$criteria->compare('tailgate_weight',$this->tailgate_weight,true);
		$criteria->compare('inner_length',$this->inner_length,true);
		$criteria->compare('inner_width',$this->inner_width,true);
		$criteria->compare('inner_height',$this->inner_height,true);
		$criteria->compare('inner_volume',$this->inner_volume,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Vehicle the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
