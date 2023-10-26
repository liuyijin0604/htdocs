<h1>Return</h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'wmstask-delivery-return',
		'enableAjaxValidation' => false,
	)); ?>

	<?php
	$item = new WmsTaskItem;
	$item->task_id = $model->id;
	$this->widget('zii.widgets.grid.CGridView', array(
		'id' => 'wmstask-delivery-return',
		'selectableRows' => 2,
		'cssFile' => false,
		'dataProvider' => $item->search(),
		// 'filter' => $item,
		'columns' => array(
			array(
				'id' => 'selectedItems',
				'class' => 'CCheckBoxColumn',
			),
			array('name' => 'prod', 'value' => '$data->mdata["sn"]'),
		),
	)); ?>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Type', 'type'); ?>
		<?php echo CHtml::radioButtonList('type', 1, [1 => '入库', 2 => '重派'], array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp;')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>