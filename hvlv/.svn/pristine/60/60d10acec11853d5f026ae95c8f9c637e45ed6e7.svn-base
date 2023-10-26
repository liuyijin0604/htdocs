<h1><?=$this->t('Export Search');?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'search-export-form',
	'enableAjaxValidation' => false,
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>

	<p class="note"><?=$this->t('Fields with');?> <span class="required">*</span> <?=$this->t('are required.');?></p>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('From Date:','fd'); ?>
		<?php echo CHtml::textField('date[from]', date('Y-m-d', strtotime('-7 day')), array('size' => 12, 'id' => 'fd_'.$_GET["tabid"],'class' => 'date_input')); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('To Date:','td'); ?>
		<?php echo CHtml::textField('date[to]', date('Y-m-d'), array('size' => 12, 'id' => 'td_'.$_GET["tabid"],'class' => 'date_input')); ?>
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
	var panel = win.data('opener').data('panel');
	
	$('#search-export-form', win).on('submit', function(){
		$('input#sq').val($('.filters input, .filters select', panel).serialize() + '&' + $('.search-form form', panel).serialize());
		return true;
	});

});
</script>