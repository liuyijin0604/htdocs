<?php
/* @var $this GoogleReviewController */
/* @var $data GoogleReview */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('hbn')); ?>:</b>
	<?php echo CHtml::encode($data->hbn); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ref')); ?>:</b>
	<?php echo CHtml::encode($data->ref); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('time_start')); ?>:</b>
	<?php echo CHtml::encode($data->time_start); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('time_done')); ?>:</b>
	<?php echo CHtml::encode($data->time_done); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('cnee_name')); ?>:</b>
	<?php echo CHtml::encode($data->cnee_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('cnee_email')); ?>:</b>
	<?php echo CHtml::encode($data->cnee_email); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('cnee_tel')); ?>:</b>
	<?php echo CHtml::encode($data->cnee_tel); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('cnor_city')); ?>:</b>
	<?php echo CHtml::encode($data->cnor_city); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('cnor_state')); ?>:</b>
	<?php echo CHtml::encode($data->cnor_state); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pkg')); ?>:</b>
	<?php echo CHtml::encode($data->pkg); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('weight')); ?>:</b>
	<?php echo CHtml::encode($data->weight); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('time_arrived')); ?>:</b>
	<?php echo CHtml::encode($data->time_arrived); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('shipment_id')); ?>:</b>
	<?php echo CHtml::encode($data->shipment_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('cargo_process_id')); ?>:</b>
	<?php echo CHtml::encode($data->cargo_process_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	*/ ?>

</div>