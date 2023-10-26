<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class WmsOrgController extends Controller
{
	protected $nonAjax = ['list'];

	public function actionList()
	{
		$model = new Org('search');
		$model->unsetAttributes();
		if (isset($_GET['Org'])) {
			$model->attributes = $_GET['Org'];
		}

		// add condition to filter out the org that have wms storage.
		$criteria = new CDbCriteria;

		$org_ids = WmsStock::model()->findAll(array('select' => 'org_id', 'group' => 'org_id'));
		$org_ids = array_merge(WmsOrgQuote::model()->findAll(array('select' => 'org_id', 'group' => 'org_id')), $org_ids);
		$oids = [1308];
		foreach ($org_ids as $oid) {
			if (empty($oid->org_id)) {
				continue;
			}

			$oids[] = $oid->org_id;
		}
		$criteria->addInCondition("id", $oids);
		$this->render('list', array('model' => $model, 'criteria' => $criteria));
	}

	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		if (!empty($_POST)) {
			$dpt_id = $_POST['dpt_id'];
			if (!empty($_POST['wms_from_date']) && !empty($_POST['wms_to_date'])) {
				if ($_POST['wms_invoice_type'] === 'storage') {
					$errors = WmsInvoice::genWmsStorage($model, $_POST['wms_from_date'], $_POST['wms_to_date'],$dpt_id);
					if (!empty($errors)) {
						$model->addError('GenInvoice Failed', implode('; ', $errors));
					} elseif (!empty($_POST['wms_to_date'])) {
						$model->extra['wms_from_date'] = $_POST['wms_to_date'];
						$model->sync_xero = false;
						$model->updateMeta();
					}
				} elseif ($_POST['wms_invoice_type'] === 'wms') {
					$errors = WmsInvoice::genWmsService($model, $_POST['wms_from_date'], $_POST['wms_to_date'],$dpt_id);
					if (!empty($errors)) {
						$model->addError('GenInvoice Failed', implode('; ', $errors));
					} elseif (!empty($_POST['wms_to_date'])) {
						$model->extra['wms_from_date'] = $_POST['wms_to_date'];
						$model->sync_xero = false;
						$model->updateMeta();
					}
				}
			} else {
				$model->addError('Input Invalid', 'please input the date');
			}
			$this->ajaxResult($model);
		}
		if (isset($_GET['tab'])) {
			Acl::hasAccess($this->CaName . '/' . $_GET['tab'], true);
			$this->render('tab_' . $_GET['tab'], array('model' => $model));
		} else {
			$this->render('update', array('model' => $model));
		}
	}

	public function actionIntlChargecodeMng()
	{
		$model = new IntlChargeCode('search');
		if (isset($_GET['IntlChargeCode'])) {
			$model->attributes = $_GET['IntlChargeCode'];
		}

		$this->render('list_intl_chargecode', ['model' => $model]);
	}

	public function actionCreatechargecode()
	{
		$model = new IntlChargeCode();
		if (isset($_POST['IntlChargeCode'])) {
			//$model->setScenario('create');
			$model->attributes = $_POST['IntlChargeCode'];
			$model->created = date('Y-m-d H:i:s');
			$model->save();
			$this->ajaxResult($model, array('id'));
		} else {
			$model->charge_wt = 1;
		}

		$this->render('create_intl_charge_code', array(
			'model' => $model,
		));
	}

	public function actionUpdatechargecode($id)
	{
		$model = IntlChargeCode::model()->findByPk($id);
		if (isset($_POST['IntlChargeCode'])) {
			$model->attributes = $_POST['IntlChargeCode'];
			if (!isset($_POST['selected_rates'])) {
				// $model->couriersObj = [];
				$model->couriers = '';
				// $model->addError('couriers','Please select at least one courier');
			} else {
				// $model->couriersObj = $_POST['selected_rates'];
			}
			if (isset($_POST['mdata'])) {
				foreach ($_POST['mdata'] as $v => $k) {
					$model->mdata[$v] = $k;
				}
			}
			if (!isset($_POST['mdata']['selected_service'])) {
				$model->mdata['selected_service'] = [];
			}
			$model->save();
			$this->ajaxResult($model, array('id'));
		}
		$this->render('update_intl_charge_code', array('model' => $model));
	}

	public function actionIntlchgcodezonemap($id)
	{
		$modelChargeCode = IntlChargeCode::model()->findByPk($id);
		$this->render('chargecode_zonemap', array(
			'model' => $modelChargeCode,
		));
	}

	public function actionAjaxSaveChargecodeZonemap()
	{
		$resp = array('success' => 1, 'msg' => 'import successfully');
		$template_file = empty($_FILES['org-zonemap']) ? array() : $_FILES['org-zonemap'];
		if (empty($template_file['tmp_name']) || !is_uploaded_file($template_file['tmp_name'])) {
			$resp['msg'] = 'Invalid template file';
			echo json_encode($resp);
			return;
		} else {
			$xls = new oExcel;
			$xls->load($template_file['tmp_name']);
			$data = $xls->getAll();

			$chargecodeId = $_POST['IntlChargeCode']['id'];

			// import org zone map data
			$this->importChargecodeZoneMapData($chargecodeId, $data);

			//save file
			FileRepo::storeFile($template_file['tmp_name'], $template_file['name'], 54, $chargecodeId);

		}

		echo json_encode($resp);
	}

	private function importChargecodeZoneMapData($chargecodeId, &$data)
	{
		$index = 0;

		foreach ($data as $row) {
			if ($index < 1) {
				$index++;
				continue;
			}
			if (empty($row[1])) {
				continue;
			}

			// if (preg_match('/([0-9|\,|\-|\s]*)/', $row[3], $matches)) {
			// 	if ($matches[1] != $row[3]) {
			// 		echo json_encode(['done' => false, 'msg' => 'Contains character other than<br>numbers or - or , or space']);
			// 		Yii::app()->end();
			// 	}
			// } else {
			// 	echo json_encode(['done' => false, 'msg' => 'Contains character other than<br>numbers or - or , or space']);
			// 	Yii::app()->end();
			// }
		}

		// clear old data
		ZoneMapIntl::model()->deleteAll('chargecode_id = :cid', [':cid' => $chargecodeId]);
		FileRepo::model()->deleteAll('type = 54 and fid = :cid', [':cid' => $chargecodeId]);

		unset($data[1]);
		foreach ($data as $row) {
			if ($index < 1) {
				$index++;
				continue;
			}
			if (empty($row[1])) {
				continue;
			}

			$code = $row[1];
			$name = $row[2];
			$countries = explode('.', $row[3]);
			foreach ($countries as $k => $country) {
				$countries[$k] = trim($country);
			}
			foreach ($countries as $k => $country) {
				if (!in_array($country, array_keys(Unloco::$countries)) && !in_array($country, array_values(Unloco::$countries)) && $country != 'OTHER') {
					echo json_encode(['done' => false, 'msg' => 'Country Name/Code ' . $country . ' is invalid']);
					Yii::app()->end();
				} else if (in_array($country, array_values(Unloco::$countries))) {
					$countries[$k] = array_search($country, Unloco::$countries);
				}
			}
			foreach ($countries as $country) {
				$zoneMap = new ZoneMapIntl;
				$zoneMap->setAttributes(array(
					'org_id' => 0,
					'zone_id' => 1,
					'chargecode_id' => $chargecodeId,
					'z1' => $code,
					'z2' => $country,
					'zone_name' => $name,
				));
				$zoneMap->save();
			}
		}
	}

	public function actionPcarate($id)
	{
		$chargecode_id = $id;
		$model = Org::model()->findByPk(114); // for our own org PCAE
		$chargecode = IntlChargeCode::model()->findByPk($chargecode_id);
		$this->render('pcarate', array(
			'model' => $model,
			'chgcodeid' => $chargecode_id
		));
	}

	public function actionSavePcaZonePrice()
	{
		$chargeCodeId = $_POST['cid'];

		$data = $_POST['data'];
		$data = json_decode($data, true);

		if (!empty($data) && !empty($chargeCodeId)) {
			$trans = Yii::app()->db->beginTransaction();
			try {
				$chargeCode = IntlChargeCode::model()->findByPk($chargeCodeId);
				$chargeCode->save();

				// step 1 - clear all old zone rate data
				ZoneRateIntl::model()->deleteAll('chargecode_id = :cid', array(':cid' => $chargeCodeId));

				// step 2 - update all with new zone rate data
				foreach ($data as $wRates) {
					$lo = $wRates['lo'];
					$hi = $wRates['hi'];
					$nkg = $wRates['nkg'];
					if ($hi <= 0 || $nkg < 0) {
						continue;
					}
					// ignore invalid one
					foreach ($wRates['data'] as $rate) {
						$zoneRate = new ZoneRateIntl();
						$zoneRate->rate_id = 0;
						$zoneRate->chargecode_id = $chargeCodeId;
						$zoneRate->weight_lo = $lo;
						$zoneRate->weight_hi = $hi;
						$zoneRate->zone = $rate['code'];
						$zoneRate->zone_name = $rate['name'];

						$zoneRate->item = $rate['ppc'];
						$zoneRate->base = $rate['base'];
						$zoneRate->perkg = $rate['pkg'];
						$zoneRate->nkg = $nkg;
						$zoneRate->minimum = $rate['minimum'];
						$zoneRate->gst = 1;
						$zoneRate->levy = 0;
						$zoneRate->save();
					}
				}
				$trans->commit();
			} catch (Exception $ex) {
				$trans->rollback();
				throw $ex;
			}
		}

		$resp = array('success' => 1);
		echo json_encode($resp);
	}

	public function actionWmsChargecodeMng()
	{
		$model = new WmsChargeCode('search');
		if (isset($_GET['WmsChargeCode'])) {
			$model->attributes = $_GET['WmsChargeCode'];
		}

		$this->render('list_wms_chargecode', ['model' => $model]);
	}

	public function actionCreateWmsChargecode()
	{
		$model = new WmsChargeCode;
		if (empty($_POST)) {
			$this->render('create_wms_charge_code', ['model' => $model]);
		} else {
			if (empty($_POST['Rate']['weight_lo']) || empty($_POST['Rate']['weight_hi']) || empty($_POST['Rate']['base']) || empty($_POST['Rate']['perkg']) || empty($_POST['Rate']['minimum'])) {
				echo json_encode(['done' => false, 'msg' => 'Rate is empty']);
				Yii::app()->end();
			}

			if (sizeof($_POST['Rate']['weight_lo']) != sizeof($_POST['Rate']['weight_hi']) || sizeof($_POST['Rate']['weight_lo']) != sizeof($_POST['Rate']['base']) || sizeof($_POST['Rate']['weight_lo']) != sizeof($_POST['Rate']['perkg']) || sizeof($_POST['Rate']['weight_lo']) != sizeof($_POST['Rate']['minimum'])) {
				echo json_encode(['done' => false, 'msg' => 'Rate is incomplete']);
				Yii::app()->end();
			}

			foreach ($_POST['Rate']['weight_lo'] as $k => $v) {
				if ($k != 0 && $_POST['Rate']['weight_hi'][$k - 1] > $v) {
					echo json_encode(['done' => false, 'msg' => 'There is overlap weight range']);
					Yii::app()->end();
				}
			}

			$model->attributes = $_POST['WmsChargeCode'];
			$model->status = 1;
			if ($model->save()) {
				foreach ($_POST['Rate']['weight_lo'] as $k => $v) {
					$rate = new WmsChargeRate;
					$rate->chargecode_id = $model->id;
					$rate->weight_lo = $_POST['Rate']['weight_lo'][$k];
					$rate->weight_hi = $_POST['Rate']['weight_hi'][$k];
					$rate->base = $_POST['Rate']['base'][$k];
					$rate->perkg = $_POST['Rate']['perkg'][$k];
					$rate->minimum = $_POST['Rate']['minimum'][$k];
					$rate->save();
				}
			}

			echo json_encode(['done' => true, 'msg' => 'Create successfully']);
			Yii::app()->end();
		}
	}

	public function actionUpdateWmsChargecode($id)
	{
		$model = WmsChargeCode::model()->findByPk($id);
		if (empty($_POST)) {
			$this->render('update_wms_charge_code', ['model' => $model]);
		} else {

			if (empty($_POST['Rate']['weight_lo']) || empty($_POST['Rate']['weight_hi']) || empty($_POST['Rate']['base']) || empty($_POST['Rate']['perkg']) || empty($_POST['Rate']['minimum'])) {
				echo json_encode(['done' => false, 'msg' => 'Rate is empty']);
				Yii::app()->end();
			}

			if (sizeof($_POST['Rate']['weight_lo']) != sizeof($_POST['Rate']['weight_hi']) || sizeof($_POST['Rate']['weight_lo']) != sizeof($_POST['Rate']['base']) || sizeof($_POST['Rate']['weight_lo']) != sizeof($_POST['Rate']['perkg']) || sizeof($_POST['Rate']['weight_lo']) != sizeof($_POST['Rate']['minimum'])) {
				echo json_encode(['done' => false, 'msg' => 'Rate is incomplete']);
				Yii::app()->end();
			}

			foreach ($_POST['Rate']['weight_lo'] as $k => $v) {
				if ($k != 0 && $_POST['Rate']['weight_hi'][$k - 1] > $v) {
					echo json_encode(['done' => false, 'msg' => 'There is overlap weight range']);
					Yii::app()->end();
				}
			}

			$model = WmsChargeCode::model()->findByPk($id);
			$model->attributes = $_POST['WmsChargeCode'];
			if ($model->save()) {
				foreach($model->rates as $oldrate){
					$oldrate->chargecode_id = 0;
					$oldrate->save();
				}
				foreach ($_POST['Rate']['weight_lo'] as $k => $v) {
					$rate = new WmsChargeRate;
					$rate->chargecode_id = $model->id;
					$rate->weight_lo = $_POST['Rate']['weight_lo'][$k];
					$rate->weight_hi = $_POST['Rate']['weight_hi'][$k];
					$rate->base = $_POST['Rate']['base'][$k];
					$rate->perkg = $_POST['Rate']['perkg'][$k];
					$rate->minimum = $_POST['Rate']['minimum'][$k];
					$rate->save();
				}
			}
			echo json_encode(['done' => true, 'msg' => 'Create successfully']);
			Yii::app()->end();
		}
	}

	public function actionLocalChargecodeMng()
	{
		$model = new ImportChargeCode('search');
		if ( isset($_GET['ImportChargeCode']) ) {
			$model->attributes = $_GET['ImportChargeCode'];
		}
		$model->dpmt = 30;

		$this->render('list_local_chargecode', ['model' => $model]);
	}

	public function actionIntlZoneMapList()
	{
		$model = new ZoneMapIntl('search');
		$model->unsetAttributes();
		if (isset($_GET['ZoneMapIntl'])) {
			$model->attributes = $_GET['ZoneMapIntl'];
		}
		if (!empty($_GET['chargecode_id'])) {
			$model->chargecode_id = $_GET['chargecode_id'];
		}
		if (!empty($_GET['z1'])) {
			$model->z1 = $_GET['z1'];
		}
		if (!empty($_GET['z2'])) {
			$model->z2 = $_GET['z2'];
		}
		if (!empty($_GET['zone_name'])) {
			$model->zone_name = $_GET['zone_name'];
		}

		$this->render('intl_zone_map_list', ['model' => $model]);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = Org::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}
}
