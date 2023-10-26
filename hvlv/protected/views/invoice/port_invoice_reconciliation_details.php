<div style="right: 20px;position: absolute;">
    <a href="<?=$this->createUrl('invoice/portInvoiceReconciliationExport',['id' => $model->parent_id]);?>" target="_blank" title="Export Reconciliation"><div class="icon" style="background-position:-16px 0"></div>Export</a>
</div>
<h1><?=$this->t('Details');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=> $_GET["tabid"] . "-port-invoice-reconciliation-details-grid",
	'cssFile' => false,
	'dataProvider'=>$model->search(),
    'filter' => $model,
	'columns'=>array(
		'date',
		'connote',
        'destination',
         'weight',
        'amount',
        'duplicate_count',
        'consols',
        'invoice_refs',
        array('name' => 'notexisting','value' => '$data->getExistingStatus()')
	),
)); ?>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	tab.on('onOpen', function(){
		$('#<?=$_GET['tabid'];?>-port-invoice-reconciliation-details-grid', panel).yiiGridView('update');
	});
});
</script>
