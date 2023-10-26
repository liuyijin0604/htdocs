<div style="right: 20px;position: absolute;">
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-48px -688px" class="icon"></div> Export</a>
<div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
	<ul class="dropdown-menu">
		<li><a href="<?=$this->createUrl('wmsStock/report');?>" class="jqm_link">Stock Report</a></li>
		<li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('wmsStock/export');?>" target="_blank" >Current Search</a></li>
		<li><a href="<?=$this->createUrl('wmsStock/exportLedger');?>" class="jqm_link">Export Ledger</a></li>
		<!-- <li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('wmsStock/exportLedger');?>" target="_blank" >Export Ledger</a></li> -->
		<li><a href="<?=$this->createUrl('wmsStock/inReport');?>" class="jqm_link">Stock In Report</a></li>
		<!--li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('wmsStock/exportStockLocation');?>" target="_blank" >Export Stock With Location</a></li-->
	</ul>
</div>
<?php if (Acl::hasAccess("B:wmsStock/editStock")) { ?>
<a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-2" class="grid_edit_btn">Edit Stock</a>
<div id="<?=$_GET["tabid"];?>-dropdown-2" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
  <ul class="dropdown-menu">
	<li><a class="jqm_link" data-win-class="XL" href="<?=$this->createUrl('wmsStock/editStock', array('type' => 'in'))?>">Stock In</a></li>
	<li><a class="jqm_link" data-win-class="XL" href="<?=$this->createUrl('wmsStock/editStock', array('type' => 'out'))?>">Stock Out</a></li>
  </ul>
</div>
<?php } ?>
<?php if (Acl::hasAccess("B:wmsStock/transferStock")) { ?>
<a class="jqm_link grid_edit_btn" data-win-class="XL" href="<?=$this->createUrl('wmsStock/transferStock')?>">Transfer Stock</a>
<?php } ?>
</div>
<h1><?=$this->t('WMS Stocks');?></h1>

<p>
<?=$this->t('You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.');?></p>

<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'wms-stock-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'prod_name', 'value' => 'empty($data->prod)? "" : $data->prod->name'),
		array('name' => 'prod_ean', 'type' => 'raw', 'value' => 'empty($data->prod)? "" : "<a href=\"".Yii::app()->createUrl("wmsProd/update", ["id" => $data->prod_id])."\" class=\"tab_link\" title=\"Product ".$data->prod->name."\">".$data->prod->ean."</a>"'),
		array('name' => 'cust_name', 'value' => 'empty($data->customer)? "" : $data->customer->shortName(4)'),
		array('name' => 'dpt_id', 'value' => '$data->getBranch()', 'filter'=>CHtml::dropDownList('WmsStock[dpt_id]', $model->dpt_id, Org::dptList3PL(), ['prompt'=>$this->t('All')]),),
		'qty',
		'qty_res',
		'expiry',
		'batch',
		'updated',
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn', 'data-win-class' => 'L'),
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
		$('#wms-stock-grid', panel).yiiGridView('update', {data: $('.filters input, .filters select', panel).serialize() + '&' + $(this).serialize()});
		return false;
	});

	tab.bind('onOpen', function(){
		$('#wms-stock-grid', panel).yiiGridView('update');
	});

	$('a.export_search', panel).on('mousedown', function(){
		var q = $('.filters input, .filters select', panel).serialize()+'&'+$('.search-form form', panel).serialize();
		$(this).attr('href', $(this).data('baseurl') + '?' + q);
	});
});
</script>
