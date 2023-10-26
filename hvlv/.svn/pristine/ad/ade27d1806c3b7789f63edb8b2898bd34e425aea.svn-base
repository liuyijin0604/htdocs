<h1><?=strtoupper($type." ".$this->t('Job List'));?></h1>


<?php

$columns = array(
    		//'pickup',
			array('header'=>'pickup','type'=>'raw','value'=>'$data->getPickupAddress(true)'),
         	array('header'=>'deliver','type'=>'raw','value'=>'$data->shipment->getCneeAddress(true)'),
         	array('name' =>'shipment.cbm','value'=>'$data->shipment->mdata[\'total_cbm\']'),
         	array('header'=>'pallet','value'=>'@$data->mdata["pallet"]'),
         	array('name' => 'deliveryBookingTime', 'value' => '$data->getDeliveryBookingTime()','cssClassExpression' => '$data->getShowColor()','filter'=>CHtml::textField('CargoProcess[deliveryBookingTime]',@$model->getBookingTime())),
         	array('header'=>'dimension','type'=>'raw','value'=>'$data->shipment->getDimension()'),
         	array('name'=>'shipment.weight','type'=>'raw','value'=>'$data->shipment->weight.\'kg\''),
         	array('header'=>'distance','value'=>'$data->shipment->getDeliveryDistance().\'km\''),
         	array('header'=>'region','value'=>'$data->getRegion()'),
         	'shipment.pkg',
         	array('header'=>'special requirements','type'=>'raw','value'=>'@$data->mdata["special_requirements"]'),
         	array('header'=>'note','type'=>'raw','value'=>'$data->getNote()'),);

if($type=="global")
{
	$columns[]= [
         		'header'=>'Operation',
         		'class'=>'oButtonColumn',
				'template'=>'{Accept}&nbsp;{Price}&nbsp;{picture}',
				'buttons'=>[
					'Accept' => [
						'url'=>' Yii::app()->createURL("dplatform/job/accept",["id"=>$data->id])',
						'imageUrl'=>false,
						'visible'=>'$data->status==CargoProcess::WAITINGAGENTDELIVERY?false:true',
						'options' => ['class' => 'jqm_link grid_edit_btn accept', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
					],
					'picture' => [
						'url'=>' Yii::app()->createURL("dplatform/job/getPictures")."?id=".$data->id',
						'imageUrl'=>false,
						'visible'=>'false',
						'options' => ['class' => 'jqm_link grid_view_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
					],
					'Price' => [
						'url'=>' Yii::app()->createURL("dplatform/invoice/viewCargoInvoice")."?id=".$data->id',
						'imageUrl'=>false,
						'visible'=>'false',
						'options' => ['class' => 'jqm_link grid_view_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
					],
				],
			];
}

if($type=="my")
{
	array_unshift($columns,['header' => 'Deliveried Status', 'value' => 'CargoProcess::$processTypes_driver_user[$data->isDriverDeliveried()]','filter'=>CHtml::dropDownList('CargoProcess[deliveriedStatus]', $model->deliveriedStatus, $this->t(CargoProcess::$processTypes_driver_user), ['prompt'=>$this->t('All')])],'hbn');
	$columns[] = ["name"=>'shipment.cnee.tel'];
	$columns[]= [
         		'header'=>'Operation',
         		'class'=>'oButtonColumn',
				'template'=>'{Sign}&nbsp;{Receipt}&nbsp;{Price}',
				'buttons'=>[
					'Sign' => [
						'url'=>' Yii::app()->createURL("dplatform/job/signPage",["id"=>$data->id])',
						'imageUrl'=>false,
						'visible'=>'@$data->mdata["driverDeliveryStatus"]==CargoProcess::DELIVERIED||$data->status>CargoProcess::WAITINGAGENTDELIVERY?false:true',
						'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
					],
					'Receipt' => [
						'url'=>' Yii::app()->createURL("dplatform/job/viewCargoReceipt",["id"=>$data->id])',
						'imageUrl'=>false,
						'visible'=>'@$data->mdata["driverDeliveryStatus"]==CargoProcess::DELIVERIED||$data->status>CargoProcess::WAITINGAGENTDELIVERY?true:false',
						'options' => ['class' => 'jqm_link grid_view_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
					],
					'Price' => [
						'url'=>' Yii::app()->createURL("dplatform/invoice/viewCargoInvoice")."?id=".$data->id',
						'imageUrl'=>false,
						'visible'=>'false',
						'options' => ['class' => 'jqm_link grid_view_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
					],
				],
			];
}



if($type=="fba"||$type=="b2b")
{

	$columns = array(
    		//'pickup',
			array('header' => 'Deliveried Status', 'value' => 'CargoProcess::$processTypes_driver_user[$data->isDriverDeliveried()]','filter'=>CHtml::dropDownList('CargoProcess[deliveriedStatus]', $model->deliveriedStatus, $this->t(CargoProcess::$processTypes_driver_user), ['prompt'=>$this->t('All')])),
			array('header'=>'hbn','value'=>'$data->getFBAConnote()'),
			array('header'=>'ref','type'=>'raw','value'=>'$data->getFBARef(true)'),
			array('header'=>'pickup','type'=>'raw','value'=>'$data->getPickupAddress(true)'),
         	array('header'=>'deliver','type'=>'raw','value'=>'!empty($data->mdata["cargo_receipt_addr"])?$data->mdata["cargo_receipt_addr"]:$data->shipment->getCneeAddress(true)'),
         	array('name' => 'deliveryBookingTime', 'value' => '$data->getDeliveryBookingTime()','cssClassExpression' => '$data->getShowColor()','filter'=>CHtml::textField('CargoProcess[deliveryBookingTime]',@$model->getBookingTime())),
         	array('header'=>'note','type'=>'raw','value'=>'$data->getNote()'),);

	$columns[]= [
         		'header'=>'Operation',
         		'class'=>'oButtonColumn',
				'template'=>'{Sign}&nbsp;{Receipt}&nbsp;{Price}',
				'buttons'=>[
					'Sign' => [
						'url'=>' Yii::app()->createURL("dplatform/job/signPage",["id"=>$data->id])',
						'imageUrl'=>false,
						'visible'=>'@$data->mdata["driverDeliveryStatus"]==CargoProcess::DELIVERIED||$data->status>CargoProcess::WAITINGAGENTDELIVERY?false:true',
						'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
					],
					'Receipt' => [
						'url'=>' Yii::app()->createURL("dplatform/job/viewCargoReceipt",["id"=>$data->id])',
						'imageUrl'=>false,
						'visible'=>'@$data->mdata["driverDeliveryStatus"]==CargoProcess::DELIVERIED||$data->status>CargoProcess::WAITINGAGENTDELIVERY?true:false',
						'options' => ['class' => 'jqm_link grid_view_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
					],
					'Price' => [
						'url'=>' Yii::app()->createURL("dplatform/invoice/viewCargoInvoice")."?id=".$data->id',
						'imageUrl'=>false,
						'visible'=>'false',
						'options' => ['class' => 'jqm_link grid_view_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
					],
				],
			];
}

 $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'job_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->getJobList($type),
    'template' => "{summary}\n{items}\n{pager}",
    'afterAjaxUpdate'=>'function(){initButtons();}',
    'columns'=>$columns,
    ),
    
); ?>
<script type="text/javascript">
function initButtons()
	{
		$('.accept').click(function(){
		if(confirm('Are you sure to accept this job?'))
		{
			let thisUrl = '<?php echo $this->createURL("job/accept");?>'+'?id='+$(this).attr('title');
	        $.ajax({
	            type : 'GET',
	            url : thisUrl,
	            data: [],
	            dataType: 'json',
	            success:function(resp){
	            	if(resp.done!=true)
	            	{
	            		alert("Job is accepted");
	            	}
	               $('#job_grid_view').yiiGridView('update');
	            },
	        });
	        return false;
		}
		return false;
		});
	}
$(function(){
	// $('.search-button').click(function(){
	// 	$('.search-form').toggle();
	// 	return false;
	// });
	// $('.search-form form').submit(function(){
	// 	$.fn.yiiGridView.update('job-grid', {
	// 		data: $(this).serialize()
	// 	});
	// 	return false;
	// });

	initButtons();
	// tab.bind('onOpen', function(){
	// 	$('#job-grid', panel).yiiGridView('update');
	// });
});

</script>
