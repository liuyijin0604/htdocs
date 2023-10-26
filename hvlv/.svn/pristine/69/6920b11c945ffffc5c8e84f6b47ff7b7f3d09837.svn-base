<h1><?=$this->t('Invoice GST Report');?></h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'invoice-gst-form',
		'enableAjaxValidation' => false,
		'action' => $this->createUrl('report/invoiceGST'),
		'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
	)); ?>

	<div class="row rowcol">
		<?php echo CHtml::label('From Date:', 'from'); ?>
		<?php echo CHtml::textField('fromdate', '', ['size' => 12, 'class' => 'date_input']); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('To Date:', 'to'); ?>
		<?php echo CHtml::textField('todate', '', ['size' => 12, 'class' => 'date_input']); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>