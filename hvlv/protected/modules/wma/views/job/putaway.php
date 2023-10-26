<div class="content-padded ">
<h3>Put Away</h3>
<div class="putaway-form">
<form action="" method="post" data-bit="1">
	<div class="input-addon">
  	<input class="plt barcode required" type="search" placeholder="Pallet/Carton Barcode" name="plt" />
  	<span><div class="toggle kplt"><div class="toggle-handle"></div></div></span>
  </div>
  <div class="input-addon">
  	<input class="loc barcode required" type="search" placeholder="Location Barcode" name="loc" />
  	<span><div class="toggle kloc"><div class="toggle-handle"></div></div></span>
  </div>
  <button type="submit" class="btn btn-primary btn-block">Save</button>
</form>
</div>
<div class="undo">
</div>
<div class="res">
</div>
</div>

<script type="text/javascript">
$(function(){
	$('input.plt').focus().on('change', function(){
		$('.res').load('<?=Yii::app()->getRequest()->requestUri;?>?info='+$(this).val());
		$('input.loc').focus();
	}).on('keydown', function(e){
		if(e.which == 13){
			$(this).trigger('change');
			return false;
		}
	}).on('afterBarcode', function(){
		$(this).trigger('change');
		if($('input.loc').val() != '') $('.putaway-form form').submit();
	});
	
	$('input.loc').on('keydown', function(e){
		if(e.which == 13 && $('input.plt').val() == ''){
			$('input.plt').focus();
			return false;
		}
	}).on('afterBarcode', function(){
		if($('input.plt').val() == ''){
			$('input.plt').focus();
			return false;
		}else{
			$('.putaway-form form').submit();
		}
	});

	$('.putaway-form form').on('success', function(e, r){
		$('input.plt').focus();
		if (!$('.kplt').hasClass('active')) {
			$('input[name="plt"]').val('');
		}
		if (!$('.kloc').hasClass('active')) {
			$('input[name="loc"]').val('');
		}
		var audio = new Audio();
		audio.src = 'https://os.toplogistics.com.au/site/voice/confirmed.mp3';
		audio.play();
	}).on('error', function(e, r) {
		var audio = new Audio();
		audio.src = 'https://os.toplogistics.com.au/site/voice/beep_err.mp3';
		audio.play();
	});
});
</script>
