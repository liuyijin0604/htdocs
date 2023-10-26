
<h1><?=$this->t('All Billing');?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'tow-billing-all-grid',
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
        array( 'name' => 'billing_ref', 'header' => 'Console No.'),
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
            /*
        array(
            'class'=>'oButtonColumn',
            'template'=>'{edit}',
            'buttons'=>array
            (
                'edit' => array(
                    'imageUrl'=>false,
                    'options' => array('class' => 'jqm_link grid_edit_btn', 'label' => 'Mod', 'data-win-class' => 'L'),
                    'visible' => 'true',
                    'url' => 'Yii::app()->createUrl("billing/updateActual", ["fid" => $data->id])',
                    'label' => 'Mod'
                ),
            ),
        ),*/
    ),
)); ?>

<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');

        tab.bind('onOpen', function(){
            $('#tow-billing-all-grid', panel).yiiGridView('update');
        });

    });
</script>