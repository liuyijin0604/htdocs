<h2>Export Amazon</h2>
<div class="form">
<?php 
$form=$this->beginWidget('CActiveForm',array(
    'id'=>'amazon_export_form'.$_GET['tabid'],
    'enableAjaxValidation'=>false,
    'htmlOptions'=>['target'=>'amazon_export_result','class'=>'ifrm-form','enctype'=>"multipart/form-data"],
     
));?>
<div class="row">
     <?php echo CHtml::label('Direct Consol/shipments(;,或者换行)','shipments'); ?>
     <?php echo CHtml::textArea('shipments','',array('rows'=>2, 'cols' => 60)); ?>
 </div>
    <?php echo CHtml::hiddenField('amazon_id',$_GET['tabid'])?>
<div class="button">
    <?php echo CHtml::submitButton('Export')?>
</div>
    <iframe name="amazon_export_result" id="amazon_export_result<?=$_GET['tabid']?>" style="border: 1px solid black; width:80%;height:100px;margin-top: 20px;"></iframe>

<?php $this->endWidget();?>
</div>
<script>
    $(function(){
        var tab=$('#<?=$_GET['tabid']?>');
        var panel=tab.data('panel');
        $('form#amazon_export_form<?=$_GET['tabid']?>',panel).on('submit',function(){
           $('#amazon_export_result<?=$_GET['tabid']?>',panel).contents().find('body').html('');
            if(!$('#shipments',panel).val()){
               alert('Please input the consol No.');
               return false;
            }
        });
 });
</script>