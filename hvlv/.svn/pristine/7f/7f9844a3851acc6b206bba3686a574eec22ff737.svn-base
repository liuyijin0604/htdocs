<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
	'Return List' => array('return/list'),
		'Update',
	),
));
?>
<h1>Update Return - <?=$model->getNo() . ' ' . $model->getReturnStatus()?></h1>
<?php echo $this->renderPartial('_form', array('model' => $model)); ?>