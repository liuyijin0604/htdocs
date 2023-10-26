<?php
$recs = $model->getRecIds();

if(empty($recs)):
$dp = new Invoice('search');
$dp->consol_id = $model->id;
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'ordereport-grid',
	'cssFile' => false,
	'dataProvider' => $dp->search(),
	'filter' => null,
	'enableSorting' => false,
	'columns'=>array(
		array('name' => 'no', 'value' => '$data->id', ),
		array('name' => 'bill_to', 'value' => '$data->cust->name', ),
		array('header' => 'Packs', 'value' => '$data->consol->totPacks()', ),
		array('header' => 'Total Weight', 'value' => '$data->consol->totWeight()', ),
		array('header' => 'Total CBM', 'value' => '$data->consol->totCBM()', ),
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
else:
$m = new Manifest('search');
$dp = $model->billingList();
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'ordereport-grid',
	'cssFile' => false,
	'dataProvider' => $dp,
	'filter' => null,
	'enableSorting' => false,
	'columns'=>array(
		'id',
		array('header' => 'File', 'type' => 'raw', 'value' => '"<a href=\"".$data->getFileLink()."\" target=\"_blank\">".$data->getFileName()."</a>"',),
		array('name' => 'fwd_id', 'value' => '$data->owner->name', 'footer' => 'Total: ', 'footerHtmlOptions' => array('align' => 'right')),
		array('header' => 'Packs', 'value' => '$data->totPacks()', 'footer' => $m->getTotal($dp->getData(), 'packs')),
		array('header' => 'Total Weight', 'value' => '$data->totWeight()', 'footer'=> $m->getTotal($dp->getData(), 'weight')),
		array('header' => 'Total CBM', 'value' => '$data->totCBM()', 'footer'=> $m->getTotal($dp->getData(), 'cbm')),
		array('header' => 'Invoice Total', 'value' => '$data->getCurrency().$data->totCharge()', 'footer'=> $m->getTotal($dp->getData(), 'charge')),
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}',
			'buttons'=>array
			(
				'view' => array(
					'url' => 'Yii::app()->createURL("invoice/print", array("id" => $data->invoice->id))',
					'imageUrl'=>false,
					'options' => array('class' => 'grid_view_btn', 'target' => '_blank'),
					'visible' => '!empty($data->invoice->id)'
				),
			),
		),
)));
endif;
?>
<script type="text/javascript">
$(function(){
	var pane = $('#<?=$_GET["tabid"];?>');
});
</script>