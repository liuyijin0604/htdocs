<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => '3pl-dashboard-report-form',
		'enableAjaxValidation' => false,
	)); ?>

		<div class="rowcol rowleft">
			<?php echo CHtml::label('From', 'from'); ?>
			<?php echo CHtml::textField('from', '', ['class' => 'date_input', 'id' => '3pl_report_from']); ?>
		</div>

		<div class="rowcol">
			<?php echo CHtml::label('To', 'to'); ?>
			<?php echo CHtml::textField('to', '', ['class' => 'date_input', 'id' => '3pl_report_to']); ?>
		</div>

		<div class="rowcol" style="margin-right: 50px">
			<label>&nbsp;</label>
			<?php echo CHtml::button('Clear'); ?>
		</div>

		<div class="rowcol">
			<label>&nbsp;</label>
			<?php echo CHtml::button('Last 7 days'); ?>
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

		<div class="rowcol">
			<label>&nbsp;</label>
			<?php echo CHtml::button('Last Month'); ?>
		</div>

		<div class="rowcol">
			<label>&nbsp;</label>
			<?php echo CHtml::button('This Year'); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>

<br>
<br>
<div id="3pl-dashboard-report" style="min-height: 500px"></div>

<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	function toUrl(data)
	{
		url = '<?=Yii::app()->createUrl("wmsTask/dash3PLDetail")?>?';
		data = Object.assign(data);
		Object.entries(data).forEach(function(v, k) {
			if (v[1] != undefined) {
				url += v[0] + '=' + v[1] + '&';
			}
		});
		return url;
	}

	$('#3pl-dashboard-report-form #3pl_report_from, #3pl_report_to', panel).on('change', function() {
		url = toUrl({ 'from': $('#3pl_report_from', panel).val(), 'to': $('#3pl_report_to', panel).val() });
		$('#3pl-dashboard-report', panel).load(encodeURI(url.replace('.app', '')), function() {
			$('#3pl_report_from', panel).val($('#from_hidden').val());
			$('#3pl_report_to', panel).val($('#to_hidden').val());
		});
	});

	$('#3pl-dashboard-report-form input[type="button"]', panel).on('click', function() {
		url = toUrl({ 'act': $(this).val() });
		$('#3pl-dashboard-report', panel).load(encodeURI(url.replace('.app', '')), function() {
			$('#3pl_report_from', panel).val($('#from_hidden').val());
			$('#3pl_report_to', panel).val($('#to_hidden').val());
		});
	});

	$('input[value="Last 7 days"]', panel).trigger('click');
});
</script>