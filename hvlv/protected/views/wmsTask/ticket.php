<h1>Create <?=($model->type == 2110 ? 'Pickup' : 'Stockin')?> Ticket</h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'pickup-ticket-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('wmsTask/print', array('id' => $model->id, 't' => 'ticket')),
		'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
	)); ?>

	<div class="row rowcol">
		<?php echo CHtml::label('Date:', 'date', ['required' => 'required']); ?>
		<?php echo CHtml::textField('date', date('Y-m-d 09:00:00'), ['size' => '20', 'class' => 'datetime_input', 'required' => 'required']); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Create')); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>

<script>
	$(function() {
		var tab = $("#<?=$_GET['tabid'];?>");
		$('#pickup-ticket-form').on('success', function() {
			$('.popCancel').trigger('click');
			tab.trigger('reload_tab');
		});
	});
</script>