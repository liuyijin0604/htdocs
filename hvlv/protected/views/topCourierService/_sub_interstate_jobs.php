<style>
	.cloumn_red {
		background-color: pink;
	}

	.column_direct {
		color: green;
		font-weight: bold;
	}
</style>

<?php
	$job->interstate_type = $interstate_type;
	$columns = [
		array('name' => 'job_no'),
		array('name' => 'job_name'),
		array('header'=>'Courier Label','type' => 'raw','value'=>'@$data->getCourierThirdLabel()'),
		array('name' => 'description', 'type' => 'raw', 'value' => '$data->getFullDescription()'),
		array('name' => 'status', 'value' => '$data->getStatus()', 'filter' => false),
		array('header' => 'Capacity[CBM]', 'value' => '!empty($data->getAvailableCBMString())?$data->getAvailableCBMString():""', 'filter' => false),
		array('header' => 'Capacity[Count]', 'value' => '!empty($data->getAvailableCountString())?$data->getAvailableCountString():""', 'filter' => false),
		array('name' => 'driver_id', 'value' => '!empty($data->driver_id)?$data->driver->name:""'),
		array('name' => 'created'),
		array('header' => 'Total[Plt]', 'value' => '$data->getTotalPltNo()', 'filter' => false),
		array('name' => 'user_id', 'value' => '$data->getUser()'),
		array('header' => 'P/N', 'value' => '!empty($data->mdata["Post"])?"Post":""', 'filter' => false),
	];

	if ($job_type == CargoProcessJob::FBA_JOB) {
		$columns[] = array('name' => 'amazon_info.amazon_booking_time', 'value' => '@$data->amazon_info->amazon_booking_time');
		$columns[] = array('name' => 'amazon_info.booking_ref', 'value' => '@$data->amazon_info->booking_ref');
	}

	if ($job_type == CargoProcessJob::INTERSTATE_JOB) {
		$columns[] = array('header' => 'Cost', 'type' => 'raw', 'value' => '!empty($data->mdata["interstate_cost"]) ? $data->mdata["interstate_cost"] : "N/A"');
	}

	$columns[] = [
		'class' => 'oButtonColumn',
		'template' => '{details}&nbsp;{operate}&nbsp;{log}',
		'buttons' => [
			'details' => [
				'url' => ' Yii::app()->createURL("topCourierService/jobDetails")."?id=".$data->id',
				'imageUrl' => false,
				'visible' => 'true',
				'options' => ['class' => 'tab_link grid_edit_btn', 'label' => $this->t('details'), 'title' => '"edit_job_details".$data->id'],
			],
			'operate' => [
				'url' => ' Yii::app()->createURL("topCourierService/operationJob")."?id=".$data->id',
				'imageUrl' => false,
				'visible' => 'true',
				'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Operation'), 'title' => '$data->id'],
			],
			'log' => [
				'imageUrl' => false,
				'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
				'visible' => 'true',
				'url' => 'Yii::app()->createUrl("topCourierService/logJob", ["id" => $data->id])',
				'label' => 'Log'
			],
		]
	];


$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id' => $_GET["tabid"] . '_cargo_process_job_grid'.$interstate_type,
	'cssFile' => false,
	'dataProvider' => $job->search(true, 30, false, true, $pod_id, $job_type),
	'filter' => $job,
	'columns' => $columns
]);; ?>