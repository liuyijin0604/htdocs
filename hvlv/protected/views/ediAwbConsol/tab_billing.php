<style>
.row-fix {
	color: red;
}
</style>


<h2>Cost</h2>
<?php
$ledger = new CogsLine('search');
$ledger->unsetAttributes();
$ledger->consol_id = $model->id;
//$ledger->model = get_class($model);
$ledger_dp = $ledger->search();
$ledger_data = $ledger_dp->getData();
$owner = $model->owner;
$currency = 'AUD';
if ( isset($owner->extra['currency']) && isset(Invoice::$currencies[$owner->extra['currency']])) {
    $currency = Invoice::$currencies[$owner->extra['currency']];
}
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id'=>$_GET["tabid"].'_ledger-grid',
	'cssFile' => false,
	'dataProvider'=> $ledger_dp,
	'formUrl' => $this->createUrl('billing/importGrid', array('fid'=>$model->id)),
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
	'rowCssClassExpression' => 'in_array($data->status, [4,10]) ? "row-fix" : ""',
	'columns'=>array(
		//array('name' => 'date', 'class' => 'CEditableColumn', 'inputOptions' => array('class' => 'date_input')),
        // array('name' => 'org_id', 'class' => 'CEditableColumn',  'type' => 'list', 'filter' => $ledger->getAllSuppliers()),
        array('header' => 'ref', 'value'=>'$data->getRef()'),
        array('name' => 'item_code'),
		array('name' => 'org_id', 'class' => 'CEditableColumn', 'type' => 'autocomplete', 'value' => '!empty($data->cust->name) ? $data->cust->name : ""', 'acOptions' => array('source' => 'org/supplierSuggest')),
        array('name' => 'charge_code','class' => 'CEditableColumn','type' => 'autocomplete' ,
            'value' => '$data->getCCodeDesc()' ,
            'acOptions' => array('source' => 'chargeCode/chargeCodeImportFixed' )
        ),
//        array('name'=>'to_id','class' => 'CEditableColumn','type' => 'autocomplete','value'=>'$data->getToOrgName()','acOptions'=>array('source' => 'imcoConsol/getConsolClientName?consol_no='.$model->no)),
        array('name' => 'desc', 'class' => 'CEditableColumn','footer' => 'Total: ', 'footerHtmlOptions' => array('align' => 'right')),
        array('name' => 'accrual_amount', 'class' => 'CEditableColumn','footer' => $currency . ' ' . AppHelper::money_format("%i", $ledger->getTotal($ledger_data,'accrual_amount'))),
        array('name' => 'actual_amount', 'footer' => $currency . ' ' . AppHelper::money_format("%i", $ledger->getTotal($ledger_data,'actual_amount'))),
		array('name' => 'gst','class' => 'CEditableColumn', 'value' => '$data->getTaxType()', 'type' => 'list',
			'filter'=> Invoice::$InvoiceCostTaxRate ),
        array('name' => 'currency', 'class' => 'CEditableColumn',
            'value' => function($data){ return empty($data) ? 1 : $data->currency;},
            'type' => 'list', 'filter' => Invoice::$currencies),
       // array('name' => 'exchange_rate', 'class' => 'CEditableColumn'),
      // array( 'name' => 'status','class' => 'CEditableColumn', 'type' => 'list', 'filter' => BillingLine::$states),

       // array('name' => 'no', 'class' => 'CEditableColumn'),
		//array('name' => 'ref', 'class' => 'CEditableColumn'),
		array('class'=>'CEditableButtonColumn',
			'template' => '{edit} {cancel} {save} {delete}',
			'buttons' => array(
				'delete' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("billing/importGridDelete", ["id" => $data->id])',
					'visible' => '$data->status < 3',
					'options' => array('class' => 'delete_btn'),
				),
			),
		),
	),
));
?>
<?php
    // get latest uploaded zone map file
    $attachements = FileRepo::model()->findAll('fid = :oid and type in (10,17)', [':oid' => $model->id]);
    $index = 1;
    if(!empty($attachements)): 
    ?>
<h2>Letter Manifest</h2>
    <div><ul id="attachements-div">
            <?php
            foreach ( $attachements as $attachement ) {
                if(!preg_match("/letter_manifest.pdf/i", $attachement->name))     continue;
                $line = '<li>' . $index++ . '. <a target="_blank" href="' . $attachement->getUrl() . '" >' . $attachement->name . '</a></li>';
                echo $line;
            }
            ?>
        </ul></div>
<?php endif;?>
<br />
<h2>Revenue</h2>
<?php
$app_name = Yii::app()->name;
if (strtotime($model->eta) >= strtotime('2020-08-01')) Yii::app()->name = 'TLA';
//$dp = new Invoice('search');
//$dp->unsetAttributes();
//$dp->consol_id = $model->id;

// remove duplicate credit paid invoice
// in case original invoice has been frozen which will be paid by credit note and create another new invoice
// so this kind of invoice should not be showing
$invoices = Invoice::model()->findAll('consol_id = :cid AND status NOT IN (10)',[':cid' => $model->id]);

/*$lines = array();
foreach ( $invoices as $invoice ){
    $bshowing = true;
    if ( $invoice->status == Invoice::INVOICE_STATUS_PAID ) {
        // paid invoice
        $paylines = $invoice->payments;
        if ( count($paylines) == 1 ) {
            // only one credit note type payment
            $payment = $paylines[0]->payment;
            if ( $payment->type == Payment::PAYMENT_TYPE_CREDIT_NOTE &&
                strstr($payment->ref,'Revert Invoice') !== false ) {
                // payment only for credit frozen invoice
                $bshowing = false;
            }
        }
    }
    if ( $bshowing ) {
        $lines[$invoice->id] = $invoice;
    }
}
*/

$invoices = array_merge($invoices, $model->getRTSInvoices());

$dp = new CArrayDataProvider($invoices,array(
    'id' => 'imconsol_real_invoices-'.$_GET["tabid"]
));
$dp->pagination=array('pageSize' => 30,);
$total = 0;
foreach ($invoices as $invoice) {
	// $total += $invoice->total;
	$total += $invoice->getTotalByConsol($model->id);
}
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_ordereport-grid',
	'cssFile' => false,
	'dataProvider' => $dp,// $dp->search(),
	'filter' => null,
	'enableSorting' => false,
	'columns'=>array(
		array('name' => 'no', 'value' => '$data->no', ),
		array('name' => 'bill_to', 'value' => '$data->getInvoiceOrgName()', ),
		array('name' => 'status', 'value' => '$data->getStatus()', 'footer' => 'Total: ', 'footerHtmlOptions' => array('align' => 'right')),
		array('header' => 'Revenue', 'value' => '$data->getCurrency() . " " . $data->getTotalByConsol(' . $model->id . ')', 'footer' => $currency . ' ' . AppHelper::money_format("%i", $total)),
		array('header' => 'Invoice Total', 'value' => '$data->getCurrency() . " " . $data->total'),
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

<h2>Credit Notes</h2>
<?php
$creditnotes = Payment::model()->findAll('type = 5 AND (ref LIKE :ref or JSON_VALUE(meta, "$.consol") LIKE :ref) AND status != 9', [':ref' => '%' . $model->no . '%']);
$dp = new CArrayDataProvider($creditnotes, array(
	'id' => 'imconsol_real_creditnotes-' . $_GET["tabid"]
));
$dp->pagination = array('pageSize' => 30);
$total = 0;
foreach ($creditnotes as $creditnote) {
	$total += $creditnote->amount;
}
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_creditnote-grid',
	'cssFile' => false,
	'dataProvider' => $dp,// $dp->search(),
	'filter' => null,
	'enableSorting' => false,
	'columns'=>array(
		array('name' => 'no', 'value' => '$data->no', ),
		array('name' => 'bill_to', 'value' => '$data->getInvoiceOrgName()', ),
		'ref',
                array('name' => 'status', 'value' => '$data->getStatus()', 'footer' => 'Total: ', 'footerHtmlOptions' => array('align' => 'right')),
		array('header' => 'Credit Note Total', 'value' => '$data->getCurrency().$data->amount', 'footer' => $currency . ' ' . AppHelper::money_format("%i", $total)),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}',
			'buttons'=>array
			(
				'view' => array(
					'url' => 'Yii::app()->createURL("payment/printCreditNote", array("id" => $data->id))',
					'imageUrl'=>false,
					'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
				),
			),
		),
)));
//else:
/*
if(!empty($recs)):
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
endif;*/
Yii::app()->name = $app_name;
?>
<script>
    $(function(){
        var tab=$("#<?=$_GET['tabid']?>");
        var panel=tab.data('panel');
//        
//        panel.on('blur','input.egacol_to_id',function(){
//           if($(this).prevAll("input[type=hidden]").val()==0 &&!($(this).val()==''||$(this).val()=='All')){
//              $(this).css('background-color','red'); 
//           }
//        });

			$(panel).on('dblclick', 'input[id*="accrual_amount"]', function() {
				$(this).val($(this).parent().next().html());
			});
    });
</script>
