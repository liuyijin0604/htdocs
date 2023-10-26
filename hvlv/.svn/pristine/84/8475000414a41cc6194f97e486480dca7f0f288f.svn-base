<h1><?=$this->t('Manage Lists');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'o-list-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'columns'=>array(
		'name',
		array('name' => 'item', 'value' => '$data->listChildren()' ),
		array(
			'class'=>'CButtonColumn',
			'template'=>'{view}{update}',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'url'=>'"oList/view/".$data->id',
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'url'=>'"oList/update/".$data->id',
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update')),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	tab.bind('onOpen', function(){
		$('#o-list-grid', tab.data('panel')).yiiGridView('update');
	});
});
</script>
