<?php
/*
this controller is used to manage the jobs and tasks
 * which include import goods and manage out putgoods
 */
class TaskController extends Controller
{

	/*we need to list the jobs belongs to the owner
	 *
	 */
	public function actionIndex()
	{
		if(!empty($_GET['dpt_id'])){
			$dpt_id = $_GET['dpt_id'];
		}else{
			$dpt_id = 106;
		}

		if(!empty($_GET['org_id'])){
			Yii::app()->session['org_id'] = $_GET['org_id'];
		}

		$model = WmsJob::model()->find('org_id=:oid and status=10 and dpt_id=:dpt_id and week(`created`,1)=:week', array(':oid' => $_GET['org_id'], ':week' => intval(date('W')),':dpt_id'=>$dpt_id));
		if (empty($model)) {
			$model = new WmsJob();
			$model->org_id =$_GET['org_id'];
			$model->status = 10;
			$model->type = 90;
			$model->dpt_id = $dpt_id;
			$model->created = date('Y-m-d');
			$model->ref = '3PL_' . date('Y-m-d');
			$model->save();
		}

		$task = new WmsTask();
//        $model->id=Yii::app()->user->id;
		$task->unsetAttributes();

		$_GET['tabid'] = 121212123;
		if (isset($_GET['WmsTask'])) {
			unset($_GET['WmsTask']['skus']);
			$task->attributes = $_GET['WmsTask'];
		}
		//get the orgids and by org_ids
		if (Yii::app()->user->grp != 0) {
			$task->job_ids = Pcaw::jobIds(User::getOrgIds());
		}

		$task->dpt_id=$dpt_id;

		// eva customer
		$invoice_due = '';
		$limit = 0;
		$org = Org::model()->findByPk($_GET['org_id']);
		if (!empty($org->extra['sp_id']) && $org->extra['sp_id'] == 305) {
			if (!empty($org->extra['statement_invs'])) {
				foreach ($org->extra['statement_invs'] as $k => $inv) {
					$inv = Invoice::model()->findByPk($inv);
					if (in_array($inv->status, [6, 8, 9, 10, 11])) {
						$changed = true;
						unset($org->extra['statement_invs'][$k]);
					} else {
						if (empty($org->extra['creditterms']) || $org->extra['creditterms'] == 1) {
							$invoice_due = $inv->no;
						} else {
							$dStart = new DateTime(date('Y-m-d'));
							$dEnd = new DateTime($inv->date);
							$dDiff = $dStart->diff($dEnd);

							if ($dDiff->days > $org->extra['creditterms']) {
								$invoice_due = $inv->no;
							}
						}
					}
				}
				if (!empty($changed)) {
					$org->update('meta');
				}
			}
			$limit = (isset($org->extra['creditlimit']) ? floatval($org->extra['creditlimit']) : 0) * (1 - $org->getCurrentCreditOfLimit());
		}

		$this->render('task_list', array('model' => $model, 'task' => $task, 'invoice_due' => $invoice_due, 'limit' => $limit, 'release' => !$org->overCreditLimit()));
	}

	public function actionJobUpdate($id)
	{
		$model = WmsJob::model()->findByPk($id);
		$tasks = new WmsTask();
		$tasks->unsetAttributes();
		$_GET['tabid'] = 121212124;
		if (isset($_GET['WmsTask'])) {
			$tasks->attributes = $_GET['WmsTask'];
		}
		$tasks->job_id = $id;

		if (isset($_POST['WmsJob'])) {
			$model->setAttributes($_POST['WmsJob']);
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('job_update', array('model' => $model, 'tasks' => $tasks));

	}

	public function actionUpLoadFile($id){
		if (!empty($_FILES['files'])){
			$errors=[];
			$photos=empty($_FILES['files']['tmp_name'])?[]:$_FILES['files']['tmp_name'];
			foreach ($photos as $key => $photo) 
			{
				if (empty($photo)) 
				{
					$errors[]='images not exists';
				} else 
				{
					$model = WmsTask::model()->findByPk($id);
					$mainTask = $model->mainTask;
					$name = $_FILES['files']['name'][$key];
					if(!is_uploaded_file($photo)) $errors[]=$name.'images not exists';
					$hash = FileRepo::uploadHash($mainTask, FileRepo::WMS_TASK_ATTACHMENT);
					$filesize = filesize($photo);
					$date = date('Y-m-d H:i:s');
					$fileHash = hash_file('crc32b', $photo).hash('crc32b', $filesize);
					$finfo = finfo_open(FILEINFO_MIME_TYPE);
					$mime = finfo_file($finfo, $photo);
					$fr = WmsTask::updateUploadSingleFile($filesize,$date,$fileHash,$finfo,$mime,$photo,$name, $hash);
					
				}
			}
			if (!empty($err)) {
				$resp['msg'] = implode(';', $err);
				$resp['done'] = false;
				echo json_encode($resp);
				return;
			}else{
				$resp['done']=true;
				$resp['msg'] ='Saved Successfully';
				echo json_encode($resp);
				//$this->renderPartial('update_task', array('model' => $mainTask),true);
				//$this->ajaxResult($fr);
				//$this->render('update_task', array('model' => $mainTask));
				//$this->renderPartial('update_task', array('model' => $mainTask),true);
			}

			
		}
	}
	
	public function actionUpdateTask($id)
	{
		if (!empty($_POST['id'])) {
			$id = $_POST['id'];
		}

		// get last created task, fix problem caused by `actionCreateTask` redirect
		if ($id == 'undefined') {
			$id = WmsTask::model()->with('job')->find(['condition' => 'job.org_id = :org_id AND is_request = 1', 'params' => [':org_id' => Yii::app()->user->org], 'order' => 't.id DESC'])->id;
		}

		$model = WmsTask::model()->findByPk($id);

		if (!in_array($model->job_id, Pcaw::jobIds(User::getOrgIds())) && Yii::app()->user->grp != 0) {
			Acl::denied403();
		}

		if (!empty($_POST)) {
			$err = [];

			if($model->status == 30){
				$err[] = "Task already work in process.";
			}

			if (!empty($err)) {
				$resp['msg'] = implode('<br />', $err);
				$resp['done'] = false;
				echo json_encode($resp);
				return;
			}

			if (!empty($_POST['WmsTask'])) {
				$model->attributes = $_POST['WmsTask'];
			}
			if (!empty($_POST['mdata'])) {
				foreach ($_POST['mdata'] as $k => $v) {
					if (is_array($v)) {
						$v = array_map('trim', $v);
					} elseif (is_string($v)) {
						$v = trim($v);
					}
					if (!empty($v) || strlen($v) > 0) {
						$model->mdata[$k] = $v;
					}

				}
				if (!empty($_POST['mdata']['cnee']) && $_POST['mdata']['cnee']['country'] == 'AU') {
					if (!Postcode::validateAddress($_POST['mdata']['cnee']['suburb'], $_POST['mdata']['cnee']['state'], $_POST['mdata']['cnee']['postcode'])) {
						$model->addError('meta', 'Error Address');
					}
					if ($model->mainTask->type == 3040) {
						$model->mainTask->status = 99;
						$model->mainTask->update('status');
					}
				}
			}
			if (!empty($_POST['meta_items'])) {
				$meta_items = json_decode($_POST['meta_items'], true);
				if (in_array($model->type, [3020,3030])) {
					foreach ($meta_items as $k => $itm) {
						$r = WmsStock::model()->findByPk($itm['si']);
						if (empty($r) || $r->prod->type != WmsProd::WMS_PROD_KIT) continue;

						if (!empty($itm['cq'])) {
							$model->addError('id', $itm['sn'] . ' can not pick by carton');
							$this->ajaxResult($model);
						}

						foreach ($r->prod->items as $item) {
							$remain = $itm['uq'] * $item->qty;
							$rs = WmsStock::model()->findAll('org_id = :org_id AND prod_id = :prod_id AND qty - qty_res > 0', [':org_id' => $model->job->customer->id, ':prod_id' => $item->item->id]);
							foreach ($rs as $r) {
								$meta_items[] = [
									'si' => $r->id,
									'sn' => $r->stockName(),
									'uq' => min($r->qty - $r->qty_res, $remain),
									'sku' => $item->item->ean,
									'cq' => '',
									'pq' => '',
									'pl' => '',
									'nt' => $itm['nt'],
								];
								$remain -= min($r->qty - $r->qty_res, $remain);
								if ($remain <= 0) break;
							}
						}

						$meta_items[$k]['uq'] = 0;
					}
				}

				foreach ($meta_items as $k => $itm) {
					if (empty($itm['gi']) && !empty($itm['gn'])) {
						$model->addError('meta', 'Line ' . ($k + 1) . ': product ' . $itm['gn'] . ' not found');
					}
					if (!empty($itm['gi']) && empty($itm['uq']) && !empty($itm['cq'])) {
						$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $itm['gi']]);
						if (!empty($pp) && !empty($pp->qty)) {
							$meta_items[$k]['uq'] = $itm['cq'] * $pp->qty;
						}
					}
					if (!empty($itm['si']) && empty($itm['uq']) && !empty($itm['cq'])) {
						$stock = WmsStock::model()->findByPk($itm['si']);
						$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $stock->prod_id]);
						if (!empty($pp) && !empty($pp->qty)) {
							$meta_items[$k]['uq'] = $itm['cq'] * $pp->qty;
						} else {
							$model->addError('meta', 'Line ' . ($k + 1) . ': product ' . $itm['sn'] . ' cannot pick by carton');
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
			}
			if (!empty($_POST['wt'])) {
				$model->mdata['pkg'] = [];
				foreach ($_POST['wt'] as $wt) {
					if ($wt) {
						$model->mdata['pkg'][] = ['wt' => $wt];
					}
				}
				$model->mdata['pkg'] = json_encode($model->mdata['pkg']);
				$model->update('meta');
			}
			if (!empty($_POST['meta'])) {
				foreach($_POST['meta'] as $k => $v){
					$model->mdata[$k] = $v;
				}
			}
			if (empty($model->getErrors())) {
				$model->save();
				if($model->type==2120){
					$model->chooseCourier();
				}
			}

			$this->ajaxResult($model);
		}

		$this->render('update_task', array('model' => $model));
	}

	public function actionReturnCheck($id)
	{
		if (!empty($_POST['id'])) {
			$id = $_POST['id'];
		}

		$model = WmsTask::model()->findByPk($id);
		if (!in_array($model->job_id, Pcaw::jobIds(User::getOrgIds())) && Yii::app()->user->grp != 0) {
			Acl::denied403();
		}

		if (empty($_POST)) {
			$this->render('return_check', array('model' => $model));
		} else {
			if (!$model->notEmptyReturnCheck()) {
				$task = new WmsTask;
				$task->job_id = $model->job_id;
				$task->type = 3050;
				$task->status = 20;
				$task->is_request = 1;
				$task->link_id = 0;
				$task->op_id = 0;
				$task->bwf = 0;
				$task->ref = 'Return Check for ' . $model->getNo();
				$task->mdata['origin_task'] = $model->id;
				$task->save();

				$task->refresh();
				$ac = $task->actionTask;
				foreach ($_POST as $k => $v) {
					$ac->mdata[$k] = $v;
				}
				$ac->save();

				$model->mdata['return_check_task'] = $task->id;
				$model->update('meta');
			}
			$this->ajaxResult($model);
		}
	}

	public function actionReturnOption($id)
	{
		if (!empty($_POST['id'])) {
			$id = $_POST['id'];
		}

		$model = WmsTask::model()->findByPk($id);
		if (!in_array($model->job_id, Pcaw::jobIds(User::getOrgIds())) && Yii::app()->user->grp != 0) {
			Acl::denied403();
		}

		if (empty($_POST)) {
			$this->render('return_option', array('model' => $model));
		} else {
			if (!$model->notEmptyReturnOption()) {
				$task = new WmsTask;
				$task->job_id = $model->job_id;
				$task->type = $_POST['optionRadio'];
				$task->status = 20;
				$task->is_request = 1;
				$task->link_id = 0;
				$task->op_id = 0;
				$task->bwf = 0;
				$task->ref = (WmsTask::$types[$_POST['optionRadio']] == 'Pallets In' ? 'Stock In' : WmsTask::$types[$_POST['optionRadio']]) . ' for ' . $model->getNo();
				$task->mdata['origin_task'] = $model->id;
				$task->save();

				$model->mdata['return_option_task'] = $task->id;
				$model->update('meta');

				if ($task->type == 3060) {
					$task->packTask->mdata = $model->packTask->mdata;
					$task->packTask->update('meta');

					$task->deliveryTask->mdata = $model->deliveryTask->mdata;
					unset($task->deliveryTask->mdata['shipment_id']);
					$task->deliveryTask->update('meta');
				}
			}
			$this->ajaxResult($model);
		}
	}

	/**
	 * Creates a new model. for wms Job
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new WmsJob;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$_GET['tabid'] = 311211111;
		if (isset($_POST['WmsJob'])) {
			$model->attributes = $_POST['WmsJob'];
			$model->save();
			$this->ajaxResult($model, ['id', 'no']);
		}
		$model->status = 10;

		$this->render('create_job', array(
			'model' => $model,
		));
	}

	public function actionCreateTask($id)
	{
		$job = WmsJob::model()->findByPk($id);
		if($_GET['isSpecial']){
			$isSpecial = $_GET['isSpecial'];
		}else{
			$isSpecial = 0;
		}
		$dpt_id = $job->dpt_id;
		
		if (sizeof(User::getOrgIds()) > 1) {
		if (empty($_POST['org_id'])) {
			$_POST['org_id'] = $job->org_id;
		}
		if (!empty($_POST['org_id']) && $job->org_id != $_POST['org_id']) {
			$oids = User::getOrgIds();
			foreach ($oids as $oid) {
				if ($oid == $_POST['org_id']) {
						$job = WmsJob::model()->find('org_id = :oid and status = 10 and week(`created`,1) = :week', array(':oid' => $_POST['org_id'], ':week' => intval(date('W'))));
						if (empty($job)) {
							$job = new WmsJob();
							$job->org_id = $_POST['org_id'];
							$job->status = 10;
							$job->type = 90;
							$job->created = date('Y-m-d');
							$job->ref = '3PL_' . date('Y-m-d');
							$job->dpt_id = $dpt_id;
							$job->save();
						}
				}
			}
		}
		}

		$model = new WmsTask;
		$model->job_id = $job->id;
		$model->dpt_id = $job->dpt_id;
		$model->type = $_GET['type'];
		$model->is_request = 1;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$_GET['tabid'] = 1123123111;
		if (isset($_POST['WmsTask'])) {
			$model->attributes = $_POST['WmsTask'];
			if (!empty($_POST['mdata'])) {
				foreach ($_POST['mdata'] as $k => $v) {
					if (!empty($v) || strlen($v) > 0) {
						$model->mdata[$k] = $v;
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
						$meta_items[$k]['uq'] = $itm['cq'] * $pp->qty;
					}
				}
				if (!empty($itm['si']) && empty($itm['uq']) && !empty($itm['cq'])) {
					$stock = WmsStock::model()->findByPk($itm['si']);
					$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $stock->prod_id]);
					if (!empty($pp) && !empty($pp->qty)) {
						$meta_items[$k]['uq'] = $itm['cq'] * $pp->qty;
					} else {
						$model->addError('meta', 'Line ' . ($k + 1) . ': product ' . $itm['sn'] . ' cannot pick by carton');
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
			foreach($_POST['meta'] as $k => $v){
				$model->mdata[$k] = $v;
			}
			$model->mdata['isSpecial']=$isSpecial;
			if (empty($model->getErrors())) {
				$model->save();
			}
			if ($model->type == 3040) {
				$model->pickupTask->type = 2120;
				$model->pickupTask->update('type');
			}

			$this->ajaxResult($model, ['id']);
		}
		$model->status = 10;
		$model->mdata['isSpecial']=$isSpecial;
		$this->render('create_task', array(
			'model' => $model,
		));
	}

	public function actionImport($id)
	{
		$model = $this->loadModel($id);
		$_GET['tabid'] = 11122233344;
		$this->render('task_import', array('model' => $model));
	}

	public function actionImportProduct($id)
	{
		if (empty($_FILES)) {
			echo json_encode(['done' => false, 'msg' => 'Please select file']);
			return;
		} else {
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
						} else if (empty($d[2])) {
							$err[] = "line " . $i . " task ref is empty!";
						} else {
							if ($last_taskno != $d[1]) {
								if (empty($d[3])) {
									// $err[] = "line " . $i . " task schedule time is empty!";
									// continue;
									$d[3] = date('Y-m-d H:i:s');
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
								$product = WmsProd::model()->with(['orgs'])->together()->find('orgs.org_id = :oid AND sku = :sku', array(':oid' => Yii::app()->user->org, ':sku' => $d[6]));
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
						$wmstask = WmsTask::model()->with('job')->find('t.ref = :ref AND job.org_id = :oid', array(':ref' => $temp_task['ref'], ':oid' => Yii::app()->session['org_id']));
						if (!empty($wmstask)) {
							$resp['done'] = false;
							$resp['msg'] .= '<br />' . $temp_task['ref'] . ' has been uploaded before';
							continue;
						}
	
						$job = WmsJob::model()->find(['condition' => 'status != 100 AND org_id = :org_id and id=:id', 'params' => array(':org_id' => Yii::app()->session['org_id'],':id'=>$id), 'order' => 'id DESC']);
						if (empty($job)) {
							$job = new WmsJob();
							$job->org_id = Yii::app()->session['org_id'];
							$job->type = array_search('Inward', WmsJob::$types);
							$job->status = array_search('New', WmsJob::$states);
							$job->save();
						}
	
						$task = new WmsTask();
						$task->job_id = $job->id;
						$task->ref = $temp_task['ref'];
						$task->status = array_search('Scheduled', WmsTask::$states);
						$task->is_request = 1;
						$task->type = array_search('Pallets In', WmsTask::$types);
						$task->schd_time = $temp_task['sch_t'];
						$task->due_time = $temp_task['due_t'];
						$task->dpt_id = $job->dpt_id;
						foreach ($temp_task['products'] as $t) {
							$item = $t;
							$item['pli'] = '';
							$item['pl'] = '';
							if (empty($t['uq']) && !empty($t['cq'])) {
								$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $product->id]);
								if (!empty($pp) && !empty($pp->qty)) {
									$item['uq'] = $t['cq'] * $pp->qty;
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
					$org = Org::model()->findByPk(Yii::app()->session['org_id']);
					$manifest = FileRepo::storeFile($_FILES['pickup_excel']['tmp_name'], date('YmdHis') . '_' . $org->name . '.xlsx', FileRepo::WMS_TASK_MANIFEST, Yii::app()->session['org_id']);
					$xls->load($_FILES['pickup_excel']['tmp_name']);
					$xls->goSheet(0);
					$data = $xls->getAll();
					if (strtolower(implode('', $data[1])) == strtolower("Task no.Task RefSchedule TimeCourierPhoto & MarkCneeTelAddressCitySuburbStatePostcodeCountryProduct NameBarcode / SkuCarton QtyUnit QtyExpiryBatchTracking No")) {
						$hasCompany = 0;
					} else if (strtolower(implode('', $data[1])) == strtolower("Task no.Task RefSchedule TimeCourierPhoto & MarkCompanyCneeTelAddressCitySuburbStatePostcodeCountryProduct NameBarcode / SkuCarton QtyUnit QtyExpiryBatchTracking No")) {
						$hasCompany = 1;
					} else {
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
	
						if (empty($d[1]) && empty($d[3]) && empty($d[4]) && empty($d[$hasCompany + 15])) {
							continue;
						}
	
						if (empty($d[1])) {
							$err[] = "line " . $i . " task no is empty!";
						} else {
							if ($last_taskno != $d[1]) {
								if (empty($d[3])) {
									// $err[] = "line " . $i . " task schedule time is empty!";
									// continue;
									$d[3] = date('Y-m-d H:i:s');
								}
								// $d[4] = 'PCAE';
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

								if (Yii::app()->session['org_id'] == Org::ORGID_3PL_XCSOURCE && !preg_match('/^MIX$/i', $d[4]) && !preg_match('/^LETTER$/i', $d[4])) {
									$err[] = "line " . $i . " task 渠道 is empty!";
									continue;
								}
								// $address = null;
								// if (preg_match('/PCA|PAC/i', $d[4]) && !empty($d[$hasCompany + 8]) && preg_match('/[\x{4e00}-\x{9fa5}]+/u', $d[$hasCompany + 8])) {
								// 	$address = $this->pasteAddrProcess($d[$hasCompany + 8]);
								// }
								if (preg_match('/PCA|PAC/i', $d[4]) && empty($address) && (empty($d[$hasCompany + 6]) || empty($d[$hasCompany + 7]) || empty($d[$hasCompany + 8]) || empty($d[$hasCompany + 9]) || empty($d[$hasCompany + 10]) || empty($d[$hasCompany + 11]) || empty($d[$hasCompany + 12]) || empty($d[$hasCompany + 13]))) {
									$err[] = "line " . $i . " consignee detail is not completed!";
									continue;
								}
								if (preg_match('/PCA|PAC/i', $d[4])) {
									$d[12] = Addr::checkAuState($d[12]);
									if (!preg_match('/[\x{4e00}-\x{9fa5}·\.]+/u', $d[6]) && $d[13] == 'AU') {
										if (!Postcode::validateAddress($d[10], $d[11], $d[12])) {
											$err[] = 'line ' . $i . ' suburb, state, postcode does not match';
											continue;
										}
									}
								}
								$d[7] = trim($d[7]);
								if (!preg_match('/[\+]+\d{2}\s+\d*/', $d[7]) && !preg_match('/\d*/', $d[7])) {
									$err[] = 'line ' . $i . ' phone ' . $d[7] . ' has to be number';
									continue;
								}
								$last_taskno = $d[1];
								if (empty($address)) {
									$tasks[$d[1]] = array(
										'ref' => $d[2],
										'sch_t' => oExcel::toDate($d[3]),
										'courier' => $d[4],
										'pack_ref' => $d[5],
										'cnee' => $d[$hasCompany + 6],
										'tel' => $d[$hasCompany + 7],
										'address' => $d[$hasCompany + 8],
										'city' => $d[$hasCompany + 9],
										'suburb' => $d[$hasCompany + 10],
										'state' => $d[$hasCompany + 11],
										'postcode' => $d[$hasCompany + 12],
										'country' => $d[$hasCompany + 13],
										'trackingno' => $d[20],
										'products' => []
									);
									if ($hasCompany) {
										$tasks[$d[1]]['company'] = $d[6];
									}
								} else {
									$tasks[$d[1]] = array(
										'ref' => $d[2],
										'sch_t' => oExcel::toDate($d[3]),
										'courier' => $d[4],
										'pack_ref' => $d[5],
										'cnee' => $d[$hasCompany + 6],
										'tel' => $d[$hasCompany + 7],
										'address' => $address['address'],
										'city' => $address['city'],
										'suburb' => $address['suburb'],
										'state' => $address['state'],
										'postcode' => $address['postcode'],
										'country' => 'CN',
										'trackingno' => $d[20],
										'products' => []
									);
									if ($hasCompany) {
										$tasks[$d[1]]['company'] = $address['company'];
									}
								}
							}
							if (empty($d[$hasCompany + 15])) {
								$err[] = "line " . $i . " product barcode / sku is empty";
								continue;
							} else if (empty($d[$hasCompany + 16]) && empty($d[$hasCompany + 17])) {
								$err[] = "line " . $i . " product carton quantity and unit quantity are empty";
								continue;
							}
							$d[$hasCompany + 15] = preg_replace('/\'/', '', $d[$hasCompany + 15]);
							$product = WmsProd::model()->find('ean = :ean', array(':ean' => $d[$hasCompany + 15]));
							if (empty($product)) {
								$product = WmsProd::model()->with(['orgs'])->together()->find('orgs.org_id = :oid AND sku = :sku', array(':oid' => Yii::app()->user->org, ':sku' => $d[$hasCompany + 15]));
								if (empty($product)) {
									$err[] = "line " . $i . " product barcode / sku is not found in the system!";
									continue;
								}
							}
							$products = [];
							if ($product->type != WmsProd::WMS_PROD_KIT) {
								$products[] = ['product' => $product, 'times' => 1];
							} else if ($product->type == WmsProd::WMS_PROD_KIT) {
								foreach ($product->items as $item) {
									$products[] = ['product' => $item->item, 'times' => $item->qty];
								}
							}
							foreach ($products as $item) {
							$product = $item['product'];
							$times = $item['times'];
	
							$condition = 'prod_id = :prod_id AND org_id = :org_id';
							$params = array(':prod_id' => $product->id, ':org_id' => Yii::app()->session['org_id']);
							if ($d[$hasCompany + 18]) {
								$condition .= ' AND expiry = :expiry';
								$params[':expiry'] = oExcel::toDate($d[18]);
							}
							if ($d[$hasCompany + 19]) {
								$condition .= ' AND batch = :batch';
								$params[':batch'] = $d[$hasCompany + 19];
							}
							if (!empty($dpt_id)) {
								$condition .= ' AND dpt_id = :dpt_id';
								$params[':dpt_id'] = $dpt_id;
							}

							$stocks = WmsStock::model()->findAll(['condition' => $condition, 'params' => $params, 'order' => 'expiry ASC']);
							$remain_quantity = 0;
							if (!empty($d[$hasCompany + 16])) {
								$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $product->id]);
								if (!empty($pp) && !empty($pp->qty)) {
									$remain_quantity = $d[$hasCompany + 16] * $pp->qty;
								}
							} else {
								$remain_quantity = $d[$hasCompany + 17];
							}
							$remain_quantity *= $times;
							foreach ($stocks as $stock) {
								if ($stock->qty - $stock->qty_res <= 0) {
									continue;
								}
								if (!empty($reserved[$stock->id])) {
									$stock->qty_res += $reserved[$stock->id];
								}
								$sub_quantity = $stock->qty - $stock->qty_res > $remain_quantity ? $remain_quantity : $stock->qty - $stock->qty_res;
								$remain_quantity -= $sub_quantity;
								if ($sub_quantity > 0) {
									$sn = $product->name;
									if (!empty($stock->expiry)) {
										$sn .= ' (Exp: ' . $stock->expiry . ')';
									}
									if (!empty($stock->batch)) {
										$sn .= ' (Batch: ' . $stock->batch . ')';
									}
									$tasks[$d[1]]['products'][] = array(
										'si' => $stock->id,
										'sn' => $sn,
										'cq' => isset($pp->qty) ? $sub_quantity / $pp->qty : '',
										'uq' => $sub_quantity,
										'pq' => '',
										'pl' => '',
										'nt' => '',
									);
	
									if (empty($reserved[$stock->id])) {
										$reserved[$stock->id] = 0;
									}
									$reserved[$stock->id] += $sub_quantity;
								}
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
						$wmstask = WmsTask::model()->with('job')->find('t.ref = :ref AND job.org_id = :oid', array(':ref' => $temp_task['ref'], ':oid' => Yii::app()->session['org_id']));
						if (!empty($wmstask)) {
							$resp['done'] = false;
							$resp['msg'] .= '<br />' . $temp_task['ref'] . ' has been uploaded before';
							$wmstask->deliveryTask->ref = $temp_task['courier'];
							$wmstask->deliveryTask->update('ref');
							continue;
						}
	
						//$job = WmsJob::model()->find(['condition' => 'status != 100 AND org_id = :org_id', 'params' => [':org_id' => Yii::app()->session['org_id']], 'order' => 'id DESC']);
						$job = WmsJob::model()->find(['condition' => 'status != 100 AND org_id = :org_id and id=:id', 'params' => array(':org_id' => Yii::app()->session['org_id'],':id'=>$id), 'order' => 'id DESC']);
						if (empty($job)) {
							$job = new WmsJob();
							$job->org_id = Yii::app()->session['org_id'];
							$job->type = array_search('Pick Pack', WmsJob::$types);
							$job->status = array_search('New', WmsJob::$states);
							$job->save();
						}
	
						$task = new WmsTask();
						$task->job_id = $job->id;
						$task->ref = $temp_task['ref'];
						$task->status = array_search('Scheduled', WmsTask::$states);
						$task->is_request = 1;
						$task->type = $task_type;
						$task->schd_time = date("Y-m-d H:i:s");
						//$task->schd_time = $temp_task['sch_t'];
						$task->dpt_id = $job->dpt_id;
						

						foreach ($temp_task['products'] as $t) {
							$item = $t;
							if (empty($t['uq']) && !empty($t['cq'])) {
								$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $product->id]);
								if (!empty($pp) && !empty($pp->qty)) {
									$item['uq'] = $t['cq'] * $pp->qty;
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
							$sub_task->mdata['cnee']['company'] = @$temp_task['company'];
							$sub_task->mdata['cnee']['name'] = $temp_task['cnee'];
							$sub_task->mdata['cnee']['tel'] = $temp_task['tel'];
							$sub_task->mdata['cnee']['address'] = $temp_task['address'];
							$sub_task->mdata['cnee']['city'] = $temp_task['city'];
							$sub_task->mdata['cnee']['suburb'] = $temp_task['suburb'];
							$sub_task->mdata['cnee']['state'] = $temp_task['state'];
							$sub_task->mdata['cnee']['postcode'] = $temp_task['postcode'];
							if (preg_match('/^australia$|^Au$|^AUS$/i', $temp_task['country'])) {
								$sub_task->mdata['cnee']['country'] = 'AU';
							} else if (strlen($temp_task['country']) == 2) {
								$sub_task->mdata['cnee']['country'] = $temp_task['country'];
							} else if (array_search($temp_task['country'], Unloco::$countries)) {
								$sub_task->mdata['cnee']['country'] = array_search($temp_task['country'], Unloco::$countries);
							}
							$sub_task->mdata['cnee']['email'] = '';
							$sub_task->mdata['trackingno']=$temp_task['trackingno'];
							$sub_task->save();
	
							if (!preg_match('/PCA|PAC/i', $temp_task['courier']) && !preg_match('/MIX|LETTER/i', $temp_task['courier'])) {
								$sub_task->ref = $temp_task['courier'];
								$sub_task->save();
							}
	
							// customer choose mix or letter
							if (preg_match('/MIX|LETTER/i', $temp_task['courier'])) {
								$sub_task->mdata['customer_choose_courier'] = $temp_task['courier'];
								$sub_task->save();
							}

							//Author:Nero Date:2021/6/22 Description:choose courier
							$customer_select_courier="";

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
									// $err[] = "line " . $i . " task schedule time is empty!";
									// continue;
									$d[3] = date('Y-m-d H:i:s');
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
								$product = WmsProd::model()->with(['orgs'])->together()->find('orgs.org_id = :oid AND sku = :sku', array(':oid' => Yii::app()->user->org, ':sku' => $d[9]));
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
						$wmstask = WmsTask::model()->with('job')->find('t.ref = :ref AND job.org_id = :oid', array(':ref' => $temp_task['ref'], ':oid' => Yii::app()->session['org_id']));
						if (!empty($wmstask)) {
							$resp['done'] = false;
							$resp['msg'] .= '<br />' . $temp_task['ref'] . ' has been uploaded before';
							continue;
						}
	
						$job = WmsJob::model()->find(['condition' => 'status != 100 AND org_id = :org_id and id=:id', 'params' => array(':org_id' => Yii::app()->session['org_id'],':id'=>$id), 'order' => 'id DESC']);
						if (empty($job)) {
							$job = new WmsJob();
							$job->org_id = Yii::app()->session['org_id'];
							$job->type = array_search('Inward', WmsJob::$types);
							$job->status = array_search('New', WmsJob::$states);
							$job->save();
						}
	
						$task = new WmsTask();
						$task->job_id = $job->id;
						$task->ref = $temp_task['ref'];
						$task->status = array_search('Scheduled', WmsTask::$states);
						$task->is_request = 1;
						$task->dpt_id = $job->dpt_id;
						$task->type = array_search('Container Unload', WmsTask::$types);
						$task->schd_time = $temp_task['sch_t'];
						$task->due_time = $temp_task['due_t'];
						$task->mdata['ctn_no'] = $temp_task['ctn_no'];
						$task->mdata['ctn_size'] = $temp_task['ctn_size'];
						$task->mdata['ctn_seal'] = $temp_task['ctn_seal'];
	
						foreach ($temp_task['products'] as $t) {
							$item = $t;
							$item['pli'] = '';
							$item['pl'] = '';
							if (empty($t['uq']) && !empty($t['cq'])) {
								$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $product->id]);
								if (!empty($pp) && !empty($pp->qty)) {
									$item['uq'] = $t['cq'] * $pp->qty;
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
			} else if (!empty($_FILES['split_delivery_excel'])) {
				//import split delivery tasks
				$xls = new oExcel;
				$err = [];
				if (!$xls->supported($_FILES['split_delivery_excel']['name'])) {
					foreach ($xls->getError() as $e) {
						$err[] = $e;
					}
				} else {
					$org = Org::model()->findByPk(Yii::app()->session['org_id']);
					$manifest = FileRepo::storeFile($_FILES['split_delivery_excel']['tmp_name'], date('YmdHis') . '_' . $org->name . '.xlsx', FileRepo::WMS_TASK_MANIFEST, Yii::app()->session['org_id']);
					$xls->load($_FILES['split_delivery_excel']['tmp_name']);
					$xls->goSheet(0);
					$data = $xls->getAll();
					// if (strtolower(implode('', $data[1])) == strtolower("Task no.Task RefSchedule TimeCourierPhoto & MarkCneeTelAddressCitySuburbStatePostcodeCountryProduct NameBarcode / SkuCarton QtyUnit QtyExpiryBatch")) {
					// 	$hasCompany = 0;
					// } else if (strtolower(implode('', $data[1])) == strtolower("Task no.Task RefSchedule TimeCourierPhoto & MarkCompanyCneeTelAddressCitySuburbStatePostcodeCountryProduct NameBarcode / SkuCarton QtyUnit QtyExpiryBatch")) {
					// 	$hasCompany = 1;
					// } else {
					// 	$err[] = "template wrong";
					// }
					
					// validation
					foreach ($data as $i => $d) {
						if ($i < 3) {
							continue;
						}
						
						if($i ==3){
							if(empty($d[5])){
								$err[] = "empty main munber, line: ".$i;
							}
						}
						if(empty($d[6])){
							$err[] = "empty number from, line: ".$i;
						}
						if(empty($d[7])){
							$err[] = "empty munber to, line: ".$i;
						}
						if(empty($d[8])){
							$err[] = "empty munber origin, line: ".$i;
						}
						if(empty($d[9])){
							$err[] = "empty weight, line: ".$i;
						}
						if(empty($d[11])){
							$err[] = "empty cbm, line: ".$i;
						}
						if(empty($d[13])){
							$err[] = "empty pieces, line: ".$i;
						}
						if(empty($d[15])){
							$err[] = "empty address, line: ".$i;
						}
						if(empty($d[16])){
							$err[] = "empty suburb, line: ".$i;
						}
						if(empty($d[17])){
							$err[] = "empty region, line: ".$i;
						}
						else if(!in_array($d[17],['NSW','VIC','QLD','WA'])){
							$err[] = "region should be one of NSW/VIC/QLD/WA, line: ".$i;
						}
						if(empty($d[18])){
							$err[] = "empty postcode, line: ".$i;
						}
						if(empty($d[19])){
							$err[] = "empty contracter, line: ".$i;
						}
						if(empty($d[20])){
							$err[] = "empty mobile, line: ".$i;
						}
						else if(preg_match("/^04\d{8}$/", $d[20])){
							$err[] = "mobile number should like :04xxxxxxxx, line: ".$i;
						}
						if(empty($d[21])){
							$err[] = "empty email, line: ".$i;
						}
					}
					if (!empty($err)) {
						$resp['msg'] = implode(';', $err);
						$resp['done'] = false;
						echo json_encode($resp);
						return;
					}
					
					//validate suburb region postcode
					$strSql = 'postcode in (';
					foreach ($data as $i => $d) {
						if ($i < 3) {
							continue;
						}
						$strSql.=$d[18];
						if($i < sizeof($data)){
							$strSql.= ',';
						}
					}
					$strSql.= ')';
					$listPostcode = Postcode::model()->findAll($strSql);
					foreach ($data as $i => $d) {
						if ($i < 3) {
							continue;
						}
						$isMatch = false;
						foreach($listPostcode as $j=>$objPostcode){
							if($objPostcode->postcode ==$d[18] && $objPostcode->state ==strtoupper($d[17]) && $objPostcode->suburb ==strtoupper($d[16]) ){
								$isMatch = true;
								break;
							}
						}
						if(!$isMatch){
							$err[] = $d[16].' '.$d[17].' '.$d[18]." not match, line: ".$i;
						}
					}
					if (!empty($err)) {
						$resp['msg'] = implode(';', $err);
						$resp['done'] = false;
						echo json_encode($resp);
						return;
					}
					
					
					//validate pieces
					$numPieces = 0;
					$listFromTo = [];
					foreach ($data as $i => $d) {
						if ($i < 3) {
							continue;
						}
						
						$listFrom = explode('-',$d[6]);
						$listTo = explode('-',$d[7]);
						$listFromTo[]= $listFrom[1];
						$listFromTo[]= $listTo[1];
						
						$numPieces += $d[13];
					}
					sort($listFromTo);
					for($i=1;$i< sizeof($listFromTo)-1;$i+=2){
						if($listFromTo[$i+1] - $listFromTo[$i] !=1 ){
							$err[] = "number_from and number_to not successive";
						}
					}
					if($numPieces != $listFromTo[sizeof($listFromTo)-1]){
						$err[] = "number_from number_to pieces not match";
					}
	
					if (!empty($err)) {
						$resp['msg'] = implode(';', $err);
						$resp['done'] = false;
						echo json_encode($resp);
						return;
					}
					
					//job
					$strMainNo= $data[3][5];
					$objWmsjob = new WmsJob();
					$objWmsjob->ref = $strMainNo;
					$objWmsjob->type = WmsJob::type_split_delivery;
					$objWmsjob->org_id =  Yii::app()->session['org_id'];
					$objWmsjob->status = WmsJob::status_new;
					$objWmsjob->dpt_id = $id;
					$objWmsjob->save();
					
					// task
					$objTaskMain = new WmsTask;
					$objTaskMain->job_id = $objWmsjob->id;
					$objTaskMain->type = WmsTask::TYPE_Split_Delivery;
					$objTaskMain->ref = $strMainNo; // 原单号
					$objTaskMain->status = WmsTask::STATUS_NEW;
					$objTaskMain->is_request = 1;
					$objTaskMain->dpt_id = $objWmsjob->dpt_id;
					$objTaskMain->save();
					
					// split sortting
					$objActionTask = $objTaskMain->actionTask;
					$listRecords = [];
					
					foreach ($data as $i => $d) {
						if ($i < 3) {
							continue;
						}
						$listRecordsCol = [];
						$listRecordsCol['number_origin'] = $d[8];
						$listRecordsCol['number_from'] = $d[6];
						$listRecordsCol['number_to'] = $d[7];
						$listRecordsCol['weight'] = $d[9];
						$listRecordsCol['cbm'] = $d[11];
						$listRecordsCol['pieces'] = $d[13];
						$listRecordsCol['address'] = $d[15];
						$listRecordsCol['suburb'] = $d[16];
						$listRecordsCol['region'] = $d[17];
						$listRecordsCol['postcode'] = $d[18];
						$listRecordsCol['contacter'] = $d[19];
						$listRecordsCol['mobile'] = $d[20];
						$listRecordsCol['email'] = $d[21];
						$listRecordsCol['address_type'] = $d[22];
						$listRecordsCol['postfix'] = [];
						$listRecordsCol['pallet'] = [];
						
						$listRecords[] = $listRecordsCol;
					}
					
					$objActionTask->mdata['records'] = $listRecords;
					$objActionTask->save();
					
					echo json_encode($resp);
				}
			}
		}
	}

	/*/
	 * this is used to swith type  between delivery and pick up
	 */
	public function actionSwitchType($id)
	{
		$model = WmsTask::model()->findByPk($id);
		$model->type = $_GET['type'];
		$model->save();
		// $this->ajaxResult($model);
		$this->render('update_task', array('model' => $model->mainTask));
	}

	public function actionPickSelect($id)
	{
		$model = WmsJob::model()->findByPk($id);
		$_GET['tabid'] = '112412abde';
		$this->render('pick_sku', array(
			'model' => $model,
		));
	}

	public function actionPasteAddr()
	{
		echo json_encode($this->pasteAddrProcess($_GET['p']));
	}

	private function pasteAddrProcess($p)
	{
		$p = str_replace('、', ',', trim($p));
		$p = AppHelper::semiAngle($p);
		$p = preg_replace('/:\s+/', ':', $p);
		$ps = preg_split('/[\s,;]+/', $p);
		$r = ['name' => '', 'tel' => '', 'addr' => '', 'cnid_no' => ''];
		$k = null;
		foreach ($ps as $p) {
			if (in_array($p, ['身份证', '收件人', '联系人', '地址', '电话'])) {
				continue;
			}

			if (preg_match('/^(.+):(.+)/', $p, $m)) {
				$k = null;
				if (preg_match('/电话|手机|号码/', $m[1])) {
					$k = 'tel';
				} elseif (preg_match('/地址|寄到/', $m[1])) {
					$k = 'addr';
				} elseif (preg_match('/人|姓名/', $m[1])) {
					$k = 'name';
				} elseif (preg_match('/身份证/', $m[1])) {
					$k = 'cnid_no';
				}
				$p = $m[2];
			}

			if (preg_match('/^\d{18}$/', $p)) {
				$r['cnid_no'] = $p;
			} elseif (preg_match('/([\d\- \(\)]{9,16})/', $p, $m)) {
				$r['tel'] = preg_replace('/[ \(\)\-]+/', '', $m[1]);
				$p = preg_replace('/[\d\- \(\)]{9,16}/', '', $p);
				if (($k == 'addr' || mb_strlen($p) > 5 || preg_match('/(省|市|区|县)$/', $p)) && preg_match('/省|市|区|县|镇|村|乡|路|街|号|楼|室/', $p)) {
					$r['addr'] .= rtrim($p, '.');
				} elseif (preg_match('/[\x{4e00}-\x{9fa5}·\.]+/u', $p)) {
					$r['name'] = rtrim($p, '.');
				}
			} elseif (($k == 'addr' || mb_strlen($p) > 5 || preg_match('/(省|市|区|县)$/', $p)) && preg_match('/省|市|区|县|镇|村|乡|路|街|号|楼|室/', $p)) {
				$r['addr'] .= rtrim($p, '.');
			} elseif (preg_match('/[\x{4e00}-\x{9fa5}·\.]+/u', $p)) {
				if (mb_strlen($p) == 2 && (CnProvince::model()->count('name LIKE :p', array(':p' => $p . "%")) > 0 || CnCity::model()->count('name LIKE :p', array(':p' => $p . "%")) > 0)) {
					$r['addr'] .= $p;
					continue;
				}

				$r['name'] = rtrim($p, '.');
			} elseif (!empty($k)) {
				$r[$k] .= $p;
			}
		}
		if (empty(!$r['addr'])) {
			$a = new Addr();
			$a->setCnAddr($r['addr']);
			$r['state'] = $a->state;
			$r['city'] = $a->city;
			$r['suburb'] = $a->suburb;
			$r['address'] = $a->address;
			$r['postcode'] = $a->postcode;
		}

		return $r;
	}

	public function actionPrint($id)
	{
		$model = WmsTask::model()->findByPk($id);
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
				if ($tw == 0) {
					echo 'please enter weight';
					return;
				}

				if (empty($model->mdata['shipment_id']) || empty($model->mdata['shipment_courier_id']) || $model->mdata['shipment_courier_id'] != $model->mdata['courier'] ||
					($model->mdata['courier'] == 115 && sizeof($model->mdata['shipment_id']) != $pkg)) {
					$rs = $model->toShipment(); //multiple shipments in array
				} else {
					foreach ($model->mdata['shipment_id'] as $sid) {
						$p = Shipment::model()->find('id=:id', array(':id' => $sid));
						if (!empty($p)) {
							$rs[] = $p;
						}

					}
				}

				if (!empty($rs)) {
					if ($rs[0]->type == 10) {
						if (empty($rs[0]->id)) {
							print_r($rs[0]->getErrors());
						} else {
							oPDF::renderPDF('label_A6', array('rs' => $rs), 1, 'label.pdf');
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
		}
	}

	public function actionExport()
	{
		switch ($_GET['type']) {
			case 'courier':
				{
					$xls = new oExcel;
					$i = 1;
					$xls->addRow($i++, ['Task no.', 'Task Ref', 'Schedule Time', 'Complete Time', 'Courier','Tracking Id', 'Cnee', 'Tel', 'Address', 'City', 'Suburb', 'State', 'Postcode', 'Country']);
					$xls->setColWidth([10,10,10,10,15,10,15,10,10,10,10,10,10]);
					$tasks = WmsTask::model()->with('job')->findAll('t.status = 99 AND t.is_request = 1 AND t.type in (3010,3020,3030) AND job.org_id = :oid', array(':oid' => Yii::app()->user->org));
					foreach ($tasks as $task) {
						if (empty($task->deliveryTask) || empty($task->deliveryTask->mdata['shipment_id'])) {
							continue;
						}
						$sql = 'SELECT * FROM shipment WHERE id in (' . implode(',', $task->deliveryTask->mdata['shipment_id']) . ')';
						$shipments = Yii::app()->db->createCommand($sql)->queryAll();
						$ref = '';
						foreach ($shipments as $shipment) {
							$ref .= $shipment['ref'] . ', ';
						}
						if($task->deliveryTask->mdata['courier']==3079){
							$courierName ="Toll";
						}else{
							$courierName = empty($task->deliveryTask->mdata['courier'])? "":Org::model()->findByPk($task->deliveryTask->mdata['courier'])->name;
						}
						$cnee = Addr::model()->findByPk($shipment['cnee_id']);
						$xls->addRow($i++, ['T'.$task->id, $task->ref, date('Y-m-d', strtotime($task->schd_time)), date('Y-m-d', strtotime($task->compl_time)),$courierName, $ref, $cnee->name, $cnee->tel, $cnee->address, $cnee->city, $cnee->suburb, $cnee->state, $cnee->postcode, $cnee->country]);
					}
					$xls->output('wms_task_courier_export_' . time() . '.xlsx');
				}
				break;
			case 'search':
				$model = new WmsTask('search');
				$model->unsetAttributes();
				if (!empty($_GET['WmsTask'])) {
					unset($_GET['WmsTask']['skus']);
					$model->attributes = $_GET['WmsTask'];
				}
				$model->is_request = 1;

				$ec = new CDbCriteria;
				$ec->addCondition('t.type in (3020, 3030)');
				$ec->with = ['job'];
				$ec->addCondition('job.org_id = ' . Yii::app()->user->org);
				$dp = $model->search(false, 0, $ec);

				$xls = new oExcel;
				$xls->setTitle('Pick & Pack Task');
				$i = 1;
				$xls->addRow($i++, ['ID', 'Ref #', 'Status', 'Schd Time', 'Company', 'Cnee', 'Tel', 'Address', 'City', 'Suburb', 'State', 'Postcode', 'Country', 'Product', 'Brand', 'EAN', 'SKU', 'Qty', 'Expiry', 'Batch', 'Courier']);
				$xls->setColWidth([15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15, 15]);
				foreach ($dp->data as $task) {
					$overview = [$task->no, $task->ref, $task->getStatus(), $task->schd_time];
					if (!empty($task->deliveryTask)) {
						$cnee = @$task->deliveryTask->mdata['cnee'];
						$overview = array_merge($overview, [@$cnee['company'], @$cnee['name'], @$cnee['tel'], @$cnee['address'], @$cnee['city'], @$cnee['suburb'], @$cnee['state'], @$cnee['postcode'], @$cnee['country']]);
					} else {
						$overview = array_merge($overview, ['', '', '', '', '', '', '', '', '']);
					}

					$items = [];
					$taskItems = $task->items;
					foreach ($taskItems as $item) {
						$stock = WmsStock::model()->findByPk($item->mdata['si']);

						if (empty($stock) && !empty($item->mdata['pi'])) {
							$stock = WmsStock::model()->with('prod')->find('prod.id = :pi', [':pi' => $item->mdata['pi']]);
						}

						if (!empty($stock)) {
							$org = WmsProdOrg::model()->find('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $stock->prod->id, ':org_id' => Yii::app()->user->org]);
							$stock_key = $stock->prod->id . '_' . $stock->expiry . '_' . $stock->batch;
							$stock_value = [$stock->prod->name, $stock->prod->brand, $stock->prod->ean, @$org->sku, 0, $stock->expiry, $stock->batch];
							$stock_prod_id = $stock->prod->id;
						} else if (!empty($item->mdata['pi'])) {
							$prod = WmsProd::model()->findByPk($item->mdata['pi']);
							$org = WmsProdOrg::model()->find('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $prod->id, ':org_id' => Yii::app()->user->org]);
							$stock_key = $prod->id . '_' . ' ' . '_' . ' ';
							$stock_value = [$prod->name, $prod->brand, $prod->ean, @$org->sku, 0, ' ', ' '];
							$stock_prod_id = $prod->id;
						}

						if (!empty($item->mdata['uq'])) {
							$uq = $item->mdata['uq'];
						} else if (!empty($item->mdata['cq'])) {
							$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = :type', [':prod_id' => $stock_prod_id, ':type' => array_search('Carton', WmsProdPack::$types)]);
							if (!empty($pack)) {
								$uq = $pack->qty * $item->mdata['cq'];
							} else {
								$uq = 0;
							}
						} else {
							$uq = 0;
						}

						if (empty($items[$stock_key])) {
							$items[$stock_key] = $stock_value;
						}
						$items[$stock_key][4] += $uq;
					}

					$prods = [];
					if (!empty($task->deliveryTask->mdata['shipment_id'])) {
						foreach ($task->deliveryTask->mdata['shipment_id'] as $shipment) {
							$shipment = Shipment::model()->findByPk($shipment);
							if (empty($shipment->eitems['g'])) {
								if (empty($prods['empty'])) {
									$prods['empty'] = [];
								}
								$prods['empty'][] = $shipment->ref;
								continue;
							}

							foreach ($shipment->eitems['g'] as $prod) {
								if (empty($prods[$prod])) {
									$prods[$prod] = [];
								}
								$prods[$prod][] = $shipment->ref;
							}
						}
					}

					foreach ($items as $k => $item) {
						$stock = WmsStock::model()->findByPk(explode('_', $k)[0]);

						$courier = '';
						if (!empty($prods[$item[0]])) {
							$prods[$item[0]] = array_unique($prods[$item[0]]);
							$courier = implode(', ', $prods[$item[0]]);
						} else if (!empty($prods['empty'])) {
							$prods['empty'] = array_unique($prods['empty']);
							$courier = implode(', ', $prods['empty']);
						}

						$xls->addRow($i++, array_merge($overview, $item, [$courier]));
					}

					$xls->addRow($i++, []);
				}

				$ec = new CDbCriteria;
				$ec->addCondition('t.type in (1010, 1020)');
				$ec->with = ['job'];
				$ec->addCondition('job.org_id = ' . Yii::app()->user->org);
				$dp = $model->search(false, 0, $ec);

				$xls->createSheet('Stock In Task');
				$xls->goSheet(1);
				$i = 1;
				$xls->addRow($i++, ['ID', 'Ref #', 'Status', 'Schd Time', 'Product', 'Brand', 'SKU', 'Qty', 'Expiry', 'Batch']);
				$xls->setColWidth([15, 15, 15, 15, 15, 15, 15, 15, 15, 15]);
				foreach ($dp->data as $task) {
					if (in_array(Yii::app()->user->org, Org::$easyships)) {
						$overview = [$task->no, $task->ref, $task->getEasyshipStatus(), $task->schd_time];
					} else {
						$overview = [$task->no, $task->ref, $task->getStatus(), $task->schd_time];
					}

					$items = [];
					foreach ($task->actionTask->items as $item) {
						$prod = WmsProd::model()->findByPk($item->mdata['gi']);
						if (!empty($item->mdata['uq'])) {
							$uq = $item->mdata['uq'];
						} else if (!empty($item->mdata['cq'])) {
							$pack = WmsProdPack::model()->find('prod_id = :prod_id AND type = :type', [':prod_id' => $prod->id, ':type' => array_search('Carton', WmsProdPack::$types)]);
							if (!empty($pack)) {
								$uq = $pack->qty * $item->mdata['cq'];
							} else {
								$uq = 0;
							}
						} else {
							$uq = 0;
						}

						if (empty($items[$prod->name . $item->mdata['ex'] . $item->mdata['bn']])) {
							$items[$prod->name . $item->mdata['ex'] . $item->mdata['bn']] = [$prod->name, $prod->brand, $prod->ean, 0, $item->mdata['ex'], $item->mdata['bn']];
						}
						$items[$prod->name . $item->mdata['ex'] . $item->mdata['bn']][3] += $uq;

					}

					foreach ($items as $k => $item) {
						if ($k == array_keys($items)[0]) {
							$xls->addRow($i++, array_merge($overview, $item));
						} else {
							$xls->addRow($i++, array_merge(['', '', '', ''], $item));
						}
					}

					$xls->addRow($i++, []);
				}

				$xls->goSheet(0);
				$xls->output('task_export_' . time() . '.xlsx');
				break;
			case 'shopify':
				$xls = new oExcel('CSV');
				$i = 1;
				$xls->addRow($i++, ['Order Number', 'Tracking Number', 'SKU', 'Quantity', 'Tracking Company']);
				$xls->setColWidth([10,10,10,10,10]);
				$tasks = WmsTask::model()->with('job')->findAll('t.status = 99 AND t.compl_time > :time AND t.is_request = 1 AND t.type in (3020,3030) AND job.org_id = :oid', array(':time' => date('Y-m-d', strtotime(date('Y-m-d') . ' - 1 week')), ':oid' => Yii::app()->user->org));
				foreach ($tasks as $task) {
					if (empty($task->deliveryTask) || empty($task->deliveryTask->mdata['shipment_id'])) {
							continue;
					}
					$wms_map = WmsTaskMap::model()->find('task_id = :task_id', [':task_id' => $task->id]);
					if (empty($wms_map)) {
						continue;
					}

					$sql = 'SELECT * FROM shipment WHERE id in (' . implode(',', $task->deliveryTask->mdata['shipment_id']) . ')';
					$shipments = Yii::app()->db->createCommand($sql)->queryAll();
					$ref = '';
					foreach ($shipments as $shipment) {
						$ref = $shipment['ref'];
					}
					$prods = [];
					foreach ($task->actionTask->items as $item) {
						$stock = WmsStock::model()->findByPk($item->mdata['si']);
						if (empty($prods[$stock->prod_id])) $prods[$stock->prod_id] = 0;
						$prods[$stock->prod_id] += $item->mdata['uq'];
					}
					foreach ($prods as $id => $qty) {
						$prod = WmsProd::model()->findByPk($id);
						$sku = $prod->getSku(Yii::app()->user->org);
						if (empty($sku)) {
							$sku = $prod->ean;
						}
						$xls->addRow($i++, [explode(' ', $task->ref)[0], $ref, $sku, $item->mdata['uq'], $task->getCourierCompany()]);
					}
				}
				$xls->output('wms_task_shopify_fulfill_' . time() . '.csv');
				break;
		}
	}

	// public function actionCreateReturnLabel()
	// {
	// 	$model = WmsTask::model()->findByPk($_POST['id']);
	// 	if (empty($model->mdata['cnee']['name']) || empty($model->mdata['cnee']['address']) || empty($model->mdata['cnee']['suburb']) || empty($model->mdata['cnee']['postcode']) || empty($model->mdata['cnee']['state']) || empty($model->mdata['cnee']['country'])) {
	// 		$model->addError('id', 'Consignee info is incomplete');
	// 		$this->ajaxResult($model);
	// 	}

	// 	if ($model->mdata['cnee']['country'] != 'AU') {
	// 		$model->addError('id', 'Australia only');
	// 		$this->ajaxResult($model);
	// 	}

	// 	$label = $model->createReturnLabel($_POST['pkgs'], $_POST['weights']);
	// 	$this->ajaxResult($model);
	// }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = WmsJob::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

}
