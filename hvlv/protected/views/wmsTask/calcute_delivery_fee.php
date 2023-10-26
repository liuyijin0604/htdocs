<style>
    .rowcol_200 {
        display: inline-block;
        width: 300px;
    }
    .display_none {
        display: none;
    }
</style>
<h1> Calcute Delivery Fee</h1>
<div class="form">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'wms-taskfee-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => ['target' => 'err_result', 'class' => 'ifrm-form', 'enctype' => 'multipart/form-data'],
        //'action' => $this->createUrl('wmsTask/calcuteFee', ['modelType' => $modelType]),
    ));
    ?>
    <div class="row rowcol rowleft">
        <?php echo CHtml::label('Charge Code', 'charge_code'); ?>
        <?php echo CHtml::textField('charge_code', '', ['id' => 'charge_code']); ?>
    </div>
    <div class="row rowcol rowleft">
        <?php echo CHtml::label('Postcode', 'post_code'); ?>
        <?php echo CHtml::textField('post_code', ''); ?>
    </div>
    <div class="row rowcol">
        <?php echo CHtml::label('Weight (Kg)', 'wieght'); ?>
        <?php echo CHtml::textField('weight', ''); ?>
    </div>
    <div class="row rowcol rowleft">
        <?php echo CHtml::label('Length (cm)', 'length'); ?>
        <?php echo CHtml::textField('length', ''); ?>
    </div>
    <div class="row rowcol">
        <?php echo CHtml::label('Width (cm)', 'width'); ?>
        <?php echo CHtml::textField('width', ''); ?>
    </div>
    <div class="row rowcol">
        <?php echo CHtml::label('Height (cm)', 'height'); ?>
        <?php echo CHtml::textField('height', ''); ?>
    </div>
    <div class="row rowcol rowleft">
        <?php echo CHtml::label('SurCharge Type', 'courier'); ?>
        <?php echo CHtml::dropDownList('courier', 1, [1=>'Aupost/Fastway',114 => 'TLA', 3079 => 'UBI Toll', 3701 => 'Eiz Toll', 3702 => 'Eiz Allied']); ?>
    </div>

    <br>
    <br>
    <input id="submit_btn" type="button" value="Search" onclick="getFee()" />

    <div id="divSummary" class="display_none">
        <div class="form ml_15">
            <br />
            <div class="row">
                <h4>Total Chargeable Weight 总重量: <span id="spanTotalWeight"></span></h4>
                <!--<h4>Total Quantity 总数量: <span id="spanTotalQty"></span></h4>-->
            </div>
            <br />
            <br />

            <div class="row">
                <div class="rowcol_200">
                    <h4>Delivery Fee 派送费</h4>
                </div>
            </div>
            <div class="row">
                <div class="rowcol_200">
                    <h5> -ChargeCode </h5>
                </div>
                <div id="divChargecode" class="rowcol_200"></div>
            </div>
            <div class="row">
                <div class="rowcol_200">
                    <h5> -Base delivery fee </h5>
                </div>
                <div id="divBase" class="rowcol_200"></div>
            </div>
            <div class="row">
                <div class="rowcol_200">
                    <h5> -Surcharge fee </h5>
                </div>
                <div id="divSurcharge" class="rowcol_200"></div>
            </div>
            <div class="row">
                <div class="rowcol_200">
                    <h4>Total 小计</h4>
                </div>
                <div id="divTotal" class="rowcol_200"></div>
            </div>
        </div>
        <br />
        <div class="form ml_15">
            <h5>Price doesn't include GST. 本价格不包含GST</h5>
        </div>
    </div>


    <?php $this->endWidget(); ?>
</div>

<br /><br />


<script type="text/javascript">
    $(function() {
        var tab = $('#<?= $_GET["tabid"]; ?>');
        var panel = tab.data('panel');

        $('#ship-import-form', panel).on('submit', function() {
            $("#err_result", panel).contents().find("body").html('');
            $('input[type=submit]', this).prop('disabled', true);
        });

        $('#err_result', panel).load(function() {
            $('#ship-import-form input[type=submit]', panel).prop('disabled', false);
        });
    });

    function getFee() {
        var listData = new FormData();
        var listName2Value = $('#wms-taskfee-form').serializeArray();
        for (var i = 0; i < listName2Value.length; i++) {
            var objName2Value = listName2Value[i];
            listData.append(objName2Value.name, objName2Value.value);
        }
        htmlobj = $.ajax({
            type: "POST",
            url: "<?= $this->createUrl('wmsTask/calcuteFee'); ?>",
            data: listData,
            async: false,
            contentType: false,
            processData: false,
        });
        obj = JSON.parse(htmlobj.responseText);
        if (obj.isSuccess) {
            $("#divSummary").removeClass("display_none");
            $("#divChargecode").html(obj.chargecode);
            $("#spanTotalWeight").html(obj.chargeableweight + " KG");
            $("#divBase").html(funcStrPrice(obj.revenueFee));
            $("#divSurcharge").html(funcStrPrice(obj.surcharge));
            $("#divTotal").html(funcStrPrice(obj.revenueFee + obj.surcharge));

        }
    }

    function funcStrPrice(numPrice) {
        if (numPrice != null && numPrice > 0) {
            if (numPrice >= 99999999) {
                return "超出标准服务范围，暂无报价";
            }
            numPrice = Math.round(numPrice * 100) / 100;
            return "$" + numPrice;
        } else {
            return "0";
        }

    }
</script>