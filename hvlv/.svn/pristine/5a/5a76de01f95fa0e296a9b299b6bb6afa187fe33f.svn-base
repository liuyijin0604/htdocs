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
class EmailTask extends TlaTask implements TlaTaskFunction
{

	public static $my_type = 10;

	public function getTaskContent()
	{
		if(!empty($this->getLinkObj()))
		{
			return "From:&nbsp;".$this->getLinkObj()->from_email."</br>".CHtml::tag('div', ['title'=>strip_tags($this->getLinkObj()->to_email)], "To:&nbsp;&nbsp;".mb_substr(strip_tags($this->getLinkObj()->to_email), 0, 25))."</br>"."Subject:&nbsp;".$this->getLinkObj()->subject."</br></br>". CHtml::tag('div', ['title'=>strip_tags($this->getLinkObj()->plain_body)], mb_substr(strip_tags($this->getLinkObj()->plain_body), 0, 25))."</br>";
		}else
		{
			return "";
		}
	}

	
	public function assignUser($userId)
	{
		parent::assignUser($userId);
	}


	public function getOperationLink()
	{
		return Yii::app()->createURL('importsMail/update')."?id=".$this->fid;
	}

	public function getTitle()
	{
		return "Email ";
	}

	public function complete()
	{

	}

	public function close()
	{
		$this->getLinkObj()->close();
		//parent::close();
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
