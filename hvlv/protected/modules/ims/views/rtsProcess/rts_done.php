<?php
$str = ["rtsresend"=>"Waiting TLA Resend","rtsdone"=>"Done"];
echo '<h1>Shipment RTS '.$str[$op].' List</h1>';
?>


<?php $this->widget('zii.widgets.grid.CGridView',array(
    'cssFile' => false,
    'id'=>'rts_done_grid_view',
    'filter'=>$model,
    'dataProvider'=>$model->search(true,50),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
            array('name' => 'hbn','value'=>'$data->originalShipment->hbn'),
            array('name' => 'ref','value'=>'$data->originalShipment->ref'),
            array('name' => 'barcode','type'=>'raw','value'=>'$data->getRTSBarcodes(true)'),
            array('name' => 'newRef','type'=>'raw','value'=>'"<font style=\"background-color:red;\">".$data->newShipment->ref."</font>"'),
            ['header' => 'status', 'value' => '$data->getStatus()'],
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