<?php

/*
 * Manage products and storage
 *
 */

class ProductController extends Controller
{

	public function actionGoods()
	{
		$_GET['tabid'] = 11111111;
		$model = new WmsProd('search');
		$model->unsetAttributes();
		if (isset($_GET['WmsProd'])) {
			$model->attributes = $_GET['WmsProd'];
		}
		$model->status = 1;
		$model->prodids = Pcaw::wmsProd(User::getOrgIds());

		$this->render('product_list', array('model' => $model));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new WmsProd;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$_GET['tabid'] = 1112231;
		if (isset($_POST['WmsProd'])) {
			$model=WmsProd::model()->find('ean=:ean',array(':ean'=>$_POST['WmsProd']['ean']));
			if(empty($model)){
				$model=new WmsProd;
				$model->attributes=$_POST['WmsProd'];
				$isNew=false;
			}else{
				$isNew=true;
			}
			if(!empty($_POST['dim'])) $model->dims = $_POST['dim'];
			$model->save();
			//                          $wprod= new WmsProdOrg();
			//                          $wprod->org_id=Yii::app()->user->org;
			//                          $wprod->prod_id=$model->id;
			//                          if(!empty($_POST['prod_sku'])){
			//                              $wprod->sku=$_POST['prod_sku'];
			//                          }else{
			//                              $wprod->sku=$model->ean;
			//                          }
			//                          $wprod->value=empty($_POST['prod_value'])?0:$_POST['prod_value'];
			//                      if(!$wprod->save()) {
			//                                if($isNew){
			//                                  $model->delete();
			//                                }
			//                                 $this->ajaxResult($wprod);
			//                         }
			//                      }
			$model = WmsProd::model()->find('ean = :ean AND status = 1', array(':ean' => $_POST['WmsProd']['ean']));
			$prod_org = new WmsProdOrg();
			$prod_org->prod_id = $model->id;
			$prod_org->org_id = Yii::app()->user->org;
			$prod_org->sku = $_POST['prod_sku'];
			$prod_org->save();
			$this->ajaxResult($model, array('id' => $model->id));
		}
		$model->type = 10;
		$model->status = 1;
		$this->render('create', array(
			'model' => $model,
		));
	}

	public function actionCreateKit()
	{
		$_GET['tabid'] = 23456789;
		if (empty($_POST)) {
			$model = new WmsProd;
			$model->type = WmsProd::WMS_PROD_KIT;
			$this->render('create_kit', array('model' => $model));
		} else {
			$model = WmsProd::model()->find('ean = :ean AND status = 1', [':ean' => $_POST['WmsProd']['ean']]);
			if (empty($model)) {
				$model = new WmsProd;
				$model->type = WmsProd::WMS_PROD_KIT;
				$model->attributes = $_POST['WmsProd'];
				$model->status = 1;
				$model->save();
			}
			$org = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $model->id, ':oid' => Yii::app()->user->org]);
			if (empty($org)) {
				$org = new WmsProdOrg;
				$org->prod_id = $model->id;
				$org->org_id = Yii::app()->user->org;
				$org->sku = $model->ean;
				$org->save();
			}
			$stock = WmsStock::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $model->id, ':oid' => Yii::app()->user->org]);
			if (empty($stock)) {
				$stock = new WmsStock;
				$stock->prod_id = $model->id;
				$stock->org_id = Yii::app()->user->org;
				$stock->save();
			}
			if (empty($model->getErrors())) {
				foreach ($model->items as $item) {
					$item->delete();
				}
				$prods = [];
				foreach ($_POST['id'] as $k => $id) {
					if (empty($_POST['sku'][$k]) || empty($_POST['id'][$k])) {
						$model->addError('id', 'Please input sku for line ' . $k);
						continue;
					}
					if (empty($_POST['qty'][$k])) {
						$model->addError('id', 'Please input qty for line ' . $k);
						continue;
					}

					if (empty($prods[$_POST['id'][$k]])) $prods[$_POST['id'][$k]] = 0;
					$prods[$_POST['id'][$k]] += $_POST['qty'][$k];
				}
				foreach ($prods as $id => $qty) {
					$kit = new WmsProdKit;
					$kit->kit_id = $model->id;
					$kit->item_id = $id;
					$kit->qty = $qty;
					$kit->save();
				}
			}

			$this->ajaxResult($model);
		}
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		$_GET['tabid'] = 1112232;
		// if(!in_array($model->id, Pcaw::wmsProd(User::getOrgIds()))) Acl::denied403();
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['WmsProd'])) {
			$model->attributes = $_POST['WmsProd'];
			if (!empty($_POST['dim'])) $model->dims = $_POST['dim'];

			if ($model->status == 0) {
				$flag = true;
				foreach ($model->stocks as $stock) {
					if ($stock->qty > 0 || $stock->qty_res > 0) $flag = false;
				}
				if (!$flag) {
					echo json_encode(['done' => 'false', 'msg' => 'There are stocks']);
					Yii::app()->end();
				} else {
					foreach ($model->stocks as $stock) {
						if ($stock->org_id == Yii::app()->user->org) {
							$stock->bwf |= 2;
							$stock->update('bwf');
						}
					}
				}

				$model->save();
				$this->ajaxResult($model);
			}

			$model->save();
			$model = WmsProd::model()->find('ean = :ean AND status = 1', array(':ean' => $_POST['WmsProd']['ean']));
			$org = WmsProdOrg::model()->find('prod_id = :pid AND org_id = :oid', [':pid' => $model->id, ':oid' => Yii::app()->user->org]);
			if (empty($org)) {
				$org = new WmsProdOrg;
				$org->prod_id = $model->id;
				$org->org_id = Yii::app()->user->org;
			}
			if (isset($_POST['prod_sku'])) {
				$org->sku = $_POST['prod_sku'];
			}
			if (isset($_POST['WmsProd']['mdata']['expiry_span'])) {
				$model->mdata['expiry_span'] = $_POST['WmsProd']['mdata']['expiry_span'];
				$model->update('meta');
			}
			if (isset($_POST['min_stock_alert'])) {
				$org->mdata['min_stock_alert'] = $_POST['min_stock_alert'];
				$org->save();
			}

			if (!empty($_POST['WmsProd']['weight'])) {
				$packs = WmsProdPack::model()->findAll('qty = 1 AND prod_id = :pid AND type = 10', [':pid' => $model->id]);
				foreach ($packs as $pack) {
					$pack->weight = $_POST['WmsProd']['weight'];
					$pack->save();
				}
			}

			$this->ajaxResult($model);
		}
		$this->render('update', array('model' => $model));
	}

	public function actionUpdateKit($id)
	{
		$_GET['tabid'] = 34567890;
		$model = $this->loadModel($id);
		if (empty($_POST)) {
			$this->render('update_kit', ['model' => $model]);
		} else {
			foreach ($model->items as $item) {
				$item->delete();
			}
			$prods = [];
			foreach ($_POST['id'] as $k => $id) {
				if (empty($_POST['sku'][$k]) || empty($_POST['id'][$k])) {
					$model->addError('id', 'Please input sku for line ' . $k);
					continue;
				}
				if (empty($_POST['qty'][$k])) {
					$model->addError('id', 'Please input qty for line ' . $k);
					continue;
				}

				if (empty($prods[$_POST['id'][$k]])) $prods[$_POST['id'][$k]] = 0;
				$prods[$_POST['id'][$k]] += $_POST['qty'][$k];
			}
			foreach ($prods as $id => $qty) {
				$kit = new WmsProdKit;
				$kit->kit_id = $model->id;
				$kit->item_id = $id;
				$kit->qty = $qty;
				$kit->save();
			}

			$this->ajaxResult($model);
		}
	}

	public function actionPackageGrid($id)
	{
		if (!empty($_POST['WmsProdPack'])) {
			if (empty($_POST['WmsProdPack']['id'])) {
				$model = new WmsProdPack;
				$model->prod_id = $id;
			} else {
				$model = WmsProdPack::model()->findByPk($_POST['WmsProdPack']['id']);
			}
			$model->attributes = $_POST['WmsProdPack'];
			if (!empty($_POST['dim'])) {
				$model->dims = $_POST['dim'];
			}
			$model->save();
			$this->ajaxResult($model);
		}
	}

	public function actionImport()
	{
		$_GET['tabid'] = 123121111;
		if (!empty($_FILES['excel'])) {
			$xls = new oExcel;
			$err = [];
			$resp = array('done' => true, 'msg' => 'Import successfully');
			if (!$xls->supported($_FILES['excel']['name'])) {
				foreach ($xls->getError() as $e) {
					$err[] = $e;
				}
			} else {
				$xls->load($_FILES['excel']['tmp_name']);
				$data = $xls->getAll();
				if (strtolower(implode(',', $data[1])) == strtolower('EAN,Name,Chinese Name,Brand,Sku,Weight (g),DIM (WxHxD),Carton Qty,Carton Weight (kg),Carton DIM (WxHxD),Pallet Qty,Pallet Weight (kg),Pallet DIM (WxHxD),Country Of Origin,Description,Value')) {
					foreach ($data as $r => $d) {
						if (($r < 2 || (empty($d[2]) && empty($d[3])))) {
							// if (empty($d[2])) $err[] = 'line ' . $r . ' does not have name';
							continue;
						}

						$d[1] = preg_replace('/\s+/', '', $d[1]);
						if (!empty($d[1])) {
							$model = WmsProd::model()->find('ean = :d AND status = 1', [':d' => $d[1]]);
						} else {
							$model = WmsProd::model()->find('ean = "" AND name = :d AND status = 1', [':d' => $d[2]]);
						}

						if (empty($model)) {
							$model = new WmsProd;
							$model->type = 10;
							$model->status = 1;
							$model->ean = $d[1];
							$model->name = $d[2];
							$model->name_zh = $d[3];
							$model->brand = $d[4];
							$model->mdata['coo'] = @$d[14];
							// $model->org_id = Yii::app()->user->org;
							if (preg_match('/(\d*)/', $d[6], $matches)) {
								$d[6] = $matches[0];
							}
							$model->weight = $d[6];
							if (empty($d[8])) {
								$err[] = 'each carton Qty must be supplied!';
							}

							if (empty($d[1])) {
								$err[] = 'ean must be supplied!';
							}

							if (empty($d[5])) {
								$err[] = 'sku must be supplied!';
							}

							if (empty($d[9])) {
								$err[] = 'carton weight must be supplied!';
							}

							if (empty($d[8])) {
								$err[] = 'carton qty must be supplied!';
							}

							if (!empty($err)) {
								$resp['msg'] = implode(';', $err);
								$resp['done'] = false;
								echo json_encode($resp);
								return;
							}

							if (preg_match('/([\d\.]+)x([\d\.]+)x([\d\.]+)/', $d[7], $dim)) {
								$model->dims = ['d' => $dim[1], 'h' => $dim[2], 'd' => $dim[3]];
							}
							$model->save();
						}

						//we need to save the
						$wpo = WmsProdOrg::model()->find('org_id = :org_id AND prod_id = :prod_id', [':org_id' => Yii::app()->user->org, ':prod_id' => $model->id]);
						if (empty($wpo)) {
							$wpo = new WmsProdOrg('create');
							$wpo->org_id = Yii::app()->user->org;
							$wpo->prod_id = $model->id;
						}
						$wpo->sku = $d[5];
						$wpo->mdata['desc'] = @$d[15];
						$wpo->mdata['value'] = @$d[16];
						$wpo->save();
						if (!empty($d[8])) {
							$p = WmsProdPack::model()->find('prod_id = :p AND type = 10', [':p' => $model->id]);
							if (empty($p)) {
								$p = new WmsProdPack;
							}

							$p->prod_id = $model->id;
							$p->type = 10;
							$p->qty = $d[8];
							$p->weight = $d[9];
							if (preg_match('/([\d\.]+)x([\d\.]+)x([\d\.]+)/', $d[10], $dim)) {
								$p->dims = ['w' => $dim[1], 'h' => $dim[2], 'd' => $dim[3]];
							}
							$p->save();
						}

						if (!empty($d[11])) {
							$p = WmsProdPack::model()->find('prod_id = :p AND type = 20', [':p' => $model->id]);
							if (empty($p)) {
								$p = new WmsProdPack;
							}

							$p->prod_id = $model->id;
							$p->type = 20;
							$p->qty = $d[11];
							$p->weight = $d[12];
							if (preg_match('/([\d\.]+)x([\d\.]+)x([\d\.]+)/', $d[13], $dim)) {
								$p->dims = ['w' => $dim[1], 'h' => $dim[2], 'd' => $dim[3]];
							}
							$p->save();
						}
					}
				} elseif (implode(',', $data[1]) == 'EAN,SKU') {
					if (empty($_POST['org_id'])) {
						$err[] = 'Organisation not found';
					} else {
						foreach ($data as $r => $d) {
							if ($r < 2 || empty($d[1]) || empty($d[2])) {
								continue;
							}

							$model = WmsProd::model()->find('ean = :d AND status = 1', [':d' => $d[1]]);
							if (empty($model)) {
								$err[] = 'Line ' . $r . ': EAN ' . $d[1] . ' not found';
							} else {
								$wpo = WmsProdOrg::model()->find('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $model->id, ':org_id' => $_POST['org_id']]);
								if (empty($wpo)) {
									$wpo = new WmsProdOrg('create');
									$wpo->org_id = $_POST['org_id'];
									$wpo->prod_id = $model->id;
								}
								$wpo->sku = $d[2];
								$wpo->save();
							}
						}
					}
				} else {
					$err[] = 'Column mismatch, please make sure the file is correct or download the new template';
				}
			}
			if (!empty($err)) {
				$model = new WmsProd;
				foreach ($err as $e) {
					$model->addError('name', $e);
				}
			} else if (empty($model)) {
				$model = new WmsProd;
			}
			$this->ajaxResult($model);
		}
		$this->render('import');
	}

	public function actionImportKit()
	{
		$_GET['tabid'] = 12345678;
		if (empty($_FILES['excel'])) {
			$this->render('import_kit');
		} else {
			$xls = new oExcel;
			$err = [];
			$resp = array('done' => true, 'msg' => 'Import successfully');
			if (!$xls->supported($_FILES['excel']['name'])) {
				foreach ($xls->getError() as $e) {
					$err[] = $e;
				}
			} else {
				$xls->load($_FILES['excel']['tmp_name']);
				$data = $xls->getAll();
				if (strtolower(implode(',', $data[1])) == strtolower('Name,Bundle/Package SKU,Content Item Name,Content Item Barcode/Content Item SKU,Content Item Qty')) {
					foreach ($data as $r => $d) {
						if ($r < 2) continue;
						if (empty($d[1])) {
							$err[] = 'line ' . $r . ' require Name';
							continue;
						}
						if (empty($d[2])) {
							$err[] = 'line ' . $r . ' require Bundle/Package SKU';
							continue;
						}
						if (empty($d[4])) {
							$err[] = 'line ' . $r . ' require Content Item Barcode/Content Item SKU';
							continue;
						}
						if (empty($d[5])) {
							$err[] = 'line ' . $r . ' require Content Item Qty';
							continue;
						}
						if (is_nan($d[5])) {
							$err[] = 'line ' . $r . ' Content Item Qty is invalid';
							continue;
						}

						$d[2] = preg_replace('/\s+/', '', $d[2]);
						if (!empty($d[2])) {
							$model = WmsProd::model()->find('ean = :d AND type = :type AND status = 1', [':d' => $d[2], ':type' => WmsProd::WMS_PROD_KIT]);
						}
						if (empty($model)) {
							$model = new WmsProd;
							$model->type = WmsProd::WMS_PROD_KIT;
							$model->status = 1;
							$model->ean = $d[2];
							$model->name = $d[1];
							$model->save();
						}

						$d[4] = preg_replace('/\s+/', '', $d[4]);
						$item = WmsProd::model()->find('ean = :d AND type = :type AND status = 1', [':d' => $d[4], ':type' => WmsProd::WMS_PROD_PHYSICAL]);
						if (empty($item)) {
							$item = WmsProd::model()->with('orgs')->find('orgs.sku = :d AND t.type = :type AND t.status = 1', [':d' => $d[4], ':type' => WmsProd::WMS_PROD_PHYSICAL]);
						}
						if (empty($item)) {
							$err[] = 'line ' . $r . ' Content Item Barcode/Content Item SKU is invalid';
							continue;
						}
						$wpk = WmsProdKit::model()->find('kit_id = :kid AND item_id = :pid', [':kid' => $model->id, ':pid' => $item->id]);
						if (empty($wpk)) {
							$wpk = new WmsProdKit;
							$wpk->kit_id = $model->id;
							$wpk->item_id = $item->id;
						}
						$wpk->qty = $d[5];
						$wpk->save();
					}
				}
			}
			if (!empty($err)) {
				$model = new WmsProd;
				foreach ($err as $e) {
					$model->addError('name', $e);
				}
			} else if (empty($model)) {
				$model = new WmsProd;
			}
			$this->ajaxResult($model);
		}
	}

	/*
	 * for the storage list
	 */

	public function actionStorage()
	{
		$model = new WmsStock('search');
		$model->unsetAttributes();
		if (!empty($_GET['WmsStock'])) {
			$model->setAttributes($_GET['WmsStock']);
		}
		$model->orgids = User::getOrgIds();
		if (empty($model->orgids)) {
			$model->orgids = array_merge([1], User::getOrgIds()); //array_merge make sure it's not empty, because when empty, the search method will not use the  orgids field.
		}
		if(!empty($_GET['org_id'])){
			Yii::app()->session['org_id'] = $_GET['org_id'];
		}
		$this->render('storage_list', array('model' => $model));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = WmsProd::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $model;
	}

	public function actionSuggest()
	{
		$_GET['term'] = trim($_GET['term']);
		$rs = WmsProd::model()->with(['packs', 'orgs'])->together()->findAll(array(
			'condition' => 'status = 1 AND (name LIKE :n OR ean LIKE :a OR name_zh LIKE :n OR packs.barcode LIKE :a OR (orgs.org_id = :oid AND orgs.sku LIKE :a))',
			'params' => array(':n' => '%' . $_GET['term'] . '%', ':a' => '%' . $_GET['term'] . '%', ':oid' => Yii::app()->user->org),
			'order' => 'name',
			'limit' => 20,
		));
		$a = array();

		foreach ($rs as $r) {
			$pp = WmsProdPack::model()->find('type = 10 AND prod_id = :pid', [':pid' => $r->id]);
			$qpc = empty($pp) ? 0 : $pp->qty;

			$pp = WmsProdPack::model()->find('type = 20 AND prod_id = :pid', [':pid' => $r->id]);
			$qpp = empty($pp) ? 0 : $pp->qty;

			$a[] = array(
				'value' => $r->id,
				'label' => $r->name,
				'qpc' => $qpc,
				'qpp' => $qpp,
				'sku' => $r->getSku(Yii::app()->user->org),
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

	//pick unit suggest
	public function actionSuggest1()
	{
		$_GET['term'] = trim($_GET['term']);
		// 2020-04-17 add mel
		$cond = 't.qty>0 AND t.org_id = :oid AND (prod.name LIKE :n OR prod.ean LIKE :a OR prod.name_zh LIKE :n OR packs.barcode LIKE :a OR (orgs.org_id = :oid AND orgs.sku LIKE :a))';
		$params = array(':n' => '%' . $_GET['term'] . '%', ':a' => $_GET['term'] . '%', ':oid' => $_GET['oid']);
		if (!empty($_GET['dpt_id'])) {
			$cond .= ' AND t.dpt_id = :dpt_id';
			$params[':dpt_id'] = $_GET['dpt_id'];
		}
		$rs = WmsStock::model()->with(['prod.packs', 'prod.orgs'])->together()->findAll(array(
			'condition' => $cond,
			'params' => $params,
			'order' => 'name',
			'limit' => 20,
		));
		$a = array();

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

			if ($r->qty - $r->qty_res == 0) {
				continue;
			}
			$a[] = array(
				'value' => $r->id,
				'label' => $r->stockName(),
				'remain' => 'Remain: ' . (int) ($r->qty - $r->qty_res),
				'qty' => $r->qty,
				'qpc' => $qpc,
				'qpp' => $qpp,
				'meb' => $meb,
			);
		}

		if (empty($a)) {
			$prods = WmsProd::model()->with(['packs', 'orgs'])->together()->findAll(array(
				'condition' => 't.name LIKE :n OR t.ean LIKE :a OR t.name_zh LIKE :n OR packs.barcode LIKE :a OR (orgs.org_id = :oid AND orgs.sku LIKE :a) AND t.type = :type AND t.status = 1',
				'params' => array(':n' => '%' . $_GET['term'] . '%', ':a' => $_GET['term'] . '%', ':oid' => $_GET['oid'], ':type' => WmsProd::WMS_PROD_KIT),
				'order' => 'name',
				'limit' => 20,
			));
			foreach ($prods as $prod) {
				$remain = PHP_INT_MAX;
				foreach ($prod->items as $item) {
					$count = 0;
					$rs = WmsStock::model()->findAll('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $item->item->id, ':org_id' => $_GET['oid']]);
					foreach ($rs as $r) {
						$count += $r->qty - $r->qty_res;
					}
					$remain = ceil($count / $item->qty) < $remain ? ceil($count / $item->qty) : $remain;
				}
				if ($remain > 0 && $remain != PHP_INT_MAX) {
					$r = WmsStock::model()->find('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $prod->id, ':org_id' => $_GET['oid']]);
					if (empty($r)) {
						$r = new WmsStock;
						$r->prod_id = $prod->id;
						$r->org_id = $_GET['oid'];
						$r->qty = 0;
						$r->qty_res = 0;
						$r->save();
					}
					$a[] = array(
						'value' => $r->id,
						'label' => $r->stockName(),
						'remain' => 'Remain: ' . $remain,
						'qty' => $r->qty,
					);
				}
			}
		}

		echo json_encode($a);
	}

	public function actionSuggest2()
	{
		$_GET['term'] = trim($_GET['term']);
		$type = $_GET['type'];
		$rs = WmsProd::model()->with(['packs', 'orgs'])->together()->findAll(array(
			'condition' => 'status = 1 AND (name LIKE :n OR ean LIKE :a OR name_zh LIKE :n OR packs.barcode LIKE :a OR (orgs.org_id = :oid AND orgs.sku LIKE :a)) AND t.`type` = :t',
			'params' => array(':n' => '%' . $_GET['term'] . '%', ':a' => '%' . $_GET['term'] . '%', ':oid' => Yii::app()->user->org, ':t' => $type),
			'order' => 'name',
			'limit' => 20,
		));
		$a = array();

		foreach ($rs as $r) {
			$a[] = array(
				'value' => $r->id,
				'label' => $r->name,
				'name_zh' => $r->name_zh,
				'ean' => $r->ean,
				'brand' => $r->brand,
				'dim' => json_decode($r->dim),
				'cbm' => $r->cbm,
				'weight' => $r->weight,
			);
		}
		echo json_encode($a);
	}

	public function actionSuggestSKU()
	{
		$_GET['term'] = trim($_GET['term']);
		$type = $_GET['type'];
		$rs = WmsProd::model()->with(['packs', 'orgs'])->together()->findAll(array(
			'condition' => 'status = 1 AND orgs.org_id = :oid AND (orgs.sku LIKE :a OR t.ean LIKE :a) AND t.`type` = :t',
			'params' => array(':a' => '%' . $_GET['term'] . '%', ':oid' => Yii::app()->user->org, ':t' => $type),
			'order' => 'name',
			'limit' => 20,
		));
		$a = array();

		foreach ($rs as $r) {
			$a[] = array(
				'value' => $r->id,
				'label' => $r->getSku(Yii::app()->user->org),
				'name' => $r->name,
				'name_zh' => $r->name_zh,
				'ean' => $r->ean,
				'brand' => $r->brand,
				'dim' => json_decode($r->dim),
				'cbm' => $r->cbm,
				'weight' => $r->weight,
			);
		}
		echo json_encode($a);
	}

	public function actionExportStock()
	{
		$model = new WmsStock('search');
		$model->unsetAttributes(); // clear any default values
		if (isset($_GET['WmsStock'])) {
			$model->attributes = $_GET['WmsStock'];
		}

		$model->orgids = User::getOrgIds();
		if (empty(User::getOrgIds())) {
			$model->orgids = array_merge([1], User::getOrgIds());
		}
		if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
			$model->orgids = [Yii::app()->session['org_id']];
		}
		$dp = $model->search(false);
		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth(array(30, 15, 15, 40, 15, 15, 10, 10, 10));
		$xls->addRow($i++, array('Customer', 'EAN', 'SKU', 'Product', 'Brand', 'Model', 'Expiry', 'Batch', 'Avail. Qty', 'Rsvd. Qty'));

		foreach ($dp->data as $r) {
			$xls->addRow($i++, array($r->customer->name, '="' . $r->prod->ean . '"', '="' . $r->getCustSKU() . '"', $r->prod->name, $r->prod->brand, $r->prod->model, $r->expiry, $r->batch, $r->getQty(), $r->qty_res));
		}
		$xls->output('stock_search_export_' . time() . '.xlsx');

	}

	public function actionPrint($id)
	{
		$model = $this->loadModel($id);
		oPDF::renderPDF('label_plt', array('cs' => array($model->getSku()), 'dup' => false));
	}

	public function actionPrintHorizontal($id)
	{
		$model = $this->loadModel($id);
		oPDF::renderPDF('muchen_barcode', array('model' => $model));
	}

	public function actionExportStockWithLocation()
	{
		$model = new WmsStock('search');
		$model->unsetAttributes(); // clear any default values
		if (isset($_GET['WmsStock'])) {
			$model->attributes = $_GET['WmsStock'];
		}

		$model->orgids = User::getOrgIds();
		if (empty(User::getOrgIds())) {
			$model->orgids = array_merge([1], User::getOrgIds());
		}
		if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
			$model->orgids = [Yii::app()->session['org_id']];
		}
		$dp = $model->search(false);
		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth(array(15, 15, 40, 15, 10, 10, 10));
		$xls->addRow($i++, array('EAN', 'SKU', 'Product', 'Brand', 'Model', 'Location', 'Expiry', 'Batch', 'Avail. Qty', 'Rsvd. Qty', 'In Time'));

		foreach ($dp->data as $r) {
			foreach ($r->locs as $loc) {
				if ($loc->loc->type != 99) {
					$sl = WmsStockLedger::model()->find(['condition' => 'location_id = :loc_id', 'params' => [':loc_id' => $loc->loc->id], 'order' => 'id ASC']);
					$xls->addRow($i++, array('="' . $r->prod->ean . '"', '="' . $r->getCustSKU() . '"', $r->prod->name, $r->prod->brand, $r->prod->model, $loc->loc->name, $r->expiry, $r->batch, $loc->qty, '', $sl->ts));
				}
			}
			if ($r->qty != 0) {
				$xls->addRow($i++, array('', '', '', '', '', '', '', '', '', $r->qty_res));
				$xls->addRow($i++, array());
			}
		}
		$xls->output('stock_with_loc_export_' . time() . '.xlsx');
	}

	public function actionExportStockExpiry()
	{
		$model = new WmsStock('search');
		$model->unsetAttributes();

		$ec = new CDbCriteria;
		$ec->addCondition('expiry < "' . date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 month')) . '"');

		$model->orgids = User::getOrgIds();
		if (empty(User::getOrgIds())) {
			$model->orgids = array_merge([1], User::getOrgIds());
		}
		if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
			$model->orgids = [Yii::app()->session['org_id']];
		}
		$dp = $model->search(false, 0, $ec);
		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth(array(15, 15, 40, 15, 10, 10, 10));
		$xls->addRow($i++, array('EAN', 'SKU', 'Product', 'Location', 'Expiry', 'Batch', 'Avail. Qty', 'Rsvd. Qty'));

		foreach ($dp->data as $r) {
			foreach ($r->locs as $loc) {
				if ($loc->loc->type != 99) {
					$xls->addRow($i++, array('="' . $r->prod->ean . '"', '="' . $r->getCustSKU() . '"', $r->prod->name, $loc->loc->name, $r->expiry, $r->batch, $loc->qty));
				}
			}
			if ($r->qty != 0) {
				$xls->addRow($i++, array('', '', '', '', '', '', '', $r->qty_res));
				$xls->addRow($i++, array());
			}
		}
		$xls->output('stock_expiry_export_' . time() . '.xlsx');
	}

	public function actionFile($id)
	{
		$result = FileRepo::storeFile($_FILES['file']['tmp_name'], $_FILES['file']['name'], 135, $id);
		if ($result) {
			$file = FileRepo::model()->find(['condition' => 'fid = :id', 'params' => [':id' => $id], 'order' => 'id DESC']);
			echo json_encode(['file' => '<div style="border: 1px solid #ddd; padding: 10px; float: left; width: 200px; height: 200px; position: relative"><img src="' . Yii::app()->baseUrl . '/filerepo/' . $file->hash . '/' . $file->name . '" style="width: 100%; max-height: 100%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%)"></div>']);
		}
	}

	public function getBanchName($dpt_id)
	{
		if ($dpt_id==106){
			echo "SYD";
		}
		else if($dpt_id==218){
			echo "MEL";
		}
		else{
			echo "UNKNOW";
		}
	}

}
