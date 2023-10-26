<?php
$consolId = 0;
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<style>
	#a_unpack_list {
		 display: none;
	}

	.uploading {
	    background: url("<?php echo Yii::app()->request->baseUrl; ?>/images/ajaxLoader.gif") no-repeat 0 0 !important;
	    background-size: 20px 20px !important;
	    background-color: white !important;
	}
</style>
<h1>Container Searching</h1>

<div id = "uploadContainerFileDiv" class="row" style="display:none;">
</br>
<?php 
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'check_in_container_form',
	'enableAjaxValidation'=>false)
	);
?>
<?php echo CHtml::label("Take Container/AWB Photo", 'Take Container/AWB Photo' ,["style"=>"margin-left:2em;font-size:1.5em"]); ?>
	<input type="file" name="container_file" id="container_file" style="display:none;" onchange="return checkInChangeFileContainer();"> 
	<center><input id = "check_in_path_container" name="check_in_path_container" readonly class="form-control" style="width:90%;"></center>
	<div class="row">
	<center><a href="" class="form-control" id = "takeContainerPhoto" style="width:40%;display: inline-block;margin-right: 1em;" onclick="return checkInTakePhotoContainer();"><center><span class="glyphicon glyphicon-camera"></span>Take Photo</center></a>
	<a href="" class="form-control" id = "check_in_container_form_submit" style="width:40%;display: inline-block;"  ><center><span class="uploading" style="display:none">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;<span class="glyphicon glyphicon-edit"></span>Submit</center></a></center>
	</div>
<?php $this->endWidget();?>

</div>
</br>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', [
	'id'=>'scan-form',
	'enableAjaxValidation'=>false,
]);
?>
<div style="float:right;"><select name="sound"><option value="">Default Sound</option><option value="1">中文女声</option><option value="2">中文男声</option></select></div>
<div style="font-size: 1.5em;">ContainerNo/AWB: <input style="width: 95%" id="scan" type="text" size="30" name="barcode" autocomplete="off"  /></div>
<?php $this->endWidget(); ?>
</div>
<input type="hidden" name="consol-id" value="" id="consol_id">
<?php
echo CHtml::button('Print Pallet Labels', ['class' => 'print_btn_pdf','style'=> 'display:none;margin-left:20px;font-size:xx-medium;']);
?>
<a id="a_unpack_list" href="" target="_blank" >[Unpacking List]</a>	

<div id='select_device' style="display:none;">Selected Device: <select id="selected_device" onchange=onDeviceSelected(this);></select></div>

<div class="printHelper_info" style="text-align: center;"></div>
<iframe id="pdf_label" style="display: none;" name="pdf_label" src="" ></iframe>

<?php
echo '<h4>Related Shipments</h4>';
?>
<?php $this->renderPartial("consol_related_shipments_list",["model"=>$model]);?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/BrowserPrint-3.0.216.min.js"></script>
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
					
				}, function(){alert("Error getting local devices")},"printer");
				
			}, function(error){
				alert(error);
			})
}

function writeToSelectedPrinter(dataToWrite)
{
	selected_device.send(dataToWrite, undefined, errorCallback);
}
var errorCallback = function(errorMessage){
	alert("Error: " + errorMessage);	
}
setup();

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

	$('form#scan-form').on('keydown',function(e){
		if(e.which!=13)
		{			
			return true;

		}
		barcode = $('input#scan').val();		

		// $('#related_shipment_grid_view').yiiGridView('update', {data: $('.filters input, .filters select').serialize() + '&' + $(this).serialize()});
		// return false;

	    <?php $url = $this->createUrl('warehouseProcess/checkContainerDetailByBarcode');?>
	    $.ajax({
	             url: '<?=$url?>'+"?barcode="+barcode,
	             type: "post",
	             processData: false,
	             contentType: false,             
	             success: function(r) {
	             	consolId = -1;
	             	r = JSON.parse(r);
					if (r.found == 1) {
						$('.print_btn_pdf').show();
						$('#a_unpack_list').show();
						$('#consol_id').val(r.consol_id);
						$('#a_unpack_list').attr("href","<?=$this->createUrl('warehouseProcess/exportUnpackList');?>"+"?id="+r.consol_id);
						
						if($('input#auto_print').prop('checked')) $('input.print_btn_pdf').trigger('click');
						consolId = r.consol_id;
					} else {
						$('.print_btn_pdf').hide();
						$('#a_unpack_list').hide();
					}

					if ($('input[name="ImParcel[ref]"]').val() == "searching"){
						$('input[name="ImParcel[ref]"]').val("");
					}

					$('#related_shipment_grid_view').yiiGridView('update', {data: $('.filters input, .filters select').serialize() + '&' + $(this).serialize()+"&ImParcel[consol_id]="+consolId});
					
					return true;
	              //$('#content').remove();

	           },
	             error: function(e) {
	                 console.log(e);
	             }
	         });

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
			$.get("<?php echo Yii::app()->createAbsoluteUrl("whscan/warehouseProcess/printAllPalletLabels"); ?>"+"?id="+$('#consol_id').val(), function(r){
				ws.send(r.file);
			}, 'json');
			return;
		}

		<?php $url = Yii::app()->createAbsoluteUrl("whscan/warehouseProcess/printAllPalletLabels"); ?>
		window.open('<?=$url?>'+"?id="+$('#consol_id').val());
	    // $.ajax({
	    //          url: '<?=$url?>'+"?id="+$('#consol_id').val(),
	    //          type: "POST",
	    //          processData: false,
	    //          contentType: false,             
	    //          success: function(r) {
	    //          	r = JSON.parse(r);
					// if (r.result) {
					// 	let file = r.file;
					// 	// console.log(file);
					// 	writeToSelectedPrinter(file);					
					// } else {
					// 	alert("Can not print!");
					// }

	    //        },
	    //          error: function(e) {
	    //              console.log(e);
	    //          }
	    //      });
	    return false;
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
<?php $this->registerJS(ob_get_clean()); ?>

