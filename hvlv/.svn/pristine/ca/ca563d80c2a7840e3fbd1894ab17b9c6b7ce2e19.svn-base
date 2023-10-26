<?php

class WarehouseProcessController extends Controller
{
	protected $nonAjax=['scanInfo','export'];
	protected $warehouseProcessService;

	public function beforeAction($action)
	{
		$this->warehouseProcessService = new WarehouseProcessService();
		return parent::beforeAction($action);
	}

	public function actionProcessIndex()
	{
		$dptId = $_GET['pod_id'];
		$name = Org::$importWarehouseList[$dptId];
		$this->render('processIndex', ['dptId'=>$dptId,'name'=>$name]);
	}

	public function actionProcessPage()
	{
		$dptId = $_GET['dptId'];
		switch ($_GET['tab']) {
			case 'check_in':
				$op ='checkin';
				$barcode =  isset($_GET['barcode']) ? $_GET['barcode'] : '';
				$this->render('check_in_consol_list', ['dptId'=>$dptId]);
				break;
			case 'inspection':
				$this->render('inspection_list',['dptId'=>$dptId]);
				break;
			case 'putaway_sort_held':
				$this->render('putaway_sort_held_list',['dptId'=>$dptId]);
				break;
			case 'preparation':
				$this->render('preparation_list',['dptId'=>$dptId]);
				break;
			case 'gatepass_sign':
				$this->render('gatepass_sign_list',['dptId'=>$dptId]);
				break;
			case 'held_shipment_resorting':
				$filtersForm=new FiltersForm;
				if (isset($_GET['FiltersForm'])) {
					$filtersForm->filters=$_GET['FiltersForm'];

				}

				$provide = $this->warehouseProcessService->getPreparationOldGatepassWithoutResorting($dptId);
				$filteredData=$filtersForm->filter($provide);
				$dataprovider=new CArrayDataProvider($filteredData);
				$dataprovider->pagination=['pageSize' =>20,];
				$sort=new CSort();
				$sort->attributes=[
					'gatepass_time'=>[
						'asc'=>'gatepass_time ASC',
						'desc'=>'gatepass_time DESC',
					]
				];
				$sort->defaultOrder = "gatepass_time DESC";
				$dataprovider->sort=$sort;
				if (isset($_GET['FiltersForm'])||empty($dptId))
				{
					$this->renderPartial('_old_gatepass_without_resort',['dataProvider'=>[$dataprovider,$filtersForm]]);
					return;
				}

				$this->render('held_shipment_resorting_list',['dptId'=>$dptId,'dataProvider'=>[$dataprovider,$filtersForm]]);
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
					if($r['Held_Scan']>0||$r['Held_Total']>0)
					{
						$heldStr ='<font color="red">'.$r['Held_Total']."</font>";
					}else
					{
						$heldStr =$r['Held_Total'];
					}

					$str3=" {$r['Courier']}:{$str3}&nbsp;&nbsp;Left:".$r['Unscaned']." and Held:{$heldStr}";
					$strSearch = "";
					if($r['Unscaned']>0||$r['Oversize']>0||$r['Held_Scan']>0||$r['Held_Total']>0)
					{
						$strSearch = '<span class="exp" data-path="'.$pathArr[0].'/'.$pathArr[1].'/'.$r['courierId'].'" data-loaded="0"></span>';
					}else
					{
						$strSearch = 'Done:';
					}
					$str2.='<li><p>'.$strSearch.$str3.'</p><ul></ul></li>';
				}
				$str = "<li><ul>".$str2."</ul></li>";
				echo $str;
				return;
			}elseif($pathCount==3)
			{
				[$header,$result,$percent] = $this->warehouseProcessService->getCheckInReportForConsolCourier($pathArr[1],$pathArr[2]);
				echo $this->warehouseProcessService->ajaxPLchart($header,$result,$percent);
				return;
			}
		}
	
		$result = $this->warehouseProcessService->getCheckInReport($this->getDptId());
		$totalArr = $result[0];
		$resultArr = $result[1];
		$str2 = '';
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
			$str3=$r[0]."&nbsp;--&nbsp;</td><td>Total HBL:&nbsp;{$r[1]}&nbsp;&nbsp;/&nbsp;&nbsp;PKG:{$r[2]}&nbsp;&nbsp"."<table>
					<tr><td>{$blank}</td><td>Done HBL: {$r[3]}&nbsp;&nbsp;/&nbsp;&nbsp;PKG:&nbsp;{$r[4]}</td></tr>
					<tr><td>{$blank}</td><td>Left HBL: {$r[5]}&nbsp;&nbsp;/&nbsp;&nbsp;PKG:&nbsp;{$r[6]}</td></tr>
					<tr><td>{$blank}</td><td>Held Left HBL:&nbsp;{$strHeld2}&nbsp;&nbsp;/&nbsp;&nbsp;PKG:{$strHeld1}</td></tr></table>";
			$str2.='<li><p style="margin:0 0 0px;"><span class="exp" data-path="'.$this->getDptId().'/'.$key.'" data-loaded="0"></span> '.$str3.'</p><ul></ul></li>';
		}
		$str = "<li><p>Total - Consols:{$totalArr['consols']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total:&nbsp;{$totalArr['pkg']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Done:&nbsp;{$totalArr['scanCount']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Left:&nbsp;{$totalArr['left']}</p><p>Details:</p><ul>".$str2."</ul></li>";

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

	public function actionGetGatePassSignList()
	{
		
		$result = $this->warehouseProcessService->getGapsignSummeryList($this->getDptId());
		$str2 = '';
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
		$str = "<li><ul>".$str2."</ul></li>";

		echo $str;

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
				$str2.='<li><p><span class="exp" data-path="'.$paramsStr.'/'.$this->getDptId().'/'.$key.'" data-loaded="0"></span>'.$str3.' <a href="'.Yii::app()->baseUrl.'/../gapsig/gatePass/needProcessParcelList.app?ids='.$r['ids'].'&&notSearch=1'.'&&cargoType='.$key.'" class="exp" target="_blank">Prepare</a></p><ul></ul></li>';
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

		$str = "<li><p>Total - Gatepasses:{$totalArr['gatepasses']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total:&nbsp;{$totalArr['totalShipments']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Done:&nbsp;{$totalArr['allTotalResortShipment']}&nbsp;&nbsp;</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Left:&nbsp;".($totalArr['totalShipments']-$totalArr['allTotalResortShipment'])."</p><p>Details:</p><ul>".$str2."</ul></li>";
		echo $str;

	}

	
	private function getDptId()
	{
		$tempWareHouse=$_GET['dptId'];
		return $tempWareHouse;
	}


}
