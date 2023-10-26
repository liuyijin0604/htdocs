<?php

class WmsStockController extends Controller
{
	protected $nonAjax = ['export', 'report', 'inReport', 'exportLedger', 'exportStockLocation', 'updateItemNumber'];
	protected $skipAcl = [];

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model = $this->loadModel($id);
		if (isset($_GET['tab'])) {
			Acl::hasAccess($this->CaName . '/' . $_GET['tab'], true);
			$this->render('tab_' . $_GET['tab'], array('model' => $model));
		} else {
			$this->render('view', array('model' => $model));
		}
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new WmsStock;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['WmsStock'])) {
			$model->attributes = $_POST['WmsStock'];
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('create', array(
			'model' => $model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['WmsStock'])) {
			$model->attributes = $_POST['WmsStock'];
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('update', array(
			'model' => $model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if (Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax'])) {
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
			}

		} else {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}

	}

	/**
	 * Lists and search.
	 */
	public function actionList()
	{
		$model = new WmsStock('search');
		$model->unsetAttributes(); // clear any default values
		if (isset($_GET['WmsStock'])) {
			$model->attributes = $_GET['WmsStock'];
		}

		$this->render('list', array(
			'model' => $model,
		));
	}

	public function actionSuggest()
	{
		$_GET['term'] = trim($_GET['term']);
		$exs = [];
		if (preg_match('/^(.+) \(((Bat|Exp):.+)\)$/', $_GET['term'], $t)) {
			$_GET['term'] = $t[1];
			$exs = explode(', ', $t[2]);
		}

		$cond = 't.qty>0 AND t.org_id = :oid AND (prod.name LIKE :n OR prod.ean LIKE :a OR prod.name_zh LIKE :n OR packs.barcode LIKE :a OR (orgs.org_id = :oid AND orgs.sku LIKE :a))';
		$params = array(':n' => '%' . $_GET['term'] . '%', ':a' => $_GET['term'] . '%', ':oid' => $_GET['oid']);

		// 2020-04-17 add mel
		if (!empty($_GET['dpt_id'])) {
			$cond .= ' AND t.dpt_id = :dpt_id';
			$params[':dpt_id'] = $_GET['dpt_id'];
		}

		foreach ($exs as $ex) {
			$d = substr($ex, 5);
			switch (substr($ex, 0, 3)) {
				case 'Exp':
					$cond .= ' AND expiry = :exp';
					$params[':exp'] = $d;
					break;
				case 'Bat':
					$cond .= ' AND batch = :bat';
					$params[':bat'] = $d;
					break;
			}
		}

		$rs = WmsStock::model()->with(['prod.packs', 'prod.orgs'])->together()->findAll(array(
			'condition' => $cond,
			'params' => $params,
			'order' => 'name',
			'limit' => 40,
		));
		$a = [];
		$mebs = [];

		foreach ($rs as $r) {
			$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $r->prod_id]);
			$qpc = empty($pp) ? 0 : $pp->qty;

			$pp = WmsProdPack::model()->find('type = 20 AND prod_id = :pid', [':pid' => $r->prod_id]);
			$qpp = empty($pp) ? 0 : $pp->qty;

			$meb = false;
			if (!empty($mebs[$r->prod_id]) || $r->hasMultiEB()) {
				$meb = true;
				$mebs[$r->prod_id] = true;
			}

			$a[] = array(
				'value' => $r->id,
				'label' => $r->stockName(),
				'qty' => $r->qty,
				'qpc' => $qpc,
				'qpp' => $qpp,
				'meb' => $meb,
			);
		}
		echo json_encode($a);
	}

	public function actionSuggestPlt()
	{
		$_GET['term'] = trim($_GET['term']);
		if (empty($_GET['s'])) {
			$rs = WmsStockLocation::model()->with('loc')->together()->findAll(array(
				'condition' => 'loc.id > 99 AND qty > 0 AND loc.code LIKE :a',
				'params' => array(':a' => '%' . $_GET['term'] . '%'),
				'order' => 'loc.code',
				'limit' => 20,
			));
		} else {
			$rs = WmsStockLocation::model()->with('loc')->together()->findAll(array(
				'condition' => 'loc.id > 99 AND qty > 0 AND t.stock_id = :sid AND loc.code LIKE :a',
				'params' => array(':a' => '%' . $_GET['term'] . '%', ':sid' => $_GET['s']),
				'order' => 'loc.code',
				'limit' => 20,
			));
		}
		$a = array();

		foreach ($rs as $r) {
			if (empty($_GET['s'])) {
				$a[] = array(
					'value' => $r->loc->id,
					'label' => $r->loc->code . '/' . $r->stock->stockName() . ' (' . $r->qty . ')',
					'v' => $r->loc->code,
					'si' => $r->stock_id,
					'sn' => $r->stock->stockName(),
				);
			} else {
				$a[] = array(
					'value' => $r->loc->id,
					'label' => $r->loc->code . ' (' . $r->qty . ')',
					'v' => $r->loc->code,
				);
			}
		}
		echo json_encode($a);
	}

	public function actionSuggestPlt2()
	{
		$_GET['term'] = trim($_GET['term']);
		if (empty($_GET['s'])) {
			$rs = WmsStockLocation::model()->with('loc', 'stock')->together()->findAll(array(
				'condition' => 'loc.id > 99 AND t.qty > 0 AND loc.code LIKE :a AND stock.org_id = :org_id',
				'params' => array(':a' => '%' . $_GET['term'] . '%', ':org_id' => $_GET['org_id']),
				'order' => 'loc.code',
				'limit' => 20,
			));
		} else {
			$rs = WmsStockLocation::model()->with('loc')->together()->findAll(array(
				'condition' => 'loc.id > 99 AND t.qty > 0 AND loc.code LIKE :a AND t.stock_id = :sid AND loc.code LIKE :a',
				'params' => array(':a' => '%' . $_GET['term'] . '%', ':sid' => $_GET['s'], ':org_id' => $_GET['org_id']),
				'order' => 'loc.code',
				'limit' => 20,
			));
		}
		$a = array();

		foreach ($rs as $r) {
			if (empty($_GET['s'])) {
				$a[$r->loc->id] = array(
					'value' => $r->loc->id,
					'label' => $r->loc->code,
					'v' => $r->loc->code,
				);
			} else {
				$a[$r->loc->id] = array(
					'value' => $r->loc->id,
					'label' => $r->loc->code,
					'v' => $r->loc->code,
				);
			}
		}
		echo json_encode($a);
	}

	public function actionExport()
	{
		$model = new WmsStock('search');
		$model->unsetAttributes(); // clear any default values
		if (isset($_GET['WmsStock'])) {
			$model->attributes = $_GET['WmsStock'];
		}

		$dp = $model->search(false);
		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth(array(30, 15, 15, 40, 15, 15, 10, 10));
		$xls->addRow($i++, array('Customer', 'EAN', 'SKU', 'Product', 'Brand', 'Expiry', 'Batch', 'Avail. Qty', 'Rsvd. Qty'));

		foreach ($dp->data as $r) {
			$xls->addRow($i++, array($r->customer->name, '="' . $r->prod->ean . '"', '="' . $r->getCustSKU() . '"', $r->prod->name, $r->prod->brand, $r->expiry, $r->batch, $r->qty, $r->qty_res));
		}
		$xls->output('stock_search_export_' . time() . '.xlsx');
	}

	public function actionExportLedger()
	{
		if (empty($_POST)) {
			$this->render('export_ledger');
		} else {
		$model = new WmsStock('search');
		$model->unsetAttributes(); // clear any default values
		if (isset($_GET['WmsStock'])) {
			$model->attributes = $_GET['WmsStock'];
		}
		$model->org_id = $_POST['org_id'];

		$from = (!empty($_POST['fromdate']) ? $_POST['fromdate'] : '1970-01-01') . ' 00:00:00';
		$to = (!empty($_POST['todate']) ? $_POST['todate'] : date('Y-m-d')) . ' 23:59:59';

		$dp = $model->search(false);
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('Product', 'Task', 'Task Ref', 'Date', 'In', 'Out', 'Customer Name', 'Customer Contact', 'Customer Address', 'Consignment No', 'Location'));
		foreach ($dp->data as $r) {
			$rs = WmsStockLedger::model()->findAll('stock_id=:sid and location_id>10 AND ts >= :from AND ts <= :to', array(':sid' => $r->id, ':from' => $from, ':to' => $to));
			foreach ($rs as $s) {
				if (!empty($s->taskItem->task)) {
					$taskNo = $s->taskItem->task->getNo();
					$mainTask = $s->taskItem->task->mainTask;
					$taskRef = $mainTask->ref;
					$cust_name = '';
					$deliveryTask = WmsTask::model()->find('link_id=:link_id AND (type=2120 OR type=2110)', array(':link_id' => $mainTask->id));
					$cust_name = '';
					$cust_tel = '';
					$cust_addr = '';
					$connote = '';
					if (!empty($deliveryTask->mdata['cnee']['name'])) {
						$cust_name = $deliveryTask->mdata['cnee']['name'];
						$cust_tel = $deliveryTask->mdata['cnee']['tel'];
						$cust_addr = $deliveryTask->mdata['cnee']['address'].' '.$deliveryTask->mdata['cnee']['suburb'].' '.$deliveryTask->mdata['cnee']['state'].' '.$deliveryTask->mdata['cnee']['postcode'];
						if(!empty($deliveryTask->mdata['shipment_id'])){
							$p = ImParcel::model()->findByPk($deliveryTask->mdata['shipment_id']);
							$connote = $p->ref;
						}
					} else if (isset($deliveryTask->mdata['pickup'])) {
						$tmp = json_decode($deliveryTask->mdata['pickup'], true);
						if (!empty($tmp[0]['co'])) {
							$cust_name = $tmp[0]['co'];
						}
					}

					$prod = $r->prod->name;
					$date = $s->ts;
					$in = $s->qty_in;
					$out = $s->qty_out;
					$xls->addRow($i++, array($prod, $taskNo, $taskRef, $date, $in, $out, $cust_name, $cust_tel, $cust_addr, $connote, $s->loc->name));
				}
			}
		}
		$xls->output('stock_ledger_export_' . date('Ymd') . '.xlsx');
		}

	}

	public function actionExportStockLocation()
	{
		$model = new WmsStock('search');
		$model->unsetAttributes(); // clear any default values
		if (isset($_GET['WmsStock'])) {
			$model->attributes = $_GET['WmsStock'];
		}

		$ec = new CDbCriteria;
		$ec->addCondition('t.qty > 0');
		$dp = $model->search(false, 0, $ec);
		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth(array(20,10,15,10,10,15,15,10,15,10));
		$xls->addRow($i++, array('Product', 'EAN', 'Brand', 'Expiry', 'Batch', 'Pallet', 'Location', 'Qty', 'In Date', 'Days'));
		foreach ($dp->data as $r) {
			$rs = WmsStockLocation::model()->findAll('stock_id=:sid and location_id>10', array(':sid' => $r->id));
			foreach ($rs as $s) {
				if ($s->qty <= 0) {
					continue;
				}
				$sit = WmsStockLedger::stockInTime($s->location_id);
				$xls->addRow($i++, array('="' . $r->prod->name . '"', '="' . $r->prod->ean . '"', $r->prod->brand, $r->expiry, '="' . $r->batch . '"', $s->loc->name, @$s->loc->parent->name, $s->qty, substr($sit, 0, 10), round((time() - strtotime($sit)) / 86400)));
			}

		}
		$xls->output('stock_location_export_' . date('Ymd') . '.xlsx');

	}

	public function actionReport()
	{
		if (!empty($_POST['org_id'])) {
			$model = new WmsStock('search');
			$model->unsetAttributes(); // clear any default values
			$model->org_id = $_POST['org_id'];
			// stock qty back date
			// if(empty($_POST['incempty'])){
			// 	$ec = new CDbCriteria;
			// 	$ec->addCondition('t.qty > 0');
			// 	$dp = $model->search(false, 0, $ec);
			// }else{
				$dp = $model->search(false);
			// }
			$xls = new oExcel;
			$sheet = $xls->getActiveSheet();
			$sheet->setTitle('Stock');
			$i = 1;
			$org = Org::model()->findByPk($_POST['org_id']);
			$xls->setColWidth(array(15, 15, 40, 15, 15, 10, 10));
			$xls->addRow($i++, array('Customer', $org->name));
			$todate = !empty($_POST['todate']) ? $_POST['todate'] : date('Y-m-d H:i:s');
			// till end of the day
			$todate = date('Y-m-d 00:00:00', strtotime($todate . ' + 1 day'));
			$xls->addRow($i++, array('Date', $todate));
			$xls->addRow($i++, array('EAN', 'SKU', 'Product', 'Brand', 'Model', 'Expiry', 'Batch', 'Avail. Qty', 'Rsvd. Qty'));

			foreach ($dp->data as $r) {
				$ledgers = WmsStockLedger::model()->findAll('ts > :todate AND stock_id = :sid AND location_id > 10', array(':todate' => $todate, ':sid' => $r->id));
				foreach ($ledgers as $ledger) {
					if ($ledger->qty_in > 0) {
						$r->qty -= $ledger->qty_in;
					} else if ($ledger->qty_out > 0) {
						$r->qty += $ledger->qty_out;
					}
				}
				$ledgers = WmsStockLedger::model()->findAll('ts > :todate AND stock_id = :sid AND location_id = 4', array(':todate' => $todate, ':sid' => $r->id));
				foreach ($ledgers as $ledger) {
					if ($ledger->qty_in > 0) {
						$r->qty_res -= $ledger->qty_in;
					} else if ($ledger->qty_out > 0) {
						$r->qty_res += $ledger->qty_out;
					}
				}
				// stock qty back date
				if(empty($_POST['incempty']) && $r->qty == 0) {
					continue;
				}
				$xls->addRow($i++, array('="' . $r->prod->ean . '"', '="' . $r->getCustSKU() . '"', $r->prod->name, $r->prod->brand, $r->prod->model, $r->expiry, $r->batch, $r->qty, $r->qty_res));
			}

			//pallet sheet
			$xls->createSheet('Pallets');
			$xls->goSheet(1);
			$i = 1;
			$xls->setColWidth(array(20,10,15,10,10,15,15,10,15,10));
			$xls->addRow($i++, array('Product', 'EAN', 'Brand', 'Model', 'Expiry', 'Batch', 'Pallet', 'Plt Type', 'Location', 'Qty', 'In Date', 'Days', 'Notes'));
			foreach ($dp->data as $r) {
				$rs = WmsStockLocation::model()->with('loc')->findAll('t.stock_id = :sid and t.location_id > 10 and loc.status = 1', array(':sid' => $r->id));
				foreach ($rs as $s) {
					$ledgers = WmsStockLedger::model()->findAll('ts > :todate AND stock_id = :sid AND location_id = :lid', array(':todate' => $todate, ':sid' => $s->stock_id, ':lid' => $s->location_id));
					foreach ($ledgers as $ledger) {
						if ($ledger->qty_in > 0) {
							$s->qty -= $ledger->qty_in;
						} else if ($ledger->qty_out > 0) {
							$s->qty += $ledger->qty_out;
						}
					}
					if ($s->qty <= 0) {
						continue;
					}
					$sit = WmsStockLedger::stockInTime($s->location_id);
					$xls->addRow($i++, array('="' . $r->prod->name . '"', '="' . $r->prod->ean . '"', $r->prod->brand, $r->prod->model, $r->expiry, '="' . $r->batch . '"', $s->loc->name, (empty($s->loc->bwf)? '' : implode(',', AppHelper::bwf2warning($s->loc,false))),@$s->loc->parent->name, $s->qty, substr($sit, 0, 10), round((time() - strtotime($sit)) / 86400)));
				}
			}

			$xls->output('stock_report_' . date('Ymd') . '.xlsx');
		}
		$this->render('report');
	}

	public function actionInReport()
	{
		if (!empty($_POST['org_id'])) {
			$xls = new oExcel;
			$i = 1;
			$xls->setColWidth([14,14,14,33,20,16,19,16,16,16,16,10,10,10,16,33]);
			$xls->addRow($i++, ['PO No', 'Delivery time', 'Pallet No', 'Product Name产品名称', 'BARCODE', 'Batch No批次号', 'Production Date生产日期', 'Expiry Date有效期', 'Inners/master carton 件数/箱', 'master carton/pallet 箱数/板', 'Inners/pallet 件数/板', 'Pallet L', 'Pallet W', 'Pallet H', 'Pallet Weight (kg)板重', 'Pallet Material 托盘材质 (熏蒸/塑料)']);
			$xls->setBG('A1:P1', '000000');
			$xls->setFont('A1:P1', ['color' => ['rgb' => 'FFFFFF']]);
			$rs = WmsTask::model()->with('job')->findAll('job.org_id = :oid AND t.type IN (1010, 1020, 1030) AND t.link_id = 0 AND t.compl_time >= :fdate AND t.compl_time <= :tdate', [':oid' => $_POST['org_id'], ':fdate' => $_POST['fromdate'], ':tdate' => $_POST['todate']]);
			foreach($rs as $r){
				foreach ($r->actionTask->items as $k => $itm) {
					if (empty($itm->mdata['pl']) || empty($itm->mdata['gi'])) {
						continue;
					}

					$prod = WmsProd::model()->findByPk($itm->mdata['gi']);
					$plt = WmsLocation::model()->find('code = :n OR name = :n', [':n' => $itm->mdata['pl']]);
					$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = 10', [':prod_id' => $itm->mdata['gi']]);
					$pack = max(1, intval(@$pack->qty));
					$xls->addRow($i++, [$r->ref, $r->lastlog->time, $plt->name, $prod->name, $prod->ean, @$itm->mdata['bn'], @$itm->mdata['pd'], @$itm->mdata['ex'], $pack, $itm->mdata['uq'] / $pack, $itm->mdata['uq'], $plt->depth, $plt->width, $plt->height, $plt->wt, !empty($plt->extra['type']) ? $plt->extra['type'] == 'plastic' ? '塑料板' : '熏蒸板' : '']);
				}
			}

			$xls->output($_POST['org_id'].'_stock_in_report_' . date('Ymd') . '.xlsx');
		}
		$this->render('inreport');
	}

	public function actionEditStock()
	{
		if (empty($_POST)) {
			$this->render('update_stock', array('type' => $_GET['type']));
		} else {
			if (empty($_POST['owner_id'])) {
				echo json_encode(['done' => false, 'msg' => 'Please choose owner']);
				yii::app()->end();
			} else if (empty($_POST['stocks'])) {
				echo json_encode(['done' => false, 'msg' => 'Please select stocks']);
				yii::app()->end();
			}

			$dpt_id = $_POST['stocks'][0]['dpt_id'];

			$job = WmsJob::model()->find(['condition' => 'org_id = :org_id AND type = 40 AND dpt_id = :dpt_id', 'params' => array(':org_id' => $_POST['owner_id'],':dpt_id' => $dpt_id), 'order' => 'id desc']);

			
			if (empty($job)) {
				$job = new WmsJob;
				$job->org_id = $_POST['owner_id'];
				$job->type = 40;
				$job->status = 10;
				$job->dpt_id = $dpt_id;
				$job->save();
			}

			if ($_POST['type'] == 'in') {
				// create task
				$task = new WmsTask;
				$task->job_id = $job->id;
				$task->type = 1010;
				$task->is_request = 1;
				$task->op_id = Yii::app()->user->id;
				$task->status = 20;
				$task->ref = 'stock take in ' . date('Y-m-d');
				$task->bwf = $task->bwf | 16;
				$task->dpt_id = $job->dpt_id;
				$items = [];
				foreach ($_POST['stocks'] as $stock) {
					if ($stock['qty'] > 0) {
						$prod = WmsProd::model()->findByPk($stock['id']);
						$items[] = ['gi' => $prod->id, 'gn' => $prod->name, 'uq' => $stock['qty']];
					}
				}
				$task->new_items = $items;

				if (empty($task->getErrors())) {
					$task->save();
					// do wma
					foreach ($_POST['stocks'] as $stock) {
						if ($stock['qty'] > 0) {
							$prod = WmsProd::model()->findByPk($stock['id']);
							$itm = new WmsTaskItem;
							$itm->task_id = $task->actionTask->id;
							$itm->mdata = ['gi' => $prod->id, 'gn' => $prod->name, 'uq' => $stock['qty'], 'pl' => $stock['loc'], 'cq' => '', 'ex' => @$stock['expiry'], 'bn' => @$stock['batch'], 'nt' => ''];
							$itm->save();
						}
					}
				}

				$task->status = array_search('WIP', WmsTask::$states);
				$task->save();
			} else if ($_POST['type'] == 'out') {
				$task = new WmsTask;
				$task->job_id = $job->id;
				$task->type = 3030;
				$task->is_request = 1;
				$task->op_id = Yii::app()->user->id;
				$task->status = 20;
				$task->ref = 'stock take out ' . date('Y-m-d');
				$task->bwf = $task->bwf | 16;
				$task->dpt_id = $job->dpt_id;
				$items = [];
				foreach ($_POST['stocks'] as $stock) {
					if ($stock['qty'] > 0) {
						$ss = WmsStock::model()->findAll('prod_id = :prod_id AND batch = :batch and org_id = :org_id and dpt_id = :dpt_id', array(':prod_id' => $stock['id'], ':batch' => @$stock['batch'], ':org_id' => $_POST['owner_id'],':dpt_id'=>$dpt_id));
						$l = WmsLocation::model()->find('code = :code AND status = 1', array(':code' => $stock['loc']));
						if (empty($l)) {
							echo json_encode(['done' => false, 'msg' => 'Location ' . $stock['loc'] . ' is not exist']);
							yii::app()->end();
						}

						// multi stock for same batch and expiry
						foreach ($ss as $s) {
							$sl = WmsStockLocation::model()->find('stock_id = :stock_id AND location_id = :location_id', array(':stock_id' => $s->id, ':location_id' => $l->id));
							if (!empty($sl)) {
								break;
							}
						}

						if (empty($sl)) {
							echo json_encode(['done' => false, 'msg' => $s->prod->name . ' ' . @$stock['batch'] . ' is not in ' . $l->code]);
							yii::app()->end();
						} else if ($sl->qty < $stock['qty']) {
							echo json_encode(['done' => false, 'msg' => $s->prod->name . ' qty is not enough at ' . $l->code . ', having ' . $sl->qty . ' requiring ' . $stock['qty']]);
							yii::app()->end();
						} else {
							$item = array(
								'si' => $s->id,
								'sn' => $s->prod->name,
								'pq' => '',
								'cq' => '',
								'uq' => $stock['qty'],
								'pli' => $l->id,
								'pl' => $l->code,
								'nt' => '',
							);
							$items[] = $item;
						}
					}
				}
				$task->new_items = $items;

				if (empty($task->getErrors())) {
					$task->save();
					// do wma
					$ac_task = $task->actionTask;
					foreach ($task->items as $item) {
						$stock = WmsStock::model()->findByPk($item->mdata['si']);
						$sls = $stock->locs;
						$qty = $item->mdata['uq'];
						foreach ($sls as $sl) {
							if ($qty > 0) {
								$itm = new WmsTaskItem;
								$itm->task_id = $ac_task->id;
								$itm->mdata = array(
									'si' => $item->mdata['si'],
									'sn' => $item->mdata['sn'],
									'uq' => ($qty > $sl->qty ? $sl->qty : $qty),
									'pli' => $item->mdata['pli'],
									'pl' => $item->mdata['pl'],
								);
								$itm->save();
								$qty -= $qty > $sl->qty ? $sl->qty : $qty;
							}
						}
					}
					$ac_task->save();
					$task->status = array_search('WIP', WmsTask::$states);
					$task->save();
				}
			}

			echo json_encode(['done' => true, 'msg' => 'Successfully']);
			yii::app()->end();
		}
	}

	public function actionTransferStock()
	{
		if (empty($_POST)) {
			$this->render('transfer_stock');
		} else {
			$model = new WmsStock;
			if (empty($_POST['from_id'])) {
				$model->addError('id', 'Please select From Org');
				$this->ajaxResult($model);
			} else if (empty($_POST['to_id'])) {
				$model->addError('id', 'Please select To Org');
				$this->ajaxResult($model);
			} else if (empty($_POST['si'])) {
				$model->addError('id', 'Please select Stock');
				$this->ajaxResult($model);
			} else if (empty($_POST['uq'])) {
				$model->addError('id', 'Please enter qty');
				$this->ajaxResult($model);
			}

			// transfer from
			$job = WmsJob::model()->find(['condition' => 'org_id = :org_id AND type = 40', 'params' => [':org_id' => $_POST['from_id']], 'order' => 'id desc']);
			if (empty($job)) {
				$job = new WmsJob;
				$job->org_id = $_POST['from_id'];
				$job->type = 40;
				$job->status = 10;
				$job->save();
			}

			$task = new WmsTask;
			$task->job_id = $job->id;
			$task->type = 3030;
			$task->is_request = 1;
			$task->op_id = Yii::app()->user->id;
			$task->status = 20;
			$task->ref = 'stock transfer to ' . Org::model()->findByPk($_POST['to_id'])->shortName() . ' ' . date('Y-m-d');
			$task->bwf = 16;
			$items = [];
			if (!empty($_POST['si']) && array_unique(array_values($_POST['si'])) != ['']) {
				foreach ($_POST['si'] as $k => $si) {
					$qty = $_POST['uq'][$k];

					if ($qty > 0) {
						$stock = WmsStock::model()->findByPk($si);

						if ($stock->qty < $qty) {
							$model->addError('id', $stock->prod->name . ' qty is not enough');
							$this->ajaxResult($model);
						} else {
							if (!empty($_POST['pl'][$k])) {
								$loc = WmsLocation::model()->find('name = :name', [':name' => $_POST['pl'][$k]]);
								if (empty($loc)) {
									$model->addError('id', $_POST['pl'][$k] . ' is not exist');
									$this->ajaxResult($model);
								}
							}
							$items[] = array(
								'si' => $stock->id,
								'sn' => $stock->prod->name,
								'pq' => '',
								'cq' => '',
								'uq' => $qty,
								'pli' => '',
								'pl' => @$_POST['pl'][$k],
								'nt' => '',
							);
						}
					}
				}
			} else if (!empty($_POST['pl']) && array_unique(array_values($_POST['pl'])) != ['']) {
				foreach ($_POST['pl'] as $k => $pl) {
					$locs = WmsStockLocation::model()->with('loc')->findAll('loc.code = :code', [':code' => $pl]);
					foreach ($locs as $loc) {
						if ($loc->stock->org_id != $_POST['from_id'] || !($loc->qty > 0)) {
							continue;
						}
						$items[] = array(
							'si' => $loc->stock->id,
							'sn' => $loc->stock->prod->name,
							'pq' => '',
							'cq' => '',
							'uq' => $loc->qty,
							'pli' => '',
							'pl' => $pl,
							'nt' => '',
						);
					}
				}
			}
			$task->new_items = $items;

			$transfers = [];
			if (empty($task->getErrors())) {
				$task->save();

				$ac_task = $task->actionTask;
				foreach ($task->items as $item) {
					$stock = WmsStock::model()->findByPk($item->mdata['si']);
					$sls = $stock->locs;
					$qty = $item->mdata['uq'];
					foreach ($sls as $sl) {
						if ($qty > 0) {
							$itm = new WmsTaskItem;
							$itm->task_id = $ac_task->id;
							if (!empty($item->mdata['pl'])) {
								$loc = WmsLocation::model()->find('name = :name', [':name' => $item->mdata['pl']]);
							} else {
								$loc = $sl->loc;
							}
							$itm->mdata = array(
								'si' => $item->mdata['si'],
								'sn' => $item->mdata['sn'],
								'uq' => min($qty, $sl->qty),
								'pli' => $loc->id,
								'pl' => $loc->code,
							);
							$itm->save();
							$qty -= min($qty, $sl->qty);
							$transfers[] = $itm;
						}
					}
				}
				//$ac_task->save();
				$task->status = array_search('WIP', WmsTask::$states);
				$task->save();
			}

			// transfer to
			$job = WmsJob::model()->find(['condition' => 'org_id = :org_id AND type = 40', 'params' => [':org_id' => $_POST['to_id']], 'order' => 'id desc']);
			if (empty($job)) {
				$job = new WmsJob;
				$job->org_id = $_POST['to_id'];
				$job->type = 40;
				$job->status = 10;
				$job->save();
			}

			$task = new WmsTask;
			$task->job_id = $job->id;
			$task->type = 1010;
			$task->is_request = 1;
			$task->op_id = Yii::app()->user->id;
			$task->status = 20;
			$task->ref = 'stock transfer from ' . Org::model()->findByPk($_POST['from_id'])->shortName() . ' ' . date('Y-m-d');
			$task->bwf = 16;
			$items = [];
			if (!empty($_POST['si']) && array_unique(array_values($_POST['si'])) != ['']) {
				foreach ($_POST['si'] as $k => $si) {
					$qty = $_POST['uq'][$k];

					if ($qty > 0) {
						$stock = WmsStock::model()->findByPk($si);
						$items[] = array(
							'gi' => $stock->prod->id,
							'gn' => $stock->prod->name,
							'uq' => $qty,
						);
					}
				}
			} else if (!empty($_POST['pl']) && array_unique(array_values($_POST['pl'])) != ['']) {
				foreach ($transfers as $item) {
					$stock = WmsStock::model()->findByPk($item->mdata['si']);
					$items[] = array(
						'gi' => $stock->prod->id,
						'gn' => $item->mdata['sn'],
						'uq' => $item->mdata['uq'],
					);
				}
			}
			$task->new_items = $items;

			if (empty($task->getErrors())) {
				$task->save();

				$ac_task = $task->actionTask;
				foreach ($transfers as $item) {
					$stock = WmsStock::model()->findByPk($item->mdata['si']);
					$itm = new WmsTaskItem;
					$itm->task_id = $ac_task->id;
					$itm->mdata = array(
						'gi' => $stock->prod->id,
						'gn' => $stock->prod->name,
						'uq' => $item->mdata['uq'],
						'pl' => $item->mdata['pl'],
						'cq' => '',
						'ex' => $stock->expiry,
						'bn' => $stock->batch,
						'nt' => '',
					);
					$itm->save();
				}
				//$ac_task->save();
				$task->status = array_search('WIP', WmsTask::$states);
				$task->save();
			}

			$this->ajaxResult($model);
		}
	}

	public function actionUpdateItemNumber()
	{
		if (empty($_POST)) {
			if (!empty($_GET['template'])) {
				$xls = new oExcel;
				$xls->addRow(1, ['EAN', 'Expiry', 'Batch', 'Item Number']);
				$xls->output('update_item_number_template.xlsx');
			} else {
				$this->render('update_item_number');
			}
		} else {
			$file = empty($_FILES['file']) ? [] : $_FILES['file'];
			if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
				echo json_encode(['done' => false, 'msg' => 'Invalid template file']);
				Yii::app()->end();
			}

			if (empty($_POST['agent_id'])) {
				echo json_encode(['done' => false, 'msg' => 'Pleae choose customer']);
				Yii::app()->end();
			}

			$xls = new oExcel;
			$xls->load($file['tmp_name']);
			$data = $xls->getAll();
			unset($data[1]);

			$err = [];
			foreach ($data as $k => $line) {
				if (!empty($line[2])) {
					$stock = WmsStock::model()->with('prod')->find('t.org_id = :oid AND prod.ean = :ean AND expiry = :expiry AND batch = :batch', [':oid' => $_POST['agent_id'], ':ean' => $line[1], ':expiry' => $line[2], ':batch' => $line[3]]);
				} else {
					$stock = WmsStock::model()->with('prod')->find('t.org_id = :oid AND prod.ean = :ean AND expiry IS NULL AND batch = :batch', [':oid' => $_POST['agent_id'], ':ean' => $line[1], ':batch' => $line[3]]);
				}
				if (empty($stock)) {
					$err[] = 'Stock not found in line ' . $k;
				} else {
					$stock->sn = $line[4];
					$stock->update('sn');
				}
			}

			if (!empty($err)) {
				echo json_encode(['done' => false, 'msg' => implode('; ', $err)]);
				Yii::app()->end();
			} else {
				echo json_encode(['done' => true, 'msg' => 'Update successfully']);
				Yii::app()->end();
			}
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = WmsStock::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'wms-stock-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
