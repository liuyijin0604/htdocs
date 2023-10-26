<?php

/**
 * This is the model class for table "si_reconcile".
 *
 * The followings are the available columns in table 'si_reconcile':
 * @property string $id
 * @property integer $supplier_invoice_id
 * @property string $type
 * @property string $status
 * @property integer $flag
 * @property string $meta
 * @property string $create
 * @property string $total
 * @property string $total_confirmed
 * @property string $total_gst
 * @property string $total_gst_confirmed
 * @property string $total_ex_gst_confirmed
 * @property string $total_ex_gst
 */
class SiReconcile extends OMetaModel
{
	const TYPE_COURIER  = 1;
	const TYPE_BROKER = 2;
	const TYPE_TERMINAL = 3;
	const TYPE_MANUAL = 4;
	const TYPE_EXPENSE = 5;
	const ERROR_CHECKING_STATUS = 10;
	const WEIGHT_CHECKING_STATUS = 20;
	const RATE_CHECKING_STATUS = 30;
	const SURCHARGE_CHECKING_STATUS = 40;
	const LINKING_BILLING_STATUS = 50;
	const SI_RECONCILE_DONE_STATUS = 60;
	const CANCEL_STATUS = 100;
	const ERROR_CONFIRMED = 1;
	const WEIGHT_CONFIRMED = 2;
	const RATE_CONFIRMED = 4;
	const SURCHARGE_CONFIRMED = 8;
	const BLILLING_LINKED = 16;
	public $inv_no = "";
	public $inv_date = "";
	public $refs;
	public $dpmtLines = [];
	public $summary = [];


	public static $types =[
		1=>'Courier',
		2=>'Broker',
		3=>'Terminal',
		4=>'Manual',
		5=>'Expense'
	];

	public static $confirmed_status =[
		1=>'Error Confirmed',
		2=>'Weight Confirmed',
		4=>'Rate Confirmed',
		8=>'Surcharge Confirmed',
		16=>'Billing Linked'
	];

	public static $confirmed_status_check =[
		10=>1,
		20=>2,
		30=>3,
		40=>4,
		50=>5
	];

	public static $search_confirmed_status =[
		1=>'Error Confirmed',
		2=>'Weight Confirmed',
		4=>'Rate Confirmed',
		8=>'Surcharge Confirmed',
		16=>'Billing Linked'
	];

	public static $broker_confirmed_status =[
		'<16'=>'Not Linked',
		'=4'=>'Confirmed But Not Linked',
		'>=16'=>'Billing Linked'
	];

	public static $confirmed_relation =[
		self::ERROR_CHECKING_STATUS=>self::ERROR_CONFIRMED,
		self::WEIGHT_CHECKING_STATUS=>self::WEIGHT_CONFIRMED,
		self::RATE_CHECKING_STATUS=>self::RATE_CONFIRMED,
		self::SURCHARGE_CHECKING_STATUS=>self::SURCHARGE_CONFIRMED,
		self::LINKING_BILLING_STATUS=>self::BLILLING_LINKED,
		self::SI_RECONCILE_DONE_STATUS => self::BLILLING_LINKED,
	];

	public static $cbmCourier = [
		Org::ORGID_COURIER_TNT,
		Org::ORGID_COURIER_EIZ_TOLL,
		Org::ORGID_COURIER_TNT_TOP,
		Org::ORGID_COURIER_STARTRACK,
		Org::ORGID_COURIER_DFE,
		Org::ORGID_COURIER_DFE_TOP,
		Org::ORGID_SUPPLIER_AUSTWAY,
		Org::ORGID_COURIER_UBI,
		Org::ORGID_COURIER_TOLL_IPEC,
		Org::ORGID_COURIER_ALLIED_TOP
	];

	public static $pureCbmCourier = [
		Org::ORGID_SUPPLIER_AUSTWAY
	];

	public static $states =[
		10=>'Waiting for Error Checking',
		20=>'Waiting for Weight Checking',
		30=>'Waiting for Rate Checking',
		40=>'Waiting for Surcharge Checking',
		50=>'Waiting for Linking Billing',
		60=>'Si Reconcile Done',
		100=>'Delete'
	];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'si_reconcile';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('supplier_invoice_id, flag, create, total, total_gst, total_ex_gst', 'required'),
			array('supplier_invoice_id, flag', 'numerical', 'integerOnly'=>true),
			array('id, total, total_gst, total_ex_gst', 'length', 'max'=>20),
			array('type, status', 'length', 'max'=>4),
			array('inv_date', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('confirm_status, org_id, inv_no, refs', 'safe'),
			array('supplier_invoice_id, ref, type, status, flag, create, total, total_confirmed, total_gst, total_gst_confirmed, total_ex_gst_confirmed, total_ex_gst,inv_no,confirm_status,org_id,inv_date,refs', 'safe', 'on'=>'search'),
		);
	}

	public function getDbConnection(){
		return self::getTlaConnection();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'parent'=>[self::BELONGS_TO, 'SupplierInvoice', 'supplier_invoice_id'],
			'dpmts'=>[self::HAS_MANY, 'SiReconcileDpmt', 'si_reconcile_id'],
			'lines'=>[self::HAS_MANY,'SiReconcileLine','rec_id'],
			'logs' => [self::HAS_MANY, 'Log', 'lid', 'on' => "logs.model = '".get_called_class()."'", 'order' => 'logs.time ASC'],
			'supplier'=>[self::BELONGS_TO,'Org','org_id']
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'supplier_invoice_id' => 'Supplier Invoice',
			'type' => 'Type',
			'ref' => 'Ref',
			'status' => 'Status',
			'flag' => 'Flag',
			'meta' => 'Meta',
			'create' => 'Create',
			'total' => 'Total',
			'total_gst' => 'Total Gst',
			'total_ex_gst' => 'Total Ex Gst',
			'confirm_status' => 'Confirm Status',
			'org_id' => 'Supplier',
			'inv_date' => 'Inv Date',
			'refs' => 'refs'
		);
	}

	public function getStatus()
	{
		return isset(static::$states[$this->status])? Yii::t(strtolower(__CLASS__), static::$states[$this->status]) : $this->status;
	}
	public function beforeSave()
	{
		$this->total_confirmed = empty($this->total_confirmed)?"[]":$this->total_confirmed;
		$this->total_gst_confirmed = empty($this->total_gst_confirmed)?"[]":$this->total_gst_confirmed;
		$this->total_ex_gst_confirmed = empty($this->total_ex_gst_confirmed)?"[]":$this->total_ex_gst_confirmed;

		return parent::beforeSave();
	}
	public function afterSave()
	{
		$extra = [];
		//add log
		if (!empty($this))
		{

			if(!empty($this->confirm_status))
			{
				$extra["confirm status"] = $this->getFullConfirmStatus();
			}
			
			Log::add($this, $this->isNewRecord? 3 : 4, array_merge(['status' => $this->getStatus()], $extra));
		}

		if ($this->status == self::CANCEL_STATUS) $this->deleteBilling();
	}

	public function deleteBilling()
	{
		$billing = Billing::model()->find('JSON_VALUE(meta, "$.from_supplier_invoice") = :id', [':id' => $this->supplier_invoice_id]);
		if (!empty($billing)) {
			$billing->status = Billing::BILLING_STATUS_CANCELLED;
			$billing->update('status');
		}
	}

	public function log($action)
	{
		$extra =["action"=>$action];
		Log::add($this, $this->isNewRecord? 3 : 4, array_merge(['status' => $this->getStatus()], $extra));
	}
	public function getFullStatus()
	{
		return self::$states[$this->status];
	}

	public function getFullConfirmStatus($symbol = '</br>')
	{
		$status = [];
		foreach(self::$confirmed_status as $key => $value) {
			if(($this->confirm_status&$key)>0)
			{
				$status[] = $value;
			}
		}
		return join($symbol,$status);
	}

	public function havingErrorParcels($dpmt = false)
	{
		$sql = 'SELECT COUNT(id) AS t FROM si_reconcile_line WHERE type >0 and rec_id = '.$this->id.$this->getDpmtSql($dpmt);
		$c = Yii::app()->db->createCommand($sql);
		$result =  $c->queryScalar();
		return $result;
	}

	private function getDpmtSql($dpmt)
	{
		if($dpmt==Invoice::DPMT_IMPORT)
		{
			return " and (type & ".SiReconcileLine::TYPE_3PL.") =0";
		}elseif($dpmt==Invoice::DPMT_3PL)
		{
			return " and (type & ".SiReconcileLine::TYPE_3PL.")>0";
		}else
		{
			return '';
		}
	}

	public function havingSurcharge($dpmt = false)
	{
		$sql = 'SELECT COUNT(id) AS t FROM si_reconcile_line WHERE item_code !="item" and item_code !="Item"  and rec_id = '.$this->id.$this->getDpmtSql($dpmt);
		$c = Yii::app()->db->createCommand($sql);
		$result =  $c->queryScalar();
		return $result;
	}

	public function getCurrentTotalExGST($dpmt = false)
	{
		$sql = 'SELECT sum(value) AS t FROM si_reconcile_line WHERE (item_code ="eparcel" or item_code ="letter")  and rec_id = '.$this->id.$this->getDpmtSql($dpmt);
		$c = Yii::app()->db->createCommand($sql);
		$result =  $c->queryScalar();
		return $result;
	}

	public function getCurrency()
	{
		return Invoice::$currencies_s[$this->parent->currency];
	}


	public function getCurrentConfirmedTotal()
	{
		$confirmedTotal = json_decode($this->total_ex_gst_confirmed,true);
		if($this->status<30)
		{
			if(!empty($confirmedTotal))
			{
				return @$confirmedTotal[$this->status];
			}
		}else
		{
			return @$confirmedTotal[30]+@$confirmedTotal[40];
		}
	}

	public function getReconciliationWeightDiffInvoiceReport($dpmt = false)
	{
		$appName = Yii::app()->name;
		$res = $this->getLines($dpmt);
		$chargePercent = 0;
		if(in_array($this->org_id,SiReconcile::$cbmCourier))
		{
			$chargePercent =SystemSetting::getWeightDiffChargeSetting('big');
		}else
		{
			$chargePercent =SystemSetting::getWeightDiffChargeSetting('small');
		}
		$reportArr = [];
		$consolReArr = [];
		$consolObjArr = [];
		foreach ($res as $key => $re) 
		{
			if($re->item_code!='item')
			{
				continue;
			}
			if (($re->parent->org_id == Org::ORGID_COURIER_TNT && !empty($re->mdata['tnt_type']) && $re->mdata['tnt_type'] != 'Shipment')||$re->fid==0||($re->confirm_status&SiReconcile::WEIGHT_CONFIRMED)==0) continue;
			$consolReArr[$re->imparcel->consol_id][]=$re;//get ArrRe To Consol, so that it can be dealed with together
			Yii::app()->name = $appName;
		}
		foreach ($consolReArr as $key => $reArr) 
		{
			$shipment = $reArr[0]->imparcel;
			$consolId = $shipment->consol_id;
			if(empty($consolObjArr[$consolId])) $consolObjArr[$consolId] = Consol::model()->findByPk($consolId);
			$report = [];
			Yii::app()->name = $appName;
			$dpt_id = $shipment->ddpt_id;
			$dpmt = Invoice::INVOICE_TYPE_IMPORT;
			$to_id = $shipment->agent_id;
			$currency = Invoice::CURRENCY_AUD;
			$consol = $consolObjArr[$consolId];
			Yii::app()->name = $appName;
			if(empty($consol))
			{
				continue;
			}
			$awb = $consol->awb;
			$report['to_id']=$shipment->agent_id;
			$report['consol_id']=$consol->id;
			$report['consol_no']=$consol->no;
			$report['lines'] = [];
			$report['amount'] = 0;
			$lines = [];
			foreach ($reArr as $key => $re) 
			{
				Yii::app()->name = $appName;
				$cicc = $re->getCourierWeightInvoiceByChargeCode();
				$ci = $re->getChargedInvoice($consol);
				$amount = $cicc-$ci;
				$cwcc = $re->getCourierWeightByChargeCode();
				$ocw = $re->ourChargeWeight();
				$ocwc = $re->getCSChargeWeight();
				if(empty($ci)||$ci==0)
				{
					$thisPercent = 1;
				}else
				{
					$thisPercent = $amount/$ci;
				}
				if(($thisPercent<=$chargePercent)||($cwcc-$ocw)<0.01)
				{
					continue;
				}

				$amount = round($amount,2);
				$lines['ref']=$re->ref;
				$lines['id']=$re->id;
				if(in_array($re->parent->org_id,SiReconcile::$pureCbmCourier))
				{
					$lines['description']=$re->ref.'/actual CBM '.$cwcc.'cbm, was '.$ocw.'cbm/Correct invoice $'.$cicc.', was inv. $'.$ci;
				}else
				{
					$lines['description']=$re->ref.'/actual weight '.$cwcc.'kg, was '.$ocw.'kg/Correct invoice $'.$cicc.', was inv. $'.$ci;
				}
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

	public function getReconciliationSurchargeInvoiceReport($dpmt = false)
	{
		$res = $this->getLines($dpmt);
		$orgFlexibleRateService = new OrgFlexibleRateService();
		$chargePercent = 0;

		$unknownLine = [];
		$reportArr = [];
		$consolReArr = [];
		$chargedList = [];
		foreach ($res as $key => $re) 
		{
			$shipment = $re->imparcel;
			if(!empty($shipment))
			{
				if($re->getCSChargeWeight()<0.0001)
				{
					$re->getCSChargeWeight(true);
				}
				// if($shipment->agent_id==Org::ORGID_CLIENT_AOCHEN&&(in_array(strtoupper($re->item_code),["RSD","RD1","RD2","HOME DELIVERY"])||($re->weight<30||($re->weight>30&&$re->getCSChargeWeight()>30))&&in_array(strtoupper($re->item_code),["OS0","OS1","MHP","SD0","SD1","MHR","MH","OS","MI","MO","MANUAL HANDLING FEE","WIDTH SURCHARGE","LENGTH SURCHARGE","DEPOT HANDLING SURCHARGE"])))
				// {
				// 	continue;
				// }	
			}

			if(preg_match('/fuel/i',$re->item_code)||$re->value<=0.001)
			{
				continue;
			}


			if($this->org_id!=Org::ORGID_COURIER_EIZ_TOLL)
			{
				if($re->item_code=="item")
				{
					continue;
				}
			}

			if(in_array(strtoupper($re->item_code),["RZ","LEVY","RETURN TO SENDER","RTS"]))
			{
				continue;
			}

			if(empty($re->imparcel->consol_id))
			{
				$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
				continue;
			}
			$consolReArr[$re->imparcel->consol_id][]=$re;//get ArrRe To Consol, so that it can be dealed with together
		}

		foreach ($consolReArr as $key => $reArr) 
		{
			$report = [];
			$shipment = $reArr[0]->imparcel;
			if(empty($shipment))
			{
				continue;
			}
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
			if($shipment->consol->is3PLOnly()) continue;// do not charge 3PL, because they charge the customer in previous charge
			$awb = $consol->awb;
			$report['to_id']=$shipment->agent_id;
			$report['consol_id']=$consol->id;
			$report['consol_no']=$consol->no;
			$report['lines'] = [];
			$report['amount'] = 0;
			$lines = [];

			if($this->org_id==Org::ORGID_COURIER_EIZ_TOLL)
			{
				foreach ($reArr as $key3 => $re) 
				{
					$shipment = ImParcel::model()->find(' ref = :ref ',[":ref"=>$re->ref]);
					if(empty($shipment))
					{
						continue;
					}
					$amount = 0;
					$rate = null;
					$amountRe =[];
					if(ImParcelService::isEizToll($shipment))
					{
						$rates = SystemSetting::getSurchargeChargeRate(strtoupper('TOLLOS'));

						if(!empty($re->mdata['package']))
						{
							$packages = $re->mdata['package'];
						}else
						{
							$packages = $shipment->mdata['eiz']['package'];
						}
						if(empty($packages))
						{
							echo $re->ref;
							continue;
						}
						foreach($packages as $key4 => $pack)
						{
							$qty = $pack['qty'];
							$weight = $pack['weight'];
							$length = $pack['length'];
							$height = $pack['height'];
							$width =  $pack['width'];
							$bultWeight = ($length*$height*$width)*250/1000000;
							$myRate = null;
							foreach ($rates as $key => $rate)
							{
								if(($weight>=$rate[4]&&$weight<=$rate[5])||($bultWeight>=$rate[6]&&$bultWeight<=$rate[7]))
								{
									$myRate = $rate;
									break;
								}

								if(($length>=$rate[2]&&$length<=$rate[3])||($height>=$rate[2]&&$height<=$rate[3])||($width>=$rate[2]&&$width<=$rate[3]))
								{
									$myRate = $rate;
									break;
								}
							}

							if(!empty($myRate))
							{
								$amount += $myRate[0]*$qty;
							}
						}
						$label ='TOLL';
					}else
					{
						$amount = 0;
						$amountQty = 1;
						$rates = SystemSetting::getSurchargeChargeRate(strtoupper('ALLIEDOS'));
						$rates2 = SystemSetting::getSurchargeChargeRate(strtoupper('ALLIEDLEN'));
						$rate = null;
						$amountRe =[];
						$allBultWeight = 0;
						$allWeight = 0;
						$allQty = 0;

						if(!empty($re->mdata['package']))
						{
							$packages = $re->mdata['package'];
						}else
						{
							$packages = $shipment->mdata['eiz']['package'];
						}
						if(empty($packages))
						{
							echo $re->ref;
							continue;
						}
						foreach($packages as $key4 => $pack)
						{
							$qty = $pack['qty'];
							$weight = $pack['weight'];
							$length = $pack['length'];
							$height = $pack['height'];
							$width =  $pack['width'];
							$bultWeight = ($length*$height*$width)*250/1000000;
							$allWeight += $weight*$qty;
							$allBultWeight += $bultWeight;
							$allQty += $qty;
						}
						$myRate = null;
						$myRate2 = null;
						foreach ($rates as $key4 => $rate)
						{
							if(($allWeight>=$rate[4]&&$allWeight<=$rate[5])||($allBultWeight>=$rate[6]&&$allBultWeight<=$rate[7]))
							{
								$myRate = $rate;
								break;
							}
						}

						foreach ($rates2 as $key4 => $rate)
						{
							if(($length>=$rate[4]&&$length<=$rate[5])||($height>=$rate[4]&&$height<=$rate[5])||($width>=$rate[4]&&$width<=$rate[5]))
							{
								$myRate2 = $rate;
								break;
							}
						}

						if(!empty($myRate))
						{
							$amount += $myRate[0];
						}

						if(!empty($myRate2))
						{
							$amount += $myRate2[0];
						}

						$label = 'ALLIED';
					}
					
					if($amount<=0)
					{
						continue;
					}

					$amount = round($amount*1.139,2);
					$amountStr = '';
					foreach ($amountRe as $key4 => $value) {
						$amountStr.=$value[0].$value[1].$key4.";";
					}
					$amountStr.='='.$amount;
					$lines['ref']=$re->ref;
					$lines['id']=$re->id;
					$lines['description']=$re->ref.'/MHP/EIZ-'.$label.'('.$re->value.'-'.$re->my_value.'='.($re->value-$re->my_value).")";
					$lines['amount'] = $amountStr;
					$lines['myAmount'] = $amount;
					$report['lines'][] = $lines;
					$report['amount']+=$amount;
				}
			}elseif(($this->org_id==Org::ORGID_COURIER_UBI&&$this->parent->mdata['template']=="UBI-toll-surcharge")||$this->org_id==Org::ORGID_COURIER_TOLL_IPEC)
			{
				foreach ($reArr as $key3 => $re) 
				{
					//if(($re->value-$re->my_value)<6) continue;
					$shipment = ImParcel::model()->find(' ref = :ref ',[":ref"=>$re->ref]);
					if(empty($shipment))
					{
						continue;
					}
					$amount = 0;
					$total = 0;
					$rate = null;
					$amount = 0;
					$amountQty = 1;
					$charged = 0;
					$total = 0;
					$rate = null;
					$chargedList = [];
					$det = "";
					$skip = false;

					[$amount,$total,$charged,$finalCodes,$isCharged,$skip] = ReconcileService::handlingSingleSiReconcileSurchargeLine($re,$this,$orgFlexibleRateService,$chargedList);
					if($skip) continue;
					if(is_numeric($amount))
					{
						if(empty($finalCodes))
						{
							$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
							continue;
						}

						
						$amountStr = '';
						$amountStr.='='.$total."-".$charged;
						$det = '|'.$amountStr."|".join(',',$finalCodes);
						
						if($amount<=0)
						{
							continue;
						}
					}else
					{
						if($this->org_id==Org::ORGID_COURIER_UBI)
						{
							$rates = SystemSetting::getSurchargeChargeRate(strtoupper('UBI'));
						}else
						{
							if(!in_array(strtoupper($re->item_code),["MI","MO","TG","FR","Tailgate"]))
							{

								$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
								continue;
							}

							$rates = SystemSetting::getSurchargeChargeRate(strtoupper('MYTOLL'));
						}
						if(!empty($rates[$re->item_code]))
						{
							switch ($rates[$re->item_code][1])
							{
								case 'shipment':
									$amount = $rates[$re->item_code][0];
									break;

								case 'pcs':
									$amount = $rates[$re->item_code][0]*$shipment->pkg;
									break;

								case 'ipercent':
									$amount = round($re->value*$rates[$re->item_code][0]);
									break;
								
								default:
									$amount = $re->value;
									break;
							}
						}else
						{
							$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
								continue;
						}

						$label ='TOLL';
						$amountStr = '';
						$amountStr.='='.$amount;
						$total = $amount;
					}

					$label ='TOLL';

					
					if($amount<=0)
					{
						continue;
					}

					
					$lines['ref']=$re->ref;
					$lines['id']=$re->id;
					$lines['description']=$re->ref.'/'.$re->item_code.'/'.$label.'('.$total.'- supplier:'.$re->value.'='.($total-$re->value).")".$det;;
					$lines['amount'] = $amountStr;
					$lines['myAmount'] = $amount;
					$report['lines'][] = $lines;
					$report['amount']+=$amount;
				}
			}elseif($this->org_id==Org::ORGID_COURIER_FL_HUNTER)
			{
				foreach ($reArr as $key3 => $re) 
				{
					$shipment = ImParcel::model()->find(' ref = :ref ',[":ref"=>$re->ref]);
					if(empty($shipment))
					{
						$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
						continue;
					}
					$amount = 0;
					$rate = null;
					if(!in_array(strtoupper($re->item_code),["RESIDENTIAL DELIVERY","RESIDENTIAL CHARGE","RESIDENTIAL ADDRESS","TAILGATE","TAILLIFT","TAIL LIFT","TAIL-LIFT","TAIL-LIFT TRUCK","HX RESIDENTIAL CHARGE","TAILGATE TRUCK","EXCESS LENGTH","EXCESS FREIGHT","REDIRECTION","REDELIVERY"]))
					{
						$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
						continue;
					}

					$rates = SystemSetting::getSurchargeChargeRate(strtoupper('FLHUNTER'));

					if(!empty($rates[$re->item_code]))
					{
						$thisRate = null;
						if(isset($rates[$re->item_code][number_format($re->value,0,'.','').""]))
						{
							$thisRate = $rates[$re->item_code][number_format($re->value,0,'.','').""];
						}else
						{
							$thisRate = $rates[$re->item_code];
						}
						switch ($thisRate[1])
						{
							case 'shipment':
								$amount = $thisRate[0];
								break;

							case 'kg':
								$amount = $thisRate[2]+$re->weight*$thisRate[3];
								break;
							
							default:
								$amount = $re->value;
								break;
						}
					}
					$amountStr = '';
					$amountStr.='='.$amount;
					$label ='HUNTER';
					
					
					if($amount<=0)
					{
						$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
						continue;
					}

					[$amount,$amountStr] = $this->calculateLastAmount($amount,$amountStr,$re);
					if($amount<=0)
					{
						continue;
					}

					$lines['ref']=$re->ref;
					$lines['id']=$re->id;
					$lines['description']=$re->ref.'/'.$re->item_code.'('.$amount.'- supplier:'.$re->value.'='.($amount-$re->value).")";
					$lines['amount'] = $amountStr;
					$lines['myAmount'] = $amount;
					$report['lines'][] = $lines;
					$report['amount']+=$amount;
				}
			}elseif($this->org_id==Org::ORGID_COURIER_ALLIED_TOP)
			{
				foreach ($reArr as $key3 => $re) 
				{
					if(in_array($re->item_code,["On fwd delivery"])) continue;
					$shipment = ImParcel::model()->find(' ref = :ref ',[":ref"=>$re->ref]);
					if(empty($shipment))
					{
						$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
						continue;
					}
					$amount = 0;
					$total = 0;
					$charged = 0;
					$total = 0;
					$rate = null;
					$skip = false;
					[$amount,$total,$charged,$finalCodes,$isCharged,$skip] = ReconcileService::handlingSingleSiReconcileSurchargeLine($re,$this,$orgFlexibleRateService,$chargedList);
					if($skip) continue;
					if($isCharged&&$amount<=0)
					{
						continue;
					}
					if(empty($finalCodes))
					{
						$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
						continue;
					}

					
					$amountStr = '';
					$amountStr.='='.$total."-".$charged;
					$label ='ALLIED';
					
					
					if($amount<=0)
					{
						continue;
					}


					$lines['ref']=$re->ref;
					$lines['id']=$re->id;
					$lines['description']=$re->ref.'/'.$re->item_code.'/'.join(',',$finalCodes).'('.$total.'- supplier:'.$re->value.'='.($total-$re->value).")";
					$lines['amount'] = $amountStr;
					$lines['myAmount'] = $amount;
					$report['lines'][] = $lines;
					$report['amount']+=$amount;
				}
			}elseif($this->org_id==Org::ORGID_COURIER_TNT_TOP)
			{
				foreach ($reArr as $key3 => $re) 
				{

					$shipment = ImParcel::model()->find(' ref = :ref ',[":ref"=>$re->ref]);
					if(empty($shipment))
					{
						$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
						continue;
					}
					$amount = 0;
					$amountStr = '';
					if(!in_array(strtoupper($re->item_code),['RED','OS0','OS1','MHP','SD0','SD1','MHR','RSD','RD1','RD2']))
					{
						$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
						continue;
					}

					$amount = 0;
					$total = 0;
					$rate = null;
					$amount = 0;
					$amountQty = 1;
					$charged = 0;
					$total = 0;
					$rate = null;
					$chargedList = [];
					$det = "";
					$skip = false;
					[$amount,$total,$charged,$finalCodes,$isCharged,$skip] = ReconcileService::handlingSingleSiReconcileSurchargeLine($re,$this,$orgFlexibleRateService,$chargedList);
					if($skip) continue;
					if(is_numeric($amount))
					{
						if(empty($finalCodes))
						{
							$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
							continue;
						}

						
						$amountStr = '';
						$amountStr.='='.$total."-".$charged;
						$det = '|'.$amountStr."|".join(',',$finalCodes);
						
						if($amount<=0)
						{
							continue;
						}
					}else
					{
						$amount = 0;

						$rate = SystemSetting::getSurchargeChargeRate(strtoupper($re->item_code));
						if($rate[1]=="fixed")
						{
							$amount = $rate[0];
							$amountStr = $amount.'*1';
						}else if($rate[1]=='pkg')
						{
							$amount = $rate[0]*round($re->value/$rate[2]);
							$amountStr = $rate[0].'*'.round($re->value/$rate[2]);
						}else if($rate[1]=='kg')
						{
							$amount = $rate[0]*$shipment->chargeWeight();
							$amountStr = $rate[0].'*'.$shipment->chargeWeight();
						}else if($rate[1]=='cbm')
						{
							$amount = $rate[0]*$shipment->myChargeCBM();
							$amountStr = $rate[0].'*'.$shipment->myChargeCBM();
						}else if($rate[1]=='ipercent')
						{
							$re2 = SiReconcileLine::model()->with(["parent"])->find("parent.status>=4 and ref = :ref and item_code='item'",[":ref"=>$re->ref]);
							if(!empty($re2))
							{
								$cicc = $re2->getCourierWeightInvoiceByChargeCode()*$rate[0];
								$amount = $cicc;
								$amountStr = $rate[0].'*'.$re2->getCourierWeightInvoiceByChargeCode();
							}
						}
						
						if($amount<=0)
						{
							continue;
						}
						$total = $amount;
					}


					$amount = round($amount,2);
					$amountStr.='='.$amount;
					$lines['ref']=$re->ref;
					$lines['id']=$re->id;
					$lines['description']=$re->ref.'/'.$re->item_code.'('.$total.'- supplier:'.$re->value.'='.($total-$re->value).")".$det;
					$lines['amount'] = $amountStr;
					$lines['myAmount'] = $amount;
					$report['lines'][] = $lines;
					$report['amount']+=$amount;



				}
			}else
			{
				foreach ($reArr as $key3 => $re) 
				{
					$shipment = ImParcel::model()->find(' ref = :ref ',[":ref"=>$re->ref]);
					if(empty($shipment))
					{
						$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
						continue;
					}
					$amount = 0;
					$amountStr = '';
					if(!in_array(strtoupper($re->item_code),['RED','OS0','OS1','MHP','SD0','SD1','MHR','RSD','RD1','RD2']))
					{
						$unknownLine[] = [$re->ref,$re->item_code,$re->value,$re->id,(empty($re->mdata['surcharge_charge'])?$re->value:$re->mdata['surcharge_charge']),@$re->mdata['surcharge_inv_id']];
						continue;
					}

					$rate = SystemSetting::getSurchargeChargeRate(strtoupper($re->item_code));
					if($rate[1]=="fixed")
					{
						$amount = $rate[0];
						$amountStr = $amount.'*1';
					}else if($rate[1]=='pkg')
					{
						$amount = $rate[0]*round($re->value/$rate[2]);
						$amountStr = $rate[0].'*'.round($re->value/$rate[2]);
					}else if($rate[1]=='kg')
					{
						$amount = $rate[0]*$shipment->chargeWeight();
						$amountStr = $rate[0].'*'.$shipment->chargeWeight();
					}else if($rate[1]=='cbm')
					{
						$amount = $rate[0]*$shipment->myChargeCBM();
						$amountStr = $rate[0].'*'.$shipment->myChargeCBM();
					}else if($rate[1]=='ipercent')
					{
						$re2 = SiReconcileLine::model()->with(["parent"])->find("parent.status>=4 and ref = :ref and item_code='item'",[":ref"=>$re->ref]);
						if(!empty($re2))
						{
							$cicc = $re2->getCourierWeightInvoiceByChargeCode()*$rate[0];
							$amount = $cicc;
							$amountStr = $rate[0].'*'.$re2->getCourierWeightInvoiceByChargeCode();
						}
					}

					if($amount<=0)
					{
						continue;
					}


					$amount = round($amount,2);
					$amountStr.='='.$amount;
					$lines['ref']=$re->ref;
					$lines['id']=$re->id;
					$lines['description']=$re->ref.'/'.$re->item_code.'('.$amount.'- supplier:'.$re->value.'='.($amount-$re->value).")";
					$lines['amount'] = $amountStr;
					$lines['myAmount'] = $amount;
					$report['lines'][] = $lines;
					$report['amount']+=$amount;
				}
			}

			if(sizeof($lines)>0)
			{
				$reportArr[] =$report;
			}
		}
		foreach ($reportArr as $key0 => $report) {
			foreach ($report['lines'] as $key1 => $value1) {
				$check = false;
				if(preg_match('/HOME DEL1/',$value1['description']))
				{
					foreach ($report['lines'] as $key2 => $value2) {
						if($value1['ref']==$value2['ref']&&preg_match('/HOME DEL/',$value2['description'])&&!preg_match('/HOME DEL1/',$value2['description']))
						{
							$check = true;
							break;
						}
					}
					if($check)
					{
						$reportArr[$key0]['amount'] = $reportArr[$key0]['amount'] -$reportArr[$key0]['lines'][$key1]['myAmount'];
						unset($reportArr[$key0]['lines'][$key1]);
					}
				}
			}
		}

		foreach ($unknownLine as $key => $value) {
			$unknownLine[$key][] = "";
			$unknownLine[$key][] = "";
		}
		return [$reportArr,$unknownLine];
	}

	public function calculateLastAmount($amount,$amountStr,$re)
	{
		if(!empty($re->fid))
		{
			if(empty($re->imparcel->shipmentCharge))
			{
				$re->imparcel->getChargeByChargecode('',false,null,true,false,true);
			}

			if(in_array($re->item_code,SystemSetting::getSurchargeChargeRate("oscodes"))&&$re->imparcel->shipmentCharge->os_fee>0)
			{
				$amount = $amount-$re->imparcel->shipmentCharge->os_fee;
				$amountStr.= "-".$re->imparcel->shipmentCharge->os_fee;
			}

			if(in_array($re->item_code,SystemSetting::getSurchargeChargeRate("mhcodes"))&&$re->imparcel->shipmentCharge->mh_fee>0)
			{
				$amount = $amount-$re->imparcel->shipmentCharge->mh_fee;
				$amountStr.= "-".$re->imparcel->shipmentCharge->mh_fee;
			}

			if(in_array($re->item_code,SystemSetting::getSurchargeChargeRate("rsdcodes"))&&$re->imparcel->shipmentCharge->rsd_fee>0)
			{
				$amount = $amount-$re->imparcel->shipmentCharge->rsd_fee;
				$amountStr.= "-".$re->imparcel->shipmentCharge->rsd_fee;
			}

			if(in_array($re->item_code,SystemSetting::getSurchargeChargeRate("remotecodes"))&&$re->imparcel->shipmentCharge->remote_fee>0)
			{
				$amount = $amount-$re->imparcel->shipmentCharge->remote_fee;
				$amountStr.= "-".$re->imparcel->shipmentCharge->remote_fee;
			}

		}

		return [$amount,$amountStr];
	}


	public function getReconciliationRTSInvoiceReport($dpmt = false)
	{
		$res = SiReconcileLine::model()->findAll('rec_id = :recId and item_code="RTS"',[":recId"=>$this->id]);
		$chargePercent = 0;

		$reportArr = [];
		$consolReArr = [];
		$res = SiReconcile::getDpmtLines($res,$dpmt);
		foreach ($res as $key => $re) 
		{
			$p = ImParcel::model()->find(' ref = :ref ',[":ref"=>$re->ref]);
			if(empty($p))
			{
				continue;
			}
			$re->imparcel = $p;
			if(empty($re->imparcel->consol_id)) continue;
			$consolReArr[$re->imparcel->consol_id][]=$re;//get ArrRe To Consol, so that it can be dealed with together
		}

		foreach ($consolReArr as $key => $reArr) 
		{
			$report = [];
			$shipment = $reArr[0]->imparcel;
			if(empty($shipment))
			{
				continue;
			}
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
			$report['consol_id']=$consol->id;
			$report['consol_no']=$consol->no;
			$report['lines'] = [];
			$report['amount'] = 0;
			$lines = [];
			foreach ($reArr as $key => $re) 
			{
				$p =$re->imparcel;

				$trs = [];
				$invoices = ImParcelService::getImParcelRTSInvoice($p);
				$agentId = $p->agent_id;
				$rtsFee = ImParcelService::getRtsRate($agentId, $p);
				$rtsScanDate = empty($p->mdata['rts_scan_date'])?"":$p->mdata['rts_scan_date'];
				$amount = round($rtsFee,2);
				$lines['to_id']=$p->agent_id;
				$lines['ref']=$p->ref;
				$lines['error']=$re->getErrorTypes();
				$lines['id']=$re->id;
				$lines['description']=$p->cref.' Returned to Sender Receiving Fee '. $rtsScanDate;
				$lines['amount'] = $rtsFee;
				$lines['invoices'] = $invoices;

				$report['lines'][] = $lines;
				$report['amount']+=$rtsFee;
			}

			if(sizeof($lines)>0)
			{
				$reportArr[] =$report;
			}
		}
		return $reportArr;
	}


	private static function createBl($dpmt, $rs, $billing, $si) {
		foreach ($rs as $r) {
			if ($r['amount'] == 0) continue;
			$bl = BillingLine::model()->find('billing_id = :billing_id AND `desc` = :dpmt AND gst = :gst', [':billing_id' => $billing->id, ':dpmt' => $dpmt, ':gst' => $r['gst']]);
			if (empty($bl)) $bl = new BillingLine;
			$bl->billing_id = $billing->id;
			$bl->org_id = $billing->org_id;
			$bl->op_id = 0;
			$bl->link_id = 0;
			$bl->to_id = 0;
			$bl->billing_cref = $billing->billing_cref;
			$bl->dpt_id = $billing->dpt_id;
			$bl->currency = $billing->currency;
			$bl->created = $billing->created;
			$bl->date = $billing->date;
			$bl->due = $billing->due;
			$bl->transaction_date = $billing->transaction_date;
			$bl->status = Billing::BILLING_STATUS_CONFIRM;
			$bl->gst = $r['gst'];
			$bl->accrual_amount = $r['amount'];
			$bl->actual_amount = $r['amount'];
			$bl->gst_amount = $bl->getGSTValue();
			if ($dpmt == 'Import') {
				$bl->desc = 'Import';
				$bl->type = BillingLine::BILLING_TYPE_IMPORT;
				$bl->dpmt = Invoice::DPMT_IMPORT;
				if (in_array($bl->org_id, Org::$couriers) || $si->type == 1) {
					$bl->charge_code = Consol::AU_LOCAL_DELIVERY_COST_GL_CODE;
				} else if (in_array($bl->org_id, Org::$brokers) || $si->type == 2) {
					$bl->charge_code = 91032;
				} else if (in_array($bl->org_id, Org::$airports) || $si->type == 3) {
					$bl->charge_code = 91030;
				} else {
					$bl->charge_code = !empty($si->lines) && !empty($si->lines[0]->mdata['charge_code']) ? $si->lines[0]->mdata['charge_code'] : '';
				}
			} else if ($dpmt == '3PL') {
				$bl->desc = '3PL';
				$bl->type = BillingLine::BILLING_TYPE_3PL;
				$bl->dpmt = Invoice::DPMT_3PL;
				$bl->charge_code = Consol::DELIVERY_3PL_COST_GL_CODE;
			}
			$bl->save();
		}
	}

	private static function createBlNew($dpmt, $rs, $billing, $si,$dptId) {
		foreach ($rs as $r) {
			if ($r['amount'] == 0) continue;
			$bl = BillingLine::model()->find('billing_id = :billing_id AND `desc` = :dpmt AND gst = :gst AND dpt_id = :dpt_id', [':billing_id' => $billing->id, ':dpmt' => $dpmt, ':gst' => $r['gst'],':dpt_id'=>$dptId]);
			if (empty($bl)) $bl = new BillingLine;
			$bl->billing_id = $billing->id;
			$bl->org_id = $billing->org_id;
			$bl->op_id = 0;
			$bl->link_id = 0;
			$bl->to_id = 0;
			$bl->billing_cref = $billing->billing_cref;
			$bl->dpt_id =  $dptId;
			$bl->currency = $billing->currency;
			$bl->created = $billing->created;
			$bl->date = $billing->date;
			$bl->due = $billing->due;
			$bl->transaction_date = $billing->transaction_date;
			$bl->status = Billing::BILLING_STATUS_CONFIRM;
			$bl->gst = $r['gst'];
			$bl->accrual_amount = $r['amount'];
			$bl->actual_amount = $r['amount'];
			$bl->gst_amount = $bl->getGSTValue();
			if ($dpmt == Invoice::DPMT_IMPORT) {
				$bl->desc = 'Import';
				$bl->type = BillingLine::BILLING_TYPE_IMPORT;
				$bl->dpmt = Invoice::DPMT_IMPORT;
				if (in_array($bl->org_id, Org::$couriers) || $si->type == 1) {
					$bl->charge_code = Consol::AU_LOCAL_DELIVERY_COST_GL_CODE;
				} else if (in_array($bl->org_id, Org::$brokers) || $si->type == 2) {
					$bl->charge_code = 91032;
				} else if (in_array($bl->org_id, Org::$airports) || $si->type == 3) {
					$bl->charge_code = 91030;
				} else {
					$bl->charge_code = !empty($si->lines) && !empty($si->lines[0]->mdata['charge_code']) ? $si->lines[0]->mdata['charge_code'] : '';
				}
			} else if ($dpmt == Invoice::DPMT_3PL) {
				$bl->desc = '3PL';
				$bl->type = BillingLine::BILLING_TYPE_3PL;
				$bl->dpmt = Invoice::DPMT_3PL;
				$bl->charge_code = Consol::DELIVERY_3PL_COST_GL_CODE;
			}else if ($dpmt == Invoice::DPMT_COURIER_SERVICE) {
				$bl->desc = 'Top Courier Service';
				$bl->type = BillingLine::BILLING_TYPE_TOP_COURIER_SERVICE;
				$bl->dpmt = Invoice::DPMT_COURIER_SERVICE;
				$bl->charge_code = Consol::DELIVERY_COURIER_SERVCE_COST_GL_CODE;
			}
			$bl->save();
		}
	}

	private function createBlForXero($billing, $si) {
		Yii::app()->name = 'TLA';
		$supplierInvoice  = $si->parent;
		$lines = $si->parent->lines;
		$dptArrs = SystemSetting::getInvoiceRegions();
		$total = [];
		$gst = [];
		$total_ex_gst = [];
		$hasDptIds =[];
		foreach ($dptArrs as $dptId => $region)
		{
			$total[$dptId] = [Invoice::DPMT_IMPORT=>0,Invoice::DPMT_3PL=>0,Invoice::DPMT_COURIER_SERVICE=>0];
			$gst[$dptId] = [Invoice::DPMT_IMPORT=>0,Invoice::DPMT_3PL=>0,Invoice::DPMT_COURIER_SERVICE=>0];
			$total_ex_gst[$dptId] = [Invoice::DPMT_IMPORT=>0,Invoice::DPMT_3PL=>0,Invoice::DPMT_COURIER_SERVICE=>0];
		}

		foreach ($lines as $key => $line)
		{
			$dptId = 106;
			if(empty($line->ref))
			{
				$total[$dptId][Invoice::DPMT_IMPORT]+= $line->amount;
				$gst[$dptId][Invoice::DPMT_IMPORT]+= $line->gst;
				$total_ex_gst[$dptId][Invoice::DPMT_IMPORT]+= $line->amount_ex_gst;
				continue;
			}
			$siLine = SiReconcileLine::model()->find('ref = :ref and rec_id = :rec_id',[':ref'=>$line->ref,':rec_id'=>$si->id]);

			if(empty($siLine->model)||$siLine->model=='ImParcel')
			{
				$p = Shipment::model()->with('consol')->find('(ref = :ref or hbn = :ref) and t.status!=100',[":ref"=>$line->ref]);
				if(!empty($p))
				{
					$dptId = $p->ddpt_id;
					if(!empty($p->consol))
					{
						$dptId = $p->consol->dpt_id;
					}
				}
				if(empty($dptId))
				{
					$dptId = 106;
				}

				if(!empty($p->mdata['org_rate_id']))
				{
					$orgRate = OrgRate::model()->findByPk($p->mdata['org_rate_id']);
					if(!empty($orgRate->mdata['ddpt_id']))
					{
						$dptId = $orgRate->mdata['ddpt_id'];
					}
				}

				if(!empty($line->mdata['region']))
				{
					foreach(Org::$warehouse_list as $checkDptId => $value)
					{
					 	if($value==strtoupper($line->mdata['region']))
					 	{
					 		$dptId = $checkDptId;
					 	}
					}
				}


				$hasDptIds[$dptId] = $dptId;

				if(!empty($p)&&!empty($p->consol)&&$p->consol->is3PLOnly())
				{
					$total[$dptId][Invoice::DPMT_3PL]+= $line->amount;
					$gst[$dptId][Invoice::DPMT_3PL]+= $line->gst;
					$total_ex_gst[$dptId][Invoice::DPMT_3PL]+= $line->amount_ex_gst;
				}elseif(!empty($p)&&$p->isTopCourierServiceDelivery(true))
				{
					$total[$dptId][Invoice::DPMT_COURIER_SERVICE]+= $line->amount;
					$gst[$dptId][Invoice::DPMT_COURIER_SERVICE]+= $line->gst;
					$total_ex_gst[$dptId][Invoice::DPMT_COURIER_SERVICE]+= $line->amount_ex_gst;
				}else
				{
					$total[$dptId][Invoice::DPMT_IMPORT]+= $line->amount;
					$gst[$dptId][Invoice::DPMT_IMPORT]+= $line->gst;
					$total_ex_gst[$dptId][Invoice::DPMT_IMPORT]+= $line->amount_ex_gst;
				}
			}else
			{
				$p = Shipment::model()->with('consol')->find('(ref = :ref or hbn = :ref) and t.status!=100',[":ref"=>$line->ref]);
				$dptId = 106;
				if(!empty($p))
				{
					$dptId = $p->ddpt_id;
					if(!empty($p->consol))
					{
						$dptId = $p->consol->dpt_id;
					}
				}else
				{
					$cn = Consol::model()->find('no = :no or awb=:no',[':no'=>$line->ref]);
					if(!empty($cn))
					{
						$dptId = $cn->dpt_id;
					}
				}

				if(!empty($line->mdata['region']))
				{
					foreach(Org::$warehouse_list as $checkDptId => $value)
					{
					 	if($value==strtoupper($line->mdata['region']))
					 	{
					 		$dptId = $checkDptId;
					 	}
					}
				}


				$hasDptIds[$dptId] = $dptId;

				$glCode = empty($line->mdata['charge_code'])?"xx":$line->mdata['charge_code'];
				$chargeCode = Chargecode::model()->find('code = :code',[":code"=>$glCode]);
				if(empty($chargeCode))
				{
					$total[$dptId][Invoice::DPMT_IMPORT]+= $line->amount;
					$gst[$dptId][Invoice::DPMT_IMPORT]+= $line->gst;
					$total_ex_gst[$dptId][Invoice::DPMT_IMPORT]+= $line->amount_ex_gst;
				}else
				{
				    $total[$dptId][$chargeCode->dpmt]+= $line->amount;
					$gst[$dptId][$chargeCode->dpmt]+= $line->gst;
					$total_ex_gst[$dptId][$chargeCode->dpmt]+= $line->amount_ex_gst;
				}
			}
		}
		foreach ($hasDptIds as $key => $dptId) 
		{
			foreach ($total[$dptId] as $dpmt => $dpmtValue)
			{
				if(empty($dpmtValue)) continue;

				$amt_im_gst = number_format(round($gst[$dptId][$dpmt],2), 2, '.', '');
				$amt_im = number_format(round($total_ex_gst[$dptId][$dpmt],2), 2, '.', '');
				if (abs($amt_im) > 0)
				{
					$amount = [];
					if (abs($amt_im - ($amt_im_gst * 10)) < 0.01) 
					{
						$amount[] = ['amount' => number_format($amt_im, 2, '.', ''), 'gst' => 'INPUT'];
					}else
					{
						$d1 =  number_format($amt_im_gst * 10, 2, '.', '');
						$d2 = number_format($amt_im - ($amt_im_gst * 10), 2, '.', '');
						$amount[] = ['amount' => $d1, 'gst' => 'INPUT'];
						$amount[] = ['amount' => $d2, 'gst' => 'EXEMPTEXPENSES'];
					}
					$this->createBlNew($dpmt, $amount, $billing, $si,$dptId);
				}

			}
		}
	}

	private static function createBlToConsol($dpmt, $consol_no, $amount, $gst, $billing, $si,$myConsolNo=false,$glCode = false,$dptId = null) {
		Yii::app()->name = 'TLA';
		$bl = BillingLine::model()->find('billing_id = :billing_id AND `desc` = :dpmt AND billing_ref = :billing_ref AND gst = :gst AND dpt_id = :dpt_id', [':billing_id' => $billing->id, ':dpmt' => $dpmt, ':billing_ref' => $consol_no, ':gst' => $gst != 0 ? 'INPUT' : 'EXEMPTEXPENSES', ':dpt_id' => $dptId]);
		if (empty($bl)) $bl = new BillingLine;
		$bl->billing_id = $billing->id;
		$bl->org_id = $billing->org_id;
		$bl->op_id = 0;
		$bl->link_id = 0;
		$bl->to_id = 0;
		$bl->billing_cref = $billing->billing_cref;
		$bl->billing_ref = !empty($myConsolNo)?$myConsolNo:$consol_no;
		$cn = Consol::model()->find('no = :no',[":no"=>$bl->billing_ref]);
		if(!empty($dptId))
		{
			$bl->dpt_id = $dptId;
		}else
		{
			$bl->dpt_id = $cn->dpt_id;
		}

		$bl->currency = $billing->currency;
		$bl->created = $billing->created;
		$bl->date = $billing->date;
		$bl->due = $billing->due;
		$bl->transaction_date = $billing->transaction_date;
		$bl->status = Billing::BILLING_STATUS_CONFIRM;
		$bl->gst = $gst != 0 ? 'INPUT' : 'EXEMPTEXPENSES';
		if ($consol_no != 'Adjust') {
			$bl->actual_amount = $gst != 0 ? $gst * 10 : $amount;
		} else {
			$bl->actual_amount += $gst != 0 ? $gst * 10 : $amount;
		}
		$bl->actual_amount = number_format($bl->actual_amount, 2, '.', '');
		if (in_array($si->type, [2,3,4])) {
			$bl->accrual_amount = number_format($bl->actual_amount, 2, '.', '');
		}
		$bl->gst_amount = $bl->getGSTValue();
		$bl->gst_amount = number_format($bl->gst_amount, 2, '.', '');
		if ($dpmt == 'Import') {
			$bl->desc = 'Import';
			$bl->type = BillingLine::BILLING_TYPE_IMPORT;
			$bl->dpmt = Invoice::DPMT_IMPORT;
			if (in_array($bl->org_id, Org::$couriers) || $si->type == 1) {
				$bl->charge_code = Consol::AU_LOCAL_DELIVERY_COST_GL_CODE;
			} else if (in_array($bl->org_id, Org::$brokers) || $si->type == 2) {
				$bl->charge_code = 91032;
			} else if (in_array($bl->org_id, Org::$airports) || $si->type == 3) {
				$bl->charge_code = 91030;
			} else {
				$bl->charge_code = !empty($si->lines) && !empty($si->lines[0]->mdata['charge_code']) ? $si->lines[0]->mdata['charge_code'] : '';
			}
		} else if (strtoupper($dpmt) == '3PL') {
			$bl->desc = '3PL';
			$bl->type = BillingLine::BILLING_TYPE_3PL;
			$bl->dpmt = Invoice::DPMT_3PL;
			$bl->charge_code = Consol::DELIVERY_3PL_COST_GL_CODE;
		}
		if(!empty($glCode))
		{
			$bl->charge_code =$glCode;
			$glChargeCode = Chargecode::model()->find('code = :code',[":code"=>$glCode]);
			$bl->dpmt = $glChargeCode->dpmt;
			$bl->desc = $glChargeCode->name;
		}
		$bl->save();
	}

	public function toPay($split_to_consol = false)
	{
		while (true) {
			try {
				$fp = fopen(Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'system_lock' . DIRECTORY_SEPARATOR . 'xero_billing.lock', 'r');
				flock($fp, LOCK_EX);

				$this->_toPay($split_to_consol);

				flock($fp, LOCK_UN);
				fclose($fp);
				break;
			} catch (Exception $ex) {
				throw $ex;
			}
		}
	}

	public function _toPay($split_to_consol = false)
	{
		if(empty($this->parent->inv_date)||$this->parent->inv_date=='0000-00-00')
		{
			$this->addError("empty invoice date","empty invoice date");
			return;
		}
		if ($this->total == 0) return;
		Yii::app()->name = 'TLA';
		$billing = Billing::model()->find('org_id = :org_id AND billing_cref = :billing_cref AND status != 11', [':org_id' => $this->parent->org_id, ':billing_cref' => $this->parent->inv_no]);
		if (!empty($billing) && empty($billing->mdata['from_supplier_invoice'])) return;
		if (!empty($billing) &&$billing->status==Billing::BILLING_STATUS_DELETED) return;
		if (empty($billing)) {
			$billing = new Billing;
			$billing->org_id = $this->parent->org_id;
			$billing->billing_cref = $this->parent->inv_no;
			$billing->created = date('Y-m-d');
			$billing->date = $this->parent->inv_date;
			$billing->due = $this->parent->inv_date;
			$billing->transaction_date = $this->parent->inv_date;
			$billing->currency = $this->parent->currency;
			$billing->type = 0;
			$billing->status = Billing::BILLING_STATUS_CONFIRM;
			$billing->dpt_id = Org::PCAE_DEPARTMENT_SYDNEY;
			$billing->total = $this->parent->total;
			$billing->gst = $this->parent->gst;
		}
		$billing->mdata['from_supplier_invoice'] = $this->parent->id;
		$billing->save();

		if (!$split_to_consol) {
			if (empty($billing->getErrors())) $this->toPayLineToConsolNo($billing);
		} else {
			if (empty($billing->getErrors())) $this->toPayLineToConsolYes($billing);
		}
	}

	public function toPayLineToConsolNo($billing)
	{
		foreach ($billing->lines as $line) {
			$line->status = 11;
			$line->update('status');
		}
			// $amt_im_tmp = 0;
			// $amt_3pl_tmp = 0;
			// foreach ($this->lines as $line) {
			// 	if (($line->type & SiReconcileLine::TYPE_3PL) > 0) $amt_3pl_tmp += $line->value;
			// 	else $amt_im_tmp += $line->value;
			// }
			// if ($amt_im_tmp == 0 && $amt_3pl_tmp == 0) $amt_im_tmp = $this->parent->total_ex_gst;

			// // TLA supplier invoice now need to split import and 3pl// 2021-03-24
			// //if (Yii::app()->name == 'TLA') $amt_3pl_tmp = 0;

			// $amt_im = number_format($amt_im_tmp / ($amt_im_tmp + $amt_3pl_tmp) * $this->parent->total_ex_gst, 2, '.', '');
			// $amt_3pl = number_format($amt_3pl_tmp / ($amt_im_tmp + $amt_3pl_tmp) * $this->parent->total_ex_gst, 2, '.', '');

			// $amt_im_gst = number_format($amt_im_tmp / ($amt_im_tmp + $amt_3pl_tmp) * $this->parent->gst, 2, '.', '');
			// $amt_3pl_gst = number_format($amt_3pl_tmp / ($amt_im_tmp + $amt_3pl_tmp) * $this->parent->gst, 2, '.', '');

			// if (abs($amt_im) > 0) {
			// 	$amount = [];
			// 	if (abs($amt_im - $amt_im_gst * 10) < 0.1) {
			// 		$amount[] = ['amount' => $amt_im, 'gst' => 'INPUT'];
			// 	} else {
			// 		$amount[] = ['amount' => $amt_im_gst * 10, 'gst' => 'INPUT'];
			// 		$amount[] = ['amount' => number_format($amt_im - $amt_im_gst * 10, 2, '.', ''), 'gst' => 'EXEMPTEXPENSES'];
			// 	}
			// 	self::createBl('Import', $amount, $billing, $this->parent);
			// }

			// if (abs($amt_3pl) > 0) {
			// 	$amount = [];
			// 	if (abs($amt_3pl - $amt_3pl_gst * 10) < 0.1) {
			// 		$amount[] = ['amount' => $amt_3pl, 'gst' => 'INPUT'];
			// 	} else {
			// 		$amount[] = ['amount' => $amt_3pl_gst * 10, 'gst' => 'INPUT'];
			// 		$amount[] = ['amount' => number_format($amt_3pl - $amt_3pl_gst * 10, 2, '.', ''), 'gst' => 'EXEMPTEXPENSES'];
			// 	}
			// 	self::createBl('3PL', $amount, $billing, $this->parent);
			// }

			$this->createBlForXero($billing, $this);

			$billing->refresh();
			$billing->calTotal();
			$billing->checkPaid();
			$billing->nolog = true;
			$billing->dpt_id = $billing->lines[0]->dpt_id;
			$billing->update('total', 'gst', 'status','dpt_id');
			if (!empty($this->args[1])) {
				$sync = $this->args[1];
			} else {
				$sync = false;
			}
			if ($billing->sync_xero == 0 || $sync) {
				$billings = [];
				$billings[$billing->billing_cref] = $billing->lines;
				Yii::import('application.controllers.BillingController');
				$bc = new BillingController('default');
				$bc->postXero($billings);

				$billing->refresh();
				if ($billing->status == 2) {
					$bc->postXero($billings, true);
				}
			}
	}

	public function toPayLineToConsolYes($billing)
	{
		$amt_im = [];
		$amt_im_gst = [];
		$amt_3pl = [];
		$amt_3pl_gst = [];
		$amt_im_sum = 0;
		$amt_3pl_sum = 0;
		foreach ($this->lines as $line) {
			if (preg_match('/^eparcel$/i', $line->item_code) || preg_match('/^eparcel-fuel$/i', $line->item_code)) continue;

			if (($line->type & SiReconcileLine::TYPE_3PL) > 0) {
				$dpmt = '3pl';
			} else {
				$dpmt = 'im';
			}
			$glCode = "";
			if(!empty($line->mdata['charge_code']))
			{
				$glCode = $line->mdata['charge_code'];
			}

			$dptId = null;
			if(!empty($line->mdata['region']))
			{
				foreach(Org::$warehouse_list as $checkDptId => $value)
				{
				 	if($value==strtoupper($line->mdata['region']))
				 	{
				 		$dptId = $checkDptId;
				 	}
				}
			}

			// model is consol
			if (preg_match('/consol/i', $line->model)) {
				$consol = Consol::model()->findByPk($line->fid);
				if (!empty($consol)) {
					$no = $consol->no;
				} else {
					$no = 'Null';
				}
			// model is imparcel
			} else if (preg_match('/^imparcel$/i', $line->model)) {
				$shipment = Shipment::model()->findByPk($line->fid);
				if (!empty($shipment->consol)) {
					$no = $shipment->consol->no;
				} else {
					$no = 'Null';
				}
			// item_code is rts
			} else if (preg_match('/^rts$/i', $line->item_code)) {
				$shipment = Shipment::model()->with('manif')->find('manif.ref = :ref', [':ref' => $line->ref]);
				if (!empty($shipment->consol)) {
					$no = $shipment->consol->no;
				} else {
					$no = 'Null';
				}
			// item_code is letter
			} else if (preg_match('/^letter$/i', $line->item_code)) {
				$consol = Consol::model()->find('awb = :ref1 OR json_query(t.meta, "$.elms") LIKE :ref2', [':ref1' => $line->ref, ':ref2' => '%' . $line->ref . '%']);
				if (!empty($consol)) {
					$no = $consol->no;
				} else {
					$no = 'Null';
				}
			} else {
				$no = 'Null';
			}

			if (empty(${'amt_' . $dpmt}[$no])) ${'amt_' . $dpmt}[$no] = [];
			if (empty(${'amt_' . $dpmt}[$no][$glCode])) ${'amt_' . $dpmt}[$no][$glCode] = 0;
			
			${'amt_' . $dpmt}[$no][$glCode] += $line->value;
			${'amt_' . $dpmt.'_sum'}+= $line->value;
			if (empty(${'amt_' . $dpmt . '_gst'}[$no])) ${'amt_' . $dpmt . '_gst'}[$no] = [];
			if (empty(${'amt_' . $dpmt . '_gst'}[$no][$glCode])) ${'amt_' . $dpmt . '_gst'}[$no][$glCode] = 0;
			if ($this->parent->type == 1 && floatval(@$line->mdata['gst']) == 0) {
				${'amt_' . $dpmt . '_gst'}[$no][$glCode] += $line->value * 0.1;
			} else {
				${'amt_' . $dpmt . '_gst'}[$no][$glCode] += floatval(@$line->mdata['gst']);
			}
		}
		if ($amt_im_sum == 0 && $amt_3pl_sum == 0)
		{
			$amt_im_sum = $this->parent->total_ex_gst;
			$amt_im['Null'][''] = $this->parent->total_ex_gst;
		}
		if ($amt_im_sum > 0) {
			foreach ($amt_im as $no => $gls) {
				foreach ($gls as $myGlcode => $amount) {
					self::createBlToConsol('Import', $no, $amount, floatval(@$amt_im_gst[$no][$myGlcode]), $billing, $this->parent,false,$myGlcode,$dptId);
					if (@$amt_im_gst[$no][$myGlcode]>0&&$amount > floatval(@$amt_im_gst[$no][$myGlcode]) * 10) {
						self::createBlToConsol('Import', $no, $amount - floatval(@$amt_im_gst[$no][$myGlcode]) * 10, 0, $billing, $this->parent,false,$myGlcode,$dptId);
					}
				}
			}
		}

		if ($amt_3pl_sum > 0) {
			foreach ($amt_3pl as $no => $gls) {
				foreach ($gls as $myGlcode => $amount) {
					self::createBlToConsol('3PL', $no, $amount, floatval(@$amt_3pl_gst[$no][$myGlcode]), $billing, $this->parent,false,$myGlcode,$dptId);
					if (@$amt_3pl_gst[$no][$myGlcode]>0&&$amount > floatval(@$amt_3pl_gst[$no][$myGlcode]) * 10) {
						self::createBlToConsol('3PL', $no, $amount - floatval(@$amt_3pl_gst[$no][$myGlcode]) * 10, 0, $billing, $this->parent,false,$myGlcode,$dptId);
					}
				}
			}
		}

		$billing->refresh();
		$billing->calTotal();
		$billing->checkPaid();
		$billing->nolog = true;
		$billing->update('total', 'gst', 'status');

		// gst adjust
		$bg = number_format($billing->gst, 2, '.', '');
		$sg = number_format($this->parent->gst, 2, '.', '');
		if (number_format(abs($bg - $sg), 2, '.', '') >= 0.01) {
			self::createBlToConsol('Import', 'Adjust', number_format(($sg - $bg) * 10, 2, '.', ''), number_format($sg - $bg, 2, '.', ''), $billing, $this->parent,$this->getBrokerConsolNo(),$myGlcode,$dptId);
		}
		$billing->refresh();
		$billing->calTotal();
		$billing->checkPaid();
		$billing->nolog = true;
		$billing->update('total', 'gst', 'status');

		// total adjust
		$bt = number_format($billing->total, 2, '.', '');
		$st = number_format($this->parent->total, 2, '.', '');
		if (number_format(abs($bt - $st), 2, '.', '') >= 0.01) {
			self::createBlToConsol('Import', 'Adjust', number_format($st - $bt, 2, '.', ''), 0, $billing, $this->parent,$this->getBrokerConsolNo(),$myGlcode,$dptId);
		}
		$billing->refresh();
		$billing->calTotal();
		$billing->checkPaid();
		$billing->nolog = true;
		$billing->dpt_id = $billing->lines[0]->dpt_id;
		$billing->update('total', 'gst', 'status','dpt_id');

		if (!empty($this->args[1])) {
			$sync = $this->args[1];
		} else {
			$sync = false;
		}
		if ($billing->sync_xero == 0 || $sync) {
			$billings = [];
			$billings[$billing->billing_cref] = $billing->lines;
			Yii::import('application.controllers.BillingController');
			$bc = new BillingController('default');
			$bc->postXero($billings);

			$billing->refresh();
			if ($billing->status == 2) {
				$bc->postXero($billings, true);
			}
		} else {
			foreach ($billing->lines as $line) {
				if ($line->status == Billing::BILLING_STATUS_POSTED && $line->sync_xero == 1) continue;

				$line->status = Billing::BILLING_STATUS_POSTED;
				$line->sync_xero = 1;
				$line->update('status', 'sync_xero');
			}
		}
	}

	public function getSubErrorType($symbol = '</br>')
	{
		$sql = 'SELECT type FROM si_reconcile_line WHERE rec_id = '.$this->id.' group by type ';
		$c = Yii::app()->db->createCommand($sql);
		$data = $c->queryAll();
		$errorTypes = [];
		foreach ($data as $key => $line) 
		{
			if($line['type']>0)
			{
				foreach(SiReconcileLine::$type as $key => $value)
				{
					if(($line['type']&$key)>0)
					{
						$errorTypes[$line['type']] = $value;
					}
				}
			}
		}
		return join($symbol,$errorTypes);
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
	public function search($pgn = true, $ps = 30, $ec = false, $defaultOrder = true)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;
		$criteria->compare('t.supplier_invoice_id',$this->supplier_invoice_id);
		$criteria->compare('t.type',$this->type);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.flag',$this->flag);
		$criteria->compare('t.meta',$this->meta,true);
		$criteria->compare('t.total',$this->total,true);
		$criteria->compare('t.total_gst',$this->total_gst,true);
		$criteria->compare('t.total_ex_gst',$this->total_ex_gst,true);
		$criteria->compare('t.org_id',$this->org_id,true);
		$with = ['parent'];
		if(empty($this->status))
		{
			$criteria->addCondition('t.status != 100');
		}

		if(!empty($this->create))
		{
			$criteria->addCondition('t.create like "%'.$this->create.'%"');
		}
		if (!empty($this->refs)) {
			$with[] = 'lines';
			$ns = preg_split('/[\s,;]+/', trim($this->refs));
			if (sizeof($ns) > 200) {
				$ns = array_slice($ns, 0, 200);
			}
			foreach ($ns as $key => $nv) {
				$ns[$key] = trim($ns[$key]);
				if(empty($nv))
				{
					unset($ns[$key]);
				}
			}
			$sc1 = new CDbCriteria;
			$criteria->compare('lines.ref',$ns);
			// $with[] = 'lines.imparcel.consol';
			// $criteria->compare('consol.no', $ns, false, 'OR');
			// $criteria->compare('consol.awb', $ns, false, 'OR');
		}

		if(!empty($this->inv_no))
		{
			$criteria->compare('parent.inv_no',$this->inv_no);
		}
		$criteria->compare('parent.inv_date',$this->inv_date,true);

		if(!empty($this->confirm_status))
		{
			if(preg_match('/>|=|</', $this->confirm_status))
			{
				$criteria->compare('t.confirm_status',$this->confirm_status,true);
			}else
			{
				$criteria->addCondition('t.confirm_status & '.$this->confirm_status." > 0 ");
			}
		}
		$criteria->with = $with;

		$sort = new CSort(get_called_class());
		$sort->attributes = [
			'inv_no' => array(
				'asc' => 'parent.inv_no',
				'desc' => 'parent.inv_no desc',
			),
			'inv_date' => array(
				'asc' => 'parent.inv_date',
				'desc' => 'parent.inv_date desc',
			),
			'*',
		];
		if ($defaultOrder) {
			$sort->defaultOrder = 't.id desc';
		} else {
			$sort->defaultOrder = 't.id desc';
		}
		$sob = $sort->getOrderBy();
		$sobs = preg_split('/, */', $sob);
		foreach ($sobs as $ob) {
			if (!strpos($ob, '.')) {
				continue;
			}
			list($m, $f) = explode('.', $ob);
			if (str_replace('`', '', $m) == 't') {
				continue;
			}
			$with[] = $m;
		}

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}
		// in case sub-gridview, we need consol_id set by parent grid view
		$pagerparams = $_GET;

		return new CActiveDataProvider($this, [
			'criteria' => $criteria,
			'sort' => $sort,
			'pagination' => $pgn ? [
				'pageSize' => $ps,
				'params' => $pagerparams,
			] : false,
		]);
	}

	public function getAllDpmt()
	{
		$dpmts = [];
		foreach ($this->dpmts as $key => $d) {
			$dpmts[$d->dpmt] = Invoice::$dpmts[$d->dpmt];
		}

		if(empty($dpmts))
		{
			$dpmts[Invoice::DPMT_IMPORT] = Invoice::$dpmts[Invoice::DPMT_IMPORT];
		}

		return $dpmts;
	}

	public function getLines($dpmt)
	{
		if(!empty($this->dpmtLines)) return $this->dpmtLines;
		foreach ($this->lines as $key => $line)
		{
			if($dpmt==Invoice::DPMT_IMPORT)
			{
				if(($line->type&SiReconcileLine::TYPE_3PL)>0) continue;
			}else if($dpmt==Invoice::DPMT_3PL)
			{
				if(($line->type&SiReconcileLine::TYPE_3PL)==0) continue;
			}

			$this->dpmtLines[] = $line;
		}
		return $this->dpmtLines;
	}

	public static function getDpmtLines($thisLines,$dpmt)
	{
		$dpmtLines = [];
		foreach ($thisLines as $key => $line)
		{
			if($dpmt==Invoice::DPMT_3PL)
			{
				if(($line->type&SiReconcileLine::TYPE_3PL)==0) continue;
			}else if($dpmt==Invoice::DPMT_COURIER_SERVICE)
			{
				if(($line->type&SiReconcileLine::TYPE_TLD)==0) continue;
			}else if($dpmt==Invoice::DPMT_IMPORT)
			{
				if(($line->type&SiReconcileLine::TYPE_3PL)>0||($line->type&SiReconcileLine::TYPE_TLD)>0) continue;
			}

			$dpmtLines[] = $line;
		}
		return $dpmtLines;
	}

	public function getBrokerConsolNo()
	{
		foreach ($this->lines as $key => $li)
		{
			$s = Shipment::model()->find('status != 100 and (ref = :ref or hbn = :ref)',[":ref"=>$li->ref]);
			if(!empty($s))
			{
				$c = Consol::model()->findByPk($s->consol_id);
			}else
			{
				$c = Consol::model()->find('no = :no',[":no"=>$li->ref]);
			}
			return empty($c)?"":$c->no;
		}
		return "";
	}

	public function getSiReconcileConsolIds()
	{
		$consolIds = [];
		$thisLines = SiReconcileLine::model()->with(['imparcel'])->findAll('t.rec_id = :recId',[":recId"=>$this->id]);
		foreach ($thisLines as $key => $line)
		{
			if(preg_match('/consol/i', $line->model))
			{
				$consolIds[] = $line->fid;
			}else
			{
				if(!empty($line->imparcel))
				{
					$consolIds[] = $line->imparcel->consol_id;
				}
			}
		}

		return array_unique($consolIds);
	}

	public function checkSyncBilling()
	{
		$billing = Billing::model()->count('org_id = :org_id AND billing_cref = :billing_cref AND status != 11', [':org_id' => $this->parent->org_id, ':billing_cref' => $this->parent->inv_no]);
		if($billing>0)
		{
			return true;
		}else
		{
			return false;
		}
	}

	public function updateSiReconcileSummary()
	{
		if(empty($this->mdata['tla_accrued']))
		{
			$tlaAccrued = 0;
			$customerCharged = 0;
			foreach ($this->lines as $key => $line) {
				if($line->item_code=="eparcel"||$line->item_code=="eparcel-fuel") continue;
				if($line->my_value>0)
				{
					$tlaAccrued+=$line->my_value;
				}else
				{
					$tlaAccrued+=$line->value;
				}

				if($line->item_code=="item"&&$line->model=="ImParcel")
				{
					$customerCharged+=$line->my_charge;
				}
			}
			$this->mdata['tla_accrued'] = $tlaAccrued;
			$this->mdata['charged_customer'] = $customerCharged;
			$this->update(['meta']);
		}
	}

	public function getSiReconcileSummary()
	{
		$this->updateSiReconcileSummary();
		$tlaAccrued = $this->mdata['tla_accrued'];
		$customerCharged = $this->mdata['charged_customer'];
		$recharged = 0;
		if(!empty($this->mdata['allWeightDiffAmount']))
		{
			$customerCharged+=$this->mdata['allWeightDiffAmount'];
			$recharged+=$this->mdata['allWeightDiffAmount'];
		}
		if(!empty($this->mdata['allSurchargeAmount']))
		{
			$customerCharged+=$this->mdata['allSurchargeAmount'];
			$recharged+=$this->mdata['allSurchargeAmount'];
		}

		return [$tlaAccrued,$customerCharged,$this->mdata['charged_customer'],$recharged];
	}

	public function getTlaAccured()
	{
		if(empty($this->summary))
		{
			$this->summary = $this->getSiReconcileSummary();
		}
		return $this->summary[0];
	}

	public function getChargedCustomer()
	{
		if(empty($this->summary))
		{
			$this->summary = $this->getSiReconcileSummary();
		}
		return $this->summary[1];
	}

	public function getChargedCustomerOnly()
	{
		if(empty($this->summary))
		{
			$this->summary = $this->getSiReconcileSummary();
		}
		return $this->summary[2];
	}

	public function getRecharged()
	{
		if(empty($this->summary))
		{
			$this->summary = $this->getSiReconcileSummary();
		}
		return $this->summary[3];
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SiReconcile the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
