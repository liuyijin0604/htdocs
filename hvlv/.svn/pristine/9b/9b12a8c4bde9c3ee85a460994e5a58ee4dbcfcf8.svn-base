<?php
/* @var $this ShipmentScanController */
/* @var $model ShipmentScan */
/* @var $form CActiveForm */
?>
<div style="width: 70%">
    <br/>
    <p>Daily Total Shipment:<?=$totalShipment?></p>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'im-scan-report-grid'.$_GET['tabid'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],     //$model->search(),
    'filter'=>$dataProvider[1],
    'columns'=>array(
        ['name'=>'user_name','header'=>'User Name'],
        ['name'=>'date','header'=>'Date'],
        ['name'=>'type','header'=>'Type'],
        ['name'=>'dailyExist','header'=>'Held Number'],
        ['name'=>'existPercent','header'=>'Held Percent','value'=>'$data["existPercent"]."%"'],
        ['name'=>'daily_close', 'header'=>' Selected Period Daily Close'],
        ['name'=>'best_month_avg_close', 'header'=>'Best Month Daily Close'],
        ['name'=>'close_percent','header'=>'Percent','value'=>'$data["close_percent"]."%"'],
    ),
    )
    );
?>
  </div>