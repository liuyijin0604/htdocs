<h1><?=$this->t('Billing waiting for check');?></h1>

<?php
$ledger = new BillingLine('search');
$ledger->unsetAttributes();
$ec = new CDbCriteria;
$ec->addCondition('status in (4, 10)');
$ledger_dp = $ledger->search(true, 30, 't.id DESC', $ec);
$ledger_data = $ledger_dp->getData();
?>

<?php
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id' => 'import-billing-check-grid',
	'cssFile' => false,
	'showQuickBar' => false,
	'dataProvider' => $ledger_dp,
	'filter' => $ledger,
	'columns' => array(
		// array('header' => 'Console No.', 'type' => 'raw', 'name' => 'billing_ref', 'value' => '$data->getNo()'),
		// array('name' => 'billing_cref', 'type' => 'raw', 'value' => '"<a href=\"" . Yii::app()->createUrl("billing/lineFix", array("id" => $data->id)) . "\" class=\"jqm_link\" data-win-class=\"L\">" . $data->billing_cref . "</a>"'),
		'billing_cref',
		array('name' => 'billing_ref', 'type' => 'raw', 'value' => '$data->getNo()'),
		array('name' => 'status', 'value' => '$data->getStatus()'),
		array('name' => 'client', 'value' => ' ( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->shortName(2)'),
		'actual_amount',
		'gst',
		'gst_amount',
		array('name' => 'currency', 'value' => '$data->getCurrency()',
			'filter' => CHtml::dropDownList('Billing[currency]', $ledger->currency, $this->t(Invoice::$currencies), array('prompt' => $this->t('All')))),
		array('name' => 'dpt_id', 'value' => '$data->getDptName()',
			'filter' => CHtml::dropDownList('Billing[dpt_id]', $ledger->dpt_id, $this->t(Org::dptList()), array('prompt' => $this->t('All')))),
		'date',
		array(
			'class' => 'CEditableButtonColumn',
			'template' => '{fixaccrual}{fixinvoice}',
			'buttons' => array(
				'fixinvoice' => array(
					'imageUrl' => false,
					'visible' => '$data->status == 4',
					'url' => 'Yii::app()->createUrl("billing/invoiceFix", array("id" => $data->billing->id))',
					'options' => array('class' => 'grid_gallery_btn invoice_fix_btn'),
					'label' => 'Confirm Invoice',
				),
				'fixaccrual' => array(
					'imageUrl' => false,
					'visible' => '$data->status == 10',
					'url' => '$data->getNo($data->status)',
					'options' => array('class' => 'tab_link grid_gallery_btn'),
					'label' => 'Fix Accrual',
				),
			),
		),
	),
));?>

<script type="text/javascript">
	$(function(){
		var tab = $("#<?=$_GET['tabid'];?>");
		var panel = tab.data('panel');

		tab.bind('onOpen', function(){
			$.fn.yiiGridView.update('import-billing-check-grid', {
				success: function() {
					$('#import-billing-check-grid').yiiGridView('update');
				}
			});
		});
	});
</script>