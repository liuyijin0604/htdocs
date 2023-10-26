<style>
	.sub-couriers {
		display: none;
	}

	.pallet_number_form {
		display: none;
	}
</style>
<h1>Number of Pallets Input</h1>
<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
    echo '<a class="dash-item ajax-link" href="' . $this->createUrl('site/index', ['scan_warehouse' => 'sydney']) . '"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
    return;
}
?>
<div class="container" id="response_container" style="display: none">
	<p id="success_msg" style="background-color: greenyellow;"></p>
	<p id="unsuccess_msg" style="background-color: pink;"></p>
</div>

<div class="container" style="text-align: center;">
	<div class="form" style="width: 80%;">
		<form id="submit_awb_form">
			<div class="row">
				<input type="text" class="form-control" name="awb_search" id="awb_search" placeholder="AWB / Container Number" autocomplete="off" required />
			</div>
			<div class="row" style="text-align: left;">
				<input type="submit" value="Search" />
			</div>
		</form>
	</div>
	<br />
	<div class="form pallet_number_form" style="width: 80%;">
		<form id="update_pallet_number_form">
			<div class="sub-couriers" id="couriers_please_block">
				<div class="row" style="text-align: left;">
					<lable for="pallet_count_courier_plz">Courier please: </lable>
				</div>
				<div class="row">
					<input type="number" class="form-control" name="pallet_count_courier_plz" id="pallet_count_courier_plz" placeholder="Number of pallets" autocomplete="off" min="0" />
				</div>
				<div class="row">
					<input type="number" class="form-control" name="cage_count_courier_plz" id="cage_count_courier_plz" placeholder="Number of cages" autocomplete="off" min="0" />
				</div>
			</div>
			<div class="sub-couriers" id="fastway_block">
				<div class="row" style="text-align: left;">
					<lable for="pallet_count_fastway">FastWay: </lable>
				</div>
				<div class="row">
					<input type="number" class="form-control" name="pallet_count_fastway" id="pallet_count_fastway" placeholder="Number of pallets" autocomplete="off" min="0" />
				</div>
				<div class="row">
					<input type="number" class="form-control" name="cage_count_fastway" id="cage_count_fastway" placeholder="Number of cages" autocomplete="off" min="0" />
				</div>
			</div>
			<div class="total-count">
				<div class="row" style="text-align: left;">
					<label for="pallet_count_total">Total Count</label>
				</div>
				<div class="row">
					<input type="number" class="form-control" name="pallet_count_total" id="pallet_count_total" placeholder="Number of pallets" autocomplete="off" min="0" />
				</div>
				<div class="row">
					<input type="number" class="form-control" name="cage_count_total" id="cage_count_total" placeholder="Number of cages" autocomplete="off" min="0" />
				</div>
			</div>
			<div class="row">
				<input type="submit" value="Submit" />
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		$('input#awb').focus().on('keydown', function(e){
			if (e.which == 13) {
				e.preventDefault();
				$('input#pallet_count').focus();
			}
		});

		$('input#pallet_count').focus().on('keydown', function(e) {
			if (e.which == 13) {
				e.preventDefault();
				$('input#cage_count').focus();
			}
		});

		$('input#cage_count').focus().on('keydown', function(e) {
			if (e.which == 13) {
				e.preventDefault();
			}
		});

		function hideResponse()
		{
			$('#response_container').hide();
		}

		$('#update_pallet_number_form').submit(function(event) {
			event.preventDefault();
			event.stopImmediatePropagation();

			var formData = new FormData(this);
			formData.append('awb', $('#awb_search').val());


			$.ajax({
				type: 'POST',
				url: '<?= $this->createUrl('warehouseProcess/updatePalletCount'); ?>',
				data: formData,
				processData: false,
				contentType: false,
				success: function(res) {
					let response = JSON.parse(res);
					if (response.success == true) {
						$('input#awb').val('');
						$('input#pallet_count').val('');
						$('input#cage_count').val('');
						$('#success_msg').text(response.message);
						$('#unsuccess_msg').text('');
						$('#response_container').show();
						setTimeout(hideResponse, 5000);
					} else {
						$('input#awb').focus();
						$('#success_msg').text('');
						$('#unsuccess_msg').text(response.message);
						$('#response_container').show();
						setTimeout(hideResponse, 5000);
					}
				}
			})
		});

		$('#submit_awb_form').submit(function(e) {
			e.preventDefault();
			e.stopImmediatePropagation();
			var searchFormData = new FormData(this);

			$.ajax({
				type: 'POST',
				url: '<?= $this->createUrl('warehouseProcess/searchPalletCount'); ?>',
				data: searchFormData,
				processData: false,
				contentType: false,
				success: function(res) {
					let response  = JSON.parse(res);
					if (response.success == true) {
						//console.log(awb);
						$('.pallet_number_form').show();
						/* let strHtml = '<form id="update_pallet_number_form"><div class="row"><input type="text" class="form-control" name="awb" id="awb" value="'+response.awb+'" autocomplete="off" style="display: none;" disabled /></div><br />';
						for (let i = 0; i < response.couriers.length; i++) {
							strHtml += '<div class="row"><input type="text" class="form-control" value="'+response.couriers[i]+'" name="courier_'+i+'" id="courier_'+i+'" disabled /></div>';
							strHtml += '<div class="row"><input type="number" class="form-control" name="pallet_count_'+i+'" id="pallet_count_'+i+'" placeholder="Number of pallets" autocomplete="off" min="0" /></div>';
							strHtml += '<div class="row"><input type="number" class="form-control" name="cage_count_'+i+'" id="cage_count_'+i+'" placeholder="Number of cages" autocomplete="off" min="0" /></div><br />';
						}
						strHtml += '<div class="row"><input type="text" class="form-control" value="total" name="total_count" id="total_count" disabled /></div>';
						strHtml += '<div class="row"><input type="number" class="form-control" name="total_pallet_count" id="total_pallet_count" placeholder="Number of pallets" autocomplete="off" min="0" /></div>';
						strHtml += '<div class="row"><input type="number" class="form-control" name="total_cage_count" id="total_cage_count" placeholder="Number of cages" autocomplete="off" min="0" /></div><br />';
						strHtml += '<div class="row" style="text-align: left;"><input type="submit" /></div>';
						strHtml += '</form>';
						$(".pallet_number_form").append(strHtml); */
						if (response.couriers.includes('courier please')) {
							$('#couriers_please_block').show();
						} else {
							$('#couriers_please_block').hide();
						}
						if (response.couriers.includes('FastWay')) {
							$('#fastway_block').show();
						} else {
							$('#fastway_block').hide();
						}
					} else {

					}
				}
			})
		})
	})
</script>