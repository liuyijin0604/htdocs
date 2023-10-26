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
		<?php echo $form->label($model,'weight'); ?>
		<?php echo $form->textField($model,'weight',array('size'=>10,'maxlength'=>10)); ?>
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
		<?php echo $form->label($model,'cnee_company'); ?>
		<?php echo $form->textField($model,'cnee_company',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->label($model,'cnee_tel'); ?>
		<?php echo $form->textField($model,'cnee_tel',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model,'cnee_email'); ?>
		<?php echo $form->textField($model,'cnee_email',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->label($model,'cnee_addr'); ?>
		<?php echo $form->textField($model,'cnee_addr',array('size'=>30,'maxlength'=>45)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model,'FBA POs'); ?>
		<?php echo $form->textArea($model,'fbas',array('cols'=>60, 'rows' => 2)); ?>
		<p><small>Up to 10 numbers.This search is very slow, please be patient.</small></p>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model,'FBA IDs'); ?>
		<?php echo $form->textArea($model,'fbaIds',array('cols'=>60, 'rows' => 2)); ?>
		<p><small>Up to 10 numbers.This search is very slow, please be patient.</small></p>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('PMC/AKE Numbers','PMC/AKE Numbers'); ?>
		<?php echo $form->textArea($model,'pmc_ake_numbers',array('cols'=>60, 'rows' => 2)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->label($model,'mhbns'); ?>
		<?php echo $form->textArea($model,'mhbns',array('cols'=>60, 'rows' => 2)); ?>
		<p><small>Up to 200 numbers.</small></p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->