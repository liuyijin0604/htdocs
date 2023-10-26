<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'links' => array(
		'Shipments' => array('shipment/manage'),
		'Tracking',
	),
));
?>
<h1>Shipment Tracking</h1>
<form method="get" action="<?=$this->createUrl('shipment/tracking');?>" id="tracking_form" data-bit="1">
<div class="form-group">
<textarea class="form-control input-lg" wrap="hard" id="track_code" name="c"></textarea>
</div>
<div class="form-group">
<input class="btn btn-primary btn-lg" type="submit" value="Tracking »">
</div>
</form>
<div id="result" style="padding: 20px 0;"></div>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('#tracking_form').ajaxForm({
		success: function(r) {
			$('#result').stop().hide().html(r).slideDown();
		},
		beforeSubmit: function(a,f,o){
			return $('#track_code').val() != '';
		},
		dataType: 'html'
	}).data('ajaxf', true);
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>