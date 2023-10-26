<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route)."?type=".$_GET['type']."&&tabid=".$_GET["tabid"],
	'method'=>'get',
)); ?>


	<div class="row rowcol rowleft">
		<?php echo $form->label($model,'refs'); ?>
		<?php echo $form->textArea($model,'refs',array('cols'=>60, 'rows' => 2)); ?>
		<p><small>Up to 200 numbers.</small></p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->