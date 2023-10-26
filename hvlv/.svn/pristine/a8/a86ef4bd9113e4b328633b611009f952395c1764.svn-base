
<h1><?=$this->t('All Billing');?></h1>
<div style="right: 20px;top:20px;position: absolute;">
    <a href="#" data-dropdown="#<?=$_GET["tabid"];?>-dropdown-1"><div style="background-position:-192px -80px" class="icon"></div> Actions</a>
    <div id="<?=$_GET["tabid"];?>-dropdown-1" class="dropdown dropdown-tip dropdown-relative dropdown-anchor-right">
        <ul class="dropdown-menu">
        <li><a class="export_search" href="#" data-baseurl="<?=$this->createUrl('billing/report', ['type' => 'import']);?>" target="_blank">Export Current Search</a></li>
        </ul>
    </div>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'import-billing-all-grid',
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

        array( 'header' => 'Console No.','type' => 'raw', 'name' => 'billing_ref', 'value' => '"<a href=\"".Yii::app()->createURL("imcoConsol/updateByNo", array("no" => $data->billing_ref))."\" class=\"tab_link\" title=\"".$data->billing_ref."\">".$data->billing_ref."</a>"'),


        'awb',
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
        'accrual_amount',
        'actual_amount',
    ),
)); ?>

<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');

        tab.bind('onOpen', function(){
            $('#import-billing-all-grid', panel).yiiGridView('update');
        });

        $('a.export_search', panel).on('mousedown', function(){
            var q = $('.filters input, .filters select', panel).serialize();
            $(this).attr('href', $(this).data('baseurl') + '&' + q);
        });

    });
</script>