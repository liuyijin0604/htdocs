<form id="whgp_form" name="whgp_form" method="post" action="wh_gp?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>">
<input type="text" id="driver" name="GatePass[driver]" class="field c12" placeholder="Driver Name" />
<input type="text" id="company" name="GatePass[company]" class="field c12" placeholder="Company" />
<input type="text" id="rego" name="GatePass[rego]" class="field c6 left" placeholder="Vehicle Rego" />
<input type="text" id="ref" name="GatePass[ref]" class="field c6" placeholder="Reference" />
<div class="step2">
<div style="clear:both;background: #ddd; width:300px; height: 120px; margin: 0 auto;">
<div style="position:absolute; font-size: 32px; line-height:120px; width: 300px; text-align: center; color: #bbb; z-index:1;">Sign Here</div>
<canvas width="300" height="120" style="position: relative; z-index:2;"></canvas>
</div>
<input type="hidden" id="sig_data" name="GatePass[sig]" />
<input type="hidden" id="sids" name="sids" />
<div class="clear"></div>
<div style="text-align:center;">
<input type="button" class="btn" id="clear_btn" value="Clear Signature" /> &nbsp; <input type="submit" class="btn" value="Save" /> 
</div>
</div>
</form>
<form id="whgp_bconly_form" name="whgp_bconly_form" method="post" action="wh_gp?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>&bconly=1">
<div class="step1">
<input type="text" id="gb_bc" name="bc" class="field c12" placeholder="Barcode" />
<div style="text-align:center;">
  <input type="submit" class="btn" value="Enter" /> &nbsp; <input type="button" id="btn_fin" class="btn" value="Finish" />
</div>
</div>
<div class="step2" style="text-align:center;">
	<input type="button" class="btn" id="btn_more" value="Scan More" />
</div>
</form>
<script type="text/javascript">
$(function(){
	var goStep = function(s){
		$('.step1, .step2').hide();
		$('.step'+s).show();
	};
	goStep(1);
	var sigPad = new SignaturePad(document.querySelector("canvas"));
	$('#clear_btn').on("click", function(e){ sigPad.clear(); });
	$('#whgp_form').on("submit", function(e){
		if($('#sids').val() == ''){
			alert("Please scan barcodes.");
		}else if($('#driver').val() == ''){
			alert("Please enter driver name.");
		}else if($('#rego').val() == ''){
			alert("Please enter rego.");
		}else if(sigPad.isEmpty()){
			alert("Please provide signature.");
		}else{
			$('#sig_data').val(sigPad.toDataURL());
			return true;
		}
		return false;
	}).on('success', function(e,d){
		addLog('<img src="'+$('#sig_data').val()+'" width="150" height="60" /><br />GP: '+d.ref+', Tot: '+d.tot+', By: '+d.by);
		sigPad.clear();
		$(this).resetForm();
		$('#sids, #sig_data').val('');
		goStep(1);
	});

	$('#whgp_bconly_form').on('success', function(e,d){
		if(d.nf == 0) addLog('GP: '+d.bc+', '+d.msg);
		if(d.id > 0){
			v = $('#sids').val()+','+d.id;
			$('#sids').val(v);
		}
		$(this).resetForm();
	});

	$('#btn_fin').click(function(){
		goStep(2);
	});

	$('#btn_more').click(function(){
		goStep(1);
	});
});
</script>