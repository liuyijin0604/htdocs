<h1><?=$this->t('Billing waiting for fix accrual & actual');?></h1>

<?php
$afModel = new AFInvoiceReconciliationHistory();
$afModel->unsetAttributes();

$ec = new CDbCriteria;
$ec->with = ['af'];
if ($model === 'priority') {
	$ec->addCondition('af.supplier_id = 954');
} else if ($model === 'tne') {
	$ec->addCondition('af.supplier_id = 1133');
	$ec->addCondition('t.id != 42');
} else if ($model === 'yuyang') {
	$ec->addCondition('af.supplier_id = 964 AND JSON_VALUE(t.meta, "$.insurance") IS NULL');
} else if ($model === 'fyn') {
	$ec->addCondition('af.supplier_id = ' . Org::ORGID_BROKER_FYN);
} else if ($model === 'master') {
	$ec->addCondition('af.supplier_id = ' . Org::ORGID_BROKER_MASTER);
} else if ($model === 'skyjet') {
	$ec->addCondition('af.supplier_id = 1008');
} else if ($model === 'qantas') {
	$ec->addCondition('af.supplier_id = 955');
} else if ($model === 'menzies') {
	$ec->addCondition('af.supplier_id = 939');
} else if ($model === 'insurance') {
	$ec->addCondition('af.supplier_id = 964 AND JSON_VALUE(t.meta, "$.insurance") = 1');
}

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'af-billing-import-list-grid',
	'cssFile' => false,
	'dataProvider' => $afModel->search($ec),
	'filter' => $afModel,
	'columns' => array(
		'created',
		array('header' => 'Invoice Period', 'value' => '$data->getInvoicePeriod()', 'visible' => in_array($model, ['priority', 'yuyang'])),
		'id',
		array('name' => 'op_id', 'value' => '!empty($data->op) ? $data->op->name : ""'),

		array('header' => 'Inv No.', 'value' => '$data->af[0]->invoice_no', 'htmlOptions' => array('width' => 150), 'visible' => $model === 'tne'),

		array('name' => 'invoice_total', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("billing/afInvoiceImportAllView",["id"=> $data->id])."\" class=\"tab_link\" title=\".$data->invoice_total.\">".$data->invoice_total."</a>"'),

		array('name' => 'success', 'type' => 'raw', 'value' => '$data->totalSuccess > 0 ? "<a href=\"".Yii::app()->createUrl("billing/afInvoiceImportSuccessView",["id"=>$data->id])."\" class=\"tab_link\" title=\"Success\">".$data->totalSuccess."</a>" : "0"'),

		array('name' => 'failed', 'type' => 'raw', 'value' => ' $data->totalFailed > 0 ? "<a href=\"".Yii::app()->createURL("billing/afInvoiceImportFailedView",["id"=> $data->id])."\" class=\"tab_link\" title=\"Fix Needed\">".$data->totalFailed."</a> (Post By GC : ".$data->getPostByGeneralCostCount().", Rejected : " . $data->getMatchedRejectedCount() . ")" : "0"', 'visible' => in_array($model, ['priority', 'tne', 'yuyang', 'insurance'])),
		array('name' => 'failed', 'type' => 'raw', 'value' => ' $data->totalFailed > 0 ? "<a href=\"".Yii::app()->createURL("billing/afInvoiceImportFailedView",["id"=> $data->id])."\" class=\"tab_link\" title=\"Fix Needed\">".$data->totalFailed."</a>" : "0"', 'visible' => in_array($model, ['fyn', 'master', 'skyjet', 'qantas', 'menzies'])),

		array('name' => 'total', 'value' => '$data->total'),

		array('name' => 'totalSyncXeroSuccess', 'type' => 'raw', 'value' => '"Success(<a class=\"tab_link\" title = \"Sync Success\" href=\"".Yii::app()->createURL("billing/afbSyncXeroSuccessView",["id"=> $data->id])."\">" . $data->getTotalSyncXeroSuccess() . "</a>) Failed(<a class=\"tab_link\" title = \"Sync failed\" href=\"".Yii::app()->createURL("billing/afbSyncXeroFailedView",["id"=> $data->id])."\">" . $data->getTotalSyncXeroFailed() . "</a>) Todo(<a class=\"tab_link\" title=\"Sync todo\" href=\"".Yii::app()->createURL("billing/afbSyncXeroTodoView",["id"=>$data->id])."\">" . $data->getTotalSyncXeroTodo() . "</a>)"', 'visible' => !in_array($model, ['tne', 'insurance'])),

		array('name' => 'report_file_id', 'type' => 'raw', 'value' => '"<a href=\"". $data->getReportFileLink() ."\">Download</a>"'),
		array('name' => 'attached_file_id', 'type' => 'raw', 'value' => '"<a href=\"". $data->getAttachedFileLink() ."\">" . $data->getAttachedFileName() . "</a>"'),

		array(
			'class' => 'oButtonColumn',
			'template' => '{update}{log}{xero}',
			'buttons' => array(
				'update' => array(
					'imageUrl' => false,
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => 'Update Inv'),
					'visible' => in_array($model, ['insurance']) ? 'true' : 'false',
					'url' => 'Yii::app()->createUrl("billing/afbUpdateInv", ["fid" => $data->id])',
					'label' => 'Update Inv'
				),
				'log' => array(
					'imageUrl' => false,
					'options' => array('class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'),
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("billing/afbimportResult", ["fid" => $data->id])',
					'label' => 'Log',
				),
				'xero' => array(
					'imageUrl' => false,
					'options' => array('class' => 'jqm_link grid_swap_btn push_xero_op', 'label' => 'PushXero'),
					'visible' => !in_array($model, ['skyjet']) ? 'true' : 'false',
					'url' => 'Yii::app()->createUrl("billing/afbtoXero", ["fid" => $data->id])',
					'label' => 'Push Xero',
				),
				'check' => array(
					'imageUrl' => false,
					'options' => array('class' => 'ajax_link grid_swap_btn'),
					'visible' => in_array($model, ['fyn', 'master']) ? 'true' : 'false',
					'url' => 'Yii::app()->createUrl("billing/recheck", ["fid" => $data->id])',
					'label' => 'Check',
				),
			),
		),
	))
);
?>

<script>
$(function() {
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	tab.bind('onOpen', function() {
		$('#af-billing-import-list-grid', panel).yiiGridView('update');
	});

	$('.ajax_link', panel).on('success', function(e, r) {
		myApp.notice(r.msg);
	});
});
</script>