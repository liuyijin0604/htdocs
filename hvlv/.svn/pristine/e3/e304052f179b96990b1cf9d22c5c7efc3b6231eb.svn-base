<?php

/**
 * This is the model class for table "reconciliation".
 *
 * The followings are the available columns in table 'reconciliation':
 * @property string $id
 * @property integer $client_type
 * @property string $invoice_no
 * @property string $invoice_date
 * @property string $invoice_total
 * @property string $my_total
 */
class Reconciliation extends MetaModel
{
	public $deviation = 0;
	public $percent='', $connote,$declare_connote;
	public $no_consol;
	public $mdata, $notype;
	public $ot_inv;
	public static $my_type = 0;
	const AUPOST_TYPE = 1;
	const FASTWAY_TYPE = 0;
	const STARTRACK_TYPE = 2;
	const HUNTER_TYPE = 5;
	const ECOF_TYPE = 6;
	const TNT_CLIENT = 4;
	const D2Z_DECLARE_TYPE = 200;
	const D2Z_RTS_TYPE = 400;
	const DECLARE_TYPE = 200;
	const DECLARE_TYPE_MAX = 399;
	const RTS_TYPE = 400;
	const RTS_TYPE_MAX = 599;
	const GLOBAVEND_TYPE = 7;
	const AUPOST_RTS = 10;
	const AUPOST_INVOICE = 11;
	const UBI_TYPE = 12;
	const UBI_RTS = 13;


	const D2Z_CLIENT=3;

	public static $types = array(
		self::FASTWAY_TYPE => 'Fastway',
		self::AUPOST_TYPE => 'Aupost Manifest',
		self::AUPOST_RTS => 'Aupost RTS',
		self::AUPOST_INVOICE => 'AuPost',
		self::STARTRACK_TYPE => 'Startrack',
		3 => 'D2Z',
		self::TNT_CLIENT => 'TNT',
		self::HUNTER_TYPE => 'Hunter',
		self::ECOF_TYPE => 'ECOF',
		self::D2Z_DECLARE_TYPE => 'D2Z-declare',
		self::D2Z_RTS_TYPE => 'D2Z-RTS',
		self::GLOBAVEND_TYPE => 'Globavend',
		self::UBI_TYPE => 'UBI',
		self::UBI_RTS => 'UBI RTS',
	);

	public static $checks = array(
		self::FASTWAY_TYPE => 'Fastway',
		self::AUPOST_INVOICE => 'AuPost',
		self::STARTRACK_TYPE => 'Startrack',
		self::TNT_CLIENT => 'TNT',
	);

	public static $eParcelCourier = [
		self::FASTWAY_TYPE => 'Fastway',
		self::AUPOST_TYPE => 'AuPost',
		3 => 'D2Z',
		self::ECOF_TYPE => 'ECOF',
		self::D2Z_DECLARE_TYPE => 'D2Z-declare'
	];

	public static $cbmCourier = [
		 self::STARTRACK_TYPE => 'Startrack',
		 self::TNT_CLIENT => 'TNT',
		 self::HUNTER_TYPE => 'Hunter',
	];

	public static $declareCourier = [
		 self::D2Z_DECLARE_TYPE => 'D2Z-declare'
	];

	public static $rtsCourier = [
		 1 => 'D2Z-RTS'
	];


	public static $courierIDToTypes = array(
		101 => 1,
		115 => 0,
		858 => 2,
		1426 => 3,
		976 => 4,
		Org::ORGID_COURIER_HUNTER => 5
	);

	public static $ot_inv_states = [
		1 => 'Completely created',
		2 => 'Partially created',
		3 => 'No need',
	];

	public function __construct($scenario='insert'){
		parent::__construct($scenario);
		if(static::$my_type > 0) $this->client_type = static::$my_type;
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'reconciliation';
	}

	public function getType(){
		return Yii::t(strtolower(__CLASS__), self::$types[$this->client_type]);
	}


	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('invoice_no, invoice_date', 'required'),
			array('client_type', 'numerical', 'integerOnly'=>true),
			array('invoice_no', 'length', 'max'=>45),
			array('invoice_total, my_total', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, client_type, invoice_no, invoice_date, invoice_total, my_total,deviation,percent, connote, manifest_no, no_consol, meta,declare_connote, ot_inv', 'safe', 'on'=>'search'),
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
			'lines' => array(self::HAS_MANY, 'ReconciliationLine', 'parent_id'),
			'declareLines' => array(self::HAS_MANY, 'ReconciliationLineDeclare', 'parent_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'client_type' => 'From',
			'invoice_no' => 'Invoice/Consol No',
			'invoice_date' => 'Invoice/Consol Date',
			'invoice_total' => 'Invoice Total',
			'my_total' => 'My Total',
			'deviation' => 'Deviation',
			'manifest_no' => 'Manifest No',
		);
	}

	public function beforeSave()
	{
		$this->meta = empty($this->mdata) ? '' : json_encode($this->mdata);
		return true;
	}

	public function afterFind(){
		// if ($this->client_type == self::AUPOST_TYPE) $this->checkAupostManifest();
		// if ($this->client_type == self::AUPOST_RTS) $this->checkAupostRTS();
		// if ($this->client_type == self::AUPOST_INVOICE) $this->checkAupostInvoice();
		$this->deviation = round($this->getMyTotal() - $this->getInvoiceTotal(),2);
		$this->percent=$this->getMyTotal()>0?(round($this->getInvoiceTotal()/$this->getMyTotal(),2)*100)."%":'0%';
		if (!empty($this->meta)) {
			$this->mdata = json_decode($this->meta, true);
		}
		if ($this->invoice_total == 0) {
			$this->invoice_total = $this->getInvoiceTotal();
			$this->update('invoice_total');
		}
		if ($this->my_total == 0) {
			$this->my_total = $this->getMyTotal();
			$this->update('my_total');
		}
		parent::afterFind();
	}

	private function checkAupostManifest()
	{
		foreach ($this->lines as $line) {
			if ($line->consol_id != 0) continue;

			$shipment = ImParcel::model()->find('ref = :ref', [':ref' => $line->shipment_no]);
			if (!empty($shipment) && $shipment->consol_id != 0) {
				$line->consol_id = $shipment->consol_id;
				$line->update('consol_id');
				$line->refresh();

				$shipment->mdata['actual_delivery_cost'] = 0;
				$shipment->mdata['import_billing_id'] = 0;
				$shipment->nolog = true;
				$shipment->update('meta');
				$shipment->refresh();

				$billing = new BillingLine();
				$billing->status = 1; // initial pending status
				$billing->link_id = 0;
				$billing->org_id = Org::ORGID_COURIER_AUPOST;
				$billing->charge_code = Consol::AU_LOCAL_DELIVERY_COST_GL_CODE;
				$billing->billing_cref = $shipment->consol->no;
				$billing->currency = 1; // AUD default
				$billing->weight = 0;
				$billing->charge_weight = 0;
				$billing->billing_ref = $shipment->consol->no;
				$billing->awb = $shipment->consol->awb;
				$billing->dpt_id = empty($shipment->consol->dpt_id) ? Org::PCAE_DEPARTMENT_SYDNEY : $shipment->consol->dpt_id;
				$billing->date = $this->invoice_date;
				$billing->created = $this->invoice_date;
				$billing->transaction_date = $this->invoice_date;
				$billing->due = $this->invoice_date;
				$billing->type = BillingLine::BILLING_TYPE_IMPORT; // for import type
				$billing->dpmt = Invoice::DPMT_IMPORT;
				$billing->actual_amount = number_format(round($line->value, 2), 2, '.', '');
				$billing->gst = 'INPUT';
				$billing->gst_amount = $billing->getGSTValue();
				$billing->desc .= 'total shipment: ' . 1;
				$billing->accrual_amount = 0;
				if ($billing->save()) {
					$theCost = 0;
					$shipment->mdata['import_billing_id'] = $billing->id;
					$shipment->updateMeta();
					foreach ($shipment->trans as $ts) {
						if ($ts->type == 80) {
							$theCost += $ts->cost;
						}
					}
					$billing->accrual_amount = number_format($theCost, 2, '.', '');
					$billing->save();
				}

				$this->manifest_no = $this->invoice_no;
				$this->invoice_no = $shipment->consol->no;
				$this->mdata['no_consol'] = 0;
				$this->update('manifest_no', 'invoice_no', 'meta');
			}
		}

		foreach ($this->lines as $line) {
			if ($line->my_value != 0) continue;

			if (preg_match("/(AMQ|333UF)\d{7}/", $line->shipment_no)) {
				$org_rate_id = ImportChargeCode::SYDNEY_AUPOST_ID;
			} elseif (preg_match("/33EVH\d{7}/i", $line->shipment_no)) {
				$org_rate_id = ImportChargeCode::MELBOUNE_AUPOST_ID;
			} elseif (preg_match("/33EVJ\d{7}/i", $line->shipment_no)) {
				$org_rate_id = ImportChargeCode::BRISBANE_AUPOST_ID;
			} elseif (preg_match("/33A8Y\d{7}/i", $line->shipment_no)) {
				$org_rate_id = ImportChargeCode::D2Z_COUNTRY_ID;
			}
			$shipment = ImParcel::model()->find('ref = :ref', [':ref' => $line->shipment_no]);
			$pkg = !empty($shipment) ? $shipment->pkg : 1;
			$ourRated = Shipment::getCourierCostByShipment($line->shipment_no, $org_rate_id, $line->weight);
			$line->my_value = $ourRated['price'] / $pkg;
			$line->update('my_value');
		}

		foreach ($this->lines as $line) {
			if ($line->my_charge != 0) continue;

			$shipment = ImParcel::model()->find('ref = :ref', [':ref' => $line->shipment_no]);
			if (!empty($shipment)) {
				$ourcharge = $shipment->getCouiercost();
				$line->my_charge = $ourcharge;
				$line->update('my_charge');
			}
		}
	}

	private function checkAupostRTS()
	{
		foreach ($this->lines as $line) {
			if ($line->my_charge != 0) continue;

			$shipment = ImParcel::model()->find('ref = :ref', [':ref' => $line->shipment_no]);
			if (!empty($shipment)) {
				$ils = InvLine::model()->with('invoice')->findAll('invoice.type = 36 AND t.fid = :fid', [':fid' => $shipment->id]);
				$total = 0;
				foreach ($ils as $il) {
					$total += $il->amount - $il->gst;
				}
				if (!empty($total)) {
					$line->my_charge = number_format($total / $shipment->pkg, 2, '.', '');
					$line->update('my_charge');
				}
			}
		}
	}

	private function checkAupostInvoice()
	{
		foreach ($this->lines as $line) {
			if ($line->postcode == 'eParcel') {
				if ($line->my_value == 0 || $line->my_charge == 0 || $line->value - $line->my_value > 10) {
					$recModels = Reconciliation::model()->findAll('client_type = :type AND manifest_no = :manifest_no', [':type' => Reconciliation::AUPOST_TYPE, ':manifest_no' => $line->shipment_no]);
					$my_value = 0;
					$my_charge = 0;
					foreach ($recModels as $recModel) {
						foreach ($recModel->lines as $recLine) {
							$my_value += $recLine->my_value;
							$my_charge += $recLine->my_charge;
						}
					}
					$line->my_value = number_format($my_value, 2, '.', '');
					$line->my_charge = number_format($my_charge, 2, '.', '');
					$line->update('my_value', 'my_charge');
				}
			} else if ($line->postcode == 'ELMS') {
				if ($line->my_value == 0 || $line->my_charge == 0) {
					$consol1 = ElmsConsol::model()->find('awb = :awb', [':awb' => $line->shipment_no]);
					$consol2 = ImcoConsol::model()->find('JSON_VALUE(meta, "$.elms") = :elms OR JSON_QUERY(meta, "$.elms") LIKE :elms2', [':elms' => $line->shipment_no, ':elms2' => '%' . $line->shipment_no . '%']);
					$consol = $consol1 ? $consol1 : $consol2;
					if (!empty($consol)) {
						$bl = BillingLine::model()->find('org_id = 101 AND billing_ref = :ref AND `desc` LIKE "%LETTER"', [':ref' => $consol->no]);
						$accrual_value = $bl->accrual_amount;
						$line->consol_id = $consol->id;
					} else {
						$accrual_value = 0;
					}
					$line->my_value = $accrual_value;
					$line->update('my_value', 'consol_id');
				}
			} else if ($line->postcode == 'Return to sender') {
				if ($line->my_value == 0 && $line->getConsolNos() != '') {
					$line->my_value = $line->value;
					$line->update('my_value');
				}

				if ($line->my_charge == 0) {
					$recModels = Reconciliation::model()->findAll('client_type = :type AND manifest_no = :manifest_no', [':type' => Reconciliation::AUPOST_RTS, ':manifest_no' => $line->shipment_no]);
					$my_charge = 0;
					foreach ($recModels as $recModel) {
						foreach ($recModel->lines as $recLine) {
							$my_charge += $recLine->my_charge;
						}
					}
					$line->my_charge = $my_charge;
					$line->update('my_charge');
				}
			} else if ($line->postcode == 'UNKNOWN') {
				if ($line->my_value == 0 || $line->my_charge == 0) {
					$consol = ImcoConsol::model()->find('JSON_VALUE(meta, "$.elms") = :elms OR JSON_QUERY(meta, "$.elms") LIKE :elms2', [':elms' => $line->shipment_no, ':elms2' => '%' . $line->shipment_no . '%']);
					if (!empty($consol)) {
						$bl = BillingLine::model()->find('org_id = 101 AND billing_ref = :ref AND `desc` LIKE "%LETTER"', [':ref' => $consol->no]);
						if (!empty($bl)) {
							$line->my_value = $bl->accrual_amount;
						}
						$line->postcode = 'ELMS';
						$line->consol_id = $consol->id;
						$line->update('postcode', 'consol_id');
					}
				}
			}
		}
	}

	public function toBilling()
	{
		$trans = Yii::app()->db->beginTransaction();
		try {
			$billing = Billing::model()->find('billing_cref = :billing_cref AND status != 11', [':billing_cref' => $this->invoice_no]);
			if (empty($billing)) {
				$billing = new Billing;
				$billing->org_id = Org::ORGID_COURIER_AUPOST;
				$billing->created = date('Y-m-d');
				$billing->date = $this->invoice_date;
				$billing->due = $this->invoice_date;
				$billing->transaction_date = $this->invoice_date;
				$billing->type = BillingLine::BILLING_TYPE_IMPORT;
				$billing->dpmt = Invoice::DPMT_IMPORT;
				$billing->status = 2;
				$billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
				$billing->currency = 1;
				$billing->sync_xero = 0;
				$billing->billing_cref = $this->invoice_no;
				$billing->save();
			}

			if ($billing->total != $this->invoice_total) {
				foreach ($this->lines as $line) {
					if ($line->postcode == 'eParcel') {
						$sql = "SELECT id, actual_amount FROM billing_line WHERE id IN (SELECT IF(JSON_VALUE(s.meta,'$.import_billing_id_aupost'), JSON_VALUE(s.meta,'$.import_billing_id_aupost'), JSON_VALUE(s.meta,'$.import_billing_id')) FROM shipment s WHERE ref IN (SELECT shipment_no FROM `reconciliation_line` WHERE parent_id IN (SELECT id FROM reconciliation WHERE client_type = :type AND manifest_no = :no)))";
						$rs = Yii::app()->db->createCommand($sql)->bindValues([':type' => Reconciliation::AUPOST_TYPE, ':no' => $line->shipment_no])->queryAll();
						foreach ($rs as $r) {
							$bl = BillingLine::model()->findByPk($r['id']);
							if (empty($bl) || $bl->billing_cref == $this->invoice_no) continue;

							$bl->billing_cref = $this->invoice_no;
							$bl->billing_id = $billing->id;
							$bl->update('billing_cref', 'billing_id');
						}
						// $total = 0;
						// foreach ($rs as $r) {
						// 	$total += floatval($r['actual_amount']);
						// }
						// foreach ($rs as $r) {
						// 	$bl = BillingLine::model()->findByPk($r['id']);
						// 	if (empty($bl) || $bl->billing_cref == $this->invoice_no) continue;

						// 	$bl->billing_cref = $this->invoice_no;
						// 	$bl->billing_id = $billing->id;
						// 	$bl->accrual_amount = $bl->actual_amount;
						// 	$bl->actual_amount = number_format($line->value / $total * $bl->accrual_amount, 2, '.', '');
						// 	if (abs($bl->actual_amount - $line->value) < 0.1) $bl->actual_amount = $line->value;
						// 	$bl->gst_amount = $bl->getGSTValue();
						// 	$bl->update('billing_cref', 'billing_id', 'accrual_amount', 'actual_amount', 'gst_amount');
						// 	$line->value -= $bl->actual_amount;
						// }
					} else if ($line->postcode == 'Return to sender') {
						$allocs = BillingLine::model()->findAll('org_id = 101 AND JSON_VALUE(meta, "$.rts_manifest_no") = :no AND JSON_VALUE(meta, "$.alloc_rl") = :rl_id', [':no' => $line->shipment_no, ':rl_id' => $line->id]);
						foreach ($allocs as $alloc) {
							$line->value -= $alloc->actual_amount;
						}

						if ($line->value > 0) {
							$unallocs = BillingLine::model()->findAll('org_id = 101 AND JSON_VALUE(meta, "$.rts_manifest_no") = :no AND JSON_VALUE(meta, "$.alloc_rl") IS NULL', [':no' => $line->shipment_no]);
							foreach ($unallocs as $unalloc) {
								$count_acr = floor($unalloc->accrual_amount / 9);
								$count_act = floor($line->value / 9);
								if ($count_acr >= $count_act) {
									$new = BillingLine::copy($unalloc);
									$new->accrual_amount = number_format($unalloc->accrual_amount - $line->value, 2, '.', '');
									$new->save();

									$unalloc->billing_cref = $this->invoice_no;
									$unalloc->billing_id = $billing->id;
									$unalloc->accrual_amount = $line->value;
									$unalloc->actual_amount = $line->value;
									$unalloc->gst_amount = $unalloc->getGSTValue();
									$unalloc->mdata['alloc_rl'] = $line->id;
									$unalloc->update('billing_cref', 'billing_id', 'accrual_amount', 'actual_amount', 'gst_amount', 'meta');
									break;
								} else {
									$unalloc->billing_cref = $this->invoice_no;
									$unalloc->billing_id = $billing->id;
									$unalloc->actual_amount = number_format($unalloc->accrual_amount, 2, '.', '');
									$unalloc->gst_amount = $unalloc->getGSTValue();
									$unalloc->mdata['alloc_rl'] = $line->id;
									$unalloc->update('billing_cref', 'billing_id', 'actual_amount', 'gst_amount', 'meta');

									$line->value -= $unalloc->accrual_amount;
								}
							}
						}
					} else if ($line->postcode == 'ELMS') {
						$consol1 = ElmsConsol::model()->find('awb = :awb', [':awb' => $line->shipment_no]);
						$consol2 = ImcoConsol::model()->find('JSON_VALUE(meta, "$.elms") = :awb OR JSON_QUERY(meta, "$.elms") LIKE :awb2', [':awb' => $line->shipment_no, ':awb2' => '%' . $line->shipment_no . '%']);
						$consol = $consol1 ? $consol1 : $consol2;
						if (!empty($consol)) {
							$bl = BillingLine::model()->find('org_id = :oid AND billing_ref = :ref AND JSON_VALUE(meta, "$.rl") = :lid', [':ref' => $consol->no, ':lid' => $line->id, ':oid' => Org::ORGID_COURIER_AUPOST]);
							if (!empty($bl)) continue;

							$bl = BillingLine::model()->find('(org_id = :oid1 OR (org_id = :oid2 AND `desc` LIKE "%LETTER")) AND billing_ref = :ref AND JSON_VALUE(meta, "$.rl") IS NULL', [':ref' => $consol->no, ':oid1' => Org::ORGID_COURIER_AUSLETTER, ':oid2' => Org::ORGID_COURIER_AUPOST]);
							if (empty($bl)) {
								$bl = new BillingLine;
								$bl->org_id = Org::ORGID_COURIER_AUPOST;
								$bl->created = date('Y-m-d');
								$bl->date = $this->invoice_date;
								$bl->due = $this->invoice_date;
								$bl->transaction_date = $this->invoice_date;
								$bl->type = BillingLine::BILLING_TYPE_IMPORT;
								$bl->dpmt = Invoice::DPMT_IMPORT;
								$bl->status = 2;
								$bl->billing_ref = $consol->no;
								$bl->awb = $consol->awb;
								$bl->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
								$bl->currency = 1;
								$bl->charge_code = Consol::AU_LOCAL_DELIVERY_COST_GL_CODE;
								$bl->desc = '';
								$bl->item_code = '';
								$bl->gst_amount = $bl->getGSTValue();
							}
							$bl->org_id = 101;
							$bl->billing_cref = $this->invoice_no;
							$bl->billing_id = $billing->id;
							$bl->actual_amount = $line->value;
							$bl->gst = 'INPUT';
							$bl->gst_amount = $bl->getGSTValue();
							$bl->mdata['rl'] = $line->id;
							$bl->desc = trim(str_replace('LETTER', '', $bl->desc)) . ' LETTER';
							$bl->save();
						}
					}
				}
			}
			$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}
	}
	
	public function linedLines()
	{
		if(in_array($this->getType(),Reconciliation::$declareCourier)||in_array($this->getType(),Reconciliation::$rtsCourier))
		{
			return "reconciliation_line_declare";
		}else
		{
			return "reconciliation_line";
		}
	}    
	public function getInvoiceTotal(){
		if($this->client_type==self::D2Z_CLIENT||$this->client_type==self::D2Z_DECLARE_TYPE)
		{
			$sql="SELECT ((SUM(value)-SUM(json_value(meta,'$.fuel_charge'))/1.1)) as invoice_total FROM  `{$this->linedLines()}` where `parent_id`=:pid group by parent_id";
		} else if ($this->client_type == self::UBI_TYPE) {
			$sql="SELECT SUM(value) - SUM(JSON_VALUE(meta, '$.fuel_charge')) as invoice_total FROM  `{$this->linedLines()}` where `parent_id`=:pid group by parent_id";
		} else {
			$sql="SELECT SUM(value) as invoice_total FROM  `{$this->linedLines()}` where `parent_id`=:pid AND shipment_no != 'Can\'t upload line difference' group by parent_id";
		}
		$r=Yii::app()->db->createCommand($sql)->bindValues([':pid'=> $this->id])->queryAll();
		return round(empty($r[0]['invoice_total'])?0:$r[0]['invoice_total'],2);
	}

	public function getFuelTotal()
	{
		if ($this->client_type != self::UBI_TYPE) return '';
		$sql = "SELECT SUM(JSON_VALUE(meta, '$.fuel_charge')) as fuel_total FROM  `{$this->linedLines()}` where `parent_id`=:pid group by parent_id";
		$r=Yii::app()->db->createCommand($sql)->bindValues([':pid'=> $this->id])->queryAll();
		return ' (Fuel:' . (round(empty($r[0]['fuel_total'])?0:$r[0]['fuel_total'],2)) . ')';
	}
	
	  public function  getMyTotal(){
		$sql="SELECT SUM(my_value) as my_value FROM  `{$this->linedLines()}` where `parent_id`=:pid group by parent_id";
		$r=Yii::app()->db->createCommand($sql)->bindValues([':pid'=> $this->id])->queryAll();
		$amount = round(empty($r[0]['my_value'])?0:$r[0]['my_value'],2);

		if ($this->client_type != Reconciliation::AUPOST_TYPE) {
			return $amount;
		} else {
			$AupostFuelCharge = SystemSetting::getAupostFuelChargeSetting();
			$invoiceMonth = date("Y-m",strtotime($this->invoice_date));
			$fuelCharge = (1+@$AupostFuelCharge[$invoiceMonth]*0.01);
			return round(($amount*$fuelCharge),2);
		}
	}

	public function getAcceptTotal()
	{
		if ($this->client_type == Reconciliation::AUPOST_TYPE) {
			$AupostFuelCharge = SystemSetting::getAupostFuelChargeSetting();
			$invoiceMonth = date("Y-m",strtotime($this->invoice_date));
			$fuelCharge = (1 + @$AupostFuelCharge[$invoiceMonth] * 0.01);
			$sql = "SELECT SUM( IF(my_value * " . $fuelCharge . " < value AND JSON_VALUE(meta, '$.confirmed') IS NULL, my_value * " . $fuelCharge . ", value) ) as my_value FROM  `{$this->linedLines()}` where `parent_id` = :pid AND shipment_no != 'Can\'t upload line difference' AND consol_id != 0 group by parent_id";
			$r = Yii::app()->db->createCommand($sql)->bindValues([':pid' => $this->id])->queryAll();
			$amount = round(empty($r[0]['my_value']) ? 0 : $r[0]['my_value'], 2);
			return round($amount, 2);
		} else if ($this->client_type == Reconciliation::AUPOST_INVOICE) {
			$sql = "SELECT SUM( IF(my_value < value AND JSON_VALUE(meta, '$.confirmed') IS NULL, my_value, value) ) as my_value FROM  `{$this->linedLines()}` where `parent_id` = :pid AND shipment_no != 'Can\'t upload line difference' group by parent_id";
			$r = Yii::app()->db->createCommand($sql)->bindValues([':pid' => $this->id])->queryAll();
			$amount = round(empty($r[0]['my_value']) ? 0 : $r[0]['my_value'], 2);
			return $amount;
		} else if ($this->client_type == self::STARTRACK_TYPE || $this->client_type == self::TNT_CLIENT) {
			$sql = "SELECT SUM( IF((my_value * 1.05 < value OR my_value + 10 < value OR consol_id = 0 OR shipment_id = 0) AND JSON_VALUE(meta, '$.confirmed') IS NULL, my_value, value) ) as my_value FROM  `{$this->linedLines()}` where `parent_id` = :pid AND shipment_no != 'Can\'t upload line difference' group by parent_id";
			$r = Yii::app()->db->createCommand($sql)->bindValues([':pid' => $this->id])->queryAll();
			$amount = round(empty($r[0]['my_value']) ? 0 : $r[0]['my_value'], 2);
			return $amount;
		} else {
			$sql = "SELECT SUM( IF(my_value < value AND JSON_VALUE(meta, '$.confirmed') IS NULL, my_value, value) ) as my_value FROM  `{$this->linedLines()}` where `parent_id` = :pid AND shipment_no != 'Can\'t upload line difference' AND consol_id != 0 group by parent_id";
			$r = Yii::app()->db->createCommand($sql)->bindValues([':pid' => $this->id])->queryAll();
			$amount = round(empty($r[0]['my_value']) ? 0 : $r[0]['my_value'], 2);
			return $amount;
		}
	}

	public function getAcceptDeviation()
	{
		return round($this->getAcceptTotal() - (!empty($this->mdata['invoice_amount']) ? $this->mdata['invoice_amount'] / 1.1 : $this->getInvoiceTotal()), 2);
	}

	public function getAcceptPercent()
	{
		if (empty($this->getAcceptTotal())) return '0%';
		return round((!empty($this->mdata['invoice_amount']) ? $this->mdata['invoice_amount'] / 1.1 : $this->getInvoiceTotal()) / $this->getAcceptTotal() * 100, 2) . '%';
	}
	
	  public function  getMyTotalManifest(){
		$sql="SELECT SUM(my_value_m) as my_value_m FROM  `{$this->linedLines()}` where `parent_id`=:pid group by parent_id";
		$r=Yii::app()->db->createCommand($sql)->bindValues([':pid'=> $this->id])->queryAll();
		return round(empty($r[0]['my_value_m'])?0:$r[0]['my_value_m'],2);
	}

	public function getMyTotalWithOtherFee()
	{
		$AupostFuelCharge = SystemSetting::getAupostFuelChargeSetting();
		$invoiceMonth = date("Y-m",strtotime($this->invoice_date));
		$fuelCharge = (1+@$AupostFuelCharge[$invoiceMonth]*0.01);
		if($this->client_type==self::D2Z_CLIENT||$this->client_type==self::D2Z_DECLARE_TYPE)
		{
			return ($this->my_total*$fuelCharge);
		}
	}

	public function ifConsol()
	{
		if (!empty($this->mdata['noconsol'])) {
			return 'Yes';
		} else {
			return 'No';
		}
	}

	public function getOTinvStatus()
	{
		if (empty($this->mdata['ot_inv']) || in_array($this->mdata['ot_inv'], [2])) {
			$all_lines = BillingLine::model()->with('billing')->count('t.billing_cref = :cref AND t.flag IN (1,2) AND billing.date >= "2019-02-01" AND t.type = 1', [':cref' => $this->invoice_no]);
			$ot_lines = BillingLine::model()->with('billing')->count('t.billing_cref = :cref AND t.flag IN (1,2) AND JSON_VALUE(t.meta, "$.ot_inv") AND billing.date >= "2019-02-01" AND t.type = 1', [':cref' => $this->invoice_no]);
			if ($all_lines == 0) {
				$this->mdata['ot_inv'] = 3;
			} else if ($all_lines == $ot_lines) {
				$this->mdata['ot_inv'] = 1;
			} else {
				$this->mdata['ot_inv'] = 2;
			}
			$this->update('meta');
		}

		if ($this->mdata['ot_inv'] == 3) {
			return Yii::t(strtolower(__CLASS__), self::$ot_inv_states[$this->mdata['ot_inv']]);
		} else {
			$billing = Billing::model()->find('billing_cref = :cref AND status != 11', [':cref' => $this->invoice_no]);
			if (!empty($billing)) {
				return "<a href=\"" . Yii::app()->createUrl('billing/OTInvoice1', ['id' => $billing->id]) . "\" class=\"tab_link\" title=\"OT Invoice " . $this->invoice_no . "\">" . Yii::t(strtolower(__CLASS__), self::$ot_inv_states[$this->mdata['ot_inv']]) . "</a>";
			} else {
				return Yii::t(strtolower(__CLASS__), self::$ot_inv_states[$this->mdata['ot_inv']]);
			}
		}
	}

	public function getDeclareChargeDiff($type = false)
	{
		if($this->isDeclare())
		{
			$lines = $this->declareLines;
			$total = 0;
			foreach ($lines as $key => $value) {
				$total += $value->mdata["chargeDifference"];
			}
			if($type)
			{
				return "( Declare:".$total." )";
			}else
			{
				return $total;
			}
		}
	}

	public function tntOther()
	{
		$lines = ReconciliationLine::model()->findAll('parent_id = :id AND JSON_VALUE(meta, "$.tnt_type") != "Shipment"', [':id' => $this->id]);
		$charges = [];
		$charges_3pl = [];
		foreach ($lines as $line) {
			if ($line->mdata['tnt_type'] == 'Fuel') {
				$desc = 'Fuel';
			} else {
				$desc = $line->shipment_no . ' ' . $line->mdata['tnt_type'];
			}
			if (empty($line->consol_id)) continue;
			if ($line->consol->owner_id != 114) {
				if (empty($charges[$line->consol_id][$desc])) {
					$charges[$line->consol_id][$desc] = 0;
				}
				$charges[$line->consol_id][$desc] += $line->value;
			} else {
				// fuel already calculated in _TntReconciliationShipment
				if ($desc == 'Fuel') continue;
				$charges_3pl[] = $line;
			}
		}

		// for import
		foreach ($charges as $consol_id => $items) {
			$consol = Consol::model()->findByPk($consol_id);
			foreach ($items as $desc => $amount) {
				$billing = BillingLine::model()->find('billing_cref = :invoice_no AND billing_ref = :consol_no AND `desc` = :desc', [':invoice_no' => $this->invoice_no, ':consol_no' => $consol->no, ':desc' => $desc]);
				if (empty($billing)) {
					$billing = new BillingLine;
					$billing->status = 2;
					$billing->link_id = 0;
					$billing->org_id = Org::ORGID_COURIER_TNT;
					$billing->charge_code = Consol::AU_LOCAL_DELIVERY_COST_GL_CODE;
					$billing->billing_cref = $this->invoice_no;
					$billing->currency = 1;
					$billing->weight = 0;
					$billing->charge_weight = 0;
					$billing->billing_ref = $consol->no;
					$billing->awb = $consol->awb;
					$billing->dpt_id = empty($consol->dpt_id) ? Org::PCAE_DEPARTMENT_SYDNEY : $consol->dpt_id;
					$billing->date = $this->invoice_date;
					$billing->created = $this->invoice_date;
					$billing->transaction_date = $this->invoice_date;
					$billing->due = $this->invoice_date;
					$billing->type = BillingLine::BILLING_TYPE_IMPORT; // for import type
					$billing->dpmt = Invoice::DPMT_IMPORT;
					$billing->actual_amount = number_format($amount, 2, '.', '');
					$billing->gst = 'INPUT';
					$billing->gst_amount = $billing->getGSTValue();
					$billing->desc = $desc;
					$billing->accrual_amount = $billing->actual_amount;
					if (preg_match('/\sRED/', $desc)) {
						$billing->flag = BillingLine::BILLING_FLAG_SECOND;
					} else if (preg_match('/\sMHP/', $desc)) {
						$billing->flag = BillingLine::BILLING_FLAG_MANUAL;
					} else if ($desc != 'Fuel') {
						$billing->flag = BillingLine::BILLING_FLAG_OTHER;
					}
					$billing->save();

					Billing::linkLine($billing, false);
				} else {
					$billing->actual_amount = $amount;
					$billing->gst = 'INPUT';
					$billing->gst_amount = $billing->getGSTValue();
					$billing->desc = $desc;
					$billing->accrual_amount = $billing->actual_amount;
					$billing->save();

					Billing::linkLine($billing, false);
				}
			}
		}

		// for 3pl
		foreach ($charges_3pl as $line) {
			$task = WmsTask::model()->findByPk(substr($line->shipment->cref, 1));
			$desc = $line->shipment_no . ' ' . $line->mdata['tnt_type'];
			$billing = BillingLine::model()->find('billing_cref = :invoice_no AND billing_ref = :task_no AND `desc` = :desc', [':invoice_no' => $this->invoice_no, ':task_no' => $task->getNo(), ':desc' => $desc]);
			if (empty($billing)) {
				$billing = new BillingLine;
				$billing->status = 2;
				$billing->link_id = 0;
				$billing->org_id = Org::ORGID_COURIER_TNT;
				$billing->charge_code = Consol::DELIVERY_3PL_COST_GL_CODE;
				$billing->billing_cref = $this->invoice_no;
				$billing->currency = 1;
				$billing->weight = 0;
				$billing->charge_weight = 0;
				$billing->billing_ref = $task->getNo();
				$billing->awb = '';
				$billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
				$billing->date = $this->invoice_date;
				$billing->created = $this->invoice_date;
				$billing->transaction_date = $this->invoice_date;
				$billing->due = $this->invoice_date;
				$billing->type = BillingLine::BILLING_TYPE_3PL; // for import type
				$billing->dpmt = Invoice::DPMT_3PL;
				$billing->actual_amount = $line->value;
				$billing->gst = 'INPUT';
				$billing->gst_amount = $billing->getGSTValue();
				$billing->desc = $desc;
				$billing->accrual_amount = $billing->actual_amount;
				if (preg_match('/\sRED/', $desc)) {
					$billing->flag = BillingLine::BILLING_FLAG_SECOND;
				} else if (preg_match('/\sMHP/', $desc)) {
					$billing->flag = BillingLine::BILLING_FLAG_MANUAL;
				} else if ($desc != 'Fuel') {
					$billing->flag = BillingLine::BILLING_FLAG_OTHER;
				}
				$billing->save();

				Billing::linkLine($billing, false);
			} else {
				$billing->actual_amount = $line->value;
				$billing->gst = 'INPUT';
				$billing->gst_amount = $billing->getGSTValue();
				$billing->desc = $desc;
				$billing->accrual_amount = $billing->actual_amount;
				$billing->save();

				Billing::linkLine($billing, false);
			}
		}
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
	public function search($pgn = true)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('client_type',$this->client_type);
		$criteria->compare('invoice_no',$this->invoice_no,true);
		$criteria->compare('invoice_date',$this->invoice_date,true);
		$criteria->compare('invoice_total',$this->invoice_total,true);
		$criteria->compare('my_total',$this->my_total,true);
		$criteria->compare('manifest_no', $this->manifest_no, true);

		if (!empty($this->notype)) {
			$criteria->addCondition('t.client_type NOT IN (' . implode(',', $this->notype) . ')');
		}

		$with = [];
		if (!empty($this->connote)) {
			$with[] = 'lines';
			$criteria->addCondition(" lines.shipment_no like '%{$this->connote}%' ");
		}else if (!empty($this->declare_connote)) {
			$with[] = 'declareLines';
			$criteria->addCondition(" declareLines.shipment_no like '%{$this->declare_connote}%' ");
		}

		if (!empty($this->no_consol)) {
			if ($this->no_consol == 1) {
				$criteria->addCondition('JSON_VALUE(meta, "$.noconsol") IS NOT NULL');
			} else if ($this->no_consol == 2) {
				$criteria->addCondition('JSON_VALUE(meta, "$.noconsol") IS NULL');
			}
		}

		if (!empty($this->ot_inv)) {
			$criteria->compare('JSON_VALUE(meta, "$.ot_inv")', $this->ot_inv);
		}

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		$sort = new CSort();
		$sort->defaultOrder ='t.invoice_date DESC';


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>$sort,
			'pagination'=> $pgn ? array(
				'pageSize'=> 30,
			) : false,
		));
	}

	public function getWeightDeviation($reCal=false)
	{

		$weightDeviation = @$this->mdata['weightDeviation'];
		if(!isset($weightDeviation)||$reCal)
		{
			$trans = Yii::app()->db->beginTransaction();
			try
			{
				$myLines = null;
				
				if(in_array($this->getType(),Reconciliation::$declareCourier))
				{
					$myLines = $this->declareLines;
				}else
				{
					$myLines = $this->lines;
				}

				$weightDeviation = 0;
				foreach ($myLines as $key => $line) {
					$weightDeviation += $line->getChargeWeightDiff($reCal);
				}
				$this->mdata['weightDeviation']= $weightDeviation;
				$this->save();
				$trans->commit();
			} catch (Exception $ex) 
			{
				$trans->rollback();
				throw $ex;
			}
		}

		return $weightDeviation;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Reconciliation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	public function getReconciliationWeightDiffInvoiceReport()
	{
		$res = null;
		if(in_array($this->getType(),Reconciliation::$declareCourier))
		{
			$res = $this->declareLines;
		}else
		{
			$res = $this->lines;
		}

		$chargePercent = 0;
		if(in_array($this->getType(),Reconciliation::$cbmCourier))
		{
			$chargePercent =SystemSetting::getWeightDiffChargeSetting('big');
		}else
		{
			$chargePercent =SystemSetting::getWeightDiffChargeSetting('small');
		}

		$reportArr = [];
		$consolReArr = [];
		foreach ($res as $key => $re) 
		{
			if ($re->parent->client_type == Reconciliation::TNT_CLIENT && !empty($re->mdata['tnt_type']) && $re->mdata['tnt_type'] != 'Shipment') continue;
			$consolReArr[$re->consol_id][]=$re;//get ArrRe To Consol, so that it can be dealed with together
		}

		foreach ($consolReArr as $key => $reArr) 
		{
			$report = [];
			$shipment = ImParcel::model()->find(' ref = :ref ',[":ref"=>$reArr[0]->shipment_no]);
			$dpt_id = $shipment->ddpt_id;
			$dpmt = Invoice::INVOICE_TYPE_IMPORT;
			$to_id = $shipment->agent_id;
			$currency = Invoice::CURRENCY_AUD;
			$consol = $shipment->consol;
			if(empty($consol))
			{
				continue;
			}
			$consolId = $shipment->consol_id;
			$awb = $consol->awb;
			$report['to_id']=$shipment->agent_id;
			$report['consol_id']=$consol->id;
			$report['consol_no']=$consol->no;
			$report['lines'] = [];
			$report['amount'] = 0;
			$lines = [];
			foreach ($reArr as $key => $re) 
			{
				$cicc = $re->getCourierWeightInvoiceByChargeCode();
				$ci = $re->getChargedInvoice();
				$amount = $cicc-$ci;
				$cwcc = $re->getCourierWeightByChargeCode();
				$ocw = $re->ourChargeWeight();
				$ocwc = $re->getCSChargeWeight();
				$thisPercent = $amount/$ci;
				if(($thisPercent<=$chargePercent)||$ocwc>=$cwcc)
				{
					continue;
				}

				$amount = round($amount,2);
				$lines['ref']=$re->shipment_no;
				$lines['description']=$re->shipment_no.'/actual weight '.$cwcc.'kg, was '.$ocw.'kg/Correct invoice $'.$cicc.', was inv. $'.$ci;
				$lines['amount'] = $amount;
				$report['lines'][] = $lines;
				$report['amount']+=$amount;
			}

			if(sizeof($lines)>0)
			{
				$reportArr[] =$report;
			}
		}
		return $reportArr;
	}

	public function getGeneratedInvoice()
	{
		$result = "";
		if(!empty($this->mdata['allWeightDiffAmount']))
		{
			if(!empty($this->mdata['generateAllWeightDiff']))
			{
				$result .="[GAWD] ";
			}
			$result .= "amount:".$this->mdata['allWeightDiffAmount'];
		}
		return $result;
	}
	public function isDeclare()
	{
		if(in_array($this->getType(),Reconciliation::$declareCourier))
		{
			return true;
		}else
		{
			return false;
		}
	}

	public function isRTS()
	{
		if(in_array($this->getType(),Reconciliation::$rtsCourier))
		{
			return true;
		}else
		{
			return false;
		}
	}

	public function getLines()
	{
		if($this->isDeclare())
		{
			return $this->declareLines;
		}else
		{
			return $this->lines;
		}
	}

	public function getAttachedFileLink()
	{
		$fileInfo = FileRepo::model()->find('fid = :fid AND type = :type', [':fid' => $this->id, ':type' => FileRepo::COURIER_INVOICE_ATTACHMENT]);
		if (empty($fileInfo)) return '';

		return DIRECTORY_SEPARATOR . 'filerepo' . DIRECTORY_SEPARATOR . $fileInfo->hash . DIRECTORY_SEPARATOR . $fileInfo->name;
	}

	public function getAttachedFileName()
	{
		$fileInfo = FileRepo::model()->find('fid = :fid AND type = :type', [':fid' => $this->id, ':type' => FileRepo::COURIER_INVOICE_ATTACHMENT]);
		if (empty($fileInfo)) return '';

		return $fileInfo->name;
	}

	public function recordInvoiceAmountDiff()
	{
		$this->refresh();
		if (!empty($this->mdata['invoice_amount']) && number_format($this->mdata['invoice_amount'] / 1.1 - $this->getInvoiceTotal(), 2, '.', '') != 0) {
			$rl = ReconciliationLine::model()->find('parent_id = :parent_id AND postcode = :desc', [':parent_id' => $this->id, ':desc' => 'Can\'t upload line difference']);
			if (empty($rl)) {
				$rl = new ReconciliationLine;
				$rl->parent_id = $this->id;
				$rl->shipment_no = 'Can\'t upload line difference';
				$rl->value = number_format($this->mdata['invoice_amount'] / 1.1 - $this->getInvoiceTotal(), 2, '.', '');
				$rl->my_value = 0;
				$rl->weight = 0;
				$rl->our_charge_weight = 0;
				$rl->manifest_weight = 0;
				$rl->invoice_no = $this->invoice_no;
				$rl->postcode = 0;
			}
			$rl->save();
		}
	}

}
