<div class="container">
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'org-form',
	'enableAjaxValidation'=>false,
)); ?>
    <div class="row">
	<div class="col col-md-2 col-sm-4">
        <div class="form-group">
		<?php echo CHtml::label('Type','type'); ?>
		<?php echo CHtml::textField('type', Org::$types[30],array('disabled'=>true,'class'=>'form-control')); ?>
	</div>
        </div>
          <div class="col col-md-2 col-sm-4">
            <div class="form-group">
        	<?php
		echo CHtml::label('Code','code');
		echo CHtml::textField('code',@$model->code,array('size'=>20,'maxlength'=>30,'class'=>'form-control','disabled'=>$model->isNewRecord?false:true));
                ?>
            </div>
          </div>
        
            <div class="col col-md-2 col-sm-4">
                <div class="form-group">
		<?php echo CHtml::label('Belong To','belong'); ?>
		<?php echo CHtml::textField('belongTo', empty($model->by)? '' : ($model->by == 1)? 'System' : $model->owner->name,array('size'=>20,'maxlength'=>30,'class'=>'form-control','disabled'=>true));
		?>
	    </div>
          </div>
        
          <div class="col col-md-4 col-sm-4">
                <div class="form-group">
                <?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>150,'maxlength'=>255,'class'=>'form-control','disabled'=>$model->isNewRecord?false:true)); ?>
	  </div>
          </div>
         </div>
       <div class="row">
            <div class="col col-md-4 col-sm-4">
              <div class="form-group">
                <?php echo $form->labelEx($model,'address'); ?>
		<?php echo $form->textField($model,'address',array('size'=>50,'maxlength'=>255,'class'=>'form-control')); ?>
               </div>
             </div>
             <div class="col col-md-2 col-sm-4">
                <div class="form-group">
              	<?php echo $form->labelEx($model,'suburb'); ?>
		<?php echo $form->textField($model,'suburb',array('size'=>20,'maxlength'=>100,'class'=>'form-control')); ?>
                 </div>
               </div>
             <div class="col col-md-2 col-sm-4">
                 <div class="form-group">
                <?php echo $form->labelEx($model,'state'); ?>
		<?php echo $form->textField($model,'state',array('size'=>20,'maxlength'=>50,'class'=>'form-control')); ?>
                 </div>
             </div>
             <div class="col col-md-2 col-sm-4">
               <div class="form-group">
               <?php echo $form->labelEx($model,'postcode'); ?>
		<?php echo $form->textField($model,'postcode',array('size'=>10,'maxlength'=>10,'class'=>'form-control')); ?>
               </div>
              </div>
        </div>
 

    
      <div class="row">
            <div class="col col-md-2 col-sm-4">
             <div class="form-group">
                <?php echo $form->labelEx($model,'country'); ?>
		<?php echo $form->textField($model,'country',array('size'=>20,'maxlength'=>100,'class'=>'form-control')); ?>
             </div>
            </div>
          <div class="col col-md-2 col-sm-4">
             <div class="form-group">
                <?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>50,'class'=>'form-control')); ?>
             </div>
            </div>
          <div class="col col-md-2 col-sm-4">
             <div class="form-group">
               <?php echo $form->labelEx($model,'fax'); ?>
		<?php echo $form->textField($model,'fax',array('size'=>20,'maxlength'=>50,'class'=>'form-control')); ?>
	</div>
             </div>
            <div class="col col-md-2 col-sm-4">
             <div class="form-group">
               <?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>20,'maxlength'=>50,'class'=>'form-control')); ?>
             </div>
            </div>
       </div>
    <?php if(!$model->isNewRecord):?>
      <h2>Account Details</h2>
	<div class="row">
	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($user,'title'); ?>
		<?php echo $form->dropDownList($user, 'title', $this->t(User::$titles), array('class'=>'form-control')); ?>
	</div>
	</div>

	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($user,'fname'); ?>
		<?php echo $form->textField($user,'fname',array('size'=>40,'maxlength'=>40,'class'=>'form-control')); ?>
	</div>
	</div>

	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($user,'lname'); ?>
		<?php echo $form->textField($user,'lname',array('size'=>40,'maxlength'=>40,'class'=>'form-control')); ?>
	</div>
	</div>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($user,'email'); ?>
		<?php echo $form->textField($user,'email',array('size'=>50,'maxlength'=>255,'class'=>'email form-control')); ?>
	</div>

	<div class="row">
	<div class="col col-sm-6">
	<div class="form-group">
		<?php echo $form->labelEx($user,'password'); ?>
		<?php echo $form->passwordField($user,'password',array('size'=>32,'maxlength'=>32,'minlength'=>6,'value'=>'','class'=>'email form-control pwd_confirm')); ?>
	</div>
	</div>
	
	<div class="col col-sm-6">
	<div class="form-group">
		<?php echo $form->labelEx($user,'pwd_conf'); ?>
		<?php echo CHtml::passwordField('pwd_conf','',array('size'=>32,'maxlength'=>32,'minlength'=>6,'class'=>'email form-control pwd_confirm')); ?>
	</div>
	</div>
	</div>

	<div class="row">
	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($user,'phone'); ?>
		<?php echo $form->textField($user,'phone',array('size'=>40,'maxlength'=>255,'class'=>'form-control')); ?>
	</div>
	</div>
	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($user,'fax'); ?>
		<?php echo $form->textField($user,'fax',array('size'=>40,'maxlength'=>255,'class'=>'form-control')); ?>
	</div>
	</div>
	<div class="col col-sm-4">
	<div class="form-group">
		<?php echo $form->labelEx($user,'mobile'); ?>
		<?php echo $form->textField($user,'mobile',array('size'=>40,'maxlength'=>255,'class'=>'form-control')); ?>
	</div>
	</div>
	</div>
      <?php endif;?>
        <div class="form-group  buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'),array('class'=>'btn btn-primary ajax-link','id'=>'user_btn')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
</div>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
		$('.pwd_confirm').on('change', function(){
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
<?php $this->registerJS(ob_get_clean(),8); ?>