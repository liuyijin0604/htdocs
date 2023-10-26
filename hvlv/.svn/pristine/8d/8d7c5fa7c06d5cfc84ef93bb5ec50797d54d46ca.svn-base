<h1> Hold Agent Details </h1>

<?php

echo '<h2> For Agent -> ' . $model->name . '</h2>';
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'export-hold-agent-details-grid',
    'cssFile' => false,
    'dataProvider'=>$dp,
  //  'filter'=>$model,
    'columns'=>array(
        'no',
        'balance',
        'shipments',

     //   array('name' => 'shipments', 'type' => 'raw', 'value' => 'shipments' ),


        ),
)); ?>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        tab.bind('onOpen', function(){
            $('#export-hold-agent-details-grid', panel).yiiGridView('update');
        });
    });
</script>
