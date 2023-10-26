<?php
/* @var $this AutoCommandLogController */
/* @var $model AutoCommandLog */

$this->breadcrumbs=array(
	'Auto Command Logs'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List AutoCommandLog', 'url'=>array('index')),
	array('label'=>'Create AutoCommandLog', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#auto-command-log-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<style>
	.column_green {
		background-color: greenyellow;
	}

	.column_red {
		background-color: pink;
	}
	
	.column_yellow {
		background-color: orange;
	}

	.column_red_1 {
		background-color: red;
	}
</style>

<h1>Manage Auto Command Logs</h1>

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

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'auto-command-log-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'command_name',
		'function_name',
		array('name' => 'type', 'type' => 'raw', 'value' => 'AutoCommandLog::$types[$data->type]', 'cssClassExpression' => '$data->getColColor()'),
		'time',
	),
)); ?>
