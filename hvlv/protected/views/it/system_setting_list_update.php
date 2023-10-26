<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'system-setting-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'key'); ?>
		<?php echo $form->textField($model,'key',array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'uid'); ?>
		<?php echo $form->textField($model,'uid',array('size'=>20,'maxlength'=>50,'disabled' => true)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'date'); ?>
		<?php echo $form->textField($model,'date',array('size'=>20,'maxlength'=>50,'disabled' => true)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'meta'); ?>
		<?php echo $form->textArea($model,'meta',array('rows'=>20, 'cols'=>70)); ?>
	</div>

	<div class="row rowcol rowleft buttons">
		<?php echo CHtml::button(empty($model->id)?'Create':'Save',array('id' => 'saveButton')); ?>
	</div>

	<div class="row rowcol buttons">
		<?php if(!empty($model->id))echo CHtml::button('Delete',array('id' => 'deleteButton')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$tabid;?>');
	var tab = $('#<?=$tabid;?>');
	var panel = $('#<?=$tabid;?>').data('panel');

	tab.unbind('reload_system-setting-grid').bind('reload_system-setting-grid', function(){
		$('#system-setting-grid', tab.data('panel')).yiiGridView('update');
		return false;
	});

	$('#saveButton').on('click',function()
	{
		var form = new FormData(document.getElementById("system-setting-form"));
		$.ajax({
		            url: '<?=$this->createUrl('it/saveSystemSettingLine')."?id=".$model->id?>',
		            type: "post",
		            data: form,
		            processData: false,
		            contentType: false,
		            success: function(r) {
		            	r = JSON.parse(r);
		                if(r.done)
		                 {
							myApp.notice(r.msg, 5000);
						 }else
						 {
							myApp.alert(r.msg, false);   
			             }
			             tab.trigger('reload_system-setting-grid');
			         },
		            error: function(e) {
		                console.log(e);
		            }
		        });	
		return false;
	});

	$('#deleteButton').on('click',function()
	{
		if( confirm('Are you sure to delete?')){
			var form = new FormData(document.getElementById("system-setting-form"));
			$.ajax({
			            url: '<?=$this->createUrl('it/deleteSystemSettingLine')."?id=".$model->id?>',
			            type: "post",
			            data: form,
			            processData: false,
			            contentType: false,
			            success: function(r) {
			            	r = JSON.parse(r);
			                if(r.done)
			                 {
								myApp.notice(r.msg, 5000);
							 }else
							 {
								myApp.alert(r.msg, false);   
				             }
				             tab.trigger('reload_system-setting-grid');
				         },
			            error: function(e) {
			                console.log(e);
			            }
			        });	
		}
		return false;
	});

});
</script>