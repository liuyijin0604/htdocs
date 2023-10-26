<?php
$this->widget('application.extensions.booster.TbExtendedGridView', array(
		'id'=>'szPortal-bag-list-grid',
		'type'=>'striped bordered',
		'headerOffset'=>40,
	    'responsiveTable'=>true,
		'dataProvider'=>$model->search(true, 30),
		'filter'=>$model,
		'template' => "{summary}\n{items}\n{pager}",
		'columns'=>array(
			array('name' => 'no','type' => 'raw'),
			[	
				'class'=>'oButtonColumn',
				'template'=>'{print_bag_label}',
				'buttons'=>[
					'print_bag_label' => [
						'url'=>' Yii::app()->createURL("/szportal/shipment/printBagLabel")."?id=".$data->id',
						'imageUrl'=>false,
						'visible'=>'true',
						'options' => ['class' => 'grid_edit_btn', 'label'=>$this->t('Operation'), 'title' => '$data->id','target'=>'blank'],
					],
			],
		]
		),
	)); 
?>