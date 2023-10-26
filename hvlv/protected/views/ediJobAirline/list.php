<h1><?=$this->t('KPI Entry');?></h1>

<div class="form">
	<div class="row buttons">
		<?php echo CHtml::button($this->t('Waiting'), array('id' => 'waiting', 'disabled' => $_SESSION['edijob-airline-status'] == '< 30')); ?>
		<?php echo CHtml::button($this->t('History'), array('id' => 'history', 'disabled' => $_SESSION['edijob-airline-status'] == 30)); ?>
	</div>
</div>

<?php
$ec = new CDbcriteria;
$ec->addCondition('user_id IN (' . implode(',', EdiJob::$op) . ')');

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'edijob-airline-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(true, $ec),
	'filter' => $model,
	'columns' => array(
		array('name' => 'awb', 'type' => 'raw', 'value' => '$data->AwbTracking()'),
		array('name' => 'owner_name', 'value' => '$data->owner->name'),
		array('name' => 'awbconsol.etd'),
		array('header' => 'Pallets', 'value' => '$data->getWmsTaskPlt()'),
		array(
			'class' => 'oButtonColumn',
			'template' => '{update} {complete}',
			'buttons' => array(
				'update' => [
					'imageUrl' => false,
					'options' => ['class' => 'tab_link grid_edit_btn', 'label' => $this->t('Update'), 'title' => '$data->awb'],
					'visible' => '!empty($data->awb)',
				],
				'complete' => [
					'imageUrl' => false,
					'options' => ['class' => 'ajax_link'],
					'label' => '<div style="background-position: 0px 0px" class="icon"></div>Complete',
					'visible' => '!empty($data->awb) && $data->status < 30',
					'url' => 'Yii::app()->createUrl("ediJobAirline/complete", ["id" => $data->id])',
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
		$.fn.yiiGridView.update('edijob-airline-grid', {
			data: { 'EdiJob[status]': 30 }
		});
		$(this).attr('disabled', true);
		$('#waiting', panel).removeAttr('disabled', true);
	});

	$('#waiting', panel).on('click', function() {
		status = '< 30';
		$.fn.yiiGridView.update('edijob-airline-grid', {
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
			$.fn.yiiGridView.update('edijob-airline-grid', {
				data: { 'EdiJob[status]': status }
			});
		}, 5e2);
	});

	tab.bind('onOpen', function() {
		$.fn.yiiGridView.update('edijob-airline-grid', {
			data: { 'EdiJob[status]': status }
		});
	});
});
</script>