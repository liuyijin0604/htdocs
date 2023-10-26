<?php
if($model->status >=70):
?>
<div style="text-align: right;"><a class="ajax_link" id="fetchTracking" href="<?=$this->createUrl('imParcel/fetchTracking',['id'=> $model->id]);?>" title="Fetch IDs"><div class="icon" style="background-position:-192px -240px"></div> Fetch Tracking</a></div>
<?php
endif;
$tracking = new Tracking('search');
$tracking->pid = $model->id;

$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id'=>'_parcel-tracking-grid',
	'cssFile' => false,
	'dataProvider'=>$tracking->search(),
	'filter'=>null,
	'columns'=>array(
		array('name' => 'dt'),
		array('name' => 'activity', ),
		array('name' => 'depot', ),
		
	),
));
?>
<br />
<style>
	span.reason{
		margin-right: 1.0em;
		color: blue; 
		
	}
</style>
<?php if($model->status==55){
	$held_reason='<h3 style="color: #be3426">Held Reason</h3>';
	if(($model->bwf&4)>0){
		$held_reason.="<span class='reason'>High Value</span>";
	}
	if(($model->bwf&8)>0){
		$held_reason.="<span class='reason'>Sac</span>";
	}
	if(($model->bwf&16)>0){
		$held_reason.="<span class='reason'>Border</span>";
	}
	if(($model->bwf&32)>0){
		$held_reason.="<span class='reason'>AQIS</span>";
	}
	if(($model->bwf&512)>0){
		$held_reason.="<span class='reason'>EMPP</span>";
	}
	
	echo $held_reason;

}?>


<?php if(isset($model->process)&&($model->process->status<24)&&$model->status<=60):?>
<h3 style="color: #be3426">Customs Process</h3>
<h4> Shipment on custom process stage:<b><?= $model->getProcessStatus();?></b></h4>
<?php
if($model->process->status<=6||$model->process->status==10||$model->process->status==12){
	if(($model->bwf&4)>0){ 
		$link=$model->getTheHashUrl(2);
	}else if(($model->bwf&512)>0){
		$link=$model->getTheHashUrl(5);
	}else if(($model->bwf&32)>0){
		$link=$model->getTheHashUrl(6);
	}
	
	if ($model->process->status == 12) 
	{
		$link=$model->getTheHashUrl(3);
	}
	if(!empty($link))
	{
	echo "<a href='$link' style='color:blue;text-decoration: underline;' target='_blank'>Upload Document Link</a>";
	}
}
// } else if ($model->process->status == 12) {
// 	$link=$model->getTheHashUrl(3);
// 	echo "<a href='$link' style='color:blue;text-decoration: underline;' target='_blank'>Confirm Link</a>";
// }

if(!empty($model->mdata['custom_note'])){
	echo '<h4 style="color: #be3426">Custom Note</h4>','<h4>'.$model->mdata['custom_note'].'</h3>';
	
}
echo '<h4 style="color: #be3426">Process Log</h4>';
$log = new Log;
$log->model = get_class($model->process);
$log->lid = $model->process->id;
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id'=>'_log-grid',
	'cssFile' => false,
	'summaryText'=>'',
	'dataProvider'=> $log->search(8),
	'columns'=>array(
		'time',
		array(
			'name'=>'type',
			'value'=>'$data->getType()',
		),
		array(
			'name'=>'meta',
			'value'=>'$data->getExtra()',
		),
	),
));

?>
		
<hr/>
		
<?php endif;?>
<script type="text/javascript">
$(function(){
	$('#fetchTracking').on('success', function(e, r){
		$('_parcel-tracking-grid', panel).yiiGridView('update');
	});
});
</script>