<?php
/* @var $this AutoCommandsController */
/* @var $data AutoCommands */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('command_name')); ?>:</b>
	<?php echo CHtml::encode($data->command_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('func_name')); ?>:</b>
	<?php echo CHtml::encode($data->func_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('day')); ?>:</b>
	<?php echo CHtml::encode($data->day); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('week_day')); ?>:</b>
	<?php echo CHtml::encode($data->week_day); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('hour')); ?>:</b>
	<?php echo CHtml::encode($data->hour); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('minute')); ?>:</b>
	<?php echo CHtml::encode($data->minute); ?>
	<br />


</div>