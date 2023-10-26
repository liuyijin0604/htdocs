<h1><?= strtoupper($this->t('Cargo Job List')); ?></h1>
<?php echo CHtml::link($this->t('Advanced Search'), '#', ['class' => 'search-button']); ?>
<div class="search-form" style="display:none">
	<?php $this->renderPartial('_search_job', [
		'model' => $model,
	]);
	?>
</div>
<?php
$columns = array(
	//'pickup',
	['header' => 'Job Date', 'value' => 'date("Y-m-d",strtotime($data->created))', 'filter' => CHtml::dropDownList('CargoProcessJob[created]', $model->created, $this->t((Yii::app()->user->id == 3595) ? CargoProcessJob::model()->getCjobDateFromNowForTony() : CargoProcessJob::model()->getThreeCjobDateFromNow()), ['style' => 'width:90px;height:33px;'])],
	// ['name' => 'job_no'],
	['name' => 'job_name', 'htmlOptions' => array('style' => 'width: 100px')],
	['header' => 'GatePass', 'value' => '($data->gate_pass_id==0)?"":$data->gate_pass_id'],
	['header' => 'Type', 'value' => '$data->getJobType()'],
	['header' => 'Job Time', 'value' => 'CargoProcessJob::$cargoTimes[empty($data->mdata["time"])?30:$data->mdata["time"]]', 'filter' => CHtml::dropDownList('CargoProcessJob[time]', $model->mdata['time'], $this->t(CargoProcessJob::$cargoTimes), ['prompt' => $this->t('All'), 'style' => 'width:90px;height:33px;'])],
	['header' => 'Job Status', 'value' => 'CargoProcessJob::$processTypes_driver_user[$data->status]', 'filter' => CHtml::dropDownList('CargoProcessJob[status]', $model->status, $this->t(CargoProcessJob::$processTypes_driver_user), ['prompt' => $this->t('All'), 'style' => 'width:90px;height:33px;']), 'htmlOptions' => array('style' => 'width: 80px')],
	array('header' => 'pickup', 'type' => 'raw', 'value' => '$data->getPickupAddress(true)', 'htmlOptions' => array('style' => 'width: 150px')),
	array('name' => 'description', 'type' => 'raw', 'value' => '$data->description'),
	array('header' => 'Total[Plt]', 'value' => '$data->getTotalPltNo()', 'filter' => false),
	array('name' => 'vehicle_id', 'value' => '@$data->vehicle->vehicle_name', 'htmlOptions' => array('style' => 'width: 100px')),
);
$columns[] = [
	'header' => 'Operation',
	'class' => 'oButtonColumn',
	'template' => '{jobDetails}&nbsp;{sign}',
	'buttons' => [
		'jobDetails' => [
			'url' => ' Yii::app()->createURL("dplatform/job/jobDetails",["id"=>$data->id])',
			'imageUrl' => false,
			'visible' => 'true',
			'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Job Details'), 'title' => '$data->id'],
		],
		'sign' => [
			'url' => ' Yii::app()->createURL("dplatform/job/signJobPage",["id"=>$data->id])',
			'imageUrl' => false,
			'visible' => '$data->isFBAJob()',
			'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Job Details'), 'title' => '$data->id'],
		]
	],
];



$this->widget(
	'application.extensions.booster.TbExtendedGridView',
	array(
		'fixedHeader' => true,
		'id' => 'job_grid_view',
		'filter' => $model,
		'headerOffset' => 40,
		'responsiveTable' => true,
		'dataProvider' => $model->search(true, 30, false, true, $model->dpt_id, $model->type),
		'template' => "{summary}\n{items}\n{pager}",
		'afterAjaxUpdate' => 'function(){initButtons();}',
		'columns' => $columns,
	),

); ?>
<script type="text/javascript">
	$(function() {
		var tab = $('#<?= $_GET["tabid"]; ?>');
		var panel = tab.data('panel');


		var resetFilters = function() {
			$('.search-form form', panel).trigger('reset');
			$('#job_grid_view', panel).yiiGridView('update', {
				data: 'CoParcel=reset'
			});
		};

		$('.search-button', panel).click(function() {
			$('.search-form', panel).toggle();
			return false;
		});

		$('.search-form form', panel).on('submit', function() {
			$('#job_grid_view', panel).yiiGridView('update', {
				data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()
			});
			return false;
		}).find('.reset_btn').click(resetFilters);

		tab.bind('onOpen', function() {
			$('#job_grid_view', panel).yiiGridView('update');
		});

		tab.on('gridUpdated', function() {
			$('tr.filters td:last-child', panel).empty().append($('<input type="button" id="reset_filter" value="Reset" />').on('click', resetFilters));
		}).trigger('gridUpdated');
	});

	function initButtons() {
		$('.accept').click(function() {
			if (confirm('Are you sure to accept this job?')) {
				let thisUrl = '<?php echo $this->createURL("job/accept"); ?>' + '?id=' + $(this).attr('title');
				$.ajax({
					type: 'GET',
					url: thisUrl,
					data: [],
					dataType: 'json',
					success: function(resp) {
						if (resp.done != true) {
							alert("Job is accepted");
						}
						$('#job_grid_view').yiiGridView('update');
					},
				});
				return false;
			}
			return false;
		});
	}
</script>