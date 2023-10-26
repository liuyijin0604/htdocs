<?php echo $this->renderPartial('rts_scan_form', array('model'=>$model,'op'=>$op)); ?>

<?php echo $this->renderPartial('rts_scan_form', array('model'=>$model,'op'=>$op."_new")); ?>

<?php if(!empty($provide)):?>
<table style="font-size: 2em;">
    <tr><td colspan="2"><?=$provide[0]?></td></tr>
    <tr><td>&nbsp;--&nbsp;</td><td>Total :&nbsp;&nbsp;<?=$provide[1]?></td></tr>
    <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>Done :&nbsp;&nbsp;<?=$provide[2]?></td></tr>
    <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>Left :&nbsp;&nbsp;<?=$provide[3]?></td></tr>
</table>
<?php endif;?>
<?php
echo '<h1>Shipment RTS Waiting Resend List</h1>';
?>
<?php
echo CHtml::button("Export Resend List", ['class' => 'export_resend_'.$op,'style'=> 'margin-left:20px;font-size:1em;']);
?>

<?php $this->widget('application.extensions.booster.TbExtendedGridView',array(
    'fixedHeader'=>true,
    'id'=>'rts_waiting_resend_grid_view',
    'filter'=>$model,
    'type'=>'striped bordered',
    'headerOffset'=>40,
    'responsiveTable'=>true,
    'dataProvider'=>$model->search(true,50),
    'template' => "{summary}\n{items}\n{pager}",
    'columns'=>array(
            array('name' => 'location','type'=>'raw','value'=>'$data->getRTSLocation(true)'),
            array('name' => 'hbn','value'=>'$data->originalShipment->hbn'),
            array('name' => 'ref','value'=>'$data->originalShipment->ref'),
            array('name' => 'barcode','type'=>'raw','value'=>'$data->getRTSBarcodes(true)'),
            array('name' => 'newRef','type'=>'raw','value'=>'"<font style=\"background-color:red;\">".$data->newShipment->ref."</font>"'),
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
    $('.export_resend_<?=$op?>').click(function(e){
        var pdfFrame_<?=$op?> = window.frames["pdf_label_<?=$op?>"];
        $("#pdf_label_<?=$op?>").attr("src","<?php echo Yii::app()->createAbsoluteUrl("whscan/rtsProcess/exportRTSResendList"); ?>");
        $("#pdf_label_<?=$op?>").load(function(){
             pdfFrame_<?=$op?>.focus();
             pdfFrame_<?=$op?>.print();
        });
    }); 

});
</script>