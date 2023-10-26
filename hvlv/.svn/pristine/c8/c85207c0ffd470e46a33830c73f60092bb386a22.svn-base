<div style="right: 20px;position: absolute;">
    <a class="jqm_link" href="<?=$this->createUrl('invoice/portInvoiceReconciliationNew');?>" title="New Port Invoice Reconciliation"><div class="icon" style="background-position:-16px 0"></div>New Port Invoice Reconciliation</a>
 </div>

<h1><?=$this->t('Port Invoice Reconciliations');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'port-invoice-reconciliation-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'created',
		'invoice_ref',
		'weight',
        'amount',
        'duplicate_count',
        'notexisting_count',
		array(
			'class'=>'oButtonColumn',
			'template'=>'{details}',
			'buttons'=>array(

				'details' => array(
					'imageUrl'=>false,
					'visible'=>'true',
                    'url' => 'Yii::app()->createUrl("invoice/viewPortInvoiceReconciliation", ["id" => $data->id])',
					'options' => array('class' => 'tab_link grid_view_btn', 'label'=>$this->t('Details')),
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
		$('#port-invoice-reconciliation-grid', panel).yiiGridView('update');
	});
});
</script>
