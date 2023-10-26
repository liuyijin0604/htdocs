<div class="container">
    <br>
    <div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-job-form',
	'enableAjaxValidation'=>true,
)); ?>
<div class="row">
     <div class="col col-md-2 col-sm-4">
	<div class="form-group">
	    <?php echo $form->labelEx($model,'type'); ?>
            <?php echo $form->dropDownList($model, 'type', $this->t(WmsJob::$types_client), array('empty' => $this->t('Select One'),'class' => 'form-control')); ?>
	</div>
     </div>
     <div class="col col-md-3 col-sm-6">
	<div class="form-group">
		<?php echo $form->labelEx($model,'org_id'); ?>
		<?php echo $form->hiddenField($model,'org_id');
			$acname = empty($_GET["tabid"])? 'org_ac' : $_GET["tabid"].'_org_ac';
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'name' => $acname,
				'sourceUrl' => array('accounts/ownerSuggest'),
				'value' => empty($model->customer)? '' : $model->customer->name,
				'options' => array(
						'showAnim' => 'fold',
						'minLength' => 1,
						'delay' => 200,
						'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
						'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
				),
				'htmlOptions' => array(
					'class' => 'required form-control',
					'size' => '30',
				),
		));
		?>
         </div>
     </div>
     <div class="col col-md-2 col-sm-4">
	 <div class="form-group">
	<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model, 'status', $this->t(WmsJob::$states), array('empty' => $this->t('Select One'),'class' => 'form-control')); ?>
	 </div>
     </div>
  
    </div>
    <div class="row">
       <div class="col col-md-2 col-sm-4">
	  <div class="form-group">
	      <?php echo $form->labelEx($model,'po'); ?>
              <?php echo $form->textField($model,'po',array('size'=>20,'maxlength'=>20,'class' => 'form-control')); ?>
	 </div>
      </div>
        <div class="col col-md-2 col-sm-4">
           <div class="form-group">
	        <?php echo $form->labelEx($model,'ref'); ?>
                <?php echo $form->textField($model,'ref',array('size'=>20,'maxlength'=>50,'class' => 'form-control')); ?>
	   </div>
        </div>
        <div class="col col-md-2 col-sm-4">
           <div class="form-group">
	      <?php echo $form->labelEx($model,'oc_id'); ?>
              <?php echo $form->textField($model,'oc_id',array('size'=>11,'maxlength'=>11,'class' => 'form-control')); ?>
	   </div>
        </div>
    </div>
        <div class="row">
        <div class="col col-md-2 col-sm-4">
            <div class="form-group">
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
                                        'class'=>'form-control'
				),
		));
		?>
	    </div>
        </div>
        <div class="col col-md-2 col-sm-4">
            <div class="form-group">
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
                                        'class'=>'form-control'
				),
		));
		?>
	</div>
        </div>
    </div>
	<div class="form-group">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'),array('class'=>'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>
    </div>
</div><!-- form -->