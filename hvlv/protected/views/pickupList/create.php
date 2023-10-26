<h1><?=$this->t('Create Receipt');?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('form#shipment-receipt-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

});
</script>