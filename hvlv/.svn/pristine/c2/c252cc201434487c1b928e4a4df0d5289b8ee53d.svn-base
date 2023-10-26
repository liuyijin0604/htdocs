
<h1>EDI Job Template</h1>
<?php
    $templateJob = EdiJobTemplate::model()->find('id = :oid' , [':oid' => $model->id]);
    if ( empty($templateJob) ) $templateJob = new EdiJobTemplate();
?>
<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'edi-job-template-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <div class="row rowcol">
        <?php echo $form->labelEx($templateJob,'name'); ?>
        <?php echo $form->textField($templateJob,'name', ['size' => '12']); ?>
    </div>

    <?php if ( $templateJob->isNewRecord ) : ?>
        <div class="row rowcol">
            <?php echo CHtml::label('Basic Template','for_basic_tempate'); ?>
            <?php echo CHtml::dropDownList('btid', 'Select One', EdiJobBasicTemplate::getTemplatesList()); ?>
        </div>
        <div class="row rowcol"><button style="margin-top: 15px;" id="load-edijob-template">Load Template</button></div>
    <?php endif; ?>
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
                        'id' => 'id_edijob_invoice_template',
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
                        'id' => 'id_edijob_cost_template',
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
            'id' => 'edi-job-invoice-template-grid',
            'cssFile' => false,
            'dataProvider'=> $linesInvoiceProvider,
            'formUrl' => $this->createUrl('org/EdiInvoiceLinesGrid', array('id' => 0 )),
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
            'id' => 'edi-job-cost-template-grid',
            'cssFile' => false,
            'dataProvider'=> $linesCostProvider,
            'formUrl' => $this->createUrl('org/EdiCostLinesGrid', array('id' => 0 )),
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

    <input name="invoice_dels" id="edi-invoice-template-dels" type="hidden" value="">
    <input name="cost_dels" id="edi-cost-template-dels" type="hidden" value="">
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
        var win = $("#jqmw_<?=$_GET['tabid'];?>");
        var panel = win;

        function addOneLine2Cost(data){
            var addBtn = $('#edi-job-cost-template-grid .add_btn',panel);
            $('#edi-job-cost-template-grid .items tbody td.empty',panel).parent().remove();
            var r = addBtn.parents('tr').clone();
            $('.add_btn',r).replaceWith('<a href="" class="delete_btn" title="Remove">Remove</a>');

            $('input', r).each(function(){
                var n = $(this).attr('name');
                $(this).attr('name', n + '[]');
            });
            $('select', r).each(function(){
                var n = $(this).attr('name');
                $(this).attr('name', n+'[]');
            });
            $('#edi-job-cost-template-grid .items tbody',panel).append(r);
            addBtn.parents('tr').find('input').val('');

            // set related data
            $('select[name="BillingLine[item_code][]"]',r).val(data.item_code);
            $('#edi-job-cost-template-grid_desc',r).val(data.desc);
            $('#edi-job-cost-template-grid_qty',r).val(data.qty);
            $('#edi-job-cost-template-grid_price',r).val(data.price);
            $('#edi-job-cost-template-grid_org_id',r).val(data.org_id);
            $('input.egacol_org_id',r).val(data.supplier_name);
            $('select[name="BillingLine[gst][]"]',r).val(data.gst);
        }

        function addOneLine2Invoice(data){
            var addBtn = $('#edi-job-invoice-template-grid .add_btn',panel);
            $('#edi-job-invoice-template-grid .items tbody td.empty',panel).parent().remove();
            var r = addBtn.parents('tr').clone();
            $('.add_btn',r).replaceWith('<a href="" class="delete_btn" title="Remove">Remove</a>');

            $('input', r).each(function(){
                var n = $(this).attr('name');
                $(this).attr('name', n + '[]');
            });
            $('select', r).each(function(){
                var n = $(this).attr('name');
                $(this).attr('name', n+'[]');
            });
            $('#edi-job-invoice-template-grid .items tbody',panel).append(r);
            addBtn.parents('tr').find('input').val('');

            // set related data
            $('select[name="JobLine[ccode][]"]',r).val(data.ccode);
            $('#edi-job-invoice-template-grid_desc',r).val(data.desc);
            $('#edi-job-invoice-template-grid_qty',r).val(data.qty);
            $('#edi-job-invoice-template-grid_rate',r).val(data.rate);
            $('select[name="JobLine[inv_gst][]"]',r).val(data.inv_gst);

        }

        $('#edi-job-template-form', win).on('success', function(){
            win.data('opener').trigger('onOpen');
            win.jqmHide();
        });

        $('#edi-job-invoice-template-grid .add_btn',panel).on('click', function(){
            $('#edi-job-invoice-template-grid .items tbody td.empty',panel).parent().remove();
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

            $('#edi-job-invoice-template-grid .items tbody',panel).append(r);
            $(this).parents('tr').find('input').val('');
            return false;
        });

        $('#edi-job-invoice-template-grid',panel).on('click','.delete_btn', function(e){

            if ( confirm( ' Are you sure you want to delete the item?') ) {
                var lineId = parseInt( $(this).parent().find('input[name="JobLine[id]"]').val() ) % 100;
                $(this).parent().parent().remove();
                var dels = $('#edi-invoice-template-dels',panel).val();
                if ( dels.length > 0 ) dels += '-';
                dels += lineId.toString();
                $('#edi-invoice-template-dels',panel).val(dels);
            }
            e.preventDefault();
            e.stopPropagation();
        });


        $('#load-edijob-template',panel).click(function(e){

            e.preventDefault();
            e.stopPropagation();
            var tid = $('#btid',panel).val();
            if ( tid <= 0 ) {
                alert('please select one template!');
                $('#btid',panel).focus();
            } else {
                var data = { 'tid' : tid};
                $.ajax({
                    type : 'POST',
                    url : '<?php echo Yii::app()->createAbsoluteUrl("org/ajaxGetEdiBasicTemplate") ;?>',
                    data: data,
                    dataType: 'json',
                    success:function(resp){
                        if ( resp.success == 1 ) {
                            $('#edi-job-cost-template-grid .items tbody',panel).empty();
                            $('#edi-job-invoice-template-grid .items tbody',panel).empty();
                            $('#EdiJobTemplate_name',panel).val(resp.name);
                            var invoices = resp.data.invoice;
                            for ( var i = 0 ; i < invoices.length; i++ ) {
                                addOneLine2Invoice(invoices[i]);
                            }
                            var costs = resp.data.cost;
                            for ( var i = 0 ; i < costs.length; i++ ) {
                                addOneLine2Cost(costs[i]);
                            }
                        } else {
                            alert('Sorry, failed to get related template')
                        }
                    }
                });
            }
        });

        $('#edi-job-cost-template-grid .add_btn',panel).on('click', function(){
            $('#edi-job-cost-template-grid .items tbody td.empty',panel).parent().remove();
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

            $('#edi-job-cost-template-grid .items tbody',panel).append(r);
            $(this).parents('tr').find('input').val('');
            return false;
        });

        $('#edi-job-cost-template-grid',panel).on('click','.delete_btn', function(e){
            if ( confirm( ' Are you sure you want to delete the item?') ) {
                var lineId = parseInt( $(this).parent().find('input[name="BillingLine[id]"]').val() ) % 100;
                $(this).parent().parent().remove();
                var dels = $('#edi-cost-template-dels',panel).val();
                if ( dels.length > 0 ) dels += '-';
                dels += lineId.toString();
                $('#edi-cost-template-dels',panel).val(dels);
            }
            e.preventDefault();
            e.stopPropagation();
        });

    });
</script>
