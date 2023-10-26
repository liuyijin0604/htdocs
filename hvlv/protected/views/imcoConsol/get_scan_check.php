<?php
$this->widget('zii.widgets.grid.CGridView', [
	'id'=>'get_scan_check'.$_GET['tabid'],
	'cssFile' => false,
	'dataProvider'=>$dataProvider,
	'filter'=>$filter,
	'columns'=>[
		'hbn',
		'ref',
		array('header'=>'Agent','name'=>'agent_name','value'=>'$data->agent->name'),
		array('name' => 'status', 'value' => '$data->getStatus()'),
		'weight',
		'pkg',
		array('name' => 'Scanned','header'=>'Check-In Scan','value'=>'$data->getOutPkg()'),
		array('name' => 'Scan Check','header'=>'Held Check Scan','type'=>'raw','value' => 'Chtml::numberField($data->id,$data->scanCheck,["style"=>"width:60px;"])', ),
		array('name' => 'Diff','header'=>'Diff','type'=>'raw','value' => 'Chtml::numberField($data->id,($data->getOutPkg()-$data->scanCheck),["style"=>"width:60px;"])', ),	

		['class'=>'oButtonColumn',
			'template'=>'{log}',
			'buttons'=>[
				'log' => [
					'imageUrl'=>false,
					'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
					'visible' => 'true',
					'url' => 'Yii::app()->createUrl("imcoConsol/getScanLog", ["sid" => $data->id])."&&id="."'.$model->id.'"',
					'label' => 'Log'
				],
			],
		]
	],
]);
?>