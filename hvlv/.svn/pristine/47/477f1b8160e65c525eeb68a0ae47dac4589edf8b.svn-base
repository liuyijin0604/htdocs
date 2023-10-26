<div style="right: 20px;position: absolute;">
<?php if (in_array(Yii::app()->user->id, WmsTask::$op) || (isset(Yii::app()->user->grp) && Yii::app()->user->grp == 0)) { ?>
<a href="<?=$this->createUrl('wmsTask/dash3PL')?>" class="tab_link" title="3PL Dashboard"><div style="background-position:-48px -688px" class="icon"></div>3PL Dashboard</a>
<?php } ?>
&nbsp;&nbsp;&nbsp;
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-2"><div style="background-position:-48px -688px" class="icon"></div> Action</a>
<div id="<?=$_GET["tabid"];?>-dropdown-2" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('wmsTask/bulkTask', array('t' => 'in'))?>" class="jqm_link">Bulk Task - In</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/bulkTask', array('t' => 'out'))?>" class="jqm_link">Bulk Task - Out</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/complete')?>" data-win-class="L" class="jqm_link">Bulk Complete</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/wtDiff')?>" data-win-class="L" class="tab_link" title="StarTrack & TNT">StarTrack & TNT Weight Diff</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/backOrder')?>" class="jqm_link">Backorder</a></li>
	</ul>
</div>
&nbsp;&nbsp;&nbsp;
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="#" data-baseurl="<?=$this->createUrl('wmsTask/export', array('t' => 'search'))?>" target="_blank" class="export_search">Current Search</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export', array('t' => 'packingall'))?>" class="jqm_link">Sum Packing List</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/quickCourierLabel')?>" data-win-class="L" class="jqm_link">Quick Courier Label</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export', array('t' => 'invoice'))?>" data-win-class="L" class="jqm_link">Invoice</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export', array('t' => 'delivery_profit_rpt'))?>" data-win-class="L" class="jqm_link">Delivery & Profit Report</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export', array('t' => 'profit'))?>" data-win-class="L" class="jqm_link">AUPOST Delivery Profit Report</a></li>
		<?php if (Yii::app()->user->grp == 0) { ?>
		<li><a href="#" data-baseurl="<?=$this->createUrl('wmsTask/export', array('t' => 'pickpackinvoice'))?>" target="_blank" class="export_search">Pick&Pack Report</a>
		<li><a href="#" data-baseurl="<?=$this->createUrl('wmsTask/export', array('t' => 'pickpackinvoice2'))?>" target="_blank" class="export_search">Pick&Pack Report2</a></li>
		<?php } ?>
		<li><a href="<?=$this->createUrl('wmsTask/export', array('t' => 'packingallsingleitem'))?>" class="jqm_link">Packing List (Single item)</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export', array('t' => 'packingallmultiitems'))?>" class="jqm_link">Packing List (Multi items)</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export', array('t' => 'farmland'));?>" class="jqm_link">Farmland Report</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export', array('t' => 'pickinglistsingleitem'));?>" class="jqm_link">Picking list (Single item)</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export', array('t' => 'courierall'))?>" class="jqm_link">Batch Courier Label</a></li>
		<li><a href="<?=$this->createUrl('wmsTask/export', array('t' => 'reconciliation'))?>" class="jqm_link">Reconciliation Report </a></li>
	</ul>
</div>
&nbsp;&nbsp;&nbsp;
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-3"><div style="background-position:-48px -688px" class="icon"></div> Batch Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-3" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="#" data-baseurl="<?=$this->createUrl('wmsTask/batchExport',  ['id' => $model->id, 't' => 'rpt_reaco']);?>" target="_blank" class="batch_export_search">Comparison Report</a></li>
		<li><a href="#" data-baseurl="<?=$this->createUrl('wmsTask/batchExport',  ['id' => $model->id, 't' => 'rpt_reaco', 'eb' => 9]);?>" target="_blank" class="batch_export_search">Qty Comparison Report</a></li>
		<li><a href="#" data-baseurl="<?=$this->createUrl('wmsTask/batchExport',  ['id' => $model->id, 't' => 'rpt_reaco', 'diffonly' => true]);?>" target="_blank" class="batch_export_search">Comparison Report (Diff Only)</a></li>
		<li><a href="#" data-baseurl="<?=$this->createUrl('wmsTask/batchExport',  ['id' => $model->id, 't' => 'rpt_reaco', 'diffonly' => true, 'eb' => 9]);?>" target="_blank" class="batch_export_search">Qty Comparison Report (Diff Only)</a></li>
		<li><a href="#" data-baseurl="<?=$this->createUrl('wmsTask/batchExport',  ['id' => $model->id, 't' => 'rpt_reaco_2']);?>" target="_blank" class="batch_export_search">Comparison Report (One By One & Diff Only)</a></li>
		<li><a href="#" data-baseurl="<?=$this->createUrl('wmsTask/batchExport',  ['id' => $model->id, 't' => 'rpt_reaco_2', 'eb' => 9]);?>" target="_blank" class="batch_export_search">Qty Comparison Report (One By One & Diff Only)</a></li>
	</ul>
</div>
</div>
<h1><?=$this->t('WMS Tasks');?></h1>
<div class="row">
	<?php echo CHtml::checkbox('auto_refresh', ''), $this->t(' <b>Auto refresh</b>'); ?>
</div>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php
if (isset(Yii::app()->user->id)) {
	if (in_array(Yii::app()->user->id, WmsTask::$op) && empty($_GET['WmsTask']['dpmt'])) {
		$model->dpmt = '3PL';
	}
}
$visible_3pl =  $model->dpmt == '3PL';
$visible_other =  $model->dpmt != '3PL';

$ec = new CDbCriteria;
$ec->addCondition('t.type NOT IN (6010,6020,6030,6040)');

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'wms-task-grid',
	'selectableRows' => 2,
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 30, $ec),
	'filter'=>$model,
	'columns'=>array(
		array(
			'id'=>'selectedItems',
			'class'=>'CCheckBoxColumn',
		),
		array('name' => 'job_no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("wmsJob/update", ["id" => $data->job_id])."\" class=\"tab_link\" title=\"".$data->job->no."\">".$data->job->no."</a>"'),
		array('name' => 'id', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("wmsTask/update", ["id" => $data->id])."\" class=\"tab_link\" title=\"".$data->getNo()."\">".$data->getNo()." ".$data->getUpdate()."</a>"'),
		//array('name' => 'edi_job', 'type' => 'raw', 'value' => '$data->getEdiJob(false)', 'visible' => $visible_other),
		array('header' => 'Multi/Single','value'=>'$data->getStringIsSingleItem()','filter'=>CHtml::dropDownList('WmsTask[isSinOrMul]', $model->isSinOrMul, [0=>"All",1=>"Single",2=>"Multi"]),),
		//array('name' => 'awbn', 'type' => 'raw', 'value' => '$data->getAWBN()', 'visible' => $visible_other),
		array('name' => 'cust_name', 'value' => 'empty($data->job->customer)? "" : $data->job->customer->shortName(4)'),
		array('name' => 'ref', 'type' => 'raw', 'value' => '$data->getRef()'),
		array('header' => 'Note', 'value' => '@$data->mdata["note"]', 'visible' => $visible_3pl),
		array('header' => 'Qty', 'value' => '$data->countUq()', 'visible' => $visible_3pl),
		array('name' => 'type', 'value' => '$data->getType()', 
			'filter'=>CHtml::dropDownList('WmsTask[type]', $model->type, $this->t(WmsTask::$types), array('prompt'=>$this->t('All'))),),
		array('name' => 'status', 'value' => '$data->getStatus()', 
			'filter'=>CHtml::dropDownList('WmsTask[status]', $model->status, in_array(Yii::app()->user->id, WmsTask::$op) || Yii::app()->user->grp == 0 ? $this->t(WmsTask::$states_3pl) : $this->t(WmsTask::$states), array('prompt'=>$this->t('All'))),),
		//array('name' => 'op_name', 'value' => 'empty($data->op)? "" : $data->op->getName()', 'visible' => $visible_other),
		//Author:Nero Date:2021/6/28 Description:show dpt_id
		array('name' => 'dpt_id', 'value' => '$data->getBranch()', 'filter'=>CHtml::dropDownList('WmsTask[dpt_id]', $model->dpt_id, Org::dptList3PL(), ['prompt'=>$this->t('All'),'class' => 'form-control']),),
		//array('name' => 'dpmt', 'value' => '$data->getDpmt()', 'filter' => CHtml::dropDownList('WmsTask[dpmt]', $model->dpmt, ['3PL' => '3PL', 'Other' => 'Other'], array('prompt'=>$this->t('All'))),),
		// array('name' => 'dpt_id', 'value' => '$data->getBranch()', 'filter'=>CHtml::dropDownList('WmsTask[dpt_id]', $model->dpt_id, Org::dptList3PL(), ['prompt'=>$this->t('All')]),),
		//array('name' => 'plt_diff', 'header' => 'Have Diff', 'value' => '$data->ifPltDiff()', 'filter' => CHtml::dropDownList('WmsTask[plt_diff]', $model->plt_diff, ['wr' => 'Waiting Check', 'no' => 'No Diff', 'not' => 'Diff to Confirm', 'cfd' => 'Diff Confirmed'], array('prompt'=>$this->t('All'))), 'visible' => $visible_other),
		array('name' => 'schd_time', 'visible' => $visible_other),
		array('header'=>'Courier', 'value'=>'$data->getCourierName()'),
		array('name' => 'due_time', 'visible' => $visible_other),
		'compl_time',
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}{update}',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn'),
				),
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->getNo()'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $("#<?=$_GET['tabid'];?>");
	var panel = tab.data('panel');
	$('.search-button', panel).click(function(){
		$('.search-form', panel).toggle();
		return false;
	});
	$('.search-form form', panel).on('submit', function(){
		var refs = $('#WmsTask_refs').val();
		refs = refs.split(/[\s,;]+/);
		if (refs.length > 200) {
			myApp.alert('refs is too long', false);
			return false;
		}
		$.fn.yiiGridView.update('wms-task-grid', {
			data: $(this).serialize()
		});
		return false;
	});
	tab.bind('onOpen', function(){
		$('#wms-task-grid', panel).yiiGridView('update');
	});

	$('#auto_refresh', panel).on('change', function() {
		if ($('#auto_refresh', panel).prop('checked') == true) {
			window.clearInterval(window.interval);
			window.interval = setInterval(function() {
				$('#wms-task-grid', panel).yiiGridView('update');

				tab.on('close', function() {
					window.clearInterval(window.interval);
				});
			}, 10000);
		} else {
			window.clearInterval(window.interval);
		}
	});

	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize();
		var href = $(this).data('baseurl') + '&' + q;
		href = href.replace('.app&', '?');
		$(this).attr('href', href);
	});

	$('a.export', panel).on('mousedown', function() {
		var href = $(this).data('baseurl');
		$('.select-on-check', panel).each(function() {
			if ($(this).prop('checked') === true) {
				href += '&ids[]=' + $(this).val();
			}
		});
		href = href.replace('/.app?', '?');
		$(this).attr('href', href);
	});

	$('a.batch_export_search', panel).on('mousedown', function() {
		var q = $('.filters input, .filters select', panel).serialize();
		var href = $(this).data('baseurl') + '&' + q;
		$('.select-on-check', panel).each(function() {
			if ($(this).prop('checked') === true) {
				href += '&ids[]=' + $(this).val();
			}
		});
		href = href.replace('/.app?', '?');
		$(this).attr('href', href);
	});
});
</script>
