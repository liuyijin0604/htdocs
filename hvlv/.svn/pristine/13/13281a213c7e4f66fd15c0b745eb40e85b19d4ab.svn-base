<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model,'value'); ?>
		<?php echo $form->textField($model,'value',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->label($model,'cbm'); ?>
		<?php echo $form->textField($model,'cbm',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->label($model,'cnor_tel'); ?>
		<?php echo $form->textField($model,'cnor_tel',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model,'cnee_tel'); ?>
		<?php echo $form->textField($model,'cnee_tel',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->label($model,'cnee_cnid'); ?>
		<?php echo $form->textField($model,'cnee_cnid',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->label($model,'cnee_addr'); ?>
		<?php echo $form->textField($model,'cnee_addr',array('size'=>20,'maxlength'=>35)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model,'item_name'); ?>
		<?php echo $form->textField($model,'item_name',array('size'=>20,'maxlength'=>35)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->label($model,'note'); ?>
		<?php echo $form->textField($model,'note',array('size'=>20,'maxlength'=>35)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mhbns'); ?>
		<?php echo $form->textArea($model,'mhbns',array('cols'=>60, 'rows' => 2)); ?>
		<p><small>Up to 250 numbers.</small></p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Search')); ?>
		<?php echo CHtml::resetButton($this->t('Reset'), array('class' => 'reset_btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->