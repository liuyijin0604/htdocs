<style>
.uploading {
	background: url("https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif") no-repeat 0 0 !important;
	background-size: 20px 20px !important;
	background-color: white !important;
}
</style>

<h3>Arranged Payments</h3>
<?php
$model = new PaymentArrange;
$model->unsetAttributes();
$model->attributes = @$_GET['PaymentArrange'];

$ec = new CDbCriteria;
$ec->order = 'CASE t.status WHEN ' . PaymentArrange::PAYMENT_ARRANGE_STATUS_NEW . ' THEN 1 ELSE 2 END, created ASC';

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'billing-streamline-pay-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(true, 30, 't.id DESC'),
	'filter' => $model,
	'columns' => array(
		array('name' => 'no'),
		array('name' => 'created'),
		array('name' => 'user_id', 'value' => '$data->getUser()'),
		array('name' => 'status', 'value' => '$data->getStatus()', 'filter' => CHtml::dropDownList('PaymentArrange[status]', $model->status, $this->t(PaymentArrange::$states), array('prompt' => $this->t('All'))),),
		array('name' => 'currency', 'value' => '$data->getCurrency()', 'filter' => CHtml::dropDownList('PaymentArrange[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt' => $this->t('All'))),),
		array('name' => 'total'),
		// array('name' => 'approved'),
		array(
			'class' => 'oButtonColumn',
			'template' => '{update} {file}',
			'buttons' => array(
				'update' => array(
					'imageUrl' => false,
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("billing/updatePaymentArrange", ["id" => $data->id])',
					'options' => array('class' => 'tab_link grid_edit_btn', 'data-win-class' => 'XXL', 'title' => '$data->no'),
				),
				'file' => array(
					'imageUrl' => false,
					'visible' => '$data->status == PaymentArrange::PAYMENT_ARRANGE_STATUS_APPROVED ? true : false',
					'url' => 'Yii::app()->createUrl("billing/exportPaymentArrange", ["id" => $data->id])',
					'options' => array('class' => 'grid_print_btn', 'target' => '_blank'),
					'label' => 'File',
				),
			),
		),
	),
));
?>

<script>
$(function() {
	var tab = $('#<?=$_GET["tabid"]?>');
	var panel = tab.data('panel');
});
</script>