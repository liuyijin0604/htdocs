<?php

class CalendarItem extends CActiveRecord
{

	public $no, $cust;

	public function tableName()
	{
		return 'calendar_item';
	}

	public function rules()
	{
		return array(
			array('date, op_id, ref, time, model, fid', 'safe'),
			array('id, date, op_id, ref, time, model, fid', 'safe', 'on' => 'search'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'date' => 'Date',
			'op_id' => 'OP',
			'ref' => 'Ref',
			'time' => 'Time',
			'model' => 'Model',
			'fid' => 'Task # or Job #',
		);
	}

	public function beforeSave()
	{
		if (empty($this->op_id)) {
			$this->op_id = Yii::app()->user->id;
		}

		return true;
	}

	public function afterFind()
	{
		if (!empty($this->model) && !empty($this->fid)) {
			if ($this->model == 'EdiJob') {
				$this->no = EdiJob::model()->findByPk($this->fid)->no;
				$this->cust = EdiJob::model()->findByPk($this->fid)->owner;
			} else if ($this->model == 'WmsTask') {
				$this->no = WmsTask::model()->findByPk($this->fid)->getNo();
				$this->cust = WmsTask::model()->findByPk($this->fid)->job->customer;
			}
		}
		if (empty($this->no) && !empty($this->fid)) {
			$this->no = $this->fid;
		}
	}

	public function search($pgn = true, $ps = 30, $ec = false)
	{
		$criteria = new CDbCriteria;
		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.date', $this->date, true);
		$criteria->compare('t.op_id', $this->op_id);
		$criteria->compare('t.ref', $this->ref, true);
		$criteria->compare('t.time', $this->time, true);
		$criteria->compare('t.model', $this->model);
		$criteria->compare('t.fid', $this->fid);

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

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsTask the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}