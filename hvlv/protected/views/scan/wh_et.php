<form id="whet_form" name="whet_form" method="post" action="wh_et?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>">
<div>Consol.: <select id="cid" name="cid" class="field">
<option value="">Select One</option>
<?php
	$rs = ExcoConsol::model()->findAll(['condition' => 'status < 30', 'order' => 'id DESC']);
	$ra = [];
	foreach($rs as $r){
		echo'<option value="'.$r->id.'">'.$r->no.'</option>';
	}
?>
</select></div>
<div style="text-align:center;">
	<input type="hidden" id="b1" name="b1" />
	<input type="hidden" id="b2" name="b2" />
	<input type="text" id="gb_bc" name="bc" class="field c12" placeholder="Barcode" />
	<input type="submit" class="btn" value="Enter" /> &nbsp; <input type="button" id="reset_btn" class="btn" value="Clear" />
</div>
</form>
<script type="text/javascript">
$(function(){
	$('#whet_form').on("submit", function(e){
		var v = true;
		if($('#cid').val() == ''){
			alert("Please select a consol.");
			v = false;
		}
		if($('#b1').val() == ''){
			$('#b1').val($('#gb_bc').val());
			$('#gb_bc').val('');
			v = false;
		}else if($('#b2').val() == ''){
			$('#b2').val($('#gb_bc').val());
			$('#gb_bc').val('');
		}
		
		return v;
	}).on('success', function(e,d){
		if(d.nf == 0) addLog('ET: '+d.bc+', '+d.msg);
		$('#gb_bc, #b1, #b2').val('');
	});

	$('#reset_btn').on('click', function(){
		$('#gb_bc, #b1, #b2').val('');
	});
});
</script>