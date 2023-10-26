<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<?php
$tts = ['rtsscan' => 'RTS'];
echo '<h1>KP RTS</h1>';
?>
</br>
<div class="form kpform">
	<?php $form=$this->beginWidget('CActiveForm', [
		'id'=>'scan-form-kp',
		'enableAjaxValidation'=>false,
	]);
	?>


	<div style="margin-bottom:15px;">
		<input class="barcode required form-control" type="text" name="shipment_kp" placeholder="Barcode" id="shipment_kp" autocomplete="off" />
	</div>

	<div class="input-group"><input id="location_kp" class="barcode required form-control" type="search" placeholder="Location only for putaway" name="location_kp" />
		<label class="input-group-addon"><input type="checkbox" class="klocation_kp" /> Lock</label>
	</div>

	<?php $this->endWidget(); ?>

<div id="result_kp" style="background-color: white;margin-top:10px;">
	<table id="items" class="table table-striped table-bordered" style="font-size: 1.5em;">
		<tbody>
			<tr><td class="status"></td></tr>
			<tr><td class="area"></td></tr>
			<tr><td class="msg"></td></tr>
			<tr><td class="gatepass"></td></tr>
			<tr><td class="console"></td></tr>
			<tr><td class="hold"></td></tr>
		</tbody>
	</table>
</div>
</br>

</div>


<input type="hidden" name="scanned-id-kp" value="" id="scaned_shipment_id_kp">
<input type="hidden" value="" id="my_sn_kp">
</div>

<br />
<div id="res"></div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function() {
	lastSubmit = true;


	$('#result_kp tbody tr').hide();


	function formAfterSuccess(r){
		var audio=new Audio();
		audio.src='https://os.toplogistics.com.au/site/voice/' + (r.sounds.reverse().join('-')) + '.mp3';
		audio.play();
		$('#result_kp tbody tr').hide();
		$(['status', 'area', 'msg', 'area', 'amazon', 'gatepass', 'hold']).each(function(i){
			if(r[this]) $('#result_kp td.'+this).html(r[this]).parent().show();
		});

		if ( r.found == 1 ) {
			$('#scaned_shipment_id_kp').val(r.id).data('sn', r.sn);
			$('#my_sn_kp').val(r.sn);
			if(r.id!=0)
			{
				lastSubmit = false;
			}
		}

		if (r.print == 1) {
			$('.print_btn_pdf').show();
			if($('input#auto_print').prop('checked')) $('input.print_btn_pdf').trigger('click');
		} else {
			$('.print_btn_pdf').hide();
		}

		$('#shipment_kp').val('');
		$('.kpform').removeClass('red green blue').addClass(r.color);
		if(!$('input.klocation_kp').prop('checked')) $('input#location_kp').val('');

		return true;
	}
	function formOnSubmit()
	{
		$('#scaned_shipment_id_kp').val('');
		$('#my_sn_kp').val('');
		$('input#scan').focus();
	};


	$('input#shipment_kp').focus().on('keydown', function(e) {
		if (e.which == 13) {
			if(lastSubmit==false)
			{
				$('input#shipment_kp').val("");
				alert("please submit reason/photo before scaning next parcel.");
				return false;
			}
			formOnSubmit();
			$(this).trigger('afterBarcode');
			$('input#shipment_kp').focus();
			return false;
		}
	}).on('afterBarcode', function() {
		$.ajax({
			'url': '<?=$this->createUrl('rtsProcess/scan')."?op=".$op?>',
			'type': 'POST',
			'data': { 'barcode': $('input#shipment_kp').val(), 'location': $('input#location_kp').val()},
			success: function(r) {
				r = JSON.parse(r);
				formAfterSuccess(r);
			}
		});
		return false;
	});


});
</script>
<?php $this->registerJS(ob_get_clean()); ?>


<script type="text/javascript">
$(function() {
    

});
</script>