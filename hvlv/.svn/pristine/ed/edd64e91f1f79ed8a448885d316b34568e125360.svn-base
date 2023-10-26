<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-prod-form',
	'enableAjaxValidation'=>false,
)); ?>

<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>100, 'placeholder' => $model->getAttributeLabel('name'))); ?>
<?php echo $form->textField($model,'name_zh',array('size'=>60,'maxlength'=>100, 'placeholder' => $model->getAttributeLabel('name_zh'))); ?>
<?php echo $form->textField($model,'ean',array('size'=>50,'maxlength'=>50, 'placeholder' => $model->getAttributeLabel('ean'))); ?>
<?php echo $form->textField($model,'brand',array('size'=>60,'maxlength'=>100, 'placeholder' => $model->getAttributeLabel('brand'))); ?>
<?php echo $form->textField($model,'model',array('size'=>60,'maxlength'=>100, 'placeholder' => $model->getAttributeLabel('model'))); ?>
<div class="row input-addon">
<?php
echo CHtml::textField('dim[w]', @$model->dims['w'], array('size'=>10,'maxlength'=>10, 'placeholder' => 'W', 'style' => 'width: 33.33%')),
	CHtml::textField('dim[h]', @$model->dims['h'], array('size'=>10,'maxlength'=>10, 'placeholder' => 'H', 'style' => 'width: 33.33%')),
	CHtml::textField('dim[d]', @$model->dims['d'], array('size'=>10,'maxlength'=>10, 'placeholder' => 'D', 'style' => 'width: 33.33%'));
?>
</div>

<div class="input-addon"><?php echo $form->textField($model,'cbm', array('size'=>15, 'placeholder' => $model->getAttributeLabel('cbm'))); ?><span>cm<sup>3</sup></span></div>
<div class="input-addon"><?php echo $form->textField($model,'weight', array('size'=>15, 'placeholder' => $model->getAttributeLabel('weight'))); ?><span>g</span></div>

<button type="submit" class="btn btn-primary btn-block"><?php echo $this->t($model->isNewRecord ? 'Create' : 'Save'); ?></button>
<?php $this->endWidget(); ?>

