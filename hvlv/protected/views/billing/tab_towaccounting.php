
<h1><?=$this->t('Billing waiting for posting');?></h1>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'tow-billing-accounting-form',
    'enableAjaxValidation'=>false,
)); ?>
<div class="form">
<div class="row">
    <div class="rowcol">
        <?php echo CHtml::label('Transaction Date:','for_acounting'); ?>
        <?php echo CHtml::textField('transaction_date','', ['size' => '12', 'class' => 'date_input']); ?>
    </div>

    <div class="rowcol">
        <?php echo CHtml::label('Invoice Date:','for_acounting'); ?>
        <?php echo CHtml::textField('invoice_date','', ['size' => '12', 'class' => 'date_input']); ?>
    </div>

    <div class="rowcol">
        <?php echo CHtml::label('Invoice Due:','for_acounting'); ?>
        <?php echo CHtml::textField('invoice_due','', ['size' => '12', 'class' => 'date_input']); ?>
    </div>


</div>



    <div class="row">
        <div class="rowcol">
            <?php echo CHtml::label('Sub Total','for_acounting'); ?>
            <?php echo CHtml::textField('sub_total','', ['size' => '12', 'id' => 'sub-total','disabled' => 'disabled','data-subtotal' => 0]); ?>
        </div>

        <div class="rowcol">
            <?php echo CHtml::label('GST','for_acounting'); ?>
            <?php echo CHtml::textField('gst_total','', ['size' => '12', 'id' => 'gst-total','disabled' => 'disabled','data-gsttotal' => 0]); ?>
        </div>

        <div class="rowcol">
            <?php echo CHtml::label('Total','for_acounting'); ?>
            <?php echo CHtml::textField('all_total','', ['size' => '12', 'id' => 'all-total','disabled' => 'disabled','data-alltotal' => 0]); ?>
        </div>

    </div>

<div class="row buttons" style="margin-bottom: 20px;">
    <?php
    echo CHtml::submitButton('Post');
    ?>
</div>

    </div>


<?php
// zii.widgets.grid.CGridView
$this->widget('application.extensions.editablegrid.CEditableGridView', array(
    'id'=>'tow-billing-post-grid',
    'selectableRows' => 2,
    'cssFile' => false,
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'formUrl' => $this->createUrl('billing/updateLineFromAc'),
    'columns'=>array(
        array(
            'id'=>'selectedItems',
            'class'=>'CCheckBoxColumn',
        ),
        //'no',
        array( 'name' => 'billing_ref', 'header' => 'Console No.'),
        'awb',
        array('name'=>'billing_cref','class' => 'CEditableColumn'),
        array('name' => 'op_id', 'value' => '$data->user->name'),
        array('name' => 'client', 'value' => ' ( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->shortName(2)'),
        //  array('name' => 'status', 'value' => '$data->getStatus()',
        //    'filter'=>CHtml::dropDownList('Billing[status]', $model->status, $this->t($model::$states), array('prompt'=>$this->t('All'))),),
        array('name' => 'type', 'value' => '$data->getType()',
            'filter'=>CHtml::dropDownList('Billing[type]', $model->type, $this->t($model::$types), array('prompt'=>$this->t('All'))),),
        array('name' => 'currency', 'value' => '$data->getCurrency()',
            'filter'=>CHtml::dropDownList('Billing[currency]', $model->currency, $this->t(Invoice::$currencies), array('prompt'=>$this->t('All'))),),

        array('name' => 'dpt_id', 'value' => '$data->getDptName()',
            'filter'=>CHtml::dropDownList('Billing[dpt_id]', $model->dpt_id, $this->t(Org::dptList()), array('prompt'=>$this->t('All'))),),
    //    'weight',
    //    'charge_weight',
        'date',
    //    'due',
    //    'accrual_amount',
        array('name' => 'actual_amount','class' => 'CEditableColumn'),

        array('header' => 'GST', 'name' => 'gst','class' => 'CEditableColumn','type' => 'list',
            'filter'=> Invoice::$InvoiceCostTaxRate ),
        //array('name'=>'gst','value' => '$data->getGSTValue()'),// 'htmlOptions' => array('style' => 'display:none;','class' => 'gst_value'),  'headerHtmlOptions' => array('style' => 'display:none')),
        array('name' => 'revenue', 'value' => '$data->getRevenue()'),
        array(
           // 'class'=>'oButtonColumn',
            'class'=>'CEditableButtonColumn',
            'template'=>'{edit} {cancel} {save} {revert}',
            'buttons'=>array
            (
                'revert' => array(
                    'imageUrl'=>false,
                    'options' => array('class' => 'tab_link grid_swap_btn send_back_op', 'label' => 'Revert', 'data-win-class' => 'L'),
                    'visible' => 'true',
                    'url' => 'Yii::app()->createUrl("billing/sendOp", ["fid" => $data->id])',
                    'label' => ''
                ),
            ),
        ),
    ),
)); ?>

<?php $this->endWidget(); ?>


<script type="text/javascript">
    $(function(){
        var tab = $("#<?=$_GET['tabid'];?>");
        var panel = tab.data('panel');

        function addGst(gstTag){
            if ( gstTag == 'OUTPUT' || gstTag == 'INPUT' || gstTag ==  'CAPEXINPUT' || gstTag ==   'GSTONCAPIMPORTS' || gstTag ==  'GSTONIMPORTS') {
                return true;
            }
            return false;
        }

        tab.bind('onOpen', function(){
            $('#tow-billing-post-grid', panel).yiiGridView('update');
        });

        $('form#tow-billing-accounting-form', panel).on('success', function(e, r){
            $('#tow-billing-post-grid', panel).yiiGridView('update');
            return true;
        });

        $('form#tow-billing-accounting-form',panel).on('click','.send_back_op',function(e){
            if ( !confirm('Are your sure send back to operator?') ) {
                e.stopPropagation();
                e.preventDefault();
            }
        });

        $('form#tow-billing-accounting-form',panel).on('click','.select-on-check-all',function(e){
            var selectedMe = $(this).prop('checked');
            if ( selectedMe ) {
                var allTotal = 0;
                var gstTotal = 0;
                var subTotal = 0;
                $('.select-on-check').each(function(e){
                    var pitem = $(this).parent().parent();
                    var actualAmount = parseFloat(pitem.find('td:nth-child(11)').find('span').html());
                    if ( isNaN(actualAmount)) actualAmount = 0;
                    var hasGst = addGst(pitem.find('td:nth-child(12)').find('select[name="BillingLine[gst]"]').val());
                    var gstValue = 0;
                    if ( hasGst )  gstValue = Math.round( actualAmount * 10 / 100 * 100 ) / 100 ;
                    subTotal += actualAmount;
                    gstTotal += gstValue;
                    allTotal += actualAmount;
                    allTotal += gstValue;
                });


                $('#all-total',panel).attr('data-alltotal',allTotal);
                $('#gst-total',panel).attr('data-gsttotal',gstTotal);
                $('#sub-total',panel).attr('data-subtotal',subTotal);


                allTotal = myApp.formatNumber(allTotal,2, '.', ',');
                gstTotal = myApp.formatNumber(gstTotal,2, '.', ',');
                subTotal = myApp.formatNumber(subTotal,2, '.', ',');

                $('#all-total',panel).val(allTotal);
                $('#gst-total',panel).val(gstTotal);
                $('#sub-total',panel).val(subTotal);
            } else {

                $('#all-total',panel).attr('data-alltotal',0);
                $('#gst-total',panel).attr('data-gsttotal',0);
                $('#sub-total',panel).attr('data-subtotal',0);

                $('#all-total',panel).val(0);
                $('#gst-total',panel).val(0);
                $('#sub-total',panel).val(0);
            }
        });

        $('form#tow-billing-accounting-form',panel).on('click','tr',function(e){

            if ( e.target.className == 'button-column' ) return;

            var selectedMe = $(this).find('.select-on-check').prop('checked');

            if ( e.target.className != 'select-on-check' ) {
                selectedMe = !selectedMe;
            }
            var actualAmount = parseFloat($(this).find('td:nth-child(11)').find('span').html());
            if ( isNaN(actualAmount)) actualAmount = 0;

            var hasGst = addGst($(this).find('td:nth-child(12)').find('select[name="BillingLine[gst]"]').val());
            var gstValue = 0;
            if ( hasGst )  gstValue = Math.round( actualAmount * 10 / 100 * 100 ) / 100 ;

            var subTotal = parseFloat($('#sub-total',panel).val());
            if ( isNaN(subTotal)) subTotal = 0;
            var gstTotal = parseFloat($('#gst-total',panel).val());
            if ( isNaN(gstTotal)) gstTotal = 0;
            var allTotal = parseFloat($('#all-total',panel).val());
            if ( isNaN(allTotal)) allTotal = 0;
            if ( selectedMe ) {
                subTotal += actualAmount;
                gstTotal += gstValue;
                allTotal += actualAmount;
                allTotal += gstValue;
            } else {
                subTotal -= actualAmount;
                gstTotal -= gstValue;
                allTotal -= actualAmount;
                allTotal -= gstValue;
            }
            if ( subTotal < 0 ) subTotal = 0;
            if ( gstTotal < 0 ) gstTotal = 0;
            if ( allTotal < 0 ) allTotal = 0;
            $('#all-total',panel).attr('data-alltotal',allTotal);
            $('#gst-total',panel).attr('data-gsttotal',gstTotal);
            $('#sub-total',panel).attr('data-subtotal',subTotal);

            allTotal = myApp.formatNumber(allTotal,2, '.', ',');
            gstTotal = myApp.formatNumber(gstTotal,2, '.', ',');
            subTotal = myApp.formatNumber(subTotal,2, '.', ',');

            $('#all-total',panel).val(allTotal);
            $('#gst-total',panel).val(gstTotal);
            $('#sub-total',panel).val(subTotal);
        });

    });
</script>