<?php

class ReportController extends Controller
{

	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionExport()
	{
		switch ($_GET['type']) {
			case 'consumer-backorder': {
				Pcaw::getConsumerBackorder(Yii::app()->user->org, true);
			}
			break;
			case 'retailer-backorder': {
				Pcaw::getRetailerBackorder(Yii::app()->user->org, true);
			}
			break;
			case 'goods-return': {
				Pcaw::getGoodsReturn(Yii::app()->user->org, true);
			}
			break;
			case 'minimum-stock-alert': {
				Pcaw::getMinimumStockAlert(Yii::app()->user->org, true);
			}
			break;
			case 'delivery-despatch-consumer': {
				Pcaw::getDeliveryDespatchConsumer(Yii::app()->user->org, true);
			}
			break;
			case 'delivery-despatch-retailer': {
				Pcaw::getDeliveryDespatchRetailer(Yii::app()->user->org, true);
			}
			break;
			case 'inbound-delivery-advice': {
				Pcaw::getInboundDeliveryAdvice(Yii::app()->user->org, true);
			}
			break;
			case 'inbound-delivery-report': {
				Pcaw::getInboundDeliveryReport(Yii::app()->user->org, true);
			}
			break;
		}
	}

	public function actionExportSingle()
	{
		switch ($_GET['type']) {
			case 'consumer-backorder': {
				Pcaw::getConsumerBackorder(Yii::app()->user->org, true, $_GET['id']);
			}
			break;
			case 'retailer-backorder': {
				Pcaw::getRetailerBackorder(Yii::app()->user->org, true, $_GET['id']);
			}
			break;
			case 'delivery-despatch-consumer': {
				Pcaw::getDeliveryDespatchConsumer(Yii::app()->user->org, true, $_GET['id']);
			}
			break;
			case 'delivery-despatch-retailer': {
				Pcaw::getDeliveryDespatchRetailer(Yii::app()->user->org, true, $_GET['id']);
			}
			break;
			case 'inbound-delivery-advice': {
				Pcaw::getInboundDeliveryAdvice(Yii::app()->user->org, true, $_GET['id']);
			}
			break;
			case 'inbound-delivery-report': {
				Pcaw::getInboundDeliveryReport(Yii::app()->user->org, true, $_GET['id']);
			}
			break;
			case 'goods-return': {
				Pcaw::getGoodsReturn(Yii::app()->user->org, true, $_GET['id']);
			}
			break;
			case 'minimum-stock-alert': {
				Pcaw::getMinimumStockAlert(Yii::app()->user->org, true, $_GET['id']);
			}
			break;
		}
	}

	public function actionSetting()
	{
		$org = Org::model()->findByPk(Yii::app()->user->org);
		if (empty($_POST)) {
			$this->render('setting', ['org' => $org]);
		} else {
			unset($_POST['yt0']);
			foreach ($_POST as $k => $v) {
				$org->extra[$k] = $v;
			}
			$org->save();

			$this->ajaxResult($org);
		}
	}
	
	public function actionConsol(){
		$strTbody='';
		$this->render('report_consol',['strTbody'=>$strTbody]);
	}
	
	public function actionConsolReportSearch(){
		$strFrom = $_GET['from'];
		$strTo = $_GET['to'];
		$listStatus = [];
		$strConsolId ='';
		$listAirSea = [];
		if(!empty($_GET['air_sea'])){
			$listAirSea[] = $_GET['air_sea'];
		}
		$strOwnerId = Org::model()->findByPk(Yii::app()->user->org)->code;
		// $strOwnerId = '';
		$strState = $_GET['state'];
		$strByUrgent = $_GET['by_urgent'];
		
		foreach($_GET as $k=>$v){
			if(in_array($k,ImcoConsol::$states)){
				$listStatus[] = $v;
			}
			if(in_array($k,ImcoConsol::$services)){
				$listAirSea[]=$v;
			}
		}
		
		$objModelReport = new ModelReport($strFrom,$strTo,$listStatus,$listAirSea,$strConsolId,$strOwnerId,$strState,$strByUrgent,null);
		$listRecord = $objModelReport->funcRecords();
		
		// $this->renderPartial('report_consol_table_content',['listRecord'=>$listRecord]);
		echo $objModelReport->funcListRecords2Tbody($listRecord);
	}

}