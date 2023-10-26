
<h1><?=$this->t('Reconciliations');?></h1>
<style>
    .cloumn_red_rec{
        background-color: red;
    }
</style>
<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search_reconciliation',array(
	'model'=>$model,
));
?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'wc-reconciliation-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'client_type', 'value' => '$data->getType()', 'filter'=>Reconciliation::$types),
		array('name'=>'invoice_no','type'=>'raw','value'=>'preg_match("/^C\d{8}/i",$data->invoice_no)?"<a href=\"".Yii::app()->createURL("imcoConsol/update", array("id" =>ImcoConsol::getIdByNo($data->invoice_no)))."\" class=\"tab_link\" title=\"".$data->invoice_no."\">".$data->invoice_no."</a>":$data->invoice_no'),
		'invoice_date',
		array('name'=>'invoice_total', 'value'=>'$data->getInvoiceTotal().$data->getDeclareChargeDiff(true)'),
                 array('name'=>'my_total','value'=>'$data->getMyTotal()'),
                 'deviation',
         array('header'=>'weight_deviation','value'=>'$data->getWeightDeviation()','cssClassExpression' => '$data->getWeightDeviation()>=10? "cloumn_red_rec" : ""'),
                  'percent',
        array('header'=>'GenerateInvoice','value'=>'$data->getGeneratedInvoice()'),
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
					'url' => 'Yii::app()->createUrl("importWeightCheck/viewWeightDetails", ["id" => $data->id])',
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
		$('#wc-reconciliation-grid', panel).yiiGridView('update', {data: 'Reconciliation=reset'});
	};

	tab.on('onOpen', function(){
		$('#wc-reconciliation-grid', panel).yiiGridView('update');
	});

	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});

	$('.search-form form', panel).on('submit', function(){
		$('#wc-reconciliation-grid', panel).yiiGridView('update', {data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()});
		return false;
	}).find('.reset_btn').click(resetFilters);

	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize();
		$(this).attr('href', $(this).data('baseurl') + '&' + q);
	});
});
</script>
