<?php 
  $wmsStock=new WmsStock('search'); 
  $wmsStock->unsetAttributes();
 if(!empty($_GET['WmsStock'])) {
  $wmsStock->setAttributes($_GET['WmsStock']);  
 }
    $wmsStock->org_id=$model->id;
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'wms-org-stock-grid',
	'cssFile' => false,
	'dataProvider'=>$wmsStock->search(),
	'filter'=>$wmsStock,
	'columns'=>array(
		array('name' => 'prod_name', 'value' => 'empty($data->prod)? "" : $data->prod->name'),
		array('name' => 'prod_ean', 'type' => 'raw', 'value' => 'empty($data->prod)? "" : "<a href=\"".Yii::app()->createUrl("wmsProd/update", ["id" => $data->prod_id])."\" class=\"tab_link\" title=\"Product ".$data->prod->name."\">".$data->prod->ean."</a>"'),
		'qty',
		'qty_res',
		'expiry',
		'batch',
		array('name' => 'dpt_id', 'value' => '$data->getBranch()', 'filter'=>CHtml::dropDownList('WmsStock[dpt_id]', $wmsStock->dpt_id, Org::dptList3PL(), ['prompt'=>$this->t('All'),'class' => 'form-control']),),
		'updated',
		array(
			'class'=>'oButtonColumn',
			'template'=>'{view}',
			'buttons'=>array
			(
				'view' => array(
					'imageUrl'=>false,
                                        'url'=>'Yii::app()->createUrl("wmsStock/view", ["id" => $data->id])',
					'options' => array('class' => 'jqm_link grid_view_btn', 'data-win-class' => 'L'),
				),
			),
		),
	),
)); ?>