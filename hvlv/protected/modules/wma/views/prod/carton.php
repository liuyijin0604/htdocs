<div class="content-padded ">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-pcarton-form',
	'enableAjaxValidation'=>false,
));

echo '<div class="row">Name: '.$prod->name.'</div>';
echo '<div class="row">EAN: <b>'.$prod->ean.'</b></div>';
echo '<div class="row">Brand: '.$prod->brand.'</div>';
echo '<div class="row">Model: '.$prod->model.'</div>';
echo $form->textField($model,'barcode',array('size'=>60,'maxlength'=>100, 'placeholder' => 'Carton Barcode'));
echo $form->textField($model,'qty',array('size'=>60,'maxlength'=>100, 'placeholder' => $model->getAttributeLabel('qty')));
?>
<div class="row input-addon">
<?php
echo CHtml::textField('dim[w]', @$model->dims['w'], array('size'=>10,'maxlength'=>10, 'placeholder' => 'W', 'style' => 'width: 33.33%')),
	CHtml::textField('dim[h]', @$model->dims['h'], array('size'=>10,'maxlength'=>10, 'placeholder' => 'H', 'style' => 'width: 33.33%')),
	CHtml::textField('dim[d]', @$model->dims['d'], array('size'=>10,'maxlength'=>10, 'placeholder' => 'D', 'style' => 'width: 33.33%'));
?>
</div>

<div class="input-addon"><?php echo $form->textField($model,'weight', array('size'=>15, 'placeholder' => $model->getAttributeLabel('weight'))); ?><span>kg</span></div>

<button type="submit" class="btn btn-primary btn-block"><?php echo $this->t($model->isNewRecord ? 'Create' : 'Save'); ?></button>
<?php $this->endWidget(); ?>
</div>
