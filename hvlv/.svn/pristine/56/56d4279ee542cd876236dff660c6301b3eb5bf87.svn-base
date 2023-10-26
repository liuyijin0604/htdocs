<?php

$p = new ImParcel('search');
$p->unsetAttributes();
$p->consol_id = $model->id;

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>$_GET["tabid"].'_cost-validation-details-grid',
    'cssFile' => false,
    'summaryText'=>'',
    'dataProvider'=> $p->search(),
    'columns'=>array(
        'hbn',
        'ref',
        'pkg',
        'weight',
        'cbm',
        array(
            'header' => 'Cost',
            'type' => 'raw',
            'value' => '$data->getTranshipCost()'
        ),
        array(
            'header' => 'Invoice',
            'type' => 'raw',
            'value' => '$data->getShipmentInvoice()'
        )
    ),
));
?>