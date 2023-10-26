<?php
class JobController extends Controller{

	public function actionList(){
		if(!empty($_GET)){
			$model = new WmsJob('search');
			$model->unsetAttributes();
			$ec = new CDbCriteria;
			if(!empty($_GET['org'])) $model->cust_name = $_GET['org'];

			$ec->addCondition('(t.no LIKE :ref OR t.po LIKE :ref OR t.ref = :ref)');
			$ec->params = [':ref' => '%'.$_GET['ref'].'%'];
			$dp = $model->search(true, 10, $ec);
			$pg = empty($_GET['WmsJob_page'])? 1 : $_GET['WmsJob_page'];
			$more_url = $pg * 10 < $dp->totalItemCount? $this->createUrl('list', ['ref' => $_GET['ref'], 'org' => $_GET['org'], 'WmsJob_page' => $pg + 1]) : '';
			echo json_encode(['done' => true, 'data' => $this->renderPartial('search_result', ['dp' => $dp], true), 'url' => $more_url]);
			return;
		}
		$this->render('list');
	}

	public function actionDemand($id) {
		$model = WmsTask::model()->findByPk($id);
		if (!empty($_POST)) {
			foreach ($_POST as $k => $v) {
				$model->mainTask->mdata[$k] = $v;
			}

			$volumn = 0;
			$weight = 0;
			for ($i = 1; $i < count($_POST) / 3; $i ++) {
				if (!empty($_POST['length_'.$i])) {
					$model->mainTask->mdata['volumn_'.$i] = floatval($_POST['length_'.$i]) * floatval($_POST['width_'.$i]) * floatval($_POST['height_'.$i]);
					$volumn += $model->mainTask->mdata['volumn_'.$i];
					$weight = floatval($_POST['weight_'.$i]);
				}
			}
			$model->mainTask->mdata['pi_volumn_number'] = $volumn;
			$model->mainTask->mdata['pi_weight_number'] = $weight;

			$model->mainTask->mdata['res'] = date('Y-m-d H:i:s');
			$model->mainTask->save();
			$this->ajaxResult($model);
		}
		$this->renderPartial('demand', ['model' => $model]);
	}

	public function actionPlt($id) {
		function randVc() {
			return chr(rand(97,122)) . rand(10,99) . chr(rand(97,122));
		}
		$model = WmsTask::model()->findByPk($id);
		if (!empty($_POST)) {
			if (empty($model->mainTask->mdata['simple_in'])) {
				$plt = WmsLocation::model()->find('code = :plt', array(':plt' => $_POST['pl']));
				foreach ($model->items as $item) {
					if ($item->mdata['pl'] == $_POST['pl']) {
						$plt->extra = $_POST['meta'];
						$plt->update('extra');
						$plt->depth = @$_POST['meta']['length'];
						$plt->width = @$_POST['meta']['width'];
						$plt->height = @$_POST['meta']['height'];
						$plt->wt = @$_POST['meta']['weight'];
						$plt->update('depth', 'width', 'height', 'wt');
						$this->ajaxResult($model, ['plt' => $_POST['pl'], 'length' => $plt->depth, 'width' => $plt->width, 'height' => $plt->height, 'weight' => $plt->wt]);
					}
				}
				$plt = new WmsLocation;
				$plt->addError('code', 'Pallet does not belong to this task');
				$this->ajaxResult($plt);
			} else {
				$plt = WmsLocation::model()->find('code = :plt', array(':plt' => $_POST['pl']));

				// already have plt
				foreach ($model->items as $item) {
					if ($item->mdata['pl'] == $_POST['pl']) {
						$plt = WmsLocation::model()->find('code = :plt', array(':plt' => $_POST['pl']));
						$plt->extra = $_POST['meta'];
						$plt->update('extra');
						$plt->depth = @$_POST['meta']['length'];
						$plt->width = @$_POST['meta']['width'];
						$plt->height = @$_POST['meta']['height'];
						$plt->wt = @$_POST['meta']['weight'];
						$plt->notes = @$_POST['note'];
						$plt->update('depth', 'width', 'height', 'wt', 'notes');
						$this->ajaxResult($model, ['plt' => $_POST['pl'], 'length' => $plt->depth, 'width' => $plt->width, 'height' => $plt->height, 'weight' => $plt->wt, 'type' => $_POST['meta']['type'], 'port' => oList::kvp('ex_port')[$_POST['meta']['port']], 'item' => '<div class="del_item pull-right" href="'.$this->createUrl('job/delItem',['id' => $item->id]).'" data-vc="'.randVc().'"><span class="icon icon-trash" style="font-size:1.2em"></span></div>']);
					}
				}

				// no plt
				$prod = WmsProd::model()->find('name = "MISC PALLET"');
				$item = new WmsTaskItem;
				$item->task_id = $model->id;
				$item->mdata = array(
					'gi' => $prod->id,
					'gn' => $prod->name,
					'pl' => $_POST['pl'],
					'uq' => 1,
					'cq' => '',
					'ex' => '',
					'bn' => '',
					'nt' => '',
				);
				$item->save();

				$plt = WmsLocation::model()->find('code = :plt', array(':plt' => $_POST['pl']));
				$plt->extra = $_POST['meta'];
				$plt->update('extra');
				$plt->depth = @$_POST['meta']['length'];
				$plt->width = @$_POST['meta']['width'];
				$plt->height = @$_POST['meta']['height'];
				$plt->wt = @$_POST['meta']['weight'];
				$plt->notes = @$_POST['note'];
				$plt->update('depth', 'width', 'height', 'wt', 'notes');
				$this->ajaxResult($model, ['plt' => $_POST['pl'], 'length' => $plt->depth, 'width' => $plt->width, 'height' => $plt->height, 'weight' => $plt->wt, 'type' => $_POST['meta']['type'], 'port' => oList::kvp('ex_port')[$_POST['meta']['port']], 'item' => '<div class="del_item pull-right" href="'.$this->createUrl('job/delItem',['id' => $item->id]).'" data-vc="'.randVc().'"><span class="icon icon-trash" style="font-size:1.2em"></span></div>']);
			}
		} else {
			if (empty($model->mainTask->mdata['simple_in'])) {
				$plts = [];
				foreach ($model->items as $item) {
					$plt = WmsLocation::model()->find('code = :plt', array(':plt' => $item->mdata['pl']));
					$plts[] = ['plt' => $plt->code, 'length' => @$plt->depth, 'width' => @$plt->width, 'height' => @$plt->height, 'weight' => @$plt->wt];
				}
				$this->renderPartial('plt', ['model' => $model, 'plts' => $plts]);
			} else {
				$plts = [];
				foreach ($model->items as $item) {
					$plt = WmsLocation::model()->find('code = :plt', array(':plt' => $item->mdata['pl']));
					$plts[] = ['plt' => $plt->code, 'length' => @$plt->depth, 'width' => @$plt->width, 'height' => @$plt->height, 'weight' => @$plt->wt, 'type' => @$plt->extra['type'], 'port' => !empty($plt->extra['port']) ? oList::kvp('ex_port')[$plt->extra['port']] : '', 'item' => '<div class=\"del_item pull-right\" href=\"'.$this->createUrl('job/delItem',['id' => $item->id]).'\" data-vc=\"'.randVc().'\"><span class=\"icon icon-trash\" style=\"font-size:1.2em\"></span></div>'];
				}
				$this->renderPartial('plt', ['model' => $model, 'plts' => $plts]);
			}
		}
	}

	public function actionPltSunnya($id)
	{
		$model = WmsTask::model()->findByPk($id);
		if (empty($_POST)) {
			$this->renderPartial('plt_sunnya', ['model' => $model]);
		} else {
			$model->mainTask->mdata['plt_sunnya'] = $_POST['plt'];
			$model->mainTask->update('meta');
			$this->ajaxResult($model);
		}
	}

	public function actionGetPltInfo()
	{
		$plt = WmsLocation::model()->find('name = :n OR code = :n', [':n' => $_GET['plt']]);
		$this->ajaxResult($plt, ['data' => $this->renderPartial('_plt', ['model' => $plt], true)]);
	}

	public function actionSetPltInfo()
	{
		if (empty($_POST)) {
			$this->render('set_plt');
		} else {
			$plt = WmsLocation::model()->find('name = :n OR code = :n', [':n' => $_POST['plt']]);
			$plt->extra = $_POST['meta'];
			$plt->update('extra');
			$plt->depth = @$_POST['meta']['length'];
			$plt->width = @$_POST['meta']['width'];
			$plt->height = @$_POST['meta']['height'];
			$plt->wt = @$_POST['meta']['weight'];
			$plt->update('depth', 'width', 'height', 'wt');
			$this->ajaxResult($plt, ['data' => $this->renderPartial('_plt', ['model' => new WmsLocation], true)]);
		}
	}

	public function actionNote($id) {
		$model = WmsTask::model()->findByPk($id);
		if (!empty($_POST)) {
			$model->mainTask->mdata['res'] = date('Y-m-d H:i:s');
			$model->mainTask->save();
			Log::add($model->mainTask, 6, array('notes' => $_POST['notes']));
			$this->ajaxResult($model);
		} else {
			$logs = Log::model()->findAll('model = :model AND lid = :lid AND type = :type order by time desc', array(':model' => 'WmsTask', ':lid' => $model->mainTask->id, ':type' => Log::LOG_TYPE_NOTES));
			$this->renderPartial('note', ['model' => $model, 'logs' => $logs]);
		}
	}

	public function actionSummary($id){
		$model = WmsTask::model()->findByPk($id);
		$this->renderPartial('summary', ['model' => $model]);
	}

	public function actionCourier($id) {
		$model = WmsTask::model()->findByPk($id);
		if (!empty($_POST)) {
			if (empty($model->mainTask->deliveryTask->ref) && empty($model->mainTask->deliveryTask->mdata['shipment_id'])) {
				$model->addError('ref', 'Courier No has not been recorded in this task');
				$this->ajaxResult($model);
			} else {
				$flag = 1;
				foreach ($model->items as $item) {
					$stock = WmsStock::model()->findByPk($item->mdata['si']);
					if ($stock->prod->ean == strtoupper($_POST['prod']) || $stock->prod->name == strtoupper($_POST['prod'])) $flag = 0;
				}
				if ($flag) {
					$model->addError('prod', 'This product does not belong to this task');
					$this->ajaxResult($model);
				}

				// provided label
				if (preg_match('/^019931265099999891(\w{3,5}\d{7}\d{11})$/i', $_POST['courier'], $m)) {
					if ($m[1] == trim($model->mainTask->deliveryTask->ref) || '019931265099999891' . $m[1] == trim($model->mainTask->deliveryTask->ref)) {
						$model->mainTask->status = 99;
						$model->mainTask->update('status');
						$this->ajaxResult($model, [], 'Match successfully');
					}
				}
				if (preg_match('/^019931265099999891(\w{3,5}\d{7}\d{11})\d{23}$/i', $_POST['courier'], $m)) {
					if ($m[1] == trim($model->mainTask->deliveryTask->ref) || '019931265099999891' . $m[1] == trim($model->mainTask->deliveryTask->ref)) {
						$model->mainTask->status = 99;
						$model->mainTask->update('status');
						$this->ajaxResult($model, [], 'Match successfully');
					}
				}
				if (preg_match('/^019931265099999891(\w{3,5}\d{7}\d{13})$/i', $_POST['courier'], $m)) {
					if ($m[1] == trim($model->mainTask->deliveryTask->ref) || '019931265099999891' . $m[1] == trim($model->mainTask->deliveryTask->ref)) {
						$model->mainTask->status = 99;
						$model->mainTask->update('status');
						$this->ajaxResult($model, [], 'Match successfully');
					}
				}

				// our label
				if (!empty($model->mainTask->deliveryTask->mdata['shipment_id'])) {
					foreach ($model->mainTask->deliveryTask->mdata['shipment_id'] as $shipment) {
						$shipment = Shipment::model()->findByPk($shipment);

						// for fastway
						if ($shipment->ref === $_POST['courier']) {
							$model->mainTask->status = 99;
							$model->mainTask->update('status');
							$this->ajaxResult($model, [], 'Match successfully');
						}

						// for aupost
						if (preg_match('/(AMQ\d{7})/i', $_POST['courier'], $m)) {
							if ($m[1] == $shipment->ref) {
								$model->mainTask->status = 99;
								$model->mainTask->update('status');
								$this->ajaxResult($model, [], 'Match successfully');
							}
						}

						// for startrack
						if (preg_match('/(7RFZ\d{8})EXP/i', $_POST['courier'], $m)) {
							if ($m[1] == $shipment->ref) {
								$model->mainTask->status = 99;
								$model->mainTask->update('status');
								$this->ajaxResult($model, [], 'Match successfully');
							}
						}

						// for tnt
						if (preg_match('/^610412\d{18}0\d{4}0$/', $_POST['courier'], $m)) {
							$prefix = TntAPI::decodePrefix(substr($m[0], 6, 6));
							$ref = $prefix.substr($m[0], 12, 9);
							if ($ref == $shipment->ref) {
								$model->mainTask->status = 99;
								$model->mainTask->update('status');
								$this->ajaxResult($model, [], 'Match successfully');
							}
						}
					}
				}

				$model->addError('ref', 'This courier label does not belong to this task');
				$this->ajaxResult($model);
			}
		} else {
			$this->renderPartial('courier', ['model' => $model]);
		}
	}

	public function actionLabel($id)
	{
		$model = WmsTask::model()->findByPk($id);
		if (!empty($_POST)) {
			$prod = WmsProd::model()->with('packs', 'orgs')->find('t.status = 1 AND (t.name = :ref OR t.ean = :ref OR packs.barcode = :ref OR orgs.sku = :ref)', [':ref' => $_POST['prod']]);
			$shipment = Shipment::model()->find('hbn = :ref OR ref = :ref', [':ref' => $_POST['shipment']]);

			if (empty($prod)) {
				$model->addError('id', 'Product barcode ' . $_POST['prod'] . ' is invalid');
			} else if (empty($shipment)) {
				$model->addError('id', 'Shipment barcode ' . $_POST['shipment'] . ' is invalid');
			} else {
				$shipment->eitems['g'][] = $prod->name;
				$shipment->update('items');
			}
			$this->ajaxResult($model, [], 'Match successfully');
		} else {
			$this->renderPartial('label', ['model' => $model]);
		}
	}

	public function actionHistoryLabel($id)
	{
		$model = WmsTask::model()->findByPk($id);
		$this->renderPartial('history_label', ['model' => $model]);
	}

	public function actionDelLabel($id, $line)
	{
		$shipment = Shipment::model()->findByPk($id);
		unset($shipment->eitems['g'][$line]);
		$shipment->eitems['g'] = array_values($shipment->eitems['g']);
		$shipment->update('items');
	}

	public function actionOther($id) {
		$model = WmsTask::model()->findByPk($id);
		if (!empty($_POST)) {
			$mainTask = $model->mainTask;

			$mainTask->mdata['other_plain'] = @$_POST['other_plain'];
			$mainTask->mdata['other_chep'] = @$_POST['other_chep'];
			$mainTask->mdata['other_wood'] = @$_POST['other_wood'];
			$mainTask->mdata['other_other'] = @$_POST['other_other'];
			$mainTask->update('meta');

			$quote = WmsOrgQuote::model()->find('org_id = :org_id and status = 1 and vfrom <= :fd and vto >= :td', [':org_id' => $model->job->org_id, ':fd' => date('Y-m-d'), ':td' => date('Y-m-d')]);
			// plain
			$line = InvLine::model()->find('fid = :id AND model = "WmsInvoiceLine" AND ccode = "plain"', array(':id' => $mainTask->id));
			if (!empty($mainTask->mdata['other_plain']) && empty($line)) {
				$line = new InvLine;
				$line->inv_id = 0;
				$line->model = 'WmsInvoiceLine';
				$line->fid = $mainTask->id;
				$line->ccode = 'plain';
				$line->tax = 'OUTPUT';
				$line->qty = 1;
				$line->amount = $quote->mdata[WmsOrgQuote::QUOTE_MISC_CHANGE_PALLET];
				$line->det = WmsOrgQuote::$chgcodes[WmsOrgQuote::QUOTE_MISC_CHANGE_PALLET][0];
				$line->gst = round($line->amount * 10, 2) / 100;
				$line->mdata['items'] = [];
				$line->mdata['items'][] = [$mainTask->getNo(), ucwords($mainTask->ref), $mainTask->compl_time, $mainTask->getType() . ' - ' . $line->det, $line->amount, 1, $line->amount];
				$line->save();
			} else if (empty($mainTask->mdata['other_plain']) && !empty($line)) {
				$line->delete();
			}

			// chep
			$line = InvLine::model()->find('fid = :id AND model = "WmsInvoiceLine" AND ccode = "chep"', array(':id' => $mainTask->id));
			if (!empty($mainTask->mdata['other_chep']) && empty($line)) {
				$line = new InvLine;
				$line->inv_id = 0;
				$line->model = 'WmsInvoiceLine';
				$line->fid = $mainTask->id;
				$line->ccode = 'chep';
				$line->tax = 'OUTPUT';
				$line->qty = 1;
				$line->amount = $quote->mdata[WmsOrgQuote::QUOTE_MISC_PALLET_HIRE_DAY];
				$line->det = WmsOrgQuote::$chgcodes[WmsOrgQuote::QUOTE_MISC_PALLET_HIRE_DAY][0];
				$line->gst = round($line->amount * 10, 2) / 100;
				$line->mdata['items'] = [];
				$line->mdata['items'][] = [$mainTask->getNo(), ucwords($mainTask->ref), $mainTask->compl_time, $mainTask->getType() . ' - ' . $line->det, $line->amount, 1, $line->amount];
				$line->save();
			} else if (empty($mainTask->mdata['other_chep']) && !empty($line)) {
				$line->delete();
			}

			// wood
			$line = InvLine::model()->find('fid = :id AND model = "WmsInvoiceLine" AND ccode = "wood"', array(':id' => $mainTask->id));
			if (!empty($mainTask->mdata['other_wood']) && empty($line)) {
				$line = new InvLine;
				$line->inv_id = 0;
				$line->model = 'WmsInvoiceLine';
				$line->fid = $mainTask->id;
				$line->ccode = 'wood';
				$line->tax = 'OUTPUT';
				$line->qty = 1;
				$line->amount = $quote->mdata[WmsOrgQuote::QUOTE_MISC_PLASTIC_PALLET];
				$line->det = WmsOrgQuote::$chgcodes[WmsOrgQuote::QUOTE_MISC_PLASTIC_PALLET][0];
				$line->gst = round($line->amount * 10, 2) / 100;
				$line->mdata['items'] = [];
				$line->mdata['items'][] = [$mainTask->getNo(), ucwords($mainTask->ref), $mainTask->compl_time, $mainTask->getType() . ' - ' . $line->det, $line->amount, 1, $line->amount];
				$line->save();
			} else if (empty($mainTask->mdata['other_wood']) && !empty($line)) {
				$line->delete();
			}
			$this->ajaxResult($model);
		} else {
			$this->renderPartial('other', ['model' => $model]);
		}
	}

	public function actionPicking(){
		$this->render('picking');
	}

	public function actionGatepass(){
		$this->render('gatepass');
	}

	public function actionStocktake(){
		$this->render('stocktake');
	}

	public function actionHistory($id){
		$model = WmsTask::model()->findByPk($id);
		$this->renderPartial('history', ['model' => $model]);
	}

	public function actionRecSerial($id)
	{
		$model = WmsTaskItem::model()->findByPk($id);
		if(!empty($_POST['sn'])){
			$r = ['done' => true, 'msg' => ''];
			$wsn = new WmsSerialNo('create');
			$wsn->task_id = $model->task->link_id;
			$wsn->stock_id = $model->mdata['si'];
			$wsn->type = substr($model->task->type, 0, 2);
			$wsn->sn = $_POST['sn'];
			$wsn->location_id = $model->mdata['pli'];

			if($wsn->save()){
				$r['id'] = $wsn->id;
				$r['vc'] = $this->randVc();
				$r['sn'] = $wsn->sn;
			}else{
				$r['done'] = false;
				$r['msg'] = $wsn->errMsg();
			}
			echo json_encode($r);
			Yii::app()->end();
		}
		$this->renderPartial('rec_serial', ['model' => $model]);
	}

	public function actionDelSerial($id){
		$model = WmsSerialNo::model()->findByPk($id);
		if($model) $model->delete();
	}

	public function randVc(){
		return chr(rand(97,122)).rand(10,99).chr(rand(97,122));
	}

	public function actionTask($id){
		$model = WmsTask::model()->findByPk($id);
		if (in_array($model->type, [1010]) && !empty($model->mainTask->mdata['req']) && !empty($model->mainTask->mdata[Yii::app()->user->id]) && $model->mainTask->mdata[Yii::app()->user->id] != $model->mainTask->mdata['req']) {
			$model->mainTask->mdata[Yii::app()->user->id] = $model->mainTask->mdata['req'];
			$model->mainTask->save();
		}
		if(!empty($_POST)){
			if(in_array($model->type, [1010, 1020, 1030])){
				if (!empty($_POST['sig'])) {
					$send = array();
					$send[0]['co'] = $_POST['company'];
					$send[0]['dv'] = $_POST['driver'];
					$send[0]['rg'] = $_POST['rego'];
					$send[0]['nt'] = $_POST['note'];
					$model->mainTask->mdata[] = array('send' => json_encode($send));
					$model->mainTask->save();

					$f = tempnam(Yii::app()->basePath.DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
					file_put_contents($f, base64_decode(preg_replace('/^data:image\/jpeg;base64,/', '', $_POST['sig'])));
					$id = FileRepo::storeFile($f, 'Driver-P'.date('YmdHis').'.jpeg', 84, $model->mainTask->id);
					unlink($f);
					$this->ajaxResult($model);
				} else {
					$p = WmsProd::model()->with('packs', 'orgs')->findAll('t.status = 1 AND ((t.ean != "" AND t.ean = :b) OR ((t.name != "" AND t.name = :b)) OR (packs.barcode != "" AND packs.barcode = :b) OR (orgs.sku != "" AND orgs.sku = :b))', [':b' => $_POST['prod']]);
					if (empty($p)) {
						$p = WmsProd::model()->with('packs', 'orgs')->findAll('t.status = 1 AND ((t.ean != "" AND t.ean = :b) OR ((t.name != "" AND t.name = :b)) OR (packs.barcode != "" AND packs.barcode = :b) OR (orgs.sku != "" AND orgs.sku = :b))', [':b' => '0' . $_POST['prod']]);
					}
					// single item stock in
					if (!empty($model->mainTask->mdata['single_item'])) {
						if (empty($p)) {
							echo json_encode(['done' => false, 'data' => '<span style="color:#c00">Product not found! <a href="' . $this->createUrl('prod/create') . '" class="modal_link" title="Create Product" data-ignore="push">Create Product</a></span>']);
							Yii::app()->end();
						} else if (sizeof($p) > 1) {
							echo json_encode(['done' => false, 'data' => '<span>Multiple item found! Please check and fix <a href="'. $this->createUrl('prod/list'),'" data-transition="slide-in">product data</a></span>']);
							Yii::app()->end();
						} else {
							$itm = new WmsTaskItem;
							$itm->task_id = $model->id;
							$itm->mdata = array(
								'gi' => $p[0]->id,
								'gn' => $p[0]->name,
								'uq' => '1',
								'pl' => $_POST['plt'],
								'cq' => '',
								'ex' => '',
								'bn' => '',
								'nt' => '',
							);
							$itm->save();
							$this->ajaxResult($model);
						}
					} else {
						echo json_encode(['done' => !empty($p), 'data' => $this->renderPartial('task_entry', ['model' => $model, 'p' => $p], true)]);	
					}
				}
			}elseif($model->type == 3010){
				$plt = WmsLocation::model()->find('(pid > 99 OR pid = 1) AND code = :n', [':n' => $_POST['plt']]);
				echo json_encode(['done' => !empty($plt), 'data' => $this->renderPartial('task_pick_pallet', ['model' => $model, 'plt' => $plt], true)]);
			}elseif(in_array($model->type, [2030, 2040])){
				if (empty($model->mainTask->mdata['ct']) && $model->job->type != WmsJob::TYPE_PICK_LOAD) {
					$plt = WmsStockLocation::model()->with(['loc', 'stock'])->together()->find('(loc.pid > 99 OR loc.pid IN (1,2)) AND loc.code = :n AND t.qty > 0 AND stock.org_id in (' . implode(',', array_merge([$model->job->org_id], empty($model->mainTask->mdata['other_org']) ? [] : $model->mainTask->mdata['other_org'])) . ')', [':n' => $_POST['plt']]);
				} else {
					$plt = WmsStockLocation::model()->with(['loc'])->find('loc.code = :n', [':n' => $_POST['plt']]);
					if (!empty($plt)) {
						if (!empty($model->mainTask->madata['ct'])) {
							$items = WmsTaskItem::model()->with('task.mainTask')->findAll('mainTask.meta like :no', array(':no' => '%CT%' . end($model->mainTask->mdata['ct']) . '%'));
						} else {
							$items = WmsTaskItem::model()->with('task.mainTask')->findAll('mainTask.type = :type AND mainTask.job_id = :jid', [':type' => WmsTask::TYPE_PICK_PALLET, ':jid' => $model->job_id]);
						}
						$flag = false;
						foreach ($items as $item) {
							if ($plt->loc->id == $item->mdata['pli']) {
								$flag = true;
								break;
							}
						}
					}
					if (empty($flag)) {
						$plt = null;
					}
				}
				echo json_encode(['done' => !empty($plt), 'data' => $this->renderPartial('task_load_container', ['model' => $model, 'plt' => $plt], true)]);
			}elseif(in_array($model->type, [3020, 3030])){
				if (!empty($_POST['plt'])) {
					$err = [];
					$rs = [];
					$plt = WmsLocation::model()->find('t.type in (30,50,60) AND code = :n AND status = 1', [':n' => $_POST['plt']]);
					if(empty($plt)) $err[] = 'Pallet not found.';
					$prods = WmsProd::model()->with('packs', 'orgs')->findAll('t.status = 1 AND ((t.ean != "" AND t.ean = :b) OR (packs.barcode != "" AND packs.barcode = :b) OR (orgs.sku != "" AND orgs.sku = :b))', [':b' => $_POST['prod']]);

					if(empty($prods)) $err[] = 'Product not found.';

					if(empty($err)){
						foreach ($prods as $prod) {
							$rs = WmsStockLocation::model()->with('stock')->together()->findAll('t.qty > 0 AND stock.prod_id = :pid AND location_id = :lid', [':pid' => $prod->id, ':lid' => $plt->id]);
							if (sizeof($rs) > 0) {
								break;
							}
						}
						if(empty($rs)) $err[] = 'Product not found on this pallet.';
					}

					echo json_encode(['done' => empty($err), 'data' => $this->renderPartial('task_pick_item', ['err' => $err, 'rs' => $rs, 'model' => $model, 'plt' => $plt], true)]);
				} else if (!empty($_POST['sign'])) {
					echo json_encode(['done' => true, 'data' => $this->renderPartial('sign', ['model' => $model], true)]);
				} else if (!empty($_POST['sig'])) {
					foreach ($model->mainTask->subTasks as $task) {
						if ($task->type == '2110') {
							$model->mainTask->status = 99;
							$model->mainTask->compl_time = date('YmdHis');
							$model->mainTask->save();

							$pickup = array();
							$pickup[0]['co'] = $_POST['company'];
							$pickup[0]['dv'] = $_POST['driver'];
							$pickup[0]['rg'] = $_POST['rego'];
							$pickup[0]['nt'] = $_POST['note'];
							$task->mdata = array('pickup' => json_encode($pickup));
							$task->compl_time = $model->mainTask->compl_time;
							$task->save();

							$f = tempnam(Yii::app()->basePath.DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
			        file_put_contents($f, base64_decode(preg_replace('/^data:image\/png;base64,/', '', $_POST['sig'])));
			        $id = FileRepo::storeFile($f, 'Driver-P'.date('YmdHis').'.png', 84, $model->mainTask->id);
			        unlink($f);
						}
					}
					$this->ajaxResult($model);
				} else if (!empty($_POST['pin'])) {
					echo json_encode(['done' => true, 'data' => $this->renderPartial('pin', ['model' => $model], true)]);
				} else if (!empty($_POST['pinno'])) {
					if ($model->mainTask->pickupTask->mdata['pin'] !== $_POST['pinno']) {
						echo json_encode(['done' => false, 'msg' => 'Wrong PIN']);
					} else {
						$model->mainTask->pickupTask->mdata['pincheck'] = true;
						$model->mainTask->pickupTask->update('meta');
						$html = '<h4><span style="color: #3c763d">该订单自提，需签收</h4>'
						. '<input type="hidden" name="sign" value="true" />'
						. '<script>$(".entry-form button[name=search]").hide();</script>'
						. '<button type="submit" class="btn btn-primary btn-block"><span class="icon icon-pencil"></span>Sign</button>';
						echo json_encode(['done' => true, 'msg' => 'Successfully', 'html' => $html]);
					}
				}
			}
			return;
		}
		if (!empty($_FILES)) {
			$errors = [];
			$photos = empty($_FILES['WmsTask']['tmp_name']['photos']) ? [] : $_FILES['WmsTask']['tmp_name']['photos'];
			foreach ($photos as $key => $photo) 
			{
				if (empty($photo)) {
					$errors[] = 'images not exists';
				} else {
					$name = $_FILES['WmsTask']['name']['photos'][$key];
					if (!is_uploaded_file($photo)) $errors[] = $name . 'images not exists';
					$hash = FileRepo::uploadHash($model, FileRepo::FILE_TYPE_WMS_TASK_ATTACHMENT);
					$filesize = filesize($photo);
					$date = date('Y-m-d H:i:s');
					$fileHash = hash_file('crc32b', $photo) . hash('crc32b', $filesize);
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mime = finfo_file($finfo, $photo);
					$fr = FileRepo::storeFile($photo, $name, FileRepo::FILE_TYPE_WMS_TASK_ATTACHMENT, $model->mainTask->id);
				}
			}
			echo json_encode(['done' => true, 'data' => 'Upload Successfully']);
			return;
		}
		$this->renderPartial('task', ['model' => $model]);
	}

	public function actionTaskAct($id)
	{
		$model = WmsTask::model()->findByPk($id);
		$this->renderPartial('_task', ['model' => $model]);
	}

	public function actionEntry($id){
		$model = WmsTask::model()->findByPk($id);
		if(!empty($_POST)){
			if(in_array($model->type, [3020, 3030])){
				if (!empty($_POST['serial'])) {
					$tasks = WmsTask::model()->findAll('ref like :serial', [':serial' => '%' . $_POST['serial'] . '%']);
					if (sizeof($tasks) == 0) {
						$model->mainTask->ref .= ' / ' . $_POST['serial'];
						$model->mainTask->update('ref');
					} else if (sizeof($tasks) == 1 && $tasks[0]->id == $model->mainTask->id) {

					} else {
						$model->addError('id', 'dup serial no');
						$this->ajaxResult($model);
					}
				}
				if (!empty($_POST['pk'])) {
					foreach($_POST['pk'] as $sid => $q){
						$sl = WmsStockLocation::model()->findByPk($sid);
						$_POST['meta'] = ['si' => $sl->stock_id, 'sn' => $sl->stock->stockName(), 'uq' => $q, 'pli' => $sl->location_id, 'pl' => $sl->loc->code];
					}
				}
				if (!empty($_POST['ck'])) {
					foreach($_POST['ck'] as $sid => $q){
						$sl = WmsStockLocation::model()->findByPk($sid);
						$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $sl->stock->prod->id]);
						if (!empty($pp)) {
							$q = $q * $pp->qty;
						}
						$_POST['meta'] = ['si' => $sl->stock_id, 'sn' => $sl->stock->stockName(), 'uq' => $q, 'pli' => $sl->location_id, 'pl' => $sl->loc->code];
					}
				}
				if (!empty($_POST['shortpk'])) {
					foreach($_POST['shortpk'] as $sid => $q){
						if (!$q) continue;
						$sl = WmsStockLocation::model()->findByPk($sid);
						$_POST['short'] = ['si' => $sl->stock_id, 'sn' => $sl->stock->stockName(), 'uq' => $q, 'pli' => $sl->location_id, 'pl' => $sl->loc->code, 'short' => true];
					}
				}
				if (!empty($_POST['shortck'])) {
					foreach($_POST['shortck'] as $sid => $q){
						if (!$q) continue;
						$sl = WmsStockLocation::model()->findByPk($sid);
						$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $sl->stock->prod->id]);
						if (!empty($pp)) {
							$q = $q * $pp->qty;
						}
						$_POST['short'] = ['si' => $sl->stock_id, 'sn' => $sl->stock->stockName(), 'uq' => $q, 'pli' => $sl->location_id, 'pl' => $sl->loc->code, 'short' => true];
					}
				}
			}
			if (in_array($model->type, [3010])) {
				$sls = WmsStockLocation::model()->findAll('location_id = :lid AND qty > 0', [':lid' => $_POST['meta']['pli']]);
				foreach ($sls as $sl) {
					$_POST['meta']['pq'] = 1;
					$_POST['meta']['si'] = $sl->stock->id;
					$_POST['meta']['sn'] = $sl->stock->stockName();
					$itm = new WmsTaskItem;
					$itm->task_id = $model->id;
					$itm->mdata = $_POST['meta'];
					if(!empty($itm->mdata['gi'])){
						if(empty($itm->mdata['uq']) && !empty($itm->mdata['cq'])){
							$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $itm->mdata['gi']]);
							if(!empty($pp) && !empty($pp->qty)){
								$itm->mdata['uq'] = $itm->mdata['cq'] * $pp->qty;
							}
						}elseif(!empty($itm->mdata['uq']) && empty($itm->mdata['cq'])){
							$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $itm->mdata['gi']]);
							if(!empty($pp) && !empty($pp->qty) && $itm->mdata['uq'] % $pp->qty == 0){
								$itm->mdata['cq'] = $itm->mdata['uq'] / $pp->qty;
							}
						}
					}
					$_SESSION['entry_meta_'.$id] = $itm->mdata;
					$itm->save();
					$model->refresh();
					$model->compl_time = date('YmdHis');
					$model->save();
					if ($model->mainTask->status < array_search('WIP', WmsTask::$states)) {
						$model->mainTask->status = array_search('WIP', WmsTask::$states);
						$model->mainTask->update('status');
					}
				}
				$this->ajaxResult($model);
			}
			// check if plt belong to others
			// if(!empty($_POST['meta']['pl'])){
			// 	$sl = WmsStockLocation::model()->with('loc')->find('t.qty > 0 AND loc.code = :p', [':p' => $_POST['meta']['pl']]);
			// 	if(!empty($sl) && $sl->stock->org_id != $model->job->org_id && (empty($model->mainTask->mdata['other_org']) || (!empty($model->mainTask->mdata['other_org']) && !in_array($sl->stock->org_id, $model->mainTask->mdata['other_org'])))) {
			// 		$model->addError('job_id', 'This pallet is already used by another customer, please double check!');
			// 		$this->ajaxResult($model);
			// 	}
			// }
			// allow pick unit/carton after transfer
			// and container load 2019-09-03
			if (!in_array($model->type, [3020, 3030, 2030]) && !empty($_POST['meta']['pl'])) {
				$sls = WmsStockLocation::model()->with('loc')->findAll('t.qty > 0 AND loc.code = :p', [':p' => $_POST['meta']['pl']]);
				foreach ($sls as $sl) {
					if($sl->stock->org_id != $model->job->org_id){
						$model->addError('job_id', 'This pallet is already used by another customer, please double check!');
						$this->ajaxResult($model);
					}
				}
			}
			// invalid plt no | loc no
			if (in_array($model->type, [1010, 1020]) && !empty($_POST['meta']['pl'])) {
				$loc = WmsLocation::model()->find('name = :n OR code = :n', [':n' => $_POST['meta']['pl']]);
				if (empty($loc)) {
					$model->addError('job_id', 'This pallet no or location no is invalid, please double check!');
					$this->ajaxResult($model);
				}
				if(!empty($_POST['meta']['pas'])){
					$loc->bwf = $loc->bwf | intval($_POST['meta']['pas']);
					$loc->update(['bwf']);
				}
				// check dup full pallet
				$dup = WmsTaskItem::model()->find('task_id = :task_id AND meta = :meta AND del = 0', [':task_id' => $model->id, ':meta' => json_encode($_POST['meta'])]);
				$sql = 'SELECT MAX(qty_in) AS max FROM wms_stock_ledger wsl JOIN wms_stock ws ON wsl.stock_id = ws.id WHERE ws.prod_id = :prod_id AND wsl.location_id > :location_id AND (SELECT COUNT(stock_id) FROM wms_stock_ledger WHERE location_id = wsl.location_id) = 1';
				$highest = Yii::app()->db->createCommand($sql)->bindValues([':prod_id' => $_POST['meta']['gi'], ':location_id' => WmsLocation::WMS_LOCATION_MAX_MAGIC_ID])->queryAll();
				if (!empty($dup) && !empty($highest[0]['max']) && $_POST['meta']['uq'] == $highest[0]['max']) {
					$model->addError('job_id', 'Duplicate pallet and product information!');
					$this->ajaxResult($model);
				}
			}
			$itm = new WmsTaskItem;
			$itm->task_id = $model->id;
			$itm->mdata = $_POST['meta'];
			if(!empty($itm->mdata['gi'])){
				if(empty($itm->mdata['uq']) && !empty($itm->mdata['cq'])){
					$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid AND qty > 0', [':pid' => $itm->mdata['gi']]);
					if(!empty($pp) && !empty($pp->qty)){
						$itm->mdata['uq'] = $itm->mdata['cq'] * $pp->qty;
					} else {
						$model->addError('job_id', '系统中未发现箱规，请填写件数或在系统中添加箱规');
						$this->ajaxResult($model);
					}
				}elseif(!empty($itm->mdata['uq']) && empty($itm->mdata['cq'])){
					$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid AND qty > 0', [':pid' => $itm->mdata['gi']]);
					if(!empty($pp) && !empty($pp->qty) && $itm->mdata['uq'] % $pp->qty == 0){
						$itm->mdata['cq'] = $itm->mdata['uq'] / $pp->qty;
					}
				}
			}
			$_SESSION['entry_meta_'.$id] = $itm->mdata;
			$itm->save();
			if (!empty($_POST['short'])) {
				$itm = new WmsTaskItem;
				$itm->task_id = $model->id;
				$itm->mdata = $_POST['short'];
				if(!empty($itm->mdata['gi'])){
					if(empty($itm->mdata['uq']) && !empty($itm->mdata['cq'])){
						$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid AND qty > 0', [':pid' => $itm->mdata['gi']]);
						if(!empty($pp) && !empty($pp->qty)){
							$itm->mdata['uq'] = $itm->mdata['cq'] * $pp->qty;
						} else {
							$model->addError('job_id', '系统中未发现箱规，请填写件数或在系统中添加箱规');
							$this->ajaxResult($model);
						}
					}elseif(!empty($itm->mdata['uq']) && empty($itm->mdata['cq'])){
						$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid AND qty > 0', [':pid' => $itm->mdata['gi']]);
						if(!empty($pp) && !empty($pp->qty) && $itm->mdata['uq'] % $pp->qty == 0){
							$itm->mdata['cq'] = $itm->mdata['uq'] / $pp->qty;
						}
					}
				}
				$itm->save();
			}
			$model->refresh();
			$model->compl_time = date('YmdHis');
			$model->save();
			if ($model->mainTask->status < array_search('WIP', WmsTask::$states)) {
				$model->mainTask->status = array_search('WIP', WmsTask::$states);
				$model->mainTask->update('status');
			}
			if (!empty($itm->mdata['uq']) && $itm->mdata['uq'] > 100000) {
				echo json_encode(['done' => true, 'msg' => '入库数字>100000，请检查']);
				Yii::app()->end();
			}
			$this->ajaxResult($model);
		}
	}

	public function actionSumEntry($id){
		$model = WmsTask::model()->findByPk($id);
		if(!empty($_POST)){
			foreach($_POST['mdata'] as $k => $v){
				if($k == 'ctn_no') $v = strtoupper($v);
				$model->mdata[$k] = $v;
			}
			$model->save();
			$this->ajaxResult($model);
		}
	}

	public function actionDelItem($id){
		$model = WmsTaskItem::model()->findByPk($id);
		if($model) $model->delete();
	}

	public function actionPutaway(){
		$plt = false;
		if(!empty($_POST['plt'])) $plt = $_POST['plt'];
		if(!empty($_GET['info'])) $plt = $_GET['info'];

		$p = WmsLocation::model()->find('code != "" AND code = :n', [':n' => $plt]);

		if(!empty($_POST)){
			$l = WmsLocation::model()->find('code != "" AND code = :n AND type != :t', [':n' => $_POST['loc'], ':t' => $p->type]);

			if ($p->type < $l->type) {
				$p->addError('id', 'Please input pallet/carton barcode and location barcode at right place.');
			} else if (!empty($p) && !empty($l)) {
				$p->pid = $l->id;
				$p->save();
			} else {
				if (empty($p)) {
					$p = new WmsLocation;
					$p->addError('id', 'Pallet not found!');
				}
				if (empty($l)) {
					$p->addError('pid', 'Location not found!');
				}
			}
			$this->ajaxResult($p);
		}

		if(!empty($_GET['info'])){
			$this->renderPartial('plt_info', ['p' => $p]);
			return;
		}

		$this->render('putaway');
	}

	public function actionRelocate(){
		if(!empty($_POST)){
			$err = [];
			$rs = [];
			$pf = WmsLocation::model()->find('code = :c AND status = 1', [':c' => $_POST['plt']]);
			if(empty($pf)) $err[] = 'From pallet not found.';
			$pt = WmsLocation::model()->find('code = :c AND status = 1', [':c' => $_POST['plt2']]);
			if(empty($pt)) $err[] = 'To pallet not found.';

			if($_POST['plt'] == $_POST['plt2']) $err[] = 'From/To pallets are the same.';

			$prod = WmsProd::model()->with('packs', 'orgs')->find('t.status = 1 AND ((t.ean != "" AND t.ean = :b) OR (packs.barcode != "" AND packs.barcode = :b) OR (t.name != "" AND t.name = :b) OR (orgs.sku != "" AND orgs.sku = :b))', [':b' => $_POST['prod']]);
			if(empty($prod)) $err[] = 'Product not found.';

			if(empty($err)){
				$rs = WmsStockLocation::model()->with('stock')->together()->findAll('stock.prod_id = :pid AND t.location_id = :lid AND t.qty > 0', [':pid' => $prod->id, ':lid' => $pf->id]);
				if(empty($rs)) $err[] = 'Product not found on this pallet.';
			}

			echo json_encode(['done' => empty($err), 'data' => $this->renderPartial('reloc_entry', ['err' => $err, 'rs' => $rs, 'pt' => $pt], true)]);
			Yii::app()->end();
		}

		$this->render('relocate');
	}

	public function actionRelocEntry(){
		$err = [];
		foreach($_POST['mv'] as $k=>$q){
			$r = WmsStockLocation::model()->findByPk($k);
			if($r->qty >= $q){
				$sl = new WmsStockLedger('create');
				$sl->stock_id = $r->stock_id;
				$sl->location_id = $r->location_id;
				$sl->qty_out = $q;
				$sl->save();

				$sl = new WmsStockLedger('create');
				$sl->stock_id = $r->stock_id;
				$sl->location_id = $_POST['tloc'];
				$sl->qty_in = $q;
				$sl->save();

				if(empty($sl->loc->pid)){
					$sl->loc->pid = $r->loc->pid;
					$sl->loc->save();
				}

				$r->stock->updateStock();
			}else{
				$err[] = 'Not enough '.$r->stock->stockName();
			}
		}
		$sl = new WmsStockLedger('create');
		foreach($err as $e) $sl->addError('qty_out', $e);
		$this->ajaxResult($sl);
	}

	public function actionUploadPhoto()
	{
		$f = tempnam(Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR, 'pp');
		file_put_contents($f, base64_decode(preg_replace('/^data:image\/jpeg;base64,/', '', $_POST['data'])));
		if ($_POST['type'] == 'task') {
			$id = FileRepo::storeFile($f, 'P'.date('YmdHis').'.jpg', 84, $_POST['id']);
		} else {
			$id = FileRepo::storeFile($f, 'DR'.date('YmdHis').'.jpg', 93, $_POST['id']);
		}
		if($id > 0) {
			$file = FileRepo::model()->findByPk($id);
			$file->mdata['note'] = $_POST['note'];
			$file->save();
			echo 'DONE';
		}
		unlink($f);
	}

	public function actionComplete($id) {
		$intPallets = $_POST['numOfPalletsAction'];
		
		$model = WmsTask::model()->findByPk($id);
		if ($model->type == 1010 && !empty($model->mainTask->mdata['pi_cargo'])) {
			echo json_encode(['done' => true, 'data' => $this->renderPartial('demand_confirm', ['model' => $model], true)]);
		} else {
			if(!empty($intPallets)){
				$model->mainTask->mdata['numPalletsAction']=$intPallets;
			}
			$model->mainTask->status = 99;
			$model->mainTask->compl_time = date('YmdHis');
			$model->mainTask->mdata['res'] = date('Y-m-d H:i:s');
			$model->mainTask->save();
			$model->compl_time = $model->mainTask->compl_time;
			$model->save();
			$this->ajaxResult($model);
		}
	}

	public function actionDemandConfirm($id)
	{
		$model = WmsTask::model()->findByPk($id);
		// update demand
		if (!empty($_POST)) {
			foreach ($_POST as $k => $v) {
				$model->mainTask->mdata[$k] = $v;
			}

			$volumn = 0;
			$weight = 0;
			for ($i = 1; $i < count($_POST) / 3; $i ++) {
				if (!empty($_POST['length_'.$i])) {
					$model->mainTask->mdata['volumn_'.$i] = floatval($_POST['length_'.$i]) * floatval($_POST['width_'.$i]) * floatval($_POST['height_'.$i]);
					$volumn += $model->mainTask->mdata['volumn_'.$i];
					$weight = floatval($_POST['weight_'.$i]);
				}
			}
			$model->mainTask->mdata['pi_volumn_number'] = $volumn;
			$model->mainTask->mdata['pi_weight_number'] = $weight;

			$model->mainTask->mdata['res'] = date('Y-m-d H:i:s');
			$model->mainTask->save();
		}
		// complete
		$model->mainTask->status = 99;
		$model->mainTask->compl_time = date('YmdHis');
		$model->mainTask->mdata['res'] = date('Y-m-d H:i:s');
		$model->mainTask->save();
		$model->compl_time = $model->mainTask->compl_time;
		$model->save();
		$this->ajaxResult($model);
	}

	public function actionStorage() {
		if ($_GET['type'] == 'in') {
			if (empty($_POST)) {
				$this->render('storagein');
			} else {
				$parcel = $this->findImParcel($_POST['parcel']);
				if (empty($parcel)) {
					echo json_encode(array('done' => false, 'msg' => 'Parcel is not found'));
					Yii::app()->end();
				}

				$storage = Storage::model()->find('name = :name', array(':name' => $_POST['storage']));
				if (empty($storage)) {
					echo json_encode(array('done' => false, 'msg' => 'Location is not found'));
					Yii::app()->end();
				}

				$slog = new StorageLog;
				$slog->sid = $storage->id;
				$slog->model = 'ImParcel';
				$slog->fid = $parcel->id;
				$slog->in_dt = date('Y-m-d H:i:s');
				$slog->qty = $_POST['qty'];
				$slog->ckd = 1;
				$slog->save();

				$this->ajaxResult($slog);
			}
		} else if ($_GET['type'] == 'search') {
			if (empty($_POST)) {
				$this->render('storagesearch');
			} else {
				$parcel = $this->findImParcel($_POST['parcel']);
				if (empty($parcel)) {
					echo json_encode(array('done' => false, 'msg' => 'Parcel is not found'));
					Yii::app()->end();
				} else {
					$slogs = StorageLog::model()->findAll('model = "ImParcel" AND fid = :fid AND ckd = 1 AND out_dt is NULL', array(':fid' => $parcel->id));
					if (empty($slogs)) {
						echo json_encode(array('done' => false, 'msg' => 'Parcel is not found'));
						Yii::app()->end();
					} else {
						$result = '';
						foreach ($slogs as $slog) {
							$result .= '<p>' . $slog->parcel->hbn . ' at ' . $slog->storage->name . ' x ' . $slog->qty . '</p>';
						}
						echo json_encode(array('done' => true, 'data' => $this->renderPartial('storageout', array('slogs' => $result), true)));
						Yii::app()->end();
					}
				}
			}
		} else if ($_GET['type'] == 'out') {
			if (empty($_POST)) {
				$this->render('storagesearch');
			} else {
				$parcel = $this->findImParcel($_POST['parcel']);
				if (empty($parcel)) {
					echo json_encode(array('done' => false, 'msg' => 'Parcel is not found'));
					Yii::app()->end();
				}

				$storage = Storage::model()->find('name = :name', array(':name' => $_POST['storage']));
				if (empty($storage)) {
					echo json_encode(array('done' => false, 'msg' => 'Location is not found'));
					Yii::app()->end();
				}

				$slog = StorageLog::model()->find(array(
					'condition' => 'sid = :sid AND model = "ImParcel" AND fid = :fid AND qty = :qty AND ckd = 1 AND out_dt is NULL',
					'params' => array(
						':sid' => $storage->id,
						':fid' => $parcel->id,
						':qty' => $_POST['qty'],
					),
					'order' => 'in_dt desc'
				));

				if (!empty($slog)) {
					$slog->out_dt = date('Y-m-d H:i:s');
					$slog->save();

					$slogs = StorageLog::model()->findAll('model = "ImParcel" AND fid = :fid AND ckd = 1 AND out_dt is NULL', array(':fid' => $parcel->id));
					$result = '';
					foreach ($slogs as $slog) {
						$result .= '<p>' . $slog->parcel->hbn . ' at ' . $slog->storage->name . ' x ' . $slog->qty . '</p>';
					}
					$this->ajaxResult($slog, array('slogs' => $result));
				} else {
					echo json_encode(array('done' => 'false', 'msg' => 'Storage log is not found'));
					Yii::app()->end();
				}
			}
		}
	}

	private function findImParcel($barcode) {
		$parcel = null;
		$barcode = strtoupper(trim($barcode));

		$tollMatch1 = '/^T\d{6}'.TollAPI::TOLL_SLID.'\d{10}/';
		$tollMatch2 = '/^T\d{6}'.TollAPI::TOLL_SLID_OFFPEAK.'\d{10}/';
		if ( preg_match($tollMatch1, $barcode, $m) || preg_match($tollMatch2, $barcode, $m) ) {
			// for toll barcode
			// toll barcode format : 'T207711AWNL5450160016';
			$tollBarcode = $m[0];
			$ref = substr($tollBarcode, 7, 10);
			$parcel = ImParcel::model()->find('ref = :r', [':r' => $ref]);
		} else if ( preg_match('/^(7RFZ|4XHZ)\d{8}'.StarTrackAPI::API_PRODUCT_ID.'\d{5}/', $barcode, $m) ) {
			// for startrack barcode
			// startrack barcode : 7RFZ50000034EXP00001
			$ssBarcode = $m[0];
			$ref = substr($ssBarcode, 0, 12);
			$parcel = ImParcel::model()->find('ref = :r AND cbwf&'.ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN.'=0', [':r' => $ref]);
		} else if ( preg_match('/^99700160AMQ\d{18}/', $barcode, $m) ) {
			// for eparcel long barcode
			// barcode format : 99700160 + AMQ2992393 + 01 50 2 603462
			$postBarcode = $m[0];
			$ref = substr($postBarcode, 8, 10);
			$parcel = ImParcel::model()->find('ref = :r', [':r' => $ref]);
		} else if ( preg_match('/^019931265099999891EBA\d{20}/', $barcode, $m) ) {
			// our special agent, just for eparcel parcel reporting
			// barcode format : 019931265099999891EBA00752884701004440907
			$epBarcode = $m[0];
			$ref = substr($epBarcode, 18);
			$parcel = ImParcel::model()->find('hbn = :r', [':r' => $ref]);
		} else if ( preg_match('/^0199312650999998912TD\d{18}/', $barcode, $m) ) {
			// our special agent, just for eparcel parcel reporting
			// barcode format : 019931265099999891EBA00752884701004440907
			$epBarcode = $m[0];
			$ref = substr($epBarcode, 18);
			$parcel = ImParcel::model()->find('hbn = :r OR ref=:r', [':r' => $ref]);
		} else if ( preg_match('/^019931265099999891((\w{3,5})\d{7})\d{11}$/', $barcode, $m) ) {
			// for eparcel long barcode
			// 019931265099999891AMQ328549401000935002
			// barcode format : 99700160 + AMQ2992393 + 01 50 2 603462
			$postBarcode = $m[0];
			$ref = $m[1];
			$parcel = ImParcel::model()->find('(ref = :r OR hbn=:r) AND (cbwf&:cbwf)=0', [':r' => $ref,':cbwf'=> ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
		} else if (preg_match('/^(019931265099999891((\w{3,5})\d{7})\d{11})\d{23}$/', $barcode, $m) ) {
			// for eparcel long barcode
			// barcode format : 99700160 + AMQ2992393 + 01 50 2 603462
			$postBarcode = $m[1];
			$ref = $m[2];
			$parcel = ImParcel::model()->find('(ref = :r OR hbn=:r) AND cbwf&:cbwf=0', [':r' => $ref,':cbwf'=> ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN]);
		} else {
			$parcel = ImParcel::model()->find('hbn = :r OR hbn = :r', [':r' => $barcode]);
		}

		return $parcel;
	}

	public function actionStorageSuggest() {
		$parcel = $this->findImParcel($_POST['parcel']);
		$storages = Storage::model()->with('items.parcel')->findAll('parcel.agent_id = :oid AND items.out_dt is NULL', array(':oid' => $parcel->agent_id));
		$result = '';
		foreach ($storages as $storage) {
			$result .= '<p>' . $parcel->agent->shortName() . ' has parcels at ' . $storage->name . '</p>';
		}
		echo json_encode(array('result' => $result));
		Yii::app()->end();
	}

	public function actionBulkTask()
	{
		if (empty($_POST)) {
			$wait = WmsTask::waitActionTotalNum($_GET['ct']);
			$this->renderPartial('bulk_task', array('ct' => $_GET['ct'], 'type' => $wait['type']));
		} else {
			$_GET['ct'] = preg_replace('/CT/', '', $_GET['ct']);
			$wmstasks = WmsTask::model()->findAll('meta like :no', array(':no' => '%CT%' . $_GET['ct'] . '%'));
			if ($_GET['type'] === 'in') {
				$prod = WmsProd::model()->with('packs', 'orgs')->findAll('t.status = 1 AND ((t.ean != "" AND t.ean = :b) OR ((t.name != "" AND t.name = :b)) OR (packs.barcode != "" AND packs.barcode = :b) OR (orgs.sku != "" AND orgs.sku = :b))', array(':b' => $_POST['prod']));
				echo json_encode(array('done' => true, 'data' => $this->renderPartial('bulk_task_entry', ['p' => $prod], true)));
			} else if ($_GET['type'] === 'out') {
				$plt = WmsLocation::model()->find('(pid > 99 OR pid = 1) AND code = :n', [':n' => $_POST['plt']]);
				echo json_encode(['done' => !empty($plt), 'data' => $this->renderPartial('bulk_task_pick_pallet', ['plt' => $plt], true)]);
			}
		}
	}

	public function actionBulkTaskEntry()
	{
		if (empty($_POST)) {
			echo json_encode(array('done' => false, 'msg' => 'Please refresh system'));
			Yii::app()->end();
		} else {
			$_GET['ct'] = preg_replace('/CT/', '', $_GET['ct']);
			$wmstasks = WmsTask::model()->findAll('meta like :no', array(':no' => '%CT%' . $_GET['ct'] . '%'));
			if ($_GET['type'] === 'in') {
				// check if plt belong to others
				$flag = 0;
				if (!empty($_POST['meta']['pl'])) {
					$sl = WmsStockLocation::model()->with('loc')->find('t.qty > 0 AND loc.code = :p', [':p' => $_POST['meta']['pl']]);
					if (!empty($sl)) {
						foreach ($wmstasks as $task) {
							if ($task->job->org_id === $sl->stock->org_id) {
								$flag = 1;
							}
						}
					} else {
						$flag = 1;
					}
				}
				if ($flag === 0) {
					echo json_encode(array('done' => false, 'msg' => 'This pallet is already used by another customer, please double check!'));
					Yii::app()->end();
				}

				foreach ($wmstasks as $wmstask) {
					foreach ($wmstask->items as $item) {
						if ($_POST['meta']['cq'] == 0 && $_POST['meta']['uq'] == 0) {
							break 2;
						}

						if ($item->waitInNum() == 0) {
							continue;
						}

						$prod = WmsProd::model()->with('packs', 'orgs')->find('t.status = 1 AND ((t.ean != "" AND t.ean = :n) OR (packs.barcode != "" AND packs.barcode = :n) OR (orgs.sku != "" AND orgs.sku = :n) OR (t.name != "" AND t.name = :n))', array(':n' => $_POST['meta']['gn']));
						$prod_pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = :type', array(':prod_id' => $prod->id, ':type' => array_search('Carton', WmsProdPack::$types)));
						if (empty($prod_pack) && !empty($_POST['meta']['cq']) && empty($_POST['meta']['uq'])) {
							echo json_encode(array('done' => false, 'msg' => 'This prod do not have carton info'));
							Yii::app()->end();
						}

						if ($item->mdata['gn'] === $prod->name) {
							$taskitem = new WmsTaskItem;
							$taskitem->task_id = $wmstask->actionTask->id;
							$taskitem->mdata = array(
								'gi' => $prod->id,
								'gn' => $prod->name,
								'uq' => min(empty($_POST['meta']['cq']) ? $_POST['meta']['uq'] : $_POST['meta']['cq'] * floatval($prod_pack->qty), $item->waitInNum()),
								'pl' => $_POST['meta']['pl'],
								'cq' => !empty($prod_pack) ? min(empty($_POST['meta']['cq']) ? $_POST['meta']['uq'] : $_POST['meta']['cq'] * floatval($prod_pack->qty), $item->waitInNum()) / floatval($prod_pack->qty) : '',
								'ex' => $_POST['meta']['ex'],
								'bn' => $_POST['meta']['bn'],
								'nt' => '',
							);
							if (!empty($_POST['meta']['cq'])) {
								$_POST['meta']['cq'] -= $taskitem->mdata['cq'];
							} else if (!empty($_POST['meta']['uq'])) {
								$_POST['meta']['uq'] -= $taskitem->mdata['uq'];
							}
							$taskitem->save();
						}
					}
				}

				if ($_POST['meta']['cq'] > 0) {
					echo json_encode(array('done' => false, 'msg' => $_POST['meta']['cq'] . ' cartons of ' . $_POST['meta']['gn'] . ' do not belong to this bulk task'));
				} else if ($_POST['meta']['uq'] > 0) {
					echo json_encode(array('done' => false, 'msg' => $_POST['meta']['uq'] . ' of ' . $_POST['meta']['gn'] . ' do not belong to this bulk task'));
				} else {
					echo json_encode(array('done' => true, 'msg' => 'Successfully'));
				}
				Yii::app()->end();
			} else if ($_GET['type'] === 'out') {
				$status = false;
				foreach ($wmstasks as $wmstask) {
					foreach ($wmstask->items as $item) {
						$status = $item->waitOutNum()['status'];
						if ($item->mdata['pli'] === $_POST['meta']['pli'] && $status) {
							if (in_array($wmstask->type, [3020,3030])) {
								$taskitem = new WmsTaskItem;
								$taskitem->task_id = $wmstask->actionTask->id;
								$taskitem->mdata = array(
									'si' => $item->mdata['si'],
									'sn' => $item->mdata['sn'],
									'pq' => 1,
									'pli' => $item->mdata['pli'],
									'pl' => $item->mdata['pl'],
									'nt' => '',
								);
								$taskitem->save();
								break 2;
							} else if (in_array($wmstask->type, [3010])) {
								$wsls = WmsStockLocation::model()->findAll('location_id = :lid', [':lid' => $_POST['meta']['pli']]);
								foreach ($wsls as $wsl) {
									$taskitem = new WmsTaskItem;
									$taskitem->task_id = $wmstask->actionTask->id;
									$taskitem->mdata = array(
										'si' => $wsl->stock->id,
										'sn' => $wsl->stock->prod->name,
										'pq' => 1,
										'pli' => $wsl->location_id,
										'pl' => $wsl->loc->name,
										'nt' => '',
									);
									$taskitem->save();
								}
								break 2;
							}
						}
					}
				}

				if ($status == false) {
					echo json_encode(array('done' => false, 'msg' => $_POST['meta']['pli'] . ' do not belong to this bulk task'));
					Yii::app()->end();
				} else {
					echo json_encode(array('done' => true, 'msg' => 'Successfully'));
					Yii::app()->end();
				}
			}
		}
	}

	public function actionHistorys($ct)
	{
		$ct = preg_replace('/CT/', '', $ct);
		$wmstasks = WmsTask::model()->findAll('meta like :no', array(':no' => '%CT%' . $ct . '%'));
		$this->renderPartial('historys', ['tasks' => $wmstasks]);
	}

	public function actionBulkTaskPlt($ct)
	{
		$ct = preg_replace('/CT/', '', $ct);
		$wmstasks = WmsTask::model()->findAll('meta like :no', array(':no' => '%CT%' . $ct . '%'));
		if (!empty($_POST)) {
			$plt = WmsLocation::model()->find('code = :plt', array(':plt' => $_POST['pl']));
			foreach ($wmstasks as $model) {
				foreach ($model->actionTask->items as $item) {
					if ($item->mdata['pl'] == $_POST['pl']) {
						$plt->extra = $_POST['meta'];
						$plt->update('extra');
						$plt->depth = $_POST['meta']['length'];
						$plt->width = $_POST['meta']['width'];
						$plt->height = $_POST['meta']['height'];
						$plt->update('depth', 'width', 'height');
						$this->ajaxResult($model, ['plt' => $_POST['pl'], 'length' => $_POST['meta']['length'], 'width' => $_POST['meta']['width'], 'height' => $_POST['meta']['height'], 'weight' => $_POST['meta']['weight']]);
					}
				}
			}
			$plt = new WmsLocation;
			$plt->addError('code', 'Pallet does not belong to this task');
			$this->ajaxResult($plt);
		} else {
			$plts = [];
			$showed = [];
			foreach ($wmstasks as $model) {
				foreach ($model->actionTask->items as $item) {
					$plt = WmsLocation::model()->find('code = :plt', array(':plt' => $item->mdata['pl']));
					if (in_array($plt->code, $showed)) {
						continue;
					} else {
						$showed[] = $plt->code;
					}
					$plts[] = ['plt' => $plt->code, 'length' => @$plt->depth, 'width' => @$plt->width, 'height' => @$plt->height, 'weight' => @$plt->wt];
				}
			}
			$this->renderPartial('bulk_task_plt', ['ct' => $ct, 'plts' => $plts]);
		}
	}

	public function actionBulkTaskSummary($ct)
	{
		$ct = preg_replace('/CT/', '', $ct);
		$models = WmsTask::model()->findAll('meta like :no', array(':no' => '%CT%' . $ct . '%'));
		$this->renderPartial('summarys', ['models' => $models]);
	}

	public function actionActions($ct)
	{
		$ct = preg_replace('/CT/', '', $ct);
		$wmstasks = WmsTask::model()->findAll('meta like :no', array(':no' => '%CT%' . $ct . '%'));
		$wait = WmsTask::waitActionTotalNum($ct);
		$this->renderPartial('actions', ['wait' => $wait]);
	}

	public function actionDelivery()
	{
		if (empty($_POST)) {
			$model = new DeliveryRecord('create');
			$this->render('delivery',['model'=>$model]);
		} else {
			$record = new DeliveryRecord;
			$record->rego = $_POST['rego'];
			$record->plt = $_POST['plt'];
			$record->note = $_POST['note'];
			$record->task = $_POST['task'];
			$record->status = 1;
			$record->damage = @$_POST['damage'];
			$record->op_id = Yii::app()->user->id;
			$record->created = date('Y-m-d H:i:s');
			$record->savePOSTMeta($_POST);
			$record->save();

			if ($record->save()) {
				$this->saveFiles($record);
				$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
				file_put_contents($f, base64_decode(preg_replace('/^data:image\/jpeg;base64,/', '', $_POST['sig'])));
				FileRepo::storeFile($f, 'DR' . date('YmdHis') . '-signature.jpeg', 93, $record->id);
			}

			$this->ajaxResult($record, ['id' => $record->id]);
		}
	}

	public function actionPltCT($id)
	{
		$model = WmsTask::model()->findByPk($id);
		if (empty($_POST)) {
			$this->renderPartial('plt_ct', ['model' => $model]);
		} else {
			foreach ($_POST as $k => $v) {
				$model->mainTask->mdata[$k] = $v;
			}
			$model->mainTask->save();
			$this->ajaxResult($model);
		}
	}

public function actionGetDeliveryRecordList()
	{
			$model = new DeliveryRecord('search');
			$model->unsetAttributes();
			if(!empty($_GET['DeliveryRecord']))
			{
				$model->setAttributes($_GET['DeliveryRecord']);
			}

			if(!empty($_GET['booking_time']))
			{
				$model->booking_time = $_GET['booking_time'];
			}

			$this->render('deliveryRecordList',["model"=>$model]);
	}

	public function actionDeliveryBooking()
	{
			$this->layout = 'wmaBooking';
			$model = new DeliveryRecord('search');
			$model->unsetAttributes();
			$this->render('deliveryBooking',["model"=>$model]);
	}

	public function actionConfirmDeliveryRecord($id)
	{

		if (empty($_POST)) {
			$model = DeliveryRecord::model()->findByPk($id);
			$this->render('deliveryConfirm',["model"=>$model]);
		} else 
		{

			$record = DeliveryRecord::model()->findByPk($_POST['id']);

			$this->saveFiles($record);

			$record->plt = $_POST['plt'];
			$record->pkg = $_POST['pkg'];
			$record->note = $_POST['note'];
			$record->task = $_POST['task'];
			$record->status = DeliveryRecord::DONESTATE;
			$record->damage = @$_POST['damage'];
			$record->op_id = Yii::app()->user->id;
			$record->mdata['confirm_time'] = date('Y-m-d H:i:s');

			$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
			file_put_contents($f, base64_decode(preg_replace('/^data:image\/jpeg;base64,/', '', $_POST['sig'])));
			$fileId = FileRepo::storeFile($f, 'DR' . date('YmdHis') . '-signature.jpeg', 93, $record->id);
			$record->mdata['signature_file_id'] =$fileId;
			$record->save();
			$this->ajaxResult($record, ['id' => $record->id]);
		}

	}

	private function saveFiles($record)
	{
		if (!empty($_FILES['DeliveryRecord'])) 
			{
				$errors=[];
				$photos=empty($_FILES['DeliveryRecord']['tmp_name']['photos'])?[]:$_FILES['DeliveryRecord']['tmp_name']['photos'];
				foreach ($photos as $key => $photo) 
				{
					if (empty($photo)) 
					{
						$errors[]='images not exists';
					} else 
					{
						$name = $_FILES['DeliveryRecord']['name']['photos'][$key];
						if(!is_uploaded_file($photo)) $errors[]=$name.'images not exists';
						$hash = FileRepo::uploadHash($record, FileRepo::DELIVERYRECORDFILE);
						$filesize = filesize($photo);
						$date = date('Y-m-d H:i:s');
						$fileHash = hash_file('crc32b', $photo).hash('crc32b', $filesize);
						$finfo = finfo_open(FILEINFO_MIME_TYPE);
						$mime = finfo_file($finfo, $photo);
						$fr = CargoProcess::updateUploadSingleFile($filesize,$date,$fileHash,$finfo,$mime,$photo,$name, $hash);
					}
				}
			}
	}

	public function actionDayBookingSummary()
	{

		$filtersForm=new FiltersForm;
		$model = new DeliveryRecord('search');
		$model->unsetAttributes();
		$date ="";
		if(!empty($_GET['date']))
		{

			$provide = $model->getTimeSelectArr($_GET['date'],true);
			$date =$_GET['date'];
		}else
		{
			$provide = $model->getTimeSelectArr(date('Y-m-d'),true);
			$date =date('Y-m-d');
		}
		if(empty($provide))
		{
			$provide[] = ['id'=>1,'datetime'=>'none','num'=>0];
		}

		$filteredData=$filtersForm->filter($provide);
		$dataprovider=new CArrayDataProvider($filteredData);
		$dataprovider->pagination=['pageSize' =>3000,];
		$sort=new CSort();
		$sort->attributes=[
			'datetime'=>[
				'asc'=>'datetime ASC',
				'desc'=>'datetime DESC',
			]
		];
		$sort->defaultOrder = "datetime ASC";
		$dataprovider->sort=$sort;
		if(is_array($date))
		{
			$date = $date[0];
		}
		$this->render('day_booking_summary',['dataProvider'=>[$dataprovider,$filtersForm],'date'=>$date]);

	}

	public function actionSwitchDelivery($id)
	{
		$task = WmsTask::model()->findByPk($id);
		$task->type = $task->type == 2120 ? 2110 : 2120;
		$task->update('type');
		$this->ajaxResult($task);
	}

	public function actionTaskComplete($id)
	{
		$task = WmsTask::model()->findByPk($id);
		$this->renderPartial('task_complete', ['model' => $task]);
	}

	public function actionScanOut()
	{
		if (empty($_POST)) {
			$this->render('scan_out');
		} else {
			$ref = $_POST['ship'];
			// $task = WmsTask::model()->find('type = 2120 AND :ref LIKE concat("%", ref, "%") AND ref != ""', [':ref' => $ref]);
			$sn = 0;
			$courier_id = 0;
			$shipment = ShipmentScan::getShipmentByBarcode($ref, $sn, $courier_id, true);
			if (!empty($shipment->cref)) {
				$task = WmsTask::model()->findByPk(ltrim($shipment->cref, 'T'));
			}
			if (empty($task)) {
				echo json_encode(['done' => false, 'msg' => 'Cannot find related task']);
			} else {
				// $task->mainTask->easyshipScanOut();
				$task->status = 99;
				$task->update('status');
				echo json_encode(['done' => true, 'msg' => 'Successfully']);
			}
		}
	}

	public function actionBatchSorting()
	{
		if (empty($_POST)) {
			$this->render('batch_sorting');
		} else {
			$batch_no = $_POST['batch_no'];
			$shelf_number = $_POST['shelf_number'];
			if (empty($batch_no)) {
				echo json_encode(['done' => false, 'msg' => 'Please input batch no.']);
			} else if (empty($shelf_number)) {
				echo json_encode(['done' => false, 'msg' => 'Please input shelf no.']);
			} else {
				$date = explode('-', $batch_no)[0];
				$date = substr($date, 0, 2) . substr($date, 2, 2) . substr($date, 4, 2);
				$batch = sprintf('%02d', intval(explode('-', $batch_no)[1]));
				$type = WmsBatch::$types[intval(explode('-', $batch_no)[2])];

				$model = WmsBatch::model()->find('no = :no', [':no' => $date . ' - ' . $batch . ' - ' . $type]);
				if (empty($model)) {
					echo json_encode(['done' => false, 'msg' => 'Batch no. is invalid']);
					Yii::app()->end();
				}

				$shelf_number = intval(substr($shelf_number, 1, 2));
				if (!is_numeric($shelf_number) || $shelf_number > WmsBatch::WMS_BATCH_SHELF_MAX) {
					echo json_encode(['done' => false, 'msg' => 'Shelf no. is invalid']);
					Yii::app()->end();
				}

				$occupied = WmsTaskBatch::model()->find('shelf_number = :sn AND status = :status AND type = :type AND batch_id != :bid', [':sn' => $shelf_number, ':status' => WmsTaskBatch::WMS_TASK_BATCH_STATUS_NEW, ':type' => WmsBatch::WMS_BATCH_TYPE_MULTI, ':bid' => $model->id]);
				if (!empty($occupied)) {
					echo json_encode(['done' => false, 'msg' => 'Shelf ' . $shelf_number . ' 已占用']);
					Yii::app()->end();
				}

				foreach ($model->tasks as $task) {
					if ($task->status != 30) continue;
					$task->batch->shelf_number = $shelf_number;
					$task->batch->update('shelf_number');
				}

				echo json_encode(['done' => true, 'data' => $this->renderPartial('batch_sorting_entry', ['batch_no' => $batch_no], true)]);
			}
		}
	}

	public function actionBatchSortingEntry()
	{
		$ean = $_POST['ean'];
		$batch_no = $_POST['batch_no'];
		$prod = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => trim($ean)]);
		if (empty($prod)) {
			echo json_encode(['done' => false, 'msg' => 'Product not found']);
			return;
		}

		$date = explode('-', $batch_no)[0];
		$date = '20' . substr($date, 0, 2) . '-' . substr($date, 2, 2) . '-' . substr($date, 4, 2);
		$batch = intval(explode('-', $batch_no)[1]);
		$type = explode('-', $batch_no)[2];

		$tasks = WmsTask::model()->with('batch')->findAll('batch.date = :date AND batch.batch = :batch AND batch.status = :status AND batch.type = :type', [':date' => $date, ':batch' => $batch, ':status' => WmsTaskBatch::WMS_TASK_BATCH_STATUS_NEW, ':type' => $type]);

		// find empty shelf
		$shelf_number = WmsTaskBatch::findEmptyShelf();
		if (empty($shelf_number) && $tasks[0]->batch->shelf_number == 0) {
			echo json_encode(['done' => false, 'msg' => 'No empty shelf']);
			return;
		} else if ($tasks[0]->batch->shelf_number == 0) {
			foreach ($tasks as $task) {
				if ($task->status != 30) continue;
				$task->batch->shelf_number = $shelf_number;
				$task->batch->update('shelf_number');
			}
		}

		foreach ($tasks as $task) {
			if ($task->status != 30) continue;
			foreach ($task->items as $item) {
				if (empty($item->mdata['uq']) || $item->mdata['uq'] == 0) continue;
				if (!empty($item->mdata['sort_qty']) && $item->mdata['sort_qty'] == $item->mdata['uq']) continue;
				if (empty($item->mdata['si'])) continue;
				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				if (empty($stock)) continue;
				if ($stock->prod_id == $prod->id) {
					if (empty($item->mdata['sort_qty'])) {
						$item->mdata['sort_qty'] = 0;
					}
					$item->mdata['sort_qty'] += 1;
					$item->update('meta');

					$task->refresh();
					$task->batch->parent->mdata['sort_remain'] = intval(@$task->batch->parent->mdata['sort_remain']) - 1;
					$task->batch->parent->update('meta');
					$box_number = $task->batch->box_number;
					$shelf_number = $task->batch->shelf_number;
					break 2;
				}
			}
		}

		if (!empty($box_number)) {
			echo json_encode(['done' => true, 'msg' => $prod->name . '<br /><br />Put in box ' . $box_number . ' on shelf ' . $shelf_number, 'box_number' => $box_number, 'shelf_number' => $shelf_number, 'prod_name' => $prod->name, 'prod_ean' => $prod->ean]);
		} else {
			echo json_encode(['done' => false, 'msg' => $prod->name . '<br /><br />Not in current Batch']);
		}
	}
	
	public function actionSplitSorting(){
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			$this->render('split_sorting_task_number');
		}
		else
		{
			$objLocation = WmsLocation::model()->find('code=:code',[':code'=>$_POST['pallet_number']]);
			if($objLocation == null){
				echo json_encode(['done' => false, 'msg' => 'wrong pallet number '.$_POST['pallet_number']]);
				return;
			}
			
			$objWmsTask = WmsTask::model()->find('link_id=:link_id and type=:type',[':link_id'=>$_POST['task_number'],':type'=>WmsTask::TYPE_Split_Delivery]);
			if($objWmsTask == null){
				echo json_encode(['done' => false, 'msg' => 'wrong task number '.$_POST['task_number']]);
			}
			else{
				if(strpos($_POST['split_number'],',')){
					$listBatch = explode(',',$_POST['split_number']);
					$numStart = $listBatch[0];
					$numEnd = $listBatch[1];
					for($num=$numStart; $num<=$numEnd; $num++){
						foreach($objWmsTask->mdata['records'] as $i=> $objRecord){
							$listFrom = explode('-',$objRecord['number_from']);
							$numFrom = $listFrom[1]; 
							$numFrom0 = $listFrom[0]; 
							$listTo = explode('-',$objRecord['number_to']);
							$numTo = $listTo[1];
							$numTo0 = $listTo[0];
							if($num>=$numFrom && $num<=$numTo){
								if(!in_array($num,$objWmsTask->mdata['records'][$i]['postfix'])){
									$objWmsTask->mdata['records'][$i]['postfix'][] =$num; 
									if(!in_array($objLocation->id,$objWmsTask->mdata['records'][$i]['pallet'])){
										$objWmsTask->mdata['records'][$i]['pallet'][] =$objLocation->id; 
									}
									$objWmsTask->save();
									break;
								}
							}
						}
					}
					
				}
				else{
					$list = explode('-',$_POST['split_number']);
					$number = $list[0];
					$postfix = $list[1];
					foreach($objWmsTask->mdata['records'] as $i=> $objRecord){
						$listFrom = explode('-',$objRecord['number_from']);
						$numFrom = $listFrom[1]; 
						$numFrom0 = $listFrom[0]; 
						$listTo = explode('-',$objRecord['number_to']);
						$numTo = $listTo[1];
						$numTo0 = $listTo[0];
						if($numFrom0==$number&&$numTo0==$number&&$postfix>=$numFrom && $postfix<=$numTo){
							if(!in_array($postfix,$objWmsTask->mdata['records'][$i]['postfix'])){
								$objWmsTask->mdata['records'][$i]['postfix'][] =$postfix; 
								if(!in_array($objLocation->id,$objWmsTask->mdata['records'][$i]['pallet'])){
									$objWmsTask->mdata['records'][$i]['pallet'][] =$objLocation->id; 
								}
								$objWmsTask->save();
								break;
							}
						}
					}
				}
				
				
				$strRecords = $this->funcSplitSortingHTML($objWmsTask->mdata['records'] );;
				
				echo json_encode(['done' => true, 'msg' => 'success' , 'records'=>$strRecords]);
			}
		}
		
	}

	public function actionSplitSortingTask(){
		$objWmsTask = WmsTask::model()->find('link_id=:link_id and type=:type',[':link_id'=>$_GET['task_number'],':type'=>WmsTask::TYPE_Split_Delivery]);
		if($objWmsTask == null){
			echo json_encode(['done' => false, 'msg' => 'wrong task number '.$_GET['task_number']]);
		}
		else{
			$objTaskMain = $objWmsTask->mainTask;
			$strTaskNumber = 'Task '.$objTaskMain->id;
			$strHbn = $objTaskMain->ref;
			$strRecords = $this->funcSplitSortingHTML($objWmsTask->mdata['records'] );
			echo json_encode(['done' => true, 'msg' => 'success','records'=>$strRecords ,'hbn'=>$strHbn,'task_number'=>$strTaskNumber]);
		}
	}
	
	private function funcSplitSortingHTML($listRecords){
		//location
		$strIds = '';
		foreach ($listRecords as $i => $objRecord) {
			foreach($objRecord['pallet'] as $j=> $strPallet){
				$strIds.= $strPallet;
				if(!($i == sizeof($listRecords)-1 && $j == sizeof( $objRecord['pallet'])-1)){
					$strIds.= ',';
				}
			}
		}
		
		$sql = 'select id , code ,pid from wms_location where id in (';
		$sql.= $strIds;
		$sql.= ') or id in (select pid from wms_location where id in (';
		$sql.= $strIds;
		$sql.= ')) ';
		
		
		$listResult =Yii::app()->db->createCommand($sql)->queryAll();
		$dicId2Code_Pid = [];
		foreach($listResult as $i => $objRow){
			$obj = new stdClass;
			$obj->code = $objRow['code'];
			$obj->pid = $objRow['pid'];
			$dicId2Code_Pid[$objRow['id']] = $obj;
		}
		
		
		$strHtml = '';
		foreach ($listRecords as $i => $objRecord) {
			$strHtml.='<tr class="' . ($i % 2 == 0 ? 'odd' : 'even') . '">';
			$strHtml.= '<td>' . $objRecord['number_from'] . '</td>';
			$strHtml.= '<td>' . $objRecord['number_to'] . '</td>';
			$strRecords = ' [';
			
			foreach($objRecord['postfix'] as $j=>$strPostfix){
				$strRecords.= $strPostfix.',';
			}
			$strRecords.= ']';
			
			if(sizeof($objRecord['postfix'])== $objRecord['pieces']){
				$strRecords.=' ✔';
			}
			else{
				$strRecords.=' ('.sizeof($objRecord['postfix']).'/'.$objRecord['pieces'].') ';
			}
			
			$strHtml.= '<td>' . $strRecords. '</td>';
			$strLocation = '';
			foreach($objRecord['pallet'] as $j=> $strPallet){
				$strLocation .= $dicId2Code_Pid[ $strPallet]->code;
				if($dicId2Code_Pid[ $strPallet]->pid != 0){
					$strLocation.= '['.$dicId2Code_Pid[$dicId2Code_Pid[ $strPallet]->pid]->code.']';
				}
				$strLocation.= ' ';
			}
			$strHtml.= '<td>' . $strLocation . '</td>';
			
			$strHtml.= '<td>' . $objRecord['address_type'] . '</td>';
			$strHtml.= '</tr>';
		}
		return $strHtml; 
	}
	
	public function actionBatchPicking()
	{
		if (empty($_POST)) {
			$this->render('batch_picking');
		} else {
			$batch_no = $_POST['batch_no'];
			$np = $this->getNpStr($batch_no);
			preg_match('/<b>(\d*)<\/b>\s\&times;/i', $np, $matches);
			$qty = $matches[1];
			echo json_encode(['done' => true, 'data' => $this->renderPartial('batch_picking_entry', ['np' => $np, 'qty' => $qty, 'batch_no' => $batch_no], true)]);
		}
	}

	public function actionBatchPickingEntry()
	{
		$plt = $_POST['plt'];
		$prod = $_POST['prod'];
		$qty = $_POST['qty'];
		$short = $_POST['short'];
		$batch_no = $_POST['batch_no'];
		$expiry = @$_POST['expiry'];
		$batch = @$_POST['batch'];
		$need_qty = 0;
		$remain_qty = $qty;
		$remain_short = $short;
		$loc = WmsLocation::model()->find('name = :plt', [':plt' => $plt]);
		if (empty($loc)) {
			echo json_encode(['done' => false, 'msg' => 'Pallet not found', 'code' => 'plt']);
			return;
		}

		$cond = 'prod.ean = :prod AND loc.name = :plt AND t.qty > 0';
		$params = [':prod' => $prod, ':plt' => $plt];
		if (!empty($expiry)) {
			$cond .= ' AND stock.expiry = :expiry';
			$params[':expiry'] = $expiry;
		}
		if (!empty($batch)) {
			$cond .= ' AND stock.batch = :batch';
			$params[':batch'] = $batch;
		}
		$stocks = WmsStockLocation::model()->with('stock.prod', 'loc', 'stock')->findAll($cond, $params);
		if (empty($stocks)) {
			echo json_encode(['done' => false, 'msg' => 'Stock not found', 'code' => 'prod']);
			return;
		}

		$date = explode('-', $batch_no)[0];
		$date = '20' . substr($date, 0, 2) . '-' . substr($date, 2, 2) . '-' . substr($date, 4, 2);
		$batch = intval(explode('-', $batch_no)[1]);
		$type = explode('-', $batch_no)[2];
		foreach ($stocks as $stock) {
			$tasks = WmsTask::model()->with('batch', 'items')->findAll('batch.date = :date AND batch.batch = :batch AND batch.status = :status AND batch.type = :type AND JSON_VALUE(items.meta, "$.si") = :si', [':date' => $date, ':batch' => $batch, ':status' => WmsTaskBatch::WMS_TASK_BATCH_STATUS_NEW, ':type' => $type, ':si' => $stock->stock_id]);
			$wtis = [];
			foreach ($tasks as $task) {
				$task_need = 0;
				$res_items = WmsTaskItem::model()->findAll('task_id = :tid AND JSON_VALUE(meta, "$.si") = :si AND del = 0', [':tid' => $task->id, ':si' => $stock->stock_id]);
				if (empty($res_items)) continue;
				$ac_items = WmsTaskItem::model()->findAll('task_id = :tid AND JSON_VALUE(meta, "$.si") = :si AND del = 0', [':tid' => $task->actionTask->id, ':si' => $stock->stock_id]);
				foreach ($res_items as $res_item) {
					$task_need += $res_item->mdata['uq'];
				}
				foreach ($ac_items as $ac_item) {
					$task_need -= $ac_item->mdata['uq'];
				}
				if ($task_need > 0 && $remain_qty > 0) {
					$wti = new WmsTaskItem;
					$wti->task_id = $task->actionTask->id;
					$wti->sl_id = 0;
					$wti->mdata['si'] = $stock->stock_id;
					$wti->mdata['sn'] = $stock->stock->prod->name;
					$wti->mdata['uq'] = min($task_need, $remain_qty);
					$wti->mdata['pli'] = $loc->id;
					$wti->mdata['pl'] = $loc->name;
					$wtis[] = $wti;
					$need_qty += $task_need;
					$remain_qty -= min($task_need, $remain_qty);
				} else if ($task_need > 0 && $remain_short > 0) {
					$wti = new WmsTaskItem;
					$wti->task_id = $task->actionTask->id;
					$wti->sl_id = 0;
					$wti->mdata['si'] = $stock->stock_id;
					$wti->mdata['sn'] = $stock->stock->prod->name;
					$wti->mdata['uq'] = min($task_need, $remain_short);
					$wti->mdata['pli'] = $loc->id;
					$wti->mdata['pl'] = $loc->name;
					$wti->mdata['short'] = true;
					$wtis[] = $wti;
					$need_qty += $task_need;
					$remain_short -= min($task_need, $remain_short);
				}
			}
		}

		if ($qty + $short > $need_qty) {
			echo json_encode(['done' => false, 'msg' => 'Only need to pick ' . $need_qty . ' units', 'code' => 'qty']);
		} else if ($qty + $short > $stock->qty) {
			echo json_encode(['done' => false, 'msg' => $stock->stock->prod->name . ' &times ' . ($qty + $short) . ' is not enough at ' . $loc->name . ' only have ' . intval($stock->qty), 'code' => 'qty']);
		} else {
			foreach ($wtis as $wti) {
				$wti->save();
				$wti->refresh();

				//WIP OR Complete?
				$wti->task->mainTask->status = 99;
				$wti->task->mainTask->update('status');
				$wti->task->mainTask->compl_time = date("Y-m-d H:i:s");
				$wti->task->save();
				
			}

			$np = $this->getNpStr($batch_no);
			preg_match('/<b>(\d*)<\/b>\s\&times;/i', $np, $matches);
			$old_qty = $qty + $short;
			$new_qty = $matches[1];
			echo json_encode(['done' => true, 'msg' => 'Successfully', 'np' => $np, 'qty' => $new_qty, 'prod_name' => $stock->stock->prod->name, 'prod_ean' => $stock->stock->prod->ean, 'loc_name' => $loc->name, 'old_qty' => $old_qty]);
		}
	}

	public function getNpStr($batch_no)
	{
		$np = WmsTask::nextItemBatchPick($batch_no);
		if (!empty($np)) {
			$np_str = '<div class="table-view-cell table-view-cell-full" style="margin: -15px -15px 0 -15px;">';
			$np_str .= '<b>' . $np[3] . '</b> &times; ' . $np[1];
			if (!empty($np[4]) || !empty($np[5])) {
				$np_str .= ' (';
				if (!empty($np[4])) {
					$np_str .= 'Expiry: ' . $np[4];
					$np_str .= '<input type="hidden" name="expiry" value="' . $np[4] . '">';
				}
				if (!empty($np[5])) {
					if (!empty($np[4])) $np_str .= ' ';
					$np_str .= 'Batch: ' . $np[5];
					$np_str .= '<input type="hidden" name="batch" value="' . $np[5] . '">';
				}
				$np_str .= ')';
			}
			$np_str .= '<p>' . $np[0] . '</p>';
			foreach ($np[2] as $loc) {
				$np_str .= '<div data-plt="' . $loc->loc->name . '">' . $loc->loc->name . ' (' . $loc->loc->parent->name . ') &nbsp; Qty: ' . $loc->qty . '</div>';
			}
			$np_str .= '</div>';
		} else {
			$np_str = 'No batch order';
		}

		return $np_str;
	}

	public function actionBatchSortingCheck()
	{
		if (empty($_POST)) {
			$this->render('batch_sorting_check');
		} else {
			$batch_no = $_POST['batch_no'];

			$remains = $this->getSortingRemains($batch_no);

			echo json_encode(['done' => true, 'data' => $this->renderPartial('batch_sorting_check_entry', ['batch_no' => $batch_no, 'remains' => $remains], true)]);
		}
	}

	public function getSortingRemains($batch_no)
	{
		$date = explode('-', $batch_no)[0];
		$date = '20' . substr($date, 0, 2) . '-' . substr($date, 2, 2) . '-' . substr($date, 4, 2);
		$batch = intval(explode('-', $batch_no)[1]);
		$type = explode('-', $batch_no)[2];

		$tasks = WmsTask::model()->with('batch')->findAll('batch.date = :date AND batch.batch = :batch AND batch.status = :status AND batch.type = :type', [':date' => $date, ':batch' => $batch, ':status' => WmsTaskBatch::WMS_TASK_BATCH_STATUS_NEW, ':type' => $type]);
		$remains = [];
		foreach ($tasks as $task) {
			foreach ($task->items as $item) {
				if (empty($item->mdata['si'])) continue;
				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				if (empty($stock)) continue;
				if ($item->mdata['uq'] != floatval(@$item->mdata['sort_qty'])) {
					$remains['shelf ' . $task->batch->shelf_number . ' box ' . $task->batch->box_number][] = [$stock->prod->name, $stock->prod->ean, $item->mdata['uq'] - floatval(@$item->mdata['sort_qty'])];
				}
			}
		}

		return $remains;
	}

	public function actionBatchSortingCheckEntry()
	{
		$ean = $_POST['ean'];
		$batch_no = $_POST['batch_no'];
		$prod = WmsProd::model()->find('ean = :ean', [':ean' => $ean]);

		$date = explode('-', $batch_no)[0];
		$date = '20' . substr($date, 0, 2) . '-' . substr($date, 2, 2) . '-' . substr($date, 4, 2);
		$batch = intval(explode('-', $batch_no)[1]);
		$type = explode('-', $batch_no)[2];

		$tasks = WmsTask::model()->with('batch')->findAll('batch.date = :date AND batch.batch = :batch AND batch.status = :status AND batch.type = :type', [':date' => $date, ':batch' => $batch, ':status' => WmsTaskBatch::WMS_TASK_BATCH_STATUS_NEW, ':type' => $type]);

		$box_numbers = [];
		foreach ($tasks as $task) {
			foreach ($task->items as $item) {
				if (empty($item->mdata['si'])) continue;
				$stock = WmsStock::model()->findByPk($item->mdata['si']);
				if (empty($stock)) continue;
				if ($stock->prod_id == $prod->id) {
					$box_numbers[] = $task->batch->box_number;
					$shelf_number = $task->batch->shelf_number;
				}
			}
		}

		if (!empty($box_numbers)) {
			$msg = $prod->name . '<br />Can be found in box <span style="font-size: 50px">' . implode(', ', $box_numbers) . '</span> on shelf ' . $shelf_number;
			echo json_encode(['done' => true, 'msg' => $msg]);
		} else {
			echo json_encode(['done' => false, 'msg' => $prod->name . '<br /><br />Not in current Batch']);
		}
	}

	public function actionShipmentRackRecording()
	{
		return null;
		$model = new WmsRackShipment('create');
		if (empty($_POST)) 
		{
			$this->render('shipmentRackRecording',['model'=>$model]);
		} else 
		{
			$err = [];
			$rack= null;
			$cRack = '';
			if (empty($_POST['mhbns'])) 
			{
					$err[] = "Barcodes of shipments are empty";
			}
			if (empty($_POST['WmsRackShipment']["code"])) 
			{
					$err[] = "Code of rack is empty";
			}else
			{
				$rack = WmsLocation::model()->find("code = :code",[":code"=>$_POST["WmsRackShipment"]["code"]]);
				if(empty($rack))
				{
					$err[] = "Not found rack";
				}
			}

			if (!empty($_POST['WmsRackShipment']["ccode"])) 
			{
				$cRack = WmsLocation::model()->find("code = :code",[":code"=>$_POST["WmsRackShipment"]["ccode"]]);
				if(empty($rack))
				{
					$err[] = "Not found the changed rack";
				}
				$ccode = $_POST['WmsRackShipment']["ccode"];
			}

			if(!empty($err))
			{
				$model->addErrors($err);
				$this->ajaxResult($model);
			}

			$transaction = Yii::app()->db->beginTransaction();
			try 
			{
				$mhbns = $_POST['mhbns'];
				$ns = preg_split('/[\s,;]+/', trim($mhbns));
				foreach ($ns as $key => $value) {
					$sbArr = explode(":", $value);
					$shipmentBarcode = $sbArr[0];
					$sum = isset($sbArr[1])?$sbArr[1]:1;
					$sn = 0;
					$courier_id = 0;
					$p = ShipmentScan::getShipmentByBarcode($shipmentBarcode,$sn,$courier_id);
					if(!empty($p))
					{
						$record = null;
						if(!empty($ccode))//change shipment from cRack to rack
						{
							$record = WmsRackShipment::model()->find(" shipment_id = :shipment_id and rack_id = :rack_id",[":shipment_id"=>$p->id,":rack_id"=>$cRack->id]);
						}else
						{
							$record = WmsRackShipment::model()->find(" shipment_id = :shipment_id and rack_id = :rack_id",[":shipment_id"=>$p->id,":rack_id"=>$rack->id]);
						}
						
						if(!empty($record))
						{
							$record->status = WmsRackShipment::REMOVED;
							$record->save();
						}
						
						$record = new WmsRackShipment('create');
						$record->shipment_id = $p->id;
						$record->sno = $sn;
						$record->sum = $sum;
						$record->rack_id = $rack->id;
						$record->save();
						Log::add($record, 1, ['notes' =>'']);
					}else
					{
						$model->addError(" code {$shipmentBarcode} is not found "," code {$shipmentBarcode} is not found ");
					}
				}
				$transaction->commit();
			} catch (Exception $ex) {
				$transaction->rollback();
				throw $ex;
			}

			$this->ajaxResult($model);
		}

	}

	public function actionAck()
	{
		Yii::app()->cache->set('ack' . $_GET['id'], true, 300);
	}

	public function actionHoldTask($id)
	{
		$model = WmsTask::model()->findByPk($id);
		$model->mainTask->status = 40;
		$model->update('status');

		$this->ajaxResult($model);
	}

	public function actionDoubleCheck($id)
	{
		$model = WmsTask::model()->findByPk($id);
		$prods = [];
		foreach ($model->items as $item) {
			$stock = WmsStock::model()->findByPk($item->mdata['si']);
			if (empty($prods[$stock->prod->ean . '_' . $stock->prod->name])) {
				$prods[$stock->prod->ean . '_' . $stock->prod->name] = [0,0];
			}
			$prods[$stock->prod->ean . '_' . $stock->prod->name][0] += intval(@$item->mdata['sort_qty']);
			$prods[$stock->prod->ean . '_' . $stock->prod->name][1] += intval($item->mdata['uq']);
		}
		$this->renderPartial('double_check', ['prods' => $prods]);
	}

	public function actionQuickCourierLabel()
	{
		if (empty($_POST)) {
			$this->render('quick_courier_label');
		} else {
			$sn = 0;
			$courier_id = 0;
			$shipment = ShipmentScan::getShipmentByBarcode($_POST['courier'], $sn, $courier_id, true);
			if (!empty($shipment->cref)) {
				$task = WmsTask::model()->findByPk(ltrim($shipment->cref, 'T'));
			}

			if (empty($task)) {
				$task = new WmsTask;
				$task->addError('id', $_POST['courier'] . ' Invalid No');
			} else if ($task->status == 20) {
				foreach ($task->items as $item) {
					$stock = WmsStock::model()->findByPk($item->mdata['si']);
					$loc = $stock->locs[0];
					if (!empty($stock) && !empty($loc)) {
						$ac_item = new WmsTaskItem;
						$ac_item->task_id = $task->actionTask->id;
						$ac_item->mdata = array(
							'si' => $stock->id,
							'sn' => $stock->prod->name,
							'uq' => $item->mdata['uq'],
							'pli' => $loc->loc->id,
							'pl' => $loc->loc->code,
						);
						$ac_item->save();
					}
				}
				$task->status = 99;
				$task->save();
			} else {
				$task->addError('id', 'Duplicate Label');
			}

			$this->ajaxResult($task);
		}
	}

	public function actionAjaxDoubleCheck($id)
	{
		$model = WmsTask::model()->findByPk($id);

		while (true) {
			try {
				$fp = fopen(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'system_lock'.DIRECTORY_SEPARATOR.'double_check.lock', 'r');
				flock($fp, LOCK_EX);
				foreach ($model->items as $item) {
					$stock = WmsStock::model()->findByPk($item->mdata['si']);
					if ($stock->prod->ean == $_POST['ean'] && intval(@$item->mdata['sort_qty']) < $item->mdata['uq']) {
						$item->mdata['sort_qty'] = intval(@$item->mdata['sort_qty']) + 1;
						$item->update('meta');
					}
				}
				flock($fp, LOCK_UN);
				fclose($fp);
				break;
			} catch (Exception $ex) {
				throw $ex;
			}
		}
	}

	public function actionTopItem($task, $id)
	{
		$task = WmsTask::model()->findByPk($task);
		$task->mdata['top'] = $id;
		$task->updateMeta();
	}

	public function actionSign($id)
	{
		$model = WmsTask::model()->findByPk($id);
		if (empty($_POST)) {
			$this->renderPartial('sign', ['model' => $model]);
		} else {
			$pickup = array();
			$pickup[0]['co'] = $_POST['company'];
			$pickup[0]['dv'] = $_POST['driver'];
			$pickup[0]['rg'] = $_POST['rego'];
			$pickup[0]['nt'] = $_POST['note'];
			$model->mainTask->mdata = array('pickup' => json_encode($pickup));
			$model->mainTask->save();

			if (!empty($model->mainTask->pickupTask)) {
				$model->mainTask->pickupTask->mdata = array('pickup' => json_encode($pickup));
				$model->mainTask->pickupTask->save();
			}

			$f = tempnam(Yii::app()->basePath.DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
			file_put_contents($f, base64_decode(preg_replace('/^data:image\/png;base64,/', '', $_POST['sig'])));
			$id = FileRepo::storeFile($f, 'Driver-P'.date('YmdHis').'.png', 84, $model->mainTask->id);
			unlink($f);

			$this->ajaxResult($model);
		}
	}

}