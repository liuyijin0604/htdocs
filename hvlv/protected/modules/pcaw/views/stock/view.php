<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink' => CHtml::link('Home', array('site/index/org_id/' . Yii::app()->session['org_id'])),
	'links' => array(
		'Stock List' => array('stock/list'),
	),
));
?>

<?php
$ec = new CDbCriteria;
if (in_array(Yii::app()->user->org, [Org::ORGID_3PL_COBAYER])) {
	$ec->addCondition('qty > 0');
}
if (Yii::app()->session['org_id'] != Yii::app()->user->org) {
	$ec->addCondition('t.org_id = ' . Yii::app()->session['org_id']);
} else {
	$ec->addCondition('t.org_id = ' . Yii::app()->user->org);
}
$ec->addCondition('t.bwf & 2 = 0');
$ec->with = 'prod';
$ec->addCondition('t.prod_id = ' . $prod_id);
$model = new WmsStock('search');
$this->widget('application.extensions.booster.TbExtendedGridView', array(
	'id' => 'wms-org-stock-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true, 30, $ec),
	'filter' => $model,
	'columns' => array(
		array('name' => 'cust_name', 'value' => '$data->customer->name', 'visible' => sizeof(User::getOrgIds()) > 1 ? true : false),
		array('name' => 'prod_sku', 'header' => 'Item Code / SKU', 'value' => '$data->prod->getSku(Yii::app()->session["org_id"])', 'filter' => CHtml::textField('WmsStock[prod_sku]', $model->prod_sku, ['class' => 'form-control'])),
		array('name' => 'prod_name', 'header' => 'Description', 'value' => 'empty($data->prod)? "" : $data->prod->name'),
		array('name' => 'prod_ean', 'header' => 'Barcode', 'type' => 'raw', 'value' => 'empty($data->prod)? "" : "<a href=\"".Yii::app()->createUrl("pcaw/product/update", ["id" => $data->prod_id, "from" => "stock"])."\" class=\"tab_link ajax-link\" title=\"Product ".$data->prod->name."\">".$data->prod->ean."</a>"'),
		array('name' => 'qty', 'header' => 'SOH', 'value' => '$data->getQty() + $data->qty_res'),
		array('name' => 'qty_res', 'header' => 'Allocated To Current Order', 'value' => '$data->qty_res'),
		array('header' => 'Available Stock', 'value' => '$data->getQty()'),
		array('name' => 'expiry', 'type' => 'raw', 'value' => '$data->showExpiry()'),
		'batch',
	),
));
?>