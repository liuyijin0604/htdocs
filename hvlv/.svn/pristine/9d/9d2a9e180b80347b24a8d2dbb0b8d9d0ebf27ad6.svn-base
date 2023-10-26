<div class="content-padded ">
<h3>Stock Relocation <span class="exp-ef icon icon-down" style="display:none;"></span></h3>
<div class="reloc-form">
<form action="" method="post" data-bit="3">
  	<div class="input-addon"><input class="plt barcode required" type="search" placeholder="From Pallet" name="plt" />
	<span><div class="toggle kplt active"><div class="toggle-handle"></div></div></span>
	</div>
	<div class="input-addon"><input class="plt2 barcode required" type="search" placeholder="To Pallet" name="plt2" />
	<span><div class="toggle kplt2"><div class="toggle-handle"></div></div></span>
	</div>
	<input class="prod barcode required" type="search" placeholder="Product Name/Barcode" name="prod" />
	
	<button type="submit" class="btn btn-primary btn-block"><span class="icon icon-search"></span>Search</button>
</form>
</div>
<div class="res">
</div>
<div class="his">
</div>
</div>

<script type="text/javascript">
$(function(){
	$('input.plt').on('keydown', function(e){
		if(e.which == 13){
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(){
		if($('.reloc-form .kplt2').hasClass('active') || $('.reloc-form .plt2').val() != ''){
			$('input.prod').trigger('focus');
		}else{
			$('input.plt2').trigger('focus');
		}
	}).focus();
	
	$('input.plt2').on('keydown', function(e){
		if(e.which == 13){
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(){
		$('input.prod').trigger('focus');
	});

	$('input.prod').on('keydown', function(e){
		if(e.which == 13){
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(){
		$('.reloc-form form').submit();
	});

	$('span.exp-ef').on('click touchend', function(){
		$('.reloc-form').slideDown();
		$('span.exp-ef').hide();
	});

	$('.reloc-form form').on('success', function(e, r){
		wmaApp.btnLoading($('button[type=submit]', this), true);
		$('.res').html(r.data);
		$('div.his').hide();
		if(r.done){
			$('.reloc-form').slideUp();
			$('span.exp-ef').show();
		}else{
			if($('input.prod').length > 0) $('input.prod').val('').focus();
			else $('input.plt').val('').trigger('focus');
		}
	});

	$('.res').on('success', '.reloc-data-form', function(e,r){
		$('.reloc-form').slideDown();
		$('span.exp-ef').hide();
		$('.reloc-data-form input.mvq').each(function(){
			var q = Number($(this).val());
			if(q == 0) return;
			$('.his').prepend('<p style="border-top: 1px solid #aaa; padding-top: 5px;">'+q+' &times; '+$(this).data('stock')+'<br />'+$('.reloc-form .plt').val()+' => '+$('.reloc-form .plt2').val()+'</p>');
		});
		$('.his').show();
		$('.his>p').each(function(i){
			if(i > 19) $(this).remove();
		});
		$('.res').empty();

		if(!$('.reloc-form .kplt').hasClass('active')){
			$('input.plt').val('');
			$('input.plt').trigger('focus');
		}else if(!$('.reloc-form .kplt2').hasClass('active')){
			$('input.plt2').trigger('focus');
		}else{
			$('input.prod').trigger('focus');
		}
		if(!$('.reloc-form .kplt2').hasClass('active')) $('input.plt2').val('');
		$('input.prod').val('');
	});
});
</script>
