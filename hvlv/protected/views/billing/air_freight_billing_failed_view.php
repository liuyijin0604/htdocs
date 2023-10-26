
<div style="right: 40px;position: absolute;">
    <a href="<?=$this->createUrl('billing/exportAFIFailedList',['id' => $id]);?>" title="Export"><div class="icon" style="background-position:-16px 0"></div>Export</a>
</div>

<h1> Air Freight Billing Import Failed List </h1>

<?php
    $supplier_id = $model->search()->getData()[0]->supplier_id;
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'af-billing-import-list-failed-grid-'.$_GET['tabid'],
        'cssFile' => false,
        'dataProvider'=>$model->search(),
        'filter' => $model,
        'columns'=>array(
            'date',
            array('name' => 'invoice_no','type' => 'raw',
                'value' => '"<a href=\"".Yii::app()->createURL("billing/afInvoiceImportLinesView",["id"=> $data->id])."\" class=\"jqm_link\" title=\".$data->invoice_no.\">".$data->invoice_no."</a>" '),
            //   array( 'name' => 'op_id','value' => '!empty($data->op) ? $data->op->name : ""'),
            array('header' => 'Desc', 'value' => '$data->lines[0]->desc', 'visible' => in_array($supplier_id, [1133])),
            array('header' => 'Shipment', 'type' => 'raw', 'value' => '$data->getShipmentView()', 'visible' => in_array($supplier_id, Org::$brokers)),
            array('name' => 'awb', 'type' => 'raw', 'value' => '$data->getAwbView()'),
            'subtotal',
            'gst',
            'total',
            array('name' => 'op','type' => 'raw','value' => 'empty($data->user) ? "" : $data->user->name'),
            array('name' => 'matched_result','type' => 'raw','value' => '$data->getMatchedErrMessage()'),
            array('name' => 'inv',  'type' => 'raw', 'value' => '$data->getInvView()', 'visible' => in_array($supplier_id, array_merge(Org::$brokers, Org::$airports))),
            array('name' => 'note','type' => 'raw','value' => '$data->getLastLog()'),
            array(
                'class'=>'oButtonColumn',
                'template'=>'{fix}{update}{confirm1}{confirm2}{confirm_import}{confirm_export}{log}{update_amount}',
                'buttons'=>array(
                    'fix' => array(
                        'imageUrl'=>false,
                        'options' => array('class' => 'tab_link grid_gallery_btn', 'label' => 'Fix'),
                        'visible' => '$data->matched_result != 2 && $data->matched_result != 8 && $data->matched_result != 10 && in_array($data->supplier_id, array_merge([954, 964], Org::$brokers))',
                        'url' => 'Yii::app()->createUrl($data->model."/update", ["id" => $data->fid,"afid" => $data->id ])',
                        'label' => 'Fix'
                    ),
                    'update' => array(
                        'imageUrl'=>false,
                        'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => 'update'),
                        'visible' => '($data->matched_result == 2 || $data->matched_result == 8 || $data->matched_result = 9 || $data->matched_result == 10 || $data->notFoundAwb() || in_array($data->supplier_id, [1133])) && !in_array($data->supplier_id, Org::$airports)',
                        'url' => 'Yii::app()->createUrl("billing/afbUpdateAwb", ["afid" => $data->id ])',
                        'label' => 'Update'
                    ),
                    // TNE confirm to dept - wont link to any EDIJOB / CONSOL / WMSTASK
                    'confirm1' => array(
                        'imageUrl' => false,
                        'options' => array('class' => 'jqm_link grid_gallery_btn', 'label' => 'Confirm To Dept'),
                        'visible' => '$data->notFoundAwb() === true && $data->supplier_id == 1133',
                        'url' => 'Yii::app()->createUrl("billing/tneConfirm", ["afid" => $data->id])',
                        'label' => 'Confirm To Dept'
                    ),
                    'confirm2' => array(
                        'imageUrl' => false,
                        'options' => array('class' => 'jqm_link grid_gallery_btn', 'label' => 'Confirm'),
                        'visible' => 'in_array($data->supplier_id, Org::$airports)',
                        'url' => 'Yii::app()->createUrl("billing/confirm", ["afid" => $data->id])',
                        'label' => 'Confirm',
                    ),
                    // TNE confirm to CONSOL / WMSTASK - direct create billing line in CONSOL / WMSTASK
                    'confirm_import' => array(
                        'imageUrl'=>false,
                        'options' => array('class' => 'jqm_link grid_gallery_btn', 'label' => 'Confirm Import/3PL'),
                        'visible' => '$data->notFoundAwb() === true && $data->supplier_id == 1133',
                        'url' => 'Yii::app()->createUrl("billing/afbUpdateAwbImport", ["afid" => $data->id ])',
                        'label' => 'Confirm Import/3PL'
                    ),
                    // YUYANG confirm to dept - wont link to any EDIJOB
                    'confirm_export' => array(
                        'imageUrl' => false,
                        'options' => array('class' => 'jqm_link grid_gallery_btn', 'label' => 'Confirm'),
                        'visible' => 'in_array($data->supplier_id, [954, 964]) && $data->notFoundAwb() === true',
                        'url' => 'Yii::app()->createUrl("billing/afbUpdateAwbExport", ["afid" => $data->id ])',
                        'label' => 'Confirm',
                    ),
                    'log' => array(
                        'imageUrl'=>false,
                        'options' => array('class' => 'jqm_link grid_view_btn', 'label' => 'Log', 'data-win-class' => 'L'),
                        'visible' => 'true',
                        'url' => 'Yii::app()->createUrl("billing/afblog", ["fid" => $data->id])',
                        'label' => 'Log'
                    ),
                    'update_amount' => array(
                        'imageUrl' => false,
                        'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => 'Edit Amount'),
                        'visible' => '$data->supplier_id == 1133 && Acl::hasAccess("B:billing/updateTNEAmount")',
                        'url' => 'Yii::app()->createUrl("billing/updateTNEAmount", ["id" => $data->id])',
                        'label' => 'Edit Amount',
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
            $('#af-billing-import-list-failed-grid-<?=$_GET["tabid"];?>', panel).yiiGridView('update');
        });
    });
</script>
