<div class="container">
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'wms-prod-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<div class="col col-md-3 col-sm-4">
			<div class="form-group">
			<?php echo $form->labelEx($model,'name'); ?>
			<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>100,'class'=>'form-control')); ?>
			</div>
		</div>
		<div class="col col-md-3 col-sm-4">
			<div class="form-group">
			<?php echo CHtml::label('SKU','WmsProd[ean]', ['required' => 'required']); ?>
			<?php echo $form->textField($model,'ean',array('size'=>50,'maxlength'=>50,'class'=>'form-control')); ?>
			</div>
		</div>
	</div>

	<?php echo CHtml::label('Items','items'); ?>
	<div class="form-group table-responsive">
		<table id="items" class="table table-striped table-bordered">
			<thead>
				<tr><th width="10%">#</th><th width="30%"><?=$this->t('Product');?></th><th width="20%"><?=$this->t('SKU / EAN');?> <span class="required">*</span></th><th width="10%"><?=$this->t('Qty');?> <span class="required">*</span></th></tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="1">
						<button class="moreitem btn btn-normal"><span class="glyphicon glyphicon-plus" style="color: #be3426; font-size: 1.2em; padding: 3px 10px;"></span></button>
					</td>
					<th></th><th></th><th></th>
				</tr>
			</tfoot>
		</table>
	</div>

	<div class="form-group button">
	<?php echo CHtml::submitButton($this->t($model->isNewRecord ? 'Create' : 'Save'),array('class'=>'btn btn-primary')); ?>
	</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
</div>

<?php ob_start(); ?>
<script type="text/javascript">
$(function(){
	<?php
	$items = [];
	$mebs = [];
	if (!empty($model->items)) {
		foreach ($model->items as $k => $item) {
			$org = WmsProdOrg::model()->find('org_id = :org_id AND prod_id = :prod_id', [':org_id' => Yii::app()->user->org, ':prod_id' => $item->item->id]);
			$sku = !empty($org) ? $org->sku : $item->item->ean;
			$items[] = array('id' => $item->item->id, 'name' => $item->item->name, 'sku' => $sku, 'qty' => $item->qty);
		}
	}
	?>
	var pitems = <?=json_encode($items)?> || [];
	var addItem = function(add) {
		let tb = $('#wms-prod-form #items tbody');
		let id = $('tr', tb).length;
		var add = add || 1;
		var temp = add + id - 1;
		while (add-- > 0) {
			tb.append('<tr class="'+((temp-add)%2==0? 'even' : 'odd')+'"><td class="rid">'+(temp-add+1)+'</td>\n\
			<td><input type="text" class="in_name form-control" id="name'+(temp-add)+'" name="name[]" size="10" value="'+(pitems[(temp-add)]?pitems[(temp-add)].name: '')+'" /></td>\n\
			<td><input type="hidden" class="in_id" name="id['+(temp-add)+']" value="'+(pitems[(temp-add)] ? pitems[(temp-add)].id : '')+'"/><input type="text" class="in_sku form-control" name="sku['+(temp-add)+']" size="10" value="'+(pitems[(temp-add)] ? pitems[(temp-add)].sku: '')+'" /></td>\n\
			<td><input type="text" class="in_qty form-control" name="qty['+(temp-add)+']" size="6" value="'+(pitems[(temp-add)] ? pitems[(temp-add)].qty: '')+'" /></td>\n\
			</tr>');
		}

		$('input.in_sku').on('focus', function() {
			var me = $(this);
			var url = "<?=$this->createUrl('product/suggestSKU', ['type' => 10])?>";
			me.autocomplete({
				'source': url,
				'showAnim': 'fold',
				'minLength': 2,
				'delay': 200,
				'select': function(event, ui) {
					ac_select($(this), ui.item);
					return false;
				},
				'response': function(evt, ui) {
					if (ui.content.length == 1) {
						ui.item = ui.content[0];
						ac_select($(this), ui.item);
					} else if(ui.content.length == 0) {
						$(this).val('');
					}
					return false;
				},
			});
		});
	}

	var ac_select = function(me, ui) {
		me.parent().prev().find('input[class^=in_name]').val(ui.name).data('ov', ui.name);
		me.val(ui.label).data('ov', ui.label);
		me.prevAll('input[class^=in_id]').val(ui.value).data('ov', ui.value);
	};

	$('.moreitem').off('click').on('click', function(e) {
		addItem(1);

		return false;
	});

	addItem(pitems ? pitems.length : 1);
});
</script>
<?php $this->registerJS(ob_get_clean()); ?>