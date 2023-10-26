<h1><?=$this->t('Export');?></h1>

<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'export-form',
	'enableAjaxValidation' => false,
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>
	
	<div class="row rowcol rowleft">
		<label>Supplier:</label>
		<?php echo CHtml::hiddenField('supplier_id');
			$acname1 = empty($_GET["tabid"])? 'supplier_ac' : $_GET["tabid"].'_supplier_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('org/supplierSuggest'),
				'value' => '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '22',
				),
		));
		?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo CHtml::label('From Date:', 'fromdate'); ?>
		<?php echo CHtml::textField('fromdate', '', ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<div class="row rowcol">
		<?php echo CHtml::label('To Date:', 'todate'); ?>
		<?php echo CHtml::textField('todate', '', ['size' => '12', 'class' => 'date_input']); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->