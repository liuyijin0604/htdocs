<h1><?=$this->t('Update Range');?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('form#connote-range-form', win).on('success', function(r){
		win.data('opener').trigger('refresh');
		win.jqmHide();
	}).find('input, select').each(function(i){
		var me = $(this);
		if(me.attr('id') != 'ConnoteRange_status') me.prop('readonly', 'readonly');
	});
});
</script>