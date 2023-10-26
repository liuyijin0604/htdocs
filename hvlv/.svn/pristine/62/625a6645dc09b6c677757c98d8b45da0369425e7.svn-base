<?php
$this->breadcrumbs=array(
	'Instructions'=>array('index'),
	$this->t('List'),
);
?>

<h1><?php echo 'Customer Black List'; ?></h1>
<?php echo '<div style="margin-top: -20px; margin-left: 200px;"><a class="jqm_link" href="app/updateBlackListCneeName"><div style="background-position:-16px 0" class="icon"></div>Create Keyword</a></div>';?>

<p>
<?php echo 'You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.';?></p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('cnee_name_blacklist_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'cnee-name-blacklist-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'keyword',
		'name',
		'note',
		//'body',
		'created',
		'updated',

		array(
            'name'=>'active',
            'value'=>'$data->getActive()',
			'filter'=>CHtml::dropDownList('BlacklistCneeName[active]', $model->active, $this->t(['1' => 'Yes', 0 => 'No']), array('prompt'=>$this->t('All'))),
        ),

		array(
			'class'=>'CButtonColumn',
			'template'=>'{update}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
					'visible' => function($row, $data) {
		                // 在这里检查$data->active的值
		                return $data->active == 1;
		            },
					'url' => 'Yii::app()->createUrl("app/updateBlackListCneeName", array("id" => $data->id))',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>'Update'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = $('#<?=$_GET["tabid"];?>').data('panel');

	$('.search-button', tab.data('panel')).click(function(){
		$('.search-form', tab.data('panel')).toggle();
		return false;
	});
	$('.search-form form', tab.data('panel')).submit(function(){
		$('#instruction-grid', tab.data('panel')).yiiGridView('update', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#instruction-grid', tab.data('panel')).yiiGridView('update');
	});

	// $('.grid_delete_btn', tab.data('panel')).click(function(){
	// 	alert('xxxxxxxxxx');
	// 	//tab.trigger('reload_system-setting-grid');
	// 	$('#cnee-name-blacklist-grid', tab.data('panel')).yiiGridView('update');
	// 	return false;
	// });

});
</script>

