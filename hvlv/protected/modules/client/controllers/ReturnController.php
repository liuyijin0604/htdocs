<?php

class ReturnController extends PController
{
	public $layout = 'client';

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 /client/return?c=AMQ5511441&h=17da4592
	 */
	public function actionIndex(){
		$model = $this->getShipment();

		$this->render('index', ['model' => $model]);
	}

	public function actionPrint(){
		$model = $this->getShipment();

		oPDF::renderPDF('label_A6', ['rs' => [$model]], 1, $model->ref . '.pdf');
	}

	public function getShipment(){
		if(empty($_GET['c']) || empty($_GET['h']) || hash('crc32b', $_GET['c'].'#pca3plReturn$') != $_GET['h']){
			throw new CHttpException(404, 'Return not found.');
		}
		return Shipment::model()->find('ref = :r', [':r' => $_GET['c']]);
	}
}