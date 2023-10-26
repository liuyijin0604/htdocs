<?php

/**
 * This is the model class for table "billing_line".
 *
 * The followings are the available columns in table 'billing_line':
 * @property string $id
 * @property  $billing_id
 * @property string $org_id
 * @property integer $op_id
 * @property integer $link_id
 * @property string $to_id    The client who take this cost. for example if JCEX AND CHINYUN in ONE Consol, we need to know this cost is for JCEX OR CHINYUN;
 * @property string $created
 * @property string $date
 * @property string $due
 * @property string $transaction_date
 * @property string $type
 * @property string $dpmt
 * @property string $gst
 * @property integer $status
 * @property string $billing_cref
 * @property string $billing_ref
 * @property string $awb
 * @property string $dpt_id
 * @property integer $currency
 * @property string $no
 * @property string $actual_amount
 * @property string $accrual_amount
 * @property string $gst_amount
 * @property string $charge_code
 * @property string $desc
 * @property integer $qty
 * @property string $item_code
 * @property string $price
 * @property string $weight
 * @property string $charge_weight
 * @property integer $sync_xero
 * @property string $meta
 */
class BillingLine extends oActiveRecord
{
	public $client, $revenue;
	public $date_from, $date_to, $dept, $billing_total, $note;
	public $mdata = array();
	public $triggerCal = false;

	public static $flags = array(
		1 => 'Second Delivery',
		2 => 'Manual Handling',
		4 => 'P.O. BOX',
		8 => 'Road Express Fee',
		16 => 'OTHER',
	);

	const BILLING_FLAG_SECOND = 1;
	const BILLING_FLAG_MANUAL = 2;
	const BILLING_FLAG_PO_BOX = 4;
	const BILLING_FLAG_ROAD_EXPRESS = 8;
	const BILLING_FLAG_OTHER = 16;

	const BILLING_TYPE_IMPORT = 1;
	const BILLING_TYPE_EXPORT = 2;
	const BILLING_TYPE_AIR_SEA = 3;
	const BILLING_TYPE_3PL = 4;
	const BILLING_TYPE_TOW_SERVICE = 5;
	const BILLING_TYPE_OTHERS = 6;
	const BILLING_TYPE_TOP_COURIER_SERVICE = 7;

	public static $types = array(
		'1' => 'Import',
		'2' => 'Export',
		'3' => 'Air/Sea',
		'4' => '3PL',
		'5' => 'Tow Service',
		'6' => 'Others',
		'7' => 'Top Courier Service'
	);

	public static $states = array(
		'1' => 'Pending',
		'2' => 'Confirm',
		'3' => 'Posted',
		'4' => 'Invoice Check',
		'10' => 'Accrual Check',
		'11' => 'Cancelled',
	);

	public static $general_cost_dpmts = array(
		'3PL' => '3PL',
		'Small Parcel - Export' => 'Small Parcel - Exp',
		'Small Parcel - Import' => 'Imp',
		'Commercial - Export & Import (Ocean Freight & Airfreight)' => 'Commercial - E/I',
	);

	public static $xero_segments = array(
		'1' => 'Imp',
		'2' => 'Small Parcel - Exp',
		'3' => 'Commercial - E/I',
		'4' => '3PL',
	);

	public static $desc = array(
		// clearance
		'Customs Disbursement Charges' => 91032,
		'CUSTOMS CLEARANCE' => 91032,
		'Customs Clearance / Agency Fees' => 91032,
		// terminal
		'Import Document Fee' => 91030,
		'Import Terminal Charge' => 91030,
		'Import Document Fee RD' => 91030,
		'Terminal Handling Import RD' => 91030,
		'Import Document Fee (IDF)' => 91030,
		'Import Storage (General Cargo)' => 91030,
		'Import Terminal Handling Fee (ITF)' => 91030,
		'Import Terminal Handling Fee' => 91030,
		'ULD Per Kg Fee' => 91030,
		'International Terminal Fee Loose' => 91030,
		'Import Storage Fee General Loose' => 91030,
		// truck
		'40 DROPOUT HIGH CUBE' => 91031,
		'1 x VBS TERMINAL TIMESLOTTING FEE' => 91031,
		'DP WORLD INFRASTRUCTURE FEE' => 91031,
		'HAZARDOUS SURCHARGE' => 91031,
		'EMPTY PARK BOOKING FEE' => 91031,
		'TOLL CHARGES - DANDENONG SOUTH' => 91031,
		'Fuel Levy' => 91031,
		'Fuel Surcharge' => 91031,
		// port ocean
		'Destination Port Charges' => 91033,
		'Destination Terminal Handling Charges' => 91033,
		'Delivery Order Fee' => 91033,
		'Destination Customs Managememt Re-Engineering Fee' => 91033,
		'Destination Cargo Reporting Fee' => 91033,
		'Import Processing Fee' => 91033,
		'Admin Fee' => 91033,
		'DEST TRML HANDLG' => 91033,
		'DEST DOC FEE' => 91033,
		'THC DESTINATION' => 91033,
		'LIFT ON/OFF IMPORT' => 91033,
		'SECURITY CH.DEST.' => 91033,
		'DOC.FEE/B/L ISS.(I)' => 91033,
		'Documentation fee - Destination' => 91033,
		'Terminal Handling Service - Destination' => 91033,
		'TERMINAL HANDLING CHARGE (D)' => 91033,
		'DOC FEE (DEST)' => 91033,
		'LANDSIDE CHARGE' => 91033,
	);

	public function getStatus()
	{
		return Yii::t(strtolower(__CLASS__), self::$states[$this->status]);
	}

	private function shouldAddGst($gst)
	{
		if (!empty($gst)) {
			if (in_array($gst, ['OUTPUT', 'INPUT', 'CAPEXINPUT', 'GSTONCAPIMPORTS', 'GSTONIMPORTS'])) {
				return true;
			}
		}
		return false;
	}

	/**
	 * get 30 days before missing import actual data
	 */
	public static function totImportActualMissing()
	{
		$sql = "select count(id) from billing_line where dpmt = 10 and created <= '" . date('Y-m-d', strtotime('-30 days')) . "' and actual_amount = 0";
		$c = Yii::app()->db->createCommand($sql);
		return floatval($c->queryScalar());
	}
	public static function totExportActualMissing()
	{
		$sql = "select count(id) from billing_line where dpmt = 20 and created <= '" . date('Y-m-d', strtotime('-30 days')) . "' and actual_amount = 0";
		$c = Yii::app()->db->createCommand($sql);
		return floatval($c->queryScalar());
	}

	public static function tot3PLActualMissing()
	{
		$sql = "select count(id) from billing_line where dpmt = 40 and created <= '" . date('Y-m-d', strtotime('-30 days')) . "' and actual_amount = 0";
		$c = Yii::app()->db->createCommand($sql);
		return floatval($c->queryScalar());
	}

	public static function totAirSeaFreightMissing()
	{
		$sql = "select count(id) from billing_line where dpmt = 30 and created <= '" . date('Y-m-d', strtotime('-30 days')) . "' and actual_amount = 0";
		$c = Yii::app()->db->createCommand($sql);
		return floatval($c->queryScalar());
	}

	public function getType()
	{
		return Yii::t(strtolower(__CLASS__), self::$types[$this->type]);
	}

	public function getDptName()
	{
		$dptList = Org::dptList();
		if (isset($dptList[$this->dpt_id])) {
			return Yii::t(strtolower(__CLASS__), $dptList[$this->dpt_id]);
		} else {
			return '';
		}
	}

	public function getDpmt()
	{
		if (isset(Invoice::$dpmts[$this->dpmt])) {
			return Yii::t(strtolower(__CLASS__), Invoice::$dpmts[$this->dpmt]);
		} else {
			return 0;
		}
	}

	public function getNo($fix = null)
	{
		if ($fix == null || $fix == 10) {
			if ($this->dpmt != 40) {
				$terms = array('no' => $this->billing_ref);
			} else {
				$terms = array('id' => substr($this->billing_ref, 1));
			}

			if ($fix == 10) {
				$terms['fix'] = true;
				$terms['tab'] = 'billing';

				if ($this->dpmt == 10) {
					return Yii::app()->createURL("imcoConsol/updateByNo", $terms);
				} else if ($this->dpmt == 20) {
					return Yii::app()->createURL("excoConsol/updateByNo", $terms);
				} else if ($this->dpmt == 30) {
					return Yii::app()->createURL("ediJob/updateByNo", $terms);
				} else if ($this->dpmt == 40) {
					return Yii::app()->createURL("wmsTask/update", $terms);
				}
			} else {
				if ($this->dpmt == 10) {
					$consol = Consol::model()->find('no = :no', [':no' => $this->billing_ref]);
					if (empty($consol)) return $this->billing_ref;
					else return "<a href=\"" . Yii::app()->createURL($consol->getType() . "/updateByNo", $terms) . "\" class=\"tab_link\" title=\"" . $this->billing_ref . "\">" . $this->billing_ref . "</a>";
				} else if ($this->dpmt == 20) {
					return "<a href=\"" . Yii::app()->createURL("excoConsol/updateByNo", $terms) . "\" class=\"tab_link\" title=\"" . $this->billing_ref . "\">" . $this->billing_ref . "</a>";
				} else if ($this->dpmt == 30) {
					return "<a href=\"" . Yii::app()->createURL("ediJob/updateByNo", $terms) . "\" class=\"tab_link\" title=\"" . $this->billing_ref . "\">" . $this->billing_ref . "</a>";
				} else if ($this->dpmt == 40) {
					return "<a href=\"" . Yii::app()->createURL("wmsTask/update", $terms) . "\" class=\"tab_link\" title=\"" . $this->billing_ref . "\">" . $this->billing_ref . "</a>";
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function getAllSuppliers()
	{
		// we think all Courier CN(10), Courier AU(15), Supplier(70) as all supplier type
		$criteria = new CDbCriteria();
		$criteria->addInCondition('type', array(10, 15, 70));
		$orgs = Org::model()->findAll($criteria);
		$suppliers = array();
		foreach ($orgs as $org) {
			$suppliers[$org->id] = $org->code . ':' . $org->name;
		}
		return $suppliers;
	}

	/**
	 * @param $rs
	 * @param $key
	 * @return int
	 */
	public function getTotal(&$rs, $key)
	{
		$total = 0;
		if (empty($rs)) {
			return $total;
		}
		foreach ($rs as $r) {
			switch ($key) {
				case 'accrual_amount':
					$total +=  $this->getByCurrency($r->accrual_amount,$r);
					break;

				case 'actual_amount':
					$total += $this->getByCurrency($r->actual_amount,$r);
					break;

				case 'gst_amount':
					$total += $this->getByCurrency($r->gst_amount,$r);
					break;
			}
		}
		return $total;
	}
	
	public static function getByCurrency($value,$r)
	{
		if ($r instanceof BillingLine) {
			$currency = $r->currency;
		} else {
			$currency = $r;
		}
		if($currency==1)
		{
			return $value;
		}else
		{
			$rate = Currency::getExrate('',$r->currency)[0];
			if (empty($rate)) $rate = 1;
			return round($value / $rate,2);
		}
	}

	public function getCurrency()
	{
		return Yii::t(strtolower(__CLASS__), Billing::$currencies[$this->currency]);
	}

	public function canConfirm()
	{
		return in_array($this->status, [1]);
	}

	public function canSyncXero()
	{
		return in_array($this->status, [2]);
	}

	public function getGSTValue($gstPercent = 10)
	{
		if ($this->shouldAddGst($this->gst)) {
			if ($gstPercent == 10) {
				$gstPercent = empty(Billing::$gstPercents[$this->currency]) ? 10 : Billing::$gstPercents[$this->currency];
			}
			return round($this->actual_amount * $gstPercent / 100, 2);
		}
		return 0;
	}

	/**
	 * based on billing line type to get job or consol revenue by GL code
	 * @return int
	 */
	public function getRevenue()
	{
		$revenue = 0;

		switch ($this->type) {
			case self::BILLING_TYPE_IMPORT:
				break;

			case self::BILLING_TYPE_EXPORT:
				break;

			case self::BILLING_TYPE_AIR_SEA:{
					$cost = $this->actual_amount;
					$revenueTotal = 0;
					if ($cost <= 0) {
						$cost = $this->accrual_amount;
					}

					$job = EdiJob::model()->find('no = :no', [':no' => $this->billing_ref]);
					if (!empty($job)) {
						$glcode = $this->item_code;
						// find revenue based on gl code
						foreach ($job->lines as $line) {
							if ($glcode == $line->ccode) {
								$revenueTotal += round($line->qty * $line->rate, 2);
							}
						}
					}
					$revenue = $revenueTotal - $cost;
				}
				break;
		}

		return $revenue;
	}

	public static function getModel($ref)
	{
		$nonexist = 1;
		if (preg_match('/\d{3}(\-|)\d{8}/', $ref)) {
			// awb
			$awbNoDash = str_replace('-', '', $ref);
			$dashAwb = substr($awbNoDash, 0, 3) . '-' . substr($awbNoDash, 3);

			$consol = Consol::model()->find('awb = :awb1 OR awb = :awb2', array(':awb1' => $awbNoDash, ':awb2' => $dashAwb));
			if (empty($consol)) {
				$consol = EdiJob::model()->find('awb = :awb1 OR awb = :awb2', array(':awb1' => $awbNoDash, ':awb2' => $dashAwb));
			}
			if (!empty($consol)) {
				$nonexist = 0;
			}
		} else if (preg_match('/JB(\d{8})/', $ref)) {
			// edijob - JB\d{8}
			$consol = EdiJob::model()->find('no = :no', array(':no' => $ref));
			if (!empty($consol)) {
				$nonexist = 0;
			}
		} else {
			// consol - C\d{8} | DW\d{8} / container no
			$consol = Consol::model()->find('no = :no OR meta like :ctn OR awb = :no', array(':no' => $ref, ':ctn' => '%' . $ref . '%'));
			if (!empty($consol)) {
				$nonexist = 0;
			}
		}

		// shipment no
		if ($nonexist) {
			$shipment = Shipment::model()->find('hbn = :ref or ref = :ref', [':ref' => $ref]);
			if (!empty($shipment->consol)) {
				$consol = $shipment->consol;
				$nonexist = 0;
			}
		}

		return array('consol' => $consol, 'nonexist' => $nonexist);
	}

	public static function copy($old)
	{
		$new = new BillingLine;
		$new->billing_id = $old->billing_id;
		$new->org_id = $old->org_id;
		$new->op_id = $old->op_id;
		$new->link_id = $old->link_id;
		$new->to_id = $old->to_id;
		$new->created = $old->created;
		$new->date = $old->date;
		$new->due = $old->due;
		$new->transaction_date = $old->transaction_date;
		$new->type = $old->type;
		$new->dpmt = $old->dpmt;
		$new->gst = $old->gst;
		$new->status = $old->status;
		$new->billing_cref = $old->billing_cref;
		$new->billing_ref = $old->billing_ref;
		$new->awb = $old->awb;
		$new->dpt_id = $old->dpt_id;
		$new->currency = $old->currency;
		$new->charge_code = $old->charge_code;
		$new->desc = $old->desc;
		$new->qty = $old->qty;
		$new->item_code = $old->item_code;
		$new->price = $old->price;
		$new->weight = $old->weight;
		$new->charge_weight = $old->charge_weight;
		$new->sync_xero = $old->sync_xero;
		$new->actual_amount = 0;
		$new->gst_amount = $new->getGSTValue();
		$new->mdata = $old->mdata;
		$new->save();

		return $new;
	}

	public function isBillingClosed()
	{
		if (isset($this->mdata['accrual_closed']) && $this->mdata['accrual_closed'] == 1) {
			return true;
		}

		return false;
	}

	public function getCostAmount()
	{
		return $this->accrual_amount;
	}

	public function beforeSave()
	{
		$this->actual_amount = number_format($this->actual_amount, 2, '.', '');
		$this->accrual_amount = number_format($this->accrual_amount, 2, '.', '');

		if (empty($this->created) || $this->created === '0000-00-00') {
			$this->created = date('Y-m-d');
		}

		if (empty($this->transaction_date) || $this->transaction_date === '0000-00-00') {
			$this->transaction_date = $this->created;
		}

		if (empty($this->date) || $this->date === '0000-00-00') {
			$this->date = date('Y-m-d');
		}

		if (empty($this->due) || $this->due === '0000-00-00') {
			$this->due = $this->date;
		}

		if (empty($this->status)) {
			$this->status = 1;
		}

		if (empty($this->no)) {
			$this->no = $this->genNo();
		}

		if (!empty($this->dept)) {
			$this->mdata['gdepartment'] = $this->dept;
		}

		if (!empty($this->mdata)) {
			$this->meta = json_encode($this->mdata);
		} else {
			$this->meta = '';
		}

		if (!empty($this->billing_id)) {
			$o = BillingLine::model()->findByPk($this->id);
			if ($this->isNewRecord || empty($o->billing_id)) {
				$this->triggerCal = true;
			} else {
				if ($o->actual_amount != $this->actual_amount) {
					$this->triggerCal = true;
				}
			}
		}

		$this->actual_amount = number_format(round($this->actual_amount * 100) / 100, 2, '.', '');
		$this->accrual_amount = number_format(round($this->accrual_amount * 100) / 100, 2, '.', '');
		$this->gst_amount = number_format(round($this->gst_amount * 100) / 100, 2, '.', '');

		if (!empty($this->getErrors())) {
			foreach ($this->getErrors() as $k => $errors) {
				yii::log($this->id . ' ' . $k . ' ' . $this->{$k} . ' ' . json_encode($errors), 'warning');
			}
		}

		return true;
	}

	public function afterSave()
	{
		if (!empty($this)) {
			// push actual cost to p & l ledger table
			$this->syncWithPlLedger();
		}

		// for AirFreightInvoiceReconciliation
		$af = AFInvoiceReconciliation::model()->find('invoice_no = :inv AND supplier_id = :supplier_id', [':inv' => $this->billing_cref, ':supplier_id' => $this->org_id]);
		if (!empty($af) && $this->billing_id == 0 && $this->status != Billing::BILLING_STATUS_CANCELLED) {
			Billing::linkLine($this, false, $this->status);
		}

		if ($this->triggerCal) {
			$this->refresh();
			if (!empty($this->billing)) {
				$this->billing->calTotal();
			}
			$this->triggerCal = false;
		}
	}

	public function afterFind()
	{
		if (!empty($this->meta)) {
			$this->mdata = json_decode($this->meta, true);
		}

		if (empty($this->desc) && !empty($this->mdata['rts_manifest_no'])) {
			$this->desc = 'RTS ' . $this->mdata['rts_manifest_no'];
		}

		if (isset($this->mdata['gdepartment'])) {
			$this->dept = $this->mdata['gdepartment'];
		}
	}

	/**
	 * pl ledger actual amount changed
	 * sync all details with P & L ledger table
	 */
	private function syncWithPlLedger()
	{
		// check actual cost
		if ($this->actual_amount > 0) {
			if (($this->org_id == Org::ORGID_COURIER_TOLL || $this->org_id == Org::ORGID_COURIER_STARTRACK) &&
				$this->charge_code == Consol::AU_LOCAL_DELIVERY_COST_GL_CODE
			) {
				$pl = PlLedger::model()->find('fid = :fid AND model = :model AND gl = 29  AND grp2 = :cid AND dpmt = :dpmt',
					[':fid' => $this->link_id, ':cid' => $this->org_id, ':model' => 'ImParcel', ':dpmt' => Invoice::DPMT_IMPORT]);
				if (!empty($pl)) {
					$pl->actual_amt = $this->actual_amount;
					$pl->update('actual_amt');
				}
			} else {
				// $pl = PlLedger::model()->find('fid = :fid AND model = :model AND gl = 29  AND grp2 = :cid AND dpmt = :dpmt',
				//     [':fid' => $this->link_id, ':cid' =>  $this->org_id , ':model' => 'ImParcel',':dpmt' => Invoice::DPMT_IMPORT]);
				// if ( !empty($pl) ) {
				//     $pl->actual_amt = $this->actual_amount;
				//     $pl->update('actual_amt');
				// }
			}
		}
	}

	public function updateMeta()
	{
		$this->meta = json_encode($this->mdata);
		$this->update(['meta']);
	}
	/**
	 * generate billing number
	 * @return string
	 */
	public function genNo()
	{
		$n = 'BL' . date('ymd', strtotime($this->created));
		$s = self::model()->count('no LIKE :n', [':n' => $n . '%']) + 1;
		return $n . sprintf('%02d', $s) . 'SYD';
	}

	/**
	 * @param $ledger
	 * @throws CDbException
	 */
	public static function deleteImportConsolBillingLine($ledger)
	{
		$line = BillingLine::model()->find('link_id = :lid', [':lid' => $ledger->id]);
		if (!empty($line)) {
			// only can delete not posted billing
			if ($line->status < 3) {
				$line->delete();
			}
		}
	}

	/**
	 * @param $ledger
	 * @throws CDbException
	 */
	public static function deleteExportConsolBillingLine($ledger)
	{
		$line = BillingLine::model()->find('link_id = :lid', [':lid' => $ledger->id]);
		if (!empty($line)) {
			// only can delete not posted billing
			if ($line->status < 3) {
				$line->delete();
			}
		}
	}

	/**
	 * @param $jobLine
	 * @throws CDbException
	 */
	public static function deleteExportEdiBillingLine($jobLine)
	{
		$line = BillingLine::model()->find('link_id = :lid', [':lid' => $jobLine->id]);
		if (!empty($line)) {
			// only can delete not posted billing
			if ($line->status < 3) {
				// in case is frozen , we can't delet it again
				$plLedger = PlLedger::model()->find('fid = :fid AND model = :model', [':fid' => $line->id, ':model' => 'BillingLine']);
				if (!empty($plLedger)) {
					$plLedger->delete();
				}
				$line->delete();
			}
		}
	}

	/**
	 * get all total Op lines for specified type
	 * @param $type
	 * @return int
	 */
	public static function totOpLines($type, $status = 1)
	{
		$sql = 'SELECT count(id) FROM billing_line WHERE status =  ' . $status . ' AND type = ' . $type;
		$c = Yii::app()->db->createCommand($sql);
		return $c->queryScalar();
	}

	/**
	 * @param $job
	 * @param $billingInfo
	 */
	public static function checkMatchedEdiJobRealCost($job)
	{
		$afLines = AFInvoiceReconciliation::model()->findAll('model = :model AND fid = :fid AND matched_result != 1', [':model' => 'EdiJob', ':fid' => $job->id]);
		if (!empty($job->awb)) {
			$afLines = array_merge($afLines, AFInvoiceReconciliation::model()->findAll('meta like :model AND meta like :awb AND matched_result != 1', [':model' => '%EdiJob%', ':awb' => '%' . str_replace('-', '', explode('_', $job->awb)[0]) . '%']));
		}
		if (!empty($afLines)) {
			foreach ($afLines as $afLine) {
				// get job cost lines related gl code
				$costGlCodes = array();
				if (in_array($afLine->supplier_id, [954, 964])) {
					$jobCostLines = BillingLine::model()->findAll('billing_ref = :bref AND billing_cref = :cref', [':bref' => $job->no, ':cref' => $afLine->invoice_no]);
				} else if ($afLine->supplier_id == 1133) {
					$jobCostLines = [];
					foreach ($afLine->mdata['model'] as $item) {
						$edijob = EdiJob::model()->findByPk($item['id']);
						$jobCostLines[] = BillingLine::model()->find('billing_ref = :bref AND billing_cref = :cref', [':bref' => $edijob->no, ':cref' => $afLine->invoice_no]);
					}
				}
				$att = 0;
				if (empty($jobCostLines)) {
					continue;
				}
				foreach ($jobCostLines as $line) {
					if ($line->org_id == $afLine->supplier_id) {
						// $costGlCodes[$line->item_code] = $line;
						$att += $line->accrual_amount;
					}
				}

				$result = true;
				// check lines count
				if (count($afLine->lines) > count($costGlCodes) && false) {
					$afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_WRONG_LINES_COUNT_NOTMATCHED;
					$result = false;
				} else {
					// check lines content
					if (empty($afLine->lines) && false) {
						$result = false;
						$afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_WRONG_LINES_COUNT_NOTMATCHED;
					}
					$btt = 0;
					foreach ($afLine->lines as $line) {
						$btt += $line->amount;

						// if (!isset($costGlCodes[$line->glcode])) {
						//     $afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_WRONG_LINES_GLCODE_NOTMATCHED;
						//     $result = false;
						// } else {

						//     // we update actual cost
						//     $jbCostLine = $costGlCodes[$line->glcode];
						//     $jbCostLine->actual_amount = $line->amount;
						//     $jbCostLine->billing_cref = $afLine->invoice_no;
						//                    if ( $line->gst > 0 ) {
						//                        $jbCostLine->gst ='INPUT' ;
						//                    } else {
						//                        $jbCostLine->gst = 'EXEMPTEXPENSES';
						//                    }
						//     $jbCostLine->update('actual_amount','billing_cref','gst');

						//     // ... TODO soon
						//     // if actual is small or equal to accrual amount , just post billing line to accouting tab
						//     // ....
						//     if ( $jbCostLine->accrual_amount * 1.05 < $line->amount && abs($line->amount - $jbCostLine->accrual_amount) > 5) {
						//         $afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_WRONG_DIFF_TOOBIG;
						//         $result = false;
						//     }
						// }
					}
					if ((abs($btt) < abs($att) * 0.8 || $btt - $att > 50 || abs($btt) > abs($att) * 1.05) && in_array($afLine->supplier_id, [954, 964])) {
						$afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_WRONG_DIFF_TOOBIG;
						$afLine->mdata['nt'] = 'diff ' . number_format($btt - $att, 2);
						$result = false;
					}
					if ($att < $btt && $afLine->supplier_id == 1133) {
						$afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_WRONG_DIFF_TOOBIG;
						$afLine->mdata['nt'] = 'diff ' . number_format($btt - $att, 2);
						$result = false;
					}
					if ($result) {
						$afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_SUCCESS;

						// push all matched to billing accounting tab directly
						// foreach ($afLine->lines as $line) {
						//     if ( isset($costGlCodes[$line->glcode])) {
						//         $bline = $costGlCodes[$line->glcode];
						//         $bline->status = 2; // set as confirmed status
						//         $bline->update('status');
						//     }
						// }
					}
				}

				// update now
				$afLine->update('matched_result', 'meta');
			}
		}
	}

	/**
	 * @param $consol
	 * @throws CDbException
	 */
	public static function checkMatchedExconsolRealCost($consol)
	{
		$afLines = AFInvoiceReconciliation::model()->findAll('model = :model AND fid = :fid AND matched_result != 1', [':model' => 'ExcoConsol', ':fid' => $consol->id]);
		if (!empty($consol->awb)) {
			$afLines = array_merge($afLines, AFInvoiceReconciliation::model()->findAll('meta like :model AND meta like :awb AND matched_result != 1', [':model' => '%ExcoConsol%', ':awb' => '%' . str_replace('-', '', $consol->awb) . '%']));
		}
		if (!empty($afLines)) {
			foreach ($afLines as $afLine) {
				// get job cost lines related gl code
				$costGlCodes = array();
				if (in_array($afLine->supplier_id, [954, 964])) {
					$jobCostLines = BillingLine::model()->findAll('billing_ref = :bref AND billing_cref = :cref', [':bref' => $consol->no, ':cref' => $afLine->invoice_no]);
				} else if ($afLine->supplier_id == 1133) {
					$jobCostLines = [];
					foreach ($afLine->mdata['model'] as $item) {
						$exco = ExcoConsol::model()->findByPk($item['id']);
						$jobCostLines[] = BillingLine::model()->find('billing_ref = :bref AND billing_cref = :cref', [':bref' => $exco->no, ':cref' => $afLine->invoice_no]);
					}
				}
				$att = 0;
				foreach ($jobCostLines as $line) {
					if ($line->org_id == $afLine->supplier_id) {
						// $costGlCodes[$line->item_code] = $line;
						$att += $line->accrual_amount;
					}
				}

				$result = true;
				// check lines count
				if (count($afLine->lines) > count($costGlCodes) && false) {
					$afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_WRONG_LINES_COUNT_NOTMATCHED;
					$result = false;
				} else {
					// check lines content
					if (empty($afLine->lines) && false) {
						$result = false;
						$afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_WRONG_LINES_COUNT_NOTMATCHED;
					}
					$btt = 0;
					foreach ($afLine->lines as $line) {
						$btt += $line->amount;
						// if (!isset($costGlCodes[$line->glcode]) && false) {
						//     $afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_WRONG_LINES_GLCODE_NOTMATCHED;
						//     $result = false;
						// } else {

						//     // we update actual cost
						//     $jbCostLine = $costGlCodes[$line->glcode];
						//     $jbCostLine->actual_amount = $line->amount;
						//     $jbCostLine->billing_cref = $afLine->invoice_no;
						//                    if ( $line->gst > 0 ) {
						//                        $jbCostLine->gst = 'EXEMPTEXPENSES';
						//                    } else {
						//                        $jbCostLine->gst = 'INPUT';
						//                    }
						//     $jbCostLine->update('actual_amount','billing_cref','gst');

						//     // ... TODO soon
						//     // if actual is small or equal to accrual amount , just post billing line to accouting tab
						//     // ....
						//     if ( $jbCostLine->accrual_amount * 1.1 < $line->amount && abs($line->amount - $jbCostLine->accrual_amount) > 10 ) {
						//         $afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_WRONG_DIFF_TOOBIG;
						//         $result = false;
						//     }
						// }
					}
					if ((abs($btt) < abs($att) * 0.8 || $btt - $att > 50 || abs($btt) > abs($att) * 1.05) && in_array($afLine->supplier_id, [954, 965])) {
						$afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_WRONG_DIFF_TOOBIG;
						$afLine->mdata['nt'] = 'diff ' . number_format($btt - $att, 2);
						$result = false;
					}
					if ($att != $btt && $afLine->supplier_id == 1133) {
						$afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_WRONG_DIFF_TOOBIG;
						$afLine->mdata['nt'] = 'diff ' . number_format($btt - $att, 2);
						$result = false;
					}
					if ($result) {
						$afLine->matched_result = AFInvoiceReconciliation::MATCHED_RESULT_SUCCESS;
					}
				}

				// update now
				$afLine->update('matched_result', 'meta');
			}
		}
	}

	/**
	 * @param $job
	 * @param $billingInfo
	 * @return string
	 */
	public static function saveExportAirFreightBillingByBilling($job, $billingInfo)
	{

		// just update
		if ($billingInfo->id > 0) {
			$billing = $billingInfo;
		} else {
			// create a new one
			$billing = new BillingLine();
			$billing->billing_cref = '';
			$billing->status = 1;
			$billing->date = $job->created;
			$billing->weight = 0;
			$billing->charge_weight = 0;
			$billing->link_id = 0;
		}

		$billing->accrual_amount = floatval($billingInfo->price) * floatval($billingInfo->qty);
		$gstAmount = 0;
		if ($billingInfo->gst == 'INPUT') {
			$gstAmount = round($billing->accrual_amount * 10 / 100, 2);
		}
		// if  ( $billing->accrual_amount  <= 0 ) {
		//     return 'accural amount must not be zero!';
		// }

		$billing->due = $job->due;
		// $billing->currency = !empty($job) ?  $job->currency : 1;
		$billing->billing_ref = !empty($job) ? $job->no : '';

		$billing->dpt_id = !empty($job) ? $job->dpt_id : 0;
		$billing->dpmt = $job->dpmt;
		// $billing->type = BillingLine::BILLING_TYPE_AIR_SEA;
		$billing->type = $job->dpmt / 10;
		$billing->org_id = $billingInfo->org_id;
		$billing->desc = $billingInfo->desc;
		$billing->gst = $billingInfo->gst;
		$billing->price = $billingInfo->price;
		$billing->qty = $billingInfo->qty;
		$billing->item_code = $billingInfo->item_code;

		// get related supplier cost gl code
		$billing->charge_code = EdiJob::getSupplierCostCode($billingInfo->item_code, ($job->dpmt == Job::DPMT_3PL));

		$billing->op_id = isset(Yii::app()->user) ? Yii::app()->user->id : 0;
		$billing->sync_xero = 0;
		$billing->beforeSave();
		$billing->save();
		$errors = $billing->getErrors();

		$errMsg = '';
		// push to P&L ledger as well
		if (empty($errors)) {
			$chargeCodeModel = Chargecode::model()->find('status = 1 AND code = :code', [':code' => $billing->charge_code]);
			$chargeCodeId = 0;
			if (!empty($chargeCodeModel)) {
				$chargeCodeId = $chargeCodeModel->id;
			}

			// check accrual cost
			if ($billing->accrual_amount > 0) {
				//if ( $chargeCodeId <= 0 ) {
				// we should notify admin charge code not found
				// ...
				//} else
				//{

				$d = [
					'gl' => $chargeCodeId,
					'fid' => $billing->id,
					'model' => 'BillingLine',
					'dpt_id' => $billing->dpt_id,
					'lid' => 0,
					'org_id' => $billing->org_id,
					'dpmt' => $billing->dpmt,
					'grp1' => '',
					'date' => $billing->created,
					'amt' => $billing->accrual_amount,
					'gst' => $gstAmount,
				];
				/*$pl = PlLedger::add($d, true, ['org_id', 'dpmt', 'date', 'lid', 'grp1', 'grp2']);
				$pl->getErrors();
				if ( !empty($errs) ) {
				foreach ( $errors as $k => $err ) {
				$errMsg .= implode(' ' , $err);
				}
				}*/
				//}
			}
		} else {
			foreach ($errors as $k => $err) {
				$errMsg .= implode(' ', $err);
			}
		}

		return $errMsg;
	}

	/**
	 * save export edi cost into our billing table
	 * @param $ledger
	 */
	public static function saveExportAirFreightBilling($jobLine)
	{
		$job = EdiJob::model()->findByPk($jobLine->job_id);

		// $billing = BillingLine::model()->find('link_id = :pid AND type = :type', [':pid' => $jobLine->id, ':type' => BillingLine::BILLING_TYPE_AIR_SEA]);
		$billing = BillingLine::model()->find('link_id = :pid AND type = :type', [':pid' => $jobLine->id, ':type' => $job->dpmt / 10]);
		if (empty($billing)) {
			$billing = new BillingLine();
			$billing->billing_cref = $jobLine->invoice_ref;
			$billing->status = 1; // initial pending status
			$billing->date = $job->created;
			$billing->due = $job->due;
			$billing->weight = 0;
			$billing->charge_weight = 0;
			$billing->link_id = $jobLine->id;
			$billing->currency = !empty($job) ? $job->currency : 1;
		}

		// normally billing reference will be awb no , if not set we set job ID as reference
		$billing->billing_cref = isset($jobLine->invoice_ref) ? $jobLine->invoice_ref : '';
		if (empty($billing->billing_ref)) {
			$billing->billing_ref = !empty($job) ? $job->no : '';
		}

		$billing->dpt_id = !empty($job) ? $job->dpt_id : 0;
		$billing->dpmt = $job->dpmt;
		// $billing->type = BillingLine::BILLING_TYPE_AIR_SEA;
		$billing->type = $job->dpmt / 10;
		$billing->org_id = $jobLine->supplier_id;
		$billing->desc = $jobLine->desc;
		$billing->accrual_amount = $jobLine->cost_amount;
		$billing->actual_amount = 0;
		$billing->gst = $jobLine->cost_gst;

		// get related supplier cost gl code
		$billing->charge_code = EdiJob::getSupplierCostCode($jobLine->ccode, ($job->dpmt == Job::DPMT_3PL));

		$billing->op_id = isset(Yii::app()->user) ? Yii::app()->user->id : 0;
		$billing->sync_xero = 0;
		$billing->beforeSave();
		$billing->save();
		$errors = $billing->getErrors();

		// push to P&L ledger as well
		if (empty($errors)) {
			$chargeCodeModel = Chargecode::model()->find('status = 1 AND code = :code', [':code' => $billing->charge_code]);
			$chargeCodeId = 0;
			if (!empty($chargeCodeModel)) {
				$chargeCodeId = $chargeCodeModel->id;
			}

			// check accrual cost
			if ($billing->accrual_amount > 0) {
				//if ( $chargeCodeId <= 0 ) {
				// we should notify admin charge code not found
				// ...
				//} else
				{
					$d = [
						'gl' => $chargeCodeId,
						'fid' => $billing->id,
						'model' => 'BillingLine',
						'dpt_id' => $billing->dpt_id,
						'lid' => 0,
						'org_id' => $billing->org_id,
						'dpmt' => $billing->dpmt,
						'grp1' => '',
						'date' => $billing->created,
						'amt' => $billing->accrual_amount,
						'gst' => 0,
					];

					/*$pl = PlLedger::add($d, true, ['org_id', 'dpmt', 'date', 'lid', 'grp1', 'grp2']);
				$errs = $pl->getErrors();
				if ( !empty($errs) ) {
				// should notify admin
				// ....
				}*/
				}
			}
		}

		return $errors;
	}

	/**
	 * save export port cost into our billing table
	 * @param $data
	 */
	public static function savePortBilling($data)
	{

		$billing = BillingLine::model()->find('org_id = :oid AND type = 2 AND charge_code = :ccode AND billing_cref = :invref',
			[':oid' => $data['org_id'], ':ccode' => $data['chargecode'], 'invref' => $data['invoice_ref']]);

		if (empty($billing)) {
			$billing = new BillingLine();
			$billing->billing_cref = $data['invoice_ref'];
			$billing->status = 1; // initial pending status
		}
		$billing->currency = 1;
		$billing->weight = 0;
		$billing->charge_weight = $data['chargeable_weight'];
		$billing->link_id = 0;

		// normally billing reference will be awb no , if not set we set job ID as reference
		$billing->billing_ref = $data['consol'];
		$billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
		$billing->type = BillingLine::BILLING_TYPE_EXPORT; // for import type
		$billing->dpmt = Invoice::DPMT_EXPORT;

		$billing->org_id = $data['org_id'];

		$billing->accrual_amount = $data['cost'];
		$billing->actual_amount = 0;
		$billing->gst = 0;

		$billing->charge_code = $data['chargecode'];

		$billing->op_id = Yii::app()->user->id;
		$billing->sync_xero = 0;
		$billing->beforeSave();
		$billing->save();
		$errors = $billing->getErrors();

		return $errors;
	}

	/**
	 * @param int $type
	 * @return CDbDataReader|mixed|string
	 */
	public static function totAccoutingLinesByType($dpmt = Invoice::DPMT_AIRSEA)
	{
		return BillingLine::model()->count('status = 2 AND dpmt = :dpmt', [':dpmt' => $dpmt]);
	}

	public function getTaxType()
	{
		return Yii::t(strtolower(__CLASS__), Invoice::$TaxType[$this->tax]);
	}

	public function getCostTaxType()
	{
		return Yii::t(strtolower(__CLASS__), Invoice::$InvoiceCostTaxRate[$this->tax]);
	}

	public function getItemcodeDesc()
	{
		if (empty($this->item_code)) {
			return '';
		}

		$desc = ucwords(str_replace('_', ' ', $this->item_code));
		$ccodeKeys = EdiJob::getChargeItemTypes();
		if (isset($ccodeKeys[$this->item_code])) {
			$desc = $ccodeKeys[$this->item_code];
		} elseif (isset(ExcoConsol::$chargeItems[$this->item_code])) {
			$desc = ExcoConsol::$chargeItems[$this->item_code];
		}

		return $desc;
	}

	public static function getItemcodeDesc2($itemcode)
	{
		if (empty($itemcode)) {
			return '';
		}

		$desc = ucwords(str_replace('_', ' ', $itemcode));
		$ccodeKeys = EdiJob::getChargeItemTypes();
		if (isset($ccodeKeys[$itemcode])) {
			$desc = $ccodeKeys[$itemcode];
		} elseif (isset(ExcoConsol::$chargeItems[$itemcode])) {
			$desc = ExcoConsol::$chargeItems[$itemcode];
		}

		return $desc;
	}

	public function getCCodeDesc()
	{
		if (!isset($this->charge_code)) {
			return '';
		}

		$desc = '';
		$ccodeModel = Chargecode::model()->find('status = 1 AND code = :ccode', [':ccode' => $this->charge_code]);
		if (!empty($ccodeModel)) {
			$desc = $ccodeModel->name;
		}

		$desc .= '(' . $this->charge_code . ')';
		return $desc;
	}

	public function getToOrgName()
	{
		if (empty($this->to_id)) {
			return 'All';
		} else {
			return empty($this->toOrg->name) ? '' : $this->toOrg->name;
		}
	}

	public function getOT()
	{
		if (empty($this->mdata['ot_inv'])) {
			return '';
		} else {
			$invoice = Invoice::model()->findByPk($this->mdata['ot_inv']);
			if ($invoice->status == 10) return '';
			return '<a href="' . Yii::app()->createUrl('invoice/print', ['id' => $invoice->id]) . '" target="_blank">' . $invoice->no . '</a>';
		}
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'billing_line';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id,type', 'required'),
			array('op_id, link_id, status,to_id,currency, sync_xero', 'numerical', 'integerOnly' => true),
			array('org_id, billing_id,type, dpt_id, dpmt,qty,actual_amount,price, accrual_amount, weight, charge_weight', 'length', 'max' => 10),
			array('gst, charge_code', 'length', 'max' => 20),
			array('billing_cref, billing_ref,item_code', 'length', 'max' => 45),
			array('awb', 'length', 'max' => 50),
			array('no', 'length', 'max' => 15),
			array('desc', 'length', 'max' => 450),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, billing_id,org_id, op_id, link_id,to_id, created, date, due, client, transaction_date, type, gst, status, billing_cref,awb, billing_ref, desc, qty, item_code, price,dpt_id,dpmt, currency, no, actual_amount, accrual_amount, charge_code, weight,revenue, charge_weight, sync_xero, meta, gst_amount', 'safe', 'on' => 'search'),
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
			'cust' => array(self::BELONGS_TO, 'Org', 'org_id'),
			'user' => array(self::BELONGS_TO, 'User', 'op_id'),
			//  'jobline' => array(self::BELONGS_TO, 'JobLine', 'link_id'),
			'toOrg' => array(self::BELONGS_TO, 'Org', 'to_id'),
			'billing' => array(self::BELONGS_TO, 'Billing', 'billing_id'),
			'invoice' => array(self::HAS_ONE, 'BillingInvoice', ['billing_cref' => 'billing_cref', 'org_id' => 'org_id']),
			'consol' => array(self::HAS_ONE, 'Consol', ['no' => 'billing_ref']),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'org_id' => 'Org',
			'op_id' => 'Operator',
			'client' => 'Supplier',
			'revenue' => 'Revenue',
			'link_id' => 'Link',
			'created' => 'Created',
			'date' => 'Date',
			'due' => 'Due',
			'transaction_date' => 'Transaction Date',
			'type' => 'Type',
			'gst' => 'Gst',
			'awb' => 'AWB',
			'status' => 'Status',
			'billing_cref' => 'Invoice No.',
			'billing_ref' => 'Billing Ref',
			'dpt_id' => 'Dpt',
			'currency' => 'Currency',
			'no' => 'No',
			'desc' => 'Desc',
			'qty' => 'Qty',
			'item_code' => 'Item Code',
			'price' => 'Price',
			'actual_amount' => 'Actual Amount',
			'accrual_amount' => 'Accrual Amount',
			'charge_code' => 'Charge Code',
			'weight' => 'Weight',
			'charge_weight' => 'Charge Weight',
			'sync_xero' => 'Sync Xero',
			'meta' => 'Meta',
		);
	}

	public function getGstListTagValue()
	{

	}

	public function getUpdateUrl()
	{
		if ($this->type == 3) {
			return 'ediJob/updateByNo';
		} else if ($this->type == 2) {
			return 'excoConsol/updateByNo';
		} else if ($this->type == 1) {
			return 'imcoConsol/updateByNo';
		}
	}

	public function getAirportAccural($org_id, $weight, $uld = false, $export = false)
	{
		if ($org_id == 955) { // Qantas
			// $amount = 54 + 0.52 * floatval($weight);
			// from 2020-04-01
			$amount = 58 + max(0.57 * floatval($weight), 52);
		} else if ($org_id == 939) { // Menzies
			// if ($uld) {
			// 	$amount = 54 + 0.135 * floatval($weight);
			// } else {
			// 	// miniumu 100kg
			// 	$amount = 54 + 0.525 * max(100, floatval($weight));
			// }
			// from 2020-04-01
			if ($uld) {
				if (!$export) {
					$amount = 56 + max(0.14 * floatval($weight), 54.5);
				} else {
					$amount = 56 + max(0.15 * floatval($weight), 54.5);
				}
			} else {
				$amount = 56 + max(0.545 * floatval($weight), 54.5);
			}
		} else if ($org_id == 977) { // Dnata
			$amount = 53.6 + 0.525 * floatval($weight);
		}

		return number_format($amount, 2, '.', '');
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
	public function search($pgn = true, $ps = 100, $odr = 't.id DESC', $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('t.org_id', $this->org_id);
		$criteria->compare('t.op_id', $this->op_id);
		$criteria->compare('billing_id', $this->billing_id);
		$criteria->compare('link_id', $this->link_id);
		$criteria->compare('to_id', $this->to_id);
		$criteria->compare('t.created', $this->created, true);
		$criteria->compare('due', $this->due, true);
		$criteria->compare('transaction_date', $this->transaction_date, true);
		$criteria->compare('t.type', $this->type);
		$criteria->compare('t.dpmt', $this->dpmt);
		$criteria->compare('gst', $this->gst, true);
		$criteria->compare('billing_cref', $this->billing_cref, true);
		$criteria->compare('billing_ref', $this->billing_ref, true);
		$criteria->compare('awb', $this->awb, true);
		$criteria->compare('dpt_id', $this->dpt_id, true);
		$criteria->compare('currency', $this->currency);
		$criteria->compare('no', $this->no, true);
		$criteria->compare('actual_amount', $this->actual_amount, true);
		$criteria->compare('accrual_amount', $this->accrual_amount, true);
		$criteria->compare('charge_code', $this->charge_code, true);
		$criteria->compare('weight', $this->weight, true);
		$criteria->compare('charge_weight', $this->charge_weight, true);
		$criteria->compare('sync_xero', $this->sync_xero);
		$criteria->compare('t.meta', $this->meta, true);
		if (empty($this->status)) {
			$criteria->addCondition('t.status != 11');
		} else {
			$criteria->compare('t.status', $this->status);
		}
		$criteria->compare('t.desc', $this->desc, true);

		$hasDateSpan = false;
		if (!empty($this->date_from) && !empty($this->date_to)) {
			$hasDateSpan = true;
			$criteria->addBetweenCondition('t.date', $this->date_from, $this->date_to);
		} else if (!empty($this->date_from) && empty($this->date_to)) {
			$hasDateSpan = true;
			$criteria->addBetweenCondition('t.date', $this->date_from, '2100-12-30');
		} else if (empty($this->date_from) && !empty($this->date_to)) {
			$hasDateSpan = true;
			$criteria->addBetweenCondition('t.date', '1970-01-01', $this->date_to);
		}
		if (!$hasDateSpan) {
			$criteria->compare('t.date', $this->date, true);
		}

		$with = array();
		if (!empty($this->client)) {
			$with[] = 'cust';
			$criteria->compare('cust.name', $this->client, true);
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

	public function getRegion()
	{
		$regions =  SystemSetting::getInvoiceRegions();
		if(empty($this->dpt_id))
		{
			$region = $regions[Org::TLA_DEPARTMENT_SYDNEY];
		}else
		{
			$region = $regions[$this->dpt_id];
		}

		return $region;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BillingLine the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}
