<h1><?=$this->t('Create Product');?></h1>

<?php 
if(!empty($_GET['g'])) $model->name_zh = $_GET['g'];
if(!empty($_GET['t'])) $model->type = $_GET['t'];
echo $this->renderPartial('_form', array('model'=>$model));
?>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('form#ex-prodb-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

});
</script>