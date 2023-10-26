<?php
/* @var $this QuotesController */
/* @var $model Quotes */
/* @var $form CActiveForm */
?>
<div class="form" style="margin-left:10px">
<?php $form=$this->beginWidget('CActiveForm', array(
//	'action'=>Yii::app()->createUrl($this->route),
	'enableAjaxValidation'=>false,
	'method'=>'get',
)); ?>
    <div class="row">
        <?php echo $form->labelEx($model,'key_word_search',array('label'=>'Search Body/Subject')); ?>
        <?php echo $form->textField($model,'key_word_search',array('size'=>50)); ?>
    </div>
	<div class="row">
		<?php echo $form->labelEx($model,'from_time',array('label'=>'From')); ?>
		<?php echo $form->textField($model,'from_time',['class' => 'date_input','id'=>'from_time'.$_GET['tabid']]); ?>
        </div>
       <div class="row">
                <?php echo $form->labelEx($model,'to_time',array('label'=>'To')); ?>
		<?php echo $form->textField($model,'to_time',['class' => 'date_input','id'=>'to_time'.$_GET['tabid']]); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->