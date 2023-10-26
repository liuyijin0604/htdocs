<h1><?=$this->t('Quick Create Stock Out Task')?></h1>
<br />

<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'quick-out-task-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="row rowcol">
		<?php echo CHtml::label('Task Type', 'type'); ?>
		<?php echo CHtml::dropDownList('type', '', array('3010' => 'Pick Pallet', '2030' => 'Container Load'), array('prompt' => $this->t('Select One'), 'required' => 'required')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Stock Out All', 'all'); ?>
		<?php echo CHtml::checkBox('all', ''); ?>
	</div>

	<?php if (!empty($model->mdata['simple_in'])) { ?>
		<div class="row rowcol">
			<?php echo CHtml::label('Port', 'port'); ?>
			<?php echo CHtml::dropDownList('port', '', oList::kvp('ex_port')); ?>
		</div>
	<?php } ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

<?php $this->endWidget(); ?>
</div>

<script>
$(function() {
	$('#quick-out-task-form').on('success', function(e, r) {
		$('#wms-task-grid').yiiGridView('update');
		setTimeout(function() {
			$('.popCancel').trigger('click');
			$('#wms-task-grid').find('a[href*="/wmsTask/update/' + r.id + '"]').trigger('click');
		}, 7e2);
	});
});
</script>