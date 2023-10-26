<style>
.checkbox-hidden input[type="checkbox"] {
	display: none;
}
</style>

<div style="right: 20px;position: absolute;">
	<a href="#" data-dropdown="#<?=$_GET['tabid'];?>_rec_dropdown" ><div style="background-position:-48px -688px" class="icon"></div>Export</a>
	<div id="<?=$_GET['tabid'];?>_rec_dropdown" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
		<ul class="dropdown-menu">
			<?php if ($model->client_type == Reconciliation::AUPOST_INVOICE) { ?>
				<li><a href="<?=$this->createUrl('invoice/exportManifestAmountDiff',['id' => $model->id]);?>" target="_blank" title="Export Manifest Amount Diff">Export Dispute Detail</a></li>
			<?php } else { ?>
				<li><a href="<?=$this->createUrl('invoice/reconciliationExport',['id' => $model->id, 'dispute' => true]);?>" target="_blank" title="Export Reconciliation">Export Dispute Detail</a></li>
			<?php } ?>
	   </ul>
	</div>

	<?php
	$cant = ReconciliationLine::model()->find('shipment_no = "Can\'t upload line difference" AND value > 0');
	if (!empty($cant)) {
		echo '<a href="' . Yii::app()->createUrl('billing/createRecLine', ['id' => $model->id]) . '" class="jqm_link"><div class="icon" style="background-position:-16px 0"></div> Add Line</a>';
	}
	?>
</div>
<h1><?=$model->invoice_no?> Dispute</h1>

<?php
$lines = new ReconciliationLine('search');
$lines->unsetAttributes();
if (isset($_GET['ReconciliationLine'])) {
	$lines->attributes = $_GET['ReconciliationLine'];
}
$lines->setAttribute('parent_id', $model->id);

$ec = !empty($ec) ? $ec : new CDbCriteria;
$ec->order = 't.consol_id ASC';

$form = $this->beginWidget('CActiveForm', array(
	'id' => 'dispute-batch-accept-form',
	'enableAjaxValidation' => false,
	'action' => Yii::app()->createUrl('billing/disputeBatchAccept'),
));

echo CHtml::submitButton('Batch accept') . '<br />';

if ($model->client_type == Reconciliation::AUPOST_INVOICE) {
	$ec->addCondition('t.my_value < t.value');
	$columns = array(
		array('id' => 'selectedItems', 'class' => 'CCheckBoxColumn', 'cssClassExpression' => '!empty($data->mdata["confirmed"]) || preg_match("/can\'t upload line difference/i", $data->shipment_no) ? "checkbox-hidden" : ""',),
		array('name' => 'shipment_no', 'header' => 'Manifest No', 'type' => 'raw', 'value' => '$data->getAupostInfo()'),
		array('header' => 'Consol No', 'value' => '$data->getConsolNos()'),
		array('name' => 'postcode', 'header' => 'Type', 'value' => '$data->postcode', 'filter' => CHtml::dropDownList('ReconciliationLine[postcode]', $lines->postcode, $this->t(['eParcel' => 'eParcel', 'Return to sender' => 'Return to sender', 'ELMS' => 'ELMS', 'UNKNOWN' => 'UNKNOWN']), array('prompt'=>$this->t('All'))),),
		'value',
		array('header' => 'Claim Amount (Excl. GST)', 'value' => 'floatval(@$data->mdata["claim_value"])'),
		array('header' => 'Diff(Claim Amount-Amount) (Excl. GST)', 'value' => 'number_format(floatval(@$data->mdata["claim_value"]) - $data->value, 2, ".", "")'),
		array('name' => 'my_value', 'header' => 'Accept Amount (Excl. GST)'),
		array('name' => 'chargeInvoiceDiff', 'header' => 'Diff(Accept Amount-Amount) (Excl. GST)', 'filter' => false, 'value' => '$data->getCostDiff()'),
		// array('name' => 'my_charge'),
		// 'subAmount',
	);
} else {
	if (in_array($model->client_type, [Reconciliation::STARTRACK_TYPE, Reconciliation::TNT_CLIENT])) {
		$ec->addCondition('(t.my_value * 1.05 < t.value OR t.my_value + 10 < t.value OR (t.shipment_id = 0 AND t.shipment_no != "Admin Fee") OR t.consol_id = 0)');
	} else {
		$ec->addCondition('(t.my_value < t.value OR (t.shipment_id = 0 AND t.shipment_no != "Admin Fee") OR t.consol_id = 0)');
	}
	$columns = array(
		array('id' => 'selectedItems', 'class' => 'CCheckBoxColumn', 'cssClassExpression' => '!empty($data->mdata["confirmed"]) || preg_match("/can\'t upload line difference/i", $data->shipment_no) ? "checkbox-hidden" : ""'),
		array('name' => 'shipment_no', 'type' => 'raw', 'value' => '!empty($data->shipment_id) ? "<a href=\"".Yii::app()->createURL("imParcel/updateByNo", array("no" => $data->shipment_no))."\" class=\"tab_link\" title=\"".$data->shipment_no."\">".$data->shipment_no."</a>" : $data->shipment_no'),
		array('name' => 'consol_no', 'type' => 'raw', 'value' => 'empty($data->consol_id) ? "" : "<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" => @$data->consol_id))."\" class=\"tab_link\" title=\"".@$data->consol->no."\">".@$data->consol->no."</a>"',),
		array( 'name' => 'invoice_no', 'value' => '$data->getInvoiceNoColumnData()'),
		'postcode',
		'weight',
		'courier_cubic',
		array('header' => '250To167 Weight', 'value' => '$data->getCourierToOurWeight()', 'cssClassExpression' => '$data->getCourierToOurWeight()>$data->ourChargeWeight()? "cloumn_red_rec" : ""'),
		array('header' => 'Our Charge Weight', 'value' => '$data->ourChargeWeight()'),
		'manifest_weight',
		'value',
		array('name' => 'my_value', 'value' => '$data->getMyValue()'),
		array('name' => 'chargeInvoiceDiff', 'filter' => false, 'value' => '$data->getCostDiff()'),
		// array('name' => 'my_charge'),
		// 'subAmount',
		array('name' => 'tnt_type', 'visible' => $model->client_type == Reconciliation::TNT_CLIENT, 'filter' => CHtml::dropDownList('ReconciliationLine[tnt_type]', $lines->tnt_type, ['Shipment' => 'Shipment', 'Fuel' => 'Fuel', 'LR' => 'LR', 'MC' => 'MC', 'NEC' => 'NEC', 'OS0' => 'OS0', 'OS1' => 'OS1', 'OS2' => 'OS2', 'OS3' => 'OS3', 'OS4' => 'OS4', 'OS5' => 'OS5', 'PR' => 'PR', 'RES' => 'RES', 'RET' => 'RET', 'RMP' => 'RMP', 'RSP ' => 'RSP ', 'RMD' => 'RMD', 'RSD' => 'RSD', 'RED' => 'RED', 'MHP' => 'MHP'], ['prompt' => 'All']), 'value' => '@$data->mdata["tnt_type"]'),
		array('name' => 'st_type', 'visible' => $model->client_type == Reconciliation::STARTRACK_TYPE, 'filter' => CHtml::dropDownList('ReconciliationLine[st_type]', $lines->st_type, ['Shipment' => 'Shipment', 'Fuel' => 'Fuel'], ['prompt' => 'All']), 'value' => '@$data->mdata["st_type"]'),
	);
}

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => $_GET['tabid'] . '-dispute-grid',
	'cssFile' => false,
	'dataProvider' => $lines->search(false, $ec, true, 30),
	'selectableRows' => 2,
	'filter' => $lines,
	'columns' => array_merge(
		$columns,
		array(array(
			'class' => 'oButtonColumn',
			'template' => '{confirm}{view}{delete}',
			'buttons' => array
			(
				'confirm' => array(
					'imageUrl' => false,
					'options' => array('class' => 'jqm_link grid_edit_btn'),
					'url' => 'Yii::app()->createUrl("billing/disputeConfirmCost", ["id" => $data->id])',
					'label' => 'Accept',
					'visible' => '!empty($data->mdata["confirmed"]) || preg_match("/can\'t upload line difference/i", $data->shipment_no) ? false : true',
				),
				'view' => array(
					'imageUrl' => false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
					'url' => 'Yii::app()->createUrl("billing/disputeConfirmCost", ["id" => $data->id])',
					'label' => 'View',
					'visible' => '!empty($data->mdata["confirmed"]) ? true : false',
				),
				'delete' => array(
					'imageUrl' => false,
					'options' => array('class' => 'ajax_link grid_delete_btn'),
					'url' => 'Yii::app()->createUrl("billing/disputeDelete", ["id" => $data->id])',
					'label' => 'Delete',
					'visible' => '!empty($data->mdata["confirmed"]) || preg_match("/can\'t upload line difference/i", $data->shipment_no) || !empty($data->shipment_id) ? false : true',
				),
			),
		)),
	),
));

$this->endWidget();
?>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	tab.on('onOpen', function(){
		$('#<?=$_GET["tabid"]?>-dispute-grid', panel).yiiGridView('update');
	});

	$('#dispute-batch-accept-form').on('success', function() {
		$('#<?=$_GET["tabid"]?>-dispute-grid', panel).yiiGridView('update');
	});
});
</script>