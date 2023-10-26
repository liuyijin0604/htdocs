<?php
if(OrgRate::model()->count('code = :c', [':c' => $model->code]) < 1):
?>
<a class="jqm_link copy_rate" href="<?=$this->createUrl('exChannel/newRate', ['id' => $model->id]);?>" title="New Rate"><div class="icon" style="background-position:-16px 0"></div> New Rate</a>
<?php
else:
$rates = new OrgRate;
$rates->code = $model->code;

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'_rate-grid',
	'cssFile' => false,
	'summaryText'=>'',
	'dataProvider'=> $rates->search(),
	'columns'=>array(
		['name' => 'org_id', 'header' => 'Courier', 'value' => '$data->org->name'],
		'vfrom',
		'vto',
		['name' => 'currency', 'value' => 'Invoice::$currencies[$data->currency]'],
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update} {view}',
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'url' => 'Yii::app()->createUrl("exChannel/rates", ["id" => $data->id])',
					'options' => array('data-win-class' => 'L', 'class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->name." Rate"'),
					'visible' => '$data->isCurrent()',
				),
				'view' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'url' => 'Yii::app()->createUrl("exChannel/rates", ["id" => $data->id])',
					'options' => array('data-win-class' => 'L', 'class' => 'jqm_link grid_view_btn', 'label'=>$this->t('View'), 'title' => '$data->name." Rate"'),
					'visible' => '!$data->isCurrent()',
				),
			),
		),
	),

));
endif;
?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	
	tab.on('refresh', function(e,r){
		$('#<?=$_GET["tabid"]?>_rate-grid', panel).yiiGridView('update');
	});
});
</script>