<?php
/* @var $this CartageController */
/* @var $model Cartage */

$this->breadcrumbs = array(
	'Cartages' => array('index'),
	'Manage',
);

$this->menu = array(
	array('label' => 'List Cartage', 'url' => array('index')),
	array('label' => 'Create Cartage', 'url' => array('create')),
);
$selectedList = [
	10 => 'Scheduled',
	20 => 'Accepted',
	70 => 'Completed',
	80 => 'Confirmed',
	100 => 'Cancelled'];
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#cartage-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="pane">

<h1>Manage Cartages</h1>
<span style="right: 20px;position: absolute;">
<a class="tab_link"  href="<?=$this->createUrl('cartage/create')?>" title="New Cartage" ><div class="icon" style="background-position:-16px 0"></div> New Cartage</a>
<a class="tab_link"  href="<?=$this->createUrl('org/create')?>" title="New org" ><div class="icon" style="background-position:-16px 0"></div> New org</a>
</span>
<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<br>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'task-list-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		'id',
		array('name' => 'org_name',
			'header' => 'Assign To',
			'value' => '@$data->org->name',
		),
		'ref',

		'from_addr',
		'to_addr',
		'scd_time',

		array(
			'name' => 'status',
			'value' => '$data->getStatus()',
			'filter' => CHtml::dropDownList('Cartage[status]', $model->status, $selectedList, ['prompt' => 'All', 'class' => 'form-control']),
		),

		'take_time',
		'comp_time',
		'rego',
		array(
			'class' => 'CButtonColumn', //'oButtonColumn',
			'template' => '{View}&nbsp{&nbspConfirm}&nbsp{&nbspCancel}&nbsp&nbsp{Log}&nbsp{update}',
			'buttons' => array(

				'View' => array(
					'url' => 'Yii::app()->createUrl("cart/cartage/viewDetail",["id" => $data->id])',
					'options' => array('class' => 'jqm_link grid_gallery_btn', 'title' => 'View Notes'),
				),
				'&nbspConfirm' => array(
					'visible' => 'Yii::app()->user->grp<=40&&$data->status==70',
					'url' => 'Yii::app()->createUrl("cart/cartage/finishConfirm",["id" => $data->id])',
					'options' => array('class' => 'jqm_link '),
				),
//
				'&nbspCancel' => array(
					'visible' => '$data->status!=100',
					'url' => 'Yii::app()->createUrl("cart/cartage/cancelConfirm",["id" => $data->id])',
					'options' => array('class' => 'jqm_link grid_delete_btn'),
				),
				'Log' => array(
					'visible' => Yii::app()->user->grp <= '40' ? 'true' : 'false',
					'url' => 'Yii::app()->createUrl("cart/cartage/viewLog",["id" => $data->id])',
					'options' => array('class' => 'jqm_link grid_file_btn', 'title' => 'View Log'),
				),
				'update' => array(
					'visible' => '$data->status==10',
					'imageUrl' => false,
					'options' => array('class' => 'tab_link grid_edit_btn', 'label' => $this->t('Update')),
				),

//
			),
		),
	),
));?>

</div>


<script>
 $(function(){
	 var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
		var win = $('#jqmw_<?=$_GET["tabid"];?>');
  //        'click'=>'js:function(){if(z){function(){$("task-list-grid").yiiGridView.update("task-list-grid");})}}',


 }
  );
</script>