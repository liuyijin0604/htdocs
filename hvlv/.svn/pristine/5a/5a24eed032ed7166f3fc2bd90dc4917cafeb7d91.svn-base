<?php
/* @var $this QuotesController */
/* @var $model Quotes */

$this->breadcrumbs = array(
	'Quotes' => array('index'),
	'Manage',
);

$this->menu = array(
	array('label' => 'List Quotes', 'url' => array('index')),
	array('label' => 'Create Quotes', 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#quotes-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="pane">
<h1>Manage Quotes</h1>
<div style="right: 20px;position: absolute;">
	 <?php $importExcel = '<a class="jqm_link" data-win-class="XL" href="quotes/import.app"><div style="background-position:-16px 0" class="icon"></div>' . $this->t('Import') . '</a> &nbsp;';
echo $importExcel?>
<!--     <a class="tab_link" href="<?=$this->createUrl('quotes/import');?>" title="New Quotes"><div class="icon" style="background-position:-16px 0"></div> New Quotes</a> &nbsp; -->
<a class="tab_link" href="<?=$this->createUrl('quotes/create');?>" title="New Quotes"><div class="icon" style="background-position:-16px 0"></div> New Quotes</a> &nbsp;
<a class="tab_link" href="<?=$this->createUrl('airline/admin');?>" title="Manage Airline"><div class="icon" style="background-position:-16px 0"></div> Manage Airline</a> &nbsp;
<!--<a class="tab_link" href="<?=$this->createUrl('route/admin');?>" title="Manage Routes"><div class="icon" style="background-position:-16px 0"></div> Manage Route</a> &nbsp; -->
</div>


<?php echo CHtml::link('Advanced Search', 'serach', array('class' => 'search-button')); ?>
<div class="search-form">
<?php $this->renderPartial('_search', array(
	'model' => $model,
));?>
</div><!-- search-form -->

<?php
if (!empty($model->destination)) {
	$model->route_code = @explode('-', $model->destination)[0];
	$model->route_destination = @explode('-', $model->destination)[1];
}
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'quotes-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		array(
			'name' => 'route.destination',
			'value' => '$data->route->destination',
			'filter' => CHtml::activeTextField($model, 'route_destination'),
		),
		array(
			'name' => 'route.code',
			'value' => '$data->route->code',
			'filter' => CHtml::activeTextField($model, 'route_code'),
		),
		array(
			'name' => 'route.departure',
			'value' => '$data->route->departure',
			'filter' => CHtml::activeTextField($model, 'route_departure'),
		),
		array(
			'name' => 'airline.name',
			'value' => '$data->route->airline->name',
			'filter' => CHtml::activeTextField($model, 'route_airline_name'),
		),
		array(
			'name' => 'route.flight_no',
			'value' => '$data->route->flight_no',
			'filter' => CHtml::activeTextField($model, 'route_flight_no'),
		),

		array(
			'name' => 'route.days',
			'value' => '$data->route->days',
			'filter' => CHtml::activeTextField($model, 'route_days'),
		),

		array(
			'name' => 'uld_type', 'value' => '$data->getType()',
			'filter' => CHtml::dropDownList('Quotes[uld_type]', $model->type, $this->t(Quotes::$types), array('prompt' => $this->t('All'))),
		),

		array(
			'name' => 'route.deptime',
			'value' => '$data->route->deptime',
			'filter' => CHtml::activeTextField($model, 'route_deptime'),
		),
		'wt_lo',
		'wt_hi',
		'pkg',
		'sec_fuel',
		'min',
		'date_eff',
		'date_exp',
		array(
			'name' => 'route.info',
			'value' => '$data->route->info',
		),

		array(
			'class' => 'oButtonColumn',
			'template' => '{view}',
			'buttons' => array
			(
				'view' => array(
					'imageUrl' => false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl' => false,
					'options' => array('class' => 'tab_link grid_edit_btn', 'label' => $this->t('Update'), 'title' => '"Quotes-" . $data->route->code . "-" . $data->route->flight_no'),
				),
				'delete' => array(
					'imageUrl' => false,
					'options' => array('class' => 'grid_delete_btn'),
				),

			),

		),
	),
));?>
</div>