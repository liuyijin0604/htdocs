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
		<!-- <div style="background-position:-16px 0" class="icon"></div> Batch Export -->
	</a>
	<div id="<?= $_GET["tabid"]; ?>-dropdown-2" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">

	</div>
</div>
<h1>Cargo Bidding List</h1>

<!-- <p>
<?= $this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.'); ?></p> -->

<?php echo CHtml::link($this->t('Advanced Search'), '#', ['class' => 'search-button']); ?>
<div class="search-form" style="display:none">
	<?php $this->renderPartial('_search_filter', [
		'model' => $model,
	]);
	?>
</div>
<div class="uploading"><img src="https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif" width="24" /> Loading</div>
<div id="export_cargo_cost_detail" style="margin: 10px 0; border: 1px solid;padding:20px;display: none;"></div>
<div style="text-align:center;">
	<?php

	$columns = [
		['name'=>'id','type' => 'raw', 'value' => '$data->id'],
		//['name' => 'shipment_id', 'type' => 'raw', 'value' => '$data->shipment->ref', 'htmlOptions' => array('style' => 'width: 120px')],
		//['header' => 'Ref', 'type' => 'raw', 'value' => '@$data->shipment->ref'],
		['header' => 'Pick Up', 'value' => '@$data->getFromString()'],
		['header' => 'Delivery To', 'value' => '@$data->getToString()'],
		//['header' => 'Cargo Type', 'value' => '@$data->getCargoType()'],
		'str_date',
		'end_date',
		//['header' => 'Date Period', 'value' => '@$data->getBiddingDate()'],
		['header' => 'Business Hour', 'value' => '@$data->getReceivbleTime()'],
		['header' => 'CBM(M³)', 'value' => '@$data->cargo_process->getTotalCBM()'],
		['header' => 'Weight(Kg)', 'value' => '@$data->cargo_process->getWeight()'],
		['header' => 'Forklift', 'value' => '@$data->getBiddingForlift()'],
		//'op_cost',
		['header' => 'Reference Price', 'value' => '@$data->getReservePrice()'],
		['header'=>'My Quote','value'=>'@$data->getMyCost('.User::getCurrentUser()->org_id.')'],

		[
			'class' => 'oButtonColumn',
			'template' => '{Biddings}',
			'buttons' => [
				'Biddings' => [
					'imageUrl' => false, //jqm_link grid_view_btn
					'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Biddings', 'data-win-class' => 'L', 'title' => '@$data->getBiddingTabName()'],
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("dplatform/job/getBiddingDetail", ["id" => $data->id])',
					//'label' => 'Biddings'
				],
			],
		]
	];


	$this->widget(
		'application.extensions.booster.TbExtendedGridView',
		array(
			'fixedHeader' => true,
			'id' => 'bidding_grid_view',
			//'filter' => $model,
			'type' => 'striped bordered',
			'headerOffset' => 40,
			'responsiveTable' => true,
			'dataProvider' => $model->search(true, 30, false, true),
			'template' => "{summary}\n{items}\n{pager}",
			'afterAjaxUpdate' => 'function(){initButtons();}',
			'columns' => $columns,
		),
	);
	?>
</div>
<script type="text/javascript">
	$(function() {
		var tab = $('#<?= $_GET["tabid"]; ?>');
		var panel = tab.data('panel');


		var resetFilters = function() {
			$('.search-form form', panel).trigger('reset');
			$('#bidding_grid_view', panel).yiiGridView('update', {
				data: 'CoParcel=reset'
			});
		};

		$('.search-button', panel).click(function() {
			$('.search-form', panel).toggle();
			return false;
		});

		$('.search-form form', panel).on('submit', function() {
			$('#bidding_grid_view', panel).yiiGridView('update', {
				data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()
			});
			return false;
		}).find('.reset_btn').click(resetFilters);

		tab.bind('onOpen', function() {
			$('#bidding_grid_view', panel).yiiGridView('update');
		});

		tab.on('gridUpdated', function() {
			$('tr.filters td:last-child', panel).empty().append($('<input type="button" id="reset_filter" value="Reset" />').on('click', resetFilters));
		}).trigger('gridUpdated');
	});
</script>