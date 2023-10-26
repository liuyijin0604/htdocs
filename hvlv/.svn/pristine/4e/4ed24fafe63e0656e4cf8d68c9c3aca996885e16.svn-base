<h1><?=$this->t('Bulk Task') . ' - ' . $_GET['t'];?></h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'bulk-task-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('wmsTask/bulkTask', ['t' => $_GET['t']]),
	)); ?>

	<div class="row rowcol">
		<?php echo CHtml::label('Tasks divide by enter', 'tasks'); ?>
		<?php echo CHtml::textArea('tasks', '', ['rows' => 5, 'cols' => 50]); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Submit')); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>

<script>
$(function() {
	$('#bulk-task-form').on('success', function() {
		$('.popCancel').trigger('click');
	});
});
</script>