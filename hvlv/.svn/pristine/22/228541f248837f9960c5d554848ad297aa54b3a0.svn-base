<div class="container">
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
  'id'=>'org-password-form',
  'enableAjaxValidation'=>false,
)); ?>
    <div class="row">
        <div class="col col-md-4 col-sm-4">
            <div class="form-group">
            <?php echo CHtml::label('ID / 代理号', 'id', array('required'=>'required')); ?>
            <?php echo CHtml::textField('id','',array('size'=>150,'maxlength'=>255,'class'=>'form-control','required'=>'required')); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col col-md-4 col-sm-4">
            <div class="form-group">
            <?php echo CHtml::label('Email / 邮箱', 'email', array('required'=>'required')); ?>
            <?php echo CHtml::textField('email','',array('size'=>150,'maxlength'=>255,'class'=>'form-control','required'=>'required')); ?>
            </div>
        </div>
    </div>

    <div class="form-group  buttons">
        <?php echo CHtml::submitButton(('Reset / 重置'),array('class'=>'btn btn-primary ajax-link','id'=>'user_btn')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
  $('form#org-password-form').on('success', function() {
    window.location.href = '<?=$this->createUrl('accounts/finish', array('result'=>'success'));?>';
  });
  $('form#org-password-form').on('error', function() {
    window.location.href = '<?=$this->createUrl('accounts/finish', array('result'=>'fail'));?>';
  });
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>