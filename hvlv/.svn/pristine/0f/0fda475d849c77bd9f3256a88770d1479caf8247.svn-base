<div style="right: 20px; position: absolute">
	<a class="jqm_link" data-win-class="XL" href="<?=$this->createUrl('invoiceTemplate/create')?>"><div class="icon" style="background-position: -128px -480px"></div> New Invoice Template</a>
</div>

<h1><?=$this->t('Invoice Template');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'invoice-template-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		'ref',
		array('name' => 'currency', 'value' => '$data->getCurrency()',
			'filter' => CHtml::dropDownList('InvoiceTemplate[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt' => $this->t('All')))),
		array('name' => 'dpmt', 'value' => '$data->getDpmt()',
			'filter' => CHtml::dropDownList('InvoiceTemplate[dpmt]', $model->dpmt, $this->t(Invoice::$dpmts), array('prompt' => $this->t('All')))),
		array('name' => 'dpt_id', 'value' => '$data->getBranch()',
			'filter' => CHtml::dropDownList('InvoiceTemplate[dpt_id]', $model->dpt_id, Org::dptList(), array('prompt' => $this->t('All')))),
		'total',
		'gst',
		'total2',
		array('name' => 'status', 'value' => '$data->getStatus()',
			'filter' => CHtml::dropDownList('InvoiceTemplate[status]', $model->status, $this->t($model::$states), array('prompt' => $this->t('All')))),
		array(
			'class' => 'oButtonColumn',
			'template' => '{update} {log}',
			'buttons' => array(
				'update' => array(
					'imageUrl' => false,
					'visible' => 'true',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Update'), 'data-win-class' => 'XL'),
				),
				'log' => array(
					'imageUrl' => false,
					'visible' => 'true',
					'options' => array('class' => 'jqm_link grid_view_btn', 'label' => $this->t('Log'), 'data-win-class' => 'L'),
					'url' => 'Yii::app()->createUrl("invoiceTemplate/log", ["fid" => $data->id])',
				)
			)
		),
	),
)); ?>

<script type="text/javascript">
$(function() {
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#invoice-template-grid', panel).yiiGridView('update');
	});
});
</script>