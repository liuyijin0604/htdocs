<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var pane = tab.data('panel');
	$($('.item_qty', pane).get(0)).trigger('change');
});
</script>