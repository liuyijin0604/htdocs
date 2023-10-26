<div class="container" style="width: 100%"><div class="row" style="font-size: 30px">
<?php
foreach ($prods as $k => $prod) {
	$ean = explode('_', $k)[0];
	$name = explode('_', $k)[1];
	echo '<div id="' . $ean . '" class="col-xs-3" style="border: 3px solid #337ab7; text-align: center; color: ' . ($prod[0] == $prod[1] ? '#4cd964' : '#FC7272') . '">' . $ean . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="remain" style="font-size: 40px">' . $prod[0] . '</span>/<span class="total" style="color: black">' . $prod[1] . '</span></div>';
}
?>
</div></div>

<script type="text/javascript">
$(function() {
	var prods = Object.entries(JSON.parse('<?=json_encode($prods)?>'));
	var count = 0;

	if (prods) {
		prods.forEach(function(v, k) {
			count += prods[k][1][1] - prods[k][1][0];
		});
	}

	$('#submit').off('click').on('click', function() {
		var flag = false;
		var current;
		var barcode = $('#input').val();
		$('#input').val('');
		prods.forEach(function(v, k) {
			ean = v[0].split('_')[0];
			if (ean == barcode && prods[k][1][1] - prods[k][1][0] > 0) {
				flag = true;
				current = k;
				return;
			}
		});

		if (flag) {
			qty = parseInt($('.res #' + barcode + ' .remain').html()) + 1;
			$('.res #' + barcode + ' .remain').html(qty);
			if (qty == parseInt($('.res #' + barcode + ' .total').html())) {
				$('.res #' + barcode).prop('style', 'border: 3px solid #337ab7; text-align: center; color: #4cd964');
			}

			var audio = new Audio();
			audio.src = 'https://os.toplogistics.com.au/site/voice/confirmed.mp3';
			audio.play();
			prods[current][1][0] += 1;

			$.ajax({
				type: 'POST',
				data: { 'ean': barcode.split('&nbsp;&nbsp;&nbsp;')[0] },
				url: '<?=Yii::app()->createUrl("pack/pack/ajaxDoubleCheck", ["id" => $_GET["id"]])?>'
			});

			count --;
		} else {
			var audio = new Audio();
			audio.src = 'https://os.toplogistics.com.au/site/voice/beep_err.mp3';
			audio.play();
		}
	});
});
</script>