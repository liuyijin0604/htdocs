<?php $this->widget('zii.widgets.grid.CGridView', [
	'id'=>$_GET["tabid"].'recommendation_list'.$recommendation->dpt_id,
	'cssFile' => false,
	'dataProvider'=>$recommendation->search(true, 30),
	'filter'=>$recommendation,
	'columns'=>[
		['name' => 'ground_label'],
		['name' => 'level'],
		['name' => 'is_held'],
		['name' => 'is_oversize'],
		['name' => 'columns'],
		['name' => 'status']
	],
]); ?>
