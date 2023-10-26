<h1><?=$this->t('Create Organisation');?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'own'=>$own)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');

	$('form#org-form', tab.data('panel')).on('success', function(e, r){
		myApp.tabs.CreateTab({
			title: 'Org-'+r.code,
			url: tab.data('url').replace('org/create','org/update/'+r.id),
		});
		tab.trigger('close');
	});
});
</script>