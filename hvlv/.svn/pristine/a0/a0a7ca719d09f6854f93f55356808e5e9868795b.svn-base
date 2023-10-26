<?php

class WarehouseProcessController extends WhscanController
{
	/**
	 * Declares class-based actions.
	 */
	protected $nonAjax = ['index','genYlwLabel'];
	protected $skipAcl = ['genYlwLabel'];

	protected $org;
	protected $type;
	protected $warehouseProcessService;

	public function beforeAction($action)
	{
		if(!Yii::app()->request->isAjaxRequest) $this->layout = 'whscan';
		if (!Yii::app()->user->isGuest) {
			$this->org = Org::model()->findByPk(Yii::app()->user->org);
			$this->type = 'sc';
		}
		$this->warehouseProcessService = new WarehouseProcessService();
		return parent::beforeAction($action);
	}
	
	public function actionIndex()
	{
		$this->render('index',[]);
	}

	public function actionProcessPage()
	{
		switch ($_GET['tab']) {
			case 'check_in':
				$op ='checkin';
				$barcode =  isset($_GET['barcode']) ? $_GET['barcode'] : '';
				$this->render('check_in_consol_list', ['op' => $op,'barcode'=>$barcode]);
				break;
			case 'inspection':
				$this->render('inspection_list',[]);
				break;
			case 'putaway_sort_held':
				$this->render('putaway_sort_held_list',[]);
				break;
			case 'preparation':
				$this->render('preparation_list',[]);
				break;
			case 'gatepass_sign':
				$this->render('gatepass_sign_list',[]);
				break;
			case 'held_shipment_resorting':
				$this->render('held_shipment_resorting_list',[]);
				break;
			case 'unlink_pallet':
				$this->render('unlink_pallet', []);
				break;
			case 'pallet_count_update':
				$this->render('pallet_count_update', []);
				break;
			case 'unknown_shipment':
				$model = new ImportsUnknownShipment("search");
				$model->unsetAttributes();
				if(!empty($_GET['ImportsUnknownShipment']))
				{
					$model->setAttributes($_GET['ImportsUnknownShipment']);
				}
				$model->dpt_id = $this->getDptId();
				$this->render('unknown_shipment',['model'=>$model]);
				break;
			case 'today_held_scan_check':
				
				$rs = $this->warehouseProcessService->getTodayHeldShipments($this->getDptId());
				$filtersForm=new FiltersForm;
				$filteredData=$filtersForm->filter($rs);
				$dataProvider=new CArrayDataProvider($filteredData);
				$dataProvider->pagination=['pageSize' => 10000];
				$this->render('today_held_scan_check',['dataProvider'=>$dataProvider,'filter'=>$filtersForm]);
				break;

			case 'scan_surplus':
				$this->render('scan_surplus',[]);
				break;
			case 'surplus_list':
				$model = new ImportsUnknownShipment("search");
				$model->unsetAttributes();
				if(!empty($_GET['ImportsUnknownShipment']))
				{
					$model->setAttributes($_GET['ImportsUnknownShipment']);
				}
				$model->type = ImportsUnknownShipment::SURPLUS;
				$model->dpt_id = $this->getDptId();
				$this->render('surplus_list',['model'=>$model]);
				break;

			default:
				# code...
				break;
		}
	}

	public function actionGetCheckInReport()
	{
		if(!empty($_GET['path']))
		{
			$pathArr = explode('/', $_GET['path']);
			$pathCount = count($pathArr);
			if($pathCount==2)
			{
				$result = $this->warehouseProcessService->getCheckInReportForConsol($pathArr[1]);
				$str2 = '';
				foreach ($result as $key => $r) {
					$str3="";
					$heldStr = "";
					// foreach ($r as $key2 => $value) {
					// 	if($key2!="courierId"&&$key2!="Courier"&&!preg_match('/held/i', $key2))
					// 	{
					// 		$str3.=" {$key2}:{$value}&nbsp;&nbsp;";
					// 	}
					// }
					if($r['Held_Total']>0)
					{
						$str3=" {$r['Courier']}:{$str3}&nbsp;&nbsp;Held:<font color='red'>".$r['Held_Total']."</font>";
						$strSearch = "";
						$strSearch = '<span class="exp" data-path="'.$this->getDptId().'/'.$pathArr[1].'/'.$r['courierId'].'/55" data-loaded="0"></span>';
						$str2.='<li><p>'.$strSearch.$str3.'</p><ul></ul></li>';
						$str3=" {$r['Courier']}:&nbsp;&nbsp;Left:".$r['Unscaned'];
					}else
					{
						$str3=" {$r['Courier']}:{$str3}&nbsp;&nbsp;Left:".$r['Unscaned'];
					}

					$strSearch = "";
					if($r['Unscaned']>0||$r['Oversize']>0||$r['Held_Scan']>0||$r['Held_Total']>0)
					{
						$strSearch = '<span class="exp" data-path="'.$this->getDptId().'/'.$pathArr[1].'/'.$r['courierId'].'/60" data-loaded="0"></span>';
					}else
					{
						$strSearch = 'Done:';
					}
					$str2.='<li><p>'.$strSearch.$str3.'</p><ul></ul></li>';
				}
				$str = "<li><ul>".$str2."</ul></li>";
				echo $str;
				return;
			}elseif($pathCount==4)
			{
				[$header,$result,$percent] = $this->warehouseProcessService->getCheckInReportForConsolCourier($pathArr[1],$pathArr[2],$pathArr[3]);
				echo $this->warehouseProcessService->ajaxPLchart($header,$result,$percent);
				return;
			}
		}
	
		$result = $this->warehouseProcessService->getCheckInReport($this->getDptId());
		$totalArr = $result[0];
		$resultArr = $result[1];
		$str2 = '';
		$today = date("Y-m-d");
		foreach ($resultArr as $key => $r) {
			$strHeld1 = "{$r[7]}";
			$strHeld2 = "{$r[8]}";
			if($r[5]>0)
			{
				$strHeld1 = "<font color='red'>{$strHeld1}</font>";
			}
			if($r[6]>0)
			{
				$strHeld2 = "<font color='red'>{$strHeld2}</font>";
			}
			$blank = "";
			$strLen = 2*strlen($r[0])+13;
			for ($i=0; $i < $strLen; $i++) { 
				$blank .="&nbsp;";
			}
			$strNotCleared = "";
			$strNotCleared = "<tr><td>{$blank}</td><td>Not Cleared--";
			$strNotCleared .="&nbsp;&nbsp;Held Left HBL:&nbsp;{$strHeld2}&nbsp;&nbsp;/&nbsp;&nbsp;PKG:{$strHeld1}</td></tr>";
			foreach ($r[9] as $nck=> $ncv) {
				$strNotCleared.="<tr><td>{$blank}</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
				ImParcel::$states[$ncv['status']].": {$ncv['shipments']}&nbsp;&nbsp;</td></tr>";
			}
			$awbStr = $r[0];
			if(in_array($r[10],[Org::ORGID_COURIER_UBI_CLIENT,Org::ORGID_CLIENT_EWE_ITEM,Org::ORGID_CLIENT_EWE_KG,Org::ORGID_CLIENT_EWE_GROUP,Org::ORGID_CLIENT_DAIPOST,Org::ORGID_CLIENT_GLOBAVEND]))
			{
				if((strtotime($r[11])<time()&&HolidayHelper::hourDiffBetweenTwoDays(date("Y-m-d"),$r[11])>=48)&&$r[2]!=$r[4])
				{
					$awbStr="<font color='red' style='font-weight:bold'>{$awbStr}</font>";
				}
			}else
			{
				if((strtotime($r[11])<time()&&HolidayHelper::hourDiffBetweenTwoDays(date("Y-m-d"),$r[11])>=72)&&$r[2]!=$r[4])
				{
					$awbStr="<font color='red' style='font-weight:bold'>{$awbStr}</font>";
				}
			}

			$str3=$awbStr."&nbsp;--&nbsp;</td><td>Total HBL:&nbsp;{$r[1]}&nbsp;&nbsp;/&nbsp;&nbsp;PKG:{$r[2]}&nbsp;&nbsp;<font style='font-weight:bold'>Weight:{$r[12]}"."</font><table>
					<tr><td>{$blank}</td><td>Done HBL: {$r[3]}&nbsp;&nbsp;/&nbsp;&nbsp;PKG:&nbsp;{$r[4]}</td></tr>
					<tr><td>{$blank}</td><td>Left HBL: {$r[5]}&nbsp;&nbsp;/&nbsp;&nbsp;PKG:&nbsp;{$r[6]}</td></tr>".$strNotCleared;
			$str3.="</table>";
			$str2.='<li><p style="margin:0 0 0px;"><span class="exp" data-path="'.$this->getDptId().'/'.$key.'" data-loaded="0"></span> '.$str3.'</p><ul></ul></li>';
		}
		$str = "<li><h2>{$today}</h2><p>Total - Consols:{$totalArr['consols']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total:&nbsp;{$totalArr['pkg']}&nbsp;&nbsp;&nbsp;&nbsp;{$totalArr['totalWeight']}t&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Done:&nbsp;{$totalArr['scanCount']}&nbsp;&nbsp;&nbsp;&nbsp;{$totalArr['totalScanWeight']}t({$totalArr['scanPercent']})</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Left:&nbsp;{$totalArr['left']}&nbsp;&nbsp;&nbsp;&nbsp;{$totalArr['leftWeight']}t</p><p>Details:</p><ul>".$str2."</ul></li>";

		echo $str;

		// echo '<li><p>Total - Consols: 1,433,071.93 &nbsp;&nbsp; GP: 626,875.90 &nbsp;&nbsp; Cost: 720,256.84 + 85,939.19 &nbsp;&nbsp; Margin: 43.74%</p><ul><li><p><span class="exp" data-path="106/2022-03-01/2022-03-31//10" data-loaded="0"></span> Import &nbsp;&nbsp; Rev: 1,228,824.68 &nbsp;&nbsp; GP: 430,633.01 &nbsp;&nbsp; Cost: 712,252.48 + 85,939.19 &nbsp;&nbsp; Margin: 35.04%&nbsp;</p><ul></ul></li><li><p><span class="exp" data-path="106/2022-03-01/2022-03-31//40" data-loaded="0"></span> 3PL &nbsp;&nbsp; Rev: 184,127.83 &nbsp;&nbsp; GP: 176,258.19 &nbsp;&nbsp; Cost: 7,869.64 + 0.00 &nbsp;&nbsp; Margin: 95.73%&nbsp;</p><ul></ul></li><li><p><span class="exp" data-path="106/2022-03-01/2022-03-31//50" data-loaded="0"></span> TopCourierService &nbsp;&nbsp; Rev: 20,119.42 &nbsp;&nbsp; GP: 19,984.70 &nbsp;&nbsp; Cost: 134.72 + 0.00 &nbsp;&nbsp; Margin: 99.33%&nbsp;</p><ul></ul></li></ul></li>';
	}

	public function actionGetPutawaySortHeldList()
	{
		if(!empty($_GET['path']))
		{
			$pathArr = explode('/', $_GET['path']);
			$pathCount = count($pathArr);
			if($pathCount==2)
			{
				if (isset($_GET['FiltersForm'])) {
					$filtersForm->filters = $_GET['FiltersForm'];
				}
				[$sortAttributes,$provide,$percent] = $this->warehouseProcessService->getPutawaySortHeldDetail($pathArr[0],$pathArr[1]);
				//echo $this->warehouseProcessService->ajaxPLchart($header,$result,$percent);
				$filtersForm = new FiltersForm;
				$reportService = new ReportService();
				$dataprovider = $reportService->preDataProvider($filtersForm, $provide, $sortAttributes);
				$this->renderPartial('grid_view',['model'=>$dataprovider, 'filter' => $filtersForm,'attributes' => $sortAttributes,'uid'=>join('',$pathArr)]);
				return;
			}
		}
	
		$result = $this->warehouseProcessService->getPutawaySortHeldList($this->getDptId());
		$totalArr = $result['total'];
		$today = date("Y-m-d");
		$str2 = '';
		foreach ($result as $key => $r) {
			if($key=='total') continue;
			$str3="";
			foreach ($r as $key2 => $value) {
				if($key2!="name")
				{
					$str3.=" {$key2}:{$value}&nbsp;&nbsp;";
				}
			}
			$str2.='<li><p><span class="exp" data-path="'.$this->getDptId().'/'.$key.'" data-loaded="0"></span> '.Yii::t('whscan',$r['name']).'&nbsp;&nbsp;'.$str3.'</p><ul></ul></li>';
		}
		$str = "<li><p><h2>{$today}</h2>".Yii::t('whscan','Put in Pallet')." and ".Yii::t('whscan','Put Away')." Summary Total - :{$totalArr['total']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Done:&nbsp;{$totalArr['done']}&nbsp;&nbsp;({$totalArr['donePercent']})</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Left:&nbsp;{$totalArr['left']}</p><p>Details:</p><ul>".$str2."</ul></li>";

		echo $str;
		// echo '<li><p>Total - Consols: 1,433,071.93 &nbsp;&nbsp; GP: 626,875.90 &nbsp;&nbsp; Cost: 720,256.84 + 85,939.19 &nbsp;&nbsp; Margin: 43.74%</p><ul><li><p><span class="exp" data-path="106/2022-03-01/2022-03-31//10" data-loaded="0"></span> Import &nbsp;&nbsp; Rev: 1,228,824.68 &nbsp;&nbsp; GP: 430,633.01 &nbsp;&nbsp; Cost: 712,252.48 + 85,939.19 &nbsp;&nbsp; Margin: 35.04%&nbsp;</p><ul></ul></li><li><p><span class="exp" data-path="106/2022-03-01/2022-03-31//40" data-loaded="0"></span> 3PL &nbsp;&nbsp; Rev: 184,127.83 &nbsp;&nbsp; GP: 176,258.19 &nbsp;&nbsp; Cost: 7,869.64 + 0.00 &nbsp;&nbsp; Margin: 95.73%&nbsp;</p><ul></ul></li><li><p><span class="exp" data-path="106/2022-03-01/2022-03-31//50" data-loaded="0"></span> TopCourierService &nbsp;&nbsp; Rev: 20,119.42 &nbsp;&nbsp; GP: 19,984.70 &nbsp;&nbsp; Cost: 134.72 + 0.00 &nbsp;&nbsp; Margin: 99.33%&nbsp;</p><ul></ul></li></ul></li>';
	}


	public function actionGetInspectionList()
	{
		$model = new ShipmentWhInspection('search');
		$model->unsetAttributes();

		if(!empty($_GET['ShipmentWhInspection']))
		{
			$model->setAttributes($_GET['ShipmentWhInspection']);
		}
		$model->dpt_id = $this->getDptId();

		$result = $this->warehouseProcessService->getInspectionTotalInfo($this->getDptId());
		$str2 = '';
		$today = date("Y-m-d");
		$str = "<li><p><h2>{$today}</h2>Total - :{$result['total']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Done:&nbsp;{$result['done']}&nbsp;&nbsp;({$result['donePercent']})</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Left:&nbsp;{$result['left']}</p><p>Details:</p></li>";

		echo $str;


		$this->renderPartial('_sub_inspection',['model'=>$model,'uid'=>$model->dpt_id."inspection"]);
		return;

	}

	public function actionDeleteInspectionFile()
	{
		$id = $_POST['id'];
		$file = FileRepo::model()->findByPk($id);
		$file->status = FileRepo::DELETED;
	}

	public function actionGetGatePassSignList()
	{
		
		$result = $this->warehouseProcessService->getGapsignSummeryList($this->getDptId(),false,false,false,true);
		$totalArr = $result['total'];
		unset($result['total']);
		$str2 = '';
		$today = date("Y-m-d");
		$strTotal = "<li><p><h2>{$today}</h2>Total - :{$totalArr['total']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OnTime:&nbsp;{$totalArr['done']}&nbsp;&nbsp;({$totalArr['donePercent']})</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Late:&nbsp;{$totalArr['late']}</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Left:&nbsp;{$totalArr['left']}</p></li>";


		foreach ($result as $key => $r) {
			$str3="";
			foreach ($r as $key2 => $value) {
				if($key2!="courier"&&$value!=null)
				{
					$str3.=" {$key2}:{$value}&nbsp;&nbsp;";
				}
			}
			$str2.='<li><p> '.$str3.' <a href="'.Yii::app()->baseUrl.'/../gapsig/gatePass/index.app?direct='.$r['courier'].'&&dptId='.$this->getDptId().'" class="exp" target="_blank" style="display:inline-block">Sign '.$r['Courier'].'</a></p><ul></ul></br></li>';
		}
		$str = "{$strTotal}<li><p>Gatepass Sign Summary Group By Courier</p><ul>".$str2."</ul></li>";

		echo $str;

		// echo '<li><p>Total - Consols: 1,433,071.93 &nbsp;&nbsp; GP: 626,875.90 &nbsp;&nbsp; Cost: 720,256.84 + 85,939.19 &nbsp;&nbsp; Margin: 43.74%</p><ul><li><p><span class="exp" data-path="106/2022-03-01/2022-03-31//10" data-loaded="0"></span> Import &nbsp;&nbsp; Rev: 1,228,824.68 &nbsp;&nbsp; GP: 430,633.01 &nbsp;&nbsp; Cost: 712,252.48 + 85,939.19 &nbsp;&nbsp; Margin: 35.04%&nbsp;</p><ul></ul></li><li><p><span class="exp" data-path="106/2022-03-01/2022-03-31//40" data-loaded="0"></span> 3PL &nbsp;&nbsp; Rev: 184,127.83 &nbsp;&nbsp; GP: 176,258.19 &nbsp;&nbsp; Cost: 7,869.64 + 0.00 &nbsp;&nbsp; Margin: 95.73%&nbsp;</p><ul></ul></li><li><p><span class="exp" data-path="106/2022-03-01/2022-03-31//50" data-loaded="0"></span> TopCourierService &nbsp;&nbsp; Rev: 20,119.42 &nbsp;&nbsp; GP: 19,984.70 &nbsp;&nbsp; Cost: 134.72 + 0.00 &nbsp;&nbsp; Margin: 99.33%&nbsp;</p><ul></ul></li></ul></li>';
	}

	public function actionGetPreparationList()
	{
		if(!empty($_GET['path']))
		{
			$pathArr = explode('/', $_GET['path']);
			$pathCount = count($pathArr);
			if($pathCount==3)
			{
				$pa = explode(',', $pathArr[0]);
				$params = [];
				foreach ($pa as $k => $v) {
					if(!empty($v))
					{
						$pp = explode('|', $v);
						$params[$pp[0]] = $pp[1];
					}
				}
				$dptId = $pathArr[1];
				$type = $pathArr[2];
				$result = $this->warehouseProcessService->getPreparationSummeryGatepassList($dptId,$params,$type);
				$str2 = '';
				foreach ($result as $key => $r) {
					$str3="";
					foreach ($r as $key2 => $value) {
						if($key2=="ids")
						{

						}elseif($key2!="Type")
						{
							$str3.=" {$key2}:{$value}&nbsp;&nbsp;";
						}else
						{
							$str3.=" {$value}&nbsp;&nbsp;";
						}
					}

					if(empty($paramsStr))
					{
						$paramsStr=$k."|".$v;
					}else
					{
						$paramsStr=$paramsStr.",".$k."|".$v;
					}

					$str2.='<li><p><span class="exp" data-path="'.$paramsStr.'/'.$this->getDptId().'/'.$type.'/'.$key.'" data-loaded="0"></span>'.$str3.' <a href="'.Yii::app()->baseUrl.'/../gapsig/gatePass/needProcessParcelList.app?ids='.$r['ids'].'&&notSearch=1'.'&&cargoType='.$type.'&&gatepass_no='.$key.'&&ddpt_id='.$this->getDptId().'" class="exp" target="_blank">Prepare</a></p><ul></ul></li>';
				}
				$str = "<li><ul>".$str2."</ul></li>";
				echo $str;

				return;

			}elseif($pathCount==4)
			{
				$pa = explode(',', $pathArr[0]);
				$params = [];
				foreach ($pa as $k => $v) {
					if(!empty($v))
					{
						$pp = explode('|', $v);
						$params[$pp[0]] = $pp[1];
					}
				}
				$dptId = $pathArr[1];
				$type = $pathArr[2];
				$gatepassNo = $pathArr[3];
				[$sortAttributes,$provide] = $this->warehouseProcessService->getPreparationGatepassShipments($dptId,$params,$type,$gatepassNo);
				
				$filtersForm = new FiltersForm;
				$reportService = new ReportService();
				$dataprovider = $reportService->preDataProvider($filtersForm, $provide, $sortAttributes);
				$this->renderPartial('grid_view',['model'=>$dataprovider, 'filter' => $filtersForm,'attributes' => $sortAttributes,'uid'=>$gatepassNo]);
				return;
			}
		}


		$params = [];
		if(!empty($_POST))
		{
			$params = $_POST;
		}else
		{
			$params['pickupDate'] = 'tomorrow';
		}
		$result = $this->warehouseProcessService->getPreparationSummeryList($this->getDptId(),$params);
		$totalArr = $result['total'];
		$str2 = '';
		foreach ($result as $key => $r) {
			if($key=='total') continue;
			$str3="";
			foreach ($r as $key2 => $value) {
				if($key2=="ids")
				{

				}elseif($key2!="Type")
				{
					$str3.=" {$key2}:{$value}&nbsp;&nbsp;";
				}else
				{
					$str3.=" {$value}&nbsp;&nbsp;";
				}
			}
			if($key!=CargoProcess::PICKUP_CARGO)
			{
				$paramsStr = "";
				foreach ($params as $k => $v) {
					if(empty($paramsStr))
					{
						$paramsStr=$k."|".$v;
					}else
					{
						$paramsStr=$paramsStr.",".$k."|".$v;
					}
				}
				$str2.='<li><p><span class="exp" data-path="'.$paramsStr.'/'.$this->getDptId().'/'.$key.'" data-loaded="0"></span>'.$str3.' <a href="'.Yii::app()->baseUrl.'/../gapsig/gatePass/needProcessParcelList.app?ids='.$r['ids'].'&&notSearch=1'.'&&cargoType='.$key.'&&ddpt_id='.$this->getDptId().'" class="exp" target="_blank">Prepare</a></p><ul></ul></li>';
			}else
			{
				$str2.='<li><p> '.$str3.' <a href="'.Yii::app()->baseUrl.'/../gapsig/gatePass/needProcessParcelList.app?ids='.$r['ids'].'&&notSearch=1'.'&&cargoType='.$key.'&&ddpt_id='.$this->getDptId().'" class="exp" target="_blank">Prepare</a></p><ul></ul></li>';
			}
		}

		$today = date("Y-m-d");
		$str = "<li><p><h2>{$today}</h2>Total - :{$totalArr['total']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Done:&nbsp;{$totalArr['done']}&nbsp;&nbsp;({$totalArr['donePercent']})</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Left:&nbsp;{$totalArr['left']}</p><p>Preparation Group By Courier:</p><ul>".$str2."</ul></li>";

		if(!empty( $_POST))
		{
			$strAll = '<ul id="pl-sum-report-data_'.$params['tabid'].'" class="report_list" style="margin-top: 20px; list-style: none;font-size: 1.2em;">'.$str.'</ul>';
			echo $strAll;
		}else
		{
			echo $str;
		}
	}

	public function actionGetTodayHeldList()
	{
		$params = [];
		if(!empty($_POST))
		{
			$params = $_POST;
		}

		$totalArr = $this->warehouseProcessService->getTodayHeldSummeryList($this->getDptId());
		$str2 = '';
		$today = date("Y-m-d");
		$str = "<li><p><h2>{$today}</h2>Total - :{$totalArr['total']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Done:&nbsp;{$totalArr['done']}&nbsp;&nbsp;({$totalArr['donePercent']})</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Left:&nbsp;{$totalArr['left']}</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Late:&nbsp;{$totalArr['late']}</p><ul>".$str2."</ul></li>";

		if(!empty( $_POST))
		{
			$strAll = '<ul id="pl-sum-report-data_'.$params['tabid'].'" class="report_list" style="margin-top: 20px; list-style: none;font-size: 1.2em;">'.$str.'</ul>';
			echo $strAll;
		}else
		{
			echo $str;
		}
	}

	public function actionGetHeldShipmentGatepassList()
	{
		if(!empty($_GET['path']))
		{
			$pathArr = explode('/', $_GET['path']);
			$pathCount = count($pathArr);
			if($pathCount==2)
			{
				$pa = explode(',', $pathArr[0]);
				$params = [];
				$dptId = $pathArr[0];
				$gatepassNo = $pathArr[1];
				[$sortAttributes,$provide] = $this->warehouseProcessService->getPreparationGatepassShipments($dptId,$params,"",$gatepassNo,1);
				
				$filtersForm = new FiltersForm;
				$reportService = new ReportService();
				$dataprovider = $reportService->preDataProvider($filtersForm, $provide, $sortAttributes);
				$this->renderPartial('grid_view',['model'=>$dataprovider, 'filter' => $filtersForm,'attributes' => $sortAttributes,'uid'=>$gatepassNo]);
				return;
			}
		}
	
		$result = $this->warehouseProcessService->getHeldShipmentGatepassSummary($this->getDptId());
		$totalArr = $result[0];
		$resultArr = $result[1];
		$today = date("Y-m-d");
		$str2 = '';
		foreach ($resultArr as $key => $r)
		{
			$blank = "";
			$strLen = 2*strlen("GP No. ".$r[0])+13;
			for ($i=0; $i < $strLen; $i++) { 
				$blank .="&nbsp;";
			}
			$str3="GP No. ".$r[1]." "."&nbsp;--&nbsp;</td><td>Total HBL:&nbsp;{$r[2]}&nbsp;&nbsp;"."<table>
					<tr><td>{$blank}</td><td>Done HBL: {$r[4]}&nbsp;&nbsp;</td></tr>
					<tr><td>{$blank}</td><td>Left HBL: ".($r[2]-$r[4])."&nbsp;&nbsp;</td></tr>
					</table>";
			$str2.='<li><p style="margin:0 0 0px;"><span class="exp" data-path="'.$this->getDptId().'/'.$key.'" data-loaded="0"></span> '.$str3.'</p><ul></ul></li>';
		}

		$str = "<li><p><h2>{$today}</h2>Total - Gatepasses:{$totalArr['gatepasses']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total:&nbsp;{$totalArr['totalShipments']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Done:&nbsp;{$totalArr['allTotalResortShipment']}&nbsp;&nbsp;({$totalArr['donePercent']})</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Left:&nbsp;".($totalArr['totalShipments']-$totalArr['allTotalResortShipment'])."</p><p>Details:</p><ul>".$str2."</ul></li>";
		echo $str;

	}

	public function actionUploadFile($id)
	{
		$f = $_FILES['file'];
		$filename = $f['tmp_name'];
		$name = $f['name'];
		$comment = $_POST['ShipmentWhInspectionRelations']['comment'];
		if(!empty($comment))
		{
			$namearr = explode('.', $name);
			$name = $comment.".".end($namearr);
		}
		$relation = ShipmentWhInspectionRelations::model()->findByPk($id);
		$consol = Consol::model()->findByPk($relation->inspection->consol_id);
		$hash = FileRepo::uploadHash($consol, FileRepo::CONSOL_ATTACHMENT);
		if(!is_uploaded_file($filename)) return false;
		$thisHash = $hash;
		$filesize = filesize($filename);
		$date = date('Y-m-d H:i:s');
		$fileHash = hash_file('crc32b', $filename).hash('crc32b', $filesize);
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $f['tmp_name']);
		$fr = Service::updateUploadSingleFile($filesize,$date,$fileHash,$finfo,$mime,$filename,$name, $thisHash,false,false,false,true,FileRepo::ACTIVE);
		$relation->file_id = $fr->id;
		$relation->process_status = ShipmentWhInspection::UPLOAD_STATUS;
		$relation->upload_time = $date;
		$relation->check_status = $_POST['ShipmentWhInspectionRelations']['check_status'];
		$relation->comment = $comment;
		$relation->save();
		$relation->inspection->process_status = ShipmentWhInspection::UPLOAD_STATUS;
		$relation->inspection->upload_time = $date;
		$relation->inspection->save();
		$consol->log("upload Inspection File");
		echo 'done';
	}

	public function actionInspectionOperation()
	{
		$id = $_GET["id"];
		$_GET["tabid"] = "123456";
		$result =[];
		$result['model'] = ShipmentWhInspectionRelations::model()->findByPk($id);
		$this->render('operation_tab', $result);
	}

	public function actionDoneInspection()
	{
		$id = $_GET["id"];
		$model = $this->warehouseProcessService->doneInspection($id);
		$this->ajaxResult($model, ['id']);
	}

	public function actionUploadDamageFile()
	{
		$id = $_GET["id"];
		$found = $_GET["found"];
		$sn = $_GET["sn"];
		$location = $_GET["location"];
		$f = $_FILES['damage_file'];
		$filename = $f['tmp_name'];
		$name = $f['name'];
		$namearr = explode('.', $name);
		$tempModel = null;

		if(!empty($found))
		{
			$p = ImParcel::model()->findByPk($id);
			$name = $p->ref."-".$sn.".".end($namearr);
			$hash = FileRepo::uploadHash($p, FileRepo::SHIPMENT_DAMAGE);
		}else
		{
			$barcode = $_GET["barcode"];
			$containerNo = trim($_GET["containerNo"]);
			$containerNo2 = substr($containerNo,0,3)."-".substr($containerNo,3,strlen($containerNo));
			$consol = ImcoConsol::model()->find("(container_no = :containerNo or awb = :awb2 or awb = :awb ) and status != :status",[":containerNo"=>$containerNo,":awb"=>$containerNo,":awb2"=>$containerNo2,":status"=>ImcoConsol::Status_Cancelled]);
			$recordNo = $containerNo;
			$recordConsolId = 0;
			$changed_label = false;
			$courier_id = 0;
			$sn2 = 0;
			$barcode2 = $barcode;
			$p = ShipmentScan::getShipmentByBarcode($barcode2, $sn2, $courier_id, true, 0, $changed_label);
			if(empty($id)&&!empty($p))
			{
				$id = $p->id;
			}
			if(!empty($consol))
			{
				$recordNo = (empty($consol->container_no)?$consol->awb:$consol->container_no);
			}
			if(!empty($consol))
			{
				$recordConsolId = $consol->id;
			}
			$tempModel = ImParcelService::generateTempLabel($barcode,$recordNo,$recordConsolId,$id,$this->getDptId(),$location);
			$name = $tempModel->temp_barcode.".".end($namearr);
			if(!empty($consol))
			{
				$hash = FileRepo::uploadHash($consol, FileRepo::UNKNOWN_SHIPMENT_PHOTO);
			}else
			{
				$hash = FileRepo::uploadHash($tempModel, FileRepo::UNKNOWN_SHIPMENT_PHOTO);
			}
		}
		

		if(!is_uploaded_file($filename))
		{
			if(!empty($tempModel))
			{
				echo json_encode(["done"=>true,"msg"=>"temp","data"=>$tempModel->id]);
				return;
			}
			return false;
		}
		$thisHash = $hash;
		$filesize = filesize($filename);
		$date = date('Y-m-d H:i:s');
		$fileHash = hash_file('crc32b', $filename).hash('crc32b', $filesize);
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $f['tmp_name']);
		$fr = Service::updateUploadSingleFile($filesize,$date,$fileHash,$finfo,$mime,$filename,$name, $thisHash,false,false,false,true,FileRepo::ACTIVE);

		if(!empty($found))
		{		
			$p->bwf  = $p->bwf|ImParcel::DAMAGEBWFCODE;
			$files = FileRepo::model()->findAll("fid = :fid and type =:type",[":fid"=>$p->id,":type"=>FileRepo::SHIPMENT_DAMAGE]);
			$damagedPacks = 0;
			if(!empty($files))
			{
				$fileArr = [];
				foreach ($files as $key => $value) {
					$fileArr[$value->name] = $value->id;
				}
				$damagedPacks = count($fileArr);
				$p->mdata["damaged_packs"] = $damagedPacks;
			}	
			$p->update('bwf','meta');
			$p->log("upload Damage File");
		}

		if(!empty($tempModel))
		{
			echo json_encode(["done"=>true,"msg"=>"temp","data"=>$tempModel->id]);
			return;
		}

		echo json_encode(["done"=>true,"msg"=>"success"]);
		return;
	}

	public function actionScanSurplus()
	{
		$barcode = $_POST["shipment_surplus"];
		$containerNo = trim($_POST["surplus_myContainerNo"]);
		$containerNo2 = substr($containerNo,0,3)."-".substr($containerNo,3,strlen($containerNo));
		$location = $_POST["ground_label_surplus"];
		$consol = ImcoConsol::model()->find("(container_no = :containerNo or awb = :awb2 or awb = :awb ) and status != :status",[":containerNo"=>$containerNo,":awb"=>$containerNo,":awb2"=>$containerNo2,":status"=>ImcoConsol::Status_Cancelled]);
		$recordNo = $containerNo;
		$recordConsolId = 0;
		$changed_label = false;
		$courier_id = 0;
		$sn2 = 0;
		$barcode2 = $barcode;
		$sn3 = 0;
		$barcode3 = $barcode;

		$sn1 = 0;
		$barcode1 = $barcode;
		if(empty($consol))
		{
			echo json_encode(["done"=>false,"msg"=>"Consol is not existed"]);
			return;
		}

		if($consol->dpt_id!=$this->getDptId()||($this->getDptId()==Org::TLA_DEPARTMENT_PERTH&&!in_array($consol->dpt_id,Org::TLA_DEPARTMENT_PERTH,Org::TLA_DEPARTMENT_FREMANTLE)))
		{
			echo json_encode(["done"=>false,"msg"=>"Consol is not belong to this depot"]);
			return;
		}

		$newContainerNo = empty($consol->awb)?$consol->container_no:$consol->awb;
		$id = 0;
		$checkp = ShipmentScan::getShipmentByBarcode($barcode3, $sn3, $courier_id, false, 0, $changed_label,$newContainerNo,true);
		if(!empty($checkp))
		{
			echo json_encode(["done"=>false,"msg"=>"Shipment is existed in this AWB now"]);
			return;
		}

		$p = ShipmentScan::getShipmentByBarcode($barcode1, $sn1, $courier_id, false, 0, $changed_label);
		$ref = ShipmentScan::getShipmentByBarcode($barcode2, $sn2, $courier_id, false, 0, $changed_label,$newContainerNo,true,true);
		if(!empty($consol))
		{
			$recordNo = (empty($consol->container_no)?$consol->awb:$consol->container_no);
		}
		if(!empty($consol))
		{
			$recordConsolId = $consol->id;
		}
		$tempModel = ImParcelService::generateTempLabel($ref,$recordNo,$recordConsolId,$id,$this->getDptId(),$location,ImportsUnknownShipment::SURPLUS,$p);

		if(!empty($tempModel))
		{
			echo json_encode(["done"=>true,"msg"=>$barcode2." Upload Success","data"=>$tempModel->id]);
			return;
		}else
		{
			echo json_encode(["done"=>false,"msg"=>"Submit Failure"]);
			return;
		}
		return;
	}

	public function actionUploadContainerFile()
	{
		$containerNo = $_GET["containerNo"];
		$consol = ImcoConsol::model()->find("(container_no = :containerNo or awb = :awb ) and status != :status",[":containerNo"=>$containerNo,":awb"=>$containerNo,":status"=>ImcoConsol::Status_Cancelled]);
		if(empty($consol))
		{
			echo 'done';
			return;
		}
		$f = $_FILES['container_file'];
		$filename = $f['tmp_name'];
		$name = $f['name'];
		$namearr = explode('.', $name);
		$name = $containerNo.".".end($namearr);

		$hash = FileRepo::uploadHash($consol, FileRepo::CONTAINER_PHOTO);
		if(!is_uploaded_file($filename)) return false;
		$thisHash = $hash;
		$filesize = filesize($filename);
		$date = date('Y-m-d H:i:s');
		$fileHash = hash_file('crc32b', $filename).hash('crc32b', $filesize);
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $f['tmp_name']);
		$fr = Service::updateUploadSingleFile($filesize,$date,$fileHash,$finfo,$mime,$filename,$name, $thisHash,false,false,false,true,FileRepo::ACTIVE);
		echo 'done';
	}

	public function actionCheckContainerPhoto()
	{
		$containerNo = $_GET["containerNo"];
		$containerNo2 = substr($containerNo,0,3)."-".substr($containerNo,3,strlen($containerNo));
		$consol = ImcoConsol::model()->find("(container_no = :containerNo or awb = :awb2 or awb = :awb ) and status != :status",[":containerNo"=>$containerNo,":awb"=>$containerNo,":awb2"=>$containerNo2,":status"=>ImcoConsol::Status_Cancelled]);
		if(empty($consol))
		{
			echo 'done';
		}
		$filerepos = FileRepo::model()->count("fid = :fid and type = :type and status = :status",[":fid"=>$consol->id,':type'=>FileRepo::CONTAINER_PHOTO,":status"=>FileRepo::ACTIVE]);
		if($filerepos>0)
		{
			echo 'done';
		}else
		{
			echo 'failed';
		}
	}

	public function actionCheckConsol()
	{
		$containerNo = $_GET["containerNo"];
		$containerNo2 = substr($containerNo,0,3)."-".substr($containerNo,3,strlen($containerNo));
		$consol = ImcoConsol::model()->find("(container_no = :containerNo or awb = :awb2 or awb = :awb ) and status != :status",[":containerNo"=>$containerNo,":awb"=>$containerNo,":awb2"=>$containerNo2,":status"=>ImcoConsol::Status_Cancelled]);
		if(empty($consol))
		{
			echo '{"success":false,"msg":"not found ContainerNo or AWB"}';
		}else
		{

			if($consol->dpt_id!=$this->getDptId()||($this->getDptId()==Org::TLA_DEPARTMENT_PERTH&&!in_array($consol->dpt_id,Org::TLA_DEPARTMENT_PERTH,Org::TLA_DEPARTMENT_FREMANTLE)))
			{
				echo json_encode(["success"=>false,"msg"=>"Consol is not belong to this depot"]);
				return;
			}

			echo '{"success":true}';
		}
	}

	private function getDptId()
	{
		$tempWareHouse=106;
		if (!empty(Yii::app()->session['scan_warehouse']) && Yii::app()->session['scan_warehouse']=='melbourne') {
			$tempWareHouse=218;
		}else if (!empty(Yii::app()->session['scan_warehouse']) && Yii::app()->session['scan_warehouse']=='brisbane') {
			$tempWareHouse=530;
		}else if (!empty(Yii::app()->session['scan_warehouse']) && Yii::app()->session['scan_warehouse']=='perth') {
			$tempWareHouse=811;
		}
		return $tempWareHouse;
	}
	


	public function actionGetLocationRecommendation()
	{
		$result = ["success"=>false,'recommendLocation'=>''];
		$palletNo = $_GET['pallet_no'];
		$location = $this->warehouseProcessService->getLocationRecommendation($palletNo);
		if(!empty($location))
		{
			$result['success'] = true;
			$result['recommendLocation'] = $location;
		}

		echo json_encode($result);
	}
	public function actionUnlinkPallet()
	{
		$typeSelected = $_POST['typeSelection'];
		$res = new stdClass;
		if (!empty($typeSelected)) {
			if ($typeSelected == 'Pallet') {
				$palletNumber = $_POST['palletNumber'];
				$pallet = WmsLocation::model()->findByAttributes(['code' => $palletNumber]);
				if (!empty($pallet)) {
					$rackShipments = WmsRackShipment::model()->findAllByAttributes(['rack_id' => $pallet->id]);
					if (!empty($rackShipments)) {
						foreach ($rackShipments as $rackShipment) {
							$rackShipment->status = WmsRackShipment::REMOVED;
							$rackShipment->save();
						}
						$res->status = 'success';
						$res->message = 'Successfully unlinked pallet from shipment.';
					} else {
						$res->status = 'fail';
						$res->message = 'This pallet hasn\'t linked to any shipments yet.';
					}
				} else {
					$res->status = 'fail';
					$res->message = 'Cannot find this pallet in system.';
				}
				echo json_encode($res);
			} else {
				$barcode = $_POST['barcode'];
				$rackShipments = WmsRackShipment::model()->findAllByAttributes(['barcode' => $barcode]);
				if (empty($rackShipments)) {
					$res->status = 'fail';
					$res->message = 'Cannot find this shipment in system.';
				} else {
					foreach ($rackShipments as $rackShipment) {
						$rackShipment->status = WmsRackShipment::REMOVED;
						$rackShipment->save();
					}
					$res->status = 'success';
					$res->message = 'Successfully unlinked pallet from shipment.';
				}
				echo json_encode($res);
			}
		}
	}

	public function actionCheckContainerDetail(){
		$op ='printout';
		$barcode =  isset($_GET['barcode']) ? $_GET['barcode'] : '';
		$model=new ImParcel('search');
		$model->unsetAttributes();  // clear any default values
		// $model->ref = "searching";
		if(isset($_GET['ImParcel']))
		{
			$model->setAttributes($_GET["ImParcel"]);
			if(empty($model->consol_id))
			{
				$model->ref = "searching";
			}else
			{
				if($model->ref=="searching")
				{
					$model->ref = "";
				}
			}
			$this->render('consol_related_shipments_list', ['model' => $model]);
			return;
		}else
		{
			$model->ref = "searching";
		}

		$this->render('print_out_container', ['op' => $op,'barcode'=>$barcode,'model' => $model]);
	}

	public function actionCheckContainerDetailByBarcode()
	{
		if (isset($_GET['barcode'])) {

			$groupSound = function() use(&$r, &$p){
				$ss = [];
				foreach(['blueLabel','sound', 'amazon', 'area', 'label','dg','cfire', 'sorting','special'] as $k){
					if(!empty($r->{$k})){
						$ss[] = $r->{$k};
					}
					unset($r->{$k});
				}
				
				if(!empty($_POST['unpacking']))
				{
					if(!empty($p->ref) && preg_match('/.+(\d{4})[^\d]*$/', $p->ref, $m)){
						$ss = array_merge($ss, str_split($m[1]));
					}
					$r->sounds = $ss;

					if(!empty($r->pkg))
					{
						$r->sounds[] = "p";
						$r->sounds[] = $r->pkg;
					}
				}else
				{
					$r->sounds = $ss;
				}
			};
			$courier_id = 0;
			$sn = 0;
			$isHbn = false;
			$changedShipment = null;
			$changed_label = false;
			$scaned = false;
			$r = new StdClass;
			$r->color = 'red';
			$r->sound = 'not_found';
			$r->amazon = '';
			$r->blueLabel = '';
			$r->dg = '';
			$r->area = 'sydney';
			$r->stop = 0;
			$r->msg = 'NOT FOUND';
			$r->status = 'UNKNOWN';
			$r->nosound = 0;
			$r->found = 0;
			$r->id = 0;
			$r->label = '';
			$r->print = 0;
			$r->pkg = 0;
			$r->cfire = '';
			$r->log = "";

			$status_suffix = '';

			$barcode = $_GET['barcode'];
			$obarcode = $_GET['barcode'];
			if(empty($barcode))
			{
				$r->color = 'red';
				$r->sound = 'not_found';
				$r->stop = 0;
				$r->msg = 'NOT FOUND';
				$r->status = 'UNKNOWN';
				$r->nosound = 0;
				$r->found = 0;
				$groupSound();
				echo json_encode($r);
				Yii::app()->end();
			}
			$containerNo = empty($_POST['search_container_no'])?"":$_POST['search_container_no'];
			$r->msg = 'Not Found ' . $barcode;

		$this->consolCheckOnlyConsol($barcode,$r,$groupSound);
		return;
		}
	}

	private function consolCheckOnlyConsol(&$barcode,&$r,$groupSound)
	{
		$containerNo = $barcode;
		$msg = "";
		$msgArr= [];
		$consol = Consol::model()->find("container_no = :container_no",[":container_no"=>$containerNo]);
		if(empty($consol))
		{
			$consol = Consol::model()->find("awb = :awb",[":awb"=>$containerNo]);
		}

		if(empty($consol))
		{
			$r->color = 'red';
			$r->sound = 'not_found';
			$r->stop = 0;
			$r->msg = 'Consol: '.$barcode." not found ";
			$r->status = 'not_found';
			$r->nosound = 0;
			$r->found = 0;
			$groupSound();
			echo json_encode($r);
			Yii::app()->end();
		}

		
		$r->color = 'green';
		$r->sound = 'all_scan';
		$r->stop = 0;
		$r->msg = 'Consol: '.$barcode;
		$r->print=1;
		$r->consol_id = $consol->id;
		$r->found = 1;
		$groupSound();
		echo json_encode($r);
		// $this->showRelatedShipments();
		Yii::app()->end();
	}


	private function consolCheck(&$barcode,&$r,$groupSound)
	{
		$containerNo = $barcode;
		$msg = "";
		$msgArr= [];
		$consol = Consol::model()->find("container_no = :container_no",[":container_no"=>$containerNo]);
		if(empty($consol))
		{
			$consol = Consol::model()->find("awb = :awb",[":awb"=>$containerNo]);
		}

		if(empty($consol))
		{
			$r->color = 'red';
			$r->sound = 'not_found';
			$r->stop = 0;
			$r->msg = 'Consol: '.$barcode." not found ";
			$r->status = 'not_found';
			$r->nosound = 0;
			$r->found = 0;
			$groupSound();
			echo json_encode($r);
			Yii::app()->end();
		}
		foreach ($consol->shipments as $key => $p) 
		{
			if(!empty($p->bag_tag))
			{
				$shipmentScans = ShipmentScan::model()->findAll('pid = :pid and type = 5',[":pid"=>$p->bag_tag->import_bag_id]);
				if(empty($shipmentScans))
				{
					if(!in_array($p->bag_tag->bag_tag, $msgArr))
					{
						$msg .="</br>Bag ".$p->bag_tag->bag_tag." haven't scan record";
						$msgArr[] = $p->bag_tag->bag_tag;
					}

				}
			}
		}
		if($msg=="")
		{
			$r->color = 'green';
			$r->sound = 'all_scan';
			$r->stop = 0;
			$r->msg = 'Consol: '.$barcode." all scan ";
			$r->print=1;
			$r->consol_id = $consol->id;
			$groupSound();
			echo json_encode($r);
			// $this->showRelatedShipments();
			Yii::app()->end();
		}else
		{
			$r->color = 'red';
			$r->sound = 'bag_miss_scan';
			$r->stop = 0;
			$r->msg = 'Consol: '.$barcode.$msg;
			$r->status = 'bag_miss_scan';
			$r->nosound = 0;
			$r->found = 0;			
			$groupSound();
			echo json_encode($r);
			Yii::app()->end();
		}
	}

	public function actionShowRelatedShipments(){
		if(isset($_GET['consol_id'])){
			$consolId = $_GET['consol_id'];
			$model= Consol::model()->with('shipments')->findByPk($consolId);
			$shipmentsModel = $model->shipments;
			// print_r($model->shipments);
			// Yii::app()->end();
			if(isset($model)){
				$this->renderPartial('consol_related_shipments_list',['model'=>$shipmentsModel]);
				return ;
			}
			else{
				echo '{"done":false,"msg":"no shipments."}';
            	return ;
			}			
		}
		else{
			echo '{"done":false,"msg":"no shipments."}';
            return ;
		}
	}

	public function actionPrintAllPalletLabels()
	{		
		if(!empty($_POST['id'])){
			// print_r($_POST['id']);
			// Yii::app()->end();
			$model= Consol::model()->findByPk($_POST['id']);
			if ($model===null) {
				throw new CHttpException(404, 'The printed page does not exist.');
			}
			if (!empty($model->shipments)) {
				$this->getPalletLabelMultiZpl($model->shipments);
			}
		}
		else{
			$model= Consol::model()->findByPk($_GET['id']);
			if ($model===null) {
				throw new CHttpException(404, 'The printed page does not exist.');
			}
			if (!empty($model->shipments)) {
				$this->render('all_plt_label',array('model' => $model));
			}
		}		
	}

	public function actionExportUnpackList($id)
	{		
		$model= Consol::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404, 'The container number does not exist.');
		}
		$imConsolService = new ImConsolService();
		$imConsolService->exportUnpackList($model);
	}

	public function actionGenYlwLabel(){	
		$model = null;
		$shipmentId = -1;
		if(!empty($_GET['id']))
		{
			$id = $_GET['id'];
			$model=ImParcel::model()->findByPk($id);
			if(empty($model))
			{
				$model=ImParcelArchive::model()->findByPk($id);
			}

			if ($model===null) {
				throw new CHttpException(404, 'The requested page does not exist.');
			}
			$this->render('ylw_plt_label',array('model' => $model));
			return ;
		}

		if(!empty($_GET['shipmentId'])){
			$shipmentId=$_GET['shipmentId'];			
			$imParcelService = new ImParcelService();
			if (!$imParcelService->checkShipmentPalletLabel($shipmentId)){
				// throw new CHttpException(404, 'This shipment can not print out Yellow Pallet Label.');
				print_r('This shipment can not print out Pallet Label.');
				Yii::app()->end();
			}
			$_POST['id'] = $_GET['shipmentId'];
			$_POST['date'] = $_GET['date'];			
			$_POST['total_pcs'] = $_GET['total_pcs'];
			$_POST['total_pallets'] = $_GET['total_pallets'];
			$_POST['notes'] = $_GET['notes'];
			if(isset($_GET['cont_no'])){
				$_POST['cont_no'] = @$_GET['cont_no'];
			}
			else{
				$_POST['awb'] = @$_GET['awb'];
			}

			$imParcelService->generateYlwLabel($shipmentId);
			return ;
		}
		
	}

	public function actionGenerateYlwLabelZpl(){
        if(isset($_POST['id'])){
        	$id = $_POST['id'];
        	$this->generateYlwLabelZpl($id);
        }
    }

    public function generateYlwLabelZpl($id, $output=false){
    	$allData = "";
        $numOfPallets = 0;
        $cs = [];   
     
        if(!isset($id)){
        	throw new CHttpException(404, 'The shipment does not exist.');
        }
        $model=ImParcel::model()->findByPk($id);
		if(empty($model))
		{
			$model=ImParcelArchive::model()->findByPk($id);
		}		

		if ($model===null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}

        $parent = $model->getParentShipment();
        if(!empty($parent)&&(($parent->status!=ImParcel::STATE_LOCAL_ARRIVAL&&$parent->status<ImParcel::STATE_CLEAR)||$parent->status==ImParcel::STATE_CANCELLED))
        {
            echo "not ready for WDT shipments";
            return;
        }

        $copyShipment = $model->getCopyShipment();
        if(!empty($copyShipment))
        {
            $model = $copyShipment;
        }

        $pltCodes = WmsLocation::model()->findAll("shipment_id = :shipment_id",[":shipment_id"=>$model->id]);
        if(isset($pltCodes)){
            $cs = array_merge($cs, array_column($pltCodes, "code"));
        }

        if(isset($_POST['previous_label'])&&$_POST['previous_label']==1){
            if(!isset($pltCodes)){
            	echo "no previous labels.";
            	Yii::app()->end();
            }
            $numOfPallets += count($cs);
    	}

        $numOfPallets += $_POST['total_pallets'];
        $cs = array_merge($cs,WmsLocation::genCodes($_POST['total_pallets'], 50, $model->consol->dpt_id, $model->id));           
        if($model->consol->service == ImcoConsol::AIRCONSOL){
            $inputModel=['id'=>$_POST['id'],'date'=>$_POST['date'],'awb'=>$_POST['awb'],'total_pcs'=>$_POST['total_pcs'],'total_pallets'=>$numOfPallets,'notes'=>$_POST['notes']];              
        }
        else{
            $inputModel=['id'=>$_POST['id'],'date'=>$_POST['date'],'cont_no'=>$_POST['cont_no'],'total_pcs'=>$_POST['total_pcs'],'total_pallets'=>$numOfPallets,'notes'=>$_POST['notes']];
        }

        $nn=count($cs);
        for($pkg_sn = $nn-$inputModel['total_pallets']+1; $pkg_sn <= $nn; $pkg_sn++){
        	$allData .= $this->renderPartial('_label_plt_ylw_sgl_zpl', array('model' => $model, 'inputModel' => $inputModel, 'cs' => $cs, 'pkg_sn' =>$pkg_sn, 'dup' => false), true);
        }
        if(!$output){
        	echo json_encode(array("result" => true, "msg" => "订单完成, 等待打印", "file" => $allData));
        	Yii::app()->end();
        }
        else{
        	return $allData;
        }
            
    }

    public function getPalletLabelMultiZpl($shipments)
    {    	
        $notPintShipment = [];
        $ps = [];
        $cs = [];
        $normalLabel = [];
        $consolNum = $shipments[0]->consol->no;
        $allData = ""; 
        $imParcelService= new ImParcelService();
        foreach ($shipments as $key => $d)
        {
            if (!$imParcelService->checkShipmentPalletLabel($d->id)){
                unset($shipments[$key]);
            }
            else
            {
                $notPintShipment[] = $d;
            }
        }

        if(isset($_POST['previous_label'])&&$_POST['previous_label']==1){
    		foreach ($shipments as $key => $d)
        	{
        		$copyShipment = $d->getCopyShipment();
	            if(!empty($copyShipment))
	            {
	                $d = $copyShipment;
	            }

	            if($d->consol->service == ImcoConsol::AIRCONSOL){
                $inputModel=['id'=>$d->id,'date'=>$_POST['date'],'awb'=>$_POST['awb'],'total_pcs'=>$d->pkg,'notes'=>$_POST['notes']];                
	            }
	            else{
	                $inputModel=['id'=>$d->id,'date'=>$_POST['date'],'cont_no'=>$_POST['cont_no'],'total_pcs'=>$d->pkg,'notes'=>$_POST['notes']];
	            }

	            $pltCodes = WmsLocation::model()->findAll("shipment_id = :shipment_id",[":shipment_id"=>$d->id]);
	            $cs = array_column($pltCodes, "code");
	            if(!isset($pltCodes)){
	            	echo "no previous labels.";
	            	Yii::app()->end();
	            }
	            foreach ($cs as $key => $c)
	            {
	            	 $allData .= $this->renderPartial('_label_plt_ylw_sgl_zpl', array('model' => $d, 'inputModel' => $inputModel, 'cs' => $cs, 'pkg_sn' =>$key+1, 'dup' => false), true);
	            }	            
        	}
        	echo json_encode(array("result" => true, "msg" => "订单完成, 等待打印", "file" => $allData));
            Yii::app()->end();
    	}
        
        foreach ($shipments as $key => $d)
        {
        	$_POST['total_pallets'] = $d->getNumOfPallets();
            $_POST['total_pcs'] = $d->pkg;
            $allData .= $this->generateYlwLabelZpl($d->id,true);            

            // $copyShipment = $d->getCopyShipment();
            // if(!empty($copyShipment))
            // {
            //     $d = $copyShipment;
            // }
            
            // $numofPallets = $d->getNumOfPallets();

            // $cs = WmsLocation::genCodes($numofPallets, 50, $d->consol->dpt_id, $d->id);           
            // if($d->consol->service == ImcoConsol::AIRCONSOL){
            //     $inputModel=['id'=>$d->id,'date'=>$_POST['date'],'awb'=>$_POST['awb'],'total_pcs'=>$d->pkg,'total_pallets'=>$numofPallets,'notes'=>$_POST['notes']];                
            // }
            // else{
            //     $inputModel=['id'=>$d->id,'date'=>$_POST['date'],'cont_no'=>$_POST['cont_no'],'total_pcs'=>$d->pkg,'total_pallets'=>$numofPallets,'notes'=>$_POST['notes']];
            // }
            // // print_r($d);
            // // Yii::app()->end();              

            // $nn=$inputModel['total_pallets'];;
            // for($pkg_sn = 1; $pkg_sn <= $nn; $pkg_sn++){            		
            //         $allData .= $this->renderPartial('_label_plt_ylw_sgl_zpl', array('model' => $d, 'inputModel' => $inputModel, 'cs' => $cs, 'pkg_sn' =>$pkg_sn, 'dup' => false), true);
            // }         
        }

        echo json_encode(array("result" => true, "msg" => "订单完成, 等待打印", "file" => $allData));
            Yii::app()->end();
    }

	public function actionUpdatePalletCount()
	{
		$response = new stdClass;
		if (!empty($_POST)) {
			$awb = $_POST['awb'];
			$palletCountCourierPlease = !empty($_POST['pallet_count_courier_plz']) ? $_POST['pallet_count_courier_plz'] : 0;
			$cageCountCourierPlease = !empty($_POST['cage_count_courier_plz']) ? $_POST['cage_count_courier_plz'] : 0;
			$palletCountFastway = !empty($_POST['pallet_count_fastway']) ? $_POST['pallet_count_fastway'] : 0;;
			$cageCountFastway = !empty($_POST['cage_count_fastway']) ? $_POST['cage_count_fastway'] : 0;
			$palletCount = !empty($_POST['pallet_count_total']) ? $_POST['pallet_count_total'] : 0;
			$cageCount = !empty($_POST['cage_count_total']) ? $_POST['cage_count_total'] : 0;

			$consol = ImcoConsol::model()->find('(awb = "' . $awb . '" OR container_no = "' . $awb . '") and status !=100');
			if (!empty($consol)) {
				$prevTotalPallets = !empty($consol->mdata['cargo_receipt_pallets']) ? $consol->mdata['cargo_receipt_pallets'] : 0;
				$prevTotalCages = !empty($consol->mdata['cargo_receipt_cages']) ? $consol->mdata['cargo_receipt_cages'] : 0;
				$prevCourierPlzPallets = !empty($consol->mdata['pallet_count_courier_plz']) ? $consol->mdata['pallet_count_courier_plz'] : 0;
				$prevCourierPlzCages = !empty($consol->mdata['cage_count_courier_plz']) ? $consol->mdata['cage_count_courier_plz'] : 0;
				$prevFastwayPallets = !empty($consol->mdata['pallet_count_fastway']) ? $consol->mdata['pallet_count_fastway'] : 0;
				$prevFastwayCages = !empty($consol->mdata['cage_count_fastway']) ? $consol->mdata['cage_count_fastway'] : 0;
				$consol->mdata['cargo_receipt_pallets'] = $palletCount;
				$consol->mdata['cargo_receipt_cages'] = $cageCount;
				$consol->mdata['pallet_count_courier_plz'] = $palletCountCourierPlease;
				$consol->mdata['cage_count_courier_plz'] = $cageCountCourierPlease;
				$consol->mdata['pallet_count_fastway'] = $palletCountFastway;
				$consol->mdata['cage_count_fastway'] = $cageCountFastway;
				if ($consol->save()) {
					$logInfo = 'Total pallets updated from ' . $prevTotalPallets . ' to ' . $palletCount . ', total cages updated from ' . $prevTotalCages . ' to ' . $cageCount . ', courier please pallets updated from ' . $prevCourierPlzPallets . ' to ' . $palletCountCourierPlease . ', courier please cages updated from ' . $prevCourierPlzCages . ' to ' . $cageCountCourierPlease . ', fastway pallets updated from ' . $prevFastwayPallets . ' to ' . $palletCountFastway . ', fastway cages updated from ' . $prevFastwayCages . ' to ' . $cageCountFastway . ' by ' . Yii::app()->user->id;
					$consol->log($logInfo);
					$response->success = true;
					$response->message = 'Consol pallet count and cage count have been successfully updated.';
				}
			} else {
				$response->success = false;
				$response->message = 'Cannot find consol, please check your input awb / container number';
			}

			echo json_encode($response);
			Yii::app()->end();
		}
	}
	


    public function actionHeldCheckScanBarcode()
	{
		$warehouseProcessService = new WarehouseProcessService();
		$result = $warehouseProcessService->heldCheckScanBarcode($_GET['barcode']);
		echo json_encode($result);
	}

	public function actionSaveHeldScanCheck()
	{
		$warehouseProcessService = new WarehouseProcessService();
		$warehouseProcessService->saveHeldScanCheck($_POST['data'],$this->getDptId());
		echo "done";
	}

	public function actionSearchPalletCount()
	{
		$response = new stdClass;
		if (!empty($_POST['awb_search'])) {
			$consol = Consol::model()->findBySql('SELECT * FROM consol WHERE awb = "' . $_POST['awb_search'] . '" OR container_no = "' . $_POST['awb_search'] . '"');
			if (!empty($consol)) {
				$response->success = true;
				$response->awb = $_POST['awb_search'];
				//$data = $consol->courierSummaryWeight();
				$couriers = ['FastWay', 'courier please'];
				/* foreach ($data as $record) {
					if (strtolower($record[0]) == 'courier please' || strtolower($record[0]) == 'fastway') {
						array_push($couriers, $record[0]);
					}
				} */
				$response->couriers = $couriers;
			} else {
				$response->success = false;
				$response->message = 'Cannot find consol, check your input and try again';
			}
		} else {
			$response->success = false;
			$response->message = 'Empty search content.';
		}
		echo json_encode($response);
	}

	public function actionCheckInIndex()
	{
		$this->render('checkin_dash_index',[]);
	}

	public function actionCheckinPage()
	{
		switch ($_GET['tab']) {
			case 'check_in_dash':
				$this->actionGetCheckinDash();
				break;
			case 'coming_tmrw':
				$depot = $this->getDptId();
				$onRoad = 0;
				$model=new Consol('search');
				$model->unsetAttributes();
				$model->dpt_id=$depot;
				$model->service=ImcoConsol::AIRCONSOL;
				$model->process_statuses=[ConsolProcess::STATE_WAITING_AIRPORT_CKIN,ConsolProcess::STATE_WAITING_NOTIFY_DRIVER_PICKUP, ConsolProcess::STATE_NOTIFIED_DRIVER];
				$model->process_types=[ConsolProcess::TYPE_FULL_PROCESS, ConsolProcess::TYPE_LOCAL_PROCESS,ConsolProcess::TYPE_DIRECT_AIR_CONSOL, ConsolProcess::TYPE_DIRECT_SEA_CONSOL];
				$datModel=$model->search(false,30,false,false,false)->data;

				$consolProcessService = new ConsolProcessService();
				$provide = $consolProcessService->getPreAlert($datModel,false,$depot);
				$filtersForm=new FiltersForm;
				if (isset($_GET['FiltersForm'])) {
					$filtersForm->filters = $_GET['FiltersForm'];
				}
	            $filteredData=$filtersForm->filter($provide);
	            $dataprovider=new CArrayDataProvider($filteredData);
	            $sort=new CSort();
	            $sort->attributes=[
	                '*'
	            ];
	            $sort->defaultOrder = "doAgent ASC, status ASC, eta ASC";
	            $dataprovider->sort=$sort;
	            $dataprovider->pagination = ['pageSize' => 9999];

				$this->render('coming_tmrw',['dataprovider'=>$dataprovider,'filtersForm'=>$filtersForm,'width'=>$_GET['width']]);
				break;
			case 'arriving_today':
				$warehouseProcessService = new WarehouseProcessService();
				$model = new Bwtrunk('search');
				$model->unsetAttributes();;
				$model->dpt_id = $this->getDptId();
				$model->arrivingToday = true;
				$filtersForm = new FiltersForm;
				$provide = [];
				if (isset($_GET['FiltersForm'])) {
					$filtersForm->filters = $_GET['FiltersForm'];
				}
				$data = $model->search(true, 9999)->data;
				$provide = [];
				$onBoardConsols = $warehouseProcessService->getOnBoardConsols($model->dpt_id,"14:00:00");
				$MAWBNumbers = array_column($data, "MAWB_number");
				foreach ($onBoardConsols as $key => $value) {
					if(!in_array($value->awb, $MAWBNumbers))
					{
						$bwtrunk = new Bwtrunk();
						$bwtrunk->MAWB_number = $value->awb;
						$bwtrunk->id = $value->id;
						$bwcs = Bwtrunk::model()->findAll("MAWB_number = :awb",[":awb"=>$value->awb]);
						if(!empty($bwcs))
						{
							foreach ($bwcs as $bk => $vb) {
								$data[] = $vb;
							}
						}else
						{
							$data[] = $bwtrunk;
						}
					}
				}
				
				foreach ($data as $key => $value) {
					if($value->getCTOStartStr()=="ON THE WAY"&&date("H")>14)
					{
						continue;
					}
					$provide[] = ['id'=>$value->id,'MAWB_number'=>$value->MAWB_number,'Airport'=>$value->getConsolAirport(),'Air Type'=>$value->getConsolAirType(),'Customer'=>$value->getConsolCustomer(),'Weight'=>$value->getConsolWeight(),'pieces_pick_up'=>$value->getConsolPcs(),'CTO_start_time'=>$value->getCTOStartStr(true),'CTO_start'=>$value->getCTOStartStr(),'CTO_finish'=>$value->CTO_finish,'Client_start'=>$value->Client_start,'Client_finish'=>$value->Client_finish,'wh_note'=>$value->getConsolWHNote()];
				}


				$filteredData = $filtersForm->filter($provide);
				$dataprovider = new CArrayDataProvider($filteredData);
				$dataprovider->pagination = ['pageSize' => 9999];
				$sort=new CSort();
				$sort->defaultOrder = "CTO_start_time ASC,Airport ASC";
				$dataprovider->sort=$sort;

				$this->render('arriving_today',['dataprovider'=>$dataprovider,'filtersForm'=>$filtersForm,'width'=>$_GET['width']]);
				break;
			default:
				# code...
				break;
		}
	}

	public function actionGetCheckinDash()
	{
		$depot = @$_GET['depot'];
		$warehouseProcessService = new WarehouseProcessService();
		$checkInDashboardReport = $warehouseProcessService->getCheckinDashboardReport($depot,$this->getDptId());
		$this->render('checkin_dash',['provide'=>$checkInDashboardReport]);
	}

	public function actionGetCheckinDashDetail()
	{
		$depot = $_GET['depot'];
		$warehouseProcessService = new WarehouseProcessService();
		$checkInDashboardReport = $warehouseProcessService->getCheckinDashboardReportDetail($depot);
		$this->render('checkin_dash_detail',['provide'=>$checkInDashboardReport,'depot'=>Org::$warehouse_list[$depot]]);
	}

	public function actionLabel()
	{
		$this->layout = false;
		if (empty($_GET['tab'])) {
			$this->render('label');
		} else {
			switch ($_GET['tab']) {
				case 'generate':
					$this->render('generate_label');
					break;
				case 'reprint':
					$this->render('reprint_label');
					break;
				default:

					break;
			}
		}
	}

	public function actionGenerateLabel()
	{	
		if (!empty(Yii::app()->session['scan_warehouse'])) {
			switch (Yii::app()->session['scan_warehouse']) {
				case 'sydney':
					$depot = 106;
					break;
				case 'melbourne':
					$depot = 218;
					break;
				case 'brisbane':
					$depot = 530;
					break;
				case 'perth':
					$depot = 811;
					break;
				default:
					$depot = 106;
			}
		} else {
			$depot = 106;
		}
		if (!empty($_POST)) {
			$qty = intval($_POST['label_count']);
			if ($qty > 10) {	// to avoid generate too many labels at once
				$qty = 10;
			}
			$cs = WmsLocation::genCodes($qty, 50, $depot);
			$path = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR.'temp';
			if (!is_dir($path)) {
				mkdir($path);
			}
			$postFix = rand(1,10000);
			$file = $path . DIRECTORY_SEPARATOR . 'pallet_label_' . $postFix . '.pdf';
			oPDF::renderPDF('label_plt', array('cs' => $cs, 'dup' => false), 2, $file);
			echo $postFix;
		}
	}

	public function actionReprintLabel()
	{
		if (!empty($_POST)) {
			$q = preg_split('/[\n\r\s]+/', $_POST['pallet_no']);
			$q = array_filter($q);

			if (!empty($q)) {
				$cs = [];
				$plts = WmsLocation::model()->findAll('name in ("' . implode('","', $q) . '")');
				foreach ($plts as $plt) {
					$cs[] = $plt->name;
				}

				if (!empty(array_diff($q, $cs))) {
					echo 'Pallet No.: ' . implode(', ', array_values(array_diff($q, $cs))) . ' not exist in system';
				} else {
					if (!empty($cs)) {
						$path = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR.'temp';
						if (!is_dir($path)) {
							mkdir($path);
						}
						$postFix = rand(1,10000);
						$file = $path . DIRECTORY_SEPARATOR . 'pallet_label_' . $postFix . '.pdf';
						oPDF::renderPDF('label_plt', array('cs' => $cs, 'dup' => true), 2, $file);
						echo $postFix;
					} else {
						echo 'empty';
					}
				}
			} else {
				echo 'empty';
			}
		}
	}

	public function actionDownloadLabel($postFix)
	{
		$file = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR.'temp' . DIRECTORY_SEPARATOR . 'pallet_label_' . $postFix . '.pdf';
		oPDF::output(file_get_contents($file),1, $file);
	}

	public function actionEditConsolWhNote()
	{
		$bwtrunk = Bwtrunk::model()->findByPk($_GET["id"]);
		if(!empty($_POST))
		{
			$consol = $bwtrunk->getConsol();
			$consol->mdata['wh_note'] = $_POST['wh_note'];
			$consol->updateMeta();
			$this->ajaxResult($consol);
			return;
		}
		$this->render('edit_consol_wh_note',['model'=>$bwtrunk]);
	}
}
