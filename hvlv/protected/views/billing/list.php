
<h1><?=$this->t('Billing List');?></h1>

<div style="right: 20px;position: absolute;margin-top: -30px;">
    <a class="tab_link" href="<?=$this->createUrl('billing/importTransport');?>" title="Import Billing"><div class="icon" style="background-position:-16px 0"></div>Import Transport Billing</a>
    <a class="tab_link" href="<?=$this->createUrl('billing/import');?>" title="Import Billing"><div class="icon" style="background-position:-16px 0"></div>Import Air Freight Billing</a>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'billing-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'no',
      //  'created',
        'billing_ref',
        'billing_cref',
        array('name' => 'client', 'value' => ' ( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->shortName(2)'),
        array('name' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('Billing[status]', $model->status, $this->t($model::$states), array('prompt'=>$this->t('All'))),),
        array('name' => 'type', 'value' => '$data->getType()',
            'filter'=>CHtml::dropDownList('Billing[type]', $model->type, $this->t($model::$types), array('prompt'=>$this->t('All'))),),
        array('name' => 'currency', 'value' => '$data->getCurrency()',
            'filter'=>CHtml::dropDownList('Billing[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt'=>$this->t('All'))),),

        array('name' => 'dpt_id', 'value' => '$data->getDptName()',
                'filter'=>CHtml::dropDownList('Billing[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt'=>$this->t('All'))),),
        'weight',
        'charge_weight',
        'date',
        'due',
        'total',
        array(
            'class'=>'oButtonColumn',
            'template'=>'{approve}{detail}{xero}',
            'buttons'=>array
            (
                'approve' => array(
                    'imageUrl'=>false,
                    'options' => array('class' => 'grid_edit_btn billing-approve-btn', 'label' => 'Approve'),
                    'visible' => '$data->canApprove() ? true : false',
               //     'url' => 'Yii::app()->createUrl("billing/approve", ["fid" => $data->id])',
                    'url' => '$data->id',
                    'label' => 'Approve'
                ),
                'xero' => array(
                    'imageUrl'=>false,
                    'options' => array('class' => 'grid_edit_btn billing-syncxero-btn', 'label' => 'SyncXero'),
                    'visible' => '$data->canSyncXero() ? true : false',
                    //     'url' => 'Yii::app()->createUrl("billing/approve", ["fid" => $data->id])',
                    'url' => '$data->id',
                    'label' => 'SyncXero'
                ),
                'detail' => array(
                    'imageUrl'=>false,
                    'options' => array('class' => 'jqm_link grid_view_btn', 'label' => 'Detail', 'data-win-class' => 'L'),
                    'visible' => 'true',
                    'url' => 'Yii::app()->createUrl("billing/details", ["fid" => $data->id])',
                    'label' => 'Details'
                ),
            ),
        ),
    ),
)); ?>
<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');

        tab.bind('onOpen', function(){
            $('#billing-grid', panel).yiiGridView('update');
        });

        $(document).on('click','.billing-approve-btn',function(e){
            e.preventDefault();
            e.stopPropagation();
            var fid = parseInt( $(this).attr('href') );
            $.get('billing/approve?fid='+fid, function(e){
                alert('update successfully');
                $('#billing-grid', panel).yiiGridView('update');
            });
        });


        $(document).on('click','.billing-syncxero-btn',function(e){
            e.preventDefault();
            e.stopPropagation();
            var fid = parseInt( $(this).attr('href') );
            $.get('billing/syncxero?fid='+fid, function(e){
                alert('sync xero successfully');
                $('#billing-grid', panel).yiiGridView('update');
            });
        });


    });
</script>