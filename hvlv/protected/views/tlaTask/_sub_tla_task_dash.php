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
	'id'=>$_GET["tabid"].'_sub_tla_task_dash_grid',
	'cssFile' => false,
	'dataProvider'=>$tlaTask->search(true, 200,false,true),
	'filter'=>$tlaTask,
	'columns'=>[
		array('header' => 'Processing','type'=>'raw', 'value' => '$data->getStatusForList()'),	
		array('name' => 'task_no'),
		array('name' => 'type','value'=>'$data->getInstance()->getType()','filter'=>CHtml::dropDownList('TlaTask[type]',@$tlaTask->type, TlaTask::$types,array('prompt'=>'Select'))),
		array('name' => 'task_content','type'=>'raw'),
		array('name' => 'comment','type'=>'raw','value'=>'$data->showComment()'),
		array('name' => 'dpt_id', 'value' => 'Org::dptList()[$data->dpt_id]',
				'filter'=>CHtml::dropDownList('TlaTask[dpt_id]', $tlaTask->dpt_id, Org::dptList(), ['prompt'=>$this->t('All')]),),
		array('name' => 'status','type'=>'raw', 'value' => '$data->getStatus()','filter'=>CHtml::dropDownList('TlaTask[status]',@$tlaTask->status, TlaTask::$processTypes,array('prompt'=>'Select'))),
		array('name' => 'createdUserName','value'=>'empty($data->user_id)?"System":$data->createUser->fname." ".$data->createUser->lname'),
		array('name' => 'assignedUserName','value'=>'$data->getAssignUsers()'),
		array('name' => 'create'),
		array('name' => 'ete'),
		// 'ref',
		// ['name'=>'agent_id','value'=>'$data->shipment->agent_id'],
		// ['name' => 'shipment.consol_no', 'type'=>'raw', 'value' => 'empty($data->shipment->consol_id)? "" : "<a href=\"".Yii::app()->createURL(Consol::getTheConsolType($data->shipment->consol_id)==70?"dmawbConsol/update":"imcoConsol/update", array("id" => $data->shipment->consol_id))."\" class=\"tab_link\" title=\"".@$data->shipment->consol->no."\">".@$data->shipment->consol->no."</a>"','filter'=>CHtml::textField('ImParcel[consol_no]', $cargo->consol_no)],
		// ['name' => 'shipment.consol.awb','filter'=>CHtml::textField('CargoProcess[awb]',@$cargo->awb)],
		// ['name'=>'shipment.cnee.postcode','filter'=>CHtml::textField('CargoProcess[postcode]',@$cargo->postcode)],
		// ['name'=>'shipment.can','filter'=>CHtml::textField('CargoProcess[memo]',@$cargo->memo)],
		// array('name'=>'assignedAgent','filter'=>CHtml::dropDownList('CargoProcess[assignedUser]',@$cargo->assignedUser, @User::getTruckUsers(),array('prompt'=>'Select'))),
		// ['name' => 'status','type'=>'raw', 'value' => '$data->getFullStatus()','filter'=>false],
		// ['name'=>'shipment.status','value' => '$data->shipment->getStatus()','cssClassExpression' => '$data->getShowColor("held")'],
		
		// ['name'=>'note','value'=>'$data->note'],
		// ['header'=>'First Process Time','value' => '$data->getFirstProcessTime()','cssClassExpression' => '$data->getShowColor("last_process_time")'],
		// ['header'=>'Last Process Time','value' => '$data->getLastProcessTime()','cssClassExpression' => '$data->getShowColor("last_process_time")'],
		// ['header'=>'goods available address', 'type' => 'raw','value'=>'$data->shipment->getAvailabelLabel()','filter'=>CHtml::textField('CargoProcess[goodsAvailableAddress]',@$cargo->goodsAvailableAddress)],
		// ['name'=>'shipment.pkg','cssClassExpression' => '$data->getShowColor("pkg")','filter'=>CHtml::textField('CargoProcess[pkg]',@$cargo->pkg),'value'=>'$data->getPackages()'],
		// ['name'=>'plt','type'=>'raw','value'=>'$data->getPltStr()'],
		// ['name'=>'shipment.weight','filter'=>CHtml::textField('CargoProcess[weight]',@$cargo->weight),'value'=>'$data->getWeight()'],
		// ['name' =>'shipment.cbm','value'=>'$data->getTotalCBM()','filter'=>CHtml::textField('CargoProcess[cbm]',@$cargo->cbm)],
		// ['name'=>'shipment.cnee.name','filter'=>CHtml::textField('CargoProcess[cname]',@$cargo->cname)],
		// ['name'=>'shipment.cnee.address','filter'=>CHtml::textField('CargoProcess[address]',@$cargo->address)],
		// ['name'=>'shipment.cnee.state','filter'=>CHtml::textField('CargoProcess[state]',@$cargo->state)],
		// ['header'=>'extra Info','type'=>'raw','value'=>'$data->shipment->getDGWarnings()','filter'=>CHtml::dropDownList('CargoProcess[exInfo]', $cargo->exInfo, $this->t(ImParcel::$exInfos), ['prompt'=>'All'])],
		['class'=>'oButtonColumn',
			'template'=>'{operate}&nbsp;{Handling Dispute}&nbsp;{Check Si Reconcile}&nbsp;{log}',
			'buttons'=>[
				'operate' => [
					'url'=>'Yii::app()->createURL("tlaTask/operation")."?id=".$data->id',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id'],
				],
				'Handling Dispute' => [
					'url'=>'$data->getInstance()->getOperationLink()',
					'imageUrl'=>false,
					'visible'=>'$data->type==DisputeTask::$my_type',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>'$data->getInstance()->getGoToLinkText()', 'title' => '$data->id' , 'data-id' => '$data->id'],
				],
				'Check Si Reconcile' => [
					'url'=>'$data->getInstance()->getOperationLink()',
					'imageUrl'=>false,
					'visible'=>'$data->type==DisputeSupplierTask::$my_type',
					'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>'$data->getInstance()->getGoToLinkText()', 'title' => '$data->id' , 'data-id' => '$data->id'],
				],
				'log' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("tlaTask/log", ["id" => $data->id])',
					'label' => 'Log'
				],
			],
		]
	],
]);

?>