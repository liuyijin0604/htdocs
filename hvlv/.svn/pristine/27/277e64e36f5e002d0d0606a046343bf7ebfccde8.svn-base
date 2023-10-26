<form id="lob_form" name="lob_form" method="post" action="lob?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>">
<div style="text-align:center;">
	<div class="row">
	<div class="col c9"><input type="text" id="gb_bc" name="bc" class="field" placeholder="Barcode" /></div>
	<div class="col c3"><input type="submit" class="btn" value="Enter" /></div>
	</div>
</div>
</form>
<script type="text/javascript">
$(function(){
	$('#lob_form').on('success', function(e,d){
		if(d.nf == 0) addLog('LOB: '+d.bc+', <a class="undo" data-exp="'+((new Date()).getTime() + 18e5)+'" href="undoLob?md=<?=$_GET["md"];?>&sn=<?=$_GET["sn"];?>&bc='+d.bc+'&tid='+d.ti+'">undo</a>');
		$(this).resetForm();
	});
});
</script>