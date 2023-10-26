<div class="row" style="margin: 20px 0">
	<div class="col-xs-12" style="padding: 0">
		<a style="width: 10em" type="button" role="menuitem" tabindex="-1" target="_blank" href="#" data-baseurl="<?=$this->createUrl('report/export', array('type' => 'minimum-stock-alert'))?>" class="export_search btn btn-default" id="minimum-stock-alert">Export</a>
	</div>
</div>
<?php
$prods = [];
$orgs = WmsProdOrg::model()->findAll('org_id = :org_id AND JSON_VALUE(meta, "$.min_stock_alert") IS NOT NULL', [':org_id' => Yii::app()->user->org]);
foreach ($orgs as $org) {
	$qty = 0;
	foreach ($org->stocks as $stock) {
		$qty += $stock->availQty();
	}
	if ($qty < $org->mdata['min_stock_alert']) {
		$prods[] = $org->prod_id;
	}
}

$model = new WmsProd('search');
$model->unsetAttributes();
if (!empty($_GET['WmsProd'])) {
	$model->setAttributes($_GET['WmsProd']);
}
$ec = new CDbCriteria;
if (!empty($prods)) {
	$ec->addCondition('id IN (' . implode(',', $prods) . ')');
} else {
	$ec->addCondition('1 = 0');
}
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id' => 'minimum-stock-alert-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(true, 30, $ec),
	'filter' => $model,
	'template' => "{items}\n{pager}",
	'columns' => array(
		array('name' => 'sku', 'header' => 'Item Code / SKU', 'value' => '$data->getSku(Yii::app()->user->org)', 'filter' => CHtml::textField('WmsProd[sku]', $model->sku, ['class' => 'form-control'])),
		array('name' => 'name', 'header' => 'Description', 'value' => '$data->name'),
		array('name' => 'ean', 'header' => 'Barcode', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("pcaw/product/update", ["id" => $data->id, "from" => "stock"])."\" class=\"tab_link ajax-link\" title=\"Product ".$data->name."\">".$data->ean."</a>"'),
		array('header' => 'Available Stock', 'value' => 'intval($data->availQty(Yii::app()->user->org))'),
		array('header' => 'Minimum Stock Alert', 'value' => 'intval($data->getMinStockAlert(Yii::app()->user->org))'),
		array(
			'class' => 'application.extensions.booster.TbButtonColumn',
			'template' => '{export}',
			'buttons' => array(
				'export' => array(
					'visible' => 'true',
					'icon' => 'glyphicon glyphicon-download',
					'url' => 'Yii::app()->createUrl("pcaw/report/exportSingle", ["type" => "minimum-stock-alert", "id" => $data->id])',
					'options' => array('target' => '_blank', 'label' => 'Export', 'title' => 'Export'),
				),
			),
		),
	),
)); ?>