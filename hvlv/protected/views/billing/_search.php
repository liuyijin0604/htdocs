<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>


    <div class="row">
	<div class="rowcol">
		<?php echo CHtml::label('Date From','for_import_billing_search'); ?>
        <?php echo $form->textField($model,'date_from',array('size' => '12', 'class' => 'date_input')); ?>
	</div>

	<div class="rowcol">
        <?php echo CHtml::label('Date To','for_import_billing_search'); ?>
        <?php echo $form->textField($model,'date_to',array('size' => '12', 'class' => 'date_input')); ?>
	</div>
    </div>
	<div class="row buttons">
		<?php echo CHtml::button($this->t('Search'),['class' => 'search-button-done']); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->