<?php
class UploadIDController extends PController{
	protected $debug = false;

	public function beforeAction($action){
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Cache-Control');
		header('Access-Control-Max-Age: 3600');
		return parent::beforeAction($action);
	}

	public function actionIndex(){
		if($this->debug) $this->log('POST: '.print_r($_POST, true)."\nFILES:".print_r($_FILES['file'], true));
		if(empty($_POST) || empty($_FILES['file'])) die('No upload file detected.');
		
		$cnid = CnID::model()->find('no = :no AND status = 14', array(':no' => $_POST['id_no']));
		if(empty($cnid)){
			$cnid = new CnID;
			$cnid->status = 14;
			$cnid->name = $_POST['id_name'];
			$cnid->no = strtoupper($_POST['id_no']);
			$cnid->mobile = empty($_POST['id_mobile'])? '' : preg_replace('/[^\d]+/', '', $_POST['id_mobile']);
			$cnid->mdata['connote'] = empty($_POST['id_connote'])? '' : $_POST['id_connote'];
			if(!$cnid->save()) $this->log('Saving error, '.print_r($cnid->getErrors(), true));
		}
		
		//resize
		if($this->debug) $this->log('Saving file '.$_POST['name']);
		$img = AppHelper::resizeImg($_FILES['file']['tmp_name'], 600);
		if($img) @imagejpeg($img, $_FILES['file']['tmp_name']);
		$fid = FileRepo::storeFile($_FILES['file']['tmp_name'], $_POST['name'], 60, $cnid->id);
		
		if(empty($fid)){
			copy($_FILES['file']['tmp_name'], Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'error_upload'.DIRECTORY_SEPARATOR.$_POST['name']);
			die('FAILED');
		}elseif($this->debug){
			$this->log('Saved: '.$fid);
		}

		if(strpos($_POST['name'], '_1.') > 0){
			$cnid->front = $fid;
		}else{
			$cnid->back = $fid;
		}
		$cnid->nolog = true;
		if(!$cnid->save()) $this->log('Saving error, '.print_r($cnid->getErrors()), true);

		if($cnid->front > 0 && $cnid->back > 0){
			$cnid->nolog = false;
			$cnid->joinPhoto();
			$cnid->status = 15;
			$cnid->save();
			$cnid->matchCnee();
		}

		echo 'DONE';
	}

	public function actionV2(){
		if($this->debug) $this->log('POST: '.print_r($_POST, true)."\nFILES:".print_r($_FILES, true));
		if(empty($_POST) || empty($_FILES['front']) || empty($_FILES['back'])) die('No upload file detected.');

		$cnid = CnID::model()->find('no = :no AND status = 14', array(':no' => $_POST['id_no']));

		if(empty($cnid)){
			$cnid = new CnID;
			$cnid->status = 14;
			$cnid->name = $_POST['id_name'];
			$cnid->no = $_POST['id_no'];
			$cnid->mobile = empty($_POST['id_mobile'])? '' : $_POST['id_mobile'];
			$cnid->mdata['connote'] = empty($_POST['id_connote'])? '' : $_POST['id_connote'];
			if(!$cnid->save()) $this->log('Saving error, '.print_r($cnid->getErrors(), true));
		}

		$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR;

		foreach($_FILES as $k=>$f){
			$fd = base64_decode(preg_replace('/data:image\/[^;]+;base64,/', '', file_get_contents($f['tmp_name'])));
			if(strlen($fd) < 100) continue;
			$tf = tempnam($td, "idp");
			file_put_contents($tf, $fd);
			$img = AppHelper::resizeImg($tf, 600);
			if($img) @imagejpeg($img, $tf);
			$fn = $cnid->no.'_'.($k=='front'? 1 : 2).'.jpg';
			$fid = FileRepo::storeFile($tf, $fn, 60, $cnid->id);
			if(empty($fid)){
				copy($_FILES['file']['tmp_name'], Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'error_upload'.DIRECTORY_SEPARATOR.$fn);
				die('FAILED');
			}elseif($this->debug){
				$this->log('Saved: '.$fid);
			}
			unlink($tf);
			$cnid->{$k} = $fid;
		}

		if($cnid->front > 0 && $cnid->back > 0){
			$cnid->nolog = false;
			$cnid->status = 15;
			$cnid->save();
			$cnid->refresh();
			$cnid->joinPhoto();
			$cnid->save();
			$cnid->matchCnee();
		}
		echo 'DONE';
	}

	public function actionYoutuAuth(){
		echo CnID::youtuAuth();
	}

	public function log($m){
		$lf = Yii::app()->basePath.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'upload-id.log';
		@file_put_contents($lf, date('Y-m-d H:i:s').' '.$m."\n", FILE_APPEND);
	}

}