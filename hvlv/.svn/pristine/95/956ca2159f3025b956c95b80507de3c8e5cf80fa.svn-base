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
        array('name'=>'daily_d2z_packs','header'=>'Daily Delivery Packs(D2Z)'),
        array('name'=>'daily_d2z_weight','header'=>'Daily Delivery Weight(D2Z)'),
        array('name'=>'daily_pca_packs','header'=>'Daily Delivery Packs(PCA)'),
        array('name'=>'daily_pca_weight','header'=>'Daily Delivery Weight(PCA)'),
        array('name'=>'daily_tot_packs','header'=>'Daily Total Packs'),
        array('name'=>'daily_tot_weight','header'=>'Daily Total Weight'),
        array('name'=>'daily_po_pca_packs','header'=>'Daily Possible PCA Packs'),
        array('name'=>'daily_po_pca_weight','header'=>'Daily Possible PCA Weight'),
    ),
    )
    );
?>
  </div>