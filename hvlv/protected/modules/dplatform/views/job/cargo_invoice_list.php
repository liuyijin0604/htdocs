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
<h1><?= strtoupper($this->t('Deliveryed Cargo List')); ?></h1>

<div> <?php echo CHtml::button('Export Current Search', array(
			"id" => 'getCargoprocessInvoice', "class" => "btn btn-primary btn-sm"
		)); ?>
</div>
</br>
<div class="uploading"><img src="https://www.pcaexpress.com.au/client/css/images/ajaxLoader.gif" width="24" /> Loading</div>
<div id="export_cargo_cost_detail" style="margin: 10px 0; border: 1px solid;padding:20px;display: none;"></div>
<?php

$columns = array(
	['header' => 'Job Date', 'value' => '@$data->getCargoProcessDeliveryDate()', 'filter' => CHtml::dropDownList('week', $model->week, CargoProcessJobRelations::model()->getAllCargoByWeek(), ['style' => 'height:30px; width:170px;']), 'htmlOptions' => ['style' => 'width:170px;text-align: center;']],
	['header' => 'Job Name', 'value' => '@$data->job->job_name', 'htmlOptions' => ['style' => 'width:100px;text-align: center;']],
	['header' => 'Connote', 'value' => '@$data->cargo_process->shipment->hbn', 'htmlOptions' => ['style' => 'width:200px;text-align: center;']],
	['header' => 'Ref', 'value' => '@$data->cargo_process->shipment->ref', 'htmlOptions' => ['style' => 'width:200px;text-align: center;']],
	['header' => 'Weight', 'value' => '@$data->cargo_process->getWeight(true).\'kg\'', 'type' => 'raw', 'htmlOptions' => ['style' => 'width:80px;text-align: center;']],
	['header' => 'Pkg', 'value' => '@$data->cargo_process->shipment->pkg', 'htmlOptions' => ['style' => 'width:80px;text-align: center;']],
	['header' => 'Effective Delivery', 'value' => '@$data->isGoodDelivery()', 'htmlOptions' => ['style' => 'width:120px;text-align: center;']],
	['header' => 'ReDelivery', 'value' => '@$data->isRelivery()', 'htmlOptions' => ['style' => 'width:90px;text-align: center;']],
	['header' => 'Fee', 'value' => '@$data->getCargoCost()', 'htmlOptions' => ['style' => 'width:80px;text-align: center;']],
);
// $columns[]= [
//      		'header'=>'Operation',
//      		'class'=>'oButtonColumn',
// 			'template'=>'{jobDetails}&nbsp;{sign}',
// 			'buttons'=>[
// 				'jobDetails' => [
// 					'url'=>' Yii::app()->createURL("dplatform/job/jobDetails",["id"=>$data->id])',
// 					'imageUrl'=>false,
// 					'visible'=>'true',
// 					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Job Details'), 'title' => '$data->id'],
// 				],
// 				'sign' => [
// 					'url'=>' Yii::app()->createURL("dplatform/job/signJobPage",["id"=>$data->id])',
// 					'imageUrl'=>false,
// 					'visible'=>'$data->isFBAJob()',
// 					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Job Details'), 'title' => '$data->id'],
// 				]
// 			],
// 		];



$this->widget(
	'application.extensions.booster.TbExtendedGridView',
	array(
		'fixedHeader' => true,
		'id' => 'job_grid_view',
		'filter' => $model,
		'type' => 'striped bordered',
		'headerOffset' => 40,
		'responsiveTable' => true,
		'dataProvider' => $model->search(true, 100),
		'template' => "{summary}\n{items}\n{pager}",
		'afterAjaxUpdate' => 'function(){initButtons();}',
		'columns' => $columns,
	),

); ?>
<script type="text/javascript">
	var tab = $('#<?= $_GET["tabid"]; ?>');
	var panel = tab.data('panel');

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
	$(function() {
		$('#getCargoprocessInvoice').click(function(e) {
			e.preventDefault();
			e.stopPropagation();
			var listData = new FormData();
			var weekValue = $("#week").find("option:checked").val();
			listData.append('week', weekValue);

			$('.uploading', panel).fadeIn();
			$('#export_cargo_cost_detail', panel).hide();
			$('#export_cargo_cost_detail', panel).html('');
			$.ajax({
				type: "POST",
				url: "<?= $this->createUrl('job/exportInvoice'); ?>",
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

		initButtons();
		// tab.bind('onOpen', function(){
		// 	$('#job-grid', panel).yiiGridView('update');
		// });
	});
</script>