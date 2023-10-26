<?php
/* @var $this ShipmentScanController */
/* @var $model ShipmentScan */
/* @var $form CActiveForm */
?>
<div style="width: 50%">
<?php
$columns=[];
$columns[]= array('name'=>'user','header'=>'User');
foreach (ExParcel::$errTypes as $key=>$value){
    $columns[]=['name'=>'number'.$key,'header'=>$value];
}

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'im-scan-report-grid'.$_GET['tabid'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],     //$model->search(),
    'filter'=>$dataProvider[1],
    'columns'=>$columns,
    )
    );
?>
  </div>