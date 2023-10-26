<style type="text/css">
	.uploading {
	    background: url("<?php echo Yii::app()->request->baseUrl; ?>/images/ajaxLoader.gif") no-repeat 0 0 !important;
	    background-size: 20px 20px !important;
	    background-color: white !important;
	}

</style>

<div id="select_device" style="display: none;">Selected Device: <select id="selected_device" onchange=onDeviceSelected(this);></select></div>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'blank-pltLabel-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => ['class' => 'ifrm-form', 'target' => '_blank'],
)); ?>
	
	<div class="row">
		<input type="text" name="id" id="id" value="<?php echo $model->id;?>" style="display: none;" />
	</div>
	<div class="row">
		<label>Date(日期)</label>
		<?php echo CHtml::textField('date', date("Y-m-d"), array('size' => 12, 'id' => 'date', 'name' => 'date', 'class' => 'date_input')); ?>
	</div>
	<div class="row">
		<?php
		if($model->service == ImcoConsol::AIRCONSOL){
			echo '
			<label>AWB Nunber</label>
			<input type="text" value="'.substr($model->awb, -5).'" name="awb" size="15" />';
		}
		else{
			echo '
			<label>Cont No.(后五位柜号)</label>
			<input type="text" value="'.substr($model->container_no, -5).'" name="cont_no" size="15" />';
		}
		?>		
	</div>
	<div class="row">
		<label>Total Pcs(总箱数)</label>
		<input type="text" value="" name="total_pcs" size="15" />
	</div>
	<div class="row">
		<label>Total Pallets(总板数)</label>
		<input required type="text" name="total_pallets" size="15" value="" />
	</div>
	<div class="row">
		<label>Notes/(拆柜人大写字母)</label>
	<?php
		$notesName = "";		
		if(!empty(User::getCurrentUser())){			
			$notesName = substr(User::getCurrentUser()->lname, 0, 1);
		}
		echo '<input type="text" name="notes" size="15" value="'.$notesName.'"/>';	
	?>

	</div>

	</div>

	<!-- <div class="row">
		<?php echo CHtml::checkbox('previous_label', false) . 'previous label';?>
	</div> -->

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Generate'),["id"=>"submitButton"]); ?>
	</div>	
	<div class="uploading" style="display: none;">
		<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/ajaxLoader.gif" width="24" />
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

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


	$('#submitButton').click(function(e){
		$('#submitButton').prop('disabled', true);
		$('.uploading').show();

		<?php $url = $this->createUrl('unpackContainer/printBlankPalletLabels');?>
		//console.log(document.getElementById("blank-pltLabel-form"));
		var form = new FormData(document.getElementById("blank-pltLabel-form"));
	    $.ajax({
	             url: '<?=$url?>',
	             type: "POST",
	             data: form,
	             processData: false,
	             contentType: false,             
	             success: function(r) {
	             	// alert(r);
	             	r = JSON.parse(r);
					if (r.result) {
						let file = r.file;
						// console.log(file);
						writeToSelectedPrinter(file);

						$('#submitButton').prop('disabled', false);
						$('.uploading').hide();
					} else {
						alert("Can not print!");
					}

	           },
	             error: function(e) {
	                 console.log(e);
	             }
	         });
	    return false;
	});

</script>
