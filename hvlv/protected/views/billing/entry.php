<?php
    $allAirSeaBillingOpLines = BillingLine::totOpLines(BillingLine::BILLING_TYPE_AIR_SEA);
    $allImportBillingOpLines = BillingLine::totOpLines(BillingLine::BILLING_TYPE_IMPORT);
    $allExportBillingOpLines = BillingLine::totOpLines(BillingLine::BILLING_TYPE_EXPORT);
    $allGeneralBillingOpLines = BillingLine::totOpLines(BillingLine::BILLING_TYPE_OTHERS,2);
?>
<div style="margin-bottom: 20px;">
    <ol>
        <li><a class="tab_link" href="billing/list" title="Air Freight & 3PL Billing">Air Freight & 3PL(<span style="color:darkred"> <?php echo $allAirSeaBillingOpLines; ?> </span>Op lines )</a></li>
        <li><a class="tab_link" href="billing/importList" title="Import Cost Billing">Import Service Cost Billing(<span style="color:darkred"> <?php echo $allImportBillingOpLines; ?> </span>Op lines)</a></li>
        <li><a class="tab_link" href="billing/exportList" title="Export Cost Billing">Export Service Cost Billing(<span style="color:darkred"> <?php echo $allExportBillingOpLines; ?> </span>Op lines)</a></li>
        <li><a class="tab_link" href="billing/generalCost" title="General Cost Billing">General Cost Billing(<span style="color:darkred"> <?php echo $allGeneralBillingOpLines; ?> </span>Accounting lines)</a></li>
        <!--   <li><a class="tab_link" href="billing/towList" title="Tow Service Billing">Tow Service Cost Billing</a></li> -->
        <li><a class="tab_link" href="billing/accounting" title="Accounting">Accounting</a></li>
    </ol>
</div>
<div style="right: 20px;position: absolute;">
    <a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-192px -80px" class="icon"></div> Actions</a>
    <div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
        <ul class="dropdown-menu">
        <li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('billing/report', ['type' => 'entry']);?>" target="_blank">Export Current Search</a></li>
        <li><a class="jqm_link" href="<?=$this->createUrl('billing/rcvb');?>">Receivables Report</a></li>
        </ul>
    </div>
</div>

<h1><?=$this->t('Confirmed Billing List');?></h1>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'all-billing-grid',
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
                'detail' => array(
                    'imageUrl'=>false,
                    'options' => array('class' => 'jqm_link grid_view_btn', 'label' => 'Detail', 'data-win-class' => 'L'),
                    'visible' => 'true',
                    'url' => 'Yii::app()->createUrl("billing/billingDetails", ["fid" => $data->id])',
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
            $('#all-billing-grid', panel).yiiGridView('update');
        });

        $('a.export_search', panel).on('mousedown', function(){
            var q = $('.filters input, .filters select', panel).serialize();
            $(this).attr('href', $(this).data('baseurl') + '&' + q);
        });
    });
</script>
