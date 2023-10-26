<h1><?=$this->t('Update Product');?> <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('form#ex-prodb-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

	$('a.dupli', win).on('click', function(){
		if(window.confirm('Are you sure to copy this product?')){
			win.jqmHide();
		}else{
			return false;
		}
	});

});
</script>