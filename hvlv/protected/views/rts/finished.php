
<h1> ALL Finished RTS Shipments</h1>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'im-rts-finished-parcel-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'columns'=>array(
        array('name' => 'hbn', 'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->id))."\" class=\"tab_link\" title=\"".$data->hbn."\">".$data->hbn."</a>"',),
        array('header' => 'Reshipping Connote',  'type' => 'raw', 'value' => '"<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->getRTSTranshipNoId()))."\" class=\"tab_link\" title=\"".$data->getRTSTranshipNo()."\">".$data->getRTSTranshipNo()."</a>"'),
        array('header' => 'Ref.', 'type' => 'raw','value' => '$data->getRtsNewShipmentRef()'),
        array('header' => 'Customer', 'type' => 'raw', 'value' => 'empty($data->agent)? "" : $data->agent->name'),
        array('header' => 'Received', 'type' => 'raw','value' => 'empty($data->mdata["rts_scan_date"])? "" : $data->mdata["rts_scan_date"]'),
        array('header' => 'Reshipping', 'type' => 'raw','value' => 'empty($data->mdata["rts_reshipping_time"])? "" : $data->mdata["rts_reshipping_time"]'),
        array('header' => 'Invoice', 'type' => 'raw','value' => '$data->getRtsReshippingInvoiceLink()'),
        'note'
    ),
)); ?>

<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');
        tab.on('onOpen', function(){
            $('#im-rts-finished-parcel-grid', panel).yiiGridView('update');
        });
    });
</script>