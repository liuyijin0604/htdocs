<div style="right: 20px; position: absolute">
	<a class="export_search" href="#" data-baseurl="<?=$this->createUrl('ediJobAirline/export')?>" target="_blank"><div style="background-position:-48px -688px" class="icon"></div> Export Report</a>
</div>

<h1><?=$this->t('KPI Report');?></h1>

<table class="chart">
	<tr>
		<th width="50"></th>
		<?php foreach ($record as $time => $item) {
			echo '<th width="100">' . $time . '</th>';
		} ?>
	</tr>
	<tr>
		<td align="center">合格</td>
		<?php foreach ($record as $time => $item) {
			echo '<td align="center">' . $item[1] . '</td>';
		} ?>
	</tr>
	<tr>
		<td align="center">不合格</td>
		<?php foreach ($record as $time => $item) {
			echo '<td align="center">' . $item[2] . '</td>';
		} ?>
	</tr>
	<tr>
		<td align="center">总数</td>
		<?php foreach ($record as $time => $item) {
			echo '<td align="center">' . $item[0] . '</td>';
		} ?>
	</tr>
	<tr>
		<td></td>
		<?php foreach ($record as $time => $item) {
			echo '<td align="center">' . round($item[1] / max($item[0], 1) * 100) . '%</td>';
		} ?>
	</tr>
</table>
<br />

<div class="form">
	<div class="row buttons">
		<?php echo CHtml::button($this->t('Waiting'), array('id' => 'waiting', 'disabled' => $_SESSION['edijob-airline-report-status'] == '< 30')); ?>
		<?php echo CHtml::button($this->t('History'), array('id' => 'history', 'disabled' => $_SESSION['edijob-airline-report-status'] == 30)); ?>
	</div>
</div>

<?php
$ec = new CDbcriteria;
$ec->addCondition('user_id IN (' . implode(',', EdiJob::$op) . ')');

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'edijob-airline-report-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(true, $ec),
	'filter' => $model,
	'columns' => array(
		array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("ediJob/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"'),
		array('name' => 'awb', 'type' => 'raw', 'value' => '$data->AwbTracking()'),
		array('name' => 'owner_name', 'value' => '$data->owner->name'),
		array('name' => 'plt', 'header' => 'Pallets', 'value' => '$data->getWmsTaskPlt()'),
		array('name' => 'etd', 'header' => 'ETD', 'value' => '$data->awbconsol->etd'),
		array('name' => 'ata', 'header' => 'ATA', 'value' => '$data->getATA()'),
		array('name' => 'transit_type', 'header' => 'Type', 'value' => '$data->getTransitType()', 'filter' => CHtml::dropDownList('EdiJob[transit_type]', $model->transit_type, ['direct' => 'Direct', 'transit' => 'Transit'], ['prompt' => 'All'])),
		array('name' => 'result', 'header' => '是否合格', 'value' => '$data->getResult()', 'filter' => CHtml::dropDownList('EdiJob[result]', $model->result, ['合格' => '合格', '不合格' => '不合格'], ['prompt' => 'All'])),
		array('header' => '货齐?', 'type' => 'raw', 'value' => '$data->getAllDone()'),
		array(
			'class' => 'oButtonColumn',
			'template' => '{update}',
			'buttons' => array(
				'update' => [
					'imageUrl' => false,
					'options' => ['class' => 'tab_link grid_edit_btn', 'label' => $this->t('Update'), 'title' => '$data->awb'],
					'visible' => '!empty($data->awb)',
				],
			),
		),
	),
));
?>

<script>
$(function() {
	var tab = $("#<?=$_GET['tabid']?>");
	var panel = tab.data('panel');
	var status = '< 30';

	$('#history', panel).on('click', function() {
		status = 30;
		$.fn.yiiGridView.update('edijob-airline-report-grid', {
			data: { 'EdiJob[status]': 30 }
		});
		$(this).attr('disabled', true);
		$('#waiting', panel).removeAttr('disabled', true);
	});

	$('#waiting', panel).on('click', function() {
		status = '< 30';
		$.fn.yiiGridView.update('edijob-airline-report-grid', {
			data: { 'EdiJob[status]': '< 30' }
		});
		$(this).attr('disabled', true);
		$('#history', panel).removeAttr('disabled', true);
	});

	$(panel).on('click', '.ajax_link', function() {
		if (!confirm('Are you sure?')) {
			return false;
		}
		setTimeout(function() {
			$.fn.yiiGridView.update('edijob-airline-report-grid', {
				data: { 'EdiJob[status]': status }
			});
		}, 5e2);
	});

	tab.bind('onOpen', function() {
		$.fn.yiiGridView.update('edijob-airline-report-grid', {
			data: { 'EdiJob[status]': status }
		});
	});

	$('a.export_search', panel).on('mousedown', function() {
		var q = $('.filters input, .filters select', panel).serialize();
		var href = $(this).data('baseurl') + '&' + q;
		href = href.replace('.app&', '?');
		$(this).attr('href', href);
	});
});
</script>