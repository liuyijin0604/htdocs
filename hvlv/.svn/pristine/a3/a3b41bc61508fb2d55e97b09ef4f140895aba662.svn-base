<br>
<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'pin-form',
	'enableAjaxValidation' => false,
)); ?>

	<div class="row">
		<?php echo CHtml::textField('pinno', '', array('size' => 20, 'maxlength' => 30, 'placeholder' => 'PIN')); ?>
	</div>

	<div class="row">
		<?php echo CHtml::submitButton($this->t('Check'), array('class' => 'save_btn btn btn-primary btn-block')); ?>
	</div>

<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
	$(function() {
		$('#pin-form').on('success', function(e, r) {
			$('.entry-form form').html(r.html);
			$('.entry-form').slideDown();
			$('.res').html('');
		});

		$('#pin-form').on('error', function(e, r) {
			$('#pinno').val('');
		});
	});
</script>