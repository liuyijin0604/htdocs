<h2><?=Yii::app()->name.' '.$this->t('Feedback');?></h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'feedback-form',
	'enableAjaxValidation'=>false,
));
?>
	<div class="row">
		<?php echo $form->labelEx($model,'comment'); ?>
		<?php echo $form->textArea($model,'comment',array('rows'=>8, 'cols'=>80)); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Send')); ?>
	</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('form#feedback-form', win).on('success', function(e, r){
		win.jqmHide();
	});
});
</script>