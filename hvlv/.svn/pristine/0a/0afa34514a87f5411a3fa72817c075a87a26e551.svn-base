<h2><?=$model->file->name;?></h2>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'cnldoc-form',
	'enableAjaxValidation'=>false,
)); ?>
	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model,'type',CnlDoc::$types); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>


	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model,'status',[
		20 => 'Uploaded',
		40 => 'Attached',
		100 => 'Deleted',]); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('.jqmWindow.jqmID<?=$_GET['jqmid'];?>');
	
	$('form#cnldoc-form', win).on('success', function(e, r){
		win.data('opener').trigger('reload_cnldoc_grid');
		win.jqmHide();
	});
	
});
</script>