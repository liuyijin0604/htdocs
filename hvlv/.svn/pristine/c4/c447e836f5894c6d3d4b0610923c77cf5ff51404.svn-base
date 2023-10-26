<?php

/**
 * This is the model class for table "deconsolidation".
 *
 * The followings are the available columns in table 'deconsolidation':
 * @property integer $id
 * @property integer $shipment_id
 * @property integer $cargo_process_id
 * @property integer $status
 * @property integer $depot
 * @property string $create_time
 * @property string $due_time
 * @property string $op_complete_time
 * @property string $warehouse_complete_time
 * @property string $task_complete_time
 * @property string $meta
 * @property integer $assigned_user
 * @property integer $error_check_user
 * @property string $error_check_complete_time
 * @property string $error_check_assign_time
 */
class Deconsolidation extends MetaModel
{
	public $connote;
	public $service;
	/**
	 * Constant declearation
	 */
	const STATUS_NEW = 1;
	const STATUS_WAREHOUSE_PROCESSING = 2;
	const STATUS_WAITING_ERROR_CHECK = 3;
	const STATUS_ERROR_CHECKING = 4;
	const STATUS_WAITING_SCAN_ALL = 5;
	const STATUS_COMPLETE = 99;
	const STATUS_INVOICED = 101;

	public static $state = [
		self::STATUS_NEW => 'New',
		self::STATUS_WAREHOUSE_PROCESSING => 'Warehouse Processing',
		self::STATUS_WAITING_ERROR_CHECK => 'Waiting Error Check',
		self::STATUS_ERROR_CHECKING => 'Error Checking',
		self::STATUS_WAITING_SCAN_ALL => 'Waiting Scan All',
		self::STATUS_COMPLETE => 'Complete',
		self::STATUS_INVOICED => 'Invoice Generated'
	];

	public static $warehouse = [
		106 => 'Sydney',
		218 => 'Melbourne',
		530 => 'Brisbane',
		811 => 'Perth'
	];

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'deconsolidation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time', 'required'),
			array('shipment_id, cargo_process_id, status, depot, assigned_user, error_check_user', 'numerical', 'integerOnly' => true),
			array('due_time, op_complete_time, warehouse_complete_time, task_complete_time, error_check_complete_time, error_check_assign_time, meta', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, shipment_id, cargo_process_id, status, depot, create_time, due_time, op_complete_time, warehouse_complete_time, error_check_complete_time, task_complete_time, meta, assigned_user, connote, service, error_check_user, error_check_assign_time', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'shipment' => [self::BELONGS_TO, 'ImParcel', 'shipment_id'],
			'cargo_process' => [self::BELONGS_TO, 'CargoProcess', 'cargo_process_id'],
			'user' => [self::BELONGS_TO, 'User', 'assigned_user'],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'shipment_id' => 'Shipment',
			'cargo_process_id' => 'Cargo Process',
			'status' => 'Status',
			'depot' => 'Depot',
			'create_time' => 'Create Time',
			'due_time' => 'Due Time',
			'op_complete_time' => 'Op Complete Time',
			'warehouse_complete_time' => 'Warehouse Complete Time',
			'error_check_complete_time' => 'Error Check Complete Time',
			'task_complete_time' => 'Task Complete Time',
			'meta' => 'Meta',
			'assigned_user' => 'Assigned User',
			'error_check_user' => 'Error Check User',
			'error_check_assign_time' => 'Error Check Assign Time',
		);
	}

	public function getAvailableUsers()
	{
		$systemSetting = SystemSetting::model()->findByAttributes(array('key' => 'deconsolidationUser'));
		$users = $systemSetting->mdata[$this->depot];
		$availableUsers = [];
		foreach ($users as $user => $isActive) {
			if (empty($isActive)) {
				continue;
			}
			$userId = explode(":", $user)[0];
			$userName = explode(":", $user)[1];
			$availableUsers[$userId] = $userName;
		}
		return $availableUsers;
	}

	public function getAssignedUser()
	{
		if (!empty($this->assigned_user)) {
			$assignedUser = User::model()->findByPk($this->assigned_user);
			if (!empty($assignedUser)) {
				return $assignedUser->fname . " " . $assignedUser->lname;
			}
		}
		return 'N/A';
	}

	public function getServiceType()
	{
		if (!empty($this->shipment->consol->service)) {
			return Consol::$services[$this->shipment->consol->service];
		} else {
			return '';
		}
	}

	public function getDueDate()
	{
		if (!empty($this->shipment->consol)) {
			if ($this->shipment->consol->service == Consol::AIRCONSOL) {
				return HolidayHelper::getDateByBusinessDays($this->shipment->consol->pod, $this->create_time, 2, true) . " 23:59:59" ;
			} else {
				return HolidayHelper::getDateByBusinessDays($this->shipment->consol->pod, $this->shipment->consol->mdata['ContainerUnloadDate'], 3, true) . " 23:59:59";
			}
		}
		return 'N/A';
	}

	public function getDueDateColor()
	{
		if ($this->status == self::STATUS_COMPLETE) {
			if (empty($this->task_complete_time)) {
				return '';
			} else {
				if (strtotime($this->task_complete_time) <= strtotime($this->due_time)) {
					return 'column_green';
				} else {
					return 'column_red_1';
				}
			}
		} else {
			if (strtotime(date('Y-m-d H:i:s')) > strtotime($this->due_time)) {
				return 'column_red_1';
			}
			return '';
		}
	}

	public function getErrorColor()
	{
		return 'column_red_1';
	}

	public function getWdtConsolNo()
	{
		$postFix = substr($this->shipment->ref, 6);
		return 'WDT' . $postFix;
	}

	public function getWdtConsolId()
	{
		$consol = ImcoConsol::model()->findByAttributes(array('no' => $this->getWdtConsolNo()));
		return !empty($consol) ? $consol->id : 0;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search($order = 'due', $error_page = false, $error_checking_page = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;
		$with = array(
			'shipment',
			'shipment.consol'
		);
		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.shipment_id', $this->shipment_id);
		$criteria->compare('t.cargo_process_id', $this->cargo_process_id);
		$criteria->compare('t.status', $this->status);
		$criteria->compare('t.depot', $this->depot);
		$criteria->compare('t.create_time', $this->create_time, true);
		$criteria->compare('t.op_complete_time', $this->op_complete_time, true);
		$criteria->compare('t.warehouse_complete_time', $this->warehouse_complete_time, true);
		$criteria->compare('t.assigned_user', $this->assigned_user);

		if (!empty($this->connote)) {
			$criteria->compare('shipment.hbn', $this->connote);
		}

		if (!empty($this->service)) {
			$criteria->compare('consol.service', $this->service);
		}

		if ($error_page) {
			$criteria->addCondition('json_value(t.meta, "$.errors") IS NOT NULL AND (t.error_check_user IS NULL OR t.error_check_user = 0) AND t.error_check_complete_time IS NULL');
		}

		if ($error_checking_page) {
			$criteria->addCondition('json_value(t.meta, "$.errors") IS NOT NULL AND (t.error_check_complete_time IS NULL OR t.error_check_complete_time = "0000-00-00 00:00:00") AND (t.error_check_user IS NOT NULL AND t.error_check_user != 0)');
		}		
		
		$criteria->with = $with;

		$sort = new CSort();
		$sort->attributes = [
			'service' => [
				'asc' => 'consol.service',
				'desc' => 'consol.service DESC'
			],
			'due_time' => [
				'asc' => 'due_time',
				'desc' => 'due_time DESC'
			],
			'create_time' => [
				'asc' => 'create_time',
				'desc' => 'create_time DESC'
			]
		];

		if ($order == 'due_time') {
			$sort->defaultOrder = 'due_time ASC';
		} else {
			$sort->defaultOrder = 'create_time DESC';
		}
		

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => $sort,
			'pagination' => array(
				'pageSize' => 20,
			)
		)
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Deconsolidation the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}