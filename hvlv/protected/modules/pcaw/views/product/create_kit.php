<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink'=>CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
		'Product List'=>array('product/goods'),
		'Create',
	),
));
?>
<h2><?=$this->t('Create Product Kit');?></h2>

<?php echo $this->renderPartial('_form_kit', array('model'=>$model)); ?>