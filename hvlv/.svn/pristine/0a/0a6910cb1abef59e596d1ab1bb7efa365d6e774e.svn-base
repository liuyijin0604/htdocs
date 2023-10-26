<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model,'hbn'); ?>
		<?php echo $form->textField($model,'hbn'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Search')); ?>
		<?php echo CHtml::resetButton($this->t('Reset'), array('class' => 'reset_btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->