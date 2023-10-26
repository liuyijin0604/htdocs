<?php
/* @var $this RouteController */
/* @var $model Route */

$this->breadcrumbs = array(
	'Routes' => array('index'),
	'Manage',
);

$this->menu = array(
	array('label' => 'List Route', 'url' => array('index')),
	array('label' => 'Create Route', 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form2').toggle();
	return false;
});
$('.search-form2 form').submit(function(){
	$('#route-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<!--<div style="right: 20px;position: absolute;">

<a class="tab_link" href="<?=$this->createUrl('route/create');?>" title="New Route"><div class="icon" style="background-position:-16px 0"></div> New Route</a> &nbsp;
</div>-->

<div class="pane">
<h1>Manage Routes</h1>

<div style="right: 20px;position: absolute;">
	 <?php $importExcel = '<a class="jqm_link" data-win-class="XL" href="quotes/import.app"><div style="background-position:-16px 0" class="icon"></div>' . $this->t('Import') . '</a> &nbsp;';
	echo $importExcel?>
	<!--     <a class="tab_link" href="<?=$this->createUrl('quotes/import');?>" title="New Quotes"><div class="icon" style="background-position:-16px 0"></div> New Quotes</a> &nbsp; -->
	<a class="tab_link" href="<?=$this->createUrl('quotes/create');?>" title="New Quotes"><div class="icon" style="background-position:-16px 0"></div> New Quotes</a> &nbsp;
	<a class="tab_link" href="<?=$this->createUrl('airline/admin');?>" title="Manage Airline"><div class="icon" style="background-position:-16px 0"></div> Manage Airline</a> &nbsp;
	<!--<a class="tab_link" href="<?=$this->createUrl('route/admin');?>" title="Manage Routes"><div class="icon" style="background-position:-16px 0"></div> Manage Route</a> &nbsp; -->
</div>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search', '#', array('class' => 'search-button')); ?>
<div class="search-form2" style="display:none">
<!-- <?php $this->renderPartial('_search', array(
	'model' => $model,
));?> -->
</div><!-- search-form -->

<?php
$model->status = 1;
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'route-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		array(
			'name' => 'airline.name',
			'value' => '$data->airline->name',
		),
		'flight_no',
		'destination',
		'departure',
		array(
			'name' => 'code',
			'header' => 'Dest Code',
		),
		/*
		'stops',
		'days',
		 */
		array(
			'name' => 'status',
			'value' => '$data->getStatus()',
			'filter' => CHtml::dropDownList('Route[status]', $model->status, $this->t(Route::$states), array('prompt' => $this->t('All'))),
		),
		array(
			'class' => 'oButtonColumn',
			'template' => '{view} {update}',
			'buttons' => array(
				'view' => array(
					'imageUrl' => false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl' => false,
					'options' => array('class' => 'tab_link grid_edit_btn', 'label' => $this->t('Update'), 'title' => '"Route-".$data->flight_no'),
				),
				// 'delete' => array(
				//     'imageUrl'=>false,
				// ),
				'delete' => array(
					'imageUrl' => false,
					'options' => array('class' => 'grid_delete_btn'),
				),

			),

		),
	),
));?>
</div>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET["tabid"];?>");
	panel = tab.data('panel');

	tab.bind('onOpen', function(){
		$('#route-grid', panel).yiiGridView('update');
	});
});
</script>