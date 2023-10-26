<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link('Home', array('site/index')),
	'links' => array(
		'Gatepass List',
	),
));
?>
<?php $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'gatepass_shipment_signature',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
        'no',
        array('name'=>'courier_id','value'=>'$data->getCourierName()',
            'filter'=>CHtml::dropDownList("GatepassCourier[courier_id]",$model->courier_id, GatepassCourier::$gatepass_courier,array('prompt'=>'All','class'=>'form-control'))
            ),
        'driver_name',
        'driver_rego',
        'ref',
        'create_time',
        array('header'=>'Total Packs','value'=>'$data->getTotalPacks()'),
        array(
            'class'=>'application.extensions.booster.TbButtonColumn',
            'template'=>'{View} &nbsp',
            'buttons'=>array(
                'View' => array(
	                  'visible'=>'true',
		           'icon' => 'glyphicon glyphicon-edit',
                           'url' => 'Yii::app()->createUrl("gapsig/gatePass/genGatepassPdf",array("id"=>$data->id))',
		           'options' => array('label'=>$this->t('View'),'target'=>'_blank', 'title' => 'View'),
		            ),
            ),
        )
    ),
));

?> 
<input type="hidden"  id="shipment_number" value="<?=sizeof($model->search()->data)?>">
    
