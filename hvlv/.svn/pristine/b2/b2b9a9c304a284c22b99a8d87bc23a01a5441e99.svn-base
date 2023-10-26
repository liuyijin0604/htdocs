<form id="whec_form" name="whec_form" method="post" action="wh_ec?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>">
<div class="c12">Consol.: <select id="cid" name="cid" class="field">
<option value="">Select One</option>
<?php
	$rs = ExcoConsol::model()->findAll(['condition' => 'status < 70', 'order' => 'id DESC']);
	$ra = [];
	foreach($rs as $r){
		echo'<option value="'.$r->id.'">'.$r->no.'</option>';
	}
?>
</select>
</div>
<div>
<div class="c9">Pallet: <select id="pid" name="pid" class="field">
<option value="">##</option>
</select> 
<input type="button" id="np_btn" class="btn c3" value="New Pallet" />
</div>
</div>
<div class="row">
	<div class="col c9"><input type="text" id="gb_bc" name="bc" class="field" placeholder="Barcode" /></div>
	<div class="col c3"><input type="submit" class="btn" value="Enter" /></div>
</div>
</form>
<div id="tally" style="font-size:2em;"></div>
<script type="text/javascript">
$(function(){
	$('#whec_form').on("submit", function(e){
		if($('#pid').val() == ''){
			alert("Please select a consol/pallet.");
			return false;
		}
		return true;
	}).on('success', function(e,d){
		if(d.nf == 1) return;

		if(d.dw == 2){
			addLog('EC: '+d.bc+', in pallet '+d.pn+', <a class="undo" data-exp="'+((new Date()).getTime() + 18e5)+'" href="undoWhEc?md=<?=$_GET["md"];?>&sn=<?=$_GET["sn"];?>&pid='+d.pid+'&bc='+d.bc+'">remove</a>');
		}else if(d.dw == 1){
			addLog('EC: '+d.bc+', already scaned, <a class="undo" data-exp="'+((new Date()).getTime() + 18e5)+'" href="undoWhEc?md=<?=$_GET["md"];?>&sn=<?=$_GET["sn"];?>&pid='+d.pid+'&bc='+d.bc+'">undo</a>');
		}else{
			addLog('EC: '+d.bc+', to pallet '+d.pn+', <a class="undo" data-exp="'+((new Date()).getTime() + 18e5)+'" href="undoWhEc?md=<?=$_GET["md"];?>&sn=<?=$_GET["sn"];?>&pid='+d.pid+'&bc='+d.bc+'">undo</a>');
		}
		$('#tally').html(d.tt);
		$('#gb_bc').val('');
	}).on('undo', function(e, d){
		$('#tally').html(d.tt);
	});

	var loadPallets = function(){
		$('#pid').load('wh_ec?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>&getPallets='+$('#cid').val());
	};

	$('#cid').on('change', function(){
		loadPallets();
	});

	$('#pid').on('change', function(){
		var pid = $('#pid').val();
		if(pid > 0)	$('#tally').load('wh_ec?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>&getTally='+pid);
	});

	$('#np_btn').on('click', function(){
		if($('#cid').val() == ''){
			alert("Please select a consol.");
			return false;
		}else{
			if(window.confirm('Create new pallet?')){
				$.get('wh_ec?md=<?=$_GET['md'];?>&sn=<?=$_GET['sn'];?>&newPallet='+$('#cid').val(), function(){
					loadPallets();
				});
			}
		}
	});

});
</script>