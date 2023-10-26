<h1><?=$this->t('Export Products');?></h1>
<form id="form1" name="form1" class="ifrm-form" enctype="multipart/form-data" method="post" action="export/products" target="<?=$_GET["tabid"];?>_ifrm">
Retailer:
<?php echo CHtml::dropDownList('ret', '', Org::getRetailers(), array('empty' => $this->t('Select One'), 'id'=> "retailer")); ?>
<div id="pdls">
</div>
<p id="sus" style="display:none"><a href="#" id="sall">Select All</a> | <a href="#" id="usall">Unselect All</a><br />
<br /><input type="submit" value="Export" /><br />
<small>It may take few minutes to compile the pack file, please be patient.</small></p>
</form>

<iframe name="<?=$_GET["tabid"];?>_ifrm" id="<?=$_GET["tabid"];?>_ifrm" src="" border="0" style="display:none;">
</iframe>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#retailer', tab.data('panel')).change(function(){
		$('#pdls', tab.data('panel')).empty();
		$('#sus', tab.data('panel')).hide();
		if($('#retailer', tab.data('panel')).val() > 0){
			$('#pdls', tab.data('panel')).load('export/products?ret='+$('#retailer', tab.data('panel')).val());
			$('#sus', tab.data('panel')).show();
		}
	});
	$('#sall', tab.data('panel')).click(function(){
		$('#pdls input', tab.data('panel')).attr('checked', true);
	});
	$('#usall', tab.data('panel')).click(function(){
		$('#pdls input', tab.data('panel')).attr('checked', false);
	});
});
</script>