<h1><?=$this->t('Delivery Record');?></h1>

<?php 
$model = new DeliveryRecord('search');
$model->unsetAttributes();
if (isset($_GET['DeliveryRecord'])) {
	$model->attributes = $_GET['DeliveryRecord'];
}
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'delivery-record-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		'id',
		'rego',
		'plt',
		'note',
		'task',
		array('name' => 'damage', 'value' => '$data->getDamage()', 'filter' => CHtml::dropDownList('DeliveryRecord[damage]', $model->damage, $this->t(DeliveryRecord::$damage_states), array('prompt'=>$this->t('All'))),),
		array('name' => 'op_id', 'value' => 'empty($data->op) ? "" : $data->op->getName()'),
		'created',
		array(
			'class' => 'oButtonColumn',
			'template' => '{update} {file}',
			'buttons' => array(
				'update' => array(
					'imageUrl' => false,
					'options' => array('class' => 'jqm_link grid_edit_btn'),
					'url' => 'Yii::app()->createUrl("ediJob/deliveryUpdate", ["id" => $data->id])',
					'lable' => 'Update',
				),
				'file' => array(
					'imageUrl' => false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
					'url' => 'Yii::app()->createUrl("ediJob/deliveryRecordFiles", ["id" => $data->id])',
					'label' => 'Files',
				),
			),
		),
	),
)); ?>

<script type="text/javascript">
$(function() {
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');

	tab.bind('onOpen', function() {
		$('#delivery-record-grid', panel).yiiGridView('update');
	});
});
</script>