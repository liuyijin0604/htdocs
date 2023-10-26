<?php //
$this->widget('zii.widgets.CBreadcrumbs', [
	'homeLink' => CHtml::link('Home', ['site/index/org_id/' . Yii::app()->session['org_id']]),
	'links' => [
		'Invoice List',
	],
]);
?>
<h2>Invoice</h2>
<?php
$model = new Invoice('search');
$model->unsetAttributes();
if (!empty($_GET['Invoice'])) {
	$model->attributes = $_GET['Invoice'];
}
if (!in_array(Yii::app()->user->org, [Org::ORGID_3PL_COBAYER, Org::ORGID_3PL_SUNNYA])) {
	$model->toids = User::getOrgIds();
} else {
	$model->toids = User::getParentOrgIds();
}
$criteria = new CDbCriteria;
$criteria->addCondition('t.status in (2,3,6,7,8,9)');
// if (Yii::app()->session['org_id'] != Yii::app()->user->org && !empty(Yii::app()->session['org_id'])) {
// 	$criteria->addCondition('to_id = ' . Yii::app()->session['org_id']);
// }
$this->widget('application.extensions.booster.TbExtendedGridView', [
	'id' => 'wms-org-invoice-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(true, 30, "date DESC", $criteria),
	'filter' => $model,
	'enableSorting' => false,
	'columns' => [
		array('name' => 'to_name', 'value' => '$data->cust->name', 'visible' => sizeof(User::getOrgIds()) > 1 ? true : false),
		['name' => 'no', 'value' => '$data->no'],
		// ['header' => 'bill_to', 'value' => '$data->cust->name', ],
		['name' => 'status', 'value' => '$data->getStatus()', 'filter' => CHtml::dropDownList('Invoice[status]', $model->status, $this->t($model::$states), ['prompt' => $this->t('All'), 'class' => 'form-control'])],
		['header' => 'from date', 'value' => 'empty($data->mdata["billfrom"])?"":$data->mdata["billfrom"]'],
		['header' => 'to date', 'value' => 'empty($data->mdata["billto"])?"":$data->mdata["billto"]'],
		['name' => 'date'],
		['header' => 'Invoice Total', 'value' => '$data->getCurrency().$data->total'],
		[
			'class' => 'application.extensions.booster.TbButtonColumn',
			'template' => '{view}',
			'buttons' => [
				'view' => [
					'url' => 'Yii::app()->createURL("pcaw/accounts/invView", array("id" => $data->id))',
					'imageUrl' => false,
					'options' => ['class' => 'grid_view_btn', 'target' => '_blank'],
				],
				// 'detail' => [
				//     'url' => 'Yii::app()->createURL("pcaw/accounts/invDetail", array("id" => $data->id))',
				//     'label' => 'Detail',
				//     'imageUrl'=>false,
				//     'options' => ['class' => 'grid_file_btn', 'target' => '_blank'],
				// ],
			],
		],
	]]);
?>