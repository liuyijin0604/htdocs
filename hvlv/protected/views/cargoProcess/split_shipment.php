<h2>Split Shipment</h2>
<div class="form" method="POST">
	<?php
		$form=$this->beginWidget('CActiveForm', array(
			'id'=>'cargo-process-split-shipment_form',
			'enableAjaxValidation'=>false)
		);
	?>
	<div class="row">
		<?php echo CHtml::label('Original Ref(Only original shipment can be used for spliting','Original Ref'); ?>
		<?php echo CHtml::textField('ref',''); ?>
 	</div>

	<div class="row">
		<?php echo CHtml::label('packages','packages'); ?>
		<P>For example, if there are left 100 packages of the original parcel, input 20. the original one will become ref having 80 packages and generate ref-A having 20 packages</P>
		<?php echo CHtml::numberField('packages',0); ?>
 	</div>

<!--  	<div class="row">
 		<p>You can only use one of the packages or pallet to split shipment</p>
		<?php echo CHtml::label('pallets','pallets'); ?>
		<P>For example, if there are 3 pallets of the original parcel, input 2,1,1. the original one will become ref-A having 2 pallets and generate ref-B having 1 pallet and ref-C having 1 pallet</P>
		<?php echo CHtml::textField('pallets','pallets'); ?>
 	</div> -->
 	<?php echo CHtml::submitButton("confirm");?>
</div>
<?php $this->endWidget();?>
