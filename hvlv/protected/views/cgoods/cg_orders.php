
<div>
    <h2>Latest Order Report</h2>
    <div id="latest-order-report">
    <?php
        $allInfo = CgOrder::getNewsOrdersStatic();
        echo '<span>Orders : ' . $allInfo['total'] . '</span><br/>';
        // calculate all items count
        foreach ( $allInfo['details'] as $cg ) {
            echo '<span>' .$cg['name'] . ' : ' . $cg['total'] . '</span><br/>';
        }

    if ( !empty($allInfo['details']) ) {
        echo '<br/>'. CHtml::button('Print Order',['id' => 'print-order-btn']);
      //  echo '<br/>'. CHtml::checkBox('chgo',true,['id' => 'ck-change-status']) . 'Change status as Prepare done';
    }
    ?>
        </div>
</div>

<br/>
<h1><?php echo $this->t('Consumable Goods Orders'); ?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'cg-orders-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'filter' => $model,
    'columns'=>array(
        'client_id',
        array('header' => 'Name','name' => 'client_id','type' => 'raw','value' => '$data->org->name'),
        'no',
        array('name' => 'status','value' => '$data->getStatus()'),
        'added_datetime',
        'dispatch_date',
        array('header' => 'Amount(AUD)','name' => 'amount'),
        array(
            'class'=>'CButtonColumn',
            'template'=>'{details}{update}',
            'buttons'=>array
            (
                'details' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'url' => 'Yii::app()->createURL("cgoods/showdetails", array("id" => $data->id))',
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Details')),
                ),
                'update' => array(
                    'imageUrl'=>false,
                    'visible'=>'true',
                    'url' => 'Yii::app()->createURL("cgoods/updateOrder", array("id" => $data->id))',
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update')),
                ),
            ),
        ),
    ),
)); ?>
<script type="text/javascript">
    $(function(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel = tab.data('panel');

        console.log('debug');

        function updateLatestOrders(){
            $.ajax({
                'url' : '<?php echo $this->createUrl('cgoods/getLatestOrders'); ?>',
                'type' : 'GET',
                'dataType' : 'json',
                success: function(resp){
                    if ( resp.success ) {
                        $('#latest-order-report',panel).html(resp.data);
                    }
                }
            });
        }
        tab.bind('onOpen', function(){
            $('#cg-orders-grid', panel).yiiGridView('update');
            updateLatestOrders();
        });

        $('body').on('click','#print-order-btn',function(e){
            e.preventDefault();
            e.stopPropagation();
            $.ajax({
                'url' : '<?php echo $this->createUrl('cgoods/printOrdersReq'); ?>',
                'type' : 'GET',
                'dataType' : 'json',
                success: function(resp){

                    updateLatestOrders();
                    $('#cg-orders-grid', panel).yiiGridView('update');

                    window.open(resp.url);

                }
            });
        });
    });
</script>
