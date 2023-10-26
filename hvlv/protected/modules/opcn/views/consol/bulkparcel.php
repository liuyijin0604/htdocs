<div id="<?=$_GET['tabid']?>-bulkparcels-tabs">
  <ul>
  <li><a href="#<?=$_GET["tabid"];?>-add">Bulk Add</a></li>
  <li><a href="#<?=$_GET["tabid"];?>-remove">Bulk Remove</a></li>
  <li><a href="#<?=$_GET["tabid"];?>-rbw">Remove by Weight</a></li>
  </ul>
<div id="<?=$_GET["tabid"];?>-add"><div class="pane form" style="min-height:400px">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'bulk-add-form',
	'enableAjaxValidation'=>false,
));
?>
	<div class="row s1">
		<?php echo CHtml::label('Connotes','mhbns'); ?>
		<?php echo CHtml::textArea('mhbns', '', array('cols'=>30, 'rows' => 10)); ?>
	</div>
	<div class="row s2">
	</div>
	<div class="row cbta" style="display:none">
		<label><input id="chkbox_all" type="checkbox" /> Toggle All</label>
	</div>
	<div class="row buttons">
		<?php echo CHtml::hiddenField('act', 'add'); ?>
		<?php echo CHtml::submitButton('Add'); ?>
	</div>
<?php $this->endWidget(); ?>
</div>
</div>
<div id="<?=$_GET["tabid"];?>-remove"><div class="pane form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'bulk-remove-form',
	'enableAjaxValidation'=>false,
));
?>
	<div class="row">
		<?php echo CHtml::label('Connotes','mhbns'); ?>
		<?php echo CHtml::textArea('mhbns', '', array('cols'=>30, 'rows' => 10)); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::hiddenField('act', 'brm'); ?>
		<?php echo CHtml::submitButton('Remove'); ?>
	</div>
<?php $this->endWidget(); ?>
</div>
</div>
<div id="<?=$_GET["tabid"];?>-rbw"><div class="pane form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'bulk-rbw-form',
	'enableAjaxValidation'=>false,
));
?>
	<div class="row">
		<?php echo CHtml::label('Weight','wt'); ?>
		<?php echo CHtml::textField('weight', '', array('size'=>10,'maxlength'=>10)); ?>kg
	</div>

	<div class="row">
		<?php echo CHtml::label('Priority','st'); ?>
		<?php echo CHtml::radioButtonList('st', '1', ['1' => 'Light Parcels', '2' => 'Heavy Parcels'], array('labelOptions' => array('class' => 'radio_label'), 'separator' => '&nbsp;&nbsp')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::hiddenField('act', 'rbw'); ?>
		<?php echo CHtml::submitButton('Remove'); ?>
	</div>
<?php $this->endWidget(); ?>
</div>
</div>
</div>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	var tabs = $('#<?=$_GET["tabid"];?>-bulkparcels-tabs').tabs();

	$('form#bulk-add-form', win).on('success', function(r){
		var f = $('form#bulk-add-form', win);
		if(r.s2){
			$('.s2', f).html(r.s2);
			$('.s1 textarea, .s1 input', f).val('');
			$('.s1', f).hide();
			$('.cbta', f).show();
		}else{
			win.data('opener').trigger('update-parcels-grid');
			win.jqmHide();
		}
	});

	win.off('click').on('click', 'input#chkbox_all', function(r){
		$('input.chkbox:visible', win).prop('checked', $(this).is(':checked'));
	});

	$('form#bulk-remove-form, form#bulk-rbw-form', win).on('success', function(){
		win.data('opener').trigger('update-parcels-grid');
		win.jqmHide();
	});

});
</script>