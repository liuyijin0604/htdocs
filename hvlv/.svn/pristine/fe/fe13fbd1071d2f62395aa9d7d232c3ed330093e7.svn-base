<?php
if($model->status == 70):
?>
<div style="text-align: right;"><a class="ajax_link" id="fetchTracking" href="<?=$this->createUrl('coParcel/fetchTracking',['id'=> $model->id]);?>" title="Fetch IDs"><div class="icon" style="background-position:-192px -240px"></div> Fetch Tracking</a></div>
<?php
endif;
$tracking = new Tracking('search');
$tracking->pid = $model->id;

$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id'=>$_GET["tabid"].'_parcel-tracking-grid',
	'cssFile' => false,
	'dataProvider'=>$tracking->search(),
	'formUrl' => $this->createUrl('coParcel/trackingGrid', array('id'=>$model->id)),
	'filter'=>null,
	'summaryText' => '',
	'editable' => 'strtotime($data->dt) > (time() - 86400)',
	'showQuickBar' => $model->status < 90,
	'afterSave' => "function(r){
		if(r.done == true){
			myApp.notice(r.msg, 5000);
		}else{
			myApp.alert(r.msg, false);
		}
		return r.done;
	}",
	'columns'=>array(
		array('name' => 'dt', 'class' => 'CEditableColumn', 'inputOptions' => array('class' => 'datetime_input')),
		array('name' => 'activity', 'class' => 'CEditableColumn'),
		array('name' => 'depot', 'class' => 'CEditableColumn'),
		array('header' => 'Info', 'type' => 'raw', 'value' => '$data->getInfo()'),
		array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save}'),
	),
));
?>
<br />
<h3>Transhipment</h3>
<?php
foreach($model->trans as $ts){
	echo $ts->infoLink().'<br />';
}
?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('#fetchTracking', panel).on('success', function(e, r){
		$('#<?=$_GET["tabid"];?>_parcel-tracking-grid', panel).yiiGridView('update');
	});
});
</script>