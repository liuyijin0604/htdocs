<?php
[$app_name, Yii::app()->name] = [Yii::app()->name, 'TLA'];
$dp = new InvLine('search');
$dp->unsetAttributes();
$dp->model = 'WmsInvoiceLine';

$ec = new CDbCriteria;
$ec->condition = '(invoice.status != 10 OR invoice.id IS NULL) AND t.fid in (' . $model->id;
foreach ($model->subTasks as $task) {
	$ec->condition .= ',' . $task->id;
}
$ec->condition .= ')';
$ec->with = 'invoice';
$ec->order = 't.inv_id DESC';

$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id' => $_GET['tabid'] . '_wmsInvoice-grid',
	'cssFile' => false,
	'dataProvider' => $dp->search($ec),
	'formUrl' => $this->createUrl('wmsTask/invLineGrid', array('id' => $model->id)),
	'filter' => null,
	'summaryText' => '',
	'editable' => true,
	'showQuickBar' => true,
	'afterSave' => 'function(r) {
		if (r.done == true) {
			myApp.notice(r.msg, 5000);
		} else {
			myApp.alert(r.msg, false);
		}
		return r.done;
	}',
	'columns' => array(
		array('name' => 'det', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 100]),
		array('name' => 'amount', 'header' => 'Rate', 'class' => 'CEditableColumn', 'type' => 'raw', 'value' => 'CHtml::textField("InvLine[amount]", number_format($data->amount - $data->gst, 2))'),
		array('name' => 'qty', 'class' => 'CEditableColumn'),
		array('name' => 'amount', 'value' => 'number_format(($data->amount - $data->gst) * $data->qty, 2)'),
		array('name' => 'gst', 'header' => 'GST Amount', 'value' => 'number_format($data->gst * $data->qty, 2)', 'htmlOptions' => array('class' => 'gst')),
		array('name' => 'tax', 'class' => 'CEditableColumn', 'value' => '$data->getTaxType()', 'type' => 'list', 'filter'=> Invoice::$InvoiceRevenueTaxRate),
		array('name' => 'invoice.no', 'value' => '!empty($data->invoice) ? $data->invoice->no : ""'),
		array('name' => 'invoice.status', 'value' => '!empty($data->invoice) ? $data->invoice->getStatus() : ""'),
		array('class'=>'CEditableButtonColumn',
			'template' => '{edit} {cancel} {save} {delete}',
			'buttons' => array(
				'edit' => array(
					'visible' => '$data->inv_id == 0 || $data->invoice->status == 1',
				),
				'cancel' => array(
					'visible' => '$data->inv_id == 0 || $data->invoice->status == 1',
				),
				'save' => array(
					'visible' => '$data->inv_id == 0 || $data->invoice->status == 1',
				),
				'delete' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("wmsTask/invLineGrid", ["id" => $data->id])',
					'options' => array('class' => 'delete_btn'),
					'visible' => '$data->inv_id == 0 || $data->invoice->status == 1',
				)
			),
		),
	),
));
Yii::app()->name = $app_name;
?>

<div>
	<h2 style="margin-bottom: 0">Note</h2>
	<?php echo CHtml::textarea('note', @$model->mdata['other_other'], array('style' => 'height:300px; width:600px')); ?>
</div>

<script type="text/javascript">
$(function() {
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	$('select').on('change', function() {
		var amount = $(this).parent().parent().find('#InvLine_amount').val();
		if ($(this).val() == 'OUTPUT') {
			$(this).parent().parent().find('.gst').html((amount * 0.1).toFixed(2));
		} else {
			$(this).parent().parent().find('.gst').html('0.00');
		}
	});
});
</script>