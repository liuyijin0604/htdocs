<div class="pane">
    <div class="form">
  <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'custom-user-assign-form',
	'enableAjaxValidation'=>false,
      )); ?>
    <h3>Assign User</h3>
    <?php 
    $user_maps=[];
       $rs =TypeMapUser::model()->findAll('type=:type and status=1',array(':type'=> TypeMapUser::TYPE_CUSTOM_PROCESS));
       foreach($rs as $oneMap){
           $user_maps[$oneMap['map_type']]=$oneMap['user_id'];
       }
    
     foreach (ShipmentProcess::$the_types as $key => $value): ?>
        <div class="row">
            <label><?=$value?></label>
           <?php echo CHtml::dropDownList($key,@$user_maps[$key], ImportsMail::$importscs_list_id,array('prompt'=>'SELECT'));?>
        </div>
    <?php endforeach; ?>
    <div class="row button">
        <?php echo CHtml::submitButton('Save');?>
    </div>
    <?php $this->endWidget(); ?>
     </div>
</div>


