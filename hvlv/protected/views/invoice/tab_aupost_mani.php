<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php
$model = new Reconciliation('search');
$model->unsetAttributes();
if (isset($_GET['Reconciliation'])) {
	$model->attributes = $_GET['Reconciliation'];
}
$model->client_type = 1;
$this->renderPartial('_search_reconciliation',array(
	'model'=>$model,
));
?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'aupost-manifest-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'htmlOptions' => ['style' => 'min-height: 700px;'],
	'columns'=>array(
		array('name' => 'client_type', 'value' => '$data->getType()', 'filter'=>CHtml::dropDownList('Reconciliation[client_type]', $model->client_type, $this->t([Reconciliation::AUPOST_TYPE => 'Aupost Manifest']), array('prompt' => $this->t('All')))),
		array('name'=>'invoice_no','type'=>'raw','value'=>'preg_match("/^C\d{8}/i",$data->invoice_no)?"<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" =>ImcoConsol::getIdByNo($data->invoice_no)))."\" class=\"tab_link\" title=\"".$data->invoice_no."\">".$data->invoice_no."</a>":$data->invoice_no'),
		'manifest_no',
		'invoice_date',
		array('name'=>'invoice_total', 'value'=>'$data->getInvoiceTotal().$data->getDeclareChargeDiff(true)'),
		array('name'=>'my_total','value'=>'$data->getMyTotal()'),
		array('header'=>'My Total With Other Fee','value'=>'$data->getMyTotalWithOtherFee()'),
		array('header'=>'my_total_m','value'=>'$data->getMyTotalManifest()'),
		'deviation',
		'percent',
		array('name' => 'no_consol', 'value' => '$data->ifConsol()', 'filter'=>[1 => 'Yes', 2 => 'No']),
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
		$('#aupost-manifest-grid', panel).yiiGridView('update', {data: 'Reconciliation=reset'});
	};

	tab.on('onOpen', function(){
		$('#aupost-manifest-grid', panel).yiiGridView('update');
	});

	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});

	$('.search-form form', panel).on('submit', function(){
		$('#aupost-manifest-grid', panel).yiiGridView('update', {data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()});
		return false;
	}).find('.reset_btn').click(resetFilters);

	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize();
		$(this).attr('href', $(this).data('baseurl') + '&' + q);
	});
});
</script>
