
<?php
$str = ["rtsresend"=>"Waiting Resend","rtsdone"=>"Done"];
echo '<h1>Shipment RTS '.$str[$op].' List</h1>';
?>


<?php $this->widget('zii.widgets.grid.CGridView',array(
    'cssFile' => false,
    'id'=>$_GET["tabid"].'rts_waiting_resend_grid_view',
    'filter'=>$model,
    'dataProvider'=>$model->search(true,50),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
             array('name' => 'hbn','value'=>'$data->originalShipment->hbn'),
            array('name' => 'ref','value'=>'$data->originalShipment->ref'),
            array('name' => 'barcode','type'=>'raw','value'=>'$data->getRTSBarcodes(true)'),
            array('header' => 'New Ref','value'=>'$data->newShipment->ref'),
            array('header' => 'New Ref Status','value'=>'$data->newShipment->getStatus()'),
            array('header' => 'Warehouse','type'=>'raw','value'=>'@$data->records[0]->warehouse->name','filter'=>CHtml::dropDownList(get_class($model).'[warehouse_id]', $model->warehouse_id, $this->t(Org::dptList()), ['prompt'=>$this->t('All')])),
            ['header' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('ShipmentRtsRecordConfirm[status]', $model->status, $this->t(ShipmentRtsRecordConfirm::$whscan_states)),],
            ['name' => 'create_time','header'=>'RTS Submit Time'],
            array('header' => 'Check Invoice','type'=>'raw','value'=>'"<a class=\"grid_edit_btn\" target=\"_blank\" href=\"".Yii::app()->createURL("rtsProcess/rtsInvoice")."?id=".$data->id."\">Check RTS Invoice ".$data->getInvoice()->no."</a>"'),

        ),
    )
    
);
?>

<script type="text/javascript">
$(function() {
    

});
</script>