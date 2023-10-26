<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model,'mnos'); ?>
		<?php echo $form->textArea($model,'mnos',array('cols'=>40, 'rows' => 2)); ?>
		<p><small>Up to 200 numbers.</small></p>
	</div>

	<div class="row rowcol">
		<?php echo $form->label($model,'mnames'); ?>
		<?php echo $form->textArea($model,'mnames',array('cols'=>40, 'rows' => 2)); ?>
		<p><small>Up to 200 names.</small></p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Search')); ?> &nbsp; 
		<?php echo CHtml::button($this->t('Export'), ['class' => 'exp_btn']); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->