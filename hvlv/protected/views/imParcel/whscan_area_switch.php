<div class="pane">
    <div class="form">
      <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'whscan_area_switch_form',
	'enableClientValidation'=>true,
       ));
     ?>
        <div class="row">
            <?php echo CHtml::label('Sound Switch','sound switch');?>
            <?php  echo CHtml::radioButtonList('import_scan_area_sound',empty($model->extra['import_scan_area_sound'])?0:1,Array('0'=>'Switch ON',1=>'Switch Off'),array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp'));?>
        </div>   
       <div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
       </div>
    <?php $this->endWidget(); ?>    
    </div> 
</div>