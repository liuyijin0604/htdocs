<div class="form">
<fieldset>
        <legend>Import WCA Members</legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'update-fastway',
//	'htmlOptions'=>['target'=>'_blank','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	'enableAjaxValidation'=>false,
)); ?>
 <div class="row">
       <label for='wca_members'>Excel File- <small>.csv/.xls/.xlsx File (<a href="/ims/wca_members_template.xlsx" target="_blank">Get template file</a>)</label><br></small></label>
       <input type='file' name='wca_members'/>
 </div>
   <div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
   </div>
 
<?php $this->endWidget(); ?>
    
    </fieldset>   
</div>