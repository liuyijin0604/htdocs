<h1><?=$this->t('Rules');?></h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'ex-channel-grules-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<?php echo CHtml::label('Before Channels','bcs');?>
		<?php echo CHtml::textArea('mkv[bcs]', @$model->_mkv['bcs'], ['rows' => '10', 'cols' => '75']); ?><br />
	</div>

	<div class="row">
		<?php echo CHtml::label('After Channels','acs');?>
		<?php echo CHtml::textArea('mkv[acs]', @$model->_mkv['acs'], ['rows' => '10', 'cols' => '75']); ?><br />
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	
	$('#ex-channel-grules-form', win).on('success', function(){
		win.jqmHide();
	});
});
</script>