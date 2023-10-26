<div style="right: 20px;position: absolute;">
</div>

<h1><?=$this->t('Consol. Cost Check');?></h1>

<p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'consol-weight-check-consollist-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array('header' => 'Consol.#','name' => 'consol_id','type' => 'raw', 'value' => '"<a href=\"ExcoConsol/update/" . (!empty($data->consol)?$data->consol->id:"") . "\" class=\"tab_link\" title=\"" . $data->consol_id . "\">" . $data->consol_id . "</a>"'),
		array('header' => 'Date','name' => 'consol_date'),
		array('header' => 'AWB NO.','name' => 'awb_no'),
		array('header' => 'Invoice Ref.','name' => 'invoice_ref'),
		array('header' => 'AWB Weight','name' => 'awb_weight'),
		array('header' => 'Invoice Weight','name' => 'invoice_weight'),
		array('header' => 'Channel Weight','name' => 'channel_weight'),
		array('header' => 'Wt. Var', 'name' => 'weight_var'),
		array('header' => 'AWB Qty','name' => 'awb_qty'),
		array('header' => 'Channel Qty','name' => 'channel_qty'),
		array('header' => 'Qty Var', 'name' => 'qty_var'),
		array('header' => 'ACCR Clearance', 'name' => 'accr_clearance'),
		array('header' => 'Clearance','name' => 'clearance_cost'),
		array('header' => 'Var', 'name' => 'clearance_var'),
		array('header' => 'ACCR Delivery', 'name' => 'accr_delivery'),
		array('header' => 'Delivery','name' => 'delivery_cost'),
		array('header' => 'Var', 'name' => 'delivery_var'),
		array('header' => 'ACCR Duty', 'name' => 'accr_duty'),
		array('header' => 'Duty','name' => 'duty'),
		array('header' => 'Var', 'name' => 'duty_var'),
		array('header' => 'ACCR Others', 'name' => 'accr_others'),
		array('header' => 'Others','name' => 'others'),
		array('header' => 'Var', 'name' => 'others_var'),
		array('header' => 'Invoice Revenue','name' => 'invoice_revenue'),
		array('header' => 'Total Cost','name' => 'ttlCost', 'htmlOptions' => ['class' => 'show-total-cost']),
		array('header' => 'Var', 'name' => 'cost_var'),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{update} {delete}',//{view}
			'buttons'=>array
			(
				'update' => array(
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => array('class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->id'),
				),
			),
		),
	),
)); ?>
<script type="text/javascript">
$(function(){
	var tab = $('#<?=$_GET["tabid"];?>');
	var panel = tab.data('panel');
	tab.bind('onOpen', function(){
		$('#consol-weight-check-consollist-grid', panel).yiiGridView('update');
	});
});
</script>
