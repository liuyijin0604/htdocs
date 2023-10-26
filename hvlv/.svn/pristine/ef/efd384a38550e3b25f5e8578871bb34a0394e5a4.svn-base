<br>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'sign-form',
	'enableAjaxValidation'=>false,
)); ?>
	<div class="row">
		<div class="col-6" style="padding-right: 5px;">
			<?php echo CHtml::textField('company', '', array('size'=>20,'maxlength'=>30,'placeholder'=>'Company')); ?>
		</div>
		<div class="col-6" style="padding-left: 5px;">
			<?php echo CHtml::textField('driver', '', array('size'=>20,'maxlength'=>30,'placeholder'=>'Driver')); ?>
		</div>
	</div>

	<div class="row">
		<div class="col-6" style="padding-right: 5px;">
			<?php echo CHtml::textField('rego', '', array('size'=>30,'maxlength'=>50,'placeholder'=>'Rego')); ?>
		</div>
		<div class="col-6" style="padding-left: 5px;">
			<?php echo CHtml::textField('note', '', array('size'=>30,'maxlength'=>50,'placeholder'=>'Note')); ?>
		</div>
	</div>

	<div class="row">
		<canvas style="border: 1px solid #ddd; width: 100%"></canvas>
		<?php echo CHtml::hiddenField('sig'); ?>
	</div>


	<div class="row">
		<div class="col-6" style="padding-right: 5px;">
			<?php echo CHtml::button($this->t('Clear Signature'), array('class' => 'clear btn btn-primary btn-block')); ?>
		</div>
		<div class="col-6" style="padding-left: 5px;">
			<?php echo CHtml::submitButton($this->t('Save'), array('class' => 'save_btn btn btn-primary btn-block')); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div>

<script type='text/javascript' src='<?php echo Yii::app()->request->baseUrl; ?>/../js/signature_pad.min.js'></script>
<script type="text/javascript">
	$(function(){
		var signaturePad = new SignaturePad(document.querySelector("canvas"), {
			backgroundColor: 'white',
			penColor: 'black'
		});
		$('input.clear').on("click", function (event) {
			signaturePad.clear();
		});

		$('#sign-form').on("submit", function (event) {
			$('#sig').val(signaturePad.toDataURL("image/png"));
			return true;
		});

		$('#sign-form').on('success', function(e, r) {
			setTimeout(function() {
				$('span.tog-sign').removeClass("icon-up").addClass('icon-down');
				$('.sign-div').slideUp();
			}, 500);

			setTimeout(function() {
				$('.entry-form').slideDown();
				$('.res').slideUp();
			}, 500);
		});

	});
</script>