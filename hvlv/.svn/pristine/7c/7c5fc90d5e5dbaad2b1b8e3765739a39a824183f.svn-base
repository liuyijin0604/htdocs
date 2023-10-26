<?php

class PickupListController extends Controller{

	protected $nonAjax = ['export', 'report', 'exportAll'];
	protected $skipAcl = [];

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate(){
		$model=new PickupList;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['PickupList'])){
			$model->attributes=$_POST['PickupList'];
			$model->ref = $model->fwd_id.'-'.date('ymdH', empty($model->created)? time() : strtotime($model->created));
			$model->mdata = $_POST['mdata'];
			if(!empty($model->owner->extra['sergra'])){
				$model->mdata['sergra'] = $model->owner->extra['sergra'];
			}
			if(empty($model->fwd_id)){
				$model->addError('fwd_id', 'Agent required!');
			}else{
				$model->save();
			}
			$this->ajaxResult($model);
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id){
		$model=$this->loadModel($id);

		if(!empty($_POST['barcode'])){
			$hbn = $_POST['barcode'];
			$p = ExParcel::model()->find('hbn = :n', array(':n' => $hbn));
			$r = new StdClass;
			if(!$p){
				$r->msg = $hbn.' Not found';
				$r->color = '#c00';
				$r->sound = 'not_found';
			}else{
				$mm = $model->hasMap($p, false);
				if($mm){
					$r->msg = $hbn.' found in list';
					$r->color = '#0c0';
					$r->sound = 'beep_double';
					$mm->bwf = $mm->bwf | 1;
					$mm->save();
				}else{
					$r->msg = $hbn.' not in this list';
					$r->color = '#c00';
					$r->sound = 'beep_warn';
				}
			}
			echo json_encode($r);
			Yii::app()->end();
		}elseif(!empty($_POST['PickupList'])){
			$model->attributes = $_POST['PickupList'];
			$model->mdata = array_merge($model->mdata, $_POST['mdata']);
			if(empty($_POST['mdata']['it'])) $model->mdata['it'] = 0;
			$model->save();

			if($model->dpt_id != 106){
				if(empty($model->mdata['it'])){
					foreach($model->getFids() as $pid){
						$p = ExParcel::model()->findByPk($pid);
						if(($p->bwf & 256) == 256){
							$p->bwf = $p->bwf & (~ 256);
							$p->save();
						}
					}
				}else{
					foreach($model->getFids() as $pid){
						$p = ExParcel::model()->findByPk($pid);
						if(!$p->isReceived() && ($p->bwf & 256) == 0){
							$p->bwf = $p->bwf | 256;
							$p->save();
						}
					}
				}
			}
			$this->ajaxResult($model);
		}
		
		if(isset($_GET['tab'])){
			Acl::hasAccess($this->CaName.'/'.$_GET['tab'], true);
			$this->render('tab_'.$_GET['tab'], array('model'=>$model));
		}else{
			$this->render('update',array('model'=>$model));
		}
	}

	public function actionListUpdate() {
		if (!empty($_POST['pkgs'])) {
			$pkgss = $_POST['pkgs'];
			foreach ($pkgss as $id => $pkgs) {
				$model = $this->loadModel($id);
				$model->mdata['pkgs'] = $pkgs;
				$model->save();
			}
		}
		if (!empty($_POST['total'])) {
			$totals = $_POST['total'];
			foreach ($totals as $id => $total) {
				$model = $this->loadModel($id);
				$model->mdata['total'] = $total;
				$model->save();
			}
		}
		echo json_encode(array('done' => true, 'msg' => 'Modify Successfully!'));
	}

	public function actionAddConnote($id){
		$model=$this->loadModel($id);
		if($model->isBilled()) return;
		if(!empty($_POST['connotes'])){
			$cs = preg_split('/[,;\n\r]+/', $_POST['connotes']);

			$trans = Yii::app()->db->beginTransaction();
			try{
			foreach($cs as $c){
				$w = 0;
				if(preg_match('/\t/', $c)) list($c, $w) = explode("\t", strtoupper($c));
				$p = ExParcel::model()->find('hbn = :h', array(':h' => $c));
				$map = true;
				$save = false;
				if(empty($p)){
					$p = new ExParcel;
					$p->hbn = $c;
					$p->odpt_id = $model->dpt_id;
					$p->status = 12;
					$p->agent_id = $model->fwd_id;
					$cnor = new Addr;
					$cnor->country = 'Australia';
					$cnor->save();
					$p->cnor_id = $cnor->id;
					$cnee = new Addr;
					$cnee->country = 'PR China';
					$cnee->save();
					$p->cnee_id = $cnee->id;
					$p->save();
				}else{
					if($p->status < 12){
						$p->status = 12;
						$save = true;
					}
					if($model->hasMap($model)){
						$map = false;
					}else{
						$rs = ManiMap::model()->findAll("fid = :id AND model = 'ExParcel'", [':id' => $p->id]);
						foreach($rs as $r){
							if($r->mani_id == $model->id) continue;
							if(!empty($r->mani_id) && $r->manifest->type == 40){
								$model->addError('ref', $c.' belongs to another receipt list');
								$map = false;
							}
						}
					}
				}
				if(empty($p->odpt_id)){
					$p->odpt_id = $model->dpt_id;
					$save = true;
				}
				if($map){
					$model->map($p);
					$p->agent_id = $model->fwd_id;
					$save = true;
				}
				if(floatval($p->weight) == 0 && !empty($w)){
					$p->weight = $w;
					$save = true;
				}
				if(!empty($model->mdata['it']) && !$p->isReceived()){
					$p->bwf = $p->bwf | 256;
					$save = true;
				}
				if($save) $p->save();

				$ht = Tracking::model()->count('pid = :pid AND type = :t', array(':pid' => $p->id, ':t' => 12));
				if(empty($ht)) $p->addTracking(12, 'Consignment Picked Up');
			}
				$trans->commit();
			} catch (Exception $ex) {
				$trans->rollback();
				throw $ex;
			}
			Log::add($model, 4, array('status' => $model->getStatus(), 'note' => sizeof($cs).' shipments added'));
			$this->ajaxResult($model);
		}
		$this->render('add_connote',array('model'=>$model));
	}

	public function actionManageConnote($id){
		$model=$this->loadModel($id);
		if(!empty($_POST['PickupList'])){
			$m = PickupList::model()->find('fwd_id = :aid AND DATE(created) = :d AND id != :id', [':aid' => $_POST['PickupList']['fwd_id'], ':d' => substr($_POST['PickupList']['created'], 0, 10), ':id' => $id]);
			if(empty($m)){
				$m = new PickupList;
				unset($_POST['PickupList']['id']);
				$m->attributes=$_POST['PickupList'];
				$m->ref = $m->fwd_id.'-'.date('ymdH', empty($m->created)? time() : strtotime($m->created));
				$m->save();
			}elseif($m->isBilled()){
				$m = new PickupList;
				unset($_POST['PickupList']['id']);
				$m->attributes=$_POST['PickupList'];
				$m->ref = $m->fwd_id.'-'.date('ymdH', empty($m->created)? time() : strtotime($m->created));
				$m->save();
				// $m->addError('id', 'Target pickup list already invoiced');
				// $this->ajaxResult($m);
			}
			
			if($m->status == 100){
				$m->status = 10;
				$m->save();
			}
			foreach($_POST['connotes'] as $c){
				$p = ExParcel::model()->findByPk($c);
				if($p->agent_id != $m->fwd_id){
					$p->agent_id = $m->fwd_id;
					$p->save();
				}
				$omm = ManiMap::model()->find('mani_id = :id AND fid = :p', [':id' => $m->id, ':p' => $c]);
				$mm = ManiMap::model()->find('mani_id = :id AND fid = :p', [':id' => $id, ':p' => $c]);
				$mm->mani_id = empty($omm)? $m->id : 0;
				$mm->save();
			}

			Log::add($m, 4, array('status' => $m->getStatus(), 'note' => sizeof($_POST['connotes']).' shipments moved from '.$model->ref));
			Log::add($model, 4, array('status' => $model->getStatus(), 'note' => sizeof($_POST['connotes']).' shipments moved to '.$m->ref));
			$this->ajaxResult($m);
		}
		$this->render('manage_connote',array('model'=>$model));
	}

	public function actionRemove($id){
		$model=$this->loadModel($id);
		if(!empty($_GET['pid'])){
			$p = ExParcel::model()->findByPk($_GET['pid']);
			$mm = ManiMap::model()->find('mani_id = :id AND fid = :p', [':id' => $id, ':p' => $_GET['pid']]);
			$mm->mani_id = 0;
			$mm->save();
			Log::add($model, 4, array('status' => $model->getStatus(), 'note' => $p->hbn.' removed from '.$model->ref));
			$this->ajaxResult($mm);
		}
		$this->render('manage_connote',array('model'=>$model));
	}

	public function actionReceived($id){
		$model=$this->loadModel($id);
		$parcel = new ExParcel('search');
		$parcel->mids = $model->getFids();
		$dp = $parcel->search(false);


		$trans = Yii::app()->db->beginTransaction();
		try{
			foreach($dp->data as $r){
				if($r->status < 12){
					$r->status = 12;
					$r->save();
				}
			}
			$model->mdata['cc_status'] = 20;
			$model->save();
			$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}
		$this->ajaxResult($model);
	}

	public function actionExport($id){
		$model=$this->loadModel($id);
		$parcel = new ExParcel('search');
		$parcel->mids = $model->getFids();
		$dp = $parcel->search(false);

		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Location', 'WBN', 'Ref', 'Status', 'Agent', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State', 'Weight', 'Wt Check', 'Charge Wt', 'Type', 'Goods', 'Rating Code','Entry Goods', 'CN ID'));
		foreach($dp->data as $r){
			if(empty($r->cnee)) continue;
			$ln = $r->getLastLog();
			$rate = $r->getAgentRate();
			$temp = '';
			if (!empty($r->mdata['client_entry'])) {
				$cltent = $r->mdata['client_entry'];
				$items = $cltent['items'];
				if (isset($items['g']) && is_array($items['g'])) {
					foreach ($items['g'] as $index => $name) {
						$temp .= $items['q'][$index] . 'X' . $name . "   \n";
					}
				}
			}
			$cnid = empty($r->cnee->cnid_id)? '' : $r->cnee->cnid->no;
			$xls->addRow($i++, array($r->getLocation(), $r->hbn, '="'.$r->ref.'"', $r->getStatus(), empty($r->agent)? '' : $r->agent->name, $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->address, $r->cnee->state, $r->weight, $r->wtck, $r->chargeWeight(), $r->goodsType(), $r->GoodsNames(false, ', ', true), $rate[0], $temp, '="'.$cnid.'"'));
		}
		$xls->output('Receipt_'.$model->ref.'.xlsx');
	}

	public function actionExportAll() {
		$model = new PickupList('search');
		$model->unsetAttributes();
		if (isset($_GET['PickupList'])) {
			$model->attributes = $_GET['PickupList'];
		}
		$dp = $model->search(false);

		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth(array(30,15,15));
		$xls->addRow($i++, array('Created', 'Owner', 'Packs'));
		foreach ($dp->data as $r) {
			$xls->addRow($i++, array(substr($r->created, 0, 10), empty($r->fwd_id)? "" : $r->owner->shortName(2), $r->countLines()));
		}
		$xls->output('pickuplist_' . time() . '.xlsx');
	}

	public function actionReport(){
		if(empty($_POST)){
			$this->render('report');
		}else{
			$xls = new oExcel;
			$i = 1;
			switch($_GET['type']){
			case 'det':
				$q = 'type = 40 AND created >= :fd AND created <= date_add(:td, INTERVAL 1 DAY)';
				$p = [':fd' => $_POST['date']['from'], ':td' => $_POST['date']['to']];
				if(!empty($_POST['fwd_id'])){
					$q .= ' AND fwd_id = :aid';
					$p[':aid'] = $_POST['fwd_id'];
				}
				if(!empty($_POST['wh'])){
					$q .= ' AND dpt_id = :wid';
					$p[':wid'] = $_POST['wh'];
				}
				$rs = Manifest::model()->findAll(array('condition' => $q, 'params' => $p, 'order' => 'created desc'));
				$gt = ['Grand Total:', '', 'Packages:', 0, 'Total:', 0, 'Sum Total:', 0, 'Weight:', 0, 'Sum Weight:', 0, '', '', '', 0];

				$xls->addRow($i++, array('Date', 'Service', 'WBN', 'Status', 'Agent', 'Agent ID', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State', 'Goods', 'Entry', 'Weight', 'Wt Check', 'Charge Wt.', 'Type', 'Qty', 'Rating Code', 'Rate', 'Charge'));
				foreach($rs as $r){
					$pkgs = $r->countLines();
					$wts = $r->totWeight();
					//$xls->addRow($i++, array('Ref#:', $r->ref, 'Created:', $r->created, 'Packages:', $pkgs, 'Total:', @$r->mdata['total'], 'Weight: ', @$r->mdata['tweight'], 'Sum Total:', '', 'Sum Weight:', $wts, 'Rcpt #s:', str_replace("\n", '', @$r->mdata['rnos']), 'Driver:', @$r->mdata['driver']));
					$gt[3] += $pkgs;
					$gt[5] += (float) @$r->mdata['total'];
					$gt[7] += 0;
					$gt[9] += (float) @$r->mdata['tweight'];
					$gt[11] += $wts;
					$mids = $r->getFids();
					foreach($mids as $id){
						$p = ExParcel::model()->findByPk($id);
						if(empty($p->cnee)) continue;
						$typ = $p->goodsType();
						$ln = $p->getLastLog();
						$weight = $p->chargeWeight();
						$rate = $p->getAgentRate();
						$temp='';
						if(!empty($r->mdata['client_entry'])){
							$cltent = $r->mdata['client_entry'];
							$items= $cltent['items'];
							if(isset($items['g'])&&is_array($items['g'])){
								foreach($items['g'] as $index=>$name){
									$temp.=$items['q'][$index].'X'.$name."   \n";
								}
							}
						}
						$xls->addRow($i++, array($r->created, $p->getStyp(), $p->hbn, $p->getStatus(), empty($p->agent)? '' : $p->agent->name, $p->agent_id, $p->cnor->name, '="'.$p->cnor->tel.'"', $p->cnee->name, '="'.$p->cnee->tel.'"', $p->cnee->address, $p->cnee->state, $p->GoodsNames(false, ', ', true), $temp, $p->weight, $p->wtck, $weight, $typ, empty($p->eitems['q'])? 0 : array_sum($p->eitems['q']), $rate[0], $rate[1]->perkg, $rate[2]));
					}
					$gt[15] += $rate[2];
					//$xls->addRow($i++, []);
				}
				$xls->addRow($i++, $gt);
				$xls->output('pickup_detail_'.time().'.xlsx');
			break;
			case 'bal':
				$q = 'created >= :fd AND created <= date_add(:td, INTERVAL 1 DAY)';
				$p = [':fd' => $_POST['date']['from'], ':td' => $_POST['date']['to']];
				if(!empty($_POST['fwd_id'])){
					$q .= ' AND fwd_id = :aid';
					$p[':aid'] = $_POST['fwd_id'];
				}
				if(!empty($_POST['wh'])){
					$q .= ' AND dpt_id = :wid';
					$p[':wid'] = $_POST['wh'];
				}
				$rs = PickupList::model()->findAll($q, $p);

				// group by agent
				$agentsBalances = array();
				foreach ( $rs as $r ) {
					if ( !isset($agentsBalances[$r->fwd_id]) ) {
						$agentsBalances[$r->fwd_id] = array('totWeight' => 0 , 'totPacks' => 0,'totReceived' => 0,'totCharge' => 0,'totInvoiced' => 0,'data' => array());
					}
					$agentBalance = &$agentsBalances[$r->fwd_id];
					$agentBalance['data'][] = $r;
					$agentBalance['totWeight'] += $r->totWeight();
					if ( !empty($r->mdata['pkgs'] )  ) {
						$agentBalance['totPacks'] += $r->mdata['pkgs'];
					}
					$agentBalance['totReceived'] += $r->countLines();
					$agentBalance['totCharge'] +=  floatval($r->mdata['total']);
					$il = $r->getInvoiceLine();
					$agentBalance['totInvoiced'] += $il->amount;
				}

				$xls->addRow($i++, array('Date','ID','Agent','Weight', 'Packs', 'Received', 'Charge','Invoiced', 'Inv #', 'Note'));
				$allTotal = array('totWeight' => 0 , 'totPacks' => 0,'totReceived' => 0,'totCharge' => 0,'totInvoiced' => 0);
				foreach ( $agentsBalances as $agentId => $balance){
					foreach($balance['data'] as $r){
						$il = $r->getInvoiceLine();
						$xls->addRow($i++, array($r->created, $r->fwd_id, $r->owner->name, $r->totWeight(),empty($r->mdata['pkgs'])? '-' : $r->mdata['pkgs'], $r->countLines(), $r->mdata['total'], $il->amount, $il->invoice->no, ''));
					}
					// add total for the agent
					$xls->setFont('A' . $i . ':J' . $i, array('bold' => true));
					$xls->mergeCells('A' . $i . ':C' . $i);
					$xls->addRow($i++, array('Total','','',$balance['totWeight'],$balance['totPacks'],$balance['totReceived'],$balance['totCharge'],$balance['totInvoiced']));

					// add blank data
					$xls->addRow($i++,array());

					$allTotal['totWeight'] += $balance['totWeight'];
					$allTotal['totPacks'] += $balance['totPacks'];
					$allTotal['totReceived'] += $balance['totReceived'];
					$allTotal['totCharge'] += $balance['totCharge'];
					$allTotal['totInvoiced'] += $balance['totInvoiced'];
				}

				// add all total
				$xls->setFont('A' . $i . ':J' . $i, array('bold' => true));
				$xls->mergeCells('A' . $i . ':C' . $i);
				$xls->addRow($i++, array('All Total','','',$allTotal['totWeight'],$allTotal['totPacks'],$allTotal['totReceived'],$allTotal['totCharge'],$allTotal['totInvoiced']));

				$xls->output('pickup_balance_'.time().'.xlsx');
			break;

				case 'drv':
					$this->makeDriverReport();
					break;

				case 'drvsalary':
					$this->makeDriverSalaryReport();
					break;

				case 'top':
					$this->makeTopAgentReport();
					break;
			}
		}
	}

	/**
	 * make top agent report by items
	 */
	private function makeTopAgentReport(){
		$xls = new oExcel;
		$i = 1;
		$q = 'created >= :fd AND created <= date_add(:td, INTERVAL 1 DAY)';
		$p = [':fd' => $_POST['date']['from'], ':td' => $_POST['date']['to']];
		if(!empty($_POST['fwd_id'])){
			$q .= ' AND fwd_id = :aid';
			$p[':aid'] = $_POST['fwd_id'];
		}
		if(!empty($_POST['wh'])){
			$q .= ' AND dpt_id = :wid';
			$p[':wid'] = $_POST['wh'];
		}
		$rs = PickupList::model()->findAll($q, $p);

		// group by agent
		$agentsBalances = array();
		foreach ( $rs as $r ) {
			if ( !isset($agentsBalances[$r->fwd_id]) ) {
				$agentsBalances[$r->fwd_id] = array('id'=> 0, 'name' => '','totWeight' => 0 , 'totPacks' => 0,'totReceived' => 0,'totCharge' => 0,'totInvoiced' => 0,'data' => array());
			}
			$agentBalance = &$agentsBalances[$r->fwd_id];
			$agentBalance['id'] = $r->fwd_id;
			$agentBalance['name'] = $r->owner->name;
			$agentBalance['totWeight'] += $r->totWeight();
			if ( !empty($r->mdata['pkgs'] )  ) {
				$agentBalance['totPacks'] += floatval($r->mdata['pkgs']);
			}
			$agentBalance['totReceived'] += $r->countLines();
			$agentBalance['totCharge'] += floatval($r->mdata['total']);
			$il = $r->getInvoiceLine();
			$agentBalance['totInvoiced'] += $il->amount;
		}

		// sort by total packages
		$packages = array();
		foreach ($agentsBalances as $key => $row)
		{
			$packages[$key] = $row['totReceived'];
		}
		array_multisort($packages, SORT_DESC, $agentsBalances);

		$xls->addRow($i++, array('ID','Agent','Weight', 'Packs', 'Received', 'Charge','Invoiced'));
		$allTotal = array('totWeight' => 0 , 'totPacks' => 0,'totReceived' => 0,'totCharge' => 0,'totInvoiced' => 0);
		foreach ( $agentsBalances as $agentId => $balance){

			// add total for the agent
			$xls->addRow($i++, array($balance['id'],$balance['name'],$balance['totWeight'],$balance['totPacks'],$balance['totReceived'],$balance['totCharge'],$balance['totInvoiced']));

			$allTotal['totWeight'] += $balance['totWeight'];
			$allTotal['totPacks'] += $balance['totPacks'];
			$allTotal['totReceived'] += $balance['totReceived'];
			$allTotal['totCharge'] += $balance['totCharge'];
			$allTotal['totInvoiced'] += $balance['totInvoiced'];
		}

		// add all total
		$xls->setFont('A' . $i . ':J' . $i, array('bold' => true));
		$xls->mergeCells('A' . $i . ':B' . $i);
		$xls->addRow($i, array('All Total','',$allTotal['totWeight'],$allTotal['totPacks'],$allTotal['totReceived'],$allTotal['totCharge'],$allTotal['totInvoiced']));

		$xls->output('top_agent_'.time().'.xlsx');
	}

	/**
	 * make pickup report by driver
	 */
	private function makeDriverReport(){
		$xls = new oExcel;
		$i = 1;
		$q = 'type = 40 AND created >= :fd AND created <= :td';
		$p = [':fd' => $_POST['date']['from'] . ' 00:00:00', ':td' => $_POST['date']['to'] .  ' 23:59:59'];

		// filter by agent
		if ( !empty($_POST['fwd_id']) ) {
			$q .= ' AND fwd_id = :aid';
			$p[':aid'] = $_POST['fwd_id'];
		}

		// filter by driver
		if ( !empty($_POST['did']) && $_POST['did'] > 0 ) {
			$q .= ' AND by_id = :did';
			$p[':did'] = $_POST['did'];
		}

		// filter by warehouse
		if ( !empty($_POST['wh']) ) {
			$q .= ' AND dpt_id = :wid';
			$p[':wid'] = $_POST['wh'];
		}

		$xls->setColWidth(array(40,20,20));
		$rs = Manifest::model()->findAll(['condition' => $q, 'params' => $p, 'order' => 'by_id']);

		// group all pickup items information by Driver and Agent
		$driverAgents = array();
		foreach($rs as $r){
			if ( !isset($driverAgents[$r->by_id]) ) {
				$driverAgents[$r->by_id] = array( 'agents' => array(),'totPks' => 0,'totWeight' => 0,'firstItemId' => PHP_INT_MAX);
				$driver = &$driverAgents[$r->by_id];
			}

			$pkgs = $r->countLines();
			$wts = $r->totWeight();
			if ( !isset($driver['agents'][$r->fwd_id]) ) {
				$driver['agents'][$r->fwd_id] = array('name' => $r->owner->name,'pks' => 0,'wts' => 0);
			}
			$driver['agents'][$r->fwd_id]['pks'] += $pkgs;
			$driver['agents'][$r->fwd_id]['wts'] += $wts;
			$driver['totPks'] += $pkgs;
			$driver['totWeight'] += $wts;

			// first pickuped item id
			$mids = $r->getFids();
			if (!empty($mids) ) {
				$smallOne = min($mids);
				if ($smallOne < $driver['firstItemId'])  $driver['firstItemId'] = $smallOne;
			}

		}

		// output all agent pickuped items sum information
		foreach( $driverAgents as $dvr => $info ) {
			// output driver name
			$dvr = User::model()->findByPk($dvr);
			$xls->setFont('A' . $i . ':B' . $i, array('bold' => true));
			$xls->addRow($i++, array('Driver:', $dvr->name . '(' . $dvr->id . ')'));

			// output date from and to time
			$xls->addRow($i++, array('Date From:', $p[':fd']));
			$xls->addRow($i++, array('Date To:', $p[':td']));

			// dummy positon for first item pickuped time
			$cellIndexPickupTime = $i;
			$xls->setFont('A' . $i . ':B' . $i, array('bold' => true));
			$xls->addRow($i++, array('First Item Pickuped Time:', '0000-00-00 00:00:00'));

			$xls->addRow($i++, array('Agent ID', 'Agent', 'Packages','Weight'));
			foreach ($info['agents'] as $aid => $agent) {
				$xls->addRow($i++, array($aid, $agent['name'], $agent['pks'], $agent['wts']));
			}

			$xls->setFont('A' . $i . ':C' . $i, array('bold' => true));
			$xls->addRow($i++, array('', 'Total',  $info['totPks'],  $info['totWeight']));

			// try to calculate the first item pickuped time
			$exp = ExParcel::model()->findByPk($info['firstItemId']);
			if (!empty($exp)) {
				if (!empty($exp->tracks)) {
					foreach ($exp->tracks as $track) {
						if ($track->type == 12) { // pickup time
							$xls->setCell('B' . $cellIndexPickupTime, $track->dt);
						}
					}
				}
			}
			// add two blank rows
			$xls->addRow($i++, array());
			$xls->addRow($i++, array());
		}
		$xls->output('driver_pickup_detail_'.time().'.xlsx');
	}

	/**
	 * make pickup report by driver
	 */
	private function makeDriverSalaryReport(){
		$xls = new oExcel;
		$q = 'type = 40 AND created >= :fd AND created < :td';
		$p = array();

		// filter by agent
		if ( !empty($_POST['fwd_id']) ) {
			$q .= ' AND fwd_id = :aid';
			$p[':aid'] = $_POST['fwd_id'];
		}

		// filter by driver
		if ( !empty($_POST['did']) && $_POST['did'] > 0 ) {
			$q .= ' AND by_id = :did';
			$p[':did'] = $_POST['did'];
		}

		// filter by warehouse
		if ( !empty($_POST['wh']) ) {
			$q .= ' AND dpt_id = :wid';
			$p[':wid'] = $_POST['wh'];
		}

		// start on Monday
		$fd = $_POST['date']['from'];
		$wd = date('N', strtotime($fd));
		if ($wd != 1) {
			$fd = date('Y-m-d', strtotime($fd . ' - ' . ($wd - 1) . ' day'));
		}
		// end on Sunday
		$td = $_POST['date']['to'];
		$wd = date('N', strtotime($td));
		if ($wd != 7) {
			$td = date('Y-m-d', strtotime($td . ' + ' . (7 - $wd) . ' day'));
		}

		// print as week
		$index = 0;
		while (strtotime($td) > strtotime($fd)) {
			$i = 1;
			$p[':fd'] = date('Y-m-d', strtotime($td . ' -6 day'));
			$p[':td'] = $td;
			$rs = Manifest::model()->findAll(['condition' => $q, 'params' => $p, 'order' => 'by_id']);

			if ($index) $xls->createSheet();
			$xls->goSheet($index);
			$xls->setTitle(date('m-d', strtotime($p[':fd'])) . ' ~ ' . date('m-d', strtotime($p[':td'])));
			$xls->setColWidth(array(20,15,15,15,15,15,25,30,15));

			if (!empty($rs)) {
				// group all pickup items information by Driver and Agent
				$driverAgents = array();
				foreach($rs as $r){
					if ( !isset($driverAgents[$r->by_id]) ) {
						$driverAgents[$r->by_id] = array('agents' => array(), 'pkgs' => array(
							'dayPks' => array(),
							'totalOtherPks' => 0, // 提货
							'totalCompanyPks' => 0, // 公司客
							'totalOther1Pks' => 0, // 销售且提货
							'totalOther2Pks' => 0, // 销售不提货
							'totalDays' => 0,
						));
						$driver = &$driverAgents[$r->by_id];
					}

					$pkgs = $r->countLines();
					if ( !isset($driver['agents'][$r->fwd_id]) ) {
						$driver['agents'][$r->fwd_id] = array('name' => $r->owner->name, 'pks' => 0);
					}
					$driver['agents'][$r->fwd_id]['pks'] += $pkgs;

					if (!empty($r->owner->extra['company_customer'])) {
						// 公司客
						if (!isset($driver['pkgs']['dayPks']['company'.date('N', strtotime($r->created))])) {
							$driver['pkgs']['dayPks']['company'.date('N', strtotime($r->created))] = 0;
						}
						$driver['pkgs']['dayPks']['company'.date('N', strtotime($r->created))] += $pkgs;
						$driver['pkgs']['totalCompanyPks'] += $pkgs;
					} else {
						if (!empty($r->owner->extra['op_id'])) {
							if ($r->owner->extra['op_id'] == $r->by_id) {
								// 销售且提货
								if (!isset($driver['pkgs']['dayPks']['other1'.date('N', strtotime($r->created))])) {
									$driver['pkgs']['dayPks']['other1'.date('N', strtotime($r->created))] = 0;
								}
								$driver['pkgs']['dayPks']['other1'.date('N', strtotime($r->created))] += $pkgs;
								$driver['pkgs']['totalOther1Pks'] += $pkgs;
							} else {
								// 提货
								if (!isset($driver['pkgs']['dayPks']['other'.date('N', strtotime($r->created))])) {
									$driver['pkgs']['dayPks']['other'.date('N', strtotime($r->created))] = 0;
								}
								$driver['pkgs']['dayPks']['other'.date('N', strtotime($r->created))] += $pkgs;
								$driver['pkgs']['totalOtherPks'] += $pkgs;

								// 销售不提货
								if (!isset($driverAgents[$r->owner->extra['op_id']]['pkgs']['dayPks']['other2'.date('N', strtotime($r->created))])) {
									$driverAgents[$r->owner->extra['op_id']]['pkgs']['dayPks']['other2'.date('N', strtotime($r->created))] = 0;
								}
								$driverAgents[$r->owner->extra['op_id']]['pkgs']['dayPks']['other2'.date('N', strtotime($r->created))] += $pkgs;

								$driverAgents[$r->owner->extra['op_id']]['pkgs']['totalDays'] = 0;
								if (!isset($driverAgents[$r->owner->extra['op_id']]['pkgs']['totalOtherPks'])) {
									$driverAgents[$r->owner->extra['op_id']]['pkgs']['totalOtherPks'] = 0;
								}
								if (!isset($driverAgents[$r->owner->extra['op_id']]['pkgs']['totalCompanyPks'])) {
									$driverAgents[$r->owner->extra['op_id']]['pkgs']['totalCompanyPks'] = 0;
								}
								if (!isset($driverAgents[$r->owner->extra['op_id']]['pkgs']['totalOther1Pks'])) {
									$driverAgents[$r->owner->extra['op_id']]['pkgs']['totalOther1Pks'] = 0;
								}
								if (!isset($driverAgents[$r->owner->extra['op_id']]['pkgs']['totalOther2Pks'])) {
									$driverAgents[$r->owner->extra['op_id']]['pkgs']['totalOther2Pks'] = 0;
								}
								$driverAgents[$r->owner->extra['op_id']]['pkgs']['totalOther2Pks'] += $pkgs;
							}
						} else {
							// 提货
							if (!isset($driver['pkgs']['dayPks']['other'.date('N', strtotime($r->created))])) {
								$driver['pkgs']['dayPks']['other'.date('N', strtotime($r->created))] = 0;
							}
							$driver['pkgs']['dayPks']['other'.date('N', strtotime($r->created))] += $pkgs;
							$driver['pkgs']['totalOtherPks'] += $pkgs;
						}
					}
				}

				// output all agent pickuped items sum information
				$xls->setFont('A' . $i . ':K' . ($i+1), array('bold' => true));
				$xls->addRow($i++, array('', '公司客', '提货', '销售+提货', '销售'));
				$xls->addRow($i++, array('Driver', date('m月d日', strtotime($p[':fd'])), date('m月d日', strtotime($p[':fd'] . ' +1 day')), date('m月d日', strtotime($p[':fd'] . ' +2 day')), date('m月d日', strtotime($p[':fd'] . ' +3 day')), date('m月d日', strtotime($p[':fd'] . ' +4 day')), 'Average', 'Amount / $', 'Total / $'));
				foreach( $driverAgents as $dvr => $info ) {
					for ($day = 1; $day <= 7; $day++) {
						if (!empty($info['pkgs']['dayPks']['other'.$day]) || !empty($info['pkgs']['dayPks']['company'.$day]) || !empty($info['pkgs']['dayPks']['other1'.$day])) {
							$info['pkgs']['totalDays'] ++;
						}
					}
					// output driver name
					$dvr = User::model()->findByPk($dvr);
					$days = max($info['pkgs']['totalDays'], 1);
					$avg_company = $info['pkgs']['totalCompanyPks'] / $days;
					$avg_other = $info['pkgs']['totalOtherPks'] / $days;
					$avg_other1 = $info['pkgs']['totalOther1Pks'] / $days;
					$avg_other2 = $info['pkgs']['totalOther2Pks'] / $days;

					$amount = 120;
					$amount_company = 0.1 * $avg_company;
					$amount_other = 0.15 * $avg_other;
					$amount_other1 = 0.3 * $avg_other1;
					$amount_other2 = 0.1 * $avg_other2;

					$xls->setFont('A' . $i . ':A' . $i, array('bold' => true));
					$xls->addRow($i++, array($dvr->name . ' (' . $dvr->id . ')',
						(empty($info['pkgs']['dayPks']['company1']) ? 0 : $info['pkgs']['dayPks']['company1']) . ' / ' . (empty($info['pkgs']['dayPks']['other1']) ? 0 : $info['pkgs']['dayPks']['other1']) . ' / ' . (empty($info['pkgs']['dayPks']['other11']) ? 0 : $info['pkgs']['dayPks']['other11']) . ' / ' . (empty($info['pkgs']['dayPks']['other21']) ? 0 : $info['pkgs']['dayPks']['other21']),
						(empty($info['pkgs']['dayPks']['company2']) ? 0 : $info['pkgs']['dayPks']['company2']) . ' / ' . (empty($info['pkgs']['dayPks']['other2']) ? 0 : $info['pkgs']['dayPks']['other2']) . ' / ' . (empty($info['pkgs']['dayPks']['other12']) ? 0 : $info['pkgs']['dayPks']['other12']) . ' / ' . (empty($info['pkgs']['dayPks']['other22']) ? 0 : $info['pkgs']['dayPks']['other22']),
						(empty($info['pkgs']['dayPks']['company3']) ? 0 : $info['pkgs']['dayPks']['company3']) . ' / ' . (empty($info['pkgs']['dayPks']['other3']) ? 0 : $info['pkgs']['dayPks']['other3']) . ' / ' . (empty($info['pkgs']['dayPks']['other13']) ? 0 : $info['pkgs']['dayPks']['other13']) . ' / ' . (empty($info['pkgs']['dayPks']['other23']) ? 0 : $info['pkgs']['dayPks']['other23']),
						(empty($info['pkgs']['dayPks']['company4']) ? 0 : $info['pkgs']['dayPks']['company4']) . ' / ' . (empty($info['pkgs']['dayPks']['other4']) ? 0 : $info['pkgs']['dayPks']['other4']) . ' / ' . (empty($info['pkgs']['dayPks']['other14']) ? 0 : $info['pkgs']['dayPks']['other14']) . ' / ' . (empty($info['pkgs']['dayPks']['other24']) ? 0 : $info['pkgs']['dayPks']['other24']),
						(empty($info['pkgs']['dayPks']['company5']) ? 0 : $info['pkgs']['dayPks']['company5']) . ' / ' . (empty($info['pkgs']['dayPks']['other5']) ? 0 : $info['pkgs']['dayPks']['other5']) . ' / ' . (empty($info['pkgs']['dayPks']['other15']) ? 0 : $info['pkgs']['dayPks']['other15']) . ' / ' . (empty($info['pkgs']['dayPks']['other25']) ? 0 : $info['pkgs']['dayPks']['other25']),
						number_format($avg_company, 2) . ' / ' . number_format($avg_other, 2) . ' / ' . number_format($avg_other1, 2) . ' / ' . number_format($avg_other2, 2),
						'$' . number_format($amount_company * $days, 2) . ' / ' . '$' . number_format($amount_other * $days, 2) . ' / ' . '$' . number_format($amount_other1 * $days, 2) . ' / ' . '$' . number_format($amount_other2 * $days, 2),
						'$' . number_format(round($amount_company * $days, 2) + round($amount_other * $days, 2) + round($amount_other1 * $days, 2) + round($amount_other2 * $days, 2) + 120 * $info['pkgs']['totalDays'], 2)
					));

					// add two blank rows
					$xls->addRow($i++, array());
				}
			}

			$td = date('Y-m-d', strtotime($td . ' -7 day'));
			$index ++;
		}
		
		$xls->output('driver_pickup_detail_'.time().'.xlsx');
	}

	public function actionReportSetting() {
		if (!empty($_POST['Settings'])) {
			$f = Yii::app()->basePath.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'settings.php';
			$set = Yii::app()->params['settings'];
			foreach ($set as $k => $v) {
				if ($k == 'drv_sal_rate0' || $k == 'drv_sal_rate1' || $k == 'drv_sal_rate1_from' || $k == 'drv_sal_rate2' || $k == 'drv_sal_rate2_from' || $k == 'drv_sal_rate3' || $k == 'drv_sal_rate3_from') {
					$set[$k]['value'] = $_POST['Settings'][$k]['value'];
				}
			}
			Yii::app()->params['settings'] = $set;
			$r = var_export(Yii::app()->params['settings'], true);
			file_put_contents($f, "<?php\nreturn ".$r.';');
			$r = new stdClass;
			$r->done = true;
			$r->msg = 'Settings Saved!';
			echo json_encode($r);
			Yii::app()->end();
		}
		$this->render('report_settings');
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new PickupList('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['PickupList']))
			$model->attributes=$_GET['PickupList'];

		if(!empty($_GET['PickupList']['hbn'])) $model->hbn = $_GET['PickupList']['hbn'];

		$this->render('list',array(
			'model'=>$model,
		));
	}

	public function actionNotes($id){
		$model=$this->loadModel($id);
		if(!empty($_POST['notes'])){
			$log = Log::add($model, 6, array('notes' => $_POST['notes']));
			$this->ajaxResult($log);
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=PickupList::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='shipment-receipt-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
