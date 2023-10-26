<h1>Update Aupost Fuel Surcharge Rate</h1>

<?php
$filtersForm = new FiltersForm();
if (isset($_GET['filtersForm'])) {
	$filtersForm->filters = $_GET['filtersForm'];
}
$setting = SystemSetting::getAupostFuelChargeSetting();
$provide = [];
foreach ($setting as $month => $rate) {
	$p = new FiltersForm;
	$p->id = $month;
	$p->month = $month;
	$p->rate = $rate;
	$provide[] = $p;
}
$filteredData = $filtersForm->filter($provide);
$dataProvider = new CArrayDataProvider($filteredData, array(
	'pagination' => false,
));
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id' => 'fuel-surcharge-rate-grid',
	'cssFile' => false,
	'dataProvider' => $dataProvider,
	'filter' => $filtersForm,
	'summaryText' => '',
	'filter' => null,
	'formUrl' => $this->createUrl('org/setFuelSurcharge'),
	'afterSave' => 'function(r) {
		if (r.done == true) {
			myApp.notice(r.msg, 5000);
		} else {
			myApp.alert(r.msg, false);
		}
		return r.done;
	}',
	'columns' => array(
		array('name' => 'month', 'header' => 'Month', 'class' => 'CEditableColumn'),
		array('name' => 'rate', 'header' => 'Rate', 'class' => 'CEditableColumn'),
		array('class'=>'CEditableButtonColumn',
			'template' => '{edit} {cancel} {save} {delete}',
			'buttons' => array(
				'delete' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("org/deleteFuelSurcharge", ["month" => $data->month])',
					'options' => array('class' => 'delete_btn'),
				),
			),
		),
	),
));