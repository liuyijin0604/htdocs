<?php
/* @var $this ShipmentScanController */
/* @var $model ShipmentScan */
/* @var $form CActiveForm */
?>

<div style="width: 100%">
<!-- <?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'import-tell-me-cost-zone-grid'.$_GET['tabid'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],     //$model->search(),
    'filter'=>$dataProvider[1],
    'columns'=>$columns,
));
?> -->
<table style="border: 1px solid #999999;">
<?php
     echo "<tr>";
    foreach ($columns as $key => $value) {
       echo "<td style='border: 1px solid #999999;'>".$value["name"]."</td>";
    }
     echo "</tr>";
    foreach ($provide as $key => $value) {
        echo "<tr>";
         foreach ($columns as $key2 => $value2) {
               echo "<td style='border: 1px solid #999999;'>". $value[$value2["name"]]."</td>";
        }
        echo "</tr>";
    }
?>
</table>
  </div>