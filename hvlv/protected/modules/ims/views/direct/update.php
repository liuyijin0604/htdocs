<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Orders' => array('direct/manage'),
		'Update  - '.$model->hbn,
	),
));
?>

<h1>Update Shipment - <?=$model->hbn;?></h1>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#shipment-form').on('success', function(e,r){
		posApp.toPage(posApp.baseUrl+"direct/manage", true);
	});
});
</script>
<?php $this->registerJS(ob_get_clean(),2); ?>