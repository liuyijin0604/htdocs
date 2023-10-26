<style type="text/css">
label.left {
	float: left;
	min-width: 70px;
}
</style>
  <div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'email-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	<div class="row"><label class="left">To:</label>
		<?php echo CHtml::textField('extra[to]', empty($model->mdata['to'])? '' : $model->mdata['to'], array('size' => 60)); ?> <span class="count"></span>
	</div>
	
	<div class="row"><label class="left">CC:</label>
		<?php echo CHtml::textField('extra[cc]', empty($model->mdata['cc'])? '' : $model->mdata['cc'], array('size' => 60)); ?> <span class="count"></span>
	</div>

	<div class="row">
		<label class="left">Subject:</label>
		<?php echo $form->textField($model,'subject',array('size' => 60,'maxlength' => 500)); ?>
	</div>
	
	<div class="row" style="min-height:330px">
		<?php //echo $form->textArea($model,'body',array('rows'=>6, 'cols'=>50));
		$this->widget('ext.ckeditor.CKEditorWidget',array(
		  "model"=>$model,
		  "attribute"=>'body',
		  "id" => 'email_body_'.$_GET['tabid'],
		  "ckBasePath"=>'//cdn.ckeditor.com/4.4.7/standard/',
		  //"defaultValue"=>"Test Text",
		  "config" => array(
			  "height"=>"200px",
			  "width"=>"570px",
			  "toolbar"=>"Standard",
			  ),
		  ));
		?>
		
	</div>
	
	<div class="row">
	<?php
	echo '<br />', CHtml::label($this->t('Attachments'),'uploader');
	$pphash = FileRepo::uploadHash($model, 80);
	$this->widget('application.extensions.plupload.PluploadWidget', array(
	 'config' => array(
		 'url' => $this->createUrl('filerepo/upload/'.$pphash),
		 'max_file_size' => Yii::app()->params['maxFileSize'],
		 'unique_names' => true,
		 'file_list_height' => 60,
		 'visible_header' => false,
		 'filters' => array(
			  array('title' => Yii::t('app', 'JPG, PDF, Word, Excel, TXT, EML files'), 'extensions' => 'pdf,doc,docx,xls,xlsx,jpg,txt,jpeg,eml'),
		  ),
		 //'resize' => array('width' => 800, 'height' => 800, 'quality' => 80),
		 'language' => Yii::app()->language,
		 'max_file_number' => 5,
		 'autostart' => true,
		 'jquery_ui' => false,
		 'reset_after_upload' => true,
	 ),
	 'id' => $_GET['tabid'].'_attachments_uploader',
	));
	echo CHtml::HiddenField('ppupload', $pphash);
	?>
	</div>
	
<?php if(!$model->isNewRecord): ?>

	<div class="row">
		<?php echo $form->labelEx($model,'note'); ?>
		<?php echo $form->textArea($model,'note',array('rows'=>3,'cols'=>50)); ?>
	</div>
	
<?php endif; ?>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Send' : 'Save')); ?>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('.jqmWindow.jqmID<?=$_GET['jqmid'];?>');
	var tab = $('#<?=$_GET["tabid"];?>');
	var tabs = $('#<?=$_GET["tabid"];?>-email-tabs').tabs();
	
	tabs.off('click', 'input.ab_add').on('click', 'input.ab_add', function(){
		var t = $('input#extra_'+$(this).data('to'), tabs);
		var tv = t.val();
		var es = [];
		if(tv.length > 0){
			tv.replace(/,+$/,'');
			es = tv.split(',');
		}
		$('input.chkbox:checked', tabs).each(function(i){
			var e = $(this).val();
			if(e.length == 0) return;
			if($.inArray(e, es) == -1) es.push(e);
		});
		t.val(es.join(',', es));
		t.trigger('change');
	}).off('click', 'input#chkbox_all').on('click', 'input#chkbox_all', function(r){
		$('input.chkbox', tabs).attr('checked', this.checked);
	}).off('change', 'input#extra_to, input#extra_cc, input#extra_bcc').on('change', 'input#extra_to, input#extra_cc, input#extra_bcc', function(){
		var ec = $(this).val().split(',');
		$(this).next('.count').text(ec[0]==''? '' : ec.length);
	});
	
	$('.grid-view .summary').css('margin-top', '-15px');
	
	$('form#email-form', tabs).data({reset: true}).on('success', function(e, r){
		tab.trigger('load');
	});
	
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