<?php

class WmsJobController extends Controller
{
	protected $nonAjax = ['export','print'];
	protected $skipAcl = [];

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view', [
			'model'=>$this->loadModel($id),
		]);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new WmsJob;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['WmsJob'])) {
			$model->attributes=$_POST['WmsJob'];
			$model->save();
			$this->ajaxResult($model, ['id', 'no']);
		}
		$model->status = 10;

		$this->render('create', [
			'model'=>$model,
		]);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['WmsJob'])) {
			$pretype = $model->type;
			$model->attributes=$_POST['WmsJob'];
			$model->save();
			if (isset($_POST["create_invoice"])) {
				$consol_no=isset($_POST['consol_no'])?$_POST['consol_no']:0;
				$errors=$model->genValueAddedInvoice($consol_no);
				if (!empty($errors)) {
					$model->addError('GenInvoice Failed', implode('; ', $errors));
				}
			}
			if ($model->type == WmsJob::TYPE_PICK_LOAD && $pretype != WmsJob::TYPE_PICK_LOAD) {
				$picks = 0;
				foreach ($model->tasks as $task) {
					if ($picks > 1) {
						$model->type = $pretype;
						$model->save();
						$model->addError('id', 'Cannot transfer to Pick & Load job');
						break;
					}
					if ($task->type == WmsTask::TYPE_PICK_PALLET) {
						$picks ++;
					} else if (!in_array($task->type, [WmsTask::TYPE_PICK_PALLET, WmsTask::TYPE_CONTAINER_LOAD])) {
						$model->type = $pretype;
						$model->save();
						$model->addError('id', 'Cannot transfer to Pick & Load job');
						break;
					}
				}
			}
			$this->ajaxResult($model);
		}
				
				
		
		if (isset($_GET['tab'])) {
			Acl::hasAccess($this->CaName.'/'.$_GET['tab'], true);
			$this->render('tab_' . $_GET['tab'], ['model' => $model]);
		} else {
			$this->render('update', ['model'=>$model]);
		}
	}

	public function actionExport($id)
	{
		$model = WmsJob::model()->findByPk($id);
		switch ($_GET['t']) {
			case 'cmb_pik':
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth([15,15,40,20,20,15,15,10,10,30]);
				$xls->addRow($i++, ['Combined Picking for Job', $model->no]);
				$xls->addRow($i++, ['Date', date('Y-m-d')]);
				$xls->addRow($i++, ['Location', 'Pallet', 'Prod. Name', 'Prod. Brand', 'Prod. Model', 'Prod. EAN', 'Cust. SKU', 'Pick Qty', 'Pick Tot', 'Notes']);
				$tt = [];
				foreach ($model->tasks as $t) {
					if ($t->is_request == 0 || substr($t->type, 0, 2) != 30) {
						continue;
					}
					if ($t->status >= 99) {
						continue;
					}
					foreach ($t->items as $itm) {
						if (empty($itm->mdata['sid'])) {
							continue;
						}
						if (!isset($tt[$itm->mdata['si']])) {
							$tt[$itm->mdata['si']] = 0;
						}
						$tt[$itm->mdata['si']] += $itm->mdata['uq'];
					}
				}

				$ss = [];
				foreach ($tt as $sid => $tq) {
					$rs = WmsStockLocation::model()->findAll('stock_id = :sid AND qty > 0', [':sid' => $sid]);
					$pq = $tq;
					foreach ($rs as $r) {
						if ($pq <= 0) {
							break;
						}
						$xls->addRow($i++, [empty($r->loc->pid)? '' : $r->loc->parent->code, $r->loc->code, $r->stock->prod->name, $r->stock->prod->brand, $r->stock->prod->model, '="'.$r->stock->prod->ean.'"', '="'.$r->stock->getCustSKU().'"', ($r->qty < $pq)? $r->qty : $pq, $tq, '']);
						$pq -= $r->qty;
					}
				}

				$xls->output('combined_picking_'.$model->no.'.xlsx');
			break;
			case 'odr_pik':
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth([15,15,40,20,20,15,15,10,10,30]);
				foreach ($model->tasks as $t) {
					if ($t->is_request == 0 || substr($t->type, 0, 2) != 30) {
						continue;
					}
					$xls->addRow($i++, ['Order Picking for Job', $model->no.'/'.$t->getNo()]);
					$xls->addRow($i++, ['Ref', $t->ref]);
					$xls->addRow($i++, ['Date', date('Y-m-d')]);
					$xls->addRow($i++, ['Location', 'Pallet', 'Prod. Name', 'Prod. Brand', 'Prod. Model', 'Prod. EAN', 'Cust. SKU', 'Pick Qty', 'Pick Tot', 'Notes']);
					$apd = [];
					foreach ($t->items as $itm) {
						if (empty($itm->mdata['si'])) {
							continue;
						}
						$rs = WmsStockLocation::model()->findAll('stock_id = :sid AND qty > 0', [':sid' => $itm->mdata['si']]);
						$pq = $itm->mdata['uq'];
						$tq = $pq;
						foreach ($rs as $r) {
							if ($pq <= 0) {
								break;
							}
							if (!isset($apd[$r->id])) {
								$apd[$r->id] = 0;
							}
							$aq = $r->qty - $apd[$r->id];
							$cq = ($aq < $pq)? $aq : $pq;

							$xls->addRow($i++, [empty($r->loc->pid)? '' : $r->loc->parent->code, $r->loc->code, $r->stock->prod->name, $r->stock->prod->brand, $r->stock->prod->model, '="'.$r->stock->prod->ean.'"', '="'.$r->stock->getCustSKU().'"', $cq, $tq, '']);
							$apd[$r->id] += $cq;
							$pq -= $r->qty;
						}
					}

					$xls->addRow($i++, []);
				}

				$xls->output('orders_picking_'.$model->no.'.xlsx');
			break;
			case 'cmb_pak':
				$xls = new oExcel;
				$i = 1;
				$xls->setColWidth([15,30,15,15,15,15,10,15,15,15,20,30]);
				//$xls->addRow($i++, array('Job #'.$model->no));
				$xls->addRow($i++, ['Task', 'Date', 'Container Size',  'Container #', 'Seal #', 'Pallet', 'Prod. Name', 'Prod. Brand', 'Prod. Model', 'Prod. EAN', 'Expiry Date', 'Batch', 'Cartons', 'Unit', 'Weight', 'Load Time', 'Notes']);
				$rows = [];
				$plt = [];
				foreach ($model->tasks as $tsk) {
					foreach ($tsk->actionTask->items as $k => $itm) {
						if (empty($itm->mdata['pli'])) {
							continue;
						}
						$rs = WmsStockLocation::model()->findAll('location_id = :pli', [':pli' => $itm->mdata['pli']]);
						foreach ($rs as $j => $r) {
							$wsl = WmsStockLedger::model()->find('ti_id = :tid AND location_id = :lid AND stock_id = :sid AND l2_id = 5 AND qty_out > 0', [':tid' => $itm->id, ':lid' => $r->location_id, ':sid' => $r->stock_id]);
							$uq = $wsl->qty_out;
							if (empty($uq)) {
								continue;
							}
							$cq = $r->stock->prod->uq2cq($uq);
							$rows[] = [$tsk->no, substr($tsk->schd_time, 0, 10), $tsk->mdata['ctn_size'], $tsk->mdata['ctn_no'], $tsk->mdata['ctn_seal'], $itm->mdata['pl'], $r->stock->prod->name, $r->stock->prod->brand, $r->stock->prod->model, '="'.$r->stock->prod->ean.'"', $r->stock->expiry, $r->stock->batch, $cq, $uq, ($r->stock->prod->getCqWeight($cq, $uq) + ($j == 0? 12 : 0)), $itm->ts, $itm->mdata['nt']];
						}
						$plt[] = $itm->mdata['pl'];
					}
				}

				foreach ($rows as $r) {
					$xls->addRow($i++, $r);
				}
				$li = $i-1;
				$xls->addRow($i++, ['', '', '', '', '', sizeof(array_unique($plt)), '', '', '', '', '', '', '=SUM(M4:M'.$li.')', '=SUM(N4:N'.$li.')', '=SUM(O4:O'.$li.')']);
				$xls->output('combined_packing_list_'.$model->no.'.xlsx');
			break;
		}
	}

	public function actionNotes($id)
	{
		$model=$this->loadModel($id);
		if (!empty($_POST['notes'])) {
			$log = Log::add($model, 6, ['notes' => $_POST['notes']]);
			$this->ajaxResult($log);
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
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : ['admin']);
			}
		} else {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	public function actionWkinv()
	{
		if (!empty($_GET['wid'])) {
			$td = date('Y-m-d', strtotime(date('Y-m-d').' -1 week'));
			$wd = date('N', strtotime($td));
			if ($wd != 5) {
				$td = date('Y-m-d', strtotime($td.' +'.(5-$wd).' day'));
			}
			echo '<p>Ending: '.$td.'</p>';
			if ($_GET['wid'] == 106) {
				$rs = WmsTask::model()->findAll('link_id = 0 AND billed = 0 AND status > 10 AND status < 100');
				$cts = [];
				foreach ($rs as $r) {
					if (!isset($cts[$r->job->org_id])) {
						$cts[$r->job->org_id] = [];
					}
					$cts[$r->job->org_id][] = $r;
				}
				foreach ($cts as $o=>$ts) {
					$warn = [];
					foreach ($ts as $t) {
						if ($t->status < 99) {
							if (empty($t->schd_time) || strtotime($t->schd_time) < strtotime($td)) {
								$warn[] = '<a class="tab_link" href="'.$this->createUrl('wmsTask/update', ['id' => $t->id]).'" title="'.$t->getNo().'">'.$t->getNo().'</a> not completed';
							}
						} else {
							//check rates
							$rc = $t->checkRates();
							if ($rc === true) {
								$t->autoCharges();
							} //add charges for task type
						}
					}
					echo '<br /><p><label><input type="checkbox" value="'.$o.'" /> <b>'.$ts[0]->job->customer->name.'</b></label></p>';
					
					if ($rc !== true) {
						echo '<p>Rates missing <a class="tab_link" href="'.$this->createUrl('org/update', ['id' => $ts[0]->job->org_id]).'" title="Org-'.$ts[0]->job->org_id.'"></a>:<br />', implode('<br />', $rc).'</p>';
					}

					if (!empty($warn)) {
						echo '<ul style="padding-left:20px;list-style:circle;"><li>'.implode('</li><li>', $warn).'</li></ul>';
					} else {
						if ($rc === true) {
							echo 'OK<br />';
						}
					}
				}
			}
		} else {
			$this->render('wkinv');
		}
	}
		
	/*
	 *
	 * print the job labels
	 */
	public function actionPrint($id)
	{
		$model=$this->loadModel($id);
		$tasks= WmsTask::model()->findAll('job_id=:jid and is_request=1 AND type=3030', [':jid'=>$id]);
		//find linked delivery label
		$shipments=[];
		foreach ($tasks as $task) {
			if ($task->status==100) {
				continue;
			}  //cancle;
			$delivery_task= WmsTask::model()->find('link_id=:lid AND type=2120', [':lid'=>$task->id]);
			if (!empty($delivery_task)) {
				if (!empty($delivery_task->mdata['shipment_id'])) {
					$p= Shipment::model()->find('id=:id', [':id'=>$delivery_task->mdata['shipment_id']]);
					if (!empty($p)) {
						$shipments[]=$p;
					}
				} else {
					if (empty($delivery_task->mdata['cnee']['name'])||empty($delivery_task->mdata['cnee']['address'])) {
						continue;
					}
					$shipments[]=$delivery_task->toShipment();
				}
			}
		}
		$fn='labels';
			   
		oPDF::renderPDF('label_A6', ['rs'=>$shipments], 1, $fn . '.pdf');
	}
		
	/**
	 * Lists and search.
	 */
	public function actionList()
	{
		$model=new WmsJob('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['WmsJob'])) {
			$model->attributes=$_GET['WmsJob'];
		}

		$this->render('list', [
			'model'=>$model,
		]);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=WmsJob::model()->findByPk($id);
		if ($model===null) {
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
		if (isset($_POST['ajax']) && $_POST['ajax']==='wms-job-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
