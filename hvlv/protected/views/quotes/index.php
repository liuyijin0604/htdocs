<?php
/* @var $this QuotesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Quotes',
);

$this->menu=array(
	array('label'=>'Create Quotes', 'url'=>array('create')),
	array('label'=>'Manage Quotes', 'url'=>array('admin')),
);
?>

<h1>Quotes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
