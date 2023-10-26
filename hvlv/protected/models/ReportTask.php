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
class ReportTask extends ProcessTask
{
	public $invNo,$ref;
	public static $my_type = 120;

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return parent::relations() + array(
			'process' => [self::BELONGS_TO, 'MqProcess', 'fid']
		);
	}

	public function assignUser($userId=null)
	{
		parent::assignUser([$userId]);
	}

	public function getGoToLinkText()
	{
		return "Check Report";
	}

	public function getTaskContent()
	{
		return "Report: ".$this->getLinkObj()->des."</br>Status:".$this->getLinkObj()->getStatus();
	}

	public function close()
	{
		$this->noNotice= true;
		parent::close();
	}

	public function done()
	{
	}

	public function getTitle()
	{
		return "Report ";
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
