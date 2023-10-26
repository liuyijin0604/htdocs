<h3></h3>
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
	$list = AppHelper::setting2List('pods'); ksort($list);
	$pod = [];
	$cpod = empty($consol->pod)?[]:$consol->pod;
	foreach ($list as $key => $value) {
		if(in_array($value,$cpod))
		{
			$pod[] = $key;
		}
	}

	$this->widget('application.extensions.CSpanableGridView.CSpanableGridView', [
	'id'=>$_GET["tabid"].'_consol_process_grid',
	'cssFile' => false,
	'dataProvider'=>$consol->search(true, 30,false,false,false,true),
	'filter'=>$consol,
	'columns'=>[
		['class'=>'application.extensions.CSpanableGridView.oSpanableButtonColumn',
			'template'=>'{operate}',
			'buttons'=>[
				'operate' => [
					'url'=>'Yii::app()->createURL("consolProcess/seaOperation")."?id=".$this->ids',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operate'), 'title' => '$data->id'],
					'label' => 'Bulk Operate'
				],
			],
			'spanable' => true, 'spanDepands' => ['$data->eta','$data->airline','$data->flight','$data->process->status','$data->pod']
		],
		['header'=>'Documentation Status','type'=>'raw','value'=>'$data->process->getDocumentationStatus()','filter'=>CHtml::dropDownList('Consol[sea_process_status]', $consol->sea_process_status, $this->t(ConsolProcess::$seaStates), ['prompt'=>$this->t('All')]),],
		['name' => 'no', 'type' => 'raw','cssClassExpression' => '$data->type==70?"column_direct" : ""',
			'value' => '"<a href=\"".Yii::app()->createURL(($data->type==15||$data->type==90)?"imcoConsol/update":"dmawbConsol/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->no."\">".$data->no."</a>"',],
		['name' => 'container_no', 'type' => 'raw', 'value' => '$data->container_no'],
		['header'=>'Customer','name' => 'owner_name','value' => '$data->getOwnerName()'],
		['name' => 'status', 'value' => '$data->getStatus()',
			'filter'=>CHtml::dropDownList('ImcoConsol[status]', $consol->status, $this->t($consol::$states), ['prompt'=>$this->t('All')]),],
		['name'=>'process_status','type'=>'raw','value'=>'$data->getSeaProcessStatus()','filter'=>CHtml::dropDownList('ImcoConsol[process_status]', $consol->process_status, $this->t(ConsolProcess::$newSeaStates), ['prompt'=>$this->t('All')]),],
		// ['name' => 'dpt_id', 'value' => '$data->depot->name',
		// 	'filter'=>CHtml::dropDownList('ImcoConsol[dpt_id]', $consol->dpt_id, $this->t(Org::dptList()), ['prompt'=>$this->t('All')]),],
		'pol',
		['name'=>'pod',
			'spanable' => true, 'spanDepands' => ['$data->eta','$data->airline','$data->flight','$data->process->status','$data->pod'],
			'filter'=>"<div style='width:200px;'>".CHtml::checkBoxList('Consol[pod]',$pod,$this->t($list),array(
									'template'=>'{input}{label}',
									'separator'=>'',
									'labelOptions'=>array(
										'style'=> 'padding-right:1px;float:left;width:40px;'),
										'style'=>'float:left;width:20px;'))."</div>"],

		['name'=>'airline','header'=>'Vessel',
			'spanable' => true, 'spanDepands' => ['$data->eta','$data->airline','$data->flight','$data->process->status']],
		['name'=>'flight','header'=>'Voyage',
			'spanable' => true, 'spanDepands' => ['$data->eta','$data->airline','$data->flight','$data->process->status']],
		['name'=>'eta','cssClassExpression' => '$data->getEtaToNow()>=3? "cloumn_red" : ""',
			'spanable' => true, 'spanDepands' => ['$data->eta','$data->airline','$data->flight','$data->process->status']],
		['header' => 'Cargo Type', 'value' => '@$data->mdata["cargo_type"]','filter'=>CHtml::dropDownList('ImcoConsol[cargoType]', @$consol->cargoType, $this->t(['LCL'=>'LCL','FCL'=>'FCL']), ['prompt'=>$this->t('All')])],
		['header' => 'Notes', 'value' => '@$data->process->mdata["process_note"]'],
		['header' => 'CFS Address', 'value' => '@$data->mdata["cfs_address"]'],
		['header' => 'Shipments', 'value' => '$data->totShipments()'],
		['header' => 'Weight', 'value' => '$data->totWeight()'],
		['header' => 'B/L pcs', 'value' => '$data->getBLpcs()'],
		['header' => 'Chargeable Weight', 'value' => '@$data->mdata["cgb_wt"]'],
		['header' => 'UnScanPacks', 'value' => '$data->totLeftPacks()'],
		['header'=>'extra Info','type'=>'raw','value'=>'$data->getConsolDGWarnings()'],
		['header'=>'AOUT','value'=>'$data->getAOutStatus()','filter'=>CHtml::dropDownList('Consol[aout]', $consol->aout, [1=>"no outturn",2=>"Y",3=>"Y[D]",4=>"[R]",5=>"C"], ['prompt'=>'All'])],
		['class'=>'oButtonColumn',
			'template'=>'{operate}&nbsp;{log}',
			'buttons'=>[
				'operate' => ['url'=>'Yii::app()->createURL("consolProcess/seaOperation",array("id"=>$data->id))',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->no'],
				],
				'log' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("consolProcess/log", ["id" => $data->id])',
					'label' => 'Log'
				],
			],
		],
		['header'=>'All Documentation Status','type'=>'raw','value'=>'$data->process->getAllDocumentationStatus()','filter'=>CHtml::dropDownList('Consol[sea_process_status]', $consol->sea_process_status, $this->t(ConsolProcess::$seaStates), ['prompt'=>$this->t('All')]),]
	],
]); ?>