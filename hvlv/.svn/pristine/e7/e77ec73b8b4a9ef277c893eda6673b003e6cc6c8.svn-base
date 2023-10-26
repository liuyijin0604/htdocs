<div class="form">
<h2><?=$model->prod->name;?> - <?=empty($model->org_id)? '' : $model->customer->name;?></h2>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-prod-org-form',
	'enableAjaxValidation'=>false,
)); ?>

<?php if($model->isNewRecord): ?>
	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'org_id'); ?>
		<?php 
		echo $form->hiddenField($model,'org_id');
			$acname = empty($_GET["tabid"])? 'org_ac' : $_GET["tabid"].'_org_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('org/ownerSuggest'),
				'value' => empty($model->customer)? '' : $model->customer->name,
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'class' => 'required',
					'size' => '30',
				),
		));
		?>
	</div>
<?php endif; ?>

	<div class="row">
	<?php echo $form->labelEx($model,'sku'); ?>
<?php echo $form->textField($model,'sku',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Expiry Accuracy', 'exp_acc');?>
		<?php echo CHtml::dropDownList('mdata[exp_acc]', @$model->mdata['exp_acc'], ['Date' => 'Date', 'Month' => 'Month'], array('empty' => $this->t('Select One'))); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Expiry/Batch Mgmt', 'exp_bat');?>
		<?php echo CHtml::dropDownList('mdata[exp_bat]', @$model->mdata['exp_bat'], ['1' => 'Expiry Only', '2' => 'Batch Only', '3' => 'Both', '9' => 'None'], array('empty' => $this->t('Select One'))); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Description', 'desc');?>
		<?php echo CHtml::textField('mdata[desc]', @$model->mdata['desc'], array('size' => 50, 'maxlength' => 50)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Value', 'value');?>
		<?php echo CHtml::textField('mdata[value]', @$model->mdata['value'], array('size' => 20, 'maxlength' => 20)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('Serial Number Format (regex)', 'serform');?>
		<?php echo CHtml::textField('mdata[sn_regex]', @$model->mdata['sn_regex'], array('size' => 30, 'maxlength' => 30)); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('Record Serial Number', 'rec_sn');?>
		<?php echo CHtml::dropDownList('mdata[rec_sn]', @$model->mdata['rec_sn'], ['1' => 'Stock In', '2' => 'Stock Out', '3' => 'Both In/Out'], array('empty' => $this->t('Select One'))); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('#wms-prod-org-form', win).on('success', function(){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
		return true;
	});

});
</script>