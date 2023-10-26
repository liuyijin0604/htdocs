<form id="whsc_form" name="whsc_form" method="post" action="wh_sc?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>">
<div id="s1" style="text-align:center;">
	<input type="text" id="loc" name="loc" class="field c12" placeholder="Location" />
	<input type="text" id="gb_bc" name="bc" class="field c12" placeholder="Barcode" />
	<input type="submit" class="btn" value="Enter" /> &nbsp; <input type="button" id="reset_btn" class="btn" value="Clear" /> &nbsp; <a id="fin_btn" href="#">Finish</a>
</div>
<div id="s2" style="display: none;">
<h3>Check Slots</h3>
<input type="button" id="cancel_btn" class="btn" value="Cancel" />
<p><small>If empty leave the tick, if not empty and shipment number correct please untick. If not empty and shipment number incorrect please click on Cancel button, and rescan this location and parcel. When finish please click on Confirm button</small></p>
<div id="empties"></div>
<input id="cfm_btn" type="button" class="btn" value="Confirm" />
</div>
</form>
<script type="text/javascript">
$(function(){
	$('#whsc_form').on("submit", function(e){
		var v = true;
		var bc = $('#gb_bc').val();
		if(/ES-[A-Z]+\d+-\d+-\d+/.test(bc)){
			$('#loc').val(bc.substring(3));
			$('#gb_bc').val('');
			v = false;
		}
		if($('#loc').val() == ''){
			alert('Please scan location first');
			v = false;
		}
		
		return v;
	}).on('success', function(e,d){
		if(d.nf == 0) addLog('SC: '+d.bc+', '+d.msg);
		$('#gb_bc, #loc').val('');
	});

	$('#reset_btn').on('click', function(){
		$('#gb_bc, #loc').val('');
	});

	$('#fin_btn').on('click', function(){
		$('#s2').show();
		$('#empties').load("whsc_empt?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>");
		return false;
	});

	$('#cfm_btn').on('click', function(){
		if(window.confirm('Are you sure?')){
			$.post("whsc_empt?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>", $('input.emp').searialize(), function(){
				$('#s2').hide();
				$('#empties').empty();
			});
		}
	});

	$('#cancel_btn').on('click', function(){
		$('#s2').hide();
		$('#empties').empty();
	});
});
</script>