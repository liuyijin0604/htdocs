<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Direct Orders' => array('direct/manage'),
		'New',
	),
));
?>

<h1>New Order</h1>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#shipment-form').on('success', function(e,r){
		if(r.done && r.id > 0){
			posApp.toPage(posApp.baseUrl+"direct/create", false);
		}
	});
});
</script>
<?php $this->registerJS(ob_get_clean(),2); ?>