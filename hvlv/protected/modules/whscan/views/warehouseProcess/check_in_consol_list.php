<?php

?>

<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<?php
$tts = ['resort' => 'Resorting', 'checkin' => 'CheckIn', 'change' => 'Change Label'];
echo '<h1>Scan for '.(isset($tts[$op])? $tts[$op] : 'Check Status').'</h1>';//'<a style="float:right;font-size:1.2em;" href="'.Yii::app()->request->getUrl().'?cache=1">加速版</a><h1>Scan for '.(isset($tts[$op])? $tts[$op] : 'Check Status').'</h1>';
?>
<div style="font-size: 1.5em;<?php if(!isset($tts[$op])||$tts[$op]!="CheckIn"){echo "display:none;";}?>">ContainerNo/AWB: <input style="width: 95%" id="myContainerNo" type="text" size="10" name="myContainerNo" /></div>
<div id = "uploadContainerFileDiv" class="row" style="display:none;">
</br>
<?php 
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'check_in_container_form',
	'enableAjaxValidation'=>false,
	'action'=> 'warehouseProcess/uploadContainerFile')
	);
?>
<?php echo CHtml::label("Take Container/AWB Photo", 'Take Container/AWB Photo' ,["style"=>"margin-left:2em;font-size:1.5em"]); ?>
	<input type="file" name="container_file" id="container_file" style="display:none;" onchange="return checkInChangeFileContainer();"> 
	<center><input id = "check_in_path_container" name="check_in_path_container" readonly class="form-control" style="width:90%;"></center>
	<div class="row">
	<center><a href="" class="form-control" id = "takeContainerPhoto" style="width:40%;display: inline-block;margin-right: 1em;" onclick="return checkInTakePhotoContainer();"><center><span class="glyphicon glyphicon-camera"></span><font id="containerPhotoNotice">Take Photo</font></center></a>
	<a href="" class="form-control" id = "check_in_container_form_submit" style="width:40%;display: inline-block;"  ><center><span class="uploading" style="display:none">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<span class="glyphicon glyphicon-edit"></span>Submit</center></a></center>
	</div>
<?php $this->endWidget();?>

</div>
<select id="selected_device" onchange=onDeviceSelected(this); style="display: none;"></select> 
<div style="font-size: 1.5em;<?php if(!isset($tts[$op])||$tts[$op]!="CheckIn"){echo "display:none;";}?>">Location: <input style="width: 95%" id="mylocation" type="text" size="30" name="mylocation" /></div>
</br>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', [
	'id'=>'scan-form',
	'action'=>$this->createUrl('shipment/scan')."?op=".$op,
	'enableAjaxValidation'=>false,
]);
?>
<div style="float:right;">
	
	<!-- <label id="unpacking_label">CheckCourier: <input type="checkbox" id="checkCourier" name="checkCourier" value="1" />&nbsp;&nbsp;<label id="unpacking_label">PalletScan: <input type="checkbox" id="palletScan" name="palletScan" value="1" />&nbsp;&nbsp;<label id="unpacking_label">CheckConsolBag: <input type="checkbox" id="checkConsolBag" name="checkConsolBag" value="1" />&nbsp;&nbsp;<label id="unpacking_label">CheckBag: <input type="checkbox" id="checkBagScan" name="checkBagScan" value="1" />&nbsp;&nbsp;<label id="unpacking_label">Unpacking: <input type="checkbox" id="unpacking" name="unpacking" value="1" /></label> &nbsp; -->

	<label id = "auto_print_label">Auto Print: <input type="checkbox" id="auto_print" name="auto_print" value="1" /></label> &nbsp; <select name="sound"><option value="">Default Sound</option><option value="1">中文女声</option><option value="2">中文男声</option></select></div>
	<input type="hidden" name="location" id = "location"/>
	<input type="hidden" name="search_container_no" id = "search_container_no"/>
<div style="font-size: 1.5em;">Barcode: <input style="width: 95%" id="scan" type="text" size="30" name="barcode" autocomplete="off" value="<?=@$barcode?>" /></div>
<?php $this->endWidget(); ?>

<?php if(!isset($tts[$op])):?>
<p><input type="input" id="containerNo" name="containerNo" value="" style="margin:5px 5px 5px 5px;"/><a href="<?=$this->createUrl('shipment/exportAout');?>" target="_blank" id = "outre">[Outturn Report]</a><a href="<?=$this->createUrl('shipment/exportLocationList');?>" target="_blank" id = "lolist">[Location List]</a></p>

<?php endif;?>

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
?>
	<div id = "uploadDamageFileDiv" class="row" style="display:none;">
	</br>
	<?php 
		$form=$this->beginWidget('CActiveForm', array(
		'id'=>'check_in_damage_form',
		'enableAjaxValidation'=>false,
		'action'=> 'warehouseProcess/uploadFile')
		);
	?>
	<?php 
		echo "<div style='width:100%'>";
		echo CHtml::button('Print Temp Label', ['class' => 'print_btn_pdf_temp','style'=> 'display:none;margin-left:20px;font-size:xx-large;','id'=>'print_btn_pdf_temp']);
		echo "</div>";

		echo CHtml::label("Take Damage Photo", 'Take Damage Photo' ,["style"=>"margin-left:2em;font-size:1.5em"]); ?>
		<input type="file" name="damage_file" id="damage_file" style="display:none;" onchange="return checkInChangeFile();"> 
		<center><input id = "check_in_path" name="check_in_path" readonly class="form-control" style="width:90%;"></center>
		<div class="row">
		<center><a href="" class="form-control" id = "takePhoto" style="width:40%;display: inline-block;margin-right: 1em;" onclick="return checkInTakePhoto();"><center><span class="glyphicon glyphicon-camera"></span>Take Photo</center></a>
		<a href="" class="form-control" id = "check_in_damage_form_submit" style="width:40%;display: inline-block;"  ><center><span class="uploading" style="display:none">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<span class="glyphicon glyphicon-edit"></span>Submit</center></a></center>
		</div>
	<?php $this->endWidget();?>

	</div>
<input type="hidden" name="scanned-id" value="" id="scaned_shipment_id">
<input type="hidden" name="scan-code" value="" id="scan-code">
<input type="hidden" name="temp_id" value="" id="temp_id">
<input type="hidden" name="found" value="" id="found">
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
<script type="text/javascript">
			function checkInTakePhotoContainer()
			{
				$('#container_file').click();
				return false;
			};

			function checkInChangeFileContainer()
			{
				$('#check_in_path_container').val($("#container_file").val());
			}


			function checkInTakePhoto()
			{
				$('#damage_file').click();
				return false;
			};

			function checkInChangeFile()
			{
				$('#check_in_path').val($("#damage_file").val());
			}
</script>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	let uploadContainerPhoto = 1;
	$('input#myContainerNo').on('blur', function(e) {
		if ($('input#myContainerNo').val() != "") {
			$.ajax({
				'url': '<?=$this->createUrl('warehouseProcess/checkContainerPhoto');?>',
				'type': 'GET',
				'data': {'containerNo': $('input#myContainerNo').val()},
				success: function(r) {
					$('#uploadContainerFileDiv').show();
					if(r == "done")
					{
						uploadContainerPhoto = 1;
						$('#containerPhotoNotice').html("Take More Photo");
					}else
					{
						$('#containerPhotoNotice').html("Take Photo");
						uploadContainerPhoto = 1;
					}
				}
			});
		}
	})

	$('#check_in_damage_form_submit').on('click',function(){
		var questionTitle = "Are you sure to upload damage photo?";
		let location = "";
		if($('#check_in_path').val()=="")
		{
			questionTitle = "Are you sure to submit exception without photo?";
		}

		if($('#mylocation').val()!="")
		{
			location = $('#mylocation').val();
		}else
		{
			location = $('#location').val();
		}

		if(confirm(questionTitle)){
			$('.uploading').show();
			var form = new FormData(document.getElementById("check_in_damage_form"));
			 $.ajax({
			            url: '<?=$this->createUrl('warehouseProcess/uploadDamageFile')?>'+"?id="+$('#scaned_shipment_id').val()+"&&sn="+$('#my_sn').val()+"&&containerNo="+$('#myContainerNo').val()+"&&barcode="+$('input#scan-code').val()+"&&found="+$('#found').val()+"&&location="+location,
					    type: "post",
					    data: form,
					    processData: false,
					    contentType: false,
			            success: function(r) {
			            	$('.uploading').hide();
			            	r = JSON.parse(r)
			                if(r.done)
			                 {
			                 	if(r.msg=="temp")
			                 	{
				                 	$('#temp_id').val(r.data);
				                 	$('#print_btn_pdf_temp').show();
			                 	}
			                 	$('#notifc').notify({message: {html: "Upload Success"}}).show();
							 }else
							 {
								$('#notifc').notify({message: {html: r.msg},type: 'danger'}).show();
				             }
				         },
			            error: function(e) {
			                console.log(e);
			            }
			        });			
		}
		return false;
	});

	$('#check_in_container_form_submit').on('click',function(){
		if( confirm('Are you sure to upload container photo?')){
			$('.uploading').show();
			var form = new FormData(document.getElementById("check_in_container_form"));
			 $.ajax({
			            url: '<?=$this->createUrl('warehouseProcess/uploadContainerFile')?>'+"?containerNo="+$('#myContainerNo').val(),
					    type: "post",
					    data: form,
					    processData: false,
					    contentType: false,
			            success: function(r) {
			            	$('.uploading').hide();
			                if(r=="done")
			                 {
			                 	uploadContainerPhoto = 1;
			                 	$('#containerPhotoNotice').html("Take More Photo");
			                 	$('#notifc').notify({message: {html: "Upload Success"}}).show();
							 }else
							 {
								$('#notifc').notify({message: {html: "Upload Fail"},type: 'danger'}).show();
				             }
				         },
			            error: function(e) {
			                console.log(e);
			            }
			        });			
		}
		return false;
	});


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

	$('form#scan-form').on('success', function(e,r){
		if(uploadContainerPhoto==0)
		{
			alert("Input Container No. and Upload Container Photo first");
			return false;
		}
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

		$('#uploadDamageFileDiv').show();
		$('#scaned_shipment_id').val(r.id).data('sn', r.sn);
		$('#my_sn').val(r.sn);
		if(r.found==1)
		{
			$('#found').val("1");
		}else
		{
			$('#found').val("0");
		}


		if (r.print == 1) {
			$('.print_btn_pdf').show();
			if($('input#auto_print').prop('checked')) $('input.print_btn_pdf').trigger('click');
		} else {
			$('.print_btn_pdf').hide();
		}

		$('.form').removeClass('red green blue').addClass(r.color);
		return true;
	}).on('submit', function(){
		$('#print_btn_pdf_temp').hide();
		$('#temp_id').val("");
		$('#uploadDamageFileDiv').hide();
		$('#scaned_shipment_id').val('');
		$('#check_in_path').val("");
		$('#my_sn').val('');
		$('#search_container_no').val($('#myContainerNo').val());
		$('#location').val($('#mylocation').val());
		$('input#scan').focus();
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
			$.get("<?php echo Yii::app()->createAbsoluteUrl("whscan/shipment/connote"); ?>"+"?helper=1&id="+sid+'&sn='+$('#scaned_shipment_id').data('sn'), function(r){
				ws.send(r.file);
			}, 'json');
			return;
		}
		var pdfFrame = window.frames["pdf_label"];
		$("#pdf_label").attr("src","<?php echo Yii::app()->createAbsoluteUrl("whscan/shipment/connote"); ?>"+"?id="+sid+'&sn='+$('#scaned_shipment_id').data('sn'));
		$("#pdf_label").load(function(){
			 pdfFrame.focus();
			 pdfFrame.print();
		});
	});  

	$('.print_btn_pdf_temp').click(function(e){
			$.ajax({
				'url': "<?=Yii::app()->createAbsoluteUrl('whscan/shipment/generateTempLabel');?>"+"?tempId="+$('#temp_id').val()+"&v=zpl",
				'type': 'GET',
				'data': {},
				success: function(r) {
					// console.log(r);
					writeToSelectedPrinter(r);
				}
			});
	});  

	if(navigator.userAgent.match(/Android|iPhone|iPad|iPod|SymbianOS|Windows Phone/) !== null || (window.screen.width < 500 && window.screen.height < 800)){ //Mobile
		$("#auto_print_label").hide();
	}
	var oriRef = "";
	$('#outre').on('mousedown', function(){
		if(oriRef=="")
		{
			oriRef = $(this).attr('href');
		}
		var cNo = $('#containerNo').val();
		$(this).attr('href', oriRef + '?containerNo=' + cNo);
	});

	var lolist = "";
	$('#lolist').on('mousedown', function(){
		if(lolist=="")
		{
			lolist = $(this).attr('href');
		}
		var cNo = $('#containerNo').val();
		$(this).attr('href', lolist + '?containerNo=' + cNo);
	});

});
</script>




<script type="text/javascript" src="../js/BrowserPrint-3.0.216.min.js"></script>
<script type="text/javascript">
var selected_device;
var devices = [];
function setup()
{
	//Get the default device from the application as a first step. Discovery takes longer to complete.
	BrowserPrint.getDefaultDevice("printer", function(device)
			{
		
				//Add device to list of devices and to html select element
				selected_device = device;
				devices.push(device);
				var html_select = document.getElementById("selected_device");
				var option = document.createElement("option");
				option.text = device.name;
				html_select.add(option);
				
				//Discover any other devices available to the application
				BrowserPrint.getLocalDevices(function(device_list){
					for(var i = 0; i < device_list.length; i++)
					{
						//Add device to list of devices and to html select element
						var device = device_list[i];
						if(!selected_device || device.uid != selected_device.uid)
						{
							devices.push(device);
							var option = document.createElement("option");
							option.text = device.name;
							option.value = device.uid;
							html_select.add(option);
						}
					}
					
				}, function(){
					//alert("Error getting local devices");
				},"printer");
				
			}, function(error){
				//alert(error);
			})
}
function getConfig(){
	BrowserPrint.getApplicationConfiguration(function(config){
		alert(JSON.stringify(config))
	}, function(error){
		alert(JSON.stringify(new BrowserPrint.ApplicationConfiguration()));
	})
}
function writeToSelectedPrinter(dataToWrite)
{
	selected_device.send(dataToWrite, undefined, errorCallback);
}
var readCallback = function(readData) {
	if(readData === undefined || readData === null || readData === "")
	{
		alert("No Response from Device");
	}
	else
	{
		alert(readData);
	}
	
}
var errorCallback = function(errorMessage){
	alert("Error: " + errorMessage);	
}
function readFromSelectedPrinter()
{

	selected_device.read(readCallback, errorCallback);
	
}
function getDeviceCallback(deviceList)
{
	alert("Devices: \n" + JSON.stringify(deviceList, null, 4))
}

function onDeviceSelected(selected)
{
	for(var i = 0; i < devices.length; ++i){
		if(selected.value == devices[i].uid)
		{
			selected_device = devices[i];
			return;
		}
	}
}
setup();
</script>

<?php $this->registerJS(ob_get_clean()); ?>
Search By AWB/Container #:<div style="width:20em;"><input type="text" name="awbcheckin" id = "awbcheckin" class="form-control"/></div>
<?php echo $this->renderPartial('loading_list', array('url'=>"warehouseProcess/getCheckInReport")); ?>

<script type="text/javascript">
	$('#awbcheckin').keyup(function(event) {
	  if (event.keyCode === 13) {
	    $('#pl-sum-report-data_<?=$_GET['tabid']?> li ul li:not(:contains("'+$.trim($(this).val())+'"))').hide();
	    $('#pl-sum-report-data_<?=$_GET['tabid']?> li ul li:contains("'+$.trim($(this).val())+'")').show();
	  }
	});

</script>