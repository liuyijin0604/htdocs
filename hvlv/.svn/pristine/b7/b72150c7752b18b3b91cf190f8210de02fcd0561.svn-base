<?php

class ExprodController extends Controller
{
	protected $skipAcl = ['suggest'];
	protected $nonAjax = ['impexp'];

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
		$model=new ExProdb;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ExProdb'])){
			$model->attributes=$_POST['ExProdb'];
			$model->status = 1;
			if(!empty($_POST['meta'])){
				foreach($_POST['meta'] as $k => $v){
					$model->mdata[$k] = $v;
				}
			}
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}


	public function actionCopy($id){
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ExProdb'])){
			$model->attributes=$_POST['ExProdb'];
			$model->id = null;
			$model->isNewRecord = true;
			$model->save();
			$this->ajaxResult($model);
		}
		
		$model->isNewRecord = true;

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

		if(isset($_POST['ExProdb'])){
			$model->attributes=$_POST['ExProdb'];
			if(!empty($_POST['meta'])){
				foreach($_POST['meta'] as $k => $v){
					$model->mdata[$k] = $v;
				}
			}
			$model->save();
			$this->ajaxResult($model);
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionTagUpdate(){
		$model=new ExProdb('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ExProdb']))
			$model->attributes=$_GET['ExProdb'];

		if(isset($_POST['get'])){
			$dp = $model->search();
			$tags = array();
			$pc = count($dp->data);
			foreach($dp->data as $r){
				if(empty($r->tag)) continue;
				$ts = explode(',', $r->tag);
				foreach($ts as $t){
					if(!isset($tags[$t])) $tags[$t] = 0;
					$tags[$t]++;
				}
			}
			foreach($tags as $t=>$c){
				if($c < $pc) unset($tags[$t]);
			}
			echo implode(',', array_keys($tags));

			Yii::app()->end();
		}

		if(isset($_POST['tags'])){
			$tags = explode(',',$_POST['tags']);
			$otags = explode(',',$_POST['otags']);
			$rms = array();
			foreach($otags as $ot){
				if(!in_array($ot, $tags)) $rms[] = $ot;
			}
			$dp = $model->search();
			foreach($dp->data as $r){
				$ts = empty($r->tag)? array() : explode(',', $r->tag);
				foreach($ts as $i=>$t){//remove
					if(in_array($t, $rms)) unset($ts[$i]);
				}
				foreach($tags as $i=>$t){//add
					if(!in_array($t, $ts)) $ts[] = $t;
				}
				$ntag = trim(implode(',', $ts), ',');
				if($ntag != $r->tag){
					$r->tag = $ntag;
					$r->save();
				}
			}

			$this->ajaxResult($model);
		}

		$this->render('tagupdate',array(
			'model'=>$model,
		));
	}

	public function actionImpexp(){
		if(!empty($_GET['poc'])){
			$poc = $_GET['poc'];
			$xls = new oExcel;
			$i = 1;
			$xls->setColWidth(array(10,10,20,25,25,15,15,10,10,10,15,10,20,15,15));
			$xls->addRow($i++, array('ID', 'Type', 'SKU', 'Name', '品名', 'Brand', 'Model', 'Weight', 'Unit', 'Price', 'HS', $poc.' Price', $poc.' Name', $poc.' HS', $poc.' SKU'));

			$rs = ExProdb::model()->findAll('status = 1');
			
			foreach($rs as $r){
				$pp = $r->getPocPrice($poc);
				$hs = empty($pp->hs)? '' : $pp->hs;
				$price = empty($pp->price)? '' : $pp->price;
				$name = empty($pp->name)? '' : $pp->name;
				$ns = empty($pp->sn)? '' : $pp->sn;
				$xls->addRow($i++, array($r->id, $r->type, '="'.$r->sku.'"', $r->name, $r->name_zh, $r->brand, $r->model, $r->weight, $r->unit, $r->price, '="'.$r->hs.'"', $price, $name, '="'.$hs.'"', '="'.$sn.'"'));
			}
			$xls->output('priceList_'.$poc.'.xlsx');
		}
		if(!empty($_FILES['excel'])){
			$xls = new oExcel;
			$err = [];
			$resp=array('done'=>TRUE,'msg'=>'Import successfully');
			if(!$xls->supported($_FILES['excel']['name'])){
				foreach($xls->getError() as $e){
					$err[] = $e;
				}
			}else{
				$xls->load($_FILES['excel']['tmp_name']);
				ini_set('precision', 12);
				$data = $xls->getAll();
				$poc = $_POST['poc'];
				if(implode(',', array_slice($data[1], 0, 11)) == 'ID,Type,SKU,Name,品名,Brand,Model,Weight,Unit,Price,HS'){
					$trans = Yii::app()->db->beginTransaction();
					try{
						foreach($data as $l => $r){
							if($l == 1) continue;
							if(empty($r[1])){
								$ptyp = empty($r[2])? 90 : $r[2];
								$p = ExProdb::model()->find('type = :t AND name = :n AND name_zh = :nz AND brand = :b', [':t' => $ptyp, ':n' => $r[4], ':nz' => $r[5], ':b' => $r[6]]);
								if(empty($p)){
									$p = new ExProdb;
									$p->type = $ptyp;
									$p->status = 1;
								}
								if(empty($r[12])) $r[12] = $r[10];
							}else{
								$p = ExProdb::model()->findByPk($r[1]);
								if(empty($p)){
									$err[] = $l.':'.$r[1]." not found\n";
									continue;
								}
							}

							$mp = [4 => 'name', 5 => 'name_zh', 6 => 'brand', 7 => 'model', 8 => 'weight', 9 => 'unit', 10 => 'price', 11 => 'hs'];
							$up = false;
							foreach($mp as $k=>$kn){
								if($p->{$kn} != $r[$k]){
									$p->{$kn} = $r[$k];
									$up = true;
								}
							}
							$p->noaup = true;
							if($up) $p->save();

							$pp = ExProdbPrice::model()->find('pid = :id AND poc = :poc', [':id' => $p->id, ':poc' => $poc]);
							if(empty($pp)){
								$pp = new ExProdbPrice;
								$pp->type = 10;
								$pp->pid = $p->id;
								$pp->poc = $poc;
								$pp->status = 1;
							}
							$pp->price = round(floatval($r[12])*100)/100;
							$pp->name = '';
							if(!empty($r[13]) && $r[13] != $p->name) $pp->name = $r[13];
							if(!empty($r[14]) && $r[14] != $p->hs) $pp->hs = $r[14];
							if(!empty($r[15]) && $r[15] != $p->sku) $pp->sn = $r[15];
							$pp->save();
						}
						$trans->commit();
					} catch (Exception $ex) {
						$trans->rollback();
						throw $ex;
					}
				}else{
					$err[] = 'Column mismatch, please make sure the file is correct';
				}
			}
			$model = new ExProdb;
			if(!empty($err)){
				foreach($err as $e) $model->addError('id', $e);
			}
			$this->ajaxResult($model);
		}
		$this->render('impexp');
	}

	public function actionUnmatched(){
		$this->render('unmatched');
	}


	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new ExProdb('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ExProdb']))
			$model->attributes=$_GET['ExProdb'];
		$model->status = 1;

		$this->render('list',array(
			'model'=>$model,
		));
	}

	public function actionSuggest(){
		$prs = ExProdb::model()->findAll(array(
				'condition' => "status = 1 AND type = :t AND (code = :n OR CONCAT(',',tag,',') LIKE :tn OR name_zh LIKE :fn)",
				'params' => array(':t' => ExProdb::$rtypes[$_GET['t']], ':fn' => '%'.$_GET['term'].'%', ':n' => $_GET['term'], ':tn' => '%,'.$_GET['term'].',%'),
			));
		$a = [];
		foreach($prs as $r){
			/*if($_GET['t'] == 'O'){
				if(empty($r->tag)) continue;
				$tgs = explode(',', $r->tag);
				foreach($tgs as $t){
					if(strpos($t, $_GET['term']) !== false && !in_array($t, $a)){
						$a[] = $t;
						break;
					}
				}
			}else{*/
				$a[] = $r->name_zh;
			//}
		}
		$a = array_unique($a);
		echo json_encode($a);
	}

	public function actionPocPriceGrid($id){
		if(empty($_POST['ExProdbPrice']['id'])){
			$m = new ExProdbPrice;
			$m->pid = $id;
			$m->status = 1;
			$m->type = 10;
		}else{
			$m = ExProdbPrice::model()->findByPk($_POST['ExProdbPrice']['id']);
		}
		unset($_POST['ExProdbPrice']['id']);
		$m->setAttributes($_POST['ExProdbPrice']);
		$m->save();
		$this->ajaxResult($m);
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=ExProdb::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='ex-prodb-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
