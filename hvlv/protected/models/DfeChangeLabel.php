<?php

/**
 * This is the model class for table "dfe_change_label".
 *
 * The followings are the available columns in table 'dfe_change_label':
 * @property integer $id
 * @property string $dfe_ref
 * @property integer $sn
 * @property string $allied_ref
 * @property integer $pid
 * @property string $create
 */
class DfeChangeLabel extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dfe_change_label';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dfe_ref, sn, allied_ref, pid, create', 'required'),
			array('sn, pid', 'numerical', 'integerOnly'=>true),
			array('dfe_ref, allied_ref', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, dfe_ref, sn, allied_ref, pid, create', 'safe', 'on'=>'search'),
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
			'imparcel' => [self::BELONGS_TO, 'ImParcel', 'pid'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'dfe_ref' => 'Dfe Ref',
			'sn' => 'pack number',
			'allied_ref' => 'New Ref',
			'pid' => 'Pid',
			'create' => 'Create',
			'user_id' => 'By User'
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
		$criteria->compare('dfe_ref',$this->dfe_ref,true);
		$criteria->compare('sn',$this->sn);
		$criteria->compare('allied_ref',$this->allied_ref,true);
		$criteria->compare('pid',$this->pid);
		$criteria->compare('create',$this->create,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DfeChangeLabel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
