<div class="form" style="min-height: 500px">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'wms-charge-code-form',
	'enableClientValidation' => true,
));
?>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'type'); ?>
		<?php echo $form->dropDownList($model, 'type', $this->t(WmsChargeCode::$types), array('empty' => $this->t('Select One'))); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'v_from'); ?>
		<?php echo $form->textField($model, 'v_from', ['class' => 'date_input']); ?>
	</div>

	<div class="row rowcol">
		<?php echo $form->labelEx($model, 'v_to'); ?>
		<?php echo $form->textField($model, 'v_to', ['class' => 'date_input']); ?>
	</div>

	<div class="row rowcol rowleft">
		<?php echo $form->labelEx($model, 'note'); ?>
		<?php echo $form->textArea($model, 'note', ['rows' => 5, 'cols' => 40]); ?>
	</div>

	<div class="row">
		<div class="col">
			<span> <h2> Weight Range (Unit kg) </h2></span>

			<div id="weight-range-grid" class="grid-view editableGrid">
				<table class="items">
					<thead>
					<tr>
						<th id="weight-range-grid_c0">From</th><th id="weight-range-grid_c1">To</th><th id="weight-range-grid_c3">base</th><th id="weight-range-grid_c4">perkg</th><th id="weight-range-grid_c5">minimum</th><th class="button-column" id="weight-range-grid_c2">&nbsp;</th></tr>
					</thead>
					<tfoot>
					<tr><td><input style="width:100%" id="weight-range-grid_weight_lo" name="Rate[weight_lo]" type="text" maxlength="10"></td><td><input style="width:100%" id="weight-range-grid_weight_hi" name="Rate[weight_hi]" type="text" maxlength="10"></td><td><input style="width:100%" id="weight-range-grid_base" name="Rate[base]" type="text" maxlength="10"></td><td><input style="width:100%" id="weight-range-grid_perkg" name="Rate[perkg]" type="text" maxlength="10"></td><td><input style="width:100%" id="weight-range-grid_minimum" name="Rate[minimum]" type="text" maxlength="10"></td><td><a href="" class="save_btn add_btn" title="Add">Add</a></td></tr></tfoot>
					<tbody>
					<tr><td colspan="6" class="empty"><span class="empty">No results found.</span></td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
	$(document).ready(function(e){

	var win = $('#jqmw_<?=$_GET["tabid"];?>');

	// current data
	var curData = <?php echo json_encode($model->getWeightRangeByArrayByChargecode()); ?>;

	// set default rate data
	var allRowIndex = 0;
	appendRateData(curData);

	function appendRateData(rateData){
		$('#weight-range-grid .items tbody td.empty', win).parent().remove();
		var wtpl = $('#weight-range-grid .items tfoot', win);
		var row = wtpl.find('tr').clone();
		for ( var i in rateData ) {
			allRowIndex++;
			var r = wtpl.find('tr').clone();
			$('.add_btn',r).replaceWith('<a href="javascript:;" data-index="'+allRowIndex+'"class="delete_btn" title="Remove">Remove</a>');
			$('input', r).each(function(){
				var n = $(this).attr('name');
				$(this).attr('name', n+'[]');
			});
			$('#weight-range-grid .items tbody', win).append(r);
			r.find('#weight-range-grid_weight_lo').val(rateData[i]['weight_lo']);
			r.find('#weight-range-grid_weight_hi').val(rateData[i]['weight_hi']);
			r.find('#weight-range-grid_base').val(rateData[i]['base']);
			r.find('#weight-range-grid_perkg').val(rateData[i]['perkg']);
			r.find('#weight-range-grid_minimum').val(rateData[i]['minimum']);
		}
	}

	$('form#wms-charge-code-form', win).on('success', function(e, r){
		win.jqmHide();
		$('#wms-charge-code-grid').yiiGridView('update');
	});

	$('#weight-range-grid .add_btn', win).on('click', function(e){
		e.preventDefault();
		allRowIndex++;
		$('#weight-range-grid .items tbody td.empty', win).parent().remove();
		var r = $(this).parents('tr').clone();
		$('.add_btn',r).replaceWith('<a href="javascript:;" data-index="'+allRowIndex+'"class="delete_btn" title="Remove">Remove</a>');
		$('input', r).each(function(){
			var n = $(this).attr('name');
			$(this).attr('name', n+'[]');
		});
		$('#weight-range-grid .items tbody').append(r);
		$(this).parents('tr').find('input').val('');
		return false;
	});

	$('#weight-range-grid', win).on('click','.delete_btn',function(e){
		e.preventDefault();
		if ( confirm( 'Are you sure remove the rate data?') ) {
			$(this).parents('tr').remove();
			return false;
		}
		return true;
	});
});
</script>