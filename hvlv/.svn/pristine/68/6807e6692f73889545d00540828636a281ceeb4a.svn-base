<?php

/**
 * This is the model class for table "warehouse_location_recommendation".
 *
 * The followings are the available columns in table 'warehouse_location_recommendation':
 * @property integer $id
 * @property string $ground_label
 * @property integer $level
 * @property integer $is_held
 * @property integer $is_oversize
 * @property integer $status
 */
class WarehouseLocationRecommendation extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'warehouse_location_recommendation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ground_label, status', 'required'),
			array('level, is_held, is_oversize, status', 'numerical', 'integerOnly'=>true),
			array('ground_label', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, ground_label, level, is_held, is_oversize, status,dpt_id', 'safe', 'on'=>'search'),
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
			'ground_label' => 'Ground Label',
			'level' => 'Level',
			'is_held' => 'Is Held',
			'is_oversize' => 'Is Oversize',
			'status' => 'Status',
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
		$criteria->compare('ground_label',$this->ground_label,true);
		$criteria->compare('level',$this->level);
		$criteria->compare('is_held',$this->is_held);
		$criteria->compare('is_oversize',$this->is_oversize);
		$criteria->compare('status',$this->status);
		$criteria->compare('dpt_id',$this->dpt_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WarehouseLocationRecommendation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
