<div class="form">
	<h1><?=$title?></h1>
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'confirm-form',
		'enableAjaxValidation' => false,
	)); ?>

		<div class="row buttons">
			<?php echo CHtml::submitButton('Confirm'); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>