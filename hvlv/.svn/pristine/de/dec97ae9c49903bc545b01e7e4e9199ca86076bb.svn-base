<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink'=>CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
		'Stock List',
	),
));
?>
<h2><?=$this->t('Manage Stock');?></h2>
<div class="row">
	<div class="col-xs-12">
		<div class="dropdown" style="display: inline-block; margin-right: 1em">
			<button class="btn btn-default dropdown-toggle" style="width: 10em" type="button" id="menu1" data-toggle="dropdown">Export <span class="caret"></span></button>
			<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
				<li role="presentation"><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('stock/export');?>" target="_blank">Current Search</a></li>
				<li role="presentation"><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('product/exportStockWithLocation');?>" target="_blank">Whole Stock With Loc</a></li>
				<li role="presentation"><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('product/exportStockExpiry')?>" target="_blank">Expiry Report</a></li>
			</ul>
		</div>
	</div>
</div>
<?php
// $ec = new CDbCriteria;
// if (in_array(Yii::app()->user->org, [Org::ORGID_3PL_COBAYER])) {
// 	$ec->addCondition('qty > 0');
// }
// if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
// 	$ec->addCondition('org_id = ' . Yii::app()->session['org_id']);
// }
// $ec->addCondition('t.bwf & 2 = 0');
// $this->widget('application.extensions.booster.TbExtendedGridView', array(
// 	'id' => 'wms-org-stock-grid',
// 	'cssFile' => false,
// 	'dataProvider'=>$model->search(true, 30, $ec),
// 	'filter' => $model,
// 	'columns' => array(
// 		array('name' => 'cust_name', 'value' => '$data->customer->name', 'visible' => sizeof(User::getOrgIds()) > 1 ? true : false),
// 		array('name' => 'prod_sku', 'header' => 'Item Code / SKU', 'value' => '$data->prod->getSku(Yii::app()->session["org_id"])', 'filter' => CHtml::textField('WmsStock[prod_sku]', $model->prod_sku, ['class' => 'form-control'])),
// 		array('name' => 'prod_name', 'header' => 'Description', 'value' => 'empty($data->prod)? "" : $data->prod->name'),
// 		array('name' => 'prod_ean', 'header' => 'Barcode', 'type' => 'raw', 'value' => 'empty($data->prod)? "" : "<a href=\"".Yii::app()->createUrl("pcaw/product/update", ["id" => $data->prod_id, "from" => "stock"])."\" class=\"tab_link ajax-link\" title=\"Product ".$data->prod->name."\">".$data->prod->ean."</a>"'),
// 		array('name' => 'qty', 'header' => 'SOH', 'value' => 'intval($data->getQty())'),
// 		array('name' => 'qty_res', 'header' => 'Allocated To Current Order', 'value' => 'intval($data->qty_res)'),
// 		array('header' => 'Allocated To Back Order', 'value' => '0'),
// 		array('header' => 'Available Stock', 'value' => '$data->getQty() - $data->qty_res'),
// 		array('header' => 'Incoming Stock', 'value' => '0'),
// 		array('header' => 'Minimum Stock Alert', 'value' => '$data->getMinStockAlert()'),
// 		array('name' => 'expiry', 'type' => 'raw', 'value' => '$data->showExpiry()'),
// 		'batch',
// 		array('header' => 'Weight g', 'value' => '$data->prod->weight'),
// 		array('header' => 'Dimension (mm)', 'value' => '$data->prod->getDim()'),
// 	),
// ));
$prods = [];
$stocks = WmsStock::model()->findAll('org_id = :org_id AND (bwf & 2) = 0', [':org_id' => Yii::app()->user->org]);
foreach ($stocks as $stock) {
	$prods[] = $stock->prod_id;
}

$model = new WmsProd('search');
$model->unsetAttributes();
if (!empty($_GET['WmsProd'])) {
	$model->setAttributes($_GET['WmsProd']);
}
// $model->type = WmsProd::WMS_PROD_PHYSICAL;
$ec = new CDbCriteria;
if (!empty($prods)) {
	$ec->addCondition('t.id IN (' . implode(',', $prods) . ')');
} else {
	$ec->addCondition('1 = 0');
}
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id' => 'wms-org-stock-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(true, 30, $ec),
	'filter' => $model,
	'template' => "{items}\n{pager}",
	'columns' => array(
		array('name' => 'sku', 'header' => 'Item Code / SKU', 'value' => '$data->getSku(Yii::app()->user->org)', 'filter' => CHtml::textField('WmsProd[sku]', $model->sku, ['class' => 'form-control'])),
		array('name' => 'name', 'header' => 'Description', 'value' => '$data->name'),
		array('name' => 'ean', 'header' => 'Barcode', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl($data->type == 50 ? "pcaw/product/updateKit" : "pcaw/product/update", ["id" => $data->id, "from" => "stock"])."\" class=\"tab_link ajax-link\" title=\"Product ".$data->name."\">".$data->ean."</a>"'),
		array('name' => 'stock_qty', 'header' => 'SOH', 'value' => '$data->getStockQty(Yii::app()->user->org, "qty")'),
		array('name' => 'stock_res', 'header' => 'Allocated To Current Order', 'value' => '$data->getStockQty(Yii::app()->user->org, "qty_res")'),
		array('name' => 'stock_avail', 'header' => 'Available Stock', 'value' => '$data->getStockQty(Yii::app()->user->org, "avail")'),
		array('header' => 'Minimum Stock Alert', 'value' => '$data->getMinStockAlert(Yii::app()->user->org)'),
		array(
			'class' => 'application.extensions.booster.TbButtonColumn',
			'template' => '{details}',
			'header' => 'Actions',
			'buttons' => array(
				'details' => array(
					'visible' => 'true',
					'icon' => 'glyphicon glyphicon-search',
					'url' => 'Yii::app()->createUrl("pcaw/stock/view", ["id" => $data->id])',
					'options' => array('class' => 'tab_link ajax-link', 'label' => $this->t('View'), 'title' => 'View'),
				),
			),
		),
	),
));
?>

<script type="text/javascript">
$(function(){
	   $('a.export_search').on('mousedown', function(){
		var q = $('.filters input, .filters select').serialize()+'&'+$('.search-form form').serialize();
		$(this).attr('href', $(this).data('baseurl') + '?' + q);
	});
});
</script>