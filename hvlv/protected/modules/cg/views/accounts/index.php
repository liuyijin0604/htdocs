<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home / 主页', array('site/index')),
  'links' => array(
          'My Acount / 我的账户'
  ),
));
?>

<div class="form">
<h2><?=$this->t('Basic Info / 基本信息')?></h2>
<?php $form=$this->beginWidget('CActiveForm', array(
  'id'=>'org-form',
  'enableAjaxValidation'=>false,
)); ?>
<?php if (!empty(Yii::app()->user->incomplete)) { ?>
    <h3 style="height:35px;"><a id="msg" class="text-danger" style="cursor:pointer; text-decoration:none;"><span class="glyphicon glyphicon-exclamation-sign"></span>请修改密码并补全基本信息</a></h3>
<?php } ?>
    <div class="row">
          <div class="col col-md-4 col-sm-4">
                <div class="form-group">
                <?php echo $form->labelEx($model,'company name / 公司名'); ?>
    <?php echo $form->textField($model,'name',array('size'=>150,'maxlength'=>255,'class'=>'form-control','disabled'=>$model->isNewRecord?false:true)); ?>
    </div>
          </div>
         </div>
       <div class="row">
            <div class="col col-md-4 col-sm-4">
              <div class="form-group">
                <?php echo $form->labelEx($model,'address / 地址',array('required'=>'required')); ?>
    <?php echo $form->textField($model,'address',array('size'=>50,'maxlength'=>255,'class'=>'form-control','required'=>'required')); ?>
               </div>
             </div>
             <div class="col col-md-2 col-sm-4">
                <div class="form-group">
                <?php echo $form->labelEx($model,'suburb / 街区',array('required'=>'required')); ?>
    <?php echo $form->textField($model,'suburb',array('size'=>20,'maxlength'=>100,'class'=>'form-control','required'=>'required')); ?>
                 </div>
               </div>
             <div class="col col-md-2 col-sm-4">
                 <div class="form-group">
                <?php echo $form->labelEx($model,'state / 州',array('required'=>'required')); ?>
    <?php echo $form->textField($model,'state',array('size'=>20,'maxlength'=>50,'class'=>'form-control','required'=>'required')); ?>
                 </div>
             </div>
             <div class="col col-md-2 col-sm-4">
               <div class="form-group">
               <?php echo $form->labelEx($model,'postcode / 邮编',array('required'=>'required')); ?>
    <?php echo $form->textField($model,'postcode',array('size'=>10,'maxlength'=>10,'class'=>'form-control','required'=>'required')); ?>
               </div>
              </div>
        </div>
 

    
      <div class="row">
            <div class="col col-md-2 col-sm-4">
             <div class="form-group">
                <?php echo $form->labelEx($model,'country / 国家'); ?>
    <?php echo $form->textField($model,'country',array('size'=>20,'maxlength'=>100,'class'=>'form-control')); ?>
             </div>
            </div>
          <div class="col col-md-2 col-sm-4">
             <div class="form-group">
                <?php echo $form->labelEx($model,'phone / 联系电话',array('required'=>'required')); ?>
    <?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>50,'class'=>'form-control','required'=>'required')); ?>
             </div>
            </div>
          <div class="col col-md-2 col-sm-4">
             <div class="form-group">
               <?php echo $form->labelEx($model,'fax / 传真'); ?>
    <?php echo $form->textField($model,'fax',array('size'=>20,'maxlength'=>50,'class'=>'form-control')); ?>
  </div>
             </div>
            <div class="col col-md-2 col-sm-4">
             <div class="form-group">
               <?php echo $form->labelEx($model,'email / 电子邮箱',array('required'=>'required')); ?>
    <?php echo $form->textField($model,'email',array('size'=>20,'maxlength'=>50,'class'=>'form-control','required'=>'required')); ?>
             </div>
            </div>
       </div>

<h2><?=$this->t('Password / 密码')?></h2>

       <?php if (!empty($model->extra['password'])) { ?>
    <div class="row">
        <div class="col col-md-4 col-sm-4">
            <div class="form-group">
            <?php echo $form->labelEx($model,'current password / 当前密码'); ?>
            <?php echo CHtml::passwordField('current_password','',array('size'=>150,'maxlength'=>255,'class'=>'form-control')); ?>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="row">
        <div class="col col-md-4 col-sm-4">
            <div class="form-group">
            <?php echo $form->labelEx($model,'new password / 新密码'); ?>
            <?php echo CHtml::passwordField('password','',array('size'=>150,'maxlength'=>255,'class'=>'form-control')); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col col-md-4 col-sm-4">
            <div class="form-group">
            <?php echo $form->labelEx($model,'new password again / 再次输入新密码'); ?>
            <?php echo CHtml::passwordField('password_again','',array('size'=>150,'maxlength'=>255,'class'=>'form-control')); ?>
            </div>
        </div>
    </div>


        <div class="form-group  buttons">
    <?php echo CHtml::submitButton(('Save / 保存'),array('class'=>'btn btn-primary ajax-link','id'=>'user_btn')); ?>
  </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
  $('#msg').effect('shake', 'slow');
  $('form#org-form').on('success', function() {
    window.location.href = '<?=$this->createUrl('site/index');?>';
  });
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>