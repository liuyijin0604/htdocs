<h3>
	<?php
	$no = explode('-', $batch_no);
	if ($no[2] == 1) {
		$no[2] = 'Single';
	} else if ($no[2] == 2) {
		$no[2] = 'Multi';
	}
	$no[1] = sprintf('%02d', $no[1]);
	$no = implode(' - ', $no);

	$batch = WmsBatch::model()->find('no = :no', [':no' => $no]);
	if (!empty($batch)) {
		$batch->calRemain();
	}

	echo $no . (isset($batch->mdata['sort_remain']) ? ' Remain: <span style="color: red" id="remain">' . $batch->mdata['sort_remain'] . '</span>' : '');
	?>
</h3>
<div class="sorting-entry-form">
<form action="<?=$this->createUrl('job/batchSortingEntry')?>" method="post" data-bit="1">
	<input class="prod barcode required" type="search" placeholder="Product Barcode" name="ean" />
	<input type="hidden" name="batch_no" value="<?=$batch_no?>" />
	<button type="submit" class="btn btn-primary btn-block">Submit</button>
</form>
</div>
<div class="res-entry">
</div>
<div class="sorting-log">
</div>

<script type="text/javascript">
$(function(){
	$('input.prod').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function() {
		$('.sorting-entry-form form').submit();
	});

	$('.sorting-entry-form form').on('success', function(e, r) {
		$('input.prod').val('');
		$('input.prod').focus();
		$('.res-entry').html('<h1 style="font-size: 30px; color: green">' + r.msg + '</h1>');
		$('#remain').val(parseInt($('#remain').val()) - 1);

		var audio = new Audio();
		audio.src = 'https://os.toplogistics.com.au/site/voice/box_' + r.box_number + '.mp3';
		audio.play();

		if (wmaApp.storage.sortingLog == undefined || Object.keys(wmaApp.storage.sortingLog).length == 0) {
			wmaApp.storage.write('sortingLog', {});
		}

		var uuid = wmaApp.uuid();
		wmaApp.storage.sortingLog.write(uuid, ['<?=$batch_no?>', r.prod_name, r.prod_ean, r.box_number, r.shelf_number]);

		showLog();

		setTimeout(function() {
			$('.res-entry').html('');
		}, 10e3);
	}).on('error', function(e, r) {
		$('input.prod').val('');
		$('input.prod').focus();
		$('.res-entry').html('<h1 style="font-size: 30px; color: red">' + r.msg + '</h1>');

		var audio = new Audio();
		audio.src = 'https://os.toplogistics.com.au/site/voice/not_found.mp3';
		audio.play();

		setTimeout(function() {
			$('.res-entry').html('');
		}, 10e3);
	});

	function showLog() {
		if (wmaApp.storage.sortingLog == undefined || Object.keys(wmaApp.storage.sortingLog).length == 0) {
			wmaApp.storage.write('sortingLog', {});
		}

		var html = '';
		let data = wmaApp.storage.sortingLog;
		data = sortObj(data);
		for (let i in data) {
			if (data[i][0] == '<?=$batch_no?>') {
				html += data[i][1] + ' ' + data[i][2] + ' box:' + data[i][3] + ' shelf:' + data[i][4] + '<br />';
			}
		}
		$('.sorting-log').html(html);
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
