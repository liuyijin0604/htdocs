<form id="whot_form" name="whot_form" method="post" action="wh_ot?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>">
<div>
	<div class="row">
	<div class="col c9"><input type="text" id="gb_bc" name="bc" class="field" placeholder="Barcode" /></div>
	<div class="col c3"><input type="submit" class="btn" value="Enter" /></div>
	</div>
</div>
</form>
<script type="text/javascript">
$(function(){
	$('#whot_form').on('success', function(e,d){
		if(d.nf == 0) addLog('OT: '+d.bc+', '+d.msg);
		$(this).resetForm();
	});
});
</script>