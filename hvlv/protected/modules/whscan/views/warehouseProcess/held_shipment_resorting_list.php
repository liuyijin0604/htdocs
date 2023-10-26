<h1>旧货 Resorting</h1>
<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<br>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', [
	'id'=>'resorting-scan-form',
	'action'=>$this->createUrl('shipment/scan')."?op=resort",
	'enableAjaxValidation'=>false,
]);
?>
<label style="float: left;font-size: 2em;">Resorting:</label><div style="float:right;"><label id="unpacking_label">PalletScan: <input type="checkbox" id="palletScan" name="palletScan" value="1" />&nbsp;&nbsp;

 <label id = "auto_print_label_resort">Auto Print: <input type="checkbox" id="auto_print" name="auto_print" value="1" /></label> &nbsp; <select name="sound"><option value="">Default Sound</option><option value="1">中文女声</option><option value="2">中文男声</option></select></div>
<div style="font-size: 1.5em;"><input style="width: 95%" id="resorting-scan" type="text" size="30" name="barcode" autocomplete="off" value="<?=@$barcode?>" /></div>
<br>
<br>
<div id="resorting-result" style="background-color: white;margin-top:10px;">
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
echo CHtml::button('Print PDF Label', ['class' => 'resorting_print_btn_pdf','style'=> 'display:none;margin-left:20px;font-size:xx-large;']);
?>
<input type="hidden" name="scanned-id" value="" id="scaned_shipment_id_resort">
<input type="hidden" value="" id="my_sn_resort">
<div id="print-result" style="margin-left: 20px;margin-top:10px;font-size: 1.5em;"></div>
	<table style="text-align: right;width:100%;">
	</table>
</div>
<div class="printHelper_info_resort" style="text-align: center;"></div>
<iframe id="pdf_label_resort" style="display: none;" name="pdf_label_resort" src="" ></iframe>
<?php $this->endWidget(); ?>


<div id = "preparation_content">
	<?php echo $this->renderPartial('loading_list', array('url'=>"warehouseProcess/getHeldShipmentGatepassList")); ?>
</div>

<!-- <iframe src="http://localhost/hvlv/gapsig/gatePass/needProcessParcelList.app?render=partial" style="width: 100%;height:80em;">
	

</iframe> -->
<script type="text/javascript">
	$(function(){
	let ws = null;
	let resortprintHelper = false;
	let resortprintLog = [];

	function websocket_connect_resort() {
		ws = new WebSocket("ws://127.0.0.1:10081");

		ws.onopen = function() {
			$('.printHelper_info_resort').html('<b style="color:green">打印工具已开启</b>');
			printHelper = true;
		}

		ws.onclose = function (){
			printHelper = false;
			websocket_connect_resort();
		}

		ws.onerror = function(e) {
			$('.printHelper_info_resort').html('<b style="color:red">打印工具未开启</b> <a href="https://os.pcaex.com/PrintHelper.zip" target="_blank">(点击下载)</a>');
			printHelper = false;
		}

		ws.onmessage = function(msg) {
			// console.log(msg);
		}
	}

	$('#auto_print_label_resort').on('change', function(){
		websocket_connect_resort();
	});

	if(navigator.userAgent.match(/Android|iPhone|iPad|iPod|SymbianOS|Windows Phone/) !== null || (window.screen.width < 500 && window.screen.height < 800)){ //Mobile
		$("#auto_print_label_resort").hide();
	}

	$('#resorting-result tbody tr').hide();
	$('form#resorting-scan-form').on('success', function(e,r){
		$('input#resorting-scan').val('').focus();
		$('#print-result').html('');
		var audio=new Audio();
		audio.src='https://os.toplogistics.com.au/site/voice/' + (r.sounds.reverse().join('-')) + '.mp3';
		audio.play();
		$('#resorting-result tbody tr').hide();
		$(['status', 'area', 'msg', 'area', 'amazon', 'gatepass', 'hold']).each(function(i){
			if(r[this]) $('#resorting-result td.'+this).html(r[this]).parent().show();
		});

		if ( r.found == 1 ) {
			$('#scaned_shipment_id_resort').val(r.id).data('sn', r.sn);
			$('#my_sn_resort').val(r.sn);
		}

		if (r.print == 1) {
			$('.resorting_print_btn_pdf').show();
			if($('input#auto_print').prop('checked')) $('input.resorting_print_btn_pdf').trigger('click');
		} else {
			$('.resorting_print_btn_pdf').hide();
		}

		$('#resorting-scan-form').removeClass('red green blue').addClass(r.color);
		return true;
	}).on('submit', function(){
		$('#scaned_shipment_id_resort').val('');
		$('#my_sn_resort').val('');
		$('#search_container_no').val($('#myContainerNo').val());
		$('#location').val($('#mylocation').val());
		$('input#resorting-scan').focus();
	});

	$('.resorting_print_btn_pdf').click(function(e){
		var sid = $('#scaned_shipment_id_resort').val();
		var sn = $('#my_sn_resort').val();
		if($.inArray(sid+"_"+sn, resortprintLog) > -1){
			if(!window.confirm('Are you sure to reprint? 确认重复打印吗？')) return false;
		}else{
			if(resortprintLog.length > 100) resortprintLog.pop();
			resortprintLog.unshift(sid+"_"+sn);
		}

		
		if(resortprintHelper){
			$.get("<?php echo Yii::app()->createAbsoluteUrl("whscan/shipment/connote"); ?>"+"?helper=1&id="+sid+'&sn='+$('#scaned_shipment_id_resort').data('sn'), function(r){
				ws.send(r.file);
			}, 'json');
			return;
		}
		var pdfFrame = window.frames["pdf_label_resort"];
		$("#pdf_label_resort").attr("src","<?php echo Yii::app()->createAbsoluteUrl("whscan/shipment/connote"); ?>"+"?id="+sid+'&sn='+$('#scaned_shipment_id_resort').data('sn'));
		$("#pdf_label_resort").load(function(){
			 pdfFrame.focus();
			 pdfFrame.print();
		});
	});    
});

</script>