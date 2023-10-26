<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'pltLabel-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => ['class' => 'ifrm-form', 'target' => '_blank'],
)); ?>
	<div class="row">
	<label>Type of labels</label>
	<select name="t">
		<option value="50" selected>Pallet</option>
		<option value="60">Carton</option>
		<option value="70">LF</option>
	</select>
	</div>
	<div class="row">
	<label>Warehouse</label>
	<?php echo CHtml::dropDownList('wid', 106, Org::dptList(), array('prompt'=>$this->t('Select One')));?>
	</div>
	<div class="row">
	<label>Direction</label>
	<select name="d">
		<option value="vertical" selected>Vertical</option>
		<option value="horizontal">Horizontal</option>
	</select>
	</div>
	<div class="row">
	<label>Qty of labels</label>
	<input type="text" name="q" size="10" />
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t('Generate')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
$(function(){
	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	$('form#pltLabel-form', win).on('submit', function(r){
		setTimeout(function(){
			win.data('opener').trigger('onOpen');
			win.jqmHide();
		}, 1e3);
	});

});
</script>