<?php

/**
 * This is the model class for table "wms_task".
 *
 * The followings are the available columns in table 'wms_task':
 * @property string $id
 * @property string $job_id
 * @property string $op_id
 * @property integer $type
 * @property integer $status
 * @property integer $billed
 * @property string $due_time
 * @property string $schd_time
 * @property string $start_time
 * @property string $compl_time
 * @property integer $bwf  // claim below
 * @property string $meta
 * @property integer $dpt_id
 */
class WmsTask extends CActiveRecord
{
	public static $types = array(
		1010 => 'Pallets In',
		1020 => 'Bulk In',
		1030 => 'Container Unload',
		1110 => 'Put Away',
		//2010 => 'Pallet Out',
		//2020 => 'Bulk Out',
		2030 => 'Container Load',
		2040 => 'Pack PMC',
		2110 => 'Pickup',
		2120 => 'Delivery',
		3010 => 'Pick Pallet',
		3020 => 'Pick Carton',
		3030 => 'Pick Unit',
		3040 => 'From Office',
		3050 => 'Return Check',
		3060 => 'Re-delivery',
		3210 => 'Pack Order',
		4010 => 'Stock Take',
		4020 => 'Stock Relocate',
		4030 => 'Discard',
		5010 => 'CG Delivery',
		6010 => 'Adhoc Task',
		6020 => 'Request',
		6030 => 'Process',
		6040 => 'QA',
		7010 => 'Return',
		7020 => 'Restock',
		self::TYPE_Split_Delivery_SF=>'Split Delivery SF',
		self::TYPE_Split_Delivery => 'Split Delivery',
	);

	const TYPE_PICK_PALLET = 3010;
	const TYPE_PICK_CARTON = 3020;
	const TYPE_PICK_UNIT = 3030;
	const TYPE_CONTAINER_LOAD = 2030;
	const TYPE_ADHOC_TASK = 6010;
	const TYPE_RETURN = 7010;
	const TYPE_RESTOCK = 7020;
	const TYPE_PACK_ORDER = 3210;
	const TYPE_DELIVERY = 2120;
	const TYPE_Split_Delivery_SF = 8010;
	const TYPE_Split_Delivery = 8030;
	// const TYPE_Split_Sorting = 8040;
	const type_pallet_in = 1010;
	
	
	const OT_ID_3PL = 10;



	public static $types_ex = array(
		10 => 'In',
		20 => 'out',
	);

	public static $itemsAction_Options = array(
		10 => 'Wrapping Combine Carton',
		20 => 'Combine Carton',
		30 => 'Pick Accessories from Carton',
		40 => 'FBA label',
		50 => 'Check Carton',
	);

	public static $palletsAction_Options = array(
		10 => 'Palletize',
		20 => 'Wrap',
	);

	public static $types_client = array(
		1030 => 'Container Upload',
		1010 => 'Stock In',
		1020 => 'Bulk In',
		3010 => 'Pick Pallet',
		3020 => 'Pick Carton',
		3030 => 'Pick Unit',
		3050 => 'Return Check',
		3060 => 'Re-deliver',
		4030 => 'Discard',
		2110 => 'Pick Up',
		self::TYPE_Split_Delivery => 'Split Delivery',
	);

	public static $types_return = array(
		1 => 'Courier RTS',
		2 => 'Customer Return',
	);

	public static $states = array(
		self::STATUS_NEW => 'New',
		self::STATUS_SCHEDULED => 'Scheduled',
		self::STATUS_WIP => 'WIP',
		self::STATUS_HOLD => 'Hold',
		self::STATUS_COMPLETED => 'Completed',
		self::STATUS_CANCELLED => 'Cancelled',
	);
	const STATUS_NEW = 10;
	const STATUS_SCHEDULED = 20;
	const STATUS_WIP = 30;
	const STATUS_HOLD = 40;
	const STATUS_COMPLETED = 99;
	const STATUS_CANCELLED = 100;

	// show in hvlv for 3pl
	public static $states_3pl = array(
		5 => 'Pending',
		10 => 'New',
		20 => 'Scheduled',
		30 => 'WIP',
		self::STATUS_HOLD => 'Hold',
		90 => 'Packed',
		99 => 'Completed',
		100 => 'Cancelled',
	);

	// show in pcaw for most clients
	public static $states_client = array(
		10 => 'New',
		20 => 'Confirmed',
		30 => 'WIP',
		self::STATUS_HOLD => 'Hold',
		99 => 'Completed',
		100 => 'Cancelled',
	);

	// biophysics
	public static $states_client_bio = array(
		10 => 'Backorder',
		20 => 'Order Received',
		30 => 'Packing in Progress',
		self::STATUS_HOLD => 'On Hold',
		99 => 'Shipped',
		100 => 'Cancelled',
	);
	public static $states_client_inbound = array(
		10 => 'Not Ready to Process',
		20 => 'Ready to Process',
		30 => 'Received In Progress',
		self::STATUS_HOLD => 'On Hold',
		99 => 'Completed',
		100 => 'Cancelled',
	);

	// show in pcaw for easyship
	public static $states_easyship = array(
		5 => 'Pending',
		10 => 'New',
		20 => 'Confirmed',
		30 => 'WIP',
		self::STATUS_HOLD => 'Hold',
		90 => 'Packed',
		99 => 'Completed',
	);

	// show in dash 3pl adhoc
	public static $states_adhoc = array(
		10 => 'New',
		31 => 'WIP Completed',
		32 => 'QA Completed',
		99 => 'Completed',
		100 => 'Cancelled',
	);

	// show in pcaw for return
	public static $states_return = array(
		10 => 'New',
		20 => 'Arrive Warehouse',
		30 => 'In Processing',
		99 => 'Complete',
	);

	const WMS_TASK_RETURN_STATUS_NEW = 10;
	const WMS_TASK_RETURN_STATUS_ARRIVE_WAREHOUSE = 20;
	const WMS_TASK_RETURN_STATUS_IN_PROCESSING = 30;
	const WMS_TASK_RETURN_STATUS_COMPLETE = 99;

	public static $bwfs = array(
		2, // completed by cron
		4, // do not generate invoice
		8, // not count in export dashboard
		16, // invisible to customer
		32, // cg delivery trans
		64, // waiting lodge in courier
		128, // rts detected
		256, // shopify and cin7 and easyship sync
		512, // stock in have comparison
	);

	public static $check_services = array(
		1 => 'Check Outter Box',
		2 => 'Check Seal',
		3 => 'Check Inside Product is Broker or Used if it\'s OPEN',
		10 => 'Other',
	);

	public static $return_services = array(
		1010 => 'Stock In',
		3060 => 'Re-deliver',
		4030 => 'Discard',
		2110 => 'Pick Up',
	);

	public static $return_options_courier_rts = array(
		2 => 'Redelivery',
		1 => 'No',
	);

	public static $return_options_customer_return = array(
		1 => 'Restock',
		2 => 'Redelivery',
		3 => 'Return-to-office',
		4 => 'Discard',
		5 => 'Check',
	);

	const CUSTOMER_RETURN_RESTOCK = 1;
	const CUSTOMER_RETURN_REDELIVERY = 2;
	const CUSTOMER_RETURN_REPAIR = 3;
	const CUSTOMER_RETURN_DISCARD = 4;
	const CUSTOMER_RETURN_CHECK = 5;

	public static $sources = array(
		1 => 'Manual',
		2 => 'Shopify',
		3 => 'Cin7',
	);
	const WMS_TASK_SOURCE_MANUAL = 1;
	const WMS_TASK_SOURCE_SHOPIFY = 2;
	const WMS_TASK_SOURCE_CIN7 = 3;

	public static $countries = array(
		'AU' => 'Australia',
		'NZ' => 'New Zealand',
		'CN' => 'China',
		'GB' => 'United Kingdom',
		'CA' => 'Canada',
		'US' => 'USA',
		'SG' => 'Singapore',
	);

	public $mdata = [];
	public $new_items = [];
	public $job_ids;


	//search params
	public $job_no, $op_name, $custom_log_note, $cust_name, $skus, $type_ex, $source, $refs, $prod_name, $prod_sku, $delivery_name, $delivery_tel, $delivery_postcode, $delivery_connote, $dpmt, $plt_diff;
	public $cnee_name, $cnee_company, $cnee_city, $cnee_email, $cnee_full_address, $create_date, $cnee_address, $cnee_suburb, $cnee_state, $cnee_postcode, $cnee_country, $consignment, $carrier, $trackingno;
	public $adhoc_request;
	public $return_type, $return_status, $redelivery;
	public $edi_job, $awbn,$isSinOrMul;

	public $nolog = false, $auto_rank = true, $noAfterSave = false;

	public static $op = array(
		//3412,
		//3424,
	);

	public static $checkBackOrders = array(
		Org::ORGID_3PL_LIFESTYLE,
		Org::ORGID_3PL_BIOPHYSICS,
	);

	public static $checkBackOrdersManual = array(
		Org::ORGID_3PL_IGEA,
		Org::ORGID_3PL_BIOPHYSICS,
	);

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_task';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('job_id, type, status, dpt_id', 'required'),
			array('id, op_id, link_id, is_request, billed, ref, due_time, schd_time, start_time, compl_time, bwf, meta, cust_name, type_ex', 'safe'),
			array('type, status, billed, bwf', 'numerical', 'integerOnly' => true),
			array('job_id, op_id', 'length', 'max' => 11),
			array('ref', 'length', 'max' => 50),
			array('due_time, schd_time, start_time, compl_time', 'default', 'setOnEmpty' => true, 'value' => null),
			// The following rule is used by search().
			array('source, cnee_name, cnee_city, cnee_company, cnee_email, cnee_full_address, create_date, cnee_address, cnee_suburb, cnee_state, cnee_postcode, cnee_country, consignment, carrier, mdata, dpt_id, adhoc_request, return_type, return_status, redelivery, edi_job, awbn', 'safe'),
			// @todo Please remove those attributes that should not be searched.
			array('id, job_id, job_no, link_id, is_request, op_id, op_name, type, status, billed,job_ids,ref, due_time, schd_time, start_time, compl_time, bwf, meta, cust_name, type_ex, source, refs, prod_name, prod_sku, delivery_name, delivery_tel, delivery_postcode, delivery_connote, dpmt, plt_diff, cnee_name, cnee_city, cnee_company, cnee_email, cnee_full_address, create_date, cnee_address, cnee_suburb, cnee_state, cnee_postcode, cnee_country, consignment, carrier, dpt_id, adhoc_request, return_type, return_status, redelivery, edi_job, awbn,isSinOrMul', 'safe', 'on' => 'search'),
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
			'job' => array(self::BELONGS_TO, 'WmsJob', 'job_id'),
			'op' => array(self::BELONGS_TO, 'User', 'op_id'),
			'subTasks' => array(self::HAS_MANY, 'WmsTask', 'link_id', 'order' => 'subTasks.id ASC'),
			'packTask' => array(self::HAS_ONE, 'WmsTask', 'link_id', 'on' => 'packTask.type = 3210'),
			'pickupTask' => array(self::HAS_ONE, 'WmsTask', 'link_id', 'on' => 'pickupTask.type = 2110'),
			'deliveryTask' => array(self::HAS_ONE, 'WmsTask', 'link_id', 'on' => 'deliveryTask.type = 2120'),
			// 'splitSortingTask' => array(self::HAS_ONE, 'WmsTask', 'link_id', 'on' => 'splitSortingTask.type = '.self::TYPE_Split_Sorting),
			'palletinTask' => array(self::HAS_ONE, 'WmsTask', 'link_id', 'on' => 'palletinTask.type = 1010'),
			'mainTask' => array(self::BELONGS_TO, 'WmsTask', 'link_id'),
			'actionTask' => array(self::HAS_ONE, 'WmsTask', ['link_id' => 'id', 'type' => 'type']),
			'items' => array(self::HAS_MANY, 'WmsTaskItem', 'task_id', 'on' => 'items.del = 0'),
			'edijobs' => array(self::MANY_MANY, 'EdiJob', 'wms_edi(task_id, job_id)'),
			'createlog' => array(self::HAS_ONE, 'Log', 'lid', 'on' => 'createlog.model = "WmsTask" AND createlog.type = 3'),
			'lastlog' => array(self::HAS_ONE, 'Log', 'lid', 'on' => 'lastlog.model = "WmsTask" AND lastlog.type = 4', 'order' => 'lastlog.id DESC'),
			'map' => array(self::HAS_ONE, 'WmsTaskMap', 'task_id'),
			'batch' => array(self::HAS_ONE, 'WmsTaskBatch', 'task_id', 'on' => 'batch.status != 100'),
			'branch' => array(self::BELONGS_TO, 'Org', 'dpt_id'),
		);
	}

	public function getType()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$types[$this->type]) ? '' : self::$types[$this->type]);
	}

	public function getStatus()
	{
		if (in_array($this->type, [6010, 6020, 6030, 6040])) {
			return $this->getAdhocStatus();
		} else if (in_array($this->job->org_id, Org::$easyships)) {
			return $this->getEasyshipStatus();
		} else {
			return Yii::t(strtolower(__CLASS__), empty(self::$states[$this->status]) ? '' : self::$states[$this->status]);
		}
	}

	public function getClientStatus()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$states_client[$this->status]) ? '' : self::$states_client[$this->status]);
	}

	public function getClientStatusBIO()
	{
		$text = '';
		if (empty(self::$states_client_bio[$this->status])) {
			$text = '';
		} else {
			if ($this->status == 10) {
				$color = 'red';
			} else if ($this->status == 20) {
				$color = 'orange';
			} else if ($this->status == 30) {
				$color = 'blue';
			} else if ($this->status == 99) {
				$color = 'green';
			} else if ($this->status == self::STATUS_HOLD) {
				$color = 'red';
			} else if ($this->status == 100) {
				$color = 'black';
			}
			// $text = "<span style=\"color: " . $color . "\">" . self::$states_client_bio[$this->status] . "</span>";
			$text = self::$states_client_bio[$this->status];
		}
		return $text;
	}

	public function getAdhocStatus()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$states_adhoc[$this->status]) ? '' : self::$states_adhoc[$this->status]);
	}

	public function getColor()
	{
		if ($this->status == 10) {
			$color = 'red';
		} else if ($this->status == 20) {
			$color = 'orange';
		} else if ($this->status == 30) {
			$color = 'blue';
		} else if ($this->status == 99) {
			$color = 'green';
		} else if ($this->status == self::STATUS_HOLD) {
			$color = 'red';
		} else if ($this->status == 100) {
			$color = 'black';
		}
		return $color;
	}

	public function getClientStatusInbound()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$states_client_inbound[$this->status]) ? '' : self::$states_client_inbound[$this->status]);
	}

	public function getEasyshipStatus()
	{
		if ($this->status == 10 && !empty($this->mdata['errs'])) {
			return 'Pending (Not Enough Inventory)';
		} if ($this->status == 99 && !empty($this->mdata['api_hook']) && in_array($this->type, [3020,3030])) {
			if (empty($this->mdata['api_comp'])) {
				if (time() - strtotime($this->compl_time) < 60 * 60 * 1000) {
					return 'API Sending';
				} else {
					return 'API Failed';
				}
			} else if (empty($this->mdata['api_ship'])) {
				return 'Packed';
			} else {
				return 'Completed';
			}
		} else {
			if (!empty(self::$states_client[$this->status])) {
				$status = self::$states_client[$this->status];
			} else if (!empty(self::$states[$this->status])) {
				$status = self::$states[$this->status];
			} else {
				$status = '';
			}
			return Yii::t(strtolower(__CLASS__), $status);
		}
	}

	public function getReturnStatus()
	{
		if ($this->getReturnType() == 'Customer Return') {
			return Yii::t(strtolower(__CLASS__), empty(self::$states_return[$this->status]) ? '' : self::$states_return[$this->status]);
		} else if ($this->getReturnType() == 'Courier RTS') {
			return Yii::t(strtolower(__CLASS__), empty(self::$states_return[@$this->mdata['return_status']]) ? '' : self::$states_return[@$this->mdata['return_status']]);
		}
	}

	public function getReturnType()
	{
		if ($this->type == self::TYPE_RETURN) {
			return 'Customer Return';
		} else if (in_array($this->type, [3020,3030])) {
			return 'Courier RTS';
		} else {
			return '';
		}
	}

	public function getReturnLink()
	{
		return '<a href="' . Yii::app()->createUrl('pcaw/return/update', ['id' => $this->id]) . '">' . $this->getNo() . '</a>';
	}

	public function getRedeliveryLink()
	{
		if (!empty($this->mdata['redelivery_task'])) {
			$rt = WmsTask::model()->findByPk($this->mdata['redelivery_task']);
			return '<a href="' . Yii::app()->createUrl('pcaw/task/updateTask', ['id' => $rt->id, 'from' => 'return']) . '">' . $rt->getNo() . '</a>';
		} else {
			return '';
		}
	}

	public function generateRedeliveryTask($cnee)
	{
		if (empty($this->mdata['redelivery_task'])) {
			$task = new WmsTask;
			$task->job_id = $this->job->id;
			$task->ref = $this->no . ' return re-delivery';
			$task->type = 3030;
			$task->is_request = 1;
			$task->status = 20;
			$task->mdata['return'] = true;
			$task->save();

			$task->pickupTask->type = 2120;
			$task->pickupTask->mdata['cnee'] = $cnee;
			$task->pickupTask->save();
		} else {
			$task = self::model()->findByPk($this->mdata['redelivery_task']);

			$task->deliveryTask->mdata['cnee'] = $cnee;
			$task->deliveryTask->save();
		}

		$this->mdata['redelivery_task'] = $task->id;
		$this->updateMeta();
	}

	public function generateRestockTask()
	{
		if (empty($this->mdata['restock_task'])) {
			$task = new WmsTask;
			$task->job_id = $this->job->id;
			$task->ref = $this->no . ' return restock';
			$task->type = 7020;
			$task->is_request = 1;
			$task->status = 20;
			$task->save();
		} else {
			$task = self::model()->findByPk($this->mdata['restock_task']);
		}

		$this->mdata['restock_task'] = $task->id;
		$this->update('meta');
	}

	public function getBranch()
	{
		return empty($this->dpt_id) ? '' : $this->branch->shortName(1);
	}

	public function getSource()
	{
		return Yii::t(strtolower(__CLASS__), empty(self::$sources[$this->source]) ? '' : self::$sources[$this->source]);
	}

	public function getNo()
	{
		return "T" . sprintf("%06d", empty($this->link_id) ? $this->id : $this->link_id);
	}

	public function getRef()
	{
		// 2019-Oct-21 EVA
		$express = false;
		if (preg_match('/express/i', @$this->mdata['note'])) $express = true;
		foreach ($this->items as $item) if (preg_match('/express/i', @$item->mdata['nt'])) $express = true;
		if ($express) $this->ref .= ' <span style="color:red">Express</span>';

		if (!empty($this->deliveryTask) && !empty($this->deliveryTask->mdata['cnee']['country']) && $this->deliveryTask->mdata['cnee']['country'] != 'AU') {
			$this->ref .= ' <span style="color:red">Int</span>';
		}
		return $this->ref;
	}

	public function notEmptyReturnCheck()
	{
		return !empty($this->mdata['return_check_task']) && WmsTask::model()->findByPk($this->mdata['return_check_task']) != null;
	}

	public function notEmptyReturnOption()
	{
		return !empty($this->mdata['return_option_task']) && WmsTask::model()->findByPk($this->mdata['return_option_task']) != null;
	}

	public function getNoPCAW()
	{
		$n = "T" . sprintf("%06d", empty($this->link_id) ? $this->id : $this->link_id);
		if ($this->bwf & 128) {
			if (!$this->notEmptyReturnCheck() && !$this->notEmptyReturnOption()) {
				$n .= '&nbsp;&nbsp;<span class="badge" style="background-color:red"><a class="ajax-link" href="' . Yii::app()->createUrl('pcaw/task/returnCheck', array('id' => $this->id)) .'">Return!</a></span>';
			} else if ($this->notEmptyReturnOption()) {
				$n .= '&nbsp;&nbsp;<span class="badge" style="background-color:green"><a class="ajax-link" href="' . Yii::app()->createUrl('pcaw/task/updateTask', array('id' => $this->mdata['return_option_task'])) .'">Processing!</a></span>';
			} else if ($this->notEmptyReturnCheck()) {
				$n .= '&nbsp;&nbsp;<span class="badge" style="background-color:green"><a class="ajax-link" href="' . Yii::app()->createUrl('pcaw/task/updateTask', array('id' => $this->mdata['return_check_task'])) .'">Checking!</a></span>';
			}
		}
		return $n;
	}

	public function getUpdate()
	{
		if (in_array($this->type, [1010])) {
			return (((empty($this->mdata[Yii::app()->user->id]) || (!empty($this->mdata['res'])) && ($this->mdata[Yii::app()->user->id] < $this->mdata['res'])) && $this->type == 1010) && !empty($this->mdata['pi_cargo']) ? ' <span style="color:red;">New</span>' : '');
		} else {
			return '';
		}
	}

	public function getIsRequest()
	{
		return empty($this->is_request) ? 'N' : 'Y';
	}

	public function nextItemPick()
	{
		$si = [];
		$psi = [];
		$locs = [];
		$sl = [];
		$ss = [];
		$pq = [];

		foreach ($this->mainTask->items as $itm) {
			if (empty($itm->mdata['uq']) || empty($itm->mdata['si'])) {
				continue;
			}

			if (!isset($si[$itm->mdata['si']])) {
				$si[$itm->mdata['si']] = 0;
			}

			$si[$itm->mdata['si']] += $itm->mdata['uq'];
		}

		foreach ($this->items as $itm) {
			if (empty($itm->mdata['uq']) || empty($itm->mdata['si'])) {
				continue;
			}

			if (!isset($psi[$itm->mdata['si']])) {
				$psi[$itm->mdata['si']] = 0;
			}

			$psi[$itm->mdata['si']] += $itm->mdata['uq'];
		}

		foreach ($si as $sid => $q) {
			if (empty($psi[$sid])) {
				$psi[$sid] = 0;
			}

			if ($psi[$sid] < $q) {
				$ss[$sid] = WmsStock::model()->findByPk($sid);
				if (empty($ss[$sid])) {
					continue;
				}

				$pq[$sid] = $si[$sid] - $psi[$sid];
				$sl[$sid] = $ss[$sid]->getBestLocs($pq[$sid]);
				if (empty($sl[$sid][1])) {
					continue;
				}

				$t = 0;
				foreach ($sl[$sid][1] as $lw) {
					$locs[$lw[1]][] = $sid;
					$t += $lw[0]->qty;
					if ($t >= $pq[$sid]) {
						break;
					}

				}
			}
		}

		if (empty($sl)) {
			return [9, []];
		}

		if (empty($locs)) {
			return [3, $ss, $pq];
		}

		// krsort($locs);

		// $ip = array_shift($locs);

		// sort $ip by loc parent name
		$order_ip = [];
		foreach ($locs as $ip) {
		foreach ($ip as $stock_id) {
			$stock = WmsStock::model()->findByPk($stock_id);
			$blocs = $stock->getBestLocs($pq[$stock_id]);
			$order_ip[$blocs[1][0][0]->loc->parent->name . $stock_id] = $stock_id;
		}
		}
		ksort($order_ip);
		$ip = array_values($order_ip);

		if (!empty($this->mdata['top'])) {
			if (in_array($this->mdata['top'], $order_ip)) {
				$ip[0] = $this->mdata['top'];
			}
		}

		return [1, $sl[$ip[0]], $pq[$ip[0]]];
	}

	public static function nextItemBatchPick($batch_no)
	{
		$date = explode('-', $batch_no)[0];
		$date = '20' . substr($date, 0, 2) . '-' . substr($date, 2, 2) . '-' . substr($date, 4, 2);
		$batch = intval(explode('-', $batch_no)[1]);
		$type = explode('-', $batch_no)[2];
		$tasks = WmsTask::model()->with('batch')->findAll('batch.date = :date AND batch.batch = :batch AND batch.status = :status AND batch.type = :type', [':date' => $date, ':batch' => $batch, ':status' => WmsTaskBatch::WMS_TASK_BATCH_STATUS_NEW, ':type' => $type]);
		$stocks = [];
		foreach ($tasks as $task) {
			foreach ($task->items as $item) {
				if (empty($item->mdata['si'])) continue;
				if (empty($stocks[$item->mdata['si']])) {
					$stocks[$item->mdata['si']] = 0;
				}
				$stocks[$item->mdata['si']] += $item->mdata['uq'];
			}

			foreach ($task->actionTask->items as $item) {
				if (empty($stocks[$item->mdata['si']])) continue;
				$stocks[$item->mdata['si']] -= $item->mdata['uq'];
			}
		}

		$items = WmsTask::_nextItemBatchPick($stocks);
		$np = array_shift($items);
		$cond = 'prod.ean = :ean AND t.qty > 0 AND location_id > 10';
		$params = [':ean' => $np[0]];
		if (!empty($np[6])) {
			$cond .= ' AND expiry = :expiry';
			$params[':expiry'] = $np[6];
		}
		if (!empty($np[7])) {
			$cond .= ' AND batch = :batch';
			$params[':batch'] = $np[7];
		}
		$locs = WmsStockLocation::model()->with('stock.prod', 'stock')->findAll($cond, $params);

		return [$np[0], $np[1], $locs, $np[4], $np[6], $np[7]];
	}

	public static function _nextItemBatchPick($stocks)
	{
		$items = [];
		foreach ($stocks as $sid => $qty) {
			if ($qty == 0) continue;
			$s = WmsStock::model()->findByPk($sid);
			$locs = $s->getBestLocs($qty);
			if (!empty($locs[1])) {
				$items[sprintf('%06d', $locs[1][0][0]->loc->parent->wt) . $locs[1][0][0]->loc->name . $s->prod->ean . $s->expiry . $s->batch] = [$s->prod->ean, $s->prod->name, $locs[1][0][0]->loc->name, $locs[1][0][0]->loc->parent->name, $qty, $s->id, $s->expiry, $s->batch];
			} else {
				$items['no stock' . 'no stock' . $s->prod->ean . $s->expiry . $s->batch] = [$s->prod->ean, $s->prod->name, 'no stock', 'no stock', $qty, $s->id, $s->expiry, $s->batch];
			}
		}
		ksort($items);

		return $items;
	}

	public static function _nextItemBatchPickForPickingList($stocks,$stocksAll)
	{
		$items = [];
		foreach ($stocks as $sid => $qty) {
			$temp = $stocksAll[$sid];
			if ($qty == 0) continue;
			$s = WmsStock::model()->findByPk($sid);
			$locs = $s->getFirstLocs($qty);

			if(count($locs)==1){
				$items[sprintf('%06d', $locs[0][0]->loc->parent->wt) . $locs[0][0]->loc->name . $s->prod->ean . $s->expiry . $s->batch] = [$s->prod->ean, $s->prod->name, $locs[0][0]->loc->name, $locs[0][0]->loc->parent->name, $qty, $s->id, $s->expiry, $s->batch];
			}
			else if(!empty($locs)){
				$i=0;
				foreach($locs as $key){
					if($temp<=($key[0]->qty)){
						$items[sprintf('%06d', $key[0]->loc->parent->wt) . $key[0]->loc->name . $s->prod->ean . $s->expiry . $s->batch] = [$s->prod->ean, $s->prod->name, $key[0]->loc->name, $key[0]->loc->parent->name, $qty, $s->id, $s->expiry, $s->batch];
						break;
					}else{
						$temp-=$key[0]->qty;
						$i++;
					}
				}
			}
			else{
				$items['no stock' . 'no stock' . $s->prod->ean . $s->expiry . $s->batch] = [$s->prod->ean, $s->prod->name, 'no stock', 'no stock', $qty, $s->id, $s->expiry, $s->batch];
			}
		}
		ksort($items);

		return $items;
	}

	public function nextPltPick()
	{
		$locs = [];

		foreach ($this->mainTask->items as $item) {
			if (!empty($item->mdata['pli']) && !empty($item->mdata['pl'])) {
				$locs[$item->mdata['pli']] = $item->mdata['pl'];
			} else if (!empty($item->mdata['pl'])) {
				$location = WmsLocation::model()->find('name = :name', [':name' => $item->mdata['pl']]);
				if (!empty($location)) {
					$locs[$location->id] = $item->mdata['pl'];
				}
			}
		}

		foreach ($this->items as $item) {
			if (!empty($item->mdata['pli']) && !empty($locs[$item->mdata['pli']])) {
				unset($locs[$item->mdata['pli']]);
			}
		}

		$locs = array_values($locs);
		if (!empty($locs)) {
			$plt = WmsLocation::model()->find('name = :name', [':name' => current($locs)]);
			return $plt->name . ' ' . $plt->parent->name;
		} else {
			return '';
		}
	}

	public function allItemPick()
	{
		$si = [];
		$psi = [];
		$locs = [];
		$sl = [];
		$ss = [];
		$pq = [];

		foreach ($this->mainTask->items as $itm) {
			if (empty($itm->mdata['uq']) || empty($itm->mdata['si'])) {
				continue;
			}

			if (!isset($si[$itm->mdata['si']])) {
				$si[$itm->mdata['si']] = 0;
			}

			$si[$itm->mdata['si']] += $itm->mdata['uq'];
		}

		foreach ($this->items as $itm) {
			if (empty($itm->mdata['uq']) || empty($itm->mdata['si'])) {
				continue;
			}

			if (!isset($psi[$itm->mdata['si']])) {
				$psi[$itm->mdata['si']] = 0;
			}

			$psi[$itm->mdata['si']] += $itm->mdata['uq'];
		}

		foreach ($si as $sid => $q) {
			if (empty($psi[$sid])) {
				$psi[$sid] = 0;
			}

			if ($psi[$sid] < $q) {
				$ss[$sid] = WmsStock::model()->findByPk($sid);
				if (empty($ss[$sid])) {
					continue;
				}

				$pq[$sid] = $si[$sid] - $psi[$sid];
				$sl[$sid] = $ss[$sid]->getBestLocs($pq[$sid]);
				if (empty($sl[$sid][1])) {
					continue;
				}

				$t = 0;
				foreach ($sl[$sid][1] as $lw) {
					$locs[$lw[1]][] = $sid;
					$t += $lw[0]->qty;
					if ($t >= $pq[$sid]) {
						break;
					}

				}
			}
		}

		return [$sl, $pq];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'job_id' => 'Job',
			'job_No' => 'Job #',
			'op_id' => 'Operator',
			'link_id' => 'Link to',
			'is_request' => 'Request',
			'type' => 'Type',
			'status' => 'Status',
			'billed' => 'Billed',
			'ref' => 'Ref #',
			'due_time' => 'Due Time',
			'schd_time' => 'Scheduled Time',
			'start_time' => 'Start Time',
			'compl_time' => 'Completion Time',
			'bwf' => 'Bwf',
			'meta' => 'Meta',
			'dpt_id' => 'Branch',
			'awbn' => 'AWBN',
		);
	}

	public function checkRates()
	{
		// case 1010: //Pallets In
		// case 1020: //Bulk In
		// case 1030: //Container Unload
		// case 2030: //Container Load
		// case 3010: //Pick Pallet
		// case 3020: //Pick Carton
		// case 3030: //Pick Unit
		// case 3210: //Pack Order
		// case 2120: //Delivery
	}

	public function autoCharges()
	{

	}

	public function saveItems()
	{
		if (empty($this->new_items)) {
			return;
		}

		$k = 0;
		foreach ($this->new_items as $i => $itm) {
			$t = !is_array($itm) ? implode('', $itm) : $itm;
			if (empty($t)) {
				unset($this->new_items[$i]);
			}

		}

		$sql = 'UPDATE wms_stock_ledger SET qty_in = 0, qty_out = 0 WHERE task_id = ' . $this->id;
		Yii::app()->db->createCommand($sql)->execute();
		$casErr = function ($itm) {
			$err = $itm->getErrors();
			if (!empty($err)) {
				foreach ($err as $k => $es) {
					foreach ($es as $e) {
						$this->addError('type', $e);
					}
				}
			}
		};

		if (!empty($this->items)) {
			foreach ($this->items as $k => $itm) {
				if (empty($this->new_items[$k])) {
					$itm->delete();
					continue;
				}
				$itm->mdata = $this->new_items[$k];
				$itm->save();
				$casErr($itm);
			}
			$k++;
		}

		$il = sizeof($this->new_items);
		for ($i = $k; $i < $il; $i++) {
			$itm = new WmsTaskItem;
			$itm->task_id = $this->id;
			$itm->mdata = $this->new_items[$i];
			$itm->del = 0;
			$itm->save();
			$casErr($itm);
		}

		$this->getRelated('items', true);
	}

	public function workflow()
	{
		if ($this->is_request != 1) {
			return;
		}

		$act = $this->actionTask;
		if (empty($act) && !in_array($this->type, [3040, 3060, 2110, 6010, 7010])) {
			$act = new WmsTask;
			$act->type = $this->type;
			$act->status = 10;
			$act->link_id = $this->id;
			$act->job_id = $this->job_id;
			$act->dpt_id = $this->dpt_id;
			$act->save();
		}


		if (in_array($this->type, [3010, 3020, 3030, 3040, 3060,self::TYPE_Split_Delivery_SF])) {
			// pack
			$t = WmsTask::model()->find('link_id = :id AND type = 3210', [':id' => $this->id]);
			if (empty($t)) {
				$t = new WmsTask;
				$t->type = 3210;
				$t->status = 10;
				$t->link_id = $this->id;
				$t->job_id = $this->job_id;
				$t->dpt_id = $this->dpt_id;
				
				// set weight and dim from production
				$listWmsTaskItem = WmsTaskItem::model()->findAll('task_id=:task_id',['task_id'=>$this->id]);
				foreach ($listWmsTaskItem as $objWmsTaskItem){
					$objProdItem = $objWmsTaskItem->mdata;
					
					if(empty($objProdItem['si'])){
						continue;
					}
					
					$objWmsStock = WmsStock::model()->findByPk($objProdItem['si']);
					$objProd = WmsProd::model()->findByPk($objWmsStock->prod_id);
					$listPkg = [];
					$numQty = $objProdItem['uq'];
					for($i=0;$i<$numQty;$i++){
						$listItem = [];
						$listItem['wt'] = $objProd->weight/1000;
						$listItem['w'] = $objProd->dims['w'];
						$listItem['h'] = $objProd->dims['h'];
						$listItem['d'] = $objProd->dims['d'];
						$listItem['nt'] = 'Added from production information when task was created';
						$listPkg[] = $listItem;
					}
					$t->mdata['pkg'] = json_encode($listPkg);
				}
				
				$t->save();
			}
		}
		if (in_array($this->type, [3010, 3020, 3030, 5010, 3040])) {
			// pickup or delivery
			$t = WmsTask::model()->find('link_id = :id AND type IN (2110, 2120)', [':id' => $this->id]);
			if (empty($t)) {
				$t = new WmsTask;
				$t->type = 2110;
				$t->status = 10;
				$t->link_id = $this->id;
				$t->job_id = $this->job_id;
				$t->dpt_id = $this->dpt_id;
				$t->save();
			}
		}

		if (in_array($this->type, [3060, 7010,self::TYPE_Split_Delivery_SF])) {
			// delivery
			$t = WmsTask::model()->find('link_id = :id AND type IN (2120)', [':id' => $this->id]);
			if (empty($t)) {
				$t = new WmsTask;
				$t->type = 2120;
				$t->status = 10;
				$t->link_id = $this->id;
				$t->job_id = $this->job_id;
				$t->dpt_id = $this->dpt_id;
				$t->save();
			}
		}
		
		// if(in_array($this->type,[8030])){
		// 	// split sorting
		// 	$t = WmsTask::model()->find('link_id = :id AND type IN ('.self::TYPE_Split_Sorting.')', [':id' => $this->id]);
		// 	if (empty($t)) {
		// 		$t = new WmsTask;
		// 		$t->type = self::TYPE_Split_Sorting;
		// 		$t->status = 10;
		// 		$t->link_id = $this->id;
		// 		$t->job_id = $this->job_id;
		// 		$t->dpt_id = $this->dpt_id;
		// 		$t->save();
		// 	}
			
		// }
		
		if (in_array($this->type, [6010])) {
			foreach ([6020, 6030, 6040, 6010] as $sub_type) {
				$t = WmsTask::model()->find('link_id = :id AND type = :type', [':id' => $this->id, ':type' => $sub_type]);
				if (empty($t)) {
					$t = new WmsTask;
					$t->type = $sub_type;
					$t->status = 10;
					$t->link_id = $this->id;
					$t->job_id = $this->job_id;
					$t->dpt_id = $this->dpt_id;
					$t->save();
				}
			}
		}
		
	}

	public function getItem($id)
	{
		$rs = [];
		foreach ($this->items as $k => $itm) {
			if (empty($itm->mdata['gi'])) {
				continue;
			}

			if ($itm->mdata['gi'] == $id) {
				$rs[] = $itm;
			}
		}
		return $rs;
	}

	public function getItemTot($id)
	{
		$rs = $this->getItem($id);
		$t = 0;
		foreach ($rs as $r) {
			if (empty($r->mdata['uq'])) {
				continue;
			}

			$t += $r->mdata['uq'];
		}
		return $t;
	}

	public function getItemsArray()
	{
		$rs = [];
		$mebs = [];
		foreach ($this->items as $r) {
			if (in_array($this->type, [3020,3030]) && empty($r->mdata['uq']) && empty($r->mdata['cq'])) continue;
			if (!empty($r->mdata['pli']) && empty($r->mdata['pl'])) {
				$p = WmsLocation::model()->findByPk($r->mdata['pli']);
				if (!empty($p)) {
					$r->mdata['pl'] = $p->code;
				}

			}

			$t = json_decode(json_encode($r->mdata), true);
			$t['id'] = $r->id; 
			if (array_key_exists("si", $t)) {
				$s = WmsStock::model()->findByPk($t["si"]);
				$meb = false;
				if (!empty($s->prod_id) && (!empty($mebs[$s->prod_id]) || $s->hasMultiEB())) {
					$meb = true;
					$mebs[$s->prod_id] = true;
				}
				$t["meb"] = $meb;
			}

			if (array_key_exists("si", $t) && empty($t['si']) && !empty($t['sku']) && $this->job->org_id == Org::ORGID_3PL_BIOPHYSICS) {
				$prod_org = WmsProdOrg::model()->find('sku = :sku AND org_id = :oid', [':sku' => $t['sku'], ':oid' => Org::ORGID_3PL_BIOPHYSICS]);
				if (!empty($prod_org) && $prod_org->prod->type == WmsProd::WMS_PROD_KIT) {
					continue;
				}
			}

			$rs[] = $t;
		}
		return $rs;
	}

	public function toShipment()
	{
		try {
			$rs = $this->_toShipment();
		} catch (Exception $ex) {
			if ($this->mdata['courier'] == Org::ORGID_COURIER_FASTWAY) {
				$this->mdata['courier'] = Org::ORGID_COURIER_AUPOST;
				$this->update('meta');
				$rs = $this->_toShipment();
			}
		}

		return $rs;
	}

	public function _toShipment()
	{
		if ($this->type != 2120 || empty($this->mdata['cnee'])) {
			return;
		}
		$numTaskID='T'.$this->mainTask->id;
		$objOldImParcels = ImParcel::model()->findAll('cref like :t',[':t'=>$numTaskID]);
		foreach($objOldImParcels as $objOldImParcel)
		{
			$objOldImParcel->status = 100;
			$objOldImParcel->save();
		}

		$pm = preg_match('/[\x{4e00}-\x{9fa5}]+/u', $this->mdata['cnee']['state']) ? 'ExParcel' : 'ImParcel';
		$rs = []; //to store multiple shipments;

		$p = new $pm;
		$ar = new Addr;
		$ar->name = $this->job->customer->name . (((!empty($this->job->customer->extra['op_id']) && in_array($this->job->customer->extra['op_id'], WmsTask::$op)) || (!empty($this->job->customer->extra['sp_id']) && in_array($this->job->customer->extra['sp_id'], WmsTask::$op))) ? ' - 3PL' : '');
		$ar->tel = $this->job->customer->phone;
		$ar->country = 'Australia';
		$ar->state = self::getState();
		$ar->save();

		$ae = new Addr;
		unset($this->mdata['cnee']['phone']);
		$ae->setAttributes($this->mdata['cnee']);
		$ae->save();

		$pt = WmsTask::model()->find('type = 3210 AND link_id = :t', [':t' => $this->link_id]);
		$pkg = 0;
		$tw = 0;
		$cbm = 0;
		$fw_wt = [];
		$fw_cbm = [];
		$numPallets =0;
		if (!empty($pt) && !empty($pt->mdata['pkg'])) {
			foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
				if (empty($pk['wt'])||empty($pk['w'])||empty($pk['h'])||empty($pk['d'])) {
					continue;
				}
				$fw_cbm[] = $pk['w']*$pk['h']*$pk['d'];
				$cbm += $pk['w']*$pk['h']*$pk['d'];

				$fw_wt[] = $pk['wt']; //store each pack's weight;
				$tw += $pk['wt'];

				$pkg++;
			}
			if(!empty($pt->mdata['Palltes'])){
				$numPallets = $pt->mdata['Palltes'];
			} 
		} else {
			$pkg = 1;
		}
		$strFbaId = $pt->mainTask->mdata['fba_id'];
		$strFbaPo = $pt->mainTask->mdata['fba_po'];

		$p->setAttributes(array(
			'agent_id' => $this->job->org_id,
			// 'odpt_id' => 106,
			'odpt_id' => $this->dpt_id,
			'ddpt_id' => $this->dpt_id,
			'cnor_id' => $ar->id,
			'cnee_id' => $ae->id,
			'status' => empty($p->status) ? 10 : $p->status,
			'state' => $ae->state,
			'postcode' => $ae->postcode,
			'pkg' => $pkg,
			'currency' => 1,
			'cref' => $this->getNo(),
			'weight' => sprintf('%0.2f', round($tw * 100) / 100),
			'cbm' => round(($cbm/1000000)/$pkg,6),
		));

		$items = [];
		foreach ($this->mainTask->items as $itm) {
			if (empty($itm->mdata['si'])) {
				continue;
			}

			$s = WmsStock::model()->findByPk($itm->mdata['si']);
			if ($pm == 'ExParcel') {
				$items['type'][] = 'O';
				$items['g'][] = $s->prod->name;
				$items['b'][] = $s->prod->brand;
				$items['m'][] = $s->prod->model;
				$items['v'][] = '1';
				$items['q'][] = $itm->mdata['uq'];
				$items['hs'][] = '';
			} else {
				$items['g'][] = $s->prod->name;
				$items['v'][] = '1';
				$items['q'][] = $itm->mdata['uq'];
				$items['hs'][] = '';
			}
		}
		$p->eitems = $items;
		$p->hbn = $p->genHbn();
		$p->ref = $p->hbn;
		$p->mdata['wmstask_id'] = $this->link_id;
		//$p->mdata['chargecode'] = 3131;
		//ref

		unset($this->mdata['shipment_id']);
		$this->save();

		switch ($this->mdata['courier']) {
			case Org::ORGID_COURIER_AUPOST_EXPRESS:
				if($ae->country == 'AU'){
					$p->mdata['aupost_exp'] = 1;
					$p->cbwf = $p->cbwf|ImParcel::CBWF_FOR_3PL;
					$p->ref = $p->genEparcelNo();
					$p->mdata['org_rate_id'] = ImportChargeCode::AUPOST_EXPRESS_ID;
					if (!empty($pt) && !empty($pt->mdata['pkg'])) {
						foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
							$p->packs[]=[
								'weight'=>$pk['wt'],
								'length'=>$pk['d'],
								'height'=>$pk['h'],
								'width'=>$pk['w'],
								'reference'=>0,
								'cbm'=>($pk['d']*$pk['h']*$pk['w'])/1000000
							];
						}
					}
					$p->save();
					$this->mdata['trackingNoForScan']=$p->ref;
				}
				if ($p->id > 0) {
					$rs[] = $p;
					$this->mdata['shipment_id'][] = $p->id;
				}
				break;
			case Org::ORGID_COURIER_AUPOST: //Auspost
				// $p->nolog = true;
				if($ae->country == 'AU'){
					//$p->mdata['aupost_exp'] = 0;
					if($this->dpt_id==106){
						$p->mdata['org_rate_id'] = Org::TPLORGRATE_COURIER_AUSPOST_SYD;
					}else if($this->dpt_id==218){
						$p->mdata['org_rate_id'] = Org::TPLORGRATE_COURIER_AUSPOST_MEL;
					}
					
					$p->mdata['test_aupost_2017'] = 1;
					$p->ref = $this->funcGenerateEparcelNumberByDepotId($this->dpt_id);
					$this->mdata['trackingNoForScan']=$p->ref;
					if (!empty($pt) && !empty($pt->mdata['pkg'])) {
						foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
							$p->packs[]=[
								'weight'=>$pk['wt'],
								'length'=>$pk['d'],
								'height'=>$pk['h'],
								'width'=>$pk['w'],
								'reference'=>0,
								'cbm'=>($pk['d']*$pk['h']*$pk['w'])/1000000
							];
						}
					}
					$p->save();
					
					//------express demo-------
					// $p->mdata['aupost_exp'] = 1;
					// $p->cbwf = $p->cbwf|ImParcel::CBWF_FOR_3PL;
					// $p->ref = $p->genEparcelNo();
					// $p->mdata['org_rate_id'] = '848';
					// $p->save();

				}else{ // Aupost international
					$apa = new AusPostAPI('syd', true, false);
					$p->save();
					$r = $apa->createIntShipments($p);
					$p->mdata['ap_sid'] = $r->shipments[0]->shipment_id;
					$p->ref = $r->shipments[0]->items[0]->tracking_details->article_id;
					$r2 = $apa->createLabels($p->mdata['ap_sid']);
					$p->mdata['ap_lbls'] = [];
					foreach($r2->labels as $lbl){
						$p->mdata['ap_lbls'][] = $lbl->request_id;
					}
					$p->save();
				}

				if ($p->id > 0) {
					$rs[] = $p;
					$this->mdata['shipment_id'][] = $p->id;
				}
				break;
			case Org::ORGID_COURIER_TLA: //pca
				if ($this->mdata['cnee']['country'] == 'CN') {
					for ($j = 0; $j < $pkg; $j++) {
						$p = new $pm;
						$ar = new Addr;
						$ae = new Addr;
						$ar->name = $this->job->customer->name;
						$ar->tel = $this->job->customer->phone;
						$ar->country = 'Australia';
						$ar->save();
						$ae->setAttributes($this->mdata['cnee']);
						$ae->save();
						$p->setAttributes(array(
							'agent_id' => $this->job->org_id,
							// 'odpt_id' => 106,
							'odpt_id' => $this->dpt_id,
							'ddpt_id' => $this->dpt_id,
							'cnor_id' => $ar->id,
							'cnee_id' => $ae->id,
							'status' => empty($p->status) ? 10 : $p->status,
							'state' => $ae->state,
							'postcode' => $ae->postcode,
							'pkg' => 1,
							'currency' => 1,
							'cref' => $this->getNo(),
							'weight' => sprintf('%0.2f', round($fw_wt[$j] * 100) / 100),
							'cbm' => sprintf('%0.2f',round($fw_cbm[$j])/1000000),
						));
						$items = [];
						foreach ($this->mainTask->items as $itm) {
							if (empty($itm->mdata['si'])) {
								continue;
							}

							$s = WmsStock::model()->findByPk($itm->mdata['si']);
							if ($pm == 'ExParcel') {
								$items['type'][] = 'O';
								$items['g'][] = $s->prod->name;
								$items['b'][] = $s->prod->brand;
								$items['m'][] = $s->prod->model;
								$items['v'][] = '';
								$items['q'][] = $itm->mdata['uq'];
								$items['hs'][] = '';
							} else {
								$items['g'][] = $s->prod->name;
								$items['v'][] = '';
								$items['q'][] = $itm->mdata['uq'];
								$items['hs'][] = '';
							}
						}
						$p->eitems = $items;
						$p->hbn = $p->genHbn();
						$p->mdata['wmstask_id'] = $this->link_id;
						$p->save();
						if ($p->id > 0) {
							$this->mdata['shipment_id'][] = $p->id;
							$rs[] = $p;
						}
					}
				} else {
					if($numPallets!=0){
					$p->mdata['countofPallets']=$numPallets;
					$p->cbwf = $p->cbwf | Imparcel::CBWF_PALLET_CARO;
					}
					if(!empty($strFbaId)){
						$p->mdata['amazon_shipment_ids']=$strFbaId;
						$p->mdata['amazon_po']=$strFbaPo;
					}
					$p->hbn=$p->gen3PLHbn();
					$p->ref = $p->hbn;
					if (!empty($pt) && !empty($pt->mdata['pkg'])) {
						foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
							$p->packs[]=[
								'weight'=>$pk['wt'],
								'length'=>$pk['d'],
								'height'=>$pk['h'],
								'width'=>$pk['w'],
								'reference'=>0,
								'cbm'=>($pk['d']*$pk['h']*$pk['w'])/1000000
							];
						}
					}
					$this->mdata['trackingNoForScan']=$p->ref;
					$p->status =  ImParcel::STATE_LOCAL_ARRIVAL;
					$p->mdata['cargo_delivery'] = 1;
					$p->mdata['cargo_ignore_scan'] = 1;

					if($this->dpt_id==106){
						$p->mdata['org_rate_id'] = 70;
					}else if($this->dpt_id==218){
						$p->mdata['org_rate_id'] = 525;
					}else if($this->dpt_id==530){
						$p->mdata['org_rate_id'] = 675;
					}

					$quote = WmsOrgQuote::model()->find(['condition' => 'org_id = :org_id AND status = 1 AND (JSON_VALUE(meta, "$.whole_sale") IS NULL OR JSON_VALUE(meta, "$.whole_sale") = 0)', 'params' => [':org_id' => $this->job->org_id], 'order' => 'id DESC']);

					if ($this->dpt_id == Org::TLA_DEPARTMENT_SYDNEY) {
						$p->mdata['chargecode'] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE_TLACARGO];
					} else if ($this->dpt_id == Org::TLA_DEPARTMENT_MELBOURNE) {
						$p->mdata['chargecode'] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_MEL_CHARGECODE_TLACARGO];
					} else if ($this->dpt_id == Org::TLA_DEPARTMENT_BRISBANE) {
						$p->mdata['chargecode'] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_BNE_CHARGECODE_TLACARGO];
					}

					//$p->ref = '';
					$p->save();
					if ($p->id > 0) {
						$rs[] = $p;
						$this->mdata['shipment_id'][] = $p->id;
					}
				}
				break;
			

			case Org::ORGID_COURIER_FASTWAY: //fastway
				//when multiple package, we need to create multiple shipments for fastway
				for ($j = 0; $j < $pkg; $j++) {
					$p = new ImParcel();
					$ar = new Addr;
					$ae = new Addr;
					$ar->name = $this->job->customer->name;
					$ar->tel = $this->job->customer->phone;
					$ar->country = 'Australia';
					$ar->save();
					$ae->setAttributes($this->mdata['cnee']);
					$ae->save();
					$p->setAttributes(array(
						'agent_id' => $this->job->org_id,
						// 'odpt_id' => 106,
						'odpt_id' => $this->dpt_id,
						'ddpt_id' => $this->dpt_id,
						'cnor_id' => $ar->id,
						'cnee_id' => $ae->id,
						'status' => empty($p->status) ? 10 : $p->status,
						'state' => $ae->state,
						'postcode' => $ae->postcode,
						'pkg' => 1,
						'currency' => 1,
						'cref' => $this->getNo(),
						'weight' => sprintf('%0.2f', round($fw_wt[$j] * 100) / 100),
						'cbm' => round($fw_cbm[$j])/1000000,
					));
					if (!empty($pt) && !empty($pt->mdata['pkg'])) {
						foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
							$p->packs[]=[
								'weight'=>$pk['wt'],
								'length'=>$pk['d'],
								'height'=>$pk['h'],
								'width'=>$pk['w'],
								'reference'=>0,
								'cbm'=>($pk['d']*$pk['h']*$pk['w'])/1000000
							];
						}
					}
					$p->mdata['wmstask_id'] = $this->link_id;
					$p->save();
					// $p->createFastwayLableAPI();
					if($this->dpt_id == Org::PCAE_DEPARTMENT_MELBOURNE){
						$p->mdata['org_rate_id'] = ImportChargeCode::FASTWAY_MEL_3PL_ID;
						$p->createFastwayLabel('mel');
						//$p->createFwLabelWithNR('mel');
					}
					// else if($this->dpt_id == Org::PCAE_DEPARTMENT_SYDNEY){
					else {
						$p->mdata['org_rate_id'] = 799;
						$p->createFwLabelWithNR();
					}
					$this->mdata['trackingNoForScan']=$p->ref;
					$p->save();
					
					if ($p->id > 0) {
						$this->mdata['shipment_id'][] = $p->id;
						$rs[] = $p;
					}
				}
				break;
			case Org::ORGID_COURIER_STARTRACK: // Startrack
				$p = new $pm;
				$ar = new Addr;
				$ae = new Addr;
				$ar->name = $this->job->customer->name;
				$ar->tel = $this->job->customer->phone;
				$ar->country = 'Australia';
				$ar->save();
				$ae->setAttributes($this->mdata['cnee']);
				// unset($ae->tel);
				$p->mdata['wmstask_id'] = $this->link_id;
				$ae->save();
				$p->setAttributes(array(
					'agent_id' => $this->job->org_id,
					// 'odpt_id' => 106,
					'odpt_id' => $this->dpt_id,
					'ddpt_id' => $this->dpt_id,
					'cnor_id' => $ar->id,
					'cnee_id' => $ae->id,
					'status' => empty($p->status) ? 10 : $p->status,
					'state' => $ae->state,
					'postcode' => $ae->postcode,
					'pkg' => sizeof(json_decode($pt->mdata['pkg'], true)),
					'currency' => 1,
					'cref' => $this->getNo(),
					'weight' => sprintf('%0.2f', round($tw * 100) / 100),
				));

				$vol = 0;
				foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
					$items = [];
					foreach ($this->mainTask->items as $itm) {
						if (empty($itm->mdata['si'])) {
							continue;
						}

						$s = WmsStock::model()->findByPk($itm->mdata['si']);
						if ($pm == 'ExParcel') {
							$items['type'][] = 'O';
							$items['g'][] = $s->prod->name;
							$items['b'][] = $s->prod->brand;
							$items['m'][] = $s->prod->model;
							$items['v'][] = '';
							$items['q'][] = $itm->mdata['uq'];
							$items['hs'][] = '';
						} else {
							$items['g'][] = $s->prod->name;
							$items['v'][] = '';
							$items['q'][] = $itm->mdata['uq'];
							$items['hs'][] = '';
						}
					}

					$vol += floatval($pk['w']) * floatval($pk['h']) * floatval($pk['d']) * 0.01 * 0.01 * 0.01;
				}
				$p->cbm = number_format($vol / sizeof(json_decode($pt->mdata['pkg'], true)), 2, '.', '');

				$p->eitems = $items;
				$p->hbn = $p->genHbn();
				// $p->nolog = true;
				$p->save();
				if ($p->id > 0) {
					$this->mdata['shipment_id'][] = $p->id;
					$rs[] = $p;
				}

				$ss = new StarTrackAPI('syd', true);
				$results = $ss->createShipments($rs);
				foreach ($results->shipments as $shipment) {
					foreach ($rs as $p) {
						if ($p->hbn == $shipment->shipment_reference) {
							$cost = $shipment->shipment_summary->total_cost;
							$p->mdata['ss_shipment_id'] = $shipment->shipment_id;
							foreach ($shipment->items as $item) {
								if (!empty($item->tracking_details->article_id)) {
									if (preg_match('/7RFZ\d{8}EXP00001/i', $item->tracking_details->article_id)) {
										$p->mdata['ss_shipment_items'][] = $item->item_id;
										break;
									}
								}
							}
							$p->ref = substr($shipment->items[0]->tracking_details->article_id, 0, 12);
							$p->update('ref');

							$respLabel = $ss->createLabels([$shipment->shipment_id]);
							$lblRequestId = '';
							if ($respLabel) {
								$lblRequestId = $respLabel->labels[0]->request_id;
								$p->mdata['ss_lbl_request_id'] = $lblRequestId;
								$p->update('meta');

								$ts = new Tranship;
								$ts->pid = $p->id;
								$ts->org_id = Org::ORGID_COURIER_STARTRACK;
								$ts->man_id = $p->man_id;
								$ts->type = 80;
								$ts->status = 19;
								$ts->connote = $p->ref;
								$ts->time = date('Y-m-d H:i:s');
								$ts->mdata['ss_shipment_id'] = $shipment->shipment_id;
								$ts->mdata['ss_lbl_request_id'] = $lblRequestId;
								$ts->cost = round($cost, 2);
								$ts->save();
							}
						}
					}
				}
				break;
			case Org::ORGID_COURIER_TNT:
				$p = new $pm;
				$ar = new Addr;
				$ae = new Addr;
				$ar->name = $this->job->customer->name;
				$ar->tel = $this->job->customer->phone;
				$ar->country = 'Australia';
				$ar->save();
				$ae->setAttributes($this->mdata['cnee']);
				// unset($ae->tel);
				$ae->save();
				$p->setAttributes(array(
					'agent_id' => $this->job->org_id,
					// 'odpt_id' => 106,
					'odpt_id' => $this->dpt_id,
					'ddpt_id' => $this->dpt_id,
					'cnor_id' => $ar->id,
					'cnee_id' => $ae->id,
					'status' => empty($p->status) ? 10 : $p->status,
					'state' => $ae->state,
					'postcode' => $ae->postcode,
					'pkg' => sizeof(json_decode($pt->mdata['pkg'], true)),
					'currency' => 1,
					'cref' => $this->getNo(),
					'weight' => sprintf('%0.2f', round($tw * 100) / 100),
					'cbm' => round($fw_cbm[0])/1000000,
				));

				$vol = 0;
				foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
					$items = [];
					foreach ($this->mainTask->items as $itm) {
						if (empty($itm->mdata['si'])) {
							continue;
						}

						$s = WmsStock::model()->findByPk($itm->mdata['si']);
						if ($pm == 'ExParcel') {
							$items['type'][] = 'O';
							$items['g'][] = $s->prod->name;
							$items['b'][] = $s->prod->brand;
							$items['m'][] = $s->prod->model;
							$items['v'][] = '';
							$items['q'][] = $itm->mdata['uq'];
							$items['hs'][] = '';
						} else {
							$items['g'][] = $s->prod->name;
							$items['v'][] = '';
							$items['q'][] = $itm->mdata['uq'];
							$items['hs'][] = '';
						}
					}

					$vol += floatval($pk['w']) * floatval($pk['h']) * floatval($pk['d']) * 0.01 * 0.01 * 0.01;
				}
				$p->cbm = number_format($vol / sizeof(json_decode($pt->mdata['pkg'], true)), 2, '.', '');

				if($this->dpt_id==106){
					$p->mdata['org_rate_id'] = 109;
				}else if($this->dpt_id==218){
					$p->mdata['org_rate_id'] = 309;
				}else if($this->dpt_id==530){
					$p->mdata['org_rate_id'] = 597;
				}

				$p->eitems = $items;
				$p->hbn = $p->genHbn();
				$p->mdata['wmstask_id'] = $this->link_id;
				// $p->nolog = true;
				$this->mdata['trackingNoForScan']=$p->ref;
				$p->save();
				if (!empty($p->getErrors())) {
					yii::log(json_encode($p->getErrors()), 'warning');
				}
				if ($p->id > 0) {
					// $tnt = TntAPI::getTntInterface('syd');
					$tnt = $this->funcTntApiByDepotId($this->dpt_id);
					$result = $tnt->createOneShipment($p);
					if ($result['status']) {
						$this->mdata['shipment_id'][] = $p->id;
					}
					$rs[] = $p;
				}
				break;
			case Org::ORGID_COURIER_SENDLE:
				for ($j = 0; $j < $pkg; $j++) {
					$p = new ImParcel;
					$ar = new Addr;
					$ae = new Addr;
					$ar->name = $this->job->customer->name;
					$ar->tel = $this->job->customer->phone;
					$ar->save();
					$ae->setAttributes($this->mdata['cnee']);
					$ae->save();
					$p->setAttributes(array(
						'agent_id' => $this->job->org_id,
						// 'odpt_id' => 106,
						'odpt_id' => $this->dpt_id,
						'ddpt_id' => $this->dpt_id,
						'cnor_id' => $ar->id,
						'cnee_id' => $ae->id,
						'status' => empty($p->status) ? 10 : $p->status,
						'state' => $ae->state,
						'postcode' => $ae->postcode,
						'pkg' => sizeof(json_decode($pt->mdata['pkg'], true)),
						'currency' => 1,
						'cref' => $this->getNo(),
						'weight' => sprintf('%0.2f', round($fw_wt[$j] * 100) / 100),
					));

					$vol = 0;
					foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
						$items = [];
						$vol += floatval($pk['w']) * floatval($pk['h']) * floatval($pk['d']) * 0.01 * 0.01 * 0.01;
					}
					$p->cbm = number_format($vol / sizeof(json_decode($pt->mdata['pkg'], true)), 2, '.', '');

					$tq = 0;
					foreach ($this->mainTask->items as $itm) {
						$tq += intval($itm->mdata['uq']);
					}
					foreach ($this->mainTask->items as $itm) {
						if (empty($itm->mdata['si'])) {
							continue;
						}

						$s = WmsStock::model()->findByPk($itm->mdata['si']);
						$items['type'][] = 'O';
						$items['g'][] = $s->prod->name;
						$items['b'][] = $s->prod->brand;
						$items['m'][] = $s->prod->model;
						$items['v'][] = $fw_wt[$j] * 20 * $tq / $tw;
						$items['q'][] = $itm->mdata['uq'];
						$items['hs'][] = '';
						$items['coo'][] = 'Australia';
						break;
					}

					$p->eitems = $items;
					$p->hbn = $p->genHbn();
					$p->mdata['wmstask_id'] = $this->link_id;
					// $p->nolog = true;
					$p->save();
					if ($p->id > 0) {
						$sendle = new SendleAPI();
						$result = $sendle->createInternationalOrder($p);
						if (!empty($result['state']) && in_array($result['state'], ['Booking', 'Pickup'])) {
							$p->ref = $result['sendle_reference'];
							$p->save();
							$this->mdata['shipment_id'][] = $p->id;
							$rs[] = $p;

							// save tranship information for Sendle courier
							$ts = new Tranship;
							$ts->pid = $p->id;
							$ts->org_id = Org::ORGID_COURIER_SENDLE; // for Sendle
							$ts->man_id = $p->man_id;
							$ts->type = 80; // shipment transfer to a different delivery courier
							$ts->status = 19; // in finally moving status
							$ts->connote = $p->ref;
							$ts->time = date('Y-m-d H:i:s');
							$ts->mdata['order_id'] = $result['order_id'];
							$ts->mdata['price'] = $result['price'];
							$ts->cost = round(floatval(@$result['price']['gross']['amount']), 2);
							$ts->save();
						}
					}
				}
				break;
			case Org::ORGID_COURIER_DFE_TOP:
				$p->save();
				$this->funcCreateDFEShipment($p);
				if ($p->id > 0) {
					$this->mdata['shipment_id'][] = $p->id;
					$rs[] = $p;
				}
				break;
			case Org::ORGID_COURIER_UBI_AP:
				if (!empty($pt) && !empty($pt->mdata['pkg'])) {
					foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
						$p->packs[]=[
							'weight'=>$pk['wt'],
							'length'=>$pk['d'],
							'height'=>$pk['h'],
							'width'=>$pk['w'],
							'reference'=>0,
							'cbm'=>($pk['d']*$pk['h']*$pk['w'])/1000000
						];
					}
				} else {
					
				}

				$p->ref = null;
				$p->save();

				if ($this->dpt_id == Org::PCAE_DEPARTMENT_SYDNEY){
					$orgRateCourier= OrgRate::model()->findByPk(Org::TPLORGRATE_COURIER_UBI_AP_SYD);
				}else if($this->dpt_id == Org::PCAE_DEPARTMENT_MELBOURNE){
					$orgRateCourier= OrgRate::model()->findByPk(Org::TPLORGRATE_COURIER_UBI_AP_MEL);
				}

				$p->mdata['org_rate_id'] = $orgRateCourier->id;

				$p->createUbiLabel($orgRateCourier);
				$this->mdata['trackingNoForScan']=$p->hbn;
				if ($p->id > 0) {
					$this->mdata['shipment_id'][] = $p->id;
					$rs[] = $p;
				}

				break;
			case Org::ORGID_COURIER_BORDER:
				if (!empty($pt) && !empty($pt->mdata['pkg'])) {
					foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
						$p->packs[]=[
							'weight'=>$pk['wt'],
							'length'=>$pk['d'],
							'height'=>$pk['h'],
							'width'=>$pk['w'],
							'reference'=>0,
							'cbm'=>($pk['d']*$pk['h']*$pk['w'])/1000000
						];
					}
				} else {
					
				}
				$p->ref = null;
				$p->save();

				if ($this->dpt_id == Org::PCAE_DEPARTMENT_SYDNEY){
					$orgRateCourier= OrgRate::model()->findByPk(Org::TPLORGRATE_COURIER_UBI_BORDER_SYD);
				}
				// }else if($this->dpt_id == Org::PCAE_DEPARTMENT_MELBOURNE){
				// 	$orgRateCourier= OrgRate::model()->findByPk(Org::TPLORGRATE_COURIER_UBI_AP_MEL);
				// }
				$p->mdata['org_rate_id'] = $orgRateCourier->id;
				$p->createUbiLabel($orgRateCourier);
				$this->mdata['trackingNoForScan']=$p->mdata['barcode'];
				if ($p->id > 0) {
					$this->mdata['shipment_id'][] = $p->id;
					$rs[] = $p;
				}
				break;

			case Org::ORGID_COURIER_TOLL_IPEC:
				if (!empty($pt) && !empty($pt->mdata['pkg'])) {
					foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
						$p->packs[]=[
							'weight'=>$pk['wt'],
							'length'=>$pk['d'],
							'height'=>$pk['h'],
							'width'=>$pk['w'],
							'reference'=>0,
							'cbm'=>($pk['d']*$pk['h']*$pk['w'])/1000000
						];
					}
				} else {
					
				}

				$p->ref = null;
				$p->save();

				if ($this->dpt_id == Org::PCAE_DEPARTMENT_SYDNEY){
					$orgRateCourier= OrgRate::model()->findByPk(Org::TPLORGRATE_COURIER_MYTOLL_SYD);
				}else if($this->dpt_id == Org::PCAE_DEPARTMENT_MELBOURNE){
					$orgRateCourier= OrgRate::model()->findByPk(Org::TPLORGRATE_COURIER_MYTOLL_MEL);
				}else if($this->dpt_id == Org::PCAE_DEPARTMENT_BRISBANE){
					$orgRateCourier= OrgRate::model()->findByPk(Org::TPLORGRATE_COURIER_MYTOLL_BNE);
				}
				$p->mdata['org_rate_id'] = $orgRateCourier->id;
				$type = MyTollAPI::RATE_TYPE[$orgRateCourier->code];
				if(!empty($type))
				{
					 $p->createMyTollLabel($orgRateCourier->id,$type);
				}
				$this->mdata['trackingNoForScan']=$p->mdata['barcode'];
				if ($p->id > 0) {
					$rt = ChooseShipment::courierCanDelivery(3752,$p,true,$orgRateCourier->id,"");
					if($rt->success){
						$this->mdata['shipment_id'][] = $p->id;
						$rs[] = $p;
					}
				}
				break;
			case Org::ORGID_COURIER_UBI_TOLL:
					if (!empty($pt) && !empty($pt->mdata['pkg'])) {
						foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
							$p->packs[]=[
								'weight'=>$pk['wt'],
								'length'=>$pk['d'],
								'height'=>$pk['h'],
								'width'=>$pk['w'],
								'reference'=>0,
								'cbm'=>($pk['d']*$pk['h']*$pk['w'])/1000000
							];
						}
					} else {
						
					}
	
					$p->ref = null;
					$p->save();
	
					if ($this->dpt_id == Org::PCAE_DEPARTMENT_SYDNEY){
						$orgRateCourier= OrgRate::model()->findByPk(Org::TPLORGRATE_COURIER_UBI_TOLL_SYD);
					}else if($this->dpt_id == Org::PCAE_DEPARTMENT_MELBOURNE){
						$orgRateCourier= OrgRate::model()->findByPk(Org::TPLORGRATE_COURIER_UBI_TOLL_MEL);
					}
					$p->mdata['org_rate_id'] = $orgRateCourier->id;
					$p->createUbiLabel($orgRateCourier);
					$this->mdata['trackingNoForScan']=$p->mdata['barcode'];
					if ($p->id > 0) {
						$this->mdata['shipment_id'][] = $p->id;
						$rs[] = $p;
					}
					break;
			case Org::ORGID_COURIER_SF:
				$p->save();
				
				// $p->eitems['g'][] = $this->mainTask->ref;
				// $p->eitems['q'][] = sizeof(json_decode($pt->mdata['pkg']));
				// $p->eitems['v'][] = '1';
				
				$cl = $p->createSFLabel(ImportChargeCode::SF_TRUCK_ID,'syd');
				$this->mdata['trackingNoForScan']=$p->ref;
				if ($p->id > 0) {
					$this->mdata['shipment_id'][] = $p->id;
					$rs[] = $p;
				}
				break;
			case Org::ORGID_COURIER_EIZ_TOLL:
				if (!empty($pt) && !empty($pt->mdata['pkg'])) {
					foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
						$p->packs[]=[
							'weight'=>$pk['wt'],
							'length'=>$pk['d'],
							'height'=>$pk['h'],
							'width'=>$pk['w'],
							'reference'=>0,
							'cbm'=>($pk['d']*$pk['h']*$pk['w'])/1000000
						];
					}
				} else {
					
				}

				$p->ref = null;
				$p->save();
				
				if($this->dpt_id== Org::PCAE_DEPARTMENT_SYDNEY){
					$p->createEizLabel(ImportChargeCode::EIZ_TOLL_ID,EizAPI::RATE_TYPE[ImportChargeCode::EIZ_TOLL_SYD_CODE]);
				}
				else if($this->dpt_id == Org::PCAE_DEPARTMENT_MELBOURNE){ 
					$p->createEizLabel(ImportChargeCode::EIZ_TOLL_MEL_ID,EizAPI::RATE_TYPE[ImportChargeCode::EIZ_TOLL_MEL_CODE]);
				}
				else if($this->dpt_id== Org::PCAE_DEPARTMENT_BRISBANE){
					$p->createEizLabel(ImportChargeCode::EIZ_TOLL_BNE_ID,EizAPI::RATE_TYPE[ImportChargeCode::EIZ_TOLL_BNE_CODE]);
				}
				$this->mdata['trackingNoForScan']=$p->ref;
				//stdclass to array
				$jsonMdata = json_encode($p->mdata);
				$objMdata = json_decode($jsonMdata, true);
				$p->mdata = $objMdata;
				
				if ($p->id > 0) {
					$this->mdata['shipment_id'][] = $p->id;
					$rs[] = $p;
				}
				
				break;
			case "3702":
				if (!empty($pt) && !empty($pt->mdata['pkg'])) {
					foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
						$p->packs[]=[
							'weight'=>$pk['wt'],
							'length'=>$pk['d'],
							'height'=>$pk['h'],
							'width'=>$pk['w'],
							'reference'=>0,
							'cbm'=>($pk['d']*$pk['h']*$pk['w'])/1000000
						];
					}
				} else {
						
				}
	
				$p->ref = null;
				$p->mdata['oref'] = "";
				$p->save();
				
				if($this->dpt_id== Org::PCAE_DEPARTMENT_SYDNEY){
					if(!empty($this->mdata['trackingno']) && ($this->mdata['trackingno']!='')){
						$p->printEizLabel(ImportChargeCode::EIZ_ALLIED_ID,$this->mdata['trackingno']);
					}
					else{
						$code = OrgRate::model()->findByPk(ImportChargeCode::EIZ_ALLIED_ID)->code;
						$type = EizAPI::RATE_TYPE[$code];
						$p->createEizLabel(ImportChargeCode::EIZ_ALLIED_ID,$type);
					}
				}
				else if($this->dpt_id == Org::PCAE_DEPARTMENT_MELBOURNE){ 
					if(!empty($this->mdata['trackingno']) && ($this->mdata['trackingno']!='')){
						$p->printEizLabel(ImportChargeCode::EIZ_ALLIED_MEL_ID,$this->mdata['trackingno']);
					}
					else{
						$code = OrgRate::model()->findByPk(ImportChargeCode::EIZ_ALLIED_MEL_ID)->code;
						$type = EizAPI::RATE_TYPE[$code];
						$p->createEizLabel(ImportChargeCode::EIZ_ALLIED_MEL_ID,$type);
					}
				}
				else if($this->dpt_id== Org::PCAE_DEPARTMENT_BRISBANE){
					if(!empty($this->mdata['trackingno']) && ($this->mdata['trackingno']!='')){
						$p->printEizLabel(ImportChargeCode::EIZ_ALLIED_BNE_ID,$this->mdata['trackingno']);
					}
					else{
						$code = OrgRate::model()->findByPk(ImportChargeCode::EIZ_ALLIED_BNE_ID)->code;
						$type = EizAPI::RATE_TYPE[$code];
						$p->createEizLabel(ImportChargeCode::EIZ_ALLIED_BNE_ID,$type);
					}
				}
				$this->mdata['trackingNoForScan']=$p->ref;
	
					//stdclass to array
				$jsonMdata = json_encode($p->mdata);
				$objMdata = json_decode($jsonMdata, true);
				$p->mdata = $objMdata;
					
				if ($p->id > 0) {
					$this->mdata['shipment_id'][] = $p->id;
					$rs[] = $p;
				}
					
				break;
		}

		//only for 3PL aupostexpress && UBI AP;
		if($this->mdata['courier']==Org::ORGID_COURIER_AUPOST_EXPRESS){
			$courierId = Org::ORGID_COURIER_AUPOST;
		}else if($this->mdata['courier']==Org::ORGID_COURIER_UBI_AP){
			$courierId = Org::ORGID_COURIER_UBI;
		}else if($this->mdata['courier']==Org::ORGID_COURIER_EIZ_ALLIED){
			$courierId = Org::ORGID_COURIER_EIZ;
		}else{
			$courierId = $this->mdata['courier'];
		}
		
		$this->mdata['shipment_courier_id'] = $this->mdata['courier'];

		if ($p->type == 10) {
			$this->bwf = $this->bwf | 64;
		}

		$this->save();

		// eva, create gatepass record
		foreach ($rs as $r) {
			$gs = new GatepassShipment;
			$gs->fid = $r->id;
			$gs->sno = $r->pkg;
			$gs->courier_id = $courierId;
			// if($this->mdata['courier']=='3702'){
			// 	$gs->courier_id = '3701';
			// }else{
			// 	$gs->courier_id = $this->mdata['courier'];
			// }
			$gs->shipment_status = $r->status;
			$gs->connote_no = $r->ref;
			$gs->warehouse = $this->dpt_id;
			$gs->scan_time = date('Y-m-d H:i:s');
			$gs->status = 0;
			$gs->mdata['description'] = '3PL';
			$gs->save();
		}

		return $rs;
	}
	
	private function funcGenerateEparcelNumberByDepotId($numDepotId){
		$strEparcelnumber = '';
		if($numDepotId == Org::PCAE_DEPARTMENT_MELBOURNE){ 
			$strEparcelnumbe = ImParcel::genEparcelNo(101, '33EVH');
		}
		else{
			$strEparcelnumbe = ImParcel::genEparcelNo();
		}
		return $strEparcelnumbe;
	}
	
	private function funcTntApiByDepotId($numDepotId){
		if($numDepotId == Org::PCAE_DEPARTMENT_MELBOURNE){ 
			return TntAPI::getTntInterface(TntAPI::str_mel_top);
		}
		else if($numDepotId == Org::PCAE_DEPARTMENT_BRISBANE){
			return TntAPI::getTntInterface(TntAPI::str_bne_top);
		}
		else{
			return TntAPI::getTntInterface(TntAPI::str_syd_top);
		}
	}
	

	public function createReturnLabel($tw)
	{
		foreach ($tw as $w) {
		$p = new ImParcel;
		$ae = new Addr;
		$ae->name = $this->job->customer->name . ((!empty($this->job->customer->extra['sp_id']) && $this->job->customer->extra['sp_id'] == 305) ? ' - 3PL' : '');
		$ae->address = Org::IM_COMPANY_ADDRESS;
		$ae->suburb = Org::IM_COMPANY_SUBURB;
		$ae->city = Org::IM_COMPANY_SUBURB;
		$ae->state = Org::IM_COMPANY_STATE;
		$ae->postcode = Org::IM_COMPANY_POSTCODE;
		$ae->country = 'Australia';
		// $ae->tel = $this->job->customer->phone;
		$ae->tel = Org::IM_COMPANY_PHONE_SHOW;
		$ae->email = 'nero@toplogistics.com.au';
		$ae->save();

		$ar = new Addr;
		$ar->setAttributes($this->mdata['cnee']);
		$ar->save();

		$p->setAttributes(array(
			'agent_id' => $this->job->org_id,
			// 'odpt_id' => 106,
			'odpt_id' => $this->dpt_id,
			'ddpt_id' => $this->dpt_id,
			'cnor_id' => $ar->id,
			'cnee_id' => $ae->id,
			'status' => empty($p->status) ? 10 : $p->status,
			'state' => $ae->state,
			'postcode' => $ae->postcode,
			'pkg' => 1,
			'currency' => 1,
			'cref' => $this->getNo(),
			'weight' => sprintf('%0.2f', round($w * 100) / 100),
		));

		$items = [];
		foreach ($this->mainTask->items as $item) {
			$items['g'][] = $item->mdata['sn'];
			$items['v'][] = '';
			$items['q'][] = $item->mdata['uq'];
			$items['hs'][] = '';
		}
		$p->eitems = $items;
		$p->hbn = $p->genHbn();

		// $p->nolog = true;
		$p->mdata['aupost_return'] = 1;
		$p->mdata['chargecode'] = 3131;
		$p->save();
		$p->ref = ImParcel::genEparcelNo();
		$p->save();

		if ($p->id > 0) {
			$this->bwf = $this->bwf | 64;
			$this->mdata['return_shipment_id'][] = $p->id;
			$this->save();
		}
		}
	}

	public function getItemsWeights()
	{
		$tw = 0;
		$listWmsTaskItem = WmsTaskItem::model()->findAll('task_id=:task_id',['task_id'=>$this->link_id]);
		foreach ($listWmsTaskItem as $objWmsTaskItem){
			$objProdItem = $objWmsTaskItem->mdata;				
			if(empty($objProdItem['si'])){
				continue;
			}
					
			$objWmsStock = WmsStock::model()->findByPk($objProdItem['si']);
			$objProd = WmsProd::model()->findByPk($objWmsStock->prod_id);
			$numQty = $objProdItem['uq'];
			for($i=0;$i<$numQty;$i++){
				$tw += $objProd->weight/1000;
			}
		}
		return $tw;
	}

	//Author:Nero Date:2021/6/21 Description: function chooseCourier rewrite.
	public function chooseCourier()
	{
		if ($this->type != 2120 || empty($this->mdata['cnee'])) {
			return;
		}

		$pm = preg_match('/\w+/', $this->mdata['cnee']['state']) ? 'ImParcel' : 'ExParcel';
		if ($pm == 'ImParcel') {
			$tw = 0;
			$ctw = 0;
			$cbm = 0;
			$maxLength = 0;
			$listWmsTaskItem = WmsTaskItem::model()->findAll('task_id=:task_id',['task_id'=>$this->link_id]);
			foreach ($listWmsTaskItem as $objWmsTaskItem){
				$objProdItem = $objWmsTaskItem->mdata;				
				if(empty($objProdItem['si'])){
					continue;
				}
				
				$objWmsStock = WmsStock::model()->findByPk($objProdItem['si']);
				$objProd = WmsProd::model()->findByPk($objWmsStock->prod_id);

				$arrDim = json_decode($objProd->dim);
				if(!empty($arrDim)){
					foreach($arrDim as $key => $value){
						if(floatval($value)>$maxLength){
							$maxLength = floatval($value);
						}
					}
				}
				
				$numQty = $objProdItem['uq'];
				for($i=0;$i<$numQty;$i++){
					$tw += floatval($objProd->weight) / 1000;
					$ctw += (floatval($objProd->cbm) / 1000000) * 250;
					$cbm += (floatval($objProd->cbm) / 1000000);
				}
			}
			
			
			$courierCount=0;
			$org = Org::model()->findByPk($this->job->org_id);
			foreach (['tla', 'auspost', 'fastway', 'tnt', 'eiztoll', 'sf', 'allied','ubitoll','ubiaupost','ubiborder'] as $k) {
				if (!empty($org->extra[$k])) {
					$courierCount++;
				}
			}
			
			if ($courierCount == 1) {
				if(!empty($org->extra['tla'])){
					$this->mdata['courier'] = Org::ORGID_COURIER_PCA;
				}
				if(!empty($org->extra['auspost'])){
					$this->mdata['courier'] = Org::ORGID_COURIER_AUPOST;
				}
				if(!empty($org->extra['fastway'])){
					$this->mdata['courier'] = Org::ORGID_COURIER_FASTWAY;
				}
				if(!empty($org->extra['tnt'])){
					$this->mdata['courier'] = Org::ORGID_COURIER_TNT;
				}
				if(!empty($org->extra['eiztoll'])){
					$this->mdata['courier'] = Org::ORGID_COURIER_EIZ;
				}
				if(!empty($org->extra['sf'])){
					$this->mdata['courier'] = Org::ORGID_COURIER_SF;
				}
				if(!empty($org->extra['allied'])){
					//Distinguish ORG
					$this->mdata['courier'] = 3702;
					//$this->mdata['courier'] = Org::ORGID_COURIER_ALLIED;
				}
				if(!empty($org->extra['ubitoll'])){
					$this->mdata['courier'] = Org::ORGID_COURIER_UBI_TOLL;
				}
				if(!empty($org->extra['ubiaupost'])){
					$this->mdata['courier'] = Org::ORGID_COURIER_UBI_AP;
				}
				if(!empty($org->extra['ubiborder'])){
					$this->mdata['courier'] = Org::ORGID_COURIER_BORDER;
				}
			} else {

				//I hom
				if ($org->id=="3817"){
					//lower cost of customer
					$this->mdata['courier'] = $this->selectLowerCostOf3PLCourier($this->mdata['cnee']['postcode'],$tw,$this->dpt_id,$ctw,$cbm,floatval($maxLength/100));
				}else{
					//lower cost of us
					$this->mdata['courier'] = $this->selectBest3PLCourier($this->mdata['cnee']['suburb'], $this->mdata['cnee']['postcode'], $this->mdata['cnee']['state'], $tw,$this->dpt_id,1,$ctw,$cbm,floatval($maxLength/100));
				}

			}
			$this->save();

		} else {
			//exparcel
			$this->mdata['courier'] = '0';
			$this->save();
		}
	}

	public function selectAuCourier($suburb, $postcode, $state, $weight, $packs = 1)
	{
		$minCost = 99999; // in order to get minimum one
		$cheapOrgRate = null;
		$getCheapOrgRateError = '';
		$couriers = [
			ImportChargeCode::SYDNEY_AUPOST_ID,
			ImportChargeCode::FASTWAY_ID_OLD,
		];

		$tp = new ImParcel;
		$tp->weight = $weight;
		$tp->pkg = $packs;
		$tp->cnee = new Addr;
		$tp->cnee->state = $state;
		$tp->cnee->postcode = $postcode;
		$tp->cnee->suburb = $suburb;
		$selectedOrgRates = [];
		foreach ($couriers as $courier) {
			$orgRate = OrgRate::model()->findByPk($courier);
			if (ChooseShipment::courierCanDelivery($orgRate->org_id, $tp, false)) {
				$selectedOrgRates[] = $orgRate;
			}

		}
		foreach ($selectedOrgRates as $orgrate) {
			// get cost based on org rate
			$cost = ImcoConsol::getCourierCostPrice($orgrate, $postcode, $weight, $packs);
			if ($cost > 0 && $cost < $minCost) {
				$minCost = $cost;
				$cheapOrgRate = $orgrate;
			}
		}
		if (!empty($cheapOrgRate)) {
			return $cheapOrgRate->org_id;
		} else {
			return Org::ORGID_COURIER_PCA;
		}
	}

	public function selectLowerCostOf3PLCourier($postcode,$weight,$dpt_id,$cweight,$cbm,$maxlength)
	{
		$minCost = 99999; // in order to get minimum one
		$cheapOrgRate = '0';

		$couriers = [];
		$org = Org::model()->findByPk($this->job->org_id);

		$quote = WmsOrgQuote::model()->find(['condition' => 'org_id = :org_id AND status = 1', 'params' => [':org_id' => $this->job->org_id], 'order' => 'id DESC']);

		if($dpt_id == Org::TLA_DEPARTMENT_SYDNEY){
			if(!empty($org->extra['ubiaupost'])&& $weight < 22 && $maxlength<1.05 && $cbm<=0.13){
				$couriers[Org::ORGID_COURIER_UBI_AP] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE_UAP];
			}
			if(!empty($org->extra['tla'])){
				$couriers[Org::ORGID_COURIER_TLA] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE_TLACARGO];
			}
			if(!empty($org->extra['auspost'])&& $weight < 22 && $maxlength<1.05 && $cbm<=0.13){
				$couriers[Org::ORGID_COURIER_AUPOST] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE_AUPOSR];
			}
			if(!empty($org->extra['fastway'])){
				$couriers[Org::ORGID_COURIER_FASTWAY] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE_FASTWAY];
			}
			if(!empty($org->extra['tnt'])){
				$couriers[Org::ORGID_COURIER_TNT] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE_TNT];
			}
			if(!empty($org->extra['eiztoll'])&& $weight >= 22){
				$couriers[Org::ORGID_COURIER_EIZ_TOLL] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE_ETOLL];
			}
			if(!empty($org->extra['sf'])){
				$couriers[Org::ORGID_COURIER_SF] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE_SF];
			}
			if(!empty($org->extra['allied'])){
				//skip allied
				//$couriers[Org::ORGID_COURIER_EIZ_ALLIED] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE_EALLIED];
			}
			if(!empty($org->extra['ubitoll'])&& $weight >= 22){
				$couriers[Org::ORGID_COURIER_UBI_TOLL] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE_UTOLL];
			}
			if(!empty($org->extra['ubiborder'])){
				$couriers[Org::ORGID_COURIER_UBI_TOLL] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE_BORDER];
			}
			
		}else if($dpt_id==Org::TLA_DEPARTMENT_MELBOURNE){
			if(!empty($org->extra['ubiaupost'])&& $weight < 22 && $maxlength<1.05 && $cbm<=0.13){
				$couriers[Org::ORGID_COURIER_UBI_AP] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_MEL_CHARGECODE_UAP];
			}
			if(!empty($org->extra['tla'])){
				$couriers[Org::ORGID_COURIER_TLA] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_MEL_CHARGECODE_TLACARGO];
			}
			if(!empty($org->extra['auspost'])&& $weight < 22 && $maxlength<1.05 && $cbm<=0.13){
				$couriers[Org::ORGID_COURIER_AUPOST] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_MEL_CHARGECODE_AUPOSR];
			}
			if(!empty($org->extra['fastway'])){
				$couriers[Org::ORGID_COURIER_FASTWAY] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_MEL_CHARGECODE_FASTWAY];
			}
			if(!empty($org->extra['tnt'])){
				$couriers[Org::ORGID_COURIER_TNT] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_MEL_CHARGECODE_TNT];
			}
			if(!empty($org->extra['eiztoll'])&& $weight > 22){
				$couriers[Org::ORGID_COURIER_EIZ_TOLL] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_MEL_CHARGECODE_ETOLL];
			}
			if(!empty($org->extra['sf'])){
				$couriers[Org::ORGID_COURIER_SF] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_MEL_CHARGECODE_SF];
			}
			if(!empty($org->extra['allied'])){
				//allied
				//$couriers[Org::ORGID_COURIER_EIZ_ALLIED] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_MEL_CHARGECODE_EALLIED];
			}
			if(!empty($org->extra['ubitoll'])&& $weight > 22){
				$couriers[Org::ORGID_COURIER_UBI_TOLL] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_MEL_CHARGECODE_UTOLL];
			}
			if(!empty($org->extra['ubiborder'])){
				$couriers[Org::ORGID_COURIER_UBI_TOLL] = $quote->mdata[WmsOrgQuote::QUOTE_COURIER_MEL_CHARGECODE_BORDER];
			}
		}

		foreach ($couriers as $key => $courier) {
			// get cost based on org rate
			if(!empty($courier)){
				if($key == Org::ORGID_COURIER_UBI_AP || $key == Org::ORGID_COURIER_AUPOST || $key == Org::ORGID_COURIER_FASTWAY){
					$tw = $weight;
				}else{
					$tw = max($cweight,$weight);
				}

				$cc = ImportChargeCode::model()->find('chargecode = :chargecode AND status = 1', [':chargecode' => $courier]);
				$cost = $this->getChargeByChargecode($tw, $postcode, $cc->chargecode, true);
				if ($cost > 0 && $cost < $minCost) {
					$minCost = $cost;
					$cheapOrgRate = $key;
				}
			}
		}

		if (!empty($cheapOrgRate)) {
			return $cheapOrgRate;
		}
	}

	public function deliveryTaskSurCharge($courier,$length,$width,$height,$weight){
		$listlengthLimit =[];
		$listDeadWeightLimit =[];
		$listCubicWeightLimit =[];
		if($courier=='114'){
			$numlengthSurcharge = 0;
			$listlengthLimit = [//m /per piece
				20=>[1.2,2.4],
				50=>[2.4,3.6],
				100=>[3.6,9999],
				//450=>[4,9999],
			];
			$listDeadWeightLimit = [//KG per shipment
				//=>[35,9999],
				//43.5=>[56,90],
				//85=>[90,9999],
			];
			$listCubicWeightLimit = [// per shipment
				//50=>[175,9999],
				//43.5=>[56,135],
				//85=>[135,9999],
			];
		}else if($courier=='3079'){
			$listlengthLimit = [//m /per piece
				12=>[1.2,1.8],
				50=>[1.8,9999],
				//11.5=>[2,3],
				//24.5=>[3,9999],
				//450=>[4,9999],
			];
			$listDeadWeightLimit = [//KG per shipment
				12=>[30,35],
				50=>[35,9999],
				//43.5=>[56,90],
				//85=>[90,9999],
			];
			$listCubicWeightLimit = [// per shipment
				50=>[175,9999],
				//43.5=>[56,135],
				//85=>[135,9999],
			];
		}else if($courier=='3701'){
			$listlengthLimit = [//m
				14=>[1.2,1.8],
				75=>[1.8,3],
				105=>[3,4],
				450=>[4,9999],
			];
			$listDeadWeightLimit = [//KG
				14=>[30,35],
				75=>[35,85],
				105=>[85,9999],
			];
			$listCubicWeightLimit = [
				14=>[30,35],
				75=>[35,85],
				105=>[85,9999],
			];
		}else if($courier=='3702'){
			$listlengthLimit = [//m
				5.4=>[1.2,2.39],
				11.93=>[2.4,3.59],
				25.40=>[3.6,4.19],
				//450=>[4,9999],
			];
			$listDeadWeightLimit = [//KG
				6.95=>[0,23],
				13.50=>[23,56],
				47=>[56,90],
				92=>[90,9999]
			];
			$listCubicWeightLimit = [
				6.95=>[0,23],
				13.50=>[23,56],
				47=>[56,135],
				92=>[135,9999]
			];
		}

		$numlengthSurcharge = 0;
		$numlengthMax =  max($length, $width, $height)/100; //cm to m
		foreach($listlengthLimit as $numPrice => $listRange){
			$numFrom = $listRange[0];
			$numTo = $listRange[1];
			if($numlengthMax >= $numFrom && $numlengthMax < $numTo){
				$numlengthSurcharge = $numPrice;
			}
		}

		$numDeadWeightSurcharge = 0;
		$numDeadWeight=  $weight;
		foreach($listDeadWeightLimit as $numPrice => $listRange){
			$numFrom = $listRange[0];
			$numTo = $listRange[1];
			if($numDeadWeight >= $numFrom && $numDeadWeight < $numTo){
					$numDeadWeightSurcharge = $numPrice;
				}
		}

		$numCubicWeightSurcharge = 0;
		$numCubicWeight=  $length * $width * $height*0.00025; // cm m
		foreach($listCubicWeightLimit as $numPrice => $listRange){
			$numFrom = $listRange[0];
			$numTo = $listRange[1];
			if($numCubicWeight >= $numFrom && $numCubicWeight < $numTo){
				$numCubicWeightSurcharge = $numPrice;
			}
		}

		return  max($numlengthSurcharge,$numDeadWeightSurcharge,$numCubicWeightSurcharge);
	}

	public function chooseOrgRate($courier,$dpt_id){

		$selectedOrgRate ="";
		if($dpt_id == 106){
			if($courier == '114'){
				$selectedOrgRate = '70';
			}
			if($courier == '101'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_AUSPOST_SYD;
			}
			if($courier == '115'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_FASTWAY_SYD ;
			}
			if($courier == '976'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_TNT_SYD;
			}
			if($courier == '3701'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_EIZTOLL_SYD;
			}
			if($courier == '3590'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_SF_SYD;
			}
			if($courier == '3702'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_ALLIED_SYD;
			}
			if($courier == '3079'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_UBI_TOLL_SYD;
			}
			if($courier == '3958'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_AUSPOST_SYD_EXPRESS;
			}
			if($courier == '3994'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_UBI_AP_SYD;
			}
			if($courier == '3447'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_UBI_BORDER_SYD;
			}
		}else if($dpt_id==218){
			if($courier == '114'){
				$selectedOrgRate = '525';
			}
			if($courier == '101'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_AUSPOST_MEL;
			}
			if($courier == '115'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_FASTWAY_MEL ;
			}
			if($courier == '976'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_TNT_MEL;
			}
			if($courier == '3701'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_EIZTOLL_MEL;
			}
			if($courier == '3590'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_SF_MEL;
			}
			if($courier == '3702'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_ALLIED_MEL;
			}
			if($courier == '3079'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_UBI_TOLL_MEL;
			}
			if($courier == '3994'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_UBI_AP_MEL;
			}
			if($courier == '3447'){
				$selectedOrgRate = Org::TPLORGRATE_COURIER_UBI_BORDER_MEL;
			}
		}

		if(!empty($selectedOrgRate)){
			return OrgRate::model()->findByPk($selectedOrgRate);
		}
	}

	public function selectBest3PLCourier($suburb, $postcode, $state, $weight,$dpt_id, $packs = 1, $cweight,$cbm,$maxlength)
	{
		$minCost = 99999; // in order to get minimum one
		$cheapOrgRate = null;
		$getCheapOrgRateError = '';

		$couriers = [];
		$org = Org::model()->findByPk($this->job->org_id);

		if($dpt_id == 106){
			if(!empty($org->extra['tla'])){
				$couriers[] = '70';
			}
			if(!empty($org->extra['ubiaupost'])&& $weight < 22 && $maxlength<1.05 && $cbm<=0.13){
				$couriers[] = Org::TPLORGRATE_COURIER_UBI_AP_SYD;
			}
			if(!empty($org->extra['auspost'])&& $weight < 22 && $maxlength<1.05 && $cbm<=0.13){
				$couriers[] = Org::TPLORGRATE_COURIER_AUSPOST_SYD;
			}
			if(!empty($org->extra['fastway'])){
				$couriers[] = Org::TPLORGRATE_COURIER_FASTWAY_SYD;
			}
			if(!empty($org->extra['tnt'])){
				$couriers[] = Org::TPLORGRATE_COURIER_TNT_SYD;
			}
			if(!empty($org->extra['eiztoll'])){
				$couriers[] = Org::TPLORGRATE_COURIER_EIZTOLL_SYD;
			}
			if(!empty($org->extra['sf'])){
				$couriers[] = Org::TPLORGRATE_COURIER_SF_SYD;
			}
			if(!empty($org->extra['allied'])){
				//skip addlid
				//$couriers[] = Org::TPLORGRATE_COURIER_ALLIED_SYD;
			}
			if(!empty($org->extra['ubitoll'])){
				$couriers[] = Org::TPLORGRATE_COURIER_UBI_TOLL_SYD;
			}
			if(!empty($org->extra['ubiborder'])){
				$couriers[] = Org::TPLORGRATE_COURIER_UBI_BORDER_SYD;
			}
		}else if($dpt_id==218){
			if(!empty($org->extra['tla'])){
				$couriers[] = '525';
			}
			if(!empty($org->extra['ubiaupost'])&& $weight < 22 && $maxlength<1.05 && $cbm<=0.13){
				$couriers[] = Org::TPLORGRATE_COURIER_UBI_AP_MEL;
			}
			if(!empty($org->extra['auspost'])&& $weight < 22 && $maxlength<1.05 && $cbm<=0.13){
				$couriers[] = Org::TPLORGRATE_COURIER_AUSPOST_MEL;
			}
			if(!empty($org->extra['fastway'])){
				$couriers[] = Org::TPLORGRATE_COURIER_FASTWAY_MEL ;
			}
			if(!empty($org->extra['tnt'])){
				$couriers[] = Org::TPLORGRATE_COURIER_TNT_MEL;
			}
			if(!empty($org->extra['eiztoll'])){
				$couriers[] = Org::TPLORGRATE_COURIER_EIZTOLL_MEL;
			}
			if(!empty($org->extra['sf'])){
				$couriers[] = Org::TPLORGRATE_COURIER_SF_MEL;
			}
			if(!empty($org->extra['allied'])){
				//skip allied
				//$couriers[] = Org::TPLORGRATE_COURIER_ALLIED_MEL;
			}
			if(!empty($org->extra['ubitoll'])){
				$couriers[] = Org::TPLORGRATE_COURIER_UBI_TOLL_MEL;
			}
			if(!empty($org->extra['ubiborder'])){
				$couriers[] = Org::TPLORGRATE_COURIER_UBI_BORDER_MEL;
			}
			
		}
		

		$selectedOrgRates = [];
		foreach ($couriers as $courier) {
			$orgRate = OrgRate::model()->findByPk($courier);
			$selectedOrgRates[$courier] = $orgRate;
		}
		foreach ($selectedOrgRates as $key => $orgrate) {

			if($key == Org::TPLORGRATE_COURIER_UBI_AP_SYD || $key ==  Org::TPLORGRATE_COURIER_UBI_AP_MEL || $key == Org::TPLORGRATE_COURIER_AUSPOST_SYD || $key == Org::TPLORGRATE_COURIER_AUSPOST_MEL || $key == Org::TPLORGRATE_COURIER_FASTWAY_SYD || $key == Org::TPLORGRATE_COURIER_FASTWAY_MEL){
				$tw = $weight;
			}else{
				$tw = max($cweight,$weight);
			}
			// get cost based on org rate
			$cost = ImcoConsol::getCourierCostPrice($orgrate, $postcode, $tw, $packs,"",$suburb);
			if ($cost > 0 && $cost < $minCost) {
				$minCost = $cost;
				$cheapOrgRate = $orgrate;
			}
		}
		if (!empty($cheapOrgRate)) {
			//3PL need old org_id to print label
			if($cheapOrgRate->org_id=="3466"){
				return "976";
			}
			if($cheapOrgRate->id=="924"||$cheapOrgRate->id=="934"){
				return "3702";
			}
			if($cheapOrgRate->id=="688"||$cheapOrgRate->id=="684"){
				return "3994";
			}
			return $cheapOrgRate->org_id;
		} else {
			return '0';
		}
	}

	public function getChargeByChargecode($weight, $postcode, $chargecode, $pkg = false)
	{
		$total = 0;
		// based on chargecode to get chargecode ID
		$chargecodeInfo = ImportChargeCode::model()->find('chargecode = :cid', [':cid' => $chargecode]);
		$zoneMap = ZoneMap::model()->find('chargecode_id = :cid AND pc_lo <= :pc AND pc_hi >= :pc', [':cid' => $chargecodeInfo['id'], ':pc' => $postcode]);
		if (!empty($zoneMap)) {
			if ($pkg) {
				$pack = $this->mainTask->packTask;
				$meta = json_decode($pack->meta, true);
				if ($meta && array_key_exists("pkg", $meta)) {
					$pkgs = json_decode($meta['pkg'], true);
				} else {
					$pkgs = array();
				}
				foreach ($pkgs as $pkg) {
					$amount = 0;
					$weight = floatval($pkg['wt']);
					$zoneRates = ZoneRate::model()->findAll('chargecode_id = :cid AND weight_lo <:w AND weight_hi >= :w AND zone = :z AND base+item+perkg > 0', [':cid' => $chargecodeInfo['id'], ':w' => ceil($weight * 10) / 10, ':z' => $zoneMap['z1']]);
					if (empty($zoneRates)) {
						$zoneRates = ZoneRate::model()->findAll('chargecode_id = :cid AND weight_lo <=:w AND weight_hi >= :w AND zone = :z AND base+item+perkg > 0', [':cid' => $chargecodeInfo['id'], ':w' => ceil($weight * 10) / 10, ':z' => $zoneMap['z1']]);
					}
					// get maximum one
					foreach ($zoneRates as $zoneRate) {
						if ($zoneRate['nkg'] > 0) {
							// used to break weight charge  45g==>50g; 43g=>50;   93g=>100g;
							$temp = $zoneRate['base'] + $zoneRate['item'] + $zoneRate['perkg'] * ($zoneRate['nkg'] - fmod($weight, $zoneRate['nkg']) + $weight);
							$this->tempChargeweight = $zoneRate['nkg'] - fmod($weight, $zoneRate['nkg']) + $weight;
						} else {
							$temp = $zoneRate['base'] + $zoneRate['item'] + $zoneRate['perkg'] * $weight;
						}

						if ($zoneRate['minimum'] > 0) {
							$temp = max($temp, $zoneRate['minimum']);
						}
						$amount = max($amount, $temp);
					}
					$total += $amount;
				}
			} else {
				$amount = 0;
				$zoneRates = ZoneRate::model()->findAll('chargecode_id = :cid AND weight_lo <:w AND weight_hi >= :w AND zone = :z AND base+item+perkg > 0', [':cid' => $chargecodeInfo['id'], ':w' => ceil($weight * 10) / 10, ':z' => $zoneMap['z1']]);
				if (empty($zoneRates)) {
					$zoneRates = ZoneRate::model()->findAll('chargecode_id = :cid AND weight_lo <=:w AND weight_hi >= :w AND zone = :z AND base+item+perkg > 0', [':cid' => $chargecodeInfo['id'], ':w' => ceil($weight * 10) / 10, ':z' => $zoneMap['z1']]);
				}
				// get maximum one
				foreach ($zoneRates as $zoneRate) {
					if ($zoneRate['nkg'] > 0) {
						// used to break weight charge  45g==>50g; 43g=>50;   93g=>100g;
						$temp = $zoneRate['base'] + $zoneRate['item'] + $zoneRate['perkg'] * ($zoneRate['nkg'] - fmod($weight, $zoneRate['nkg']) + $weight);
						$this->tempChargeweight = $zoneRate['nkg'] - fmod($weight, $zoneRate['nkg']) + $weight;
					} else {
						$temp = $zoneRate['base'] + $zoneRate['item'] + $zoneRate['perkg'] * $weight;
					}

					if ($zoneRate['minimum'] > 0) {
						$temp = max($temp, $zoneRate['minimum']);
					}
					$amount = max($amount, $temp);
				}
				$total += $amount;
			}
		}
		// no chargecode set
		// we use default charge rate
		// if ($total == 0) {
		// 	if ($weight >= 5) {
		// 		$total = 8.0 + ($weight - 5) * 0.36; // default only
		// 	} else {
		// 		$total = 8.0;
		// 	}
		// }

		return $total;
	}

	public function getChargeByChargecodeInternational($weight, $country, $chargecode)
	{
		$weight = ceil($weight * 2) / 2;
		$total = 0;
		// based on chargecode to get chargecode ID
		$chargecodeInfo = IntlChargeCode::model()->find('chargecode = :cid', [':cid' => $chargecode]);
		$zoneMap = ZoneMapIntl::model()->find('chargecode_id = :cid AND z2 = :country', [':cid' => $chargecodeInfo['id'], ':country' => $country]);
		if (empty($zoneMap)) {
			$zoneMap = ZoneMapIntl::model()->find('chargecode_id = :cid AND z2 = :country', [':cid' => $chargecodeInfo['id'], ':country' => 'OTHER']);
		}
		if (!empty($zoneMap)) {
			$amount = 0;
			$zoneRates = ZoneRateIntl::model()->findAll('chargecode_id = :cid AND weight_lo <:w AND weight_hi >= :w AND zone = :z AND base+item+perkg > 0', [':cid' => $chargecodeInfo['id'], ':w' => ceil($weight * 10) / 10, ':z' => $zoneMap['z1']]);
			if (empty($zoneRates)) {
				$zoneRates = ZoneRateIntl::model()->findAll('chargecode_id = :cid AND weight_lo <=:w AND weight_hi >= :w AND zone = :z AND base+item+perkg > 0', [':cid' => $chargecodeInfo['id'], ':w' => ceil($weight * 10) / 10, ':z' => $zoneMap['z1']]);
			}
			// get maximum one
			foreach ($zoneRates as $zoneRate) {
				if ($zoneRate['nkg'] > 0) {
					// used to break weight charge  45g==>50g; 43g=>50;   93g=>100g;
					$temp = $zoneRate['base'] + $zoneRate['item'] + $zoneRate['perkg'] * ($zoneRate['nkg'] - fmod($weight, $zoneRate['nkg']) + $weight);
					$this->tempChargeweight = $zoneRate['nkg'] - fmod($weight, $zoneRate['nkg']) + $weight;
				} else {
					$temp = $zoneRate['base'] + $zoneRate['item'] + $zoneRate['perkg'] * $weight;
				}

				if ($zoneRate['minimum'] > 0) {
					$temp = max($temp, $zoneRate['minimum']);
				}
				$amount = max($amount, $temp);
			}
			$total += $amount;
		}
		// no chargecode set
		// we use default charge rate
		if ($total == 0) {
			if ($weight >= 5) {
				$total = 8.0 + ($weight - 5) * 0.36; // default only
			} else {
				$total = 8.0;
			}
		}

		return $total;
	}

	public static function waitActionTotalNum($ctno)
	{
		$type = '';
		$items = [];

		$wmstasks = WmsTask::model()->findAll('meta like :no', array(':no' => '%CT%' . $ctno . '%'));
		foreach ($wmstasks as $wmstask) {
			if (in_array($wmstask->type, [1010, 1020, 1030])) {
				$type = 'in';
				foreach ($wmstask->items as $item) {
					if (empty($items[$item->mdata['gn']])) {
						$items[$item->mdata['gn']] = 0;
					}
					$items[$item->mdata['gn']] += floatval($item->waitInNum());
				}
			} else if (in_array($wmstask->type, [3010])) {
				$type = 'out';
				foreach ($wmstask->items as $item) {
					if (empty($items[$item->mdata['sn']])) {
						$items[$item->mdata['sn']] = [];
					}
					$items[$item->mdata['sn']][] = $item->waitOutNum();
				}
			}
		}

		return array('type' => $type, 'items' => $items);
	}

	public static function doneAction($ctno)
	{
		$items = [];

		$wmstasks = WmsTask::model()->findAll('meta like :no', array(':no' => '%CT%' . $ctno . '%'));
		foreach ($wmstasks as $wmstask) {
			$items[] = $wmstask->actionTask->id;
		}

		return $items;
	}

	public function getDeliveryExpiry()
	{
		if (empty($this->deliveryTask)) {
			return '';
		}

		if (empty($this->deliveryTask->mdata['shipment_id'])) {
			$from = date('Y-m-d');
		} else {
			$tracking = Tracking::model()->find(['condition' => 'pid IN (' . implode(',', $this->deliveryTask->mdata['shipment_id']) . ')', 'order' => 'dt ASC']);
			if (empty($tracking)) {
				$from = date('Y-m-d');
			} else {
				$from = date('Y-m-d', strtotime($tracking->dt . ' + 1 day'));
			}
		}

		if (empty($this->deliveryTask->mdata['shipment_id'])) {
			$to = date('Y-m-d');
		} else {
			$tracking = Tracking::model()->find(['condition' => 'pid IN (' . implode(',', $this->deliveryTask->mdata['shipment_id']) . ') AND activity = "Your parcel has been delivered and a signature obtained."', 'order' => 'dt DESC']);
			if (empty($tracking)) {
				$to = date('Y-m-d');
			} else {
				$to = date('Y-m-d', strtotime($tracking->dt));
			}
		}

		$days = 0;
		$temp = $from;
		while ($temp != $to && strtotime($temp) < strtotime($to)) {
			if (!in_array(date('N', strtotime($temp)), [6, 7])) {
				$days ++;
			}
			$temp = date('Y-m-d', strtotime($temp . ' + 1 day'));
		}

		$state = @$this->deliveryTask->mdata['cnee']['state'];
		switch ($state) {
			case 'NSW': {
				if ($days > 3) {
					$msg = '<span style="color:red">Longer than 3 days</span>';
				} else {
					$msg = '<span style="color:green">Less than 3 days</span>';
				}
			}
			break;
			case 'VIC':
			case 'QLD': {
				if ($days > 5) {
					$msg = '<span style="color:red">Longer than 5 days</span>';
				} else {
					$msg = '<span style="color:green">Less than 5 days</span>';
				}
			}
			break;
			default: {
				if ($days > 10) {
					$msg = '<span style="color:red">Longer than 10 days</span>';
				} else {
					$msg = '<span style="color:green">Less than 10 days</span>';
				}
			}
			break;
		}

		return $msg;
	}

	public function getDemand()
	{
		if (!empty($this->mdata['pi_cargo'])) {
		} else if (!empty($this->mdata['simple_in'])) {
			if (!empty($this->mdata['si_expect'])) {
				$this->mdata['pi_expect'] = $this->mdata['si_expect'];
			} else {
				$this->mdata['pi_expect'] = @$this->items[0]->mdata['uq'];
			}
			$this->mdata['pi_eta'] = @$this->mdata['si_eta'];
		}

		return array(
			'pi_expect' => @$this->mdata['pi_expect'],
			'pi_stockin' => !empty($this->mdata['pi_stockin']) ? ($this->status == 99 ? '<span class="icon icon-check"></span>' : '<div class="icon" style="background-position: 0 -672px">') : '',
			'pi_photo' => !empty($this->mdata['pi_photo']) ? ($this->status == 99 ? '<span class="icon icon-check"></span>' : '<div class="icon" style="background-position: 0 -672px">') : '',
			'pi_weight' => !empty($this->mdata['pi_weight']) ? ($this->status == 99 ? '<span class="icon icon-check"></span>' : '<div class="icon" style="background-position: 0 -672px">') : '',
			'pi_count' => !empty($this->mdata['pi_count']) ? ($this->status == 99 ? '<span class="icon icon-check"></span>' : '<div class="icon" style="background-position: 0 -672px">') : '',
			'pi_batch' => !empty($this->mdata['pi_batch']) ? ($this->status == 99 ? '<span class="icon icon-check"></span>' : '<div class="icon" style="background-position: 0 -672px">') : '',
			'pi_expiry' => !empty($this->mdata['pi_expiry']) ? ($this->status == 99 ? '<span class="icon icon-check"></span>' : '<div class="icon" style="background-position: 0 -672px">') : '',
			'pi_split' => !empty($this->mdata['pi_split']) ? ($this->status == 99 ? '<span class="icon icon-check"></span>' : '<div class="icon" style="background-position: 0 -672px">') : '',
			'pi_eta' => @$this->mdata['pi_eta'],
			'pi_etd' => @$this->mdata['pi_etd'],
			'pi_other' => @$this->mdata['pi_other'],
		);
	}

	public function updateMeta($nolog=true)
	{
		$this->nolog = $nolog;
		$this->meta = json_encode($this->mdata);
		$this->update(['meta']);
	}

	public function getLog()
	{
		$wip_log = Log::model()->find(['condition' => 'lid = :lid AND model ="WmsTask" AND meta LIKE "%WIP%"', 'params' => [':lid' => $this->id], 'order' => 'id ASC']);
		$com_log = Log::model()->find(['condition' => 'lid = :lid AND model ="WmsTask" AND meta LIKE "%Completed%"', 'params' => [':lid' => $this->id], 'order' => 'id ASC']);

		return array(
			'proc' => @$wip_log->user->fname,
			'check' => @$com_log->user->fname,
		);
	}

	public function getAdhocRequest()
	{
		$task = WmsTask::model()->find('type = 6020 AND link_id = :id', [':id' => $this->id]);
		if (!empty($task)) {
			$log = Log::model()->find(['condition' => 'lid = :lid AND model = "WmsTask" AND user_id = :op_id AND type = 6', 'params' => [':lid' => $task->id, ':op_id' => $this->op_id], 'order' => 'id DESC']);
			if (!empty($log)) {
				if (mb_strlen($log->getExtra()) > 100) {
					return "<div class=\"tooltip-custom\"><span class=\"tooltiptext-custom\">" . str_replace("\n", '<br>', $log->getExtra()) . "</span>" . mb_substr(str_replace("\n", '<br>', $log->getExtra()), 0, 100) . "...</div>";
				} else {
					return str_replace("\n", '<br>', $log->getExtra());
				}
			}
		}

		return '';
	}

	public function beforeSave()
	{
		if (empty($this->dpt_id)) {
			//$this->dpt_id = 106;
		}

		if ($this->is_request == 1 && $this->type == 6010) {
			$this->mdata['adhoc_request'] = $this->getAdhocRequest();
		}

		if (empty($this->status)) {
			$this->status = 10;
		}

		if (empty($this->op_id)) {
			$this->op_id = 0;
		}

		if ($this->is_request == 1 && empty($this->mdata['dpmt'])) {
			if (!empty($this->job->customer->extra['sp_id']) && in_array($this->job->customer->extra['sp_id'], WmsTask::$op)) {
				$this->mdata['dpmt'] = '3PL';
			} else if (!empty($this->job->customer->extra['op_id']) && in_array($this->job->customer->extra['op_id'], WmsTask::$op)) {
				$this->mdata['dpmt'] = '3PL';
			} else if (!empty($this->job->customer->extra['sp_id']) && in_array($this->job->customer->extra['sp_id'], EdiJob::$op)) {
				$this->mdata['dpmt'] = '大货';
			} else if (!empty($this->job->customer->extra['op_id']) && in_array($this->job->customer->extra['op_id'], EdiJob::$op)) {
				$this->mdata['dpmt'] = '大货';
			}
		}

		if ($this->is_request == 1 && empty($this->compl_time) && $this->status == 99) {
			$this->compl_time = date('Y-m-d H:i:s');
			unset($this->mdata['rank']);
		}

		// check WIP but no pick item
		// if ($this->is_request == 1 && $this->status == 30 && in_array($this->type, [3020, 3030])) {
		// 	if (count($this->actionTask->items) == 0) {
		// 		$this->status = 20;
		// 	}
		// }

		if ($this->is_request == 1 && $this->status < 99) {
			// rank task
			$wto = WmsTaskOperator::model()->find('op_id = :op_id', [':op_id' => $this->op_id]);
			if (!empty($wto) && $this->auto_rank) {
				if (!empty($this->due_time)) {
					// find due_time before this & rank last; and first
					$prev_rank = WmsTask::model()->find(['condition' => 'op_id = :op_id AND id != :id AND due_time < :due AND status < 99', 'params' => [':op_id' => $this->op_id, ':id' => $this->id, ':due' => $this->due_time], 'order' => 'JSON_VALUE(t.meta, "$.rank") DESC']);
					$first_rank = WmsTask::model()->find(['condition' => 'op_id = :op_id AND id != :id AND status < 99', 'params' => [':op_id' => $this->op_id, ':id' => $this->id], 'order' => 'JSON_VALUE(t.meta, "$.rank") ASC']);
					if (!empty($prev_rank)) {
						// rank = prev + 1; every next move one down
						$this->mdata['rank'] = intval(@$prev_rank->mdata['rank']) + 1;
						$offset = 1;
					} else {
						// rank = first - 1
						$this->mdata['rank'] = intval(@$first_rank->mdata['rank']) - 1;
						if ($this->mdata['rank'] < 1) {
							$offset = 1 - $this->mdata['rank'];
							$this->mdata['rank'] += $offset;
						}
					}
					// adjust nexts
					if (!empty($offset) && $offset >= 1) {
						$next_ranks = WmsTask::model()->findAll('op_id = :op_id AND id != :id AND JSON_VALUE(t.meta, "$.rank") >= :rank AND status < 99', [':op_id' => $this->op_id, ':id' => $this->id, ':rank' => $this->mdata['rank']]);
						foreach ($next_ranks as $next_rank) {
							$next_rank->mdata['rank'] += $offset;
							$next_rank->auto_rank = false;
							$next_rank->update('meta');
						}
					}
				} else {
					// rank = last + 1 or 1;
					$last_rank = WmsTask::model()->find(['condition' => 'op_id = :op_id AND id != :id AND status < 99', 'params' => [':op_id' => $this->op_id, ':id' => $this->id], 'order' => 'JSON_VALUE(t.meta, "$.rank") DESC']);
					$this->mdata['rank'] = @$last_rank->rank + 1;
				}
			}
		}

		$o = WmsTask::model()->findByPk($this->id);
		if ($this->is_request == 1 && $this->status == 100 && $o->status >= 30 && !Acl::hasAccess('B:WmsTask/CancelWIP')) {
			$this->addError('id', 'Task status is ' . $o->getStatus() . ', CANNOT be cancelled');
			return false;
		}

		// create restock task
		// if ($this->is_request == 1 && $this->status == 100 && in_array($this->type, [3020, 3030])) {
		// 	if (empty($this->mdata['restock']) && !empty($this->actionTask->items)) {
		// 		foreach ($this->items as $item) {
		// 			$stock = WmsStock::model()->findByPk($item->mdata['si']);
		// 			$restock_items[] = ['pli' => '', 'pl' => '', 'gi' => $stock->prod_id, 'gn' => $item->mdata['sn'], 'cq' => '', 'uq' => $item->mdata['uq'], 'ex' => '', 'bn' => '', 'nt' => ''];
		// 		}
		// 		$restock = new WmsTask;
		// 		$restock->job_id = $this->job_id;
		// 		$restock->type = 1020;
		// 		$restock->is_request = 1;
		// 		$restock->status = 20;
		// 		$restock->ref = 'Cancel restock ' . $this->getNo();
		// 		$restock->new_items = $restock_items;
		// 		if ($restock->save()) {
		// 			$this->mdata['restock'] = $restock->id;
		// 		}
		// 	}
		// }

		// lodge sync
		if ($this->is_request == 1 && $this->status == 99 && in_array($this->type, [3020, 3030]) && empty($this->mdata['synced' . $this->status])) {
			$this->bwf |= 256;
		}
		if ($this->is_request == 1 && in_array($this->status, [10,20]) && $this->job->org_id == Org::ORGID_3PL_BIOPHYSICS && in_array($this->type, [3020, 3030]) && empty($this->mdata['synced' . $this->status])) {
			$this->bwf |= 256;
		}
		if ($this->is_request == 1 && !empty($this->mdata['api_hook']) && $this->mdata['api_hook'] == 'ufl') {
			$this->uflCallback();
		}

		/**
		 * 2020-04-14
		 * if mobile or tel or phone is empty, get org contact where func = delivery address
		 */
		if ($this->type == 2120 && empty($this->mdata['cnee']['tel'])) {
			$contacts = OrgContact::model()->findAll('org_id = :oid AND (func & 2 > 0) AND status = 1', [':oid' => $this->job->org_id]);
			foreach ($contacts as $contact) {
				if (!empty($contact->phone)) {
					$this->mdata['cnee']['tel'] = $contact->phone;
				}
				if (!empty($contact->mobile)) {
					$this->mdata['cnee']['tel'] = $contact->mobile;
				}
			}

			$org = Org::model()->findByPk($this->job->org_id);
			if (!empty($org->extra['3pl_cnee_tel']) && empty($this->mdata['cnee']['tel'])) {
				$this->mdata['cnee']['tel'] = $org->extra['3pl_cnee_tel'];
			}
		}

		/**
		 * 2020-04-23
		 * air&sea re-complete task => re check pallets in
		 */
		if ($this->type == 1010 && $this->status == 99 && $this->mdata['dpmt'] == '大货' && !empty($this->mdata['email_sent']) && empty($this->mdata['confirmed_diff']) && isset(Yii::app()->user->org)) {
			unset($this->mdata['email_sent']);
			unset($this->mdata['confirmed_diff']);
			$this->bwf = ($this->bwf ^ 512) & $this->bwf;
		}

		/**
		 * 2020-04-27
		 * long state => short state
		 * empty suburb | postcode | state
		 */
		if ($this->type == 2120 && !empty($this->mdata['cnee']['state'])) {
			if (in_array(strtolower($this->mdata['cnee']['state']), Unloco::$austates)) {
				$this->mdata['cnee']['state'] = array_search(strtolower($this->mdata['cnee']['state']), Unloco::$austates);
			}

			if ($this->mdata['cnee']['country'] == 'AU' && $this->ref == '' && $this->mainTask->status < 99) {
				$this->mainTask->mdata['address_error'] = [];

				if (empty($this->mdata['cnee']['suburb']) || empty($this->mdata['cnee']['postcode']) || empty($this->mdata['cnee']['state']) || empty($this->mdata['cnee']['tel'])) {
					$this->mainTask->mdata['address_error'][] = 'Address is incomplete';
					$this->mainTask->status = $this->mainTask->status != 10 ? self::STATUS_HOLD : 10;
					$this->mainTask->update('status', 'meta');
				} else if (!Postcode::validateAddress($this->mdata['cnee']['suburb'], $this->mdata['cnee']['state'], $this->mdata['cnee']['postcode'])) {
					$this->mainTask->mdata['address_error'][] = 'Address is incorrect';
					$this->mainTask->status = $this->mainTask->status != 10 ? self::STATUS_HOLD : 10;
					$this->mainTask->update('status', 'meta');
				} else {
					$this->mainTask->updateMeta();
				}
			}
		}

		$this->meta = empty($this->mdata) ? '' : json_encode($this->mdata);

		return true;
	}

	// public function uflCallback()
	// {
	// 	// cancel
	// 	if ($this->status == 100 && empty($this->mdata['api_cancel'])) {
	// 		$api = new UflAPI;
	// 		$r = $api->outboundUpdate($this->mdata['api_meta']['customer'], $this->ref, 'X');
	// 		if (!empty($r) && preg_match('/success/', $r['result'])) {
	// 			$this->mdata['api_cancel'] = 1;
	// 		}
	// 	}
	// }

	public function uflCallback()
	{
		// cancel
		if ($this->status == 100 && empty($this->mdata['api_cancel'])) {
			$o = WmsTask::model()->findByPk($this->id);
			if (!empty($o) && $o->status > 10) {
				// cancel after confirm
				$api = new UflAPI;
				$r = $api->outboundUpdate($this->mdata['api_meta']['customer'], $this->ref, 'X');
			} else if (!empty($o) && $o->status == 10) {
				// cancel when pending
				$api = new UflAPI;
				$r = $api->outboundUpdate($this->mdata['api_meta']['customer'], $this->ref, 'XN');
			}
			if (!empty($r) && preg_match('/success/', $r['result'])) {
				$this->mdata['api_cancel'] = 1;
			}
		}
		// pending
		if ($this->status == 10 && empty($this->mdata['api_pending'])) {
			$api = new UflAPI;
			$r = $api->outboundUpdate($this->mdata['api_meta']['customer'], $this->ref, 'N');
			if (!empty($r) && preg_match('/success/', $r['result'])) {
				$this->mdata['api_pending'] = 1;
			}
		}
		// allocate
		if ($this->status == 20 && empty($this->mdata['api_allocate'])) {
			$o = WmsTask::model()->findByPk($this->id);
			if (!empty($o) && $o->status == 10) {
				$api = new UflAPI;
				$r = $api->outboundUpdate($this->mdata['api_meta']['customer'], $this->ref, 'A');
				if (!empty($r) && preg_match('/success/', $r['result'])) {
					$this->mdata['api_allocate'] = 1;
				}
			}
		}
		// return
		if (($this->bwf & 128) > 0 && empty($this->mdata['api_return'])) {
			$api = new UflAPI;
			$r = $api->outboundUpdate($this->mdata['api_meta']['customer'], $this->ref, 'RT');
			if (!empty($r) && preg_match('/success/', $r['result'])) {
				$this->mdata['api_return'] = 1;
			}
		}
	}

	public function easyshipScanOut()
	{
		// ship hook
		if ($this->is_request == 1 && $this->status == 99 && !empty($this->mdata['api_hook']) && empty($this->mdata['api_ship'])) {
			if ($this->mdata['api_hook'] == 'ufl') {
				$api = new UflAPI;
				if (!empty($this->mdata['sourceLinkId'])) {
					$data = [
						'completedDate' => date('Y-m-d H:i:s'),
						'completedTimeZone' => date('GMT+8'),
						'sourceLinkId' => $this->mdata['sourceLinkId'],
						'orderSPT' => $this->mdata['orderSPT'],
					];
					if (!empty($this->deliveryTask->mdata['shipment_id'])) {
						$vcourier = $this->_getCourierNameAndRef();
						$data['courierBillNo'] = implode(', ', $vcourier[0]);
						$data['courierName'] = $vcourier[1];
					}
				} else {
					$data = [];
				}
				$r = $api->outboundUpdate($this->mdata['api_meta']['customer'], $this->ref, 'C', $data);
				if (!empty($r) && preg_match('/success/', $r['result'])) {
					$this->mdata['api_ship'] = 1;
					$this->updateMeta();
				}
			}
		}
	}

	public function afterSave()
	{
		if ($this->noAfterSave == true) {
			return;
		}

		$this->saveItems();
		$this->workflow();

		//leave warehouse
		if ($this->is_request == 1 && $this->status == 99 && in_array($this->type, [3010, 3020, 3030, 5010])) {
			$act = $this->actionTask;
			if (!empty($act->items)) {
				foreach ($act->items as $itm) {
					$itm->leaveWarehouse();
				}
			}
		}

		//cancel need to relase the reserve;
		if ($this->is_request == 1 && $this->status == 100 && in_array($this->type, [3020, 3030])) {
			foreach ($this->items as $itm) {
				$itm->realaseReserve();
			}
			foreach ($this->actionTask->items as $itm) {
				$itm->revertPick();
			}
		}

		// cancel container load & pick pallet
		if ($this->is_request == 1 && $this->status == 100 && in_array($this->type, [3010, 2030])) {
			foreach ($this->items as $itm) {
				$itm->delete();
			}
			foreach ($this->actionTask->items as $itm) {
				$itm->delete();
			}
		}

		// pcaw no log
		// if (empty($this->mainTask)) {
		// 	$task = $this;
		// } else {
		// 	$task = $this->mainTask;
		// }
		if (!$this->nolog && !empty($this)) {
			$tab = $this->is_request == 1 ? 'Main' : $this->getType();
			$extra = empty($this->custom_log_note) ? array() : array('note' => $this->custom_log_note);
			Log::add($this, $this->isNewRecord ? 3 : 4, array_merge(array('status' => !empty($this->mainTask) ? $this->mainTask->getStatus() : $this->getStatus(), 'tab' => $tab), $extra));
		}

		// wms task batch
		if (in_array($this->status, [self::STATUS_HOLD,99,100]) && $this->is_request == 1 && !empty($this->batch) && $this->batch->status != $this->status) {
			$this->batch->status = $this->status;
			$this->batch->update('status');
		} else if (!in_array($this->status, [self::STATUS_HOLD,99,100]) && $this->is_request == 1 && !empty($this->batch) && $this->batch->status != 10) {
			$this->batch->status = 10;
			$this->batch->update('status');
		}

		if ($this->status == self::STATUS_HOLD && $this->is_request == 1 && !empty($this->batch)) {
			$this->batch->shelf_number = 0;
		}

		// if ($this->is_request == 1 && $this->status == 99 && in_array($this->job->org_id, [Org::ORGID_AIRSEA_WGAU, Org::ORGID_AIRSEA_HOUPU, Org::ORGID_AIRSEA_MINENSSEY, Org::ORGID_AIRSEA_WTMANAGE]) && empty($this->mdata['sent_abm'])) {
		// 	$this->sendABM();
		// }
	}

	public function sendABM()
	{
		if (in_array($this->type, [1010,1020])) {
			$data = [
				'no' => $this->ref,
				'type' => 20,
				'warehouse' => !empty($this->mdata['warehouse']) ? $this->mdata['warehouse'] : 'WSYD1',
				'items' => [],
			];
			foreach ($this->actionTask->items as $item) {
				$prod = WmsProd::model()->findByPk($item->mdata['gi']);
				$sku = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $prod->id, ':oid' => $this->job->org_id]);
				if (empty($data['items'][$prod->ean . '_' . $item->mdata['ex']])) {
					if (empty($item->mdata['ex'])) {
						$expiry = '';
						$proddate = '';
					} else if (empty($prod->mdata['expiry_span'])) {
						$expiry = $item->mdata['ex'];
						$proddate = '';
					} else {
						$expiry = $item->mdata['ex'];
						$proddate = date('Y-m-d', strtotime($item->mdata['ex'] . ' - ' . intval($prod->mdata['expiry_span']) . ' year'));
					}
					$data['items'][$prod->ean . '_' . $item->mdata['ex']] = [
						'ean' => $prod->ean,
						'sku' => !empty($sku) ? $sku->sku : '',
						'qty' => 0,
						'productDate' => $proddate,
						'expireDate' => $expiry,
					];
				}
				$data['items'][$prod->ean . '_' . $item->mdata['ex']]['qty'] += intval($item->mdata['uq']);
			}
		} else if (in_array($this->type, [3020,3030])) {
			$data = [
				'no' => $this->ref,
				'type' => 10,
				'warehouse' => 'WSYD1',
				'items' => [],
			];
			foreach ($this->actionTask->items as $item) {
				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				$sku = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $stock->prod->id, ':oid' => $this->job->org_id]);
				if (empty($data['items'][$stock->prod->ean . '_' . $stock->expiry])) {
					if (empty($stock->expiry)) {
						$expiry = '';
						$proddate = '';
					} else if (empty($stock->prod->mdata['expiry_span'])) {
						$expiry = $stock->expiry;
						$proddate = '';
					} else {
						$expiry = $stock->expiry;
						$proddate = date('Y-m-d', strtotime($stock->expiry . ' - ' . intval($stock->prod->mdata['expiry_span']) . ' year'));
					}
					$data['items'][$stock->prod->ean . '_' . $stock->expiry] = [
						'ean' => $stock->prod->ean,
						'sku' => !empty($sku) ? $sku->sku : '',
						'qty' => 0,
						'productDate' => $proddate,
						'expireDate' => $expiry,
					];
				}
				$data['items'][$stock->prod->ean . '_' . $stock->expiry]['qty'] += intval($item->mdata['uq']);
			}
		}

		$data['items'] = array_values($data['items']);

		$abm = new ABMAPI(true);
		$res = $abm->httpPost($data);

		if (!empty($res['flag']) && $res['flag'] == 'success') {
			$this->mdata['sent_abm'] = true;
			$this->update('meta');
		}
	}

	public function _getCourierNameAndRef()
	{
		$refs = [];
		if (!empty($this->deliveryTask->mdata['shipment_id'])) {
			foreach ($this->deliveryTask->mdata['shipment_id'] as $id) {
				$shipment = Shipment::model()->findByPk($id);
				$refs[] = $shipment->ref;
			}
		}
		if ($this->deliveryTask->mdata['courier'] == Org::ORGID_COURIER_AUPOST) {
			$courier = 'AUPOST';
		} else if ($this->deliveryTask->mdata['courier'] == Org::ORGID_COURIER_FASTWAY) {
			$courier = 'FASTWAY';
		} else if ($this->deliveryTask->mdata['courier'] == Org::ORGID_COURIER_STARTRACK) {
			$courier = 'STARTRACK';
		} else if ($this->deliveryTask->mdata['courier'] == Org::ORGID_COURIER_TNT) {
			$courier = 'TNT';
		} else if ($this->deliveryTask->mdata['courier'] == Org::ORGID_COURIER_SENDLE) {
			$courier = 'SENDLE';
		} else {
			$courier = '';
		}

		return [$refs, $courier];
	}

	public function countSKU()
	{
		$count = 0;
		foreach ($this->items as $item) {
			if (!empty($item->mdata['cq']) || !empty($item->mdata['uq'])) $count += 1;
		}
		return $count;
	}

	public function countUq()
	{
		$count = 0;
		foreach ($this->items as $item) {
			if (!empty($item->mdata['uq'])) $count += intval($item->mdata['uq']);
		}
		return $count;
	}

	public function countActionUq()
	{
		$count = 0;
		foreach ($this->actionTask->items as $item) {
			if (!empty($item->mdata['uq'])) $count += intval($item->mdata['uq']);
		}
		return $count;
	}

	public function getDpmt()
	{
		if (!empty($this->mdata['dpmt'])) {
			return $this->mdata['dpmt'];
		} else {
			return 'Other';
		}
	}

	public function ifPltDiff()
	{
		if ($this->type != 1010 || $this->status != 99) {
			return '';
		} else if (($this->bwf & 512) > 0) {
			if (!empty($this->mdata['confirmed_diff'])) {
				return 'Diff Confirmed';
			} else {
				return 'Diff to Confirm';
			}
		} else if (!empty($this->mdata['email_sent'])) {
			return 'No Diff';
		} else {
			return 'Waiting Check';
		}
	}

	public function getCneeName()
	{
		return @$this->deliveryTask->mdata['cnee']['name'];
	}

	public function getCneeCompany()
	{
		return @$this->deliveryTask->mdata['cnee']['company'];
	}

	public function getCneeCity()
	{
		return @$this->deliveryTask->mdata['cnee']['city'];
	}

	public function getCneeEmail()
	{
		return @$this->deliveryTask->mdata['cnee']['email'];
	}

	public function getCneeTrackingno()
	{
		return @$this->deliveryTask->mdata['trackingno'];
	}

	public function getCneeFullAddress()
	{
		$address = [];
		if (!empty($this->deliveryTask->mdata['cnee']['address'])) {
			$address[] = $this->deliveryTask->mdata['cnee']['address'];
		}
		if (!empty($this->deliveryTask->mdata['cnee']['suburb'])) {
			$address[] = ucfirst($this->deliveryTask->mdata['cnee']['suburb']);
		}
		if (!empty($this->deliveryTask->mdata['cnee']['state'])) {
			$address[] = strtoupper($this->deliveryTask->mdata['cnee']['state']);
		}
		if (!empty($this->deliveryTask->mdata['cnee']['postcode'])) {
			$address[] = $this->deliveryTask->mdata['cnee']['postcode'];
		}
		return implode(', ', $address);
	}

	public function getCreateDate()
	{
		if (!empty($this->createlog->time)) {
			return date('Y-m-d', strtotime($this->createlog->time));
		} else {
			return '';
		}
	}

	public function getComplDate()
	{
		if (!empty($this->compl_time)) {
			return date('Y-m-d', strtotime($this->compl_time));
		} else {
			return '';
		}
	}

	public function getCneeAddress()
	{
		return @$this->deliveryTask->mdata['cnee']['address'];
	}

	public function getCneeSuburb()
	{
		return ucfirst(@$this->deliveryTask->mdata['cnee']['suburb']);
	}

	public function getCneeState()
	{
		return strtoupper(@$this->deliveryTask->mdata['cnee']['state']);
	}

	public function getCneePostcode()
	{
		return @$this->deliveryTask->mdata['cnee']['postcode'];
	}

	public function getCneeCountry()
	{
		$country = @$this->deliveryTask->mdata['cnee']['country'];
		return Yii::t(strtolower(__CLASS__), empty(Unloco::$countries[$country]) ? '' : Unloco::$countries[$country]);
	}

	public function getConsignment($text_only = false)
	{
		$html = [];
		if (!empty($this->deliveryTask->mdata['shipment_id'])) {
			foreach ($this->deliveryTask->mdata['shipment_id'] as $sid) {
				$shipment = Shipment::model()->find('id = :id', array(':id' => $sid));
				if (empty($shipment)) continue;
				if ($text_only) {
					$html[] = $shipment->ref;
					continue;

					if ($this->deliveryTask->mdata['shipment_courier_id'] == 101) {
						$html[] = '<a style="text-decoration: none;" target="_blank" href="https://auspost.com.au/mypost/track/#/details/' . $shipment->ref . '">' . $shipment->ref . '</a>';
					} else if ($this->deliveryTask->mdata['shipment_courier_id'] == 115) {
						$html[] = '<a style="text-decoration: none;" target="_blank" href="https://www.fastway.com.au/tools/track/?l=' . $shipment->ref . '">' . $shipment->ref . '</a>';
					} else if ($this->deliveryTask->mdata['shipment_courier_id'] == 114 && $shipment->agent_id == Org::ORGID_3PL_IGEA) {
						$html[] = '<a style="text-decoration: none;" target="_blank" href="https://track.sendle.com/tracking?ref=' . $shipment->ref . '">' . $shipment->ref . '</a>';
					} else if ($this->deliveryTask->mdata['shipment_courier_id'] == 976) {
						$html[] = '<a style="text-decoration: none;" target="_blank" href="https://www.tnt.com/express/en_au/site/home.html">' . $shipment->ref . '</a>';
					} else if ($this->deliveryTask->mdata['shipment_courier_id'] == 858) {
						$html[] = '<a style="text-decoration: none;" target="_blank" href="https://msto.startrack.com.au/track-trace/?id=' . $shipment->ref . '">' . $shipment->ref . '</a>';
					} else if ($this->deliveryTask->mdata['shipment_courier_id'] == Org::ORGID_COURIER_SENDLE) {
						$html[] = '<a style="text-decoration: none;" target="_blank" href="https://track.sendle.com/tracking?ref=' . $shipment->ref . '">' . $shipment->ref . '</a>';
					}
				}
			}

			return implode('&nbsp;', $html);
		}
	}

	public function getConsignmentUrl()
	{
		$html = [];
		if (!empty($this->deliveryTask->mdata['shipment_id'])) {
			foreach ($this->deliveryTask->mdata['shipment_id'] as $sid) {
				$shipment = Shipment::model()->find('id = :id', array(':id' => $sid));
				if ($this->deliveryTask->mdata['shipment_courier_id'] == 101) {
					$html[] = 'https://auspost.com.au/mypost/track/#/details/' . $shipment->ref;
				} else if ($this->deliveryTask->mdata['shipment_courier_id'] == 115) {
					$html[] = 'https://www.fastway.com.au/tools/track/?l=' . $shipment->ref;
				} else if ($this->deliveryTask->mdata['shipment_courier_id'] == 114 && $shipment->agent_id == Org::ORGID_3PL_IGEA) {
					$html[] = 'https://track.sendle.com/tracking?ref=' . $shipment->ref;
				} else if ($this->deliveryTask->mdata['shipment_courier_id'] == 976) {
					$html[] = 'https://www.tnt.com/express/en_au/site/home.html';
				} else if ($this->deliveryTask->mdata['shipment_courier_id'] == 858) {
					$html[] = 'https://msto.startrack.com.au/track-trace/?id=' . $shipment->ref;
				} else if ($this->deliveryTask->mdata['shipment_courier_id'] == Org::ORGID_COURIER_SENDLE) {
					$html[] = 'https://track.sendle.com/tracking?ref=' . $shipment->ref;
				}
			}
		}

		return implode('&nbsp;', $html);
	}

	public function getCarrier()
	{
		if (!empty($this->deliveryTask->mdata['shipment_id'])) {
			foreach ($this->deliveryTask->mdata['shipment_id'] as $sid) {
				$shipment = Shipment::model()->find('id = :id', array(':id' => $sid));
				if (empty($this->deliveryTask->mdata['shipment_courier_id'])) {
					return '';
				} else if ($this->deliveryTask->mdata['shipment_courier_id'] == Org::ORGID_COURIER_AUPOST) {
					return 'AUPOST';
				} else if ($this->deliveryTask->mdata['shipment_courier_id'] == Org::ORGID_COURIER_FASTWAY) {
					return 'FASTWAY';
				} else if ($this->deliveryTask->mdata['shipment_courier_id'] == 114 && $shipment->agent_id == Org::ORGID_3PL_IGEA) {
					return '';
				} else if ($this->deliveryTask->mdata['shipment_courier_id'] == Org::ORGID_COURIER_TNT) {
					return 'TNT';
				} else if ($this->deliveryTask->mdata['shipment_courier_id'] == Org::ORGID_COURIER_STARTRACK) {
					return 'STARTRACK';
				} else if ($this->deliveryTask->mdata['shipment_courier_id'] == Org::ORGID_COURIER_SENDLE) {
					return 'SENDLE';
				}
			}
		}
	}

	public function getBatchSortingRemainByTask()
	{
		$remain = 0;
		foreach ($this->items as $item) {
			if (empty($item->stockLedgers)) continue;
			$remain += intval($item->mdata['uq']) - intval($item->mdata['sort_qty']);
		}

		return $remain;
	}

	public static function getQuickCourierLabelTasks()
	{
		$types = [
			'LIP004_2' => 'LIP004',
			'LIP001_1' => 'LIP001',
			'LIP004_1' => 'LIP004',
			'LIP005_3' => 'LIP005',
			'LIP005_2' => 'LIP005',
		];
		$orgs = [
			Org::ORGID_3PL_IGEA,
		];
		$sum = [];

		$tasks = WmsTask::model()->with('job')->findAll('t.is_request = 1 AND t.status = 20 AND t.type IN (3020,3030) AND job.org_id IN (' . implode(',', $orgs) . ')');
		foreach ($tasks as $task) {
			if (!empty($task->deliveryTask->mdata['cnee']['country']) && $task->deliveryTask->mdata['cnee']['country'] != 'AU') continue;
			$items = [];
			foreach ($task->items as $item) {
				if (empty($item->mdata['si'])) continue;
				if (empty($item->mdata['uq'])) continue;
				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				if (in_array($stock->prod->getSku($stock->org_id), array_values($types))) {
					$items[$stock->prod->getSku($stock->org_id)] = intval(intval(@$items[$stock->prod->getSku($stock->org_id)]) + $item->mdata['uq']);
				} else {
					continue 2;
				}
			}

			if (sizeof($items) != 1) continue;

			$temp = [];
			foreach ($items as $k => $v) {
				$temp[$k . '_' . $v] = $k;
			}

			if (array_values(array_intersect(array_keys($types), array_keys($temp))) != array_keys($temp)) continue;

			$sum[$task->job->org_id][str_replace('_', ' X ', array_keys($temp)[0])][] = $task;
		}

		return $sum;
	}

	public static function getQuickCourierLabelTasksRemain()
	{
		$types = [
			'LIP004_2' => 'LIP004',
			'LIP001_1' => 'LIP001',
			'LIP004_1' => 'LIP004',
			'LIP005_3' => 'LIP005',
			'LIP005_2' => 'LIP005',
		];
		$orgs = [
			Org::ORGID_3PL_IGEA,
		];
		$count = 0;

		$tasks = WmsTask::model()->with('job')->findAll('t.is_request = 1 AND t.status = 20 AND t.type IN (3020,3030) AND job.org_id IN (' . implode(',', $orgs) . ')');
		foreach ($tasks as $task) {
			if (!empty($task->deliveryTask->mdata['cnee']['country']) && $task->deliveryTask->mdata['cnee']['country'] != 'AU') continue;
			$items = [];
			foreach ($task->items as $item) {
				if (empty($item->mdata['si'])) continue;
				if (empty($item->mdata['uq'])) continue;
				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				if (in_array($stock->prod->getSku($stock->org_id), array_values($types))) {
					$items[$stock->prod->getSku($stock->org_id)] = intval(intval(@$items[$stock->prod->getSku($stock->org_id)]) + $item->mdata['uq']);
				} else {
					continue 2;
				}
			}

			if (sizeof($items) != 1) continue;

			$temp = [];
			foreach ($items as $k => $v) {
				$temp[$k . '_' . $v] = $k;
			}

			if (array_values(array_intersect(array_keys($types), array_keys($temp))) != array_keys($temp)) continue;

			if (empty($task->deliveryTask) || ($task->deliveryTask->bwf&64) == 0) continue;

			$count++;
		}

		return $count;
	}

	public function getEdiJob($del = true)
	{
		foreach ($this->edijobs as $k => $job) {
			echo '<a class="tab_link" href="/EdiJob/update/' . $job->id . '" title="' . $job->no . '">' . $job->no . '</a><a class="ajax_link" href="' . Yii::app()->createUrl('wmsTask/deleteJob', ['task_id' => $this->id, 'job_id' => $job->id]) . '">' . ($del ? '<div style="background-position: -272px -128px" class="icon"></div></a>' : '');
			if ($k != sizeof($this->edijobs) - 1) {
				echo '&nbsp;&nbsp;';
			}
		}
	}

	public function getAWBN()
	{
		return !empty($this->mdata['awbn']) ? $this->mdata['awbn'] : '';
	}

	public function getKitItems()
	{
		$kitItems = [];
		$items = [];

		foreach ($this->items as $item) {
			$stock = WmsStock::model()->findByPk($item->mdata['si']);
			if (empty($items[$stock->prod_id])) {
				$items[$stock->prod_id] = array('prod' => $stock->prod, 'qty' => 0);
			}
			$items[$stock->prod_id]['qty'] += $item->mdata['uq'];
		}

		$kits = WmsProd::model()->with('stocks')->findAll('stocks.org_id = :org_id AND t.type = :type', [':org_id' => $this->job->org_id, ':type' => WmsProd::WMS_PROD_KIT]);
		foreach ($kits as $kit) {
			$qty = PHP_INT_MAX;

			foreach ($kit->items as $item) {
				if (empty($items[$item->item_id])) continue 2;
				$qty = ceil($items[$item->item_id]['qty'] / $item->qty) < $qty ? ceil($items[$item->item_id]['qty'] / $item->qty) : $qty;
			}

			if ($qty == 0 || $qty == PHP_INT_MAX) continue;

			$kitItems[$kit->id] = ['kit' => $kit, 'qty' => $qty];
			foreach ($kit->items as $item) {
				$items[$item->item_id]['qty'] -= $qty * $item->qty;
			}
		}

		foreach ($items as $pid => $item) {
			if ($item['qty'] == 0) unset($items[$pid]);
		}

		$rs = [];
		foreach ($kitItems as $item) {
			$r = new WmsTaskItem;
			$r->mdata = [
				'si' => $item['kit']->stocks[0]->id,
				'sn' => $item['kit']->name,
				'uq' => $item['qty'],
				'pl' => '',
				'cq' => '',
				'nt' => '',
				'pq' => '',
			];
			$rs[] = $r;
		}
		foreach ($items as $item) {
			$r = new WmsTaskItem;
			$r->mdata = [
				'si' => $item['prod']->stocks[0]->id,
				'sn' => $item['prod']->name,
				'uq' => $item['qty'],
				'pl' => '',
				'cq' => '',
				'nt' => '',
				'pq' => '',
			];
			$rs[] = $r;
		}

		return $rs;
	}

	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata = json_decode($this->meta, true);
		}

		if (!empty($this->map)) {
			if ($this->map->platform == 'shopify') {
				$this->source = self::WMS_TASK_SOURCE_SHOPIFY;
			} else if ($this->map->platform == 'cin7') {
				$this->source = self::WMS_TASK_SOURCE_CIN7;
			} else {
				$this->source = self::WMS_TASK_SOURCE_MANUAL;
			}
		} else {
			$this->source = self::WMS_TASK_SOURCE_MANUAL;
		}

		if ($this->is_request == 1 && $this->type == 6010) {
			$this->mdata['adhoc_request'] = $this->getAdhocRequest();
		}

		if (!empty($this->mdata['address_error'])) {
			$this->mdata['note'] = (!empty($this->mdata['note']) ? $this->mdata['note'] . "\n" : '') . implode("\n", $this->mdata['address_error']);
		}

		// if ($this->is_request == 1 && empty($this->mdata['dpmt'])) {
		// 	$this->noAfterSave = true;
		// 	if (!empty($this->job->customer->extra['sp_id']) && in_array($this->job->customer->extra['sp_id'], WmsTask::$op)) {
		// 		$this->mdata['dpmt'] = '3PL';
		// 		$this->updateMeta();
		// 	} else if (!empty($this->job->customer->extra['op_id']) && in_array($this->job->customer->extra['op_id'], WmsTask::$op)) {
		// 		$this->mdata['dpmt'] = '3PL';
		// 		$this->updateMeta();
		// 	} else if (!empty($this->job->customer->extra['sp_id']) && in_array($this->job->customer->extra['sp_id'], EdiJob::$op)) {
		// 		$this->mdata['dpmt'] = '大货';
		// 		$this->updateMeta();
		// 	} else if (!empty($this->job->customer->extra['op_id']) && in_array($this->job->customer->extra['op_id'], EdiJob::$op)) {
		// 		$this->mdata['dpmt'] = '大货';
		// 		$this->updateMeta();
		// 	}
		// }

		return true;
	}

	public static function checkBackOrder()
	{
		$tasks = self::model()->with('job')->findAll(['condition' => 'job.org_id IN (' . implode(',', self::$checkBackOrdersManual) . ') AND t.is_request = 1 AND t.status = 10', 'order' => 't.id ASC']);
		foreach ($tasks as $task) {
			$back = false;
			foreach ($task->items as $item) {
				if ($item->mdata['uq'] == 0) continue;
				if (!empty($item->mdata['si'])) {
					$stock = WmsStock::model()->findByPk($item->mdata['si']);
				} else if (!empty($item->mdata['pi'])) {
					$stock = WmsStock::model()->find('prod_id = :prod_id AND org_id = :org_id AND qty > 0', [':prod_id' => $item->mdata['pi'], ':org_id' => $task->job->org_id]);
					if (!empty($stock)) {
						$item->mdata['si'] = $stock->id;
						$item->update('meta');
					}
				} else {
					$stock = WmsStock::model()->with('prod')->find('prod.name = :name AND t.org_id = :org_id AND t.qty > 0', [':name' => $item->mdata['sn'], ':org_id' => $task->job->org_id]);
					if (!empty($stock)) {
						$item->mdata['si'] = $stock->id;
						$item->update('meta');
					}
				}
				if (empty($stock) || $stock->availQty() < $item->mdata['uq']) {
					$back = true;
				}
			}

			if (!$back) {
				$task->status = 20;
				$task->save();
				$task->refresh();
				foreach ($task->items as $item) {
					$item->toStock();
				}
				$task->deliveryTask->save();
			}
		}
	}

	public function getCourierCompany()
	{
		if (!empty($this->deliveryTask->mdata['courier'])) {
			switch ($this->deliveryTask->mdata['courier']) {
				case Org::ORGID_COURIER_AUPOST:
					return 'Australia Post';
				case Org::ORGID_COURIER_FASTWAY:
					return 'Aramex Australia';
				case Org::ORGID_COURIER_STARTRACK:
					return 'Star Track';
				case Org::ORGID_COURIER_TNT:
					return 'TNT';
				case Org::ORGID_COURIER_SENDLE:
					return 'Sendle';
			}
		}

		return 'PCA Express';
	}

	public function isNeedChargePackFee(){

		if($this->type == 3210){
			$countOfPickQty = 0;
			$countOfPack = 0;
			
			foreach($this->mainTask->items as $item){
				$countOfPickQty +=  intval($item->mdata['uq']);
			}
			if(empty(json_decode($this->mdata['pkg']))){
				$countOfPack = 0;
			}else{
				$countOfPack = count(json_decode($this->mdata['pkg'], true));
			}
			
			if($countOfPickQty==$countOfPack){
				return false;
			}else{
				return true;
			}
		}
	}

	public function getCourierShortName(){
		$result = "NA";
		if($this->type==2120){
			switch ($this->mdata['courier']){
				case Org::ORGID_COURIER_AUPOST:
					return "AP";
					break;
				case Org::ORGID_COURIER_TNT:
					return "TT";
					break;
				case Org::ORGID_COURIER_SF:
					return "SF";
					break;
				case Org::ORGID_COURIER_EIZ_TOLL:
					return "TO";
					break;
				case Org::ORGID_COURIER_EIZ_ALLIED:
					return "AL";
					break;
				case Org::ORGID_COURIER_UBI_TOLL:
					return "TO";
					break;
				case Org::ORGID_COURIER_TLA:
					return "KP";
					break;
				case Org::ORGID_COURIER_FASTWAY:
					return "FW";
					break;
				case Org::ORGID_COURIER_UBI_AP:
					return "AP";
					break;
				case Org::ORGID_COURIER_BORDER:
					return "BD";
					break;
			}
		}
		return $result;
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
	public function search($pgn = true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$this->id = ltrim($this->id, 'Tt0');

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.job_id', $this->job_id);
		$criteria->compare('t.op_id', $this->op_id);
		if (empty($this->type)) {
			$criteria->addCondition('t.type != 5010');
		} else {
			$criteria->compare('t.type', $this->type);
		}
		if ($this->status == 1) {
			$status = [10];
		} else if ($this->status == 2) {
			$status = [20, 30];
		} else if ($this->status == 3) {
			$status = [99];
		} else if ($this->status == 5) {
			$status = [10];
			$criteria->addCondition('t.meta LIKE "%errs%"');
		} else if ($this->status == 10) {
			$status = [10];
			$criteria->addCondition('t.meta NOT LIKE "%errs%"');
		} else if ($this->status == 90) {
			$status = [99];
			$criteria->addCondition('t.meta LIKE "%api_hook%" AND t.meta NOT LIKE "%api_ship%" AND t.type IN (3020,3030)');
		} else if ($this->status == 99) {
			$status = [99];
			$criteria->addCondition('t.meta NOT LIKE "%api_hook%" OR t.meta LIKE "%api_ship%" OR t.type NOT IN (3020,3030)');
		} else if ($this->status == 50) {
			$status = [10, 31, 32];
		} else {
			$status = $this->status;
		}

		if (!empty($status)) {
			$criteria->compare('t.status', $status);
		} else if (empty($this->id) && empty($this->ref) && empty($this->cust_name)) {
			$criteria->addCondition('t.status != 100');
		}
		$criteria->compare('t.billed', $this->billed);
		// $criteria->compare('t.ref', $this->ref, true);
		$criteria->compare('t.is_request', 1);

		$criteria->compare('t.due_time', $this->due_time, true);
		$criteria->compare('t.schd_time', $this->schd_time, true);
		$criteria->compare('t.start_time', $this->start_time, true);
		$criteria->compare('t.compl_time', $this->compl_time, true);
		$criteria->compare('t.bwf', $this->bwf);
		$criteria->compare('t.meta', $this->meta, true);
		$criteria->compare('t.dpt_id', $this->dpt_id);

		$with = array();
		if (!empty($this->job_no)) {
			$with[] = 'job';
			$criteria->compare('job.no', $this->job_no, true);
		}

		if (!empty($this->cust_name)) {
			$with[] = 'job.customer';
			$with[] = 'job.customer.owner';
			$criteria->addCondition('customer.name like "%' . $this->cust_name . '%" OR customer.id = "' . $this->cust_name . '" OR owner.name like "%' . $this->cust_name . '%" OR owner.id = "' . $this->cust_name . '"');
		}

		if (!empty($this->job_ids)) {
			$criteria->addInCondition('t.job_id', $this->job_ids);
		}

		if (!empty($this->op_name)) {
			$with[] = 'op';
			$criteria->addCondition('op.fname like "%' . $this->op_name . '%" OR op.lname like "%' . $this->op_name . '%"');
		}

		if (!empty($this->source)) {
			$with[] = 'map';
			if ($this->source == self::WMS_TASK_SOURCE_SHOPIFY) {
				$criteria->addCondition('map.platform = "shopify"');
			} else {
				$criteria->addCondition('map.id IS NULL');
			}
		}

		if (!empty($this->refs)) {
			$ns = preg_split('/[\s,;]+/', trim($this->refs));
			if (sizeof($ns) > 200) {
				$ns = array_slice($ns, 0, 200);
			}
			foreach ($ns as $k => $n) {
				if (preg_match('/T\d{6}/', $n)) {
					$ns[$k] = ltrim($n, 'T');
				} else if ($n == '') {
					unset($ns[$k]);
				}
			}
			$criteria->addCondition('t.ref REGEXP ("' . implode('|', $ns) . '") OR t.id REGEXP ("' . implode('|', $ns) . '")');
		}

		if(!empty($this->prod_name) || !empty($this->prod_sku)){
			$prod_cond = [];
			if(!empty($this->prod_name)){
				$criteria->params[':prod_name'] = '%'.$this->prod_name.'%';
				$prod_cond[] = 'wp.name LIKE :prod_name';
			}
			if(!empty($this->prod_sku)){
				$criteria->params[':prod_sku'] = '%'.$this->prod_sku.'%';
				$prod_cond[] = 'wp.ean LIKE :prod_sku';
				$prod_cond[] = 'wpo.sku LIKE :prod_sku';
			}
			$criteria->addCondition('t.id IN (SELECT DISTINCT(task_id) FROM wms_task_item WHERE JSON_VALUE(meta, "$.si") IN (SELECT ws.id FROM wms_stock ws INNER JOIN wms_prod wp ON ws.prod_id = wp.id LEFT JOIN wms_prod_org wpo ON wpo.prod_id = wp.id WHERE '.implode(' OR ', $prod_cond).'))');
		}

		if (!empty($this->delivery_connote)) {
			$with[] = 'deliveryTask';
			$criteria->addCondition('deliveryTask.ref LIKE :delivery_connote');
			$criteria->params[':delivery_connote'] = '%'.$this->delivery_connote.'%';
		}

		foreach(['delivery_name', 'delivery_tel', 'delivery_postcode'] as $k){
			if (!empty($this->{$k})) {
				$with[] = 'deliveryTask';
				$criteria->addCondition('JSON_VALUE(deliveryTask.meta, "$.cnee.'.substr($k, 9).'") LIKE :'.$k);
				$criteria->params[':'.$k] = '%'.$this->{$k}.'%';
			}
		}

		if (preg_match('/int/i', $this->ref)) {
			$with[] = 'deliveryTask';
			$criteria->addCondition('json_value(deliveryTask.meta, "$.cnee.country") != "AU"');
		} else if (preg_match('/express/i', $this->ref)) {
			$criteria->addCondition('json_value(t.meta, "$.note") regexp "express"');
		} else {
			$criteria->compare('t.ref', $this->ref, true);
		}

		if (!empty($this->dpmt)) {
			if (in_array($this->dpmt, ['3PL'])) {
				$criteria->addCondition('json_value(t.meta, "$.dpmt") = "3PL"');
			} else if ($this->dpmt == 'Other') {
				$criteria->addCondition('json_value(t.meta, "$.dpmt") != "3PL" OR json_value(t.meta, "$.dpmt") IS NULL');
			}
		}

		if (!empty($this->plt_diff)) {
			if ($this->plt_diff == 'cfd') {
				$criteria->addCondition('t.bwf & 512 > 0 AND t.type = 1010 AND JSON_VALUE(t.meta, "$.confirmed_diff")');
			} else if ($this->plt_diff == 'not') {
				$criteria->addCondition('t.bwf & 512 > 0 AND t.type = 1010 AND JSON_VALUE(t.meta, "$.confirmed_diff") IS NULL');
			} else if ($this->plt_diff == 'no') {
				$criteria->addCondition('t.bwf & 512 = 0 AND t.type = 1010 AND JSON_VALUE(t.meta, "$.email_sent")');
			} else if ($this->plt_diff == 'wr') {
				$criteria->addCondition('t.bwf & 512 = 0 AND t.type = 1010 AND JSON_VALUE(t.meta, "$.email_sent") IS NULL');
			}
		}

		if (!empty($this->cnee_name)) {
			$with[] = 'deliveryTask';
			$criteria->compare('JSON_VALUE(deliveryTask.meta, "$.cnee.name")', $this->cnee_name, true);
		}

		if (!empty($this->cnee_company)) {
			$with[] = 'deliveryTask';
			$criteria->compare('JSON_VALUE(deliveryTask.meta, "$.cnee.company")', $this->cnee_company, true);
		}

		if (!empty($this->cnee_city)) {
			$with[] = 'deliveryTask';
			$criteria->compare('JSON_VALUE(deliveryTask.meta, "$.cnee.city")', $this->cnee_city, true);
		}

		if (!empty($this->cnee_email)) {
			$with[] = 'deliveryTask';
			$criteria->compare('JSON_VALUE(deliveryTask.meta, "$.cnee.email")', $this->cnee_email, true);
		}

		if (!empty($this->cnee_full_address)) {
			$with[] = 'deliveryTask';
			$criteria->compare('CONCAT(JSON_VALUE(deliveryTask.meta, "$.cnee.address"),JSON_VALUE(deliveryTask.meta, "$.cnee.suburb"),JSON_VALUE(deliveryTask.meta, "$.cnee.state"),JSON_VALUE(deliveryTask.meta, "$.cnee.postcode"))', $this->cnee_full_address, true);
		}

		if (!empty($this->cnee_address)) {
			$with[] = 'deliveryTask';
			$criteria->compare('JSON_VALUE(deliveryTask.meta, "$.cnee.address")', $this->cnee_address, true);
		}

		if (!empty($this->cnee_suburb)) {
			$with[] = 'deliveryTask';
			$criteria->compare('JSON_VALUE(deliveryTask.meta, "$.cnee.suburb")', $this->cnee_suburb, true);
		}

		if (!empty($this->cnee_state)) {
			$with[] = 'deliveryTask';
			$criteria->compare('JSON_VALUE(deliveryTask.meta, "$.cnee.state")', $this->cnee_state, true);
		}

		if (!empty($this->cnee_postcode)) {
			$with[] = 'deliveryTask';
			$criteria->compare('JSON_VALUE(deliveryTask.meta, "$.cnee.postcode")', $this->cnee_postcode, true);
		}

		if (!empty($this->cnee_country)) {
			$with[] = 'deliveryTask';
			$criteria->compare('JSON_VALUE(deliveryTask.meta, "$.cnee.country")', $this->cnee_country, true);
		}

		if (!empty($this->create_date)) {
			$with[] = 'createlog';
			$criteria->compare('createlog.time', $this->create_date, true);
		}

		if (!empty($this->adhoc_request)) {
			$criteria->compare('JSON_VALUE(meta, "$.adhoc_request")', $this->adhoc_request, true);
		}

		if (!empty($this->type_ex)) {
			if ($this->type_ex == 10) {
				$criteria->addInCondition('t.type', [1010, 1020, 1030]);
			} else if ($this->type_ex == 20) {
				$criteria->addInCondition('t.type', [2030, 2040, 3010, 3020, 3030, 3040]);
			}
		}

		if (!empty($this->return_type)) {
			if ($this->return_type == 1) {
				$criteria->addCondition('t.type != 7010');
			} else if ($this->return_type == 2) {
				$criteria->addCondition('t.type = 7010');
			}
		}

		if (!empty($this->return_status)) {
			$criteria->addCondition('t.status = ' . $this->return_status . ' OR JSON_VALUE(t.meta, "$.return_status") = ' . $this->return_status);
		}

		if (!empty($this->redelivery)) {
			$criteria->compare('JSON_VALUE(t.meta, "$.redelivery_task")', $this->redelivery);
		}

		if (!empty($this->edi_job)) {
			$with[] = 'edijobs';
			$criteria->compare('edijobs.no', $this->edi_job, true);
		}

		if (!empty($this->awbn)) {
			$criteria->compare('JSON_VALUE(t.meta, "$.awbn")', $this->awbn, true);
		}

		if(!empty($this->isSinOrMul)&&$this->isSinOrMul!=0){
			$with[] = 'items';
			if($this->isSinOrMul==1){
				$criteria->addCondition('JSON_VALUE(items.meta, "$.si")!="" and JSON_VALUE(items.meta, "$.si") is not null and JSON_VALUE(items.meta, "$.uq") >= 1');
				$criteria->group = 'items.task_id';
				$criteria->having = 'sum(JSON_VALUE(items.meta, "$.uq")) = 1';
			}else{
				$criteria->addCondition('JSON_VALUE(items.meta, "$.si")!="" and JSON_VALUE(items.meta, "$.si") is not null and JSON_VALUE(items.meta, "$.uq") >= 1');
				$criteria->group = 'items.task_id';
				$criteria->having = 'sum(JSON_VALUE(items.meta, "$.uq")) > 1';
			}
		}

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if ($ec) {
			$criteria->mergeWith($ec);
		}

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

	public function getTaskWeight($id){
		$mainTask = self::model()->findByPk($id);
		
	}

	public static function updateUploadSingleFile($filesize,$date,$fileHash,$finfo,$mime,$filename,$name, $thisHash,$move=false,$isCargoProcess=false,$cargoProcessId = false,$isFullPod = false,$active = FileRepo::PENDING)
	{
		$fr = new FileRepo;
		$fr->name = $name;
		$fr->size = $filesize;
		$fr->date = $date;
		$fr->hash = $fileHash;
		$sup = Yii::app()->session['uploads'][$thisHash];
		$fr->type = $sup[0];
		$fr->mime = empty($mime)? 'application/octet-stream' : $mime;
		$fr->status = 20;
		$fr->user_id = User::currentUserID();
		$fr->org_id = User::currentUserOrgId();
		$d = Yii::app()->params['fileRepoPath'].DIRECTORY_SEPARATOR.substr($fr->hash,0,2);
		if(!is_dir($d)) mkdir($d);
		if($move)
		{
			copy($filename, $d.DIRECTORY_SEPARATOR.$fr->hash);
  			unlink($filename);
		}else
		{
			move_uploaded_file($filename, $d.DIRECTORY_SEPARATOR.$fr->hash);
		}
		if(empty($sup[1])){
			$fr->fid = 0;
			$fr->save();
			if(!isset($sup[2])) $sup[2] = array();
			$sup[2][] = $fr->id;
			$su = Yii::app()->session['uploads'];
			$su[$thisHash] = $sup;
			Yii::app()->session['uploads'] = $su;
		}else{
			$fr->fid = $sup[1];
			$fr->save();
			if(!isset($sup[2])) $sup[2] = array();
			$sup[2][] = $fr->id;
		}
		return $fr;
	}

	public function getStringIsSingleItem()
	{
		$result ="";
		$i=0;
		$j=0;
		$items = WmsTaskItem::model()->findAll('task_id = :task_id and del = 0',[':task_id'=>$this->id]);
		foreach ($items as $itm) {
			if (!empty($itm->mdata['si'])) {
				$j +=1;
				if(!empty($itm->mdata['uq'])){
					$tempUq = intval($itm->mdata['uq']);
					if(is_int($tempUq)){
						$i += $tempUq;
					}
				}
			}
		}
		if($j>1){
			$result = "Multi";
		}elseif($j=1){
			if($i==1){
				$result = "Single";
			}else if($i>1){
				$result = "Multi";
			}
		}
		return $result;
	}

	public function getCourierName(){
		$result = "";
		$courierId = $this->deliveryTask->mdata['courier'];
		if(!empty($courierId)){
			$result = Org::model()->findByPk($courierId)->name;
			$result .= " ".$courierId;
		}
		return $result;
	}

	public function getState(){
		$model = Org::model()->findByPk($this->dpt_id);
		return $model->state;
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


