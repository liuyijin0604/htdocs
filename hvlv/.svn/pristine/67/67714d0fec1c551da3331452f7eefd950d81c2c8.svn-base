<h3>Import Product</h3>
<hr>
<div class="container" >
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'enableAjaxValidation'=>false,
	'htmlOptions' => ['class'=>'import-form'],
	'id' => 'product-form',
)); ?>
    <div class="row"> 
	<div class="form-group">
		<label for="excel">Excel - <small>.csv/.xls/.xlsx File</small> (<a href="../../template/wms_prod_template.xlsx" target="_blank">Tempalte file</a>)</label>
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
//	var win = $('#jqmw_<?=$_GET["tabid"];?>');
//	$('form.import-form', win).on('success', function(e, r){
//		win.data('opener').trigger('onOpen');
//		win.jqmHide();
//	});
//	$('#wmsprod_import-tabs', win).tabs();
	$('#product-form').on('success', function() {
		$('.modal-footer button').trigger('click');
	});
});
</script>