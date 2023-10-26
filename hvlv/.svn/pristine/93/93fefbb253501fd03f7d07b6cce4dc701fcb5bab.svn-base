<?php

class ZoneMapController extends Controller
{
	protected $nonAjax = ['download','downloadTransformedZonemap'];
	protected $skipAcl = [];

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{
		$model=new ZoneMap('search');
		$model->org_id = $_GET['org_id'];
		$model->zone_id = $_GET['zone_id'];
		if (!empty($_FILES['zonemap'])) {
			$data = oExcel::getAllData($_FILES['zonemap']['tmp_name'], $_FILES['zonemap']['name'], true);
			$exd = [];
			$err = [];
			unset($data[0]);
			foreach ($data as $l => $r) {
				$r[4] = intval($r[4]);
				$r[5] = intval($r[5]);
				if (empty($r[4])) {
					$err[] = 'Line '.$l.': Postcode is not numeric';
					continue;
				}
				if (empty($r[5])) {
					$r[5] = $r[4];
				}
				if ($r[5] < $r[4]) {
					$err[] = 'Line '.$l.': Postcode to is lower than from';
					continue;
				}
				for ($i = $r[4]; $i <= $r[5]; $i++) {
					if (isset($exd[$i]) && ($exd[$i][0] != $r[1] || $exd[$i][1] != $r[2])) {
						$err[] = 'Line '.$l.': Postcode '.$i.' exists in multiple zones';
					} else {
						$exd[$i] = [$r[1], $r[2], $r[3]];
					}
				}
			}

			if (empty($err)) {
				ksort($exd);
				$trans = Yii::app()->db->beginTransaction();
				try {
					Yii::app()->db->createCommand('DELETE FROM zone_map WHERE org_id = :oid AND zone_id = :zid')->bindValues([':oid'=> $model->org_id, ':zid' => $model->zone_id])->execute();
					$pk = 0;
					$c = 0;
					foreach ($exd as $k => $r) {
						if ($k == $pk + 1) {
							$t[4] = $k;
						} else {
							if ($pk > 0) {
								$z = new ZoneMap;
								$z->org_id = $model->org_id;
								$z->zone_id = $model->zone_id;
								$z->z1 = $t[0];
								$z->z2 = $t[1];
								$z->zone_name = $t[2];
								$z->pc_lo = $t[3];
								$z->pc_hi = $t[4];
								$z->save();
								$c++;
							}
							$t = [$r[0], $r[1], $r[2], $k, $k];
						}
						$pk = $k;
					}
					$trans->commit();
				} catch (Exception $ex) {
					$trans->rollback();
					throw $ex;
				}
				$this->ajaxResult($model, [], $c.' records updated');
			} else {
				foreach ($err as $e) {
					$model->addError('id', $e);
				}
				$this->ajaxResult($model);
			}
		}
		$this->render('update', [
			'model'=>$model,
		]);
	}

	/**
	 * Lists and search.
	 */
	public function actionList()
	{
		$model=new ZoneMap('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['ZoneMap'])) {
			$model->attributes=$_GET['ZoneMap'];
		}
		if(!empty($_GET['oid'])){
			$model->org_id = $_GET['oid'];
		}
		if(!empty($_GET['zid'])){
			$model->zone_id = $_GET['zid'];
		}
		if(!empty($_GET['z1'])){
			$model->z1 = $_GET['z1'];
		}

		$this->render('list', [
			'model'=>$model,
		]);
	}

	public function actionDownload()
	{
		$model=new ZoneMap('search');
		$model->org_id = $_GET['org_id'];
		$model->zone_id = $_GET['zone_id'];
		$dp = $model->search(false);

		$xls = new oExcel;
		$xls->setColWidth([10,10,15,10,10]);
		$i = 1;
		$xls->addRow($i++, ['Zone 1','Zone 2','Zone Name','Postcode From','Postcode To']);
		$zones = [];
		foreach ($dp->data as $r) {
			$xls->addRow($i++, [$r->z1, $r->z2, $r->zone_name, $r->pc_lo, $r->pc_hi]);
			if(!isset($zones[$r->z1])){
				$zones[$r->z1] = [$r->zone_name, []];
			}
			$zones[$r->z1][1][] = $r->pc_lo == $r->pc_hi? $r->pc_lo : $r->pc_lo.'-'.$r->pc_hi;
		}
		//pallet sheet
		$xls->createSheet('Zones');
		$xls->goSheet(1);
		$i = 1;
		$xls->setColWidth([10,20,200]);
		$xls->addRow($i++, ['Zone','Zone Name','Postcodes List']);
		foreach($zones as $z => $d){
			$xls->addRow($i++, [$z, $d[0], implode(',', $d[1])]);
		}

		$xls->output($model->org->name.':'.$model->zone_id.'_zonemap.xlsx');
	}
	public function actionTransformingTemplate()
	{
		$this->render('transform_template');
	}


	public function actionAjaxTransformingTemplate()
	{
		$type = $_POST['type'];
		$t_type = $_POST['t_type'];

		$resp = ['success' => 1,'msg' => 'import successfully'];
		$template_file = empty($_FILES['template_file']) ? [] : $_FILES['template_file'];
		if (empty($template_file['tmp_name']) || !is_uploaded_file($template_file['tmp_name'])) {
			$resp['msg'] = 'Invalid template file';
			echo json_encode($resp);
			return;
		} else {
			$xls = new oExcel;
			$xls->load($template_file['tmp_name']);
			$data = $xls->getAll();
			$msg = "";
			$zoneMapService = new ZoneMapService();
			if($type==1&&$t_type==1)
			{
				if($zoneMapService->transformingNewFastwayTemplate($data))
				{
					$dlUrl =  $this->createUrl('zoneMap/downloadTransformedZonemap');
					$msg = '<span>File is ready , Please <a href="'.$dlUrl.'" target="_blank">click me</a> to download now<span>';
				}
			}
			$resp['msg'] = $msg;
		}
		echo json_encode($resp);
	}

	public function actionDownloadTransformedZonemap()
	{
		$xls = new oExcel;
		$mfn = 'transformed_zoneMap';
		$tempfile = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$mfn.'.xlsx';
		$xls->load($tempfile);
		$xls->output($mfn.'.xlsx');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=ZoneMap::model()->findByPk($id);
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
		if (isset($_POST['ajax']) && $_POST['ajax']==='zone-map-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
