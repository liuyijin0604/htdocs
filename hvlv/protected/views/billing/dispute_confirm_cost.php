<h1>Accept <?=$model->shipment_no?></h1>

<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'billing-courier-dispute-allocate-form',
	'enableAjaxValidation' => false,
)); ?>

	<br>

	<div class="row">
		<?php echo CHtml::label('Amount: ', 'amount'); ?>
		<?php echo CHtml::textField('amount', !empty($model->mdata['amount']) ? $model->mdata['amount'] : number_format($model->value - $model->my_value, 2, '.', ''), ['size' => 20]); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Consol', 'consol'); ?>
		<?php echo CHtml::textField('consol', @$model->consol->no, ['size' => 20]); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Code', 'code'); ?>
		<?php echo CHtml::textField('code', @$model->mdata['code'], ['size' => 20]); ?>
	</div>

	<?php if (empty($model->mdata['confirmed'])) { ?>
		<div class="row buttons">
			<?php echo CHtml::submitButton($this->t('Update'), ['id' => 'dispute-allocate-btn']); ?>
		</div>
	<?php } ?>

<?php $this->endWidget(); ?>
</div>

<script>
$(function() {
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('#billing-courier-dispute-allocate-form').off('click', '#dispute-allocate-btn').on('click', '#dispute-allocate-btn', function() {
		if (!confirm('Are sure to allocate this cost to ' + $('#consol').val())) {
			return false;
		}
	}).on('success', function() {
		$('.popCancel', win).trigger('click');
	});
});
</script>