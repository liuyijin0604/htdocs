
<h1> Batch Change isB2B</h1>
<div class="form">
	<?php
	$form=$this->beginWidget('CActiveForm', array(
		'id'=>'batch-change-weight-import-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>['target'=>'err_result','class'=>'ifrm-form','enctype' =>'multipart/form-data'],
	));
	?>
<div class="row">
	
	</div>
   <div class="row" style="margin-top: 2px;">
		<label for="postw-batch">Batch Change isB2B - <small>.xlsx File</small></label> <br>
		<input type="file" name="batch_change_file" id="batch_change_file" />
		<!--<span>Only Output Failed:<input type="checkbox" name="upcheck"/></span>-->
	</div>
	<br>
	<br>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit', ['id' => 'submit']); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>
   
<br>
<br>

<br/><br/>
<iframe id="err_result" name="err_result" style="color: green; margin: 10px 0; border: 1px solid black;padding:20px; width: 90%; "></iframe>

<script type="text/javascript">
$(function(){
	var tab = $('#jqmw<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');

	
	$('#batch-change-weight-import-form', panel).on('submit', function(){
		$("#err_result", panel).contents().find("body").html('');
		$('#submit', panel).prop('disabled', true);
	});

	$('#err_result', panel).load(function(){
		$('#batch-change-weight-import-form #submit', panel).prop('disabled', false);
	});
});
</script>
