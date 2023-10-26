<h1><?=$this->t('Create WCA Email Template');?></h1>
<br>
<style type="text/css">
label.left {
	float: left;
	min-width: 70px;
}
</style>
<div id="<?=$_GET['tabid']?>-email-tabs">
  
  <div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'email-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
       <div class="row">
		<label class="left">Subject:</label>
		<?php echo $form->textField($model,'subject',array('size' => 50,'maxlength' => 500)); ?>
	</div>

	<div class="row" style="min-height:530px">
		<?php //echo $form->textArea($model,'body',array('rows'=>6, 'cols'=>50));
		$this->widget('ext.ckeditor.CKEditorWidget',array(
		  "model"=>$model,
		  "attribute"=>'body',
		  "id" => 'email_body_'.$_GET['tabid'],
		  "ckBasePath"=>'//cdn.ckeditor.com/4.4.7/standard/',
		  //"defaultValue"=>"Test Text",
		  "config" => array(
			  "height"=>"400px",
			  "width"=>"570px",
			  "toolbar"=>"Standard",
			  ),
		  ));
		?>
		
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->
  </div>

</div>

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var tabs = $('#<?=$_GET["tabid"];?>-email-tabs').tabs();
        $('.grid-view .summary').css('margin-top', '-15px');
	$('form#email-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
	win.on('close', function(){
		if(CKEDITOR){
			for(i in CKEDITOR.instances){
				if($('#'+i, win).length > 0) CKEDITOR.instances[i].destroy(true);
			}
		}
	});
});
</script>