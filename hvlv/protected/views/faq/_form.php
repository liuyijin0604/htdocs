<div class="form" style="min-height: 500px">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'faq-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>
	<div class="row">
	<?php echo $form->labelEx($model,'cid'); ?>
<?php echo $model->cateList(); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'title'); ?>
<?php echo $form->textField($model,'title',array('size'=>100,'maxlength'=>200)); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'cont'); ?>
		<?php //echo $form->textArea($model,'cont',array('rows'=>6, 'cols'=>50));
		$this->widget('ext.ckeditor.CKEditorWidget',array(
		  "model"=>$model,
		  "attribute"=>'cont',
		  "config" => array(
			  "height"=>"220px",
			  "width"=>"770px",
			  "toolbar"=>"Basic",
			  ),
		  ));
		?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
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
	$('form#faq-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
});
</script>