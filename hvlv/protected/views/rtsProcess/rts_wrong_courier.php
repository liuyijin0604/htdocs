<?php
echo '<h1>Shipment RTS Wrong Courier </h1>';
?>
<div style="right: 20px;position: absolute;">
<a class="export_search" id = "wrong_courier_export_search" target="_blank" href=""><div style="background-position:-48px -688px" class="icon"></div> Export Current Search</a>
</div> 
<?php $this->widget('zii.widgets.grid.CGridView',array(
    'id'=>$_GET["tabid"].'_rts_wrong_courier_grid_view',
    'filter'=>$model,
    'dataProvider'=>$model->search(true,50),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
            array('name' => 'barcode','type'=>'raw','value'=>'$data->getBarcode()'),
            array('name' => 'location_id','value'=>'@$data->location->code'),
            
            array('name' => 'warehouse_id',
            'filter'=>CHtml::dropDownList(get_class($model).'[warehouse_id]', $model->warehouse_id, $this->t(Org::dptList()), ['prompt'=>$this->t('All')])),

            array('name' => 'record_time'),
            array('name' => 'reason','header' => 'Wrong Courier','value'=>'$data->reason'),
            array('header' => 'Courier','value'=>'$data->getShipmentCourier()'),
            array('header' => 'Checkin User','value'=>'$data->getScanUser()'),
            array('header' => 'Checkin Time','value'=>'$data->getScanTime()'),
            array('header' => 'Image Url','type'=>'raw','value'=>'$data->getUrl()')
        ),
    )
    
);
?>

<script type="text/javascript">
$(function() {
    var panel = $('#<?=$_GET["tabid"]?>_rts_wrong_courier_grid_view');
    $('#wrong_courier_export_search').on('mousedown', function(){
        var q = $('.filters input, .filters select', panel).serialize();
        $(this).attr('href', '<?=$this->createUrl('rtsProcess/exportWrongCourier');?>?iii=1' + '&' + q);
    });

});
</script>