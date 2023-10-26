<style type="text/css">
	.display_none {
		display: none;
	}
</style>
<h3 id="bidheader">Bidding Operation - <?= $objBidding->id ?></h3>
<div class="form">
	<div class="row rowcol-left">
		<?php
		$ref = $model->shipment->ref;
		echo CHtml::label('Shipment Ref : ' . $ref, 'Shipment Ref');
		?>
	</div>
	<br />
	<div class="row rowcol-left">
		<?php echo CHtml::label('Cost', 'Cost'); ?>
		<?php echo CHtml::numberField('CargoProcessBidding[op_cost]', @$objBidding->op_cost,['']) ?>
	</div>
	<div class="row rowcol-left display_none">
		<?php echo CHtml::textField('CargoProcessBidding[shipment_id]', @$model->shipment->id) ?>
		<?php echo CHtml::textField('CargoProcessBidding[cargo_process_id]', @$model->id) ?>
		<?php echo CHtml::textField('CargoProcessBidding[dpt_id]', @$model->dpt_id) ?>
		<?php echo CHtml::textField('CargoProcessBidding[id]', @$objBidding->id) ?>
	</div>
	<div class="row rowcol-left">
		<?php echo CHtml::label('Unloading Type', 'Unloading Type'); ?>
		<?php echo CHtml::dropDownList('CargoProcessBidding[address_type]', @$objBidding->address_type,CargoProcessBidding::$unloading_Types,[]) ?>
	</div>
	<div class="row rowcol-left">
		<?php echo CHtml::label('Address Type', 'Address Type'); ?>
		<?php echo CHtml::dropDownList('CargoProcessBidding[unload_type]', @$objBidding->unload_type,CargoProcessBidding::$address_Types,[]) ?>
	</div>
	<div class="row rowcol-left">
		<?php echo CHtml::label('Delivery Range', 'Delivery Range'); ?>
		<?php echo CHtml::dropDownList('CargoProcessBidding[delivery_range]', @$objBidding->delivery_range,CargoProcessBidding::$delivery_Ranges,[]) ?>
	</div>
	<div class="row rowcol-left">
		<?php echo CHtml::label('Delivery Period', 'Delivery Period'); ?>
		<?php echo "Delivery Date " . CHtml::textField('CargoProcessBidding[str_date]', @$objBidding->str_date, ['class' => 'date_input']) . " to " . CHtml::textField('CargoProcessBidding[end_date]', @$objBidding->end_date, ['class' => 'date_input']) ?>
	</div>
	<div class="row rowcol-left">
		<?php echo "Business Hour " . CHtml::textField('CargoProcessBidding[str_time]', @$objBidding->str_time, ['class' => 'time_input']) . " to " . CHtml::textField('CargoProcessBidding[end_time]', @$objBidding->end_time, ['class' => 'time_input']) ?>
	</div>
	<div class="row rowcol-left">
		<?php echo CHtml::label('Note', 'Note'); ?>
		<?php echo CHtml::textArea('CargoProcessBidding[note]', @$objBidding->note, ['style' => 'width:430px; height:200px;']) ?>
	</div>

	<div class="row buttons">
		<?php
		if ($objBidding->isNewRecord) {
			echo CHtml::submitButton('Save', array('class' => 'save'));
		} else {
			echo CHtml::submitButton('Update', array('class' => 'update'))." "; 
			echo CHtml::submitButton('Recreate', array('class' => 'recreate'));
		}
		?>
	</div>
	<?php if (!empty($cargoProcessJob)) { ?>
		<?php $this->renderPartial('cargoProcessjob_detail',array('model'=>$cargoProcessJob));?>
	<?php } else { ?>
		<div id="cargojob-detail<?= $model->id ?>" class="display_none">
		</div>
		<br />
		<div id="cargo-list-view<?= $model->id ?>" style="width:100%;">
			<?php
			$objBiddingDetail = new CargoProcessBiddingDetail('search');
			$objBiddingDetail->unsetAttributes();
			if (!empty($objBidding->id)) {
				$objBiddingDetail->bid_id = $objBidding->id;
			}
			$this->renderPartial('_bidding_detail', ['model' => $objBiddingDetail, 'cargoprocess' => $model]);
			?>
		</div>
	<?php } ?>

</div>
<script type="text/javascript">
	$(function() {
		var win = $('#jqmw_<?= $_GET["tabid"]; ?>');
		var tab = $('#<?= $_GET["tabid"]; ?>');
		var panel = $('#<?= $_GET["tabid"]; ?>').data('panel');

		<?php if ($model->mdata['need_invoice'] == 0) { ?>
			$("#ot_invoice").addClass("display_none");
		<?php } ?>

		$('.save', win).on('click', function() {
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
				listData.append('CargoProcessBidding[address_type]', $('#CargoProcessBidding_address_type').val());
				listData.append('CargoProcessBidding[unload_type]', $('#CargoProcessBidding_unload_type').val());
				listData.append('CargoProcessBidding[delivery_range]', $('#CargoProcessBidding_delivery_range').val());
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
			return false;
		});

		$('.update', win).on('click', function() {
			if (confirm('Are you sure to update bidding?')) {
				//const tr = $(this).parents('tr');
				var listData = new FormData();
				listData.append('CargoProcessBidding[id]', $('#CargoProcessBidding_id').val());
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
					url: '<?= $this->createUrl("cargoProcessBidding/ajaxUpdate") ?>',
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
			return false;
		});

		$('.recreate', win).on('click', function() {
			if (confirm('Are you sure to recreate bidding?')) {
				//const tr = $(this).parents('tr');
				var listData = new FormData();
				listData.append('CargoProcessBidding[id]', $('#CargoProcessBidding_id').val());
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
					url: '<?= $this->createUrl("cargoProcessBidding/ajaxRecreate") ?>',
					type: "post",
					data: listData,
					async: false,
					contentType: false,
					processData: false,
				});
				obj = JSON.parse(htmlobj.responseText);
				if (obj.isSuccess) {
					$('#bidheader',win).html("Bidding Operation - "+obj.newBiddingId);
					$('#CargoProcessBidding_id',win).val(obj.newBiddingId);
					myApp.notice('Successful', 5000);
				} else {
					myApp.notice('Error', 5000);
				}
			}
			return false;
		});

	});
</script>