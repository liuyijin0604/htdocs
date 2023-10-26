<?php
switch($_GET['type']){
	case 'inv':
		$title = 'Invoice';
	break;
	case 'sfr':
		$title = 'Sea Freight';
	break;
}
?>
<h1><?=$this->t($title.' Report');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'cnl-report-form',
	'enableAjaxValidation' => false,
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>


	<div class="row">
		<?php echo CHtml::label('From date:', 'fromdate'); ?>
		<?php echo CHtml::textField('fromdate', '', array('class' => 'date_input', 'autocomplete' => 'off')); ?>
	</div>

	<div class="row">
		<?php echo CHtml::label('To date:', 'todate'); ?>
		<?php echo CHtml::textField('todate', '', array('class' => 'date_input', 'autocomplete' => 'off')); ?>
	</div>

	<div class="row buttons">
		<input id="sq" type="hidden" name="q" />
		<?php echo CHtml::submitButton($this->t('Export')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	
	$('#cnl-report-form', win).validate();

});
</script>