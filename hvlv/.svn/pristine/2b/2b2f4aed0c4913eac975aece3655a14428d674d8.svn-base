<div class="form">
<?php 
$form=$this->beginWidget('CActiveForm',array(
    'id'=>'err_record_form'.$_GET['tabid'],
    'enableAjaxValidation'=>false,     
));?>
<div class="row">
    <?php echo CHtml::label('Error Reasons','err_reasons'); ?>
<?php
$selectedReason = [];
if (!empty($model->err_record)) {
    foreach (array_keys(ExParcel::$errTypes) as $key) {
        if (($model->err_record->record & $key) > 0) {
            $selectedReason[] = $key;
        }
    }
}
echo CHtml::checkBoxList('err_reasons', $selectedReason, ExParcel::$errTypes,array(
                    'template'=>'{input}{label}',
                    'separator'=>'',
                    'labelOptions'=>array(
                        'style'=> 'padding-right:8px;min-width: 60px;float: left;'),
                    'style'=>'float:left;',) );
                ?>
 </div>
  
    <br>
    <div class="button" style="clear:both;">
    <?php echo CHtml::submitButton('submit')?>
</div>

<?php $this->endWidget();?>
</div>



