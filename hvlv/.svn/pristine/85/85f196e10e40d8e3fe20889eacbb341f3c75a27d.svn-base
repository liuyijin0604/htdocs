<?php

/**
 * This is the model class for table "wms_location_ground_label".
 *
 * The followings are the available columns in table 'wms_location_ground_label':
 * @property integer $id
 * @property integer $location_id
 * @property string $ground_label
 * @property string $ground_label_in_time
 */
class WmsLocationGroundLabel extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_location_ground_label';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('location_id, ground_label, ground_label_in_time', 'required'),
			array('location_id', 'numerical', 'integerOnly'=>true),
			array('ground_label', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, location_id, ground_label, ground_label_in_time', 'safe', 'on'=>'search'),
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
			'location' => array(self::BELONGS_TO, 'WmsLocation', 'location_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'location_id' => 'Location',
			'ground_label' => 'Ground Label',
			'ground_label_in_time' => 'Ground Label In Time',
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
		$criteria->compare('location_id',$this->location_id);
		$criteria->compare('ground_label',$this->ground_label,true);
		$criteria->compare('ground_label_in_time',$this->ground_label_in_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsLocationGroundLabel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
