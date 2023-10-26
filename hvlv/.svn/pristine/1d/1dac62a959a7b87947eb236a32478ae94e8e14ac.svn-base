
<h1><?=$this->t('System Setting Data');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<div style="right: 100px;position: absolute;">
      <a class="jqm_link" href="<?=$this->createUrl('it/createSystemSetting');?>" title="Create System Setting"><div class="icon" style="background-position:-16px -0px"></div>Create System Setting</a>
</div>


<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'system-setting-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'key',
		// array('name' => 'type', 'value' => '$data->getType()', 
		// 	'filter'=>CHtml::dropDownList('Storage[type]', $model->type, $this->t(Storage::$types), array('prompt'=>$this->t('All'))),),
		// array('name' => 'status', 'value' => '$data->getStatus()', 
		// 	'filter'=>CHtml::dropDownList('Storage[status]', $model->status, $this->t(Storage::$states), array('prompt'=>$this->t('All'))),),
		// array('name' => 'cap', 'value' => '$data->cap."%"'),
		// 'meta',
		'uid',
		'date',
		// array('name' => 'wid', 'value' => 'empty($data->wid)? "" : $data->warehouse->name', 
		// 	'filter'=>CHtml::dropDownList('Storage[wid]', $model->wid, Org::dptList(), array('prompt'=>$this->t('All'))),),
		// array('name' => 'pid', 'value' => 'empty($data->pid)? "" : $data->parent->name', ),

		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => ''),
					'url' => 'Yii::app()->createUrl("it/updateSystemSetting", array("id" => $data->id))',
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
		$('#storage-grid', panel).yiiGridView('update');
	});
});
</script>
