<?php
/* @var $this WordReplaceUsageLogController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Word Replace Usage Logs',
);

$this->menu=array(
	array('label'=>'Create WordReplaceUsageLog', 'url'=>array('create')),
	array('label'=>'Manage WordReplaceUsageLog', 'url'=>array('admin')),
);
?>

<h1>Word Replace Usage Logs</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
