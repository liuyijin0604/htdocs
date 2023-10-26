<?php
$filtersForm = new FiltersForm();
$filteredData = $filtersForm->filter(array_values($consols));
$dataprovider = new CArrayDataProvider($filteredData);
$dataprovider->pagination = ['pageSize' => 200];
$sort = new CSort();
$sortAttr = [];
$sortAttributes = ['Consol' => 'consol_no', 'Accrual' => 'Accrual', 'Payable' => 'Payable'];
foreach ($sortAttributes as $tempAttribute) {
	$sortAttr[$tempAttribute] = ['asc' => $tempAttribute . ' ASC', 'desc' => $tempAttribute . ' DESC'];
}
$sort->attributes = $sortAttr;
$sort->defaultOrder = 'consol_no DESC';
$dataprovider->sort = $sort;

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'im-pl-by-cost-detail',
	'cssFile' => false,
	'dataProvider' => $dataprovider,
	'filter' => $filtersForm,
	'columns' => array(
		array('name' => 'consol_no', 'header' => 'Consol'),
		array('name' => 'accrual', 'header' => 'Accrual'),
		array('name' => 'payable', 'header' => 'Payable'),
	),
));
?>