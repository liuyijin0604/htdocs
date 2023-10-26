<h1><?= $this->t('Create Invoice'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>

<script type="text/javascript">
	$(function() {
		var arrName = ['InvLine[ccode]', 'InvLine[det]', 'InvLine[amount]', 'InvLine[qty]', 'InvLine[tax]', 'InvLine[rebate]','InvLine[linkto]'];
		var win = $('#jqmw_<?= $_GET["tabid"]; ?>');
		$('#invline-grid tfoot select', win).on('change', function() {
			var v = $(this).val();
			$('option', this).each(function() {
				$(this).attr('selected', $(this).attr('value') == v);
			});
		});
		$('#invline-grid .add_btn', win).on('click', function() {
			for (var i in arrName) {
				$("[name='" + arrName[i] + "']").show();
			}

			$('#invline-grid .items tbody td.empty', win).parent().remove();
			var r = $(this).parents('tr').clone();
			$('.add_btn', r).remove();
			$('input, select', r).each(function() {
				var n = $(this).attr('name');
				$(this).attr('name', n + '[]');
			});
			$('#invline-grid .items tbody').append(r);
			$(this).parents('tr').find('select option').attr('selected', false);
			$(this).parents('tr').find('input, select').val('');

			for (var i in arrName) {
				$("[name='" + arrName[i] + "']").hide();
			}
			//$("input[name='InvLine[linkto][]']").val('PS:T1231231;SKP8888888888');
			return false;
		});

		for (var i in arrName) {
			$("[name='" + arrName[i] + "']").hide();
		}
	});
</script>