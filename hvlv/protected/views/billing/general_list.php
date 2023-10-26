
<div style="right: 20px;position: absolute;">
    <a class="jqm_link" data-win-class="L" href="<?=$this->createUrl('billing/createGeneral');?>" title="Create General Cost"><div class="icon" style="background-position:-16px 0"></div>New General Cost</a>
    <a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-192px -80px" class="icon"></div> Actions</a>
    <div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
        <ul class="dropdown-menu">
        <li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('billing/report', ['type' => 'general']);?>" target="_blank">Export Current Search</a></li>
        </ul>
    </div>
</div>


<h1><?=$this->t('General Cost List');?></h1>


<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'general-cost-all-grid',
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'no',
        array('name' => 'client', 'value' => ' ( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->shortName(2)'),
        'billing_cref',
        'total',
        array('name' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('Billing[status]', $model->status, $this->t($model::$states), array('prompt'=>$this->t('All'))),),

        array('name' => 'currency', 'value' => '$data->getCurrency()',
            'filter'=>CHtml::dropDownList('Billing[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt'=>$this->t('All'))),),
        array('name' => 'dpt_id', 'value' => '$data->getDptName()',
                'filter'=>CHtml::dropDownList('Billing[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt'=>$this->t('All'))),),
        'created',
        'date',
        'due',
        array(
            'class'=>'oButtonColumn',
            'template'=>'{detail}',
            'buttons'=>array
            (
                /*
                'approve' => array(
                    'imageUrl'=>false,
                    'options' => array('class' => 'grid_edit_btn billing-approve-btn', 'label' => 'Approve'),
                    'visible' => '$data->canApprove() ? true : false',
                    'url' => '$data->id',
                    'label' => 'Approve'
                ),
                'xero' => array(
                    'imageUrl'=>false,
                    'options' => array('class' => 'grid_edit_btn billing-syncxero-btn', 'label' => 'SyncXero'),
                    'visible' => '$data->canSyncXero() ? true : false',
                    'url' => 'Yii::app()->createUrl("billing/syncGeneralXero", ["fid" => $data->id])',
                    'label' => 'SyncXero'
                ),*/
                'detail' => array(
                    'imageUrl'=>false,
                    'options' => array('class' => 'jqm_link grid_view_btn', 'label' => 'Detail', 'data-win-class' => 'L'),
                    'visible' => 'true',
                    'url' => 'Yii::app()->createUrl("billing/generalDetails", ["fid" => $data->id])',
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
            $('#general-cost-all-grid', panel).yiiGridView('update');
        });

        $('a.export_search', panel).on('mousedown', function(){
            var q = $('.filters input, .filters select', panel).serialize();
            $(this).attr('href', $(this).data('baseurl') + '&' + q);
        });
    });
</script>