<?php if(!isset($_GET["tabid"])):?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
<?php endif?>
<div class="form">
<?php
echo AwbTracking::trackForm($_GET['code']);
?>
</div>
<script type="text/javascript">
$(function(){
	var winId = '<?=@$_GET["tabid"];?>';
	var win = $('#jqmw_<?=@$_GET["tabid"];?>');
	var vvc = $('img.vvc', win);

	$('.awb-tracking-form', win).on('submit', function(){
		setTimeout(function(){ win.jqmHide(); }, 1e3);
	});

	if(vvc.length > 0){
		vvc.on('click', function(){
			$(this).attr('src', '').attr('src', $(this).data('vu')+($(this).data('vu').indexOf('?') > 0? '&':'?')+'r='+Math.random());
		}).on('load', function(){
			/*var canvas = document.createElement("canvas");
			canvas.width =this.width;
			canvas.height =this.height;

			var ctx = canvas.getContext("2d");
			ctx.drawImage(this, 0, 0);

			var dataURL = canvas.toDataURL("image/png");
			$('input.vvc_txt').prop('readonly', true);
			$.post('tracking/readVvc', {data: dataURL.replace(/^data:image\/(png|jpg);base64,/, "")}, function(r){
				$('input.vvc_txt').val(r).prop('readonly', false);
			});*/
		});

		$('.awb-tracking-form', win).on('submit', function(){
			$.cookie("awb-tracking-"+$('img.vvc', win).data('pfx'), [$('input.vvc_txt').val(), Date.now()]);
		});
		var cc = $.cookie("awb-tracking-"+$('img.vvc', win).data('pfx'));
		if(cc){
			cc = cc.split(',');
			if((Number(cc[1]) + Number($('img.vvc', win).data('ttl'))) > Date.now()) $('input.vvc_txt').val(cc[0]);
			else{
				vvc.trigger('click');
			}
		}else{
			vvc.trigger('click');
		}
	}else if($('input.copy', win).length > 0){
		$('.awb-tracking-form', win).on('submit', function(){
			$('input.copy', win).select();
			document.execCommand("copy");
		});
	}else{
		setTimeout(function(){
			$('.awb-tracking-form', win).submit();
		}, $('iframe', win).length > 0? 1e3 : 0);
	}
	if(winId=='')
	{
		$('.awb-tracking-form').submit();
	}
});
</script>