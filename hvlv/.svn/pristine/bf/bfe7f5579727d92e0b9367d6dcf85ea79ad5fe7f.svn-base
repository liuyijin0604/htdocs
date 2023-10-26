<?php

class ExacConsolController extends Controller{

	protected $nonAjax = array('export', 'download', 'palletRpt');

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate(){
		$model=new ExacConsol;

		if(isset($_POST['ExacConsol'])){
			$model->attributes = $_POST['ExacConsol'];
			$model->save();
			$this->ajaxResult($model, ['id']);
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

		if(isset($_POST['ExacConsol'])){
			$model->attributes = $_POST['ExacConsol'];
			if(!empty($_POST['yt1']) && $model->status == 10)  {
                $model->status = 20;

                // for dxt we send stock out sync message
                DxtPushQueue::addStockOut($model);
            }
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

	public function actionImport($id){
		$model = $this->loadModel($id);
		if(!empty($_FILES['manifest'])){
			$m = new Manifest('upload');
			$m->type = 130;
			$m->status = 20;
			$m->consol_id = $model->id;
			$m->save();
			$err = $m->getErrors();
			$o = new StdClass;
			if(!empty($err)){
				$o->err = implode('<br />', $err);
			}else{
				$model->mdata['mf'] = $m->rfile->id;
				$model->save();
				$xls = new oExcel;
				$xls->supported($_FILES['manifest']['name']);
				$xls->load($_FILES['manifest']['tmp_name']);
				$data = $xls->getAll();
				$o->cols = [];
				foreach($data[1] as $i=>$t){
					$o->cols[PHPExcel_Cell::stringFromColumnIndex($i-1)] = $t;
				}
				$o->rc = sizeof($data) - 1;
			}
			echo json_encode($o);
		}elseif(!empty($_POST['map'])){
			$mm = explode(',', $_POST['map']);
			$mtmap = [];
			foreach($mm as $m){
				$p = explode(':', $m);
				$mtmap[$p[0]] = $p[1];
			}
			$model->owner->extra['mtmap'] = $mtmap;
			$model->owner->save();
			$fr = FileRepo::model()->findByPk($model->mdata['mf']);
			$xls = new oExcel;
			ini_set('precision', 12);
			if(!$xls->supported($fr->name)){
				$xls->getError(true);
				exit(0);
			}
			$xls->load($fr->getFile());
			$data = $xls->getAll();
			unset($data[1]);
			$getData = function($r,$i,$d='') use($mtmap){
				if(!isset($mtmap[$i])) return $d;
				$c = PHPExcel_Cell::columnIndexFromString($mtmap[$i]);
				return isset($r[$c])? $r[$c] : $d;
			};

			$trans = Yii::app()->db->beginTransaction();
			try{
				foreach($data as $r){
					$hbn = $getData($r, 'f0');
					$s = empty($hbn)? null : ExAfs::model()->find('hbn = :h', [':h' => $hbn]);
					if(empty($s)){
						$s = new ExAfs;
						$s->cnor = new Addr;
						$s->cnee = new Addr;
						$s->consol_id = $model->id;
						$s->exm = 'EXLV';
						$s->status = 20;
					}
					$s->agent_id = $model->owner_id;
					$s->hbn = $getData($r, 'f0', '');
					if(empty($s->hbn)) $s->hbn = $s->genHbn();
					$s->cref = $getData($r, 'f1');
					$s->weight = sprintf('%0.2f', $getData($r, 'f3'));
					$s->pkg = $getData($r, 'f2', 1);
					//cnor
					$s->cnor->name = $getData($r, 'f4', $model->owner->name);
					$s->cnor->tel = $getData($r, 'f5', $model->owner->phone);
					$s->cnor->address = $getData($r, 'f6', $model->owner->address);
					$s->cnor->save();
					$s->cnor_id = $s->cnor->id;
					//cnee
					$s->cnee->name = $getData($r, 'f7');
					$s->cnee->tel = $getData($r, 'f8');
					$s->cnee->address = $getData($r, 'f9');
					$s->cnee->state = $getData($r, 'f10');
					$s->cnee->city = $getData($r, 'f11');
					$s->cnee->suburb = $getData($r, 'f12');
					$s->cnee->postcode = $getData($r, 'f13');
					if(empty($s->cnee->postcode)) $s->cnee->setCnAddr($s->cnee->address);
					$s->cnee->save();
					$s->cnee_id = $s->cnee->id;

					$em = ['g' => 'f14', 'g_zh' => 'f15', 'q' => 'f16', 'hs' => 'f17', 'v' => 'f18'];
					foreach($em as $k=>$f){
						$v = $getData($r, $f);
						if(empty($v)){
							$s->eitems[$k] = [];
							continue;
						}
						$s->eitems[$k] = explode(',', $getData($r, $f));
					}
					
					$s->save();
				}
				$trans->commit();
			} catch (Exception $ex) {
				$trans->rollback();
				throw $ex;
			}
			
			$o = new StdClass;
			$o->done = true;
			echo json_encode($o);
		}else{
			$this->render('import',array(
				'model'=>$model,
			));
		}
	}

	public function afsMaps(){
		return [
			'f0' => ['HBN', 0],
			'f1' => ['Cust. Ref', 0],
			'f2' => ['Packs', 0],
			'f3' => ['Weight', 1],
			'f4' => ['Shipper Name', 0],
			'f5' => ['Shipper Tel', 0],
			'f6' => ['Shipper Addr', 0],
			'f7' => ['Cnee Name', 1],
			'f8' => ['Cnee Tel', 0],
			'f9' => ['Cnee Addr', 1],
			'f10' => ['Cnee State', 0],
			'f11' => ['Cnee City', 0],
			'f12' => ['Cnee Suburb', 0],
			'f13' => ['Cnee Postcode', 0],
			'f14' => ['Goods Name (Eng)', 1],
			'f15' => ['货物品名', 0],
			'f16' => ['Goods Qty', 1],
			'f17' => ['Goods HS', 0],
			'f18' => ['Goods Value', 0],
		];
	}
	
	public function actionNotes($id){
		$model=$this->loadModel($id);
		if(!empty($_POST['notes'])){
			$log = Log::add($model, 6, array('notes' => $_POST['notes']));
			$this->ajaxResult($log);
		}
	}

	public function actionDownload($id){
		$model=$this->loadModel($id);

		switch($_GET['type']){
			case 'label':
				$rs = $model->shipments;
				oPDF::renderPDF('label_A6', array('tpl' => '_label-ex', 'empty' => false, 'rs' => $rs));
				Yii::app()->end();
			break;
			case 'label-a4':
				$rs = $model->shipments;
				oPDF::renderPDF('label_A4', array('tpl' => '_label-ex', 'empty' => false, 'rs' => $rs));
				Yii::app()->end();
			break;
		}
	}

	public function actionRemoveParcel($id){
		$p=ExAfs::model()->findByPk($id);
		if($p){
			$con = $p->consol;
			$p->consol_id = 0;
			$p->status = 100;
			$p->custom_log_note = 'Removed from consol '.$con->no;
			$p->save();
		}
		echo 'done';
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new ExacConsol('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ExacConsol']))
			$model->attributes=$_GET['ExacConsol'];

		$this->render('list',array(
			'model'=>$model,
		));
	}
	
	public function actionEsm($id){
		$model=$this->loadModel($id);
		if(!empty($_GET['act'])){
			switch($_GET['act']){
				case 'send':
					$edi = $model->newESM();
					if($edi->send()){
						$model->status = 30;
						$model->save();
					}
				break;
				case 'withdraw':
					$edi = $model->withdrawESM();
					if($edi->send()){
						$model->status = 50;
						$model->save();
					}
				break;
			}
			$this->ajaxResult($edi);
		}
	}

	public function actionPalletRpt($id){
		$model=$this->loadModel($id);
		$xls = new oExcel;
		$i = 1;
		$of = $model->no.'_Pallets.xlsx';
		$rs = Manifest::model()->findAll('type = 60 AND consol_id = :cid', [':cid' => $model->id]);
		$xls->addRow($i++, array('序号','板号','单号','转单号','重量'));
		$sn = 1;
		if(!empty($rs)){
			foreach($rs as $m){
				$tw = 0;
				foreach($m->lines as $l){
					$r = $l->mm();
					$xls->addRow($i++, array($sn++,$m->ref,$r->hbn,$r->ref,$r->weight));
					$tw += $r->weight;
				}
				$xls->addRow($i++, array('', $m->ref, 'Total', '', round($tw*100)/100));
				$xls->addRow($i++, array(''));
			}
		}
		$xls->output($of);
	}

	public function actionExport($id){
		$model=$this->loadModel($id);
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, array('WBN', 'Ref', 'Cust Ref', 'Status', 'Agent', 'Weight', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State'));

		foreach($model->shipments as $r){
			if(empty($r->cnee)) continue;
			$xls->addRow($i++, array($r->hbn, '="'.$r->ref.'"', empty($r->cref)? '' : '="'.$r->cref.'"', $r->getStatus(), empty($r->agent)? '' : $r->agent->name, $r->weight, $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->address, $r->cnee->state));
		}
		$xls->output($model->no.'_manifest.xlsx');
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=ExacConsol::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
}
