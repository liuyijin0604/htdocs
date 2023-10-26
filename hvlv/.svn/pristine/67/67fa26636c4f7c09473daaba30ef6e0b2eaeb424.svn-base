<div class="form">
<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'scan-form',
	'enableAjaxValidation'=>false,
));
?>
<p>Barcode: <input id="scan" type="text" size="30" name="barcode" /></p>
<?php $this->endWidget(); ?>
<div id="result" style="margin: 10px; border: 1px solid;padding:20px 30px; font-weight: bold; font-size: 32px;">
</div>
<audio id="sound" src=""></audio>
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('select#wid', panel).on('change', function(){
		$('input#scan', panel).focus();
	}).focus();

	$('form#scan-form', panel).data('custom_success', function(r){
		$('#result', panel).prepend($('<p>'+r.msg+'</p>').css('color', r.color).fadeIn());
		$('#sound', panel).attr('src', 'site/voice/'+r.sound+'.mp3');
		$('#sound', panel)[0].play();
		if(r.id > 0){
			v = $('#sids', panel).val()+','+r.id;
			$('#sids', panel).val(v);
		}
		return true;
	}).on('submit', function(){
		$('input#scan', panel).focus();
	});
	$('input#scan', panel).on('focus', function(){
		$(this).select();
	});
});
</script>