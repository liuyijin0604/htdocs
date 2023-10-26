
<h1>EDI Basic Template</h1>
<?php
    $templateJob = EdiJobBasicTemplate::model()->find('id = :oid' , [':oid' => $model->id]);
    if ( empty($templateJob) ) $templateJob = new EdiJobBasicTemplate();
?>
<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'edi-job-basic-template-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <div class="row rowcol">
        <?php echo $form->labelEx($templateJob,'name'); ?>
        <?php echo $form->textField($templateJob,'name', ['size' => '12']); ?>
    </div>

    <div class="row">
        <?php

        if ( !empty($templateJob) && !empty($templateJob->meta) ) {
            $metaData = json_decode($templateJob->meta);
            $lines = $metaData->invoice;
            if ( is_array($lines) && !empty($lines) ) {
                $allLines = array();
                foreach ($lines as $k => $line) {
                    $jobLine = new JobLine();
                    $jobLine->id = $model->id * 100 + $k + 1;
                    $jobLine->rate = $line->rate;
                    $jobLine->desc = $line->desc;
                    $jobLine->qty = $line->qty;
                    $jobLine->ccode = $line->ccode;
                    $jobLine->inv_gst = $line->inv_gst;
                    // in key we get org id and template item line id
                    $allLines[$model->id * 100 + $k] = $jobLine;
                }
                $linesInvoiceProvider = new CArrayDataProvider($allLines,
                    array(
                        'id' => 'id_edijob_basic_invoice_template',
                        'pagination' => array('pageSize' => '30')
                    )
                );
            } else {
                $il = new JobLine('search');
                $il->unsetAttributes();
                $il->job_id = 0;
                $linesInvoiceProvider = $il->search();
            }

            $lines = $metaData->cost;
            if ( is_array($lines) && !empty($lines) ) {
                $allLines = array();
                foreach ($lines as $k => $line) {
                    $billingLine = new BillingLine();
                    $billingLine->id = $model->id * 100 + $k + 1;
                    $billingLine->price = $line->price;
                    $billingLine->desc = $line->desc;
                    $billingLine->qty = $line->qty;
                    $billingLine->org_id = $line->org_id;
                    $billingLine->item_code = $line->item_code;
                    $billingLine->gst = $line->gst;
                    // in key we get org id and template item line id
                    $allLines[$model->id * 100 + $k] = $billingLine;
                }
                $linesCostProvider = new CArrayDataProvider($allLines,
                    array(
                        'id' => 'id_edijob_basic_cost_template',
                        'pagination' => array('pageSize' => '30')
                    )
                );
            } else {
                $ilcost = new BillingLine('search');
                $ilcost->unsetAttributes();
                $ilcost->org_id = -1;
                $linesCostProvider = $ilcost->search();
            }


        } else {
            $il = new JobLine('search');
            $il->unsetAttributes();
            $il->job_id = -1;
            $linesInvoiceProvider = $il->search();

            $ilcost = new BillingLine('search');
            $ilcost->unsetAttributes();
            $ilcost->org_id = -1;
            $linesCostProvider = $ilcost->search();
        }
?>
        <h2>Invoice Template</h2>
        <div style="width: 100%;">
        <?php
        // for invoices grid view
        $this->widget('application.extensions.editablegrid.CEditableGridView', array(
            'id' => 'edi-job-basic-invoice-template-grid',
            'cssFile' => false,
            'dataProvider'=> $linesInvoiceProvider,
            'formUrl' => $this->createUrl('ediJob/EdiBasicInvoiceLinesGrid', array('id' => 0 )),
            'summaryText' => '',
            'afterSave' => "function(r){
			if(r.done == true){
				myApp.notice(r.msg, 5000);
			}else{
				myApp.alert(r.msg, false);
			}
			return r.done;
		}",
            'columns'=>array(
                array('header' => 'Type','name' => 'ccode','class' => 'CEditableColumn','type' => 'list' ,
                    'value' => '$data->getCCodeDesc' ,
                    'filter'=> EdiJob::getChargeItemTypes()),
                array('header' => 'Description','name' => 'desc', 'class' => 'CEditableColumn'),
                array('header' => 'Qty/Kg','name' => 'qty', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class' => 'change-event change-inv-amount']),
                array('header' => 'Rate','name' => 'rate', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class' => 'change-event change-inv-amount']),
                array('header' => 'GST', 'name' => 'inv_gst','class' => 'CEditableColumn', 'value' => '$data->getTaxType()','type' => 'list',
                    'filter'=> Invoice::$InvoiceRevenueTaxRate ),
                array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}'),
            ),
        ));
?>
            </div>

        <h2>Cost Template</h2>
        <div style="width: 100%;">
        <?php
        //  for cost grid view
        $this->widget('application.extensions.editablegrid.CEditableGridView', array(
            'id' => 'edi-job-basic-cost-template-grid',
            'cssFile' => false,
            'dataProvider'=> $linesCostProvider,
            'formUrl' => $this->createUrl('ediJob/EdiBasicCostLinesGrid', array('id' => 0 )),
            'summaryText' => '',
            'afterSave' => "function(r){
			if(r.done == true){
				myApp.notice(r.msg, 5000);
			}else{
				myApp.alert(r.msg, false);
			}
			return r.done;
		}",
            'columns'=>array(
                array('header' => 'Type','name' => 'item_code','class' => 'CEditableColumn','type' => 'list' ,
                    'value' => '$data->getItemcodeDesc' ,
                    'filter'=> EdiJob::getChargeItemTypes()),
                array('header' => 'Description','name' => 'desc', 'class' => 'CEditableColumn'),

                array('header' => 'Supplier','name' => 'org_id','class' => 'CEditableColumn','type' => 'autocomplete' ,
                    'value' => '( empty($data->org_id) || empty($data->cust) ) ? "" : $data->cust->name',
                    'acOptions' => array('source' => 'org/supplierSuggest' )
                ),
                array('header' => 'Qty/Kg','name' => 'qty', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class' => 'change-event change-inv-amount']),
                array('header' => 'Rate','name' => 'price', 'class' => 'CEditableColumn', 'inputOptions' => ['size' => 5,'class' => 'change-event change-inv-amount']),

                array('header' => 'GST', 'name' => 'gst','class' => 'CEditableColumn', 'value' => '$data->getCostTaxType()','type' => 'list',
                    'filter'=> Invoice::$InvoiceCostTaxRate ),
                array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}'),
            ),
        ));
        ?>
            </div>
    </div>

    <input name="invoice_dels" id="edi-basic-invoice-template-dels" type="hidden" value="">
    <input name="cost_dels" id="edi-basic-cost-template-dels" type="hidden" value="">
    <div class="row buttons">

        <?php
        if ( $templateJob->isNewRecord ) {
            echo CHtml::submitButton('Create');
        } else {
            echo CHtml::submitButton('Save');
        }
        ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
    $(function(){

        console.log('debuge me');
        var win = $("#jqmw_<?=$_GET['tabid'];?>");
        $('#edi-job-basic-template-form', win).on('success', function(){
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });

       // var tab = $('#<?=$_GET["tabid"];?>');
        var panel = win;


        $('#edi-job-basic-invoice-template-grid .add_btn',panel).on('click', function(){
            $('#edi-job-basic-invoice-template-grid .items tbody td.empty',panel).parent().remove();
            var r = $(this).parents('tr').clone();
            // $('.add_btn', r).remove();
            $('.add_btn',r).replaceWith('<a href="" class="delete_btn" title="Remove">Remove</a>');

            $('input', r).each(function(){
                var n = $(this).attr('name');
                $(this).attr('name', n+'[]');
            });
            $('select', r).each(function(){
                var n = $(this).attr('name');
                $(this).attr('name', n+'[]');
            });

            $('#edi-job-basic-invoice-template-grid .items tbody',panel).append(r);
            $(this).parents('tr').find('input').val('');
            return false;
        });

        $('#edi-job-basic-invoice-template-grid',panel).on('click','.delete_btn', function(e){

            console.log('debug');
            if ( confirm( ' Are you sure you want to delete the item?') ) {
                var lineId = parseInt( $(this).parent().find('input[name="JobLine[id]"]').val() ) % 100;
                $(this).parent().parent().remove();
                var dels = $('#edi-basic-invoice-template-dels',panel).val();
                if ( dels.length > 0 ) dels += '-';
                dels += lineId.toString();
                $('#edi-basic-invoice-template-dels',panel).val(dels);
            }
            e.preventDefault();
            e.stopPropagation();
        });


        $('#edi-job-basic-cost-template-grid .add_btn',panel).on('click', function(){
            $('#edi-job-basic-cost-template-grid .items tbody td.empty',panel).parent().remove();
            var r = $(this).parents('tr').clone();
            $('.add_btn',r).replaceWith('<a href="" class="delete_btn" title="Remove">Remove</a>');

            $('input', r).each(function(){
                var n = $(this).attr('name');
                $(this).attr('name', n+'[]');
            });
            $('select', r).each(function(){
                var n = $(this).attr('name');
                $(this).attr('name', n+'[]');
            });

            $('#edi-job-basic-cost-template-grid .items tbody',panel).append(r);
            $(this).parents('tr').find('input').val('');
            return false;
        });

        $('#edi-job-basic-cost-template-grid',panel).on('click','.delete_btn', function(e){

            console.log('debug');
            if ( confirm( ' Are you sure you want to delete the item?') ) {
                var lineId = parseInt( $(this).parent().find('input[name="BillingLine[id]"]').val() ) % 100;
                $(this).parent().parent().remove();
                var dels = $('#edi-basic-cost-template-dels',panel).val();
                if ( dels.length > 0 ) dels += '-';
                dels += lineId.toString();
                $('#edi-basic-cost-template-dels',panel).val(dels);
            }
            e.preventDefault();
            e.stopPropagation();
        });

    });
</script>
