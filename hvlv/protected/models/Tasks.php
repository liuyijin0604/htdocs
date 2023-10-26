<?php

/**
 * This is the model class for table "tasks".
 *
 * The followings are the available columns in table 'tasks':
 * @property string $id
 * @property integer $type
 * @property string $driver_id
 * @property string $agent_id
 * @property string $pickup_time_from
 * @property string $pickup_time_to
 * @property integer $repeat
 * @property string $notes
 * @property string $added_time
 * @property integer $active
 */
class Tasks extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tasks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type,repeat, active', 'numerical', 'integerOnly'=>true),
			array('driver_id, agent_id', 'length', 'max'=>10),
            array('pickup_time_from, pickup_time_to', 'length', 'max'=>10),
			array('notes', 'length', 'max'=>500),
			array('added_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, driver_id, agent_id, pickup_time_from, pickup_time_to, repeat, notes, added_time, active', 'safe', 'on'=>'search'),
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
            'driver' => array(self::BELONGS_TO, 'User', 'driver_id'),
            'agent' => array(self::BELONGS_TO, 'Org', 'agent_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'driver_id' => 'Driver',
			'agent_id' => 'Agent',
			'pickup_time_from' => 'Pickup Time From',
			'pickup_time_to' => 'Pickup Time To',
			'repeat' => 'Repeat',
			'notes' => 'Notes',
			'added_time' => 'Added Time',
			'active' => 'Active',
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
		$criteria->compare('type',$this->type);
		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('agent_id',$this->agent_id,true);
		$criteria->compare('pickup_time_from',$this->pickup_time_from,true);
		$criteria->compare('pickup_time_to',$this->pickup_time_to,true);
		$criteria->compare('repeat',$this->repeat);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('added_time',$this->added_time,true);
		$criteria->compare('active',$this->active);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public static function driverList(){
        $rs = User::model()->findAll('id > 0 AND type = 100'); // type 100 means driver type id
        $a = array();
        foreach($rs as $v){
            $a[$v['id']] = $v['fname'] . ' ' . $v['lname'];
        }
        return $a;
    }

    public static function agentList(){
        $rs = Org::model()->findAll('id > 0 AND type IN (60,65)'); // type 60,65 means agent type id
        $a = array();
        foreach($rs as $v){
            $a[$v['id']] = $v['name'];
        }
        return $a;
    }

    public function beforeSave() {
        if ($this->isNewRecord)
            $this->added_time = new CDbExpression('NOW()');

        return parent::beforeSave();
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Tasks the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


}
