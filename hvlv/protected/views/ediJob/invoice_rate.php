<h1>Invoice Rate</h1>
<br />
<h3>Air Freight</h3>
<div class="form" style="height: 600px">
	<?php $form = $this->beginWidget('CActiveForm', [
		'id' => 'invoice-rate-form',
		'enableAjaxValidation' => false,
	]); ?>

	<div class="grid-view">
		<table class="items">
			<thead>
				<tr>
					<th>Airline</th>
					<th>POL</th>
					<th>POD</th>
					<th>Range (From)</th>
					<th>Rate</th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr><td></td><td></td><td></td><td></td><td></td><td></td><td><a id="add"><div class="icon" style="background-position: -288px -672px; cursor: pointer"></div></a></td></tr>
			</tfoot>
		</table>
	</div>
	<br />
	<h3>Export Security Fee</h3>
	<div class="row">
		<?php echo CHtml::textField('esf', @$esf_rate); ?>
	</div>
	<br />
	<h3>X-ray Fee</h3>
	<div class="row">
		<?php echo CHtml::textField('xray', @$xray_rate); ?>
	</div>
	<br />

	<div class="button">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>

<script>
$(function() {
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	var rates = '<?=json_encode($rates);?>';
	rates = JSON.parse(rates);
	if (rates) {
		rates.map(function(v, k) {
			if (v['range'] == null) {
				return null;
			}
			if (v['range'].length > 1) {
				first_button = '<td><a class="del_line"><div class="icon" style="background-position: -304px -672px; cursor: pointer"></div></a></td>';
			} else {
				first_button = '<td><a class="add_line"><div class="icon" style="background-position: -288px -672px; cursor: pointer"></div></a></td>';
			}
			var html = '<tr style="background: ' + (k % 2 == 0 ? '#E5F1F4' : '#F8F8F8') + '" id="' + k + '"><td rowspan="' + v['range'].length + '"><input type="text" name="airline[]" value="' + (v['airline'] ? v['airline'] : '') + '" style="width: 100%" /></td><td rowspan="' + v['range'].length + '"><?=$pols?></td><td rowspan="' + v['range'].length + '"><?=$pods?></td><td><input type="text" name="range[' + k + '][]" value="' + v['range'][0] + '" style="width: 100%" /></td><td><input type="text" name="rate[' + k + '][]" value="' + v['rate'][0] + '" style="width: 100%" /></td>' + first_button + '<td rowspan="' + v['range'].length + '"><a class="del"><div class="icon" style="background-position: -304px -672px; cursor: pointer"></div></a></td></tr>';
			html = html.replace('value="' + v['pol'] + '"', 'value="' + v['pol'] + '" selected="selected"');
			html = html.replace('value="' + v['pod'] + '"', 'value="' + v['pod'] + '" selected="selected"');
			$('tbody', win).append(html);

			for (var i = 1; i < v['range'].length; i ++) {
				if (i == v['range'].length - 1) {
					last_button = '<td><a class="add_line"><div class="icon" style="background-position: -288px -672px; cursor: pointer"></div></a></td>';
				} else {
					last_button = '<td><a class="del_line"><div class="icon" style="background-position: -304px -672px; cursor: pointer"></div></a></td>';
				}
				html = '<tr style="background: ' + (k % 2 == 0 ? '#E5F1F4' : '#F8F8F8') + '" data-id="' + k + '"><td><input type="text" name="range[' + k + '][]" value="' + v['range'][i] + '" style="width: 100%" /></td><td><input type="text" name="rate[' + k + '][]" value="' + v['rate'][i] + '" style="width: 100%" /></td>' + last_button + '</tr>';
				$('tbody', win).append(html);
			}

			// add empty
			if (k != rates.length - 1) {
				$('tbody', win).append('<tr><td colspan="7">&nbsp;</td></tr>');
			}
		});
	}

	$('#add', win).on('click', function() {
		var last_line = $('tbody tr', win).last();
		var color = last_line.css('background-color');
		if (color == 'rgb(229, 241, 244)') {
			color = 'rgb(248, 248, 248)';
		} else if (color == 'rgb(248, 248, 248)') {
			color = 'rgb(229, 241, 244)';
		} else {
			color = 'rgb(229, 241, 244)';
		}

		var count = $('select', win).length / 2;

		// add empty
		if (count != 0) {
			$('tbody', win).append('<tr><td colspan="7">&nbsp;</td></tr>');
		}

		$('tbody', win).append('<tr style="background: ' + color + '" id="' + count + '"><td rowspan="1"><input type="text" name="airline[]" value="" style="width: 100%" /></td><td rowspan="1">' + '<?=$pols?></td><td rowspan="1">' + '<?=$pods?></td><td><input type="text" name="range[' + count + '][]" value="" style="width: 100%" /></td><td><input type="text" name="rate[' + count + '][]" value="" style="width: 100%" /></td><td><a class="add_line"><div class="icon" style="background-position: -288px -672px; cursor: pointer"></div></a></td><td rowspan="1"><a class="del"><div class="icon" style="background-position: -304px -672px; cursor: pointer"></div></a></td></tr>');
	});

	$(win).on('click', '.del', function() {
		var id = $(this).parent().parent().attr('id');
		// delete empty
		var last_child = $('tr[data-id="' + id + '"').last();
		if (last_child.length == 0) {
			last_child = $(this).parent().parent();
		}
		if (last_child.html() != $('tbody tr').last().html()) {
			last_child.next().remove();
		} else {
			$(this).parent().parent().prev().remove();
		}

		$(this).parent().parent().remove();
		$('tr[data-id="' + id + '"').remove();
	});

	$(win).on('click', '.add_line', function() {
		var line = $(this).parent().parent();
		line.find('td').each(function(k, v) {
			// replace add line button to del line button
			if ($(this).find('a[class="add_line"]').length) {
				$(this).html('<a class="del_line"><div class="icon" style="background-position: -304px -672px; cursor: pointer"></div></a>');
			}
		});
		if (line.attr('data-id')) {
			first = $('#' + line.data('id'));
		} else {
			first = line;
		}
		var color = first.css('background-color');
		first.find('td').each(function(k, v) {
			// increase rowspan
			if ($(this).attr('rowspan')) {
				$(this).attr('rowspan', parseInt($(this).attr('rowspan')) + 1);
			}
		});

		line.after('<tr style="background: ' + color + '" data-id="' + first.attr('id') + '"><td><input type="text" name="range[' + first.attr('id') + '][]" value="" style="width: 100%" /></td><td><input type="text" name="rate[' + first.attr('id') + '][]" value="" style="width: 100%" /></td><td><a class="add_line"><div class="icon" style="background-position: -288px -672px; cursor: pointer"></div></a></td></tr>');
	});

	$(win).on('click', '.del_line', function() {
		var line = $(this).parent().parent();
		if (line.attr('data-id')) {
			first = $('#' + line.data('id'));
		} else {
			first = line;
			line = first.next();
			first.find('td input[name*="range"]').val(line.find('td input[name*="range"]').val());
			first.find('td input[name*="rate"]').val(line.find('td input[name*="rate"]').val());
		}
		first.find('td').each(function(k, v) {
			// increase rowspan
			if ($(this).attr('rowspan')) {
				$(this).attr('rowspan', parseInt($(this).attr('rowspan')) - 1);
			}
		});
		line.remove();

		if (win.find('tr[data-id="' + first.attr('id') + '"]').length == 0) {
			// replace del line button to add line button
			first.find('a[class="del_line"]').parent().html('<a class="add_line"><div class="icon" style="background-position: -288px -672px; cursor: pointer"></div></a>');
		}
	});
});
</script>