<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>
<?php
$tts = ['rtsscan' => 'RTS'];
echo '<h1>Put Away RTS</h1>';
?>
</br>
<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', [
		'id'=>'scan-form',
		'enableAjaxValidation'=>false,
	]);
	?>
	<div style="margin-bottom:15px;">
		<?php echo CHtml::RadioButtonList('rts_type',0, array('0'=>'Normal RTS','1'=>'Wrong Courier'),array('labelOptions'=>array('class'=>'radio_label'),'separator'=>'&nbsp;&nbsp;')),'</span>'; ?>
	</div>
					

	<div style="margin-bottom:15px;">
		<input class="barcode required form-control" type="text" name="shipment" placeholder="Barcode" id="shipment" autocomplete="off" />
	</div>

	<div class="input-group"><input id="location" class="barcode required form-control" type="search" placeholder="Location only for putaway" name="location" />
		<label class="input-group-addon"><input type="checkbox" class="klocation" /> Lock</label>
	</div>

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
</br>
<iframe id="pdf_label" style="display: none;" name="pdf_label" src="" ></iframe>
<?php
echo CHtml::button('Print PDF Label', ['class' => 'print_btn_pdf','style'=> 'display:none;margin-left:20px;font-size:xx-large;']);
?>
<div id = "uploadReasonDiv" class="row" style="display:none;">
</br>
<?php 
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'rts_reason_form',
	'enableAjaxValidation'=>false,
	'action'=> 'rts/uploadRTSReason')
	);
?>

<?php echo CHtml::label("Select RTS Reason(*)", 'Select RTS Reason(*)' ,["style"=>"margin-left:2em;font-size:1.5em;display:none;","id"=>"rts_reason_label"]); ?>
<?php echo CHtml::label("Select Wrong Courier(*)", 'Select Wrong Courier(*)' ,["style"=>"margin-left:2em;font-size:1.5em;display:none;","id"=>"wrong_courier_label"]); ?>
<center>
<?php
	echo CHtml::dropDownList("reason","", ["WRONG ADDRESS"=>"WRONG ADDRESS","WRONG COURIER"=>"WRONG COURIER","DISTANCE TOO LONG"=>"DISTANCE TOO LONG","RECEIVER REJECT"=>"RECEIVER REJECT","DAMAGE"=>"DAMAGE(Photo Required)","OTHERS"=>"OTHERS(Photo Required)","OTHERS_TEXT"=>"OTHERS(Text Required)"],["style"=>"font-size:1.5em;width:90%;display:none;","class"=>"form-control"]);
	echo CHtml::dropDownList("wrong_courier","", [""=>"Select","Aupost"=>"Aupost","TNT"=>"TNT","FASTWAY"=>"FASTWAY","ALLIED"=>"ALLIED","TOLL"=>"TOLL","BORDER"=>"BORDER"],["style"=>"font-size:1.5em;width:90%;display:none;","class"=>"form-control"]); 
    echo CHtml::label("Enter Other Reason", "other_reason_label", ["style" => "font-size:1.5em;display:none;"]);
	echo CHtml::textField("other_reason", "", ["style" => "font-size:1.5em;width:90%;display:none;", "class" => "form-control", "placeholder" => "Enter your reason here"]);
	?>
	<input type="hidden" name="barcode_bk" id="barcode_bk">
	<input type="hidden" name="location_bk" id="location_bk">
</center>


<?php echo CHtml::label("Take Record Photo", 'Take Record Photo' ,["style"=>"margin-left:2em;font-size:1.5em"]); ?>

		<input type="file" name="record_file" id="record_file" style="display:none;" onchange="return rtsRecordChangeFile();"> 
		<center><input id = "rts_record_path" name="rts_record_path" readonly class="form-control" style="width:90%;"></center>
		<div class="row">
			<center>
				<a href="" class="form-control" id = "takePhoto" style="width:40%;display: inline-block;margin-right: 1em;" onclick="return rtsRecordTakePhoto();"><center><span class="glyphicon glyphicon-camera"></span>Take Photo</center></a>
				<a href="" class="form-control" id = "rts_reason_form_submit" style="width:40%;display: inline-block;"  ><span class="glyphicon glyphicon-edit"></span>Submit</center></a>
			</center>
<?php $this->endWidget();?>

		</div>
</div>

<div id = "uploadUnknownFileDiv" class="row" style="display:none;">
	</br>
	<?php 
		$form=$this->beginWidget('CActiveForm', array(
		'id'=>'rts_unknown_form',
		'enableAjaxValidation'=>false,
		'action'=> 'rts/uploadUnknownFile')
		);
	?>
	<?php echo CHtml::label("Take Unknown Photo", 'Take Unknown Photo' ,["style"=>"margin-left:2em;font-size:1.5em"]); ?> 
		<input type="file" name="unknown_file" id="unknown_file" style="display:none;" onchange="return checkInChangeFile();"> 
		<center><input id = "check_in_path" name="check_in_path" readonly class="form-control" style="width:90%;"></center>
		<div class="row">
		<center><a href="" class="form-control" id = "takePhoto" style="width:40%;display: inline-block;margin-right: 1em;" onclick="return checkInTakePhoto();"><center><span class="glyphicon glyphicon-camera"></span>Take Photo</center></a>
		<a href="" class="form-control" id = "rts_unknown_form_submit" style="width:40%;display: inline-block;"  ><center><span class="glyphicon glyphicon-edit"></span>Submit</center></a></center>
		</div>
	<?php $this->endWidget();?>

</div>


<input type="hidden" name="scanned-id" value="" id="scaned_shipment_id">
<input type="hidden" value="" id="my_sn">
</div>

<br />
<div id="res"></div>
<script type="text/javascript">
			function checkInTakePhoto()
			{
				$('#unknown_file').click();
				return false;
			};

			function checkInChangeFile()
			{
				$('#check_in_path').val($("#unknown_file").val());
			}

			function rtsRecordTakePhoto()
			{
				$('#record_file').click();
				return false;
			};

			function rtsRecordChangeFile()
			{
				$('#rts_record_path').val($("#record_file").val());
			}
</script>


<?php ob_start(); ?>
<script type="text/javascript">
	$('#reason').on('change',function(){
		var selectedReason =$(this).val();
		if (selectedReason == "OTHERS_TEXT") {
		$('#other_reason_label').css('display', 'block');
		$('#other_reason').css('display', 'block'); 
		} else {
		$('#other_reason_label').css('display', 'none'); 
		$('#other_reason').css('display', 'none'); 
	 }
	});
$(function() {
	$lastSubmit = true;
	let ws = null;
	let printHelper = false;
	let printLog = [];

	$('#rts_reason_form_submit').on('click',function(){
		if(($('#reason').val()=="DAMAGE"||$('#reason').val()=="OTHERS")&&$('#rts_record_path').val()=="")
		{
			alert($('#reason').val()+" Reason Is Photo Required");
			return false;
		}
		let reason = $('#reason').val();
		if (reason =="OTHERS_TEXT"){
			var otherReason = $('#other_reason').val().trim();
			console.log(otherReason);
			if (otherReason == "") {
			alert(reason + " Reason Is Text Required");
			return false;
		  }
		  reason = otherReason;
		}
		if($('input[name=rts_type]:checked').val()==1)
		{
			reason = $('#wrong_courier').val();
			if(reason=="")
			{
				alert("Please Select Sent Courier");
				return false;
			}
		}
	    var form = new FormData(document.getElementById("rts_reason_form"));
		$.ajax({
		        url: '<?=$this->createUrl('rtsProcess/uploadRTSReason')?>'+"?id="+$('#scaned_shipment_id').val()+"&&sn="+$('#my_sn').val()+"&&reason="+reason+"&&barcode="+$('#barcode_bk').val()+"&&location="+$('#location_bk').val()+"&&rts_type="+$('input[name=rts_type]:checked').val(),
			    type: "post",
			    data: form,
			    processData: false,
			    contentType: false,
	            success: function(r) {
	                if(r=="done")
	                 {
	                 	$lastSubmit = true;
	                 	$('#notifc').notify({message: {html: "Upload Success"}}).show();
	                 	$('#rts_received_grid_view').yiiGridView('update');
					 }else
					 {
						$('#notifc').notify({message: {html: "Upload Fail"},type: 'danger'}).show();
		             }
		             $('input#scan').focus();
		         },
	            error: function(e) {
	                console.log(e);
	            }
	        });			
	    return false;
	});


	$('#rts_unknown_form_submit').on('click',function(){
		if($('#check_in_path').val()=="")
		{
			alert("Photo Required");
			return false;
		}
		if( confirm('Are you sure to upload unknown photo?')){
			var form = new FormData(document.getElementById("rts_unknown_form"));
			 $.ajax({
			            url: '<?=$this->createUrl('rtsProcess/uploadUnknownFile')?>'+"?barcode="+$('#barcode_bk').val()+"&&location="+$('#location_bk').val(),
					    type: "post",
					    data: form,
					    processData: false,
					    contentType: false,
			            success: function(r) {
			                if(r=="done")
			                 {
			                 	$('#notifc').notify({message: {html: "Upload Success"}}).show();
							 }else
							 {
								$('#notifc').notify({message: {html: "Upload Fail"},type: 'danger'}).show();
				             }
				             $('input#scan').focus();
				         },
			            error: function(e) {
			                console.log(e);
			            }
			        });			
		}
		return false;
	});

	$('#result tbody tr').hide();


	function formAfterSuccess(r){
		var audio=new Audio();
		audio.src='https://os.toplogistics.com.au/site/voice/' + (r.sounds.reverse().join('-')) + '.mp3';
		audio.play();
		$('#result tbody tr').hide();
		$(['status', 'area', 'msg', 'area', 'amazon', 'gatepass', 'hold']).each(function(i){
			if(r[this]) $('#result td.'+this).html(r[this]).parent().show();
		});

		if ( r.found == 1 ) {
			$('#uploadReasonDiv').show();
			$('#scaned_shipment_id').val(r.id).data('sn', r.sn);
			$('#my_sn').val(r.sn);
			$('#barcode_bk').val(r.barcode);
			$('#location_bk').val(r.location);
			if(r.id!=0)
			{
				$lastSubmit = false;
			}
		}

		if ( r.found == 0 ) {
			$('#uploadUnknownFileDiv').show();
			$('#barcode_bk').val(r.barcode);
			$('#location_bk').val(r.location);
		}

		if (r.found == 1&&$('input[name=rts_type]:checked').val()==1) {
			$('.print_btn_pdf').show();
			if($('input#auto_print').prop('checked')) $('input.print_btn_pdf').trigger('click');
		} else {
			$('.print_btn_pdf').hide();
		}

		if ($("input[name=rts_type]:checked").val()==1){
			$('#wrong_courier').show();
			$('#wrong_courier_label').show();
			$('#reason').hide();
			$('#rts_reason_label').hide();
		} else {
			$('#rts_courier').hide();
			$('#wrong_courier_label').hide();
			$('#reason').show();
			$('#rts_reason_label').show();
		}

		$('#shipment').val('');
		$('.form').removeClass('red green blue').addClass(r.color);
		if(!$('input.klocation').prop('checked')) $('input#location').val('');

		if ( r.found == 1 ) {
			let reason = $('#reason').val();
			if($('input[name=rts_type]:checked').val()==1)
			{
				reason = $('#wrong_courier').val();
			}
			var form = new FormData(document.getElementById("rts_reason_form"));
			 $.ajax({
			            url: '<?=$this->createUrl('rtsProcess/uploadRTSReason')?>'+"?id="+$('#scaned_shipment_id').val()+"&&sn="+$('#my_sn').val()+"&&reason="+reason+"&&barcode="+$('#barcode_bk').val()+"&&location="+$('#location_bk').val()+"&&rts_type="+$('input[name=rts_type]:checked').val(),
					    type: "post",
					    data: form,
					    processData: false,
					    contentType: false,
			            success: function(r) {
			                if(r=="done")
			                 {
			                 	$('#notifc').notify({message: {html: "Upload Success"}}).show();
			                 	$('#rts_received_grid_view').yiiGridView('update');
							 }else
							 {
								$('#notifc').notify({message: {html: "Upload Fail"},type: 'danger'}).show();
				             }
				             $('input#scan').focus();
				         },
			            error: function(e) {
			                console.log(e);
			            }
			        });			
		}
		return true;
	}
	function formOnSubmit()
	{
		$('#uploadReasonDiv').hide();
		$('#uploadUnknownFileDiv').hide();
		$('#uploadDamageFileDiv').hide();
		$('#scaned_shipment_id').val('');
		$('#check_in_path').val("");
		$('#my_sn').val('');
		$('#wrong_courier').val("");
		$('#search_container_no').val($('#myContainerNo').val());
		$('input#scan').focus();
	};


	$('input#shipment').focus().on('keydown', function(e) {
		if (e.which == 13) {
			if($lastSubmit==false)
			{
				$('input#shipment').val("");
				alert("please submit reason/photo before scaning next parcel.");
				return false;
			}
			formOnSubmit();
			$(this).trigger('afterBarcode');
			$('input#shipment').focus();
			return false;
		}
	}).on('afterBarcode', function() {
		$.ajax({
			'url': '<?=$this->createUrl('rtsProcess/scan')."?op=".$op?>',
			'type': 'POST',
			'data': { 'barcode': $('input#shipment').val(), 'location': $('input#location').val(), 'rts_type': $('input[name=rts_type]:checked').val()},
			success: function(r) {
				r = JSON.parse(r);
				formAfterSuccess(r);
			}
		});
		return false;
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


});
</script>
<?php $this->registerJS(ob_get_clean()); ?>


<?php
echo '<h1>Shipment RTS Received List</h1>';
?>


<?php $this->widget('zii.widgets.grid.CGridView',array(
    'cssFile' => false,
    'id'=>'rts_received_grid_view',
    'filter'=>$model,
    'dataProvider'=>$model->search(true,50,false,true,true),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
    		array('header' => 'Location','type'=>'raw','value'=>'$data->getRTSLocation(true)'),
            array('name' => 'hbn'),
            array('name' => 'ref'),
            array('header' => 'RTS Barcodes','type'=>'raw','value'=>'$data->getRTSBarcodes(true)'),
            array('header' => 'RTS Reasons','type'=>'raw','value'=>'$data->getRTSReasons(true)'),
            ['header' => 'status', 'value' => '$data->getStatus()']
        ),
    )
    
);
?>

