<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Settings',
	),
));
?>
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
<?php if(!Yii::app()->user->isGuest && Yii::app()->user->grp == 80): ?>
<div class="form">
<h2>Preference</h2>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'pref-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('data-bit' => 1),
));
?>
	<div class="checkbox">
		<label>
		<?php echo CHtml::checkbox('extra[always_last_shipper]', !empty($model->org->extra['always_last_shipper'])), $this->t('Always use last entered shipper details for new shipment.'); ?> 
		</label><br />
		<label>
		<?php echo CHtml::checkbox('extra[mark_print]', !empty($model->org->extra['mark_print'])), $this->t('Mark connotes as not printed when importing shipments.'); ?> 
		</label><br />
		<label>
		<?php echo CHtml::checkbox('extra[new_multi]', !empty($model->org->extra['new_multi'])), $this->t('Allow create multiple connotes for shipment.'); ?> 
		</label><br />
		<label>
		<?php echo CHtml::checkbox('extra[pickup_when_weigh]', !empty($model->org->extra['pickup_when_weigh'])), $this->t('Allow pickup when weigh parcel.'); ?> 
		</label>
	</div>

	<div class="form-group">
		<label>Page Size</label>
		<div class="input-group" style="max-width:150px">
		<?php echo CHtml::dropDownList('extra[pager_size]', empty($model->org->extra['pager_size'])? 20 : $model->org->extra['pager_size'], [20 => 20, 30 => 30, 50 => 50, 100 => 100], array('class' => 'form-control')); ?>
    	</div>
	</div>

	<div class="form-group">
		<label>Direct Orders Margin</label>
		<div class="input-group" style="max-width:150px">
    		<div class="input-group-addon">$</div>
    		<?php echo CHtml::textField('extra[edo_margin]', empty($model->org->extra['edo_margin'])? 1 : $model->org->extra['edo_margin'], array('class' => 'form-control')); ?>
    		<div class="input-group-addon">/item</div>
    	</div>
	</div>

	<div class="form-group buttons">
		<?php echo CHtml::hiddenField('extra[pos]', 1); ?> 
		<button class="btn btn-primary btn-lg" id="user_btn" type="submit"><?=$this->t('Save');?></button>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php endif; ?>

<p><a href="<?=$this->createUrl('qr/show');?>" target="_blank">Download QR Code</a></p>

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