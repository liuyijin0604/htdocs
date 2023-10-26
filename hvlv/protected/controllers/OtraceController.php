<?php

class OtraceController extends PController{

	public function actionIndex($ref){
		$p = ExParcel::model()->find('hbn = :h', [':h' => $ref]);
		if(!empty($p)){
			$o = OriginTrace::model()->find('pid = :pid', [':pid' => $p->id]);
			if(!empty($o)){
				//log
				Log::add($o, 2, array(
					'ip' => $_SERVER['REMOTE_ADDR'],
					'agent' => empty($_SERVER['HTTP_USER_AGENT'])? '' : $_SERVER['HTTP_USER_AGENT'],
				));

				//redirect
				switch($o->pvdr){
					case 10:
						$this->redirect('http://ccicorigin.com/express/'.$o->no);
					break;
				}
			}
		}
		throw new CHttpException(404, 'Page not found!');
	}

	public function actionQr($ref){
		Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
		$qr = new TCPDF2DBarcode('https://ot.pcaex.com/'.$ref, 'QRCODE,H');
		$qrimg = imagecreatefromstring($qr->getBarcodePngData(3, 3));
		$sldimg = imagecreatefrompng(dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'images/ccic_shield.png');
		$cloimg = imagecreatefrompng(dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'images/ccic_qrover.png');
		imagealphablending($sldimg, true);
		imagesavealpha($sldimg, true);
		imagecopy($sldimg, $qrimg, 42, 28, 0, 0, 100, 100);
		imagecopy($sldimg, $cloimg, 72, 58, 0, 0, 100, 100);
		header('Content-Type: image/png');
		imagepng($sldimg);
	}

}