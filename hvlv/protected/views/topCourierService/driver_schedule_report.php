<style type="text/css">
	.cargoNormal{
		text-align: center;
		vertical-align: top;
		width: 14.2%;
	}
</style>
<style>
	.uploading {
		position: relative;
		clear: both;
		width: 150px;
		font-size: 1.4em;
		font-weight: bold;
		color: #BC3426;
		line-height: 32px;
		z-index: 99;
		padding: 15px 5px;
		margin-bottom: -20px;
		display: none;
	}
</style>
<div style="right: 20px;position: absolute;">
	<a href="#" data-dropdown="#<?= $_GET["tabid"]; ?>-dropdown-2">
	</a>
	<div id="<?= $_GET["tabid"]; ?>-dropdown-2" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	</div>
</div>
<h1>Driver Schedule</h1>

<!-- <p>
<?= $this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.'); ?></p> -->

<?php echo CHtml::link($this->t('Advanced Search'), '#', ['class' => 'search-button']); ?>
<div class="search-form" style="display:none">
	<?php $this->renderPartial('_search_driver_schedule', [
		'model' => $model,
	]);
	?>
</div>
<div class="uploading"><img src="https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif" width="24" /> Loading</div>
<div id="export_cargo_cost_detail" style="margin: 10px 0; border: 1px solid;padding:20px;display: none;"></div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'im-parcel-grid' . @$modelType,
	'cssFile' => false,
	'dataProvider' => $provide,
	'summaryText' => '',
	'enablePagination' => true,
	'pager' => array(
		'prevPageLabel' => 'Prev.',
		'maxButtonCount' => 5,
	),
	'columns' => array(
		array('header' => 'Monday', 'type' => 'raw', 'value' => '$data["monday"]', 'htmlOptions' => array('class' => 'cargoNormal')),
		array('header' => 'Tuesday', 'type' => 'raw', 'value' => '$data["tuesday"]', 'htmlOptions' => array('class' => 'cargoNormal')),
		array('header' => 'Wednesday', 'type' => 'raw', 'value' => '$data["wednesday"]', 'htmlOptions' => array('class' => 'cargoNormal')),
		array('header' => 'Thursday', 'type' => 'raw', 'value' => '$data["thursday"]', 'htmlOptions' => array('class' => 'cargoNormal')),
		array('header' => 'Friday', 'type' => 'raw', 'value' => '$data["friday"]', 'htmlOptions' => array('class' => 'cargoNormal')),
		array('header' => 'Saturday', 'type' => 'raw', 'value' => '$data["saturday"]', 'htmlOptions' => array('class' => 'cargoNormal')),
		array('header' => 'Sunday', 'type' => 'raw', 'value' => '$data["sunday"]', 'htmlOptions' => array('class' => 'cargoNormal')),
	),
));
?>


<script type="text/javascript">
	$(function() {
		var tab = $('#<?= $_GET["tabid"]; ?>');
		var panel = tab.data('panel');


		var resetFilters = function() {
			$('.search-form form', panel).trigger('reset');
			$('#im-parcel-grid<?= @$modelType ?>', panel).yiiGridView('update', {
				data: 'CoParcel=reset'
			});
		};

		$('.search-button', panel).click(function() {
			$('.search-form', panel).toggle();
			return false;
		});

		$('.search-form form', panel).on('submit', function() {
			$('#im-parcel-grid<?= @$modelType ?>', panel).yiiGridView('update', {
				data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()
			});
			return false;
		}).find('.reset_btn').click(resetFilters);

		tab.bind('onOpen', function() {
			$('#im-parcel-grid<?= @$modelType ?>', panel).yiiGridView('update');
		});

		tab.on('gridUpdated', function() {
			$('tr.filters td:last-child', panel).empty().append($('<input type="button" id="reset_filter" value="Reset" />').on('click', resetFilters));
		}).trigger('gridUpdated');



		$('#ecl', panel).on('mousedown', function() {
			var q = $('.filters input, .filters select', panel).serialize() + '&' + $('.search-form form', panel).serialize();
			$(this).attr('href', $(this).attr('href') + '&' + q);
		});
		$('#epl', panel).on('mousedown', function() {
			var q = $('.filters input, .filters select', panel).serialize() + '&' + $('.search-form form', panel).serialize();
			$(this).attr('href', $(this).attr('href') + '&' + q);
		});
		$('#edd', panel).on('mousedown', function() {
			var q = $('.filters input, .filters select', panel).serialize() + '&' + $('.search-form form', panel).serialize();
			$(this).attr('href', $(this).attr('href') + '&' + q);
		});
		$('#eso', panel).on('mousedown', function() {
			var q = $('.filters input, .filters select', panel).serialize() + '&' + $('.search-form form', panel).serialize();
			$(this).attr('href', $(this).attr('href') + '&' + q);
		});
		$('#amio', panel).on('mousedown', function() {
			var q = $('.filters input, .filters select', panel).serialize() + '&' + $('.search-form form', panel).serialize();
			$(this).attr('href', $(this).attr('href') + '&' + q);
		});
		var dfeHref = $('#dfe').attr('href');
		$('#dfe', panel).on('mousedown', function() {
			var q = $('.filters input, .filters select', panel).serialize() + '&' + $('.search-form form', panel).serialize();
			$(this).attr('href', dfeHref + '&' + q);
		});

		// $('input[name="yt0"]', panel).click(function() {
		// 	if ($('input[name="CargoProcess[ref]"]', panel).length) {
		// 		$('input[name="CargoProcess[ref]"]', panel).val("");
		// 	}
		// 	if ($('input[name="CargoProcess[hbn]"]', panel).length) {
		// 		$('input[name="CargoProcess[hbn]"]', panel).val("");
		// 	}
		// });

		$('.pane', panel).on('focus', 'input[name="CargoProcess[ref]"]', function() {
			if ($('input[name="CargoProcess[ref]"]', panel).val() == "searching") {
				$('input[name="CargoProcess[ref]"]', panel).val("");
			}
		});

		$('#export_button').click(function(e) {
			e.preventDefault();
			e.stopPropagation();
			var listData = new FormData();
			var listName2Value = $('#yw0', panel).serializeArray();
			for (var i = 0; i < listName2Value.length; i++) {
				var objName2Value = listName2Value[i];
				listData.append(objName2Value.name, objName2Value.value);
			}
			$('.uploading', panel).fadeIn();
			$('#export_cargo_cost_detail', panel).hide();
			$('#export_cargo_cost_detail', panel).html('');
			$.ajax({
				type: "POST",
				url: "<?= $this->createUrl('report/cargoProcessReportExport'); ?>",
				data: listData,
				dataType: 'html',
				// async: false,
				contentType: false,
				processData: false,
				success: function(resp) {
					$('#export_cargo_cost_detail', panel).show();
					$('#export_cargo_cost_detail', panel).html(resp);
					$('.uploading', panel).fadeOut();
				}
			});
			//obj = JSON.parse(htmlobj.responseText);
		});

		// var dfeCostHref = $('#dfeCost').attr('href');
		// $('#dfeCost', panel).on('mousedown', function() {
		// 	var q = $('.filters input, .filters select', panel).serialize() + '&' + $('.search-form form', panel).serialize();
		// 	$(this).attr('href', dfeCostHref + '&' + q);
		// });

	});
</script>