<?php
if (empty(Yii::app()->session['scan_warehouse'])) {
	echo '<a class="dash-item ajax-link" href="'.$this->createUrl('site/index', ['scan_warehouse' => 'sydney']).'"><span class="glyphicon glyphicon-wrench"></span><br/>Home Page(To Choose Warehouse)</a>';
	return;
}
?>



<?php
echo '<h1>RTS Discard Done List</h1>';
?>

<?php $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>$_GET["tabid"].'_rts_unknown_discard_done_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(true,50),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
            array('name' => 'barcode','type'=>'raw','value'=>'$data->getBarcode()','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),
            array('name' => 'location_id','value'=>'@$data->location->code','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),

            array('name' => 'record_time','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),
             array('name' => 'discard_time','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")'),

            array('header' => 'Image Url','type'=>'raw','value'=>'$data->getUrl()','cssClassExpression'=>'($data->adjust_shipment_id!=0&&$data->is_print==0)?"red":($data->adjust_shipment_id!=0&&$data->is_print==1?"green":"")')
        ),
    )
    
);
?>

<script type="text/javascript">
$(function() {


});
</script>