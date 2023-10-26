<?php
/* @var $this ShipmentScanController */
/* @var $model ShipmentScan */
/* @var $form CActiveForm */
?>
<div style="width: 50%">
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'import-tell-me-cost-grid'.$_GET['tabid'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],     //$model->search(),
    'filter'=>$dataProvider[1],
    'columns'=>array(
        array('name'=>'courier','header'=>'Courier','filter'=>false),
        array('name'=>'cost','filter'=>false),
        array('name'=>'weight','filter'=>false),
        array('name'=>'packs','filter'=>false),
        array('name'=>'left_packs','filter'=>false),

    ),));
?>
  </div>

<div style="width: 100%">
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'import-tell-me-cost-detail-grid'.$_GET['tabid'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider1[0],     //$model->search(),
    'filter'=>$dataProvider1[1],
    'columns'=>array(
        array('name'=>'courier','header'=>'Courier','filter'=>false),
        array('name'=>'cost','filter'=>false),
        array('name'=>'preCost','filter'=>false),
        array('name'=>'weight','filter'=>false),
        array('name'=>'packs','filter'=>false),
        array('name'=>'left_packs','filter'=>false),
        array('name'=>'weightPercent','filter'=>false),
        array('name'=>'packsPercent','filter'=>false),
    ),));
?>
  </div>