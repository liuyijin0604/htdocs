<div class="form">
<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'wt-diff-form',
	'action' => Yii::app()->createUrl('wmsTask/wtDiffInv'),
	'enableAjaxValidation' => false,
)); ?>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Create Invoice'); ?>
	</div>

<?php $this->endWidget(); ?>
</div>

<?php
$model = new ReconciliationLine('search');
unset($model->consol_id);
$ec = new CDbCriteria;
$ec->with = ['consol', 'shipment'];
$ec->addCondition('consol.no LIKE "3PL%" AND t.shipment_no REGEXP ("7RFZ|TNT") AND t.weight > t.our_charge_weight AND JSON_VALUE(shipment.meta, "$.wt_diff_inv") IS NULL');

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'wt-diff-grid',
	'cssFile' => false,
	'dataProvider' => $model->search(false, $ec),
	'columns' => array(
		'shipment_no',
		array('header' => 'Task', 'value' => '$data->shipment->cref'),
		array('header' => 'Cust', 'value' => '$data->shipment->agent->name'),
		array('header' => 'Courier Charge Weight', 'value' => '$data->weight'),
		array('header' => 'Our Charge Weight', 'value' => '$data->our_charge_weight'),
	),
));
?>