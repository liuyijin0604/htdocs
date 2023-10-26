
  <div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'new-signaure-form-mail',
	'enableAjaxValidation'=>false,
)); ?>
	<div class="row">
		<label class="left">Signature Name:</label>
		<?php echo $form->textField($model,'sig_name',array('size' => 20,'maxlength' => 50)); ?>
	</div>
	
	<div class="row" style="min-height:330px">
		<?php //echo $form->textArea($model,'body',array('rows'=>6, 'cols'=>50));
		$this->widget('ext.ckeditor.CKEditorWidget',array(
		  "model"=>$model,
		  "attribute"=>'new_sig_body',
		  "id" => 'imports_sig_body'.$_GET['tabid'],
		  "config" => array(
			  "height"=>"150px",
			  "width"=>"570px",
			  "toolbar"=>"Standard",
			  ),
		  ));
		?>
		
	</div>
	


	<div class="row buttons">
		<?php echo CHtml::submitButton('Create'); ?>
	</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
        var tab=$('#<?=$_GET['tabid']?>');
        $('form#new-signaure-form-mail', win).on('submit', function(e, r){
            if(!$('#ImportsMailSignature_sig_name',win).val()){
                myApp.alert("Please Input the signature Name;");
                return false;
        }
        });

	$('form#new-signaure-form-mail', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});	
	win.on('close', function(){
		if(CKEDITOR){
			for(i in CKEDITOR.instances){
				if($('#'+i, win).length > 0) CKEDITOR.instances[i].destroy(true);
			}
		}   
                CKEDITOR.instances['ota-ImportsMailSignature_sig_body'].destroy(true)
               tab.trigger('load');
	});
});
</script>