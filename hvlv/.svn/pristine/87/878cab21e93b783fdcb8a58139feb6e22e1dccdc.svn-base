<h1> Hold Agent Shipments Report </h1>

<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'export-hold-agent-grid',
    'cssFile' => false,
    'dataProvider'=>$dp,
  //  'filter'=>$model,
    'columns'=>array(
        'id',
        'name',
        'aramount',
        'shipments',
        array(
            'class'=>'oButtonColumn',
            'template'=>'{detail}',
            'buttons'=>array
            (
                'detail' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'url' => 'Yii::app()->createUrl("report/holdDetails",array("id"=>$data["id"],"title" => "hold details"))',
                    'options' => array('class' => 'tab_link grid_edit_btn'),
                ),
            ),
        ),
    ),
)); ?>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');
        tab.bind('onOpen', function(){
            $('#export-hold-agent-grid', panel).yiiGridView('update');
        });
    });
</script>
