<div id="exprod_import-tabs" style="min-height: 350px">
  <ul>
  <li><a href="#jqmw_<?=$_GET["tabid"];?>_tab1">Export</a></li>
  <li><a href="#jqmw_<?=$_GET["tabid"];?>_tab2">Import</a></li>
  </ul>
  <div class="q_f" id="jqmw_<?=$_GET["tabid"];?>_tab1" style="padding: 5px;">
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'enableAjaxValidation'=>false,
	'method' => 'GET',
	'htmlOptions' => ['target' => '_blank', 'class' => 'ifrm-form'],
)); ?>

	<div class="row rowcol">
		<label><?=$this->t('POC');?></label>
		<?php echo CHtml::dropDownList('poc', '', ExChannel::getPocs(), array('empty' => 'Select One')); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Export')); ?>
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

	<div class="row rowcol">
		<label><?=$this->t('POC');?></label>
		<?php echo CHtml::dropDownList('poc', '', ExChannel::getPocs(), array('empty' => 'Select One')); ?>
	</div>
	<div class="row">
		<label for="excel">Excel - <small>.csv/.xls/.xlsx File</small></label>
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
	$('#exprod_import-tabs', win).tabs();
});
</script>