<div class="form">
<div style="right: 20px;text-align:right;">
<?php if (!in_array($model->mainTask->type, [3060]) && $model->mainTask->status < 100) { ?>
<a href="<?=$this->createUrl('task/switchType', array('id' => $model->id, 'type' => 2120));?>" class="ajax-link type_switch"><div style="background-position:-16px -480px" class="icon"></div> Switch to Delivery</a>
<?php } ?>
</div>
<h2> please switch to Delivery</h2>
</div>
<!--</div>
    <div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-task211-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array(
		'enctype' => 'multipart/form-data')
)); ?>
<div class="row grid-view">
<table id="t211_item" class="items tsk_item">
<thead><tr><th width="25">#</th><th width="300">Company</th><th width="200">Driver</th><th width="100">Rego</th><th>Notes</th></tr></thead>
<tbody>
</tbody>
<tfoot>
	<tr><td><button class="add btn btn-info"><b>+</b></button></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
</tfoot>
</table>
</div>

	<div class="row buttons">
		<?php echo CHtml::hiddenField('mdata[pickup]'); ?>
		<?php echo CHtml::submitButton($this->t('Save')); ?>
	</div>

<?php $this->endWidget(); ?>
    </div>--><!-- form -->
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	$('a.type_switch').on('success', function(){
		window.location.reload(true);
	});
	var t = $('#t211_item', panel);

	t.data('line', '<tr><td class="sn"></td><td><input type="text" class="in_co" name="co" size="30" /></td><td><input type="text" class="in_dv" name="dv" size="20" /></td><td><input type="text" class="in_rg" name="rg" size="10" /></td><td><input type="text" class="in_nt" name="nt" style="width:100%" /></td></tr>').trigger('addLine');

	$('#wms-task211-form', panel).on({
		'success': function(e, r){
			$('tbody input', t).prop('disabled', false);
		},
		'error': function(e, r){
			$('tbody input', t).prop('disabled', false);
		}
	}).on('beforeSerialize', function(){
		var ia = [];
		$('tbody tr', t).each(function(){
			var o = {};
			$('input', this).each(function(){
				o[$(this).attr('name')] = $(this).val();
			});
			ia.push(o);
		});
		$('#mdata_pickup', this).val(JSON.stringify(ia));
		$('tbody input', t).prop('disabled', true);
		return true;
	});

	var item_data = <?=empty($model->mdata['pickup'])? '[]' : $model->mdata['pickup'];?>;
	var e = item_data.length - $('tbody tr', t).length;
	if(e > 0) for(var c = 0; c < e; c++) t.trigger('addLine');
	$('tbody tr', t).each(function(i){
		for(p in item_data[i]){
			$('input.in_'+p, this).val(item_data[i][p]);
		}
	});
});
</script>