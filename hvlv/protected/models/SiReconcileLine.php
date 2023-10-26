<?php

/**
 * This is the model class for table "si_reconcile_line".
 *
 * The followings are the available columns in table 'si_reconcile_line':
 * @property string $id
 * @property integer $rec_id
 * @property string $ref
 * @property integer $type
 * @property integer $fid
 * @property string $model
 * @property string $charge_code
 * @property string $weight
 * @property double $courier_cubic
 * @property double $cust_check_weight
 * @property double $our_charge_weight
 * @property double $manifest_weight
 * @property string $value
 * @property string $my_value
 * @property string $my_value_m
 * @property string $my_charge
 * @property string $invoice_no
 * @property string $postcode
 * @property integer $agent_id
 * @property double $cs_charge_weight
 * @property double $weight_diff
 * @property double $org_charge_weight
 * @property string $meta
 * @property integer $confirm_status
 */
class SiReconcileLine extends OMetaModel
{
	const EMPTY_PARCEL_TYPE = 1;
	const CHANGED_LABEL_TYPE = 2;
	const EMPTY_ORG_RATE_TYPE = 4;
	const EMPTY_CONSOL = 8;
	const FOUND_IN_OTHER = 16;
	const TYPE_3PL = 32;
	const TYPE_TLD = 128;
	const NOT_FOUND_RTS = 64;
	const CUBICRATE = 250;
	public $itemCodes = [];
	public $itemCodesNot = [];
	public $oldInvoiceRecord = null;
	public $dpmt,$isDispute,$surchargeInvoice;

	//this is the error type
	public static $type = [
		1=>'Empty Parcel',
		2=>'Changed Label',
		4=>'Empty Org Rate',
		8=>'Empty Consol',
		16=>'Found in other',
		32=>'3PL',
		64=>'Not Found RTS'
	];
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'si_reconcile_line';
	}
	

	public function getDbConnection(){
		return self::getTlaConnection();
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('rec_id, our_charge_weight, manifest_weight, value, my_value_m', 'required'),
			array('rec_id, type, agent_id, confirm_status', 'numerical', 'integerOnly'=>true),
			array('courier_cubic, cust_check_weight, our_charge_weight, manifest_weight, cs_charge_weight, weight_diff, org_charge_weight', 'numerical'),
			array('model, charge_code', 'length', 'max'=>45),
			array('ref', 'length', 'max'=>50),
			array('postcode,value, my_value, my_value_m, my_charge,weight', 'length', 'max'=>20),
			array('item_code', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('rec_id, ref, type, fid, model, charge_code, weight, courier_cubic, cust_check_weight, our_charge_weight, manifest_weight, value, my_value, my_value_m, my_charge, postcode, agent_id, cs_charge_weight, weight_diff, org_charge_weight, meta, confirm_status,inline_pid,item_code', 'safe', 'on'=>'search'),
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
			'parent'=>[self::BELONGS_TO, 'SiReconcile', 'rec_id'],
			'imparcel'=>[self::BELONGS_TO,'ImParcel','fid'],
			'apmanifest'=>[self::BELONGS_TO,'SiReconcileLine','inline_pid'],
			'inlines'=>[self::HAS_MANY,'SiReconcileLine','inline_pid']
		);
	}

	public function getErrorTypes($symbol = '</br>')
	{
		$errorTypes = [];
		foreach(self::$type as $key => $value) {
			if(($this->type&$key)>0)
			{
				if($key==self::FOUND_IN_OTHER)
				{
					$errorTypes[] = $value."(".join(',',$this->mdata['found_invoice']).")";
				}else
				{
					$errorTypes[] = $value;
				}
			}
		}
		return join($symbol,$errorTypes);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'rec_id' => 'Rec',
			'ref' => 'Ref',
			'type' => 'Type',
			'fid' => 'Fid',
			'model' => 'Model',
			'charge_code' => 'Charge Code',
			'weight' => 'Weight',
			'courier_cubic' => 'Courier Cubic',
			'cust_check_weight' => 'Cust Check Weight',
			'our_charge_weight' => 'Our Charge Weight',
			'manifest_weight' => 'Manifest Weight',
			'value' => 'Value/供应商实际Cost',
			'my_value' => 'My Value/系统预估Cost',
			'my_value_m' => 'My Value M',
			'my_charge' => 'TLA Invoice/系统预估收费',
			'invoice_no' => 'Invoice No',
			'postcode' => 'Postcode',
			'agent_id' => 'Agent',
			'cs_charge_weight' => 'Cs Charge Weight',
			'weight_diff' => 'Weight Diff',
			'org_charge_weight' => 'Org Charge Weight',
			'meta' => 'Meta',
			'confirm_status' => 'Confirm Status',
			'item_code'=>'Item Code'
		);
	}
	public function getIsConfirmed($symbol = '</br>',$dpmt = null)
	{
		if($this->parent->status==SiReconcile::SI_RECONCILE_DONE_STATUS) return "";

		if(!empty($dpmt))
		{
			$dpmtParent = SiReconcileDpmt::model()->find("si_reconcile_id = :recId and dpmt=:dpmt",[":recId"=>$this->rec_id,":dpmt"=>$dpmt]);
			if(($this->confirm_status&SiReconcile::$confirmed_status_check[$dpmtParent->status])>0)
			{
				return 'Yes';
			}
		}else
		{
			if(($this->confirm_status&SiReconcile::$confirmed_status_check[$this->parent->status])>0)
			{
				return 'Yes';
			}
		}
		
	}
	public function getFullConfirmStatus($symbol = '</br>')
	{
		$status = [];
		foreach(SiReconcile::$confirmed_status as $key => $value) {
			if(($this->confirm_status&$key)>0)
			{
				$status[] = $value;
			}
		}
		return join($symbol,$status);
	}

	public  function getCustomerWeight()
	{
		if($this->isPureCBMSiReconcileLine())
		{
			return empty($this->imparcel)? 0 : $this->imparcel->getTotalCBM();//this is for calculating the CBM pricing
		}else
		{
			return empty($this->imparcel)? 0 : $this->imparcel->weight;
		}
	}

	public  function getBulkyWeight()
	{
		if($this->isCBMSiReconcileLine())
		{
			if($this->isPureCBMSiReconcileLine())
			{
				$bulkyWeight = $this->imparcel->weight/$this->getCubicRate();//this is for calculating the CBM pricing
			}else
			{
				$bulkyWeight = $this->getCustomerCBM()*$this->getCubicRate();
			}
			return round($bulkyWeight,3);
		}else
		{
			return 0;
		}
	}

	/**
	 * get the getCSChargeWeight
	 * @return float
	 */
	public function getCSChargeWeight($reset = false)
	{
		$csChargeWeight = $this->cs_charge_weight;
		if(empty($csChargeWeight)||$reset == true)
		{
			$this->cs_charge_weight = round($this->getBulkyWeight()>$this->getCustomerWeight()?$this->getBulkyWeight():$this->getCustomerWeight(),3);

			if (!empty($this->imparcel)) 
			{
				if(!$this->isCBMSiReconcileLine())
				{
					$weightInRangeCharge = $this->imparcel->getChargeByChargecode(ImParcelService::getImparcelChargecode($this->imparcel),false,$this->cs_charge_weight,true)['rate'];
					$thisWeightCharge = $weightInRangeCharge['perkg']>0?$this->cs_charge_weight:$weightInRangeCharge['weight_hi'];
					$this->cs_charge_weight = $thisWeightCharge*$this->imparcel->pkg;
				}
			}
			if(empty($this->cs_charge_weight))$this->cs_charge_weight = 0;
			$this->cs_charge_weight = round($this->cs_charge_weight,3);
			return $this->cs_charge_weight;
		}else
		{
			return $csChargeWeight;
		}
	}
	public function isCBMSiReconcileLine()
	{
		if(in_array($this->parent->org_id,SiReconcile::$cbmCourier)||$this->getChangeForCBMStatus()||$this->imparcel->checkIsCargoProcessWithRef())
		{
			if(preg_match('/^(33G7K|33G7L|349PU|349PV)\d{7}/', $this->ref))
			{
				return false;
			}
			return true;
		}else
		{
			return false;
		}
	}

	public function isPureCBMSiReconcileLine($type=true)
	{
		if(in_array($this->parent->org_id,SiReconcile::$pureCbmCourier)||($this->getChangeForPureCBMStatus()&&$type))
		{
			return true;
		}else
		{
			return false;
		}
	}

	public function isAllCBMSireconcileLine()
	{
		return ($this->isCBMSiReconcileLine()||$this->isPureCBMSiReconcileLine());
	}

	private function getChangeForCBMStatus()
	{
		$cref = ChangeShipmentLabel::model()->find('newref=:newref',[":newref"=>$this->ref]);
		if(!empty($cref))
		{
			if(in_array(ShipmentScan::checkRefCourierId($cref->pref),SiReconcile::$cbmCourier))
			{
				return true;
			}
		}
		return false;
	}

	public function getChangeForPureCBMStatus()
	{
		$cref = ChangeShipmentLabel::model()->find('newref=:newref',[":newref"=>$this->ref]);
		if(!empty($cref))
		{
			if(in_array(ShipmentScan::checkRefCourierId($cref->pref),SiReconcile::$pureCbmCourier))
			{
				return true;
			}
		}
		return false;
	}

	public function getWeight($reset = false)
	{
		if($this->org_charge_weight == null||$reset == true)
		{
			if (!empty($this->imparcel)) 
			{
				if($this->isCBMSiReconcileLine())
				{
					$this->org_charge_weight = $this->getCdeadwtOrWeight();
				}else
				{
					$weightInRangeCheck = $this->imparcel->getChargeByChargecode(ImParcelService::getImparcelChargecode($this->imparcel),false,$this->weight,true)['rate'];
					$thisWeightCheck = $weightInRangeCheck['perkg']>0?$this->getCdeadwtOrWeight():$weightInRangeCheck['weight_hi'];
					$this->org_charge_weight = $thisWeightCheck*$this->imparcel->pkg;


				}
			}else
			{
				$this->org_charge_weight =  $this->getCdeadwtOrWeight();
			}

			if(empty($this->org_charge_weight))$this->org_charge_weight = 0;
			$this->org_charge_weight = round($this->org_charge_weight,3);
			return $this->org_charge_weight;
		}else
		{
			return $this->org_charge_weight;
		}
	}

	public  function getCustomerCBM()
	{
		if (!empty($this->imparcel)) {
			if(isset($this->imparcel->mdata['total_cbm']))
			{
				return round($this->imparcel->mdata['total_cbm'],3);
			}else
			{
				return round($this->imparcel->cbm * $this->imparcel->pkg,3);
			}
		}
		return 0;
	}

	public function getCubicRate()
	{
		// if (!empty($this->shipment)) {
		// 	return $this->shipment->getCubicRate();
		// }
		return 250;
	}

		/**
	 * get the charge weight for the linked Parcel
	 * @return float
	 */
	public function ourChargeWeight(){
		$thisWeight = 0;
		if($this->our_charge_weight>0){
		 	 $thisWeight = $this->our_charge_weight;

		}else{
		   if(!empty($this->imparcel)){
		   		 $thisWeight =$this->imparcel->chargeWeight();
		   }
		}


		// if(in_array($this->parent->getType(),Reconciliation::$cbmCourier))
		// {
		// 	 return $thisWeight;
		// }
		// else
		// {
		// 	$weightInRangeCheck = $imparcel->getChargeByChargecode('',false,$thisWeight,true)['rate'];
		// 	return $weightInRangeCheck['perkg']>0?$thisWeight:$weightInRangeCheck['weight_hi'];
		// }
		return $thisWeight;

		//return 0;
	}

	/**
	 * get the customerShouldChargeWeight
	 * @return float
	 */
	public function getChargeWeightDiff($reset = false)
	{
		if($this->weight_diff == null||$reset == true)
		{
			$weightDiff = 0;
			if (!empty($this->imparcel)) 
			{
				$weightDiff = $this->getWeight($reset)-$this->getCSChargeWeight($reset);
			}
			$this->weight_diff = $weightDiff;
			$this->weight_diff = round($this->weight_diff,3);
			return $this->weight_diff;
		}else
		{
			return $this->weight_diff;
		}
	}

	public function getWeightDiffInvoice()
	{
		if($this->oldInvoiceRecord===false)
		{
			return null;
		}else if(!empty($this->oldInvoiceRecord))
		{
			return $this->oldInvoiceRecord;
		}
		$appName = Yii::app()->name;
		if(!empty($this->imparcel->id))
		{
			$ccode=InvLine::WEIGHTCCODEAUTO;
			$oldRecord = Invoice::model()->with('lines')->find(["condition"=>"((lines.fid=:fid and lines.model='Shipment') or (t.meta like '%pid".$this->imparcel->id."%' and t.meta like '%{$ccode}%')) and (t.type = :type or t.type= :typeo) and t.status != :status1 and t.status != :status3","params"=>[":fid"=>$this->imparcel->id,":type"=>Invoice::INVOICE_TYPE_WEIGHT_DIFF,":typeo"=>Invoice::INVOICE_TYPE_OTHERS,":status1"=>Invoice::INVOICE_STATUS_CACELLED,":status3"=>Invoice::INVOICE_STATUS_FULLY_CREDITED],"order"=>"t.id desc"]);
			Yii::app()->name = $appName;

			if($oldRecord!=null)
			{
				$this->oldInvoiceRecord = $oldRecord;
				return $oldRecord;
			}else
			{
				$this->oldInvoiceRecord = false;
				return null;
			}
		}
		$this->oldInvoiceRecord = false;
		return null;
	}

	public function getSurchargeInvoice()
	{
		if($this->surchargeInvoice===false)
		{
			return null;
		}else if(!empty($this->surchargeInvoice))
		{
			return $this->surchargeInvoice;
		}

		if(!empty($this->imparcel->id))
		{
			$oldRecord = null;
			$ccode=InvLine::SURCHARGECCODE;
			$itemCode = empty(InvLine::SURCHARGECODEARR[$this->item_code])?$this->item_code:InvLine::SURCHARGECODEARR[$this->item_code];
			if(!empty($this->mdata["surcharge_inv_id"]))
			{
				$oldRecord = Invoice::model()->with('lines')->findByPk($this->mdata["surcharge_inv_id"]);
				$this->surchargeInvoice = $oldRecord;
				return $oldRecord;
			}

			$oldRecord = Invoice::model()->with('lines')->find(["condition"=>"t.meta like '%pid".$this->imparcel->id."%' and t.meta like '%{$ccode}%' and (t.type = :type or t.type= :typeo) and t.status != :status1 and t.status != :status3 and lines.ccode = :ccode","params"=>[":type"=>Invoice::INVOICE_TYPE_WEIGHT_DIFF,":typeo"=>Invoice::INVOICE_TYPE_OTHERS,":status1"=>Invoice::INVOICE_STATUS_CACELLED,":status3"=>Invoice::INVOICE_STATUS_FULLY_CREDITED,":ccode"=>$itemCode],"order"=>"t.id desc"]);

			if(empty($oldRecord))
			{
				if(preg_match('/MH|OS/i', $this->item_code))
				{
					$surcharge=$this->imparcel->getChargeByChargecode(ImParcelService::getImparcelChargecode($this->imparcel),false,null,true,false,true);
					if(!empty($surcharge['MHCharge']+$surcharge['OSCharge']))
					{
						$oldRecord = Invoice::model()->find(" type in (10,104) and meta like '%".$this->imparcel->ref."%' and status not in (8,10,99)");
					}
				}
			}

			if($oldRecord!=null)
			{
				$this->surchargeInvoice = $oldRecord;
				return $oldRecord;
			}else
			{
				$this->surchargeInvoice = false;
				return null;
			}
		}
		$this->surchargeInvoice = false;
		return null;
	}

	public function getOtherChargeInvoiceLine()
	{
		if(!empty($this->imparcel->id))
		{
			$li = InvLine::model()->findAll("det=:det or det=:det2 or det like '".$this->imparcel->ref.'/actual weight'."%'",[":det"=>$this->imparcel->ref.'/MHP',":det2"=>$this->imparcel->ref.'/SURCHARGE']);
			if($li!=null)
			{
				return $li;
			}else
			{
				return null;
			}
		}
		return null;
	}

	public function getCdeadwtOrWeight()
	{
		if($this->isPureCBMSiReconcileLine())
		{
			$courierCubic = ($this->weight/250)>$this->courier_cubic?($this->weight/250):$this->courier_cubic;
			return $courierCubic;
		}else
		{
			return $this->cdeadwt>0&&$this->cdeadwt<99999?$this->cdeadwt:$this->weight;
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
	public function search($pgn = true, $ps = 30, $ec = false, $defaultOrder = true)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id,true);
		$criteria->compare('t.rec_id',$this->rec_id);
		$criteria->compare('t.type',$this->type);
		$criteria->compare('t.fid',$this->fid);
		$criteria->compare('t.model',$this->model,true);
		$criteria->compare('t.charge_code',$this->charge_code,true);
		$criteria->compare('t.weight',$this->weight,true);
		$criteria->compare('t.courier_cubic',$this->courier_cubic);
		$criteria->compare('t.cust_check_weight',$this->cust_check_weight);
		$criteria->compare('t.our_charge_weight',$this->our_charge_weight);
		$criteria->compare('t.manifest_weight',$this->manifest_weight);
		$criteria->compare('t.value',$this->value,true);
		$criteria->compare('t.my_value',$this->my_value,true);
		$criteria->compare('t.my_value_m',$this->my_value_m,true);
		$criteria->compare('t.my_charge',$this->my_charge,true);
		$criteria->compare('t.postcode',$this->postcode,true);
		$criteria->compare('t.agent_id',$this->agent_id);
		$criteria->compare('t.cs_charge_weight',$this->cs_charge_weight);
		$criteria->compare('t.weight_diff',$this->weight_diff);
		$criteria->compare('t.org_charge_weight',$this->org_charge_weight);
		$criteria->compare('t.item_code',$this->item_code);
		$criteria->compare('t.meta',$this->meta,true);
		$criteria->compare('t.item_code',$this->itemCodes);
		$criteria->compare('t.inline_pid',$this->inline_pid);
		$with = [];

		if(!empty($this->isDispute))
		{
			if(empty($this->id))
			{
				$criteria->addCondition('t.confirm_status in (0,1,2,3) or (t.item_code="item" and t.confirm_status=10) or (t.value != json_value(t.meta,"$.confirm_cost_ex_gst") and json_value(t.meta,"$.confirm_cost_ex_gst")  is not null)');
			}
		}else
		{
			$criteria->compare('t.confirm_status',$this->confirm_status);
		}


		if(!empty($this->ref)&&$this->parent->org_id == Org::ORGID_COURIER_AUPOST&&empty($this->inline_pid))
		{	
			$db = null;
			if(Yii::app()->name == "TLA")
			{
				$db = Yii::app()->db_tla;
			}else
			{
				$db = Yii::app()->db;
			}
				$sql = "SELECT * from (SELECT t.* from (select * from `si_reconcile_line` `t` where t.rec_id = {$this->rec_id} and inline_pid =0) t left join (select * from `si_reconcile_line` `t` where t.rec_id = {$this->rec_id} and inline_pid >0) s on t.id=s.inline_pid where (t.item_code not in ('item','fuel')) and (s.ref = '{$this->ref}')) t group by id";
				$data1 = $db->createCommand($sql)->queryAll();
				$sql = "SELECT * from (SELECT t.* from (select * from `si_reconcile_line` `t` where t.rec_id = {$this->rec_id} and inline_pid =0) t left join (select * from `si_reconcile_line` `t` where t.rec_id = {$this->rec_id} and inline_pid >0) s on t.id=s.inline_pid where (t.item_code not in ('item','fuel')) and (t.ref = '{$this->ref}')) t group by id";
				$data2 = $db->createCommand($sql)->queryAll();

				$ids = [];
				if(!empty($data1))
				{
					$ids = array_merge($ids,array_column($data1, 'id'));
				}
				if(!empty($data2))
				{
					$ids = array_merge($ids,array_column($data2, 'id'));
				}
			$criteria->compare('t.id',$ids,true);
		}else
		{
			$criteria->compare('t.ref',$this->ref);
		}
		if(isset($this->itemCodesNot))
		{
			$criteria->addCondition('t.item_code not in ("'.join('","',$this->itemCodesNot).'")');
		}

		if($this->dpmt==Invoice::DPMT_IMPORT)
		{
			$criteria->addCondition('(t.type & '.self::TYPE_3PL.") =0 ".' and (t.type & '.self::TYPE_TLD.") =0 ");
		}

		if($this->dpmt==Invoice::DPMT_3PL)
		{
			$criteria->addCondition('(t.type & '.self::TYPE_3PL.") >0");
		}

		if($this->dpmt==Invoice::DPMT_COURIER_SERVICE)
		{
			$criteria->addCondition('(t.type & '.self::TYPE_TLD.") >0");
		}

		$sort = new CSort(get_called_class());
		$sort->attributes = [
			'diff'=> [
					'asc' => 't.value-t.my_value',
			 		'desc' => 't.value-t.my_value DESC',
			],
			'type'=>[
				'asc' => 't.type',
			 	'desc' => 't.type DESC',
			],
			'value'=>[
				'asc' => 't.value',
			 	'desc' => 't.value DESC',
			],
			'my_value'=>[
				'asc' => 't.my_value',
			 	'desc' => 't.my_value DESC',
			],
			'api_value'=>[
				'asc' => 't.api_value',
			 	'desc' => 't.api_value DESC',
			],
			'weight'=>[
				'asc' => 't.weight',
			 	'desc' => 't.weight DESC',
			],
			'weight_diff'=>[
				'asc' => 't.weight_diff',
			 	'desc' => 't.weight_diff DESC',
			],
			'item_code'=>[
				'asc' => 't.item_code',
			 	'desc' => 't.item_code DESC',
			]
		];
		$sort->attributes['diff'] = [
					'asc' => 't.value-t.my_value',
			 		'desc' => 't.value-t.my_value DESC',
		];
		if ($defaultOrder) {
			$sort->defaultOrder = 't.item_code asc';
		} else {
			$sort->defaultOrder = 't.item_code asc';
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
	public function checkErrorsAndSetTypeForManual()
	{
		$consol = ImcoConsol::model()->find('(no = :no or awb=:no or json_value(meta,"$.container_no") =:no)  and type !=90 and status!=100',[":no"=>$this->ref]);
		if(empty($consol))
		{
			$consol = DmawbConsol::model()->find('(no = :no or awb=:no or json_value(meta,"$.container_no") =:no)  and type !=90 and status!=100',[":no"=>$this->ref]);
		}
		if(empty($consol))
		{
			$this->type = $this->type | SiReconcileLine::EMPTY_CONSOL;
		}else
		{
			if($consol->is3PLOnly())
			{
				$this->type = $this->type | self::TYPE_3PL;
			}
		}

		if(!empty($this->mdata['charge_code']))
		{
			$glCode = empty($this->mdata['charge_code'])?"xx":$this->mdata['charge_code'];
			$glCode = Chargecode::model()->find('code = :code',[":code"=>$glCode]);
			if(!empty($glCode))
			{
				if($glCode->dpmt==Invoice::DPMT_3PL)
				{
					$this->type = $this->type | self::TYPE_3PL;
				}

				if($glCode->dpmt==Invoice::DPMT_COURIER_SERVICE)
				{
					$this->type = $this->type | self::TYPE_TLD;
				}
			}
		}
	}

	public function checkErrorsAndSetType($checkShipments = null)
	{
		if(empty($this->ref)) return;
		if(!empty($this->imparcel)&&!empty($this->imparcel->consol)&&!empty($this->imparcel->consol->is3PLOnly()))
		{
			$this->type = $this->type | self::TYPE_3PL;
		}else
		{
			if(!empty($this->imparcel)&&!empty($this->imparcel->isTopCourierServiceDelivery(true)))
			{
				$this->type = $this->type | self::TYPE_TLD;
			}
		}


		$ob = SiReconcileLine::model()->with('parent')->findAll(['condition'=>'parent.status != 100 and ref = :ref and rec_id != :rec_id and item_code=:item_code','params' =>["ref"=>$this->ref,"rec_id"=>$this->rec_id,"item_code"=>$this->item_code], 'group' => 'parent.id']);
		if(!empty($ob))
		{
			$this->type = $this->type | self::FOUND_IN_OTHER;
		}
		$this->mdata['found_invoice'] = [];
		foreach ($ob as $key => $value) {
			$this->mdata['found_invoice'][] = $value->parent->parent->inv_no;
		}

		if(!empty($checkShipments[$this->ref.$this->item_code]))
		{
			$this->type = $this->type | self::FOUND_IN_OTHER;
			$this->mdata['found_invoice'][] = $this->parent->parent->inv_no;
		}
	}

	public function getCourierWeightInvoiceByChargeCode()
	{
		$invoice = 0;
		if (!empty($this->imparcel)) 
		{
			$invoice += $this->imparcel->getChargeByChargeCode(ImParcelService::getImparcelChargecode($this->imparcel),false,$this->getCourierWeightByChargeCode());
			if($this->imparcel->getIsIncludeGst())
			{
				$invoice = $invoice+$invoice*0.1;
			}
		}
		return round($invoice,2);
	}

	public function getShipmentCubicRate()
	{
		if (!empty($this->imparcel)) {
			return $this->imparcel->getCubicRate();
		}
		return 250;
	}

	public function getChargedInvoice($consol = false)
	{
		if (!empty($this->imparcel)) 
		{
				return $this->imparcel->getChargedInvoice($consol);
		}
		return 0;
	}

	public function getCourierWeightByChargeCode()
	{
		$courierWeight = $this->weight;
		if($this->isCBMSiReconcileLine()&&!$this->isPureCBMSiReconcileLine())
		{
			if(abs(ceil($this->courier_cubic*self::CUBICRATE)-$courierWeight)<=1)
			{
				if (!empty($this->imparcel)) {
					return $this->courier_cubic*$this->imparcel->getCubicRate();
				}
			}

			if(abs(($this->weight/self::CUBICRATE)-($this->imparcel->cbm*$this->imparcel->pkg))<=0.1)
			{
				if (!empty($this->imparcel)) {
					return ($this->weight/self::CUBICRATE)*$this->imparcel->getCubicRate();
				}
			}
		}else
		{
			// $imparcel=ImParcel::model()->find('ref=:ref',array(':ref'=> $this->shipment_no));
		 //   	if(!empty($imparcel))
		 //   	{
		 //   		 $weightInRangeCheck = $imparcel->getChargeByChargecode('',false,$courierWeight,true)['rate'];
		 //   		 return $weightInRangeCheck['perkg']>0?$courierWeight:$weightInRangeCheck['weight_hi'];
		 //   	}

			return $this->getCdeadwtOrWeight();
		}
		return $courierWeight;
	}

	public function isInBrokerField()
	{
		if($this->diff>-65.001&&$this->diff<=0)
		{
			return true;
		}

		if(($this->confirm_status&SiReconcile::RATE_CONFIRMED)>0)
		{
			return true;
		}
		return false;
	}

	public function getAirportAccural($item_code, $org_id, $weight, $uld = false, $export = false,$consolId = false)
	{
		if(preg_match('/storage/i',$item_code))
		{
			return 0;
		}

		if(empty($weight)) return 0;
		if ($org_id == 955) { // Qantas
			if(preg_match('/Import Document Fee/i',$item_code))
			{
				$amount = 69;
			}else
			{
				if(!empty($consolId))
				{
					$consol = Consol::model()->findByPk($consolId);
					$airType  = $consol->mdata['air_type'];
					if($airType==ImcoConsol::AIR_TYPE_PMC)
					{
						$amount = 167;
					}elseif($airType==ImcoConsol::AIR_TYPE_AKE)
					{
						$amount = 59;
					}elseif($airType==ImcoConsol::AIR_TYPE_DQF)
					{
						$amount = 118;
					}else
					{
						$amount = max(0.69 * floatval($weight), 69);
					}
				}
			}
		} else if ($org_id == 939) { // Menzies
			if(preg_match('/Import Document Fee/i',$item_code))
			{
				$amount = 67;
			}else
			{
				if(!empty($consolId))
				{
					$consol = Consol::model()->findByPk($consolId);
					$airType  = $consol->mdata['air_type'];
					if($airType==ImcoConsol::AIR_TYPE_PMC)
					{
						$amount =  0.24 * floatval($weight);
					}elseif($airType==ImcoConsol::AIR_TYPE_AKE)
					{
						$amount =  0.24 * floatval($weight);
					}else
					{
						$amount =  max(0.67 * floatval($weight), 67);
					}
				}
				
			}
		} else if ($org_id == 977) { // Dnata
			if(preg_match('/Import Document Fee/i',$item_code))
			{
				$amount = 67;
			}else
			{
				if(!empty($consolId))
				{
					$consol = Consol::model()->findByPk($consolId);
					$airType  = $consol->mdata['air_type'];
					if($airType==ImcoConsol::AIR_TYPE_PMC)
					{
						$amount =  0.24 * floatval($weight);
					}elseif($airType==ImcoConsol::AIR_TYPE_AKE)
					{
						$amount =  0.24 * floatval($weight);
					}else
					{
						$amount =  max(0.67 * floatval($weight), 67);
					}
				}
			}
		} else if ($org_id == 1363) { // AMI
			$amount = 0;
			if(preg_match('/terminal handling/i',$item_code))
			{
				if(!empty($consolId))
				{
					$consol = Consol::model()->findByPk($consolId);
					$airType  = $consol->mdata['air_type'];
					$ss = $consol->shipments;
					$packages = count($ss);
					$cgbWt = $consol->mdata['cgb_wt'];
					$agentId = $ss[0]->agent_id;
					if($agentId==Org::ORGID_CLIENT_GLOBAVEND)
					{
						$amount = 0.15*$packages;
						if($airType==ImcoConsol::AIR_TYPE_LOOSE)
						{
							$amount += max(170,0.9 * $cgbWt);
						}elseif($airType==ImcoConsol::AIR_TYPE_PMC)
						{
							$amount += max(90,0.4 * $cgbWt);
						}elseif($airType==ImcoConsol::AIR_TYPE_AKE)
						{
							$amount += max(90,0.4 * $cgbWt);
						}
					}elseif($agentId==Org::ORGID_CLIENT_AUSTWAY)
					{
						$amount += 60;
						$amount += ($packages-1)*5+12;
						$amount += 0.16 * $cgbWt;
						$amount += 0.6 * $cgbWt;
					}
				}
			}
		}

		return number_format($amount, 2, '.', '');
	}

	public function getItemType()
	{
		$imparcel = $this->imparcel;
		if($this->item_code=='item'&&!empty($imparcel))
		{
			if(preg_match("/LH|CH/i", $imparcel->ref))
			{
				return "intl";
			}

			if(!empty($imparcel->mdata['aupost_int']))
			{
				return "intl";
			}else if(!empty($imparcel->mdata['aupost_exp']))
			{
				return "express";
			}else if(!empty($imparcel->mdata['aupost_return']))
			{
				return 'return';
			}

			return "eparcel";
		}else
		{
			return "";
		}

	}
	public function isNormalType()
	{
		if($this->type == 0||$this->type == self::TYPE_3PL)
		{
			return true;
		}else
		{
			return false;
		}
	}
	public static function getUnmanifestInvoiceLine($ref)
	{
		$invLine = InvLine::model()->with(['invoice'])->find("invoice.status != 10 and det = :word1",[":word1"=>'Unmanifest '.$ref]);
		return $invLine;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SiReconcileLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
