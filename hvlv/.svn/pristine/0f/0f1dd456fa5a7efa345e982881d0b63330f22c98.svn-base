<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'import-client-report-form',
        'enableAjaxValidation'=>false,
        'action' => $this->createUrl('import/AjaxClientReport'),
    ));
    ?>
    <div class="row">
    <div class="rowcol">
        <?php echo CHtml::label('From','forimclient'); ?>
        <?php echo CHtml::textField('from_date',date('Y-m-d',strtotime('-7 days',time())),['class' => 'date_input']); ?>
    </div>
        <div class="rowcol">
            <?php echo CHtml::label('To','forimclient'); ?>
            <?php echo CHtml::textField('to_date',date('Y-m-d'),['class' => 'date_input']); ?>
        </div>
    </div>
    <div class="row">
    <div class="rowcol">
        <?php $client = Org::model(); ?>
        <?php echo CHtml::label('Customer','id'),
        $form->hiddenField($client,'id', array('data-ov' => ''));
        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
            'name' => 'id',
            'sourceUrl' => array('org/clientSuggest'),
            'value' => '',
            'options' => array(
                'showAnim' => 'fold',
                'minLength' => 2,
                'delay' => 200,
                'autoFocus' => true,
                'select' => 'js:function(evt, ui){ $(this).val(ui.item["label"]); $(this).prevAll("input[type=hidden]").val(ui.item["value"]).data("ov",ui.item["value"]); return false; }',
                'change' => 'js:function(evt, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val($(this).prevAll("input[type=hidden]").data("ov")); return false; }',
            ),
            'htmlOptions' => array(
                'size' => '25',
            ),
        ));
        ?>
    </div>
    </div>

    <div class="row">
        <div class="rowcol">
            <?php echo CHtml::label('Console No.','forimclient'); ?>
            <?php echo CHtml::textField('console',''); ?>
        </div>
    </div>



    <div class="row rowcol rowleft">
        <?php echo CHtml::checkBox('forall',0) . ' Query For All'; ?>
    </div>

    <div class="row rowcol rowleft">
        <?php echo CHtml::checkBox('details',0) . ' Show Details'; ?>
    </div>

    <div class="row">
    <input id="import_client_query_btn" type="submit" value="Query" />
    </div>
    <?php $this->endWidget(); ?>
</div>

<div class="grid-view row" style="width: 80%;margin-top: 50px;">
    <label> <h2> Reports </h2></label>
    <table class="items">
        <thead>
        <tr>
            <th>Date</th>
            <th>Customer</th>
            <th>Console No</th>
            <th>Qty</th>
            <th>Weight(kg)</th>
            <th>AWB Weight(kg)</th>
            <th>Charge Weight(kg)</th>
            <th>Invoice(AUD)</th>
            <th>Real Invoice(AUD)</th>
            <th>AccrualCost(AUD)</th>
            <th>Cost(AUD)</th>
            <th>Profit(AUD)</th>
        </tr>
        </thead>
        <tbody id="body-all-report-details">
        </tbody>
    </table>

</div>

<script type="text/javascript">
$(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel = tab.data('panel');

    function addData(data){
        var existingNum = $('#body-all-report-details tr').length;
        var trClassType = 'odd';
        if (existingNum % 2 == 0) trClassType = 'even';
        var dataElements = '<tr class="' + trClassType + '">';
        dataElements += '<td>' + data.date + '</td>';
        dataElements += '<td>' + data.name + '</td>';
        dataElements += '<td>' + data.cno + '</td>';
        dataElements += '<td>' + myApp.formatNumber(data.totQty,2, '.', ',') + '</td>';
        dataElements += '<td>' + myApp.formatNumber(data.totWeight,2, '.', ',') + '</td>';
        dataElements += '<td>' + myApp.formatNumber(data.awbWeight,2, '.', ',') + '</td>';
        dataElements += '<td>' + myApp.formatNumber(data.cgbWeight,2, '.', ',') + '</td>';
        dataElements += '<td>' + myApp.formatNumber(data.totInvoice,2, '.', ',') + '</td>';
        dataElements += '<td></td>';
        dataElements += '<td>' + myApp.formatNumber(data.totAccrualCost,2, '.', ',') + '</td>';
        dataElements += '<td>' + myApp.formatNumber(data.totCost,2, '.', ',') + '</td>';
        dataElements += '<td>' + myApp.formatNumber(data.totProfit,2, '.', ',') + '</td>';
        dataElements += '</tr>';
        var obj = $(dataElements);
        $('#body-all-report-details').append(obj.fadeIn());
    }

    function addOneTotalData(orgName,consoleNo,totQty,totWeight,awbWeight, cgbWeight,totInvoice,totRealInvoice,totAccrualCost, totCost,totProfit){
        var existingNum = $('#body-all-report-details tr').length;
        var trClassType = 'odd';
        if (existingNum % 2 == 0) trClassType = 'even';
        var dataElements = '<tr class="' + trClassType + '" style="font-weight:bold;">';
        dataElements += '<td colspan="2" style="text-align: right;"> ' + orgName + ' -> Total</td>';
        dataElements += '<td>' + consoleNo + '</td>';
        dataElements += '<td>' + myApp.formatNumber(totQty,2, '.', ',') + '</td>';
        dataElements += '<td>' + myApp.formatNumber(totWeight,2, '.', ',') + '</td>';
        dataElements += '<td>' + myApp.formatNumber(awbWeight,2, '.', ',') + '</td>';
        dataElements += '<td>' + myApp.formatNumber(cgbWeight,2, '.', ',') + '</td>';
        dataElements += '<td>' + totInvoice.formatMoney(2, '.', ',') + '</td>';
        dataElements += '<td>' + totRealInvoice.formatMoney(2, '.', ',') + '</td>';
        dataElements += '<td>' + totAccrualCost.formatMoney(2, '.', ',') + '</td>';
        dataElements += '<td>' + totCost.formatMoney(2, '.', ',') + '</td>';
        dataElements += '<td>' + totProfit.formatMoney(2, '.', ',') + '</td>';
        dataElements += '</tr>';
        var obj = $(dataElements);
        $('#body-all-report-details').append(obj.fadeIn());
    }

        $('form#import-client-report-form', panel).data('custom_success', function(r){
            $('#import_client_query_btn', panel).attr('disabled', false);
            $('#body-all-report-details',panel).empty();
            if ( r.success == 1 ) {

                var showDetails = r.details;

                var totQty = 0;
                var totAwbWeight = 0;
                var totCgbWeight = 0;
                var totRealInvoice = 0;
                var totRealProfit = 0;
                var totWeight = 0;
                var totInvoice = 0;
                var totCost = 0 ;
                var totAccrualCost = 0;
                var totProfit = 0;
                for (var i in r.data) {
                    var orgName = '';
                    var tQty = 0;
                    var tWeight = 0;
                    var tAwbWeight = 0;
                    var tCgbWeight = 0;
                    var tInvoice = 0;
                    var tRealInvoice = 0;
                    var tRealProfit = 0;
                    var tCost = 0 ;
                    var tAccrualCost = 0;
                    var tProfit = 0;
                    var consoleNo = '';
                    for ( var j in r.data[i] ) {
                        var st = r.data[i][j];
                        if ( orgName.length == 0 ) orgName = st.name;
                        consoleNo = st.cno;
                        if ( st.name != null && st.name.length > 0 ) {
                            if ( showDetails ) {
                                addData(st);
                            }
                            var tmp = parseInt(st.totQty);
                            if ( !isNaN(tmp) ) tQty += tmp;

                            tmp =  parseFloat(st.awbWeight);
                            if ( !isNaN(tmp) ) tAwbWeight += tmp;

                            tmp =  parseFloat(st.cgbWeight);
                            if ( !isNaN(tmp) ) tCgbWeight += tmp;

                            tmp =  parseFloat(st.totWeight);
                            if ( !isNaN(tmp) ) tWeight += tmp;

                            tmp =  parseFloat(st.totInvoice);
                            if ( !isNaN(tmp) ) tInvoice += tmp;

                            tmp =  parseFloat(st.totAccrualCost);
                            if ( !isNaN(tmp) ) tAccrualCost += tmp;

                            tmp =  parseFloat(st.totCost);
                            if ( !isNaN(tmp) ) tCost += tmp;

                            tmp =  parseFloat(st.totProfit);
                            if ( !isNaN(tmp) ) tProfit += tmp;

                        } else {
                            var tmp =  parseFloat(st.totRealInvoice);
                            if ( !isNaN(tmp) ) tRealInvoice += tmp;

                            tmp =  parseFloat(st.totProfit);
                            if ( !isNaN(tmp) ) tRealProfit += tmp;

                        }

                    }
                    addOneTotalData(orgName,'',tQty,tWeight,tAwbWeight,tCgbWeight,tInvoice.toFixed(2),tRealInvoice.toFixed(2),tAccrualCost.toFixed(2),tCost.toFixed(2),tProfit.toFixed(2));
                    totQty += tQty;
                    totWeight += tWeight;
                    totAwbWeight += tAwbWeight;
                    totCgbWeight += tCgbWeight;
                    totInvoice += tInvoice;
                    totRealInvoice += tRealInvoice;
                    totRealProfit += tRealProfit;
                    totCost += tCost;
                    totAccrualCost += tAccrualCost;
                    totProfit += tProfit;
                }

                addOneTotalData('All Clients','',totQty,totWeight,totAwbWeight,totCgbWeight,totInvoice.toFixed(2),totRealInvoice.toFixed(2),totAccrualCost.toFixed(2),totCost.toFixed(2),totProfit.toFixed(2));

            }
            return true;
        });
    });
</script>