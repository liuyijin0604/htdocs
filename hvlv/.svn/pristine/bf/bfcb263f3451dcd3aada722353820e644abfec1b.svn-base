<h1><?= $this->t('Create Invoice'); ?></h1>

<?php echo $this->renderPartial('_invoice_form', array('model' => $model,'cargoplan'=>$cargoplan)); ?>

<script type="text/javascript">
	$(function() {
		var amount = 1;
		var qty = 1;
		var arrName = ['InvLine[ccode]', 'InvLine[det]', 'InvLine[amount]', 'InvLine[qty]', 'InvLine[tax]'];
		var win = $('#jqmw_<?= $_GET["tabid"]; ?>');
		$('#invline-grid tfoot select').on('change', function() {
			var v = $(this).val();
			$('option', this).each(function() {
				$(this).attr('selected', $(this).attr('value') == v);
			});
		});
		var j =0;
		$('#invline-grid .add_btn').on('click', function() {
			for (var i in arrName) {
				$("[name='" + arrName[i] + "']").show();
			}
			$('#invline-grid .items tbody td.empty', win).parent().remove();
			var r = $(this).parents('tr').clone();
			$('.add_btn', r).remove();
			$('input, select', r).each(function() {
				var n = $(this).attr('name');
				$(this).attr('name', n + '['+j+']');
			});
			$('#invline-grid .items tbody').append(r);
			$(this).parents('tr').find('select option').attr('selected', false);
			$(this).parents('tr').find('input, select').val('');

			for (var i in arrName) {
				$("[name='" + arrName[i] + "']").hide();
			}
			
			if(j==0){
				$("input[name='InvLine[ccode][0]']").val('#');
				$("input[name='InvLine[det][0]").val('Delivery Fee');
				$("input[name='InvLine[amount][0]']").val(amount);
				$("input[name='InvLine[qty][0]").val(qty);
			}
			j++;
			return false;
		});

		for (var i in arrName) {
			$("[name='" + arrName[i] + "']").hide();
		}
	});
</script>