<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
	'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		'Surplus Shipments',
	),
));

?>

<div style="right: 20px;position: absolute;">
<a class="export_search" target="_blank" href="<?=$this->createUrl('shipment/exportSurplus', ['typ' => '']);?>"><div style="background-position:-48px -688px" class="icon"></div>Export Current Search</a>
</div>

<?php
	echo "<h1>Surplus Shipments List</h1>";
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
				'id'=>'unknown_shipment-grid',
				'cssFile' => false,
				'dataProvider'=> $model->search(true,15,'t.create_time'),
				'filter'=>$model,
				'afterAjaxUpdate'=>'function(){initList();}',
				'columns'=>[
					['name'=>'barcode'],
					['name'=>'container_no','header'=>'awb'],
					['name' => 'shipment.status','header'=>'Shipment Status','value' => '!empty($data->shipment_id)?$data->shipment->getStatus():""', 'filter' => CHtml::dropDownList(get_class($model).'[shipmentStatus]', $model->shipmentStatus, $this->t(ImParcel::$states), ['prompt' => $this->t('All')])],
					['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('ImportsUnknownShipment[status]', $model->status, $this->t($model::$states), ['prompt'=>$this->t('All')]),],
					['name'=>'ground_label'],
					['name'=>'create_time','value'=>'date("d/m/Y",strtotime($data->create_time))'],
					['name'=>'client_upload_time',"value"=>'$data->client_upload_time!="0000-00-00 00:00:00"?date("d/m/Y",strtotime($data->client_upload_time)):""'],
					['header'=>'Check In','value'=>'!empty($data->shipment_id)?@$data->shipment->mdata["scan_time"]:""'],
					['name'=>'customer_comment','type'=>'raw','value'=>'CHtml::textfield("customer_comment", $data->customer_comment,["class"=>"customer_comment","title"=>$data->id])'],
					['name'=>'comment','header'=>'TLA Notes']
				],
			]);


?>

<script type="text/javascript">
function initList(){

	$('.customer_comment').on('blur', function(){
		var id = $(this).attr('title'); 
		$.ajax({
			url: '<?=$this->createUrl("shipment/exceptionShipmentOperation")?>'+"?id="+id,
			type: "post",
			data: {"comment":$(this).val()},
			success: function(r) {
				
			 },
			error: function(e) {
				console.log(e);
			}
		});
	});

}
 initList();
 	$('a.export_search').on('mousedown', function(){
		var q = $('.filters input, .filters select').serialize();
		$(this).attr('href', '<?=$this->createUrl('shipment/exportSurplus', ['typ' => '']);?>&' + q);
	});
</script>