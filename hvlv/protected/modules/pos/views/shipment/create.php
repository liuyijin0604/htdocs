<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Shipments' => array('shipment/manage'),
		'New',
	),
));
?>

<h1>New Shipment</h1>
<?php echo $this->renderPartial($type.'_form', array('model'=>$model, 'org'=>$org)); ?>

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#shipment-form').on('success', function(e,r){
		if(r.done && r.id > 0){
			if(r.print == 1){
				bootbox.confirm("Would you want to print label now?", function(result) {
					if(r.ids.length > 1){
						if(result) window.open(posApp.baseUrl+"shipment/bulkPrint/"+r.ids.join(',')+"/multi.pdf");
					}else{
						if(result) window.open(posApp.baseUrl+"shipment/print/"+r.id+"/"+r.hbn+".pdf");
					}
				});
				posApp.toPage(posApp.baseUrl+"shipment/create", false);
			}else if(r.print == 2){
				if(r.ids.length > 1){
					window.open(posApp.baseUrl+"shipment/print2.html?ids="+r.ids.join(','), '_blank');
				}else{
					window.open(posApp.baseUrl+"shipment/print2.html?ids="+r.id, '_blank');
				}
				$('#ExParcel_hbn').focus();
			}
		}
	});
});
</script>
<?php $this->registerJS(ob_get_clean(),2); ?>