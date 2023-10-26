<div style="position: absolute; right: 20px;">
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('exAfs/label', array('id'=>$model->id))?>" target="_blank">Label</a></li>
	</ul>
</div>
</div>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var pane = tab.data('panel');
	$($('.item_qty', pane).get(0)).trigger('change');
});
</script>