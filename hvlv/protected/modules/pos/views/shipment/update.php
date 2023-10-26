<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Shipments' => array('shipment/manage'),
		'Update  - '.$model->hbn,
	),
));
?>

<h1>Update Shipment - <?=$model->hbn;?></h1>
<?php echo $this->renderPartial($type.'_form', array('model'=>$model, 'org' => $org)); ?>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#shipment-form').on('success', function(e,r){
		if(r.done && (r.status == 8 || r.status == 10) && r.id > 0){
			bootbox.confirm("Would you want to print label now?", function(result) {
				if(result)
				<?php if(empty($model->agent->extra['printer_id'])):?>
					window.open(posApp.baseUrl+"shipment/print/"+r.id+"/"+r.hbn+".pdf");
				<?php else: ?>
					$.get(posApp.baseUrl+'client/print/'+r.id+'?c='+r.hbn);
				<?php endif; ?>
			});
		}
		posApp.toPage(posApp.baseUrl+"shipment/manage", true);
	});
});
</script>
<?php $this->registerJS(ob_get_clean(),2); ?>