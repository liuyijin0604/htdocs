<?php
$consolId = 0;
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
$fileRepo = new FileRepo();

?>
<style>
	.uploading {
	    background: url("<?php echo Yii::app()->request->baseUrl; ?>/images/ajaxLoader.gif") no-repeat 0 0 !important;
	    background-size: 20px 20px !important;
	    background-color: white !important;
	}
	.star {
		color: red;
	}
</style>

<input type="hidden" name="consol-id" value="" id="consol_id">
<div class="row" style="display:flex;align-items:center;justify-content:center;">
<?php echo CHtml::button('有TLA面单的pallet label', ['class' => 'print_btn_pdf','style'=> 'font-size:xx-medium;width:80%;']); ?>
</div>
<div class="row" style="display: flex;align-items: center;justify-content: center;padding-top:10px;">
<?php echo CHtml::button('无TLA 面单的pallet label', ['class' => 'print_btn_pdf_blank','style'=> 'font-size:xx-medium;width:80%;']);?>
</div>
<div class="row" style="display: flex;align-items: center;justify-content: center;padding-top:10px;">
<?php echo CHtml::button('Unpacking List', ['id' => 'a_unpack_list','style'=> 'font-size:xx-medium;width:80%;']);?>
</div>

<div id='select_device' style="display:none;">Selected Device: <select id="selected_device" onchange=onDeviceSelected(this);></select></div>

<div class="printHelper_info" style="text-align: center;"></div>
<iframe id="pdf_label" style="display: none;" name="pdf_label" src="" ></iframe>
</br>


<div id = "uploadPhotosDiv" class="row" >
	<h2><?=$consol->no?></h2>
	<h3>Upload Container Photos</h3>
	</br>
<?php if($consol->process->getUnpackContainStatus()!="Completed"):?>
	
	<?php 
		$form=$this->beginWidget('CActiveForm', array(
		'id'=>'container_photo_form',
		'enableAjaxValidation'=>false,
		// 'action'=> 'rts/uploadUnknownFile'
		));
	?>
	<?php echo CHtml::label("1. Take Seal Photo", 'Take Seal Photo' ,["style"=>"margin-left:2em;font-size:1.5em"]); ?> 		
		<div class="row">
			<input type="file" name="seal_file" id="seal_file" style="display:none;" onchange="return checkInChangeFile();"> 
		<center><input id = "check_in_path" name="check_in_path" readonly class="form-control" style="width:40%;display: inline-block;"
			<?php if(!empty($completedTypes[0])) echo' value="Done" ';?>>
			<a href="" class="form-control" id = "takePhoto" style="width:40%;display: inline-block;margin-right: 1em;
			<?php if(!empty($completedTypes[0])) echo'pointer-events: none; background-color:grey;';?>
			" onclick="return checkInTakePhoto();"><span class="glyphicon glyphicon-camera"></span>Seal Photo<span class="star">*</span></center></a>		
		</div>

	<?php echo CHtml::label("2. Take Container Photos", 'Take Container Photos' ,["style"=>"margin-left:2em;font-size:1.5em"]); ?>
		<div class="row">
			<input type="file" name="container_front_file" id="container_front_file" style="display:none;" onchange="return CheckContianerFrontFile();"> 
		<center><input id = "container_front_path" name="container_front_path" readonly class="form-control" style="width:40%;display: inline-block;"
			<?php if(!empty($completedTypes[1])) echo' value="Done" ';?>>
			<a href="" class="form-control" id = "takePhoto" style="width:40%;display: inline-block;margin-right: 1em;
			<?php if(!empty($completedTypes[1])) echo'pointer-events: none; background-color:grey;';?>
			"onclick="return CheckContianerFrontPhoto();"><span class="glyphicon glyphicon-camera"></span>Container Front<span class="star">*</span></center></a>		
		</div>
		<div class="row">
			<input type="file" name="container_back_file" id="container_back_file" style="display:none;" onchange="return CheckContianerBackFile();"> 
		<center><input id = "container_back_path" name="check_back_path" readonly class="form-control" style="width:40%;display: inline-block;"
			<?php if(!empty($completedTypes[2])) echo' value="Done" ';?>>
			<a href="" class="form-control" id = "takePhoto" style="width:40%;display: inline-block;margin-right: 1em;
			<?php if(!empty($completedTypes[2])) echo'pointer-events: none; background-color:grey;';?>
			" onclick="return CheckContianerBackPhoto();"><span class="glyphicon glyphicon-camera"></span>Container Back<span class="star">*</span></center></a>		
		</div> 		
		<div class="row">
			<input type="file" name="container_left_file" id="container_left_file" style="display:none;" onchange="return CheckContianerLeftFile();"> 
		<center><input id = "container_left_path" name="container_left_path" readonly class="form-control" style="width:40%;display: inline-block;"
			<?php if(!empty($completedTypes[3])) echo' value="Done" ';?>>
			<a href="" class="form-control" id = "takePhoto" style="width:40%;display: inline-block;margin-right: 1em; 
			<?php if(!empty($completedTypes[3])) echo'pointer-events: none; background-color:grey;';?> 
			" onclick="return CheckContianerLeftPhoto();"><span class="glyphicon glyphicon-camera"></span>Container Left</center></a>		
		</div>
		<div class="row">
			<input type="file" name="container_right_file" id="container_right_file" style="display:none;" onchange="return CheckContianerRightFile();"> 
		<center><input id = "container_right_path" name="container_right_path" readonly class="form-control" style="width:40%;display: inline-block;"
			<?php if(!empty($completedTypes[4])) echo' value="Done" ';?>>
			<a href="" class="form-control" id = "takePhoto" style="width:40%;display: inline-block;margin-right: 1em;
			<?php if(!empty($completedTypes[4])) echo'pointer-events: none; background-color:grey;';?>
			" onclick="return CheckContianerRightPhoto();"><span class="glyphicon glyphicon-camera"></span>Container Right</center></a>		
		</div>		
		<?php echo CHtml::label("3.  Take Processing Photos", 'Take Container Photos' ,["style"=>"margin-left:2em;font-size:1.5em"]); ?> 		
		<div class="row">
			<input type="file" name="process_100_file" id="process_100_file" style="display:none;" onchange="return Check100ProcessFile();"> 
		<center><input id = "process_100_path" name="process_100_path" readonly class="form-control" style="width:40%;display: inline-block;"
			<?php if(!empty($completedTypes[5])) echo' value="Done" ';?>>
			<a href="" class="form-control" id = "takePhoto" style="width:40%;display: inline-block;margin-right: 1em;
			<?php if(!empty($completedTypes[5])) echo'pointer-events: none; background-color:grey;';?>
			" onclick="return Check100ProcessPhoto();"><span class="glyphicon glyphicon-camera"></span>100% Process<span class="star">*</span></center></a>		
		</div>
		<div class="row">
			<input type="file" name="process_50_file" id="process_50_file" style="display:none;" onchange="return Check50ProcessFile();"> 
		<center><input id = "process_50_path" name="process_50_path" readonly class="form-control" style="width:40%;display: inline-block;"
			<?php if(!empty($completedTypes[6])) echo' value="Done" ';?>>
			<a href="" class="form-control" id = "takePhoto" style="width:40%;display: inline-block;margin-right: 1em;
			<?php if(!empty($completedTypes[6])) echo'pointer-events: none; background-color:grey;';?>
			" onclick="return Check50ProcessPhoto();"><span class="glyphicon glyphicon-camera"></span>50% Process</center></a>		
		</div>
		<div class="row">
			<input type="file" name="process_0_file" id="process_0_file" style="display:none;" onchange="return Check0ProcessFile();"> 
		<center><input id = "process_0_path" name="process_0_path" readonly class="form-control" style="width:40%;display: inline-block;"
			<?php if(!empty($completedTypes[7])) echo' value="Done" ';?>>
			<a href="" class="form-control" id = "takePhoto" style="width:40%;display: inline-block;margin-right: 1em;
			<?php if(!empty($completedTypes[7])) echo'pointer-events: none; background-color:grey;';?>
			" onclick="return Check0ProcessPhoto();"><span class="glyphicon glyphicon-camera"></span>0% Process<span class="star">*</span></center></a>		
		</div>
		<?php echo CHtml::label("4.  Extra Info(Water damage, Physical damage, etc.)", 'Take Container Photos' ,["style"=>"margin-left:2em;font-size:1.5em"]); ?>
		<div id="extra_photo_container"> 		
			<div class="row">
				<input type="file" name="extra_file[0]" id="extra_file_0" style="display:none;" multiple="multiple" onchange="return CheckExtraPhotoFile(this.id);"> 
			<center><input id = "extra_photo_path_0" name="extra_photo_path_0" readonly class="form-control" style="width:35%;display: inline-block;">
				<a href="" class="form-control" data-id="0" id = "takePhoto_0" style="width:35%;display: inline-block;margin-right: 1em;" onclick="return CheckExtraPhoto(this.id);"><span class="glyphicon glyphicon-camera"></span>Extra Photo</a><a class="less form-control" data-id="0" style="width:5%;display: inline-block;margin-right: 1em;" >-</a></center>					
			</div>
		</div>
	</br>

		<tfoot>
			<tr>
		        <td><button id ="addBtn" class="moreitem btn btn-normal"><span class="glyphicon glyphicon-plus" style=" font-size: 1.2em; padding: 3px 10px;">Extra Photos</span></button></td>
		    </tr>
		</tfoot>

		</br>
		</br>

		<center>
			<a href="" class="form-control" id = "container_photo_form_submit" style="display:inline-block;width:40%;"  ><center><span class="glyphicon glyphicon-edit"></span>Submit</center></a>
			<a href="" class="form-control" id = "container_photo_form_complete" style="display:inline-block;width:40%;"  ><center><span class="glyphicon glyphicon-edit"></span>Complete</center></a>
		</center>
	<?php $this->endWidget();?>

<?php else : ?>
	<h3>(<?=$consol->process->getUnpackContainStatus()?>)</h3>
<?php endif;?>

</div>


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
				alert("can not connect to printer, please install the browser print APK");
				// alert(error);
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

			function checkInTakePhoto()
			{
				$('#seal_file').click();
				return false;
			};

			function checkInChangeFile()
			{
				$('#check_in_path').val($("#seal_file").val());
			}

			function CheckContianerLeftPhoto()
			{
				$('#container_left_file').click();
				return false;
			};

			function CheckContianerLeftFile()
			{
				$('#container_left_path').val($("#container_left_file").val());
			}

			function CheckContianerRightPhoto()
			{
				$('#container_right_file').click();
				return false;
			};

			function CheckContianerRightFile()
			{
				$('#container_right_path').val($("#container_right_file").val());
			}

			function CheckContianerFrontPhoto()
			{
				$('#container_front_file').click();
				return false;
			};

			function CheckContianerFrontFile()
			{
				$('#container_front_path').val($("#container_front_file").val());
			}

			function CheckContianerBackPhoto()
			{
				$('#container_back_file').click();
				return false;
			};

			function CheckContianerBackFile()
			{
				$('#container_back_path').val($("#container_back_file").val());
			}

			function Check100ProcessPhoto()
			{
				$('#process_100_file').click();
				return false;
			};

			function Check100ProcessFile()
			{
				$('#process_100_path').val($("#process_100_file").val());
			}

			function Check50ProcessPhoto()
			{
				$('#process_50_file').click();
				return false;
			};

			function Check50ProcessFile()
			{
				$('#process_50_path').val($("#process_50_file").val());
			}

			function Check0ProcessPhoto()
			{
				$('#process_0_file').click();
				return false;
			};

			function Check0ProcessFile()
			{
				$('#process_0_path').val($("#process_0_file").val());
			}

			function CheckExtraPhoto(idStr)
			{
				let partsOfStr = idStr.split('_');
				id = partsOfStr[1];
				// console.log(id);
				$('#extra_file_'+id).click();
				return false;
			}

			function CheckExtraPhotoFile(idStr)
			{
				let partsOfStr = idStr.split('_');
				id = partsOfStr[2];
				$('#extra_photo_path_'+id).val($("#extra_file_"+id).val());
			}


</script>
<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	let uploadContainerPhoto = 1;	
	$('#consol_id').val(<?=$consol->id; ?>);
	// $('#a_unpack_list').attr("href","<?=$this->createUrl('warehouseProcess/exportUnpackList');?>"+"?id="+$('#consol_id').val());

	$('input#scan').focus();
	$('#result tbody tr').hide();
	let ws = null;
	let printHelper = false;
	let printLog = [];
	let epId = 1;

	var addItem = function(add){
		var tb = $('#extra_photo_container');
		// var id = $('#extra_photo_container div').length;
		var add = add || 1;
		while(add-- > 0){
			tb.append('<div class="row"> <input type="file" name="extra_file['+epId+']" id="extra_file_'+epId+'" style="display:none;" multiple="multiple" onchange="return CheckExtraPhotoFile(this.id);" data-id="'+epId+'"> <center><input id = "extra_photo_path_'+epId+'" name="extra_photo_path_'+epId+'" readonly class="form-control" style="width:35%;display: inline-block;"> <a href="" class="form-control" data-id="'+epId+'" id = "takePhoto_'+epId+'" style="width:35%;display: inline-block;margin-right: 1em;" onclick="return CheckExtraPhoto(this.id);"><span class="glyphicon glyphicon-camera"></span>Extra Photo</a><a class="less form-control" data-id="'+epId+'" style="width:5%;display: inline-block;margin-right: 1em;" >-</a></center> </div>');
			epId++;
		}
				
	};

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

	function check_photos_submitted() {
		if($('#check_in_path').val()!="Done")
		{
			alert("Seal Photo Required");
			return false;
		}
		// if($('#container_left_path').val()=="")
		// {
		// 	alert("Container left Required");
		// 	return false;
		// }
		// if($('#container_right_path').val()=="")
		// {
		// 	alert("Container right Required");
		// 	return false;
		// }
		if($('#container_front_path').val()!="Done")
		{
			alert("Container front Required");
			return false;
		}
		if($('#container_back_path').val()!="Done")
		{
			alert("Container back Required");
			return false;
		}
		if($('#process_100_path').val()!="Done")
		{
			alert("100% Process Required");
			return false;
		}
		if($('#process_0_path').val()!="Done")
		{
			alert("0% Process Required");
			return false;
		}
		return true;
	}

	function emptyFilePath() {
		$('#check_in_path').val('');
		$('#container_left_path').val('');
		$('#container_right_path').val('');
		$('#container_front_path').val('');
		$('#container_back_path').val('');
		$('#process_100_path').val('');
		$('#process_50_path').val('');
		$('#process_0_path').val('');
		$('#extra_photo_path').val('');
	}

	$('#auto_print_label').on('change', function(){
		websocket_connect();
	});

	$(window).unload(function() {
		ws.close();
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
			$.get("<?php echo Yii::app()->createAbsoluteUrl("whscan/unpackContainer/printAllPalletLabels"); ?>"+"?id="+$('#consol_id').val(), function(r){
				ws.send(r.file);
			}, 'json');
			return;
		}

		<?php $url = Yii::app()->createAbsoluteUrl("whscan/unpackContainer/printAllPalletLabels"); ?>
		window.open('<?=$url?>'+"?id="+$('#consol_id').val());
	    return false;
	}); 

	$('#a_unpack_list').click(function(e){

		<?php $url = Yii::app()->createAbsoluteUrl("whscan/unpackContainer/exportUnpackList"); ?>
		window.open('<?=$url?>'+"?id="+$('#consol_id').val());
	    return false;
	}); 

	$('.print_btn_pdf_blank').click(function(e){
		var sid = $('#scaned_shipment_id').val();
		var sn = $('#my_sn').val();

		if($.inArray(sid+"_"+sn, printLog) > -1){
			if(!window.confirm('Are you sure to reprint? 确认重复打印吗？')) return false;
		}else{
			if(printLog.length > 100) printLog.pop();
			printLog.unshift(sid+"_"+sn);
		}
		
		if(printHelper){
			$.get("<?php echo Yii::app()->createAbsoluteUrl("whscan/unpackContainer/printBlankPalletLabels"); ?>"+"?id="+$('#consol_id').val(), function(r){
				ws.send(r.file);
			}, 'json');
			return;
		}

		<?php $url = Yii::app()->createAbsoluteUrl("whscan/unpackContainer/printBlankPalletLabels"); ?>
		window.open('<?=$url?>'+"?id="+$('#consol_id').val());
	    return false;
	}); 


	$('#container_photo_form_submit').click(function(e){
		// if(!check_photos_empty()){
		// 	return false;
		// }

		if( confirm('Are you sure to upload the container photos?')){
			var form = new FormData(document.getElementById("container_photo_form"));
			 $.ajax({
			            url: '<?=$this->createUrl('unpackContainer/uploadContainerPhotos')?>'+"?id="+$('#consol_id').val(),
					    type: "post",
					    data: form,
					    processData: false,
					    contentType: false,
			            success: function(r) {
			                if(r=="done")
			                 {
			                 	$('#notifc').notify({message: {html: "Upload Success"}}).show();
			                 	// emptyFilePath();
			                 	// $('#container_photo_form')[0].reset();
			                 	<?php $url = Yii::app()->createAbsoluteUrl("whscan/unpackContainer/checkContainerDetail"); ?>
								window.open('<?=$url?>'+"?id="+$('#consol_id').val());

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

	$('#container_photo_form_complete').click(function(e){
		if(!check_photos_submitted()){
			return false;
		}

		if( confirm('Are you sure to complete the container ?')){
			var form = new FormData(document.getElementById("container_photo_form"));
			 $.ajax({
			            url: '<?=$this->createUrl('unpackContainer/completeContainer')?>'+"?id="+$('#consol_id').val(),
					    type: "post",
					    data: form,
					    processData: false,
					    contentType: false,
			            success: function(r) {
			                if(r=="done")
			                 {
			                 	$('#notifc').notify({message: {html: "Upload Success"}}).show();
			                 	
			                 	<?php $url = Yii::app()->createAbsoluteUrl("whscan/unpackContainer/checkContainerDetail"); ?>
								window.open('<?=$url?>'+"?id="+$('#consol_id').val());

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

	$('#addBtn').off('click').on('click', function(e){
	    e.preventDefault();
		addItem(1);

		$('.less').off('click').on('click', function(e){
	        e.preventDefault();
	        $(this).parent().parent().remove();
		});
	}); 

	$('.less').off('click').on('click', function(e){
        e.preventDefault();
        $(this).parent().parent().remove();
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

