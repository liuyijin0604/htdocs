<h1> All New Claims </h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'insurance-approving-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'id',
        'note',
        array('header' => 'Shipment', 'type' => 'raw','value' => 'empty($data->shipment_id)? "" : "<a href=\"".Yii::app()->createURL("imParcel/update", array("id" => $data->shipment_id))."\" class=\"tab_link\" title=\"".$data->shipment->hbn."\">".$data->shipment->hbn."</a>"'),
        array('header' => 'Insurance', 'type' => 'raw','value' => 'empty($data->shipment_id)? "" : $data->shipment->insurance'),
        array('header' => 'Customer', 'type' => 'raw','value' => 'empty($data->shipment_id)? "" : $data->shipment->agent->name'),
        array(
            'class'=>'oButtonColumn',
            'template'=>'{approve}{reject}',
            'buttons'=>array
            (
                'approve' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'label' => 'Approve',
                    'url' => 'Yii::app()->createUrl("insurance/approve", ["id" => $data->id])',
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'title' => '$data->id'),
                ),

                'reject' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'label' => 'Reject',
                    'url' => 'Yii::app()->createUrl("insurance/reject", ["id" => $data->id])',
                    'options' => array('class' => 'jqm_link grid_gallery_btn', 'title' => '$data->id'),
                ),
            ),
        ),
    ),
)); ?>


<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');
        tab.on('onOpen', function(){
            $('#insurance-approving-grid', panel).yiiGridView('update');
        });
    });
</script>