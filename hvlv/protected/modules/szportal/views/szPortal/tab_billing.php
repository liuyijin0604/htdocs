<h2>Cost</h2>
<?php
$ledger = new BillingLine('search');
$ledger->unsetAttributes();
$ledger->billing_ref = $model->no;
//$ledger->model = get_class($model);
$ledger_dp = $ledger->search();
$ledger_data = $ledger_dp->getData();
$owner = $model->owner;
$currency = 'AUD';
if ( isset($owner->extra['currency']) ) $currency = Invoice::$currencies[$owner->extra['currency']];
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id'=>$_GET["tabid"].'_exledger-grid',
	'cssFile' => false,
	'dataProvider'=>$ledger_dp,
	'formUrl' => $this->createUrl('billing/exportGrid', array( 'fid'=>$model->id)),
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
     //   array('name' => 'invoice_number', 'class' => 'CEditableColumn'),
     //   array('name' => 'invoice_date', 'class' => 'CEditableColumn', 'inputOptions' => array('class' => 'date_input')),
     //   array('name' => 'invoice_due_date', 'class' => 'CEditableColumn', 'inputOptions' => array('class' => 'date_input')),
        array('name' => 'org_id','class' => 'CEditableColumn','type' => 'autocomplete' ,
            'value' => 'empty($data->cust) ? "" : $data->cust->shortName(3)' ,
            'acOptions' => array('source' => 'org/supplierSuggest' )
        ),
        array('name' => 'weight', 'class' => 'CEditableColumn'),
      //  array('name' => 'volume', 'class' => 'CEditableColumn'),
        array('name' => 'charge_weight', 'class' => 'CEditableColumn'),

        array('header' => 'Type','name' => 'item_code','class' => 'CEditableColumn','type' => 'list' ,
            'value' => '$data->getItemcodeDesc()' ,
            'filter' => array_merge(EdiJob::getChargeItemTypes(), ExcoConsol::$chargeItems)),
        array('name' => 'currency', 'class' => 'CEditableColumn',  'type' => 'list', 'filter' => Invoice::$currencies),
        array('name' => 'accrual_amount', 'class' => 'CEditableColumn', 'footer' => $currency . ' ' . AppHelper::money_format("%i", $ledger->getTotal($ledger_data,'accrual_amount'))),
        array('name' => 'actual_amount', 'footer' => $currency . ' ' . AppHelper::money_format("%i", $ledger->getTotal($ledger_data,'actual_amount'))),
        array('name' => 'gst','class' => 'CEditableColumn', 'value' => '$data->getTaxType()','type' => 'list',
            'filter'=> Invoice::$InvoiceCostTaxRate ),
        'billing_cref',

        array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}')
	),
));
?>


<?php
if ( isset($aflines) && !empty($aflines) ) {

    echo '<div style="width: 70%;background-color: #a9a9a9;"><span style="font-size: 20px;font-weight: bold;">Please fix as following real cost details</span>';
    $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'op-ex-af-billing-import-list-lines-grid',
            'cssFile' => false,
            'dataProvider'=>$aflines->search(),
            'columns'=>array(
                'glcode',
                'desc',
                'gst',
                'amount'
            ))
    );
    echo '</div>';
}
?>

<br />
<h2>Revenue</h2>
<?php
$recs = $model->getRecIds();
if(empty($recs)):
    $dp = new Invoice('search');
    $dp->consol_id = $model->id;
    $dp_invoices = $dp->search();
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'ordereport-grid',
        'cssFile' => false,
        'dataProvider' => $dp_invoices,
        'filter' => null,
        'enableSorting' => false,
        'columns'=>array(
            array('name' => 'no', 'value' => '$data->id', ),
            array('name' => 'bill_to', 'value' => '$data->cust->name',  'footer' => 'Total: ', 'footerHtmlOptions' => array('align' => 'right')),
            array('header' => 'Invoice Total', 'value' => '$data->getCurrency().$data->total',  'footer' => $currency . ' ' . AppHelper::money_format("%i", $dp->getTotalByInvoices($dp_invoices->getData()))),
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
else:
$m = new Manifest('search');
$dp = $model->billingList();
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'ordereport-grid',
	'cssFile' => false,
	'dataProvider' => $dp,
	'filter' => null,
	'enableSorting' => false,
	'columns'=>array(
		'id',
		array('header' => 'File', 'type' => 'raw', 'value' => '"<a href=\"".$data->getFileLink()."\" target=\"_blank\">".$data->getFileName()."</a>"',),
		array('name' => 'fwd_id', 'value' => '$data->owner->name', 'footer' => 'Total: ', 'footerHtmlOptions' => array('align' => 'right')),
		array('header' => 'Packs', 'value' => '$data->totPacks()', 'footer' => $m->getTotal($dp->getData(), 'packs')),
		array('header' => 'Total Weight', 'value' => '$data->totWeight()', 'footer'=> $m->getTotal($dp->getData(), 'weight')),
		array('header' => 'Total CBM', 'value' => '$data->totCBM()', 'footer'=> $m->getTotal($dp->getData(), 'cbm')),
        array('header' => 'Invoice Total', 'value' => '$data->getCurrency()." " .AppHelper::money_format("%i", $data->totChargePro())', 'footer'=> $m->getTotal($dp->getData(), 'chargepro')),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}',
			'buttons'=>array
			(
				'view' => array(
					'url' => 'Yii::app()->createURL("invoice/print", array("id" => $data->invoice->id))',
					'imageUrl'=>false,
					'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
					'visible' => '!empty($data->invoice->id)'
				),
			),
		),
)));
endif;
?>


<script type="text/javascript">
$(function(){
	var pane = $('#<?=$_GET["tabid"];?>');
});
</script>