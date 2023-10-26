<?php

class WmsProdController extends Controller
{
	protected $nonAjax = ['printBarcode', 'printBarcodeLarge', 'file', 'printLabel'];
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
	public function actionCreate()
	{
		$model = new WmsProd;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['WmsProd'])) {
			$model->attributes = $_POST['WmsProd'];
			if (!empty($_POST['dim'])) {
				$model->dims = $_POST['dim'];
			}

			if (!empty($_POST['mdata'])) {
				$model->mdata = $_POST['mdata'];
			}

			$model->save();
			$this->ajaxResult($model);
		}
		$model->type = 10;
		$model->status = 1;

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

		if (isset($_POST['WmsProd'])) {
			$model->attributes = $_POST['WmsProd'];
			if (!empty($_POST['dim'])) {
				$model->dims = $_POST['dim'];
			}

			if (!empty($_POST['mdata'])) {
				$model->mdata = $_POST['mdata'];
			}

			$model->save();
			$this->ajaxResult($model);
		}

		if (isset($_GET['tab'])) {
			Acl::hasAccess($this->CaName . '/' . $_GET['tab'], true);
			$this->render('tab_' . $_GET['tab'], array('model' => $model));
		} else {
			$this->render('update', array('model' => $model));
		}
	}

	public function actionNotes($id)
	{
		$model = $this->loadModel($id);
		if (!empty($_POST['notes'])) {
			$log = Log::add($model, 6, array('notes' => $_POST['notes']));
			$this->ajaxResult($log);
		}
	}

	public function actionPrintBarcode($id)
	{
		$model = $this->loadModel($id);
		oPDF::renderPDF('wmsprod_barcode', array('model' => $model));
	}

	public function actionPrintBarcodeLarge($id)
	{
		$model = $this->loadModel($id);
		oPDF::renderPDF('wmsprod_barcode_large', array('model' => $model));
	}

	public function actionOrg()
	{
		if (empty($_GET['oid'])) {
			$model = new WmsProdOrg('create');
			$model->prod_id = $_GET['pid'];
		} else {
			$model = WmsProdOrg::model()->find('prod_id = :prod_id AND org_id = :org_id', [':prod_id' => $_GET['pid'], ':org_id' => $_GET['oid']]);
		}

		if (!empty($_POST)) {
			$model->attributes = $_POST['WmsProdOrg'];
			foreach ($_POST['mdata'] as $k => $v) {
				$model->mdata[$k] = $v;
			}

			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('org_options', array('model' => $model));
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

	public function actionPackageGrid($id)
	{
		if (!empty($_POST['WmsProdPack'])) {
			if (empty($_POST['WmsProdPack']['id'])) {
				$model = new WmsProdPack;
				$model->prod_id = $id;
			} else {
				$model = WmsProdPack::model()->findByPk($_POST['WmsProdPack']['id']);
			}
			unset($_POST['WmsProdPack']['id']);
			$model->attributes = $_POST['WmsProdPack'];
			if (!empty($_POST['dim'])) {
				$model->dims = $_POST['dim'];
			}
			$model->save();
			$this->ajaxResult($model);
		}
	}

	public function actionPackageGridDelete($id)
	{
		$model = WmsProdPack::model()->findByPk($id);
		if (!empty($model)) {
			$model->delete();
			$this->ajaxResult($model);
		}
	}

	/**
	 * Lists and search.
	 */
	public function actionList()
	{
		$model = new WmsProd('search');
		$model->unsetAttributes(); // clear any default values
		if (isset($_GET['WmsProd'])) {
			$model->attributes = $_GET['WmsProd'];
		}

		$this->render('list', array(
			'model' => $model,
		));
	}

	public function actionImport()
	{
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

					$trans = Yii::app()->db->beginTransaction();
					try{
					foreach ($data as $r => $d) {
						if ($r < 2 || (empty($d[2]) && empty($d[3]))) {
							// if (empty($d[2])) $err[] = 'line ' . $r . ' does not have name';
							continue;
						}

						$d[1] = preg_replace('/\s+/', '', $d[1]);
						if (!empty($d[1])) {
							$model = WmsProd::model()->find('ean = :d', [':d' => $d[1]]);
						} else {
							$model = WmsProd::model()->find('ean = "" AND name = :d', [':d' => $d[2]]);
						}

						if (!empty($model)) {
							continue;
						}

						$model = new WmsProd;
						$model->type = 10;
						$model->status = 1;
						$model->ean = $d[1];
						$model->name = $d[2];
						$model->name_zh = $d[3];
						$model->brand = $d[4];
						$model->model = $d[5];
						$model->weight = $d[6];
						$model->mdata['coo'] = @$d[14];
						// if(empty($d[8])) $err[] = 'each carton Qty must be supplied!';
						// if(empty($d[9])) $err[] = 'carton weight must be supplied!';

						if (!empty($err)) {
							$resp['msg'] = implode(';', $err);
							$resp['done'] = false;
							echo json_encode($resp);
							return;
						}

						if (preg_match('/([\d\.]+)x([\d\.]+)x([\d\.]+)/', $d[7], $dim)) {
							$model->dims = ['w' => $dim[1], 'h' => $dim[2], 'd' => $dim[3]];
						}
						$model->save();

						if (!empty($_POST['org_id'])) {
							$wpo = new WmsProdOrg('create');
							$wpo->org_id = $_POST['org_id'];
							$wpo->prod_id = $model->id;
							$wpo->sku = $d[5];
							$wpo->mdata['desc'] = @$d[15];
							$wpo->mdata['value'] = @$d[16];
							$wpo->save();
						}

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
							$p = WmsProdPack::model()->find('prod_id = :p AND type = 10', [':p' => $model->id]);
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
					$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}
				} elseif (implode(',', $data[1]) == 'EAN,SKU,Expiry') {
					if (empty($_POST['org_id'])) {
						$err[] = 'Organisation not found';
					} else {
						$trans = Yii::app()->db->beginTransaction();
						try{
						foreach ($data as $r => $d) {
							if ($r < 2 || empty($d[1])) {
								continue;
							}

							$model = WmsProd::model()->find('ean = :d', [':d' => $d[1]]);
							if (empty($model)) {
								$err[] = 'Line ' . $r . ': EAN ' . $d[1] . ' not found';
							} else {
								// $wpo = WmsProdOrg::model()->findByPk(['prod_id' => $model->id, 'org_id' => $_POST['org_id']]);
								// if (empty($wpo)) {
									$wpo = new WmsProdOrg('create');
									$wpo->org_id = $_POST['org_id'];
									$wpo->prod_id = $model->id;
								// }
								$wpo->sku = $d[2];
								if (!empty($d[3])) {
									$wpo->mdata['exp_acc'] = $d[3];
								}

								$wpo->save();
							}
						}
							$trans->commit();
		} catch (Exception $ex) {
			$trans->rollback();
			throw $ex;
		}
					}
				} else {
					$err[] = 'Column mismatch, please make sure the file is correct';
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

	public function actionSuggest()
	{
		$_GET['term'] = trim($_GET['term']);
		$rs = WmsProd::model()->with(['packs', 'orgs'])->together()->findAll(array(
			'condition' => 'status = 1 AND (name LIKE :n OR ean LIKE :a OR name_zh LIKE :n OR packs.barcode LIKE :a OR (orgs.org_id = :oid AND orgs.sku LIKE :a))',
			'params' => array(':n' => '%' . $_GET['term'] . '%', ':a' => '%' . $_GET['term'] . '%', ':oid' => $_GET['oid']),
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
			);
		}
		echo json_encode($a);
	}

	public function actionHscodeGrid($id)
	{
		if (!empty($_POST['WmsProdHscode'])) {
			if (empty($_POST['WmsProdHscode']['id'])) {
				$model = new WmsProdHscode;
				$model->prod_id = $id;
			} else {
				$model = WmsProdHscode::model()->findByPk($_POST['WmsProdHscode']['id']);
			}
			unset($_POST['WmsProdHscode']['id']);
			$model->attributes = $_POST['WmsProdHscode'];
			$model->save();
			$this->ajaxResult($model);
		}
	}

	public function actionHscodeGridDelete($id)
	{
		$model = WmsProdHscode::model()->findByPk($id);
		if (!empty($model)) {
			$model->delete();
			$this->ajaxResult($model);
		}
	}

	public function actionGenBarcode()
	{
		function randEAN()
		{
			$n = rand(200000000000, 299999999999);
			$ds = str_split($n);
			$tt = 0;
			for($i = 0; $i < 12; $i++){
				if($i%2 == 0){
					$tt += $ds[$i];
				}else{
					$tt += $ds[$i] * 3;
				}
			}

		    return $n.(10 - $tt % 10);
		}

		for ($i = 0; $i < 10; $i ++) {
			$ean = randEAN();
			$prod = WmsProd::model()->find('ean = :ean', [':ean' => $ean]);
			if (empty($prod)) {
				break;
			}
		}
		// if (!empty($_GET['id'])) {
		// 	$model = $this->loadModel($_GET['id']);
		// 	if (!empty($model)) {
		// 		$model->ean = $ean;
		// 		$model->update('ean');
		// 	}
		// }

		echo json_encode(['done' => true, 'msg' => 'Successfully', 'ean' => $ean]);
		yii::app()->end();
	}

	public function actionKit()
	{

	}

	public function actionFile($id)
	{
		$result = FileRepo::storeFile($_FILES['file']['tmp_name'], $_FILES['file']['name'], 135, $id);
		if ($result) {
			$file = FileRepo::model()->find(['condition' => 'fid = :id', 'params' => [':id' => $id], 'order' => 'id DESC']);
			echo json_encode(['file' => '<div style="border: 1px solid #ddd; padding: 10px; float: left; width: 200px; height: 200px; position: relative"><img src="' . Yii::app()->baseUrl . '/filerepo/' . $file->hash . '/' . $file->name . '" style="width: 100%; max-height: 100%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%)"></div>']);
		}
	}

	public function actionPrintLabel()
	{
		if (empty($_FILES)) {
			$this->render('label');
		} else {
			if (preg_match('/csv/i', $_FILES['print_label']['name'])) {
				$xls = new oExcel('CSV');
			} else {
				$xls = new oExcel;
			}
			$xls->load($_FILES['print_label']['tmp_name']);
			$data = $xls->getAll();

			$rs = [];
			unset($data[1]);
			foreach ($data as $line) {
				$rs[$line[1]] = [$line[1], $line[3], $line[2]];
			}

			oPDF::renderPDF('product_label', ['rs' => $rs], 2, 'label.pdf');

			echo json_encode(['done' => true, 'msg' => 'Successfully']);
			Yii::app()->end();
		}
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

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'wms-prod-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
