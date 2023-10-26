<?php
    $model = Payment::model();

?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=> 'bank-transactions-grid',
    'cssFile' => false,
    'dataProvider'=>$model->searchForReconciled(),
    'filter'=>$model,
    'columns'=>array(
        'date',
        'amount',
        'ref',
        array('header' => 'Status','type' => 'raw', 'value' => '$data->getStatus()'),
        array(
            'class'=>'oButtonColumn',
            'template'=>'{update}',
            'buttons'=>array
            (
                'update' => array(
                    'imageUrl' => false,
                   // 'visible' => '$data->bank_transaction_id == 0 ? true : false',
                    'url' => 'Yii::app()->createUrl("payment/update", ["id" => $data->id])',
                    'label' => 'Update',
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'label'=>$this->t('Update'), 'title' => '$data->ref'),
                ),
            ),
        ),
    ),
)); ?>
</p>

<script type="text/javascript">
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel = tab.data('panel');
    tab.bind('onOpen', function(){
        $('#bank-transactions-grid', panel).yiiGridView('update');
    });
</script>
