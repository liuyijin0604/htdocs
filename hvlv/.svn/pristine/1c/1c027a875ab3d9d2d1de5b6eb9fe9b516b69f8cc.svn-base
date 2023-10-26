<?php

/**
 * This is the model class for table "holidays".
 *
 * The followings are the available columns in table 'holidays':
 * @property integer $id
 * @property string $pod
 * @property string $date
 * @property string $holiday_name
 * @property string $information
 * @property string $jurisdiction
 * @property string $year
 */
class Holidays extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'holidays';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, holiday_name, information, jurisdiction, year', 'required'),
			array('date, holiday_name, jurisdiction, year', 'length', 'max'=>45),
			array('information', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pod, date, holiday_name, information, jurisdiction, year', 'safe', 'on'=>'search'),
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
			'pod' => 'Pod',
			'date' => 'Date',
			'holiday_name' => 'Holiday Name',
			'information' => 'Information',
			'jurisdiction' => 'Jurisdiction',
			'year' => 'Year',
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
		$criteria->compare('pod',$this->pod,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('holiday_name',$this->holiday_name,true);
		$criteria->compare('information',$this->information,true);
		$criteria->compare('jurisdiction',$this->jurisdiction,true);
		$criteria->compare('year',$this->year,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Holidays the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
