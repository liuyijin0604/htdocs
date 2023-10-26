<form id="whes_form" name="whes_form" method="post" action="wh_es?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>">
<div class="row">
<div class="col c9"><input type="text" class="field" name="bsid" id="bsid" placeholder="Begin Location" /></div>
</div>
<div class="row">
	<div class="col c9"><input type="text" id="gb_bc" name="bc" class="field" placeholder="Barcode" /></div>
	<div class="col c3"><input type="submit" class="btn" value="Enter" /></div>
</div>
</form>
<script type="text/javascript">
$(function(){
	$('#whes_form').on("submit", function(e){
		var bc = $('#gb_bc').val();
		if(/ES-[A-Z]+\d+-\d+-\d+/.test(bc)){
			$('#bsid').val(bc.substring(3));
			$('#gb_bc').val('');
			return false;
		}
		return true;
	}).on('success', function(e,d){
		addLog('ES: '+d.msg);
		$('#gb_bc').val('');
	});
	$('#bsid').autoComp({ url: "esblSuggest?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>" });
});
</script>