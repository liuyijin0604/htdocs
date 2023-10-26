<form id="pod_form" name="pod_form" method="post" action="pod?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>">
<input type="text" id="gb_bc" name="bc" class="field c12" placeholder="Barcode" />
<input type="text" id="sname" name="sname" class="field c12" placeholder="Name" />
<div style="background: #ddd; width:300px; height: 120px; margin: 0 auto;">
<div style="position:absolute; font-size: 32px; line-height:120px; width: 300px; text-align: center; color: #bbb; z-index:1;">Sign Here</div>
<canvas width="300" height="120" style="position: relative; z-index:2;"></canvas>
</div>
<input type="hidden" id="sig_data" name="sig" />
<div class="clear"></div>
<div style="text-align:center;">
<input type="button" class="btn" id="clear_btn" value="Clear Signature" /> &nbsp; <input type="submit" class="btn" value="Enter" />
</div>
</form>
<script type="text/javascript">
$(function(){
	var sigPad = new SignaturePad(document.querySelector("canvas"));
	$('#clear_btn').on("click", function(e){ sigPad.clear(); });
	$('#pod_form').on("submit", function(e){
		if($('#gb_bc').val() == ''){
			alert("Please scan barcodes.");
		}else if($('#sname').val() == ''){
			alert("Please enter name.");
		}else if(sigPad.isEmpty()){
			alert("Please provide signature.");
		}else{
			$('#sig_data').val(sigPad.toDataURL());
			return true;
		}
		return false;
	}).on('success', function(e,d){
		if(d.nf == 0) addLog('<img src="'+$('#sig_data').val()+'" width="150" height="60" /><br />POD: '+d.bc+', By: '+d.by);
		sigPad.clear();
		$(this).resetForm();
	});
});
</script>