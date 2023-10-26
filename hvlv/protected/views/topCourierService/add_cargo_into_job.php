<h3>Add Cargos Into Job <?=$model->job_name?></h3>
<style>
    .cloumn_red{
        background-color:pink;
    }  
    .column_direct{
        color: green;
        font-weight: bold;
    }
    .jqmWindow {
		    display: none;
		    position: absolute;
		    top: 12%;
		    left: 4%;
		    margin-left: -0px;
		    width: 90%;
		    background-color: #EBF0FA;
		    color: #333;
		    border: 1px solid black;
		    padding: 12px;
			max-height: 75%;
		}
</style>
<?php 
echo "Select All",CHtml::checkbox("add_job_id_all",0);
	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id'=>$_GET["tabid"].'_cargo_process_for_into_job_grid',
	'cssFile' => false,
	'dataProvider'=>$cargo->search(false, 30,false,true,$model->dpt_id,$model->type),
	'filter'=>$cargo,
	'columns'=>[
		['header'=>'select','type'=>'raw','value'=>'CHtml::checkbox("cargo_id",0,["value"=>$data->id])'],
		/* array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"',),
		array('name'=>'ref','value'=>'$data->getRef()'),
		['name'=>'agent_id','value'=>'$data->shipment->agent_id'],

		['name' => 'shipment.consol_no', 'type'=>'raw', 'value' => 'empty($data->shipment->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->shipment->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->shipment->consol_id))."\" class=\"tab_link\" title=\"".@$data->shipment->consol->no."\">".@$data->shipment->consol->no."</a>"','filter'=>CHtml::textField('ImParcel[consol_no]', $cargo->consol_no)],
		['name' => 'shipment.consol.awb','filter'=>CHtml::hiddenField('CargoProcess[status]',@$cargo->status).CHtml::textField('CargoProcess[awb]',@$cargo->awb)],
		['name'=>'shipment.cnee.suburb','filter'=>CHtml::textField('CargoProcess[suburb]',@$cargo->suburb)],
		['name'=>'shipment.cnee.postcode','filter'=>CHtml::textField('CargoProcess[postcode]',@$cargo->postcode)],
		['name'=>'shipment.can','filter'=>CHtml::textField('CargoProcess[memo]',@$cargo->memo)],
		array('name'=>'assignedAgent','filter'=>CHtml::dropDownList('CargoProcess[assignedUser]',@$cargo->assignedUser, @User::getTruckUsers(),array('prompt'=>'Select'))),
		['name' => 'status','type'=>'raw', 'value' => '$data->getFullStatus()','filter'=>false],
		['name'=>'shipment.status','value' => '$data->shipment->getStatus()','cssClassExpression' => '$data->getShowColor("held")'],
		['header'=>'Unpacking Date','value'=>'@$data->shipment->consol->mdata["ContainerUnloadDate"]'],
		['name' => 'deliveryBookingTime', 'value' => '$data->getDeliveryBookingTime()','cssClassExpression' => '$data->getShowColor()','filter'=>CHtml::textField('CargoProcess[deliveryBookingTime]',@$cargo->getBookingTime())],
		'note',
        array('header'=>'distance','value'=>'$data->shipment->getDeliveryDistance().\'km\''),
        array('header'=>'region','value'=>'$data->getRegion('.$model->dpt_id.')'),
        array('header'=>'zone','value'=>'$data->getZone()'),
		array('name'=>'driver.name','filter'=>CHtml::dropDownList('CargoProcess[driver_id]',@$cargo->driver_id, @Org::getDriverList($cargo->getDriverTypes($model->dpt_id)),array('prompt'=>'Select'))),
		['header'=>'First Process Time','value' => '$data->getFirstProcessTime()','cssClassExpression' => '$data->getShowColor("last_process_time")'],
		['header'=>'Last Process Time','value' => '$data->getLastProcessTime()','cssClassExpression' => '$data->getShowColor("last_process_time")'],
		['header'=>'goods available address', 'type' => 'raw','value'=>'$data->shipment->getAvailabelLabel()','filter'=>CHtml::textField('CargoProcess[goodsAvailableAddress]',@$cargo->goodsAvailableAddress)],
		['name'=>'shipment.pkg','cssClassExpression' => '$data->getShowColor("pkg")','filter'=>CHtml::textField('CargoProcess[pkg]',@$cargo->pkg),'value'=>'$data->getPackages()'],
		['name'=>'plt','type'=>'raw','value'=>'$data->getPltStr()'],
		['name'=>'shipment.weight','filter'=>CHtml::textField('CargoProcess[weight]',@$cargo->weight),'value'=>'$data->getWeight()'],
		['name' =>'shipment.cbm','value'=>'$data->getTotalCBM()','filter'=>CHtml::textField('CargoProcess[cbm]',@$cargo->cbm)],
		['name'=>'shipment.cnee.name','filter'=>CHtml::textField('CargoProcess[cname]',@$cargo->cname)],
		['name'=>'shipment.cnee.tel','filter'=>CHtml::textField('CargoProcess[tel]',@$cargo->tel)],
		['name'=>'shipment.cnee.address','filter'=>CHtml::textField('CargoProcess[address]',@$cargo->address)],	
		['name'=>'shipment.cnee.state','filter'=>CHtml::textField('CargoProcess[state]',@$cargo->state)],
		['header'=>'extra Info','type'=>'raw','value'=>'$data->shipment->getDGWarnings().($data->shipment->isOversize()?"<label style=\"color:red\">Oversize</label>":"")','filter'=>CHtml::dropDownList('CargoProcess[exInfo]', $cargo->exInfo, $this->t(ImParcel::$exInfos), ['prompt'=>'All'])],
		['header'=>'is_3pl','type'=>'raw','value'=>'$data->shipment->is3PL()?"3PL":"Imports"','filter'=>CHtml::dropDownList('CargoProcess[is_3pl]', $cargo->is_3pl, [false=>'Imports',true=>'3PL'], ['prompt'=>'All'])] */
		array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"',),
		array('name' => 'ref', 'value' => '$data->getRef()'),
		//['name' => 'agent_id', 'value' => '$data->shipment->agent_id'],
		['name' => 'shipment.consol_no', 'type' => 'raw', 'value' => 'empty($data->shipment->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->shipment->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->shipment->consol_id))."\" class=\"tab_link\" title=\"".@$data->shipment->consol->no."\">".@$data->shipment->consol->no."</a>"', 'filter' => CHtml::textField('ImParcel[consol_no]', $cargo->consol_no)],
		//['name' => 'shipment.consol.awb', 'filter' => CHtml::hiddenField('CargoProcess[status]', @$cargo->status) . CHtml::textField('CargoProcess[awb]', @$cargo->awb)],
		['name' => 'shipment.cnee.address', 'filter' => CHtml::textField('CargoProcess[address]', @$cargo->address)],
		['name' => 'shipment.cnee.suburb', 'filter' => CHtml::textField('CargoProcess[suburb]', @$cargo->suburb)],
		['name' => 'shipment.cnee.postcode', 'filter' => CHtml::textField('CargoProcess[postcode]', @$cargo->postcode)],
		array('header' => 'region', 'type' => 'raw', 'filter' => CHtml::textField('CargoProcess[region]', @$cargo->region), 'value' => '$data->shipment->getRegion()'),
		array('header' => 'Combined Plt Region', 'type' => 'raw', 'filter' => CHtml::textField('CargoProcess[combined_plt_region]', $cargo->combined_plt_region), 'value' => '$data->shipment->getTLDRegion()'),
		//array('header' => 'distance', 'value' => '$data->shipment->getDeliveryDistance().\'km\''),
		array('header' => 'zone', 'value' => '$data->getZone()." ".$data->shipment->getDeliveryDistance().\'km\''),
		['name' => 'deliveryBookingTime', 'value' => '$data->getDeliveryBookingTime()','cssClassExpression' => '$data->getShowColor()','filter'=>CHtml::textField('CargoProcess[deliveryBookingTime]',@$cargo->getBookingTime())],
		['name' => 'shipment.can', 'filter' => CHtml::textField('CargoProcess[memo]', @$cargo->memo)],
		'note',
		//['header'=>'CustomerNote', 'value'=>'@$data->getCustomerResponse()'],
		//['header' => 'extra Info', 'type' => 'raw', 'value' => '$data->shipment->getDGWarnings().($data->shipment->isOversize()?"<label style=\"color:red\">Oversize</label>":"")', 'filter' => CHtml::dropDownList('CargoProcess[exInfo]', $cargo->exInfo, $this->t(ImParcel::$exInfos), ['prompt' => 'All'])],
		['header' => 'extra Info', 'type' => 'raw', 'value' => '$data->shipment->getDGWarnings().($data->shipment->isOversize()?"<label style=\"color:red\">Oversize</label>":"").@$data->isCustomerConfirmForklift()', 'filter' => CHtml::dropDownList('CargoProcess[exInfo]', $cargo->exInfo, $this->t(CargoProcess::$exInfos), ['prompt' => 'All'])],
		['name'=>'customerNote', 'value'=>'@$data->getCustomerResponse()', 'filter' => CHtml::dropDownList('CargoProcess[customerNote]', $cargo->customerNote, $this->t(CargoProcess::$addressTypes), ['prompt' => 'All'])],
		['name' => 'plt', 'type' => 'raw', 'value' => '$data->getPltStr()'],
		['name' => 'shipment.pkg', 'cssClassExpression' => '$data->getShowColor("pkg")', 'filter' => CHtml::textField('CargoProcess[pkg]', @$cargo->pkg), 'value' => '$data->getPackages()'],
		['name' => 'shipment.weight', 'filter' => CHtml::textField('CargoProcess[weight]', @$cargo->weight), 'value' => '$data->getWeight()'],
		['name' => 'shipment.cbm', 'value' => '$data->getTotalCBM()', 'filter' => CHtml::textField('CargoProcess[cbm]', @$cargo->cbm)],
		['header' => 'Unpacking Date', 'value' => '@$data->shipment->consol->mdata["ContainerUnloadDate"]', 'cssClassExpression' => '$data->getShowColor("unpacking")'],
		['header' => 'First Process Time', 'value' => '$data->getFirstProcessTime()', 'cssClassExpression' => '$data->getShowColor("last_process_time")'],
		//array('name' => 'assignedAgent', 'filter' => CHtml::dropDownList('CargoProcess[assignedUser]', @$cargo->assignedUser, @User::getTruckUsers(), array('prompt' => 'Select'))),
		['name' => 'status', 'type' => 'raw', 'value' => '$data->getFullStatus()', 'filter' => false],
		//['name' => 'shipment.status', 'value' => '$data->shipment->getStatus()', 'cssClassExpression' => '$data->getShowColor("held")'],
		['header' => 'goods available address', 'type' => 'raw', 'value' => '$data->shipment->getAvailabelLabel()', 'filter' => CHtml::textField('CargoProcess[goodsAvailableAddress]', @$cargo->goodsAvailableAddress)],
	],
]);


; ?>
<div class="row" style="font-size: 2em;font-weight: bold;">
	<div class="row buttons">
		<div class="col-block"><?php   echo CHtml::button('save cargo into job',array('class'=>'save_cargo_into_job','id'=>'save_cargo_into_job'));?></div>
	</div>
</div>

<script type="text/javascript">

			$(function(){
				var win = $('#jqmw_<?=$_GET["tabid"];?>');

				function uploading_on(obj) {
					obj.addClass('uploading');
					obj.val('    Uploading');
					obj.prop('disabled', 'disabled');
				}
	
				function uploading_off(obj) {
					obj.removeClass('uploading');
					obj.val('add into cargo job');
					obj.removeProp('disabled');
				}
				$('#add_job_id_all',win).on('click',function() {
					 var selectAll = $(this);
					 if(selectAll.prop("checked")==true)
					 {
					 	$("input[name='cargo_id']",win).prop("checked",true);
					 }else
					 {
					 	$("input[name='cargo_id']",win).prop("checked",false);
					 }
				});
				$('#save_cargo_into_job',win).on('click',function(e) {
					e.preventDefault();
					e.stopImmediatePropagation();
					uploading_on($('#save_cargo_job',win));
					var ids =new Array();
					var jobId  = '<?=$model->id?>';

					$("input[name='cargo_id']:checked").each(function() {  
		            	ids.push($(this).attr("value"));
		      		}); 
					if(ids.length==0)
					{
						myApp.alert('select cargos first',false);
						uploading_off($('#save_cargo_into_job',win));
						return false;
					}
					var formData = new FormData();
					let uniqueIds = [...new Set(ids)];
					formData.append('ids', uniqueIds);
					formData.append('jobId', jobId);
					$.ajax({
						url: '<?=Yii::app()->createUrl("topCourierService/saveCargoIntoJob")?>',
						type: 'POST',
						data: formData,
						processData: false,
						contentType: false,
						success: function(r) {
							uploading_off($('#save_cargo_into_job',win));
							r = JSON.parse(r);
							if (r.done) {
								$('#<?=$_GET["tabid"]?>_cargo_process_for_job_grid').yiiGridView('update');
								$('#<?=$_GET["tabid"]?>_cargo_process_for_into_job_grid').yiiGridView('update');
								myApp.notice(r.msg, 5000);
							} else {
								myApp.alert(r.msg, false);
							}
						},
						error: function(r) {
							uploading_off($('#save_cargo_into_job',win));
							myApp.alert('System error', false);
						}
					});
				});
			});
</script>

