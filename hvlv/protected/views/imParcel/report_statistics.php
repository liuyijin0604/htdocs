<style>
    #divModal{
        width: 80%;
        /* height: 650px; */
        background-color: #EBF0FA;
        position: Fixed;
        left: 10%;
        top: 100px;
        border: 1px solid black;
        padding: 12px;
        max-height: 700px;
        overflow-y:auto;
    }

    .display_none{
        display: none;
    }
</style>

<div class="row grid-view">
    <div class="form">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'form_report_statistics',
        ));
        ?>

        <div class="row rowcol">
            <?php echo CHtml::label('Date', 'date'); ?>
            <?php echo CHtml::textField('date', date("Y-m-d"), array('size' => 12, 'id' => 'to', 'class' => 'date_input')); ?>
        </div>


        <?php $this->endWidget(); ?>

        <div class="row buttons">
            <input id="btn_search" type="button" value="Search" onclick="funcSearch()" style="cursor:pointer"/>
        </div>
    </div>
    <br />

    <div id='div_loading' class="grid-view grid-view-loading" style="display: none;"></div>
    <h3>清关时效</h3>
    <table class="items">
        <thead>
            <tr>
                <th></th>
                <th>早于ETA(N列等于0)</th>
                <th>晚于ETA(N列大于0)</th>
                <th>晚于ETA超过2天(N列为空)</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_clearance">

        </tbody>
    </table>
    <br/>

    <h3>交快递时效-AUPOST</h3>
    <table class="items">
        <thead>
            <tr>
                <th></th>
                <th>当天交(O列等于0)</th>
                <th>超过1天交(O列>1)</th>
                <th>未交快递(O列为空)</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_dispatch_aupost">

        </tbody>
    </table>
    <br/>
    <h3>交快递时效-FASTWAY</h3>
    <table class="items">
        <thead>
            <tr>
                <th></th>
                <th>当天交(O列等于0)</th>
                <th>超过1天交(O列>1)</th>
                <th>未交快递(O列为空)</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_dispatch_fastway">

        </tbody>
    </table>
    <br/>
    <h3>交快递时效-TOLL</h3>
    <table class="items">
        <thead>
            <tr>
            <th></th>
                <th>当天交(O列等于0)</th>
                <th>超过1天交(O列>1)</th>
                <th>未交快递(O列为空)</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_dispatch_toll">

        </tbody>
    </table>
    <br/>
    <h3>交快递时效-ALLIED</h3>
    <table class="items">
        <thead>
            <tr>
                <th></th>
                <th>当天交(O列等于0)</th>
                <th>超过1天交(O列>1)</th>
                <th>未交快递(O列为空)</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_dispatch_allied">

        </tbody>
    </table>
    <br/>

    <h3>快递上网时效-AUPOST</h3>
    <table class="items">
        <thead>
            <tr>
                <th></th>
                <th>当天上网(P列等于0)</th>
                <th>超过一天上网(p列>1)</th>
                <th>未交快递(P列为空)</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_to_courier_aupost">

        </tbody>
    </table>
    <br/>
    <h3>快递上网时效-FASTWAY</h3>
    <table class="items">
        <thead>
            <tr>
                <th></th>
                <th>当天上网(P列等于0)</th>
                <th>超过一天上网(p列>1)</th>
                <th>未交快递(P列为空)</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_to_courier_fastway">

        </tbody>
    </table>
    <br/>
    <h3>快递上网时效-TOLL</h3>
    <table class="items">
        <thead>
            <tr>
                <th></th>
                <th>当天上网(P列等于0)</th>
                <th>超过一天上网(p列>1)</th>
                <th>未交快递(P列为空)</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_to_courier_toll">

        </tbody>
    </table>
    <br/>
    <h3>快递上网时效-ALLIED</h3>
    <table class="items">
        <thead>
            <tr>
                <th></th>
                <th>当天上网(P列等于0)</th>
                <th>超过一天上网(p列>1)</th>
                <th>未交快递(P列为空)</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_to_courier_allied">

        </tbody>
    </table>
    <br/>

    <h3>派送时效-AUPOST</h3>
    <table class="items">
        <thead>
            <tr>
                <th></th>
                <th>1天</th>
                <th>3天</th>
                <th>5天</th>
                <th>5天以上</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_delivery_aupost">

        </tbody>
    </table>
    <br/>
    <h3>派送时效-FASTWAY</h3>
    <table class="items">
        <thead>
            <tr>
                <th></th>
                <th>1天</th>
                <th>3天</th>
                <th>5天</th>
                <th>5天以上</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_delivery_fastway">
            
        </tbody>
    </table>
    <br/>
    <h3>派送时效-TOLL</h3>
    <table class="items">
        <thead>
            <tr>
                <th></th>
                <th>1天</th>
                <th>3天</th>
                <th>5天</th>
                <th>5天以上</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_delivery_toll">
            
        </tbody>
    </table>
    <br/>
    <h3>派送时效-ALLIED</h3>
    <table class="items">
        <thead>
            <tr>
                <th></th>
                <th>1天</th>
                <th>3天</th>
                <th>5天</th>
                <th>5天以上</th>
                <th>总计</th>
            </tr>
        </thead>
        <tbody id="tbody_delivery_allied">
            
        </tbody>
    </table>
    <br/>


</div>

<div id="divModal" class="display_none">
    <button onclick="funcCloseModal()" style="float:right;">Close</button>
    <br/>
    <table class="items" border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th>Dept</th>
                <th>Depot</th>
                <th>Courier</th>
                <th>Hbn</th>
                <th>Ref</th>
                <th>org</th>
                <th>Shipment Create</th>
                <th style="min-width: 100px;">ETA</th>
                <!-- <th>Clearance</th>
                <th>Check-in</th>
                <th>Gate-Out</th>
                <th>Handover</th>
                <th>to Courier</th>
                <th>Delivered</th>	 -->
                <th id="thTime"></th>
            </tr>
        </thead>
        <tbody id="tbody_details">

        </tbody>
    </table>

</div>


<script type="text/javascript">
    var listRecord = [];

    function funcSearch() {
        $('#btn_search').attr('disabled', true);
        $('#div_loading').attr('style', '');
        setTimeout(() => {
            listData = $('#form_report_statistics').serializeArray();
            htmlobj = $.ajax({
                url: "/imParcel/reportStatistics",
                data: listData,
                async: false
            });

            $('#btn_search').attr('disabled', false);
            $('#div_loading').attr('style', 'display:none');

            obj = JSON.parse(htmlobj.responseText);
            if(obj.isSuccess){
                $('#tbody_clearance').html(obj.objResult.strHtmlClearance);

                $('#tbody_dispatch_aupost').html(obj.objResult.dicCourier2StrHtmlDispatch.AuPost);
                $('#tbody_dispatch_fastway').html(obj.objResult.dicCourier2StrHtmlDispatch["Fast Way"]);
                $('#tbody_dispatch_toll').html(obj.objResult.dicCourier2StrHtmlDispatch.Toll);
                $('#tbody_dispatch_allied').html(obj.objResult.dicCourier2StrHtmlDispatch.Allied);

                $('#tbody_to_courier_aupost').html(obj.objResult.dicCourier2StrHtmlToCourier.AuPost);
                $('#tbody_to_courier_fastway').html(obj.objResult.dicCourier2StrHtmlToCourier["Fast Way"]);
                $('#tbody_to_courier_toll').html(obj.objResult.dicCourier2StrHtmlToCourier.Toll);
                $('#tbody_to_courier_allied').html(obj.objResult.dicCourier2StrHtmlToCourier.Allied);
                
                $('#tbody_delivery_aupost').html(obj.objResult.dicCourier2StrHtmlDelivery.AuPost);
                $('#tbody_delivery_fastway').html(obj.objResult.dicCourier2StrHtmlDelivery["Fast Way"]);
                $('#tbody_delivery_toll').html(obj.objResult.dicCourier2StrHtmlDelivery.Toll);
                $('#tbody_delivery_allied').html(obj.objResult.dicCourier2StrHtmlDelivery.Allied);

                listRecord = obj.listRecord;

                myApp.notice('success', 5000);
            }

        }, 0);
    }

    function funcShowDetailsClearance(strDate,isNull,isE0,isM0){
        // Clearance Time	
        //     Despatch Time	
        //     To Courier Time	
        //     Delivery Time

        $('#thTime').html('Clearance Time');

        strHtml ='';
        for (i=0;i<listRecord.length;i++) {
            objCurrent = listRecord[i];

            listDeliveryType = [
                '<?=CDeliveryType::type_aupost?>',
                '<?=CDeliveryType::type_fastway?>',
                '<?=CDeliveryType::type_toll?>',
                '<?=CDeliveryType::type_allied?>',
            ];
            if(listDeliveryType.indexOf( objCurrent.delivery_type)==-1){
                continue;
            }
            if(objCurrent.created == strDate){
                if(isNull && objCurrent.days_eta_cleatance === ''){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_eta_cleatance);
                }
                else if(isE0 && objCurrent.days_eta_cleatance ==0 && objCurrent.days_eta_cleatance !== ''){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_eta_cleatance);
                }
                else if(isM0 && objCurrent.days_eta_cleatance >0){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_eta_cleatance);
                }
            }
        }

        $('#tbody_details').html(strHtml);
        $("#divModal").removeClass("display_none");

    }

    function funcShowDetailsDispatch(strDate,strDeliveryType,isNull,isE0,isM1){
        // Clearance Time	
        //     Despatch Time	
        //     To Courier Time	
        //     Delivery Time

        $('#thTime').html('Despatch Time');

        strHtml ='';
        for (i=0;i<listRecord.length;i++) {
            objCurrent = listRecord[i];

            listDeliveryType = [
                strDeliveryType,
            ];
            if(listDeliveryType.indexOf( objCurrent.delivery_type)==-1){
                continue;
            }

            if(objCurrent.created == strDate){
                if(isNull && objCurrent.days_scan_sorted === ''){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_scan_sorted);
                }
                else if(isE0 && objCurrent.days_scan_sorted ==0 && objCurrent.days_scan_sorted !== ''){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_scan_sorted);
                }
                else if(isM1 && objCurrent.days_scan_sorted >1){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_scan_sorted);
                }
            }
        }

        $('#tbody_details').html(strHtml);
        $("#divModal").removeClass("display_none");
    }

    function funcShowDetailsToCourier(strDate,strDeliveryType,isNull,isE0,isM1){

        $('#thTime').html('To Courier Time');

        strHtml ='';
        for (i=0;i<listRecord.length;i++) {
            objCurrent = listRecord[i];

            listDeliveryType = [
                strDeliveryType,
            ];
            if(listDeliveryType.indexOf( objCurrent.delivery_type)==-1){
                continue;
            }

            if(objCurrent.created == strDate){
                if(isNull && objCurrent.days_sorted_handover === ''){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_sorted_handover);
                }
                else if(isE0 && objCurrent.days_sorted_handover ==0 && objCurrent.days_sorted_handover !== ''){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_sorted_handover);
                }
                else if(isM1 && objCurrent.days_sorted_handover >1){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_sorted_handover);
                }
            }
        }

        $('#tbody_details').html(strHtml);
        $("#divModal").removeClass("display_none");
    }


    function funcShowDetailsDelivery(strDate,strDeliveryType,isE1,isM1le3,isM3le5,isM5){

        $('#thTime').html('Delivery Time');

        strHtml ='';
        for (i=0;i<listRecord.length;i++) {
            objCurrent = listRecord[i];

            listDeliveryType = [
                strDeliveryType,
            ];
            if(listDeliveryType.indexOf( objCurrent.delivery_type)==-1){
                continue;
            }

            if(objCurrent.created == strDate){
                if(isE1 && objCurrent.days_sorted_done ==1){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_sorted_done);
                }
                else if(isM1le3 && objCurrent.days_sorted_done >1 && objCurrent.days_sorted_done <=3){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_sorted_done);
                }
                else if(isM3le5 && objCurrent.days_sorted_done >3 && objCurrent.days_sorted_done <=5){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_sorted_done);
                }
                else if(isM5 && (objCurrent.days_sorted_done >5  || objCurrent.days_sorted_done==='')){
                    strHtml += funcHtemRow(objCurrent,objCurrent.days_sorted_done);
                }
            }
        }

        $('#tbody_details').html(strHtml);
        $("#divModal").removeClass("display_none");
    }

    function funcHtemRow(objCurrent,strDays){
        strHtml ='<tr>';
            strHtml += '<td>' +objCurrent.department + '</td>';
			strHtml += '<td>' +objCurrent.depot+ '</td>';
			strHtml += '<td>' +objCurrent.delivery_type + '</td>';
			strHtml += '<td>' + objCurrent.hbn+ '</td>';
			strHtml += '<td>' + objCurrent.ref + '</td>';
			strHtml += '<td>' + objCurrent.customer_name + '</td>';

            strHtml += '<td>' + objCurrent.created + '</td>';
			strHtml += '<td>' + objCurrent.eta + '</td>';
			// strHtml += '<td>' + objCurrent.clearance + '</td>';
			// strHtml += '<td>' + objCurrent.scan_time + '</td>';
			// strHtml += '<td>' + objCurrent.sorted + '</td>';
			// // strHtml += '<td>' + objCurrent.dispatch+ '</td>';
			// strHtml += '<td>' + objCurrent.handover + '</td>';
			// strHtml += '<td>' + objCurrent.done + '</td>';
            strHtml += '<td>' + strDays +'</td>';
			// strHtml += '<td>' + objCurrent.days_eta_cleatance + '</td>';
			// strHtml += '<td>' + objCurrent.days_scan_sorted + '</td>';
			// strHtml += '<td>' + objCurrent.days_sorted_handover + '</td>';
			// // strHtml += '<td>' + objCurrent.days_handover_done + '</td>';
			// strHtml += '<td>' + objCurrent.days_sorted_done + '</td>';
        strHtml +='</tr>';

        return strHtml
    }

    function funcCloseModal(){
        $("#divModal").addClass("display_none");

    }

</script>
