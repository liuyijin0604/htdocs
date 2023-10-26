<h1><?=$this->t('Billing Input');?></h1>


<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'edi-job-billing-input-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <div class="row rowcol">
        <?php echo CHtml::label('Supplier','for_supplier_id'); ?>
        <?php echo CHtml::hiddenField('supplier_id');
        $acname1 = empty($_GET["tabid"])? 'supplier_ac' : $_GET["tabid"].'_supplier_ac';
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => $acname1,
            'sourceUrl' => array('org/supplierSuggest'),
            'options' => array(
                'showAnim' => 'fold',
                'minLength' => 2,
                'delay' => 200,
                'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]).trigger("change");updateawb(ui.item["value"]); return false; }',
                'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
            ),
            'htmlOptions' => array(
                'size' => '30',
            ),
        ));
        ?>
    </div>

    <div class="row" style="width: 70%">
        <?php
        $il = new JobLine('search');
        $il->unsetAttributes();
        $il->job_id = 0;

        $this->widget('application.extensions.editablegrid.CEditableGridView', array(
            'id' => 'edi-job-billing-input-grid',
            'cssFile' => false,
            'dataProvider'=> $il->search(),
            'formUrl' => $this->createUrl('ediJob/AwbInvoiceLinesGrid', array('id' => 0 )),
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
                    'filter'=> EdiJob::getAwbBillingGlCodes()),

              //  array('header' => 'Awb','name' => 'awb_no', 'class' => 'CEditableColumn'),
                array('header' => 'Awb','name' => 'awb_no','class' => 'CEditableColumn','type' => 'list','filter'=> array()),
                array('header' => 'Amount','name' => 'cost_amount', 'class' => 'CEditableColumn'),
                array('class'=>'CEditableButtonColumn', 'template' => '{edit} {cancel} {save} {delete}'),
            ),
        ));
        ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Submit'); ?>
    </div>

    <?php $this->endWidget(); ?>
    <div id="result" style="margin: 10px; border: 1px solid;padding:20px 30px; font-weight: bold; font-size: 32px;">
    </div>
</div><!-- form -->
<script type="text/javascript">
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel = tab.data('panel');

    function updateawb(sid){
        if ( sid > 0 ) {
            var data = {'sid': sid};
            $.ajax({
                type: 'POST',
                url: '<?php echo Yii::app()->createAbsoluteUrl("ediJob/ajaxGetSupplierAwbs") ;?>',
                data: data,
                dataType: 'json',
                success: function (resp) {
                    if (resp.success == 1) {
                        var awbObj = $('select[name="JobLine[awb_no]"]');
                        awbObj.find('option').remove();
                        for ( var i = 0 ; i < resp.data.length ; i++  ) {
                            awbObj.append($('<option>',{
                                value: resp.data[i].id,
                                text: resp.data[i].awb
                            }));
                        }
                    } else {
                        alert('Could not find related AWB');
                    }
                }
            });
        }

    }

    $(function(){
        tab.off('reload_tab').on('reload_tab', function(){
            var t = $('.ui-tabs', panel);
            t.tabs('load', t.tabs('option','active'));
        });

        $('#edi-job-billing-input-grid .add_btn',panel).on('click', function(){
            $('#edi-job-billing-input-grid .items tbody td.empty',panel).parent().remove();
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

            $('#edi-job-billing-input-grid .items tbody',panel).append(r);
            $(this).parents('tr').find('input').val('');

            return false;
        });

        $('#edi-job-billing-input-grid',panel).on('click','.delete_btn', function(e){

            if ( confirm( ' Are you sure you want to delete the item?') ) {
                $(this).parent().parent().remove();
            }
            e.preventDefault();
            e.stopPropagation();
            return false;
        });

        $('form#edi-job-billing-input-form', panel).data('custom_success', function(r){

            $('#result', panel).empty();
            $('#result', panel).prepend($('<p>'+r.msg+'</p>').css('color', r.color).fadeIn());
            $('input[type="submit"]',panel).prop('disabled',false);
            return true;
        })


    });
</script>
