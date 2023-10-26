
<h1><?=$this->t('All Billing');?></h1>
<div style="right: 20px;top:20px;position: absolute;">
    <a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-192px -80px" class="icon"></div> Actions</a>
    <div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
        <ul class="dropdown-menu">
        <li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('billing/report', ['type' => 'air']);?>" target="_blank">Export Current Search</a></li>
        </ul>
    </div>
</div>


<?php $this->widget('application.extensions.editablegrid.CEditableGridView', array(
    'id'=>'acc-billing-all-grid',
    'selectableRows' => 2,
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        array(
            'id'=>'selectedItems',
            'class'=>'CCheckBoxColumn',
        ),
          'no',
        array( 'header' => 'Job#','type' => 'raw', 'name' => 'billing_ref', 'value' => '$data->getNo()'),

        'billing_cref',
        array('name' => 'client', 'value' => ' ( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->shortName(3)'),
        'desc',
          array('name' => 'status', 'value' => '$data->getStatus()',
            'filter'=>CHtml::dropDownList('BillingLine[status]', $model->status, $this->t($model::$states), array('prompt'=>$this->t('All'))),),
        array('name' => 'type', 'value' => '$data->getType()',
            'filter'=>CHtml::dropDownList('BillingLine[type]', $model->type, $this->t($model::$types), array('prompt'=>$this->t('All'))),),
        array('name' => 'currency', 'value' => '$data->getCurrency()',
            'filter'=>CHtml::dropDownList('BillingLine[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt'=>$this->t('All'))),),

        array('name' => 'dpt_id', 'value' => '$data->getDptName()',
            'filter'=>CHtml::dropDownList('BillingLine[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt'=>$this->t('All'))),),
        'weight',
        'charge_weight',
        'date',
        'due',
        'accrual_amount',
        'actual_amount',
        array('header' => 'GST', 'name' => 'gst','filter'=> Invoice::$InvoiceCostTaxRate),
        'gst_amount',
        array(
            'class' => 'CEditableButtonColumn',
            'template' => $model->status < 3 ? '{delete}' : '',
            'buttons' => array(
                'delete' => array(
                    'imageUrl' => false,
                    'url' => 'Yii::app()->createUrl("billing/importGridDelete", ["id" => $data->id])',
                    'visible' => '$data->status < 3',
                    'options' => array('class' => 'delete_btn'),
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
            $('#acc-billing-all-grid', panel).yiiGridView('update');
        });

        $('a.export_search', panel).on('mousedown', function(){
            var q = $('.filters input, .filters select', panel).serialize();
            $(this).attr('href', $(this).data('baseurl') + '&' + q);
        });

    });
</script>