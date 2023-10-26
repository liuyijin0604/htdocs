<?php

class ConsolController extends Controller{

	public $nonAjax=array('export','invoice','invxls', 'trifree');
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id){
		$this->render('update',array(
			'model'=>$this->loadModel($id),
		));
	}

    public function actionManifest(){
        $model=new ImcoConsol;
        if(isset($_POST['manifest'])){
            $this->ajaxResult($model, array('id'));
        }

        // we should list all received and paid manifest and related shipments
        // waiting for operator to confirm , if every thing is ok
        // then create console now

        $this->render('manifest',array(
            'model'=>$model,
        ));
    }

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionNew(){
		$model=new ImcoConsol;
        if(isset($_POST['ImcoConsol'])){
			if( !empty($_POST['recs'])) {

                // check to see if there are some paid shipments waiting for console
                // if existing, create console for them now
                $model->attributes = $_POST['ImcoConsol'];
                $model->status = 10; // means new console
                $model->save();

                // once console create successfully
                // set all selected  manifest's shipments' console id
                // and set as manifested ( status = 30)
                $allManiIds = $_POST['recs'];
                foreach ($allManiIds as $k => $maniId) {
                    $crt = new CDbCriteria();
                    $crt->addInCondition('man_id', $_POST['recs']);
                    ImParcel::model()->updateAll(array('consol_id' => $model->id, 'status' => 30), $crt);

                    // update manifest's consol id
                    Manifest::model()->updateByPk($maniId,['consol_id' => $model->id]);
                }
            }

			$this->ajaxResult($model, array('id'));
		}

        // we should list all received and paid manifest and related shipments
        // waiting for operator to confirm , if every thing is ok
        // then create console now

		$this->render('new',array(
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

		if(isset($_POST['ImcoConsol'])){
			$model->attributes=$_POST['ImcoConsol'];
			if(!empty($_POST['confirm-consol'])){
				if($model->status < 20) $model->status = 20;
			}
			$model->save();
			$this->ajaxResult($model);
		}
		
		$this->render('update',array('model'=>$model));
	}

    public function actionMupdate($id){

        $manifest = Manifest::model()->findByPk($id);

        if(isset($_POST['status'])){
            $manifest->status = $_POST['status'];
            $manifest->save();
            $this->ajaxResult($manifest);
        }

        $this->render('mupdate',array('model'=>$manifest));
    }

	/**
	 * Lists and search.
	 */
	public function actionManage(){
		$model=new ImcoConsol('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ImcoConsol']))
			$model->attributes=$_GET['ImcoConsol'];

		$this->render('list',array(
			'model'=>$model,
		));
	}

    public function actionAddtrack($id){
        $model=$this->loadModel($id);

        if(isset($_POST['note'])){
            $note = $_POST['note'];
            if ( !empty($note) ) {
                // add tracking for each shipments
                foreach ($model->shipments as $shipment) {
                    $shipment->addTracking(20,$note);
                }
            }
            $this->ajaxResult($model);

        }
        $this->render('addtrack',array(
            'model'=>$model,
        ));
    }

	public function actionRemoveParcel($id){
		$p = ImParcel::getLabel($id);
		if($p->status == 30){
			$p->consol_id = 0;
			$p->status = 25;
			$p->save();
		}
		echo 'done';
	}
	
	public function actionAddParcel($id){
		$model=$this->loadModel($id);
		if(isset($_POST['recs'])){
			if(empty($_POST['recs'])){
				$model->addError('awb', 'Please select shipment');
			}else{
				$criteria = new CDbCriteria();
				$criteria->compare('status', 25);
				$criteria->addInCondition('id', $_POST['recs']);
				$c = ImParcel::model()->updateAll(['status' => 30, 'consol_id' => $id], $criteria);
			}
			$this->ajaxResult($model);
		}
		$this->render('addparcel',array(
			'model'=>$model,
		));
	}

	public function actionBulkParcels($id){
		$model=$this->loadModel($id);
		if($model->status >= 20) return;

		if(!empty($_POST['act'])){
			switch($_POST['act']){
				case 'add':
					if(!empty($_POST['mhbns'])){
						$ns = preg_split('/[\s,;]+/', trim($_POST['mhbns']));
						$rs = ImParcel::model()->findAll("hbn IN ('".implode("','", $ns)."')");
						$c2 = [];
						$nfs = [];
						foreach($rs as $r){
							$nfs[] = $r->hbn;
							$warn = '';
							if($r->consol_id == $model->id){ //already in
								$c2[] = '<input type="checkbox" disabled /> '.$r->hbn.' already in this consol';
								continue;
							}
							if($r->status != 25){ //status
								$c2[] = '<input type="checkbox" disabled /> '.$r->hbn.' incorrect status - '.ImParcel::getStatus($r->status);
								continue;
							}

							$c2[] = '<input type="checkbox" value="'.$r->id.'" name="addids[]" class="chkbox" '.(empty($warn)? 'checked ':'').'/> '.$r->hbn.$warn;
						}

						foreach($ns as $hbn){
							if(!in_array($hbn, $nfs)){
								$c2[] = '<input type="checkbox" disabled /> '.$hbn.' not found';
							}
						}
						$o = new stdClass;
						$o->s2 = '<p>'.implode("</p>\n<p>", $c2).'</p>';
						$o->done = true;
						$o->msg = 'Please check parcels to add';
						echo json_encode($o);
						Yii::app()->end();
					}elseif(!empty($_POST['addids'])){
						$criteria = new CDbCriteria();
						$criteria->compare('status', 25);
						$criteria->addInCondition('id', $_POST['addids']);
						$c = ImParcel::model()->updateAll(['status' => 30, 'consol_id' => $model->id], $criteria);
						$msg = $c.' shipments added';
						$this->ajaxResult($model, [], $msg);
					}
				break;
				case 'brm':
					if(!empty($_POST['mhbns'])){
						$ns = preg_split('/[\s,;]+/', trim($_POST['mhbns']));
						$rs = ImParcel::model()->findAll("consol_id = :cid AND status = 30 AND hbn IN ('".implode("','", $ns)."')", [':cid' => $model->id]);
						if(empty($rs)){
							$msg = 'No shipment found!';
						}else{
							foreach($rs as $r){
								$p = ImParcel::getLabel($r->id);
								$p->consol_id = 0;
								$p->status = 25;
								$p->custom_log_note = 'Removed from consol '.$model->no;
								$p->save();
							}
							$msg = sizeof($rs).' shipments removed';
						}
					}
					$this->ajaxResult($model, [], $msg);
				break;
				case 'rbw':
					$rs = ImParcel::model()->findAll([
						'condition' => 'consol_id = :cid',
						'params' => [':cid' => $model->id],
						'order' => 'weight '.($_POST['st']==1? 'DESC' : 'ASC').', created DESC',
						]);
					if(empty($rs)){
						$msg = 'No shipment found!';
					}else{
						$c = 0;
						$tw = 0;
						foreach($rs as $r){
							$p = ImParcel::getLabel($r->id);
							if($tw + $p->weight > $_POST['weight']) break;
							$p->consol_id = 0;
							$p->status = 25;
							$p->custom_log_note = 'Removed from consol '.$model->no;
							$p->save();
							$c++;
							$tw += $p->weight;
						}
						$msg = $c.' shipments removed';
					}
					$this->ajaxResult($model, [], $msg);
				break;
			}
		}

		$this->render('bulkparcel',array(
			'model'=>$model,
		));
	}
	
	public function actionExport($id){
		$model=$this->loadModel($id);
		$csv = array();
		$csv[] = 'TYPE,CONNOTE NO.,WEIGHT,CNEE,TEL,ADDRESS,SUBURB,STATE,P/C,DESTINATION,,PCS,COMMODITY,INNER ITEMS,UNIT VALUE,TTL VALUE,CMETER,SHIPPER,SHIPPER ADD,SHIPPER STATE,SHIPPER PC,SHIPPER COUNTRY CODE,SHIPPER CONTACT';
		$rs = ImParcel::model()->findAll('consol_id = :cid', array(':cid' => $id));
		foreach($rs as $r){
			//$r = ImParcel::getLabel($r->id);
			$goods = empty($r->eitems['g'])? '' : implode("/", $r->eitems['g']);
			$item = empty($r->eitems['q'])? '' : implode("/", $r->eitems['q']);
			$value = empty($r->eitems['v'])? '' : implode("/", $r->eitems['v']);
			
			$csv[] = 'parcel,'.$r->hbn.','.$r->weight.','.$r->cnee->name.','.$r->cnee->tel.','.$r->cnee->address.','.$r->cnee->suburb.','.$r->cnee->state.','.$r->cnee->postcode.','.$r->cnee->country.','.$r->cnee->country.','.$r->pkg.','.$goods.','.$item.','.$value.','.$r->dvalue.','.$r->cbm.','.$r->cnor->name.','.$r->cnor->address.','.$r->cnor->state.','. $r->cnor->postcode .','.$r->cnor->country.','.$r->cnor->tel;
		}
		header("Cache-Control: maxage=1");
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=console_manifest_".$model->awb.".csv");
		echo implode("\n", $csv);
	}

	public function actionTrifree($id){
		$model=$this->loadModel($id);
		$xls = new oExcel;
		$xls->setColWidth(array(5,20,20,20,5,12,12,12,12,20,20,20,20,5,5,20,20,30,12,12,12,30,12,12));
		$i = 1;
		$xls->addRow($i++, array('中国出口三免清单','','','','','','','','','','','','','','','','','面单发票必要信息						'));
		$xls->mergeCell('A1:Q1');
		$xls->mergeCell('R1:X1');
		$xls->setFont('A1', array('bold' => true, 'size' => '28'));
		$xls->setFont('R1', array('bold' => true, 'size' => '20'));
		$xls->centerAlignment('A1');
		$xls->centerAlignment('R1');
		$xls->addRow($i++, array('','总运单号','运输工具航次','进/出口标志','','','总重量','总件数','分运单总数','运输方式','进港日期','','进出口岸代码'));
		$xls->addRow($i++, array('',$model->awb,$model->flight,'E','','', $model->totWeight(), $model->totPacks(),$model->totShipments(),'5',$model->etd,'116','2244'));
		$xls->addRow($i++, array());
		$xls->addRow($i++, array('','分运单号','中文品名','HS编码','','件数','重量','价值','币种','收件人','收货人城市','发件人','报关类别','','','经营单位','经营单位代码','发件人地址','发件联系人','发件人电话','发件人城市','收件人地址','收件联系人','收件人电话'));
		
		$rs = ImParcel::model()->findAll('consol_id = :cid', array(':cid' => $id));
		foreach($rs as $r){
			$r = ImParcel::getLabel($r->id);
			$goods = empty($r->eitems['g_zh'])? '' : implode("/", $r->eitems['g_zh']);
			$hs = empty($r->eitems['hs'])? '' : implode("/", $r->eitems['hs']);
			
			$xls->addRow($i++, array('',$r->hbn,$goods,$hs,'',$r->pkg,$r->weight,$r->dvalue,'USD',$r->cnee_name,$r->cnee_suburb,$r->cnor_name,2,'','','上海万般贸易有限公司','3116960817',$r->cnor_address,$r->cnor_name,$r->cnor_tel,$r->cnor_city,$r->cnee_address,$r->cnee_name,$r->cnee_tel));
		}

		$xls->output($model->no.'_trifree_Manifest.xlsx');
	}

	public function actionInvoice($id){
		$model=$this->loadModel($id);
		$inv = $model->getInvoice();
		if(empty($inv)) die('Invoice not ready.');
		oPDF::renderPDF('invoice', array('inv'=> $inv));
	}

	public function actionInvxls($id){
		$model=$this->loadModel($id);
		$inv = $model->getInvoice();
		if(empty($inv)) die('Invoice not ready.');
		$xls = new oExcel;
		$mfn = 'Invoice_detail_'.$id;
		$i = 1;
		$xls->addRow($i++, array('HBN', 'Detail', 'Packages', 'Weight', 'CBM', 'Base Rate', 'Rate', 'Unit', 'Amount'));
		$xls->setFont('A1:I1', array('bold' => true));
		$qty = 0;
		$wei = 0;
		$cbm = 0;
		$tot = 0;
		foreach($inv->mdata['items'] as $r){
			$xls->addRow($i++, array($r[0], $r[1], $r[2], $r[3], $r[4], $r[6], $r[7], $r[8], $r[5]));
			$tot += $r[5];
			$qty += $r[2];
			$wei += $r[3];
			$cbm += $r[4];
		}
		$xls->addRow($i, array('', 'Total:', $qty, $wei, $cbm, '', '', '', $tot));
		$xls->setFont('A'.$i.':I'.$i, array('bold' => true));
		$i++;
		$xls->output($mfn.'.xlsx');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=ImcoConsol::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='consol-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
