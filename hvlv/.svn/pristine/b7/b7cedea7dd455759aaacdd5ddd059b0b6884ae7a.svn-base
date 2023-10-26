
<h1><?=$this->t('Delivery Record/Booking');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>$_GET["tabid"].'delivery-record-grid',
	'cssFile' => false,
	'dataProvider'=>$model->search(true,30,'DESC',true),
	'filter'=>$model,
	'columns'=>array(
		array('name' => 'rego'), 
		array('name' => 'status','value'=>'$data->getStatus()','filter'=>false), 
		array('name' => 'booking_time'),
		array('name' => 'plt'), 
		array('name' => 'pkg'), 
		array('name' => 'name'), 
		array('header'=>'Company Name','filter'=>false,'type'=>'raw','value'=>'empty(@$data->mdata["company_name"])?"&nbsp;":@$data->mdata["company_name"]'),
		array('header'=>'Booking Type','type'=>'raw','filter'=>false,'value'=>'@$data->getBookingType();'),
		array('name' => 'mobile'), 
		array('name' => 'note'), 
		array('name' => 'damage','value'=>'$data->getDamage()'), 
		array('name' => 'op_id','value'=>'@$data->op->fname'), 
		array('name' => 'created'), 

		array(
			'class'=>'oButtonColumn',
			'template'=>'{update}{operation}',
			'buttons'=>array(
				'update' => array(
					'imageUrl'=>false,
                    'visible'=> 'true',
					'url'=>'Yii::app()->createUrl("deliveryRecord/update", ["id" => $data->id])',
					'options' => array('class' => 'jqm_link grid_edit_btn'),
				),
				'operation' => array(
					'imageUrl'=>false,
                    'visible'=> 'true',
					'url'=>'Yii::app()->createUrl("deliveryRecord/operation", ["id" => $data->id])',
					'options' => array('class' => 'jqm_link grid_edit_btn'),
				)
			),
		),
	),
)); ?>
