<h1>Print Labels</h1>

<div class="form">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'print-label-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row" style="margin-top: 20px;">
		<input type="file" name="print_label" id="print_label" />
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit', ['id' => 'btn-save']); ?>
	</div>

	<div class="result" style="height: 50px; padding-top: 20px"></div>

	<?php $this->endWidget(); ?>

</div>

<script>
$(function() {
	$('#print-label-form').on('success', function() {
		$('.result').html('<a href="../label.pdf" target="_blank">Download</a>');
	});
});
</script>