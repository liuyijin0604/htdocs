<h1><?=$this->t('Create WmsJob');?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');

	$('form#wms-job-form', tab.data('panel')).on('success', function(e, r){
		myApp.tabs.CreateTab({
			title: r.no,
			url: tab.data('url').replace('wmsJob/create','wmsJob/update/'+r.id),
		});
		tab.trigger('close');
	});
});
</script>