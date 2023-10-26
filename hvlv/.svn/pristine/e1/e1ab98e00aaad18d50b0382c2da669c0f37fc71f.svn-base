<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Manual Receive' => array('warehouse/manualin'),
		'Update  - '.$model->hbn,
	),
));
?>

<h1>Update Shipment - <?=$model->hbn;?></h1>
<?php echo $this->renderPartial('im_form', array('model'=>$model)); ?>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#shipment-form').on('success', function(e,r){
		posApp.toPage(posApp.baseUrl+"warehouse/manualin", true);
	});
});
</script>
<?php $this->registerJS(ob_get_clean(),2); ?>