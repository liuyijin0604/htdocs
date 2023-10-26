<div class="picking-entry-form">
<form action="<?=$this->createUrl('job/batchPickingEntry')?>" method="post" data-bit="1">
<?php if ($np != '<div class="table-view-cell table-view-cell-full" style="margin: -15px -15px 0 -15px;"><b></b> &times; <p></p></div>') { ?>
	<div class="np">
	<?php echo $np; ?>
	</div>
	<input class="plt barcode required" type="search" placeholder="Pallet Barcode" name="plt" />
	<input class="prod barcode required" type="search" placeholder="Product Name/Barcode" name="prod" />
	捡取数量&nbsp;<input class="qty tqty barcode required" type="search" placeholder="Qty" name="qty" value="<?=$qty?>" style="width: 100px" /> / <span class="pqty"><?=$qty?></span>
	<br>缺货数量&nbsp;<input class="tqty barcode required" type="search" placeholder="Qty" name="short" value="0" style="width: 100px" /> / <span class="pqty"><?=$qty?></span>
	<input type="hidden" name="batch_no" value="<?=$batch_no?>" />
	<button type="submit" class="btn btn-primary btn-block">Submit</button>
<?php } else { ?>
All picked.
<?php } ?>
</form>
</div>
<div class="entry-res">
</div>
<div class="picking-log">
</div>

<script type="text/javascript">
$(function(){
	$('.picking-entry-form input.plt').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function() {
		$('.picking-entry-form input.prod').trigger('afterBarcode');
	});

	$('.picking-entry-form input.prod').on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(e) {
		if ($('.picking-entry-form input.prod').val() != '') $('.picking-entry-form input.qty').trigger('afterBarcode');
		else $('.picking-entry-form input.prod').focus();
	});

	$('.picking-entry-form input.qty').on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(e) {
		if ($('.picking-entry-form input.qty').val() != '') $('.picking-entry-form').submit();
		else $('.picking-entry-form input.qty:first').focus();
	});

	// $('.picking-entry-form form').on('submit', function(e) {
	// 	var total = '<?=$qty?>';
	// 	$('.picking-entry-form form').find('input.tqty').each(function() {
	// 		total -= parseInt($(this).val());
	// 	});
	// 	if (total < 0) {
	// 		alert('Qty cannot exceed total');
	// 		return false;
	// 	}
	// });

	$('.picking-entry-form form').on('success', function(e, r) {
		var qtys = $('input.qty').val().split("");
		$('input.plt').val('');
		$('input.prod').val('');
		$('input.qty').val('');
		$('input.plt').focus();
		$('.entry-res').html('<h1 style="font-size: 30px; color: green">' + r.msg + '</h1>');
		if (r.np == '<div class="table-view-cell table-view-cell-full" style="margin: -15px -15px 0 -15px;"><b></b> &times; <p></p></div>') {
			$('.np').html('All picked');
			$('.picking-entry-form input, button').hide();
		} else {
			$('.np').html(r.np);
		}
		$('input.qty').val(r.qty);
		$('.pqty').html(r.qty);

		let count = 1;
		for (let count = 0; count < qtys.length; count++) {
			setTimeout(function() {
				audio = new Audio();
				audio.src = 'https://os.toplogistics.com.au/site/voice/' + qtys[count] + '.mp3';
				audio.play();
			}, 500 * count);
		}

		if (wmaApp.storage.pickingLog == undefined || Object.keys(wmaApp.storage.pickingLog).length == 0) {
			wmaApp.storage.write('pickingLog', {});
		}

		var uuid = wmaApp.uuid();
		wmaApp.storage.pickingLog.write(uuid, ['<?=$batch_no?>', r.prod_name, r.prod_ean, r.loc_name, r.old_qty]);

		showLog();

		setTimeout(function() {
			$('.entry-res').html('');
		}, 10e3);
	}).on('error', function(e, r) {
		$('input.' + r.code).focus();
		$('.entry-res').html('<h1 style="font-size: 30px; color: red">' + r.msg + '</h1>');

		setTimeout(function() {
			$('.entry-res').html('');
		}, 10e3);
	});

	function showLog() {
		if (wmaApp.storage.pickingLog == undefined || Object.keys(wmaApp.storage.pickingLog).length == 0) {
			wmaApp.storage.write('pickingLog', {});
		}

		var html = '';
		let data = wmaApp.storage.pickingLog;
		data = sortObj(data);
		for (let i in data) {
			if (data[i][0] == '<?=$batch_no?>') {
				html += data[i][4] + ' of ' + data[i][1] + '/' + data[i][2] + ' at ' + data[i][3] + '<br />';
			}
		}
		$('.picking-log').html(html);
	}

	function sortObj(obj) {
		var arr = [];
		for (var i in obj) {
			arr.push([obj[i], i]);
		};
		arr.reverse();
		var len = arr.length;
		var obj = {};
		for (var i = 0; i < len; i++) {
			obj[arr[i][1]] = arr[i][0];
		}
		return obj;
	}

	showLog();
});
</script>