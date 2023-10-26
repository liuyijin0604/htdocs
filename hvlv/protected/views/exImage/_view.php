<?php
/* @var $this ExImageController */
/* @var $data ExImage */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('file_adr')); ?>:</b>
	<?php echo CHtml::encode($data->file_adr); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('hbn')); ?>:</b>
	<?php echo CHtml::encode($data->hbn); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('islinked')); ?>:</b>
	<?php echo CHtml::encode($data->islinked); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('agent_id')); ?>:</b>
	<?php echo CHtml::encode($data->agent_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pdf_number')); ?>:</b>
	<?php echo CHtml::encode($data->pdf_number); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('date')); ?>:</b>
	<?php echo CHtml::encode($data->date); ?>
	<br />


</div>