<?php

/**
 * This is the model class for table "container_cartage".
 *
 * The followings are the available columns in table 'container_cartage':
 * @property string $id
 * @property string $org_id
 * @property string $task_id
 * @property time $container_load_time
 * @property string $booking_release_no
 * @property string $vessel_name
 * @property int $container_type
 * @property date $etd
 * @property time $start_receiving_time
 * @property time $cutoff_time
 * @property string $empty_depot_name
 * @property string $full_return_name
 * @property date $cargo_estimate_ready_date
 * @property int $if_fq
 * @property int $calendar_item_id
 * @property string $note
 * @property int $status
 * @property int $if_pra
 */
class ContainerCartage extends CActiveRecord
{

	public $ids, $container_no;

	public static $container_types = array(
		1 => '1 x 40HC',
		2 => '1 x 40GP',
		3 => '1 x 40RE',
		4 => '1 x 20GP',
		5 => '1 x 20RE',
	);

	public static $empty_depot_names = array(
		1 => 'ACFS e-link',
		2 => 'DPW Logistics',
		3 => 'MCS COOKS RIVER',
		4 => 'TYNE ACFS',
		5 => 'MCS RAIL SYSTEMS',
	);

	public static $full_return_names = array(
		1 => 'Patrick',
		2 => 'Hutchison',
		3 => 'DP World',
	);

	public static $if_fqs = array(
		0 => 'No',
		1 => 'Yes',
	);

	public static $if_pras = array(
		0 => 'No',
		1 => 'Yes',
	);

	public function tableName()
	{
		return 'container_cartage';
	}

	public function rules()
	{
		return array(
			array('org_id, task_id', 'required'),
			array('org_id, task_id, container_load_time, booking_release_no, vessel_name, etd, start_receiving_time, cutoff_time, empty_depot_name, full_return_name, container_type, cargo_estimate_ready_date, if_fq, calendar_item_id, note, if_pra, container_no', 'safe'),
			array('id, org_id, task_id, container_load_time, booking_release_no, vessel_name, etd, start_receiving_time, cutoff_time, empty_depot_name, full_return_name, container_type, cargo_estimate_ready_date, if_fq, calendar_item_id, note, if_pra, container_no', 'safe', 'on' => 'search'),
		);
	}

	public function relations()
	{
		return array(
			'org' => array(self::BELONGS_TO, 'Org', 'org_id'),
			'task' => array(self::BELONGS_TO, 'WmsTask', 'task_id'),
			'calendar_item' => array(self::BELONGS_TO, 'CalendarItem', 'calendar_item_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'org_id' => 'Org',
			'task_id' => 'Task',
			'container_load_time' => 'Container Load Time',
			'booking_release_no' => 'Booking Release No',
			'vessel_name' => 'Vessel Name',
			'etd' => 'ETD',
			'start_receiving_time' => 'Start Receiving Time',
			'cutoff_time' => 'Cut-off Time',
			'empty_depot_name' => 'Empty Depot Name',
			'full_return_name' => 'Full Return Name',
			'container_type' => 'Container Type',
			'cargo_estimate_ready_date' => 'Cargo Estimate Ready Date',
			'if_fq' => 'If FQ',
			'note' => 'Note',
			'if_pra' => 'PRA Done',
		);
	}

	public function getEmptyDepotName()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$container_types[$this->empty_depot_name]) ? '' : self::$empty_depot_names[$this->empty_depot_name]);
	}

	public function getFullReturnName()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$full_return_names[$this->full_return_name]) ? '' : self::$full_return_names[$this->full_return_name]);
	}

	public function getContainerType()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$container_types[$this->container_type]) ? '' : self::$container_types[$this->container_type]);
	}

	public function getOrg()
	{
		if (!empty($this->org)) {
			return $this->org->name;
		} else {
			return '';
		}
	}

	public function getTask()
	{
		if (!empty($this->task)) {
			return "<a href=\"" . Yii::app()->createUrl('wmsTask/update', ['id' => $this->task_id]) . "\" class=\"tab_link\" title=\"" . $this->task->getNo() . "\">" . $this->task->getNo() . "</a>";
		} else {
			return '';
		}
	}

	public function getIfFQ()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$if_fqs[$this->if_fq]) ? '' : self::$if_fqs[$this->if_fq]);
	}

	public function getIfPRA()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$if_pras[$this->if_pra]) ? '' : self::$if_pras[$this->if_pra]);
	}

	public function getContainerNo()
	{
		if (!empty($this->task->mdata['ctn_no'])) {
			return $this->task->mdata['ctn_no'];
		} else {
			return '';
		}
	}

	public function getPrint()
	{
		$v = '<a href="' . Yii::app()->createUrl('containerCartage/print', ['id' => $this->id]) . '" class="tab_link" title="Print ' . $this->id . '"><div style="background-position: -128px -576px" class="icon"></div></a>';
		if (!empty($this->mdata['printed'])) {
			$v .= '<span class="warn">Printed</span><br />';
		}

		$files = FileRepo::model()->findAll('type = :type AND status = 20 AND fid = :fid', [':type' => FileRepo::CONTAINER_CARTAGE_ATTACHMENT, ':fid' => $this->id]);
		if (!empty($files)) {
			$v .= '<span style="background: #66ff66; font-weight: bold;">Uploaded</span>';
		}

		return $v;
	}

	// public function beforeSave()
	// {
	// 	if (empty($this->calendar_item)) {
	// 		$calendar_item = new CalendarItem;
	// 		$calendar_item->date = date('Y-m-d', strtotime($this->container_load_time));
	// 		$calendar_item->op_id = Yii::app()->user->id;
	// 		$calendar_item->ref = '';
	// 		$calendar_item->time = date('H:i:s', strtotime($this->container_load_time));
	// 		$calendar_item->model = 'WmsTask';
	// 		$calendar_item->fid = $this->task_id;
	// 		$calendar_item->save();

	// 		$this->calendar_item_id = $calendar_item->id;
	// 	}

	// 	return true;
	// }

	public function beforeSave()
	{
		foreach (['container_load_time', 'start_receiving_time', 'cutoff_time'] as $v) {
			if ($this->{$v} == '') {
				$this->{$v} = '0000-00-00 00:00:00';
			}
		}

		foreach (['etd', 'cargo_estimate_ready_date'] as $v) {
			if ($this->{$v} == '') {
				$this->{$v} = '0000-00-00';
			}
		}

		if (!isset($this->status)) {
			$this->status = 1;
		}

		return true;
	}

	public function afterFind()
	{
		foreach (['container_load_time', 'start_receiving_time', 'cutoff_time'] as $v) {
			if ($this->{$v} == '0000-00-00 00:00:00') {
				$this->{$v} = '';
			}
		}

		foreach (['etd', 'cargo_estimate_ready_date'] as $v) {
			if ($this->{$v} == '0000-00-00') {
				$this->{$v} = '';
			}
		}
	}

	public function search()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('t.container_load_time', $this->container_load_time, true);
		$criteria->compare('t.booking_release_no', $this->booking_release_no, true);
		$criteria->compare('t.vessel_name', $this->vessel_name, true);
		$criteria->compare('t.container_type', $this->container_type, true);
		$criteria->compare('t.etd', $this->etd, true);
		$criteria->compare('t.start_receiving_time', $this->start_receiving_time, true);
		$criteria->compare('t.cutoff_time', $this->cutoff_time, true);
		$criteria->compare('t.empty_depot_name', $this->empty_depot_name, true);
		$criteria->compare('t.full_return_name', $this->full_return_name, true);
		$criteria->compare('t.cargo_estimate_ready_date', $this->cargo_estimate_ready_date, true);
		$criteria->compare('t.if_fq', $this->if_fq, true);
		$criteria->compare('t.if_pra', $this->if_pra, true);

		$with = [];
		if (!empty($this->org_id)) {
			$with[] = 'org';
			$criteria->addCondition('t.org_id = "' . $this->org_id . '" OR org.name LIKE "%' . $this->org_id . '%"');
		}

		if (!empty($this->ids)) {
			$criteria->addInCondition('t.id', $this->ids);
		}

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'CASE t.cutoff_time WHEN "0000-00-00 00:00:00" THEN 2 ELSE 1 END ASC, t.cutoff_time ASC',
			),
			'pagination' => false,
		));
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

}