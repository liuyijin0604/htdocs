<h1><?=$this->t('Create Courier Shipment');?></h1>

<?php
if(empty($model->cnor)){
	$model->cnor = new Addr;
	$model->cnor->country = 'Australia';
}
if(empty($model->cnee)){
	$model->cnee = new Addr;
	$model->cnee->country = 'Australia';
}
?>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	$('form#coparcel-form', tab.data('panel')).on('success', function(e, r){
		var url = tab.data('url').replace('coParcel/create','coParcel/update/'+r.id);
		tab.data('url', url).trigger('load');
	});

});
</script>
