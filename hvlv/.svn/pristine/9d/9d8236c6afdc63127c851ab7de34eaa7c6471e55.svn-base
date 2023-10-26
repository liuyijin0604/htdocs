<?php

/**
 * This is the model class for table "auto_commands".
 *
 * The followings are the available columns in table 'auto_commands':
 * @property integer $id
 * @property string $command_name
 * @property string $func_name
 * @property integer $day
 * @property integer $week_day
 * @property integer $hour
 * @property integer $minute
 * @property integer $type
 * @property integer $hour_interval
 * @property integer $minute_interval
 */
class AutoCommands extends CActiveRecord
{
	const TYPE_TIME_FIXED = 1;
	const TYPE_TIME_INTERVAL = 2;

	public $types = [
		self::TYPE_TIME_FIXED => 'Fixed',
		self::TYPE_TIME_INTERVAL => 'Interval'
	];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'auto_commands';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('command_name, func_name, type', 'required'),
			array('day, week_day, hour, minute, type, hour_interval, minute_interval', 'numerical', 'integerOnly'=>true),
			array('command_name, func_name', 'length', 'max'=>150),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, command_name, func_name, day, week_day, hour, minute, type, hour_interval, minute_interval', 'safe', 'on'=>'search'),
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
			'command_name' => 'Command Name',
			'func_name' => 'Func Name',
			'day' => 'Day',
			'week_day' => 'Week Day',
			'hour' => 'Hour',
			'minute' => 'Minute',
			'type' => 'Type',
			'hour_interval' => 'Hour Interval',
			'minute_interval' => 'Minute Interval'
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
		$criteria->compare('command_name',$this->command_name,true);
		$criteria->compare('func_name',$this->func_name,true);
		$criteria->compare('day',$this->day);
		$criteria->compare('week_day',$this->week_day);
		$criteria->compare('hour',$this->hour);
		$criteria->compare('minute',$this->minute);
		$criteria->compare('type', $this->type);
		$criteria->compare('hour_interval', $this->hour_interval);
		$criteria->compare('minute_interval', $this->minute_interval);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => false,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AutoCommands the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
