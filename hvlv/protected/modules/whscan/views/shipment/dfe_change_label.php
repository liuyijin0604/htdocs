<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<center>
<h1><?=ucfirst(Yii::app()->session['scan_warehouse'])?> Warehouse</h1>
</center>
<?php
$tts = ['resort' => 'Resorting', 'checkin' => 'CheckIn', 'change' => 'Change Label'];
echo '<a style="float:right;font-size:1.2em;" href="'.Yii::app()->request->getUrl().'?cache=1">加速版</a><h1>拆单换单</h1>';
?>
</br>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', [
	'id'=>'scan-form',
	'enableAjaxValidation'=>false,
]);
?>
<div style="float:right;">
	<label id = "auto_print_label">Auto Print: <input type="checkbox" id="auto_print" name="auto_print" value="1" /></label> &nbsp; <select name="sound"><option value="">Default Sound</option><option value="1">中文女声</option><option value="2">中文男声</option></select></div>
<div style="font-size: 1.5em;">Barcode:<p id="uploading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p> <input style="width: 95%" id="scan" type="text" size="30" name="barcode" autocomplete="off" /></div>
<?php $this->endWidget(); ?>

<div id="result" style="background-color: white;margin-top:10px;">
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
<?php
echo CHtml::button('Print PDF Label', ['class' => 'print_btn_pdf','style'=> 'display:none;margin-left:20px;font-size:xx-large;']);
echo CHtml::label("PKG:","PKG:",['style'=> 'display:none;margin-left:20px;font-size:xx-large;width:2em;','id'=>'pkgl']);
echo CHtml::textField('pkg','', ['class' => 'pkg','style'=> 'display:none;margin-left:20px;font-size:xx-large;width:2em;','id'=>'pkg']);
echo CHtml::label("Pallet:","Pallet:",['style'=> 'display:none;margin-left:20px;font-size:xx-large;width:2.5em;','id'=>'palletl']);
echo CHtml::textField('pallet','', ['class' => 'pallet','style'=> 'display:none;margin-left:20px;font-size:xx-large;width:2em;','id'=>'pallet']);
echo CHtml::button('Save PKG&Pallet', ['class' => 'save_pallet','style'=> 'display:none;margin-left:20px;font-size:xx-large;','id'=>'save_pallet']);
?>
<input type="hidden" name="scanned-id" value="" id="scaned_shipment_id">
<input type="hidden" value="" id="my_sn">
<div id="print-result" style="margin-left: 20px;margin-top:10px;font-size: 1.5em;"></div>
	<table style="text-align: right;width:100%;">
		<tfoot>
			<tr><td><?php echo CHtml::button('Home', ['class' => 'gohome','style'=> 'text-align:right;1.5em']); ?></td></tr>
		</tfoot>
	</table>
</div>
<div class="printHelper_info" style="text-align: center;"></div>
<iframe id="pdf_label" style="display: none;" name="pdf_label" src="" ></iframe>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	$('input#scan').focus();
	$('#result tbody tr').hide();
	let ws = null;
	let printHelper = false;
	let printLog = [];

	function websocket_connect() {
		ws = new WebSocket("ws://127.0.0.1:10081");

		ws.onopen = function() {
			$('.printHelper_info').html('<b style="color:green">打印工具已开启</b>');
			printHelper = true;
		}

		ws.onclose = function (){
			printHelper = false;
			websocket_connect();
		}

		ws.onerror = function(e) {
			$('.printHelper_info').html('<b style="color:red">打印工具未开启</b> <a href="https://os.pcaex.com/PrintHelper.zip" target="_blank">(点击下载)</a>');
			printHelper = false;
		}

		ws.onmessage = function(msg) {
			// console.log(msg);
		}
	}

	$('#auto_print_label').on('change', function(){
		websocket_connect();
	});

	$(window).unload(function() {
		ws.close();
	});

	function uploading_on(obj) {
		obj.addClass('uploading');
		obj.val('    Uploading');
		obj.prop('disabled', 'disabled');
	}

	function uploading_off(obj) {
		obj.removeClass('uploading');
		obj.val('Submit');
		obj.removeProp('disabled');
	}

	var isSubmitting = false;
	var firstTime = true;
	$('form#scan-form').on('success', function(e,r){
		uploading_off($('#uploading'));
		isSubmitting = false;
		$('input#scan-code').val($('input#scan').val());
		$('input#scan').val('').focus();
		$('#print-result').html('');
		var audio=new Audio();
		audio.src='https://os.toplogistics.com.au/site/voice/' + (r.sounds.reverse().join('-')) + '.mp3';
		audio.play();
		$('#result tbody tr').hide();
		$(['status', 'area', 'msg', 'area', 'amazon', 'gatepass', 'hold']).each(function(i){
			if(r[this]) $('#result td.'+this).html(r[this]).parent().show();
		});

		if ( r.found == 1 ) {
			$('#scaned_shipment_id').val(r.id).data('sn', r.sn);
			$('#my_sn').val(r.sn);
		}

		if (r.print == 1) {
			$('.print_btn_pdf').show();
			$('#pallet').show();
			$('#pkg').show();
			$('#pkgl').show();
			$('#palletl').show();
			$('#pallet').val('');
			$('#pkg').val('');
			$('#save_pallet').show();
			if($('input#auto_print').prop('checked')) $('input.print_btn_pdf').trigger('click');
		} else {
			$('.print_btn_pdf').hide();
			$('#pallet').hide();
			$('#pkg').hide();
			$('#pkgl').hide();
			$('#palletl').hide();
			$('#save_pallet').hide();
		}

		$('.form').removeClass('red green blue').addClass(r.color);
		return true;
	}).on('submit', function(){
		$('#scaned_shipment_id').val('');
		$('#my_sn').val('');
		$('input#scan').focus();
		if(firstTime)
		{
			firstTime = false;
			return true;
		}

		if(isSubmitting)
		{
			return false;
		}
		isSubmitting = true;
		uploading_on($('#uploading'));
	});

	$('input#scan').on('focus', function(){
		$(this).select();
	});

	$('.gohome').click(function(e){
		window.location.href = '/whscan';
	});

	$('.print_btn_pdf').click(function(e){
		var sid = $('#scaned_shipment_id').val();
		var sn = $('#my_sn').val();
		if($.inArray(sid+"_"+sn, printLog) > -1){
			if(!window.confirm('Are you sure to reprint? 确认重复打印吗？')) return false;
		}else{
			if(printLog.length > 100) printLog.pop();
			printLog.unshift(sid+"_"+sn);
		}
		
		if(printHelper){
			$.get("<?php echo Yii::app()->createAbsoluteUrl("whscan/shipment/connoteZpl"); ?>"+"?helper=1&id="+sid+'&sn='+$('#scaned_shipment_id').data('sn'), function(r){
				ws.send(r.file);
			}, 'json');
			return;
		}
		var pdfFrame = window.frames["pdf_label"];
		$("#pdf_label").attr("src","<?php echo Yii::app()->createAbsoluteUrl("whscan/shipment/connoteZpl"); ?>"+"?id="+sid+'&sn='+$('#scaned_shipment_id').data('sn'));
		$("#pdf_label").load(function(){
			 pdfFrame.focus();
			 pdfFrame.print();
		});
	}); 


	$('.save_pallet').click(function(e){
		var sid = $('#scaned_shipment_id').val();
		var pallet = $('#pallet').val();
		var pkg = $('#pkg').val();
	
		$.get("<?php echo Yii::app()->createAbsoluteUrl("whscan/shipment/savePalletNumber"); ?>"+"?id="+sid+'&pallet='+pallet+'&pkg='+pkg, function(r){
		}, 'json');
		alert('done');
	});   

	if(navigator.userAgent.match(/Android|iPhone|iPad|iPod|SymbianOS|Windows Phone/) !== null || (window.screen.width < 500 && window.screen.height < 800)){ //Mobile
		$("#auto_print_label").hide();
	}

});
</script>
<?php $this->registerJS(ob_get_clean()); ?>