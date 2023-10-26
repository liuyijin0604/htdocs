<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<center>
<h1><?=ucfirst(Yii::app()->session['scan_warehouse'])?>  Warehouse</h1>
</center>

<h1>Reship</h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'reship-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div style="font-size: 1.5em;">
		Shipment: <input style="width: 95%;" size="30" type="text" name="shipment" id="shipment" autocomplete="off" />
	</div>

	<?php $this->endWidget(); ?>
</div>
<br />
<div id="res"></div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
	$('input#shipment').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function() {
		$.ajax({
			'url': 'reship',
			'type': 'POST',
			'data': { 'shipment': $('input#shipment').val() },
			success: function(r) {
				r = JSON.parse(r);
				if (r['success'] == true) {
					$('#res').html('<span style="color:green; font-size: 2em;">' + r['msg'] + '</span>');
					$('input#shipment').val('').focus();
				} else {
					$('#res').html('<span style="color:red; font-size: 2em;">' + r['msg'] + '</span>');
				}
			}
		});
		return false;
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>