<h2>For Import Parcels</h2>
<?php
$ledger = new Ledger('search');
$ledger->type = 40;
$ledger_dp = $ledger->search();
$ledger_data = $ledger_dp->getData();
$currency = 'AUD';
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id'=>$_GET["tabid"].'_import_ledger-grid',
	'cssFile' => false,
	'dataProvider'=> $ledger_dp,
	'formUrl' => $this->createUrl('ledger/grid', array('type' => 40, 'status' => 1,'model' => 'ImcoConsol','fid' => 0)),
	'filter'=>null,
	'summaryText' => '',
	'showQuickBar' => true,
	'afterSave' => "function(r){
		if(r.done == true){
			myApp.notice(r.msg, 5000);
		}else{
			myApp.alert(r.msg, false);
		}
		return r.done;
	}",
	'columns'=>array(
        array('name' => 'from_id', 'class' => 'CEditableColumn',  'type' => 'list', 'filter' => $ledger->getAllSuppliers()),
        array('name' => 'chgcode','class' => 'CEditableColumn','type' => 'autocomplete' ,
            'value' => '$data->chgcode' ,
            'acOptions' => array('source' => 'chargeCode/chargeCodeSuggest' )
        ),
        array('name' => 'notes', 'class' => 'CEditableColumn','footer' => 'Total: ', 'footerHtmlOptions' => array('align' => 'right')),
        array('name' => 'accrual_amount', 'class' => 'CEditableColumn','footer' => $currency . ' ' . AppHelper::money_format("%i", $ledger->getTotal($ledger_data,'accrual_amount'))),
        array('name' => 'currency', 'class' => 'CEditableColumn',  'type' => 'list', 'filter' => Ledger::$currencies),
        array('name' => 'exchange_rate', 'class' => 'CEditableColumn'),
		array('name' => 'ref', 'class' => 'CEditableColumn'),
		array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}')
	),
));
?>
<br />
<h2>For Export Parcels</h2>
<?php
$exledger = new Ledger('search');
$exledger->type = 50;
$exledger_dp = $exledger->search();
$exledger_data = $exledger_dp->getData();
$currency = 'AUD';
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
    'id'=>$_GET["tabid"].'_export_ledger-grid',
    'cssFile' => false,
    'dataProvider'=> $exledger_dp,
    'formUrl' => $this->createUrl('ledger/grid', array('type' => 50, 'status' => 1,'model' => 'ExcoConsol','fid' => 0)),
    'filter'=>null,
    'summaryText' => '',
    'showQuickBar' => true,
    'afterSave' => "function(r){
		if(r.done == true){
			myApp.notice(r.msg, 5000);
		}else{
			myApp.alert(r.msg, false);
		}
		return r.done;
	}",
    'columns'=>array(
        array('name' => 'from_id', 'class' => 'CEditableColumn',  'type' => 'list', 'filter' => $exledger->getAllSuppliers()),
        array('name' => 'chgcode','class' => 'CEditableColumn','type' => 'autocomplete' ,
            'value' => '$data->chgcode' ,
            'acOptions' => array('source' => 'chargeCode/chargeCodeSuggest' )
        ),
        array('name' => 'notes', 'class' => 'CEditableColumn','footer' => 'Total: ', 'footerHtmlOptions' => array('align' => 'right')),
        array('name' => 'accrual_amount', 'class' => 'CEditableColumn','footer' => $currency . ' ' . AppHelper::money_format("%i", $exledger->getTotal($ledger_data,'accrual_amount'))),
        array('name' => 'currency', 'class' => 'CEditableColumn',  'type' => 'list', 'filter' => Ledger::$currencies),
        array('name' => 'exchange_rate', 'class' => 'CEditableColumn'),
        array('name' => 'ref', 'class' => 'CEditableColumn'),
        array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save}')
    ),
));
?>
<script type="text/javascript">
$(function(){
	var pane = $('#<?=$_GET["tabid"];?>');
});
</script>