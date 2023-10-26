<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<h3>Product</h3>
	<div class="row rowcol rowleft">
		<label>Name</label>
		<?php echo $form->textField($model,'prod_name'); ?>
	</div>
	<div class="row rowcol">
		<label>EAN/SKU</label>
		<?php echo $form->textField($model,'prod_sku'); ?>
	</div>
<h3 style="clear: both;">Delivery</h3>
	<div class="row rowcol rowleft">
		<label>Connote No.</label>
		<?php echo $form->textField($model,'delivery_connote'); ?>
	</div>
	<div class="row rowcol">
		<label>Name</label>
		<?php echo $form->textField($model,'delivery_name'); ?>
	</div>
	<div class="row rowcol">
		<label>Tel</label>
		<?php echo $form->textField($model,'delivery_tel'); ?>
	</div>
	<div class="row rowcol">
		<label>Postcode</label>
		<?php echo $form->textField($model,'delivery_postcode'); ?>
	</div>
<h3 style="clear: both;">Bulk Ref / ID</h3>
	<div class="row">
			<?php echo $form->label($model,'refs'); ?>
			<?php echo $form->textArea($model,'refs',array('rows'=>6, 'cols'=>50)); ?>
			<p><small>Up to 200 numbers.</small></p>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->