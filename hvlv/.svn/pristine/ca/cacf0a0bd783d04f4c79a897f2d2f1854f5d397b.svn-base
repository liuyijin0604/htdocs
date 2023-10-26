<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'instruction-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?php echo 'Fields with';?> <span class="required">*</span> <?php echo 'are required.';?></p>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'body'); ?>
		<?php //echo $form->textArea($model,'body',array('rows'=>6, 'cols'=>50));
		$this->widget('ext.ckeditor.CKEditorWidget',array(
		  "model"=>$model,
		  "attribute"=>'body',
		  "config" => array(
			  "height"=>"200px",
			  "width"=>"570px",
			  "toolbar"=>"Basic",
			  ),
		  ));
		?>
	</div>

	<!-- <div class="row">
		<?php echo $form->labelEx($model,'updated'); ?>
		<?php echo $form->textField($model,'updated'); ?>
	</div> -->

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	win.on('close', function(){
		if(typeof CKEDITOR != 'undefined'){
			for(i in CKEDITOR.instances){
				if($('#'+i, win).length > 0) CKEDITOR.instances[i].destroy(true);
			}
		}
	});
	$('form#instruction-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
});
</script>
