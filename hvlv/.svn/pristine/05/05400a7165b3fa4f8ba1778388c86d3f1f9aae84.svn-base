<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>

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

<?php $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>$_GET["tabid"].'_rts_unknown_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(true,50),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
            array('name' => 'location_id','value'=>'@$data->location->code','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),
            array('name' => 'barcode','type'=>'raw','value'=>'$data->getBarcode()','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),
             ['name' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('ShipmentRtsRecord[status]', $model->status, $this->t(ShipmentRtsRecord::$states), ['prompt'=>$this->t('All'),'class'=>'form-control']),'cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'],

            array('name' => 'record_time','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),

            array('header' => 'Image Url','type'=>'raw','value'=>'$data->getUrl()','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),
            ['class'=>'oButtonColumn',
                'template'=>'{print}',
                'buttons'=>[
                    'print' => [
                        'imageUrl'=>false,
                        'options' => ['class' => 'grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L','target'=>'_blank'],
                        'visible'=>'empty($data->adjust_shipment_id)?false:true',
                        'url' => 'Yii::app()->createUrl("rtsProcess/printAdjustShipmentLabel")."?id=".$data->id."&&sno=".$data->adjust_sn',
                        'label' => 'print'
                    ]
                ],
                'cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'
            ]
        ),
    )
    
);
?>

<script type="text/javascript">
$(function() {


});
</script>