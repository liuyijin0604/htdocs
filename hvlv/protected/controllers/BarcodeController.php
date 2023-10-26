<?php

class BarcodeController extends PController{
	
	public $lib_path;

	public function init(){
		$this->lib_path = Yii::app()->basePath.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'barcode'.DIRECTORY_SEPARATOR;
		parent::init();
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionDraw(){
		Yii::import('application.libs.tcpdf.tcpdf_barcodes_1d', true);
		$options = array(
			'scale' => empty($_GET['scale'])? 2 : $_GET['scale'],
			'height' => empty($_GET['height'])? 80 : $_GET['height'],
		);
		$bc = new TCPDFBarcode($_GET['text'], $_GET['code']);
		$bc->getBarcodeSVG($options['scale'], $options['height'], 'black');
	}
	
	public function actionQr(){
		Yii::import('application.libs.tcpdf.tcpdf_barcodes_2d', true);
		$qr = new TCPDF2DBarcode($_GET['url'], 'QRCODE,H');
		header('Content-Type: image/svg+xml');
		$qr->getBarcodeSVG(6, 6, 'black');
	}
}