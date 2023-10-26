<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Pcaw
{

	public static $feqs = array(
		'event' => 'Event',
		'daily' => 'Daily',
		'weekly' => 'Weekly',
		'monthly' => 'Monthly',
	);

	public static $ranges = array(
		'one' => 'Yesterday',
		'seven' => 'Last 7 days',
		'forteen' => 'Last 14 days',
		'twenty_eight' => 'Last 28 days',
	);

	public static function wmsProd($ids)
	{
		$prodIds = [0];
		$oids = implode(',', $ids);
		$sql = 'SELECT prod_id from wms_prod_org where org_id in (:oids) group by prod_id UNION SELECT prod_id from wms_stock where org_id in (:oids) group by prod_id';
		$rs = Yii::app()->db->createCommand($sql)->bindValues(array(':oids' => $oids))->queryAll();
		foreach ($rs as $prod) {
			$prodIds[] = $prod['prod_id'];
		}
		//print_r($prodIds);
		return $prodIds;
	}

	public static function jobIds($ids)
	{
		$jobIds = [0];
		$oids = implode(',', $ids);
		$sql = 'SELECT id from wms_job where org_id in (' . $oids . ')';
		$rs = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rs as $r) {
			$jobIds[] = $r['id'];
		}
		return $jobIds;
	}

	public static function ifOldVersion()
	{
		if (!empty(Yii::app()->user->id) && !empty(User::model()->findByPk(Yii::app()->user->id)->extra['3pl_portal']) && User::model()->findByPk(Yii::app()->user->id)->extra['3pl_portal'] == 'new') {
			return false;
		} else {
			return true;
		}
	}

	public static function getConsumerBackorder($org_id = null, $export = true, $task_id = null, $split = false)
	{
		$task = new WmsTask();
		$task->unsetAttributes();
		if (isset($_GET['WmsTask'])) {
			$task->attributes = $_GET['WmsTask'];
		}
		if (!empty($task_id)) {
			$task->id = $task_id;
		}

		$ec = new CDbCriteria;
		$ec->addCondition('t.type IN (3020,3030) AND t.status = 10');
		$ec->addCondition('not t.bwf & 16 > 0');
		$ec->addCondition('JSON_VALUE(t.meta, "$.b2") = "c" OR JSON_VALUE(t.meta, "$.b2") IS NULL');

		if (empty($org_id)) {
			// get the orgids and by org_ids
			if (Yii::app()->user->grp != 0) {
				$task->job_ids = self::jobIds(User::getOrgIds());
			}
			if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
				$ec->with = ['job'];
				$ec->addCondition('job.org_id = ' . Yii::app()->session['org_id']);
			}
		} else {
			$task->job_ids = self::jobIds([$org_id]);
		}

		$data = $task->search(false, 0, $ec)->getData();
		if (!$split) {
			return self::_getConsumerBackorder($data, $export);
		} else {
			$fns = [];
			foreach ($data as $task) {
				if (!empty($task->mdata['sent_consumer_backorder'])) continue;
				$fns[] = [self::_getConsumerBackorder([$task], $export), $task->ref];
				$task->mdata['sent_consumer_backorder'] = true;
				$task->save();
			}

			return $fns;
		}
	}

	private function _getConsumerBackorder($data, $export)
	{
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['PCAE Order#', 'Sale Order#', 'Customer Name', 'Company', 'Address', 'City', 'State', 'Country', 'Suburb', 'PostCode', 'Email', 'Item Code', 'Description', 'BO QTY']);
		$xls->setFont('A1:Z1', ['bold' => true]);
		foreach ($data as $task) {
			foreach ($task->items as $item) {
				if (empty($item->mdata['pi'])) {
					$xls->addRow($i++, [$task->getNo(), $task->ref, $task->getCneeName(), $task->getCneeCompany(), $task->getCneeAddress(), $task->getCneeCity(), $task->getCneeState(), $task->getCneeCountry(), $task->getCneeSuburb(), $task->getCneePostcode(), $task->getCneeEmail(), 'not found', $item->mdata['sn'], intval($item->mdata['uq'])]);
				} else {
					$stock = WmsStock::model()->find('prod_id = :pi AND org_id = :oid', [':pi' => $item->mdata['pi'], ':oid' => $task->job->org_id]);
					if (empty($stock) || $stock->availQty() >= intval($item->mdata['uq'])) continue;
					$xls->addRow($i++, [$task->getNo(), $task->ref, $task->getCneeName(), $task->getCneeCompany(), $task->getCneeAddress(), $task->getCneeCity(), $task->getCneeState(), $task->getCneeCountry(), $task->getCneeSuburb(), $task->getCneePostcode(), $task->getCneeEmail(), $stock->getCustSKU(), $item->mdata['sn'], intval($item->mdata['uq'])]);
				}
			}
		}

		$file = 'Consumer Backorder ' . time() . '.xlsx';
		if ($export) {
			$xls->output($file);
		} else {
			$xls->output($file, null, false);
			return $file;
		}
	}

	public static function getRetailerBackorder($org_id = null, $export = true, $task_id = null, $split = false)
	{
		$task = new WmsTask();
		$task->unsetAttributes();
		if (isset($_GET['WmsTask'])) {
			$task->attributes = $_GET['WmsTask'];
		}
		if (!empty($task_id)) {
			$task->id = $task_id;
		}

		$ec = new CDbCriteria;
		$ec->addCondition('t.type IN (3020,3030) AND t.status = 10');
		$ec->addCondition('not t.bwf & 16 > 0');
		$ec->addCondition('JSON_VALUE(t.meta, "$.b2") = "b"');

		if (empty($org_id)) {
			// get the orgids and by org_ids
			if (Yii::app()->user->grp != 0) {
				$task->job_ids = self::jobIds(User::getOrgIds());
			}
			if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
				$ec->with = ['job'];
				$ec->addCondition('job.org_id = ' . Yii::app()->session['org_id']);
			}
		} else {
			$task->job_ids = self::jobIds([$org_id]);
		}

		$data = $task->search(false, 0, $ec)->getData();
		if (!$split) {
			return self::_getRetailerBackorder($data, $export);
		} else {
			$fns = [];
			foreach ($data as $task) {
				if (!empty($task->mdata['sent_retailer_backorder'])) continue;
				$fns[] = [self::_getRetailerBackorder([$task], $export), $task->ref];
				$task->mdata['sent_retailer_backorder'] = true;
				$task->save();
			}

			return $fns;
		}
	}

	private function _getRetailerBackorder($data, $export)
	{
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['PCAE Order#', 'Sale Order#', 'Customer Name', 'Company', 'Address', 'City', 'State', 'Country', 'Suburb', 'PostCode', 'Email', 'Item Code', 'Description', 'BO QTY']);
		$xls->setFont('A1:Z1', ['bold' => true]);
		foreach ($data as $task) {
			foreach ($task->items as $item) {
				if (empty($item->mdata['pi'])) {
					$xls->addRow($i++, [$task->getNo(), $task->ref, $task->getCneeName(), $task->getCneeCompany(), $task->getCneeAddress(), $task->getCneeCity(), $task->getCneeState(), $task->getCneeCountry(), $task->getCneeSuburb(), $task->getCneePostcode(), $task->getCneeEmail(), 'not found', $item->mdata['sn'], intval($item->mdata['uq'])]);
				} else {
					$stock = WmsStock::model()->find('prod_id = :pi AND org_id = :oid', [':pi' => $item->mdata['pi'], ':oid' => $task->job->org_id]);
					if (empty($stock) || $stock->availQty() >= intval($item->mdata['uq'])) continue;
					$xls->addRow($i++, [$task->getNo(), $task->ref, $task->getCneeName(), $task->getCneeCompany(), $task->getCneeAddress(), $task->getCneeCity(), $task->getCneeState(), $task->getCneeCountry(), $task->getCneeSuburb(), $task->getCneePostcode(), $task->getCneeEmail(), $stock->getCustSKU(), $item->mdata['sn'], intval($item->mdata['uq'])]);
				}
			}
		}

		$file = 'Retailer Backorder ' . time() . '.xlsx';
		if ($export) {
			$xls->output($file);
		} else {
			$xls->output($file, null, false);
			return $file;
		}
	}

	public static function getGoodsReturn($org_id = null, $export = true, $task_id = null, $split = false)
	{
		$task = new WmsTask();
		$task->unsetAttributes();
		if (isset($_GET['WmsTask'])) {
			$task->attributes = $_GET['WmsTask'];
		}
		if (!empty($task_id)) {
			$task->id = $task_id;
		}

		$ec = new CDbCriteria;
		$ec->addCondition('t.type IN (3020,3030)');
		$ec->addCondition('(t.bwf & 128) > 0');
		$ec->addCondition('not t.bwf & 16 > 0');

		if (empty($org_id)) {
			// get the orgids and by org_ids
			if (Yii::app()->user->grp != 0) {
				$task->job_ids = self::jobIds(User::getOrgIds());
			}
			if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
				$ec->with = ['job'];
				$ec->addCondition('job.org_id = ' . Yii::app()->session['org_id']);
			}
		} else {
				$task->job_ids = self::jobIds([$org_id]);
		}

		$data = $task->search(false, 0, $ec);
		if (!$split) {
			return self::_getGoodsReturn($data, $export);
		} else {
			$fns = [];
			foreach ($data as $task) {
				if (!empty($task->mdata['sent_goods_return'])) continue;
				$fns[] = [self::_getGoodsReturn([$task], $export), $task->ref];
				$task->mdata['sent_goods_return'] = true;
				$task->save();
			}

			return $fns;
		}
	}

	private function _getGoodsReturn($data, $export)
	{
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['Despatch Date', 'Consignment', 'Customer Order#', 'Customer Name', 'Address', 'Suburb', 'State', 'PostCode', 'Country', 'PCAE Order#', 'Item QTY']);
		foreach ($data as $task) {
			$xls->addRow($i++, [$task->getComplDate(), $task->getConsignment(true), $task->ref, $task->getCneeName(), $task->getCneeAddress(), $task->getCneeSuburb(), $task->getCneeState(), $task->getCneePostcode(), $task->getCneeCountry(), $task->getNo(), $task->countUq()]);
		}

		$file = 'Goods Return ' . time() . '.xlsx';
		if ($export) {
			$xls->output($file);
		} else {
			$xls->output($file, null, false);
			return $file;
		}
	}

	public static function getMinimumStockAlert($org_id = null, $export = true, $prod_id = null, $split = false)
	{
		if (empty($org_id)) $org_id = Yii::app()->user->org;
		$prods = [];
		$orgs = WmsProdOrg::model()->findAll('org_id = :org_id AND JSON_VALUE(meta, "$.min_stock_alert") IS NOT NULL', [':org_id' => $org_id]);
		foreach ($orgs as $org) {
			$qty = 0;
			foreach ($org->stocks as $stock) {
				$qty += $stock->availQty();
			}
			if ($qty < $org->mdata['min_stock_alert']) {
				$prods[] = $org->prod_id;
			}
		}

		$prod = new WmsProd('search');
		$prod->unsetAttributes();
		if (!empty($_GET['WmsProd'])) {
			$prod->setAttributes($_GET['WmsProd']);
		}
		$ec = new CDbCriteria;
		if (!empty($prods)) {
			$ec->addCondition('id IN (' . implode(',', $prods) . ')');
		} else {
			$ec->addCondition('1 = 0');
		}

		$data = $prod->search(false, 0, $ec)->getData();
		if (!$split) {
			return self::_getMinimumStockAlert($data, $export, $org_id);
		} else {
			$fns = [];
			foreach ($data as $prod) {
				if (!empty($prod->mdata['sent_minimum_stock_alert'])) continue;
				$fns[] = [self::_getMinimumStockAlert([$prod], $export, $org_id), $prod->name];
				$prod->mdata['sent_minimum_stock_alert'] = true;
				$prod->save();
			}

			return $fns;
		}
	}

	private function _getMinimumStockAlert($data, $export, $org_id)
	{
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['Item Code', 'Product Name', 'Barcode', 'Available Stock', 'Minimum Stock']);
		$xls->setFont('A1:Z1', ['bold' => true]);
		foreach ($data as $prod) {
			$xls->addRow($i++, [$prod->getSku($org_id), $prod->name, $prod->ean, intval($prod->availQty($org_id)), $prod->getMinStockAlert($org_id)]);
		}

		$file = 'Minimum Stock Alert ' . time() . '.xlsx';
		if ($export) {
			$xls->output($file);
		} else {
			$xls->output($file, null, false);
			return $file;
		}
	}

	public static function getDeliveryDespatchConsumer($org_id = null, $export = true, $task_id = null, $split = false)
	{
		$task = new WmsTask();
		$task->unsetAttributes();
		if (isset($_GET['WmsTask'])) {
			$task->attributes = $_GET['WmsTask'];
		}
		if (!empty($task_id)) {
			$task->id = $task_id;
		}

		$ec = new CDbCriteria;
		$ec->addCondition('t.type IN (3020,3030) AND t.status = 99');
		$ec->addCondition('not t.bwf & 16 > 0');
		$ec->addCondition('JSON_VALUE(t.meta, "$.b2") = "c" OR JSON_VALUE(t.meta, "$.b2") IS NULL');

		if (empty($org_id)) {
			// get the orgids and by org_ids
			if (Yii::app()->user->grp != 0) {
				$task->job_ids = self::jobIds(User::getOrgIds());
			}
			if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
				$ec->with = ['job'];
				$ec->addCondition('job.org_id = ' . Yii::app()->session['org_id']);
			}
		} else {
			$task->job_ids = self::jobIds([$org_id]);
		}

		$data = $task->search(false, 0, $ec)->getData();
		if (!$split) {
			return self::_getDeliveryDespatchConsumer($data, $export);
		} else {
			$fns = [];
			foreach ($data as $task) {
				if (!empty($task->mdata['sent_delivery_despatch_consumer'])) continue;
				$fns[] = [self::_getDeliveryDespatchConsumer([$task], $export), $task->ref];
				$task->mdata['sent_delivery_despatch_consumer'] = true;
				$task->save();
			}

			return $fns;
		}
	}

	private function _getDeliveryDespatchConsumer($data, $export)
	{
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['Despatch Date', 'Consignment', 'Customer Order#', 'Customer Name', 'Company Name', 'Email', 'Address', 'Suburb', 'State', 'PostCode', 'Country', 'PCAE Order#', 'Despatch Carrier', 'Item QTY']);
		$xls->setFont('A1:Z1', ['bold' => true]);
		foreach ($data as $task) {
			$day = 28;
			if (!empty($task->job->customer->extra['delivery-despatch-consumer-range'])) {
				switch ($task->job->customer->extra['delivery-despatch-consumer-range']) {
					case 'one':
						$day = 1;
						break;
					case 'seven':
						$day = 7;
						break;
					case 'forteen':
						$day = 14;
						break;
					case 'twenty_eight':
						$day = 28;
						break;
					default:
						$day = 28;
						break;
				}
			}
			if (strtotime($task->getComplDate()) < strtotime(date('Y-m-d') . ' - ' . $day . ' days') && count($data) != 1) continue;
			$xls->addRow($i++, [$task->getComplDate(), $task->getConsignment(true), $task->ref, $task->getCneeName(), $task->getCneeCompany(), $task->getCneeEmail(), $task->getCneeAddress(), $task->getCneeSuburb(), $task->getCneeState(), $task->getCneePostcode(), $task->getCneeCountry(), $task->getNo(), $task->getCarrier(), $task->countUq(), $task->getConsignment(true) ? 'Tracking' : '']);
			if (!empty($task->getConsignment(true))) {
				$xls->setUrl(14, $i-1, $task->getConsignmentUrl());
			}
		}

		$file = 'Delivery Despatch Consumer ' . time() . '.xlsx';
		if ($export) {
			$xls->output($file);
		} else {
			$xls->output($file, null, false);
			return $file;
		}
	}

	public static function getDeliveryDespatchRetailer($org_id = null, $export = true, $task_id = null, $split = false)
	{
		$task = new WmsTask();
		$task->unsetAttributes();
		if (isset($_GET['WmsTask'])) {
			$task->attributes = $_GET['WmsTask'];
		}
		if (!empty($task_id)) {
			$task->id = $task_id;
		}

		$ec = new CDbCriteria;
		$ec->addCondition('t.type IN (3020,3030) AND t.status = 99');
		$ec->addCondition('not t.bwf & 16 > 0');
		$ec->addCondition('JSON_VALUE(t.meta, "$.b2") = "b"');

		if (empty($org_id)) {
			// get the orgids and by org_ids
			if (Yii::app()->user->grp != 0) {
				$task->job_ids = self::jobIds(User::getOrgIds());
			}
			if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
				$ec->with = ['job'];
				$ec->addCondition('job.org_id = ' . Yii::app()->session['org_id']);
			}
		} else {
			$task->job_ids = self::jobIds([$org_id]);
		}

		$data = $task->search(false, 0, $ec)->getData();
		if (!$split) {
			return self::_getDeliveryDespatchRetailer($data, $export);
		} else {
			$fns = [];
			foreach ($data as $task) {
				if (!empty($task->mdata['sent_delivery_despatch_retailer'])) continue;
				$fns[] = [self::_getDeliveryDespatchRetailer([$task], $export), $task->ref];
				$task->mdata['sent_delivery_despatch_retailer'] = true;
				$task->save();
			}

			return $fns;
		}
	}

	private function _getDeliveryDespatchRetailer($data, $export)
	{
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['Despatch Date', 'Consignment', 'Customer Order#', 'Customer Name', 'Company Name', 'Email', 'Address', 'Suburb', 'State', 'PostCode', 'Country', 'PCAE Order#', 'Despatch Carrier', 'Item QTY']);
		$xls->setFont('A1:Z1', ['bold' => true]);
		foreach ($data as $task) {
			$day = 28;
			if (!empty($task->job->customer->extra['delivery-despatch-retailer-range'])) {
				switch ($task->job->customer->extra['delivery-despatch-retailer-range']) {
					case 'one':
						$day = 1;
						break;
					case 'seven':
						$day = 7;
						break;
					case 'forteen':
						$day = 14;
						break;
					case 'twenty_eight':
						$day = 28;
						break;
					default:
						$day = 28;
						break;
				}
			}
			if (strtotime($task->getComplDate()) < strtotime(date('Y-m-d') . ' - ' . $day . ' days') && count($data) != 1) continue;
			$xls->addRow($i++, [$task->getComplDate(), $task->getConsignment(true), $task->ref, $task->getCneeName(), $task->getCneeCompany(), $task->getCneeEmail(), $task->getCneeAddress(), $task->getCneeSuburb(), $task->getCneeState(), $task->getCneePostcode(), $task->getCneeCountry(), $task->getNo(), $task->getCarrier(), $task->countUq(), $task->getConsignment(true) ? 'Tracking' : '']);
			if (!empty($task->getConsignment(true))) {
				$xls->setUrl(14, $i-1, $task->getConsignmentUrl());
			}
		}

		$file = 'Delivery Despatch Retailer ' . time() . '.xlsx';
		if ($export) {
			$xls->output($file);
		} else {
			$xls->output($file, null, false);
			return $file;
		}
	}

	public static function getInboundDeliveryAdvice($org_id = null, $export = true, $task_id = null, $split = false)
	{
		$task = new WmsTask();
		$task->unsetAttributes();
		if (isset($_GET['WmsTask'])) {
			$task->attributes = $_GET['WmsTask'];
		}
		if (!empty($task_id)) {
			$task->id = $task_id;
		}

		$ec = new CDbCriteria;
		$ec->addCondition('t.type IN (1010,1020,1030) AND t.status IN (10,20)');
		$ec->addCondition('not t.bwf & 16 > 0');

		if (empty($org_id)) {
			// get the orgids and by org_ids
			if (Yii::app()->user->grp != 0) {
				$task->job_ids = self::jobIds(User::getOrgIds());
			}
			if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
				$ec->with = ['job'];
				$ec->addCondition('job.org_id = ' . Yii::app()->session['org_id']);
			}
		} else {
			$task->job_ids = self::jobIds([$org_id]);
		}

		$data = $task->search(false, 0, $ec)->getData();
		if (!$split) {
			return self::_getInboundDeliveryAdvice($data, $export);
		} else {
			$fns = [];
			foreach ($data as $task) {
				if (!empty($task->mdata['sent_inbound_delivery_advice'])) continue;
				$fns[] = [self::_getInboundDeliveryAdvice([$task], $export), $task->ref];
				$task->mdata['sent_inbound_delivery_advice'] = true;
				$task->save();
			}

			return $fns;
		}
	}

	private function _getInboundDeliveryAdvice($data, $export)
	{
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['PCAE Order#', 'Purchase Order#', 'Inbound Order Date', 'Item Code', 'Item Type', 'Description', 'Alt Item Code', 'Quantity']);
		$xls->setFont('A1:Z1', ['bold' => true]);
		foreach ($data as $task) {
			$req = [];
			foreach ($task->items as $item) {
				if (empty($req[$item->mdata['gi']])) {
					$req[$item->mdata['gi']] = 0;
				}
				$req[$item->mdata['gi']] += intval($item->mdata['uq']);
			}
			foreach ($req as $id => $qty) {
				$prod = WmsProd::model()->findByPk($id);
				$xls->addRow($i++, [$task->getNo(), $task->ref, $task->getCreateDate(), $prod->getSku($task->job->org_id), 'Existing Item', $prod->name, $prod->ean, $qty]);
			}
		}

		$file = 'Inbound Delivery Advice ' . time() . '.xlsx';
		if ($export) {
			$xls->output($file);
		} else {
			$xls->output($file, null, false);
			return $file;
		}
	}

	public static function getInboundDeliveryReport($org_id = null, $export = true, $task_id = null, $split = false)
	{
		$task = new WmsTask();
		$task->unsetAttributes();
		if (isset($_GET['WmsTask'])) {
			$task->attributes = $_GET['WmsTask'];
		}
		if (!empty($task_id)) {
			$task->id = $task_id;
		}

		$ec = new CDbCriteria;
		$ec->addCondition('t.type IN (1010,1020,1030) AND t.status IN (30,99)');
		$ec->addCondition('not t.bwf & 16 > 0');

		if (empty($org_id)) {
			// get the orgids and by org_ids
			if (Yii::app()->user->grp != 0) {
				$task->job_ids = self::jobIds(User::getOrgIds());
			}
			if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
				$ec->with = ['job'];
				$ec->addCondition('job.org_id = ' . Yii::app()->session['org_id']);
			}
		} else {
			$task->job_ids = self::jobIds([$org_id]);
		}

		$data = $task->search(false, 0, $ec)->getData();
		if (!$split) {
			return self::_getInboundDeliveryReport($data, $export);
		} else {
			$fns = [];
			foreach ($data as $task) {
				if (!empty($task->mdata['sent_inbound_delivery_report'])) continue;
				$fns[] = [self::_getInboundDeliveryReport([$task], $export), $task->ref];
				$task->mdata['sent_inbound_delivery_report'] = true;
				$task->save();
			}

			return $fns;
		}
	}

	private function _getInboundDeliveryReport($data, $export)
	{
		$xls = new oExcel;
		$xls->setTitle('Summary');
		$i = 1;
		$xls->addRow($i++, ['PCAE Order#', 'Purchase Order#', 'Inbound Order Date', 'Delivery Date', 'Date Receipted to Inventory', 'Item Code', 'Item Type', 'Description', 'Alt Item Code', 'Quantity', 'Received Quantity', 'Back Order Qty']);
		$xls->setFont('A1:Z1', ['bold' => true]);
		foreach ($data as $task) {
			$day = 28;
			if (!empty($task->job->customer->extra['inbound-delivery-report-range'])) {
				switch ($task->job->customer->extra['inbound-delivery-report-range']) {
					case 'one':
						$day = 1;
						break;
					case 'seven':
						$day = 7;
						break;
					case 'forteen':
						$day = 14;
						break;
					case 'twenty_eight':
						$day = 28;
						break;
					default:
						$day = 28;
						break;
				}
			}
			if (strtotime($task->getComplDate()) < strtotime(date('Y-m-d') . ' - ' . $day . ' days') && count($data) != 1) continue;
			$req = [];
			foreach ($task->items as $item) {
				if (empty($req[$item->mdata['gi']])) {
					$req[$item->mdata['gi']] = 0;
				}
				$req[$item->mdata['gi']] += intval($item->mdata['uq']);
			}
			$res = [];
			foreach ($task->actionTask->items as $item) {
				if (empty($res[$item->mdata['gi']])) {
					$res[$item->mdata['gi']] = 0;
				}
				$res[$item->mdata['gi']] += intval($item->mdata['uq']);
			}
			ksort($req);
			foreach ($req as $id => $qty) {
				$prod = WmsProd::model()->findByPk($id);
				$xls->addRow($i++, [$task->getNo(), $task->ref, $task->getCreateDate(), $task->getComplDate(), $task->getComplDate(), $prod->getSku($task->job->org_id), 'Existing Item', $prod->name, $prod->ean, $qty, @$res[$id], $qty - intval(@$res[$id])]);
			}
			foreach ($res as $id => $qty) {
				if (!empty($req[$id])) continue;
				$prod = WmsProd::model()->findByPk($id);
				$xls->addRow($i++, [$task->getNo(), $task->ref, $task->getCreateDate(), $task->getComplDate(), $task->getComplDate(), $prod->getSku($task->job->org_id), 'Existing Item', $prod->name, $prod->ean, 0, $qty, 0]);
			}
		}

		$xls->createSheet('Details');
		$xls->goSheet(1);
		$xls->setTitle('Details');
		$i = 1;
		$xls->addRow($i++, ['PCAE Order#', 'Purchase Order#', 'Inbound Order Date', 'Delivery Date', 'Date Receipted to Inventory', 'Item Code', 'Item Type', 'Description', 'Alt Item Code', 'Received Quantity', 'Expiry', 'Batch']);
		$xls->setFont('A1:Z1', ['bold' => true]);
		foreach ($data as $task) {
			$day = 28;
			if (!empty($task->job->customer->extra['inbound-delivery-report-range'])) {
				switch ($task->job->customer->extra['inbound-delivery-report-range']) {
					case 'one':
						$day = 1;
						break;
					case 'seven':
						$day = 7;
						break;
					case 'forteen':
						$day = 14;
						break;
					case 'twenty_eight':
						$day = 28;
						break;
					default:
						$day = 28;
						break;
				}
			}
			if (strtotime($task->getComplDate()) < strtotime(date('Y-m-d') . ' - ' . $day . ' days') && count($data) != 1) continue;
			$res = [];
			foreach ($task->actionTask->items as $item) {
				if (empty($res[$item->mdata['gi'] . '_' . @$item->mdata['ex'] . '_' . @$item->mdata['bn']])) {
					$res[$item->mdata['gi'] . '_' . @$item->mdata['ex'] . '_' . @$item->mdata['bn']] = 0;
				}
				$res[$item->mdata['gi'] . '_' . @$item->mdata['ex'] . '_' . @$item->mdata['bn']] += intval($item->mdata['uq']);
			}
			ksort($res);
			foreach ($res as $id => $qty) {
				$prod_id = explode('_', $id)[0];
				$expiry = explode('_', $id)[1];
				$batch = explode('_', $id)[2];

				$prod = WmsProd::model()->findByPk($prod_id);
				$xls->addRow($i++, [$task->getNo(), $task->ref, $task->getCreateDate(), $task->getComplDate(), $task->getComplDate(), $prod->getSku($task->job->org_id), 'Existing Item', $prod->name, $prod->ean, $qty, $expiry, $batch]);
			}
		}
		$xls->goSheet(0);

		$file = 'Inbound Delivery Report ' . time() . '.xlsx';
		if ($export) {
			$xls->output($file);
		} else {
			$xls->output($file, null, false);
			return $file;
		}
	}

	public function getStockOnHand($org_id = null, $export = true, $prod_id = null, $split = false)
	{
		if (empty($org_id)) $org_id = Yii::app()->user->org;
		$prods = [];
		$stocks = WmsStock::model()->findAll('org_id = :org_id AND (bwf & 2) = 0', [':org_id' => $org_id]);
		foreach ($stocks as $stock) {
			$prods[] = $stock->prod_id;
		}

		$prod = new WmsProd('search');
		$prod->unsetAttributes();
		if (!empty($_GET['WmsProd'])) {
			$prod->setAttributes($_GET['WmsProd']);
		}
		$prod->type = WmsProd::WMS_PROD_PHYSICAL;
		$ec = new CDbCriteria;
		if (!empty($prods)) {
			$ec->addCondition('t.id IN (' . implode(',', $prods) . ')');
		} else {
			$ec->addCondition('1 = 0');
		}

		$data = $prod->search(false, 0, $ec)->getData();
		if (!$split) {
			return self::_getStockOnHand($data, $export, $org_id);
		} else {
			$fns = [];
			foreach ($data as $prod) {
				if (!empty($prod->mdata['sent_stock_on_hand'])) continue;
				$fns[] = [self::_getStockOnHand([$prod], $export, $org_id), $prod->name];
				$prod->mdata['sent_stock_on_hand'] = true;
				$prod->save();
			}

			return $fns;
		}
	}

	public function _getStockOnHand($data, $export, $org_id)
	{
		$xls = new oExcel;
		$xls->setTitle('Summary');
		$i = 1;
		$xls->setColWidth(array(15, 40, 15, 15, 15, 15, 15));
		$xls->addRow($i++, array('Item Code / SKU', 'Description', 'Barcode', 'SOH', 'Allocated To Current Order', 'Available Stock', 'Minimum Stock Alert'));
		foreach ($data as $r) {
			$xls->addRow($i++, array($r->getSku($org_id), $r->name, $r->ean, $r->getStockQty($org_id, 'qty'), $r->getStockQty($org_id, 'qty_res'), $r->getStockQty($org_id, 'avail'), $r->getMinStockAlert($org_id)));
		}

		$xls->createSheet('Details');
		$xls->goSheet(1);
		$xls->setTitle('Details');
		$i = 1;
		$xls->setColWidth(array(15, 40, 15, 15, 15, 15, 15, 15));
		$xls->addRow($i++, array('Item Code / SKU', 'Description', 'Barcode', 'SOH', 'Allocated To Current Order', 'Available Stock', 'Expiry', 'Batch'));
		foreach ($data as $r) {
			$stocks = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $r->id, ':org_id' => $org_id]);
			foreach ($stocks as $stock) {
				$xls->addRow($i++, array($stock->prod->getSku($org_id), $stock->prod->name, $stock->prod->ean, $stock->getQty() + $stock->qty_res, $stock->qty_res, $stock->getQty(), $stock->expiry, $stock->batch));
			}
		}
		$xls->goSheet(0);

		$file = 'Stock On Hand ' . time() . '.xlsx';
		if ($export) {
			$xls->output($file);
		} else {
			$xls->output($file, null, false);
			return $file;
		}
	}

	public static function getHoldTask($org_id = null, $export = true, $task_id = null, $split = false)
	{
		$task = new WmsTask();
		$task->unsetAttributes();
		if (isset($_GET['WmsTask'])) {
			$task->attributes = $_GET['WmsTask'];
		}
		if (!empty($task_id)) {
			$task->id = $task_id;
		}

		$ec = new CDbCriteria;
		$ec->addCondition('t.type IN (3020,3030) AND t.status = 40');
		$ec->addCondition('not t.bwf & 16 > 0');

		if (empty($org_id)) {
			// get the orgids and by org_ids
			if (Yii::app()->user->grp != 0) {
				$task->job_ids = self::jobIds(User::getOrgIds());
			}
			if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
				$ec->with = ['job'];
				$ec->addCondition('job.org_id = ' . Yii::app()->session['org_id']);
			}
		} else {
			$task->job_ids = self::jobIds([$org_id]);
		}

		$data = $task->search(false, 0, $ec)->getData();
		if (!$split) {
			return self::_getHoldTask($data, $export);
		} else {
			$fns = [];
			foreach ($data as $task) {
				if (!empty($task->mdata['sent_hold_task'])) continue;
				$fns[] = [self::_getHoldTask([$task], $export), $task->ref];
				$task->mdata['sent_hold_task'] = true;
				$task->save();
			}

			return $fns;
		}
	}

	private function _getHoldTask($data, $export)
	{
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['Customer Order#', 'Customer Name', 'Company Name', 'Email', 'Address', 'Suburb', 'State', 'PostCode', 'Country', 'PCAE Order#', 'Reason']);
		$xls->setFont('A1:Z1', ['bold' => true]);
		foreach ($data as $task) {
			$xls->addRow($i++, [$task->ref, $task->getCneeName(), $task->getCneeCompany(), $task->getCneeEmail(), $task->getCneeAddress(), $task->getCneeSuburb(), $task->getCneeState(), $task->getCneePostcode(), $task->getCneeCountry(), $task->getNo(), 'Address error']);
		}

		$file = 'Hold Task ' . time() . '.xlsx';
		if ($export) {
			$xls->output($file);
		} else {
			$xls->output($file, null, false);
			return $file;
		}
	}

}
