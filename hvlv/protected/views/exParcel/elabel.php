<h1>Generat Export E-Label</h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'exparcel-elabel-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>['target' => '_blank', 'class' => 'ifrm-form'],
));
?>
	<div class="row rowcol rowcol left">
		<?php echo CHtml::label('Starting Number', 'sn'); ?>
		<?php echo CHtml::textField('start', '', array('size'=>15, 'placeholder' => 'EAU123000001')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Qty (max 1000)', 'qty'); ?>
		<?php echo CHtml::textField('qty', '', array('size' => 5)); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Paper Size', 'ps'); ?>
		<?php echo CHtml::radioButtonList('size', 'A6', array('A6' => 'A6', 'A4' => 'A4'), array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Generate')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

