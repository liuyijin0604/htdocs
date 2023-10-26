<style> 
#wms-stock-edit-form table {
	display: block;
	width: 100%;
}

#wms-stock-edit-form tbody {
	display: block;
	max-height: 150px;
	overflow: auto;
	width: 100%;
}

#wms-stock-edit-form thead tr {
	display: table;
	width: 100%;
	table-layout: fixed;
}

#wms-stock-edit-form tbody tr {
	display: table;
	width: calc(100% - 1em);
	table-layout: fixed;
}
</style>
<div class="form" style="height: 500px;">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'wms-stock-edit-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row rowcol">
		<?php echo CHtml::label('owner_id', 'owner_id'); ?>
		<?php echo CHtml::hiddenField('owner_id', '', array('data-ov' => ''));
		$acname1 = empty($_GET["tabid"]) ? 'agent_ac' : $_GET["tabid"] . '_agent_ac';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $acname1,
			'sourceUrl' => array('org/ownerSuggest'),
			'value' => '',
			'options' => array(
				'showAnim' => 'fold',
				'minLength' => 2,
				'delay' => 200,
				'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]).trigger("change"); return false; }',
				'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
			),
			'htmlOptions' => array(
				'size' => '30',
			),
		));
		?>
	</div>

	<div class="row">
		<?php
		$stock = new WmsStock('search');
		$stock->unsetAttributes();
		if (!empty($_GET['WmsStock'])) {
			if (!empty($_GET['WmsStock']['prod'])) {
				$stock->prod_name = $_GET['WmsStock']['prod'];
				unset($_GET['WmsStock']['prod']);
			}
			unset($_GET['WmsStock']['edit_qty']);
			$stock->attributes = $_GET['WmsStock'];
		}
		if (!empty($_GET['owner_id'])) {
			$stock->org_id = $_GET['owner_id'];
		} else {
			$stock->org_id = 0;
		}

		if ($type === 'in') {
			$ec = new CDbCriteria;
			$ec->select = 'prod_id, org_id, SUM(qty) AS qty, SUM(qty_res) AS qty_res,dpt_id';
			$ec->group = 'prod_id,dpt_id';

			$this->widget('zii.widgets.grid.CGridView', array(
				'id' => 'wms-stock-edit-grid',
				'cssFile' => false,
				'dataProvider' => $stock->search(true, 30, $ec),
				'filter' => $stock,
				'columns' => array(
					array('name' => 'prod', 'type' => 'raw', 'value' => '"<span id=\"name_" . $data->prod_id . "\">" . @$data->prod->name . "</span>"'),
					array('name' => 'qty', 'type' => 'raw', 'value' => '"<span id=\"qty_all_" . $data->prod_id . "\">" . ($data->qty-$data->qty_res) . "</span>"'),
					array('name' => 'dpt_id', 'type' => 'raw', 'value' => '"<span id=\"dpt_id_" . $data->prod_id . "\">" . $data->dpt_id . "</span>"'),
					array('name' => 'edit_qty', 'header' => 'Expiry', 'type' => 'raw', 'value' => '"<input id=\"expiry_" . $data->prod_id . "\" type=\"text\" />"'),
					array('name' => 'edit_qty', 'header' => 'Batch No.', 'type' => 'raw', 'value' => '"<input id=\"batch_" . $data->prod_id . "\" type=\"text\" />"'),
					array('name' => 'edit_qty', 'header' => 'Location', 'type' => 'raw', 'value' => '"<input id=\"loc_" . $data->prod_id . "\" type=\"text\" />"'),
					array('name' => 'edit_qty', 'type' => 'raw', 'value' => '"<input id=\"edit_qty_" . $data->prod_id . "\" type=\"text\" />"'),
					array('name' => 'edit_qty', 'header' => '', 'type' => 'raw', 'value' => '"<button class=\"add_btn\" id=\"add_btn_" . $data->prod_id . "\">Add</button>"', 'htmlOptions' => array('width' => '30px'), 'headerHtmlOptions' => array('width' => '43px'), 'filterHtmlOptions' => array('width' => '43px')),
				),
			));
		} else if ($type === 'out') {
			$ec = new CDbCriteria;
			$ec->select = 'prod_id, org_id, SUM(qty) AS qty, SUM(qty_res) AS qty_res, batch, expiry,dpt_id';
			$ec->group = 'prod_id, batch, expiry,dpt_id';
			$ec->addCondition('qty - qty_res > 0');

			$this->widget('zii.widgets.grid.CGridView', array(
				'id' => 'wms-stock-edit-grid',
				'cssFile' => false,
				'dataProvider' => $stock->search(true, 30, $ec),
				'filter' => $stock,
				'columns' => array(
					array('name' => 'prod', 'type' => 'raw', 'value' => '"<span id=\"name_" . $data->prod_id . "_" . $data->batch . "_" . $data->expiry . "\">" . $data->prod->name . "</span>"'),
					array('name' => 'qty', 'type' => 'raw', 'value' => '"<span id=\"qty_all_" . $data->prod_id . "_" . $data->batch . "_" . $data->expiry . "\">" . ($data->qty-$data->qty_res) . "</span>"'),
					array('name' => 'dpt_id', 'type' => 'raw', 'value' => '"<span id=\"dpt_id" . $data->prod_id . "_" . $data->batch . "_" . $data->expiry . "\">" . $data->dpt_id . "</span>"'),
					//array('name' => 'dpt_id', 'value' => '$data->getBranch()', 'filter'=>CHtml::dropDownList('WmsStock[dpt_id]', $stock->dpt_id, Org::dptList3PL(), ['prompt'=>$this->t('All')]),),
					array('name' => 'edit_qty', 'header' => 'Batch No.', 'type' => 'raw', 'value' => '"<input id=\"batch_" . $data->prod_id . "_" . $data->batch . "_" . $data->expiry . "\" type=\"text\" value=\"" . $data->batch . "\" readonly = \"readonly\" />"'),
					array('name' => 'edit_qty', 'header' => 'Location', 'type' => 'raw', 'value' => '"<input id=\"loc_" . $data->prod_id . "_" . $data->batch . "_" . $data->expiry . "\" type=\"text\" />"'),
					array('name' => 'edit_qty', 'type' => 'raw', 'value' => '"<input id=\"edit_qty_" . $data->prod_id . "_" . $data->batch . "_" . $data->expiry . "\" type=\"text\" />"'),
					array('name' => 'edit_qty', 'header' => '', 'type' => 'raw', 'value' => '"<button class=\"add_btn\" id=\"add_btn_" . $data->prod_id . "_" . $data->batch . "_" . $data->expiry . "\">Add</button>"', 'htmlOptions' => array('width' => '30px'), 'headerHtmlOptions' => array('width' => '43px'), 'filterHtmlOptions' => array('width' => '43px')),
				),
			));
		}
		?>
	</div>

	<div class="row" style="margin-top: 20px; width: 70%;">
		<div class="grid-view">
			<table class="items" style="display: none;">
				<thead>
					<?php if ($type === 'in') { ?>
						<th>Prod</th><th>Qty</th><th>Branch</th><th>Location</th><th>Batch</th><th>Expiry</th><th width="73px"></th>
					<?php } else if ($type === 'out') { ?>
						<th>Prod</th><th>Qty</th><th>Branch</th><th>Location</th><th>Batch</th><th>Expiry</th><th width="73px"></th>
					<?php } ?>
				</thead>
				<tbody id="edit_stock">
				</tbody>
			</table>
		</div>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save', array('id' => 'submit_btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>

<script>
$(function() {
	var tab = $('#jqmw_<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	var stock = [];

	$('#owner_id', panel).on('change', function() {
		tab.trigger('searchProd');
	});

	$('#<?=$_GET["tabid"];?>_agent_ac', panel).on('keyup', function() {
		if (!$('#<?=$_GET["tabid"];?>_agent_ac', panel).val()) {
			$('#owner_id').val('');
			tab.trigger('searchProd');
		}
	});

	tab.on('searchProd', function() {
		$.fn.yiiGridView.update('wms-stock-edit-grid', {
			data: {'owner_id': $('#owner_id').val()}
		});
		$('#<?=$_GET["tabid"];?>_agent_ac', panel).prop('disabled', true);
	});

	$('#wms-stock-edit-form', panel).on('success', function() {
		setTimeout(function() {
			$('.popCancel', panel).trigger('click');
			$('#wms-stock-grid', panel).yiiGridView('update');
		}, 1e3);
	});

	$(document, panel).off('click', '.add_btn').on('click', '.add_btn', function(e) {
		e.preventDefault();
		var type = '<?php echo $_GET["type"]; ?>';

		if (type == 'in') {
			var id = $(this).attr('id').split('_')[2];
			var edit_qty = $(this).parent().parent().find('input[id*="edit_qty"]').val();
			edit_qty = isNaN(edit_qty) ? 0 : parseInt(edit_qty);
			$(this).parent().parent().find('input[id*="edit_qty"]').val('');
			var name = $('#name_' + id, panel).text();
			var qty = parseInt($('#qty_all_' + id, panel).text());
			var dpt_id = $(this).parent().parent().find('span[id*="dpt_id"]').text();
			var loc = $(this).parent().parent().find('input[id*="loc"]').val();
			$(this).parent().parent().find('input[id*="loc"]').val('');
			var batch = $('#batch_' + id, panel).val();
			var expiry = $('#expiry_' + id, panel).val();
			
			$('#batch_' + id, panel).val('');
			$('#expiry_' + id, panel).val('');
		} else if (type == 'out') {
			var id = $(this).attr('id').split('_')[2];
			var batch = $(this).attr('id').split('_')[3];
			var expiry = $(this).attr('id').split('_')[4];
			var edit_qty = $(this).parent().parent().find('input[id*="edit_qty"]').val();
			edit_qty = isNaN(edit_qty) ? 0 : parseInt(edit_qty);
			$(this).parent().parent().find('input[id*="edit_qty"]').val('');

			var name = $(this).parent().parent().find('span[id*="name"]').text();
			var qty = $(this).parent().parent().find('span[id*="qty_all"]').text();
			qty = isNaN(qty) ? 0 : parseInt(qty);
			var dpt_id = $(this).parent().parent().find('span[id*="dpt_id"]').text();
			var loc = $(this).parent().parent().find('input[id*="loc"]').val();
			$(this).parent().parent().find('input[id*="loc"]').val('');
		}

		if (edit_qty <= qty || type == 'in') {
			if (loc == '' || edit_qty == '') {
				alert('qty and loc is required');
			} else {
				$('.items', panel).show();

				var has = 0;
				stock.forEach(function(value, key) {
					if (value.id == id && value.loc == loc && value.batch == batch && value.expiry == expiry) {
						has = 1;
						stock[key]['qty'] = edit_qty;
						$('#stock_' + id).remove();
						$('#edit_stock').append('<tr id="stock_' + id + '_loc_' + loc + '"><td>' + name + '</td><td>' + edit_qty + '</td><td>' + dpt_id + '</td><td>' + loc + '</td><td>' + batch + '</td><td>' + expiry + '</td><td width="60px"><button class="del_btn" id="del_btn_' + id + '_loc_' + loc + '">Remove</button></td></tr>');
					}
				});

				if (has == 0) {
					$('#edit_stock', panel).append('<tr id="stock_' + id + '_loc_' + loc + '"><td>' + name + '</td><td>' + edit_qty + '</td><td>' + dpt_id + '</td><td>' + loc + '</td><td>' + batch + '</td><td>' + expiry + '</td><td width="60px"><button class="del_btn" id="del_btn_' + id + '_loc_' + loc + '">Remove</button></td></tr>');
					stock.push({id: id, qty: edit_qty,dpt_id: dpt_id, loc: loc, batch: batch, expiry: expiry});
				}
			}
		} else {
			alert('qty is not enough');
		}
	});

	$(document, panel).off('click', '.del_btn').on('click', '.del_btn', function(e) {
		e.preventDefault();
		var id = $(this).attr('id').split('_')[2];
		var loc = $(this).attr('id').split('_')[4];
		stock.forEach(function(value, key) {
			if (value.id == id && value.loc == loc) {
				stock.splice(key, 1);
			}
		});
		$('#stock_' + id + '_loc_' + loc, panel).remove();
	});

	$(document, panel).off('click', '#submit_btn').on('click', '#submit_btn', function(e) {
		$(this).prop('disabled', true);
		e.preventDefault();

		var type = '<?php echo $_GET["type"]; ?>';

		var data = {'type' : type, 'stocks' : stock, 'owner_id' : $('#owner_id').val()};
		$.ajax({
			type: 'POST',
			url: '<?php echo Yii::app()->createUrl("wmsStock/editStock"); ?>',
			data: data,
			dataType: 'json',
			success: function(resp) {
				if (resp.done) {
					alert(resp.msg);
					setTimeout(function() {
						$('.popCancel', panel).trigger('click');
						$('#wms-stock-grid', panel).yiiGridView('update');
					}, 5e2);
				} else {
					alert(resp.msg);
					$('#submit_btn', panel).removeProp('disabled');
				}
			}
		});
	});
});
</script>