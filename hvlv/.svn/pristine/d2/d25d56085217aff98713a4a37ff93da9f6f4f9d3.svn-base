<?php

class LocoConsolController extends Controller{

	protected $nonAjax=array('export', 'download', 'ap_manifest', 'cp_manifest');

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
		$model=new LocoConsol;
		$model->pol = 'AUSYD';
		$model->pod = 'AUSYD';

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['LocoConsol'])){
			if(empty($_POST['recs'])){
				$model->addError('awb', 'Please select shipments');
			}else{
				$model->attributes=$_POST['LocoConsol'];
				$model->status = 10;
				$model->save();
				$ot=new Outturn;
				$ot->save();
				$ot_id = $ot->id;

				foreach($_POST['recs'] as $m){
					$man = Manifest::model()->findByPk($m);
					$man->status = 20;
					$man->save();
					$man->genInvoice();
					foreach($man->rec_shipments as $p){
						$p->consol_id = $model->id;
						$p->type = 30;
						$p->status = 65;
						$p->ot_id = $ot_id;
						$p->save();
					}
				}
			}
			$this->ajaxResult($model, array('id'));
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

		if(isset($_POST['LocoConsol'])){
			$model->attributes=$_POST['LocoConsol'];
			$model->save();
			$this->ajaxResult($model);
		}
		
		if(isset($_GET['tab'])){
			Acl::hasAccess($this->CaName.'/'.$_GET['tab'], true);
			$this->render('tab_'.$_GET['tab'], array('model'=>$model));
		}else{
			$this->render('update',array('model'=>$model));
		}
	}
	
	public function actionNotes($id){
		$model=$this->loadModel($id);
		if(!empty($_POST['notes'])){
			$log = Log::add($model, 6, array('notes' => $_POST['notes']));
			$this->ajaxResult($log);
		}
	}
	
	public function actionAp_manifest($id){
		$model=$this->loadModel($id);
		$csv = array();
		foreach($model->shipments as $p){
			if($p->zrate->orgrate->org_id != 101) continue;
			$addr = str_split(str_replace("\n",' ',$p->cnee->address), 40);
			while(sizeof($addr)<4){
				$addr[] = '';
			}
			$goods = $p->getGoods();
			$csv[] = 'C,"'.$p->hbn.'",,S1,,"'.$p->cnee->name.'",,"'.implode('","', $addr).'","'.$p->cnee->suburb.'",'.$p->cnee->state.','.$p->cnee->postcode.',AU,"'.$p->cnee->getTel().'",N,,,N,N,,N,0,"'.$p->hbn.'",Y,,N,,N,Sender,15-17 Byrnes Street,,,,Botany,NSW,2019,AU,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,';
			$csv[] = 'A,'.$p->weight.',,,,,"'.$p->goods.'",,N,0,,,,,,,,,,';
			$csv[] = 'G,,,"'.$goods.'",,,'.$p->itemsTotQty().','.$p->weight.','.$p->value.','.$p->dvalue;
		}
		header("Cache-Control: maxage=1");
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=ap_manifest_".date('YmdHis').".csv");
		echo implode("\n", $csv);
	}
	
	public function actionCp_manifest($id){
		$model=$this->loadModel($id);
		$csv = array();
		foreach($model->shipments as $p){
			if($p->zrate->orgrate->org_id != 102) continue;
			$pweight = $p->weight<1? 1 : $p->weight;
			$csv[] = 'C,,"'.$p->cnee->name.'","'.str_replace("\n",' ',$p->cnee->address).'",,,"'.$p->cnee->suburb.'",'.$p->cnee->postcode.',,"'.$p->cnee->getTel().'",,"'.$p->hbn.'",,,'.$p->zrate->getCode().',1,'.$pweight.','.(round($pweight/165*1000)/1000);
		}
		header("Cache-Control: maxage=1");
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=cp_manifest_".date('YmdHis').".csv");
		echo implode("\n", $csv);
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new LocoConsol('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['LocoConsol']))
			$model->attributes=$_GET['LocoConsol'];

		$this->render('list',array(
			'model'=>$model,
		));
	}
	
	public function actionExport($id){
		$model=$this->loadModel($id);
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, ['TYPE','CONNOTE NO.','WEIGHT','CNEE','TEL','ADDRESS','SUBURB','STATE','P/C','DESTINATION','','PCS','COMMODITY','INNER ITEMS','UNIT VALUE','TTL VALUE','CMETER','SHIPPER','SHIPPER ADD','SHIPPER STATE','SHIPPER PC','SHIPPER COUNTRY CODE','SHIPPER CONTACT']);
		foreach($model->shipments as $r){
			$xls->addRow($i++, array('parcel', $r->hbn, $r->weight, $r->cnee->name, $r->cnee->tel, $r->cnee->address, $r->cnee->suburb, $r->cnee->state, $r->cnee->postcode, $r->cnee->country, $r->cnee->country, $r->pkg, implode('/', $r->eitems['g']), implode('/', $r->eitems['q']), implode('/', $r->eitems['v']), $r->dvalue, $r->cbm, $r->cnor->name, $r->cnor->address, $r->cnor->state, $r->cnor->postcode, $r->cnor->country, $r->cnor->tel));
		}

		$xls->output('consol_manifest_'.$model->no.'.xlsx');
	}

	public function actionDownload($id){
		$model=$this->loadModel($id);

		$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.'-'.strtoupper($_GET['type']).(empty($_GET['joint'])? '' : '-joint').date('YmdHi').'.zip';
		$zip = new ZipArchive;
		$zip->open($zf, ZipArchive::CREATE);
		$copied = [];
		$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		mkdir($td);

		switch($_GET['type']){
			case 'pdf':
				$rs = $model->shipments;
				oPDF::renderPDF('label_A6', array('tpl' => '_label', 'empty' => false, 'rs' => $rs));
				AppHelper::unlinkRecursive($td);
				Yii::app()->end();
			break;
			case 'pod':
				$rs = $model->shipments;
				oPDF::renderPDF('pod_A4', array('rs' => $rs));
				AppHelper::unlinkRecursive($td);
				Yii::app()->end();
			break;
		}

		$zip->close();
		header("Cache-Control: maxage=1");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header('Content-Disposition: attachment; filename="'.basename($zf).'"');
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($zf));
		readfile($zf);
		unlink($zf);
		AppHelper::unlinkRecursive($td);
		Yii::app()->end();
	}
	
	public function actionEparcel($id){
		$model=$this->loadModel($id);
		$pods = array(
			'NSW' => 'AUSYD',
			'ACT' => 'AUSYD',
			'VIC' => 'AUMEL',
			'SA' => 'AUMEL',
			'TAS' => 'AUMEL',
			'WA' => 'AUPER',
			'QLD' => 'AUBNE',
			'NT' => 'AUBNE',
		);
		$pdata = array();
		foreach($model->shipments as $r){
			$addr = str_split(str_replace("\n",' ',$r->cnee->address), 40);
			while(sizeof($addr)<4){
				$addr[] = '';
			}
			
			$goods = $r->getGoods();
			$cbm = round($r->cbm*1000000)/1000000;
			$weight = round($r->weight*1000)/1000;
			$pod = empty($pods[$r->state])? 'AUSYD' : $pods[$r->state];
			$pdata[$pod][] = array(
				'C,'.$r->hbn.',,PL5,,"'.$r->cnee->name.'",,"'.implode('","', $addr).'","'.$r->cnee->suburb.'",'.$r->cnee->state.','.$r->cnee->postcode.',AU,'.preg_replace('/[^\d]+/','',$r->cnee->tel).',N,,,N,N,,N,0,,N,,N,,N,Sender,15-17 Byrnes Street,,,,Botany,NSW,2019,AU,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,',
				'A,'.$weight.',,,,,'.$goods.',,N,0,,,,,,,,,,',
				'G,,,"'.$goods.'",,,'.$r->itemsTotQty().','.$weight.','.$r->value.','.$r->dvalue
			);
		}
		
		$bd = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'eParcel_'.time().DIRECTORY_SEPARATOR;
		if(is_dir($bd)) unlinkRecursive($bd);
		mkdir($bd);
		$zip = new ZipArchive;
		$zf = $bd.'PCAE_eParcel'.date('Ymd').'.zip';
		$zip->open($zf, ZipArchive::CREATE);
		
		foreach($pdata as $pod => $rs){
			$csv = array();
			$i = 1;
			foreach($rs as $r){
				$csv = array_merge($csv, $r);
			}
			$mfn = 'eParcel_'.$model->awb.'_'.$pod.'.csv';
			file_put_contents($bd.$mfn, implode("\n", $csv));
			$zip->addFile($bd.$mfn, $mfn);
		}
		
		$zip->close();
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".basename($zf)."\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($zf));
		readfile($zf);
		AppHelper::unlinkRecursive($bd);
		unlink($zf);
		Yii::app()->end();
	}
	
	public function actionSac($id){
		$model=$this->loadModel($id);
		$xls = new oExcel;
		$mfn = 'PCAE'.date('Ymd').$model->pod;
		$i = 1;
		$xls->addRow($i++, array('fld_TM_manifest_id', 'fld_TM_cartons', 'fld_TM_cbm', 'fld_TM_weight', 'fld_TM_container_type', 'fld_TM_container_no', 'fld_TM_seal_no', 'fld_TM_vessel_name', 'fld_TM_edt', 'fld_TMD_connote_no', 'fld_TMD_order_no', 'fld_TMD_cartons', 'fld_TMD_cbm', 'fld_TMD_weight', 'fld_TO_filename', 'fld_TO_import_date', 'fld_TO_order_no', 'fld_TO_carrier', 'fld_TO_surname', 'fld_TO_address_1', 'fld_TO_address_2', 'fld_TO_country_code', 'fld_TO_suburb', 'fld_TO_state', 'fld_TO_postcode', 'fld_TO_telephone', 'fld_TO_delivery_instr', 'fld_TO_address_3', 'fld_TO_weight', 'fld_TOL_line_no', 'fld_TOL_product_no', 'fld_TOL_item_no', 'fld_TOL_quantity', 'fld_TOL_promised_date', 'fld_TOL_price', 'fld_TOL_total_amount', 'fld_edi_description', 'name', 'addr1', 'addr2', 'city', 'state', 'postcode', 'country'));
		$xls->setFont('A1:H1', array('bold' => true));
		foreach($model->shipments as $r){
			$addr = $r->cnee->address;
			$addr2 = '';
			if(preg_match('/P\.*O\.*\s+BOX/', $r->cnee->address)){
				$addr = '';
				$addr2 = $r->cnee->address;
			}
			
			preg_match('/(.+)(?:[,\s]+hs[:\s]+(\d+))/i',$r->goods,$gd);
			$goods = empty($gd[1])? $r->goods : $gd[1];
			$cbm = round($r->cbm*1000000)/1000000;
			$weight = round($r->weight*1000)/1000;
			$service = $weight > 2? 'AeParcelPar' : 'AeParcelPac';
			$etd = date('d/m/Y', strtotime($model->eta.' +5 days'));
			$xls->addRow($i++, array($r->hbn, 1, $cbm, $weight, 'AIR', 'PCAE', 'AUD', $model->airline, date('d/m/Y', strtotime($model->eta)), 'CONNOTE', 'ORDER_NO', 1, $cbm, $weight, $mfn, date('d/m/Y', strtotime($model->eta)), $r->hbn, 1, $r->cnee->name, $addr, $addr2, 'AUS', $r->cnee->suburb, $r->state, $r->postcode, preg_replace('/[^\d]+/','',$r->cnee->tel), $service, '', $weight, 1, '', '', $r->pkg, $etd, $r->value, $r->dvalue, $goods, $r->cnor->name, $r->cnor->address, '', $r->cnor->suburb, $r->cnor->state, $r->cnor->postcode, substr($model->pol, 2)));
		}
		$xls->output($mfn.'.xls', 'Excel5');
	}
	
	public function actionMsac($id){
		$model=$this->loadModel($id);
		$pods = array(
			'NSW' => 'AUSYD',
			'ACT' => 'AUSYD',
			'VIC' => 'AUMEL',
			'SA' => 'AUMEL',
			'TAS' => 'AUMEL',
			'WA' => 'AUPER',
			'QLD' => 'AUBNE',
			'NT' => 'AUBNE',
		);
		$pdata = array();
		foreach($model->shipments as $r){
			$addr = $r->cnee->address;
			$addr2 = '';
			if(preg_match('/P\.*O\.*\s+BOX/', $r->cnee->address)){
				$addr = '';
				$addr2 = $r->cnee->address;
			}
			
			preg_match('/(.+)(?:[,\s]+hs[:\s]+(\d+))/i',$r->goods,$gd);
			$goods = empty($gd[1])? $r->goods : $gd[1];
			$cbm = round($r->cbm*1000000)/1000000;
			$weight = round($r->weight*1000)/1000;
			$service = $weight > 2? 'AeParcelPar' : 'AeParcelPac';
			$etd = date('d/m/Y', strtotime($model->eta.' +5 days'));
			$pod = empty($pods[$r->state])? 'AUSYD' : $pods[$r->state];
			$pdata[$pod][] = array($r->hbn, 1, $cbm, $weight, 'AIR', 'PCAE', 'AUD', $model->airline, date('d/m/Y', strtotime($model->eta)), 'CONNOTE', 'ORDER_NO', 1, $cbm, $weight, $pod, date('d/m/Y', strtotime($model->eta)), $r->hbn, 1, $r->cnee->name, $addr, $addr2, 'AUS', $r->cnee->suburb, $r->state, $r->postcode, preg_replace('/[^\d]+/','',$r->cnee->tel), $service, '', $weight, 1, '', '', $r->pkg, $etd, $r->value, $r->dvalue, $goods, $r->cnor->name, $r->cnor->address, '', $r->cnor->suburb, $r->cnor->state, $r->cnor->postcode, substr($model->pol, 2));
		}
		
		$bd = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'SACM'.time().DIRECTORY_SEPARATOR;
		if(is_dir($bd)) AppHelper::unlinkRecursive($bd);
		mkdir($bd);
		$zip = new ZipArchive;
		$zf = $bd.'PCAE_SACM'.date('Ymd').'.zip';
		$zip->open($zf, ZipArchive::CREATE);
		
		foreach($pdata as $pod => $rs){
			$xls = new oExcel;
			$mfn = 'PCAE'.date('Ymd').$pod;
			$i = 1;
			$xls->addRow($i++, array('fld_TM_manifest_id', 'fld_TM_cartons', 'fld_TM_cbm', 'fld_TM_weight', 'fld_TM_container_type', 'fld_TM_container_no', 'fld_TM_seal_no', 'fld_TM_vessel_name', 'fld_TM_edt', 'fld_TMD_connote_no', 'fld_TMD_order_no', 'fld_TMD_cartons', 'fld_TMD_cbm', 'fld_TMD_weight', 'fld_TO_filename', 'fld_TO_import_date', 'fld_TO_order_no', 'fld_TO_carrier', 'fld_TO_surname', 'fld_TO_address_1', 'fld_TO_address_2', 'fld_TO_country_code', 'fld_TO_suburb', 'fld_TO_state', 'fld_TO_postcode', 'fld_TO_telephone', 'fld_TO_delivery_instr', 'fld_TO_address_3', 'fld_TO_weight', 'fld_TOL_line_no', 'fld_TOL_product_no', 'fld_TOL_item_no', 'fld_TOL_quantity', 'fld_TOL_promised_date', 'fld_TOL_price', 'fld_TOL_total_amount', 'fld_edi_description', 'name', 'addr1', 'addr2', 'city', 'state', 'postcode', 'country'));
			$xls->setFont('A1:H1', array('bold' => true));
			foreach($rs as $r){
				$r[14] = $mfn;
				$xls->addRow($i++, $r);
			}
			$xls->output($bd.$mfn.'.xls', 'Excel5', false);
			$zip->addFile($bd.$mfn.'.xls', $mfn.'.xls');
		}
		
		$zip->close();
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"".basename($zf)."\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($zf));
		readfile($zf);
		unlinkRecursive($bd);
		unlink($zf);
		Yii::app()->end();
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=LocoConsol::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='consol-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
