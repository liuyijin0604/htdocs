<?php

class CnlOrderController extends Controller
{
	protected $nonAjax = ['report'];
	protected $skipAcl = [];

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate(){
		$model=new CnlOrder;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['CnlOrder']))
		{
			$model->attributes=$_POST['CnlOrder'];
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id){
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['meta'])){
			foreach($_POST['meta'] as $k => $v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						$model->mdata[$k][$k2] = trim($v2);
					}
				}else{
					$model->mdata[$k] = trim($v);
				}
			}
			if(!empty($_POST['confirm'])){
				if(empty($model->mdata['msg'][131])){
				 	CnlMsg::createMsg($model, 'GFP_INTL_LINEHAUL_BOOKING_CALLBACK');
				 	$model->mdata['msg'][131] = 1;
				 	$model->custom_log_note = CnlMsg::$type_names[131];
					if($model->status < 20) $model->status = 20;
				}else{
				 	CnlMsg::createMsg($model, 'GFP_INTL_LINEHAUL_SHIPMENT_UPDATE_CALLBACK');
				 	$model->custom_log_note = CnlMsg::$type_names[133];
				}
			}
			$model->save();
			$this->ajaxResult($model);
		}

		if(isset($_GET['tab'])){
			Acl::hasAccess($this->CaName.'/'.$_GET['tab'], true);
			$this->render('tab_' . $_GET['tab'], array('model' => $model));
		}else{
			$this->render('update',array('model'=>$model));
		}
	}

	public function actionSendMsg($id){
		$model = $this->loadModel($id);
		if(!empty($_GET['meta'])){
			foreach($_GET['meta'] as $k => $v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						$model->mdata[$k][$k2] = trim($v2);
					}
				}else{
					$model->mdata[$k] = trim($v);
				}
			}
			if(CnlMsg::createMsg($model, CnlMsg::$types[$_GET['t']])){
				$model->mdata['msg'][$_GET['t']] = 1;
				$model->custom_log_note = CnlMsg::$type_names[$_GET['t']];
				$model->save();
				$this->ajaxResult($model);
			}
		}
	}

	public function actionNotes($id){
		$model=$this->loadModel($id);
		if(!empty($_POST['notes'])){
			$log = Log::add($model, 6, array('notes' => $_POST['notes']));
			$this->ajaxResult($log);
		}
	}

	public function actionUpFiles($id){
		$rs = FileRepo::model()->findAll('type = 110 AND fid = :id AND status = 20', [':id' => $id]);
		foreach($rs as $r){
  			$od = new CnlDoc;
  			$od->order_id = $id;
  			$od->file_id = $r->id;
  			$od->type = 0;
  			$od->status = 20;
  			if($od->save()){
  				$r->status = 30;
  				$r->save();
  			}
		}
		echo 'Done';
	}

	public function actionFileUpdate($id){
		$model = CnlDoc::model()->findByPk($id);
		if(!empty($_POST)){
			$model->attributes=$_POST['CnlDoc'];
			$model->save();
			$this->ajaxResult($model);
		}
		$this->render('file_update', ['model' => $model]);
	}

	public function actionCargoUpdate($id){
		$model=CnlCargo::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		$model->scenario = 'update';

		if(isset($_POST['cargo'])){
			$model->attributes = $_POST['cargo'];
			if($model->save()){
				if(!empty($_POST['send'])){
					if(empty($model->order->mdata['msg'][134]) && CnlMsg::createMsg($model->order, 'GFP_INTL_LINEHAUL_CARGO_RECEIPT_CALLBACK')){
						$model->order->mdata['msg'][134] = 1;
						$model->order->custom_log_note = CnlMsg::$type_names[134];
						$model->order->save();
					}else{
						CnlMsg::createMsg($model->order, 'GFP_INTL_LINEHAUL_CARGO_UPDATE_CALLBACK');
						$model->order->addLog([CnlMsg::$type_names[135]]);
					}
				}
			}
			$this->ajaxResult($model);
		}
	}

	public function actionCargoGrid($id){
		$model=CnlCargo::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		$model->scenario = 'update';
		foreach(['CnlCargoContainer' => 'containers', 'CnlCargoPackage' => 'packages'] as $k => $a){
			if(!isset($_POST[$k])) continue;
			foreach($_POST[$k] as $pk => $pv){
				if($pv == '') $model->addError($k, $pk.' cannot be empty');
			}
			if(!isset($_POST[$k]['id'])){
				$model->{$a}[] = $_POST[$k];
			}else{
				foreach($model->{$a} as $id=>$r){
					if($id == $_POST[$k]['id']){
						foreach($_POST[$k] as $pk => $pv){
							$model->{$a}[$id][$pk] = $pv;
						}
					}
				}
			}
		}
		if(!$model->hasErrors()) $model->save();
		$this->ajaxResult($model);
	}

	public function actionReport(){
		if(!empty($_POST)){
			$xls = new oExcel;
			$sheet = $xls->getActiveSheet();
			// $sheet->setTitle('Stock');
			$i = 1;

			$rs = CnlOrder::model()->findAll("status < 100 AND (json_value(meta, '$.bk.ata') >= :fdate OR json_value(meta, '$.bk.eta') >= :fdate) AND (json_value(meta, '$.bk.ata') <= :tdate OR json_value(meta, '$.bk.eta') <= :tdate)", [':fdate' => $_POST['fromdate'], ':tdate' => $_POST['todate']]);
			switch ($_GET['type']) {
				case 'inv':
				$xls->setColWidth([2, 2, 2, 15, 25, 15, 15, 15, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10]);
				$xls->addRow($i++, ['','','', 'CP端订单号', '商家昵称', '头程单号', 'CP端订单号', 'JOB #', '下单时间', 'POD', 'POD', 'MODE ', 'ETD', 'ETA', 'ATA', 'WEIGHT ', 'DIMS', 'CONTAINER TYPE', '20GP / 20DC', '40GP', '40HQ', '20RF', '40RF', 'FCL/LCL', '20GP FEE', '40GP FEE', '40HQ FEE', '20RF FEE', '40RF FEE', 'LCL RATE', '20GP FEE', '40GP FEE', '40HQ FEE', '20RF FEE', '40RF FEE', 'LCL RATE', '起运港MIN', '起运港Per Kg', '国际干线Min', '国际干线Per Kg', '提货费', '出口报关费', '起运地单证费', '空运起运港港口费', '国际干线费用', 'CFS操作费', '食品柜', '换板', '拆板', '熏蒸板费', '保险费', '海关X光检验费', '服务费用', '香港政府消费税', '香港杂费', '其他增值费汇总', '金额', '币种', 'CHK']);
				foreach($rs as $r){
					$c = $r->bCargos[0];
					$ctnr = $c->containerCount();
					$xls->addRow($i++, ['','','', '="'.$r->mdata['bk']['bln'].'"', $r->sellerName, $r->orderCode, '="'.$r->mdata['bk']['bln'].'"', '="'.$r->mdata['bk']['hbn'].'"', substr($r->gmtModified, 0, 10), $r->pol, $r->pod, $r->getTransportMode(), empty($r->mdata['bk']['atd'])? $r->mdata['bk']['etd'] : $r->mdata['bk']['atd'], $r->mdata['bk']['eta'], $r->mdata['bk']['ata'], $c->totalGrossWeight, $c->totalVolume, $c->containerCount(true), empty($ctnr['20GP'])? '' : $ctnr['20GP'], empty($ctnr['40GP'])? '' : $ctnr['40GP'], empty($ctnr['40HQ'])? '' : $ctnr['40HQ'], empty($ctnr['20RF'])? '' : $ctnr['20RF'], empty($ctnr['40RF'])? '' : $ctnr['40RF'], $r->getContainerLoad()]);
				}
				$xls->output('Cainiao_Invoice_Report_' . time() . '.xlsx');
				break;
				case 'sfr':
				$xls->setColWidth([20, 20, 15, 15, 15, 20, 10, 10, 10, 10, 10, 25, 10, 10]);
				$xls->addRow($i++, ['业务编号', 'CP端订单号', 'JOB #', 'MB/L No.', 'HB/L No.', '船名航次/航班号', '起运港', 'ETD', 'ATA', '目的地', '毛重', '尺码', '计费重', 'CNY金额']);
				foreach($rs as $r){
					if($r->transportMode != 10) continue;
					$c = $r->bCargos[0];
					$ctnr = $c->containerCount();
					$ctnr_list = [];
					foreach($ctnr as $t => $q){
						$ctnr_list[] = $t.'x'.$q;
					}
					$xls->addRow($i++, [$r->orderCode, $r->focOrderCode, $r->refCode, '="'.$r->mdata['bk']['bln'].'"', '="'.$r->mdata['bk']['hbn'].'"', '="'.$r->mdata['bk']['vsl'].' '.$r->mdata['bk']['voy'].'"', $r->pol, empty($r->mdata['bk']['atd'])? $r->mdata['bk']['etd'] : $r->mdata['bk']['atd'], $r->mdata['bk']['ata'], $r->pod, $c->totalGrossWeight, implode(', ', $ctnr_list), $c->chargeWeight]);
				}
				$xls->output('Cainiao_Sea_Freight_Report_' . time() . '.xlsx');
				break;
			}
		}
		$this->render('report');
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id){
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new CnlOrder('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['CnlOrder']))
			$model->attributes=$_GET['CnlOrder'];

		$this->render('list',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=CnlOrder::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='cnl-order-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
