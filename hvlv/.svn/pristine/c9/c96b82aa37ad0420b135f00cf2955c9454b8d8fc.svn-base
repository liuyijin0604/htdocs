<?php
class WmsTaskSort extends CActiveRecord
{

	public function tableName()
	{
		return 'wms_task_sort';
	}

	public function rules()
	{
		return array(
			array('task_id, prod_id, qty, sort_qty', 'required'),
			array('id, task_id, prod_id, qty, sort_qty', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
	{
		return array(
			'task' => array(self::BELONGS_TO, 'WmsTask', 'task_id'),
			'prod' => array(self::BELONGS_TO, 'WmsProd', 'prod_id'),
		);
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}