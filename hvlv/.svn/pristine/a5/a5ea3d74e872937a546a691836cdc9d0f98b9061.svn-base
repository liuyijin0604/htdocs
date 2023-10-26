<?php
echo '<h1>Shipment RTS Done List</h1>';
?>


<?php $this->widget('zii.widgets.grid.CGridView',array(
    'cssFile' => false,
    'id'=>'rts_done_grid_view',
    'filter'=>$model,
    'dataProvider'=>$model->search(true,50),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
            array('header' => 'Original HBN','value'=>'$data->originalShipment->hbn'),
            array('header' => 'Original Ref','value'=>'$data->originalShipment->ref'),
            array('header' => 'RTS Barcodes','type'=>'raw','value'=>'$data->getRTSBarcodes(true)'),
            ['header' => 'status', 'value' => '$data->getStatus()'],
            ['name' => 'create_time','header'=>'Discard Time']

        ),
    )
    
);
?>

<script type="text/javascript">
$(function() {
    

});
</script>