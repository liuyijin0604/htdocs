<h1><?=$this->t('Template Files');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'templates-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'name',
		array(
			'name'=>'size',
			'value'=>'$data->formatSize()',
		),
		'date',
		array(
			'class'=>'CButtonColumn',
			'template'=>'{view}',
			'buttons'=>array(
				'view' => array(
					'url'=>'Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name',
					'imageUrl'=>false,
					'options' => array('class' => 'grid_view_btn', 'target' => '_blank', 'title'=>'View File'),
				),
			),
		),
	),
));
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#templates-grid', panel).yiiGridView('update');
	});
});
</script>
