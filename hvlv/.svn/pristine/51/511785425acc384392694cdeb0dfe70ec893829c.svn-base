<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<div class="row">
		<?php echo $form->label($model,'incoterm'); ?>
		<?php echo $form->dropDownList($model, 'incoterm', $this->t($model::$incoterms), array('prompt'=>$this->t('All'))); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'containerLoad'); ?>
		<?php echo $form->dropDownList($model,'containerLoad', $this->t($model::$containerLoads), array('prompt'=>$this->t('All'))); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'refCode'); ?>
		<?php echo $form->textField($model,'refCode',array('size'=>30,'maxlength'=>30)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->