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
	'dataProvider'=>$cargo->search(true, 30,false,true,$pod_id,$cargo_type),
	'filter'=>$cargo,
	'columns'=>[		
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"',),
		array('name'=>'ref','value'=>'$data->getRef()'),
		['name'=>'agent_id','value'=>'$data->shipment->agent_id'],

		//['name' => 'shipment.consol_no', 'type'=>'raw', 'value' => 'empty($data->shipment->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->shipment->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->shipment->consol_id))."\" class=\"tab_link\" title=\"".@$data->shipment->consol->no."\">".@$data->shipment->consol->no."</a>"','filter'=>CHtml::textField('ImParcel[consol_no]', $cargo->consol_no)],
		//['name' => 'shipment.consol.awb','value'=>'$data->shipment->consol->service==10?$data->shipment->consol->awb:@$data->shipment->consol->mdata["container_no"]','filter'=>CHtml::textField('CargoProcess[awb]',@$cargo->awb)],
		['name'=>'shipment.cnee.suburb','filter'=>CHtml::textField('CargoProcess[suburb]',@$cargo->suburb)],
		['name'=>'shipment.cnee.postcode','filter'=>CHtml::textField('CargoProcess[postcode]',@$cargo->postcode)],
		['name'=>'shipment.can','filter'=>CHtml::textField('CargoProcess[memo]',@$cargo->memo)],
		//array('name'=>'assignedAgent','filter'=>CHtml::dropDownList('CargoProcess[assignedUser]',@$cargo->assignedUser, @User::getTruckUsers(),array('prompt'=>'Select'))),
		['name' => 'status','type'=>'raw', 'value' => '$data->getFullStatus()','filter'=>false],
		['name'=>'shipment.status','value' => '$data->shipment->getStatus()','cssClassExpression' => '$data->getShowColor("held")'],
		['name' => 'deliveryBookingTime', 'value' => '$data->getDeliveryBookingTime()','cssClassExpression' => '$data->getShowColor()','filter'=>CHtml::textField('CargoProcess[deliveryBookingTime]',@$cargo->getBookingTime())],
		'note',
		array('name'=>'driver.name','filter'=>CHtml::dropDownList('CargoProcess[driver_id]',@$cargo->driver_id, @Org::getDriverList($cargo->getDriverTypes($pod_id)),array('prompt'=>'Select'))),
		['header'=>'First Process Time','value' => '$data->getFirstProcessTime()','cssClassExpression' => '$data->getShowColor("last_process_time")'],
		//['header'=>'Last Process Time','value' => '$data->getLastProcessTime()','cssClassExpression' => '$data->getShowColor("last_process_time")'],
		//['header'=>'goods available address', 'type' => 'raw','value'=>'$data->shipment->getAvailabelLabel()','filter'=>CHtml::textField('CargoProcess[goodsAvailableAddress]',@$cargo->goodsAvailableAddress)],
		['name'=>'shipment.pkg','cssClassExpression' => '$data->getShowColor("pkg")','filter'=>CHtml::textField('CargoProcess[pkg]',@$cargo->pkg),'value'=>'$data->getPackages()'],
		['name'=>'plt','type'=>'raw','value'=>'$data->getPltStr()'],
		['name'=>'shipment.weight','filter'=>CHtml::textField('CargoProcess[weight]',@$cargo->weight),'value'=>'$data->getWeight()'],
		['name' =>'shipment.cbm','value'=>'$data->getTotalCBM()','filter'=>CHtml::textField('CargoProcess[cbm]',@$cargo->cbm)],
		['name'=>'shipment.cnee.name','filter'=>CHtml::textField('CargoProcess[cname]',@$cargo->cname)],
		['name'=>'shipment.cnee.tel','filter'=>CHtml::textField('CargoProcess[tel]',@$cargo->tel)],
		['name'=>'shipment.cnee.address','filter'=>CHtml::textField('CargoProcess[address]',@$cargo->address)],		
		['name'=>'shipment.cnee.state','filter'=>CHtml::textField('CargoProcess[state]',@$cargo->state)],	
		["header"=>"Action",'type' => 'raw',"value"=>'CHtml::dropDownList($data->id, 0, [0=>"Default",1=>"Approve",2=>"Reject"], ["class"=>"task_assignuser","style"=>"width:90px;"])',"filter"=>false],
		//['header'=>'extra Info','type'=>'raw','value'=>'$data->shipment->getDGWarnings()','filter'=>CHtml::dropDownList('CargoProcess[exInfo]', $cargo->exInfo, $this->t(ImParcel::$exInfos), ['prompt'=>'All'])],
		// ['class'=>'oButtonColumn',
		// 	'template'=>'{approve}',
		// 	'buttons'=>[
		// 		'approve' => [
		// 			'url'=>' Yii::app()->createURL("cargoProcess/approve")."?id=".$data->id',
		// 			'imageUrl'=>false,
		// 			'visible'=>'true',
		// 			'options' => ['class' => 'jqm_link', 'label'=>$this->t('Approve'), 'title' => '$data->id'],
		// 		],
		// 	],
		// ]
	],
]);
?>
<script type="text/javascript">
	$(function() {
		$('body').off('change', '.task_assignuser').on('change', '.task_assignuser', function(){
			const tr = $(this).parents('tr');
			var listData = new FormData();
			listData.append('action',$('.task_assignuser', tr).val());
			listData.append('id',$('.task_assignuser', tr).attr('id'));
			htmlobj = $.ajax({
				url: '<?=$this->createUrl("cargoProcess/approve")?>',
				type: "post",
				data: listData,
				async: false,
            	contentType: false,
            	processData: false,
			});
			obj = JSON.parse(htmlobj.responseText);
			//alert(obj.isSuccess);
        	if (obj.isSuccess) {
				myApp.notice('approved', 5000);
			}
			else{
				myApp.notice('rejected', 5000);
			}
		});

		// $('#egw0').after('<div class="pull-right"><button type="button" data-toggle="modal" data-target="#modal-export" class="btn btn-default btn-sm">Export</button></div>');
	});
</script>