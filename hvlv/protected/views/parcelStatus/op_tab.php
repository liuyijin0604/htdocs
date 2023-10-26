<h4>Download documents</h4>
<?php
$fr = new FileRepo('search');
$fr->unsetAttributes();
if (empty($_GET['FileRepo'])) {
	$fr->status = 30;
	$fr->type = $_GET['r']=='eny'? 19 : 20;
} else {
	$fr->attributes = $_GET['FileRepo'];
	if (empty($fr->type)) {
		$fr->type = $_GET['r']=='eny'? 19 : 20;
	}
}
$fr->fid = $model->id;
$mf = Acl::hasAccess('B:org/manageFile');

$this->widget('zii.widgets.grid.CGridView', [
	'id' => 'download_import_custom-grid',
	'summaryText' => '',
	'dataProvider' => $fr->search(),
	'filter' => $fr,
	'columns' => [
		['name' => 'name', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->baseUrl."/filerepo/".$data->hash."/".$data->name."\" target=\"_blank\">".$data->name."</a>"'],
		[
			'name' => 'size',
			'value' => '$data->formatSize()',
			'filter' => false,
		],
		'date',
	],
]);
?>
<div id="notifc" class='notifications bottom-right'></div>
<?php  $process= ShipmentProcess::model()->find('pid=:pid', [':pid'=>$model->id]);?>
<?php if ($_GET['r']=='eny'&&isset($process)&&($process->status== ShipmentProcess::ENTRY_SEND)):?>
<div class="form-group button">
<?php echo CHtml::submitButton('Confirm Entry', ['class'=>'btn btn-primary entry_confirm']); ?>
</div>
<?php endif;?>
<hr>
<script>
$(function(){
	$('.entry_confirm').click(function(event){
		event.preventDefault();
		if(confirm('Are you sure to Confirm Entry?')){
		$(this).hide();
		$.get('<?=$this->createUrl("parcelStatus/process", ["id" => $model->id,"status"=> ShipmentProcess::ENTRY_CONFIRM]);?>', function(r){
		 if(r == 'done'){
					 alert('Done', 5000);
				}else{
					 alert('falied');
		 }
		});
	}
	return false;
	});
});
</script>
