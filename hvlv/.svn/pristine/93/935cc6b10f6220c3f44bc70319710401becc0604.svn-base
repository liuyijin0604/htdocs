<h1><?=$this->t('Int\'l Zone Maps');?></h1>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'zone-map-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(),
	'ajaxUrl' => Yii::app()->request->url,
	'filter' => $model,
	'columns' => array(
		array('name' => 'z1', 'header' => 'Zone Code'),
		'zone_name',
		array('name' => 'z2', 'header' => 'Country Code', 'value' => '$data->z2'),
		array('name' => 'z2_name', 'header' => 'Country Name', 'value' => '$data->z2_name'),
	),
)); ?>

<script type="text/javascript">
$(function() {
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	tab.bind('onOpen', function() {
		$('#zone-map-grid', panel).yiiGridView('update');
	});
});
</script>
