<?php
$this->breadcrumbs=array(
	'Emailogs'=>array('index'),
	$this->t('List'),
);
?>

<h1><?=$this->t('Emailogs');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'emailog-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'from_id',
		'to_id',
		['name' => 'type', 'value' => '$data->getType()', 'filter' => CHtml::dropDownList('Emailog[type]', $model->type, $this->t(Emailog::$types), ['prompt' => 'All'])],
		['name' => 'status', 'value' => '$data->getStatus()', 'filter' => CHtml::dropDownList('Emailog[status]', $model->status, $this->t(Emailog::$states), ['prompt' => 'All'])],
		// array('name' => 'from_id', 'value' => '$data->getFrom()'),
		// array('name' => 'to_id', 'value' => '$data->getTo()'),
		// array('name' => 'type', 'value' => '$data->getType()'),
		// array('name' => 'fid', 'value' => '$data->getFobjName()'),
		'subject',
		'dt',
		/*
		'body',
		'meta',
		*/
		array(
			'class'=>'CButtonColumn',
			'template'=>'{view}{update}',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'label'=>'Emailogs Update',
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#emailog-grid', panel).yiiGridView('update');
	});
});
</script>
