<form id="myc_form" name="myc_form" method="post" action="myc?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>">
<div style="text-align:center;">
	<div class="row">
	<div class="col c9"><input type="text" id="gb_bc" name="bc" class="field" placeholder="Barcode" /></div>
	<div class="col c3"><input type="submit" class="btn" value="Enter" /></div>
	</div>
</div>
</form>
<script type="text/javascript">
$(function(){
	$('#myc_form').on('success', function(e,d){
		if(d.nf == 0) addLog('MYC: '+d.bc+', <a class="undo" data-exp="'+((new Date()).getTime() + 18e5)+'" href="undoMyc?md=<?=$_GET["md"];?>&sn=<?=$_GET["sn"];?>&bc='+d.bc+'&tid='+d.ti+'">undo</a>');
		$(this).resetForm();
	});
});
</script>