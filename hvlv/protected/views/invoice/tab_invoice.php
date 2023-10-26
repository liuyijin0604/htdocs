<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php
$model = new Reconciliation('search');
$model->unsetAttributes();
if (isset($_GET['Reconciliation'])) {
	$model->attributes = $_GET['Reconciliation'];
}
$model->notype = [1,10];
if (in_array($model->client_type, $model->notype)) {
	unset($model->client_type);
}
$this->renderPartial('_search_reconciliation',array(
	'model'=>$model,
));
?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'invoice-reconciliation-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'htmlOptions' => ['style' => 'min-height: 700px'],
	'columns'=>array(
		array('name' => 'client_type', 'value' => '$data->getType()', 'filter'=>CHtml::dropDownList('Reconciliation[client_type]', $model->client_type, $this->t(array_filter(Reconciliation::$types, function ($key) { return !in_array($key, [1,10]); }, ARRAY_FILTER_USE_KEY)), array('prompt' => $this->t('All')))),
		array('name'=>'invoice_no','type'=>'raw','value'=>'preg_match("/^C\d{8}/i",$data->invoice_no)?"<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" =>ImcoConsol::getIdByNo($data->invoice_no)))."\" class=\"tab_link\" title=\"".$data->invoice_no."\">".$data->invoice_no."</a>":$data->invoice_no'),
		'manifest_no',
		'invoice_date',
		array('name'=>'invoice_total', 'value'=>'$data->getInvoiceTotal().$data->getDeclareChargeDiff(true).$data->getFuelTotal()'),
		array('name'=>'my_total','value'=>'$data->getMyTotal()'),
		// array('header'=>'my_total_m','value'=>'$data->getMyTotalManifest()'),
		'deviation',
		'percent',
		array('name' => 'no_consol', 'value' => '$data->ifConsol()', 'filter'=>[1 => 'Yes', 2 => 'No']),
		array('name' => 'ot_inv', 'value' => '$data->getOTinvStatus()', 'type' => 'raw', 'filter' => CHtml::dropDownList('Reconciliation[ot_inv]', $model->ot_inv, $this->t(Reconciliation::$ot_inv_states), array('prompt' => $this->t('All')))),
		/*
		'currency',
		'meta',
		*/
		array(
			'class'=>'oButtonColumn',
			'template'=>'{details}',
			'buttons'=>array(

				'details' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'url' => 'Yii::app()->createUrl("invoice/viewReconciliation", ["id" => $data->id])',
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

	var resetFilters = function(){
		$('.search-form form', panel).trigger('reset');
		$('#invoice-reconciliation-grid', panel).yiiGridView('update', {data: 'Reconciliation=reset'});
	};

	tab.on('onOpen', function(){
		$('#invoice-reconciliation-grid', panel).yiiGridView('update');
	});

	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});

	$('.search-form form', panel).on('submit', function(){
		$('#invoice-reconciliation-grid', panel).yiiGridView('update', {data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()});
		return false;
	}).find('.reset_btn').click(resetFilters);

	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize();
		$(this).attr('href', $(this).data('baseurl') + '&' + q);
	});
});
</script>
