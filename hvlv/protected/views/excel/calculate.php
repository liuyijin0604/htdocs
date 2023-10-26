<div style="position:absolute; right: 20px">
    <a class="jqm_link" href="<?=Yii::app()->createAbsoluteUrl('email/createWCA',array('type'=>30))?>"><span class="icon"></span>WCA</a>
</div>

<div style="position: absolute; left: 20px;" class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'outturn-form',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('calculate/calculate'),
)); ?>
      <div class="row">
        <?php echo CHtml::label('start_date','start_date');?>
        <?php echo CHtml::textField('start_date','',array('class'=>'date_input'));?>
      </div>
     <div class="row">
      <?php echo CHtml::label('end_date','end_date');?>
      <?php echo CHtml::textField('end_date','');?>
     
     </div>
       <div class="row">
      <?php echo CHtml::label('org ID','org_id');?>
      <?php echo CHtml::textField('org_id','');?>
      </div>
     <div class="row">
      <?php echo CHtml::label('Courier ID','org_id');?>
      <?php echo CHtml::textField('courier_id','');?>
     </div>
<!--     <div class="row">
      <?php echo CHtml::label('agent_id','agent_id');?>
      <?php echo CHtml::textField('agent_id','');?>
     
     </div>-->
	

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'outturn-form',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('calculate/changeLabel'),
)); ?>

     <div class="row">
      <?php echo CHtml::label('label_change','label_change');?>
      <?php echo CHtml::textField('label_change','');?>
     
     </div>
	

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>
    

    
    <fieldset>
        <legend>fastway(YES OR NO  can delivery)</legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'update-fastway',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('calculate/updatefw'),
)); ?>
     
     <div class="row">
         <input type='file' name='fw_file'/>

     
     </div>
	

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>
    
    </fieldset>   
    
     <fieldset>
        <legend>change Status</legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'update-fastway',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('calculate/changeStatus'),
)); ?>
     <div class="row">
         <input type='file' name='fw_file'/>
 </div>
	 <div class="row">
      <?php echo CHtml::label('status','status_change');?>
      <?php echo CHtml::textField('status_change','');?>
     
     </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>
    
    </fieldset>   


     <fieldset>
        <legend>Create Aupost from fastway</legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'update-fastway',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('calculate/createFastway'),
)); ?>
     <div class="row">
         <input type='file' name='fw_file'/>
 </div>
   <div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>
    
    </fieldset>   


   <fieldset>
        <legend>Remove fastway Ref and Save it to Notell</legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'update-fastway',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('calculate/removeFastRef'),
)); ?>
     <div class="row">
         <input type='file' name='fw_file'/>
 </div>
   <div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>
    
    </fieldset>   

  <fieldset>
        <legend>Link old to new </legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'update-fastway',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('calculate/linkref'),
)); ?>
     <div class="row">
         <input type='file' name='fw_file'/>
 </div>
   <div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>
    
    </fieldset>   


  <fieldset>
        <legend>FastWayZone </legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'update-fastway',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('calculate/updatefw1'),
)); ?>
     <div class="row">
         <input type='file' name='fw_file'/>
 </div>
   <div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>
    
    </fieldset> 
</fieldset>   
    
     <fieldset>
        <legend>change 1shipment to Aupost</legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'update-fastway',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('calculate/change2Aupost'),
)); ?>
     <div class="row">
      <?php echo CHtml::label('status','shipment_ref');?>
      <?php echo CHtml::textField('shipment_ref','');?>
     
     </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>
    
    </fieldset>  

  <fieldset>
        <legend>get Track</legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'update-fastway',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('calculate/getTrack'),
)); ?>
     <div class="row">
         <input type='file' name='fw_file'/>
 </div>
   <div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>
    
    </fieldset>  
  <fieldset>
        <legend>change chargecode</legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'update-fastway',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('calculate/changechargecode'),
)); ?>
     <div class="row">
         <input type='file' name='fw_file'/>
 </div>
	 <div class="row">
      <?php echo CHtml::label('chargecode','status_change');?>
      <?php echo CHtml::textField('chargecode','');?>
     
     </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>
    
    </fieldset>  

  <fieldset>
        <legend>add Item</legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'update-fastway',
	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
        'action' => $this->createUrl('calculate/addItem'),
)); ?>
     <div class="row">
         <input type='file' name='fw_file'/>
 </div>
<!--	 <div class="row">
      <?php echo CHtml::label('chargecode','status_change');?>
      <?php echo CHtml::textField('chargecode','');?>
     
     </div>-->

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>
    
    </fieldset>  
</div><!-- form -->
<!--<div style="float:right">
<a href="<?=$this->createUrl('calculate/calculate1')?>" >click</a>
</div>-->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('form#cn-id-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});

});
</script>