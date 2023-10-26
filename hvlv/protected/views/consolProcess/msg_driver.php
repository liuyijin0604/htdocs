<h3>Message To Driver</h3>
<div class="form">
<?php 
$form=$this->beginWidget('CActiveForm',array(
    'id'=>'clear_log_form',
    'enableAjaxValidation'=>false,
     
));
?>
    <div class="row rowcol-left">
    <?php echo CHtml::label('Select Driver','select_driver'); ?>
    <?php echo CHtml::dropDownList('driver_select',@$model->status, ConsolProcess::$driver_tel_no); ?>
    <?php echo CHtml::textField('driver_tel_no','' ); ?>
 
</div>
<div class="row">
   <?php echo CHtml::textArea('msg_to_driver',@$model->mdata['msg_to_driver'],array('rows'=>5, 'cols' => 60, "maxlength"=>153)); ?>
 </div>

<div class="button">
    <?php echo CHtml::submitButton('Send')?>
</div>

<?php $this->endWidget();?>
</div></div>

<script>
    $(function(){
        var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = $('#<?=$_GET["tabid"];?>').data('panel');
        
        
        $("#driver_tel_no",win).val($("#driver_select",win).val());  
        $("#driver_select",win).on('change',function(){
           $("#driver_tel_no",win).val($(this).val());     
        });
           
    });
</script>
