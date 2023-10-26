<?php

/**
 * This is the model class for table "live_chat_detail".
 *
 * The followings are the available columns in table 'live_chat_detail':
 * @property integer $id
 * @property string $agent_email
 * @property string $started_timestamp
 * @property string $ended_timestamp
 * @property string $visitor_name
 * @property string $visitor_email
 * @property integer $visitor_contact_number
 */
class LiveChatDetail extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'live_chat_detail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('agent_email, started_timestamp, ended_timestamp, visitor_name, visitor_email', 'required'),
			array('visitor_contact_number', 'numerical', 'integerOnly'=>true),
			array('agent_email, visitor_name, visitor_email', 'length', 'max'=>200),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, agent_email, started_timestamp, ended_timestamp, visitor_name, visitor_email, visitor_contact_number', 'safe', 'on'=>'search'),
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
			'agent_email' => 'Agent Email',
			'started_timestamp' => 'Started Timestamp',
			'ended_timestamp' => 'Ended Timestamp',
			'visitor_name' => 'Visitor Name',
			'visitor_email' => 'Visitor Email',
			'visitor_contact_number' => 'Visitor Contact Number',
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
		$criteria->compare('agent_email',$this->agent_email,true);
		$criteria->compare('started_timestamp',$this->started_timestamp,true);
		$criteria->compare('ended_timestamp',$this->ended_timestamp,true);
		$criteria->compare('visitor_name',$this->visitor_name,true);
		$criteria->compare('visitor_email',$this->visitor_email,true);
		$criteria->compare('visitor_contact_number',$this->visitor_contact_number);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return LiveChatDetail the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
