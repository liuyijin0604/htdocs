<div style="position: absolute; right: 20px;"><a href="<?=$this->createUrl('outturn/create');?>" class="jqm_link"><div style="background-position:-16px 0" class="icon"></div> Import CSV</a></div>
<h1>Scan for Outturn</h1>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'scan-form',
	'enableAjaxValidation'=>false,
));
?>
<p>Barcode: <input id="scan" type="text" size="30" name="barcode" /></p>
<?php $this->endWidget(); ?>
<div id="result" style="display:none;margin: 20px; border: 1px solid;padding:30px 40px; font-weight: bold; font-size: 42px;">
</div>
<audio id="sound" src=""></audio>
</div>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('input#scan', panel).focus();

	$('form#scan-form', panel).data('custom_success', function(r){
        if ( r.nosound == 0 ) {
            $('#sound', panel).attr('src', 'site/voice/' + r.sound + '.mp3');
            $('#sound', panel)[0].play();
        }
		$('#result', panel).text(r.msg).css('color', r.color).fadeIn(100, function(){
			//alert(r.msg);
		});
		return true;
	}).on('submit', function(){
		$('input#scan', panel).focus();
	});
	$('input#scan', panel).on('focus', function(){
		$(this).select();
	});
	
	$('input#scan', panel).focus();
});
</script>