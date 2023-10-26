<div class="form" style="height: 600px;">

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'wmsstock-transfer-stock-form',
		'enableAjaxValidation' => false,
	)); ?>

	<div class="row rowcol">
		<?php echo CHtml::label('From Org', 'from_id'); ?>
		<?php echo CHtml::hiddenField('from_id', '', array('data-ov' => ''));
		$acname1 = empty($_GET["tabid"]) ? 'agent_from' : $_GET["tabid"] . '_agent_from';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $acname1,
			'sourceUrl' => array('org/exAgentSuggest'),
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

	<div class="row rowcol">
		<?php echo CHtml::label('To Org', 'to_id'); ?>
		<?php echo CHtml::hiddenField('to_id', '', array('data-ov' => ''));
		$acname2 = empty($_GET["tabid"]) ? 'agent_to' : $_GET["tabid"] . '_agent_to';
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name' => $acname2,
			'sourceUrl' => array('org/exAgentSuggest'),
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

	<div class="row grid-view">
	<table id="stock_itms" class="items tsk_item">
	<thead><tr><th width="25">#</th><th width="500">Stock</th><th width="40">Qty</th><th width="40">Plt Code</th></tr></thead>
	<tbody>
	</tbody>
	<tfoot>
		<tr><td><button class="add btn btn-info"><b>+</b></button></td><td class="tot_si" align="right"></td><td class="tot_uq" align="right"></td><td></td></tr>
	</tfoot>
	</table>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Save', array('id' => 'submit_btn')); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>

<script type="text/javascript">
$(function(){
	var tab = $("#jqmw_<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	var t = $('#stock_itms', panel);

	tab.on('click', 'table.tsk_item button.add', function(){
		$(this).parents('table.tsk_item').trigger('addLine');
		return false;
	}).on('contextmenu', 'table.tsk_item td.sn', function(){
		var p = $(this).position();
  		$('#sn_context', panel).data('tarow', $(this).parents('tr')).css({left: p.left, top: p.top + 15}).toggle();
  		return false;
	}).on('change', 'table.tsk_item input.tci', function(){
		$(this).parents('table.tsk_item').trigger('calcTot');
	}).on('paste','table.tsk_item tbody input[type=text]',function(e){
		if (window.clipboardData && window.clipboardData.getData){
			pastedText = window.clipboardData.getData('Text');
		} else if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.getData) {
			pastedText = e.originalEvent.clipboardData.getData('text/plain');
		}

		var rn = -1;
		var cn = -1;
		var me = $(this);
		var rows = pastedText.trim().split(/[\n\r]+/);
		var cells = [];
		var t = $(this).parents('table.tsk_item');
		
		$('tbody tr', t).each(function(i){
			if($(this).is(me.parents('tr'))){
				var e = rows.length - $('tbody tr', t).length + i;
				if(e > 0) for(var c = 0; c < e; c++) t.trigger('addLine');
				return false;
			}
		});

		$('tbody tr', t).each(function(i){
			if(rn == -1){
				if($(this).is(me.parents('tr'))) rn = 0;
				else return;
			}
			if(rn >= rows.length) return false;
			cells = rows[rn].trim().split(/\t/);
			$('input[type=text]', this).each(function(j){
				if(rn == 0 && $(this).is(me)) cn = j;
				if(cn < 0 || j < cn) return;
				if(j >= cells.length + cn) return false;
				if($(this).hasClass('in_gn')) $(this).trigger('focus');
				if(rn > -1) $(this).val(cells[j-cn].trim());
				if($(this).hasClass('ui-autocomplete-input')) $(this).autocomplete('search');
			});
			rn++;
		});

		t.trigger('caclTot');
		return false;
	}).on('addLine', 'table.tsk_item', function(evt, at){
		var at = at || false;
		var t = $(this);
		if($('tbody tr', t).length > 2000){
			myApp.alert('Too many lines, maximum 2000 allowed');
			return false;
		}
		if(at) at.before($(t.data('line')));
		else $('tbody', t).append($(t.data('line')));
		t.trigger('afterAddLine');
	}).on('afterAddLine', 'table.tsk_item', function(){
		$('tbody tr', this).each(function(i){
			$(this).removeClass('odd even');
 			$(this).addClass(i%2 == 0? 'odd' : 'even');
 			$('.sn', this).text(i + 1);
 			$('.in_si.tci', this).prop('name', 'si[' + (i + 1) + ']');
 			$('.in_uq.tci', this).prop('name', 'uq[' + (i + 1) + ']');
 			$('.in_plt.tci', this).prop('name', 'pl[' + (i + 1) + ']');
		});
	});

	t.data('line', '<tr><td class="sn"></td><td><input type="hidden" class="in_si tci" name="si" /><input type="text" class="in_sn" name="sn" style="width:100%" /></td><td><input type="text" class="in_uq tci" name="uq" style="width:100%" /></td><td><input type="text" class="in_plt tci" name="plt" style="width:100%" /></td></tr>');

	t.on('calcTot', function(){
		var countUniq = function(c){
			var vl = [];
			$('input.'+c, t).each(function(){
				var v = $(this).val();
				if(v == '') return;
				if($.inArray(v,vl) == -1) vl.push(v);
			});

			return vl.length;
		};

		var tally = function(c){
			var tt = 0;
			$('input.'+c, t).each(function(){
				var v = $(this).val();
				if(v == '') return;
				tt += Number(v);
			});

			return tt;
		};
		
		$('td.tot_gn', t).text(countUniq('in_si'));
		$('td.tot_pq', t).text(tally('in_pq'));
		$('td.tot_cq', t).text(tally('in_cq'));
		$('td.tot_uq', t).text(tally('in_uq'));
	});

	t.on('change', 'input.in_pq,input.in_cq, input.in_uq', function(){
		var tr = $(this).parents('tr');
		var qpc = tr.find('input.in_sn').data('qpc') || 0;
		var qpp = tr.find('input.in_sn').data('qpp') || 0;
		if(qpc > 0){
			if($(this).hasClass('in_cq')) tr.find('input.in_uq').val(qpc * $(this).val());
			//if($(this).hasClass('in_uq')) tr.find('input.in_cq').val(Math.ceil($(this).val() / qpc));
		}
		if(qpp > 0){
			if($(this).hasClass('in_pq')) tr.find('input.in_uq').val(qpp * $(this).val());
		}
		t.trigger('calcTot');
	});

	var ac_select = function(me, ui){
		me.val(ui.label).data({
					'qpc' : ui.qpc,
					'qpp' : ui.qpp,
				});
		me.prevAll("input[type=hidden]").val(ui.value).data("ov",ui.value);
		if (ui.meb) {
			me.css('background-color', 'rgba(255, 96, 0, 0.5)');
		} else {
			me.css('background-color', 'white');
		}
		t.trigger('calcTot');
	};

	tab.on('addStock', function(evt, sku){
		var emptyRow = function(){
			var tr = false;
			$('tbody tr', t).each(function(){
				if(Number($('.in_uq', this).val()) == 0){
					tr = $(this);
					return false;
				}
			});
			if(tr === false){
				tr = $(t.data('line'));
				$('tbody', t).append(tr);
			}
			return tr;
		};
		
		for(i in sku){
			tr = emptyRow();
			$('.in_si', tr).val(sku[i].i);
			$('.in_sn', tr).val(sku[i].n);
			$('.in_uq', tr).val(sku[i].q);
		}
	});

	tab.on('focus', 'input.in_sn', function(){
		var me = $(this);
		if(me.data('acinit') == 1) return;
		if ($('#from_id').val() == '') return;
		me.autocomplete({
			'source': 'wmsStock/suggest?oid=' + $('#from_id').val(),
			'showAnim': 'fold',
			'minLength': 2,
			'delay': 200,
			'select': function(event, ui){
				ac_select(me, ui.item);
				return false;
			},
			'response': function(evt, ui){
				me.prevAll("input[type=hidden]").val(0);
				if(ui.content.length == 1){
					ui.item = ui.content[0];
					ac_select(me, ui.item);
				}else if(ui.content.length == 0){
					me.val('').data({
						'qpc' : 0,
						'qpp' : 0,
					});
				}
				return false;
			}
		}).data('acinit', 1).data("ui-autocomplete")._renderItem = function (ul, item) {
				var sn = item.label.match(/(.*?)\((.*?)\)/);
				if (sn) {
					return $('<li>').append($('<a>').html(sn[1] + '<span style="color: red;">(' + sn[2] + ')</span>')).appendTo(ul);
				} else {
					return $('<li>').append($('<a>').html(item.label)).appendTo(ul);
				}
     	};
	}).on('blur', 'input.in_sn', function(){
		if(Number($(this).prevAll("input[type=hidden]").val()) == 0 && $(this).val() !== ''){
			$(this).css('background-color', 'rgba(255,0,0,0.5)');
		}
	});

	t.on('focus', 'input.in_pl', function(){
		var me = $(this);
		me.autocomplete({
			'source': 'wmsStock/suggestPlt?s='+me.parents('tr').find('input.in_si').val(),
			'showAnim': 'fold',
			'minLength': 2,
			'delay': 200,
			'select': function(event, ui){
				me.val(ui.item.v);
				me.prevAll("input[type=hidden]").val(ui.item.value).data("ov",ui.item.value);
				if(ui.item.si){
					tr = me.parents('tr');
					tr.find('input.in_si').val(ui.item.si);
					tr.find('input.in_sn').val(ui.item.sn);
				}
				return false;
			},
			'response': function(evt, ui){
				if(ui.content.length == 1){
					ui.item = ui.content[0];
					me.val(ui.item.v);
					me.prevAll("input[type=hidden]").val(ui.item.value).data("ov",ui.item.value);
					if(ui.item.si){
						tr = me.parents('tr');
						tr.find('input.in_si').val(ui.item.si);
						tr.find('input.in_sn').val(ui.item.sn);
					}
				}else if(ui.content.length == 0){
					me.prevAll("input[type=hidden]").val(me.prevAll("input[type=hidden]").data("ov"));
					me.val('');
				}
				return false;
			},
		});
	});

	$('#wmsstock-transfer-stock-form', panel).on('success', function() {
		setTimeout(function() {
			$('.popCancel', panel).trigger('click');
			$('#wms-stock-grid').yiiGridView('update');
		}, 1e3);
	});
});
</script>