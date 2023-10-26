<?php if(!empty($provide)):?>
<table style="font-size: 2em;">
    <tr><td colspan="2"><?=$provide[0]?></td></tr>
    <tr><td>&nbsp;--&nbsp;</td><td>Total :&nbsp;&nbsp;<?=$provide[1]?></td></tr>
    <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>Done :&nbsp;&nbsp;<?=$provide[2]?></td></tr>
    <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>Left :&nbsp;&nbsp;<?=$provide[3]?></td></tr>
</table>
<?php endif;?>

<?php
echo '<h1>RTS Unknown List</h1>';
?>
<?php $this->widget('zii.widgets.grid.CGridView',array(
    'cssFile' => false,
    'id'=>$_GET["tabid"].'_rts_unknown_grid_view',
    'filter'=>$model,
    'dataProvider'=>$model->search(true,50),
    'template' => "{summary}\n{items}\n{pager}",
    'afterAjaxUpdate'=>'function(){initUnknownList();}',
    'columns'=>array(
            array('name' => 'barcode','type'=>'raw','value'=>'$data->getBarcode()'),
            ['name' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('ShipmentRtsRecord[status]', $model->status, $this->t(ShipmentRtsRecord::$states), ['prompt'=>$this->t('All')]),],
            array('name' => 'location_id','value'=>'@$data->location->code'),
            array('name' => 'warehouse_id','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")', 'value' => '@$data->warehouse->name',
            'filter'=>CHtml::dropDownList(get_class($model).'[warehouse_id]', $model->warehouse_id, $this->t(Org::dptList()), ['prompt'=>$this->t('All')])),
            array('name' => 'record_time'),
            array('header' => 'Image Url','type'=>'raw','value'=>'$data->getUrl()'),
            ['class'=>'oButtonColumn',
                'template'=>'{Edit Unknown}&nbsp;{print}&nbsp;{discard}&nbsp;{log}',
                'buttons'=>[
                    'Edit Unknown' => [
                        'url'=>'Yii::app()->createURL("rtsProcess/unknownOperation")."?id=".$data->id',
                        'imageUrl'=>false,
                        'visible'=>'empty($data->adjust_shipment_id)?true:false',
                        'options' => ['class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Edit Unknown'), 'title' => '$data->id'],
                    ],
                    'print' => [
                        'imageUrl'=>false,
                        'options' => ['class' => 'grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L','target'=>'_blank'],
                        'visible'=>'empty($data->adjust_shipment_id)?false:true',
                        'url' => 'Yii::app()->createUrl("rtsProcess/printAdjustShipmentLabel", ["id" => $data->id,"sno" => $data->adjust_sn])',
                        'label' => 'print'
                    ],
                    'discard'=>[
                        'imageUrl'=>false,
                        'visible'=>'true',
                        'options' => ['class' => 'discard_unknown grid_view_btn', 'label' => 'Close', 'data-win-class' => 'L'],
                        'url' => 'Yii::app()->createUrl("rtsProcess/discardUnknownRTS", ["id" => $data->id])',
                        'label' => 'Discard'
                    ],
                    'log' => [
                        'imageUrl'=>false,
                        'options' => ['class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'],
                        'visible' => 'true',
                        'url' => 'Yii::app()->createUrl("rtsProcess/unknownLog", ["id" => $data->id])',
                        'label' => 'Log'
                    ],
                ],
            ]
        ),
    )
    
);
?>

<script type="text/javascript">
    function initUnknownList()
    {
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel=tab.data('panel');
        tab.unbind('reload_rts_unknown_grid_view').bind('reload_rts_unknown_grid_view', function(){
            $('#<?=$_GET["tabid"]?>_rts_unknown_grid_view', panel).yiiGridView('update');
            return false;
        });

        $('.discard_unknown',panel).on('click',function(event){
            event.preventDefault();
            if(confirm("Are you Confirm to discard unknown?")){
                $.get($(this).attr('href'),function(r){
                    if(r=='done'){
                        myApp.notice("Discarded!");
                        $('#<?=$_GET["tabid"]?>_rts_unknown_grid_view', panel).yiiGridView('update');
                    }
                });
             }
        });
    }

    $(function(){
         initUnknownList();
    })
</script>