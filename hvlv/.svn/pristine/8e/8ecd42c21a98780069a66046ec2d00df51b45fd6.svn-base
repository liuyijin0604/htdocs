<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-job-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model, 'type', $this->t(WmsJob::$types), array('empty' => $this->t('Select One'), 'disabled' => $model->type == WmsJob::TYPE_PICK_LOAD ? 'disabled' : '')); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model, 'status', $this->t(WmsJob::$states), array('empty' => $this->t('Select One'))); ?>
	</div>
	<!--@Author: Nero @Date:2021/6/3 @Department-->
	<div class="row rowcol">
	<?php echo $form->labelEx($model,'dpt_id'); ?>
		<?php //echo $form->dropDownList($model, 'dpt_id', Org::dptList3PL(), ['empty' => 'Select One']); ?>
		<?php if ($model->isNewRecord) {
			echo $form->dropDownList($model, 'dpt_id', Org::dptList3PL(), ['empty' => 'Select One']);
		} else {
			echo $form->dropDownList($model, 'dpt_id', Org::dptList3PL(), ['empty' => 'Select One', 'disabled' => 'disabled']);
		} ?>
	</div>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model,'po'); ?>
<?php echo $form->textField($model,'po',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'ref'); ?>
<?php echo $form->textField($model,'ref',array('size'=>20,'maxlength'=>50)); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model,'org_id'); ?>
		<?php echo $form->hiddenField($model,'org_id');
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

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'oc_id'); ?>
<?php echo $form->textField($model,'oc_id',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row rowcol rowleft">
	<?php echo $form->labelEx($model,'sales_id'); ?>
		<?php echo $form->hiddenField($model,'sales_id');
			$acname = empty($_GET["tabid"])? 'sales_ac' : $_GET["tabid"].'_sales_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('user/suggest'),
				'value' => empty($model->sales)? '' : $model->sales->getName(),
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
				),
		));
		?>
	</div>

	<div class="row rowcol">
	<?php echo $form->labelEx($model,'pm_id'); ?>
		<?php echo $form->hiddenField($model,'pm_id');
			$acname = empty($_GET["tabid"])? 'pm_ac' : $_GET["tabid"].'_pm_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('user/suggest'),
				'value' => empty($model->pm)? '' : $model->pm->getName(),
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 2,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'size' => '20',
				),
		));
		?>
	</div>
      <?php if(in_array($model->type, [10,30])):?>
      <div class="row rowcol rowleft">
       <?php echo CHtml::label('Create Invoice:','create_invoice');
             echo CHtml::checkbox('create_invoice','');
         ?>
      </div>
     <div class="row rowcol">
       <?php echo CHtml::label('Link Consol:','consol_no');
             echo CHtml::textField('consol_no','');
         ?>
      </div>
      <?php endif;?>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->