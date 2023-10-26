<h1> All Finished Claims </h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'insurance-finished-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(array(3,4)),
    'filter'=>$model,
    'columns'=>array(
        'id',
        'note',
        'date_added',
        array('header' => 'Status', 'type' => 'raw','value' => '$data->getStatus()'),
        array('header' => 'Memo', 'type' => 'raw','value' => '$data->getMemo()'),
        array('header' => 'Shipment', 'type' => 'raw','value' => 'empty($data->shipment_id)? "" : "<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"'),
        array('header' => 'Insurance', 'type' => 'raw','value' => 'empty($data->shipment_id)? "" : $data->shipment->insurance'),
        array('header' => 'Customer', 'type' => 'raw','value' => 'empty($data->shipment_id)? "" : $data->shipment->agent->name'),
    ),
)); ?>


<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');
        tab.on('onOpen', function(){
            $('#insurance-finished-grid', panel).yiiGridView('update');
        });
    });
</script>