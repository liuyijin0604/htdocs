<?php
/* @var $this ShipmentScanController */
/* @var $model ShipmentScan */
/* @var $form CActiveForm */
?>
<div style="width: 70%">
    <br/>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'imports_mail_report_grid_view'.$_GET['tabid'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],     //$model->search(),
    'filter'=>$dataProvider[1],
    'columns'=>array(
        ['name'=>'date','header'=>'Date'],
        ['name'=>'user_name','header'=>'User Name'],
        ['name'=>'unfinished','header'=>'Unfinished'],
        ['name'=>'open','header'=>'Open'],
        ['name'=>'read','header'=>'Read'],
        ['name'=>'reply','header'=>'Reply'],
        ['name'=>'close','header'=>'Close'],
        ['name'=>'percent','header'=>'Finish Percent','value'=>'$data["percent"]."%"'],
       
         
    ),
    )
    );
?>
  </div>