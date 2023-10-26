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
class CustomProcessTask extends TlaTask implements TlaTaskFunction
{
	public static $my_type = 40;


	public function getTaskContent()
	{
		return "this is a CustomProcessTask"."</br>".$this->comment;
	}

	
	public function assignUser($userId)
	{
		parent::assignUser($userId);
	}


	public function getOperationLink()
	{
		$imparcel = ImParcel::model()->findByPk($this->fid);
		return Yii::app()->createURL('customProcess/customList')."?ImParcel[ref]=".$imparcel->ref;
	}

	public function getTitle()
	{
		return "Custome Process ";
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
