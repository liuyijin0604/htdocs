<div style="right: 20px; position: absolute;">
	<a class="tab_link" href="<?=$this->createUrl('wmsTaskManage/list');?>" title="Manage WmsTask Object"><div class="icon" style="background-position: -176px -544px"></div> Manage WmsTask</a>
</div>
<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'dashboard-main-form',
		'enableAjaxValidation' => false,
	)); ?>

		<div class="row rowcol rowleft">
			<?php echo CHtml::label('From', 'from'); ?>
			<?php echo CHtml::textField('from', '', ['class' => 'date_input', 'id' => 'main_from']); ?>
		</div>

		<div class="row rowcol">
			<?php echo CHtml::label('To', 'to'); ?>
			<?php echo CHtml::textField('to', '', ['class' => 'date_input', 'id' => 'main_to']); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>
<br />

<?php
$model = new WmsTask('search');
$model->unsetAttributes();
if (isset($_GET['WmsTask'])) {
	$model->attributes = $_GET['WmsTask'];
}
$ec = new CDbcriteria;
$ec->with = ['job.customer', 'job.customer.owner'];
$ec->addCondition('JSON_VALUE(customer.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(customer.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.op_id") IN (' . implode(',', EdiJob::$op) . ') OR JSON_VALUE(owner.meta, "$.sp_id") IN (' . implode(',', EdiJob::$op) . ')');

if (!empty($_GET['from'])) {
	$ec->with[] = 'createlog';
	$ec->addCondition('createlog.time >= "' . date('Y-m-d', strtotime($_GET['from'])) . '"');
}

if (!empty($_GET['to'])) {
	$ec->with[] = 'createlog';
	$ec->addCondition('createlog.time < "' . date('Y-m-d', strtotime($_GET['to'] . ' + 1 day')) . '"');
}

$ec->addCondition('t.bwf & 16 = 0');

$ec->order = 'CASE t.status WHEN 30 THEN 1 WHEN 20 THEN 1 WHEN 10 THEN 3 WHEN 99 THEN 4 ELSE 5 END';

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'dashboard-main-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(true, 30, $ec),
	'filter' => $model,
	'columns' => array(
		array('name' => 'id', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("wmsTask/update", ["id" => $data->id])."\" class=\"tab_link\" title=\"".$data->getNo()."\">".$data->getNo()."</a>"', 'htmlOptions' => ['style' => 'width:50px']),
		array('name' => 'cust_name', 'value' => 'empty($data->job->customer)? "" : $data->job->customer->shortName(4)', 'htmlOptions' => ['style' => 'width:200px']),
		array('name' => 'ref', 'htmlOptions' => ['style' => 'width: 100px']),
		array('name' => 'type_ex', 'header' => 'Type', 'value' => '$data->getType()', 
			'filter' => CHtml::dropDownList('WmsTask[type_ex]', $model->type_ex, $this->t(WmsTask::$types_ex), array('prompt' => $this->t('All'))), 'htmlOptions' => ['style' => 'width:80px']),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter' => CHtml::dropDownList('WmsTask[status]', $model->status, $this->t(WmsTask::$states), array('prompt' => $this->t('All'))), 'htmlOptions' => ['style' => 'width:80px']),
		array('header' => 'Proc By', 'value' => '$data->getLog()["proc"]'),
		array('header' => 'Check By', 'value' => '$data->getLog()["check"]'),
		array('header' => '预期到达板数', 'value' => '$data->getDemand()["pi_expect"]'),
		array('header' => '入库', 'type' => 'raw', 'value' => '$data->getDemand()["pi_stockin"]'),
		array('header' => '拍照', 'type' => 'raw', 'value' => '$data->getDemand()["pi_photo"]'),
		array('header' => '提供重量', 'type' => 'raw', 'value' => '$data->getDemand()["pi_weight"]'),
		array('header' => '需点数', 'type' => 'raw', 'value' => '$data->getDemand()["pi_count"]'),
		array('header' => '检查batch no', 'type' => 'raw', 'value' => '$data->getDemand()["pi_batch"]'),
		array('header' => '检查有效期', 'type' => 'raw', 'value' => '$data->getDemand()["pi_expiry"]'),
		array('header' => '分仓', 'type' => 'raw', 'value' => '$data->getDemand()["pi_split"]'),
		array('header' => 'ETA', 'value' => '$data->getDemand()["pi_eta"]'),
		array('header' => 'ETD', 'value' => '$data->getDemand()["pi_etd"]'),
		array('header' => 'Other Requirement', 'value' => '$data->getDemand()["pi_other"]'),
		array('header' => 'Create Time', 'name' => 'createlog.time'),
		array('name' => 'due_time'),
		array(
			'class' => 'oButtonColumn',
			'template' => '{view} {update}',
			'buttons' => array
			(
				'view' => array(
					'imageUrl' => false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl' => false,
					'visible' => 'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label' => $this->t('Update'), 'title' => '$data->getNo()'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	tab.bind('onOpen', function() {
		$('#dashboard-main-grid', panel).yiiGridView('update');
	});

	$('#dashboard-main-form #main_from, #main_to', panel).on('change', function() {
		$('#dashboard-main-grid', panel).yiiGridView('update', { data: 'from=' + $('#main_from', panel).val() + '&to=' + $('#main_to', panel).val() });
	});
});
</script>
