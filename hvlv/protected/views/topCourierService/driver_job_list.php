<h3><?=$name?></h3>
<style>
    .cloumn_red{
        background-color:pink;
    }  
    .column_direct{
        color: green;
        font-weight: bold;
    }
</style>

<?php 
	
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id'=>$_GET["tabid"].'_cargo_process_grid',
	'cssFile' => false,
	'dataProvider'=>$cargo->getJobList('all'),
	'filter'=>$cargo,
	'columns'=>[		
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"',),
		'ref',
		array('name'=>'shipment.can','filter'=>CHtml::hiddenField('CargoProcess[searchFrom]',@$cargo->searchFrom).CHtml::hiddenField('CargoProcess[searchTo]',@$cargo->searchTo)),
		array('name'=>'assignedAgent','filter'=>CHtml::dropDownList('CargoProcess[assignedUser]',@$cargo->assignedUser, @User::getTruckUsers(),array('prompt'=>'Select'))),
		['name' => 'status','type'=>'raw', 'value' => '$data->getFullStatus()','filter'=>CHtml::dropDownList('CargoProcess[status]', $cargo->status, $this->t(CargoProcess::$processTypes_driver), ['prompt'=>$this->t('All')]),],
		['name'=>'shipment.status','value' => '$data->shipment->getStatus()'],
		['name' => 'shipment.ddpt_id', 'value' => '@$data->shipment->depot->name'],
		['name' => 'deliveryBookingTime', 'value' => '$data->getDeliveryBookingTime()','cssClassExpression' => '$data->getShowColor()','filter'=>CHtml::textField('CargoProcess[deliveryBookingTime]',@$cargo->getBookingTime())],
		['name' => 'cargoType','value'=> 'CargoProcess::$cargoTypeList[$data->type]','filter'=>CHtml::dropDownList('CargoProcess[type]', $cargo->type, $this->t(CargoProcess::$cargoTypeList), ['prompt'=>$this->t('All')]),],
		'note',
		array('name'=>'driver.name','filter'=>CHtml::dropDownList('CargoProcess[driver_id]',@$cargo->driver_id, @Org::getDriverList($cargo->getDriverTypes()),array('prompt'=>'Select'))),
		'shipment.pkg',
		'shipment.weight',
		['name' =>'shipment.cbm','value'=>'$data->shipment->mdata[\'total_cbm\']'],
		'shipment.cnee.name',
		'shipment.cnee.tel',
		'shipment.cnee.address',
		'shipment.cnee.suburb',
		'shipment.cnee.state',
		'shipment.cnee.postcode',
		['class'=>'oButtonColumn',
			'template'=>'{log}',
			'buttons'=>[
				'log' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("cargoProcess/log", ["id" => $data->id])',
					'label' => 'Log'
				],
			],
		]
	],
]);


; ?>