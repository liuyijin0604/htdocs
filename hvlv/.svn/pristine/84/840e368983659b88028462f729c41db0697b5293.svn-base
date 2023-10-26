<h3>Edit Notice Content-<?=$model->ref?></h3>
<br>
<div class="form">
<?php 
	$id = $model->id;
	$form=$this->beginWidget('CActiveForm', array(
	'id'=>'cs_update_form',
	'enableAjaxValidation'=>false)
	);
?>
	<div class="row buttons">
		<div class="row">
			<input type="hidden" value=<?=$id?> name="id">
			<input type="hidden" value="courier" name="type" id ="type">
		</div>
	
		<div class="row" style="min-height:330px">
			<?php //echo $form->textArea($model,'body',array('rows'=>6, 'cols'=>50));
			$this->widget('ext.ckeditor.CKEditorWidget',array(
			  "model"=>$emailTpl,
			  "attribute"=>'body',
			  "id" => 'email_body_'.$_GET['tabid'],
			  "ckBasePath"=>'//cdn.ckeditor.com/4.4.7/standard/',
			  //"defaultValue"=>"Test Text",
			  "config" => array(
				  "height"=>"200px",
				  "width"=>"570px",
				  "toolbar"=>"Standard",
				  "id"=>"cs_send_email_subject"
				  ),
			  ));
			?>
			
		</div>

		<div class="row buttons">
			<?php  echo CHtml::submitButton('Save',array('class'=>'update'));?>
		</div>
		<br>
		<br>
	

	</div>
<?php $this->endWidget();?>
<br>
<br>
<br>

<script type="text/javascript">
	$(function(){
			var win = $('#jqmw_<?=$_GET["tabid"];?>');
			var tab = $('#<?=$_GET["tabid"];?>');
			var panel = $('#<?=$_GET["tabid"];?>').data('panel');
	});
</script>



		
