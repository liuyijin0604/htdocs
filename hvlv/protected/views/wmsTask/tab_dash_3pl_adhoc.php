<style>
/* Tooltip container */
.tooltip-custom {
	position: relative;
	display: inline-block;
}

/* Tooltip text */
.tooltip-custom .tooltiptext-custom {
	visibility: hidden;
	width: 140%;
	word-break: break-word;
	background-color: #DEDEDE;
	opacity: 1;
	color: #000000;
	text-align: left;
	padding: 10px;
	border-radius: 6px;
	font-size: 25px;

	/* Position the tooltip text - see examples below! */
	position: absolute;
	z-index: 1;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip-custom:hover .tooltiptext-custom {
	visibility: visible;
}
</style>

<div style="position: absolute;">
<?php $job = WmsJob::model()->find('ref = "Adhoc Task"');
if (!empty($job)) { ?>
<a class="tab_link" href="<?=$this->createUrl('wmsTask/createAdhoc');?>" title="New Task"><div class="icon" style="background-position:-16px 0"></div> New Task</a>
<?php } else { echo 'System Error'; } ?>
</div>

<div id="adhoc">
<?php
$model = new WmsTask('search');
$model->unsetAttributes();
if (isset($_GET['WmsTask'])) {
	$model->attributes = $_GET['WmsTask'];
}
$model->type = 6010;
$model->is_request = 1;

$user = User::model()->findByPk(Yii::app()->user->id);
if (!empty($model->status)) {
	$user->extra['adhoc_status'] = $model->status;
	$user->update('meta');
} else if (!empty($user->extra['adhoc_status'])) {
	$user = User::model()->findByPk(Yii::app()->user->id);
	$model->status = $user->extra['adhoc_status'];
}

$ec = new CDbCriteria;
if (empty($model->status)) {
	$ec->addCondition('t.status != 100');
}

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'dash-3pl-adhoc-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(true, 30, $ec),
	'filter' => $model,
	'columns' => array(
		array('name' => 'id', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("wmsTask/update", ["id" => $data->id])."\" class=\"tab_link\" title=\"".$data->getNo()."\">".$data->getNo()." ".$data->getUpdate()."</a>"', 'htmlOptions' => ['style' => 'width: 4%']),
		array('name' => 'cust_name', 'value' => 'empty($data->job->customer)? "" : $data->job->customer->shortName(4)', 'htmlOptions' => ['style' => 'width: 15%']),
		array('name' => 'ref', 'type' => 'raw', 'value' => '$data->getRef()', 'htmlOptions' => ['style' => 'width: 15%']),
		array('name' => 'status', 'value' => '$data->getStatus()',
			'filter' => CHtml::dropDownList('WmsTask[status]', $model->status, array(50 => 'Opening', 10 => 'New', 31 => 'WIP Completed', 32 => 'QA Completed', 99 => 'Completed', 100 => 'Cancelled'), array('prompt' => $this->t('All'))), 'htmlOptions' => ['style' => 'width: 8%']),
		array('name' => 'adhoc_request', 'header' => 'Request', 'type' => 'raw', 'value' => '$data->getAdhocRequest()', 'htmlOptions' => ['style' => 'width: 40%']),
		array('name' => 'schd_time', 'header' => 'Scheduled', 'value' => '$data->schd_time ? date("Y-m-d", strtotime($data->schd_time)) : ""', 'htmlOptions' => ['style' => 'width: 6%']),
		array('name' => 'due_time', 'header' => 'Due', 'value' => '$data->due_time ? date("Y-m-d", strtotime($data->due_time)) : ""', 'htmlOptions' => ['style' => 'width: 6%']),
		array('name' => 'compl_time', 'header' => 'Completed', 'value' => '$data->compl_time ? date("Y-m-d", strtotime($data->compl_time)) : ""', 'htmlOptions' => ['style' => 'width: 6%']),
		array(
			'class' => 'oButtonColumn',
			'template' => '{proc_comp} {qa_comp} {total_comp}',
			'buttons'=>array
			(
				'proc_comp' => array(
					'imageUrl' => false,
					'visible' => '$data->status == 10',
					'options' => array('class' => 'ajax_link grid_edit_btn'),
					'url' => 'Yii::app()->createUrl("wmsTask/adhocComplete", ["id" => $data->id, "status" => 10])',
					'label' => $this->t('Complete (WIP)'),
				),
				'qa_comp' => array(
					'imageUrl' => false,
					'visible' => '$data->status == 31',
					'options' => array('class' => 'ajax_link grid_edit_btn'),
					'url' => 'Yii::app()->createUrl("wmsTask/adhocComplete", ["id" => $data->id, "status" => 31])',
					'label' => $this->t('Complete (QA)'),
				),
				'total_comp' => array(
					'imageUrl' => false,
					'visible' => '$data->status == 32',
					'options' => array('class' => 'ajax_link grid_edit_btn'),
					'url' => 'Yii::app()->createUrl("wmsTask/adhocComplete", ["id" => $data->id, "status" => 32])',
					'label' => $this->t('Complete (Invoice)'),
				),
			),
		),
	),
));
?>
</div>

<script>
$(function() {
	var tab = $('#<?=$_GET["tabid"]?>');
	var panel = tab.data('panel');

	$('#adhoc').height(panel.height() * 0.8);

	$(panel).off('click', '.ajax_link').on('click', '.ajax_link', function() {
		if (!confirm('Are you sure?')) {
			return false;
		}
	}).off('success', '.ajax_link').on('success', '.ajax_link', function() {
		$('#dash-3pl-adhoc-grid', panel).yiiGridView('update');
	});
});
</script>