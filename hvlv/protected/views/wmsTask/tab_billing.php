<?php
[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
$ledger = new BillingLine('search');
$ledger->unsetAttributes();
$ledger->billing_ref = $model->getNo();
$ledger_dp = $ledger->search();
$ledger_data = $ledger_dp->getData();
$currency = 'AUD';

$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id' => $_GET["tabid"].'_wmsBilling-grid',
	'cssFile' => false,
	'dataProvider' => $ledger_dp,
	'formUrl' => $this->createUrl('wmsTask/billingGrid', array('id' => $model->id)),
	'filter' => null,
	'summaryText' => '',
	'afterSave' => "function(r){
		if(r.done == true){
			myApp.notice(r.msg, 5000);
		}else{
			myApp.alert(r.msg, false);
		}
		return r.done;
	}",
	'columns'=>array(
		array('name' => 'org_id', 'class' => 'CEditableColumn', 'type' => 'autocomplete',
			'value' => 'empty($data->cust) ? "" : $data->cust->shortName(3)' ,
			'acOptions' => array('source' => 'org/ownerSuggest')
		),
		// array('name' => 'charge_code', 'class' => 'CEditableColumn', 'type' => 'autocomplete',
		// 	'value' => '$data->getCCodeDesc()',
		// 	'acOptions' => array('source' => 'chargeCode/chargeCode3PLFixed')
		// ),
		array('name' => 'charge_code', 'class' => 'CEditableColumn', 'type' => 'list', 'filter' => array(
			'91022' => '3PL Cost - Warehouse Service',
			'91023' => '3PL Cost - Warehouse Material',
			'91020' => '3PL Export&Import - Local',
			'91014' => 'Air/Sea Cost - Cartage',
			'91009' => 'Air/Sea Cost - Other',
		)),
		array('name' => 'desc', 'class' => 'CEditableColumn'),
		array('name' => 'currency', 'class' => 'CEditableColumn', 'type' => 'list', 'filter' => Invoice::$currencies),
		array('name' => 'accrual_amount', 'class' => 'CEditableColumn', 'footer' => $currency . ' ' . AppHelper::money_format("%i", $ledger->getTotal($ledger_data, 'accrual_amount'))),
		array('name' => 'actual_amount', 'footer' => $currency . ' ' . AppHelper::money_format("%i", $ledger->getTotal($ledger_data, 'actual_amount')), 'class' => 'CEditableColumn'),
		array('name' => 'gst','class' => 'CEditableColumn', 'value' => '$data->getTaxType()', 'type' => 'list',
			'filter'=> Invoice::$InvoiceCostTaxRate ),
		array('name' => 'billing_cref', 'class' => 'CEditableColumn'),
		array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}',
			'buttons' => array(
				'delete' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("wmsTask/billingGridDelete", array("id" => $data->id))',
					'options' => array('class' => 'delete_btn'),
				),
			),
		)
	),
));
Yii::app()->name = $app_name;
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
});
</script>