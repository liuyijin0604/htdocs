<h1><?=$this->t('Link Air Freight & 3PL Job');?></h1>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'edi-wms-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row rowcol">
		<?php echo CHtml::label('Edi Job No. / AWB', 'job'); ?>
		<?php echo CHtml::textField('job', ''); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Link'); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>

<script>
$(function() {
	var win = $('#jqmw_<?=$_GET["tabid"]?>');

	$('#edi-wms-form', win).on('success', function() {
		var tab = $('#<?=$_GET["tabid"];?>');
		tab.trigger('reload_tab');
		$('#cnlorder-tabs', tab.data('panel')).tabs({active: 0, load: function(event,ui) {
			myApp.ajaxifyForm(this);
		}});
		$('#cnlorder-tabs', tab.data('panel')).tabs({active: 4, load: function(event,ui) {
			myApp.ajaxifyForm(this);
		}});
	});
});
</script>