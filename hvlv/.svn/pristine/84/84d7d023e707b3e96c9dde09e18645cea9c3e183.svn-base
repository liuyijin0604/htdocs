<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'email-tpl-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row">
		<?php echo $form->labelEx($model,'slug'); ?>
		<?php echo $form->textField($model,'slug',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'language'); ?>
		<?php echo $form->dropDownList($model,'language', Yii::app()->params['langs'], array('class'=>'required', 'empty' => $this->t('Select One'))); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'subject'); ?>
		<?php echo $form->textField($model,'subject',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row" style="height:300px">
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
	$('form#email-tpl-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
});
</script>