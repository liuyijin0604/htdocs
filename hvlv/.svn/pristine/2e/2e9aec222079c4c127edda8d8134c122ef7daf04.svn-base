<?php

class ConsolReportController extends Controller{
	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax=array('export');

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex(){
		$this->render('index');
	}

	public function actionView($id){
		if(empty($_GET['t'])) return;

		if($_GET['t'] == 'pl'){
			$model = PickupList::model()->findByPk($id);
			$this->render('pl', ['model' => $model]);
		}
	}

	public function actionExport($id){
		if(empty($_GET['t'])) return;

		if($_GET['t'] == 'pl'){
			$model = PickupList::model()->findByPk($id);

			$xls = new oExcel;
			$i = 1;
			$xls->addRow($i++, array($model->ref, $model->created));
			$xls->addRow($i++, array('Connote', 'Cust Ref', 'Status', 'Sender', 'Sender Tel', 'Cnee Name', 'Cnee Tel', 'Address', 'State', 'Weight'));

			foreach($model->lines as $l){
				$r = $l->mm();
				$xls->addRow($i++, array('="'.$r->hbn.'"', empty($r->cref)? '' : '="'.$r->cref.'"', $r->getStatus(), $r->cnor->name, '="'.$r->cnor->tel.'"', $r->cnee->name, '="'.$r->cnee->tel.'"', $r->cnee->address, $r->cnee->state, $r->weight));
			}
			$xls->output('pickup_'.$model->ref.'.xlsx');
		}
	}
	
	
	public function actionConsol(){
		// if(in_array(User::currentUserOrgId(),[Org::ORGID_COURIER_UBI_CLIENT,Org::ORGID_CLIENT_SF,Org::ORGID_CLIENT_ZTO]))
		// {
		// 	$this->actionImClientConsolReport();
		// 	return;
		// }

		$strTbody='';
		$this->render('report_consol',['strTbody'=>$strTbody]);
	}
	
	private function getConsolReportModel()
	{

		$strFrom = $_GET['from'];
		$strTo = $_GET['to'];
		$listStatus = [];
		$strConsolId ='';
		$listAirSea = [];
		$strOrgId ='';
		if(!empty($_GET['air_sea'])){
				$listAirSea[] = $_GET['air_sea'];
		}
		if(!empty($_GET['client_name'])){
           $strOrgId = $_GET['client_name'];
		}
		$strOwnerId = Org::model()->findByPk(Yii::app()->user->org)->code;
		//$strOwnerId = '';
		$strState = $_GET['state'];
		$strByUrgent = $_GET['by_urgent'];
		$strAwb = $_GET['awb_or_container_id'];
		foreach($_GET as $k=>$v){
			if(in_array($k,ImcoConsol::$states)){
					$listStatus[] = $v;
				}
				if(in_array($k,ImcoConsol::$services)){
					$listAirSea[]=$v;
				}
		}	
	    
	    $objModelReport = new ModelReport($strFrom, $strTo, $listStatus, $listAirSea, $strConsolId, $strOwnerId, $strState, $strByUrgent, null, $strAwb, $strOrgId);
	    $listRecord = $objModelReport->funcRecords();
	    return [$listRecord, $objModelReport];
	}
    public function actionConsolReportSearch()
    {
	   [$listRecord, $objModelReport] = $this->getConsolReportModel();

       // Render the view with the search results
       echo $objModelReport->funcListRecords2Tbody($listRecord,$isShowButton =true);
    
    
    }

   public function actionConsolReportExport()
	{
	    [$listRecord, $objModelReport] = $this->getConsolReportModel();
	    $dicPort2State = [
			'AUSYD' => 'NSW',
			'AUMEL' => 'VIC',
			'AUBNE' => 'QLD',
			'AUPER' => 'WA',
			'AUADL' => 'SA',
			'AUFRE' => 'WA',

		]; 
	    $xls = new oExcel;
	    $i = 1;
	    $xls->addRow($i++, ['State', 'Air/Sea', 'awb/Ocean Bill', 'ETD', 'ETA', 'Checkin', 'Arrived at Airport', 'Collected', 'Arrived at TLA', 'Unloaded at TLA', 'Unpack', 'Despatch']);
	    foreach ($listRecord as $Record) {

	    	$state = isset($dicPort2State[$Record->pod]) ? $dicPort2State[$Record->pod] : '';
	    	$service = ($Record->service == 10) ? 'air' : (($Record->service == 20) ? 'sea' : '');
	        $xls->addRow($i++, [empty($state) ? '' : $state, 
	        empty($service) ? '' : $service, 
	        empty($Record->awb) ? '' : $Record->awb,
		    ($Record->etd ? date("d/m/Y H:i", strtotime($Record->etd)) : ''),
		    ($Record->eta ? date("d/m/Y H:i", strtotime($Record->eta)) : ''),
		    ($Record->checkin ? date("d/m/Y H:i", strtotime($Record->checkin)) : ''),
		    is_array($Record->cto_start) ? implode(', ', $Record->cto_start) : $Record->cto_start,
		    is_array($Record->cto_finish) ? implode(', ', $Record->cto_finish) : $Record->cto_finish,
		    is_array($Record->client_start) ? implode(', ', $Record->client_start) : $Record->client_start,
		    is_array($Record->client_finish) ? implode(', ', $Record->client_finish) : $Record->client_finish,
		    is_array($Record->unpack) ? implode(', ', $Record->unpack) : $Record->unpack,
		    !empty($Record->gatepasstime) ? date("d/m/Y H:i", strtotime($Record->gatepasstime)) : ''
	    ]);
	    }
	    $filename = 'consol_search_export_' . time() . '.xlsx';
	    $xls->output($filename);

   }

    public function generateTimeHTML($times) {
	    $count = count($times);
	    $html = '<td>';
	    foreach ($times as $key => $time) {
	        if ($time != null) {
	            $html .= '<span>' . date("d/m/Y H:i", strtotime($time)) . '</span>';
	        } else {
	            $html .= '<span style="display: inline-block; width: 100%; height: 20px; margin-bottom: 10px;">&nbsp;</span>';
	        }
	        if ($key < $count - 1) {
	            $html .= '<hr style="border-top: 1px dashed #333; margin: 5px 0;">';
	        }
	    }
	    $html .= '</td>';
	    return $html;
}


	public function actionImClientConsolReport()
	{
		$filtersForm = new FiltersForm;
		$provide = [];
		$model = new ImcoConsol('search');
		$model->unsetAttributes();

		$filteredData = $filtersForm->filter([]);
		$dataprovider = new CArrayDataProvider($filteredData);
		$this->render('im_client_consol_rpt', ['model' => $dataprovider, 'filter' => $filtersForm]);
	}


	public function actionAjaxExportImClientConsolReport()
	{
		[$provide,$sortAttributes,$sortNumberAttributes] = $this->_funImClientConsolReport();
		$xls = new oExcel;
		$i = 1;
		$xls->addRow($i++, $sortAttributes);
		foreach ($provide as $key => $d) {
			$xls->addRow($i++,[$d['mawb'],$d['dest'],$d['pkg'],$d['eta'],$d['ata'],$d['customs_cleared'],$d["recovery_date"],$d["hand_over_aup"],$d["hand_over_pp"]]);
		}


		$tempfile = Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR . 'im_client_consol_report_result'.User::getCurrentUser()->org_id.'.xlsx';

		$xls->output($tempfile, null, false);
		echo $this->createUrl('reports/downloadImClientConsolReportResult',['id'=>User::getCurrentUser()->org_id]);
	}

	public function actionAjaxImClientConsolReport()
	{
		$reportService = new ReportService();
		$filtersForm = new FiltersForm;
				if (isset($_GET['FiltersForm'])) {
			$filtersForm->filters = $_GET['FiltersForm'];
		}
		[$provide,$sortAttributes,$sortNumberAttributes] = $this->_funImClientConsolReport();
		$dataprovider = $reportService->preDataProvider($filtersForm, $provide, $sortAttributes, $sortNumberAttributes);

		$this->renderPartial('im_pl_rpt_partial', ['model' => $dataprovider, 'filter' => $filtersForm, 'total' => 0, 'attributes' => $sortAttributes, 'model2' => @$dataprovider2], false, true);
	}

	private function _funImClientConsolReport()
	{
		$reportService = new ReportService();
		return $reportService->_funImClientConsolReport();
	}

	public function actionDownloadImClientConsolReportResult($id)
	{
		$this->downloadReportFile('im_client_consol_report_result'.$id.'.xlsx');
	}

	private function downloadReportFile($filename)
	{
		$tempfile = Yii::app()->basePath . DIRECTORY_SEPARATOR . "runtime" . DIRECTORY_SEPARATOR . $filename;
		header("Cache-Control: maxage=1");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=\"" . urldecode(basename($tempfile)) . '"');
		header("Content-Transfer-Encoding: binary");
		readfile($tempfile);
		unlink($tempfile);
		Yii::app()->end();
	}

	public function actionSearchNameValidationUsage()
	{
		$res = new stdClass;
		if (!empty($_POST)) {
			$res->success = true;
			$res->records = [];
			$fromDate = !empty($_POST['from']) ? date('Y-m-d 00:00:00', strtotime($_POST['from'])) : '0000-00-00 00:00:00';
			$toDate = !empty($_POST['to']) ? date('Y-m-d 23:59:59', strtotime($_POST['to'])) : date('Y-m-d H:i:s');

			$sql = 'SELECT * FROM word_replace_usage_log WHERE org_id = ' . Yii::app()->user->org . ' AND process_time >= "' . $fromDate . '" AND process_time <= "' . $toDate . '"';
			$usageRecords = WordReplaceUsageLog::model()->findAllBySql($sql);
			if (!empty($usageRecords)) {
				foreach ($usageRecords as $record) {
					$tempObj = new stdClass;
					$imParcel = ImParcel::model()->findByPk($record->shipment_id);
					$tempObj->ref = !empty($imParcel->ref) ? $imParcel->ref : '';
					$tempObj->consolNo = !empty($imParcel->consol->no) ? $imParcel->consol->no : '';
					$tempObj->original_word = $record->original_word;
					$tempObj->replace_word = !empty($record->replace_word) ? $record->replace_word : ' ';
					$tempObj->process_time = $record->process_time;
					array_push($res->records, $tempObj);
				}
				$res->wordsCount = count($usageRecords);
				$res->amount = number_format($res->wordsCount*0.1, 2, '.', ' ');
			} else {
				$res->message = 'No result found.';
			}
		}
		echo json_encode($res);
	}
}