<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<center>
<h1><?=ucfirst(Yii::app()->session['scan_warehouse'])?>  Warehouse</h1>
</center>

<h1>Put Away RTS</h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'putaway-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div style="margin-bottom:15px;">
		<input class="barcode required form-control" type="text" name="shipment" placeholder="Shipment" id="shipment" autocomplete="off" />
	</div>

	<div class="input-group"><input id="location" class="barcode required form-control" type="search" placeholder="Location only for putaway" name="location" />
		<label class="input-group-addon"><input type="checkbox" class="klocation" /> Lock</label>
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
			$('input#shipment').focus();
			return false;
		}
	}).on('afterBarcode', function() {
		$.ajax({
			'url': 'status',
			'type': 'POST',
			'data': { 'shipment': $('input#shipment').val(), 'putaway': 1},
			success: function(r) {
				r = JSON.parse(r);
				if (r['success'] == true) {
					$('#res').html('<span style="color:green; font-size: 2em;">' + r['msg'] + '</span>');
				} else {
					$('#res').html('<span style="color:red; font-size: 2em;">' + r['msg'] + '</span>');
				}
				var audio = new Audio();
				audio.src = 'https://os.toplogistics.com.au/site/voice/' + r['sound'] + '.mp3';
				audio.play();
				if (r['sound'] != 'rts_received' && r['sound'] != '3pl_rts_received') {
					$('input#shipment').val('');
					$('input#shipment').focus();
				}
			}
		});

		if ($('input#location').length == 0 || $('input#location').val() == '') {
			$('input#location').focus();
		} else {
			$.ajax({
				'url': 'putaway',
				'type': 'POST',
				'data': { 'shipment': $('input#shipment').val(), 'location': $('input#location').val() },
				success: function(r) {
					r = JSON.parse(r);
					if (r['success'] == true) {
						$('#res').html('<span style="color:green; font-size: 2em;">' + r['msg'] + '</span>');
					} else {
						$('#res').html('<span style="color:red; font-size: 2em;">' + r['msg'] + '</span>');
					}
					$('input#shipment').val('').focus();
					if(!$('input.klocation').prop('checked')) $('input#location').val('');
				}
			});
		}
		return false;
	});

	$('input#location').on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function() {
		if ($('input#shipment').length == 0 || $('input#shipment').val() == '') {
			$('input#shipment').focus();
		} else {
			$.ajax({
				'url': 'putaway',
				'type': 'POST',
				'data': { 'shipment': $('input#shipment').val(), 'location': $('input#location').val() },
				success: function(r) {
					r = JSON.parse(r);
					if (r['success'] == true) {
						$('#res').html('<span style="color:green; font-size: 2em;">' + r['msg'] + '</span>');
					} else {
						$('#res').html('<span style="color:red; font-size: 2em;">' + r['msg'] + '</span>');
					}
					$('input#shipment').val('').focus();
					if(!$('input.klocation').prop('checked')) $('input#location').val('');
				}
			});
		}
		return false;
	});
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>