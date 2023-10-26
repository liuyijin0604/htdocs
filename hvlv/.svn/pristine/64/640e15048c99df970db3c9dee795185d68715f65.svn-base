<div class="row grid-view">

    <div class="form">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'report_consol_form',
        ));
        ?>

        <div class="row rowcol">
            <?php echo CHtml::label('From', 'From'); ?>
            <?php echo CHtml::textField('from', date("Y-m-d", strtotime("-3 day")), array('size' => 12, 'id' => 'from', 'class' => 'date_input')); ?>
        </div>
        <div class="row rowcol">
            <?php echo CHtml::label('To', 'To'); ?>
            <?php echo CHtml::textField('to', date("Y-m-d"), array('size' => 12, 'id' => 'to', 'class' => 'date_input')); ?>
        </div>
        <br />
        <div class="row rowcol">
            <?php echo CHtml::label('Customer', 'Customer'); ?>
            <!-- <?php echo CHtml::textField('customer', ''); ?> -->
            <?php
            $this->widget('zii.widgets.jui.CJuiAutoComplete', [
                'name' => 'customer',
                'sourceUrl' => ['org/ACRSuggest'],
                'value' => '',
                'options' => [
                    'showAnim' => 'fold',
                    'minLength' => 2,
                    'delay' => 200,
                    'select' => 'js:function(event, ui){ $(this).val(ui.item["label"]); $(this).parent().parent().find("#mdata_owner_id").val(ui.item["value"]); return false; }',
                    'change' => 'js:function(event, ui){ if(ui.item == null) $(this).prevAll("input[type=hidden]").val(""); return false; }',
                ],
                'htmlOptions' => [
                    'size' => '50',
                ],
            ]);
            ?>
        </div>
        <br />
        <div class="row rowcol">
            <?php echo CHtml::label('Delivery Type', 'Delivery Type'); ?>
            <?php
            echo CHtml::dropDownList('delivery_type', 'ALL',[''=>'ALL']+CDeliveryType::listDeliveryType);
            ?>
        </div>
        <div class="row rowcol">
            <?php echo CHtml::label('Department', 'Department');
            $dicOtid2Department = [
                '' => 'ALL',
                '0' => 'Imports',
                '10' => '3PL',
            ];
            echo CHtml::dropDownList('department', 'ALL', $dicOtid2Department);
            ?>
        </div>
        <br />
        <div class="row rowcol">
            <?php echo CHtml::label('Deport', 'Deport'); ?>
            <?php
            $dicPort2State = [
                '' => 'ALL',
                Org::PCAE_DEPARTMENT_SYDNEY => 'Sydney',
                Org::PCAE_DEPARTMENT_MELBOURNE => 'Melbourne',
                Org::PCAE_DEPARTMENT_BRISBANE => 'Brisbane',
                Org::PCAE_DEPARTMENT_PERTH => 'Perth',
                Org::PCAE_DEPARTMENT_ADELAIDE => 'Adelaide',
                Org::PCAE_DEPARTMENT_FREMANTLE => 'FREMANTLE',
            ];
            echo CHtml::dropDownList('deport', 'ALL', $dicPort2State);
            ?>
        </div>


        <?php $this->endWidget(); ?>

        <div class="row buttons">
            <input id="btn_search" type="button" value="Search" onclick="funcSearch()" style="cursor:pointer"/>
            <a id="btn_excel"  onclick="funcExcel()" style="margin-left: 50px;cursor:pointer" >Export Excel</a>
        </div>
    </div>
    <br />
    <!-- background: url(images/loading.gif) no-repeat; -->

    <div id='div_loading' class="grid-view grid-view-loading" style="display: none;"></div>
    <h1>Imparcel Report</h1>
    <table class="items">
        <thead>
            <tr>
                <th>Dept</th>
                <th>Depot</th>
                <th>Courier</th>
                <th>Hbn</th>
                <th>Ref</th>
                <th>org</th>
                <th>Shipment Create</th>
                <th>ETA</th>
                <th>Clearance</th>
                <th>Check-in</th>
                <th>Gate-Out</th>
                <th>Handover to Courier</th>
                <th>Delivered</th>
                <th>Clearance Time</th>
                <th>Despatch Time</th>
                <th>To Courier Time</th>
                <th>Delivery Time</th>
            </tr>
        </thead>
        <tbody id="table_records">
            <?php
            echo $strTbody;
            ?>

        </tbody>
    </table>
</div>


<script type="text/javascript">
    function funcSearch() {
        $('#btn_search').attr('disabled', true);
        $('#div_loading').attr('style', '');
        setTimeout(() => {
            listData = $('#report_consol_form').serializeArray();
            htmlobj = $.ajax({
                url: "/imParcel/imparcelReport",
                data: listData,
                async: false
            });
            $('#table_records').html(htmlobj.responseText);
            $('#btn_search').attr('disabled', false);
            $('#div_loading').attr('style', 'display:none');
        }, 0);
    }
    
    function funcExcel() {
        setTimeout(() => {
            strQueryString = $('#report_consol_form').serialize();
            strUrl = "/imParcel/shipmentReportExcel?"+strQueryString;
            window.open(strUrl, "_blank");
        }, 0);
    }
</script>