<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'dashboard-report-form',
		'enableAjaxValidation' => false,
	)); ?>

		<div class="rowcol rowleft">
			<?php echo CHtml::label('From', 'from'); ?>
			<?php echo CHtml::textField('from', '', ['class' => 'date_input', 'id' => 'report_from']); ?>
		</div>

		<div class="rowcol">
			<?php echo CHtml::label('To', 'to'); ?>
			<?php echo CHtml::textField('to', '', ['class' => 'date_input', 'id' => 'report_to']); ?>
		</div>

		<div class="rowcol" style="margin-right: 50px">
			<label>&nbsp;</label>
			<?php echo CHtml::button('Clear'); ?>
		</div>

		<div class="rowcol">
			<label>&nbsp;</label>
			<?php echo CHtml::button('Today'); ?>
		</div>

		<div class="rowcol">
			<label>&nbsp;</label>
			<?php echo CHtml::button('Yesterday'); ?>
		</div>

		<div class="rowcol">
			<label>&nbsp;</label>
			<?php echo CHtml::button('This Week'); ?>
		</div>

		<div class="rowcol">
			<label>&nbsp;</label>
			<?php echo CHtml::button('Last Week'); ?>
		</div>

		<div class="rowcol">
			<label>&nbsp;</label>
			<?php echo CHtml::button('This Month'); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>

<div id="report">
</div>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	function toUrl(data)
	{
		url = '<?=Yii::app()->createUrl("wmsTask/dashEXReport")?>?';
		data = Object.assign(data);
		Object.entries(data).forEach(function(v, k) {
			if (v[1] != undefined) {
				url += v[0] + '=' + v[1] + '&';
			}
		});
		return url;
	}

	$('#dashboard-report-form #report_from, #report_to', panel).on('change', function() {
		url = toUrl({ 'from': $('#report_from', panel).val(), 'to': $('#report_to', panel).val() });
		$('#report', panel).load(encodeURI(url.replace('.app', '')), function() {
			$('#report_from', panel).val($('#from_hidden').val());
			$('#report_to', panel).val($('#to_hidden').val());
		});
	});

	$('#dashboard-report-form input[type="button"]', panel).on('click', function() {
		url = toUrl({ 'act': $(this).val() });
		$('#report', panel).load(encodeURI(url.replace('.app', '')), function() {
			$('#report_from', panel).val($('#from_hidden').val());
			$('#report_to', panel).val($('#to_hidden').val());
		});
	});

	$('input[value="Today"]', panel).trigger('click');
});
</script>