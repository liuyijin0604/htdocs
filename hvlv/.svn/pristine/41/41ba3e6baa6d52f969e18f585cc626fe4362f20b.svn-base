<?php

/**
 * This is the model class for table "wms_task_operator".
 *
 * The followings are the available columns in table 'wms_task_operator':
 * @property int $id
 * @property int $op_id
 * @property int $status
 */

class WmsTaskOperator extends CActiveRecord
{

	public static $states = array(
		1 => 'Active',
		2 => 'Inactive',
	);

	public function tableName()
	{
		return 'wms_task_operator';
	}

	public function rules()
	{
		return array(
			array('op_id', 'required'),
			array('id, op_id, status', 'safe'),
			array('id, op_id, status', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
	{
		return array(
			'op' => array(self::BELONGS_TO, 'User', 'op_id'),
			'tasks' => array(self::HAS_MANY, 'WmsTask', ['op_id' => 'op_id'], 'order' => 'JSON_VALUE(tasks.meta, "$.rank") ASC'),
		);
	}

	public function getStatus()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$states[$this->status]) ? '' : self::$states[$this->status]);
	}

	public function getOpName()
	{
		if (!empty($this->op)) {
			return $this->op->getName();
		}
		return '';
	}

	public function treeList($options = [])
	{
		$ho = '';
		foreach ($options as $k => $v) {
			$ho .= ' ' . $k . '="' . $v . '"';
		}
		$r = '<ol' . $ho . '>';
		foreach ($this->tasks as $c) {
			if ($c->status >= 99) {
				continue;
			}
			$r .= '<li class="rowcol rowleft" id="' . $c->id . '" style="margin-bottom: 20px"><div class="rowcol rowleft">' . $c->no . '</div><div class="rowcol" style="color: red; width: 300px; max-width: 300px"><p>' . $c->ref . '</p></div><div class="rowcol" style="color: orange; width: 300px; max-width: 300px"><p>' . date('Y-m-d H:i', strtotime($c->due_time)) . '</p></div><div class="rowcol" style="color: blue; width: 300px; max-width: 300px"><p>' . $c->job->customer->name . '</p></div>';
			$r .= '</li>';
		}
		return $r . '</ol>';
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
			'op_id' => 'Operator Name',
			'status' => 'Stauts',
		);
	}

	public function search($pgn = true, $ps = 30)
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('op_id', $this->op_id);
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