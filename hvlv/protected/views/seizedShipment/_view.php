<?php
/* @var $this SeizedShipmentController */
/* @var $data SeizedShipment */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('shipment_id')); ?>:</b>
	<?php echo CHtml::encode($data->shipment_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('consol_id')); ?>:</b>
	<?php echo CHtml::encode($data->consol_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('create_time')); ?>:</b>
	<?php echo CHtml::encode($data->create_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('shipment_status')); ?>:</b>
	<?php echo CHtml::encode($data->shipment_status); ?>
	<br />


</div>