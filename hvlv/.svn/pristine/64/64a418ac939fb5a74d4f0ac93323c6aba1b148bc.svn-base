<?php
/* @var $this AutoCommandsController */
/* @var $model AutoCommands */

$this->breadcrumbs=array(
	'Auto Commands'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List AutoCommands', 'url'=>array('index')),
	array('label'=>'Create AutoCommands', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#auto-commands-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Auto Commands</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<div style="right: 80px;position: absolute;top:50px;">
	<a href="<?= $this->createUrl('autoCommands/create');?>" class="tab_link" title="Create Auto Command"><div class="icon" style="background-position:-16px 0"></div>Create Auto Command</a>
	<a href="<?= $this->createUrl('autoCommandLog/admin'); ?>" class="tab_link" title="Cron Job Log"><div class="icon" style="background-position: -16px 0;"></div>Cron Job Log</a>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'auto-commands-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'command_name',
		'func_name',
		'day',
		'week_day',
		'hour',
		'minute',
		'type',
		'hour_interval',
		'minute_interval',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
