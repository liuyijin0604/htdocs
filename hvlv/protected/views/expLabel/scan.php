<div class="form">
<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'label-scan-form',
	'enableAjaxValidation'=>false,
));
?>
<div>Consol.: <select id="cid" name="cid" class="field">
<option value="">Select One</option>
<?php
	$rs = ExcoConsol::model()->findAll(['condition' => 'status < 70', 'order' => 'id DESC']);
	$ra = [];
	foreach($rs as $r){
		if(ExParcel::model()->count('consol_id = :id', [':id' => $r->id]) == 0) continue;
		echo'<option value="'.$r->id.'">'.$r->no.'</option>';
	}
?>
</select>
 &nbsp; Pallet: <select id="pid" name="pid" class="field"><option value="">Select One</option></select>
 <div id="plref" style="background-position:-176px -32px" class="icon"></div> <div id="pladd" style="background-position:-16px 0" class="icon"></div>
 &nbsp; <label class="radio_label"><input type="checkbox" name="ptz" value="1" /> Scan to pallet</label>
</div>
<p>Barcode: <input id="scan" type="text" size="30" name="bc" /></p>
<?php $this->endWidget(); ?>
</div>
<div style="clear:both">
<div id="result" style="border: 1px solid;padding:10px; box-sizing: border-box; font-weight: bold; font-size: 20px; width:50%; float: left;">
</div>
<div style="box-sizing: border-box; width:50%; float: left;padding-left: 20px;">
<div style="width:100%;">
<iframe id="prntifm" name="prntifm" style="border:1px solid; width:100%;height: 650px;"></iframe>
</div>
</div>
</div>
<audio id="sound" src=""></audio>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	$('select#cid', panel).on('change', function(){
		$('input#scan', panel).focus();
	}).focus();

	$('form#label-scan-form', panel).data('custom_success', function(r){
		var m = $('<p>'+r.msg+'</p>');
		$('#result', panel).prepend(m.css('color', r.color).fadeIn());
		if(r.nf === 1){
			$('#sound', panel).attr('src', 'site/voice/not_found.mp3');
			$('#sound', panel)[0].play();
		}else if(r.nf ===2){
			$('#sound', panel).attr('src', 'site/voice/printed.mp3');
			$('#sound', panel)[0].play();
		}else{
			$('#prntifm', panel).attr('src', m.find('a.print').attr('href'));
		}
		return true;
	}).on('submit', function(){
		$('input#scan', panel).focus();
	});

	$('input#scan', panel).on('focus', function(){
		$(this).select();
	});
	$(panel).on('click', 'a.print', function(){
		var pc = $(this).data('pc');
		if(pc > 0){
			$(this).data('pc', pc+1);
			$(this).html('Printed <span>('+(pc+1)+')</span>');
		}else{
			$(this).data('pc', 1);
		}
		$('input#scan', panel).focus();
	});

	var loadPallets = function(){
		$('#pid', panel).load('expLabel/pallets/'+$('#cid').val());
	};

	$('#cid', panel).on('change', function(){
		loadPallets();
	});
	
	$('#plref', panel).on('click',function(){
		loadPallets();
	});

	$('#pladd', panel).on('click', function(){
		if($('#cid', panel).val() == ''){
			alert("Please select a consol.");
			return false;
		}else{
			if(window.confirm('Create new pallet?')){
				$.get('expLabel/addpallet/'+$('#cid').val(), function(){
					loadPallets();
				});
			}
		}
	});
});
</script>