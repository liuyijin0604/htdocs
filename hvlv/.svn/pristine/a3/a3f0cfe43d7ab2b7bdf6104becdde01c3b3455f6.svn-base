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
	'dataProvider'=>$cargo->search(true, 30,false,true,null,null),
	'filter'=>$cargo,
	'columns'=>[		
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"',),
		'ref',
		['name' => 'shipment.consol_no', 'type'=>'raw', 'value' => 'empty($data->shipment->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->shipment->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->shipment->consol_id))."\" class=\"tab_link\" title=\"".@$data->shipment->consol->no."\">".@$data->shipment->consol->no."</a>"','filter'=>CHtml::textField('CargoProcess[consol_no]', $cargo->consol_no)],
		['name' => 'shipment.consol.awb','filter'=>CHtml::textField('CargoProcess[awb]',@$cargo->awb)],
		['name'=>'shipment.cnee.suburb','filter'=>CHtml::textField('CargoProcess[suburb]',@$cargo->suburb)],
		['name'=>'shipment.cnee.postcode','filter'=>CHtml::textField('CargoProcess[postcode]',@$cargo->postcode)],
		['name'=>'shipment.can','filter'=>CHtml::textField('CargoProcess[memo]',@$cargo->memo)],
		array('name'=>'assignedAgent','filter'=>CHtml::dropDownList('CargoProcess[assignedUser]',@$cargo->assignedUser, @User::getTruckUsers(),array('prompt'=>'Select'))),
		['name' => 'status','type'=>'raw', 'value' => '$data->getFullStatus()','filter'=>CHtml::dropDownList('CargoProcess[status]', $cargo->status, $this->t(CargoProcess::$processTypes), ['prompt'=>$this->t('All')]),],
		['name'=>'shipment.status','value' => '$data->shipment->getStatus()','cssClassExpression' => '$data->getShowColor("held")'],
		['name' => 'deliveryBookingTime', 'value' => '$data->getDeliveryBookingTime()','cssClassExpression' => '$data->getShowColor()','filter'=>CHtml::textField('CargoProcess[deliveryBookingTime]',@$cargo->getBookingTime())],
		['name' => 'cargoType','value'=> 'CargoProcess::$cargoTypeList[$data->type]','filter'=>CHtml::dropDownList('CargoProcess[type]', $cargo->type, $this->t(CargoProcess::$cargoTypeList), ['prompt'=>$this->t('All')]),],
		'note',
		array('name'=>'driver.name','filter'=>CHtml::dropDownList('CargoProcess[driver_id]',@$cargo->driver_id, @Org::getDriverList($cargo->getDriverTypes()),array('prompt'=>'Select'))),
		['header'=>'First Process Time','value' => '$data->getFirstProcessTime()','cssClassExpression' => '$data->getShowColor("last_process_time")'],
		['header'=>'Last Process Time','value' => '$data->getLastProcessTime()','cssClassExpression' => '$data->getShowColor("last_process_time")'],
		['header'=>'goods available address','value'=>'@DmawbConsol::$available_address_name[$data->shipment->consol->mdata["goods_available_address"]]','filter'=>CHtml::textField('CargoProcess[goodsAvailableAddress]',@$cargo->goodsAvailableAddress)],
		['name'=>'shipment.pkg','filter'=>CHtml::textField('CargoProcess[pkg]',@$cargo->pkg),'value'=>'$data->getPackages()'],
		['name'=>'plt','type'=>'raw','value'=>'$data->getPltStr()'],
		['name'=>'shipment.weight','filter'=>CHtml::textField('CargoProcess[weight]',@$cargo->weight),'value'=>'$data->getWeight()'],
		['name' =>'shipment.cbm','value'=>'$data->getTotalCBM()','filter'=>CHtml::textField('CargoProcess[cbm]',@$cargo->cbm)],
		['name'=>'shipment.cnee.name','filter'=>CHtml::textField('CargoProcess[cname]',@$cargo->cname)],
		['name'=>'shipment.cnee.tel','filter'=>CHtml::textField('CargoProcess[tel]',@$cargo->tel)],
		['name'=>'shipment.cnee.address','filter'=>CHtml::textField('CargoProcess[address]',@$cargo->address)],
		['name'=>'shipment.cnee.state','filter'=>CHtml::textField('CargoProcess[state]',@$cargo->state)],		
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