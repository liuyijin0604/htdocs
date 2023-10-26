<?php
/* @var $this AutoCommandLogController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Auto Command Logs',
);

$this->menu=array(
	array('label'=>'Create AutoCommandLog', 'url'=>array('create')),
	array('label'=>'Manage AutoCommandLog', 'url'=>array('admin')),
);
?>

<h1>Auto Command Logs</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
