<h1><?=$this->t('Attach ID');?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'addr' => $addr)); ?>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('form#cn-id-form', win).on('success', function(e, r){
		win.data('opener').trigger('reload_tab');
		win.jqmHide();
	});

});
</script>