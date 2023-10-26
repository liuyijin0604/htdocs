<?php
$str = ["rtswaitingdiscard"=>"Waiting Discard","rtsdiscarded"=>"Discarded"];
echo '<h1>Shipment RTS '.$str[$op].' </h1>';
?>

<?php $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>$_GET["tabid"].'_rts_discard_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(true,50),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
            array('name' => 'barcode','type'=>'raw','value'=>'$data->getBarcode()','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),
            array('name' => 'location_id','value'=>'@$data->location->code','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),
            
            array('name' => 'warehouse_id','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")', 'value' => '@$data->warehouse->name',
            'filter'=>CHtml::dropDownList(get_class($model).'[warehouse_id]', $model->warehouse_id, $this->t(Org::dptList()), ['prompt'=>$this->t('All')])),

            array('name' => 'record_time','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),
            array('name' => 'discard_user_id'),
            array('name' => 'discard_time'),

            array('header' => 'Image Url','type'=>'raw','value'=>'$data->getUrl()','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")')
        ),
    )
    
);
?>

<script type="text/javascript">
$(function() {


});
</script>