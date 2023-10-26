<h1><?=$this->t('All Billing');?></h1>

<?php
$model = new Billing;
$model->unsetAttributes();
$model->attributes = @$_GET['Billing'];
$ec = new CDbCriteria;
if ($model->status != 11) {
	$ec->addCondition('t.status != 11');
}
$ec->addCondition('t.org_id != ' . Org::ORGID_COURIER_AUPOST . ' OR (t.billing_cref NOT REGEXP "^C\\\d{8}" AND t.billing_cref NOT REGEXP "^DW\\\d{8}" AND t.billing_cref NOT REGEXP "^3PL\\\d{8}" AND t.billing_cref NOT REGEXP "^AP\\\d{8}")');
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
	'id' => 'billing-streamline-all-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(true, 30, 't.date DESC', $ec),
	'filter' => $model,
	'summaryText' => '',
	'showQuickBar' => false,
	'afterSave' => 'function(r) {
		if (r.done == true) {
			myApp.notice(r.msg, 5000);
		} else {
			myApp.alert(r.msg, false);
		}
	}',
	'columns' => array(
		// array( 'header' => 'No','type' => 'raw', 'name' => 'billing_ref', 'value' => '$data->getNo()'),
		array('name' => 'billing_cref', 'type' => 'raw', 'value' => '"<a href=\"" . Yii::app()->createUrl("billing/lineFix", array("id" => $data->id)) . "\" class=\"jqm_link\" data-win-class=\"XXL\">" . $data->billing_cref . "</a>"'),
		array('name' => 'date', 'value' => '$data->date'),
		array('name' => 'status', 'value' => '$data->getStatus()', 'filter' => CHtml::dropDownList('Billing[status]', $model->status, $this->t(Billing::$states), array('prompt' => $this->t('All')))),
		array('name' => 'client', 'value' => ' ( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->shortName(5)'),
		array('name' => 'currency', 'value' => '$data->getCurrency()', 'filter' => CHtml::dropDownList('Billing[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt' => $this->t('All'))),),
		array('name' => 'dpt_id', 'value' => '$data->getDptName()', 'filter' => CHtml::dropDownList('Billing[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt' => $this->t('All'))),),
		array('name' => 'dpmt', 'value' => '$data->getDpmt()', 'filter' => CHtml::dropDownList('Billing[dpmt]', $model->dpmt, Invoice::$dpmts, array('prompt' => $this->t('All'))),),
		array('header' => 'Total (Excl. GST)', 'value' => '$data->total - $data->gst'),
		array('header' => 'GST', 'name' => 'gst'),
		array('name' => 'total', 'value' => '$data->total'),
		array(
			'class' => 'CEditableButtonColumn',
			'template' => '{invoice1}{invoice2} {delete} {log}',
			'buttons' => array(
				'invoice1' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("billing/OTInvoice1", ["id" => $data->id])',
					'visible' => 'in_array($data->org_id, Org::$bsl_ot) && $data->date >= "2019-02-01"',
					'options' => array('class' => 'tab_link grid_edit_btn'),
					'label' => 'OT',
				),
				'invoice2' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("billing/OTInvoice2", ["id" => $data->id])',
					'visible' => 'in_array($data->org_id, [Org::ORGID_SUPPLIER_GLOBAVEND]) && $data->date >= "2019-02-01"',
					'options' => array('class' => 'tab_link grid_edit_btn'),
					'label' => 'OT',
				),
				'delete' => array(
					'imageUrl' => false,
					'url' => 'Yii::app()->createUrl("billing/delete", ["id" => $data->id])',
					'visible' => 'Acl::hasAccess("B:Billing/delete")',
					'options' => array('class' => 'delete_btn'),
				),
				'log' => array(
					'imageUrl'=>false,
					'options' => array('class' => 'jqm_link grid_view_btn', 'data-win-class' => 'L'),
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("billing/log", ["id" => $data->id])',
					'label' => 'Log',
				),
			),
		)
	),
)); ?>