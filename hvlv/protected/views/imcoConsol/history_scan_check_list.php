<?php 
$this->widget('zii.widgets.grid.CGridView', [
	'id'=>'history_scan_check_list'.$_GET['tabid'],
	'cssFile' => false,
	'dataProvider'=>$model->search(true,50),
	'filter'=>$model,
	'columns'=>[
		['name'=>'user_id','value'=>'$data->user->name'],
		'created',
		['class'=>'oButtonColumn',
			'template'=>'{check}',
			'buttons'=>[
				'check' => [
					'url'=>' Yii::app()->createURL("imcoConsol/getScanCheck")."?id=".$data->id',
					'imageUrl'=>false,
					'visible'=>'true',
					'options' => ['class' => 'tab_link grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '"ScanCheck".$data->id'],
				]
			],
		]
	],
]);

?>