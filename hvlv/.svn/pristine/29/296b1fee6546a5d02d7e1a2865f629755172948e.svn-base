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
class CustomerServiceTask extends TlaTask implements TlaTaskFunction
{
	public static $my_type = 50;
		
	public function getTaskContent()
	{
		return "this is a CustomerServiceTask"."</br>".$this->comment;
	}

	
	public function assignUser()
	{
		parent::assignUser($userId);
	}


	public function getOperationLink()
	{
		return Yii::app()->createURL('importsMail/update')."?id=".$this->mdata['emailId'];
	}

	public function complete()
	{

	}

	public function getTitle()
	{
		return "CS ";
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
