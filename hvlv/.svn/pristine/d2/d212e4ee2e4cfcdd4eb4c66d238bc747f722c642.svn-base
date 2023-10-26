<?php
$user = User::model()->findByPk(Yii::app()->user->id);
if (!empty($_GET['from']) && $_GET['from'] == 'stock') {
	$listlink = ['Stock List' => array('stock/list')];
} else {
	$listlink = ['Product List' => array('product/goods')];
}
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink'=>CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array_merge($listlink, array(
		'Update -'.$model->name,
	)),
));
$overview=$this->renderPartial('prod_overview',array('model'=>$model),true);
$package=$this->renderPartial('prod_package',array('model'=>$model),true);
$photos = $this->renderPartial('prod_photos', array('model' => $model), true);
?>
<h2><?=$this->t('Update Product');?> <?php echo $model->name; ?></h2>
<br>
<hr>

<?php $this->widget('application.extensions.booster.TbTabs',array(
	'type'=>'tabs',
	'tabs'=>array(
		array(
			'label'=>'Overview',
			'content'=>$overview,
			'active'=>true,
		 ),
		// array('label' => 'Package', 'content' =>$package,'itemOptions'=>array('id'=>$model->id.'prod_package')),
		array('label' => 'Photos', 'content' => $photos),
		array('label' => 'Logs', 'content' => 'Comming soon'),
		
		
	)
	
));
?>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	$('#wmsprod-tabs', tab.data('panel')).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});
});
</script>