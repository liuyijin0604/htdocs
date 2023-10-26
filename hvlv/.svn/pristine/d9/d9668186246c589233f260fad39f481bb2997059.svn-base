<?php
/* @var $this ShipmentScanController */
/* @var $model ShipmentScan */
/* @var $form CActiveForm */
?>
<div style="width: 70%">
    <br/>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'crm_report_grid_ex'.$_GET['tabid'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],     //$model->search(),
    'filter'=>$dataProvider[1],
    'columns'=>array(
        ['name'=>'name','header'=>'Name'],
         ['name'=>'open'],
        'close',
         
    ),
    )
    );
?>
  </div>