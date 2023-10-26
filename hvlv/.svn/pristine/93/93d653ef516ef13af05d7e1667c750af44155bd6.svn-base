
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'confirm-invoice-notice-email-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

    <div class="row">
        <?php echo CHtml::label('Agent Name :','forme',array('class' => 'invoice-confirm-lbl')); ?>
        <?php echo $model['to_name']; ?>
    </div>

	<div class="row">
        <?php echo CHtml::label('Agent Email<span class="required">*</span> :','forme',array('class' => 'invoice-confirm-lbl')); ?>
        <?php echo CHtml::textField('toemail',$model['to_email'],array('class' => 'required valid', 'size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('Subject<span class="required">*</span> :','forme',array('class' => 'invoice-confirm-lbl')); ?>
        <?php echo CHtml::textField('subject',$model['subject'],array('class' => 'required valid','size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row" style="height:350px;">
		<?php echo CHtml::label('Content<span class="required">*</span> :','forme',array('class' => 'invoice-confirm-lbl')); ?>


        <?php
        $this->widget('ext.ckeditor.CKEditorWidget',array(
            "model" => $model['model'],
            "attribute"=>'body',
            "config" => array(
                "height"=>"220px",
                "width"=>"775px",
                "toolbar"=>"Basic",
            ),
        ));
        ?>

		
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Send'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">

$(function(){

    var win = $('#jqmw_<?=$_GET["tabid"];?>');
    win.on('close', function(){
        if(typeof CKEDITOR != 'undefined'){
            for(i in CKEDITOR.instances){
                if($('#'+i, win).length > 0) CKEDITOR.instances[i].destroy(true);
            }
        }
    });

    $('form#confirm-invoice-notice-email-form', win).on('success', function(e, r){
        win.data('opener').trigger('onOpen');
        win.jqmHide();
    });

});

</script>