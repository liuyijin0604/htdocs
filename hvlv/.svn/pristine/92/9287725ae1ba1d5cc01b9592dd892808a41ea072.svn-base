<h2>Invoice</h2>
<?php

$dp = new Invoice('search');
$dp->unsetAttributes();
$dp->to_id = $model->id;

$criteria=new CDbCriteria;
$criteria->compare('t.type', [60, 70]);

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'wms-org-invoice-grid',
	'cssFile' => false,
	'dataProvider' => $dp->search(true,30,"date DESC",$criteria),
	'filter' => null,
	'enableSorting' => false,
	'columns'=>array(
		array('name' => 'no', 'value' => '$data->no', ),
		array('name' => 'bill_to', 'value' => '$data->cust->name', ),
    array('name' => 'type', 'value' => 'Invoice::$types[$data->type]'),
                array('header' => 'from date','value'=>'empty($data->mdata["billfrom"])?"":$data->mdata["billfrom"]' ),
                array('header' => 'to date','value'=>'empty($data->mdata["billto"])?"":$data->mdata["billto"]' ),
		array('name' => 'date' ),
		array('header' => 'Invoice Total', 'value' => '$data->getCurrency().$data->total', ),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view} {detail}',
			'buttons'=>array
			(
				'view' => array(
					'url' => 'Yii::app()->createURL("invoice/print", array("id" => $data->id))',
					'imageUrl'=>false,
					'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
				),
				'detail' => array(
					'url' => 'Yii::app()->createURL("invoice/detail", array("id" => $data->id))',
					'label' => 'Detail',
					'imageUrl'=>false,
					'options' => array('class' => 'grid_file_btn', 'target' => '_blank'),
				),
			),
		),
)));