<style type="text/css">
	.cargoNew{
		color: #DAA569;
	}
	.cargoClose{
		color: #2E8B57;
	}
	.cargoLeft{
		color: #FF4500;
	}
</style>

<?php
$model = new Dash();
// if (!empty($_GET['ImParcel'])) $model->attributes = $_GET['ImParcel'];
$filtersForm = new FiltersForm;
if (isset($_GET['FiltersForm'])) {
	$filtersForm->filters = $_GET['FiltersForm'];
}
$monthProvide = $model->getCargoProcessReport(ReportCache::CargoProcessSumTotalByDaily);
$numAvgClose = $model->getAvgCargoProcessCloseByMonth(ReportCache::CargoProcessSumTotalByDaily);
$effectiveDiff = $model->getEffectiveDiff(ReportCache::CargoProcessSumTotalByDaily);
$strAVGMonthly = "N/A";
$strGoodDeliveryAVGMonthly = "N/A";
if($effectiveDiff[0]!=0){
	$numAvg = $effectiveDiff[0]/$effectiveDiff[1];
	if($numAvg>=0.95){
		$srtStyle = "color:#2E8B57";
	}/* elseif($numAvg>0.7){
		$srtStyle = "color:#DAA569";
	 }*/else{
		$srtStyle = "color:#FF4500";
	}
	$strAVGMonthly = (round(($effectiveDiff[0]) /$effectiveDiff[1], 4) * 100)."%";
}

if($effectiveDiff[2]!=0){
	$numAvg = $effectiveDiff[2]/$effectiveDiff[1];
	if($numAvg>=0.95){
		$srtStyleG = "color:#2E8B57";
	}/* elseif($numAvg>0.7){
		$srtStyleG = "color:#DAA569";
	} */else{
		$srtStyleG = "color:#FF4500";
	}
	$strGoodDeliveryAVGMonthly = (round(($effectiveDiff[2]) /$effectiveDiff[1], 4) * 100)."%";
}

$effectiveDiffB2B = $model->getEffectiveDiffB2B(ReportCache::CargoProcessSumTotalByDaily);
$strAVGMonthlyB2B = "N/A";
$strGoodDeliveryAVGMonthlyB2B = "N/A";
if($effectiveDiffB2B[0]!=0){
	$numAvg = $effectiveDiffB2B[0]/$effectiveDiffB2B[1];
	if($numAvg>=0.95){
		$srtStyleB2B = "color:#2E8B57";
	}/* elseif($numAvg>0.7){
		$srtStyleB2B = "color:#DAA569";
	} */else{
		$srtStyleB2B = "color:#FF4500";
	}
	$strAVGMonthlyB2B = (round(($effectiveDiffB2B[0]) /$effectiveDiffB2B[1], 4) * 100)."%";
}

if($effectiveDiffB2B[2]!=0){
	$numAvg = $effectiveDiffB2B[2]/$effectiveDiffB2B[1];
	if($numAvg>=0.95){
		$srtStyleB2BG = "color:#2E8B57";
	}/* elseif($numAvg>0.7){
		$srtStyleB2BG = "color:#DAA569";
	} */else{
		$srtStyleB2BG = "color:#FF4500";
	}
	$strGoodDeliveryAVGMonthlyB2B = (round(($effectiveDiffB2B[2]) /$effectiveDiffB2B[1], 4) * 100)."%";
}

$effectiveDiffFBA = $model->getEffectiveDiffFBA(ReportCache::CargoProcessSumTotalByDaily);
$strAVGMonthlyFBA = "N/A";
$strGoodDeliveryAVGMonthlyFBA = "N/A";
if($effectiveDiffFBA[0]!=0){
	$numAvg = $effectiveDiffFBA[0]/$effectiveDiffFBA[1];
	if($numAvg>=0.95){
		$srtStyleFBA = "color:#2E8B57";
	}/* elseif($numAvg>0.7){
		$srtStyleFBA = "color:#DAA569";
	} */else{
		$srtStyleFBA = "color:#FF4500";
	}
	$strAVGMonthlyFBA = (round(($effectiveDiffFBA[0]) /$effectiveDiffFBA[1], 4) * 100)."%";
}

if($effectiveDiffFBA[2]!=0){
	$numAvg = $effectiveDiffFBA[2]/$effectiveDiffFBA[1];
	if($numAvg>=0.95){
		$srtStyleFBAG = "color:#2E8B57";
	}/* elseif($numAvg>0.7){
		$srtStyleFBAG = "color:#DAA569";
	} */else{
		$srtStyleFBAG = "color:#FF4500";
	}
	$strGoodDeliveryAVGMonthlyFBA = (round(($effectiveDiffFBA[2]) /$effectiveDiffFBA[1], 4) * 100)."%";
}

$model = ReportCache::model()->findBySql('select * from report_cache where status =1 and type = '.ReportCache::CargoProcessSumTotalByDaily.' order by id desc limit 1');

if(empty($model))
{
	$periodToday = date("m-d");
}else
{
	$periodToday = date("m-d", strtotime($model->create_time));
}

$filteredDataMonth = $filtersForm->filter($monthProvide);
$monthProvide = new CArrayDataProvider($filteredDataMonth);
$monthProvide->pagination = ['pageSize' => 100,];
?>
<div class="row">
		<h3><a style="display: block;text-align:right;" href="<?= $this->createUrl('report/getCargoProcessReportList') ?>" class="tab_link" title="Cargo Process List(SYD)">+ More</a><a><?php echo "Estimated(Today) : ".@$numAvgClose['esttotal']." Days (Total) / ".@$numAvgClose['estnor']." Days (B2C) "?></a></h3>
		<h3><a><?php echo "Estimated(Avg Month) : ".@$numAvgClose['esttotalavg']." Days (Total) / ".@$numAvgClose['estnoravg']." Days (B2C) "?></a></h3>
		<h3><a><?php echo "B2C 3 Days Delivery KPI : ". $effectiveDiff[0]."/".$effectiveDiff[1]." "?></a><a style="<?php echo $srtStyle?>"><?php echo $strAVGMonthly?></a> <a><?php echo "Effective KPI : ". $effectiveDiff[2]."/".$effectiveDiff[1]." "?></a><a style="<?php echo $srtStyleG?>"><?php echo $strGoodDeliveryAVGMonthly?></a></h3>
		<h3><a><?php echo "B2B 5 Days Delivery KPI : ". $effectiveDiffB2B[0]."/".$effectiveDiffB2B[1]." "?></a><a style="<?php echo $srtStyleB2B?>"><?php echo $strAVGMonthlyB2B?></a> <a><?php echo "Effective KPI : ". $effectiveDiffB2B[2]."/".$effectiveDiffB2B[1]." "?></a><a style="<?php echo $srtStyleB2BG?>"><?php echo $strGoodDeliveryAVGMonthlyB2B?></a></h3>
		<h3><a><?php echo "FBA 5 Days Delivery KPI : ". $effectiveDiffFBA[0]."/".$effectiveDiffFBA[1]." "?></a><a style="<?php echo $srtStyleFBA?>"><?php echo $strAVGMonthlyFBA?></a> <a><?php echo "Effective KPI : ". $effectiveDiffFBA[2]."/".$effectiveDiffFBA[1]." "?></a><a style="<?php echo $srtStyleFBAG?>"><?php echo $strGoodDeliveryAVGMonthlyFBA?></a></h3>
		<h3><a style="color:#DAA569"> New </a><a style="color:#2E8B57"> Done </a><a style="color:#FF4500"> Left </a></h3>
		<?php
		$this->widget('zii.widgets.grid.CGridView', array(
			'id' => 'dashboard-wid-gp-month-grid',
			'cssFile' => false,
			'dataProvider' => $monthProvide,
			'summaryText' => '',
			'enablePagination' => true,
			'pager' => array(
				'prevPageLabel' => 'Prev.',
				'maxButtonCount' => 5,
			),
			'columns' => array(
				array('header' => $periodToday, 'type' => 'raw', 'value' => '$data["col"]'),
				// array('header' => 'TOTAL','type' => 'raw','value' => '$data["totaly"]'),
				// array('header' => 'FBA','type' => 'raw','value' => '$data["fbay"]'),
				// array('header' => 'B2B','type' => 'raw','value' => '$data["btby"]'),
				// array('header' => 'Normal','type' => 'raw','value' => '$data["nory"]'),
				array('header' => 'ALL', 'type' => 'raw', 'value' => '$data["totaln"]','htmlOptions' => array('class' => 'cargoNew')),
				array('header' => 'FBA', 'type' => 'raw', 'value' => '$data["fban"]','htmlOptions' => array('class' => 'cargoNew')),
				array('header' => 'B2B', 'type' => 'raw', 'value' => '$data["btbn"]','htmlOptions' => array('class' => 'cargoNew')),
				array('header' => 'B2C', 'type' => 'raw', 'value' => '$data["norn"]','htmlOptions' => array('class' => 'cargoNew')),
				array('header' => 'ALL', 'type' => 'raw', 'value' => '$data["totalc"]','htmlOptions' => array('class' => 'cargoClose')),
				array('header' => 'FBA', 'type' => 'raw', 'value' => '$data["fbac"]','htmlOptions' => array('class' => 'cargoClose')),
				array('header' => 'B2B', 'type' => 'raw', 'value' => '$data["btbc"]','htmlOptions' => array('class' => 'cargoClose')),
				array('header' => 'B2C', 'type' => 'raw', 'value' => '$data["norc"]','htmlOptions' => array('class' => 'cargoClose')),
				array('header' => 'ALL', 'type' => 'raw', 'value' => '$data["totall"]','htmlOptions' => array('class' => 'cargoLeft')),
				array('header' => 'FBA', 'type' => 'raw', 'value' => '$data["fbal"]','htmlOptions' => array('class' => 'cargoLeft')),
				array('header' => 'B2B', 'type' => 'raw', 'value' => '$data["btbl"]','htmlOptions' => array('class' => 'cargoLeft')),
				array('header' => 'B2C', 'type' => 'raw', 'value' => '$data["norl"]','htmlOptions' => array('class' => 'cargoLeft')),
			),
		));
		?>
</div>