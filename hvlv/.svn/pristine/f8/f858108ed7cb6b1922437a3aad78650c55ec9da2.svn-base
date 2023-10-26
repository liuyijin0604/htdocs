<style type="text/css">
	.display_none {
		display: none;
	}
</style>
<h3>Bidding Detail</h3>
<?php

$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id' => $_GET["tabid"] . '_cargo_process_bidding_grid',
	'cssFile' => false,
	'dataProvider' => $model->search(true, 50, false, true),
	'filter' => $model,
	'columns' => [
		['header' => 'Driver Name', 'value' => '@$data->driver->name'],
		'choosedate',
		'cost',
		'note',
		["header" => "Action", 'type' => 'raw', "value" => 'CHtml::ajaxLink("Accept","",[],["class"=>"bidding_assign grid_edit_btn","style"=>"width:90px;","id"=>"$data->id"])', "filter" => false],
	],
]);
?>
<script type="text/javascript">
	var win = $('#jqmw_<?= $_GET["tabid"]; ?>');
	var tab = $('#<?= $_GET["tabid"]; ?>');
	var panel = $('#<?= $_GET["tabid"]; ?>').data('panel');

	$(function() {
		$('body').off('change', '.bidding_assign',panel).on('click', '.bidding_assign', function() {
			const tr = $(this).parents('tr');
			var listData = new FormData();
			listData.append('id', $('.bidding_assign', tr).attr('id'));
			htmlobj = $.ajax({
				url: '<?= $this->createUrl("cargoProcessBiddingDetail/ajaxAcceptDetail") ?>',
				type: "post",
				data: listData,
				async: false,
				contentType: false,
				processData: false,
			});
			obj = JSON.parse(htmlobj.responseText);
			if (obj.isSuccess) {
				myApp.notice('Accepted', 5000);
				$("#cargo-list-view<?= $cargoprocess->id ?>").addClass("display_none");

				var listCargoProcessData = new FormData();
				listCargoProcessData.append('id', $('.bidding_assign', tr).attr('id'));
				htmlobj = $.ajax({
					url: '<?= $this->createUrl("cargoProcessBiddingDetail/ajaxGetCargoProcessJobDetail") ?>',
					type: "post",
					data: listData,
					async: false,
					contentType: false,
					processData: false,
					dataType: "html",
					success: function(html){
						$("#cargojob-detail<?=$cargoprocess->id?>").html(html);
						$("#cargojob-detail<?=$cargoprocess->id?>").removeClass("display_none");
					}
				});

			} else {
				myApp.notice('error', 5000);
			}
			return false;
		});
		// $('#egw0').after('<div class="pull-right"><button type="button" data-toggle="modal" data-target="#modal-export" class="btn btn-default btn-sm">Export</button></div>');
	});
</script>