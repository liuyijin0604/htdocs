<?php

class CnIDController extends Controller{

	protected $nonAjax = array('photo', 'download');
	
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
		$model=new CnID;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if(isset($_POST['CnID'])){
			$model->status = 10;
			$model->attributes=$_POST['CnID'];
			$model->front = $_POST['front'];
			$model->back = $_POST['back'];
			$model->joinPhoto();
			if($model->save()){
				$model->photo_front->fid = $model->id;
				$model->photo_front->name = $model->no.'-1.jpg';
				$model->photo_front->save();
				$model->photo_back->fid = $model->id;
				$model->photo_back->name = $model->no.'-2.jpg';
				$model->photo_back->save();
				$su = Yii::app()->session['uploads'];
				//remove any unused photo
				if(!empty($su[$_POST['pphash']])){
					foreach($su[$_POST['pphash']][2] as $id){
						$f = FileRepo::model()->findByPk($id);
						if(empty($f->fid)) $f->hardDelete();
					}
				}
				$model->matchCnee();
			}
			$this->ajaxResult($model);
		}

		$this->render('create',array(
			'model'=> $model,
			'addr' => new Addr,
		));
	}

	public function actionPhoto($id){
		$model=$this->loadModel($id);
		if(!empty($_GET['front'])) $this->redirect($model->photo_front->getUrl());
		if($model && $model->joint > 0){
			echo '<div style="min-height:400px"><img src="'.$model->photo_joint->getUrl().'" width="600" /></div>';
		}else{
			echo 'Photo Not Found!';
		}
	}


	public function actionAttach($id){
		$model=new CnID;
		$addr = Addr::model()->findByPk($id);

		if(isset($_POST['CnID'])){
			$model->attributes=$_POST['CnID'];
			$model->front = $_POST['front'];
			$model->back = $_POST['back'];
			$model->joinPhoto();
			if($model->save()){
				//remove any unused photo
				$su = Yii::app()->session['uploads'];
				if(!empty($su[$_POST['pphash']])){
					$model->photo_front->fid = $model->id;
					$model->photo_front->name = $model->no.'-1.jpg';
					$model->photo_front->save();
					$model->photo_back->fid = $model->id;
					$model->photo_back->name = $model->no.'-2.jpg';
					$model->photo_back->save();
					foreach($su[$_POST['pphash']][2] as $id){
						$f = FileRepo::model()->findByPk($id);
						if(empty($f->fid)) $f->hardDelete();
					}
				}
			}
			$addr->cnid_id = $model->id;
			if(empty($addr->name)) $addr->name = $model->name;
			if(empty($addr->state) && !empty($_POST['CnID']['state'])) $addr->state = $_POST['CnID']['state'];
			if(empty($addr->city)) $addr->city = $model->city;
			if(empty($addr->tel)) $addr->tel = $model->mobile;
			$addr->save();
			$p = ExParcel::model()->find('cnee_id = :cid', [':cid' => $addr->id]);
			$p->save();
			$this->ajaxResult($model);
		}

		$this->render('attach',array(
			'model'=> $model,
			'addr' => $addr,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id){
		$model=$this->loadModel($id);

		if(isset($_POST['CnID'])){
			$model->attributes=$_POST['CnID'];
			$photo_updated = $model->front != $_POST['front'] || $model->back != $_POST['back'];
			$model->front = $_POST['front'];
			$model->back = $_POST['back'];
			$rs = FileRepo::model()->findAll('type = 60 AND fid = :fid', array(':fid' => $_GET['id']));

			if($photo_updated || empty($model->joint)){
				$model->joinPhoto();
				$model->photo_front->name = $model->no.'-1.jpg';
				$model->photo_front->save();
				$model->photo_back->name = $model->no.'-2.jpg';
				$model->photo_back->save();
				foreach($rs as $f){
					if(!in_array($f->id, array($model->front, $model->back, $model->joint)))	$f->hardDelete();
				}
			}
			$model->save();
			$model->matchCnee();
			$this->ajaxResult($model);
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionUploaded(){
		$su = Yii::app()->session['uploads'];
		if(!empty($su[$_GET['hash']])){
			echo '<table>';
			if(!empty($su[$_GET['hash']][2])){
				foreach($su[$_GET['hash']][2] as $id){
					$f = FileRepo::model()->findByPk($id);
					if(empty($f)) continue;
					echo '<tr><td width="120"><img class="thumb" src="'.$f->getUrl().'" width="100" /><br /><i>'.$f->name.'</i></td><td><label class="radio_label"><input type="radio" class="rfrt" name="front" value="'.$f->id.'" /> Front</label> &nbsp; <label class="radio_label"><input type="radio" class="rbak" name="back" value="'.$f->id.'" /> Back</label></td></tr>';
				}
			}elseif(!empty($_GET['id'])){
				$rs = FileRepo::model()->findAll('type = 60 AND fid = :fid', array(':fid' => $_GET['id']));
				foreach($rs as $f){
					echo '<tr><td width="120"><img class="thumb" src="'.$f->getUrl().'" width="100" /><br /><i>'.$f->name.'</i></td><td><label class="radio_label"><input type="radio" class="rfrt" name="front" value="'.$f->id.'" /> Front</label> &nbsp; <label class="radio_label"><input type="radio" class="rbak" name="back" value="'.$f->id.'" /> Back</label></td></tr>';
				}
			}
			echo '</table>';
		}
	}

	public function actionFetch(){
		if(!empty($_GET['out'])){
			$logf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'ids_running.log';
			$o = new StdClass;
			$o->status = 0;
			$o->out = 'Problem running the task, please try again later.';
			if(is_file($logf)){
				$o->status = 1;
				$o->out = file_get_contents($logf);
				if(preg_match('/Done$/',$o->out)){
					$o->status = 0;
				}
			}
			echo json_encode($o);
			Yii::app()->end();
		}

		$this->render('fetch_ids');
		AppHelper::exec(Yii::app()->basePath.DIRECTORY_SEPARATOR.'yiic iexp getIDs > /dev/null 2>/dev/null &');
	}

	/**
	 * Lists and search.
	 */
	public function actionList(){
		$model=new CnID('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['CnID']))
			$model->attributes=$_GET['CnID'];

		$this->render('list',array(
			'model'=>$model,
		));
	}

	public function actionDownload(){
		$model=new CnID('search');
		$model->unsetAttributes();
		$model->attributes=$_POST['CnID'];
		$rs = $model->search();
		$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'ID_Export_'.time().'.zip';
		$zip = new ZipArchive;
		$zip->open($zf, ZipArchive::CREATE);
		$copied = [];
		if(empty($_POST['CnID']['mnos']) || empty($_POST['CnID']['mnames']) || empty($rs->data)){
			throw new CHttpException(400, 'No ID found');
		}
		foreach($rs->data as $r){
			$cnid_new = !in_array($r->id, $copied);
			if($cnid_new) $zip->addFile($r->photo_joint->getFile(), $r->no.'.jpg');
			$copied[] = $r->id;
		}
		$zip->close();
		if(!is_file($zf)) throw new CHttpException(400, 'File is empty');

		header("Cache-Control: maxage=1");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header('Content-Disposition: attachment; filename="'.basename($zf));
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($zf));
		readfile($zf);
		unlink($zf);
		Yii::app()->end();
	}

	public function actionSuggest(){
		$em = false;
		if(!empty($_GET['name'])){
			if(!empty($_GET['tel'])){
				$rs = CnID::model()->findAll(array(
					'condition' => 'status < 98 AND bwf = 0 AND `mobile` = :t AND `name` = :n',
					'params' => array(':n' => $_GET['name'], ':t' => $_GET['tel']),
					'limit'=>20,
				));
				$em = true;
			}
			if(empty($rs)){
				$rs = CnID::model()->findAll(array(
					'condition' => 'status < 98 AND bwf = 0 AND `name` LIKE :n',
					'params' => array(':n' => $_GET['name']),
					'limit'=>20,
				));
				$em = false;
			}
		}else{
			if(empty($_GET['name'])){
				$rs = CnID::model()->findAll(array(
					'condition' => 'status < 98 AND bwf = 0 AND `no` LIKE :no',
					'params' => array(':no' => $_GET['term'].'%'), 
					'limit'=>20
				));
			}else{
				$rs = CnID::model()->findAll(array(
					'condition' => 'status < 98 AND bwf = 0 AND `name` LIKE :n AND `no` LIKE :no',
					'params' => array(':n' => $_GET['name'], ':no' => $_GET['term'].'%'), 
					'limit'=>20,
				));
			}
		}	
		$a = array();
		foreach($rs as $r){
			$a[] = array(
				'value' => $r->id,
				'label' => $r->no.' ('.$r->name.'/'.$r->mobile.')',
				'no' => $r->no,
				'name' => $r->name,
				'exm' => $em,
			);
		}
		echo json_encode($a);
	}

	public function actionDelete($id){
		$model=$this->loadModel($id);
		$model->status = 99;
		$model->save();
		$this->ajaxResult($model);
	}

	public function actionBulkWarn(){
		if(!empty($_POST['d'])){
			$ds = preg_split('/[\s;,]+/', trim($_POST['d']));
			$model = new CnID;
			foreach($ds as $d){
				$cnid = CnID::model()->find('no = :n', [':n' => $d]);
				if(empty($cnid)){
					continue;
				}else{
					if(!empty($_POST['exp'])) $cnid->status = 98;
					if(!empty($_POST['warn'])){
						foreach($_POST['warn'] as $d => $v){
							if(empty($v)) continue;
							$cnid->bwf = $cnid->bwf | $d;
						}
					}
					$cnid->save();
				}
			}
			$this->ajaxResult($model);
		}

		$this->render('bulk_warn');
	}
	
	public function actionTogWarn($id){
		$model=$this->loadModel($id);
		if(!empty($_GET['b'])){
			if(($model->bwf & (int) $_GET['b']) > 0){
				$model->bwf = $model->bwf & (~(int) $_GET['b']);
				$model->custom_log_note = 'Warning ['.ExParcel::$bwfs[$_GET['b']].'] removed';
			}else{
				$model->bwf = $model->bwf | (int) $_GET['b'];
				$model->custom_log_note = 'Warning ['.ExParcel::$bwfs[$_GET['b']].'] added';
			}
			$model->save();
		}	
		echo 'done';
	}

	public function actionTogExp($id){
		$model=$this->loadModel($id);
		$model->status = $model->status == 98? 15 : 98;
		$model->save();
		echo 'done';
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id){
		$model=CnID::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model){
		if(isset($_POST['ajax']) && $_POST['ajax']==='cn-id-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
