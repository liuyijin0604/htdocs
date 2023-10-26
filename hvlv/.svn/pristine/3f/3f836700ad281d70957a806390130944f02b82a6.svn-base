<div align="right"><a href="<?=$this->createUrl('connoteRange/create', ['model' => 'ExChannel', 'fid' => $model->id]);?>" class="jqm_link"><div class="icon" style="background-position:-16px 0"></div> Add Range</a></div>
<?php
$cs = new ConnoteRange;
$cs->model = 'ExChannel';
$cs->fid = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_cs-grid',
	'cssFile' => false,
	'summaryText'=>'',
	'dataProvider'=> $cs->search(),
	'columns'=>array(
		['name' => 'type', 'value' => '$data->getType()'],
		['name' => 'status', 'value' => '$data->getStatus()'],
		'prefix',
		'start',
		'finish',
		'suffix',
		'digits',
		['header' => 'Total', 'value' => '$data->avail()."/".$data->mdata["tot"]'],
		['header' => 'Added', 'value' => '$data->mdata["date"]'],
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'url' => 'Yii::app()->createUrl("connoteRange/update", ["id" => $data->id])',
					'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => 'Update Connotes'),
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
	
	tab.on('refresh', function(e,r){
		$('#<?=$_GET["tabid"]?>_cs-grid', panel).yiiGridView('update');
	});
});
</script>