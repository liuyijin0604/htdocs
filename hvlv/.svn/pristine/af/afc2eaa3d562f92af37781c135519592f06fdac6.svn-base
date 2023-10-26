<h1><?= strtoupper($type . " " . $this->t('cargo List')); ?></h1>
<div style="position:relative">
	<div style="right: 2px;position: absolute;">
		<a href="<?= Yii::app()->createUrl('dplatform/job/export', ['id' => $cjobID]); ?>" arget="_blank"><span class="glyphicon glyphicon-stats"></span> Export Current CargoLists</a>
	</div>
</div>
</br>
<?php
//For no unloading fee
if (User::getCurrentUser()->org_id == 2622 || User::getCurrentUser()->org_id == 4033) {
	$columns = array(
		//'pickup',
		array('header' => 'Pickup', 'type' => 'raw', 'value' => '$data->getPickupAddress(true)'),
		array('header' => 'Deliver', 'type' => 'raw', 'value' => '$data->shipment->getCneeAddress(true)'),
		array('name' => 'shipment.cbm', 'value' => '$data->shipment->cbm*$data->shipment->pkg'),
		// array('header'=>'pallet','value'=>'@$data->mdata["pallet"]'),
		// array('name' => 'deliveryBookingTime', 'value' => '$data->getDeliveryBookingTime()','cssClassExpression' => '$data->getShowColor()','filter'=>CHtml::textField('CargoProcess[deliveryBookingTime]',@$model->getBookingTime())),
		// array('header'=>'dimension','type'=>'raw','value'=>'$data->shipment->getDimension()'),
		array('name' => 'shipment.weight', 'type' => 'raw', 'value' => '$data->getWeight(true).\'kg\''),
		array('header' => 'Zone', 'value' => '$data->getZone()'),
		array('header' => 'GatePass', 'value' => '@$data->isSignGatePassOnTimeHtml()'),
		'shipment.pkg',
		array('header'=>'Fee','type'=>'raw','value'=>'@$data->getCargoProcessFee()'),
		array('header' => 'Note', 'type' => 'raw', 'value' => '$data->getNote(true)')
	);
	//For Tony assign driver tony:4072
} elseif (User::getCurrentUser()->org_id == 4072) {
	$columns = array(
		//'pickup',
		array('header' => 'Pickup', 'type' => 'raw', 'value' => '$data->getPickupAddress(true)'),
		array('header' => 'Deliver', 'type' => 'raw', 'value' => '$data->shipment->getCneeAddress(true)'),
		array('name' => 'shipment.cbm', 'value' => '$data->shipment->cbm*$data->shipment->pkg'),
		// array('header'=>'pallet','value'=>'@$data->mdata["pallet"]'),
		// array('name' => 'deliveryBookingTime', 'value' => '$data->getDeliveryBookingTime()','cssClassExpression' => '$data->getShowColor()','filter'=>CHtml::textField('CargoProcess[deliveryBookingTime]',@$model->getBookingTime())),
		// array('header'=>'dimension','type'=>'raw','value'=>'$data->shipment->getDimension()'),
		array('name' => 'shipment.weight', 'type' => 'raw', 'value' => '$data->getWeight(true).\'kg\''),
		array('header' => 'Zone', 'value' => '$data->getZone()'),
		array('header' => 'GatePass', 'value' => '@$data->isSignGatePassOnTimeHtml()'),
		//array('header' => 'Distance', 'value' => '$data->shipment->getDeliveryDistance().\'km\''),
		'shipment.pkg',
		array("header" => "Driver", 'type' => 'raw', "value" => 'CHtml::dropDownList($data->id, 0, $data->getTonyDriver(), ["prompt" => empty($data->mdata["tony_assign_driver"])? "Choose Driver":$data->mdata["tony_assign_driver"],"class"=>"task_assignuser","style"=>"width:90px;"])', "filter" => false),
		//array('header' => 'Unloading Fee', 'type' => 'raw', 'value' => '@$data->getUnloadingFee()'),
		array('header' => 'Note', 'type' => 'raw', 'value' => '$data->getNote(true)')
	);
} else {
	$columns = array(
		//'pickup',
		array('header' => 'Pickup', 'type' => 'raw', 'value' => '$data->getPickupAddress(true)'),
		array('header' => 'Deliver', 'type' => 'raw', 'value' => '$data->shipment->getCneeAddress(true)'),
		array('name' => 'shipment.cbm', 'value' => '$data->shipment->cbm*$data->shipment->pkg'),
		// array('header'=>'pallet','value'=>'@$data->mdata["pallet"]'),
		// array('name' => 'deliveryBookingTime', 'value' => '$data->getDeliveryBookingTime()','cssClassExpression' => '$data->getShowColor()','filter'=>CHtml::textField('CargoProcess[deliveryBookingTime]',@$model->getBookingTime())),
		// array('header'=>'dimension','type'=>'raw','value'=>'$data->shipment->getDimension()'),
		array('name' => 'shipment.weight', 'type' => 'raw', 'value' => '$data->getWeight(true).\'kg\''),
		array('header' => 'Zone', 'value' => '@$data->getZone()'),
		array('header' => 'GatePass', 'value' => '$data->isSignGatePassOnTimeHtml()'),
		//array('header' => 'Distance', 'value' => '$data->shipment->getDeliveryDistance().\'km\''),
		'shipment.pkg',
		//array('header' => 'Unloading Fee', 'type' => 'raw', 'value' => '@$data->getUnloadingFee()'),
		array('header' => 'Note', 'type' => 'raw', 'value' => '$data->getNote(true)')
	);
}


if ($type == "global") {
	$columns[] = [
		'header' => 'Operation',
		'class' => 'oButtonColumn',
		'template' => '{Accept}&nbsp;{Price}&nbsp;{picture}',
		'buttons' => [
			'Accept' => [
				'url' => ' Yii::app()->createURL("dplatform/job/accept",["id"=>$data->id])',
				'imageUrl' => false,
				'visible' => '$data->status==CargoProcess::WAITINGAGENTDELIVERY?false:true',
				'options' => ['class' => 'jqm_link grid_edit_btn accept', 'label' => $this->t('Operation'), 'title' => '$data->id'],
			],
			'picture' => [
				'url' => ' Yii::app()->createURL("dplatform/job/getPictures")."?id=".$data->id',
				'imageUrl' => false,
				'visible' => 'false',
				'options' => ['class' => 'jqm_link grid_view_btn', 'label' => $this->t('Operation'), 'title' => '$data->id'],
			],
			'Price' => [
				'url' => ' Yii::app()->createURL("dplatform/invoice/viewCargoInvoice")."?id=".$data->id',
				'imageUrl' => false,
				'visible' => 'false',
				'options' => ['class' => 'jqm_link grid_view_btn', 'label' => $this->t('Operation'), 'title' => '$data->id'],
			],
		],
	];
}

if ($type == "my") {
	array_unshift($columns, ['header' => 'Deliveried Status', 'value' => 'CargoProcess::$processTypes_driver_user[$data->isDriverDeliveried()]', 'filter' => CHtml::dropDownList('CargoProcess[deliveriedStatus]', $model->deliveriedStatus, $this->t(CargoProcess::$processTypes_driver_user), ['prompt' => $this->t('All'),'style'=>'width:120px;height:33px;'])], 'hbn');
	$columns[] = ["name" => 'shipment.cnee.tel'];
	$columns[] = [
		'header' => 'Operation',
		'class' => 'oButtonColumn',
		'template' => '{Sign}&nbsp;{Receipt}&nbsp;{Price}&nbsp;{Error}',
		'buttons' => [
			'Sign' => [
				'url' => ' Yii::app()->createURL("dplatform/job/signPage",["id"=>$data->id])',
				'imageUrl' => false,
				'visible' => '@$data->mdata["driverDeliveryStatus"]==CargoProcess::DELIVERIED||$data->status>CargoProcess::WAITINGAGENTDELIVERY?false:true',
				'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Operation'), 'title' => '$data->id'],
			],
			'Receipt' => [
				'url' => ' Yii::app()->createURL("dplatform/job/viewCargoReceipt",["id"=>$data->id])',
				'imageUrl' => false,
				'visible' => '@$data->mdata["driverDeliveryStatus"]==CargoProcess::DELIVERIED||$data->status>CargoProcess::WAITINGAGENTDELIVERY&&$data->status!=99&&$data->type==1?true:false',
				'options' => ['class' => 'jqm_link grid_view_btn', 'label' => $this->t('Operation'), 'title' => '$data->id'],
			],
			'Price' => [
				'url' => ' Yii::app()->createURL("dplatform/invoice/viewCargoInvoice")."?id=".$data->id',
				'imageUrl' => false,
				'visible' => 'false',
				'options' => ['class' => 'jqm_link grid_view_btn', 'label' => $this->t('Operation'), 'title' => '$data->id'],
			],
			'Error' => [
				'url' => ' Yii::app()->createURL("dplatform/job/signErrorPage",["id"=>$data->id])',
				'imageUrl' => false,
				'visible' => '@$data->mdata["driverDeliveryStatus"]==CargoProcess::DELIVERIED||$data->status>CargoProcess::WAITINGAGENTDELIVERY?false:true',
				'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Operation'), 'title' => '$data->id'],
			],
			// 'Error' => [
			// 	'imageUrl' => false,
			// 	'visible' => '@$data->mdata["driverDeliveryStatus"]==CargoProcess::DELIVERIED||$data->status>CargoProcess::WAITINGAGENTDELIVERY?false:true',
			// 	'options' => ['class' => 'errorcargo grid_edit_btn', 'label' => $this->t('Operation'), 'title' => '$data->id'],
			// 	'click' => 'errorScreen',
			// ],
		],
	];
}



if ($type == "fba" || $type == "b2b") {

	$columns = array(
		//'pickup',
		array('header' => 'Deliveried Status', 'value' => 'CargoProcess::$processTypes_driver_user[$data->isDriverDeliveried()]', 'filter' => CHtml::dropDownList('CargoProcess[deliveriedStatus]', $model->deliveriedStatus, $this->t(CargoProcess::$processTypes_driver_user), ['prompt' => $this->t('All')])),
		array('header' => 'hbn', 'value' => '$data->getFBAConnote()'),
		array('header' => 'ref', 'type' => 'raw', 'value' => '$data->getFBARef(true)'),
		array('header' => 'pickup', 'type' => 'raw', 'value' => '$data->getPickupAddress(true)'),
		array('header' => 'GatePass', 'value' => '@$data->isSignGatePassOnTimeHtml()'),
		array('header' => 'deliver', 'type' => 'raw', 'value' => '!empty($data->mdata["cargo_receipt_addr"])?$data->mdata["cargo_receipt_addr"]:$data->shipment->getCneeAddress(true)'),
		array('name' => 'deliveryBookingTime', 'value' => '$data->getDeliveryBookingTime()', 'cssClassExpression' => '$data->getShowColor()', 'filter' => CHtml::textField('CargoProcess[deliveryBookingTime]', @$model->getBookingTime())),
		array('header' => 'note', 'type' => 'raw', 'value' => '$data->getNote()'),
	);

	$columns[] = [
		'header' => 'Operation',
		'class' => 'oButtonColumn',
		'template' => '{Sign}&nbsp;{Receipt}&nbsp;{Price}',
		'buttons' => [
			'Sign' => [
				'url' => ' Yii::app()->createURL("dplatform/job/signPage",["id"=>$data->id])',
				'imageUrl' => false,
				'visible' => '@$data->mdata["driverDeliveryStatus"]==CargoProcess::DELIVERIED||$data->status>CargoProcess::WAITINGAGENTDELIVERY?false:true',
				'options' => ['class' => 'jqm_link grid_edit_btn', 'label' => $this->t('Operation'), 'title' => '$data->id'],
			],
			'Receipt' => [
				'url' => ' Yii::app()->createURL("dplatform/job/viewCargoReceipt",["id"=>$data->id])',
				'imageUrl' => false,
				'visible' => '@$data->mdata["driverDeliveryStatus"]==CargoProcess::DELIVERIED||$data->status>CargoProcess::WAITINGAGENTDELIVERY?true:false',
				'options' => ['class' => 'jqm_link grid_view_btn', 'label' => $this->t('Operation'), 'title' => '$data->id'],
			],
			'Price' => [
				'url' => ' Yii::app()->createURL("dplatform/invoice/viewCargoInvoice")."?id=".$data->id',
				'imageUrl' => false,
				'visible' => 'false',
				'options' => ['class' => 'jqm_link grid_view_btn', 'label' => $this->t('Operation'), 'title' => '$data->id'],
			],
		],
	];
}

$this->widget(
	'application.extensions.booster.TbExtendedGridView',
	array(
		'fixedHeader' => true,
		'id' => 'job_grid_view',
		'filter' => $model,
		'type' => 'striped bordered',
		'headerOffset' => 40,
		'responsiveTable' => true,
		'dataProvider' => $model->getJobCargosList($type),
		'template' => "{items}\n{pager}",
		//'template' => "{summary}\n{items}\n{pager}",
		'afterAjaxUpdate' => 'function(){initButtons();}',
		'columns' => $columns,
	),

);

?>
<script type="text/javascript">
	function errorScreen() {
		var cpid = $(this).attr('title');
		bootbox.prompt({
			title: "Please choose the error type！",
			inputType: 'select',
			//Cargoprocess::$cargoProcessErrorList 
			inputOptions: [{
					text: 'Please Choose One',
					value: '',
				},
				{
					text: 'No one answer the phone, can not leave the goods at the location',
					value: '1',
				},
				{
					text: 'Consignee can not unload the goods',
					value: '2',
				},
				{
					text: 'Consignee change the address',
					value: '3',
				},
				{
					text: 'Damaged goods',
					value: '4',
				},
				{
					text: 'Wrong information',
					value: '5',
				},
				{
					text: 'Delay',
					value: '6',
				},
			],
			callback: function($result) {
				if($result == null){
					return;
				}
				if ($result == "") {
					alert("Please choose the error type!");
					return false;
				} else {
					const tr = $(this).parents('tr');
					var listData = new FormData();
					//alert($result);
					//var id = $('.error', tr).attr('id');
					listData.append('id', cpid);
					listData.append('error_id', $result);
					htmlobj = $.ajax({
						url: '<?= $this->createUrl("job/errorCargoProcess") ?>',
						type: "post",
						data: listData,
						async: false,
						contentType: false,
						processData: false,
					});
					obj = JSON.parse(htmlobj.responseText);
					if (obj!=null) {
						bootbox.alert(obj.info);
					}
				}
			}
		});
	}

	function updateCargoProcess() {

	}

	function initButtons() {
		$('.accept').click(function() {
			if (confirm('Are you sure to accept this job?')) {
				let thisUrl = '<?php echo $this->createURL("job/accept"); ?>' + '?id=' + $(this).attr('title');
				$.ajax({
					type: 'GET',
					url: thisUrl,
					data: [],
					dataType: 'json',
					success: function(resp) {
						if (resp.done != true) {
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
	$(function() {
		$('body').off('change', '.task_assignuser').on('change', '.task_assignuser', function() {
			const tr = $(this).parents('tr');
			var listData = new FormData();
			listData.append('driver_id', $('.task_assignuser', tr).val());
			listData.append('id', $('.task_assignuser', tr).attr('id'));
			htmlobj = $.ajax({
				url: '<?= $this->createUrl("job/tonyAssignDriver") ?>',
				type: "post",
				data: listData,
				async: false,
				contentType: false,
				processData: false,
			});
			obj = JSON.parse(htmlobj.responseText);
			//alert(obj.isSuccess);
			if (obj.isSuccess) {
				bootbox.confirm({
					title: 'Successful Updated',
					message: " Cargo Be Updated! <br/>" + obj.info,
					callback: function(result) {
						if (result) {
							location.reload();
						}
					}
				});
			} else {
				bootbox.alert({
					title: 'Update Error',
					message: " Cargo Error! Please fix the issues! <br/>" + obj.info,
					backdrop: true
				});
			}
		});

		//initButtons();
		// tab.bind('onOpen', function(){
		// 	$('#job-grid', panel).yiiGridView('update');
		// });
	});
</script>