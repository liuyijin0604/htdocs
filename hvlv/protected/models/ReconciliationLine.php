<?php

/**
 * This is the model class for table "reconciliation_line".
 *
 * The followings are the available columns in table 'reconciliation_line':
 * @property string $id
 * @property integer $parent_id
 * @property integer $consol_id
 * @property string $shipment_id
 * @property string $shipment_no
 * @property string $cdeadwt
 * @property string $weight
 * @property string $cust_check_weight
 * @property string $our_charge_weight   //the weight that we charge our customer
 * @property string $courier_cubic
 * @property string $manifest_weight    // the weight we log to Courier
 * @property string $value
 * @property string $my_value      for eparcel we calculate the cost based on our system weight
 * @property string $my_value_m    for eParcel we calculate the cost based on manifest weith
 * @property string $my_charge
 * @property string $invoice_no
 * @property string $postcode
 */
class ReconciliationLine extends MetaModel
{
	public $subAmount = 0;
	public $consol_no;
	public $customerCbm;
	public $customerWeight;
	public $bulkyWeight;
	public $csChargeWeight;
	public $chargeWeightDiff;
	public $chargeInvoiceDiff;
	public $cubicRate;
	public $agentId;
	public $tnt_type;
	public $st_type;
   	
   	const CUBICRATE = 250;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'reconciliation_line';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_id, shipment_no, weight, value, my_value, invoice_no, postcode', 'required'),
			array('parent_id, shipment_id, consol_id', 'numerical', 'integerOnly'=>true),
			array('shipment_no', 'length', 'max'=>50),
			array('weight, value, my_value,my_value_m,manifest_weight,our_charge_weight,courier_cubic', 'length', 'max'=>10),
			array('invoice_no', 'length', 'max'=>45),
			array('postcode', 'length', 'max'=>20),
			array('meta, mdata', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, parent_id,consol_no, consol_id,manifest_weight,our_charge_weight,courier_cubic,my_value_m, shipment_id, shipment_no, weight,value,cust_check_weight,my_value,my_charge,invoice_no, postcode,cdeadwt,chargeWeightDiff,chargeInvoiceDiff,subAmount,tnt_type,meta, mdata, st_type', 'safe', 'on'=>'search'),
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
			'consol' => array(self::BELONGS_TO, 'Consol', 'consol_id'),
			'parent' => array(self::BELONGS_TO, 'Reconciliation', 'parent_id'),
			'shipment' => array(self::BELONGS_TO, 'Shipment', 'shipment_id'),
		);
	}


	public  function afterFind(){
		$this->subAmount = $this->my_charge - $this->value;
		$this->postcode = $this->postcode === '0' ? '' : $this->postcode;
		parent::afterFind();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'parent_id' => 'Parent',
			'consol_id' => 'Consol',
			'shipment_id' => 'Shipment ID',
			'shipment_no' => 'Shipment No',
			'weight' => 'Weight(Kg)',
			'value' => 'Amount (Excl. GST)',
			'my_value' => 'Our Cost (Excl. GST)',
			'my_charge' => 'Our Charge (Excl. GST)',
			'invoice_no' => 'Invoice/Manifest No',
			'manifest_weight'=>'Manifest Weight',
			'courier_cubic' => 'Courier Cubic',
			'cust_check_weight'=>'Customer Dead Weight',
			'our_charge_weight' =>'Our Charge Weight',
			'postcode' => 'Postcode',
			'subAmount' => 'Diff(OA-A) (Excl. GST)',
			'customerCbm'=>'Customer CBM',
			'customerWeight'=>'Customer Weight',
			'bulkyWeight'=>'Customer Bulky Weight',
			'csChargeWeight'=>'Customer Should Charge Weight',
			'chargeWeightDiff'=>'Charge Weight Diff',
			'chargeInvoiceDiff'=>'Diff(Our Cost-Amount) (Excl. GST)',
			'cubicRate'=>'Cubic Rate',
			'agentId'=>'Agent'
		);
	}

	public function getInvoiceNoColumnData()
	{
	// in case aupost reconciliation line
	// we should show aupost related order id

		if ( $this->parent->client_type == 1 ) {
			if ( !empty($this->shipment) ) {
				if ( isset($this->shipment->trans) ) {
					if ( isset($this->shipment->trans[0]->mdata['oid']) ) {
						return $this->shipment->trans[0]->mdata['oid'];
					}
				}
			}
			return '';
		} else {
			return $this->invoice_no;
		}
	}

	public static function getManifestNo($shipment_no)
	{
		$shipment = ImParcel::model()->find('hbn = :n OR ref = :n', [':n' => $shipment_no]);
		if ( !empty($shipment) ) {
			if ( isset($shipment->trans) ) {
				if ( isset($shipment->trans[0]->mdata['oid']) ) {
					return $shipment->trans[0]->mdata['oid'];
				}
			}
		}
		return '';
	}

	public  function getCustomerCBM()
	{
		if (!empty($this->shipment)) {
			if(isset($this->shipment->mdata['total_cbm']))
			{
				return round($this->shipment->mdata['total_cbm'],3);
			}else
			{
				return round($this->shipment->cbm * $this->shipment->pkg,3);
			}
		}
		return 0;
	}


	public  function getCustomerWeight()
	{
		return empty($this->shipment)? 0 : $this->shipment->weight;
	}

	public function getCubicRate()
	{
		// if (!empty($this->shipment)) {
		// 	return $this->shipment->getCubicRate();
		// }
		return 250;
	}

	public function getShipmentAgent()
	{
		if (!empty($this->shipment)) {
			return $this->shipment->agent_id;
		}
	}

	public function getShipmentCubicRate()
	{
		if (!empty($this->shipment)) {
			return $this->shipment->getCubicRate();
		}
		return 250;
	}

	public  function getBulkyWeight()
	{
		if(in_array($this->parent->getType(),Reconciliation::$cbmCourier))
		{
			$bulkyWeight = $this->getCustomerCBM()*$this->getCubicRate();
			return round($bulkyWeight,3);
		}else
		{
			return 0;
		}
	}

	/**
	 * get the customerShouldChargeWeight
	 * @return float
	 */
	public function getCSChargeWeight($reset = false)
	{
		$csChargeWeight = $this->CS_charge_weight;
		if(empty($csChargeWeight)||$reset == true)
		{
			$this->CS_charge_weight = round($this->getBulkyWeight()>$this->getCustomerWeight()?$this->getBulkyWeight():$this->getCustomerWeight(),3);

			if (!empty($this->shipment)) 
			{
				if(!in_array($this->parent->getType(),Reconciliation::$cbmCourier))
				{
					$weightInRangeCharge = $this->shipment->getChargeByChargecode('',false,$this->CS_charge_weight,true)['rate'];
					$thisWeightCharge = $weightInRangeCharge['perkg']>0?$this->CS_charge_weight:$weightInRangeCharge['weight_hi'];
					$this->CS_charge_weight = $thisWeightCharge;
				}
			}
			if(empty($this->CS_charge_weight))$this->CS_charge_weight = 0;
			//if(empty($this->CS_charge_weight)) $this->CS_charge_weight = $this->ourChargeWeight();
			$this->update(["CS_charge_weight"]);
			return $this->CS_charge_weight;
		}else
		{
			return $csChargeWeight;
		}
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
			if (!empty($this->shipment)) 
			{
				$weightDiff = $this->getWeight($reset)-$this->getCSChargeWeight($reset);
			}
			$this->weight_diff = $weightDiff;
			$this->update(["weight_diff"]);
			return $this->weight_diff;
		}else
		{
			return $this->weight_diff;
		}
	}

	public function getWeight($reset = false)
	{
		if($this->org_charge_weight == null||$reset == true)
		{
			if (!empty($this->shipment)) 
			{
				if(in_array($this->parent->getType(),Reconciliation::$cbmCourier))
				{
					$this->org_charge_weight = $this->getCdeadwtOrWeight();
				}else
				{
					$weightInRangeCheck = $this->shipment->getChargeByChargecode('',false,$this->weight,true)['rate'];
					$thisWeightCheck = $weightInRangeCheck['perkg']>0?$this->getCdeadwtOrWeight():$weightInRangeCheck['weight_hi'];
					$this->org_charge_weight = $thisWeightCheck;
				}
			}else
			{
				$this->org_charge_weight =  $this->getCdeadwtOrWeight();
			}

			if(empty($this->org_charge_weight))$this->org_charge_weight = 0;
			
			$this->update(["org_charge_weight"]);
			return $this->org_charge_weight;
		}else
		{
			return $this->org_charge_weight;
		}
	}

	public function getConsol()
	{
		if(empty($this->consol_id) && !empty($this->shipment->consol_id))// when use the pre-manifest, sometimes the consol message is lost
		{
			$this->consol_id = $this->shipment->consol_id;
			if(!empty($this->consol_id))
			{
				$consol = Consol::model()->findByPk($this->consol_id);
				if(!empty($consol))
				{
					switch ($this->parent->client_type) {
						case Reconciliation::FASTWAY_TYPE:
							// update console related real fastway delivery cost
							if ($consol->owner_id != 114) {
								$consol->updateFastWayRealCost($recModel->id);
							} else {
								$consol->updateWmsDeliveryCost(Org::ORGID_COURIER_FASTWAY,$this->parent_id);
							}
							break;
						case Reconciliation::AUPOST_TYPE:
							if ($consol->owner_id != 114) {
								$consol->updateAupostRealCost();
							} else {
								$consol->updateWmsDeliveryCost(Org::ORGID_COURIER_AUPOST, $this->parent_id);
							}
							break;
						case Reconciliation::STARTRACK_TYPE:
							if ($consol->owner_id != 114) {
									$consol->updateStartrackRealCost($this->parent_id);
							} else {
								$consol->updateWmsDeliveryCost(Org::ORGID_COURIER_STARTRACK, $this->parent_id);
							}
							break;
						case Reconciliation::D2Z_CLIENT:
							$consol->updateCourierRealCost(Org::ORGID_COURIER_D2Z, "33A8Y\d{7}|ZK6\d{7}|33PET\d{7}|33PEN\d{7}|33PEH\d{7}|SJU\d{7}|33G7K\d{7}|33A93\d{7}");
							break;
						case Reconciliation::TNT_CLIENT:
							$consol->updateCourierRealCost(Org::ORGID_COURIER_TNT, '(DKC|PCD|BPC)\d{9}');
							break;
						case Reconciliation::HUNTER_TYPE:
							$consol->updateCourierRealCost(Org::ORGID_COURIER_HUNTER,'(PB)\d{6}');
							break;
						case Reconciliation::ECOF_TYPE:
							$consol->updateCourierRealCost(Org::ORGID_COURIER_ECOF,'(33MBQ)\d{7}');
							break;
						
						default:
							# code...
							break;
					}
					$this->update('consol_id');
				}
			}

		}

		return $this->consol_id;
	}

	public function getCdeadwtOrWeight()
	{
		return $this->cdeadwt>0&&$this->cdeadwt<99999?$this->cdeadwt:$this->weight;
	}

	public function getAupostInfo()
	{
		if (in_array($this->postcode, ['eParcel', 'Return to sender'])) {
			return "<a href=\"" . Yii::app()->createURL("invoice/viewAupostReconciliation", array("id" => $this->id)) . "\" class=\"tab_link\" title=\"" . $this->shipment_no . "\">" . $this->shipment_no . "</a>";
		} else if (in_array($this->postcode, ['ELMS'])) {
			$consol = ElmsConsol::model()->find('awb = :awb', [':awb' => $this->shipment_no]);
			return "<a href=\"" . Yii::app()->createURL("elmsConsol/update", array("id" => $consol->id, "tab" => "billing")) . "\" class=\"tab_link\" title=\"" . $consol->no . "\">" . $this->shipment_no . "</a>";
		} else {
			return $this->shipment_no;
		}
	}

	public function getConsolNos()
	{
		if (in_array($this->postcode, ['eParcel'])) {
			$parents = Reconciliation::model()->findAll('client_type = :type AND manifest_no = :manifest_no', [':type' => Reconciliation::AUPOST_TYPE, ':manifest_no' => $this->shipment_no]);
			foreach ($parents as $parent) {
				$temp[] = $parent->invoice_no;
			}

			return !empty($temp) ? implode(', ', $temp) : '';
		} else if (in_array($this->postcode, ['Return to sender'])) {
			$parents = Reconciliation::model()->findAll('client_type = :type AND manifest_no = :manifest_no', [':type' => Reconciliation::AUPOST_RTS, ':manifest_no' => $this->shipment_no]);
			foreach ($parents as $parent) {
				$temp[] = $parent->invoice_no;
			}

			return !empty($temp) ? implode(', ', $temp) : '';
		} else if (in_array($this->postcode, ['ELMS'])) {
			// $consol = ElmsConsol::model()->find('awb = :awb', [':awb' => $this->shipment_no]);
			// return $consol->no;
			return !empty($this->consol) ? $this->consol->no : '';
		} else {
			return '';
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
	public function search($weightCheck = false, $ec = false, $pgn = true, $ps = 100)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;
		// $criteria->with=array('consol');
		if(!empty($this->parent) && in_array($this->parent->getType(), Reconciliation::$cbmCourier))
		{
			$criteria->select = 't.*,(t.weight-(case when shipment.weight<(shipment.cbm*shipment.pkg)*250 then (shipment.cbm*shipment.pkg)*250 else shipment.weight end)) as diff';
		}else
		{
			$criteria->select = 't.*,(t.weight-shipment.weight) as diff';
		}
		$criteria->compare('consol.no',$this->consol_no,true);
		$criteria->compare('t.id',$this->id,true);
		if (!empty($this->parent_id)) $criteria->compare('t.parent_id',$this->parent_id);
		if (!empty($this->consol_id)) $criteria->compare('t.consol_id',$this->consol_id);
		$criteria->compare('t.shipment_id',$this->shipment_id);
		$criteria->compare('t.shipment_no',$this->shipment_no,true);
		$criteria->compare('t.manifest_weight',$this->manifest_weight,true);
		$criteria->compare('t.our_charge_weight',$this->our_charge_weight,true);
		$criteria->compare('t.weight',$this->weight,true);
		$criteria->compare('t.courier_cubic',$this->courier_cubic,true);
		$criteria->compare('t.my_value',$this->my_value,true);
		$criteria->compare('t.my_value',$this->my_value_m,true);
		$criteria->compare('t.invoice_no',$this->invoice_no,true);
		$criteria->compare('t.postcode',$this->postcode,true);
		if (empty($this->value)) {
			$criteria->addCondition('t.value > 0');
		} else {
			$criteria->compare('t.value', $this->value, true);
		}
		 
		$with = ['shipment', 'consol'];

		if (!empty($this->tnt_type)) {
			$criteria->compare('JSON_VALUE(t.meta, "$.tnt_type")', $this->tnt_type);
		}

		if (!empty($this->st_type)) {
			$criteria->compare('JSON_VALUE(t.meta, "$.st_type")', $this->st_type);
		}

		if (!empty($with)) {
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

		if ($ec) {
			$criteria->mergeWith($ec);
		}

		if($this->agentId!=null)
		{
			$criteria->addCondition('shipment.agent_id = '.$this->agentId);
		}
			
		if($weightCheck)
		{
			return new CActiveDataProvider($this, array(
			 'criteria'=>$criteria,
				 'sort' => array(
				   'attributes' => array(
				   'consol_no' => array(
				   'asc' => 'consol.no',
				   'desc' => 'consol.no DESC',
				),
				   'chargeWeightDiff' => [
						'asc' => ' diff ',
				 		'desc' => ' diff DESC',
				 	],
				'*',
			),
			   'defaultOrder'=>'diff DESC',
			),
		 
			'pagination'=> $pgn ? array(
				'pageSize' => $ps,
			) : false,
			));
		}else
		{
			return new CActiveDataProvider($this, array(
			 'criteria'=>$criteria,
				 'sort' => array(
				   'attributes' => array(
				   'consol_no' => array(
				   'asc' => 'consol.no',
				   'desc' => 'consol.no DESC',
				),
				   'chargeInvoiceDiff' => array(
				   	'asc'=>'t.my_value - t.value ASC',
				   	'desc'=>'t.my_value - t.value DESC',
				   ),
				'*',
			),
			   'defaultOrder'=>'t.my_value - t.value ASC',
			),
		 
			'pagination'=> $pgn ? array(
				'pageSize' => $ps,
			) : false,
			));
		}
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
		   if(!empty($this->shipment)){
		   		 $thisWeight =$this->shipment->chargeWeight();
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


	public function getCourierToOurWeight(){
	 if(preg_match("/7RFZ\d{8}/i", $this->shipment_no)){
	   if(!empty($this->courier_cubic)){
		   if(ceil($this->courier_cubic*250)<$this->weight){
		   return $this->weight;
		   }else{
		   return round($this->courier_cubic*167,2);
		   }
	   }
	   return round($this->weight/250*167,2);
	 }
	  return $this->weight;
	   }
	   
	public function getCostDiff2(){
			// return $this->getMyValue()-$this->value;
		return number_format($this->my_value-$this->getPostage(), 2, '.', '');
	}
	public function getCostDiff(){
		if ($this->parent->client_type != Reconciliation::UBI_TYPE) {
			if ($this->parent->client_type == Reconciliation::AUPOST_TYPE) {
				return number_format($this->getMyValue() - $this->value, 2, '.', '');
			} else {
				return number_format($this->my_value - $this->value, 2, '.', '');
			}
		} else {
			return number_format(round(($this->my_value-$this->value+floatval(@$this->mdata['fuel_charge'])) * 100) / 100, 2, '.', '');
		}
	}

	public function getMyValue()
	{
		if ($this->parent->client_type != Reconciliation::AUPOST_TYPE) {
			return $this->my_value;
		} else {
			$AupostFuelCharge = SystemSetting::getAupostFuelChargeSetting();
			$invoiceMonth = date("Y-m",strtotime($this->parent->invoice_date));
			$fuelCharge = (1+@$AupostFuelCharge[$invoiceMonth]*0.01);
			return round(($this->my_value*$fuelCharge),2);
		}
	}
	public function getPostage()
	{
		return round($this->value-($this->mdata['fuel_charge']/1.1),2);
	}

	public function getCourierWeightByChargeCode()
	{
		$courierWeight = $this->weight;
		if(in_array($this->parent->getType(),Reconciliation::$cbmCourier))
		{
			if(ceil($this->courier_cubic*self::CUBICRATE)==$courierWeight)
			{
				if (!empty($this->shipment)) {
					return $this->courier_cubic*$this->shipment->getCubicRate();
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

	public function getChargedInvoice()
	{
		if (!empty($this->shipment)) 
		{
				return $this->shipment->getChargedInvoice();
		}
		return 0;
	}

	public function getCourierWeightInvoiceByChargeCode()
	{
		$invoice = 0;
		if (!empty($this->shipment)) 
		{
				$invoice += $this->shipment->getChargeByChargeCode("",false,$this->getCourierWeightByChargeCode());
		}
		
		if($this->shipment->getIsIncludeGst())
		{
			$invoice = $invoice+$invoice*0.1;
		}

		return round($invoice,2);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ReconciliationLine the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	public function getWeightDiffInvoice()
	{
		$ccode=InvLine::WEIGHTCCODEAUTO;
		$oldRecord = Invoice::model()->find("meta like '%pid{$this->shipment->id}%' and meta like '%{$ccode}%' and (type = :type or type= :typeo) and status != :status1 and status != :status3",[":type"=>Invoice::	INVOICE_TYPE_WEIGHT_DIFF,":typeo"=>Invoice::	INVOICE_TYPE_OTHERS,":status1"=>Invoice::INVOICE_STATUS_CACELLED,":status3"=>Invoice::INVOICE_STATUS_FULLY_CREDITED]);
		if($oldRecord!=null)
		{
			return $oldRecord;
		}else
		{
			return null;
		}
	}

	public function beforeSave(){
		if(empty($this->shipment_id) && !empty($this->shipment_no)){
			$s = Shipment::model()->find('ref = :n OR hbn = :n', [':n' => $this->shipment_no]);
			if(!empty($s)){
				$this->shipment_id = $s->id;
			}
		}

		$this->value = number_format($this->value, 2, '.', '');
		$this->my_value = number_format($this->my_value, 2, '.', '');

		if (!empty($this->getErrors())) {
			yii::log($this->id . ' ' . json_encode($this->getErrors()), 'warning');
		}

		return parent::beforeSave();
	}
}