<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'produp-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' =>[
		'data-bit' => '1',
	]
));
?>
	<h2>SKU: <?=$model->sku;?></h2>

	<div class="form-group">
		<?php echo $form->labelEx($model,'name');?>
		<?php echo $form->textField($model,'name',array('size'=>10,'maxlength'=>100, 'class' => 'form-control')); ?>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($model,'sn');?>
		<?php echo $form->textField($model,'sn',array('size'=>10,'maxlength'=>30, 'class' => 'form-control')); ?>
	</div>
	
	<div class="form-group buttons" style="text-align:right">
		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t('Save');?></button>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#produp-form').on('success', function(e,r){
		$('#modal').modal('hide');
		$('#prod_list').yiiGridView('update');
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>