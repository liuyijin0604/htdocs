<?php

/**
 * This is the model class for table "wms_task_object".
 *
 * The followings are the available columns in table 'wms_task_object':
 * @property int $id
 * @property string $name
 * @property int $type
 * @property int $status
 */

class WmsTaskObject extends CActiveRecord
{

	public static $types = array(
		10 => 'Input',
		20 => 'Check',
	);

	public static $states = array(
		1 => 'Active',
		2 => 'Inactive',
	);

	public function tableName()
	{
		return 'wms_task_object';
	}

	public function rules()
	{
		return array(
			array('name, type', 'required'),
			array('id, name, type, status', 'safe'),
			array('id, name, type, status', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function getType()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$types[$this->type]) ? '' : self::$types[$this->type]);
	}

	public function getStatus()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$states[$this->status]) ? '' : self::$states[$this->status]);
	}

	public function beforeSave()
	{
		if (empty($this->status)) {
			$this->status = 1;
		}
		return true;
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Object Name',
			'type' => 'Type',
			'status' => 'Status',
		);
	}

	public function search($pgn = true, $ps = 30)
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('type', $this->type);
		$criteria->compare('status', $this->status);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 't.id DESC',
			),
			'pagination' => $pgn ? array(
				'pageSize' => $ps,
			) : false,
		));
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}