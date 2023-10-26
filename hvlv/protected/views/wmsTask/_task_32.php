<div class="form">
<div style="right: 20px; text-align: right; position: absolute;">
<?php if (!empty($model->job->customer->extra['carton_label'])) { ?>
	<a href="<?=$this->createUrl('wmsTask/autoCreatePkg', array('id' => $model->id));?>" class="ajax_link" id="auto_pack"><div style="background-position: -48px -688px" class="icon"></div> Auto Create</a>
<?php } ?>
</div>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-task32-form',
	'enableAjaxValidation'=>false,
)); ?>
<h4>Packed Cartons</h4>
<div class="row grid-view">
<div class="row">
  <?php echo $form->labelEx($model, 'Ref - Photo & Mark'); ?>
  <?php echo CHtml::textField('WmsTask[ref]', @$model->ref, array('size'=>60)); ?>
</div>
<table id="t32_item" class="items tsk_item">
<thead><tr><th width="25">#</th><th width="100" class="col_pl">Weight (Kg)</th><th width="200">DIM (cm)</th><th>Notes</th></tr></thead>
<tbody>
</tbody>
<tfoot>
	<tr><td><button class="add btn btn-info"><b>+</b></button></td><td class="tot_wt" align="right"></td><td class="tot_vol" align="right"></td><td>&nbsp;</td></tr>
</tfoot>
</table>
</div>
	<div>
		<?php
			if($model->mainTask->type=='3010'){
				 echo CHtml::label('Pallets','');
				 echo CHtml::telField('pallets',$model->mdata['Palltes'],false); 
			}
		?>
		
   </div>
	<div>
		<?php echo CHtml::label('From Product','');
		echo CHtml::checkBox('from_product',false); 
			  
		?>
	</div>
	<br/>

	<div class="row buttons">
		<?php echo CHtml::hiddenField('mdata[pkg]'); ?>
		<?php echo CHtml::submitButton($this->t('Save')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	var t = $('#t32_item', panel);

	t.data('line', '<tr><td class="sn"></td><td><input type="text" class="in_wt tci" name="wt" size="10" /></td><td><input type="text" class="in_w tci" placeholder="W" name="w" size="5" />&times;<input type="text" class="in_h tci" placeholder="H" name="h" size="5" />&times;<input type="text" class="in_d tci" placeholder="D" name="d" size="5" /></td><td><input type="text" class="in_nt" name="nt" style="width:100%" /></td></tr>').on('calcTot', function(){
		var tally = function(c){
			var tt = 0;
			$('input.'+c, t).each(function(){
				var v = $(this).val();
				if(v == '') return;
				tt += Number(v);
			});

			return tt;
		};
		var dim = function(){
			var tt = 0;
			$('input.in_w', t).each(function(){
				var w = Number($(this).val());
				var h = Number($(this).parent().find('.in_h').val());
				var d = Number($(this).parent().find('.in_d').val());
				var v = w*h*d;
				if(v == 0) return;
				tt += v;
			});
			return tt;
		};
		$('td.tot_wt', t).text(tally('in_wt'));
		$('td.tot_vol', t).text(dim());
	}).trigger('addLine');

	$('#wms-task32-form', panel).on({
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
		$('#mdata_pkg', this).val(JSON.stringify(ia));
		$('tbody input', t).prop('disabled', true);
		return true;
	});
	var item_data = <?=empty($model->mdata['pkg'])? '[]' : $model->mdata['pkg'];?>;
	var e = item_data.length - $('tbody tr', t).length;
	if(e > 0) for(var c = 0; c < e; c++) t.trigger('addLine');
	$('tbody tr', t).each(function(i){
		for(p in item_data[i]){
			$('input.in_'+p, this).val(item_data[i][p]);
		}
	});
	t.trigger('calcTot');

	$('#auto_pack', panel).on('success', function(e, r) {
		if (r.done == true) {
			myApp.notice(r.msg, 5000);
		} else {
			myApp.alert(r.msg, false);
		}
	});
});
</script>