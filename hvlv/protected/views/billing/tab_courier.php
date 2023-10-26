<?php
$model = new Reconciliation('search');
$model->unsetAttributes();
if (isset($_GET['Reconciliation'])) {
	$model->attributes = $_GET['Reconciliation'];
}
$model->notype = array_keys(array_diff(Reconciliation::$types, Reconciliation::$checks));
if (!in_array($model->client_type, array_keys(Reconciliation::$checks))) {
	unset($model->client_type);
}

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'billing-stream-line-check-courier-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(),
	'filter' => $model,
	'htmlOptions' => array('style' => 'min-height: 700px'),
	'columns' => array(
		array('name' => 'client_type', 'value' => '$data->getType()', 'filter' => CHtml::dropDownList('Reconciliation[client_type]', $model->client_type, $this->t(Reconciliation::$checks), array('prompt' => $this->t('All')))),
		array('name' => 'invoice_no', 'header' => 'Invoice No', 'type' => 'raw', 'value' => '"<a href=\"" . Yii::app()->createUrl("invoice/viewReconciliation", ["id" => $data->id]) . "\" class=\"tab_link\" title=\"" . $data->invoice_no . "\">" . $data->invoice_no . "</a>"'),
		'invoice_date',
		array('header' => 'Entered Total (Excl. GST)', 'type' => 'raw', 'value' => '!empty($data->mdata["invoice_amount"]) ? number_format($data->mdata["invoice_amount"] / 1.1, 2, ".", "") . "<br>(" . number_format($data->mdata["invoice_amount"], 2, ".", "") . ")" : ""'),
		array('name' => 'invoice_total', 'header' => 'Uploaded Total (Excl. GST)', 'value' => '$data->getInvoiceTotal().$data->getDeclareChargeDiff(true).$data->getFuelTotal()'),
		array('name' => 'my_total', 'header' => 'Accepted Total (Excl. GST)', 'value' => '$data->getAcceptTotal()'),
		array('name' => 'deviation', 'header' => 'Deviation (Excl. GST)', 'type' => 'raw', 'value' => '$data->getAcceptDeviation() == 0 ? $data->getAcceptDeviation() : "<a href=\"" . Yii::app()->createUrl("billing/viewDispute", ["id" => $data->id]) . "\" class=\"tab_link\" title=\"" . $data->invoice_no . "\" style=\"color: red\">" . $data->getAcceptDeviation() . "</a>"', 'filter' => false),
		array('name' => 'percent', 'value' => '$data->getAcceptPercent()', 'filter' => false),
		array('name' => 'ot_inv', 'value' => '$data->getOTinvStatus()', 'type' => 'raw', 'filter' => CHtml::dropDownList('Reconciliation[ot_inv]', $model->ot_inv, $this->t(Reconciliation::$ot_inv_states), array('prompt' => $this->t('All')))),
		array('header' => 'Attached File', 'type' => 'raw', 'value' => '"<a href=\"". $data->getAttachedFileLink() ."\" target=\"_blank\">" . $data->getAttachedFileName() . "</a>"'),
		array(
			'class' => 'oButtonColumn',
			'template' => '{weight_check}',
			'buttons' => array(
				'details' => array(
					'imageUrl' => false,
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("invoice/viewReconciliation", ["id" => $data->id])',
					'options' => array('class' => 'tab_link grid_view_btn'),
					'label' => $this->t('Details'),
				),
				'dispute' => array(
					'imageUrl' => false,
					'visible' => '$data->client_type != Reconciliation::AUPOST_INVOICE',
					'url' => 'Yii::app()->createUrl("billing/viewDispute", ["id" => $data->id])',
					'options' => array('class' => 'tab_link grid_view_btn', 'title' => '$data->invoice_no . " Dispute"'),
					'label' => $this->t('Dispute'),
				),
				'weight_check' => array(
					'imageUrl' => false,
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("importWeightCheck/viewWeightDetails", ["id" => $data->id])',
					'options' => array('class' => 'tab_link grid_view_btn', 'title' => '$data->invoice_no . " Weight Check"'),
					'label'=>$this->t('Weight Diff'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	tab.on('onOpen', function(){
		$('#billing-stream-line-check-courier-grid', panel).yiiGridView('update');
	});
});
</script>
