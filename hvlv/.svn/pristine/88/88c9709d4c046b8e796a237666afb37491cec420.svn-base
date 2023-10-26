<div style="right: 20px;position: absolute;">
     <div class="icon" style="background-position:-16px 0"></div><a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('ediJob/createEdiBasicTemplate',['id' => 0]);?>" title="New Basic Template">New Template</a>
  </div>

<h1>Edi Basic Template List</h1>

<p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'edi-basic-template-list-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'name',
        'created',
        array('name' => 'user_id','type' => 'raw', 'value' => 'empty($data->user)? "" : $data->user->name'),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
                    'url' => 'Yii::app()->createUrl("ediJob/createEdiBasicTemplate", ["id" => $data->id])',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->id','data-win-class' => 'L'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){

	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	tab.bind('onOpen', function(){
		$('#edi-basic-template-list-grid', panel).yiiGridView('update');
	});

});
</script>
