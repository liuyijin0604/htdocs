<?php

class ExdirConsolController extends Controller{

	protected $nonAjax=array('export', 'download');

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
		$model=new ExdirConsol;
		$model->pol = 'AUSYD';
		$model->pod = 'CNSHA';

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		if(!empty($_POST['recs'])){
			$model->status = 10;
			$model->save();

			foreach($_POST['recs'] as $m){
				$man = ExDirect::model()->findByPk($m);
				$man->status = 50;
				$man->consol_id = $model->id;
				$man->save();
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

		if(isset($_POST['ExdirConsol'])){
			$model->attributes=$_POST['ExdirConsol'];
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

	public function actionConfirm($id){
		$model=$this->loadModel($id);
		$o = new StdClass;
		$o->comp = false;
		if(!empty($_FILES['manifest']) && is_uploaded_file($_FILES['manifest']['tmp_name'])){
			$err = [];
			$war = [];
			$xls = new oExcel;
			if(!$xls->supported($_FILES['manifest']['name'])){
				foreach($xls->getError() as $e){
					$err[] = $e;
				}
			}else{
				$xls->load($_FILES['manifest']['tmp_name']);
				$data = $xls->getAll();
				ini_set('precision', 12);
				$hl3 = implode(',', array_slice($data[1],0,3));
				unset($data[1]);
				$ps = [];
				if($hl3 == "序号,订单号,转运单号"){
					foreach($data as $r){
						if(empty($r[3])) continue;
						$p = ExDirect::model()->find('consol_id = :id AND hbn = :h', array(':id' => $id, ':h' => $r[2]));
						if(empty($p)){
							$war[] = $r[2].' not found in this consol';
						}elseif($p->status < 90){
							$p->ref = $r[3];
							$ps[] = $p;
						}
					}
				}else{
					$err[] = 'Manifest Column Mismatch!';
				}
			}

			$o->err = empty($err)? false : implode('<br />', $err);
			$o->war = empty($war)? false : implode('<br />', $war);
			if(empty($err)){
				foreach($ps as $p){
					if($p->status < 50) $p->status = 50;
					$p->save();
				}
				$rs = ExDirect::model()->findAll('consol_id = :id AND status = 20', array(':id' => $id));
				if(empty($rs)){
					if($model->status < 20) $model->status = 20;
					$model->save();
					$o->html = 'All shipments confirmed';
					$o->comp = true;
				}
			}
			unset($xls);
		}
		echo json_encode($o);
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new ExdirConsol('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ExdirConsol']))
			$model->attributes=$_GET['ExdirConsol'];

		$this->render('list',array(
			'model'=>$model,
		));
	}
	
	public function actionExport($id){
		$model=$this->loadModel($id);
		$xls = new oExcel;
		$i = 1;
		$xls->setColWidth(array(10,15,15,15,10,15,50,15,15,50));
		$xls->addRow($i++, ['序号', '订单号', '转运单号', '姓名', '数量', '电话', '地址', '发货日期', '发货方式', '备注']);
		foreach($model->shipments as $j => $r){
			$qty = 0;
			foreach($r->eitems['q'] as $q){
				$qty += $q;
			}
			$xls->addRow($i++, [$j+1, $r->hbn, $r->ref, $r->cnee->name, $qty, $r->cnee->tel, $r->cnee->getCnFullAddress(), $model->created, '顺丰', $r->note]);
		}

		$xls->output($model->no.'_manifest.xlsx');
	}

	public function actionDownload($id){
		$model=$this->loadModel($id);

		$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$model->no.'-'.strtoupper($_GET['type']).date('YmdHi').'.zip';
		$zip = new ZipArchive;
		$zip->open($zf, ZipArchive::CREATE);
		$copied = [];
		$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		mkdir($td);

		switch($_GET['type']){
			case 'invoices':
				$rs = Yii::app()->db->createCommand("SELECT DISTINCT(agent_id) FROM shipment WHERE consol_id = :id")->bindValues(['id' => $id])->queryAll();
				foreach($rs as $r){
					$inv = Invoice::model()->with('lines')->find('t.status < 10 AND t.type = 30 AND to_id = :id AND lines.model = :m AND lines.fid = :fid', [':id' => $r['agent_id'], ':m' => 'Consol', ':fid' => $id]);
					if(empty($inv)){
						$inv = $model->genInvoice($r['agent_id']);
						$inv->afterFind();
					}
					$zip->addFromString('Invoice_'.$inv->no.'.pdf', oPDF::renderPDF('invoice', array('inv'=>$inv), 0));
				}
			break;
			default:
				throw new CHttpException(403, 'Access Denied!');
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


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=ExdirConsol::model()->findByPk($id);
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
