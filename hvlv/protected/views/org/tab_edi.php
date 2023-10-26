<div style="right: 20px;position: absolute;">
	<div class="icon" style="background-position:-16px 0"></div><a class="tab_link" data-win-class="L" href="<?=$this->createUrl('ediJob/mngEdiBasicTemplate');?>" title="Manage Charge Type">Manage Basic Template</a>
	<div class="icon" style="background-position:-176px -544px"></div><a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('org/createEdiJobTemplate', ['id' => 0,'oid' => $model->id]);?>" target="_blank">New Template</a>
	<div class="icon" style="background-position:-176px -544px"></div><a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('ediJob/invoiceRate', ['id' => $model->id]);?>" target="_blank">Invoice Rate Check</a>
</div>
<h1><?=$this->t('EDI Job Template');?></h1>

<?php
$templates = new EdiJobTemplate();
$templates->unsetAttributes();
$templates->owner_id = $model->id;
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'edi-org-template-list-grid',
	'cssFile' => false,
	'dataProvider'=>$templates->search(),
	'filter'=>$templates,
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
					'url' => 'Yii::app()->createUrl("org/createEdiJobTemplate", ["id" => $data->id])',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->id','data-win-class' => 'L'),
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
			$('#edi-org-template-list-grid', panel).yiiGridView('update');
		});
	});
</script>
