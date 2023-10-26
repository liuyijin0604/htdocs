<?php echo $this->renderPartial('rts_scan_form', array('model'=>$model,'op'=>$op)); ?>


<?php
echo '<h1>RTS Waiting Discard List</h1>';
?>

<?php $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>$_GET["tabid"].'_rts_unknown_waiting_discard_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(true,50),
    'template' => "{summary}\n{items}\n{pager}",
    'afterAjaxUpdate'=>'function(){initDiscardList();}',
    'columns'=>array(
            array('name' => 'location_id','value'=>'@$data->location->code','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),
             array('name' => 'shipment_id','header'=>'Ref','value'=>'!empty($data->shipment_id)?@$data->shipment->ref:""','filter'=>CHtml::textField('ShipmentRtsRecord[shipment_id]', @$model->shipment_id, array('class'=>'form-control'))),
            array('name' => 'barcode','type'=>'raw','value'=>'$data->getBarcode()','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),

            array('name' => 'record_time','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),

            array('header' => 'Image Url','type'=>'raw','value'=>'$data->getUrl()','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),
            ['class'=>'oButtonColumn',
                'template'=>'{discard}',
                'buttons'=>[
                    'discard' => [
                        'imageUrl'=>false,
                        'options' => ['class' => 'grid_view_btn discard', 'label' => 'Log', 'data-win-class' => 'L','target'=>'_blank'],
                        'visible'=>'true',
                        'url' => 'Yii::app()->createUrl("whscan/rtsProcess/confirmDiscard")."?id=".$data->id."&&sno=".$data->adjust_sn',
                        'label' => 'Confirm Discard'
                    ]
                ],
                'cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'
            ]
        ),
    )
    
);
?>

<script type="text/javascript">
    function initDiscardList()
    {
            $('.discard').on('click',function(){
            let url = $(this).attr('href');
            $.ajax({
                url: url,
                type: "get",
                data: [],
                processData: false,
                contentType: false,
                success: function(r) {
                   $('#notifc').notify({message: {html: 'done!'}}).show();
                   $('#<?=$_GET["tabid"]?>_rts_unknown_waiting_discard_grid_view').yiiGridView("update");
                 },
                error: function(e) {
                    console.log(e);
                }
            }); 
            return false;
        })
    }
$(function() {
   initDiscardList();

});
</script>