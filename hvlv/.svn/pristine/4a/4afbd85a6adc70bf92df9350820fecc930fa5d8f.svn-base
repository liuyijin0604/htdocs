
<h1> Air Freight Billing Sync With Xero Failed List </h1>

<?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'afb-syncxero-fail-list-all-grid',
        'cssFile' => false,
        'dataProvider'=>$datap,
        'columns'=>array(
            'created',
            array('name' => 'invoice_no','type' => 'raw',
                'value' => '"<a href=\"".Yii::app()->createURL("billing/afInvoiceImportLinesView",["id"=> $data->id])."\" class=\"jqm_link\" title=\".$data->invoice_no.\">".$data->invoice_no."</a>" '),
            array('name' => 'awb', 'type' => 'raw', 'value' => '$data->getAwbView()'),
            'subtotal',
            'gst',
            'total',
            array('name' => 'op_name','type' => 'raw','value' => 'empty($data->user) ? "" : $data->user->name'),
        ))
    ); ?>

<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        tab.bind('onOpen', function(){
            $('#afb-syncxero-fail-list-all-grid', panel).yiiGridView('update');
        });
    });
</script>
