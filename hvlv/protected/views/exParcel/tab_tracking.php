<?php
if(in_array($model->status, [80, 90])):
?>
<div style="text-align: right;"><a class="ajax_link" id="fetchTracking" href="<?=$this->createUrl('exParcel/fetchTracking',['id'=> $model->id]);?>" title="Update Tracking"><div class="icon" style="background-position:-192px -240px"></div> Update Tracking</a></div>
<?php
endif;
$tracking = new Tracking('search');
$tracking->pid = $model->id;

$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id'=>$_GET["tabid"].'_parcel-tracking-grid',
	'cssFile' => false,
	'dataProvider'=>$tracking->search(),
	'formUrl' => $this->createUrl('exParcel/trackingGrid', array('id'=>$model->id)),
	'filter'=>null,
	'summaryText' => '',
	'editable' => 'strtotime($data->dt) > (time() - 86400)',
	'showQuickBar' => true,
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
if($model->status == 80){
	if (preg_match('/(20|36|88|26|68|44)\d{10}/', $model->ref)) {
		echo '申通: <a href="http://www.kuaidi100.com/all/st.shtml?mscomnu='.$model->ref.'" target="_blank">'.$model->ref.'</a>';
	} else if (preg_match('/(12|13|21|22|23|88|81)\d{8}/', $model->ref)) {
		echo '圆通: <a href="http://www.kuaidi100.com/all/yt.shtml?mscomnu='.$model->ref.'" target="_blank">'.$model->ref.'</a>';
	} else {
		echo 'EMS: <a href="http://www.kuaidi100.com/all/ems.shtml?mscomnu='.$model->ref.'" target="_blank">'.$model->ref.'</a>';
	}
}else{
	foreach($model->trans as $ts){
		echo $ts->infoLink().'<br />';
	}
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