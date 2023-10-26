<?php
/* @var $this AirlineController */
/* @var $model Airline */

$this->breadcrumbs=array(
	'Airlines'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Airline', 'url'=>array('index')),
	array('label'=>'Create Airline', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form1').toggle();
	return false;
});
$('.search-form1 form').submit(function(){
	$('#airline-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="pane">
<div style="right: 20px;position: absolute;">

<a class="jqm_link" href="<?=$this->createUrl('airline/create');?>" title="New Airline"><div class="icon" style="background-position:-16px 0"></div> New Airline</a> &nbsp; 
</div>
<h1>Manage Airlines</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form1" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
 
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'airline-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'code',
		array('name' => 'tid', 'value' => '@$data->terminal->name'),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array(
				'update' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '"Quotes-".$data->code'),
				)
			),
		),
		
	),
)); ?>

</div>

<script>
$(function() {
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	tab.bind('onOpen', function() {
		$('#airline-grid', panel).yiiGridView('update');
	});
});
</script>
