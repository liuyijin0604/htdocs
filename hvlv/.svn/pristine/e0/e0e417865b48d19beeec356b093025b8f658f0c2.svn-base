<div class="form" style="position:relative">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'org-contact-form',
	'enableAjaxValidation'=>false,
));

?>

		<?php echo $form->hiddenField($model,'org_id',array('size'=>11,'maxlength'=>11)); ?>
	
	<!--div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model, 'type', $this->t(OrgContact::$types),array('empty' => $this->t('Select One'))); ?>
	</div-->
	
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>30,'maxlength'=>255)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'position', array('id'=>'position_label')); ?>
		<?php echo $form->textField($model,'position',array('size'=>30,'maxlength'=>255)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'address'); ?>
		<?php echo $form->textField($model,'address',array('size'=>50,'maxlength'=>255)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'suburb'); ?>
		<?php echo $form->textField($model,'suburb',array('size'=>20,'maxlength'=>100)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'state'); ?>
		<?php echo $form->textField($model,'state',array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'postcode'); ?>
		<?php echo $form->textField($model,'postcode',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'country'); ?>
		<?php echo $form->textField($model,'country',array('size'=>20,'maxlength'=>100)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>40,'maxlength'=>255,'class'=>'email')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'direct'); ?>
		<?php echo $form->textField($model,'direct',array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'mobile'); ?>
		<?php echo $form->textField($model,'mobile',array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'fax'); ?>
		<?php echo $form->textField($model,'fax',array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'desc'); ?>
		<?php echo $form->textArea($model,'desc',array('rows'=>3, 'cols'=>40, 'style'=>'width:260px;')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'accode'); ?>
		<?php echo $form->textField($model,'accode',array('size'=>20,'maxlength'=>30)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->radioButtonList($model,'status', $this->t(array(1=>'Active', 0=>'Inactive')), array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
	</div>

	<div class="row" style="position:absolute;width:300px;right:10px;top:280px;">
	<h3>Functions</h3>
	<?php
		foreach(orgContact::$funcs as $v => $f){
			echo '<label style="width:150px;float:left;">'.CHtml::checkBox('func[]', ($model->func & $v) > 0, ['value' => $v]).' '.$f.'</label>';
		}
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
	$('form#org-contact-form', win).on('success', function(e, r){
		win.data('opener').trigger('reload_contact_grid');
		win.jqmHide();
	});
	
	$('#rapp_btn', win).click(function(){
		$.post('orgContact/approval/<?=$model->id;?>', {'status': 20}, function(){
			win.data('opener').trigger('reload_contact_grid');
			win.jqmHide();
		});
	});
	
	$('#wapp_btn', win).click(function(){
		$.post('orgContact/approval/<?=$model->id;?>', {'status': 10}, function(){
			win.data('opener').trigger('reload_contact_grid');
			win.jqmHide();
		});
	});
	
	$('#rmda_btn', win).click(function(){
		$.post('orgContact/approval/<?=$model->id;?>', {'status': 30}, function(){
			win.data('opener').trigger('reload_contact_grid');
			win.jqmHide();
		});
	});
	
	$('#mdapp_btn', win).click(function(){
		$.post('orgContact/approval/<?=$model->id;?>', {'status': 1}, function(){
			win.data('opener').trigger('reload_contact_grid');
			win.jqmHide();
		});
	});
});
</script>