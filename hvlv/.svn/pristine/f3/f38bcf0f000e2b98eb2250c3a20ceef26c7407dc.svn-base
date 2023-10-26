<style type="text/css">
	.display_none {
		display: none;
	}
</style>
<h3>Bidding Operation-<?= $model->id ?></h3>
<div class="form">
	<div class="row rowcol-left">
		<?php
		$ref = $model->shipment->ref;
		echo CHtml::label('Shipment Ref : ' . $ref, 'Shipment Ref');
		?>
	</div>
	<br />
	<div class="row rowcol-left display_none">
		<?php echo CHtml::textField('CargoProcessBidding[shipment_id]', @$model->shipment->id) ?>
		<?php echo CHtml::textField('CargoProcessBidding[cargo_process_id]', @$model->id) ?>
		<?php echo CHtml::textField('CargoProcessBidding[dpt_id]', @$model->dpt_id) ?>
	</div>
	<div class="row rowcol-left">
		<?php echo CHtml::label('Delivery Period', 'Delivery Period'); ?>
		<?php echo "Delivery Date " . CHtml::textField('CargoProcessBidding[str_date]', @$objBidding->str_date, ['class' => 'date_input']) . " to " . CHtml::textField('CargoProcessBidding[end_date]', @$objBidding->end_date, ['class' => 'date_input']) ?>
	</div>
	<div class="row rowcol-left">
		<?php echo "Business Hour " . CHtml::textField('CargoProcessBidding[str_time]', @$objBidding->str_time, ['class' => 'time_input']) . " to " . CHtml::textField('CargoProcessBidding[end_time]', @$objBidding->end_time, ['class' => 'time_input']) ?>
	</div>
	<div class="row rowcol-left">
		<?php echo CHtml::label('Cost', 'Cost'); ?>
		<?php echo CHtml::numberField('CargoProcessBidding[op_cost]', @$objBidding->op_cost) ?>
	</div>
	<div class="row rowcol-left">
		<?php echo CHtml::label('Note', 'Note'); ?>
		<?php echo CHtml::textArea('CargoProcessBidding[note]', @$objBidding->note, ['style' => 'width:430px; height:200px;']) ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Save', array('class' => 'update')); ?>
	</div>
</div>
<script type="text/javascript">
	$(function() {
		var win = $('#jqmw_<?= $_GET["tabid"]; ?>');
		var tab = $('#<?= $_GET["tabid"]; ?>');
		var panel = $('#<?= $_GET["tabid"]; ?>').data('panel');

		$('.update', win).on('click', function() {
			if (confirm('Are you sure to add into bidding list?')) {
				//const tr = $(this).parents('tr');
				var listData = new FormData();
				listData.append('CargoProcessBidding[shipment_id]', $('#CargoProcessBidding_shipment_id').val());
				listData.append('CargoProcessBidding[cargo_process_id]', $('#CargoProcessBidding_cargo_process_id').val());
				listData.append('CargoProcessBidding[dpt_id]', $('#CargoProcessBidding_dpt_id').val());
				listData.append('CargoProcessBidding[str_date]', $('#CargoProcessBidding_str_date').val());
				listData.append('CargoProcessBidding[end_date]', $('#CargoProcessBidding_end_date').val());
				listData.append('CargoProcessBidding[str_time]', $('#CargoProcessBidding_str_time').val());
				listData.append('CargoProcessBidding[end_time]', $('#CargoProcessBidding_end_time').val());
				listData.append('CargoProcessBidding[op_cost]', $('#CargoProcessBidding_op_cost').val());
				listData.append('CargoProcessBidding[note]', $('#CargoProcessBidding_note').val());
				htmlobj = $.ajax({
					url: '<?= $this->createUrl("cargoProcessBidding/ajaxCreate") ?>',
					type: "post",
					data: listData,
					async: false,
					contentType: false,
					processData: false,
				});
				obj = JSON.parse(htmlobj.responseText);
				if (obj.isSuccess) {
					myApp.notice('Successful', 5000);
				} else {
					myApp.notice('Error', 5000);
				}
			}
		});

	});
</script>