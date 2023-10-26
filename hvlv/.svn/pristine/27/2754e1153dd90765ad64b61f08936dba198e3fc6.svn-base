<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'batch-status-form',
)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model, 'mhbns'); ?>
		<?php echo $form->textArea($model, 'mhbns', array('cols' => 60, 'rows' => 20)); ?>
		<p><small>Up to 200 numbers.</small></p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Confirm')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->

<script>
$(function() {
	var tab = $('#jqmw_<?=$_GET["tabid"];?>');
	var win = tab.data('panel');

	$('#batch-status-form', win).on('success', function() {
		$('.popCancel').trigger('click');
	});
});
</script>