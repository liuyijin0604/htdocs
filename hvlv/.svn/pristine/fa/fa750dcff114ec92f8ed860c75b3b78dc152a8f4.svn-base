<h2><?=$title;?></h2>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id' => 'filter-shipment-form',
	'enableAjaxValidation' => false,
	'action' => $this->createUrl('report/export', ['m' => 'account', 'rpt' => $_GET['rpt']]),
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row rowcol">
		<?php echo $form->labelEx($model,'consol_id'); ?>
		<?php echo $form->hiddenField($model,'consol_id', array('data-ov' => $model->consol_id));
			$acname1 = empty($_GET["tabid"])? 'consol_ac' : $_GET["tabid"].'_consol_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname1,
				'sourceUrl' => array('excoConsol/suggest'),
				'value' => '',
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '30',
				),
		));
		?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Report')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
