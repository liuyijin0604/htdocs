<style type="text/css">
	.cargoNew{
		color: #DAA569;
        text-align: center;
	}
	.cargoClose{
		color: #2E8B57;
        text-align: center;
	}
	.cargoLeft{
		color: #FF4500;
        text-align: center;
	}
</style>

<?php
$model = new Dash();
if (!empty($_GET['ImParcel'])) $model->attributes = $_GET['ImParcel'];
$filtersForm = new FiltersForm;
if (isset($_GET['FiltersForm'])) {
	$filtersForm->filters = $_GET['FiltersForm'];
}
$monthProvide = $model->getTPLDailyMelReport();

$model = ReportCache::model()->findBySql('select * from report_cache where status =1 and type = 9 order by id desc limit 1');
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
		<h3><a style="display: block;text-align:right;" href="<?= $this->createUrl('report/getTPLReportListMel') ?>" class="tab_link" title="TPL TASK(MEL)">+ More</a></h3>
		<h3><a><?php echo "Record Date ".$periodToday?></a><a style="color:#DAA569"> New </a><a style="color:#FF4500"> Left </a><a style="color:#2E8B57"> Done </a></h3>
		<?php
		$this->widget('zii.widgets.grid.CGridView', array(
			'id' => 'dashboard-wid-tpls-month-grid',
			'cssFile' => false,
			'dataProvider' => $monthProvide,
			'summaryText' => '',
			'enablePagination' => true,
			'pager' => array(
				'prevPageLabel' => 'Prev.',
				'maxButtonCount' => 5,
			),
			'columns' => array(
				array('header' => 'New Task', 'type' => 'raw', 'value' => '$data["todaytask"]','htmlOptions' => array('class' => 'cargoNew')),
				array('header' => 'Revenue', 'type' => 'raw', 'value' => '$data["todayinvoice"]','htmlOptions' => array('class' => 'cargoNew')),
				array('header' => 'GP', 'type' => 'raw', 'value' => '$data["todaygp"]','htmlOptions' => array('class' => 'cargoNew')),
				array('header' => 'Left TASK', 'type' => 'raw', 'value' => '$data["todayleft"]','htmlOptions' => array('class' => 'cargoLeft')),
				array('header' => 'Revenue(M)', 'type' => 'raw', 'value' => '$data["monavginv"]','htmlOptions' => array('class' => 'cargoClose')),
				array('header' => 'GP(M)', 'type' => 'raw', 'value' => '$data["monavgcost"]','htmlOptions' => array('class' => 'cargoClose')),
				array('header' => 'Done Task', 'type' => 'raw', 'value' => '$data["todaydone"]','htmlOptions' => array('class' => 'cargoClose')),
				array('header' => 'Completion', 'type' => 'raw', 'value' => '$data["todayfinish"]','htmlOptions' => array('class' => 'cargoClose')),
			),
		));
		?>
</div>