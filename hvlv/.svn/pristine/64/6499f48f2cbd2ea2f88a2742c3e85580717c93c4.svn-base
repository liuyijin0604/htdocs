<div class="container">
    <div class="row">
        <div class="col-md-6">
           <p>Parcels: <b><?=$model->totShipments();?></b> &nbsp; Total Weight: <b><?=$model->totWeight();?>KG</b></p>
        </div>
    </div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'consol-form',
	'enableAjaxValidation'=>false,
));
?>
<div class="row">
    <div class="form-group col-md-2">
        <?php echo $form->labelEx($model,'status'); ?>
        <?php echo $form->dropDownList($model, 'status', ImcoConsol::$states, array('class' => 'form-control')); ?>
    </div>
     <div class="form-group col-md-2">
        <?php echo $form->labelEx($model,'eta',array('label'=>'Create Date')); ?>
        <?php echo $form->textField($model, 'eta',array('class' => 'form-control')); ?>
    </div>
</div> 
    <div class="row">
    <div class="form-group col-md-2">
       <?php echo $form->labelEx($model,'pol'); ?>
		<?php echo $form->dropDownList($model,'pol', AppHelper::setting2List('pols'),array('class'=>'form-control')); ?>
    </div>
     <div class="form-group col-md-2">
        <?php echo $form->labelEx($model,'pod'); ?>
          <?php echo $form->dropDownList($model,'pod', AppHelper::setting2List('pods'),array('class'=>'form-control ')); ?>
    </div>
</div>   
<div class="form-group">
    <?php echo CHtml::submitButton('Save',array('class'=>'btn btn-primary')); ?>
</div>
<?php $this->endWidget(); ?>
</div>
<?php ob_start(); ?>
<script type="text/javascript">
    $(function(){
       
    })
</script>
<?php $this->registerJS(ob_get_clean()); ?>

