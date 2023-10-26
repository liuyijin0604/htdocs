<div style="right: 20px;position: absolute;">
	<?php if($model->status==20){ 
		$ajaxtab = $_GET["tabid"];
			echo CHtml::ajaxLink('Synchronize', Yii::app()->createAbsoluteUrl('cargoProcessPlan/synchronize/'.$model->id), array('type' => 'get', 'data' => array('id' => $model->id, 'type' => 'get'), 'update' => 'message', 'success' => 'function(response) {
				var tab = $("#'.$ajaxtab.'");
				var panel = tab.data("panel");
				var t = $(".ui-tabs", panel);
				t.tabs("load", t.tabs("option", "active"));
				
				alert(response);
				}'), array('id'=>'PR'.$model->id.$ajaxtab,'confirm' => 'Are you sure you want to synchronize this request?', 'role' => "button", "class" => "btn btn-danger"));
 	} //$("#PR'.$model->id.'").hide(); ?>
	<?php if(empty($model->invoice_id)){ ?>
		<a id="PRInvoice<?=$model->id?>"class="jqm_link" href="<?=$this->createUrl('cargoProcessPlan/createInvoice',['id'=>$model->id]);?>"><div style="background-position:-48px -688px" class="icon"></div>Create Invoice</a>
	<?php } ?>
</div>
<h1><a class="tab_link"  title="<?= $model->ref; ?>">Ref: <?= $model->ref; ?></a> - Schedule Time <?= $model->schedule_time; ?></h1>

<div id="cargoplan-tabs">
	<ul>
		<?php
		$tabs = [['main', $this->t('Main')]];
		$tabs[] = ['files', $this->t('Files')];
		$tabs[] = ['invoice',$this->t('Invoice')];

		foreach ($tabs as $tab) {
			if (Acl::hasAccess($this->CaName . '/' . $tab[0])) {
				$href = strpos($tab[0], '/') === false ? $this->createUrl('cargoProcessPlan/update', ['id' => empty($tab[2]) ? $model->id : $tab[2], 'tab' => $tab[0], "tabid" => $_GET["tabid"]]) : $tab[0];
				echo '<li><a href="' . $href . '">' . $tab[1] . '</a></li>';
			}
		}
		?>
	</ul>
</div>
<div id="sn_context" class="dropdown dropdown-tip dropdown-anchor-left">
	<ul class="dropdown-menu">

	</ul>
</div>
<script type="text/javascript">
	$(function() {
		var tab = $('#<?= $_GET["tabid"]; ?>');
		var panel = tab.data('panel');
		$('#cargoplan-tabs', panel).tabs({
			active: <?php echo empty($_GET['actab']) ? 0 : $_GET['actab']; ?>,
			load: function(event, ui) {
				myApp.ajaxifyForm(this);
			}
		});

		//bind reload_tab
		tab.off('reload_tab').on('reload_tab', function() {
			var t = $('.ui-tabs', panel);
			t.tabs('load', t.tabs('option', 'active'));
		});

		tab.on('onOpen', function() {
			tab.trigger('reload_tab');
		});

		$('#sn_context', panel).on('click', 'a', function() {
			var cm = $('#sn_context', panel);
			var tr = cm.data('tarow');
			var t = tr.parents('table.tsk_item');

			if ($(this).hasClass('insert_line')) {
				t.trigger('addLine', [tr]);
			} else if ($(this).hasClass('del_line') && window.confirm('Are you sure to remove line #' + $('td.sn', tr).text() + '?')) {
				tr.remove();
				t.trigger('afterAddLine');
			}
			cm.hide();
			return false;
		});

		panel.on('click', 'table.tsk_item button.add', function() {
			$(this).parents('table.tsk_item').trigger('addLine');
			return false;
		}).on('contextmenu', 'table.tsk_item td.sn', function() {
			var p = $(this).position();
			$('#sn_context', panel).data('tarow', $(this).parents('tr')).css({
				left: p.left,
				top: p.top + 15
			}).toggle();
			return false;
		}).on('change', 'table.tsk_item input.tci', function() {
			$(this).parents('table.tsk_item').trigger('calcTot');
		}).on('paste', 'table.tsk_item tbody input[type=text]', function(e) {
			if (window.clipboardData && window.clipboardData.getData) {
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

			$('tbody tr', t).each(function(i) {
				if ($(this).is(me.parents('tr'))) {
					var e = rows.length - $('tbody tr', t).length + i;
					if (e > 0)
						for (var c = 0; c < e; c++) t.trigger('addLine');
					return false;
				}
			});

			$('tbody tr', t).each(function(i) {
				if (rn == -1) {
					if ($(this).is(me.parents('tr'))) rn = 0;
					else return;
				}
				if (rn >= rows.length) return false;
				cells = rows[rn].trim().split(/\t/);
				$('input[type=text]', this).each(function(j) {
					if (rn == 0 && $(this).is(me)) cn = j;
					if (cn < 0 || j < cn) return;
					if (j >= cells.length + cn) return false;
					if ($(this).hasClass('in_gn')) $(this).trigger('focus');
					if (rn > -1) $(this).val(cells[j - cn].trim());
					if ($(this).hasClass('ui-autocomplete-input')) $(this).autocomplete('search');
				});
				rn++;
			});

			t.trigger('caclTot');
			return false;
		}).on('addLine', 'table.tsk_item', function(evt, at) {
			var at = at || false;
			var t = $(this);
			if ($('tbody tr', t).length > 2000) {
				myApp.alert('Too many lines, maximum 2000 allowed');
				return false;
			}
			if (at) at.before($(t.data('line')));
			else $('tbody', t).append($(t.data('line')));
			t.trigger('afterAddLine');
		}).on('afterAddLine', 'table.tsk_item', function() {
			$('tbody tr', this).each(function(i) {
				$(this).removeClass('odd even');
				$(this).addClass(i % 2 == 0 ? 'odd' : 'even');
				$('.sn', this).text(i + 1);
			});
		});
	});
</script>