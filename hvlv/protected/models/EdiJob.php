<?php

class EdiJob extends Job{

	public static $my_type = 10;

	private $_totCost = [];
	private $_totRevenue = 0;
	public $wms_task;

	// default supplier cost charge code
	const DEF_SUPPLIER_GL_CODE = '91012';
	const DEF_3PL_GL_CODE = '91020'; // default as warehouse cost

	public static $states = array(
		10 => 'New',
		19 => 'Waiting Confirm',
		20 => 'Confirmed',
		30 => 'Completed',
		40 => 'Cancelled',
	);

	public static $op = [
		10, // Cissy
		503, // Jessie
		609, // Antonio
		266, // Vera
	];

	public static $GOODS = array(
		'g1' =>  'Healthy Product',
		'g2' =>  'UGG Shoes',
		'g3' =>  'Milk Powder',
		'g4' =>  'Baby Formular'
	);

	public static $inv_types = array(
		0 => 'Select One',
		1 => 'Local Service',
		2 => 'Import Air',
		3 => 'Import Sea',
		4 => 'Export Air',
		5 => 'Export Sea',
	);

	public static $secDecOptions = [
		'content' => [
			10 => 'Health Products',
			15 => 'Heathy Food Suppliment',
			20 => 'Milk Powder',
			30 => 'Baby Formula',
			35 => 'Baby Food',
			40 => 'Skincare Products',
			50 => 'Personal Care Products',
			70 => 'Adult Milk Powder',
			80 => 'Swisse Health Supplement',
			60 => 'Others',
		],
		'asic' =>[
			'VEA0153282' => 'Tingqi ZHOU',
			'VEA0150045' => 'Chengjie LIU',
			'VEA0163686' => 'Peter XIE',
			'VEA0175268' => 'Wenhu LU',
			'VEA0177833' => 'Junzhou LI',
		],
		'screening' => [
			'APP' => 'Approved/Known Shipper',
			'XRY' => 'X-Ray',
			'CMD' => 'Electronic Metal Detection',
			'ETD' => 'Explosive Trace Detection',
			'PHS' => 'Physical Examination and/or Hand Search',
		],
		'trucking' =>[
			'927' => 'ILS Services',
			'1114' => 'Roller Truck',
			'1133' => 'TNE Logistics',
			'2485' => 'Royal Express',
		],
		'exemption' => [
			'BIO' => 'Exempt / Biomedical Samples',
			'DIP' => 'Exempt / Diplomatic bags or diplomatic mail',
			'LFS' => 'Exempt / Life saving material (Save Human Life)',
			'MAI' => 'Exempt / Mail',
			'NUC' => 'Exempt / Nuclear Material',
		],
		'info' => [
			10 => 'Cargo has been examined in accordance with a regulation 4.41JA notice.',
			20 => 'Cargo has been examined in accordance with a regulation 4.41J notice.',
			30 => 'Cargo is not required to be examined to receive clearance.',
			40 => 'Cargo has received clearance.',
		],
	];

	public static function tot3PLAccrualMissing($userId){
	   // $sql = "select count(job_line.id) from job left join job_line on job.id = job_line.job_id where job.dpmt = 40 and job.created <= '". date('Y-m-d',strtotime('-30 days')) ."' and job_line.cost_amount = 0";
		$sql = "select count(job_line.id) from job left join job_line on job.id = job_line.job_id where job.dpmt = 40 and job.created >= '2017-07-01' and job_line.cost_amount = 0 and job.user_id = " . $userId;
		$c = Yii::app()->db->createCommand($sql);
		return  floatval($c->queryScalar());
	}

	public static function totAirSeaFreightAccrualMissing($userId){
	   // $sql = "select count(job_line.id) from job left join job_line on job.id = job_line.job_id where job.dpmt = 30 and job.created <= '". date('Y-m-d',strtotime('-30 days')) ."' and job_line.cost_amount = 0";
		$sql = "select count(job_line.id) from job left join job_line on job.id = job_line.job_id where job.dpmt = 30 and job.created >= '2017-07-01' and job_line.cost_amount = 0 and job.user_id = " . $userId;
		$c = Yii::app()->db->createCommand($sql);
		return  floatval($c->queryScalar());
	}

	/**
	 * @param $chargeType
	 * @return bool
	 */
	public static function costCanbeEmpty($chargeType){
		$notEmptyTypes = array('GL1','GL2','GL3','GL5','GL6','GL7','GL8','GL10','GL11','GL12','GL13','GL14');
		if ( in_array($chargeType,$notEmptyTypes)) return false;
		return true;
	}

	/**
	 * map all revenue GL code related cost GL code
	 * @param $glCode
	 * @return string
	 */
	public static function getSupplierCostCode($glCode,$pl = false){

		$chargeCodes = ChargeItemType::model()->find('code = :code',[':code' => $glCode]);
		if ( empty($chargeCodes) ) {
			if ( $pl ) {
				// in case for 3PL
				return self::DEF_3PL_GL_CODE;  // default as warehouse 3pl cost
			} else {
				return self::DEF_SUPPLIER_GL_CODE; // as default one
			}
		} else {
			if ( $pl ) {
				return empty($chargeCodes->pl_cost_code) ? self::DEF_3PL_GL_CODE : $chargeCodes->pl_cost_code;
			} else {
				return empty($chargeCodes->cost_code) ? self::DEF_SUPPLIER_GL_CODE : $chargeCodes->cost_code;
			}
		}
	}

	public function AwbTracking(){
		if(empty($this->awb)) return '';

		if(AwbTracking::canTrack($this->awb)){
			return '<a class="jqm_link" href="tracking/awb/'.$this->awb.'">'.$this->awb.'</a> &#8599';
		}else{
			return $this->awb;
		}
	}

	/**
	 * get Awb Billing GLcodes for awb billing input
	 * @return array
	 */
	public static function getAwbBillingGlCodes(){
		$codes = array();
		$destCodes = array('GL8','GL13','GL14');
		$allCodes = ChargeItemType::model()->findAll();
		foreach ( $allCodes as $line ) {
			$k = $line->code;
			$v = $line->name;
			if ( in_array($k,$destCodes ) ) {
				$codes[$k] = $v;
			}
		}
		return $codes;
	}

	public static function getChargeItemTypes($type = ''){
		$types = array();
		$allCodes = ChargeItemType::model()->findAll();
		foreach ( $allCodes as $line ) {
			if (empty($line->cost_code) && empty($line->pl_cost_code) && $type == 'cost') {
				continue;
			}
			$k = $line->code;
			$types[$k] = $line->name;
		}
		asort($types);
		return $types;
	}

	public static function getChargeCodeByInsideKey($key){
		$allCodes = ChargeItemType::model()->findAll();
		foreach ( $allCodes as $line ) {
			$k = $line->code;
			if ( $k == $key ) return $line->charge_code;
		}

		return '82000'; // default one Export commercial parcel income
	}

	public function rules(){
		$rules = array(
			array('no,owner_id,dpt_id,created,due,type,status', 'required'),
			array('wms_task', 'safe'),
			array('wms_task', 'safe', 'on' => 'search'),
		);
		return array_merge(parent::rules(), $rules);
	}

	public function relations(){
		$r = parent::relations();
		$r['invoice'] = array(self::HAS_MANY, 'Invoice', 'job_id', 'on' => 'invoice.status NOT IN (10)', 'order' => 'invoice.id ASC');
		$r['wmstasks'] = array(self::MANY_MANY, 'WmsTask', 'wms_edi(job_id, task_id)');
		return $r;
	}

	public function getInvoicesString(){
		$invlinks = [];
		if(!empty($this->invoice)){
			foreach ( $this->invoice as $invoice ) {
				if($invoice->isRevert()) continue;
				$invlinks[] = '<a href="'.Yii::app()->createUrl("invoice/print",['id' => $invoice->id]).'" target="_blank">'.($invoice->status == 8? '<s>'.$invoice->no.'</s>' : $invoice->no).'</a>';
			}
		}
		return empty($invlinks)? '<span class="warn">No Invoice</span>' : implode(', ', $invlinks);
	}

	public function getUser() {
		if (!empty($this->user->name)) return $this->user->name;
		return '';
	}

	public function getSP() {
		if (!empty($this->sales->name)) return $this->sales->name;
		return '';
	}

	public function genInvoices($up = false){

	}

	public function totCost(){
		$totalActualCost = 0;
		$cst = [0,0,0];

		$rs = BillingLine::model()->findAll('status != 11 AND billing_ref = :jno', [':jno' => $this->no]);
		foreach ( $rs as $billing ) {
			if ($billing->currency == 1) {
				$exrate = 1;
			} else {
				$exrate = Currency::getExrate($this->created, $billing->currency)[0];
			}
			if (empty($exrate)) {
				$exrate = 1;
			}
			$cst[0] += $billing->accrual_amount / $exrate;
			$cst[1] += $billing->actual_amount / $exrate;
			$cst[2] += $billing->actual_amount == 0 ? $billing->accrual_amount / $exrate : $billing->actual_amount / $exrate;
		}

		$subt = [];
		if (!empty($this->mdata['sub'])) {
			foreach ($this->mdata['sub'] as $sub) {
				$j = EdiJob::model()->findByPk($sub['id']);
				if($j) $subt[] = $j->totCost();
			}
		}
		foreach($subt as $s){
			foreach($s as $k=>$v){
				$cst[$k] +=$v;
			}
		}
		$this->_totCost = $cst;
		return $cst;
	}

	public function getCurrency(){
		return Invoice::$currencies[$this->currency];
	}

	public function getDpmt()
	{
		if (isset(Invoice::$dpmts[$this->dpmt])) {
			return Yii::t(strtolower(__CLASS__), Invoice::$dpmts[$this->dpmt]);
		} else {
			return 0;
		}
	}

	/**
	 * @return int|string
	 */
	public function getAwbId(){
		$ediAwb = EdiAwbConsol::model()->find('awb = :awb',[':awb' => $this->awb]);
		if ( !empty($ediAwb) ) return $ediAwb->id;
		return 0;
	}

	public function totRevenue(){
		$sql = 'SELECT SUM(qty * rate) FROM job_line WHERE job_id = '.$this->id;
		$c = Yii::app()->db->createCommand($sql)->queryScalar();


		$subt = [0, 0];
		if (!empty($this->mdata['sub'])) {
			foreach ($this->mdata['sub'] as $sub) {
				$j = EdiJob::model()->findByPk($sub['id']);
				if($j){
					$sr = $j->totRevenue();
					$subt[0] += is_array($sr)? $sr[0] : $sr;
					$subt[1] += is_array($sr)? $sr[1] : $sr;
				}
			}
		}

		$this->_totRevenue = [round($c + $subt[0], 2)];
		if ($this->currency == 1) {
			return $this->_totRevenue[0];
		} else {
			$exrate = Currency::getExrate($this->created, $this->currency);
			$this->_totRevenue[1] = round($c / $exrate[0] + $subt[1], 2);
			return $this->_totRevenue;
		}
	}

	public function totProfit($monly = true){
		if (!empty($this->mdata['main']) && $monly) return '-';
		if ($this->currency == 1) {
			return round($this->_totRevenue[0] - $this->_totCost[2], 2);
		} else {
			$exrate = Currency::getExrate($this->created, $this->currency);
			return round($this->_totRevenue[1] - $this->_totCost[2], 2);
		}
	}

	public function getWmsTaskPlt()
	{
		$count = 0;
		if (!empty($this->wmstasks)) {
			foreach ($this->wmstasks as $wmstask) {
				$count += count($wmstask->actionTask->items);
			}
		}

		if (!empty($this->mdata['plt'])) {
			$count = $this->mdata['plt'];
		}
		return $count;
	}

	public function getATA()
	{
		$item = EdiJobAirline::model()->find(['condition' => 'job_id = :job_id', 'params' => [':job_id' => $this->id], 'order' => 'ata DESC']);
		return @$item->ata;
	}

	public function getTransitType()
	{
		$item = EdiJobAirline::model()->find(['condition' => 'job_id = :job_id', 'params' => [':job_id' => $this->id], 'order' => 'transit DESC']);
		if (empty($item)) {
			return '';
		} else if ($item->transit == 1) {
			return 'Direct';
		} else if ($item->transit > 1) {
			return 'Transit';
		}
	}

	public function getResult()
	{
		$ata = $this->getATA();
		$etd = $this->awbconsol->etd;
		$transit = $this->getTransitType();

		if (empty($ata) || $ata < $etd) {
			return '';
		}

		if ($transit == 'Direct') {
			if (in_array(date('N', strtotime($etd)), [4,5]) && date('Y-m-d', strtotime($etd . ' + 4 day')) >= $ata) {
				return '合格';
			} else if (date('N', strtotime($etd)) == 6 && date('Y-m-d', strtotime($etd . ' + 3 day')) >= $ata) {
				return '合格';
			} else if (date('Y-m-d', strtotime($etd . ' + 2 day')) >= $ata) {
				return '合格';
			} else {
				return '不合格';
			}
		} else if ($transit == 'Transit') {
			if (in_array(date('N', strtotime($etd)), [2,3,4,5]) && date('Y-m-d', strtotime($etd . ' + 6 day')) >= $ata) {
				return '合格';
			} else if (date('N', strtotime($etd)) == 6 && date('Y-m-d', strtotime($etd . ' + 5 day')) >= $ata) {
				return '合格';
			} else if (date('Y-m-d', strtotime($etd . ' + 4 day')) >= $ata) {
				return '合格';
			} else {
				return '不合格';
			}
		}
	}

	public function getAllDone()
	{
		if (!empty($this->mdata['all_done'])) {
			return '<span class="icon icon-check"></span>';
		} else {
			return '';
		}
	}

	public function beforeSave() {
		if (empty($this->owner->extra['op_id'])) {
			$this->addError('op_id', 'Please set Account Manager for client');
			return false;
		} else if (empty($this->owner->extra['sp_id'])) {
			$this->addError('sp_id', 'Please set Sales Person for client');
			return false;
		}
		$this->user_id = empty($this->owner->extra['op_id'])? 0 : $this->owner->extra['op_id'];
		$this->sp_id = empty($this->owner->extra['sp_id'])? 0 : $this->owner->extra['sp_id'];

		return parent::beforeSave();
	}

	public function afterSave(){
		return parent::afterSave();
	  //  Ledger::createDefCostConsole($this->id,Ledger::TYPE_IMPORT_DEF);
	}

	public function getWmsTask($del = true)
	{
		foreach ($this->wmstasks as $k => $task) {
			echo '<a class="tab_link" href="/wmsTask/update/' . $task->id . '" title="' . $task->no . '">' . $task->no . '</a><a class="ajax_link" href="' . Yii::app()->createUrl('wmsTask/deleteJob', ['task_id' => $task->id, 'job_id' => $this->id]) . '">' . ($del ? '<div style="background-position: -272px -128px" class="icon"></div></a>' : '');
			if ($k != sizeof($this->wmstasks) - 1) {
				echo '&nbsp;&nbsp;';
			}
		}
	}

	public function search($page = true, $ec = false)
	{
		$criteria = new CDbCriteria;
		if (!empty($this->wms_task)) {
			$criteria->with = 'wmstasks';
			$criteria->compare('CONCAT("T", wmstasks.id)', $this->wms_task, true);
		}

		return parent::search(true, $criteria);
	}

}
