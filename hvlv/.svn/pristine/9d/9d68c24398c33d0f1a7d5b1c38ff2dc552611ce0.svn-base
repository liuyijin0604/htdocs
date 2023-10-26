<form id="whep_form" name="whep_form" method="post" action="wh_ep?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>">
<div class="c12">Consol.: <select id="cid" name="cid" class="field">
<option value="">Select One</option>
<?php
	$rs = ExcoConsol::model()->findAll(['condition' => 'status < 30', 'order' => 'id DESC']);
	$ra = [];
	foreach($rs as $r){
		echo'<option value="'.$r->id.'">'.$r->no.'</option>';
	}
?>
</select></div>
<div class="row">
	<div class="col c9"><input type="text" id="gb_bc" name="bc" class="field" placeholder="Barcode" /></div>
	<div class="col c3"><input type="submit" class="btn" value="Enter" /></div>
</div>
</form>
<script type="text/javascript">
$(function(){
	$('#whep_form').on("submit", function(e){
		if($('#cid').val() == ''){
			alert("Please select a consol.");
			return false;
		}
		return true;
	}).on('success', function(e,d){
		if(d.nf == 0) addLog('EP: '+d.bc+', '+d.msg);
		$('#gb_bc').val('');
	});
});
</script>