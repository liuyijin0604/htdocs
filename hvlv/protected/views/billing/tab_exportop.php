
<h1><?=$this->t('Billing waiting for confirm');?></h1>



<?php echo CHtml::link($this->t('Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
    <?php $this->renderPartial('_search',array(
        'model'=>$model,
    ));
    ?>
</div>


<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'export-billing-op-form',
    'enableAjaxValidation'=>false,
)); ?>

<div class="form">
    <div class="row rowcol">
        <?php echo CHtml::label('Invoice No.','for_op'); ?>
        <?php echo CHtml::textField('invoice_no','', ['size' => '12']); ?>
    </div>
<div class="row buttons" style="margin-bottom: 20px;">
    <?php
    echo  CHtml::submitButton('Confirm'); //CHtml::button('Confirm', array('class' => 'billing-op-btn' , 'type' => 'submit'));
    ?>
</div>
</div>

<div class="row">
    <div class="rowcol">
        <?php echo CHtml::label('Sub Total','for_acounting'); ?>
        <?php echo CHtml::textField('sub_total','', ['size' => '12', 'id' => 'sub-op-total','disabled' => 'disabled','data-total' => 0]); ?>
    </div>
</div>

<?php
// application.extensions.editablegrid.CEditableGridView
// zii.widgets.grid.CGridView
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
    'id'=>'export-billing-op-grid',
    'selectableRows' => 2,
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'formUrl' => $this->createUrl('billing/updateLine'),
    'filter'=>$model,
    'columns'=>array(
        array(
            'id'=>'selectedItems',
            'class'=>'CCheckBoxColumn',
        ),
      //  'no',


        array( 'header' => 'Console No.','type' => 'raw', 'name' => 'billing_ref', 'value' => '"<a href=\"".Yii::app()->createURL("excoConsol/updateByNo", array("no" => $data->billing_ref))."\" class=\"tab_link\" title=\"".$data->billing_ref."\">".$data->billing_ref."</a>"'),

        'awb',
        'billing_cref',
        array('name' => 'client', 'value' => ' ( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->shortName(2)'),
        'desc',
        // array('name' => 'type', 'value' => '$data->getType()',
          //  'filter'=>CHtml::dropDownList('Billing[type]', $model->type, $this->t($model::$types), array('prompt'=>$this->t('All'))),),
        array('name' => 'currency', 'value' => '$data->getCurrency()',
            'filter'=>CHtml::dropDownList('Billing[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt'=>$this->t('All'))),),

        array('name' => 'dpt_id', 'value' => '$data->getDptName()',
                'filter'=>CHtml::dropDownList('Billing[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt'=>$this->t('All'))),),
        'weight',
        'charge_weight',
        'date',
        'accrual_amount',
        array('name' => 'actual_amount','class' => 'CEditableColumn'),
        array(
            'class'=>'CEditableButtonColumn',
            'template'=>'{edit} {cancel} {save}',
        ),
    ),
)); ?>


<?php $this->endWidget(); ?>

<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');

        tab.bind('onOpen', function(){
            $('#export-billing-op-grid', panel).yiiGridView('update');
        });

        $('form#export-billing-op-form', panel).on('success', function(e, r){
            $('#export-billing-op-grid', panel).yiiGridView('update');
            return true;
        });

        $('.search-button', panel).click(function(){
            $('.search-form', panel).toggle();
            return false;
        });


        $('.search-button-done', panel).click(function(e){
            $('#export-billing-op-grid', panel).yiiGridView('update', {data: $('.filters input, .filters select', panel).serialize() + '&' + $('.search-form form').serialize()});
            return false;
        });


        $('form#export-billing-op-form',panel).on('click','.select-on-check-all',function(e){
            var selectedMe = $(this).prop('checked');
            if ( selectedMe ) {
                var subTotal = 0;
                $('.select-on-check',panel).each(function(e){
                    var pitem = $(this).parent().parent();
                    var actualAmount = parseFloat(pitem.find('td:nth-child(13)').find('span').html());
                    if ( isNaN(actualAmount)) actualAmount = 0;
                    subTotal += actualAmount;
                });
                if ( subTotal < 0 )  subTotal = 0;
                $('#sub-op-total',panel).attr('data-total',subTotal);
                subTotal = myApp.formatNumber(subTotal,2, '.', ',');

                $('#sub-op-total',panel).val(subTotal);
            } else {
                $('#sub-op-total',panel).attr('data-total',0);
                $('#sub-op-total',panel).val(0);
            }
        });


        $('form#export-billing-op-form',panel).on('click','tr',function(e){

            if ( e.target.className == 'button-column' ) return;

            var selectedMe = $(this).find('.select-on-check').prop('checked');

            if ( e.target.className != 'select-on-check' ) {
                selectedMe = !selectedMe;
            }

            var actualAmount = parseFloat($(this).find('td:nth-child(13)').find('span').html());
            if ( isNaN(actualAmount)) actualAmount = 0;
            var subTotal = parseFloat($('#sub-op-total',panel).attr('data-total'));
            if ( isNaN(subTotal)) subTotal = 0;
            if ( selectedMe ) {
                subTotal += actualAmount;
            } else {
                subTotal -= actualAmount;
            }
            if ( subTotal < 0 )  subTotal = 0;
            $('#sub-op-total',panel).attr('data-total',subTotal);
            subTotal = myApp.formatNumber(subTotal,2, '.', ',');

            $('#sub-op-total',panel).val(subTotal);
        });


    });
</script>