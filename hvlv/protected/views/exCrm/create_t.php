<h3>创建信息缺失票</h3>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shipment-crmnotes-form',
	'enableClientValidation'=>true,
	
));
$model->type=20;
$model->source=1;
?>
        <?php  echo $form->hiddenField($model,'type');?>
        <?php  echo $form->hiddenField($model,'source');?>
    <div class="row" >
        <?php  echo CHtml::label('告诉客服什么信息缺失：','notes');?>
	<?php echo CHtml::textArea('notes', '', array('rows'=>4, 'cols' => 60)); ?>
    </div>

    <div class="row buttons">
	<?php echo CHtml::submitButton('Create'); ?>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
<script>
 $(function(){
     var tab=$('#<?=$_GET['tabid']?>');
     var panel=tab.data('panel');

     });
 
</script>