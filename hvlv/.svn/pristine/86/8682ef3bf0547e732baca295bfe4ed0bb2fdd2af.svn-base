<script src="//cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
<div class="pane">
	<div style="right: 20px;position: absolute;">
		<a class="tab_link" href="<?=$this->createUrl('massmsg/create');?>" title="New CRM Mass Message"><div class="icon" style="background-position:-16px 0"></div> New CRM Mass Message</a>
	</div>
	<h1><?=$this->t('CRM Mass Message');?></h1>

	<p>
	<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'crm-mass-msg-grid',
		'cssFile' => false,
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>array(
			array('name' => 'id', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("crmmsg/massmsg/update", ["id" => $data->id])."\" class=\"tab_link\" title=\"".$data->id."\">".$data->id."</a>"'),
			array('name' => 'op_name', 'value' => 'empty($data->mdata["operator_id"])? "" : User::model()->findByPk($data->mdata["operator_id"])->getName()'),
			array('name' => 'title', 'value' => 'empty($data->mdata["title"])?"":$data->mdata["title"]'),
			'time',
			array(
				'class'=>'oButtonColumn',
				'template'=>'{post}{view}{update}',
				'buttons'=>array
				(
					'post' => array(
						'imageUrl' => false,
						'options' => array('class' => 'jqm_link grid_email_btn'),
						'visible' => '!empty($data->mdata["title"]) && !empty($data->mdata["content"]) && !empty($data->mdata["thumb"])',
						'url' => 'Yii::app()->createUrl("crmmsg/massmsg/post", ["id" => $data->id])'
					),
					'view' => array(
						'imageUrl'=>false,
						'options' => array('class' => 'jqm_link grid_view_btn'),
					),
					'update' => array(
						'imageUrl'=>false,
						'visible'=>'true',
						'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->id'),
					),
				),
			),
		),
	)); ?>
</div>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	tab.bind('onOpen', function(){
		$('#crm-mass-msg-grid', panel).yiiGridView('update');
	});
});
</script>
