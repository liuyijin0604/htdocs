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
if (!empty($_GET['ImParcel'])) $model->attributes = $_GET['ImParcel'];
$filtersForm = new FiltersForm;
if (isset($_GET['FiltersForm'])) {
	$filtersForm->filters = $_GET['FiltersForm'];
}
$monthProvide = $model->getCargoProcessReport(ReportCache::CargoProcessSumTotalByDaily);
$numAvgClose = $model->getAvgCargoProcessCloseByMonth(ReportCache::CargoProcessSumTotalByDaily);


$periodToday = date("m-d", strtotime($model = ReportCache::model()->findBySql('select * from report_cache where status =1 and type = '.ReportCache::CargoProcessSumTotalByDaily.' order by id desc limit 1')->create_time));
$filteredDataMonth = $filtersForm->filter($monthProvide);
$monthProvide = new CArrayDataProvider($filteredDataMonth);
$monthProvide->pagination = ['pageSize' => 100,];
?>
<div class="row">
		<h3><a style="display: block;text-align:right;" href="<?= $this->createUrl('report/getCargoProcessReportList') ?>" class="tab_link" title="Cargo Process List(SYD)">+ More</a><a><?php echo "Estimated(Today) : ".$numAvgClose['esttotal']." Days (Total) / ".$numAvgClose['estnor']." Days (B2C) "?></a></h3>
		<h3><a><?php echo "Estimated(Avg Month) : ".$numAvgClose['esttotalavg']." Days (Total) / ".$numAvgClose['estnoravg']." Days (B2C) "?></a></h3>
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