<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route, array('tabid' => $_GET['tabid'])),
	'method'=>'get',
)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model, 'connote'); ?>
		<?php echo $form->textField($model, 'connote', array('size' => 20, 'maxlength' => 20)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model, 'declare_connote'); ?>
		<?php echo $form->textField($model, 'declare_connote', array('size' => 20, 'maxlength' => 20)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Search')); ?>
		<?php echo CHtml::resetButton($this->t('Reset'), array('class' => 'reset_btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->