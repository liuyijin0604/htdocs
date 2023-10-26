<h3>Import Product Kits/Packages/Bundles</h3>
<hr>
<div class="container" >
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'enableAjaxValidation'=>false,
	'htmlOptions' => ['class'=>'import-form'],
	'id' => 'product-kit-form',
)); ?>
	<div class="row"> 
	<div class="form-group">
		<label for="excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="../../template/wms_prod_kit_template.xlsx" target="_blank">Tempalte file</a>)</label>
		<input type="file" name="excel" id="excel" />
	</div>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Import'),array('class'=>'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
</div>


<div id="result"></div>
<script type="text/javascript">
$(function(){
	$('#product-kit-form').on('success', function() {
		$('.modal-footer button').trigger('click');
	});
});
</script>