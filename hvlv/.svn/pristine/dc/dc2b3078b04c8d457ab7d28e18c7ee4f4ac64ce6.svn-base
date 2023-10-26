<div style="right: 20px;position: absolute;">
	 <a class="tab_link" data-win-class="XL" href="<?=$this->createUrl('ediJob/createJob');?>" title="New Air Feight Job"><div class="icon" style="background-position:-16px 0"></div> New Job</a>
	<a class="tab_link" data-win-class="XL" href="<?=$this->createUrl('ediJob/billingInput');?>" title="Billing Input"><div class="icon" style="background-position:-16px 0"></div> Billing Input</a>
	<a href="#" data-dropdown="#dropdown-1"><div class="icon" style="background-position:-176px -544px"></div> Manage</a>
<div id="dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
	<li><a class="tab_link" data-win-class="XL" href="<?=$this->createUrl('ediJob/mngChargeTypes');?>" title="Manage Charge Type">Manage Charge Type</a></li>
	<li><a class="tab_link" data-win-class="XL" href="<?=$this->createUrl('ediJob/mngEdiBasicTemplate');?>" title="Manage Charge Type">Manage Basic Template</a></li>
	<li><a class="tab_link" data-win-class="XL" href="<?=$this->createUrl('oList/update/' . @oList::model()->find('item like "%ex_port%"')->id);?>" title="Manage Port">Manage Port</a></li>
	<li><a class="tab_link" href="<?=$this->createUrl('cartage/arrange')?>" title="Arrange Cartage">Arrange Cartage</a></li>
	<li><a class="tab_link" href="<?=$this->createUrl('ediJob/deliveryRecord')?>" title="Delivery Record">Delivery Record</a></li>
	<li><a class="tab_link" href="<?=$this->createUrl('ediJobAirline/report')?>" title="KPI Report">KPI Report</a></li>
	<li><a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('ediJob/batchXrayJob')?>" title="Batch Create X-Ray Job">Batch Create X-Ray Job</a></li>
	<li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('ediJob/export', ['type' => 'abm']);?>" target="_blank">Export ABM</a></li>
	<li><a class="tab_link" href="<?=$this->createUrl('containerCartage/list')?>" title="Container Cartage">Container Cartage</a></li>
	</ul>
</div>
<a href="<?=$this->createUrl('wmsTask/dashEX')?>" class="tab_link" title="Dashboard"><div style="background-position:-48px -688px" class="icon"></div> Dashboard</a>
<a href="<?=$this->createUrl('ediJob/calendar')?>" class="tab_link" title="Calendar"><div style="background-position:-48px -688px" class="icon"></div> Calendar</a>
</div>

<h1><?=$this->t('Air Feight & 3PL Jobs');?></h1>

<div style="left: 20px;position: absolute;">
	<a class="export_search" href="#" data-baseurl="<?=$this->createUrl('ediJob/export', ['type' => 'search']);?>" target="_blank">Export Current Search</a>
</div>

<p>

<?php $this->widget('zii.widgets.grid.CGridView', [
	'id'=>'edi-job-list-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>[
		['name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("ediJob/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"',],
		//   array('header' => 'AWB', 'type' => 'raw', 'value' => '$data->getAwb()'),
		['name' => 'awb', 'type' => 'raw', 'value' => '$data->AwbTracking()'],
		['name' => 'owner_name', 'value' => 'empty($data->owner)? "" : $data->owner->name'],
		// ['name' => 'status', 'value' => '$data->getStatus()', 'filter'=>CHtml::dropDownList('EdiJob[status]', $model->status, $this->t($model::$states), ['prompt'=>$this->t('All')]),],
		['name' => 'dpmt', 'value' => '$data->getDpmt()', 'filter'=>CHtml::dropDownList('EdiJob[dpmt]', $model->dpmt, $this->t(Invoice::$dpmts), array('prompt'=>$this->t('All'))),],
		['name' => 'dpt_id', 'value' => '$data->depot->name',
			'filter'=>CHtml::dropDownList('EdiJob[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), ['prompt'=>$this->t('All')]),],
		'created',
		// 'due',
		// ['name' => 'user_name', 'value' => 'empty($data->user_id)? "" : $data->user->fname'],
		['name' => 'create_user', 'value' => '$data->getCreateUser()'],
		['name' => 'InvoiceNo', 'type' => 'raw', 'value' => '$data->getInvoicesString()'],
		// ['name' => 'inv_type', 'value' => '$data->getInvType()', 'filter'=>CHtml::dropDownList('EdiJob[inv_type]', $model->inv_type, $this->t([0 => 'Empty', 1 => 'Local Service', 2 => 'Import Air', 3 => 'Import Sea', 4 => 'Export Air', 5 => 'Export Sea',]), array('prompt' => $this->t('All')))],
		array('name' => 'wms_task', 'type' => 'raw', 'value' => '$data->getWmsTask(false)'),
		['header' => 'Cost','value' => 'round($data->totCost()[2], 2)'],
		['header' => 'Revenue','value' => '($data->currency == 1) ? $data->totRevenue() : $data->getCurrency().$data->totRevenue()[0] . " ~ " . "AUD". $data->totRevenue()[1]'],
		['header' => 'Profit','value' => '$data->totProfit()'],
		[
			'class'=>'oButtonColumn',
			'template'=>'{update}',//{view}
			'buttons'=>[
				'view' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_view_btn'],
				],
				'update' => [
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->no'],
				],
			],
		],
	],
]); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#edi-job-list-grid', panel).yiiGridView('update');
	});


	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize();
		$(this).attr('href', $(this).data('baseurl') + '&' + q);
	});

});
</script>
