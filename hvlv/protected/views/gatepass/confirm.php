<h2>Gate Pass Confirm</h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'gate-pass-confirm-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php echo $form->dropDownList($model,'dpt_id', Org::dptList(), array('empty' => 'Select One')); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'ref'); ?>
		<?php echo $form->textField($model, 'ref', array('size'=>20,'maxlength'=>30)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'company'); ?>
		<?php echo $form->textField($model, 'company', array('size'=>30,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'driver'); ?>
		<?php echo $form->textField($model, 'driver', array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model,'rego'); ?>
		<?php echo $form->textField($model, 'rego', array('size'=>10,'maxlength'=>10)); ?>
	</div>

    <div class="row">
        <canvas style="border: 1px solid #ddd;"></canvas>
        <?php echo CHtml::hiddenField('GatePass[sig]'); ?>
    </div>


    <div class="row buttons">
		<?php echo CHtml::button($this->t('Clear Signature'), array('class' => 'clear_gp_sig_confirm_btn')); ?> &nbsp;
		<?php echo CHtml::submitButton($this->t('Finish'), array('class' => 'save_btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>

<script type='text/javascript' src='<?php echo Yii::app()->request->baseUrl; ?>/js/signature_pad.min.js'></script>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var signaturePad = new SignaturePad(document.querySelector("canvas"));
        $('input.clear_gp_sig_confirm_btn').on("click", function (event) {
            signaturePad.clear();
        });

        $('#gate-pass-confirm-form').on("submit", function (event) {
            $('#GatePass_sig').val(signaturePad.toDataURL());
            return true;
        });

        var win = $('#jqmw_<?=$_GET["tabid"];?>');
        $('form#gate-pass-confirm-form', win).on('success', function(e, r){
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });

    });
</script>