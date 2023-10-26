<div style="position: absolute; left: 150px;"><a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('cnID/create');?>"><div class="icon" style="background-position:-112px -768px"></div> Upload</a>
<a class="jqm_link" href="<?=$this->createUrl('cnID/bulkWarn');?>"><div class="icon" style="background-position:-112px -512px"></div> Bulk Warn</a></div>
<h1><?=$this->t('Manage CnID');?></h1>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
));
?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'cn-id-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'no',
		'name',
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('CnID[status]', $model->status, $this->t(CnID::$states), array('prompt'=>$this->t('All'))),),
		'city',
		'mobile',
		'created',
		array('name' => 'bwf', 'header' => 'Warnings', 'type' => 'raw', 'value' => '$data->getWarnings()','filter'=>CHtml::dropDownList('CnID[bwf]', $model->bwf, $this->t(CnID::$bwfs), array('prompt'=>$this->t('All'))),),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}{update}',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'data-win-class' => 'L', 'label'=>$this->t('Update')),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	var resetFilters = function(){
		$('.search-form form', panel).trigger('reset');
		$('#cn-id-grid', panel).yiiGridView('update', {data: 'CnID=reset'});
	};

	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});

	$('input.exp_btn', panel).on('click', function(){
		var f = $('<form action="cnID/download" method="post" target="_blank"><form>');
		$('#CnID_mnames, #CnID_mnos', panel).each(function(){
			f.append($(this).clone());
		});
		panel.append(f);
		f.submit();
		f.remove();
		return false;
	});

	$('.search-form form', panel).on('submit', function(){
		$('#cn-id-grid', panel).yiiGridView('update', {data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()});
		return false;
	}).find('.reset_btn').click(resetFilters);

	tab.bind('onOpen', function(){
		$('#cn-id-grid', panel).yiiGridView('update');
	});
});
</script>
