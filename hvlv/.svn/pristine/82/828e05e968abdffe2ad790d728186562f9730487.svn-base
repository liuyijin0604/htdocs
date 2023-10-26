<?php
/* @var $this ShipmentScanController */
/* @var $model ShipmentScan */
/* @var $form CActiveForm */
?>
<div style="width: 50%">
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'im-scan-report-grid',
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],     //$model->search(),
    'filter'=>$dataProvider[1],
    'columns'=>array(
   array('name'=>'name','header'=>'Org '),
        array('name'=>'no','header'=>'No of Shipments'),
        array('name'=>'my_value','header'=>'Customer Est cost'),
        array('name'=>'crcharge','header'=>'Couier Charge'),
        array('name'=>'mycharge','header'=>'PCA Inv Charge'),
        array('name'=>'cwt','header'=>'Couier Dead weight'),
        array('name'=>'wt','header'=>'Customer Dead weight'),
        array('name'=>'crwt','header'=>'Courier Cubic weight'),
        array('name'=>'cgwt','header'=>'Customer Cubic weight'),
        array('name'=>'cbc_rate','header'=>'weight Gap'),
        array('name'=>'profit','header'=>'PCA GP'),
        array(
        'class'=>'oButtonColumn',
        'template'=>'{download}',
        'buttons'=>array(
            'download'=>array(
                'url'=>'Yii::app()->createUrl("report/exportGap",array_merge($_GET,array("org_id"=>$data["id"])))',
                'options' => array('class' => 'grid_view_btn','target'=>'_blank'),
            )
         )
      )
        ),
   
    )
    );
?>
  </div>