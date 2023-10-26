<h1><?=$this->t('Security Declaration');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'sec_dec-form',
	'enableAjaxValidation'=>false,
	'action' => $this->createUrl('ediJob/securityDec', ['id' => $model->id]),
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form']
));?>

	<div class="row">
		<label>Content:</label>
		<?php echo CHtml::checkboxList('option[content]', empty($model->mdata['secDecOpt']['content'])? [] : $model->mdata['secDecOpt']['content'], EdiJob::$secDecOptions['content'], array('separator' => '&nbsp;&nbsp;&nbsp;', 'labelOptions' => ['class' => 'radio_label'])); ?>
		<?php echo CHtml::textField('option[content_other]', empty($model->mdata['secDecOpt']['content_other']) ? '' : $model->mdata['secDecOpt']['content_other']); ?>
	</div>

	<div class="row">
		<label>ASIC Signature:</label>
		<?php echo CHtml::dropdownList('option[asic]', empty($model->mdata['secDecOpt']['asic'])? '' : $model->mdata['secDecOpt']['asic'], EdiJob::$secDecOptions['asic'], array('empty'=>'Select One')); ?>
	</div>

	<div class="row">
		<label>Received From: (RACA Code)</label>
		<?php echo CHtml::textField('option[received_from]', empty($model->mdata['secDecOpt']['received_from'])? '' : $model->mdata['secDecOpt']['received_from'], array('size'=>'30')); ?>
	</div>

	<div class="row">
		<label>Lodgement Trucking:</label>
		<?php echo CHtml::dropdownList('option[trucking]', empty($model->mdata['secDecOpt']['trucking'])? '' : $model->mdata['secDecOpt']['trucking'], EdiJob::$secDecOptions['trucking'], array('empty'=>'Select One')); ?>
	</div>

	<div class="row">
		<label>Screen Method:</label>
		<?php echo CHtml::dropdownList('option[screening]', empty($model->mdata['secDecOpt']['screening'])? 'XRY' : $model->mdata['secDecOpt']['screening'], EdiJob::$secDecOptions['screening'], array('empty'=>'Select One')); ?>
	</div>

	<div class="row">
		<label>Exemption Code:</label>
		<?php echo CHtml::dropdownList('option[exemption]', empty($model->mdata['secDecOpt']['exemption'])? '' : $model->mdata['secDecOpt']['exemption'], EdiJob::$secDecOptions['exemption'], array('empty'=>'Select One')); ?>
	</div>

	<div class="row">
		<label>Additional Info:</label>
		<?php echo CHtml::dropdownList('option[info]', empty($model->mdata['secDecOpt']['info'])? 10 : $model->mdata['secDecOpt']['info'], EdiJob::$secDecOptions['info'], array('empty'=>'Select One')); ?>
	</div>

	<div class="row">
		<label>Override Time:</label>
		<?php echo CHtml::checkbox('override_time', null); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::button($this->t('Save'), ['id' => 'save_btn']); ?> &nbsp; 
		<?php echo CHtml::submitButton($this->t('Print')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('#save_btn', win).click(function(){
		$.post($('#sec_dec-form', win).attr('action'), $('#sec_dec-form', win).serialize(), function(){
			win.jqmHide();
		});
		return false;
	});

});
</script>