<?php
$data = [];
if (Yii::app()->cache->get('container-cartage-status' . session_id()) != 3) {
	$ccs = ContainerCartage::model()->findAll('status = :status', [':status' => Yii::app()->cache->get('container-cartage-status' . session_id())]);
} else {
	$ccs = ContainerCartage::model()->findAll('status IN (1,2)');
}
foreach ($ccs as $cc) {
	if (!empty($cc->container_load_time) && $cc->container_load_time != '0000-00-00 00:00:00') {
		$date = date('Y-m-d', strtotime($cc->container_load_time));
	} else {
		$date = '1970-01-01';
	}
	$data[$date][] = $cc->id;
}

ksort($data);

foreach ($data as $date => $items) {
	echo '<h2>' . ($date != '1970-01-01' ? $date : 'No date') . '</h2>';
	$model = new ContainerCartage;
	$model->ids = $items;
	if (!empty($_GET['ajax']) && !empty($_GET['ContainerCartage']) && $_GET['ajax'] == 'container-cartage-' . $date . '-grid') {
		unset($_GET['ContainerCartage']['id']);
		$model->attributes = $_GET['ContainerCartage'];
	}
	$this->widget('zii.widgets.grid.CGridView', [
		'id' => 'container-cartage-' . $date . '-grid',
		'cssFile' => false,
		'dataProvider' => $model->search(),
		'filter' => $model,
		'summaryText' => '',
		'columns' => array(
			'id',
			array('name' => 'org_id', 'value' => '$data->getOrg()'),
			array('name' => 'task_id', 'value' => '$data->getTask()', 'type' => 'raw'),
			array('name' => 'container_no', 'value' => '$data->getContainerNo()'),
			array('type' => 'raw', 'value' => '$data->getPrint()'),
			'container_load_time',
			'booking_release_no',
			'vessel_name',
			'etd',
			'start_receiving_time',
			'cutoff_time',
			array('name' => 'empty_depot_name', 'value' => '$data->getEmptyDepotName()', 'filter' => CHtml::dropDownList('ContainerCartage[empty_depot_name]', $model->empty_depot_name, ContainerCartage::$empty_depot_names, ['prompt' => 'All'])),
			array('name' => 'full_return_name', 'value' => '$data->getFullReturnName()', 'filter' => CHtml::dropDownList('ContainerCartage[full_return_name]', $model->full_return_name, ContainerCartage::$full_return_names, ['prompt' => 'All'])),
			array('name' => 'container_type', 'value' => '$data->getContainerType()', 'filter' => CHtml::dropDownList('ContainerCartage[container_type]', $model->container_type, ContainerCartage::$container_types, ['prompt' => 'All'])),
			'cargo_estimate_ready_date',
			array('name' => 'if_fq', 'value' => '$data->getIfFQ()', 'filter' => CHtml::dropDownList('ContainerCartage[if_fq]', $model->if_fq, ContainerCartage::$if_fqs, ['prompt' => 'All'])),
			array('name' => 'if_pra', 'value' => '$data->getIfPRA()', 'filter' => CHtml::dropDownList('ContainerCartage[if_pra]', $model->if_pra, ContainerCartage::$if_pras, ['prompt' => 'All'])),
			array(
				'class' => 'oButtonColumn',
				'template' => '{update} {complete} {cancel}',
				'buttons' => array(
					'update' => array(
						'imageUrl' => false,
						'visible' => 'true',
						'options' => array('class' => 'tab_link grid_edit_btn', 'title' => '"Update Container Cartage " . $data->id'),
						'label' => '',
					),
					'complete' => array(
						'imageUrl' => false,
						'url' => 'Yii::app()->createUrl("containerCartage/complete", ["id" => $data->id])',
						'visible' => '$data->status == 1',
						'options' => array('class' => 'ajax_link'),
						'label' => '<span style="font-size: 13px"><div style="background-position: 0px 0px" class="icon"></div>&nbsp;</span>',
					),
					'cancel' => array(
						'imageUrl' => false,
						'url' => 'Yii::app()->createUrl("containerCartage/cancel", ["id" => $data->id])',
						'options' => array('class' => 'ajax_link grid_delete_btn'),
						'label' => '',
						'visible' => '$data->status == 1',
					),
				),
			),
		),
	]);
	echo '<br><br>';
}
?>