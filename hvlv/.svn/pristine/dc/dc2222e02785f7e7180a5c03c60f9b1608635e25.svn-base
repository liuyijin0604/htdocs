<?php


$model = new Dash();
if(!empty($_GET['ImParcel'])) $model->attributes = $_GET['ImParcel'];
$filtersForm=new FiltersForm;
if (isset($_GET['FiltersForm'])) {
	$filtersForm->filters=$_GET['FiltersForm'];
}
$monthProvide = $model->getAllGpDataMonth();

$filteredDataMonth=$filtersForm->filter($monthProvide);
$monthProvide=new CArrayDataProvider($filteredDataMonth);
$monthProvide->pagination=['pageSize' =>100,];

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'dashboard-wid-gp-month-grid',
	'cssFile' => false,
	'dataProvider'=>$monthProvide,
	'summaryText' => '',
	'enablePagination' => true,
	'filter' => $filtersForm,
	'pager'=>array(
		'prevPageLabel'=>'Prev.',
		'maxButtonCount' => 5,
	),
	'columns'=>array(
		array('header' => 'Date','type' => 'raw','value' => '$data["date"]'),
		array('header' => 'Gp', 'type' => 'raw',"value"=>'$data["gp"]'),
		array('header' => 'Revenue', 'type' => 'raw',"value"=>'$data["revenue"]'),
		array('header' => 'All Shipments', 'type' => 'raw',"value"=>'$data["totalShipment"]'),
		array('header' => 'All Weight', 'type' => 'raw',"value"=>'$data["totalWeight"]'),
	),
));
