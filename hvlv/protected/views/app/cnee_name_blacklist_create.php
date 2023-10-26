<h1><?=$this->t('Blacklist Cnee Name');?> <?php echo $model->id; ?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'cnee-name-blacklist-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'keyword'); ?>
		<?php echo $form->textField($model,'keyword',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<!-- <div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>250)); ?>
	</div> -->

	<div class="row">
		<?php echo $form->labelEx($model,'note'); ?>
		<?php echo $form->textArea($model,'note',array('rows' => 5, 'cols' => 50,'maxlength'=>250)); ?>
	</div>

	<!-- <div class="row">
		<?php echo $form->labelEx($model,'updated'); ?>
		<?php echo $form->textField($model,'updated'); ?>
	</div> -->

	<div class="row rowcol buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

	<div class="row rowcol buttons">
		<?php if(!empty($model->id))echo CHtml::button('inactivate',array('id' => 'deleteButton')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = $('#<?=$_GET["tabid"];?>').data('panel');

	tab.unbind('reload_blacklist-grid').bind('reload_blacklist-grid', function(){
		$('#cnee-name-blacklist-grid', tab.data('panel')).yiiGridView('update');
		return false;
	});

	$('form#cnee-name-blacklist-form', win).on('success', function(e, r){
		//tab.trigger('reload_system-setting-grid');
		$('#cnee-name-blacklist-grid', tab.data('panel')).yiiGridView('update');
		return false;
	});

	$('#deleteButton', win).on('click',function()
	{
		if( confirm('Are you sure to inactivate this record ?')){
			$.ajax({
			            url: '<?=$this->createUrl('app/deleteBlackListCneeName')."?id=".$model->id?>',
			            type: "get",
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
				             $('#cnee-name-blacklist-grid', tab.data('panel')).yiiGridView('update');
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
