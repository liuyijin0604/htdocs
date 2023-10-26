<?php
/* @var $this InterstateChargeRateController */
/* @var $model InterstateChargeRate */

$this->breadcrumbs=array(
	'Interstate Charge Rates'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List InterstateChargeRate', 'url'=>array('index')),
	array('label'=>'Create InterstateChargeRate', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#interstate-charge-rate-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Interstate Charge Rates</h1>

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
	<a href="<?= $this->createUrl('interstateChargeRate/import');?>" class="jqm_link" title="Import Interstate Rate"><div class="icon" style="background-position:-16px 0"></div>Import Interstate Rate</a>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'interstate-charge-rate-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		//'id',
		'departure_depot',
		'destination_depot',
		'org_id',
		'weight_low',
		'weight_high',
		'base',
		'item',
		'perkg',
		'per_pallet',
		'minimum',
		'type',
		'start_date',
	),
)); ?>
