<h1>检查</h1>
<div class="check-form" style="margin-top: 20px; margin-bottom: 10px;">
<form action="" method="post" data-bit="1">
	<input class="prod barcode required" type="search" placeholder="Product Barcode" name="ean" />
</form>
</div>
<div class="res">
	<ul class="table-view"></ul>
</div>

<script type="text/javascript">
$(function(){
	var prods = Object.entries(JSON.parse('<?=json_encode($prods)?>'));
	var count = 0;

	if (prods) {
		prods.forEach(function(v, k) {
			ean = prods[k][0].split('_')[0];
			name = prods[k][0].split('_')[1];
			$('#double_check .res .table-view').append('<li class="table-view-cell table-view-cell-full" style="border: 3px solid ' + (prods[k][1][0] == prods[k][1][1] ? '#4cd964' : '#FC7272') + '"><div class="row"><div class="col-12">' + name + '</div></div><div class="row" id="' + ean + '"><div class="col-6">' + ean + '</div><div class="col-6"><span class="remain" style="' + (prods[k][1][0] == prods[k][1][1] ? 'color: #4cd964' : 'color: #FC7272') + '">' + prods[k][1][0] + '</span>/<span class="total">' + prods[k][1][1] + '</span></div></div></li>');
			count += prods[k][1][1] - prods[k][1][0];
		});
	}

	$('#double_check input.prod').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function() {
		// $(this).prop('disabled', true);

		var flag = false;
		var barcode = $(this).val();
		prods.forEach(function(v, k) {
			ean = v[0].split('_')[0];
			if (ean == barcode && prods[k][1][1] - prods[k][1][0] > 0) {
				flag = true;
				prods[k][1][0] += 1;
				return;
			}
		});

		if (flag) {
			qty = parseInt($('#double_check .res #' + barcode + ' .remain').html()) + 1;
			$('#double_check .res #' + barcode + ' .remain').html(qty);
			if (qty == parseInt($('#double_check .res #' + barcode + ' .total').html())) {
				$('#double_check .res #' + barcode + ' .remain').prop('style', 'color: #4cd964');
				$('#double_check .res #' + barcode).parent('li').prop('style', 'border: 3px solid #4cd964');
			}

			var audio = new Audio();
			audio.src = 'https://os.toplogistics.com.au/site/voice/confirmed.mp3';
			audio.play();

			$.ajax({
				type: 'POST',
				data: { 'ean': barcode.split('&nbsp;&nbsp;&nbsp;')[0] },
				url: '<?=Yii::app()->createUrl("wma/job/ajaxDoubleCheck", ["id" => $_GET["id"]])?>'
			});

			count --;
		} else {
			var audio = new Audio();
			audio.src = 'https://os.toplogistics.com.au/site/voice/beep_err.mp3';
			audio.play();
		}

		// $('#double_check input.prod').removeProp('disabled').focus();
	});
});
</script>