<?php

/**
 * This is the model class for table "tla_task".
 *
 * The followings are the available columns in table 'tla_task':
 * @property integer $id
 * @property integer $type
 * @property string $task_no
 * @property string $comment
 * @property integer $dpt_id
 * @property integer $status
 * @property integer $user_id
 * @property string $create
 * @property string $meta
 */
class WarehouseTask extends TlaTask implements TlaTaskFunction
{
	public static $my_type = 60;
		
	public function getTaskContent()
	{
		return "";
	}

	
	public function assignUser($userId)
	{
		parent::assignUser($userId);
	}


	public function getOperationLink()
	{
		return "";
	}

	public function complete()
	{

	}

	public function close()
	{
		parent::close();
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TlaTask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
