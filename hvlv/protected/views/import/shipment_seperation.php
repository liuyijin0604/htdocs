
<h1> Upload Shipment Seperation
	<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'ship-import-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>['target'=>'err_result','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
		'action' => $this->createUrl('uploadMani/seperateShipment'),
	));
	?>
<div class="row">
	
	</div>
   <div class="row" style="margin-top: 2px;">
		<label for="postw-batch">Seperation - <small>.xlsx File</small>(<a href="/ims/PCA_Import_seperate_Template.xlsx" target="_blank">Get template file</a>)</label> <br>
		<input type="file" name="sepeship_file" id="sepeship_file" />
		<!--<span>Only Output Failed:<input type="checkbox" name="upcheck"/></span>-->
	</div>
	<br>
	<input id="submit_btn" type="submit" />

	<?php $this->endWidget(); ?>
</div>
   
<br>
<br>

<br/><br/>
<iframe id="err_result" name="err_result" style="color: green; margin: 10px 0; border: 1px solid black;padding:20px; width: 90%; "></iframe>

<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	
	$('#ship-import-form', panel).on('submit', function(){
		$("#err_result", panel).contents().find("body").html('');
		$('input[type=submit]', this).prop('disabled', true);
	});

	$('#err_result', panel).load(function(){
		$('#ship-import-form input[type=submit]', panel).prop('disabled', false);
	});
});
</script>
