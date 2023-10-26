<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink'=>CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
		'Product List'=>array('product/goods'),
		'Update',
	),
));
?>
<h2><?=$this->t('Update Product Kit');?></h2>

<?php echo $this->renderPartial('_form_kit', array('model'=>$model)); ?>