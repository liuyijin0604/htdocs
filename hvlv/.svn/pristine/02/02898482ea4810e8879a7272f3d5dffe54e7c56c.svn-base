<?php
/* @var $this DeconsolidationController */
/* @var $model Deconsolidation */

$this->breadcrumbs=array(
	'Deconsolidations'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Deconsolidation', 'url'=>array('index')),
	array('label'=>'Create Deconsolidation', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#deconsolidation-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Deconsolidations</h1>

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
	'id'=>'deconsolidation-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'shipment_id',
		'cargo_process_id',
		'status',
		'depot',
		'create_time',
		/*
		'op_complete_time',
		'warehouse_complete_time',
		'meta',
		'has_problem',
		'assigned_user',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
