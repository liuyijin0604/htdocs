<div class="form">
<?php 
$form=$this->beginWidget('CActiveForm',array(
    'id'=>'clear_reason_form',
    'enableAjaxValidation'=>false,     
));?>
<div class="row" style="height:80px;">
    <input type="hidden" value="1" name="clear_held_flag"/>
    <?php echo CHtml::label('Held Reason','held_reasons'); ?>
<?php
                $SelectedReason=[];
                if(($model->bwf&4)>0) $SelectedReason[] = 4;
                if(($model->bwf&8)>0) $SelectedReason[] = 8;
                if(($model->bwf&16)>0) $SelectedReason[] =16;
                if(($model->bwf&32)>0) $SelectedReason[] =32;
                if(($model->bwf&64)>0) $SelectedReason[] =64;
                if(($model->bwf&128)>0) $SelectedReason[] =128;
                if(($model->bwf&512)>0) $SelectedReason[] =512;
                if(($model->bwf&1024)>0) $SelectedReason[] =1024;
                if(($model->bwf&2048)>0) $SelectedReason[] =2048;
                if(($model->bwf&8192)>0) $SelectedReason[] =8192;
                if(($model->bwf&16384)>0) $SelectedReason[] =16384;
                if(($model->bwf&2)>0)  $SelectedReason[] =2;
                if(($model->bwf&32768)>0)  $SelectedReason[] =32768;
                if(($model->bwf&65536)>0)  $SelectedReason[] =65536;
                if(($model->bwf&131072)>0)  $SelectedReason[] =131072;
                
                $clearReasons= array(
                           4 => 'High Value',
                           8 => 'Non-Sac',
                           16=>'Border',
                           32=>'AQIS',
                           64=>'D D P',
                           128=>'D D U'
                );
               echo CHtml::checkBoxList('clear_reasons', $SelectedReason, $clearReasons,array(
                    'template'=>'{input}{label}',
                    'separator'=>'',
                    'labelOptions'=>array(
                        'style'=> 'padding-right:8px;min-width: 60px;float: left;'),
                    'style'=>'float:left;',) );
                ?>
            </br>
<?php
                
                $clearReasons= array(
                           512=>'E M P P',
                           1024=>'O T H E R',
                           // 2 => '商壹',
                           // 2048=>'海运散货',
                           8192 => '自清',
                           16384 => 'SAC',
                           32768 => 'UPE',
                           65536 => 'INS',
                           131072 => 'DIS'
                );
               echo CHtml::checkBoxList('clear_reasons', $SelectedReason, $clearReasons,array(
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



