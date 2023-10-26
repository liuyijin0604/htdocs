<?php
class DownloadIDController extends PController{
	protected $debug = false;

	public function actionIndex(){
		if(empty($_POST['nos']) || empty($_POST['hash'])){
			throw new CHttpException(400, 'Bad Request');
		}
		$hash = sha1($_POST['nos'].'pca168Cnid!@');
		if($hash != $_POST['hash']){
			throw new CHttpException(401, 'Authentication error');
		}
		$nos = json_decode($_POST['nos'], true);

		$zf = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.$_POST['consol'].'-'.strtoupper($_GET['type']).(empty($_GET['joint'])? '' : '-joint').date('YmdHi').(empty($_GET['alt'])? '' : '_alt').'.zip';
		$zipName = '';
		$zip = new ZipArchive;
		$zip->open($zf, ZipArchive::CREATE);
		$copied = [];
		$td = Yii::app()->basePath.DIRECTORY_SEPARATOR."runtime".DIRECTORY_SEPARATOR.'zip'.time();
		if(!file_exists($td)) mkdir($td);

		if(!isset($_GET['joint'])) $_GET['joint'] = 0;
		$sum = $_GET['joint'] == 1? ['HBN,Ref,ID #,Joint file']: ['HBN,Ref,ID #,Front file,Back file'];

		foreach($nos as $r){
			$cnid = empty($r[2])? null : CnID::model()->find('no = :n', [':n' => $r[2]]);
			if(!empty($cnid)){
				$cnid_new = !in_array($cnid->id, $copied);
				$_sum = [$r[0], $r[1], '="'.$cnid->no.'"'];
				if($_GET['joint'] == 1){
					if(empty($cnid->joint)){
						$cnid->joinPhoto();
						$cnid->refresh();
					}
					if($cnid->photo_joint){
						$jn = $cnid->no.'.jpg';
						if($cnid_new) $zip->addFile($cnid->photo_joint->getFile(), $jn);
						$_sum[] = $jn;
					}
				}else{
					if($cnid->photo_front){
						$frtname = $cnid->no.'_1.jpg';
						if($cnid_new) $zip->addFile($cnid->photo_front->getFile(), $frtname);
						$_sum[] = $frtname;
					}
					if($cnid->photo_back){
						$backname = $cnid->no.'_2.jpg';
						if($cnid->photo_front->hash == $cnid->photo_back->hash){//add same file by string
							if($cnid_new) $zip->addFromString($backname, file_get_contents($cnid->photo_back->getFile()));
						}else{
							if($cnid_new) $zip->addFile($cnid->photo_back->getFile(), $backname);
						}
						$_sum[] = $backname;
					}
				}
				$sum[] = implode(',', $_sum);
				$copied[] = $cnid->id;
			}else{
				$sum[] = implode(',', [$r[0], $r[1], empty($r[2])? '' : '="'.$r[2].'"']);
			}
		}

		$zip->addFromString('_Sum.csv', implode("\n", $sum));

		$zip->close();
		if(!is_file($zf)) throw new CHttpException(400, 'File is empty');

		header("Cache-Control: maxage=1");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header('Content-Disposition: attachment; filename="'.(empty($zipName)? basename($zf) : $zipName).'"');
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($zf));
		readfile($zf);
		unlink($zf);
		AppHelper::unlinkRecursive($td);
		unlink($pid);
		Yii::app()->end();
	}
}