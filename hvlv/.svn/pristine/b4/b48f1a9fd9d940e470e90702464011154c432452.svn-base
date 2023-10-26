<?php

/**
 * This is the model class for table "auto_command_log".
 *
 * The followings are the available columns in table 'auto_command_log':
 * @property integer $id
 * @property string $command_name
 * @property string $function_name
 * @property integer $type
 * @property string $time
 */
class AutoCommandLog extends CActiveRecord
{
	public const TYPE_START = 1;
	public const TYPE_COMPLETE = 2;
	public const TYPE_FAILED = 3;

	public static $types = [
		self::TYPE_COMPLETE => 'Success',
		self::TYPE_FAILED => 'Fail',
	];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'auto_command_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('command_name, function_name, time', 'required'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('command_name, function_name', 'length', 'max'=>150),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, command_name, function_name, type, time', 'safe', 'on'=>'search'),
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
			'function_name' => 'Function Name',
			'type' => 'Type',
			'time' => 'Time',
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
		$criteria->compare('function_name',$this->function_name,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('time',$this->time,true);

		$sort = new CSort();
		$sort->defaultOrder = '`id` DESC';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => $sort,
			'pagination' => [
				'pageSize' => 50,
			],
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AutoCommandLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function afterFind()
	{
		if ($this->type == self::TYPE_START && $this->time < date('Y-m-d H:i:s', strtotime("-60 minutes"))) {
			$this->type = self::TYPE_FAILED;
			$this->update();
		}
	}

	public function getColColor()
	{
		if ($this->type == self::TYPE_COMPLETE) {
			return "column_green";
		} else {
			if ($this->time < date('Y-m-d H:i:s', strtotime("-60 minutes"))) {
				return "column_red_1";
			}
		}
		return "";
	}
}
