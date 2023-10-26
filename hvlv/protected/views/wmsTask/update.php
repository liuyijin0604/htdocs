<div style="right: 20px;position: absolute;">
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-2"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-2" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
	<?php if(in_array($model->type, [1010,1020,1030,3010,3020,3030])):?>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'rpt_reaco']);?>" target="_blank">Comparison Report</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'rpt_reaco', 'eb' => 9]);?>" target="_blank">Qty Comparison Report</a></li>
	<?php endif; ?>
	<?php if(in_array($model->type, [1010,1020])):?>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'rpt_gtin']);?>" target="_blank">GateIn Report</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'plt_lbl']);?>" target="_blank">Pallet Labels</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'rpt_pltpack']);?>" target="_blank">Pallet Weight List</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'rpt_pltpack_wangyi']);?>" target="_blank">Pallet Weight List 网易</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'rpt_qty']);?>" target="_blank">Qty Report</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'abm_stock_in']);?>" target="_blank">ABM Stock In Report</a></li>
	<?php endif; ?>
	<?php if(in_array($model->type, [3010])):?>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'rpt_ctnpack']);?>" target="_blank">Container Packing List</a></li>
	<?php endif; ?>
	<?php if(in_array($model->type, [3010,3020,3030])):?>
		<li><a href="<?=$this->createUrl('wmsTask/print',  ['id' => $model->id, 't' => 'pack_list']);?>" target="_blank">Packing List</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/print',  ['id' => $model->id, 't' => 'delivery_order']);?>" target="_blank">Delivery Order</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'stock_out']);?>" target="_blank">Stock Out Report</a></li>
	<?php endif; ?>
	<?php if(in_array($model->type, [2030])):?>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'rpt_ctnpack']);?>" target="_blank">Container Packing List</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'stock_out']);?>" target="_blank">Stock Out Report</a></li>
	<?php endif; ?>
	<?php if (in_array($model->type, [2030, 3010])) { ?>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'pkl']);?>" target="_blank">PKL</a></li>
	<?php } ?>
	<?php if (in_array($model->type, [1010, 1030])) { ?>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'pkl_pdf']);?>" target="_blank">PKL PDF</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'pkl_excel']);?>" target="_blank">PKL Excel</a></li>
	<?php } ?>
	<?php if (in_array($model->type, [2030, 3010, 3020, 3030])) { ?>
		<li><a href="<?=$this->createUrl('wmsTask/export',  ['id' => $model->id, 't' => 'gatepass']);?>" target="_blank">Gatepass</a></li>
	<?php } ?>
	</ul>
</div>
<?php if (in_array($model->type, [1010, 1020])) { ?>
	<a href="<?=$this->createUrl('wmsTask/quickOutTask', ['id' => $model->id]);?>" class="jqm_link"><div style="background-position:-48px -688px" class="icon"></div> Quick Out Task</a>
	<!--a href="<?=$this->createUrl('wmsTask/quickStockout', ['id' => $model->id]);?>" class="jqm_link"><div style="background-position:-48px -688px" class="icon"></div> Quick Stock Out</a-->
<?php } ?>
<?php if (in_array($model->type, [2030, 2040, 3010, 3020, 3030])) { ?>
	<?php if (in_array($model->type, [3010, 3020, 3030]) && $model->status == 20 && (yii::app()->user->grp == 0 || Acl::hasAccess('B:WmsTask/quickComplete'))) { ?>
		<a href="<?=$this->createUrl('wmsTask/quickComplete', ['id' => $model->id]);?>" class="jqm_link"><div style="background-position:-48px -688px" class="icon"></div> Quick Complete</a>
	<?php } ?>
	<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-16px 0" class="icon"></div> EdiJob</a>
	<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
		<ul class="dropdown-menu">
			<li><a href="<?=$this->createUrl('ediJob/createJob', array('org' => $model->job->org_id, 'wmstask' => $model->id))?>" title="New Air Feight Job" class="tab_link">New EdiJob</a></li>
			<li><a href="<?=$this->createUrl('ediJob/linkJob', array('wmstask' => $model->id))?>" title="Link Air Feight Job" class="jqm_link">Link EdiJob</a></li>
		</ul>
	</div>
	<a href="<?=$this->createUrl('wmsTask/groupOrg', ['id' => $model->id]);?>" id="group_org" class="jqm_link"><div style="background-position:-48px -688px" class="icon"></div> Group Org</a>

	<?php $cc = ContainerCartage::model()->find('task_id = :task_id', [':task_id' => $model->id]);
	if (empty($cc)) { ?>
	<a href="<?=$this->createUrl('containerCartage/create', array('org_id' => $model->job->org_id, 'task_id' => $model->id))?>" title="New Container Cartage" class="tab_link"><div style="background-position:-16px 0" class="icon"></div> CT Cartage</a>
	<?php } else { ?>
	<a href="<?=$this->createUrl('containerCartage/update', array('id' => $cc->id))?>" title="Update Container Cartage" class="tab_link"><div style="background-position:-176px -544px" class="icon"></div> CT Cartage</a>
	<?php } ?>

<?php } ?>
</div>
<h1><a class="tab_link" href="<?=$this->createUrl('wmsJob/update', ['id' => $model->job_id]);?>" title="<?=$model->job->no;?>"><?=$model->job->no;?></a>-<?=$model->getNo().' '.$model->getType();?></h1>

<div id="wmstask-tabs">
  <ul>
<?php
$tabs = [['main', $this->t('Main')]];
if ($model->type != WmsTask::TYPE_ADHOC_TASK) {
	foreach ($model->subTasks as $t) {
		$tabs[] = ['task', $this->t($t->getType()), $t->id];
	}
} else {
	foreach($model->subTasks as $t){
		if ($t->getType() == 'Adhoc Task' && !Acl::hasAccess('B:WmsTask/viewAdhocTask')) continue;
		if ($t->getType() == 'Request' && ((!empty($t->mainTask->op_id) && $t->mainTask->op_id != Yii::app()->user->id) || empty($t->mainTask->op_id))) continue;
		if ($t->getType() == 'Process' && ((!empty($t->mainTask->mdata['proc_id']) && $t->mainTask->mdata['proc_id'] != Yii::app()->user->id) || empty($t->mainTask->mdata['proc_id']))) continue;
		if ($t->getType() == 'QA' && ((!empty($t->mainTask->mdata['qa_id']) && $t->mainTask->mdata['qa_id'] != Yii::app()->user->id) || empty($t->mainTask->mdata['qa_id']))) continue;
		$tabs[] = ['task', $this->t($t->getType()), $t->id];
		break;
	}
}
if ($model->type != WmsTask::TYPE_ADHOC_TASK) {
	$tabs[] = ['files', $this->t('Files')];
}
$tabs[] = ['billing', $this->t('Billing')];
$tabs[] = ['invoice', $this->t('Invoice')];
$tabs[] = ['log', $this->t('Log')];
foreach($tabs as $tab){
if(Acl::hasAccess($this->CaName.'/'.$tab[0])){
	$href = strpos($tab[0], '/') === false? $this->createUrl('wmsTask/update', ['id' => empty($tab[2])? $model->id : $tab[2], 'tab'=>$tab[0], "tabid" => $_GET["tabid"]]) : $tab[0];
	echo '<li><a href="'.$href.'">'.$tab[1].'</a></li>';
}
}
?>
  </ul>
</div>
<div id="sn_context" class="dropdown dropdown-tip dropdown-anchor-left">
	<ul class="dropdown-menu">
		<li><a href="#" class="insert_line">Insert Line</a></li>
		<li><a href="#" class="del_line">Delete Line</a></li>
	</ul>
</div>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	$('#wmstask-tabs', panel).tabs({active: <?php echo empty($_GET['actab'])? 0 : $_GET['actab']; ?>, load: function(event,ui){
		myApp.ajaxifyForm(this);
	}});

	//bind reload_tab
	tab.off('reload_tab').on('reload_tab', function(){
		var t = $('.ui-tabs', panel);
		t.tabs('load', t.tabs('option','active'));
	});

	tab.on('onOpen', function(){
		tab.trigger('reload_tab');
	});

	$('#sn_context', panel).on('click', 'a', function(){
		var cm = $('#sn_context', panel);
		var tr = cm.data('tarow');
		var t = tr.parents('table.tsk_item');

		if($(this).hasClass('insert_line')){
			t.trigger('addLine', [tr]);
		}else if($(this).hasClass('del_line') && window.confirm('Are you sure to remove line #'+$('td.sn', tr).text()+'?')){
			tr.remove();
			t.trigger('afterAddLine');
		}
		cm.hide();
		return false;
	});

	panel.on('click', 'table.tsk_item button.add', function(){
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
		});
	});
});
</script>
