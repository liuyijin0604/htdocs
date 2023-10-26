<div id="wmsprod_import-tabs" style="min-height: 350px">
  <ul>
  <li><a href="#jqmw_<?=$_GET["tabid"];?>_tab1">Product Data</a></li>
  <li><a href="#jqmw_<?=$_GET["tabid"];?>_tab2">Customer SKU</a></li>
  </ul>
  <div class="q_f" id="jqmw_<?=$_GET["tabid"];?>_tab1" style="padding: 5px;">
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'enableAjaxValidation'=>false,
	'htmlOptions' => ['class'=>'import-form'],
)); ?>

	<div class="row">
		<label for="excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="template/wms_prod_template.xlsx" target="_blank">Tempalte file</a>)</label>
		<input type="file" name="excel" id="excel" />
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
  </div>
  <div class="q_f" id="jqmw_<?=$_GET["tabid"];?>_tab2" style="padding: 5px;">
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'enableAjaxValidation'=>false,
	'htmlOptions' => ['class'=>'import-form'],
)); ?>

	<div class="row">
	<label>Organisation</label>
	<input type="hidden" name="org_id" />
	<?php 
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
	<div class="row">
		<label for="excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="template/wms_prod_sku_template.xlsx" target="_blank">Tempalte file</a>)</label>

		<input type="file" name="excel" id="excel" />
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
  </div>
</div>


<div id="result"></div>
<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');
	$('form.import-form', win).on('success', function(e, r){
		win.data('opener').trigger('onOpen');
		win.jqmHide();
	});
	$('#wmsprod_import-tabs', win).tabs();
});
</script>