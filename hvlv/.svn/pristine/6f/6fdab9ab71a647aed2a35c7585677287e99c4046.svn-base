<?php
class ClientController extends Controller{

	public $fullLayout = 'client';

	protected $skipAcl = ['help', 'shipments', 'done', 'connote', 'print', 'label', 'tracking', 'edoPay', 'edoLabel'];

	public function init(){
		Yii::app()->language = 'zh_cn';
	}

	public function filters() {
		return array('ajaxOnly');
	}

	public function actionHelp(){
		$this->render('help');
	}

	public function actionDone($id){
		$model = ExParcel::model()->findByPk($id);
		if(empty($model) || empty($_GET['c']) || $model->hbn != $_GET['c']) throw new CHttpException(404, 'Page not found!');
		$this->render('done', ['model' => $model]);
	}

	public function actionEdoPay($id){
		$model = ExDirect::model()->findByPk($id);
		if(empty($model) || empty($_GET['c']) || $model->hbn != $_GET['c']) throw new CHttpException(404, 'Page not found!');
		$this->render('edopay', ['model' => $model]);
	}
	
	public function actionPrint($id){
		$model = ExParcel::model()->findByPk($id);
		if(empty($model) || empty($_GET['c']) || $model->hbn != $_GET['c']) throw new CHttpException(404, 'Page not found!');
		spl_autoload_unregister(array('YiiBase', 'autoload')); // Disable Yii autoloader
		Yii::import('application.libs.PrintNode.Loader', true);
		PrintNode_Loader::init();
		spl_autoload_register(array('YiiBase', 'autoload')); // Re-enable Yii autoloader
		$credentials = new PrintNode_Credentials('HVLV.5062', '414aebec72fd6b720bd36ce4b217db8816f1a945');
		$request = new PrintNode_Request($credentials);
		$printJob = new PrintNode_PrintJob();
		$printJob->printer = $model->agent->extra['printer_id'];
		$printJob->contentType = 'pdf_uri'; 
		$printJob->content = Yii::app()->request->hostInfo.substr($this->createUrl('client/connote'),0,-5).'/'.$id.'/'.$model->hbn.'.pdf';
		$printJob->source = 'HVLV POS';
		$printJob->title = $model->hbn;
		$response = $request->post($printJob);
		$this->ajaxResult($model);
	}

	public function actionConnote($id, $fn){
		$model = ExParcel::model()->findByPk($id);
		if(empty($model) || $model->hbn != $fn || $model->status > 8) throw new CHttpException(404, 'Page not found!');
		if($model->status == 8){
			$model->status = 9;
			$model->save();
		}
		oPDF::renderPDF('label_A6', array('tpl' => '_label-ex', 'empty' => false, 'rs' => [$model]));
	}
	
	public function actionLabel($id, $fn){
		$model = ExParcel::model()->findByPk($id);
		if(empty($model) || $model->hbn != $fn) throw new CHttpException(404, 'Page not found!');
		if(!empty($_GET['save'])){
			header('Content-Disposition: attachment; filename="'.$model->hbn.'.jpg"');
		}
		oPDF::renderImage('label_A6', array('tpl' => '_label-ex', 'empty' => false, 'rs' => [$model]));
	}
	
	public function actionEdoLabel($id, $fn){
		$model = ExDirect::model()->findByPk($id);
		if(empty($model) || $model->hbn != $fn) throw new CHttpException(404, 'Page not found!');
		if(!empty($_GET['save'])){
			header('Content-Disposition: attachment; filename="'.$model->hbn.'.jpg"');
		}
		oPDF::renderImage('label_A6', array('tpl' => '_label-edo', 'empty' => false, 'rs' => [$model]));
	}
	
	public function actionStatus(){
		$o = [];
		foreach($_POST['s'] as $id => $hbn){
			$p = ExParcel::model()->findByPk($id);
			if(empty($p) || $p->hbn != $hbn) continue;
			$o[$id] = $this->t($p->getStatus());
		}
		echo json_encode($o);
	}

	public function actionShipments(){
		$this->render('shipments');
	}

	public function actionTracking(){
		$this->forward('/pos/shipment/tracking');
	}

	public function registerJS($js,$id=1){
		Yii::app()->clientScript->registerScript($this->getId().$id, preg_replace('/<script[^>]+>(.+)<\/script>/ms', '\\1',$js));
	}
}