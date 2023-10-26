<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'storage-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'wid'); ?>
		<?php echo $form->dropDownList($model,'wid', Org::dptList(), array('empty' => 'Select One')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'pid'); ?>
		<?php echo $form->hiddenField($model,'pid');
			$acname = empty($_GET["tabid"])? 'parent_ac' : $_GET["tabid"].'_parent_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('storage/parentSuggest'),
				'value' => empty($model->pid)? '' : $model->parent->name,
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
				),
		));
		?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model,'status', Storage::$states, array('empty' => 'Select One')); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model, 'type', Storage::$types, array('empty' => 'Select One')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'code'); ?>
		<?php echo $form->textField($model,'code',array('size'=>15,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'width'); ?>
		<?php echo $form->textField($model,'width'); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'depth'); ?>
		<?php echo $form->textField($model,'depth'); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'height'); ?>
		<?php echo $form->textField($model,'height'); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'cx'); ?>
		<?php echo $form->textField($model,'cx'); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'cy'); ?>
		<?php echo $form->textField($model,'cy'); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'cz'); ?>
		<?php echo $form->textField($model,'cz'); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'cap_kg'); ?>
		<?php echo $form->textField($model,'cap_kg',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'cap_cbm'); ?>
		<?php echo $form->textField($model,'cap_cbm',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'cap_item'); ?>
		<?php echo $form->textField($model,'cap_item',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'notes'); ?>
		<?php echo $form->textArea($model,'notes',array('rows'=>3, 'cols'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('form#storage-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

	//parent ac
	$('#<?=$acname;?>', win).on('autocompletecreate', function(){
		$(this).data('src', $(this).autocomplete('option', 'source'));
	}).on('focus', function(){
		$(this).autocomplete({source : $(this).data('src')+'?wid='+$('#Storage_wid', win).val()});
	});
});
</script>