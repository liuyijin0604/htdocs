<?php

$model = new Dash();
if(!empty($_GET['ImParcel'])) $model->attributes = $_GET['ImParcel'];
$filtersForm=new FiltersForm;
if (isset($_GET['FiltersForm'])) {
	$filtersForm->filters=$_GET['FiltersForm'];
}


$monthProvide = $model->getConsolProcessReport(ReportCache::ConsolProcessSumTotalByDailyBne);
$model = ReportCache::model()->findBySql('select * from report_cache where status =1 and type = '.ReportCache::ConsolProcessSumTotalByDailyBne.' order by id desc limit 1');


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
$department = 'Bne';
echo $this->render('wid_imwhInOut', [
	'model'=>$model,
	'monthProvide'=>$monthProvide,
	'periodToday'=>$periodToday,
	'department'=>$department
]);

?>