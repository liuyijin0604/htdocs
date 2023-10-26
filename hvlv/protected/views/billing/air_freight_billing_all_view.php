
<h1> Air Freight Billing Import List </h1>

<?php
    $supplier_id = $model->search()->getData()[0]->supplier_id;
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'af-billing-import-list-all-grid',
        'cssFile' => false,
        'dataProvider'=>$model->search(),
        'filter' => $model,
        'columns'=>array(
            'date',
            array('name' => 'invoice_no','type' => 'raw',
                'value' => '"<a href=\"".Yii::app()->createURL("billing/afInvoiceImportLinesView",["id"=> $data->id])."\" class=\"jqm_link\" title=\".$data->invoice_no.\">".$data->invoice_no."</a>" '),
            //   array( 'name' => 'op_id','value' => '!empty($data->op) ? $data->op->name : ""'),
            array('name' => 'etd', 'value' => '$data->etd', 'visible' => in_array($supplier_id, [954, 964])),
            array('header' => 'Desc', 'value' => '$data->lines[0]->desc', 'visible' => in_array($supplier_id, [1133])),
            array('header' => 'Shipment', 'type' => 'raw', 'value' => '$data->getShipmentView()', 'visible' => in_array($supplier_id, Org::$brokers)),
            array('name' => 'awb', 'type' => 'raw', 'value' => '$data->getAwbView()'),
            'subtotal',
            'gst',
            'total',
            array('name' => 'op_name','type' => 'raw','value' => 'empty($data->user) ? "" : $data->user->name'),
            array('name' => 'matched_result','type' => 'raw','value' => '$data->getMatchedErrMessage()'),
            array('name' => 'inv',  'type' => 'raw', 'value' => '$data->getInvView()', 'visible' => in_array($supplier_id, array_merge(Org::$brokers, Org::$airports))),
            array('name' => 'note','type' => 'raw','value' => '$data->getLastLog()'),
            array(
                'class'=>'oButtonColumn',
                'template'=>'{view}{update}{reject}{log}',
                'buttons'=>array(
                    'view' => array(
                        'imageUrl'=>false,
                        'options' => array('class' => 'tab_link grid_file_btn', 'label' => 'Fix'),
                        'visible' => '$data->matched_result == 1 && in_array($data->supplier_id, [954, 964])',
                        'url' => 'Yii::app()->createUrl($data->model."/update", ["id" => $data->fid])',
                        'label' => 'View'
                    ),
                    'update' => array(
                        'imageUrl'=>false,
                        'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => 'update'),
                        'visible' => '($data->matched_result == 2 || $data->matched_result == 8 || $data->matched_result == 10 || $data->notFoundAwb()) && !in_array($data->supplier_id, Org::$brokers)',
                        'url' => 'Yii::app()->createUrl("billing/afbUpdateAwb", ["afid" => $data->id ])',
                        'label' => 'Update'
                    ),
                    'reject' => array(
                        'imageUrl'=>false,
                        'options' => array('class' => 'tab_link grid_delete_btn reject_btn', 'label' => 'Reject'),
                        'visible' => '$data->matched_result != 3',
                        'url' => '$data->id',
                        'label' => 'Reject'
                    ),
                    'log' => array(
                        'imageUrl'=>false,
                        'options' => array('class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'),
                        'visible' => 'true',
                        'url' => 'Yii::app()->createUrl("billing/afblog", ["fid" => $data->id])',
                        'label' => 'Log'
                    ),
                ),
            ),
        ))
    ); ?>

<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        tab.bind('onOpen', function(){
            $('#af-billing-import-list-all-grid', panel).yiiGridView('update');
        });

        $(panel).on('click','.reject_btn',function(e){
            e.preventDefault();
            e.stopPropagation();
            if ( confirm('Are you sure reject the invoice?') ) {
                var fid = $(this).attr('href');
                $.get('billing/rejectAFInvoice/' + fid + '.app', function(r){
                    if(r.done){
                        $('#af-billing-import-list-all-grid', panel).yiiGridView('update');
                    }else{
                        myApp.alert(r.msg);
                    }
                }, 'json');
            }
        });
    });
</script>
