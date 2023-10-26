<h2>Cost</h2>
<?php

$currency = 'AUD';


$reportService = new ReportService;
$filtersForm = new FiltersForm;
$provide = $model->getTabBilling();
if (empty($provide['data'])) $provide['data'] = [];
$sortAttributes = ['supplier' => 'supplier', 'accrual' => 'accrual', 'invoice_payable' => 'invoice_payable', 'dispute' => 'dispute', 'confirm_payable' => 'confirm_payable', 'confirm_payable / accrual' => 'confirm_payable / accrual', 'inv_no' => 'inv_no', 'currency' => 'currency', 'desc' => 'desc'];
$sortNumberAttributes = $sortAttributes;
$dataprovider = $reportService->preDataProvider($filtersForm, $provide['data'], $sortAttributes, $sortNumberAttributes);
foreach ($sortAttributes as $key => $value) {
	if (in_array($key, ['accrual', 'invoice_payable', 'dispute', 'confirm_payable'])) {
		if (preg_match('/\~/i', $provide['total'][$key])) {
			$data = '~' . AppHelper::money_format('%i', substr($provide['total'][$key], 1));
		} else {
			$data = AppHelper::money_format('%i', $provide['total'][$key]);
		}
		$columns[] = ['name' => $value, 'header' => $key, 'type' => 'raw', 'footer' => '<div style="text-align: right">' . $data . '</div>'];
	} else {
		$columns[] = ['name' => $value, 'header' => $key, 'type' => 'raw'];
	}
}
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'cost-grid-' . $model->id,
	'cssFile' => false,
	'dataProvider' => $dataprovider,
	'filter' => $filtersForm,
	'columns' => $columns,
)); 
?>

<br />
<h2>Revenue</h2>
<?php
$dp = new Invoice('search');
$dp->unsetAttributes();
$dp->consol_id = $model->id;
$dp->no_include_status=[8,10];
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'elms-ordereport-grid',
	'cssFile' => false,
	'dataProvider' => $dp->search(),
	'filter' => null,
	'enableSorting' => false,
	'columns'=>array(
		array('name' => 'no', 'value' => '$data->no', ),
		array('name' => 'bill_to', 'value' => '$data->cust->name', ),
		array('header' => 'Invoice Total', 'value' => '$data->getCurrency().$data->total', ),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view} {detail}',
			'buttons'=>array
			(
				'view' => array(
					'url' => 'Yii::app()->createURL("invoice/print", array("id" => $data->id))',
					'imageUrl'=>false,
					'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
				),
				'detail' => array(
					'url' => 'Yii::app()->createURL("invoice/detail", array("id" => $data->id))',
					'label' => 'Detail',
					'imageUrl'=>false,
					'options' => array('class' => 'grid_file_btn', 'target' => '_blank'),
				),
			),
		),
)));

?>
<script type="text/javascript">
$(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel = tab.data('panel');
    tab.bind('onOpen', function(){
        $('#<?=$_GET["tabid"];?>_ledger-grid',panel).yiiGridView('update');
    });

});
</script>