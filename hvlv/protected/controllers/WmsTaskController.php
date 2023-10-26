<?php

class WmsTaskController extends Controller
{
	protected $nonAjax = ['export', 'print', 'batchExport', 'quickCourierLabel','calcuteFee'];
	protected $skipAcl = [];

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view', array(
			'model' => $this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($id)
	{
		$job = WmsJob::model()->find(['condition' => 'id=:id', 'params' => array(':id'=>$id), 'order' => 'id DESC']);

		$model = new WmsTask;
		$model->job_id = $id;
		$model->type = $_GET['type'];
		$model->is_request = 1;
		$model->dpt_id=$job->dpt_id;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['WmsTask'])) {
			$model->attributes = $_POST['WmsTask'];
			if (!empty($_POST['mdata'])) {
				foreach ($_POST['mdata'] as $k => $v) {
					if (!empty($v) || strlen($v) > 0) {
						$model->mdata[$k] = $v;
					}

				}
				if (!empty($_POST['mdata']['cnee'])) {
					if (!Postcode::validateAddress($_POST['mdata']['cnee']['suburb'], $_POST['mdata']['cnee']['state'], $_POST['mdata']['cnee']['postcode'])) {
						$model->addError('meta', 'Error Address');
					}
				}
			}
			$meta_items = json_decode($_POST['meta_items'], true);
			foreach ($meta_items as $k => $itm) {
				if (empty($itm['gi']) && !empty($itm['gn'])) {
					$model->addError('meta', 'Line ' . ($k + 1) . ': product ' . $itm['gn'] . ' not found');
				}
				if (!empty($itm['gi']) && empty($itm['uq']) && !empty($itm['cq'])) {
					$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $itm['gi']]);
					if (!empty($pp) && !empty($pp->qty)) {
						$meta_items[$k]['uq'] = intval($itm['cq']) * $pp->qty;
					}
				}
				if (!empty($itm['ex'])) {
					$exp = WmsStock::parseExpiry($itm['ex']);
					if (empty($exp)) {
						$model->addError('meta', 'Line ' . ($k + 1) . ': exp ' . $itm['ex'] . ' is not a date');
					} else {
						$meta_items[$k]['ex'] = $exp;
					}
				}
			}
			$model->new_items = $meta_items;
			if (!empty($_POST['meta'])) {
				foreach($_POST['meta'] as $k => $v){
					$model->mdata[$k] = $v;
				}
			}
			if (empty($model->getErrors())) {
				$model->save();
			}

			$this->ajaxResult($model, ['id']);
		}
		$model->status = 10;

		/*if($model->type == '1110'){
		$ps = [];
		$itms = [];
		foreach($model->job->tasks as $t){
		if(!in_array($t->type, [1010, 1020, 1030])) continue;
		foreach($t->items as $itm){
		if(empty($itm->mdata['pl']) || in_array($itm->mdata['pl'], $ps)) continue;
		$ps[] = $itm->mdata['pl'];
		$o = new WmsTaskItem;
		$o->mdata = ['pl' => $itm->mdata['pl']];
		$itms[] = $o;
		}
		}
		$model->items = $itms;
		}*/

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
		if (in_array($model->type, [1010]) && !empty($model->mdata['res']) && !empty($model->mdata[Yii::app()->user->id]) && $model->mdata[Yii::app()->user->id] != $model->mdata['res']) {
			$model->mdata[Yii::app()->user->id] = $model->mdata['res'];
			$model->save();
		}

		if (empty($_POST)) {
			if (isset($_GET['tab'])) {
				Acl::hasAccess($this->CaName . '/' . $_GET['tab'], true);
				$this->render('tab_' . $_GET['tab'], array('model' => $model));
			} else {
				$this->render('update', array('model' => $model));
			}
		} else {
			if (empty($_POST['act_btn']) || $_POST['act_btn'] == 'Save') {
				$this->_update($model);
			} else if ($_POST['act_btn'] == 'Short Release') {
				$this->_shortRelease($model);
			} else if ($_POST['act_btn'] == 'Confirm Diff') {
				$this->_confirmDiff($model);
			}
		}
	}

	public function _shortRelease(&$model)
	{
		foreach ($model->items as $item) {
			if (empty($item->mdata['pi'])) {
				if (empty($item->mdata['sn'])) {
					$model->addError('id', 'Item id ' . $item->id . ' info incomplete');
					continue;
				}

				$prod = WmsProd::model()->find('ean = :ean', [':ean' => $item->mdata['sn']]);
				if (empty($prod)) {
					$prod = WmsProd::model()->with('orgs')->find('orgs.sku = :sku', [':sku' => $item->mdata['sn']]);
				}
				if (empty($prod)) {
					$model->addError('id', 'Item id ' . $item->id . ' ean/sku ' . $item->mdata['sn'] . ' not found');
					continue;
				}

				$item->mdata['nt'] = 'short release';
				$item->mdata['pi'] = $prod->id;
				$item->mdata['sn'] = $prod->name;
				$item->update('meta');

				$model->mdata['note'] .= 'Short release ' . $prod->name . ' ';
			}

			// dont change main task item uq
			// $stock = WmsStock::model()->find(['select' => 'SUM(qty) AS qty, SUM(qty_res) AS qty_res', 'condition' => 'prod_id = :prod_id AND org_id = :org_id', 'params' => [':prod_id' => $item->mdata['pi'], ':org_id' => $model->job->org_id]]);
			// if ($stock->qty - $stock->qty_res < $item->mdata['uq']) {
			// 	$item->mdata['uq'] = $stock->qty - $stock->qty_res;
			// 	$item->update('meta');
			// }
		}

		if (!$model->getErrors()) {
			$model->status = 20;
			$model->update('status');
			$model->refresh();
			foreach ($model->items as $item) {
				$prod = WmsProd::model()->findByPk($item->mdata['pi']);
				$stocks = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id AND qty - qty_res > 0', [':prod_id' => $item->mdata['pi'], ':org_id' => $model->job->org_id]);
				$qty = $item->mdata['uq'];
				foreach ($stocks as $stock) {
					if ($qty <= 0) {
						break;
					}
					$uq = min($qty, $stock->availQty());

					$new_item = new WmsTaskItem;
					$new_item->task_id = $item->task_id;
					$new_item->op_id = $item->op_id;
					$new_item->ts = $item->ts;
					$new_item->sl_id = 0;
					$new_item->mdata = [
						'si' => $stock->id,
						'sn' => $stock->prod->name,
						'pq' => '',
						'cq' => '',
						'uq' => $uq,
						'pli' => '',
						'pl' => '',
						'nt' => '',
						'pi' => $stock->prod->id,
					];
					$new_item->save();
					$qty -= $uq;
				}
				if ($qty > 0) {
					$new_item = new WmsTaskItem;
					$new_item->task_id = $item->task_id;
					$new_item->op_id = $item->op_id;
					$new_item->ts = $item->ts;
					$new_item->sl_id = 0;
					$new_item->mdata = [
						'si' => '',
						'sn' => $prod->name,
						'pq' => '',
						'cq' => '',
						'uq' => $qty,
						'pli' => '',
						'pl' => '',
						'nt' => '',
						'pi' => $prod->id,
					];
					$new_item->save();
					$qty -= $qty;
				}
				$item->del = 1;
				$item->meta_changed = false;
				$item->save();
			}
		}
		$this->ajaxResult($model);
	}

	public function _confirmDiff(&$model)
	{
		$model->mdata['confirmed_diff'] = true;
		$model->updateMeta();
		$this->ajaxResult($model);
	}

	public function _update(&$model)
	{
		$transaction = Yii::app()->db->beginTransaction();
		// no wms invoice - eva
		if (!empty($_POST['noinv']) && ($model->bwf & 4) == 0) {
			$model->bwf |= 4;
			$model->custom_log_note = 'mark as not creating wms invoice';
		} else if (empty($_POST['noinv']) && $model->bwf & 4) {
			$model->bwf ^= 4;
			$model->custom_log_note = 'unmark as not creating wms invoice';
		}

		unset($_POST['WmsTask']['is_request']);
		if (!empty($_POST['WmsTask'])) {
			$model->attributes = $_POST['WmsTask'];
		}
		if (!empty($_POST['mdata'])) {
			if(empty($_POST['mdata']['pi_cargo'])){
				$_POST['mdata']['pi_cargo'] = 0;
			}
			if(empty($_POST['mdata']['simple_in'])){
				$_POST['mdata']['simple_in'] = 0;
			}
			if(empty($_POST['mdata']['single_item'])){
				$_POST['mdata']['single_item'] = 0;
			}
			foreach ($_POST['mdata'] as $k => $v) {
				if ($k == 'pkg') {
					if(isset($_POST['from_product'])){
						// set weight and dim from production
						$listPkg = [];
						$listWmsTaskItem = WmsTaskItem::model()->findAll('task_id=:task_id',['task_id'=>$model->link_id]);
						foreach ($listWmsTaskItem as $objWmsTaskItem){
							$objProdItem = $objWmsTaskItem->mdata;
							
							if(empty($objProdItem['si'])){
								continue;
							}
							
							$objWmsStock = WmsStock::model()->findByPk($objProdItem['si']);
							$objProd = WmsProd::model()->findByPk($objWmsStock->prod_id);
							
							$numQty = $objProdItem['uq'];
							for($i=0;$i<$numQty;$i++){
								$listItem = [];
								$listItem['wt'] = $objProd->weight/1000;
								$listItem['w'] = $objProd->dims['w'];
								$listItem['h'] = $objProd->dims['h'];
								$listItem['d'] = $objProd->dims['d'];
								$listItem['nt'] = 'from product';
								$listPkg[] = $listItem;
							}
						}
						if(!empty($listPkg)){
							$v = json_encode($listPkg);
						}
						
					}
					else{
						if(!empty($_POST['pallets'])){
							$model->mdata['Palltes']=$_POST['pallets'];				
						}
						$v = json_decode($v, true);
						$res = [];
						foreach ($v as $pkg_k => $pkg_item) {
							if (empty($pkg_item['wt']) && empty($pkg_item['w']) && empty($pkg_item['h']) && empty($pkg_item['d']) && empty($pkg_item['nt'])) {
								continue;
							}
							$res[] = $pkg_item;
						}
						$v = json_encode($res);
					}

					
				}
				if (is_array($v)) {
					$v = array_map('trim', $v);
				} elseif (is_string($v)) {
					$v = trim($v);
				}
				if (!empty($v) || strlen($v) >= 0) {
					$model->mdata[$k] = $v;
				}

			}
			$mdata = array('pi_cargo', 'pi_stockin', 'pi_photo', 'pi_weight', 'pi_count', 'pi_batch', 'pi_expiry', 'pi_split', 'pi_eta', 'pi_etd', 'pi_expect', 'pi_other', 'simple_in', 'single_item', 'si_expect', 'pcae_arrange');
			foreach ($mdata as $key) {
				if (empty($_POST['mdata'][$key])) {
					unset($model->mdata[$key]);
				}
			}
			if (!empty($_POST['mdata']['cnee']) && in_array($_POST['mdata']['cnee']['country'], ['AU'])) {
				if (!Postcode::validateAddress($_POST['mdata']['cnee']['suburb'], $_POST['mdata']['cnee']['state'], $_POST['mdata']['cnee']['postcode'], $_POST['mdata']['cnee']['country'])) {
					$model->addError('meta', 'Error Address');
				}
				// if (Addr::checkIsPoBox($model->mdata['cnee']['address']) && !in_array($model->mdata['courier'], [Org::ORGID_COURIER_AUPOST, Org::ORGID_COURIER_STARTRACK])) {
				// 	$model->mdata['courier'] = Org::ORGID_COURIER_AUPOST;
				// 	$model->save();
				// }
				if ($model->mdata['courier'] == Org::ORGID_COURIER_STARTRACK) {
					$error = false;
					foreach (json_decode($model->mainTask->packTask->mdata['pkg'], true) as $pk) {
						if (empty($pk['w']) || empty($pk['h']) || empty($pk['d'])) {
							$error = true;
						}
					}
					if ($error) {
						$model->addError('id', 'Please fill DIM info');
						$model->mdata['courier'] = 114;
						$model->update('meta');
					}
				}
			}
		}
		if (!empty($_POST['meta_items'])) {
			$meta_items = json_decode($_POST['meta_items'], true);
			foreach ($meta_items as $k => $itm) {
				if (empty($itm['gi']) && !empty($itm['gn'])) {
					$model->addError('meta', 'Line ' . ($k + 1) . ': product ' . $itm['gn'] . ' not found');
				}
				if (!empty($itm['gi']) && empty($itm['uq']) && !empty($itm['cq'])) {
					$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $itm['gi']]);
					if (!empty($pp) && !empty($pp->qty)) {
						$meta_items[$k]['uq'] = intval($itm['cq']) * $pp->qty;
					}
				}
				if (!empty($itm['si']) && !empty($itm['cq'])) {
					$s = WmsStock::model()->findByPk($itm['si']);
					$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $s->prod->id]);
					if (!empty($pp) && !empty($pp->qty)) {
						$meta_items[$k]['uq'] = intval($itm['cq']) * $pp->qty;
					}
				}
				if (!empty($itm['si']) && !empty($meta_items[$k]['uq'])) {
					$s = WmsStock::model()->findByPk($itm['si']);
					$item = WmsTaskItem::model()->findByPk($itm['id']);
					if (!empty($item) && $meta_items[$k]['uq'] > 0 && (intval($meta_items[$k]['uq']) > intval(intval($s->availQty()) + intval($item->mdata['uq'])))) {
						$meta_items[$k]['uq'] = $s->availQty();
						if (!empty($pp) && !empty($pp->qty) && !empty($meta_items[$k]['cq'])) {
							$meta_items[$k]['cq'] = intval(intval($meta_items[$k]['uq']) / $pp->qty);
						}
						$model->addError('task_id', 'Not enough stock to reserve ' . $meta_items[$k]['sn'] . ', available ' . ($s->availQty() + intval($item->mdata['uq'])));
					}
				}
				if (!empty($itm['ex'])) {
					$exp = WmsStock::parseExpiry($itm['ex']);
					if (empty($exp)) {
						$model->addError('meta', 'Line ' . ($k + 1) . ': exp ' . $itm['ex'] . ' is not a date');
					} else {
						$meta_items[$k]['ex'] = $exp;
					}
				}
				// choose pallet to stock out
				if (!empty($itm['pl'])) {
					$plt = WmsLocation::model()->find('name = :name', [':name' => $itm['pl']]);
					if (!empty($plt)) {
						$meta_items[$k]['pli'] = $plt->id;
						$sl = WmsStockLocation::model()->with('stock.prod')->find('prod.name = :name AND t.location_id = :location_id AND t.qty > 0', [':name' => $itm['sn'], ':location_id' => $plt->id]);
						if (!empty($sl)) {
							$meta_items[$k]['si'] = $sl->stock_id;
						}
					}
				}
			}
			$model->new_items = $meta_items;
		}
		if (!empty($_POST['meta'])) {
			foreach($_POST['meta'] as $k => $v){
				$model->mdata[$k] = $v;
			}
		}
		if (empty($model->getErrors())) {
			if (in_array($model->type, [1010])) {
				$model->mdata['req'] = date('Y-m-d H:i:s');
			}
			$model->save();
		}
		if(!empty($_POST['autochoose'])){
			$model->chooseCourier();
		}
		if (!empty($model->getErrors())) {
			$transaction->rollback();
		} else {
			$transaction->commit();
		}
		$this->ajaxResult($model);
	}

	public function actionImport($id)
	{
		$model = WmsJob::model()->findByPk($id);
		$this->render('task_import', array(
			'model' => $model,
		));
	}

	public function actionImportProduct()
	{
		if (!empty($_FILES)) {
			$resp = array('done' => true, 'msg' => 'Import successfully');
			if (!empty($_POST['WmsJob']['id'])) {
				$id = $_POST['WmsJob']['id'];
				$org_id = WmsJob::model()->findByPk($id)->org_id;
				$dpt_id = WmsJob::model()->findByPk($id)->dpt_id;
			} else {
				$resp['done'] = false;
				$resp['msg'] = 'Job id not found!';
				echo json_encode($resp);
				return;
			}

			//get available courier from org
			$org = Org::model()->findByPk($org_id);

			if (!empty($_FILES['inward_excel'])) {
				$xls = new oExcel;
				$err = [];
				if (!$xls->supported($_FILES['inward_excel']['name'])) {
					foreach ($xls->getError() as $e) {
						$err[] = $e;
					}
				} else {
					$xls->load($_FILES['inward_excel']['tmp_name']);
					$data = $xls->getAll();
					if (implode('', $data[1]) != "Task no.Task RefSchedule TimeDue Time / EDTProduct NameBarcode / SkuCarton QtyUnit QtyExpiryBatchNote") {
						$err[] = "template wrong";
					}

					if (!empty($err)) {
						$resp['msg'] = implode(';', $err);
						$resp['done'] = false;
						echo json_encode($resp);
						return;
					}
					unset($data[1]);
					$tasks = [];

					$last_taskno = 0;
					$products = [];
					foreach ($data as $i => $d) {
						if (empty($d[1]) && empty($d[2])) {
							continue;
						}

						if (empty($d[1])) {
							$err[] = "line " . $i . " task no is empty!";
						} else {
							if ($last_taskno != $d[1]) {
								if (empty($d[3])) {
									$err[] = "line " . $i . " task schedule time is empty!";
									continue;
								} else if (empty($d[4])) {
									$err[] = "line " . $i . " task due time / EDT is empty!";
									continue;
								}
								$last_taskno = $d[1];
								$tasks[$d[1]] = array(
									'ref' => $d[2],
									'sch_t' => oExcel::toDate($d[3]),
									'due_t' => oExcel::toDate($d[4]),
									'products' => []
								);
							}
							if (empty($d[6])) {
								$err[] = "line " . $i . " product barcode / sku is empty!";
								continue;
							} else if (empty($d[8]) && empty($d[9])) {
								$err[] = "line " . $i . " product carton quantity and unit quantity are empty!";
								continue;
							}
							$product = WmsProd::model()->find('ean = :ean', array(':ean' => $d[6]));
							if (empty($product)) {
								$product = WmsProd::model()->with(['orgs'])->together()->find('orgs.org_id = :oid AND sku = :sku', array(':oid' => $org_id, ':sku' => $d[6]));
								if (empty($product)) {
									$err[] = "line " . $i . " product barcode / sku is not found in the system!";
									continue;
								}
							}
							$tasks[$d[1]]['products'][] = array(
								'gi' => $product->id,
								'gn' => $product->name,
								'cq' => $d[7],
								'uq' => $d[8],
								'ex' => $d[9],
								'bn' => $d[10],
								'nt' => $d[11],
							);
						}
					}

					if (!empty($err)) {
						$resp['msg'] = implode('<br />', $err);
						$resp['done'] = false;
						echo json_encode($resp);
						return;
					}

					foreach ($tasks as $temp_task) {
						if (empty($temp_task)) {
							continue;
						}

						// ausriver task ref duplicate
						$wmstask = WmsTask::model()->with('job')->find('t.ref = :ref AND job.org_id = :oid', array(':ref' => $temp_task['ref'], ':oid' => Yii::app()->user->org));
						if (!empty($wmstask)) {
							$resp['done'] = false;
							$resp['msg'] .= '<br />' . $temp_task['ref'] . ' has been uploaded before';
							continue;
						}

						$task = new WmsTask();
						$task->job_id = $id;
						$task->ref = $temp_task['ref'];
						$task->status = array_search('Scheduled', WmsTask::$states);
						$task->is_request = 1;
						$task->type = array_search('Pallets In', WmsTask::$types);
						$task->schd_time = $temp_task['sch_t'];
						$task->due_time = $temp_task['due_t'];
						$task->dpt_id = $dpt_id;

						foreach ($temp_task['products'] as $t) {
							$item = $t;
							$item['pli'] = '';
							$item['pl'] = '';
							if (empty($t['uq']) && !empty($t['cq'])) {
								$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $product->id]);
								if (!empty($pp) && !empty($pp->qty)) {
									$item['uq'] = intval($t['cq']) * $pp->qty;
								}
							}
							if (!empty($t['ex'])) {
								$exp = WmsStock::parseExpiry($t['ex']);
								$item['ex'] = $exp;
							}
							$task->new_items[] = $item;
						}

						if (!$task->save()) {
							$this->ajaxResult($task);
						}
					}
					echo json_encode($resp);
				}
			} elseif (!empty($_FILES['pickup_excel'])) {
				//import pickup tasks
				$xls = new oExcel;
				$err = [];
				if (!$xls->supported($_FILES['pickup_excel']['name'])) {
					foreach ($xls->getError() as $e) {
						$err[] = $e;
					}
				} else {
					$xls->load($_FILES['pickup_excel']['tmp_name']);
					$xls->goSheet(0);
					$data = $xls->getAll();
					if (strtolower(implode('', $data[1])) != strtolower("Task no.Task RefSchedule TimeCourierPhoto & MarkCneeTelAddressCitySuburbStatePostcodeCountryProduct NameBarcode / SkuCarton QtyUnit QtyExpiryBatchTracking No")) {
						$err[] = "template wrong";
					}

					if (!empty($err)) {
						$resp['msg'] = implode(';', $err);
						$resp['done'] = false;
						echo json_encode($resp);
						return;
					}
					unset($data[1]);
					$tasks = [];

					$last_taskno = 0;
					foreach ($data as $i => $d) {
						if ($d[1] == "Instruction") {
							break;
						}

						if (empty($d[1]) && empty($d[3]) && empty($d[4]) && empty($d[15])) {
							continue;
						}

						if (empty($d[1])) {
							$err[] = "line " . $i . " task no is empty!";
						} else {
							if ($last_taskno != $d[1]) {
								if (empty($d[3])) {
									$err[] = "line " . $i . " task schedule time is empty!";
									continue;
								}
								if (empty($d[4])) {
									$err[] = "line " . $i . " task courier is empty!";
									continue;
								}

								//Check available courier
								if(preg_match('/AUSPOST|AUPOST|AP|AU POST/i',$d[4])){
									if(empty($org->extra['auspost'])){
										$err[] = "line " . $i . " task courier ".$d[4]." is not available!";
										continue;
									}
								}
								else if(preg_match('/FASTWAY|FW/i',$d[4])){
									if(empty($org->extra['fastway'])){
										$err[] = "line " . $i . " task courier ".$d[4]." is not available!";
										continue;
									}
								}
								else if(preg_match('/TNT/i',$d[4])){
									if(empty($org->extra['tnt'])){
										$err[] = "line " . $i . " task courier ".$d[4]." is not available!";
										continue;
									}
								}
								else if(preg_match('/EIZ/i',$d[4])){
									if(empty($org->extra['eiztoll'])){
										$err[] = "line " . $i . " task courier ".$d[4]." is not available!";
										continue;
									}
								}
								else if(preg_match('/SF|SHUNFENG/i',$d[4])){
									if(empty($org->extra['sf'])){
										$err[] = "line " . $i . " task courier ".$d[4]." is not available!";
										continue;
									}
								}
								else if(preg_match('/UBI/i',$d[4])){
									if(empty($org->extra['ubitoll'])){
										$err[] = "line " . $i . " task courier ".$d[4]." is not available!";
										continue;
									}
								}
								else if(preg_match('/BOR/i',$d[4])){
									if(empty($org->extra['ubiborder'])){
										$err[] = "line " . $i . " task courier ".$d[4]." is not available!";
										continue;
									}
								}

								if ((preg_match('/^PCAE$/i', $d[4]) || preg_match('/^PCA Express$/i', $d[4])) && (empty($d[6]) || empty($d[7]) || empty($d[8]) || empty($d[9]) || empty($d[10]) || empty($d[11]) || empty($d[12]) || empty($d[13]))) {
									$err[] = "line " . $i . " consignee detail is not completed!";
									continue;
								}
								if (preg_match('/New South Wales/i', $d[12])) {
									$d[12] = 'NSW';
								} else if (preg_match('/Queensland/i', $d[12])) {
									$d[12] = 'QLD';
								} else if (preg_match('/South Australia/i', $d[12])) {
									$d[12] = 'SA';
								} else if (preg_match('/Tasmania/i', $d[12])) {
									$d[12] = 'TAS';
								} else if (preg_match('/Victoria/i', $d[12])) {
									$d[12] = 'VIC';
								} else if (preg_match('/Western Australia/i', $d[12])) {
									$d[12] = 'WA';
								} else if (preg_match('/Australia Capital Territory/i', $d[12])) {
									$d[12] = 'ACT';
								} else if (preg_match('/Northern Territory/i', $d[12])) {
									$d[12] = 'NT';
								}
								if (!preg_match('/[\x{4e00}-\x{9fa5}·\.]+/u', $d[6])) {
									if (!Postcode::validateAddress($d[10], $d[11], $d[12]) && $d[13] == 'AU') {
										$err[] = 'line ' . $i . ' suburb, state, postcode does not match';
									}
								}
								$last_taskno = $d[1];
								$tasks[$d[1]] = array(
									'ref' => $d[2],
									'sch_t' => oExcel::toDate($d[3]),
									'courier' => $d[4],
									'pack_ref' => $d[5],
									'cnee' => $d[6],
									'tel' => $d[7],
									'address' => $d[8],
									'city' => $d[9],
									'suburb' => $d[10],
									'state' => $d[11],
									'postcode' => $d[12],
									'country' => $d[13],
									'trackingno' => $d[20],
									'products' => []
								);
							}
							if (empty($d[15])) {
								$err[] = "line " . $i . " product barcode / sku is empty";
								continue;
							} else if (empty($d[16]) && empty($d[17])) {
								$err[] = "line " . $i . " product carton quantity and unit quantity are empty";
								continue;
							}
							$product = WmsProd::model()->find('ean = :ean', array(':ean' => $d[15]));
							if (empty($product)) {
								$product = WmsProd::model()->with(['orgs'])->together()->find('orgs.org_id = :oid AND sku = :sku', array(':oid' => $org_id, ':sku' => $d[15]));
								if (empty($product)) {
									$err[] = "line " . $i . " product barcode / sku is not found in the system!";
									continue;
								}
							}
							$condition = 'prod_id = :prod_id AND org_id = :org_id';
							$params = array(':prod_id' => $product->id, ':org_id' => $org_id);
							if ($d[18]) {
								$condition .= ' AND expiry = :expiry';
								$params[':expiry'] = oExcel::toDate($d[18]);
							}
							if ($d[19]) {
								$condition .= ' AND batch = :batch';
								$params[':batch'] = $d[19];
							}
	
							if (!empty($dpt_id)) {
								$condition .= ' AND dpt_id = :dpt_id';
								$params[':dpt_id'] = $dpt_id;
							}

							$stocks = WmsStock::model()->findAll($condition, $params);
							$sn = $product->name;
							if ($d[18]) {
								$sn .= ' (Exp: ' . oExcel::toDate($d[18]) . ')';
							}
							if ($d[19]) {
								$sn .= ' (Batch: ' . $d[19] . ')';
							}
							if (!empty($d[16])) {
								$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $product->id]);
								if (!empty($pp) && !empty($pp->qty)) {
									$remain_quantity = $d[16] * $pp->qty;
								}
							} else {
								$remain_quantity = $d[17];
							}
							foreach ($stocks as $stock) {
								$sub_quantity = $stock->qty - $stock->qty_res > $remain_quantity ? $remain_quantity : $stock->qty - $stock->qty_res;
								$remain_quantity -= $sub_quantity;
								$tasks[$d[1]]['products'][] = array(
									'si' => $stock->id,
									'sn' => $sn,
									'cq' => isset($pp->qty) ? $sub_quantity / $pp->qty : '',
									'uq' => $sub_quantity,
									'pq' => '',
									'pl' => '',
									'nt' => '',
								);
								if (!$remain_quantity) {
									break;
								}

							}
							if (isset($pp->qty)) {
								unset($pp->qty);
							}

							if ($remain_quantity) {
								$err[] = "line " . $i . " product not enough stock in the system";
								continue;
							}
							if (!empty($d[4])) {
								$task_type = array_search('Pick Carton', WmsTask::$types);
							} else {
								$task_type = array_search('Pick Unit', WmsTask::$types);
							}
						}
					}

					if (!empty($err)) {
						$resp['msg'] = implode('<br />', $err);
						$resp['done'] = false;
						echo json_encode($resp);
						return;
					}

					foreach ($tasks as $temp_task) {
						if (empty($temp_task)) {
							continue;
						}

						// ausriver task ref duplicate
						$wmstask = WmsTask::model()->with('job')->find('t.ref = :ref AND job.org_id = :oid', array(':ref' => $temp_task['ref'], ':oid' => Yii::app()->user->org));
						if (!empty($wmstask)) {
							$resp['done'] = false;
							$resp['msg'] .= '<br />' . $temp_task['ref'] . ' has been uploaded before';
							continue;
						}

						$task = new WmsTask();
						$task->job_id = $id;
						$task->ref = $temp_task['ref'];
						$task->status = array_search('Scheduled', WmsTask::$states);
						$task->is_request = 1;
						$task->type = $task_type;
						$task->schd_time = $temp_task['sch_t'];
						$task->dpt_id = $dpt_id;
						

						foreach ($temp_task['products'] as $t) {
							$item = $t;
							if (empty($t['uq']) && !empty($t['cq'])) {
								$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $product->id]);
								if (!empty($pp) && !empty($pp->qty)) {
									$item['uq'] = intval($t['cq']) * $pp->qty;
								}
							}
							$task->new_items[] = $item;
						}

						if (!$task->save()) {
							$this->ajaxResult($task);
						}

						if ($temp_task['courier'] != 'Pickup') {
							$sub_task = WmsTask::model()->find('link_id = :link_id and type = :type', array(':link_id' => $task->id, ':type' => array_search('Pickup', WmsTask::$types)));
							$sub_task->type = array_search('Delivery', WmsTask::$types);

							$select_courier = "0";
							$sub_task->mdata['courier'] = $select_courier;
							$sub_task->mdata['cnee']['company'] = "";
							$sub_task->mdata['cnee']['name'] = $temp_task['cnee'];
							$sub_task->mdata['cnee']['tel'] = $temp_task['tel'];
							$sub_task->mdata['cnee']['address'] = $temp_task['address'];
							$sub_task->mdata['cnee']['city'] = $temp_task['city'];
							$sub_task->mdata['cnee']['suburb'] = $temp_task['suburb'];
							$sub_task->mdata['cnee']['state'] = $temp_task['state'];
							$sub_task->mdata['cnee']['postcode'] = $temp_task['postcode'];
							$sub_task->mdata['cnee']['country'] = preg_match('/^australia$|^Au$|^AUS$/i', $temp_task['country']) ? 'AU' : 'CN';
							$sub_task->mdata['cnee']['email'] = '';
							$sub_task->mdata['trackingno']=$temp_task['trackingno'];
							$sub_task->save();

							//Author:Nero Date:2021/6/22 Description:choose courier
							$customer_select_courier="";

							//$numWeight = $sub_task->getItemsWeights();

							if(preg_match('/AUTO/i',$temp_task['courier'])){
								$sub_task->chooseCourier();
							}
							if(preg_match('/TLA|TAL/i',$temp_task['courier'])){
								if(!empty($org->extra['tla'])){
									$customer_select_courier = Org::ORGID_COURIER_TLA;
								}
							}
							else if(preg_match('/AUSPOST|AUPOST|AP|AU POST/i',$temp_task['courier'])){
								if(!empty($org->extra['auspost'])){
									$customer_select_courier = Org::ORGID_COURIER_AUPOST;
									//if(pickup){$customer_select_courier = "0";}
								}
							}
							else if(preg_match('/FASTWAY|FW/i',$temp_task['courier'])){
								if(!empty($org->extra['fastway'])){
									$customer_select_courier = Org::ORGID_COURIER_FASTWAY;
								}
							}
							else if(preg_match('/TNT/i',$temp_task['courier'])){
								if(!empty($org->extra['tnt'])){
									$customer_select_courier = Org::ORGID_COURIER_TNT;
								}
							}
							else if(preg_match('/EIZTOLL/i',$temp_task['courier'])){
								if(!empty($org->extra['eiztoll'])){
									$customer_select_courier = Org::ORGID_COURIER_EIZ;
								}
							}
							else if(preg_match('/SF|SHUNFENG/i',$temp_task['courier'])){
								if(!empty($org->extra['sf'])){
									$customer_select_courier = Org::ORGID_COURIER_SF;
								}
							}
							else if(preg_match('/ALLIED/i',$temp_task['courier'])){
								if(!empty($org->extra['allied'])){
									$customer_select_courier = "3702";
								}
							}
							else if(preg_match('/UBITOLL/i',$temp_task['courier'])){
								if(!empty($org->extra['ubitoll'])){
									$customer_select_courier = "3079";
								}
							}
							else if(preg_match('/BORDER/i',$temp_task['courier'])){
								if(!empty($org->extra['ubiborder'])){
									$customer_select_courier = "3447";
								}
							}
							else{
								$customer_select_courier = "0";
							}

							if(!empty($customer_select_courier)){
								$sub_task->mdata['courier'] = $customer_select_courier;
								$sub_task->save();
							}

						}

						if ($temp_task['pack_ref']) {
							$sub_task = WmsTask::model()->find('link_id = :link_id and type = :type', array(':link_id' => $task->id, ':type' => array_search('Pack Order', WmsTask::$types)));
							$sub_task->ref = $temp_task['pack_ref'];
							$sub_task->save();
						}
					}
					echo json_encode($resp);
				}
			} else if (!empty($_FILES['container_excel'])) {
				$xls = new oExcel;
				$err = [];
				if (!$xls->supported($_FILES['container_excel']['name'])) {
					foreach ($xls->getError() as $e) {
						$err[] = $e;
					}
				} else {
					$xls->load($_FILES['container_excel']['tmp_name']);
					$data = $xls->getAll();
					if (implode('', $data[1]) != "Task no.Task RefSchedule TimeDue Time / EDTContainer no.Container SizeContainer Seal #Product NameBarcode / SkuCarton QtyUnit QtyExpiryBatchNote") {
						$err[] = "template wrong";
					}

					if (!empty($err)) {
						$resp['msg'] = implode(';', $err);
						$resp['done'] = false;
						echo json_encode($resp);
						return;
					}
					unset($data[1]);
					$tasks = [];

					$last_taskno = 0;
					$products = [];
					foreach ($data as $i => $d) {
						if (empty($d[1]) && empty($d[2])) {
							continue;
						}

						if (empty($d[1])) {
							$err[] = "line " . $i . " task no is empty!";
						} else {
							if ($last_taskno != $d[1]) {
								if (empty($d[3])) {
									$err[] = "line " . $i . " task schedule time is empty!";
									continue;
								} else if (empty($d[4])) {
									$err[] = "line " . $i . " task due time / EDT is empty!";
									continue;
								}
								$last_taskno = $d[1];
								$tasks[$d[1]] = array(
									'ref' => $d[2],
									'sch_t' => oExcel::toDate($d[3]),
									'due_t' => oExcel::toDate($d[4]),
									'ctn_no' => $d[5],
									'ctn_size' => $d[6],
									'ctn_seal' => $d[7],
									'products' => []
								);
							}
							if (empty($d[9])) {
								$err[] = "line " . $i . " product barcode / sku is empty!";
								continue;
							} else if (empty($d[11]) && empty($d[12])) {
								$err[] = "line " . $i . " product carton quantity and unit quantity are empty!";
								continue;
							}
							$product = WmsProd::model()->find('ean = :ean', array(':ean' => $d[9]));
							if (empty($product)) {
								$product = WmsProd::model()->with(['orgs'])->together()->find('orgs.org_id = :oid AND sku = :sku', array(':oid' => $org_id, ':sku' => $d[9]));
								if (empty($product)) {
									$err[] = "line " . $i . " product barcode / sku is not found in the system!";
									continue;
								}
							}
							$tasks[$d[1]]['products'][] = array(
								'gi' => $product->id,
								'gn' => $product->name,
								'cq' => $d[10],
								'uq' => $d[11],
								'ex' => $d[12],
								'bn' => $d[13],
								'nt' => $d[14],
							);
						}
					}

					if (!empty($err)) {
						$resp['msg'] = implode('<br />', $err);
						$resp['done'] = false;
						echo json_encode($resp);
						return;
					}

					foreach ($tasks as $temp_task) {
						if (empty($temp_task)) {
							continue;
						}

						// ausriver task ref duplicate
						$wmstask = WmsTask::model()->with('job')->find('t.ref = :ref AND job.org_id = :oid', array(':ref' => $temp_task['ref'], ':oid' => Yii::app()->user->org));
						if (!empty($wmstask)) {
							$resp['done'] = false;
							$resp['msg'] .= '<br />' . $temp_task['ref'] . ' has been uploaded before';
							continue;
						}

						$task = new WmsTask();
						$task->job_id = $id;
						$task->ref = $temp_task['ref'];
						$task->status = array_search('Scheduled', WmsTask::$states);
						$task->is_request = 1;
						$task->type = array_search('Container Unload', WmsTask::$types);
						$task->schd_time = $temp_task['sch_t'];
						$task->due_time = $temp_task['due_t'];
						$task->mdata['ctn_no'] = $temp_task['ctn_no'];
						$task->mdata['ctn_size'] = $temp_task['ctn_size'];
						$task->mdata['ctn_seal'] = $temp_task['ctn_seal'];
						$task->dpt_id = $dpt_id;

						foreach ($temp_task['products'] as $t) {
							$item = $t;
							$item['pli'] = '';
							$item['pl'] = '';
							if (empty($t['uq']) && !empty($t['cq'])) {
								$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $product->id]);
								if (!empty($pp) && !empty($pp->qty)) {
									$item['uq'] = intval($t['cq']) * $pp->qty;
								}
							}
							if (!empty($t['ex'])) {
								$exp = WmsStock::parseExpiry($t['ex']);
								$item['ex'] = $exp;
							}
							$task->new_items[] = $item;
						}

						if (!$task->save()) {
							$this->ajaxResult($task);
						}
					}
					echo json_encode($resp);
				}
			} else if (!empty($_FILES['quickpick_excel'])) {
				//import quickpick tasks
				$xls = new oExcel;
				$err = [];
				if (!$xls->supported($_FILES['quickpick_excel']['name'])) {
					foreach ($xls->getError() as $e) {
						$err[] = $e;
					}
				} else {
					$xls->load($_FILES['quickpick_excel']['tmp_name']);
					$xls->goSheet(0);
					$data = $xls->getAll();
					if (strtolower(implode('', $data[1])) != strtolower("Task no.Task RefCourierCneeTelAddressCitySuburbStatePostcodeCountryWeight")) {
						$err[] = "template wrong";
					}

					if (!empty($err)) {
						$resp['msg'] = implode(';', $err);
						$resp['done'] = false;
						echo json_encode($resp);
						return;
					}
					unset($data[1]);
					$tasks = [];

					$last_taskno = 0;
					foreach ($data as $i => $d) {
						if ($d[1] == "Instruction") {
							break;
						}

						if (empty($d[1]) && empty($d[3]) && empty($d[4]) && empty($d[12])) {
							continue;
						}

						if (empty($d[1])) {
							$err[] = "line " . $i . " task no is empty!";
						} else {
							if ($last_taskno != $d[1]) {
								if (empty($d[3])) {
									$err[] = "line " . $i . " task courier is empty!";
									continue;
								}
								if (preg_match('/New South Wales/i', $d[9])) {
									$d[9] = 'NSW';
								} else if (preg_match('/Queensland/i', $d[9])) {
									$d[9] = 'QLD';
								} else if (preg_match('/South Australia/i', $d[9])) {
									$d[9] = 'SA';
								} else if (preg_match('/Tasmania/i', $d[9])) {
									$d[9] = 'TAS';
								} else if (preg_match('/Victoria/i', $d[9])) {
									$d[9] = 'VIC';
								} else if (preg_match('/Western Australia/i', $d[9])) {
									$d[9] = 'WA';
								} else if (preg_match('/Australia Capital Territory/i', $d[9])) {
									$d[9] = 'ACT';
								} else if (preg_match('/Northern Territory/i', $d[9])) {
									$d[9] = 'NT';
								}
								if (!preg_match('/[\x{4e00}-\x{9fa5}·\.]+/u', $d[4]) && !$d[11] == 'New Zealand') {
									if (!Postcode::validateAddress($d[8], $d[9], $d[10])) {
										$err[] = 'line ' . $i . ' suburb, state, postcode does not match';
									}
								}
								$last_taskno = $d[1];
								$tasks[$d[1]] = array(
									'ref' => $d[2],
									'courier' => $d[3],
									'cnee' => $d[4],
									'tel' => $d[5],
									'address' => $d[6],
									'city' => $d[7],
									'suburb' => $d[8],
									'state' => $d[9],
									'postcode' => $d[10],
									'country' => $d[11],
									'weight' => $d[12],
								);
							}
							$task_type = array_search('Pick Unit', WmsTask::$types);
						}
					}

					if (!empty($err)) {
						$resp['msg'] = implode('<br />', $err);
						$resp['done'] = false;
						echo json_encode($resp);
						return;
					}

					foreach ($tasks as $temp_task) {
						if (empty($temp_task)) {
							continue;
						}

						// ausriver task ref duplicate
						$wmstask = WmsTask::model()->with('job')->find('t.ref = :ref AND job.org_id = :oid', array(':ref' => $temp_task['ref'], ':oid' => Yii::app()->user->org));
						if (!empty($wmstask)) {
							$resp['done'] = false;
							$resp['msg'] .= '<br />' . $temp_task['ref'] . ' has been uploaded before';
							continue;
						}

						$task = new WmsTask();
						$task->job_id = $id;
						$task->ref = $temp_task['ref'];
						$task->status = WmsTask::STATUS_HOLD;
						$task->is_request = 1;
						$task->type = $task_type;
						$task->dpt_id = $dpt_id;

						if (!$task->save()) {
							$this->ajaxResult($task);
						}

						if ($temp_task['courier'] != 'Pickup') {
							$sub_task = WmsTask::model()->find('link_id = :link_id and type = :type', array(':link_id' => $task->id, ':type' => array_search('Pickup', WmsTask::$types)));
							$sub_task->type = array_search('Delivery', WmsTask::$types);

							$select_courier = '114';
							$sub_task->mdata['courier'] = $select_courier;
							$sub_task->mdata['cnee']['company'] = '';
							$sub_task->mdata['cnee']['name'] = $temp_task['cnee'];
							$sub_task->mdata['cnee']['tel'] = $temp_task['tel'];
							$sub_task->mdata['cnee']['address'] = $temp_task['address'];
							$sub_task->mdata['cnee']['city'] = $temp_task['city'];
							$sub_task->mdata['cnee']['suburb'] = $temp_task['suburb'];
							$sub_task->mdata['cnee']['state'] = $temp_task['state'];
							$sub_task->mdata['cnee']['postcode'] = $temp_task['postcode'];
							$sub_task->mdata['cnee']['country'] = preg_match('/^australia$|^Au$|^AUS$/i', $temp_task['country']) ? 'AU' : 'CN';
							$sub_task->mdata['cnee']['email'] = '';
							$sub_task->save();

							if (preg_match('/^AUPOST$|^AUSPOST$/i', $temp_task['courier'])) {
								$select_courier = Org::ORGID_COURIER_AUPOST;
								$sub_task->mdata['courier'] = $select_courier;
								$sub_task->save();
							} else if (preg_match('/^FASTWAY$/i', $temp_task['courier'])) {
								$select_courier = Org::ORGID_COURIER_FASTWAY;
								$sub_task->mdata['courier'] = $select_courier;
								$sub_task->save();
							} else if (!preg_match('/^PCAE$/i', $temp_task['courier']) || !preg_match('/^PCA Express$/i', $temp_task['courier'])) {
								$sub_task->ref = $temp_task['courier'];
								$sub_task->save();
							}
						}

						if ($temp_task['weight']) {
							$sub_task = WmsTask::model()->find('link_id = :link_id and type = :type', array(':link_id' => $task->id, ':type' => array_search('Pack Order', WmsTask::$types)));

							$meta = json_decode($sub_task->meta, true);
							if ($meta && array_key_exists('pkg', $meta)) {
								$pkg = json_decode($meta['pkg'], true);
							} else {
								$pkg = array();
							}
							$parcelId = 'P' . sprintf('%06d', $task->id) . sprintf('%03d', count($pkg) + 1);
							$pkg[] = array('wt' => $temp_task['weight'], 'w' => '', 'h' => '', 'd' => '', 'nt' => '是 ' . date('Y-m-d H:i:s', time()) . ' ' . $parcelId);
							$meta['pkg'] = json_encode($pkg);
							$sub_task->mdata = $meta;

							$sub_task->save();
						}
					}
					echo json_encode($resp);
				}
			}
		}
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

	public function actionBillingGrid($id)
	{
		$model = WmsTask::model()->findByPk($id);
		if (empty($_POST['BillingLine']['charge_code'])) {
			$model->addError('id', 'Charge code is required');
		} else if (empty($_POST['BillingLine']['gst'])) {
			$model->addError('id', 'GST type is required');
		}
		if (!empty($model->getErrors())) {
			$this->ajaxResult($model);
		}
		if (empty($_POST['BillingLine']['id'])) {
			[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
			$line = new BillingLine;
			if($_POST['BillingLine']['charge_code'] > 91020){
				$line->type = BillingLine::BILLING_TYPE_3PL;
				$line->dpmt = Invoice::DPMT_3PL;
			}else{
				$line->type = BillingLine::BILLING_TYPE_AIR_SEA;
				$line->dpmt = Invoice::DPMT_AIRSEA;
			}
			$line->op_id = Yii::app()->user->id;
			$line->date = date('Y-m-d');
			$line->created = date('Y-m-d');
			$line->transaction_date = date('Y-m-d');
			$line->due = date('Y-m-d');
			$line->billing_ref = $model->getNo();
			$line->price = $_POST['BillingLine']['accrual_amount'];
			$line->qty = 1;
			$line->dpt_id = 106;
			$line->attributes = $_POST['BillingLine'];
			$line->gst_amount = $line->getGSTValue();
			$line->save();

			if (!in_array($line->org_id, [1133])) {
				Billing::linkLine($line, false);
			}
			Yii::app()->name = $app_name;
		} else {
			[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
			$line = BillingLine::model()->findByPk($_POST['BillingLine']['id']);
			unset($_POST['BillingLine']['id']);
			$line->attributes = $_POST['BillingLine'];
			$line->gst_amount = $line->getGSTValue();
			$line->save();
			if (empty($line->billing_id) || $line->billing->billing_cref != $line->billing_cref) {
				if (!in_array($line->org_id, [1133])) {
					Billing::linkLine($line, false);
				}
			} else if (empty($line->billing->billing_cref)) {
				$line->billing->billing_cref = $line->billing_cref;
				$line->billing->update('billing_cref');
			}
			Yii::app()->name = $app_name;
		}
		$this->ajaxResult($line);
	}

	public function actionBillingGridDelete($id)
	{
		[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
		$line = BillingLine::model()->findByPk($id);
		$line->status = 11;
		$line->save();
		Yii::app()->name = $app_name;
	}

	/**
	 * Lists and search.
	 */
	public function actionList()
	{
		$model = new WmsTask('search');
		$model->unsetAttributes(); // clear any default values
		if (isset($_GET['WmsTask'])) {
			$model->attributes = $_GET['WmsTask'];
		}

		$this->render('list', array(
			'model' => $model,
		));
	}

	public function actionNotes($id)
	{
		$model = $this->loadModel($id);
		if (!empty($_POST['notes'])) {
			$log = Log::add($model, 6, array('notes' => $_POST['notes']));
			if ($model->type == 6020) {
				$model->mainTask->updateMeta();
			}
			$this->ajaxResult($log);
		}
	}

	public function actionExport()
	{
		if (!empty($_GET['id'])) {
			$model = $this->loadModel($_GET['id']);
		}
		switch ($_GET['t']) {
			case 'rpt_reaco':
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth(array(30, 15, 15, 15, 15, 10, 10, 15, 10, 15, 10, 15, 10, 10, 30));
				$xls->addRow($i++, array('Comparison Job #' . $model->job->no, $model->getNo()));
				$xls->addRow($i++, array('Date: ' . date('Y-m-d')));
				$xls->addRow($i++, array('Prod. Name', 'Prod. Brand', 'Prod. Model', 'Prod. EAN', 'Expiry Date', 'Batch', 'Request Cartons', 'Request Unit', 'Actual Cartons', 'Actual Unit', 'Carton Diff', 'Unit Diff', 'Pallets', 'Mixed Pallets', 'Notes', 'Detailed Pallet Codes'));
				$rows = [];
				$eb = empty($_GET['eb']) ? 0 : $_GET['eb'];
				foreach ($model->items as $k => $itm) {
					if (empty($itm->mdata['cq']) && empty($itm->mdata['uq'])) {
						continue;
					}

					if (!empty($itm->mdata['si'])) {
						$stock = WmsStock::model()->findByPk($itm->mdata['si']);
					} else {
						$stock = WmsStock::creget($model->job->org_id, $itm->mdata);
					}

					if(!empty($itm->mdata['uq']) && empty($itm->mdata['cq'])){
						$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $stock->prod_id]);
						if(!empty($pp) && $pp->qty > 0 && (intval($itm->mdata['uq']) % $pp->qty == 0)){
							$itm->mdata['cq'] = intval($itm->mdata['uq']) / $pp->qty;
						}
					}

					$seb = $stock->getStockEB($eb);
					if (empty($rows[$seb])) {
						$rows[$seb] = [$stock->prod->name, $stock->prod->brand, $stock->prod->model, '="' . $stock->prod->ean . '"', $stock->expiry, $stock->batch, floatval(trim($itm->mdata['cq'])), floatval(trim($itm->mdata['uq'])), 0, 0, 0, 0, 0, 0, ''];
					} else {
						$rows[$seb][6] += floatval(trim($itm->mdata['cq']));
						$rows[$seb][7] += floatval(trim($itm->mdata['uq']));
					}
				}

				$plts = [];
				$mplts = [];
				foreach ($model->actionTask->items as $k => $itm) {
					if (empty($itm->mdata['cq']) && empty($itm->mdata['uq']) && empty($itm->stockLedgers)) {
						continue;
					}

					if (empty($itm->mdata['uq'])) {
						$itm->mdata['uq'] = 0;
						foreach ($itm->stockLedgers as $ledger) {
							if ($ledger->location_id > WmsLocation::WMS_LOCATION_MAX_MAGIC_ID) {
								$itm->mdata['uq'] += intval($ledger->qty_out);
							}
						}
					}

					if (!empty($itm->mdata['si'])) {
						$stock = WmsStock::model()->findByPk($itm->mdata['si']);
					} else {
						$stock = WmsStock::creget($model->job->org_id, $itm->mdata);
					}

					if(!empty($itm->mdata['uq']) && empty($itm->mdata['cq'])){
						$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $stock->prod_id]);
						if(!empty($pp) && $pp->qty > 0 && (intval($itm->mdata['uq']) % $pp->qty == 0)){
							$itm->mdata['cq'] = intval($itm->mdata['uq']) / $pp->qty;
						}
					}
					
					if ($eb == 9) {
						$seb = $stock->org_id . '-' . $stock->prod_id;
					} else {
						$seb = $stock->org_id . '-' . $stock->prod_id . $itm->mdata['ex'] . $stock->batch;
					}
					if (empty($rows[$seb])) {
						$rows[$seb] = [$stock->prod->name, $stock->prod->brand, $stock->prod->model, '="' . $stock->prod->ean . '"', $itm->mdata['ex'], $stock->batch, 0, 0, floatval(trim($itm->mdata['cq'])), floatval(trim($itm->mdata['uq'])), 0, 0, 1, 0, $itm->mdata['nt']];
					} else {
						$rows[$seb][8] += floatval(trim($itm->mdata['cq']));
						$rows[$seb][9] += floatval(trim($itm->mdata['uq']));
						$rows[$seb][14] .= $itm->mdata['nt'];
					}
					if (!isset($plts[$seb])) {
						$plts[$seb] = [];
					}

					$plts[$seb][] = $itm->mdata['pl'];
					if (WmsStockLocation::isMixedPallet($itm->mdata['pl'])) {
						if (!isset($plts[$seb])) {
							$mplts[$seb] = [];
						}

						$mplts[$seb][] = $itm->mdata['pl'];
					}
				}

				function md_array_unique($a) {
					$ra = [];
					foreach ($a as $k => $v) {
						if (is_array($v)) {
							$ra = array_merge($ra, md_array_unique($v));
						} else {
							$ra[] = $v;
						}
					}
					return array_unique($ra);
				};
				ksort($rows);
				foreach ($rows as $seb => $r) {
					$r[10] = '=I' . $i . '-G' . $i;
					$r[11] = '=J' . $i . '-H' . $i;
					$r[12] = empty($plts[$seb]) ? 0 : sizeof(array_unique($plts[$seb]));
					$r[13] = empty($mplts[$seb]) ? 0 : sizeof(array_unique($mplts[$seb]));
					$r[14] = '';
					$r[15] = empty($plts[$seb]) ? '' : implode(',', $plts[$seb]);
					$xls->addRow($i++, $r);
				}
				$li = $i - 1;
				$xls->addRow($i++, ['', '', '', '', '', '', '=SUM(G4:G' . $li . ')', '=SUM(H4:H' . $li . ')', '=SUM(I4:I' . $li . ')', '=SUM(J4:J' . $li . ')', '=SUM(K4:K' . $li . ')', '=SUM(L4:L' . $li . ')', sizeof(md_array_unique($plts)), sizeof(md_array_unique($mplts))]);
				$xls->output('comparison_' . $model->job->no . '-' . $model->getNo() . '.xlsx');
				break;
			case 'rpt_gtin':
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth(array(15, 30, 15, 15, 15, 15, 10, 15, 15, 30));
				$xls->addRow($i++, array($model->getType() . ' Job #' . $model->job->no, $model->getNo()));
				$xls->addRow($i++, array('Date: ' . date('Y-m-d')));
				$xls->addRow($i++, array('Pallet', 'Prod. Name', 'Prod. Brand', 'Prod. Model', 'Prod. EAN', 'Expiry Date', 'Batch', 'Cartons', 'Unit', 'Notes'));
				$rows = [];
				$plt = [];
				foreach ($model->actionTask->items as $k => $itm) {
					if (empty($itm->mdata['cq']) && empty($itm->mdata['uq'])) {
						continue;
					}

					if (!empty($itm->mdata['si'])) {
						$stock = WmsStock::model()->findByPk($itm->mdata['si']);
					} else {
						$stock = WmsStock::creget($model->job->org_id, $itm->mdata);
					}
					$seb = $stock->org_id . '-' . $stock->prod_id . $itm->mdata['ex'] . $stock->batch;
					if (empty($rows[$seb])) {
						$rows[$seb] = [$itm->mdata['pl'], $stock->prod->name, $stock->prod->brand, $stock->prod->model, '="' . $stock->prod->ean . '"', $itm->mdata['ex'], $stock->batch, floatval(trim($itm->mdata['cq'])), floatval(trim($itm->mdata['uq'])), $itm->mdata['nt']];
					} else {
						$rows[$seb][7] += floatval(trim($itm->mdata['cq']));
						$rows[$seb][8] += floatval(trim($itm->mdata['uq']));
						$rows[$seb][9] .= $itm->mdata['nt'];
					}
					$plt[] = $itm->mdata['pl'];
				}

				foreach ($rows as $r) {
					$xls->addRow($i++, $r);
				}
				$li = $i - 1;
				$xls->addRow($i++, [sizeof(array_unique($plt)), '', '', '', '', '', '', '=SUM(H4:H' . $li . ')', '=SUM(I4:I' . $li . ')']);
				$xls->output('gatein_' . $model->job->no . '-' . $model->getNo() . '.xlsx');
				break;
			case 'rpt_ctnpack':
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth(array(15, 30, 15, 15, 15, 15, 15, 15, 10, 15, 15, 15, 20, 30));
				$xls->addRow($i++, array($model->getType(), 'Job #' . $model->job->no, $model->getNo()));
				$xls->addRow($i++, array('Date: ' . date('Y-m-d'), 'Container: ' . @$model->mdata['ctn_no'], 'Type: ' . @$model->mdata['ctn_size'], 'Seal: ' . @$model->mdata['ctn_seal']));
				$xls->addRow($i++, array('Pallet', 'Prod. Name', 'Prod. Brand', 'Prod. Model', 'Prod. Outer', 'Prod. EAN', 'Prod. SKU/PLU', 'Expiry Date', 'Mfr. Date', 'Batch', 'Cartons', 'Unit', 'Weight', 'Load Time', 'Notes'));
				$rows = [];
				$plt = [];
				foreach ($model->actionTask->items as $k => $itm) {
					if (empty($itm->mdata['pli']) || $itm->del == 1) {
						continue;
					}

					if (!empty($itm->mdata['pl'])) {
						$pallet = WmsLocation::model()->find('name = :name', [':name' => $itm->mdata['pl']]);
					} else if (!empty($itm->mdata['pli'])) {
						$pallet = WmsLocation::model()->findByPk($itm->mdata['pli']);
					}

					$rs = WmsStockLocation::model()->findAll('location_id = :pli', [':pli' => $itm->mdata['pli']]);
					foreach ($rs as $j => $r) {
						$wsl = WmsStockLedger::model()->with('taskItem')->find('t.ti_id = :tid AND t.location_id = :lid AND t.stock_id = :sid AND t.l2_id = 5 AND t.qty_out > 0 AND taskItem.del = 0', [':tid' => $itm->id, ':lid' => $r->location_id, ':sid' => $r->stock_id]);
						$uq = $wsl->qty_out;
						if (empty($uq)) {
							continue;
						}

						//not belong to this org
						if ($wsl->stock->org_id != $model->job->org_id) {
							continue;
						}

						//check if Inward stock is there
						$sql = 'SELECT SUM(qty_in) FROM wms_stock_ledger WHERE ti_id > 0 AND qty_in > 0 AND location_id = ' . $r->location_id . ' AND stock_id =' . $r->stock_id;
						$tq = Yii::app()->db->createCommand($sql)->queryScalar();

						if (empty($tq)) {
							continue;
						}

						if ($tq != $uq) {
							$wsl->qty_out = $tq;
							$wsl->save();
							WmsStock::countAll($wsl->stock_id);
							$uq = $tq;
						}
						$cq = $r->stock->prod->uq2cq(intval($uq));
						$prod_pack = WmsProdPack::model()->find("prod_id = :prod_id and type = :type", array(":prod_id" => $r->stock->prod->id, ":type" => array_search("Carton", WmsProdPack::$types)));
						if ($r->stock->prod->name != 'MISC PALLET') {
							$weight = $r->stock->prod->getCqWeight($cq, $uq) + ($j == 0 ? 10 : 0);
						} else {
							$weight = @$pallet->extra['weight'];
						}
						$sku = '';
						if (!empty($r->stock->prod->orgs)) {
							foreach ($r->stock->prod->orgs as $org) {
								if ($org->org_id == $model->job->org_id) {
									$sku = $org->sku;
								}
							}
						}

						$rows[] = [$pallet->name, $r->stock->prod->name, $r->stock->prod->brand, $r->stock->prod->model, '="' . @$prod_pack->barcode . '"', '="' . $r->stock->prod->ean . '"', '="' . $sku . '"', $r->stock->expiry, $r->getMfr(), $r->stock->batch, $cq, $uq, $weight, $itm->ts, $itm->mdata['nt']];
					}
					$plt[] = $pallet->name;
				}

				foreach ($rows as $r) {
					$xls->addRow($i++, $r);
				}
				$li = $i - 1;
				$xls->addRow($i++, [sizeof(array_unique($plt)), '', '', '', '', '', '', '', '', '=SUM(J4:J' . $li . ')', '=SUM(K4:K' . $li . ')', '=SUM(L4:L' . $li . ')']);
				$xls->output('container_packing_' . preg_replace('/\s+/', '_', trim(@$model->mdata['ctn_no'])) . '-' . $model->getNo() . '.xlsx');
				break;
			case 'plt_lbl':
				$t = $model->actionTask;
				$cs = [];
				foreach ($t->items as $ti) {
					$cs[] = $ti->mdata['pl'];
				}
				oPDF::renderPDF('label_plt', array('cs' => $cs, 'dup' => true));
				break;
			case 'rpt_pltpack':
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth(array(15, 30, 15, 15, 15, 15, 15, 10, 15, 15, 15, 20, 30, 15, 15, 15, 15, 15));
				$xls->addRow($i++, array($model->getType(), 'Job #' . $model->job->no, $model->getNo()));
				$xls->addRow($i++, array('Date: ' . date('Y-m-d')));
				$xls->addRow($i++, array('Pallet', 'Prod. Name', 'Prod. Brand', 'Prod. Model', 'Prod. Outer', 'Prod. EAN', 'Expiry Date', 'Batch', 'Cartons', 'Unit', 'System Weight', 'Load Time', 'Notes', 'Length', 'Width', 'Height', 'Weight', 'Pallet Type'));
				$rows = [];
				$plt = [];
				$showed = [];
				foreach ($model->actionTask->items as $k => $itm) {
					if (empty($itm->mdata['pl'])) {
						continue;
					}

					$rs = WmsStockLocation::model()->with('loc')->findAll('loc.code = :pl', [':pl' => $itm->mdata['pl']]);
					foreach ($rs as $j => $r) {
						$wsl = WmsStockLedger::model()->with('taskItem')->find('t.ti_id = :tid AND t.location_id = :lid AND t.stock_id = :sid AND t.qty_in > 0 AND taskItem.del = 0', [':tid' => $itm->id, ':lid' => $r->location_id, ':sid' => $r->stock_id]);
						$uq = $wsl->qty_in;
						if (empty($uq)) {
							continue;
						}

						//not belong to this org
						if ($wsl->stock->org_id != $model->job->org_id) {
							continue;
						}

						//check if Inward stock is there
						$sql = 'SELECT SUM(qty_in) FROM wms_stock_ledger WHERE qty_in > 0 AND location_id = ' . $r->location_id . ' AND stock_id =' . $r->stock_id;
						$tq = Yii::app()->db->createCommand($sql)->queryScalar();

						if (empty($tq)) {
							continue;
						}

						if ($tq < $uq) {
							$wsl->qty_in = $tq;
							$wsl->save();
							$uq = $tq;
						}
						$cq = $r->stock->prod->uq2cq($uq);
						$pallet = WmsLocation::model()->find('code = :code', array(':code' => $itm->mdata['pl']));
						if (in_array($itm->mdata['pl'], $showed)) {
							$pallet->extra = [];
						} else {
							$showed[] = $itm->mdata['pl'];
						}
						$prod_pack = WmsProdPack::model()->find("prod_id = :prod_id and type = :type", array(":prod_id" => $r->stock->prod->id, ":type" => array_search("Carton", WmsProdPack::$types)));
						$rows[] = [$itm->mdata['pl'], $r->stock->prod->name, $r->stock->prod->brand, $r->stock->prod->model, '="' . @$prod_pack->barcode . '"', '="' . $r->stock->prod->ean . '"', $itm->mdata['ex'], $r->stock->batch, $cq, $uq, ($r->stock->prod->getCqWeight($cq, $uq) + ($j == 0 ? 10 : 0)), $itm->ts, $itm->mdata['nt'], @$pallet->extra['length'], @$pallet->extra['width'], @$pallet->extra['height'], @$pallet->extra['weight'], (!empty($pallet->extra['plastic']) ? '塑料板' : (!empty($pallet->extra['chep']) ? '熏蒸板' : ''))];
					}
					$plt[] = $itm->mdata['pl'];
				}

				foreach ($rows as $r) {
					$xls->addRow($i++, $r);
				}
				$li = $i - 1;
				$xls->addRow($i++, [sizeof(array_unique($plt)), '', '', '', '', '', '', '', '=SUM(I4:I' . $li . ')', '=SUM(J4:J' . $li . ')', '=SUM(K4:K' . $li . ')']);
				$xls->output('pallet_weight_' . '-' . $model->getNo() . '.xlsx');
				break;
			case 'rpt_pltpack_wangyi':
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth(array(30, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 20, 30));
				$xls->addRow($i++, array($model->getType(), 'Job #' . $model->job->no, $model->getNo()));
				$xls->addRow($i++, array('Date: ' . date('Y-m-d')));
				$xls->addRow($i++, array('Prod. Name', 'Prod. EAN', 'Prod. Outer', 'Batch', 'Pallet', 'Cartons', 'Unit', 'Expiry Date', 'Length', 'Width', 'Height', 'Weight', 'Pallet Type', 'System Weight', 'Load Time', 'Notes'));
				$rows = [];
				$plt = [];
				$showed = [];
				foreach ($model->actionTask->items as $k => $itm) {
					if (empty($itm->mdata['pl'])) {
						continue;
					}

					$rs = WmsStockLocation::model()->with('loc')->findAll('loc.code = :pl', [':pl' => $itm->mdata['pl']]);
					foreach ($rs as $j => $r) {
						$wsl = WmsStockLedger::model()->find('ti_id = :tid AND location_id = :lid AND stock_id = :sid AND l2_id = 1 AND qty_in > 0', [':tid' => $itm->id, ':lid' => $r->location_id, ':sid' => $r->stock_id]);
						$uq = $wsl->qty_in;
						if (empty($uq)) {
							continue;
						}

						//not belong to this org
						if ($wsl->stock->org_id != $model->job->org_id) {
							continue;
						}

						//check if Inward stock is there
						$sql = 'SELECT SUM(qty_in) FROM wms_stock_ledger WHERE qty_in > 0 AND location_id = ' . $r->location_id . ' AND stock_id =' . $r->stock_id;
						$tq = Yii::app()->db->createCommand($sql)->queryScalar();

						if (empty($tq)) {
							continue;
						}

						if ($tq < $uq) {
							$wsl->qty_in = $tq;
							$wsl->save();
							$uq = $tq;
						}
						$cq = $r->stock->prod->uq2cq($uq);
						$pallet = WmsLocation::model()->find('code = :code', array(':code' => $itm->mdata['pl']));
						if (in_array($itm->mdata['pl'], $showed)) {
							$pallet->extra = [];
						} else {
							$showed[] = $itm->mdata['pl'];
						}
						$prod_pack = WmsProdPack::model()->find("prod_id = :prod_id and type = :type", array(":prod_id" => $r->stock->prod->id, ":type" => array_search("Carton", WmsProdPack::$types)));
						$rows[] = [$r->stock->prod->name, '="' . $r->stock->prod->ean . '"', '="' . @$prod_pack->barcode . '"', $r->stock->batch, $itm->mdata['pl'], $cq, $uq, $itm->mdata['ex'], @$pallet->extra['length'], @$pallet->extra['width'], @$pallet->extra['height'], @$pallet->extra['weight'], (!empty($pallet->extra['plastic']) ? '塑料板' : (!empty($pallet->extra['chep']) ? '熏蒸板' : '')), ($r->stock->prod->getCqWeight($cq, $uq) + ($j == 0 ? 10 : 0)), $itm->ts, $itm->mdata['nt']];
					}
					$plt[] = $itm->mdata['pl'];
				}

				foreach ($rows as $r) {
					$xls->addRow($i++, $r);
				}
				$li = $i - 1;
				$xls->addRow($i++, ['', '', '', '', sizeof(array_unique($plt)), '=SUM(F4:F' . $li . ')', '=SUM(G4:G' . $li . ')', '', '', '', '', '=SUM(L4:L' . $li . ')', '', '=SUM(N4:N' . $li . ')']);
				$xls->output('pallet_weight_' . '-' . $model->getNo() . '.xlsx');
				break;
			case 'search':
				$model = new WmsTask('search');
				$model->unsetAttributes();
				if (isset($_GET['WmsTask'])) {
					$model->attributes = $_GET['WmsTask'];
				}
				$dp = $model->search(false);
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth(array(15, 15, 15, 15, 15, 15, 15));
				$xls->addRow($i++, array('Job No', 'Task ID', 'Customer', 'Ref', 'Type', 'Status', 'OP', 'Schd Time', 'Due Time', 'Compl Time'));
				foreach ($dp->data as $r) {
					$xls->addRow($i++, array($r->job->no, $r->getNo(), empty($r->job->customer) ? '' : $r->job->customer->shortName(3), $r->ref, $r->getType(), $r->getStatus(), empty($r->op) ? '' : $r->op->getName(), $r->schd_time, $r->due_time, $r->compl_time));
				}
				$xls->output('wms_task_search_export_' . time() . '.xlsx');
				break;
			case 'packingall':
				// if (!empty($_GET['ids'])) {
					// $ids = array_unique($_GET['ids']);
					// $wmstasks = WmsTask::model()->findAll('id in ("' . implode('","', $ids) . '")');
					// oPDF::renderPDF('packing_list_all', array('tasks' => $wmstasks), 1, 'packing_list_all.pdf');
				// }
				if (empty($_POST)) {
					$sql = 'SELECT DISTINCT job.org_id FROM wms_task t JOIN wms_job job ON t.job_id = job.id WHERE t.id > 10000 AND t.status = 20 AND t.is_request = 1 AND t.type IN (3020,3030)';
					$orgs = Yii::app()->db->createCommand($sql)->queryAll();
					foreach ($orgs as $k => $org) {
						$org = Org::model()->findByPk($org['org_id']);
						if (in_array(Yii::app()->user->id, WmsTask::$op)) {	
							if (!in_array($org->extra['op_id'], WmsTask::$op) && !in_array($org->extra['sp_id'], WmsTask::$op)) {
								unset($orgs[$k]);
							}
						} else {
							if ($org->extra['op_id'] != Yii::app()->user->id && $org->extra['sp_id'] != Yii::app()->user->id && User::model()->findByPk(Yii::app()->user->id)->type > 0) {
								unset($orgs[$k]);
							}
						}
					}
					$this->render('packing_list', array('orgs' => $orgs));
				} else {
					$cond = 't.id > 10000 AND t.status = 20 AND t.is_request = 1 AND t.type in (3020,3030)';
					if (!empty($_POST['orgs'])) {
						$cond .= ' AND job.org_id in (' . implode(',', array_keys($_POST['orgs'])) . ')';
					} else {
						echo 'no org choosed';
						return;
					}
					$wmstasks = WmsTask::model()->with('job')->findAll(array('condition' => $cond, 'order' => 'job.org_id desc'));
					$tasks = [];
					foreach ($wmstasks as $wmstask) {
						if (!isset($tasks[$wmstask->job->org_id])) {
							$tasks[$wmstask->job->org_id] = [];
						}
						foreach ($wmstask->items as $i => $itm) {
							if (empty($itm->mdata['si'])) continue;
							$s = WmsStock::model()->findByPk($itm->mdata['si']);
							$sku = $s->getCustSKU();
							$locs = $s->getBestLocs($itm->mdata['uq']);
							$tasks[$wmstask->job->org_id][$locs[1][0][0]->loc->parent->name][$s->prod->name][] = $wmstask;
							break;
						}
					}
					// sort tasks by loc, prod
					foreach ($tasks as $org => $orgtasks) {
						foreach ($orgtasks as $loc => $loctasks) {
							krsort($loctasks);
							$orgtasks[$loc] = $loctasks;
						}
						krsort($orgtasks);
						$tasks[$org] = $orgtasks;
					}
					oPDF::renderPDF('packing_list_all', array('tasks' => $tasks), 1, 'packing_list_all.pdf');
				}
				break;
			case 'reconciliation':
				if (empty($_POST)) {
					$this->render('reconciliation');
				} else {
					if (!empty($_POST['inv_id'])&&$_POST['type']==0) {
						[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
						$wmsInvoice = Invoice::model()->find('no = :no and status != 10 and dpmt = 40', [':no' => $_POST['inv_id']]);
						$objInvoiceLines = $wmsInvoice->lines;
						$timeInvoiceTime = $wmsInvoice->date;
	
						$xls = new oExcel;
						$i = 1;
						$xls->setColWidth([13,10,10,10,10,10,10,10,10,10,10,10,10]);
						$xls->addRow($i++, ['Task No','Ref', 'Courier', 'PostCode', 'Suburb','Width','Length','Height', 'Weight','Revenu', 'Cost','Task Comp Time','Invoice Date','Syd','Mel']);
						$xls->setBG('A1:M1', '000000');
						$xls->setFont('A1:M1', ['color' => ['rgb' => 'FFFFFF']]);
	
						$tempTasks = [];
						Yii::app()->name = $app_name;
	
						if(!empty($objInvoiceLines)){
							foreach($objInvoiceLines as $objInvoiceLine){
								if($objInvoiceLine->det=="Delivery"){
	
									$floatAmount=$objInvoiceLine->amount;
									$floatGST=$objInvoiceLine->gst;
									
									//revenu (invoice for customer)
									
									//$floatRevenu = round($objInvoiceLine->mdata['items'][0][4],2);

									//$task = WmsTask::model()->findByPk($objInvoiceLine->fid);
									

									//$floatWeight = round(trim(substr(str_replace("Delivery ","",$objInvoiceLine->mdata['items'][0][3]),10),"kg"),2);
									$floatRevenu = round($objInvoiceLine->amount - $objInvoiceLine->gst,2);

									$wmsTask = WmsTask::model()->findByPk($objInvoiceLine->fid);

									if(!empty($wmsTask)){
										$stringTaskRef = $wmsTask->mainTask->ref;
										$shipment=ImParcel::model()->findByPk($wmsTask->mdata['shipment_id']);
									}
									$floatWeight = empty($shipment->weight)? 0:$shipment->weight;

									$strPackage = $wmsTask->mainTask->packTask->mdata['pkg'];
		
									$wmsLength = 0;
									$wmsWidth = 0;
									$wmsHeight = 0;
									$wmsPkgs = 0;
									if (!empty($strPackage)) {
										foreach (json_decode($strPackage, true) as $pkg) {
											if (!empty($pkg['w'])||!empty($pkg['h'])||!empty($pkg['d'])){
												$wmsLength += $pkg['w']/100;
												$wmsWidth += $pkg['h']/100;
												$wmsHeight += $pkg['d']/100;
												$wmsPkgs++;
											}	
										}
									}
									$timeTaskComplete = WmsTask::model()->findByPk($objInvoiceLine->fid)->mainTask->compl_time;
		
									$wmsCourierId = $wmsTask->mdata['courier'];
									$wmsCourierName = !empty(Org::model()->findByPk($wmsCourierId)->name)? Org::model()->findByPk($wmsCourierId)->name:"Default";
									$wmsPostcode = $wmsTask->mdata['cnee']['postcode'];
									$wmsSuburb = $wmsTask->mdata['cnee']['suburb'];
									$orgRate=$wmsTask->chooseOrgRate($wmsCourierId,$wmsTask->dpt_id);
									$shipment = ImParcel::model()->findByPk($wmsTask->mdata['shipment_id']);
									//cost (invoice from courier to us)
									if($wmsTask->mdata['courier']=='3702' || $wmsTask->mdata['courier']=='3701'){
										$wmsCost = round(ImcoConsol::getCourierTestCostPrice($orgRate, $wmsPostcode, $floatWeight,$wmsPkgs,false,$wmsSuburb)['price']);
									}else{
										$wmsCost = round(ImcoConsol::getCourierCostPrice($orgRate, $wmsPostcode, $floatWeight,$wmsPkgs,"",$wmsSuburb,false,'',$shipment),2);
									}
									$wmsSurcharge = round($wmsTask->deliveryTaskSurCharge($wmsCourierId,$wmsLength,$wmsWidth,$wmsHeight,$floatWeight),2);

									$modelOrg = $shipment->agent;
									$quote = WmsOrgQuote::model()->find(['condition' => 'org_id = :org_id AND status = 1 AND (JSON_VALUE(meta, "$.whole_sale") IS NULL OR JSON_VALUE(meta, "$.whole_sale") = 0)', 'params' => [':org_id' => $modelOrg->id], 'order' => 'id DESC']);
									$numSYDRevenue = WmsInvoice::getDeliveryRevenue($wmsTask,$quote,$modelOrg,106);
									$numMELRevenue = WmsInvoice::getDeliveryRevenue($wmsTask,$quote,$modelOrg,218);
									//$numBNERevenue
		
									$xls->addRow($i++, ["T".$wmsTask->link_id,$stringTaskRef,$wmsCourierName, $wmsPostcode, $wmsSuburb,$wmsLength,$wmsWidth,$wmsHeight, $floatWeight, $floatRevenu,$wmsCost+$wmsSurcharge,$timeTaskComplete,$timeInvoiceTime,$numSYDRevenue,$numMELRevenue]);
								}
							}
							$xls->output('Reconciliation Report ' . date("Y-m-d H:i:s") . '.xlsx');
						}else{
							echo 'no invoice finded';
							return;
						}

					}else if(!empty($_POST['inv_id'])&&$_POST['type']==1){

						$xls = new oExcel;
						$i = 1;
						$xls->setColWidth([13,10,10,10,10,10,10,10,10,10,10,10,10]);
						$xls->addRow($i++, ['Task No','Ref', 'Courier', 'PostCode', 'Suburb','Width','Length','Height', 'Weight','Revenu', 'Cost','Task Comp Time','Invoice Date','Syd','Mel']);
						$xls->setBG('A1:M1', '000000');
						$xls->setFont('A1:M1', ['color' => ['rgb' => 'FFFFFF']]);

						[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
						$wmsInvoices = Invoice::model()->findAll('to_id = :no and status != 10 and dpmt = 40 and type in (65,70,75)', [':no' => $_POST['inv_id']]);
						Yii::app()->name = $app_name;

						foreach($wmsInvoices as $wmsInvoice){
							[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
							$objInvoiceLines = $wmsInvoice->lines;
							$timeInvoiceTime = $wmsInvoice->date;
							$tempTasks = [];
							Yii::app()->name = $app_name;

							if(!empty($objInvoiceLines)){
								foreach($objInvoiceLines as $objInvoiceLine){
									if($objInvoiceLine->det=="Delivery"){
		
										$floatAmount=$objInvoiceLine->amount;
										$floatGST=$objInvoiceLine->gst;
										
										// //revenu (invoice for customer)
										// $floatRevenu = round($objInvoiceLine->amount - $objInvoiceLine->gst,2);
										// //$floatRevenu = round($objInvoiceLine->mdata['items'][0][4],2);
										// $floatWeight = round(trim(substr(str_replace("Delivery ","",$objInvoiceLine->mdata['items'][0][3]),10),"kg"),2);
			
										// $wmsTask = WmsTask::model()->findByPk($objInvoiceLine->fid);
										// $strPackage = WmsTask::model()->findByPk($objInvoiceLine->fid)->mainTask->packTask->mdata['pkg'];
										$floatRevenu = round($objInvoiceLine->amount - $objInvoiceLine->gst,2);
									
										$wmsTask = WmsTask::model()->findByPk($objInvoiceLine->fid);
	
										if(!empty($wmsTask)){
											$stringTaskRef = $wmsTask->mainTask->ref;
											$shipment=ImParcel::model()->findByPk($wmsTask->mdata['shipment_id']);
										}
										$floatWeight = empty($shipment->weight)? 0:$shipment->weight;
	
										$strPackage = $wmsTask->mainTask->packTask->mdata['pkg'];
			
										$wmsLength = 0;
										$wmsWidth = 0;
										$wmsHeight = 0;
										$wmsPkgs = 0;
										if (!empty($strPackage)) {
											foreach (json_decode($strPackage, true) as $pkg) {
												if (!empty($pkg['w'])||!empty($pkg['h'])||!empty($pkg['d'])){
													$wmsLength += $pkg['w']/100;
													$wmsWidth += $pkg['h']/100;
													$wmsHeight += $pkg['d']/100;
													$wmsPkgs++;
												}	
											}
										}
										$timeTaskComplete = WmsTask::model()->findByPk($objInvoiceLine->fid)->mainTask->compl_time;
			
										$wmsCourierId = $wmsTask->mdata['courier'];
										$wmsCourierName = !empty(Org::model()->findByPk($wmsCourierId)->name)? Org::model()->findByPk($wmsCourierId)->name:"Default";
										$wmsPostcode = $wmsTask->mdata['cnee']['postcode'];
										$wmsSuburb = $wmsTask->mdata['cnee']['suburb'];
										$orgRate=$wmsTask->chooseOrgRate($wmsCourierId,$wmsTask->dpt_id);
										$shipment = ImParcel::model()->findByPk($wmsTask->mdata['shipment_id']);
										//cost (invoice from courier to us)
										if($wmsTask->mdata['courier']=='3702' || $wmsTask->mdata['courier']=='3701'){
											$tempCost = round(ImcoConsol::getCourierTestCostPrice($orgRate, $wmsPostcode, $floatWeight,$wmsPkgs,false,$wmsSuburb));
											$wmsCost = $tempCost['price'];
										}else{
											$wmsCost = round(ImcoConsol::getCourierCostPrice($orgRate, $wmsPostcode, $floatWeight,$wmsPkgs,"",$wmsSuburb,false,'',$shipment),2);
										}
											$wmsSurcharge = round($wmsTask->deliveryTaskSurCharge($wmsCourierId,$wmsLength,$wmsWidth,$wmsHeight,$floatWeight),2);

											$modelOrg = $shipment->agent;
											$quote = WmsOrgQuote::model()->find(['condition' => 'org_id = :org_id AND status = 1 AND (JSON_VALUE(meta, "$.whole_sale") IS NULL OR JSON_VALUE(meta, "$.whole_sale") = 0)', 'params' => [':org_id' => $modelOrg->id], 'order' => 'id DESC']);
											$numSYDRevenue = WmsInvoice::getDeliveryRevenue($wmsTask,$quote,$modelOrg,106);
											$numMELRevenue = WmsInvoice::getDeliveryRevenue($wmsTask,$quote,$modelOrg,218);
			
										$xls->addRow($i++, ["T".$wmsTask->link_id,$stringTaskRef,$wmsCourierName, $wmsPostcode, $wmsSuburb,$wmsLength,$wmsWidth,$wmsHeight, $floatWeight, $floatRevenu,$wmsCost+$wmsSurcharge,$timeTaskComplete,$timeInvoiceTime,$numSYDRevenue,$numMELRevenue]);
									}
								}
							}
						}
						$xls->output('Reconciliation Report ' . date("Y-m-d H:i:s") . '.xlsx');
					}
					else {
						echo 'Error please check';
						return;
					}	
				}
				break;
			case 'courierall':
				if (empty($_POST)) {
					$sql = 'SELECT distinct job.org_id FROM wms_task t JOIN wms_job job ON t.job_id = job.id WHERE t.id > 10000 AND t.status = 20 AND t.is_request = 1 AND t.type in (3020,3030)';
					$orgs = Yii::app()->db->createCommand($sql)->queryAll();
					$this->render('courier_list', array('orgs' => $orgs));
				} else {
					$cond = 't.id > 10000 AND t.status = 20 AND t.is_request = 1 AND t.type in (3020,3030)';
					if (!empty($_POST['orgs'])) {
						$cond .= ' AND job.org_id in (' . implode(',', array_keys($_POST['orgs'])) . ')';
					} else {
						echo 'no org choosed';
						return;
					}
					if (!empty($_POST['dpt_id'])){
						$cond .= ' AND t.dpt_id = '.$_POST['dpt_id'];
					}

					$transaction = Yii::app()->db->beginTransaction();

					try {
					$wmstasks = WmsTask::model()->with('job')->findAll(array('condition' => $cond, 'order' => 'job.org_id desc'));

					$stockAll = [];
					$tempTasks = [];
					foreach ($wmstasks as $wmstask) {
						$stocks = [];
						//if (!empty($wmstask->deliveryTask) && ($wmstask->deliveryTask->mdata['courier']) == "114") continue;
						$count = 0;
						foreach ($wmstask->items as $item) {
							if ($item->mdata['si'] == 0 || empty($item->mdata['uq'])) continue 2;
							//if ($item->mdata['si'] != 0 && (empty($item->stockLedgers) || $item->mdata['uq'] != $item->stockLedgers[0]->qty_in || $item->stockLedgers[0]->stock->qty < $item->mdata['uq'])) continue 2;
							$count += intval($item->mdata['uq']);
						}

						if ($count > 1){
							continue;
						}

						foreach ($wmstask->items as $item) {
							if (empty($item->mdata['si'])) continue ;
							if (empty($stocks[$item->mdata['si']])) {
								$stocks[$item->mdata['si']] = 0;
							}
							$stocks[$item->mdata['si']] += intval($item->mdata['uq']);
							$stockAll[$item->mdata['si']] += intval($item->mdata['uq']);
						}
						$locationitems = WmsTask::_nextItemBatchPickForPickingList($stocks,$stockAll);

						foreach ($locationitems as $locationitem) {
							$tempTasks[$wmstask->id] = array('ID'=>$wmstask->id,'COURIER'=>empty($wmstask->deliveryTask->mdata['courier'])? "":Org::model()->findByPk($wmstask->deliveryTask->mdata['courier'])->name,'LOC'=>$locationitem[3],'PLT'=>$locationitem[2],'REF'=>$wmstask->ref,'PRODNAME'=>$locationitem[1],'QTY'=>$locationitem[4]);
						}
					}
					
					foreach ($tempTasks as $key => $row) {
						$locSort[$key]  = $row['LOC'];
						$prodnameSort[$key] = $row['PRODNAME'];
						$courierSort[$key] = $row['COURIER'];
					}
					if(is_array($tempTasks)&&is_array($locSort)&&is_array($prodnameSort)&&is_array($courierSort)){
						array_multisort($courierSort, SORT_ASC, $prodnameSort, SORT_ASC, $locSort, SORT_ASC, $tempTasks);
					}

					$qp_tasks = $tempTasks;

					$td = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
					
					$ps = [];
					foreach ($qp_tasks as $temptask) {
						$rs = [];
						$task = WmsTask::model()->with('job')->findByPk($temptask['ID']);//$tempTask['ID']

						
						if (!empty($task->deliveryTask) && !empty($task->packTask->mdata['pkg'])){
							$pkgs = json_decode($task->packTask->mdata['pkg'], true);

							if ($_POST["recreate"] || ($task->deliveryTask->mdata['courier'] != 0 && (empty($task->deliveryTask->mdata['shipment_id']) || empty($task->deliveryTask->mdata['shipment_courier_id']) || $task->deliveryTask->mdata['shipment_courier_id'] != $task->deliveryTask->mdata['courier'] || (count($task->deliveryTask->mdata['shipment_id']) != count($pkgs))))) {
								$task->deliveryTask->toShipment();
							} else {
								if (empty($task->deliveryTask->mdata['shipment_id'])){
									continue ;
								}
							}

							if($task->deliveryTask->mdata['courier'] != 0){
								foreach ($task->deliveryTask->mdata['shipment_id'] as $sid) {
									$p = Shipment::model()->find('id = :id', array(':id' => $sid));
									if (!empty($p)) {
										$rs[] = $p;
	
										foreach($rs as $i=> $objImParcel){
											$objImParcel->ot_id = WmsTask::OT_ID_3PL;
											$objImParcel->save();
										}
										
										$tf = tempnam($td, 'courierall');
										//Eiz Toll
										if(!empty($rs) && $task->deliveryTask->mdata['courier'] == Org::ORGID_COURIER_EIZ_TOLL ){
											$imparcelService = new ImParcelService();
											$temp = $imparcelService->getImparcelLabel( $rs,0,true);
											$ps[$rs[0]->ref] = $temp;
											continue ;
										}
	
										//Allied
										if(!empty($rs) && $task->deliveryTask->mdata['courier'] == "3702" ){
											$customerPrint=0;
											if(!empty( $task->deliveryTask->mdata['trackingno'])){
											$customerPrint=1;
											}
											$imparcelService = new ImParcelService();
											$temp= $imparcelService->getImparcelLabelAllied( $rs,$customerPrint,0,true);
											$ps[$rs[0]->ref] = $temp;
											continue ;
										}

										//UBI Toll
										if(!empty($rs) && $task->deliveryTask->mdata['courier'] == Org::ORGID_COURIER_UBI_TOLL){
											$imparcelService = new ImParcelService();
											$temp=$imparcelService->getImparcelLabel( $rs,0,true);
											$ps[$rs[0]->ref] = $temp;
											continue ;
										}

										//Border
										if(!empty($rs) && ($task->deliveryTask->mdata['courier'] == Org::ORGID_COURIER_BORDER)){
											$imparcelService = new ImParcelService();
											$temp = $imparcelService->getImparcelLabel($rs,0,true);
											$ps[$rs[0]->ref] = $temp;
											continue ;
										}

										//MY Toll
										if(!empty($rs) && $task->deliveryTask->mdata['courier'] == Org::ORGID_COURIER_TOLL_IPEC){
											$imparcelService = new ImParcelService();
											$temp=$imparcelService->getImparcelLabel( $rs,0,true);
											$ps[$rs[0]->ref] = $temp;
											continue ;
										}

	
										if ($rs[0]->type == 10) {//ImParcel
											$p = $rs[0];
											if (empty($rs[0]->id)) {
												print_r($rs[0]->getErrors());
											} else {
												oPDF::renderPDF('label_A6', array('rs' => $rs), 2, $tf);
												$ps[$p->cref] = $tf;
											}
										}
									}
								}
							}
						}
						//$task->mdata['isBatch']="1";
						//$task->update('meta');
					}
					$transaction->commit();
					} catch (Exception $ex) {
						$transaction->rollback();
						throw $ex;
					}

					if (empty($ps)) {
						echo 'no tasks';
					} else {
						oPDF::mergePDF($ps, 1, true, 'courierall'.date("Y-m-d H:i:s").'.pdf');
					}
				}
				break;
			case 'rpt_qty':
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth([30, 15, 10, 10]);
				$xls->addRow($i++, array($model->getType() . ' Job #' . $model->job->no, $model->getNo()));
				$xls->addRow($i++, array('Date: ' . date('Y-m-d')));
				$xls->addRow($i++, ['Prod. Name', 'Prod. EAN', 'Cartons', 'Units']);
				$rows = [];
				foreach ($model->actionTask->items as $k => $itm) {
					if (empty($itm->mdata['cq']) && empty($itm->mdata['uq'])) {
						continue;
					}

					if (!empty($itm->mdata['si'])) {
						$stock = WmsStock::model()->findByPk($itm->mdata['si']);
					} else {
						$stock = WmsStock::creget($model->job->org_id, $itm->mdata);
					}

					if (empty($rows[$stock->prod->name])) {
						$rows[$stock->prod->name] = [$stock->prod->name, '="' . $stock->prod->ean . '"', floatval(trim($itm->mdata['cq'])), floatval(trim($itm->mdata['uq']))];
					} else {
						$rows[$stock->prod->name][2] += floatval(trim($itm->mdata['cq']));
						$rows[$stock->prod->name][3] += floatval(trim($itm->mdata['uq']));
					}
				}
				foreach ($rows as $r) {
					$xls->addRow($i++, $r);
				}
				$li = $i - 1;
				$xls->addRow($i++, ['', '', '=SUM(C4:C' . $li . ')', '=SUM(D4:D' . $li . ')']);
				$xls->output('qty_' . $model->job->no . '-' . $model->getNo() . '.xlsx');
				break;
			case 'invoice':
				if (empty($_POST)) {
					$this->render('invoice');
				} else {
					$fromdate = !empty($_POST['fromdate']) ? $_POST['fromdate'] : '1970-01-01';
					$todate = !empty($_POST['todate']) ? $_POST['todate'] : date('Y-m-d');
					if (empty($_POST['agent_id'])) {
						echo 'Please choose agent';
						return;
					}
					$quote = WmsOrgQuote::model()->find('org_id = :org_id and status = 1 and vfrom <= :fd and vto >= :td', [':org_id' => $_POST['agent_id'], ':fd' => $fromdate, ':td' => $todate]);
					if (empty($quote)) {
						$quote = WmsOrgQuote::model()->find(['condition' => 'org_id = :org_id and status = 1', 'params' => [':org_id' => $_POST['agent_id']], 'order' => 'id DESC']);
					}

					$i = 1;
					$xls = new oExcel;
					$xls->setTitle('入库费');
					$xls->addRow($i++, ['Task no.', '入库时间', '商品明细', '效期', '批号', '数量', '费用']);
					$xls->setColWidth([15, 20, 40, 15, 20, 10, 10]);
					$xls->centerAlignment('A1:G1000');
					$tasks = WmsTask::model()->with('job')->findAll('t.compl_time >= :fromdate AND t.compl_time <= :todate AND t.status = 99 AND t.is_request = 1 AND t.type in (1010,1020) AND job.org_id = :org_id', [':fromdate' => $fromdate, ':todate' => $todate, ':org_id' => $_POST['agent_id']]);
					foreach ($tasks as $task) {
						$palletinTask = $task->actionTask;
						$pallets = [];
						foreach ($palletinTask->items as $item) {
							$pallets[$item->mdata['pl']][] = $item;
						}

						$items = [];
						foreach ($pallets as $pallet) {
							if (count($pallet) > 1) {
								foreach ($pallet as $item) {
									$prod_pack = WmsProdPack::model()->find("prod_id = :prod_id and type = :type", [":prod_id" => $item->mdata['gi'], ":type" => array_search("Carton", WmsProdPack::$types)]);
									if ($prod_pack) {
										$cq = floor(intval($item->mdata['uq']) / $prod_pack->qty);
										$uq = intval($item->mdata['uq']) % $prod_pack->qty;
									} else {
										$cq = 0;
										$uq = $item->mdata['uq'];
									}
									if (empty($items[$item->mdata['gn'].$item->mdata['ex'].$item->mdata['bn']])) {
										$items[$item->mdata['gn'].$item->mdata['ex'].$item->mdata['bn']] = [0, 0, $item->mdata['gn'], $item->mdata['ex'], $item->mdata['bn']];
									}
									$items[$item->mdata['gn'].$item->mdata['ex'].$item->mdata['bn']][0] += $item->mdata['uq'];
									$items[$item->mdata['gn'].$item->mdata['ex'].$item->mdata['bn']][1] += $cq * floatval($quote->mdata[WmsOrgQuote::QUOTE_SORTING_COUNTING_CARTON]) + $uq * floatval($quote->mdata[WmsOrgQuote::QUOTE_SORTING_COUNTING_UNIT]);
								}
							} else {
								if (empty($items[$item->mdata['gn'].$item->mdata['ex'].$item->mdata['bn']])) {
									$items[$item->mdata['gn'].$item->mdata['ex'].$item->mdata['bn']] = [0, 0, $item->mdata['gn'], $item->mdata['ex'], $item->mdata['bn']];
								}
								$items[$item->mdata['gn'].$item->mdata['ex'].$item->mdata['bn']][0] += $item->mdata['uq'];
								$items[$item->mdata['gn'].$item->mdata['ex'].$item->mdata['bn']][1] += floatval($quote->mdata[WmsOrgQuote::QUOTE_PALLET_IN]);
							}
						}

						foreach ($items as $prod => $item) {
							if ($prod == array_keys($items)[0]) {
								$xls->addRow($i++, [$task->no, date('Y-m-d', strtotime($task->compl_time)), $item[2], $item[3], $item[4], $item[0], '$' . AppHelper::money_format('%i', $item[1])]);
							} else {
								$xls->addRow($i++, ['', '', $item[2], $item[3], $item[4], $item[0], '$' . AppHelper::money_format('%i', $item[1])]);
							}
						}

						$xls->addRow($i++, []);
					}

					$i = 1;
					$xls->createSheet('出库费');
					$xls->goSheet(1);
					$xls->addRow($i++, ['Task no.', '完成时间', '商品明细', '数量', '重量', '拣货费', '订单录入费', '操作费', '邮费', '其他费用', '共计费用', '备注']);
					$xls->setColWidth([15, 20, 40, 10, 10, 10, 15, 10, 10, 15, 15, 20]);
					$xls->centerAlignment('A1:L1000');
					$tasks = WmsTask::model()->with('job')->findAll('t.compl_time >= :fromdate AND t.compl_time <= :todate AND t.status = 99 AND t.is_request = 1 AND t.type in (3020,3030) AND job.org_id = :org_id', [':fromdate' => $fromdate, ':todate' => $todate, ':org_id' => $_POST['agent_id']]);
					foreach ($tasks as $task) {
						$pickTask = $task->actionTask;

						// pick
						$items = [];
						foreach ($pickTask->items as $item) {
							$stock = WmsStock::model()->findByPk($item->mdata['si']);
							$prod_pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = :type', [':prod_id' => $stock->prod_id, ':type' => array_search('Carton', WmsProdPack::$types)]);
							if ($prod_pack) {
								$cq = floor(intval($item->mdata['uq']) / intval($prod_pack->qty));
								$uq = intval($item->mdata['uq']) % intval($prod_pack->qty);
							} else {
								$cq = 0;
								$uq = $item->mdata['uq'];
							}
							if ($prod_pack && $cq) {
								$items[] = [$stock->prod->name, $cq * $prod_pack->qty, $cq * $prod_pack->weight, $cq * floatval($quote->mdata[WmsOrgQuote::QUOTE_PICKING_CARTON])];
							}
							if ($uq) {
								if (!empty($stock->prod->weight)) {
									$weight = $stock->prod->weight;
								} else if (preg_match('/(\d*g)/', $stock->prod->name, $m)) {
									$weight = $m[0];
								} else {
									$weight = 0;
								}
								$items[] = [$stock->prod->name, $uq, $uq * $weight, $uq * floatval($quote->mdata[WmsOrgQuote::QUOTE_PICKING_UNIT])];
							}
						}

						// manual
						$creator = User::model()->findByPk(Log::model()->getWmsTaskCreateUserID($task->id));
						if ($creator->org_id != $task->job->org_id && !empty($quote->mdata[WmsOrgQuote::QUOTE_PICKING_MANUAL_ORDER])) {
							$manual = floatval($quote->mdata[WmsOrgQuote::QUOTE_PICKING_MANUAL_ORDER]);
						} else {
							$manual = 0;
						}

						// standard
						if (!empty($quote->mdata[WmsOrgQuote::QUOTE_PICKING_ORDER])) {
							$standard = floatval($quote->mdata[WmsOrgQuote::QUOTE_PICKING_ORDER]);
						} else {
							$standard = 0;
						}

						// material
						if (!empty($quote->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER_EXTRA])) {
							if (!empty($task->packTask->mdata['pkg'])) {
								$pkgs = json_decode($task->packTask->mdata['pkg']);

								$quantity = 0;
								foreach ($pkgs as $pkg) {
									if (explode(' ', $pkg->nt)[0] == '是') {
										$quantity ++;
									}
								}

								$material = $quantity * floatval($quote->mdata[WmsOrgQuote::QUOTE_PACKING_ORDER_EXTRA]);
							} else {
								$material = 0;
							}
						} else {
							$material = 0;
						}

						// delivery
						$delivery = 0;
						if (!empty($task->deliveryTask->mdata['shipment_id'])) {
							$criteria = new CDbCriteria();
							$criteria->compare('id', $task->deliveryTask->mdata['shipment_id']);
							$shipments = Shipment::model()->findAll($criteria);
							if (preg_match('/[\x{4e00}-\x{9fa5}]+/u', $task->deliveryTask->mdata['cnee']['state'])) {
								foreach ($shipments as $shipment) {
									$delivery += max(1, $shipment->weight) * 5 + 1;
								}
							} else {
								if (in_array($_POST['agent_id'], [Org::ORGID_3PL_COBAYER, 2025])) {
									$chargecode = ImportChargeCode::model()->find('org_id = :org_id', [':org_id' => $_POST['agent_id']]);
								} else {
									$chargecode = ImportChargeCode::model()->find('org_id = 114');
								}
								if (!empty($chargecode)) {
									foreach ($shipments as $shipment) {
										$delivery += $task->deliveryTask->getChargeByChargecode($shipment->weight, $task->deliveryTask->mdata['cnee']['postcode'], $chargecode->chargecode);
									}
								}
							}
						}

						$total = 0;
						foreach ($items as $item) {
							$total += $item[3];
						}
						$total += $manual + $standard + $delivery + $material;

						foreach ($items as $k => $item) {
							if ($k == 0) {
								$xls->addRow($i++, [$task->no, date('Y-m-d', strtotime($task->compl_time)), $item[0], $item[1], $item[2] . 'kg', '$' . AppHelper::money_format('%i', $item[3]), '$' . AppHelper::money_format('%i', $manual), '$' . AppHelper::money_format('%i', $standard), '$' . AppHelper::money_format('%i', $delivery), '$' . AppHelper::money_format('%i', $material), '$' . AppHelper::money_format('%i', $total), '']);
							} else {
								$xls->addRow($i++, ['', '', $item[0], $item[1], $item[2] . 'kg', '$' . AppHelper::money_format('%i', $item[3])]);
							}
						}

						$xls->addRow($i++, []);
					}

					$i = 1;
					$xls->createSheet('仓储费');
					$xls->goSheet(2);
					$xls->addRow($i++, ['日期', '明细', '数量', '板数', '费用']);
					$xls->setColWidth([20, 40, 10, 10, 10]);
					$xls->centerAlignment('A1:E1000');
					$invoices = Invoice::model()->findAll('date >= :fromdate AND date <= :todate AND type = 60 AND to_id = :to_id AND status != 10', [':fromdate' => $fromdate, ':todate' => $todate, ':to_id' => $_POST['agent_id']]);
					foreach ($invoices as $invoice) {
						foreach ($invoice->lines as $line) {
							if ($line->ccode == WmsOrgQuote::QUOTE_PALLET_STORAGE_WEEK) {
								foreach ($line->mdata['items'] as $k => $item) {
									if ($k == 0) {
										$xls->addRow($i++, [$invoice->date, $item[0], $item[1], $item[2], '$' . AppHelper::money_format('%i', $item[3] * $item[2])]);
									}
								}
							}
						}

						$xls->addRow($i++, []);
					}

					$i = 1;
					$xls->createSheet('其他费用');
					$xls->goSheet(3);
					$xls->addRow($i++, ['项目', '明细', '单价', '数量', '费用']);
					$xls->setColWidth([35, 80, 10, 10, 10]);
					$xls->centerAlignment('A1:E1000');
					$invoices = Invoice::model()->findAll('date >= :fromdate AND date <= :todate AND type not in (60,70) AND to_id = :to_id AND status != 10', [':fromdate' => $fromdate, ':todate' => $todate, ':to_id' => $_POST['agent_id']]);
					foreach ($invoices as $invoice) {
						$xls->addRow($i++, [$invoice->no]);

						$chargeTypes = EdiJob::getChargeItemTypes();
						$tempLines = [];
						$theLines = [];
						foreach ($invoice->lines as $line) {
							$tempLines[$line->ccode][] = $line;
						}
						ksort($tempLines);
						foreach ($tempLines as $groupLine) {
							foreach ($groupLine as $line) {
								$theLines[] = $line;
							}
						}
						foreach ($theLines as $line) {
							$desc = '';
							if (isset($chargeTypes[$line->ccode])) {
								$desc = $chargeTypes[$line->ccode];
							}
							$xls->addRow($i++, [$desc, $line->det, '$' . AppHelper::money_format('%i', $line->amount - $line->gst), $line->qty, '$' . AppHelper::money_format('%i', ($line->amount - $line->gst) * $line->qty)]);
						}

						$xls->addRow($i++, []);
					}

					$xls->goSheet(0);
					$xls->output('Invoice_' . date('Ymd') . '.xlsx');
				}
				break;
			case 'delivery_profit_rpt':
				if (empty($_POST)) {
					$this->render('delivery_profit_rpt');
				} else {
					$fromdate = !empty($_POST['fromdate']) ? $_POST['fromdate'] : '1970-01-01';
					$todate = !empty($_POST['todate']) ? $_POST['todate'] : date('Y-m-d');
					if (empty($_POST['agent_id'])) {
						echo 'Please choose agent';
						return;
					}

					$i = 1;
					$xls = new oExcel;
					$xls->setTitle('Delivery');
					$xls->addRow($i++, ['Task no.', 'SKU', 'QTY', 'State', 'Postcode', 'Courier', 'Revenue', 'Cost', 'Profit']);
					$xls->setColWidth([15, 20, 20, 20, 20, 30, 15, 15, 15]);
					$xls->centerAlignment('A1:I10000');
					$tasks = WmsTask::model()->with('job')->findAll(['condition' => 't.compl_time >= :fromdate AND t.compl_time <= :todate AND t.status = 99 AND t.is_request = 1 AND t.type in (3010, 3020, 3030) AND job.org_id = :org_id AND t.id > 10000', 'params' => [':fromdate' => $fromdate, ':todate' => $todate, ':org_id' => $_POST['agent_id']], 'order' => 't.id desc']);
					$orgrate = [
						Org::ORGID_COURIER_AUPOST => OrgRate::model()->findByPk(ImportChargeCode::SYDNEY_AUPOST_ID),
						Org::ORGID_COURIER_FASTWAY => OrgRate::model()->findByPk(ImportChargeCode::FASTWAY_ID),
						Org::ORGID_COURIER_STARTRACK => OrgRate::model()->findByPk(ImportChargeCode::STARTRACK_SYDNEY),
					];
					foreach ($tasks as $task) {
						$sku = sizeof($task->items);
						$qty = 0;
						foreach ($task->items as $item) {
							$qty += intval($item->mdata['uq']);
						}
						$state = @$task->deliveryTask->mdata['cnee']['state'];
						$postcode = @$task->deliveryTask->mdata['cnee']['postcode'];
						$courier = @$task->deliveryTask->mdata['shipment_courier_id'];
						if ($courier == Org::ORGID_COURIER_AUPOST) {
							$courier_name = 'Aupost';
						} else if ($courier == Org::ORGID_COURIER_FASTWAY) {
							$courier_name = 'Fastway';
						} else if ($courier == Org::ORGID_COURIER_STARTRACK) {
							$courier_name = 'Startrack SYD';
						}

						$inv_total = 0;
						if (!empty($task->deliveryTask)) {
							$invlines = InvLine::model()->with('invoice')->findAll('invoice.status NOT IN (8,10) AND t.model = "WmsInvoiceLine" AND t.fid IN (' . $task->deliveryTask->id . ')');
							foreach ($invlines as $line) {
								$inv_total += ($line->amount - $line->gst) * $line->qty;
							}
						}

						$bill_total = 0;
						$billlines = BillingLine::model()->findAll('billing_ref = :ref AND org_id = :org_id AND status != 11', [':ref' => $task->no, ':org_id' => $courier]);
						foreach ($billlines as $line) {
							$bill_total += $line->actual_amount;
						}
						if ($bill_total == 0) {
							$weight = 0;
							if (!empty($task->packTask->mdata['pkg'])) {
								$pkgs = json_decode($task->packTask->mdata['pkg']);
								if (!empty($pkgs)) {
									if (!is_array($pkgs)) {
										foreach ($pkgs as $pkg) {
											$weight += $pkg->wt;
										}
										$packs = count($pkgs);
										$bill_total = ImcoConsol::getCourierCostPrice($orgrate[$courier], $task->deliveryTask->mdata['cnee']['postcode'], $weight, $packs);
									} else {
										Yii::log($task->getNo() . ' is not array', 'warning');
									}
								}
							}
						}

						$xls->addRow($i++, [$task->no, $sku, $qty, $state, $postcode, $courier_name, $inv_total, $bill_total, $inv_total - $bill_total]);
					}

					$i = 1;
					$xls->createSheet('Profit');
					$xls->goSheet(1);
					$xls->addRow($i++, ['Task no.', 'Revenue', 'Cost', 'Profit']);
					$xls->setColWidth([15, 15, 15, 15]);
					$xls->centerAlignment('A1:G10000');
					foreach ($tasks as $task) {
						$inv_total = 0;
						$fid = $task->id;
						foreach ($task->subTasks as $t) {
							$fid .= ',' . $t->id;
						}
						$invlines = InvLine::model()->with('invoice')->findAll('invoice.status NOT IN (8,10) AND t.model = "WmsInvoiceLine" AND t.fid in (' . $fid . ')');
						foreach ($invlines as $line) {
							$inv_total += ($line->amount - $line->gst) * $line->qty;
						}

						$bill_total = 0;
						$billlines = BillingLine::model()->findAll('billing_ref = :ref AND status != 11', [':ref' => $task->no]);
						foreach ($billlines as $line) {
							$bill_total += $line->actual_amount;
						}

						$xls->addRow($i++, [$task->no, $inv_total, $bill_total, $inv_total - $bill_total]);
					}

					$xls->goSheet(0);
					$xls->output('Delivery & Cost Report_' . $_POST['agent_id'] . '_' . date('Ymd') . '.xlsx');
				}
				break;
			case 'pickpackinvoice':
				$invoices = Invoice::model()->findAll('to_id = ' . Org::ORGID_3PL_COBAYER . ' AND status NOT IN (8,10) AND type = 70');
				$sums = [];
				$dets = [];
				foreach ($invoices as $inv) {
					if (!empty($inv->lines)) {
						foreach ($inv->lines as $line) {
							foreach ($line->mdata['items'] as $item) {
								if (preg_match('/Comsumable Materials/', $item[3]) || !in_array($line->det, ['Pick Unit', 'Pick Carton', 'Pack Order'])) {
									continue;
								}

								$prod = explode(' * ', explode(' - ', explode(' (', $item[3])[0])[1] . (!empty(explode(' - ', explode(' (', $item[3])[0])[2]) ? ' - ' . explode(' - ', explode(' (', $item[3])[0])[2] : ''))[0];
								if (empty($dets[$item[0]][$line->det][$prod])) {
									$dets[$item[0]][$line->det][$prod] = ['amount' => 0, 'inv' => $inv->no, 'cap' => 0];
								}
								$dets[$item[0]][$line->det][$prod]['amount'] += $item[6];

								if (empty($sums[$item[0]][$line->det])) {
									$sums[$item[0]][$line->det] = ['amount' => 0, 'inv' => $inv->no, 'cap' => 0];
								}
								$sums[$item[0]][$line->det]['amount'] += $item[6];
							}
						}
					}
				}

				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth([15,15,15,30,15,15,15]);
				$xls->addRow($i++, ['Inv No', 'Task No', 'Type', 'Det', 'Amount', 'SKU Cap', 'SKU Diff']);
				foreach ($dets as $taskno => $lines) {
					foreach ($lines as $type => $prods) {
						foreach ($prods as $prodname => $item) {
							$item['sku'] = $item['amount'] > 20 ? 20 : $item['amount'];
							$xls->addRow($i++, [$item['inv'], $taskno, $type, $prodname, $item['amount'], $item['sku'], round($item['amount'] - $item['sku'], 2)]);
							$sums[$taskno][$type]['sku'] += $item['sku'];
						}
					}
				}
				$xls->setTitle('Details');

				$xls->createSheet('Summary');
				$xls->goSheet(1);
				$i = 1;
				$xls->setColWidth([15,15,15,15,15,15,15,15,15]);
				$xls->addRow($i++, ['Inv No', 'Task No', 'Type', 'Amount', 'SKU CAP', 'SKU Diff', '', 'Order Cap', 'Order Diff']);
				foreach ($sums as $taskno => $lines) {
					foreach ($lines as $type => $item) {
						$item['order'] = $item['amount'] > 20 ? 20 : $item['amount'];
						$xls->addRow($i++, [$item['inv'], $taskno, $type, $item['amount'], $item['cap'], round($item['amount'] - $item['sku'], 2), $item['order'], round($item['amount'] - $item['order'], 2)]);
					}
				}

				$xls->output('wms_task_pick&pack_report.xlsx');
				break;
			case 'pickpackinvoice2':
				$tasks = WmsTask::model()->with('job')->findAll('t.type IN (3020, 3030) AND t.is_request = 1 AND t.status = 99 AND job.org_id = ' . Org::ORGID_3PL_COBAYER);
				$sums = [];
				$dets = [];

				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth([15,15,30,15,15,15,15,15]);
				$xls->addRow($i++, ['Task No', 'Type', 'Det', 'Amount', 'SKU Cap', 'SKU Diff', 'Half Cal', 'Half Diff']);
				$xls->setTitle('Details');
				foreach ($tasks as $task) {
					$qtys = [];
					foreach ($task->items as $item) {
						$stock = WmsStock::model()->findByPk($item->mdata['si']);
						if (empty($qtys[$stock->prod_id])) {
							$qtys[$stock->prod_id] = 0;
						}
						$qtys[$stock->prod_id] += floatval($item->mdata['uq']);
					}

					foreach ($qtys as $prod_id => $qty) {
						$prod = WmsProd::model()->findByPk($prod_id);
						$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = 10', [':prod_id' => $prod_id]);
						if (!empty($pack)) {
							$cq1 = floor($qty / $pack->qty);
							$uq1 = $qty % $pack->qty;

							if ($uq1 > ($pack->qty / 2)) {
								$uq2 = $pack->qty - $uq1;
								$cq2 = $cq1 + 1;
							} else {
								$uq2 = $uq1;
								$cq2 = $cq1;
							}
						} else {
							$cq1 = 0;
							$uq1 = $uq1;

							$cq2 = 0;
							$uq2 = $uq2;
						}

						$pick_amount = $uq1 * 0.25 + $cq1 * 0.4;
						$pick_sku_cap = min($pick_amount, 20);
						$pick_sku_diff = $pick_amount - $pick_sku_cap;

						$pick_half_cal = $uq2 * 0.25 + $cq2 * 0.4;
						$pick_half_diff = $pick_amount - $pick_half_cal;

						$pack_amount = $cq1 * 0.35;
						$pack_sku_cap = min($pack_amount, 20);
						$pack_sku_diff = $pack_amount - $pack_sku_cap;

						$pack_half_cal = $pack_amount;
						$pack_half_diff = 0;

						if (empty($dets[$task->getNo()]['Pick Unit'][$prod->name])) {
							$dets[$task->getNo()]['Pick Unit'][$prod->name] = ['amount' => $pick_amount, 'sku_cap' => $pick_sku_cap, 'sku_diff' => $pick_sku_diff, 'half_cal' => $pick_half_cal, 'half_diff' => $pick_half_diff];
						}
						if (empty($dets[$task->getNo()]['Pack Order'][$prod->name])) {
							$dets[$task->getNo()]['Pack Order'][$prod->name] = ['amount' => $pack_amount, 'sku_cap' => $pack_sku_cap, 'sku_diff' => $pack_sku_diff, 'half_cal' => $pack_half_cal, 'half_diff' => $pack_half_diff];
						}

						$xls->addRow($i++, [$task->getNo(), 'Pick Unit', $prod->name, $pick_amount, $pick_sku_cap, $pick_sku_diff, $pick_half_cal, $pick_half_diff]);
						$xls->addRow($i++, [$task->getNo(), 'Pack Order', $prod->name, $pack_amount, $pack_sku_cap, $pack_sku_diff, $pack_half_cal, $pack_half_diff]);

						if (empty($sums[$task->getNo()]['Pick Unit'])) {
							$sums[$task->getNo()]['Pick Unit'] = ['amount' => 0, 'sku_cap' => 0, 'sku_diff' => 0, 'half_cal' => 0, 'half_diff' => 0];
						}
						$sums[$task->getNo()]['Pick Unit']['amount'] += $pick_amount;
						$sums[$task->getNo()]['Pick Unit']['sku_cap'] += $pick_sku_cap;
						$sums[$task->getNo()]['Pick Unit']['sku_diff'] += $pick_sku_diff;
						$sums[$task->getNo()]['Pick Unit']['half_cal'] += $pick_half_cal;
						$sums[$task->getNo()]['Pick Unit']['half_diff'] += $pick_half_diff;

						if (empty($sums[$task->getNo()]['Pack Order'])) {
							$sums[$task->getNo()]['Pack Order'] = ['amount' => 0, 'sku_cap' => 0, 'sku_diff' => 0, 'half_cal' => 0, 'half_diff' => 0];
						}
						$sums[$task->getNo()]['Pack Order']['amount'] += $pack_amount;
						$sums[$task->getNo()]['Pack Order']['sku_cap'] += $pack_sku_cap;
						$sums[$task->getNo()]['Pack Order']['sku_diff'] += $pack_sku_diff;
						$sums[$task->getNo()]['Pack Order']['half_cal'] += $pack_half_cal;
						$sums[$task->getNo()]['Pack Order']['half_diff'] += $pack_half_diff;
					}
				}

				$xls->createSheet('Summary');
				$xls->goSheet(1);
				$i = 1;
				$xls->setColWidth([15,15,15,15,15,15,15,15,15]);
				$xls->addRow($i++, ['Task No', 'Type', 'Amount', 'SKU Cap', 'SKU Diff', 'Half Cal', 'Half Diff', 'Order Cap', 'Order Diff']);
				foreach ($sums as $taskno => $task) {
					foreach ($task as $type => $line) {
						$order_cap = min($line['amount'], 20);
						$order_diff = $line['amount'] - $order_cap;

						$xls->addRow($i++, [$taskno, $type, $line['amount'], $line['sku_cap'], $line['sku_diff'], $line['half_cal'], $line['half_diff'], $order_cap, $order_diff]);
					}
				}
				$xls->output('wms_task_pick&pack_report2.xlsx');
				break;
			case 'abm_stock_in':
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth([14,14,14,33,20,16,19,16,16,16,16,16,10,10,10,16,33]);
				$xls->addRow($i++, array_merge(['PO No', 'Stock in time', 'Pallet No', 'Product Name产品名称', 'BARCODE', 'Batch No批次号', 'Production Date生产日期', 'Expiry Date有效期'], in_array($model->job->org_id, Org::$airsea_heshengyuan) ? ['Mfr. Date生产日期'] : [], ['Inners/master carton 件数/箱', 'master carton/pallet 箱数/板', 'Inners/pallet 件数/板', 'Pallet L', 'Pallet W', 'Pallet H', 'Pallet Weight (kg)板重', 'Pallet Material 托盘材质 (熏蒸/塑料)']));
				$xls->setBG('A1:Q1', '000000');
				$xls->setFont('A1:Q1', ['color' => ['rgb' => 'FFFFFF']]);
				foreach ($model->actionTask->items as $k => $itm) {
					if (empty($itm->mdata['pl']) || empty($itm->mdata['gi'])) {
						continue;
					}

					$prod = WmsProd::model()->findByPk($itm->mdata['gi']);
					$plt = WmsLocation::model()->find('code = :n OR name = :n', [':n' => $itm->mdata['pl']]);
					if (!empty($itm->mdata['cq'])) {
						$cq = $itm->mdata['cq'];
						$carton_qty = '';
					} else {
						$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = 10', [':prod_id' => $itm->mdata['gi']]);
						$carton_qty = max(1, intval(@$pack->qty));
						$cq = intval($itm->mdata['uq']) / $carton_qty;
					}
					$xls->addRow($i++, array_merge([$model->ref, $itm->ts, $plt->name, $prod->name, $prod->ean, @$itm->mdata['bn'], @$itm->mdata['pd'], @$itm->mdata['ex']], in_array($model->job->org_id, Org::$airsea_heshengyuan) ? @$itm->mdata['mfr'] : [], [$carton_qty, $cq, $itm->mdata['uq'], $plt->depth, $plt->width, $plt->height, $plt->wt, !empty($plt->extra['type']) ? $plt->extra['type'] == 'plastic' ? '塑料板' : '熏蒸板' : '']));
				}
				$xls->output('ABM Stock In ' . $model->no . '.xlsx');
				break;
			case 'stock_out':
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth([14,14,14,33,20,16,19,16,16,16,16,10,10,10,16,33]);
				$xls->addRow($i++, ['PO No', 'Stock in time', 'Pallet No', 'Product Name产品名称', 'BARCODE', 'Batch No批次号', 'Production Date生产日期', 'Expiry Date有效期', 'Inners/master carton 件数/箱', 'master carton/pallet 箱数/板', 'Inners/pallet 件数/板', 'Pallet L', 'Pallet W', 'Pallet H', 'Pallet Weight (kg)板重', 'Pallet Material 托盘材质 (熏蒸/塑料)']);
				$xls->setBG('A1:P1', '000000');
				$xls->setFont('A1:P1', ['color' => ['rgb' => 'FFFFFF']]);
				if (in_array($model->type, [3020, 3030])) {
					foreach ($model->actionTask->items as $k => $itm) {
						if (empty($itm->mdata['pl']) || empty($itm->mdata['si'])) {
							continue;
						}

						$stock = WmsStock::model()->findByPk($itm->mdata['si']);
						$prod = $stock->prod;
						$plt = WmsLocation::model()->find('code = :n OR name = :n', [':n' => $itm->mdata['pl']]);
						$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = 10', [':prod_id' => $prod->id]);
						$pack = max(1, intval(@$pack->qty));
						$stock = WmsStock::model()->findByPk($itm->mdata['si']);
						$xls->addRow($i++, [$model->ref, $stock->ledgers[0]->ts, $plt->name, $prod->name, $prod->ean, $stock->batch, '', $stock->expiry, $pack, intval($itm->mdata['uq']) / $pack, $itm->mdata['uq'], $plt->depth, $plt->width, $plt->height, $plt->wt, !empty($plt->extra['type']) ? $plt->extra['type'] == 'plastic' ? '塑料板' : '熏蒸板' : '']);
					}
				} else if (in_array($model->type, [3010, 2030])) {
					// $calcued = [];
					foreach ($model->actionTask->items as $k => $itm) {
						if (empty($itm->mdata['pl']) && empty($itm->mdata['pli'])) {
							continue;
						}

						if (!empty($itm->mdata['pl'])) {
							$plt = WmsLocation::model()->find('code = :n OR name = :n', [':n' => $itm->mdata['pl']]);
						} else if (!empty($itm->mdata['pli'])) {
							$plt = WmsLocation::model()->find('id = :n', [':n' => $itm->mdata['pli']]);
						}
						// if (in_array($plt->id, $calcued)) {
						// 	continue;
						// }
						// $calcued[] = $plt->id;

						// $wsls = WmsStockLocation::model()->findAll('location_id = :lid', [':lid' => $plt->id]);
						// foreach ($wsls as $wsl) {
						// 	$stock = $wsl->stock;
						// 	$prod = $stock->prod;
						// 	$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = 10', [':prod_id' => $prod->id]);
						// 	$pack = max(1, intval(@$pack->qty));
						// 	$xls->addRow($i++, [$model->ref, $stock->ledgers[0]->ts, $plt->name, $prod->name, $prod->ean, $stock->batch, '', $stock->expiry, $pack, $wsl->qty / $pack, $wsl->qty, $plt->depth, $plt->width, $plt->height, $plt->wt, !empty($plt->extra['type']) ? $plt->extra['type'] == 'plastic' ? '塑料板' : '熏蒸板' : '']);
						// }

						$ledgers = $itm->stockLedgers;
						foreach ($ledgers as $ledger) {
							$stock = $ledger->stock;
							$prod = $stock->prod;
							$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = 10', [':prod_id' => $prod->id]);
							$pack = max(1, intval(@$pack->qty));
							$xls->addRow($i++, [$model->ref, $stock->ledgers[0]->ts, $plt->name, $prod->name, $prod->ean, $stock->batch, '', $stock->expiry, $pack, $ledger->qty_out / $pack, $ledger->qty_out, $plt->depth, $plt->width, $plt->height, $plt->wt, !empty($plt->extra['type']) ? $plt->extra['type'] == 'plastic' ? '塑料板' : '熏蒸板' : '']);
						}
					}
				}
				$xls->output('Stock Out ' . $model->no . '.xlsx');
				break;
			case 'pickinglistsingleitem':
				if (empty($_POST)) {
					$sql = 'SELECT DISTINCT job.org_id FROM wms_task t JOIN wms_job job ON t.job_id = job.id WHERE t.id > 10000 AND t.status = 20 AND t.is_request = 1 AND t.type IN (3020,3030)';
					$orgs = Yii::app()->db->createCommand($sql)->queryAll();

					$this->render('picking_list_single_item', array('orgs' => $orgs));
				} else {
					$cond = 't.id > 10000 AND t.status = 20 AND t.is_request = 1 AND t.type in (3020,3030)';
					if (!empty($_POST['orgs'])) {
						$cond .= ' AND job.org_id in (' . implode(',', array_keys($_POST['orgs'])) . ')';
					} else {
						echo 'no org choosed';
						return;
					}
					if (!empty($_POST['dpt_id'])){
						$cond .= ' AND t.dpt_id = '.$_POST['dpt_id'];
					}

					$wmstasks = WmsTask::model()->with('job')->findAll(array('condition' => $cond, 'order' => 'job.org_id desc'));

					$xls = new oExcel;
					$i = 1;
					$xls->setColWidth([10,13,10,14,20,18,4]);
					$xls->addRow($i++, ['Task No', 'Courier', 'LOC', 'PLT', 'Ref', 'Prod Name',  'QTY']);
					$xls->setBG('A1:G1', '000000');
					$xls->setFont('A1:G1', ['color' => ['rgb' => 'FFFFFF']]);

					$stockAll = [];
					$tempTasks = [];
					foreach ($wmstasks as $wmstask) {
						$stocks = [];
						//if (!empty($wmstask->deliveryTask) && ($wmstask->deliveryTask->mdata['courier']) == "114") continue;
						$count = 0;
						foreach ($wmstask->items as $item) {
							if (empty($item->mdata['uq'])) continue 2;
							//if ($item->mdata['si'] != 0 && (empty($item->stockLedgers) || $item->mdata['uq'] != $item->stockLedgers[0]->qty_in || $item->stockLedgers[0]->stock->qty < $item->mdata['uq'])) continue 2;
							$count += intval($item->mdata['uq']);
						}

						if ($count > 1){
							continue;
						}

						foreach ($wmstask->items as $item) {
							if (empty($item->mdata['si'])) continue ;
							if (empty($stocks[$item->mdata['si']])) {
								$stocks[$item->mdata['si']] = 0;
							}
							$stocks[$item->mdata['si']] += intval($item->mdata['uq']);
							$stockAll[$item->mdata['si']] += intval($item->mdata['uq']);
						}
						$locationitems = WmsTask::_nextItemBatchPickForPickingList($stocks,$stockAll);

						foreach ($locationitems as $locationitem) {
							$tempTasks[$wmstask->id] = array('ID'=>"T".$wmstask->id,'COURIER'=>empty($wmstask->deliveryTask->mdata['courier'])? "":Org::model()->findByPk($wmstask->deliveryTask->mdata['courier'])->name,'LOC'=>$locationitem[3],'PLT'=>$locationitem[2],'REF'=>$wmstask->ref,'PRODNAME'=>$locationitem[1],'QTY'=>$locationitem[4]);
						}
					}
					
					foreach ($tempTasks as $key => $row) {
						$locSort[$key]  = $row['LOC'];
						$prodnameSort[$key] = $row['PRODNAME'];
						$courierSort[$key] = $row['COURIER'];
					}
					if(is_array($tempTasks)&&is_array($locSort)&&is_array($prodnameSort)&&is_array($courierSort)){
						array_multisort($courierSort, SORT_ASC, $prodnameSort, SORT_ASC, $locSort, SORT_ASC, $tempTasks);
					}
					
					foreach ($tempTasks as $tempTask) {
						$xls->addRow($i++, [$tempTask['ID'],$tempTask['COURIER'], $tempTask['LOC'], $tempTask['PLT'], $tempTask['REF'], $tempTask['PRODNAME'],$tempTask['QTY']]);
					}

					$xls->output('Picking List ' . date("Y-m-d H:i:s") . '.xlsx');
				}
				break;
			case 'packingallsingleitem':
				if (empty($_POST)) {
					$sql = 'SELECT DISTINCT job.org_id FROM wms_task t JOIN wms_job job ON t.job_id = job.id WHERE t.id > 10000 AND t.status = 20 AND t.is_request = 1 AND t.type IN (3020,3030)';
					$orgs = Yii::app()->db->createCommand($sql)->queryAll();
					foreach ($orgs as $k => $org) {
						$org = Org::model()->findByPk($org['org_id']);
						if (in_array(Yii::app()->user->id, WmsTask::$op)) {	
							if (!empty($org->extra['op_id']) && $org->extra['op_id'] != 305 && !empty($org->extra['sp_id']) && $org->extra['sp_id'] != 305 && User::model()->findByPk(305)->type > 0) {
								unset($orgs[$k]);
							}
						} else {
							if (!empty($org->extra['op_id']) && $org->extra['op_id'] != Yii::app()->user->id && !empty($org->extra['sp_id']) && $org->extra['sp_id'] != Yii::app()->user->id && User::model()->findByPk(Yii::app()->user->id)->type > 0) {
								unset($orgs[$k]);
							}
						}
					}
					$this->render('packing_list_single_item', array('orgs' => $orgs));
				} else {
					$cond = 't.id > 10000 AND t.status = 20 AND t.is_request = 1 AND t.type in (3020,3030)';
					if (!empty($_POST['orgs'])) {
						$cond .= ' AND job.org_id in (' . implode(',', array_keys($_POST['orgs'])) . ')';
					} else {
						echo 'no org choosed';
						return;
					}
					$wmstasks = WmsTask::model()->with('job')->findAll(array('condition' => $cond, 'order' => 'job.org_id desc'));

					$batches = [];
					foreach ($wmstasks as $wmstask) {
						if (!empty($wmstask->deliveryTask) && ($wmstask->deliveryTask->bwf & 64) > 0) continue;
						if (!empty($wmstask->batch)) {
							// already have batch, but multi
							if ($wmstask->batch->type == WmsBatch::WMS_BATCH_TYPE_MULTI) continue;
						} else {
							// no exist batch

							
							// qty > 1
							if (!in_array($wmstask->job->org_id, [Org::ORGID_3PL_XCSOURCE])) {
								$count = 0;
								foreach ($wmstask->items as $item) {
									$count += intval($item->mdata['uq']);
								}
								if ($count > 1) continue;
							} else {
								// $count = 0;
								// foreach ($wmstask->items as $item) {
								// 	$count += intval($item->mdata['uq']);
								// }
								// XCSOURCE BATTERY qty > WMS_BATCH_XCSOURCE_SINGLE_MAX
								// if ($count > WmsBatch::WMS_BATCH_XCSOURCE_SINGLE_MAX) continue;
								// XCSOURCE NOT CHOOSE LETTER
								if (!empty($wmstask->deliveryTask->mdata['customer_choose_courier']) && !preg_match('/LETTER/i', $wmstask->deliveryTask->mdata['customer_choose_courier'])) continue;
							}

							// stock not enough
							foreach ($wmstask->items as $item) {
								if ($item->mdata['si'] != 0 && (empty($item->stockLedgers) || $item->mdata['uq'] != $item->stockLedgers[0]->qty_in || $item->stockLedgers[0]->stock->qty < $item->mdata['uq'])) continue 2;
							}

							if (empty($wmstask->batch)) {
								$last_batch = WmsTaskBatch::getLastBatch(WmsBatch::WMS_BATCH_TYPE_SINGLE);

								if (!empty($last_batch) && $last_batch->date == date('Y-m-d') && ($last_batch->box_number == WmsBatch::WMS_BATCH_PICKING_SINGLE_MAX || $last_batch->org_id != $wmstask->job->org_id)) {
									$batch = $last_batch->batch + 1;
									$box_number = 1;
								} else if (!empty($last_batch) && $last_batch->date == date('Y-m-d') && $last_batch->box_number < WmsBatch::WMS_BATCH_PICKING_SINGLE_MAX) {
									$batch = $last_batch->batch;
									$box_number = $last_batch->box_number + 1;
								} else {
									$batch = 1;
									$box_number = 1;
								}
								$task_batch = new WmsTaskBatch;
								$task_batch->date = date('Y-m-d');
								$task_batch->batch = $batch;
								$task_batch->task_id = $wmstask->id;
								$task_batch->box_number = $box_number;
								$task_batch->shelf_number = 0;
								$task_batch->status = WmsTaskBatch::WMS_TASK_BATCH_STATUS_NEW;
								$task_batch->type = WmsBatch::WMS_BATCH_TYPE_SINGLE;
								$task_batch->org_id = $wmstask->job->org_id;
								$task_batch->save();
								$wmstask->refresh();
							}
						}
						$batches[date('ymd', strtotime($wmstask->batch->date)) . ' - ' . sprintf('%02d', $wmstask->batch->batch)][] = $wmstask;
					}
					$ps = [];
					foreach ($batches as $no => $batch) {
						$f = tempnam(Yii::app()->basePath . "/runtime", "pls");
						oPDF::renderPDF('packing_list_single_item', ['batch' => $batch, 'no' => $no], 2, $f);
						$ps[] = $f;
					}
					if (!empty($ps)) {
						oPDF::mergePDF($ps, 1, true, 'packing_list_single_item.pdf');
					} else {
						echo 'no tasks';
					}
				}
				break;
			case 'packingallmultiitems':
				if (empty($_POST)) {
					$sql = 'SELECT DISTINCT job.org_id FROM wms_task t JOIN wms_job job ON t.job_id = job.id WHERE t.id > 10000 AND t.status = 20 AND t.is_request = 1 AND t.type IN (3020,3030)';
					$orgs = Yii::app()->db->createCommand($sql)->queryAll();
					foreach ($orgs as $k => $org) {
						$org = Org::model()->findByPk($org['org_id']);
						if (in_array(Yii::app()->user->id, WmsTask::$op)) {	
							if (!empty($org->extra['op_id']) && $org->extra['op_id'] != 305 && !empty($org->extra['sp_id']) && $org->extra['sp_id'] != 305 && User::model()->findByPk(305)->type > 0) {
								unset($orgs[$k]);
							}
						} else {
							if (!empty($org->extra['op_id']) && $org->extra['op_id'] != Yii::app()->user->id && !empty($org->extra['sp_id']) && $org->extra['sp_id'] != Yii::app()->user->id && User::model()->findByPk(Yii::app()->user->id)->type > 0) {
								unset($orgs[$k]);
							}
						}
					}
					$this->render('packing_list_multi_items', array('orgs' => $orgs));
				} else {
					try {
					$fp = fopen(Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'system_lock'.DIRECTORY_SEPARATOR.'batch_wmstask.lock', 'r');
					flock($fp, LOCK_EX);

					$cond = 't.id > 10000 AND t.status = 20 AND t.is_request = 1 AND t.type in (3020,3030)';
					if (!empty($_POST['orgs'])) {
						$cond .= ' AND job.org_id in (' . implode(',', array_keys($_POST['orgs'])) . ')';
					} else {
						echo 'no org choosed';
						return;
					}
					$wmstasks = WmsTask::model()->with('job')->findAll(array('condition' => $cond, 'order' => 'job.org_id desc'));

					$batches = [];
					foreach ($wmstasks as $wmstask) {
						if (!empty($wmstask->deliveryTask) && ($wmstask->deliveryTask->bwf & 64) > 0) continue;
						if (!empty($wmstask->batch)) {
							// already have batch, but single
							if ($wmstask->batch->type == WmsBatch::WMS_BATCH_TYPE_SINGLE) continue;
						} else {
							// no exist batch
							

							
							// qty <= 1
							if (!in_array($wmstask->job->org_id, [Org::ORGID_3PL_XCSOURCE])) {
								// if (count($wmstask->items) == 0 || (count($wmstask->items) == 1 && $wmstask->items[0]->mdata['uq'] == 1)) continue;
								$count = 0;
								foreach ($wmstask->items as $item) {
									$count += intval($item->mdata['uq']);
								}
								if ($count == 1) continue;
							} else {
								// $count = 0;
								// foreach ($wmstask->items as $item) {
								// 	$count += intval($item->mdata['uq']);
								// }
								// XCSOURCE BATTERY qty <= WMS_BATCH_XCSOURCE_SINGLE_MAX
								// if ($count <= WmsBatch::WMS_BATCH_XCSOURCE_SINGLE_MAX) continue;
								// XCSOURCE CHOOSE LETTER
								if (empty($wmstask->deliveryTask->mdata['customer_choose_courier']) || preg_match('/LETTER/i', $wmstask->deliveryTask->mdata['customer_choose_courier'])) continue;
							}

							// qty > WMS_BATCH_OTHER_MULTI_MAX
							$count = 0;
							foreach ($wmstask->items as $item) {
								$count += intval($item->mdata['uq']);
							}
							if ($count > WmsBatch::WMS_BATCH_OTHER_MULTI_MAX) continue;

							// stock not enough
							foreach ($wmstask->items as $item) {
								if ($item->mdata['si'] != 0 && (empty($item->stockLedgers) || $item->mdata['uq'] != $item->stockLedgers[0]->qty_in || $item->stockLedgers[0]->stock->qty < $item->mdata['uq'])) continue 2;
							}

							if (empty($wmstask->batch)) {
								$last_batch = WmsTaskBatch::getLastBatch(WmsBatch::WMS_BATCH_TYPE_MULTI);

								if (!empty($last_batch) && $last_batch->date == date('Y-m-d') && ($last_batch->box_number == WmsBatch::WMS_BATCH_BOX_MAX || $last_batch->org_id != $wmstask->job->org_id)) {
									$batch = $last_batch->batch + 1;
									$box_number = 1;
								} else if (!empty($last_batch) && $last_batch->date == date('Y-m-d') && $last_batch->box_number < WmsBatch::WMS_BATCH_BOX_MAX) {
									$batch = $last_batch->batch;
									$box_number = $last_batch->box_number + 1;
								} else {
									$batch = 1;
									$box_number = 1;
								}
								$task_batch = new WmsTaskBatch;
								$task_batch->date = date('Y-m-d');
								$task_batch->batch = $batch;
								$task_batch->task_id = $wmstask->id;
								$task_batch->box_number = $box_number;
								$task_batch->shelf_number = 0;
								$task_batch->status = WmsTaskBatch::WMS_TASK_BATCH_STATUS_NEW;
								$task_batch->type = WmsBatch::WMS_BATCH_TYPE_MULTI;
								$task_batch->org_id = $wmstask->job->org_id;
								$task_batch->save();
								$wmstask->refresh();
							}
						}
						$wmstask->batch->parent->calRemain();
						$batches[date('ymd', strtotime($wmstask->batch->date)) . ' - ' . sprintf('%02d', $wmstask->batch->batch)][] = $wmstask;
					}
					$ps = [];
					foreach ($batches as $no => $batch) {
						$f = tempnam(Yii::app()->basePath . "/runtime", "pls");
						oPDF::renderPDF('packing_list_multi_items', ['batch' => $batch, 'no' => $no], 2, $f);
						$ps[] = $f;
					}

					flock($fp, LOCK_UN);
					fclose($fp);

					if (!empty($ps)) {
						oPDF::mergePDF($ps, 1, true, 'packing_list_multi_items.pdf');
					} else {
						echo 'no tasks';
					}
					} catch (Exception $ex) {
						throw $ex;
					}
				}
				break;
			case 'pkl':
				$plts = [];
				foreach ($model->actionTask->items as $item) {
					foreach ($item->stockLedgers as $ledger) {
						if ($ledger->location_id < 10) continue;
						$plts[$ledger->loc->name][] = $ledger;
					}
				}
				$ps = [];
				$td = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
				foreach ($plts as $name => $ledgers) {
					$tf = tempnam($td, 'pkl');
					oPDF::renderPDF('pkl', ['task' => $model, 'plt' => $name, 'ledgers' => $ledgers], 2, $tf);
					$ps[] = $tf;
				}
				if (!empty($ps)) {
					oPDF::mergePDF($ps, 1, true, 'pkl.pdf');
				}
				break;
			case 'pkl_pdf':
				$plts = [];
				foreach ($model->actionTask->items as $item) {
					foreach ($item->stockLedgers as $ledger) {
						if ($ledger->location_id < 10) continue;
						$plts[$ledger->loc->name][] = $ledger;
					}
				}
				$ps = [];
				$td = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
				foreach ($plts as $name => $ledgers) {
					$tf = tempnam($td, 'pkl_pdf');
					oPDF::renderPDF('pkl_pdf', ['task' => $model, 'plt' => $name, 'ledgers' => $ledgers], 2, $tf);
					$ps[] = $tf;
				}
				if (!empty($ps)) {
					oPDF::mergePDF($ps, 1, true, 'pkl_pdf.pdf');
				}
				break;
			case 'pkl_excel':
				$plts = [];
				foreach ($model->actionTask->items as $item) {
					foreach ($item->stockLedgers as $ledger) {
						if ($ledger->location_id < 10) continue;
						$plts[$ledger->loc->name][] = $ledger;
					}
				}

				$xls = new oExcel;
				$sheet = 0;
				foreach ($plts as $plt => $ledgers) {
					if ($sheet) {
						$xls->createSheet();
					}
					$xls->goSheet($sheet++);
					$xls->setTitle($plt);
					$i = 1;
					$xls->setLineHeight(1, 30, 50);
					$xls->setColWidth([15, 35, 50]);

					$xls->setFont('A' . $i . ':A' . $i, array('bold' => true, 'size' => 30));
					$xls->mergeCells('A' . $i . ':C' . $i);
					$xls->centerAlignment('A' . $i . ':C' . $i);
					$xls->addRow($i++, ['Shipping Mark', '', '']);

					$xls->setFont('A' . $i . ':C30', array('size' => 20));
					$xls->mergeCells('A' . $i . ':B' . $i);
					$xls->centerAlignment('A' . $i . ':C' . $i);
					$xls->addRow($i++, ['SPD审批单号:', '', $model->ref]);

					$xls->mergeCells('A' . $i . ':C' . $i);
					$xls->addRow($i++, [' ' . $model->job->customer->name]);

					$xls->addRow($i++, ['', ' Product Name', ' Expiry Date']);

					$stocks = [];
					foreach ($ledgers as $ledger) {
						if (empty($stocks[$ledger->stock_id])) {
							$stocks[$ledger->stock_id] = 0;
						}
						$stocks[$ledger->stock_id] += $ledger->qty_out;
					}
					$count = 1;
					foreach ($stocks as $id => $qty) {
						$stock = WmsStock::model()->findByPk($id);
						$pack = WmsProdPack::model()->find('prod_id = :pid AND type = 10', [':pid' => $stock->prod_id]);
						$org = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $stock->prod_id, ':oid' => $stock->org_id]);
						$cq = $stock->prod->uq2cq(intval($qty));
						$xls->wrapAlignment('B' . $i . ':B' . $i);
						$xls->addRow($i++, [' ' . $count++, ' ' . $stock->prod->name, ' ' . $stock->expiry]);
					}

					$xls->mergeCells('A' . $i . ':B' . $i);
					$xls->addRow($i++, [' Pallet No:', '',  '']);
					$xls->mergeCells('A' . $i . ':B' . $i);
					$xls->addRow($i++, [' Dimension:', '', '']);
				}

				$xls->goSheet(0);
				$xls->output('pkl_excel.xlsx');
				break;
			case 'farmland':
				if (empty($_POST)) {
					$this->render('farmland');
				} else {
					$ins = WmsStockLedger::model()->with('stock')->findAll('stock.org_id = :org_id AND t.ts >= :from AND t.ts < :to AND t.qty_in > 0 AND t.ti_id != 0 AND t.location_id > :location_id', [':org_id' => Org::ORGID_AIRSEA_FARMLAND, ':from' => $_POST['from'], ':to' => date('Y-m-d', strtotime($_POST['to'] . ' + 1 day')), ':location_id' => WmsLocation::WMS_LOCATION_MAX_MAGIC_ID]);
					$data = [];
					foreach ($ins as $in) {
						if (empty($data[date('Y-m-d', strtotime($in->ts)) . $in->stock->id])) {
							$data[date('Y-m-d', strtotime($in->ts)) . $in->stock->id] = [date('d-M', strtotime($in->ts)), $in->stock->prod->name, $in->stock->batch, date('d/m/Y', strtotime($in->stock->expiry)), 0];
						}
						$data[date('Y-m-d', strtotime($in->ts)) . $in->stock->id][4] += $in->qty_in;
					}

					$xls = new oExcel;
					$xls->setTitle('Farmland-receival');
					$xls->addRow(1, ['Top Logistics (NSW BRANCH)']);
					$xls->addRow(2, ['Establishment No: 1507']);
					$xls->addRow(3, ['RECORD 4 - Products Storage & Dispatch Log']);
					$image = new PHPExcel_Worksheet_Drawing;
					$image->setPath('images/PCAE_Logo.png');
					$image->setCoordinates('B1');
					$image->setWidth(100);
					$image->setHeight(50);
					$image->setWorksheet($xls->getActiveSheet());

					$i = 4;
					$xls->wrapAlignment('A1:O1000');
					$xls->setLineHeight(1, 1000, 30);
					$xls->setColWidth([13,7,30,30,30,35,13,13,13,20,13,13,20,30,13]);
					$xls->addRow($i++, ['Date', 'Time', 'Supplier’ s SLI,Invoice and/or packing list No √or×', 'Transfer Cert. and/or Declarati on of Compliance √or×', 'Product', 'Batch No', 'Use by Date', 'No units in Stock', 'Temp(C)', 'Visual Check √or×', 'Accepted Rejected √or×', 'Designated Storage Area e.g Dry Store', 'Corrective Action Taken eg List items returned - Report to Supervior/Manager - Contacted supplier', 'Staff Initials']);
					foreach ($data as $line) {
						$xls->addRow($i++, [$line[0], '', '×', '√', $line[1], $line[2], $line[3], $line[4], 'DRY AMBIENT', '√', '√', 'DRY STONE', '', '']);
						// $image = new PHPExcel_Worksheet_Drawing;
						// $image->setPath('images/antonio_sign.png');
						// $image->setWidth(100);
						// $image->setHeight(30);
						// $image->setCoordinates('O' . ($i-1));
						// $image->setWorksheet($xls->getActiveSheet());
					}

					$outs = WmsStockLedger::model()->with('stock', 'taskItem.task.mainTask')->findAll('stock.org_id IN (' . implode(',', [Org::ORGID_AIRSEA_FARMLAND, Org::ORGID_AIRSEA_HHGROUP, Org::ORGID_AIRSEA_SWISSEAU]) . ') AND t.ts >= :from AND t.ts < :to AND t.qty_out > 0 AND t.ti_id != 0 AND (mainTask.bwf & 16) = 0 AND t.location_id > :location_id', [':from' => $_POST['from'], ':to' => date('Y-m-d', strtotime($_POST['to'] . ' + 1 day')), ':location_id' => WmsLocation::WMS_LOCATION_MAX_MAGIC_ID]);
					$data = [];
					foreach ($outs as $out) {
						if (empty($data[date('Y-m-d', strtotime($out->ts)) . $out->stock->id])) {
							$data[date('Y-m-d', strtotime($out->ts)) . $out->stock->id] = [date('d-M', strtotime($out->ts)), $out->stock->prod->name, $out->stock->batch, date('d/m/Y', strtotime($out->stock->expiry)), 0, 0];
						}
						$data[date('Y-m-d', strtotime($out->ts)) . $out->stock->id][5] += $out->qty_out;

						$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $out->stock->prod_id]);
						$data[date('Y-m-d', strtotime($out->ts)) . $out->stock->id][4] += number_format($out->qty_out / intval(!empty($pp->qty) ? $pp->qty : 1), 2, '.', '');
					}

					$xls->createSheet();
					$xls->goSheet(1);
					$xls->setTitle('Farmland-dispatch');
					$xls->addRow(1, ['Top Logistics (NSW BRANCH)']);
					$xls->addRow(2, ['Establishment No: 1507']);
					$xls->addRow(3, ['RECORD 4 - Products Storage & Dispatch Log']);
					$image = new PHPExcel_Worksheet_Drawing;
					$image->setPath('images/PCAE_Logo.png');
					$image->setCoordinates('B1');
					$image->setWidth(100);
					$image->setHeight(50);
					$image->setWorksheet($xls->getActiveSheet());

					$i = 4;
					$xls->wrapAlignment('A1:P1000');
					$xls->setLineHeight(1, 1000, 30);
					$xls->setColWidth([13,7,20,60,13,13,13,13,13,13,13,20,20,20,30,30]);
					$xls->addRow($i++, ['Date', 'Time', 'Shipment Identifier', 'Product Description', 'Batch No', 'Production Date', 'Expiry Date', 'Number of outer Package / Type', 'Number of inner Package / Type', 'Temp(C)', 'Visual Check √or×', 'Designated Storage Area e.g Dry Store', 'Dispatch Date', 'Document Required for Dispatch available? i.e. Transfer Certificate / Exporter Declaration / Health Certificate / Permit Number', 'Corrective Action Taken eg List items returned - Report to Supervior/Manager - Contacted supplier', 'Staff Initials']);
					foreach ($data as $line) {
						$xls->addRow($i++, [$line[0], '', '', $line[1], $line[2], '', $line[3], $line[4], $line[5], 'DRY AMBIENT', '√', 'Dry Store', $line[0], '', '', '']);
						// $image = new PHPExcel_Worksheet_Drawing;
						// $image->setPath('images/antonio_sign.png');
						// $image->setWidth(100);
						// $image->setHeight(30);
						// $image->setCoordinates('P' . ($i-1));
						// $image->setWorksheet($xls->getActiveSheet());
					}

					$xls->goSheet(0);
					$xls->output('farmland.xlsx');
				}
				break;
			case 'gatepass':
				$xls = new oExcel;
				$i = 4;
				$xls->setColWidth(array(15, 30, 15, 15, 15, 15, 15, 10, 15, 15, 15, 20, 30));
				$xls->addRow($i++, ['Gate Pass document']);
				$xls->addRow($i++, ['From: Top Logistics']);
				$xls->addRow($i++, ['AWBN / booking no.:', @$model->mdata['awbn']]);
				$xls->addRow($i++, ['ABM PO no.:', $model->ref]);
				$xls->addRow($i++, array($model->getType(), 'Job #' . $model->job->no, $model->getNo()));
				$xls->addRow($i++, array('Date: ' . (!empty($model->compl) ? date('Y-m-d', strtotime($model->compl)) : ''), 'Container: ' . @$model->mdata['ctn_no'], 'Type: ' . @$model->mdata['ctn_size']));
				$xls->addRow($i++, array('Pallet', 'Prod. Name', 'Prod. Outer', 'Prod. EAN', 'Expiry Date', 'Batch', 'Cartons', 'Unit', 'Weight'));
				$rows = [];
				$plt = [];

				switch ($model->type) {
					case 2030:
					case 3010:
						foreach ($model->actionTask->items as $k => $itm) {
							if (empty($itm->mdata['pli']) || $itm->del == 1) {
								continue;
							}

							if (!empty($itm->mdata['pl'])) {
								$pallet = WmsLocation::model()->find('name = :name', [':name' => $itm->mdata['pl']]);
							} else if (!empty($itm->mdata['pli'])) {
								$pallet = WmsLocation::model()->findByPk($itm->mdata['pli']);
							}

							$rs = WmsStockLocation::model()->findAll('location_id = :pli', [':pli' => $itm->mdata['pli']]);
							foreach ($rs as $j => $r) {
								$wsl = WmsStockLedger::model()->with('taskItem')->find('t.ti_id = :tid AND t.location_id = :lid AND t.stock_id = :sid AND t.l2_id = 5 AND t.qty_out > 0 AND taskItem.del = 0', [':tid' => $itm->id, ':lid' => $r->location_id, ':sid' => $r->stock_id]);
								$uq = $wsl->qty_out;
								if (empty($uq)) {
									continue;
								}

								//not belong to this org
								if ($wsl->stock->org_id != $model->job->org_id) {
									continue;
								}

								//check if Inward stock is there
								$sql = 'SELECT SUM(qty_in) FROM wms_stock_ledger WHERE ti_id > 0 AND qty_in > 0 AND location_id = ' . $r->location_id . ' AND stock_id =' . $r->stock_id;
								$tq = Yii::app()->db->createCommand($sql)->queryScalar();

								if (empty($tq)) {
									continue;
								}

								if ($tq != $uq) {
									$wsl->qty_out = $tq;
									$wsl->save();
									WmsStock::countAll($wsl->stock_id);
									$uq = $tq;
								}
								$cq = $r->stock->prod->uq2cq(intval($uq));
								$prod_pack = WmsProdPack::model()->find("prod_id = :prod_id and type = :type", array(":prod_id" => $r->stock->prod->id, ":type" => array_search("Carton", WmsProdPack::$types)));
								if ($r->stock->prod->name != 'MISC PALLET') {
									$weight = $r->stock->prod->getCqWeight($cq, $uq) + ($j == 0 ? 10 : 0);
								} else {
									$weight = @$pallet->extra['weight'];
								}
								$sku = '';
								if (!empty($r->stock->prod->orgs)) {
									foreach ($r->stock->prod->orgs as $org) {
										if ($org->org_id == $model->job->org_id) {
											$sku = $org->sku;
										}
									}
								}
								$rows[] = [$pallet->name, $r->stock->prod->name, '="' . @$prod_pack->barcode . '"', '="' . $r->stock->prod->ean . '"', $r->stock->expiry, $r->stock->batch, $cq, $uq, $weight];
							}
							$plt[] = $pallet->name;
						}
						break;
					case 3020:
					case 3030:
						foreach ($model->actionTask->items as $k => $itm) {
							if (empty($itm->mdata['pli']) || $itm->del == 1) {
								continue;
							}

							if (!empty($itm->mdata['pl'])) {
								$pallet = WmsLocation::model()->find('name = :name', [':name' => $itm->mdata['pl']]);
							} else if (!empty($itm->mdata['pli'])) {
								$pallet = WmsLocation::model()->findByPk($itm->mdata['pli']);
							}

							$wsl = WmsStockLedger::model()->with('taskItem')->find('t.ti_id = :tid', [':tid' => $itm->id]);
							$uq = $wsl->qty_out;
							if (empty($uq)) {
								continue;
							}

							//not belong to this org
							if ($wsl->stock->org_id != $model->job->org_id) {
								continue;
							}

							$cq = $wsl->stock->prod->uq2cq(intval($uq));
							$prod_pack = WmsProdPack::model()->find("prod_id = :prod_id and type = :type", array(":prod_id" => $wsl->stock->prod->id, ":type" => array_search("Carton", WmsProdPack::$types)));
							$sku = '';
							if (!empty($wsl->stock->prod->orgs)) {
								foreach ($wsl->stock->prod->orgs as $org) {
									if ($org->org_id == $model->job->org_id) {
										$sku = $org->sku;
									}
								}
							}
							$rows[] = [$pallet->name, $wsl->stock->prod->name, '="' . @$prod_pack->barcode . '"', '="' . $wsl->stock->prod->ean . '"', $wsl->stock->expiry, $wsl->stock->batch, $cq, $uq, 0];
							$plt[] = $pallet->name;
						}
						break;
				}

				foreach ($rows as $r) {
					$xls->addRow($i++, $r);
				}
				$li = $i - 1;
				$xls->addRow($i++, [sizeof(array_unique($plt)), '', '', '', '', '', '=SUM(G4:G' . $li . ')', '=SUM(H4:H' . $li . ')', '=SUM(I4:I' . $li . ')']);
				$i = $i + 2;
				$xls->addRow($i, ['', 'Driver\'s Name:']);

				$image = new PHPExcel_Worksheet_Drawing;
				$image->setPath('images/PCAE_Logo.png');
				$image->setCoordinates('A1');
				$image->setWidth(100);
				$image->setHeight(50);
				$image->setWorksheet($xls->getActiveSheet());

				$driver = FileRepo::model()->find('fid = :fid AND type = :type AND name LIKE "Driver%"', [':fid' => $model->id, ':type' => FileRepo::WMS_TASK_ATTACHMENT]);
				if (!empty($driver)) {
					$image = new PHPExcel_Worksheet_Drawing;
					$image->setPath('protected/filerepo/' . substr($driver->hash, 0, 2) . '/' . $driver->hash);
					$image->setCoordinates('C' . $i);
					$image->setWidth(100);
					$image->setHeight(50);
					$image->setWorksheet($xls->getActiveSheet());
					$xls->addRow($i+1, ['', 'Date: ' . date('Y-m-d', strtotime($driver->date))]);
				}

				$xls->output('gate_pass_' . $model->getNo() . '.xlsx');
				break;
			case 'profit':
				if (empty($_POST)) {
					$this->render('delivery_profit_rpt');
				} else {
					$fromdate = (!empty($_POST['fromdate']) ? $_POST['fromdate'] : '1970-01-01') . ' 00:00:00';
					$todate = (!empty($_POST['todate']) ? $_POST['todate'] : date('Y-m-d')) . ' 23:59:59';
					if (empty($_POST['agent_id'])) {
						echo 'Please choose agent';
						return;
					}

					$lines = InvLine::model()->with('invoice')->findAll('invoice.to_id = :org_id AND invoice.type = :type AND invoice.status NOT IN (8,10) AND t.det = "Delivery"', [':org_id' => $_POST['agent_id'], ':type' => Invoice::INVOICE_TYPE_WMS_INVOICE]);
					$data = [];
					$zones = ['N0' => 'Metro', 'N1' => 'Metro', 'GF' => 'Metro', 'WG' => 'Metro', 'NC' => 'Metro', 'CB' => 'Metro', 'N3' => 'Metro', 'N4' => 'Metro', 'V0' => 'Metro', 'V1' => 'Metro', 'GL' => 'Metro', 'BR' => 'Metro', 'V3' => 'Metro', 'Q0' => 'Metro', 'Q1' => 'Metro', 'IP' => 'Metro', 'GC' => 'Metro', 'Q5' => 'Metro', 'SC' => 'Metro', 'S0' => 'Metro', 'W0' => 'Metro', 'W1' => 'Metro', 'N2' => 'Country', 'V2' => 'Country', 'Q2' => 'Country', 'Q3' => 'Country', 'Q4' => 'Country', 'S1' => 'Country', 'S2' => 'Country', 'W2' => 'Country', 'W3' => 'Country', 'T0' => 'Country', 'T1' => 'Country', 'NT1' => 'Country', 'NT2' => 'Country', 'NF' => 'Country', 'W4' => 'Country', 'AAT' => 'Country'];
					$error = [];
					$orgrate = OrgRate::model()->findByPk(ImportChargeCode::SYDNEY_AUPOST_ID);
					foreach ($lines as $inv) {
						$task = WmsTask::model()->findByPk($inv->fid);
						if (empty($task->mdata['courier']) || $task->mdata['courier'] != Org::ORGID_COURIER_AUPOST) continue;
						if (strtotime($task->mainTask->compl_time) > strtotime($todate) || strtotime($task->mainTask->compl_time) < strtotime($fromdate)) continue;

						$zone = ZoneMap::model()->find('org_id = :org_id AND pc_lo <= :postcode AND pc_hi >= :postcode', [':org_id' => Org::ORGID_COURIER_AUPOST, ':postcode' => $task->mdata['cnee']['postcode']]);

						$bill = BillingLine::model()->find('billing_ref = :ref AND status != 11', [':ref' => $task->getNo()]);
						if (empty($zones[$zone->z1])) continue;
						if (!empty($bill)) {
							if (empty($data[$task->mdata['cnee']['state']][$zones[$zone->z1]])) {
								$data[$task->mdata['cnee']['state']][$zones[$zone->z1]] = ['inv' => 0, 'bill' => 0, 'pkg' => 0];
							}
							$data[$task->mdata['cnee']['state']][$zones[$zone->z1]]['inv'] += $inv->amount - $inv->gst;
							$data[$task->mdata['cnee']['state']][$zones[$zone->z1]]['bill'] += $bill->actual_amount;
							$data[$task->mdata['cnee']['state']][$zones[$zone->z1]]['pkg'] += 1;
						} else {
							if (empty($data[$task->mdata['cnee']['state']][$zones[$zone->z1]])) {
								$data[$task->mdata['cnee']['state']][$zones[$zone->z1]] = ['inv' => 0, 'bill' => 0, 'pkg' => 0];
							}
							$data[$task->mdata['cnee']['state']][$zones[$zone->z1]]['inv'] += $inv->amount - $inv->gst;
							$weight = 0;
							$pkgs = json_decode($task->mainTask->packTask->mdata['pkg']);
							if (!is_array($pkgs)) {
								$error[] = $task->getNo() . ' is not array';
							} else {
								foreach ($pkgs as $pkg) {
									$weight += $pkg->wt;
								}
								$packs = count($pkgs);
								$bill = ImcoConsol::getCourierCostPrice($orgrate, $task->mdata['cnee']['postcode'], $weight, $packs);
								if ($bill > 0) {
									$data[$task->mdata['cnee']['state']][$zones[$zone->z1]]['bill'] += $bill;
								} else {
									$error[] = $task->getNo();
								}
								$data[$task->mdata['cnee']['state']][$zones[$zone->z1]]['pkg'] += 1;
							}
						}
					}

					$xls = new oExcel;
					$i = 1;
					$xls->setColWidth([20,20,20,20,20,20,20]);
					$xls->addRow($i++, ['State', 'Metro Rev', 'Metro Cost', 'Metro PKG', 'Country Rev', 'Country Cost', 'Country PKG']);
					foreach ($data as $state => $item) {
						$xls->addRow($i++, [$state, @$item['Metro']['inv'], @$item['Metro']['bill'], @$item['Metro']['pkg'], @$item['Country']['inv'], @$item['Country']['bill'], @$item['Country']['pkg']]);
					}
					$xls->centerAlignment('A1:G' . ($i-1));
					$i += 2;
					$xls->mergeCells('A' . $i . ':G' . $i);
					$xls->addRow($i, [implode('    ', $error)]);
					$xls->output('profit.xlsx');
				}
				break;
		}
	}

	public function actionBatchExport()
	{
		switch ($_GET['t']) {
			case 'rpt_reaco':
				if (!empty($_GET['ids'])) {
					$models = WmsTask::model()->findAll('id IN (' . implode(',', $_GET['ids']) . ') AND type IN (1010,1020)');
				} else if (!empty($_GET['WmsTask'])) {
					$model = new WmsTask('search');
					$model->unsetAttributes();
					$model->attributes = $_GET['WmsTask'];
					$models = $model->search(false)->getData();
					if (sizeof($models) > 200) {
						echo 'Number of tasks is larger than 200';
						return;
					}
				} else {
					echo 'Please select task or input filter';
					return;
				}
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth(array(30, 15, 15, 15, 15, 10, 10, 15, 10, 15, 10, 15, 10, 10, 30));
				$xls->addRow($i++, array('Comparison Job'));
				$xls->addRow($i++, array('Date: ' . date('Y-m-d')));
				$xls->addRow($i++, array('Prod. Name', 'Prod. Brand', 'Prod. Model', 'Prod. EAN', 'Expiry Date', 'Batch', 'Request Cartons', 'Request Unit', 'Actual Cartons', 'Actual Unit', 'Carton Diff', 'Unit Diff', 'Pallets', 'Mixed Pallets', 'Notes'));
				$rows = [];
				$eb = empty($_GET['eb']) ? 0 : $_GET['eb'];
				foreach ($models as $model) {
					foreach ($model->items as $k => $itm) {
						if (empty($itm->mdata['cq']) && empty($itm->mdata['uq'])) {
							continue;
						}

						if (!empty($itm->mdata['si'])) {
							$stock = WmsStock::model()->findByPk($itm->mdata['si']);
						} else {
							$stock = WmsStock::creget($model->job->org_id, $itm->mdata);
						}

						if(!empty($itm->mdata['uq']) && empty($itm->mdata['cq'])){
							$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $stock->prod_id]);
							if(!empty($pp) && $pp->qty > 0 && (intval($itm->mdata['uq']) % $pp->qty == 0)){
								$itm->mdata['cq'] = intval($itm->mdata['uq']) / $pp->qty;
							}
						}

						$seb = $stock->getStockEB($eb);
						if (empty($rows[$seb])) {
							$rows[$seb] = [$stock->prod->name, $stock->prod->brand, $stock->prod->model, '="' . $stock->prod->ean . '"', $itm->mdata['ex'], $stock->batch, floatval(trim($itm->mdata['cq'])), floatval(trim($itm->mdata['uq'])), 0, 0, 0, 0, 0, 0, ''];
						} else {
							$rows[$seb][6] += intval(trim($itm->mdata['cq']));
							$rows[$seb][7] += intval(trim($itm->mdata['uq']));
						}
					}
				}

				$plts = [];
				$mplts = [];
				foreach ($models as $model) {
					foreach ($model->actionTask->items as $k => $itm) {
						if (empty($itm->mdata['cq']) && empty($itm->mdata['uq']) && empty($itm->stockLedgers)) {
							continue;
						}

						if (empty($itm->mdata['uq'])) {
							$itm->mdata['uq'] = 0;
							foreach ($itm->stockLedgers as $ledger) {
								if ($ledger->location_id > WmsLocation::WMS_LOCATION_MAX_MAGIC_ID) {
									$itm->mdata['uq'] += intval($ledger->qty_out);
								}
							}
						}

						if (!empty($itm->mdata['si'])) {
							$stock = WmsStock::model()->findByPk($itm->mdata['si']);
						} else {
							$stock = WmsStock::creget($model->job->org_id, $itm->mdata);
						}

						if(!empty($itm->mdata['uq']) && empty($itm->mdata['cq'])){
							$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $stock->prod_id]);
							if(!empty($pp) && $pp->qty > 0 && (intval($itm->mdata['uq']) % $pp->qty == 0)){
								$itm->mdata['cq'] = intval($itm->mdata['uq']) / $pp->qty;
							}
						}
						
						if ($eb == 9) {
							$seb = $stock->org_id . '-' . $stock->prod_id;
						} else {
							$seb = $stock->org_id . '-' . $stock->prod_id . $itm->mdata['ex'] . $stock->batch;
						}
						if (empty($rows[$seb])) {
							$rows[$seb] = [$stock->prod->name, $stock->prod->brand, $stock->prod->model, '="' . $stock->prod->ean . '"', $itm->mdata['ex'], $stock->batch, 0, 0, floatval(trim($itm->mdata['cq'])), floatval(trim($itm->mdata['uq'])), 0, 0, 1, 0, $itm->mdata['nt']];
						} else {
							$rows[$seb][8] += intval(trim($itm->mdata['cq']));
							$rows[$seb][9] += intval(trim($itm->mdata['uq']));
							$rows[$seb][14] .= $itm->mdata['nt'];
						}
						if (!isset($plts[$seb])) {
							$plts[$seb] = [];
						}

						$plts[$seb][] = $itm->mdata['pl'];
						if (WmsStockLocation::isMixedPallet($itm->mdata['pl'])) {
							if (!isset($plts[$seb])) {
								$mplts[$seb] = [];
							}

							$mplts[$seb][] = $itm->mdata['pl'];
						}
					}
				}

				function md_array_unique($a) {
					$ra = [];
					foreach ($a as $k => $v) {
						if (is_array($v)) {
							$ra = array_merge($ra, md_array_unique($v));
						} else {
							$ra[] = $v;
						}
					}
					return array_unique($ra);
				};
				ksort($rows);
				foreach ($rows as $seb => $r) {
					if (!empty($_GET['diffonly']) && $r[7] == $r[9]) continue;

					$r[10] = '=I' . $i . '-G' . $i;
					$r[11] = '=J' . $i . '-H' . $i;
					$r[12] = empty($plts[$seb]) ? 0 : sizeof(array_unique($plts[$seb]));
					$r[13] = empty($mplts[$seb]) ? 0 : sizeof(array_unique($mplts[$seb]));
					$xls->addRow($i++, $r);
				}
				$li = $i - 1;
				$xls->addRow($i++, ['', '', '', '', '', '', '=SUM(G4:G' . $li . ')', '=SUM(H4:H' . $li . ')', '=SUM(I4:I' . $li . ')', '=SUM(J4:J' . $li . ')', '=SUM(K4:K' . $li . ')', '=SUM(L4:L' . $li . ')', sizeof(md_array_unique($plts)), sizeof(md_array_unique($mplts))]);
				$xls->output('comparison_' . $model->job->no . '-' . $model->getNo() . '.xlsx');
				break;
			case 'rpt_reaco_2':
				$models = WmsTask::model()->findAll('id IN (' . implode(',', $_GET['ids']) . ') AND type IN (1010,1020)');
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth(array(30, 15, 15, 15, 15, 10, 10, 15, 10, 15, 10, 15, 10, 10, 30));
				$eb = empty($_GET['eb']) ? 0 : $_GET['eb'];

				function md_array_unique($a) {
					$ra = [];
					foreach ($a as $k => $v) {
						if (is_array($v)) {
							$ra = array_merge($ra, md_array_unique($v));
						} else {
							$ra[] = $v;
						}
					}
					return array_unique($ra);
				};

				foreach ($models as $model) {
					$rows = [];
					foreach ($model->items as $k => $itm) {
						if (empty($itm->mdata['cq']) && empty($itm->mdata['uq'])) {
							continue;
						}

						if (!empty($itm->mdata['si'])) {
							$stock = WmsStock::model()->findByPk($itm->mdata['si']);
						} else {
							$stock = WmsStock::creget($model->job->org_id, $itm->mdata);
						}

						if(!empty($itm->mdata['uq']) && empty($itm->mdata['cq'])){
							$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $stock->prod_id]);
							if(!empty($pp) && $pp->qty > 0 && (intval($itm->mdata['uq']) % $pp->qty == 0)){
								$itm->mdata['cq'] = intval($itm->mdata['uq']) / $pp->qty;
							}
						}

						$seb = $stock->getStockEB($eb);
						if (empty($rows[$seb])) {
							$rows[$seb] = [$stock->prod->name, $stock->prod->brand, $stock->prod->model, '="' . $stock->prod->ean . '"', $itm->mdata['ex'], $stock->batch, floatval(trim($itm->mdata['cq'])), floatval(trim($itm->mdata['uq'])), 0, 0, 0, 0, 0, 0, ''];
						} else {
							$rows[$seb][6] += intval(trim($itm->mdata['cq']));
							$rows[$seb][7] += intval(trim($itm->mdata['uq']));
						}
					}

					$plts = [];
					$mplts = [];
					foreach ($model->actionTask->items as $k => $itm) {
						if (empty($itm->mdata['cq']) && empty($itm->mdata['uq']) && empty($itm->stockLedgers)) {
							continue;
						}

						if (empty($itm->mdata['uq'])) {
							$itm->mdata['uq'] = 0;
							foreach ($itm->stockLedgers as $ledger) {
								if ($ledger->location_id > WmsLocation::WMS_LOCATION_MAX_MAGIC_ID) {
									$itm->mdata['uq'] += intval($ledger->qty_out);
								}
							}
						}

						if (!empty($itm->mdata['si'])) {
							$stock = WmsStock::model()->findByPk($itm->mdata['si']);
						} else {
							$stock = WmsStock::creget($model->job->org_id, $itm->mdata);
						}

						if(!empty($itm->mdata['uq']) && empty($itm->mdata['cq'])){
							$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $stock->prod_id]);
							if(!empty($pp) && $pp->qty > 0 && (intval($itm->mdata['uq']) % $pp->qty == 0)){
								$itm->mdata['cq'] = intval($itm->mdata['uq']) / $pp->qty;
							}
						}
						
						if ($eb == 9) {
							$seb = $stock->org_id . '-' . $stock->prod_id;
						} else {
							$seb = $stock->org_id . '-' . $stock->prod_id . $itm->mdata['ex'] . $stock->batch;
						}
						if (empty($rows[$seb])) {
							$rows[$seb] = [$stock->prod->name, $stock->prod->brand, $stock->prod->model, '="' . $stock->prod->ean . '"', $itm->mdata['ex'], $stock->batch, 0, 0, floatval(trim($itm->mdata['cq'])), floatval(trim($itm->mdata['uq'])), 0, 0, 1, 0, $itm->mdata['nt']];
						} else {
							$rows[$seb][8] += intval(trim($itm->mdata['cq']));
							$rows[$seb][9] += intval(trim($itm->mdata['uq']));
							$rows[$seb][14] .= $itm->mdata['nt'];
						}
						if (!isset($plts[$seb])) {
							$plts[$seb] = [];
						}

						$plts[$seb][] = $itm->mdata['pl'];
						if (WmsStockLocation::isMixedPallet($itm->mdata['pl'])) {
							if (!isset($plts[$seb])) {
								$mplts[$seb] = [];
							}

							$mplts[$seb][] = $itm->mdata['pl'];
						}
					}

					$diffs = [];
					foreach ($rows as $seb => $r) {
						if ($r[7] == $r[9]) continue;
						$diffs[] = $r;
					}
					if (empty($diffs)) continue;

					$xls->addRow($i++, array('Comparison Job #' . $model->job->no, $model->getNo()));
					$xls->addRow($i++, array('Date: ' . date('Y-m-d')));
					$xls->addRow($i++, array('Prod. Name', 'Prod. Brand', 'Prod. Model', 'Prod. EAN', 'Expiry Date', 'Batch', 'Request Cartons', 'Request Unit', 'Actual Cartons', 'Actual Unit', 'Carton Diff', 'Unit Diff', 'Pallets', 'Mixed Pallets', 'Notes'));
					ksort($diffs);
					foreach ($diffs as $seb => $r) {
						$r[10] = '=I' . $i . '-G' . $i;
						$r[11] = '=J' . $i . '-H' . $i;
						$r[12] = empty($plts[$seb]) ? 0 : sizeof(array_unique($plts[$seb]));
						$r[13] = empty($mplts[$seb]) ? 0 : sizeof(array_unique($mplts[$seb]));
						$xls->addRow($i++, $r);
					}
					$li = $i - 1;
					// $xls->addRow($i++, ['', '', '', '', '', '', '=SUM(G4:G' . $li . ')', '=SUM(H4:H' . $li . ')', '=SUM(I4:I' . $li . ')', '=SUM(J4:J' . $li . ')', '=SUM(K4:K' . $li . ')', '=SUM(L4:L' . $li . ')', sizeof(md_array_unique($plts)), sizeof(md_array_unique($mplts))]);
					$xls->addRow($i++, []);
				}
				$xls->output('comparison.xlsx');
				break;
		}
	}

	public function actionPrint($id)
	{
		$model = $this->loadModel($id);
		$pt = WmsTask::model()->find('type = 3210 AND link_id = :t', [':t' => $model->link_id]);
		$pkg = 0;
		$tw = 0;
		$fw_wt = [];
		$rs = [];
		if (!empty($pt) && !empty($pt->mdata['pkg'])) {
			foreach (json_decode($pt->mdata['pkg'], true) as $pk) {
				if (empty($pk['wt'])) {
					continue;
				}

				$fw_wt[] = $pk['wt']; //store each pack's weight;
				$tw += $pk['wt'];
				$pkg++;
			}
		} else {
			$pkg = 1;
		}
		switch ($_GET['t']) {
			case 'pack_list':
				oPDF::renderPDF('packing_list', array('model' => $model));
				break;
			case 'delivery_order':
				oPDF::renderPDF('delivery_order', array('model' => $model));
				break;
			case 'co_label':
				// foreach ($model->mdata['shipment_id'] as $k => $v) {
				// 	if (empty($v)) unset($model->mdata['shipment_id'][$k]);
				// }

				if (empty($model->mainTask->packTask->mdata['pkg'])) {
					echo 'package amount is zero';
					Yii::app()->end();
				}

				if (empty($model->mdata['shipment_id']) || empty($model->mdata['shipment_courier_id']) || $model->mdata['shipment_courier_id'] != $model->mdata['courier'] ||
					($model->mdata['courier'] == Org::ORGID_COURIER_FASTWAY && sizeof($model->mdata['shipment_id']) != $pkg)) {
					$rs = $model->toShipment(); //multiple shipments in array
					//ot_id = 10
					foreach($rs as $i=> $objImParcel){
						$objImParcel->ot_id = WmsTask::OT_ID_3PL;
						$objImParcel->save();
					}
				} else {
					foreach ($model->mdata['shipment_id'] as $sid) {
						$p = Shipment::model()->find('id=:id', array(':id' => $sid));
						if (!empty($p)) {
							$rs[] = $p;
						}
					}
				}

				//Eiz Toll
				if(!empty($rs) && $model->mdata['courier'] == Org::ORGID_COURIER_EIZ_TOLL ){
					$imparcelService = new ImParcelService();
					$imparcelService->getImparcelLabel( $rs);
					break;
				}
				//Allied
				if(!empty($rs) && $model->mdata['courier'] == "3702" ){
					$customerPrint=0;
					if(!empty($model->mdata['trackingno'])){
						$customerPrint=1;
					}
					$imparcelService = new ImParcelService();
					$imparcelService->getImparcelLabelAllied( $rs,$customerPrint);
					break;	
				}
				//UBI Toll & Aupost
				if(!empty($rs) && ($model->mdata['courier'] == Org::ORGID_COURIER_UBI_TOLL)){
					$imparcelService = new ImParcelService();
					$imparcelService->getImparcelLabel( $rs);
					break;
				}
				//UBI BORDER
				if(!empty($rs) && ($model->mdata['courier'] == Org::ORGID_COURIER_BORDER)){
					$imparcelService = new ImParcelService();
					$imparcelService->getImparcelLabel( $rs);
					break;
				}
				//MYTOLL
				if(!empty($rs) && ($model->mdata['courier'] == Org::ORGID_COURIER_TOLL_IPEC)){
					$imparcelService = new ImParcelService();
					$imparcelService->getImparcelLabel( $rs,$rs->pkg);
					break;
				}

				if (!empty($rs)) {
					if ($rs[0]->type == 10) {
						if (empty($rs[0]->id)) {
							print_r($rs[0]->getErrors());
						} else if (!empty($rs[0]->trans) && $rs[0]->trans[sizeof($rs[0]->trans) - 1]->org_id == Org::ORGID_COURIER_SENDLE) {
							$sendle = new SendleAPI();
							$fs = [];
							foreach ($rs as $r) {
								$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
								file_put_contents($f, $sendle->getLabel($r));
								$fs[] = $f;
							}
							oPDF::mergePDF($fs, 1, true, 'sendle.pdf');
						} else if (!empty($rs[0]->mdata['ap_lbls'])) {
							$apa = new AusPostAPI('syd', true, false);
							$fs = [];
							foreach ($rs as $p) {
								foreach($p->mdata['ap_lbls'] as $lbl){
									$t = 0;
									while($t < 3){
										if($t > 0) sleep(1);
										$r3 = $apa->getLabel($lbl);
										foreach($r3->labels as $l){
											if(empty($l->url)) continue;
											$f = tempnam(Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR, 'pp');
											file_put_contents($f, file_get_contents($l->url));
											$fs[] = $f;
											break 2;
										}
										$t++;
									}
								}
							}
							oPDF::mergePDF($fs, 1, true, 'ap_int.pdf');
						} else {
							oPDF::renderPDF('label_A6', array('rs' => $rs), 1, 'label.pdf');
							// $this->redirect(['imParcel/label', 'id' => $p->id]);
						}
					} elseif ($rs[0]->type == 20) {
						$p = $rs[0];
						if (empty($p->id)) {
							print_r($p->getErrors());
						} else {
							$this->redirect(['exParcel/label', 'id' => $p->id]);
						}
					}
				}
				break;
			case 'ticket':
				if ($model->type == 2110) {
					if (empty($model->mdata['pin'])) {
						if (empty($_POST)) {
							$this->render('ticket', array('model' => $model));
						} else {
							$model->mdata['pin'] = substr(md5($model->id), 6, 6);
							$model->mdata['date'] = $_POST['date'];
							$model->update('meta');
							oPDF::renderPDF('wmstask_ticket_pickup', array('model' => $model), 1, 'PCA Express Pickup Ticket ' . $model->no . '.pdf');
						}
					} else {
						oPDF::renderPDF('wmstask_ticket_pickup', array('model' => $model), 1, 'PCA Express Pickup Ticket ' . $model->no . '.pdf');
					}
				} else if (in_array($model->type, [1010, 1020])) {
					if (empty($model->mdata['pi_expect'])) {
						oPDF::renderPDF('wmstask_ticket_stockin', array('model' => $model), 1, 'PCA Express Stockin Ticket ' . $model->no . '.pdf');
					} else {
						for ($i = 1; $i <= $model->mdata['pi_expect']; $i++) {
							$n = 'CW' . sprintf('%06d', $model->id) . sprintf('%04d', $i);
							$plt = WmsLocation::model()->find('name = :n AND code = :n AND type = 50 AND status = 1', [':n' => $n]);
							if (empty($plt)) {
								$plt = new WmsLocation;
								$plt->name = $n;
								$plt->code = $n;
								$plt->type = 50;
								$plt->wid = 106;
								$plt->status = 1;
								$plt->save();
							}
						}
						oPDF::renderPDF('wmstask_ticket_stockin', array('model' => $model), 1, 'PCA Express Stockin Ticket ' . $model->no . '.pdf');
					}
				}
				break;
		}
	}

	public function actionSwitchType($id)
	{
		$model = $this->loadModel($id);
		$model->type = $_GET['type'];
		$model->save();
		$this->ajaxResult($model);
	}

	public function actionPickSelect($id)
	{
		$model = WmsJob::model()->findByPk($id);

		$this->render('pick_sku', array(
			'model' => $model,
		));
	}

	public function actionItemUpdate($id)
	{
		$model = WmsTaskItem::model()->findByPk($id);
		if (!empty($_POST)) {
			foreach ($_POST['mdata'] as $k => $v) {
				$model->mdata[$k] = $v;
			}

			if (empty($_POST['mdata']['cq'])) {
				$model->mdata['cq'] = 0;
			}
			if (empty($_POST['mdata']['uq'])) {
				$model->mdata['uq'] = 0;
			}

			if (!empty($model->mdata['gi']) && empty($model->mdata['uq']) && !empty($model->mdata['cq'])) {
				$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $model->mdata['gi']]);
				if (!empty($pp) && !empty($pp->qty)) {
					$model->mdata['uq'] = intval($model->mdata['cq']) * $pp->qty;
				}
			}
			$model->save();
			$this->ajaxResult($model);
		}
		$this->render('item', array(
			'model' => $model,
		));
	}

	public function actionQuickStockOut($id)
	{
		if (empty($_POST)) {
			$model = $this->loadModel($id);
			$products = [];
			foreach ($model->actionTask->items as $item) {
				if (empty($products[$item->mdata['gi']])) {
					$products[$item->mdata['gi']] = ['name' => $item->mdata['gn']];
				}
				$products[$item->mdata['gi']]['ids'][] = $item->id;
			}
			$this->render('quick_stockout', array('products' => $products));
		} else {
			$model = $this->loadModel($id);

			$job = $model->job;
			$task = new WmsTask;
			$task->job_id = $job->id;
			$task->ref = $_POST['ref'];
			$task->type = $_POST['type'];
			$task->is_request = 1;
			$task->op_id = Yii::app()->user->id;
			$task->status = 20;
			$items = [];
			if ($task->type == 3030) {
				foreach ($_POST['WmsTaskItem'] as $ti_ids => $qty) {
					$ti_ids = explode(',', $ti_ids);
					foreach ($ti_ids as $ti_id) {
						if ($qty) {
							$sl = WmsStockLedger::model()->find('ti_id = :ti_id', array(':ti_id' => $ti_id));
							$item = array(
								'si' => $sl->stock->id,
								'sn' => $sl->stock->prod->name,
								'pq' => '',
								'cq' => '',
								'uq' => min($qty, $sl->qty_in, $sl->stock->qty - $sl->stock->qty_res),
								'pli' => '',
								'pl' => '',
								'nt' => '',
							);
							$qty -= min($qty, $sl->qty_in, $sl->stock->qty - $sl->stock->qty_res);
							$items[] = $item;
						}
					}
				}
				$task->new_items = $items;

				if (empty($task->getErrors())) {
					$task->save();

					$ac_task = $task->actionTask;
					foreach ($task->items as $item) {
						$stock = WmsStock::model()->findByPk($item->mdata['si']);
						$sls = $stock->locs;
						$qty = $item->mdata['uq'];
						foreach ($sls as $sl) {
							if ($qty) {
								$itm = new WmsTaskItem;
								$itm->task_id = $ac_task->id;
								$itm->mdata = array(
									'si' => $item->mdata['si'],
									'sn' => $item->mdata['sn'],
									'uq' => ($qty > $sl->qty ? $sl->qty : $qty),
									'pli' => $sl->loc->id,
									'pl' => $sl->loc->code,
								);
								$itm->save();
								$qty -= $qty > $sl->qty ? $sl->qty : $qty;
							}
						}
					}
					$ac_task->save();
					$task->compl_time = date('Y-m-d H:i:s');
					$task->status = array_search('WIP', WmsTask::$states);
					$task->save();
				}
			} else if ($task->type == 2030) {
				if (empty($task->getErrors())) {
					$task->save();

					$ac_task = $task->actionTask;
					foreach ($model->actionTask->items as $item) {
						$location = $item->stockLedgers[0]->loc;
						if ($item->mdata['pl'] == $location->name) {
							$itm = new WmsTaskItem;
							$itm->task_id = $ac_task->id;
							$itm->mdata = array(
								'pli' => $location->id,
								'pl' => $location->name,
								'pq' => 1,
								'nt' => '',
							);
							$itm->save();
						}
					}
					$task->compl_time = date('Y-m-d H:i:s');
					$task->status = array_search('WIP', WmsTask::$states);
					$task->save();
				}
			}

			$this->ajaxResult($task);
		}
	}

	public function actionQuickOutTask($id)
	{
		$model = $this->loadModel($id);
		if (empty($_POST)) {
			$this->render('quick_outtask', ['model' => $model]);
		} else {
			$task = new WmsTask;
			$task->job_id = $model->job_id;
			if (!empty($_POST['all'])) {
				$task->ref = $model->ref . ' - 所有口岸'; 
			} else if (!empty($_POST['port'])) {
				$task->ref = $model->ref . ' - ' . @oList::kvp('ex_port')[$_POST['port']];
			}
			$task->type = $_POST['type'];
			$task->is_request = 1;
			$task->op_id = Yii::app()->user->id;
			$task->status = 20;
			$task->mdata['intaskid'] = $model->id;
			if (!empty($_POST['all'])) {
				$items = [];
				foreach ($model->actionTask->items as $item) {
					foreach ($item->stockLedgers as $ledger) {
						if ($ledger->loc->pid != 2) {
							$items[] = array(
								'si' => $ledger->stock->id,
								'sn' =>	$ledger->stock->prod->name,
								'pq' => 1,
								'cq' => '',
								'uq' => '',
								'pli' => $ledger->loc->id,
								'pl' => $ledger->loc->name,
								'nt' => '',
							);
						}
					}
				}
			} else if (!empty($_POST['port'])) {
				$items = [];
				foreach ($model->actionTask->items as $item) {
					foreach ($item->stockLedgers as $ledger) {
						if (!empty($ledger->loc->extra['port']) && $ledger->loc->extra['port'] == $_POST['port'] && $ledger->loc->pid != 2) {
							$items[] = array(
								'si' => $ledger->stock->id,
								'sn' =>	$ledger->stock->prod->name,
								'pq' => 1,
								'cq' => '',
								'uq' => '',
								'pli' => $ledger->loc->id,
								'pl' => $ledger->loc->name,
								'nt' => '',
							);
						}
					}
				}
			}

			if (empty($items)) {
				$model->addError('id', 'no items');
				$this->ajaxResult($model);
			}

			$task->new_items = $items;
			$task->save();

			if (empty($model->mdata['outtaskid'])) {
				$model->mdata['outtaskid'] = [];
			}
			$model->mdata['outtaskid'][] = $task->id;
			$model->update('meta');

			$this->ajaxResult($task, array('id' => $task->id));
		}
	}

	public function actionQuickComplete($id)
	{
		if (empty($_GET['confirm'])) {
			$model = $this->loadModel($id);
			$this->render('quick_complete', ['model' => $model]);
		} else {
			$task = $this->loadModel($id);

			if (in_array($task->type, [3020, 3030])) {
				$ac_task = $task->actionTask;
				$items_has = [];
				foreach ($ac_task->items as $item) {
					if (empty($items_has[$item->mdata['si']])) {
						$items_has[$item->mdata['si']] = 0;
					}
					$items_has[$item->mdata['si']] += intval($item->mdata['uq']);
				}

				foreach ($task->items as $item) {
					if (empty($item->mdata['si'])) {
						continue;
					}
					$stock = WmsStock::model()->findByPk($item->mdata['si']);
					$sls = $stock->locs;
					$qty = max(intval($item->mdata['uq']) - @$items_has[$item->mdata['si']], 0);
					foreach ($sls as $sl) {
						if (!empty($item->mdata['pli']) && $sl->location_id != $item->mdata['pli']) continue;
						if ($qty) {
							$itm = new WmsTaskItem;
							$itm->task_id = $ac_task->id;
							$itm->mdata = array(
								'si' => $item->mdata['si'],
								'sn' => $item->mdata['sn'],
								'uq' => min($qty, $sl->qty),
								'pli' => $sl->loc->id,
								'pl' => $sl->loc->code,
							);
							$itm->save();
							$qty -= min($qty, $sl->qty);
						}
					}
				}
				$ac_task->save();
				$task->status = array_search('Completed', WmsTask::$states);
				$task->save();
			} else if (in_array($task->type, [3010])) {
				$ac_task = $task->actionTask;
				$items_has = [];
				$items_not = [];
				foreach ($ac_task->items as $item) {
					if (empty($items_has[$item->mdata['pli']])) {
						$items_has[$item->mdata['pli']] = 1;
					}
				}

				foreach ($task->items as $item) {
					if (empty($item->mdata['pli']) && !empty($item->mdata['pl'])) {
						$pl = WmsLocation::model()->find('code = :code', [':code' => $item->mdata['pl']]);
						if (!empty($pl)) {
							$item->mdata['pli'] = $pl->id;
							$item->update('meta');
						}
					}
					if (empty($items_has[$item->mdata['pli']])) {
						$items_not[$item->mdata['pli']] = 1;
					}
				}

				foreach ($items_not as $pli => $v) {
					$loc = WmsLocation::model()->findByPk($pli);
					$sls = WmsStockLocation::model()->findAll('location_id = :loc AND qty > 0', [':loc' => $loc->id]);
					foreach ($sls as $sl) {
						$itm = new WmsTaskItem;
						$itm->task_id = $ac_task->id;
						$itm->mdata = array(
							'si' => $sl->stock->id,
							'sn' => $sl->stock->prod->name,
							'pq' => 1,
							'pli' => $loc->id,
							'pl' => $loc->name,
							'nt' => '',
						);
						$itm->save();
					}
				}

				$task->status = array_search('Completed', WmsTask::$states);
				$task->save();
			}

			$this->ajaxResult($task);
		}
	}

	public function actionInvLineGrid($id)
	{
		if (empty($_POST)) {
			// delete
			[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
			$line = InvLine::model()->findByPk($id);
			$line->delete();
			if (!empty($line->invoice)) {
				$invoice = $line->invoice;
				$invoice->getTotal();
				$invoice->save();
			}
			Yii::app()->name = $app_name;
		} else {
			$model = WmsTask::model()->findByPk($id);
			if (empty($_POST['InvLine']['tax'])) {
				$model->addError('id', 'Tax type is required');
			}
			if (!empty($model->getErrors())) {
				$this->ajaxResult($model);
			}
			if (empty($_POST['InvLine']['id'])) {
				// create
				[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
				$line = new InvLine;
				$line->inv_id = 0;
				$line->model = 'WmsInvoiceLine';
				$line->fid = $model->id;
				$line->ccode = 9999;
				$line->amount = $_POST['InvLine']['amount'];
				$line->det = $_POST['InvLine']['det'];
				$line->qty = $_POST['InvLine']['qty'];
				if (!empty($_POST['InvLine']['tax'])) {
					if (in_array($_POST['InvLine']['tax'], ['OUTPUT'])) {
						$line->gst = round($line->amount * 10, 2) / 100;
					} else {
						$line->gst = 0;
					}
				}
				$line->tax = $_POST['InvLine']['tax'];
				$line->amount += $line->gst;
				$line->mdata['items'] = [];
				$line->mdata['items'][] = [$model->getNo(), ucwords($model->ref), $model->compl_time, $model->getType() . ' - ' . $line->det, $line->amount - $line->gst, $line->qty, ($line->amount - $line->gst) * $line->qty];
				$line->save();
				Yii::app()->name = $app_name;
			} else {
				[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
				$line = InvLine::model()->findByPk($_POST['InvLine']['id']);
				$line->amount = $_POST['InvLine']['amount'];
				$line->qty = $_POST['InvLine']['qty'];
				$line->det = $_POST['InvLine']['det'];
				if (!empty($_POST['InvLine']['tax'])) {
					if (in_array($_POST['InvLine']['tax'], ['OUTPUT'])) {
						$line->gst = round($line->amount * 10, 2) / 100;
					} else {
						$line->gst = 0;
					}
				}
				$line->amount += $line->gst;
				$line->tax = $_POST['InvLine']['tax'];
				$line->mdata['items'] = [];
				$line->mdata['items'][] = [$model->getNo(), ucwords($model->ref), $model->compl_time, $model->getType() . ' - ' . $line->det, $line->amount - $line->gst, $line->qty, ($line->amount - $line->gst) * $line->qty];
				$line->save();
				Yii::app()->name = $app_name;
			}
		}
		$this->ajaxResult($line);
	}

	public function actionBulkTask()
	{
		if (empty($_POST)) {
			$this->render('bulk_task', array('t' => $_GET['t']));
		} else {
			$tasks = $_POST['tasks'];
			$tasks = preg_split('/\n|\r\n?/', $tasks);

			foreach ($tasks as $task) {
				$wmstask = WmsTask::model()->findByPk(trim(str_replace('T', '', $task)));
				if (empty($wmstask)) {
					echo json_encode(array('done' => false, 'msg' => 'Task ' . $task . ' does not exist'));
					Yii::app()->end();
				}
				if (($_GET['t'] == 'in' && !in_array($wmstask->type, [1010, 1020, 1030])) || ($_GET['t'] == 'out' && !in_array($wmstask->type, [3010, 3020, 3030, 2030]))) {
					echo json_encode(array('done' => false, 'msg' => 'Task ' . $task . ' is not a stock ' . $_GET['t'] . ' task'));
					Yii::app()->end();
				}
			}

			$first = WmsTask::model()->findByPk(trim(str_replace('T', '', $tasks[0])));
			foreach ($tasks as $task) {
				$wmstask = WmsTask::model()->findByPk(trim(str_replace('T', '', $task)));
				$wmstask->mdata['ct'][] = 'C' . $first->getNo() . count($tasks);
				$wmstask->mdata['ct'] = array_unique($wmstask->mdata['ct']);
				$wmstask->update('meta');
			}

			$this->ajaxResult($wmstask);
		}
	}

	public function actionDeleteJob($task_id, $job_id)
	{
		$wmsedi = WmsEdi::model()->find('task_id = :task_id AND job_id = :job_id', [':task_id' => $task_id, ':job_id' => $job_id]);
		if (!empty($wmsedi)) {
			$wmsedi->delete();
		}
	}

	public function actionReturn($id)
	{
		$model = $this->loadModel($id)->mainTask;
		if (empty($_POST)) {
			$this->render('return', ['model' => $model]);
		} else {
			if ($_POST['type'] == 1) {
				// 入库
				if (!empty($model->deliveryTask->mdata['return'])) {
					$model->addError('id', '已经创建退件task');
					$this->ajaxResult($model);
				}

				$task = new WmsTask;
				$task->job_id = $model->job->id;
				$task->ref = $model->no . ' 退件入库';
				$task->type = 1010;
				$task->is_request = 1;
				$task->status = 20;
				$items = [];
				foreach ($_POST['selectedItems'] as $item) {
					$item = WmsTaskItem::model()->findByPk($item);
					$stock = WmsStock::model()->findByPk($item->mdata['si']);
					$items[] = ['gi' => $stock->prod->id, 'gn' => $stock->prod->name, 'uq' => $item->mdata['uq']];
				}
				$task->new_items = $items;
				$task->mdata['return'] = true;
				$task->save();

				$model->deliveryTask->mdata['return'] = true;
				$model->deliveryTask->update('meta');
			} else if ($_POST['type'] == 2) {
				// 重派
				if (!empty($model->deliveryTask->mdata['return'])) {
					$model->addError('id', '已经创建退件task');
					$this->ajaxResult($model);
				}

				$pkgs = [];
				// foreach ($_POST['selectedItems'] as $item) {
				// 	$item = WmsTaskItem::model()->findByPk($item);
				// 	$stock = WmsStock::model()->findByPk($item->mdata['si']);
				// 	$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = :type AND qty = 1', array(':prod_id' => $stock->prod->id, ':type' => array_search('Carton', WmsProdPack::$types)));
				// 	if (empty($pack) || empty($pack->weight) || empty($pack->dims)) {
				// 		$model->addError('id', 'pack info of ' . $stock->prod->name . ' is empty');
				// 	} else {
				// 		$pkgs[] = ['wt' => $pack->weight, 'w' => $pack->dims['w'], 'h' => $pack->dims['h'], 'd' => $pack->dims['d']];
				// 	}
				// }
				if (!empty($model->packTask->mdata['pkg'])) {
					$model_pkgs = json_decode($model->packTask->mdata['pkg'], true);
					if (sizeof($model_pkgs) == 1) {
						$pkgs = $model_pkgs;
					}
				}

				if ($model->getErrors()) {
					$this->ajaxResult($model);
				}

				$task = new WmsTask;
				$task->job_id = $model->job->id;
				$task->ref = $model->no . ' return re-delivery';
				$task->type = 3030;
				$task->is_request = 1;
				$task->status = 30;
				$task->mdata['return'] = true;
				$task->save();

				$task->packTask->mdata['pkg'] = json_encode($pkgs);
				$task->packTask->update('meta');

				$task->pickupTask->type = 2120;
				// $task->pickupTask->mdata['courier'] = Org::ORGID_COURIER_STARTRACK;
				$task->pickupTask->mdata['cnee']['company'] = $model->deliveryTask->mdata['cnee']['company'];
				$task->pickupTask->mdata['cnee']['name'] = $model->deliveryTask->mdata['cnee']['name'];
				$task->pickupTask->mdata['cnee']['tel'] = $model->deliveryTask->mdata['cnee']['tel'];
				$task->pickupTask->mdata['cnee']['address'] = $model->deliveryTask->mdata['cnee']['address'];
				$task->pickupTask->mdata['cnee']['city'] = $model->deliveryTask->mdata['cnee']['city'];
				$task->pickupTask->mdata['cnee']['suburb'] = $model->deliveryTask->mdata['cnee']['suburb'];
				$task->pickupTask->mdata['cnee']['state'] = $model->deliveryTask->mdata['cnee']['state'];
				$task->pickupTask->mdata['cnee']['postcode'] = $model->deliveryTask->mdata['cnee']['postcode'];
				$task->pickupTask->mdata['cnee']['country'] = $model->deliveryTask->mdata['cnee']['country'];
				$task->pickupTask->mdata['cnee']['email'] = $model->deliveryTask->mdata['cnee']['email'];
				$task->pickupTask->save();

				$model->deliveryTask->mdata['return'] = true;
				$model->deliveryTask->update('meta');
			}
			$this->ajaxResult($model);
		}
	}

	public function actionGroupOrg($id)
	{
		$model = $this->loadModel($id);
		if (empty($_POST['agent_id'])) {
			$this->render('group_org', ['model' => $model]);
		} else {
			if (empty($model->mdata['other_org'])) {
				$model->mdata['other_org'] = [];
			}
			$model->mdata['other_org'][] = $_POST['agent_id'];
			$model->mdata['other_org'] = array_unique($model->mdata['other_org']);
			$model->update('meta');
			$this->ajaxResult($model);
		}
	}

	public function actionComplete()
	{
		if (empty($_POST)) {
			$this->render('complete');
		} else {
			if (empty($_POST['owner_id'])) {
				echo json_encode(['done' => false, 'msg' => 'Please choose agent']);
				yii::app()->end();
			} else if (empty($_POST['selectedItems'])) {
				echo json_encode(['done' => false, 'msg' => 'Please select tasks']);
				yii::app()->end();
			}

			foreach ($_POST['selectedItems'] as $task) {
				$task = WmsTask::model()->findByPk($task);
				$lastlog = Log::getLast($task);
				$task->status = 99;
				$task->compl_time = $lastlog->time;
				$task->update('status', 'compl_time');
			}

			echo json_encode(['done' => true, 'msg' => 'Successfully']);
			yii::app()->end();		
		}
	}

	public function actionDash()
	{
		function filter($task) {
			if (in_array(Yii::app()->user->id, WmsTask::$op) && ($task->job->customer->extra['op_id'] == 305 || $task->job->customer->extra['sp_id'] == 305)) {
				return false;
			}
			if ($task->job->customer->extra['op_id'] != Yii::app()->user->id && $task->job->customer->extra['sp_id'] != Yii::app()->user->id && User::model()->findByPk(Yii::app()->user->id)->type > 0) {
				return true;
			}
		}

		$list = ['before930' => [], 'after930' => [], 'wip' => [], 'complete' => []];

		$tasks = WmsTask::model()->with('createlog')->findAll(['condition' => 't.id > 80000 AND t.status = 20 AND t.is_request = 1 AND createlog.time <= "' . date('Y-m-d 09:30:00') . '"', 'order' => 't.id ASC']);
		foreach ($tasks as $task) {
			if (filter($task)) continue;
			if (empty($list['before930'][$task->job->customer->name])) {
				$list['before930'][$task->job->customer->name] = [];
			}
			$list['before930'][$task->job->customer->name][] = $task;
		}

		$tasks = WmsTask::model()->with('createlog')->findAll(['condition' => 't.id > 80000 AND t.status = 20 AND t.is_request = 1 AND createlog.time > "' . date('Y-m-d 09:30:00') . '"', 'order' => 't.id ASC']);
		foreach ($tasks as $task) {
			if (filter($task)) continue;
			if (empty($list['after930'][$task->job->customer->name])) {
				$list['after930'][$task->job->customer->name] = [];
			}
			$list['after930'][$task->job->customer->name][] = $task;
		}

		$tasks = WmsTask::model()->with('createlog')->findAll(['condition' => 't.id > 80000 AND t.status = 30 AND t.is_request = 1 AND ref NOT LIKE "stock take%"', 'order' => 't.id ASC']);
		foreach ($tasks as $task) {
			if (filter($task)) continue;
			if (empty($list['wip'][$task->job->customer->name])) {
				$list['wip'][$task->job->customer->name] = [];
			}
			$list['wip'][$task->job->customer->name][] = $task;
		}

		$tasks = WmsTask::model()->with('createlog', 'lastlog')->findAll(['condition' => 't.id > 80000 AND t.status = 99 AND t.is_request = 1 AND lastlog.time > "' . date('Y-m-d 09:30:00') . '"', 'order' => 't.id ASC']);
		foreach ($tasks as $task) {
			if (filter($task)) continue;
			if (empty($list['complete'][$task->job->customer->name])) {
				$list['complete'][$task->job->customer->name] = [];
			}
			$list['complete'][$task->job->customer->name][] = $task;
		}

		$this->render('dash', ['list' => $list]);
	}

	// public function actionDash3PL()
	// {
	// 	function filter($task) {
	// 		if ((in_array(Yii::app()->user->id, WmsTask::$op) || (isset(Yii::app()->user->grp) && Yii::app()->user->grp == 0)) && (in_array($task->job->customer->extra['op_id'], WmsTask::$op) || in_array($task->job->customer->extra['sp_id'], WmsTask::$op))) {
	// 			return false;
	// 		} else {
	// 			return true;
	// 		}
	// 	}

	// 	$tasks = WmsTask::model()->with('createlog')->findAll('createlog.time >= :time AND is_request = 1', [':time' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 7 days'))]);
	// 	$list = [];
	// 	foreach ($tasks as $task) {
	// 		if (filter($task)) continue;

	// 		if (in_array($task->type, [1010,1020])) {
	// 			foreach ($task->items as $item) {
	// 				if (empty($list[date('Y-m-d', strtotime($item->ts))]['in'])) {
	// 					$list[date('Y-m-d', strtotime($item->ts))]['in']['schd_task'] = 0;
	// 					$list[date('Y-m-d', strtotime($item->ts))]['in']['schd_unit'] = 0;
	// 					// $list[date('Y-m-d', strtotime($item->ts))]['in']['schd_carton'] = 0;
	// 				}

	// 				// if (!empty($item->mdata['cq'])) {
	// 				// 	$list[date('Y-m-d', strtotime($item->ts))]['in']['schd_carton'] += intval($item->mdata['cq']);
	// 				// } else if (!empty($item->mdata['uq'])) {
	// 					$list[date('Y-m-d', strtotime($item->ts))]['in']['schd_unit'] += intval($item->mdata['uq']);
	// 				// }
	// 			}
	// 			$list[date('Y-m-d', strtotime($item->ts))]['in']['schd_task'] += 1;

	// 			foreach ($task->actionTask->items as $item) {
	// 				if (empty($list[date('Y-m-d', strtotime($item->ts))]['in'])) {
	// 					$list[date('Y-m-d', strtotime($item->ts))]['in']['compl_task'] = 0;
	// 					$list[date('Y-m-d', strtotime($item->ts))]['in']['compl_unit'] = 0;
	// 					// $list[date('Y-m-d', strtotime($item->ts))]['in']['compl_carton'] = 0;
	// 				}

	// 				// if (!empty($item->mdata['cq'])) {
	// 				// 	$list[date('Y-m-d', strtotime($item->ts))]['in']['compl_carton'] += intval($item->mdata['cq']);
	// 				// } else if (!empty($item->mdata['uq'])) {
	// 					$list[date('Y-m-d', strtotime($item->ts))]['in']['compl_unit'] += intval($item->mdata['uq']);
	// 				// }
	// 			}
	// 			$list[date('Y-m-d', strtotime($item->ts))]['in']['compl_task'] += 1;
	// 		} else if (in_array($task->type, [3020,3030])) {
	// 			foreach ($task->items as $item) {
	// 				if (empty($list[date('Y-m-d', strtotime($item->ts))]['out'])) {
	// 					$list[date('Y-m-d', strtotime($item->ts))]['out']['schd_task'] = 0;
	// 					$list[date('Y-m-d', strtotime($item->ts))]['out']['schd_unit'] = 0;
	// 					// $list[date('Y-m-d', strtotime($item->ts))]['out']['schd_carton'] = 0;
	// 				}

	// 				// if (!empty($item->mdata['cq'])) {
	// 				// 	$list[date('Y-m-d', strtotime($item->ts))]['out']['schd_carton'] += intval($item->mdata['cq']);
	// 				// } else if (!empty($item->mdata['uq'])) {
	// 					$list[date('Y-m-d', strtotime($item->ts))]['out']['schd_unit'] += intval($item->mdata['uq']);
	// 				// }
	// 			}
	// 			$list[date('Y-m-d', strtotime($item->ts))]['out']['schd_task'] += 1;

	// 			foreach ($task->actionTask->items as $item) {
	// 				if (empty($list[date('Y-m-d', strtotime($item->ts))]['out'])) {
	// 					$list[date('Y-m-d', strtotime($item->ts))]['out']['compl_task'] = 0;
	// 					$list[date('Y-m-d', strtotime($item->ts))]['out']['compl_unit'] = 0;
	// 					// $list[date('Y-m-d', strtotime($item->ts))]['out']['compl_carton'] = 0;
	// 				}

	// 				// if (!empty($item->mdata['cq'])) {
	// 				// 	$list[date('Y-m-d', strtotime($item->ts))]['out']['compl_carton'] += intval($item->mdata['cq']);
	// 				// } else if (!empty($item->mdata['uq'])) {
	// 					$list[date('Y-m-d', strtotime($item->ts))]['out']['compl_unit'] += intval($item->mdata['uq']);
	// 				// }
	// 			}
	// 			$list[date('Y-m-d', strtotime($item->ts))]['out']['compl_task'] += 1;

	// 			if (!empty($task->deliveryTask->mdata['shipment_id'])) {
	// 				foreach ($task->deliveryTask->mdata['shipment_id'] as $id) {
	// 					$shipment = Shipment::model()->findByPk($id);
	// 					if (empty($list[date('Y-m-d', strtotime($shipment->created))]['out'])) {
	// 						$list[date('Y-m-d', strtotime($shipment->created))]['out']['compl_weight'] = 0;
	// 					}
	// 					$list[date('Y-m-d', strtotime($shipment->created))]['out']['compl_weight'] += floatval($shipment->weight);
	// 				}
	// 			}
	// 		}
	// 	}

	// 	$this->render('dash_3pl', ['list' => $list]);
	// }

	public function actionDash3PL()
	{
		if (isset($_GET['tab'])) {
			Acl::hasAccess($this->CaName . '/' . $_GET['tab'], true);
			$this->render('tab_' . $_GET['tab']);
		} else {
			$this->render('dash_3pl');
		}
	}

	public function actionDash3PLDetail()
	{
		if (!empty($_GET['act'])) {
			if ($_GET['act'] == 'Last 7 days') {
				$_GET['from'] = date('Y-m-d', strtotime(date('Y-m-d') . ' - 7 days'));
				$_GET['to'] = date('Y-m-d');
			} else if ($_GET['act'] == 'This Week') {
				$_GET['from'] = date('Y-m-d', strtotime(date('Y-m-d') . ' - ' . (date('N') - 1) . ' day'));
				$_GET['to'] = date('Y-m-d');
			} else if ($_GET['act'] == 'Last Week') {
				$_GET['from'] = date('Y-m-d', strtotime(date('Y-m-d') . ' - ' . (date('N') + 6) . ' day'));
				$_GET['to'] = date('Y-m-d', strtotime($_GET['from'] . ' + 6 day'));
			} else if ($_GET['act'] == 'This Month') {
				$_GET['from'] = date('Y-m-01');
				$_GET['to'] = date('Y-m-d');
			} else if ($_GET['act'] == 'Last Month') {
				$_GET['from'] = date('Y-m-01', strtotime(date('Y-m-d') . ' - 1 month'));
				$_GET['to'] = date('Y-m-d', strtotime(date('Y-m-01') . ' - 1 day'));
			} else if ($_GET['act'] == 'This Year') {
				$_GET['from'] = date('Y-01-01', strtotime(date('Y-m-d')));
				$_GET['to'] = date('Y-m-d');
			} else if ($_GET['act'] == 'Clear') {
				$_GET['from'] = '2020-01-01';
				$_GET['to'] = date('Y-m-d');
			}
		}
		$this->renderPartial('_dash_3pl', ['from' => $_GET['from'], 'to' => $_GET['to']]);
	}

	public function actionDashEX()
	{
		if (isset($_GET['tab'])) {
			$this->render('tab_' . $_GET['tab']);
		} else {
			$this->render('dash_ex');
		}
	}

	public function actionDashEXReport()
	{
		if (!empty($_GET['act'])) {
			if ($_GET['act'] == 'Today') {
				$_GET['from'] = date('Y-m-d 00:00:00');
				$_GET['to'] = date('Y-m-d H:i:s');
			} else if ($_GET['act'] == 'Yesterday') {
				$_GET['from'] = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . ' - 1 day'));
				$_GET['to'] = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . ' - 1 day'));
			} else if ($_GET['act'] == 'This Week') {
				$_GET['from'] = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . ' - ' . (date('N') - 1) . ' day'));
				$_GET['to'] = date('Y-m-d H:i:s');
			} else if ($_GET['act'] == 'Last Week') {
				$_GET['from'] = date('Y-m-d 00:00:00', strtotime(date('Y-m-d') . ' - ' . (date('N') + 6) . ' day'));
				$_GET['to'] = date('Y-m-d 00:00:00', strtotime($_GET['from'] . ' + 7 day'));
			} else if ($_GET['act'] == 'This Month') {
				$_GET['from'] = date('Y-m-01 00:00:00');
				$_GET['to'] = date('Y-m-d H:i:s');
			} else if ($_GET['act'] == 'Clear') {
				$_GET['from'] = '';
				$_GET['to'] = date('Y-m-d H:i:s');
			}
		}
		$this->renderPartial('_tab_dashboard_report', ['from' => $_GET['from'], 'to' => $_GET['to']]);
	}

	public function actionAutoCreatePkg($id)
	{
		$model = $this->loadModel($id);

		$sums = [];
		foreach ($model->mainTask->items as $item) {
			$stock = WmsStock::model()->findByPk($item->mdata['si']);
			$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = 10', [':prod_id' => $stock->prod_id]);

			if (!empty($pack)) {
				if (empty($sums[$pack->qty])) {
					$sums[$pack->qty] = ['weight' => $pack->weight, 'qty' => 0, 'prods' => []];
				}
				if (empty($sums[$pack->qty]['prods'][$stock->prod_id])) {
					$sums[$pack->qty]['prods'][$stock->prod_id]['qty'] = 0;
				}
				$sums[$pack->qty]['qty'] += intval($item->mdata['uq']);
				$sums[$pack->qty]['prods'][$stock->prod_id]['qty'] +=  intval($item->mdata['uq']);
			}
		}

		$pkg = [];
		$remains = [];
		foreach ($sums as $qty => $pack) {
			foreach ($pack['prods'] as $prod) {
				while ($prod['qty'] / $qty >= 1) {
					$parcelId = 'P' . sprintf('%06d', $model->mainTask->id) . sprintf('%03d', count($pkg) + 1);
					$pkg[] = ['wt' => $pack['weight'], 'w' => '', 'h' => '', 'd' => '', 'nt' => '否 ' . date('Y-m-d H:i:s') . ' ' . $parcelId];
					$prod['qty'] -= $qty;
					$pack['qty'] -= $qty;
				}
			}

			if (intval($pack['qty']) == 0) {
				continue;
			}

			while ($pack['qty'] / $qty >= 1) {
				$parcelId = 'P' . sprintf('%06d', $model->mainTask->id) . sprintf('%03d', count($pkg) + 1);
				$pkg[] = ['wt' => $pack['weight'], 'w' => '', 'h' => '', 'd' => '', 'nt' => '是 ' . date('Y-m-d H:i:s') . ' ' . $parcelId];
				$pack['qty'] -= $qty;
			}

			if (intval($pack['qty']) == 0) {
				continue;
			} else {
				$remains[] = ['weight' => $pack['weight'], 'cap' => $qty, 'qty' => $pack['qty']];
			}
		}

		if (!empty($remains)) {
			$vol = 1;
			$weight = 0;
			foreach ($remains as $k => $remain) {
				if ($vol > $remain['qty'] / $remain['cap']) {
					$vol -= $remain['qty'] / $remain['cap'];
					$weight += round($remain['qty'] / $remain['cap'] * $remain['weight'] * 100) / 100;
				} else {
					$parcelId = 'P' . sprintf('%06d', $model->mainTask->id) . sprintf('%03d', count($pkg) + 1);
					$pkg[] = ['wt' => $weight, 'w' => '', 'h' => '', 'd' => '', 'nt' => '是 ' . date('Y-m-d H:i:s') . ' ' . $parcelId];
					$vol = 1 - $remain['qty'] / $remain['cap'];
					$weight = round($remain['qty'] / $remain['cap'] * $remain['weight'] * 100) / 100;
				}
			}
			$parcelId = 'P' . sprintf('%06d', $model->mainTask->id) . sprintf('%03d', count($pkg) + 1);
			$pkg[] = ['wt' => $weight, 'w' => '', 'h' => '', 'd' => '', 'nt' => '是 ' . date('Y-m-d H:i:s') . ' ' . $parcelId];
		}

		$model->mdata['pkg'] = json_encode($pkg);
		$model->update('meta');

		$this->ajaxResult($model);
	}

	public function actionWtDiff()
	{
		if (isset($_GET['tab'])) {
			$this->render('tab_' . $_GET['tab']);
		} else {
			$this->render('wt_diff');
		}
	}

	public function actionWtDiffInv()
	{
		$model = new ReconciliationLine('search');
		unset($model->consol_id);
		$ec = new CDbCriteria;
		$ec->with = ['consol', 'shipment'];
		$ec->addCondition('consol.no LIKE "3PL%" AND t.shipment_no REGEXP ("7RFZ|TNT") AND t.weight > t.our_charge_weight AND JSON_VALUE(shipment.meta, "$.wt_diff_inv") IS NULL');
		$data = $model->search(false, $ec, false)->getData();

		$orgs = [];
		foreach ($data as $shipment) {
			$orgs[$shipment->shipment->agent_id][] = $shipment;
		}

		foreach ($orgs as $agent_id => $shipments) {
			$agent = Org::model()->findByPk($agent_id);

			$invoice = new Invoice;
			$invoice->to_id = $agent_id;
			$invoice->dpt_id = 106;
			$invoice->type = Invoice::INVOICE_TYPE_OTHERS;
			$invoice->dpmt = Invoice::DPMT_3PL;
			$invoice->currency = 1;
			$invoice->mdata['name'] = $agent->name;
			$invoice->mdata['address'] = $agent->getAddress();
			$invoice->mdata['payterm'] = empty($agent->extra['payterm']) ? 'COD' : $agent->extra['payterm'] . ' days';
			$invoice->date = date('Y-m-d');
			$invoice->due = Invoice::calcDue($invoice->date, $agent->extra['payterm']);
			$invoice->status = Invoice::INVOICE_STATUS_PENDING;
			$invoice->sync_xero = 0;
			$invoice->ref = 'WT DIFF';
			$invoice->save();

			foreach ($shipments as $shipment) {
				$was_weight = $shipment->our_charge_weight;
				$act_weight = $shipment->weight;
				$chargecode = ImportChargeCode::STARTRACK_TNT_3PL;
				$act_amount = $shipment->shipment->getChargeByChargecode($chargecode, false, $act_weight);
				$was_amount = $shipment->shipment->getChargeByChargecode($chargecode, false, $was_weight);
				$amount = $act_amount - $was_amount;

				$il = new InvLine;
				$il->inv_id = $invoice->id;
				$il->ccode = 'Weight Diff';
				$il->tax = 'OUTPUT';
				$il->amount = $amount;
				$il->gst = $amount * 10 / 100;
				$il->amount += $il->gst;
				$il->qty = 1;
				$il->det = $shipment->shipment->ref . ' / actual weight ' . $act_weight . 'kg, was ' . $was_weight . 'kg / actual amount $' . number_format($act_amount, 2, '.', '') . ', was amount $' . number_format($was_amount, 2, '.', '');
				if ($il->amount) {
					$il->save();
				}

				$shipment->shipment->mdata['wt_diff_inv'] = $invoice->id;
				$shipment->shipment->update('meta');
			}

			$invoice->getTotal();
			$invoice->save();
		}

		echo json_encode(['done' => true, 'msg' => 'Successfully']);
		Yii::app()->end();
	}

	public function actionLoadAdhocLog($id)
	{
		$model = $this->loadModel($id);
		$this->renderPartial('_task_60_log', ['model' => $model]);
	}

	public function actionCreateAdhoc()
	{
		$model = new WmsTask;
		$model->type = 6010;
		$model->is_request = 1;
		$model->status = 10;

		if (empty($_POST)) {
			$this->render('create_adhoc', array(
				'model' => $model,
			));
		} else {
			if (empty($_POST['org_id'])) {
				$model->addError('id', 'Cust Name is empty');
				$model->job_id = 0;
			} else {
				$job = WmsJob::model()->find('org_id = :org_id', [':org_id' => $_POST['org_id']]);
				if (empty($job)) {
					$job = new WmsJob;
					$job->org_id = $_POST['org_id'];
					$job->type = 90;
					$job->status = 10;
					$job->save();
				}
				$model->job_id = $job->id;
			}
			$model->attributes = $_POST['WmsTask'];
			$model->save();

			$this->ajaxResult($model, ['id']);
		}
	}

	public function actionAdhocComplete($id, $status)
	{
		$model = $this->loadModel($id);
		if ($status == 10) {
			$model->status = 31;
			$model->update('status');
		} else if ($status == 31) {
			$model->status = 32;
			$model->update('status');
		} else if ($status == 32) {
			$model->status = 99;
			$model->update('status');
		}

		$this->ajaxResult($model);
	}

	public function actionQuickCourierLabel()
	{
		$sum = WmsTask::getQuickCourierLabelTasks();
		if (empty($_POST)) {
			$this->render('quick_courier_label', ['sum' => $sum]);
		} else {
			$td = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;
			$rs = [];
			foreach ($sum as $org_id => $types) {
				foreach ($types as $type => $tasks) {
					if (empty($_POST['sum'][$org_id][$type])) continue;

					foreach ($tasks as $task) {
						// pass pickup task or items = []
						if (empty($task->deliveryTask) || empty($task->items)) {
							continue;
						}

						$total_item = 0;
						foreach ($task->items as $item) {
							$total_item += intval($item->mdata['uq']);
						}
						if ($total_item == 0) continue;

						// pkg amount != 1
						if (empty($task->packTask->mdata['pkg'])) {
							$task->packTask->mdata['pkg'] = '';
						}
						$pkgs = json_decode($task->packTask->mdata['pkg'], true);
						$pkgs = empty($pkgs) ? [] : $pkgs;

						if (count($pkgs) != 1) {
							foreach ($task->items as $item) {
								$stock = WmsStock::model()->findByPk($item->mdata['si']);
								$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = :type AND qty = 1', array(':prod_id' => $stock->prod->id, ':type' => array_search('Carton', WmsProdPack::$types)));
								if (empty($pack)) {
									$errors[$task->getNo()] = 'pack info of ' . $stock->prod->name . ' is empty';
									continue;
								}

								$pkgs[] = ['wt' => $pack->weight, 'ti' => $item->id, 'w' => @$pack->dims['w'], 'h' => @$pack->dims['h'], 'd' => @$pack->dims['d']];
							}

							$task->packTask->mdata['pkg'] = json_encode([$pkgs[0]]);
							$task->packTask->update('meta');
						}

						if (!empty($task->deliveryTask->ref) && !preg_match('/PCAE/i', $task->deliveryTask->ref)) {
							continue;
						}

						if (empty($task->deliveryTask->mdata['cnee']['address'])) {
							$errors[$task->getNo()] = '未提供地址信息';
							continue;
						}

						// choose courier
						$weight = [];
						foreach ($pkgs as $pkg) {
							$weight[] = $pkg['wt'];
						}
						if (!empty($task->deliveryTask->mdata['cnee']['suburb']) && !empty($task->deliveryTask->mdata['cnee']['postcode']) && !empty($task->deliveryTask->mdata['cnee']['state'])) {
							if (preg_match('/^australia$|^Au$|^AUS$/i', $task->deliveryTask->mdata['cnee']['country'])) {
								$task->deliveryTask->mdata['courier'] = $task->deliveryTask->selectAuCourier($task->deliveryTask->mdata['cnee']['suburb'], $task->deliveryTask->mdata['cnee']['postcode'], $task->deliveryTask->mdata['cnee']['state'], array_sum($weight), count($weight));

								// check chargecode in wms rate
								$rate = WmsOrgQuote::model()->find('org_id = :org_id AND status = 1', [':org_id' => $task->job->org_id]);
								if (!empty($rate->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE])) {
									$chargecode = ImportChargeCode::model()->find('chargecode = :code', [':code' => $rate->mdata[WmsOrgQuote::QUOTE_COURIER_CHARGECODE]]);
									$couriers = [];
									foreach ($chargecode->couriersObj as $courier) {
										$orgRate = OrgRate::model()->findByPk($courier);
										if (empty($orgRate) || empty($orgRate->org_id)) {
											continue;
										}
										$couriers[] = $orgRate->org_id;
									}
									if (!in_array($task->deliveryTask->mdata['courier'], $couriers)) {
										$task->deliveryTask->mdata['courier'] = Org::ORGID_COURIER_AUPOST;
									}
								}

								// FC Output Limited, WA => AUSPOST
								// if ($task->deliveryTask->mdata['cnee']['state'] == 'WA') {
								// 	$task->deliveryTask->mdata['courier'] = Org::ORGID_COURIER_AUPOST;
								// }
							}
						}

						// pobox
						if (Addr::checkIsPoBox($task->deliveryTask->mdata['cnee']['address']) && !in_array($task->deliveryTask->mdata['courier'], [Org::ORGID_COURIER_AUPOST, Org::ORGID_COURIER_STARTRACK])) {
							$task->deliveryTask->mdata['courier'] = Org::ORGID_COURIER_AUPOST;
						}

						// taylor, unit > 10kg
						if ($task->job->org_id == Org::ORGID_3PL_TAYLOR) {
							$pkgs = json_decode($task->packTask->mdata['pkg'], true);
							foreach ($pkgs as $pkg) {
								if ($pkg['wt'] > 10) {
									$task->deliveryTask->mdata['courier'] = Org::ORGID_COURIER_AUPOST;
								}
							}
						}

						/**
						 * Instruction contains TNT
						 */
						if (!empty($task->mdata['note']) && preg_match('/TNT/i', $task->mdata['note'])) {
							$task->deliveryTask->mdata['courier'] = Org::ORGID_COURIER_TNT;
						}

						$task->deliveryTask->save();

						$task->refresh();

						// shipment amount != pkg amount
						if (empty($task->deliveryTask->mdata['shipment_id']) || empty($task->deliveryTask->mdata['shipment_courier_id']) || $task->deliveryTask->mdata['shipment_courier_id'] != $task->deliveryTask->mdata['courier'] || ($task->deliveryTask->mdata['courier'] == Org::ORGID_COURIER_FASTWAY && count($task->deliveryTask->mdata['shipment_id']) != count($pkgs))) {
							if (empty($task->mdata['address_error'])) {
								$rs = array_merge($task->deliveryTask->toShipment(), $rs);
							}
						} else {
							foreach ($task->deliveryTask->mdata['shipment_id'] as $sid) {
								$p = Shipment::model()->find('id = :id', array(':id' => $sid));
								if (!empty($p)) {
									$rs[] = $p;
								}
							}
						}
					}
				}
			}

			usort($rs, function($a, $b) {
				return $a->ref < $b->ref;
			});

			if (!empty($rs)) {
				if ($rs[0]->type == 10) {
					$p = $rs[0];
					if (empty($rs[0]->id)) {
						print_r($rs[0]->getErrors());
					} else {
						$tf = tempnam($td, 'courierall');
						oPDF::renderPDF('label_A6', array('rs' => $rs), 2, $tf);
						$ps[$p->cref] = $tf;
					}
				} elseif ($rs[0]->type == 20) {
					$p = $rs[0];
					if (empty($p->id)) {
						print_r($p->getErrors());
					} else {
						$tf = tempnam($td, 'courierall');
						oPDF::renderPDF('label_A6', array('rs' => [$p], 'tpl' => '_label-ex', 'empty' => false), 2, $tf);
						$ps[$p->cref] = $tf;
					}
				}
			}

			if (empty($ps)) {
				echo 'no tasks';
			} else {
				oPDF::mergePDF($ps, 1, true, 'courierall.pdf');
			}
		}
	}

	public function actionBackorder()
	{
		if (empty($_GET['confirm'])) {
			$this->render('back_order');
		} else {
			WmsTask::checkBackOrder();
			echo json_encode(['done' => true, 'msg' => 'Successfully']);
			Yii::app()->end();
		}
	}

	public function actionCalcuteFee(){
		if(empty($_POST)){
			$this->render('//wmsTask/calcute_delivery_fee');
		}else{
			$weight = $_POST['weight'];
			$cbmWeight = (($_POST['length']*$_POST['width']*$_POST['height']) / 1000000 * 250)+0.2;
			$tempWeight = 0;
			$weightRevenue = 0;
			$wmsSurcharge = 0;
			$tempTask = new WmsTask();
			if($_POST['courier']==1){
				$tempWeight = $weight;
			}else{
				$tempWeight = max($cbmWeight,$weight);
			}
			$weightRevenue = $tempTask->getChargeByChargecode($tempWeight, $_POST['post_code'], $_POST['charge_code']);
			$wmsSurcharge = $tempTask->deliveryTaskSurCharge($_POST['courier'],$_POST['length'],$_POST['width'],$_POST['height'],$tempWeight);

			 echo json_encode([
                'isSuccess' => true,
				'chargeableweight'=>$tempWeight,
				'revenueFee'=>$weightRevenue,
				'surcharge'=>$wmsSurcharge,
				'chargecode'=>$_POST['charge_code'],
			]);
			return;
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = WmsTask::model()->findByPk($id);
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
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'wms-task-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
