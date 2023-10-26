<?php

/**
 * This is the model class for table "wms_job".
 *
 * The followings are the available columns in table 'wms_job':
 * @property string $id
 * @property integer $org_id
 * @property string $oc_id
 * @property string $sales_id
 * @property string $pm_id
 * @property string $no
 * @property string $po
 * @property string $ref
 * @property integer $type
 * @property integer $status
 * @property string $bwf
 * @property string $meta
 * @property integer $dpt_id
 */
class WmsJob extends CActiveRecord
{
	const status_new = 10;
	const type_CG_delivery = 50;
	const type_split_delivery = 55;
	
	public static $types = array(
		10 => 'Inward',
		20 => 'Outward',
		30 => 'Pick Pack',
		40 => 'Stock Take',
		50 => 'CG Delivery',
		55 => 'Split Delivery',
		60 => 'Pick & Load',
		90 => 'Other',
	);

	const TYPE_PICK_LOAD = 60;

	public static $types_client = array(
		10 => 'Inward',
		30 => 'Pick Pack',
	);

	public static $states = array(
		10 => 'New',
		20 => 'Planned',
		30 => 'Processing',
		90 => 'Halt',
		99 => 'Completed',
		100 => 'Cancelled',
	);

	public $nolog = false;
	public $custom_log_note = '';
	public $mdata = array();

	public $cust_name, $sales_name, $pm_name,$orgids,$dpt_id;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wms_job';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('org_id, type, status, dpt_id', 'required'),
			array('oc_id, sales_id, pm_id, no, po, ref, created, bwf, meta, dpt_id', 'safe'),
			array('org_id, type, status', 'numerical', 'integerOnly'=>true),
			array('oc_id, sales_id, pm_id, bwf', 'length', 'max'=>11),
			array('no, po', 'length', 'max'=>20),
			array('ref', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, org_id, oc_id, sales_id, sales_name, pm_id, pm_name, no, po, ref, type, status, created, bwf, meta, cust_name,orgids', 'safe', 'on'=>'search'),
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
			'customer' => array(self::BELONGS_TO, 'Org', 'org_id'),
			'sales' => array(self::BELONGS_TO, 'User', 'sales_id'),
			'pm' => array(self::BELONGS_TO, 'User', 'pm_id'),
			'tasks' => array(self::HAS_MANY, 'WmsTask', 'job_id', 'on' => 'tasks.link_id = 0'),
			'pickPltTask' => array(self::HAS_ONE, 'WmsTask', 'job_id', 'on' => 'pickPltTask.link_id = 0 AND pickPltTask.type = 3010'),
			'branch' => array(self::BELONGS_TO, 'Org', 'dpt_id'),
		);
	}
	
	public function getType(){
		return Yii::t(strtolower(__CLASS__), empty(self::$types[$this->type])? '' : self::$types[$this->type]);
	}
	
	public function getStatus(){
		return Yii::t(strtolower(__CLASS__), empty(self::$states[$this->status])? '' : self::$states[$this->status]);
	}

	//Author:Nero Date:2021/6/28 Description:get branch name
	public function getBranch()
	{
		return empty($this->dpt_id) ? '' : $this->branch->shortName(1);
	}

	public function genNo(){
		$no = 'W' . sprintf('%04s', $this->org_id);
		$this->no =  $no. sprintf('%04s', self::model()->count('no LIKE :n', [':n' => $no.'%']) + 1);
	}
	
	public function beforeSave(){
		if(!empty($this->mdata)) $this->meta = json_encode($this->mdata);
		if(empty($this->status)) $this->status = 10;
		if(empty($this->no)) $this->genNo();
		if(empty($this->created)) $this->created = date('Y-m-d');
		return true;
	}
	
	public function afterSave(){
		if(!$this->nolog && !empty($this)){
			$extra = empty($this->custom_log_note)? array() : array('note' => $this->custom_log_note);
			Log::add($this, $this->isNewRecord? 3 : 4, array_merge(array('status' => $this->getStatus()), $extra));
		}
	}
	
	public function afterFind(){
		if(!empty($this->meta)) $this->mdata = json_decode($this->meta, true);
		return true;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'org_id' => 'Customer',
			'oc_id' => 'Contact Person',
			'sales_id' => 'Sales',
			'pm_id' => 'PM',
			'no' => 'No',
			'po' => 'PO',
			'ref' => 'Ref.',
			'type' => 'Type',
			'status' => 'Status',
			'created' => 'Created',
			'bwf' => 'Bwf',
			'meta' => 'Meta',
			//@Author:Nero @Date2021/6/3 @Department
			'dpt_id' => 'Branch',
		);
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
	public function search($pgn=true, $ps = 30, $ec = false)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.org_id',$this->org_id);
		$criteria->compare('oc_id',$this->oc_id);
		$criteria->compare('sales_id',$this->sales_id);
		$criteria->compare('pm_id',$this->pm_id);
		$criteria->compare('no',$this->no,true);
		$criteria->compare('po',$this->po,true);
		$criteria->compare('t.ref',$this->ref,true);
		if (empty($this->type)) {
			$criteria->addCondition('t.type != 50');
		} else {
			$criteria->compare('t.type',$this->type);
		}
		$criteria->compare('t.status',$this->status);
		$criteria->compare('bwf',$this->bwf,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('meta',$this->meta,true);
		$criteria->compare('t.dpt_id', $this->dpt_id);

		$with = array();
		if(!empty($this->cust_name)){
			$with[] = 'customer';
			$with[] = 'customer.owner';
			$criteria->addCondition('customer.name like "%' . $this->cust_name . '%" OR customer.id = "' . $this->cust_name . '" OR owner.name like "%' . $this->cust_name . '%" OR owner.id = "' . $this->cust_name . '"');
		}

		if(!empty($this->sales_name)){
			$with[] = 'sales';
			$criteria->compare('sales.name',$this->sales_name,true);
		}

		if(!empty($this->pm_name)){
			$with[] = 'pm';
			$criteria->compare('pm.name',$this->pm_name,true);
		}

		if(!empty($with)){
			$criteria->with = array_unique($with);
			$criteria->together = true;
		}

        if(!empty($this->orgids)){
            $criteria->addInCondition("t.org_id", $this->orgids);
        }

		/*
		* @author: Nero Wang
 		* @date: 2021/5/10
		* get all the jobs which create by this userself.
		* And the jobs created by Oragnisation which created by the this user.
		* if user's organisation ecual to '1' (System) then skip.
		 */
		if (!empty(Yii::app()->user->org)&&Yii::app()->user->org!='1'){
			$criteria->compare('t.org_id',User::getOrgIds());
		}
		
		/**
		 * @Author: Nero
		 * @date:2021/6/3
		 * show all the records dpt_id = user->dpt_id
		 * @@Mark
		 */
		//if (!Acl::hasAccess("B:admin/allDpt")){
		if (Yii::app()->user->dpt_id==218){
			$criteria->compare('dpt_id',Yii::app()->user->dpt_id);
		}

		
		if($ec) $criteria->mergeWith($ec);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
    			'defaultOrder'=>'t.id DESC',
  			),
			'pagination'=> $pgn? array(
				'pageSize' => $ps,
			) : false,
		));
	}
        
        
          /*  gen wms pallet in / valued added service.
         * 
         * 
         * 
         */
	public function genValueAddedInvoice($consol_no = 0) {
		$err = [];
		$owner = Org::model()->findByPk($this->org_id);
		$inv = Invoice::model()->find('type = 70 AND to_id = :oid AND job_id = :jid', array(':oid' => $this->org_id, ':jid' => $this->id));
		if (empty($inv) || in_array($inv->status, [6,7,8,9])) {
			$inv = new Invoice;
			$inv->type = Invoice::INVOICE_TYPE_WMS_INVOICE;
			$inv->dpmt = Invoice::DPMT_3PL;
			$inv->currency = 1;
			$inv->to_id = $this->org_id;
			$inv->job_id = $this->id;
			$inv->date = date('Y-m-d H:i:s');
			$inv->status = Invoice::INVOICE_STATUS_PENDING;
		}
		if (!empty($consol_no)) {
			$consol = Consol::model()->find('no = :no', array(':no' => $consol_no));
			if (!empty($consol)) {
				$inv->consol_id = $consol->id;
			}
		}
		$inv->mdata['name'] = $owner->name;
		$inv->mdata['address'] = $owner->getAddress();
		$inv->mdata['payterm'] = empty($owner->extra['payterm'])? 'COD' : $owner->extra['payterm'].' days';
		$inv->mdata['paytype'] = empty($owner->extra['paytype'])? '' : $owner->extra['paytype'];
		$inv->mdata['billfrom'] = date('Y-m-d H:i:s');
		$inv->mdata['billto'] = date('Y-m-d H:i:s');
		$inv->due = $inv->date;
		$inv->total = 0;
		$inv->gst = 0;
		$inv->save();InvLine::model()->deleteAll('inv_id = :id', [':id' => $inv->id]);

		$quote = WmsOrgQuote::model()->find('org_id = :org_id and status = 1', array(':org_id' => $owner->id));
		if (empty($quote)) {
			$err[] = "10003 - Please set up a valid charge rate quote in the Rate Management";
			return $err;
		}
		$tasks = WmsTask::model()->with('mainTask')->findAll('t.job_id = :jid AND mainTask.status = :status', array(":jid" => $this->id, ':status' => array_search('Completed', WmsTask::$states)));
		foreach ($tasks as $task) {
			// Ref
			if (($task->getType() == 'Pick Unit' || $task->getType() == 'Pick Carton') && !empty($task->mainTask)) {
				$delivery_task = WmsTask::model()->find('link_id = :link_id AND type = 2120', array(':link_id' => $task->link_id));
				if (!empty($delivery_task)) {
					$ref = '';
					foreach ($delivery_task->mdata['shipment_id'] as $shipment_id) {
						$ref .= ' ' . Shipment::model()->findByPk($shipment_id)->ref;
					}
				} else {
					$ref = $task->mainTask->ref;
				}
			} else {
				$ref = $task->mainTask->ref;
			}
			// Pick Unit
			if ($task->getType() == 'Pick Unit' && !empty($task->mainTask)) {
				if ($quote->mdata[WmsOrgQuote::QUOTE_PICKING_UNIT] && $quote->mdata[WmsOrgQuote::QUOTE_PICKING_CARTON]) {
					$quote_type = WmsOrgQuote::QUOTE_PICKING_UNIT;

					$stot = 0;
					$items = [];
					foreach ($task->items as $item) {
						$prod_pack = WmsProdPack::model()->find('prod_id = :prod_id and type = :type', array(':prod_id' => WmsStock::model()->findByPk($item->mdata['si'])->prod_id, ':type' => array_search('Carton', WmsProdPack::$types)));
						if ($prod_pack) {
							$cq = floor(intval($item->mdata['uq']) / intval($prod_pack->qty));
							$uq = intval($item->mdata['uq']) % intval($prod_pack->qty);
						} else {
							$cq = 0;
							$uq = $item->mdata['uq'];
						}
						$stot += $cq * $quote->mdata[WmsOrgQuote::QUOTE_PICKING_CARTON] + $uq * $quote->mdata[WmsOrgQuote::QUOTE_PICKING_UNIT];
						if ($prod_pack && $cq) $items[] = [$task->getNo(), ucwords($task->mainTask->ref), $task->compl_time, 'Pick Carton - ' . $item->mdata['sn'] . ' * ' . $cq * $prod_pack->qty, $quote->mdata[WmsOrgQuote::QUOTE_PICKING_CARTON], $cq, $cq * $quote->mdata[WmsOrgQuote::QUOTE_PICKING_CARTON]];
						if ($uq) $items[] = [$task->getNo(), ucwords($task->mainTask->ref), $task->compl_time, 'Pick Unit - ' . $item->mdata['sn'] . ' * ' . $uq, $quote->mdata[WmsOrgQuote::QUOTE_PICKING_UNIT], $uq, $uq * $quote->mdata[WmsOrgQuote::QUOTE_PICKING_UNIT]];
					}
				} else {
					$err[] = "10003 - Please set up a valid charge rate for Picking / Unit and Picking / Carton in the Rate Management";
					break;
				}
			// Pick Carton
			} else if ($task->getType() == 'Pick Carton' && !empty($task->mainTask)) {
				if ($quote->mdata[WmsOrgQuote::QUOTE_PICKING_UNIT] && $quote->mdata[WmsOrgQuote::QUOTE_PICKING_CARTON]) {
					$quote_type = WmsOrgQuote::QUOTE_PICKING_CARTON;

					$stot = 0;
					$items = [];
					foreach ($task->items as $item) {
						$prod_pack = WmsProdPack::model()->find('prod_id = :prod_id and type = :type', array(':prod_id' => WmsStock::model()->findByPk($item->mdata['si'])->prod_id, ':type' => array_search('Carton', WmsProdPack::$types)));
						if ($prod_pack) {
							$cq = floor($item->mdata['uq'] / $prod_pack->qty);
							$uq = $item->mdata['uq'] % $prod_pack->qty;
						} else {
							$cq = 0;
							$uq = $item->mdata['uq'];
						}
						$stot += $cq * $quote->mdata[WmsOrgQuote::QUOTE_PICKING_CARTON] + $uq * $quote->mdata[WmsOrgQuote::QUOTE_PICKING_UNIT];
						if ($prod_pack && $cq) $items[] = [$task->getNo(), ucwords($task->mainTask->ref), $task->compl_time, 'Pick Carton - ' . $item->mdata['sn'] . ' * ' . $cq * $prod_pack->qty, $quote->mdata[WmsOrgQuote::QUOTE_PICKING_CARTON], $cq, $cq * $quote->mdata[WmsOrgQuote::QUOTE_PICKING_CARTON]];
						if ($uq) $items[] = [$task->getNo(), ucwords($task->mainTask->ref), $task->compl_time, 'Pick Unit - ' . $item->mdata['sn'] . ' * ' . $uq, $quote->mdata[WmsOrgQuote::QUOTE_PICKING_UNIT], $uq, $uq * $quote->mdata[WmsOrgQuote::QUOTE_PICKING_UNIT]];
					}
				} else {
					$err[] = "10003 - Please set up a valid charge rate for Picking / Unit and Picking / Carton in the Rate Management";
					break;
				}
			// Pack Order
			} else if ($task->getType() == 'Pack Order' && !empty($task->mainTask)) {
				// Charge by parcel
				if ($quote->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER_STATUS]) {
					if ($quote->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER]) {
						$quote_type = WmsOrgQuote::QUOTE_PACKING_ORDER;

						$stot = 0;
						$items = [];
						if (!empty($task->mdata['pkg'])) {
							$quantity = count(json_decode($task->mdata['pkg']));
							$stot += $quantity * $quote->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER];
							$items[] = [$task->getNo(), ucwords($task->mainTask->ref), $task->compl_time, $task->getType(). ' - Standard fee * ' . $quantity, $quote->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER], $quantity, $quantity * $quote->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER]];
						}
					} else {
						$err[] = "10003 - Please set up a valid charge rate for Packing / Order in the Rate Management";
						break;
					}
				// Charge by unit
				} else {
					if ($quote->mdata[WmsOrgQuote::QUOTE_PACKING_UNIT]) {
						$quote_type = WmsOrgQuote::QUOTE_PACKING_ORDER;

						$stot = 0;
						$items = [];
						$pick_task = WmsTask::model()->find('link_id = :link_id AND type in (3020, 3030)', array(
							':link_id' => $task->mainTask->id
						));
						foreach ($pick_task->items as $item) {
							$prod_pack = WmsProdPack::model()->find('prod_id = :prod_id and type = :type', array(':prod_id' => WmsStock::model()->findByPk($item->mdata['si'])->prod_id, ':type' => array_search('Carton', WmsProdPack::$types)));
							if ($prod_pack) {
								$cq = floor(intval($item->mdata['uq']) / intval($prod_pack->qty));
								$uq = intval($item->mdata['uq']) % intval($prod_pack->qty);
							} else {
								$cq = 0;
								$uq = $item->mdata['uq'];
							}
							$stot += $uq * $quote->mdata[WmsOrgQuote::QUOTE_PACKING_UNIT];
							if ($uq) $items[] = [$task->getNo(), ucwords($task->mainTask->ref), $task->compl_time, $task->getType() . ' - ' . $item->mdata['sn'] . ' * ' . $uq, $quote->mdata[WmsOrgQuote::QUOTE_PACKING_UNIT], $uq, $uq * $quote->mdata[WmsOrgQuote::QUOTE_PACKING_UNIT]];
						}
					}
				}
				if ($quote->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER_EXTRA]) {
					$quote_type = WmsOrgQuote::QUOTE_PACKING_ORDER;
					if (!empty($task->mdata['pkg'])) {
						$pkgs = json_decode($task->mdata['pkg']);

						$quantity = 0;
						foreach ($pkgs as $pkg) {
							if (explode(' ', $pkg->nt)[0] == '是') $quantity++;
						}
						$stot += $quantity * $quote->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER_EXTRA];
						if ($quantity) {
							$items[] = [$task->getNo(), ucwords($task->mainTask->ref), $task->compl_time, $task->getType() . '  - Comsumable Materials * ' . $quantity, $quote->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER_EXTRA], $quantity, $quantity * $quote->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER_EXTRA]];
						}
					}
				}
			// Delivery
			} else if ($task->getType() == 'Delivery' && !empty($task->mainTask) && !empty($task->mdata['shipment_id'])) {
				$criteria = new CDbCriteria();
				$criteria->compare('id', $task->mdata['shipment_id']);
				$shipments = Shipment::model()->findAll($criteria);
				$weight = 0;
				foreach ($shipments as $shipment) {
				  $weight += $shipment->weight;
				}

				if (preg_match('/[\x{4e00}-\x{9fa5}]+/u', $task->mdata['cnee']['state'])) {
					$stot = max(1, $weight) * 6.5 + 1;
					$items = [];
					$items[] = [$task->getNo(), ucwords($task->mainTask->ref), $task->compl_time, $task->getType() . ' - ' . $weight . ' kg', $stot, 1, 1 * $stot];
				} else {
					if (!empty($quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE])) {
						$chargecode = ImportChargeCode::model()->find('chargecode = :chargecode', array(':chargecode' => $quote->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE]));
					} else {
						$chargecode = ImportChargeCode::model()->find('org_id = :org_id', array(':org_id' => $owner->id));
					}
					$chargecode = $chargecode ? $chargecode : ImportChargeCode::model()->find('org_id = 114');
					if (!empty($chargecode)) {
						$stot = $task->getChargeByChargecode($weight, $task->mdata['cnee']['postcode'], $chargecode->chargecode);
						$items = [];
						$items[] = [$task->getNo(), ucwords($task->mainTask->ref), $task->compl_time, $task->getType() . ' - ' . $weight . ' kg', $stot, 1, 1 * $stot];
					} else {
						$err[] = "10003 - Neither a valid import chargecode for this org or a standard import chargecode is available";
						break;
					}
				}
			} else {
				continue;
			}

			// Calculate
			if ($stot == 0 && empty($items)) continue;
			$stot = $stot != 0 ? round($stot * 110) / 100 : 0;
			$il = InvLine::model()->find('fid = :id AND model = "WmsInvoiceLine"', [':id' => $task->id]);
			if (empty($il)) {
				$il = new InvLine;
				$il->inv_id = $inv->id;
			} else if ($il->invoice->id != $inv->id && $il->invoice->status != 10) {
				continue;
			}
			$il->inv_id = $inv->id;
			$il->amount = $stot;
			$il->model = 'WmsInvoiceLine';
			$il->gst = $stot != 0 ? round($stot * 100 / 11) / 100 : 0;
			$il->mdata['items'] = $items;
			if (!empty($ref)) {
				$il->mdata['ref'] = $ref;
			}
			$il->ccode = $quote_type;
			$il->det = $task->getType();
			$il->qty = 1;
			$il->fid = $task->id;
			$il->save();

			$inv->dpt_id = 106;
			$inv->lines = [$il];
			$inv->total += $il->amount;
			$inv->gst = $inv->total != 0 ? round($inv->total * 100 / 11) / 100 : 0;
			$inv->sync_xero = 0;
		}
		if ($inv->total == 0) {
			$inv->status = Invoice::INVOICE_STATUS_CACELLED;
			$inv->save();
		} else {
			$inv->status = Invoice::INVOICE_STATUS_PENDING;
			$inv->save();
		}
		return $err;
	}
//         public function genValueAddedInvoice($consol_no=0){
//             $err=[];
//             $owner = Org::model()->findByPk($this->org_id);
//             $inv= Invoice::model()->find('type=65 AND to_id=:aid AND job_id=:jid',array(':aid'=>$this->org_id,':jid'=>$this->id));
//            if(empty($inv)){
//                 $inv=new Invoice;
//                 $inv->type=65;
//                 $inv->currency=1;
//                 $inv->dpmt= Invoice::DPMT_3PL;
//                 $inv->job_id= $this->id;
//                 $inv->to_id=$this->org_id;
//                 $inv->status= Invoice::INVOICE_STATUS_PENDING;  // set as posted which means will send to client for paying
//             }
//            if(!empty($consol_no)){
//                 $consol=Consol::model()->find('no=:no',array(':no'=>$consol_no));
//                 if(!empty($consol)){
//                     $inv->consol_id=$consol->id;
//                 }
//             }
//            $inv->date=date('Y-m-d');
//            $inv->mdata['name']=$owner->name;
//            $inv->mdata['address']=$owner->getAddress();
//            $inv->mdata['payterm']=empty($owner->extra['payterm'])?'2 days':$owner->extra['payterm'].' days';
//            $inv->mdata['paytype'] = empty($owner->extra['paytype'])? '' : $owner->extra['paytype'];
//            $inv->due = Invoice::calcDue($inv->date, $inv->mdata['payterm']);
//            $inv->total = 0;
//            $stot = 0;
//            $items = [];
//            $wmsTasks= WmsTask::model()->findAll('job_id=:jid AND is_request=1',array(":jid"=>$this->id));
//            //1.sorting /picking/packing fees
//            //2.consumble fees
//            //3.postage fees
           
//            //get the wms quotes at first;
//            $quotes=WmsOrgQuote::model()->find("org_id=:org_id AND status=1",array(':org_id'=>$this->org_id));
//            $pca_quotes= WmsOrgQuote::model()->find("org_id=:org_id AND status=1",array(':org_id'=>114));
//            if(empty($quotes)){
//             $err[]= "client ".$owner->name." wms rate are not setting yet!";   
//            }else{
              
//                foreach($wmsTasks as $i=>$task){ 
//                 if($task->status==100)                    continue;   //canceled task
//                  if(in_array($task->type, [3020, 3030])){   // fisrt part, charge the pick pack task fees.
//                     foreach($task->items as $k=>$itm){
                   
//                    //1. sorting/picking/packing charge ---start
//                    $sorting_pallet=FALSE; //a flag to record if sorting by pallte;
                  
//                     if (!empty($itm->mdata['pq'])) {
//                         $plt_qty = $itm->mdata['pq'];
//                         $unit_price = $quotes->mdata[WmsOrgQuote::QUOTE_SORTING_COUNTING_PALLET];
//                         if ($unit_price <= 0) {
//                             $unit_price = $pca_quotes->mdata[WmsOrgQuote::QUOTE_SORTING_COUNTING_PALLET];  //pca quotes if not get quotes from the specify org
//                         }
//                         $sub_charge = $unit_price * $plt_qty;
//                         if($sub_charge>0) {
//                             $sorting_pallet=TRUE;
//                             $items[] = [$task->getNo(),$task->ref, "Sorting/Pallet-".$itm->mdata['sn'], $unit_price, $plt_qty, $sub_charge];
//                             $stot+=$sub_charge;
//                         }
//                     } 
//                     //to check if set the per order. if, we will charge by order.
//                     $isPickByOrder=FALSE;
//                     $isPackByOrder=FALSE;
//                     $picking_per_oder = empty($quotes->mdata[WmsOrgQuote::QUOTE_PICKING_ORDER]) ? 0 : $quotes->mdata[WmsOrgQuote::QUOTE_PICKING_ORDER];
//                     $packing_per_oder = empty($quotes->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER]) ? 0 : $quotes->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER];
//                     if($picking_per_oder>0){
//                         $isPickByOrder=TRUE;
//                         $items[] = [$task ->getNo(),$task->ref,"Picking/Order", $picking_per_oder, 1, $picking_per_oder*1];
//                          $stot+=$picking_per_oder*1;
//                     }
//                     if($packing_per_oder>0){
//                         $isPackByOrder=TRUE;
//                         $items[] = [$task ->getNo(),$task->ref,"Packing/Order", $packing_per_oder, 1, $packing_per_oder*1];
//                         $stot+=$packing_per_oder*1;
//                     }
                    
                    
//                     if (!empty($itm->mdata['cq'])) {
//                         $ct_qty = $itm->mdata['cq'];
//                         $unit_price_sorting = empty($quotes->mdata[WmsOrgQuote::QUOTE_SORTING_COUNTING_CARTON]) ? 0 : $quotes->mdata[WmsOrgQuote::QUOTE_SORTING_COUNTING_CARTON];
//                         $sub_charge = $unit_price_sorting * $ct_qty;
//                       if(!$sorting_pallet&&$sub_charge>0)  {
//                           $items[] = [$task->getNo(),$task->ref, "Sorting/carton-".$itm->mdata['sn'], $unit_price_sorting, $ct_qty, $sub_charge];
//                            $stot+=$sub_charge;
//                       }

//                         //picking
//                       if(!$isPickByOrder){
//                         $unit_price_picking = empty($quotes->mdata[WmsOrgQuote::QUOTE_PICKING_CARTON]) ? 0 : $quotes->mdata[WmsOrgQuote::QUOTE_PICKING_CARTON];
//                         $sub_charge_picking = $unit_price_picking * $ct_qty;
//                         if($sub_charge_picking>0){
//                              $items[] = [$task ->getNo(),$task->ref,"Picking/carton-".$itm->mdata['sn'], $unit_price_picking, $ct_qty, $sub_charge_picking];
//                              $stot+=$sub_charge_picking;
//                         }
//                       }
//                     } else if (!empty($itm->mdata['uq'])) {
//                         $ut_qty = $itm->mdata['uq'];
//                         $unit_price = $quotes->mdata[WmsOrgQuote::QUOTE_SORTING_COUNTING_UNIT];
//                         $sub_charge = floatval($unit_price) * floatval($ut_qty);
//                         if(!$sorting_pallet&&$sub_charge>0){
//                             $items[] = [$task->getNo(),$task->ref,"Sorting/unit-".$itm->mdata['sn'], $unit_price, $ut_qty, $sub_charge];
//                             $stot+=$sub_charge;
//                         }

//                       if(!$isPickByOrder){
//                         //picking
//                         $unit_price_picking = empty($quotes->mdata[WmsOrgQuote::QUOTE_PICKING_UNIT]) ? 0 : $quotes->mdata[WmsOrgQuote::QUOTE_PICKING_UNIT];
//                         $sub_charge_picking = $unit_price_picking * $ut_qty;
//                         if($sub_charge_picking>0){
//                             $items[] = [$task ->getNo(),$task->ref, "Picking/unit-".$itm->mdata['sn'], $unit_price_picking, $ut_qty, $sub_charge_picking];
//                             $stot+=$sub_charge_picking;
//                            }
//                          }
//                         if(!$isPackByOrder){
//                         //packing fees for unit
//                         $unit_price_packing = empty($quotes->mdata[WmsOrgQuote::QUOTE_PACKING_UNIT]) ? 0 : $quotes->mdata[WmsOrgQuote::QUOTE_PACKING_UNIT];
//                         $sub_charge_packing = $unit_price_packing * $ut_qty;
//                          if($sub_charge_packing>0){
//                             $items[] = [$task -> getNo(),$task->ref,"Packing/unit-".$itm->mdata['sn'], $unit_price_packing, $ut_qty, $sub_charge_packing];
//                             $stot+=$sub_charge_packing;
//                           }
//                        }
//                     }
//                      // end--> sorting/picking/packing charge
                
//                }
               
               
//                $box_fee = empty($quotes->mdata[WmsOrgQuote::QUOTE_BOX_FEE]) ? 0 : $quotes->mdata[WmsOrgQuote::QUOTE_BOX_FEE];
//                      //2.consumble fees start
//                if($box_fee>0){
//                    if (!empty($itm->mdata['uq'])&&empty($itm->mdata['cq'])&&empty($itm->mdata['pq'])) {
//                           $pt = WmsTask::model()->find('type = 3210 AND link_id = :t', [':t' => $task->id]);
// 		          $pkg = 0;
// 		      if(!empty($pt) && !empty($pt->mdata['pkg'])){
// 			foreach(json_decode($pt->mdata['pkg'], true) as $pk){
// 				if(empty($pk['wt'])) continue;
// 				$pkg++;
// 			  }
// 		       }
//                       if($pkg>0){
//                          $items[] = [$task->getNo(),$task->ref, "box charge", 0.50 , $pkg, 0.50*$pkg];
//                          $stot+=0.50*$pkg;
//                        }
//                     }
//                 }
                   
//                    //2.consumble fees end // only for unit picking 
                 
//                     //3.postage fee--start
//                        /*  1.get related ships
//                          2.charge each ships  based on chargecode 
//                          3.list in the items
//                         */
//                     $task_delivery= WmsTask::model()->find('link_id=:link_id AND type=2120',array(':link_id'=>$task->id));
//                   if(!empty($task_delivery->mdata['shipment_id'])){
//                      $shipment=Shipment::model()->find('id=:id',array(':id'=>$task_delivery->mdata['shipment_id'])); 
//                      $pm='imParcel';
//                      if(!empty($shipment)) {
//                          if($shipment->type==20){
//                                 $pm='exParcel';
//                          }
//                       }
                     
//                      if($pm=='imParcel'){
//                          $chargecode= empty($quotes->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE])?0:$quotes->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE];
//                          if(!empty($chargecode)){
//                               $chargecodeInfo= ImportChargeCode::model()->find('chargecode=:chargecode and status=1',array(":chargecode"=>$chargecode));
//                          }
// //                         if(empty($chargecodeInfo)){
// //                             $chargecode=$pca_quotes->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE];
// //                             $chargecodeInfo= ImportChargeCode::model()->find('chargecode=:chargecode and status=1',array(":chargecode"=>$chargecode));
// //                         }
//                          $amt=0;
//                          if(!empty($chargecodeInfo)){
//                               $amt = $shipment->getChargeByChargecode($chargecode,true);
//                               $zoneMap = ZoneMap::model()->find('chargecode_id = :chargecode AND zone_id = 1 AND pc_lo <= :p AND pc_hi >= :p', [':chargecode'=>$chargecode,':p' => $shipment->cnee->postcode]);
//                               $zoneCode = 'N1';
//                               if (!empty($zoneMap) && !empty($zoneMap['z1']) ) {
//                               $zoneCode = $zoneMap['z1'];
//                              }
//                          if($amt>0){
//                             $items[] = [$task->getNo(),$task->ref, 'Delivery Fee -'.$shipment->ref.' '.'-P/S:'.$shipment->postcode.''."-wt:".$shipment->weight, round(($amt/$shipment->pkg),2), $shipment->pkg, $amt];
//                              $stot+=$amt;
//                          }
//                          }
                     
//                       }else{
//                           ///for exparcel postage charge
//                           ///for exparcel postage charge
//                       }
//                   }
//                     //3.postage fee--end;
//                 }elseif (in_array($task->type, [1010])) {  //pallet hand in fee
//                   $task_pallet_in= WmsTask::model()->find('link_id=:link_id AND type=1010',array(':link_id'=>$task->id));
//                    if(!empty($task_pallet_in->items)){
//                    $pallet_hand_in_no= sizeof($task_pallet_in->items);
//                    $pallet_hand_in_quote=$quotes->mdata[WmsOrgQuote::QUOTE_PALLET_IN];
//                    $sub_charge =  $pallet_hand_in_quote*$pallet_hand_in_no;
//                    if($sub_charge>0){
//                        $items[] = [$task->getNo(),$task->ref, "Pallet/In-", $pallet_hand_in_quote, $pallet_hand_in_no, $sub_charge]; 
//                        $stot+=$sub_charge;
//                     }
//                   }
//                  }elseif(in_array($task->type, [2030])){
                    
//                     $task_container_load=WmsTask::model()->find('link_id=:link_id AND type=2030',array(':link_id'=>$task->id));
//                    if(!empty($task_container_load->items)){
// //                      $lpallet_out_no= sizeof($task_container_load->items);
// //                      $pallet_out_quote=$quotes->mdata[WmsOrgQuote::QUOTE_PALLET_OUT];
//                        if(sizeof($task_container_load->items)>20){
//                            $container_load_quote=$quotes->mdata[WmsOrgQuote::QUOTE_UNLOAD_50FT_CONTAINER];
//                        } else {
//                             $container_load_quote=$quotes->mdata[WmsOrgQuote::QUOTE_UNLOAD_20FT_CONTAINER];
//                        }
                       
//                       $sub_charge = $container_load_quote;
//                       if($sub_charge>0){
//                          $items[] = [$task->getNo(),$task->ref,"Container load/unload fee", $container_load_quote, 1,$container_load_quote]; 
//                          $stot+=$sub_charge;
//                       }
                       
//                     }
//                  }
//               }
//             }
//              if(empty($err)){
//                  if($stot>0){
//                      $stot = round($stot * 110)/100;
//                      $inv->total=$stot;
//                      $inv->save();
//                      $il = InvLine::model()->find('inv_id = :id', [':id' => $inv->id]);
// 	             if(empty($il)){
// 			     $il = new InvLine;
// 		             $il->inv_id = $inv->id;
// 			}
// 			  $il->amount = $stot;
// 				$il->gst = round($stot * 100 / 11) / 100;
// 				$il->mdata['items'] = $items;
// 				$il->ccode = "wms";
// 				$il->det = '3PL';
// 				$il->qty = 1;
				
// 				$il->fid = $this->id;
// 				$il->save();
			
// 				$inv->dpt_id = 106;
// 				$inv->lines = [$il];
// 				$inv->total = $stot;
// 				$inv->gst = round($stot * 100 / 11) / 100;
// 			        $inv->sync_xero = 0;
// 				$inv->save();
//                  }
               
//              }else{
                 
//                  //show err to customers
//              }
//              return $err;
//            }
   
       

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WmsJob the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
