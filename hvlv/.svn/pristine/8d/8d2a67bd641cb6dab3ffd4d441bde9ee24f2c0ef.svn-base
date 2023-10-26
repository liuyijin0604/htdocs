<h1><?=$this->t('Add Connotes');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'new-connotes-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<?php echo CHtml::label('Connotes','connotes');?>
		<?php echo CHtml::textArea('connotes', '', array('cols'=>40, 'rows'=>10)); ?>
		<p><small>*One connote per line.</small></p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Add')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('form#new-connotes-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

});
</script>