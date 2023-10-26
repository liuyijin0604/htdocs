<h1><?=$this->t('海运安排')?></h1>
<div class="form" style="margin-bottom: 20px">
	<div class="row buttons">
		<?php echo CHtml::button($this->t('Waiting'), ['id' => 'waiting', 'disabled' => Yii::app()->cache->get('container-cartage-status' . session_id()) == 1]); ?>
		<?php echo CHtml::button($this->t('History'), ['id' => 'history', 'disabled' => Yii::app()->cache->get('container-cartage-status' . session_id()) == 2]); ?>
		<?php echo CHtml::button($this->t('Waiting + History'), ['id' => 'waiting_history', 'disabled' => Yii::app()->cache->get('container-cartage-status' . session_id()) == 3]); ?>
		<?php echo CHtml::button($this->t('Cancelled'), ['id' => 'cancelled', 'disabled' => Yii::app()->cache->get('container-cartage-status' . session_id()) == 0]); ?>
	</div>
</div>

<div class="container-cartage-list" style="margin: -10px; padding: 0"></div>

<script type="text/javascript">
$(function() {
	var tab = $("#<?=$_GET['tabid']?>");
	var panel = tab.data('panel');

	window.clearInterval(window.interval);
	window.interval = setInterval(function() {
		$('.grid-view', panel).each(function() {
			$(this).yiiGridView('update', { data: $('.filters input, .filters select', $(this)).serialize() });
		});

		tab.on('close', function() {
			window.clearInterval(window.interval);
		});
	}, 10000);

	$('.container-cartage-list').load('<?=Yii::app()->createUrl("containerCartage/renderList", ["status" => Yii::app()->cache->get('container-cartage-status' . session_id())])?>');

	$('#history', panel).on('click', function() {
		$('.container-cartage-list').load('<?=Yii::app()->createUrl("containerCartage/renderList", ["status" => 2])?>');
		$(this).attr('disabled', true);
		$('#waiting', panel).removeAttr('disabled', true);
		$('#cancelled', panel).removeAttr('disabled', true);
		$('#waiting_history', panel).removeAttr('disabled', true);
	});

	$('#waiting', panel).on('click', function() {
		$('.container-cartage-list').load('<?=Yii::app()->createUrl("containerCartage/renderList", ["status" => 1])?>');
		$(this).attr('disabled', true);
		$('#history', panel).removeAttr('disabled', true);
		$('#cancelled', panel).removeAttr('disabled', true);
		$('#waiting_history', panel).removeAttr('disabled', true);
	});

	$('#cancelled', panel).on('click', function() {
		$('.container-cartage-list').load('<?=Yii::app()->createUrl("containerCartage/renderList", ["status" => 0])?>');
		$(this).attr('disabled', true);
		$('#waiting', panel).removeAttr('disabled', true);
		$('#history', panel).removeAttr('disabled', true);
		$('#waiting_history', panel).removeAttr('disabled', true);
	});

	$('#waiting_history', panel).on('click', function() {
		$('.container-cartage-list').load('<?=Yii::app()->createUrl("containerCartage/renderList", ["status" => 3])?>');
		$(this).attr('disabled', true);
		$('#waiting', panel).removeAttr('disabled', true);
		$('#history', panel).removeAttr('disabled', true);
		$('#cancelled', panel).removeAttr('disabled', true);
	});

	$(panel).off('click', '.ajax_link').on('click', '.ajax_link', function() {
		if ($(this).prop('class').match(/grid_delete_btn/)) {
			var action = 'Delete';
		} else {
			var action = 'Complete';
		}
		if (!confirm('Are you sure to ' + action + '?')) {
			return false;
		}
	}).off('success', '.ajax_link').on('success', '.ajax_link', function() {
		$('.container-cartage-list').load('<?=Yii::app()->createUrl("containerCartage/renderList", ["status" => 1])?>');
	});
});
</script>