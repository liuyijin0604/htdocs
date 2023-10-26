<h2>Account Details</h2>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('data-bit' => 1),
));
?>

	<div class="row">
	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->dropDownList($model, 'title', $this->t(User::$titles), array('class'=>'form-control')); ?>
	</div>
	</div>

	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($model,'fname'); ?>
		<?php echo $form->textField($model,'fname',array('size'=>40,'maxlength'=>40,'class'=>'form-control')); ?>
	</div>
	</div>

	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($model,'lname'); ?>
		<?php echo $form->textField($model,'lname',array('size'=>40,'maxlength'=>40,'class'=>'form-control')); ?>
	</div>
	</div>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>50,'maxlength'=>255,'class'=>'email form-control')); ?>
	</div>

	<div class="row">
	<div class="col col-sm-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>32,'maxlength'=>32,'minlength'=>6,'value'=>'','class'=>'email form-control')); ?>
	</div>
	</div>
	
	<div class="col col-sm-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'pwd_conf'); ?>
		<?php echo CHtml::passwordField('pwd_conf','',array('size'=>32,'maxlength'=>32,'minlength'=>6,'class'=>'email form-control')); ?>
	</div>
	</div>
	</div>

	<div class="row">
	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>40,'maxlength'=>255,'class'=>'form-control')); ?>
	</div>
	</div>
	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($model,'fax'); ?>
		<?php echo $form->textField($model,'fax',array('size'=>40,'maxlength'=>255,'class'=>'form-control')); ?>
	</div>
	</div>
	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($model,'mobile'); ?>
		<?php echo $form->textField($model,'mobile',array('size'=>40,'maxlength'=>255,'class'=>'form-control')); ?>
	</div>
	</div>
	</div>
	<div class="form-group buttons">
		<button class="btn btn-primary btn-lg" id="user_btn" type="submit"><?=$this->t('Save');?></button>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#pwd_conf').on('change', function(){
		if($(this).val() != $('#User_password').val()){
			$(this).parent().addClass('has-error');
			$('#user_btn').attr('disabled', true);
		}else{
			$(this).parent().removeClass('has-error');
			$('#user_btn').attr('disabled', false);
		}
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>