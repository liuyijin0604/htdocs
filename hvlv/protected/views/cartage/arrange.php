<div class="pane">
	<h1><?=$this->t('拖车安排')?></h1>

	<div style="right: 20px; position: absolute">
		<a class="jqm_link" data-win-class="XXL" href="<?=$this->createUrl('cartage/create', ['type' => 'send_terminal'])?>" title="New Cartage"><div class="icon" style="background-position: -16px 0"></div> New Cartage</a>
	</div>

	<div class="form">
		<div class="row buttons">
			<?php echo CHtml::button($this->t('Waiting'), ['id' => 'waiting', 'disabled' => $_SESSION['cartage-arrange-status'] == 10]); ?>
			<?php echo CHtml::button($this->t('History'), ['id' => 'history', 'disabled' => $_SESSION['cartage-arrange-status'] == 70]); ?>
			<?php echo CHtml::button($this->t('Waiting + History'), ['id' => 'waiting_history', 'disabled' => $_SESSION['cartage-arrange-status'] == 110]); ?>
			<?php echo CHtml::button($this->t('Cancelled'), ['id' => 'cancelled', 'disabled' => $_SESSION['cartage-arrange-status'] == 100]); ?>
		</div>
	</div>

	<?php
	$ec = new CDbCriteria;
	// $ec->addCondition('t.job_id != 0');
	$ec->with = 'job';
	if (empty($_GET['Cartage_sort'])) {
		if ($model->status >= 70) {
			$ec->order = 'CASE t.scd_time WHEN "0000-00-00 00:00:00" THEN 1 ELSE 2 END, t.scd_time DESC, job.awb ASC';
		} else {
			$ec->order = 't.scd_time ASC, job.awb ASC';
		}
	}
	$this->widget('zii.widgets.grid.CGridView', [
		'id' => 'cartage-arrange-grid',
		'cssFile' => false,
		'dataProvider' => $model->search($ec),
		'filter' => $model,
		'columns' => [
			['name' => 'id', 'type' => 'raw', 'value' => '"<a href=\"" . Yii::app()->createUrl("cartage/update", ["id" => $data->id]) . "\" title=\"Edit Cartage\" class=\"tab_link\">" . $data->id . "</a>"'],
			['header' => 'Terminal', 'name' => 'to_id', 'value' => '$data->to_org->name'],
			['header' => '客户', 'name' => 'job_owner', 'value' => '@$data->job->owner->name'],
			['header' => 'Tasks', 'name' => 'ref', 'type' => 'raw', 'value' => '$data->getWmsTaskRefView()'],
			['header' => 'Ref', 'name' => 'ref'],
			['name' => 'note', 'value' => '@$data->getOpNote()'],
			['header' => '是否打PMC', 'value' => '$data->getPMC()', 'type' => 'raw'],
			['header' => 'Xray', 'value' => '$data->showXray()', 'type' => 'raw'],
			['header' => '送机场', 'value' => '$data->showCartage()', 'type' => 'raw'],
			['header' => '板数', 'name' => 'plt'],
			['header' => 'AWB', 'name' => 'job_awb', 'type' => 'raw', 'value' => '$data->getJobAwb()'],
			['header' => 'Flight No.', 'name' => 'job_flight', 'type' => 'raw', 'value' => '$data->getJobFlight()'],
			['type' => 'raw', 'value' => '$data->getPrint()'],
			['header' => 'ETD', 'name' => 'scd_time'],
			[
				'class' => 'oButtonColumn',
				'template' => '{cancel}{complete}{retrieve}',
				'buttons' => [
					'complete' => [
						'imageUrl' => false,
						'url' => 'Yii::app()->createUrl("cartage/complete", ["id" => $data->id])',
						'options' => ['class' => 'ajax_link'],
						'label' => '<span style="font-size: 13px"><div style="background-position: 0px 0px" class="icon"></div>Complete</span>',
						'visible' => '$data->status <= 65',
					],
					'retrieve' => [
						'imageUrl' => false,
						'url' => 'Yii::app()->createUrl("cartage/retrieve", ["id" => $data->id])',
						'options' => ['class' => 'ajax_link'],
						'label' => '<span style="font-size: 13px"><div style="background-position: -224px -32px" class="icon"></div>Retrieve</span>',
						'visible' => '$data->status == 70',
					],
					'cancel' => [
						'imageUrl' => false,
						'url' => 'Yii::app()->createUrl("cartage/cancel", ["id" => $data->id])',
						'options' => ['class' => 'ajax_link grid_delete_btn'],
						'label' => ' Cancel',
						'visible' => '$data->status == 10 && Acl::hasAccess("B:cartage/cancel")',
					],
				],
			],
		],
	]);
	?>
</div>

<script>
$(function() {
	var tab = $("#<?=$_GET['tabid']?>");
	var panel = tab.data('panel');
	var status = 10;

	$('#history', panel).on('click', function() {
		status = 70;
		$.fn.yiiGridView.update('cartage-arrange-grid', {
			data: { 'Cartage[status]': 70 }
		});
		$(this).attr('disabled', true);
		$('#waiting', panel).removeAttr('disabled', true);
		$('#cancelled', panel).removeAttr('disabled', true);
		$('#waiting_history', panel).removeAttr('disabled', true);
	});

	$('#waiting', panel).on('click', function() {
		status = 10;
		$.fn.yiiGridView.update('cartage-arrange-grid', {
			data: { 'Cartage[status]': 10 }
		});
		$(this).attr('disabled', true);
		$('#history', panel).removeAttr('disabled', true);
		$('#cancelled', panel).removeAttr('disabled', true);
		$('#waiting_history', panel).removeAttr('disabled', true);
	});

	$('#cancelled', panel).on('click', function() {
		status = 100;
		$.fn.yiiGridView.update('cartage-arrange-grid', {
			data: { 'Cartage[status]': 100 }
		});
		$(this).attr('disabled', true);
		$('#waiting', panel).removeAttr('disabled', true);
		$('#history', panel).removeAttr('disabled', true);
		$('#waiting_history', panel).removeAttr('disabled', true);
	});

	$('#waiting_history', panel).on('click', function() {
		status = 110;
		$.fn.yiiGridView.update('cartage-arrange-grid', {
			data: { 'Cartage[status]': 110 }
		});
		$(this).attr('disabled', true);
		$('#waiting', panel).removeAttr('disabled', true);
		$('#history', panel).removeAttr('disabled', true);
		$('#cancelled', panel).removeAttr('disabled', true);
	});

	$(panel).on('click', '.ajax_link', function() {
		if (!confirm('Are you sure?')) {
			return false;
		}
		setTimeout(function() {
			$.fn.yiiGridView.update('cartage-arrange-grid', {
				data: { 'Cartage[status]': status }
			});
		}, 5e2);
	});

	tab.bind('onOpen', function() {
		$.fn.yiiGridView.update('cartage-arrange-grid', {
			data: { 'Cartage[status]': status }
		});
	});
});
</script>