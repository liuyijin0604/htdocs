<?php
/* @var $this ShipmentScanController */
/* @var $model ShipmentScan */
/* @var $form CActiveForm */
?>
<div style="width: 50%">
<?php


$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'mixed-eparcel-mild-report-grid'.$_GET['tabid'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],     //$model->search(),
    'filter'=>$dataProvider[1],
    'columns'=>array(
        array('name'=>'date','header'=>'Date'),
            array('name'=>'syd_cost','header'=>'Cost For Dispatched From Sydney'),
        array('name'=>'total_weight','header'=>'Total Weight'),
        array('name'=>'total_packs','header'=>'Total Packs'),
        array('name'=>'mixed_cost','header'=>'Mixed Cost'),
        array('name'=>'del_weight','header'=>'Delivery Weight'),
        array('name'=>'delivery_packs','header'=>'Delivery Packs'),
    ),
    )
    );
?>
  </div>