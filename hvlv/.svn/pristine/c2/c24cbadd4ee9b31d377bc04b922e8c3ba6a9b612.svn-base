<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<center>
<h1><?=ucfirst(Yii::app()->session['scan_warehouse'])?>  Warehouse</h1>
</center>

<h1>Put Away</h1>
<!-- <p>Not put away today: <a  id="strCount"><?php //echo $count ?></a> Updated at: <a id=strTime><?php //echo $time ?></a></p> -->
<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'sortheld-form',
		'enableAjaxValidation' => false,
	)); ?>
	
	<div style="margin-bottom:15px;">
		<input class="barcode required form-control" type="text" name="pallet_label" placeholder="pallet label" id="pallet_label" autocomplete="off" />
	</div>

	<div class="input-group"><input id="ground_label" class="barcode required form-control" type="search" placeholder="ground label" name="ground_label" />
		<label class="input-group-addon"><input type="checkbox" class="lock_ground_label" /> Lock</label>
	</div>


	<?php $this->endWidget(); ?>
</div>
<br />
<div id="res"></div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {

	$('input#pallet_label, input#ground_label').focus().on('keydown', function(e) {
		if (e.which == 13) {
			$(this).trigger('afterBarcode');
			return false;
		}
	}).on('afterBarcode', function(){
		if ($('input#ground_label').length == 0 || $('input#ground_label').val() == '') {
			$('input#ground_label').focus();
		} else {
			$.ajax({
				'url': 'putawaypallet',
				'type': 'POST',
				'data': { 'pallet_label': $('input#pallet_label').val(), 'ground_label': $('input#ground_label').val() },
				success: function(r) {
					r = JSON.parse(r);
					if (r['success'] == true) {
						$('#res').html('<span style="color:green; font-size: 2em;">' + r['msg'] + '</span>');
						var audio=new Audio();
						audio.src='https://os.toplogistics.com.au/site/voice/confirmed.mp3';
						audio.play();
						$('#strCount').text(r['count']);
						$('#strTime').text(r['time']);
					} else {
						$('#res').html('<span style="color:red; font-size: 2em;">' + r['msg'] + '</span>');
					}
					$('input#pallet_label').val('').focus();
					if(!$('input.lock_ground_label').prop('checked')) $('input#ground_label').val('');
				}
			});
		}
		return false;
	});

});
</script>
<?php $this->registerJS(ob_get_clean()); ?>