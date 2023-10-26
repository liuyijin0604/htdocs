<?php

class EdiJobAirlineController extends Controller
{
	protected $nonAjax = ['export'];

	public function actionList()
	{
		if (empty($_SESSION['edijob-airline-status'])) {
			$_SESSION['edijob-airline-status'] = '< 30';
		}
		if (!empty($_GET['EdiJob']['status'])) {
			$_SESSION['edijob-airline-status'] = $_GET['EdiJob']['status'];
		}

		$model = new EdiJob('search');
		$model->unsetAttributes();
		$model->status = $_SESSION['edijob-airline-status'];
		if (isset($_GET['EdiJob'])) {
			$model->attributes = $_GET['EdiJob'];
		}

		$this->render('list', ['model' => $model]);
	}

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		if (empty($_POST)) {
			$this->render('update', ['model' => $model]);
		} else {
			if (@$_POST['yt1'] == 'Save') {
				EdiJobAirline::model()->deleteAll('job_id = :job_id', [':job_id' => $model->id]);
				foreach ($_POST['flight'] as $k1 => $flights) {
					foreach ($flights as $k2 => $v) {
						$item = new EdiJobAirline;
						$item->job_id = $model->id;
						$item->flight_no = $v;
						$item->transit = $k1;
						$item->atd = $_POST['atd'][$k1][$k2];
						$item->ata = $_POST['ata'][$k1][$k2];
						$item->plt = $_POST['plt'][$k1][$k2];
						$item->save();
					}
				}

				$model->mdata['transit_type'] = $model->getTransitType();
				$model->mdata['ata'] = $model->getATA();
				$model->mdata['result'] = $model->getResult();
				$model->update('meta');
			} else if (@$_POST['yt2'] == '货齐') {
				$model->mdata['all_done'] = true;
				$model->update('meta');
			}
			$this->ajaxResult($model);
		}
	}

	public function actionComplete($id)
	{
		$model = $this->loadModel($id);
		$model->status = 30;
		$model->mdata['transit_type'] = $model->getTransitType();
		$model->mdata['ata'] = $model->getATA();
		$model->mdata['result'] = $model->getResult();
		$model->update('status', 'meta');
		$this->ajaxResult($model);
	}

	public function actionReport()
	{
		$record = [];
		$jobs = EdiJob::model()->findAll('created >= :date', [':date' => date('Y-m-d', strtotime(date('Y-m-d') . ' - ' . date('N') . ' day'))]);
		$record['This Week'] = [0,0,0];
		foreach ($jobs as $job) {
			if ($job->getResult() == '合格') {
				$record['This Week'][1] ++;
			} else if ($job->getResult() == '不合格') {
				$record['This Week'][2] ++;
			}

			$record['This Week'][0] ++;
		}

		$jobs = EdiJob::model()->findAll('created >= :date', [':date' => date('Y-m-01')]);
		$record['This Month'] = [0,0,0];
		foreach ($jobs as $job) {
			if ($job->getResult() == '合格') {
				$record['This Month'][1] ++;
			} else if ($job->getResult() == '不合格') {
				$record['This Month'][2] ++;
			}

			$record['This Month'][0] ++;
		}

		$jobs = EdiJob::model()->findAll('created >= :date', [':date' => date('Y-m-01', strtotime(date('Y-m-d') . ' - 1 month'))]);
		$record['Last Month'] = [0,0,0];
		foreach ($jobs as $job) {
			if ($job->getResult() == '合格') {
				$record['Last Month'][1] ++;
			} else if ($job->getResult() == '不合格') {
				$record['Last Month'][2] ++;
			}

			$record['Last Month'][0] ++;
		}

		$jobs = EdiJob::model()->findAll('created >= :date', [':date' => date('Y-m-01', strtotime(date('Y-m-d') . ' - 5 month'))]);
		$record['In 6 months'] = [0,0,0];
		foreach ($jobs as $job) {
			if ($job->getResult() == '合格') {
				$record['In 6 months'][1] ++;
			} else if ($job->getResult() == '不合格') {
				$record['In 6 months'][2] ++;
			}

			$record['In 6 months'][0] ++;
		}

		if (empty($_SESSION['edijob-airline-report-status'])) {
			$_SESSION['edijob-airline-report-status'] = '< 30';
		}
		if (!empty($_GET['EdiJob']['status'])) {
			$_SESSION['edijob-airline-report-status'] = $_GET['EdiJob']['status'];
		}

		$model = new EdiJob('search');
		$model->unsetAttributes();
		$model->status = $_SESSION['edijob-airline-report-status'];
		if (isset($_GET['EdiJob'])) {
			$model->attributes = $_GET['EdiJob'];
		}

		$this->render('report', ['model' => $model, 'record' => $record]);
	}

	public function actionExport()
	{
		// $model = new EdiJob('search');
		// $model->unsetAttributes();
		// $model->status = $_SESSION['edijob-airline-report-status'];
		// if (isset($_GET['EdiJob'])) {
		// 	$model->attributes = $_GET['EdiJob'];
		// }

		// $ec = new CDbcriteria;
		// $ec->with = ['owner'];
		// $ec->addCondition('JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')');

		// $xls = new oExcel;
		// $i = 1;
		// $xls->addRow($i++, ['AWB', 'Pallet', 'In Date', 'Product', '', 'Flight NO', 'ATD', 'ATA']);
		// $xls->setColWidth([15,15,15,15,30,15,15,15]);
		// $dp = $model->search(false, $ec);
		// foreach ($dp->data as $r) {
		// 	$products = [];
		// 	foreach ($r->wmstasks as $task) {
		// 		$ledgers = WmsStockLedger::model()->with('taskItem.task')->findAll('task.id = :id', [':id' => $task->actionTask->id]);
		// 		foreach ($ledgers as $ledger) {
		// 			$products[] = $ledger->location_id . '_' . $ledger->stock->prod_id;
		// 		}
		// 	}
		// 	$products = array_values(array_unique($products));
		// 	$airlines = EdiJobAirline::model()->findAll('job_id = :job_id', [':job_id' => $r->id]);

		// 	for ($k = 0; $k < max(count($products), count($airlines)); $k++) {
		// 		if ($k == 0) {
		// 			$line = [$r->awb];
		// 		} else {
		// 			$line = [''];
		// 		}

		// 		if (!empty($products[$k])) {
		// 			$plt = WmsLocation::model()->findByPk(explode('_', $products[$k])[0]);
		// 			$ledger = WmsStockLedger::model()->find(['condition' => 'location_id = :location_id', 'params' => [':location_id' => $plt->id], 'order' => 'id ASC']);
		// 			$prod = WmsProd::model()->findByPk(explode('_', $products[$k])[1]);
		// 			$line = array_merge($line, [$plt->name, date('Y-m-d', strtotime($ledger->ts)), $prod->name]);
		// 		} else {
		// 			$line = array_merge($line, ['', '', '']);
		// 		}

		// 		if (!empty($airlines[$k])) {
		// 			$tmep = $airlines[$k];
		// 			$line = array_merge($line, ['', $tmep->flight_no, $tmep->atd, $tmep->ata]);
		// 		}

		// 		$xls->addRow($i++, $line);
		// 	}

		// 	$xls->addRow($i++, []);
		// }

		$model = new EdiJob('search');
		$model->unsetAttributes();
		$model->status = $_SESSION['edijob-airline-report-status'];
		if (isset($_GET['EdiJob'])) {
			$model->attributes = $_GET['EdiJob'];
		}

		$ec = new CDbcriteria;
		$ec->with = ['owner'];
		$ec->addCondition('JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')');

		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['Planned Date of Delivery', 'Actual Date of Delivery', 'PO No.', 'Description', 'No. of Pallet', 'if not full pallet, master carton /pallet', 'mode of transportation', 'Tracking No.', 'Loading Plan', 'Expected ATA', 'HBL No.', 'Origin', 'Dest', '1st flight ETD', '1st flight ATD', '1st flight ETA', '1st flight ATA', '1st flight No. of Pallet', '2nd flight ETD', '2nd flight ATD', '2nd flight ETA', '2nd flight ATA', '2nd flight No. of Pallet', '3rd flight ETD', '3rd flight ATD', '3rd flight ETA', '3rd flight ATA', '3rd flight No. of Pallet']);
		$xls->setColWidth([16,13,21,26,11,19,14,14,8,12,10,8,8,18,18,18,18,18,18,18,18,18,18,18,18,18,18,18]);
		$dp = $model->search(false, $ec);
		foreach ($dp->data as $r) {
			$products = [];
			foreach ($r->wmstasks as $task) {
				$ledgers = WmsStockLedger::model()->with('taskItem.task')->findAll('task.id = :id', [':id' => $task->actionTask->id]);
				foreach ($ledgers as $ledger) {
					$date = date('Y-m-d', strtotime($ledger->ts));
					$prod = $ledger->stock->prod;
					$loc = $ledger->loc;
					if (empty($products[$task->id][$date][$prod->id][$loc->id])) {
						$products[$task->id][$date][$prod->id][$loc->id] = 1;
					}
				}
			}

			$airlines = EdiJobAirline::model()->findAll('job_id = :job_id', [':job_id' => $r->id]);
			if (empty($airlines)) {
				continue;
			} else {
				$job = @$airlines[0]->job;
				$first = [];
				$second = [];
				$third = [];
				foreach ($airlines as $airline) {
					if ($airline->transit == 1) {
						$first[] = [$job->awbconsol->etd, $airline->atd, $job->awbconsol->eta, $airline->ata, $airline->plt];
					} else if ($airline->transit == 2) {
						$second[] = [$job->awbconsol->etd, $airline->atd, $job->awbconsol->eta, $airline->ata, $airline->plt];
					} else if ($airline->transit == 3) {
						$third[] = [$job->awbconsol->etd, $airline->atd, $job->awbconsol->eta, $airline->ata, $airline->plt];
					}
				}
			}

			$k = 0;
			$job = @$airlines[0]->job;
			if (!empty($products)) {
				foreach ($products as $task_id => $tasks) {
					$task = WmsTask::model()->findByPk($task_id);
					foreach ($tasks as $date => $dates) {
						foreach ($dates as $prod_id => $locs) {
							$prod = WmsProd::model()->findByPk($prod_id);
							$xls->addRow($i++, array_merge(['', $date, $task->ref, $prod->name, count($locs), '', '', @$job->awb, count($locs), @$job->awbconsol->eta, '', @$job->awbconsol->pol, @$job->awbconsol->pod], !empty($first[$k]) ? $first[$k] : ['', '', '', '', ''], !empty($second[$k]) ? $second[$k] : ['', '', '', '', ''], !empty($third[$k]) ? $third[$k] : ['', '', '', '', '']));
							$k ++;
						}
					}
				}
			} else {
				$xls->addRow($i++, array_merge(['', '', '', '', '', '', '', @$job->awb, @$job->getWmsTaskPlt(), @$job->awbconsol->eta, '', @$job->awbconsol->pol, @$job->awbconsol->pod], !empty($first[$k]) ? $first[$k] : ['', '', '', '', ''], !empty($second[$k]) ? $second[$k] : ['', '', '', '', ''], !empty($third[$k]) ? $third[$k] : ['', '', '', '', '']));
			}

			for (; $k < max(count($first), count($second), count($third)); $k++) {
				$xls->addRow($i++, array_merge(['', '', '', '', '', '', '', '', '', '', '', '', ''], !empty($first[$k]) ? $first[$k] : ['', '', '', '', ''], !empty($second[$k]) ? $second[$k] : ['', '', '', '', ''], !empty($third[$k]) ? $third[$k] : ['', '', '', '', '']));
			}

			$xls->addRow($i++, ['']);
		}

		$xls->output('kpi_report.xlsx');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = EdiJob::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

}