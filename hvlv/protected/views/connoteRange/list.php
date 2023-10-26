<h1><?=$this->t('Connote Ranges');?></h1>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'connote-range-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'type',
		'status',
		'model',
		'fid',
		'prefix',
		/*
		'suffix',
		'start',
		'finish',
		'digits',
		'meta',
		*/
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
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => ''),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).submit(function(){
		$.fn.yiiGridView.update('connote-range-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#connote-range-grid', panel).yiiGridView('update');
	});
});
</script>
