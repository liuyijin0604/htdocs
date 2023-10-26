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
class CargoProcessTask extends TlaTask implements TlaTaskFunction
{
	public static $my_type = 30;


	public function getTaskContent()
	{
		return "this is a CargoProcessTask"."</br>".$this->comment;
	}

	
	public function assignUser($userId)
	{
		parent::assignUser($userId);
	}


	public function getOperationLink()
	{
		$cargoProcess = CargoProcess::model()->findByPk($this->fid);
		return Yii::app()->createURL('topCourierService/cargoList')."?pod_id=".$cargoProcess->dpt_id."&cargo_type=".$cargoProcess->type."&CargoProcess[ref]=".$cargoProcess->shipment->ref;
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
