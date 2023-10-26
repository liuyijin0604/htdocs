<?php
echo '<h3>Dispute</h3>';
$model = SiReconcile::model()->with('parent')->find('parent.inv_no = :no', [':no' => $model->billing_cref]);
$dispute = new DisputeLine();
$dispute->unsetAttributes();
$dispute->rec_id = $model->id;
$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id' => 'dispute_grid',
	'cssFile' => false,
	'dataProvider' => $dispute->search(true, 30),
	'filter' => $dispute,
	'columns' => [
		['name' => 'line.ref'],
		['name' => 'line.value'],
		['name' => 'line.my_value'],
		['name' => 'dispute_amount_ex_gst'],
		['name' => 'credit_amount_ex_gst'],
		['name' => 'note'],
		['name' => 'status'],
		// ['class' => 'application.extensions.CSpanableGridView.oSpanableButtonColumn',
		// 'template' => '{credit}',
		// 'buttons' => [
		// 	'credit' => [
		// 		'url' => 'Yii::app()->createURL("siReconcile/updateDisputeStatus")."?id=".$data->dispute_id',
		// 		'imageUrl' => false,
		// 		'visible' => 'true',
		// 		'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Operation'), 'title' => '$data->id'],
		// 	],
		// ],
		// 'spanable' => true, 'spanDepands' => ['$data->dispute_id']
		// ]
	],
]);
?>