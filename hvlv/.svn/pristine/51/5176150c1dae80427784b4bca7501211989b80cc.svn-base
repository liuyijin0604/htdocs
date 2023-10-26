<h1><?=$this->t('Farmland Report');?></h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'farmland-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('wmsTask/export', ['t' => 'farmland']),
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>

	<div class="row rowcol">
			<?php echo CHtml::label('From:', 'from'); ?>
			<?php echo CHtml::textField('from', '', ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<div class="row rowcol">
			<?php echo CHtml::label('To:', 'to'); ?>
			<?php echo CHtml::textField('to', '', ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>