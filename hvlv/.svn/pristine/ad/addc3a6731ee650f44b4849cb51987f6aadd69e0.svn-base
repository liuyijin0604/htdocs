<?php

class ManifestController extends Controller
{
	public function actionManage($id)
	{
		$model = new ImParcel('search');
		$model->unsetAttributes();
		$model->dbCriteria->order = 't.id DESC';
		$model->setAttributes([
			'man_id' => $id
		]);
		$model->ot_id = null;
		if (isset($_GET['ImParcel'])) {
			$model->attributes = $_GET['ImParcel'];
		}

		if (isset(Yii::app()->user->org) && Yii::app()->user->org > 1) {
			$model->agent_id = Yii::app()->user->org;
		}

		$manifest = Manifest::model()->findByPk($id);
		$isWDT = false;
		if(!empty($manifest->man_shipments[0]->consol_id)&&$manifest->man_shipments[0]->consol->isWDT())
		{
			$isWDT = true;
		}
		$this->render('manage', ['model' => $model, 'type' => 'im','manifest' => $manifest,'isWDT'=>$isWDT]);
	}

	public function actionUpdatew()
	{
		if (!empty($_FILES)) {
			$this->updateWeights();
		}
	}

	private function updateWeights()
	{
		$resp = new stdClass;
		$resp->done = true;
		$resp->msg = '';
		$file = empty($_FILES['manifest_weight'])? [] : $_FILES['manifest_weight'];
		if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
			$resp->done = false;
			$resp->msg = 'Manifest Weight File Required.';
		} else {
			$xls = new oExcel;
			$xls->supported($file['name']);
			$xls->load($file['tmp_name']);
			$data = $xls->getAll();
			unset($xls);
			unset($data[1]);
			foreach ($data as $l => $r) {
				if (empty($r[1])) {
					break;
				}
				$p = ImParcel::model()->find('status = 10 AND hbn = :hbn', [':hbn' => $r[1]]);
				if (!empty($p)) {
					$p->weight = $r[3];
					$p->save();
				}
			}
		}
		echo json_encode($resp);
		Yii::app()->end();
	}

	/**
	 * export all shipments of the manifest , only including HBN and weight
	 * so that client can modify shipments weight in bulk by upload the whole of the manifest
	 */
	public function actionExport($mid)
	{
		$shipments = ImParcel::model()->findAll('man_id = :mid', [':mid' => $mid]);
		if (empty($shipments)) {
			return;
		}
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['运单号', '参考号', '重量']);
		foreach ($shipments as $r) {
			$rowData = ['="' . $r->hbn . '"',
				empty($r->cref) ? '' : '="' . $r->cref . '"',
				'="' . $r->weight . '"'];

			$xls->addRow($i++, $rowData);
		}
		$xls->output('manifest_export_' . time() . '.xlsx');
	}

	public function actionAjaxRemoveShipment()
	{
		$resp = ['success' => 0 , 'msg' => 'Unknown Error'];
		if(!empty($_POST['id']))
		{
			$shipment = ImParcel::model()->findByPk($_POST['id']);
		}else
		{
			$hbn = strtoupper(trim($_POST['hbn']));
			$hbn = strtoupper($hbn);

			$mid = $_POST['mid'];
			$shipment = ImParcel::model()->find('hbn = :hbn and status!=100', [':hbn' => $hbn]);
		}
		if (!empty($shipment)&&$shipment->canBeDelete()) {
			if(!empty($_POST['id']))
			{
				$shipment->status = 100 ; // set as cancel status
				if(empty($shipment->mdata['was_man_id']))
				{
					$shipment->mdata['was_man_id'] = $shipment->man_id;
				}
				$shipment->man_id = 0;  // no manifest now
				$shipment->save();
				$resp['success'] = 1;
				$resp['url'] = "";
			}else
			{
				$shipment->status = 100 ; // set as cancel status
				if(empty($shipment->mdata['was_man_id']))
				{
					$shipment->mdata['was_man_id'] = $shipment->man_id;
				}
				$shipment->man_id = 0;  // no manifest now
				$shipment->save();
				$resp['success'] = 1;
				$resp['url'] = $this->createUrl('manifest/manage', ['id' => $mid]);
			}
		} else {
			$resp['msg'] = 'Shipment : ' . $hbn . ' Not Found';
		}
		echo json_encode($resp);
	}

	public function actionAjaxAddShipment()
	{
		$resp = ['success' => 0, 'msg' => 'Unknown Error'];
		$hbn = strtoupper(trim($_POST['hbn']));
		$mid = $_POST['mid'];

		$shipment = ImParcel::model()->find('hbn = :hbn', [':hbn' => $hbn]);
		if ($shipment) {
			if ($mid > 0) {
				if ($shipment->man_id == $mid) {
					$resp['msg'] = 'Shipment : ' . $hbn . ' is in the manifest already';
				} else {

					// only when shipment is cancelled or there is no any manifest
					// we can add to another manifest
					// 35 - export clear status can't be changed again
					if ($shipment->status == 100 || ($shipment->man_id == 0 && $shipment->status < 35)) {
						$shipment->man_id = $mid;
						$shipment->status = 10; // set as new shipment
						$shipment->save();
						$resp['success'] = 1;
						$resp['url'] = $this->createUrl('manifest/manage', ['id' => $mid]);
					} else {
						// in case shipment is in another manifest already
						// notice client if they want to remove it from another and add in
						if ($shipment->status < 35 && $shipment->man_id > 0) {
							$resp['msg'] = 'Shipment : ' . $hbn . ' is in Manifest (' . $shipment->man_id .'), please remove it firstly';
						} else {
							$resp['msg'] = 'Shipment : ' . $hbn . " can't be changed now";
						}
					}
				}
			} else {
				$resp['msg'] = 'Invalid Manifest ID';
			}
		} else {
			$resp['msg'] = 'Shipment : ' . $hbn . ' Not Found';
		}

		echo json_encode($resp);
	}
	
	public function actionList()
	{
		$model=new Manifest('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Manifest'])) {
			$model->attributes=$_GET['Manifest'];
		}
		$model->type = 30;
		$model->fwd_id=Yii::app()->user->org;
		$this->render('list', [
			'model'=>$model,
		]);
	}
	public function actionCreateShips()
	{
		return false;
		$_GET['tabid']=uniqid();
		$model = new Manifest;
		$model->type = 30;
		$errors=[];
		if (isset($_POST['hbns'])) {
			if (empty($_POST['hbns'])) {
				$model->addError('awb', 'Please select shipments');
			} else {
				foreach (preg_split('/[\s,;]+/', trim($_POST['hbns'])) as $h) {
					if (empty($h)) {
						continue;
					}
					$criteria=new CDbCriteria;
					$criteria->addCondition('hbn =:h OR ref=:h');
					$criteria->addInCondition('agent_id', User::getOrgIds());
					$criteria->params+=[':h'=>$h];
					//                                      var_dump($criteria);return;
					$p = ImParcel::model()->find($criteria);
					if (empty($p)) {
						$errors[]=$h.' shipment not exists';
					} else {
						if ($p->status>=30) {
							$errors[]=$h.' shipment status need to be new for creating Consol';
						}
						if ($p->consol_id>0) {
							$errors[]=$h.' shipment already included in a consol';
						}
					}
				}
				if (!empty($errors)) {
					$model->addError('shipment', implode(';', $errors));
					$this->ajaxResult($model);
				}
				// $model->attributes=$_POST['Manifest'];
				$model->status = 10;
				$model->fwd_id=Yii::app()->user->org;
				$model->dpt_id = 106;
				$model->ref='d2z-'.date('Ymd');
				$model->save();
				$transaction=Yii::app()->db->beginTransaction();
				try {
					foreach (preg_split('/[\s,;]+/', trim($_POST['hbns'])) as $h) {
						if (empty($h)) {
							continue;
						}
						$criteria=new CDbCriteria;
						$criteria->addCondition('hbn =:h OR ref=:h');
						$criteria->addInCondition('agent_id', User::getOrgIds());
						$criteria->params+=[':h'=>$h];
						$p = ImParcel::model()->find($criteria);
						if (!empty($p)) {
							// $p->consol_id = $model->id;
							// $p->cbwf= $p->cbwf| ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN;
							// $p->status = ImParcel::STATE_LOCAL_ARRIVAL;
							// $p->save();
							$model->map($p);
						}
					}
					$transaction->commit();
				} catch (Exception $ex) {
					$transaction->rollback();
					throw $ex;
				}
			}
			$this->ajaxResult($model, ['id']);
		}
		$this->render('create_ships', ['model'=>$model]);
	}
	public function actionUpdate($id)
	{
		$model= $this->loadManifest($id);
		if (!empty($_POST['Manifest'])) {
			$model->attributes=$_POST['Manifest'];
			$model->save();
			$this->ajaxResult($model);
		}
		$this->render('update', ['model'=>$model]);
	}

	public function actionD2zCountryManifest($id)
	{
		if (!(Yii::app()->user->org==1427||Yii::app()->user->grp<=0)) {
			Acl::denied403();
		}
		$cn = $this->loadManifest($id);
		$err=[];
		if (empty($cn)) {
			return;
		}
		$ss=[];
		foreach ($cn->lines as $l) {
			$s = $l->mm();
			if (preg_match('/^33A8Y\d{7}/', $s->ref) && empty($s->trans)) { // only for startrack shipment and Not sent yet
				// if ($s->status == ImParcel::STATE_LOCAL_ARRIVAL) {
					$ss[] = $s;
				// }
			}
		}
		if (!empty($ss)) {
			if (D2zCountryRate::manifest($ss, $cn->ref)) {
				// $transaction=Yii::app()->db->beginTransaction();
				// try {
				// 	foreach ($ss as $s) {
				// 		$s->cbwf=$s->cbwf | ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN;
				// 		$s->status=70;
				// 		$s->update(['status','cbwf']);
				// 	}
				// 	$transaction->commit();
				// } catch (Exception $ex) {
				// 	$transaction->rollback();
				// 	throw  $ex;
				// }
				$cn->mdata['d2zcountrysent']=1;
				$cn->update();
				echo 'done';
			} else {
				echo 'failed!';
			}
		} else {
			echo 'no shipments to Manifest';
		}
	}

	public function actionEtowerManifest($id)
	{
		if (!(Yii::app()->user->org==1427||Yii::app()->user->grp<=0)) {
			Acl::denied403();
		}
		$cn = $this->loadManifest($id);
		$err=[];
		if (empty($cn)) {
			return;
		}
		$ss=[];
		$tempShipments=[];
		foreach ($cn->lines as $l) {
			$s = $l->mm();
			if (isset($s->mdata['d2z_shipment_orderid'])&& (strlen($s->ref)>7) && !empty($s->trans) && count($s->trans) == 1) { // only for startrack shipment and Not sent yet
				// if ($s->status == ImParcel::STATE_LOCAL_ARRIVAL) {
					if (!isset($s->trans[0]->mdata['oid'])) { // if upload manifest before, we don't send again
						$ss[] = $s;
						$tempShipments[$s->ref]=$s;
					}
				// }
			}
		}
		if (!empty($ss)) {
			$d2z=new D2zAPI(false, false);  //need to fix on live
			$result=$d2z->manifest($ss);
			if (is_array($result)) {
				$transaction = Yii::app()->db->beginTransaction();
				try {
					foreach ($result as $shipmentRef) {
						$ref= substr($shipmentRef, 0, -11);
						$shipment=$tempShipments[$ref];
						// $shipment->status=70;
						// $shipment->cbwf=$shipment->cbwf| ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN;
						// $shipment->update(['status','cbwf']);
						$tranship=$shipment->trans[0];
						$tranship->mdata['oid'] = $ref;
						$tranship->cost= number_format($d2z->getD2zCost($shipment), 2, '.', '');
						$tranship->save();
					}
					$transaction->commit();
				} catch (Exception $ex) {
					$transaction->rollback();
					Log::log2file("D2Z".$cn->no."=>".$ex->getMessage(), "transaction_err_log", "transaction");
					throw $ex;
				}
			} elseif (is_object($result)) {
				if ($result->status=="Failed") {
					$err[]=$result->message;
				}
			}
		} else {
			echo 'no shipments to manifest!';
			return;
		}
		if (empty($err)) {
			$cn->mdata['d2zsent']=1;
			$cn->update();
			echo 'done';
		} else {
			echo implode('<br />', $err);
		}
	}

	public function actionEtowerUbiManifest($id)
	{
		if (!(Yii::app()->user->org==1427||Yii::app()->user->grp<=0)) {
			Acl::denied403();
		}
		$cn = $this->loadManifest($id);
		$err=[];
		if (empty($cn)) {
			return;
		}
		$ss=[];
		$tempShipments=[];
		foreach ($cn->lines as $l) {
			$s = $l->mm();
			if (isset($s->mdata['ubi_shipment_orderid'])&& (strlen($s->ref)>7) && !empty($s->trans) && count($s->trans) == 1) { // only for startrack shipment and Not sent yet
				// if ($s->status == ImParcel::STATE_LOCAL_ARRIVAL) {
					if (!isset($s->trans[0]->mdata['oid'])) { // if upload manifest before, we don't send again
						$ss[] = $s;
						$tempShipments[$s->ref]=$s;
					}
				// }
			}
		}
		if (!empty($ss)) {
			$ubi=new UbiAPI(false, false);  //need to fix on live
			$result=$ubi->manifest($ss);
			if (is_array($result)) {
				$transaction = Yii::app()->db->beginTransaction();
				try {
					foreach ($result as $shipmentRef) {
						$ref= substr($shipmentRef, 0, -11);
						$shipment=$tempShipments[$ref];
						// $shipment->status=70;
						// $shipment->cbwf=$shipment->cbwf| ImParcel::CBWF_D2Z_CONSOL_NOT_FOR_SCAN;
						// $shipment->update(['status','cbwf']);
						$tranship=$shipment->trans[0];
						$tranship->mdata['oid'] = $ref;
						$tranship->cost= number_format($ubi->getCost($shipment), 2, '.', '');
						$tranship->save();
					}
					$transaction->commit();
				} catch (Exception $ex) {
					$transaction->rollback();
					Log::log2file("UBI".$cn->no."=>".$ex->getMessage(), "transaction_err_log", "transaction");
					throw $ex;
				}
			} elseif (is_object($result)) {
				if ($result->status=="Failed") {
					$err[]=$result->message;
				}
			}
		} else {
			echo 'no shipments to manifest!';
			return;
		}
		if (empty($err)) {
			$cn->mdata['ubisent']=1;
			$cn->update();
			echo 'done';
		} else {
			echo implode('<br />', $err);
		}
	}	
	 
	public function loadManifest($id)
	{
		$model = Manifest::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}
}
