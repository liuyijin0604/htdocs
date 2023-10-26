<div  class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'outturn-form',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('import/calculate'),
)); ?>
      <div class="row">
        <?php echo CHtml::label('start_date','start_date');?>
        <?php echo CHtml::textField('start_date_time','',array('class'=>'date_input'));?>
      </div>
     <div class="row">
      <?php echo CHtml::label('end_date','end_date');?>
      <?php echo CHtml::textField('end_date_time','',array('class'=>'date_input'));?>
     
     </div>
    
	

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>
<?php $this->endWidget(); ?>

<iframe id="err_result" name="err_result" style="color: green; margin: 10px 0; border: 1px solid black;padding:20px; width: 90%; "></iframe>

</div>