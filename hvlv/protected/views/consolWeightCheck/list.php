<div style="right: 20px;position: absolute;">
    <div class="icon" style="background-position:-16px 0"></div><a class="tab_link" data-win-class="XL" href="<?=$this->createUrl('consolWeightCheck/createCheck');?>" title="New Check">New Check</a>
</div>

<h1><?=$this->t('Consol. Cost Check');?></h1>

<p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'consol-weight-check-list-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search1(),
	'filter'=>$model,
	'columns'=>array(
		// array('name' => 'no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("consolWeightCheck/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"',),
        // array('header' => 'Invoice Ref','name' => 'created','type' => 'raw','value' => '$data->getInvoiceRef()'),
        // 'channel',
        // 'channel_cost',
        // 'invoice_revenue',
        // 'cost_var',
        // 'channel_weight',
        // 'invoice_weight',
        // 'weight_var',
        // 'unit_cost_kg',
        // 'charge_rate_kg',
        // 'unit_rate_var',
        // 'channel_qty',
        // 'awb_qty',
        // 'qty_var',
        // 'created',
		// array(
		// 	'class'=>'oButtonColumn',
		// 	'template'=>'{update} {delete}',//{view}
		// 	'buttons'=>array
		// 	(
		// 		'update' => array(
		// 			'imageUrl'=>false,
		// 			'visible'=>'true',
		// 			'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->id'),
		// 		),
		// 	),
		// ),
		array('name' => 'billing_no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("consolWeightCheck/detail", array("id" => $data->billing_id, "type" => "billing"))."\" class=\"tab_link\" title=\"".$data->bill->no."\">".$data->bill->no."</a>"'),
		array('name' => 'invoice_no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("consolWeightCheck/detail", array("id" => $data->billing_id, "type" => "billing"))."\" class=\"tab_link\" title=\"".$data->bill->billing_cref."\">".$data->bill->billing_cref."</a>"'),
		array('name' => 'consol_no', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createUrl("consolWeightCheck/detail", array("id" => $data->consol_id, "type" => "consol"))."\" class=\"tab_link\" title=\"".$data->consol->no."\">".$data->consol->no."</a>"'),
		array('name' => 'consol_poc', 'value' => 'ExChannel::getName($data->consol->poc)'),
		array('name' => 'status', 'value' => '$data->getStatus()', 'filter'=>CHtml::dropDownList('ExconsolCost[status]', $model->status, $this->t(ExconsolCost::$states), array('prompt'=>$this->t('All'))),),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#consol-weight-check-list-grid', panel).yiiGridView('update');
	});
});
</script>
