<style>
    div.form select {
        width: 500px;
    }

    .width_item_label {
        width: 70px;
        display: inline-block;
        margin-left: 10px;
        margin-bottom: 5px;
    }


    .width_item_input {
        width: 400px;
        margin-left: 25px;
        display: inline-block;
    }

    .width_input {
        width: 500px;
    }

    .width_100 {
        width: 110px;
    }

    .width_400 {
        width: 400px;
        margin-bottom: 8px;
    }


    .rowcol_200 {
        display: inline-block;
        width: 300px;
    }

    .rowcol_500 {
        display: inline-block;
        width: 500px;
    }

    .ml_15 {
        margin-left: 15px;
    }

    .display_none {
        display: none;
    }
</style>



<div>
    <h1>Quick Enquiry(Cost)</h1>

    <div class="form ml_15">
        <form id="form_enquirycost" enctype="multipart/form-data">
            <div id="divItems" class="row rowcol rowleft">
                <br />
                <div>
                    <label>Item 货物</label>
                </div>
                <br />
                <span class="width_item_label">volumn(cbm)</span>
                <input onchange="funcCountItem()" class="width_item_input" type="text" name="lengthcost" id="lengthcost"></input>
                <br />
                <!-- <span class="width_item_label">width(cm)</span>
                <input onchange="funcCountItem()" class="width_item_input" type="text" name="width" id="width"></input>
                <br />
                <span class="width_item_label">height(cm)</span>
                <input onchange="funcCountItem()" class="width_item_input" type="text" name="height" id="height"></input>
                <br /> -->
                <span class="width_item_label">weight(kg)</span>
                <input onchange="funcCountItem()" class="width_item_input" type="text" name="weightcost" id="weightcost"></input>
                <br />
            </div>
            <br />
            <br />
            <div class="row rowcol rowleft">
                <?= CHtml::label('Depot From  始发地 ', 'Depot'); ?><br />
                <span id="Depot">
                    <label class="radio_label" for="Depot_0"><input value="Sydney" id="Depot_0" checked="checked" type="radio" name="Depotcost">&nbspSydney</label>

                    <label class="radio_label" for="Depot_1"><input value="Melbourne" id="Depot_1" type="radio" name="Depotcost">&nbspMelbourne</label>

                    <label class="radio_label" for="Depot_2"><input value="Brisbane" id="Depot_2" type="radio" name="Depotcost">&nbspBrisbane</label>

                    <label class="radio_label" for="Depot_3"><input value="Perth" id="Depot_3" type="radio" name="Depotcost">&nbspPerth</label>

                    <label class="radio_label" for="Depot_4"><input value="Adelaide" id="Depot_4" type="radio" name="Depotcost">&nbspAdelaide</label>
                </span>
            </div>

            <div class="row rowcol rowleft">
                <?= CHtml::label('Postcode 邮编', 'postcode'); ?><br />
                <input class="width_input" type="text" name="postcodecost" id="postcodecost" value="<?= $model->postcode ?>"></input><br />
                <span style="color:red;" id="remind"></span>
            </div>
            <br />
            <div class="row rowcol rowleft">
                <?= CHtml::label('Suburb 区/市', 'suburbcost'); ?><br />
                <input class="width_input" type="text" name="suburbcost" id="suburbcost" value="<?= $model->suburb ?>"></input><br />
            </div>
           

            <input type="hidden" name="id" value="<?= $model->id ?>" />
        </form>
        <br />


        <br />
        <div class="row rowcol rowleft">
            <input type="button" value="Show me the price 显示派送价格" onclick="funcShowPricecost()" />
        </div>
    </div>

    <div id="divSummarycost" class="display_none">
        <div class="form ml_15">
            <br />
            <div class="row">
                <h4>Total Chargeable Weight 总重量: <span id="spanTotalWeightcost"></span></h4>
                <h4>Total Dimension 总体积: <span id="spanTotalDimensioncost"></span></h4>
                <!--<h4>Total Quantity 总数量: <span id="spanTotalQty"></span></h4>-->
            </div>
            <br />
            <br />
            <div class="row">
                <div class="rowcol_200"></div>
                <div class="rowcol_200">
                    <h4>TLD卡派服务</h4>
                </div>
                <div class="rowcol_200">
                    <h4 id="hExpresscost"></h4>
                </div>
            </div>

            <div class="row">
                <div class="rowcol_200">
                    <h4>Delivery Fee 派送费</h4>
                </div>
            </div>
            <div class="row">
                <div class="rowcol_200">
                    <h5> -Base delivery fee 基本派送费</h5>
                </div>
                <div id="divBaseTldcost" class="rowcol_200"></div>
                <div id="divBaseTollcost" class="rowcol_200"></div>
            </div>
            <div class="row">
                <div class="rowcol_200">
                    <h5> -Remote area fee 偏远地区费</h5>
                </div>
                <div id="divOversizeTldcost" class="rowcol_200"></div>
                <div id="divOversizeTollcost" class="rowcol_200"></div>
            </div>
            <div class="row">
                <div class="rowcol_200">
                    <h4>Total 小计</h4>
                </div>
                <div id="divTotalTldcost" class="rowcol_200"></div>
                <div id="divTotalTollcost" class="rowcol_200"></div>
            </div>
            <!-- <div class="row">
                <div class="rowcol_200">
                    <h4>TLD Optional Fee 卡派卸货费用</h4>
                </div>
            </div>
            <div class="row">
                <div class="rowcol_200">
                    <h5> -Unloading Fee 卸货费</h5>
                </div>
                <div id="divTailgateTld" class="rowcol_200"></div>
                <div id="divTailgateToll" class="rowcol_200"></div>
            </div>
            <div class="row">
                <div class="rowcol_200">
                    <h4>Total 总计</h4>
                </div>
                <div id="divTotalTLD" class="rowcol_200"></div>
                <div id="divTotalExpress" class="rowcol_200"></div>
            </div> -->


        </div>
        <br />
        <div class="form ml_15">
            <h5>Price doesn't include GST. 本价格不包含GST</h5>
        </div>
    </div>



</div>

<script type="text/javascript">
    $('input:radio[name="Depot"]')
        .click(function() {
            $("#remind").text("");
            var str = $("#postcodecost").val();
            let arr = ['Postcode', ' ', 'Sydney', 'Melbourne', 'Brisbane', 'Adelaide', 'Perth'];
            var strfirst = str.substr(0, 1);
            if (str.length >= 4) {
                var val = $('input:radio[name="Depot"]:checked').val();
                if (jQuery.inArray(str.substr(0, 1), ['0', '1', '2', '3', '4', '5', '6']) !== -1) {
                    if (arr[str.substr(0, 1)] != val) {
                        alert("Interstate Delivery 跨州派送");
                        $("#remind").text("Interstate Delivery 跨州派送");
                    }
                }
            }
        })

    $("#postcodecost")
        .blur(function() {
            $("#remind").text("");
            var str = $("#postcodecost").val();
            let arr = [' ', ' ', 'Sydney', 'Melbourne', 'Brisbane', 'Adelaide', 'Perth'];
            var strfirst = str.substr(0, 1);
            if (str.length >= 4) {
                var val = $('input:radio[name="Depot"]:checked').val();
                if (jQuery.inArray(str.substr(0, 1), ['0', '1', '2', '3', '4', '5', '6']) !== -1) {
                    if (arr[str.substr(0, 1)] != val) {
                        alert("Interstate Delivery 跨州派送");
                        $("#remind").text("Interstate Delivery 跨州派送");
                    }
                }
            }
        })
</script>

<script type="text/javascript">
    function funcShowPricecost() {
        // if (!funcCheckListPost()) {
        //     return;
        // }

        $("#divSummarycost").addClass("display_none")
        //$("#divCodecost").addClass("display_none")

        $("#divBaseTldcost").html('');
        $("#divOversizeTldcost").html('');
        $("#divTotalTldcost").html('');
        $("#divTailgateTldcost").html('');

        $("#hExpresscost").html('');
        $("#divBaseTollcost").html('');
        $("#divOversizeTollcost").html('');
        $("#divTotalTollcost").html('');

        $("#divTotalTldcost").html('');
        $("#divTotalExpress").html('');



        if (!funcCheck()) {
            return;
        }


        var listData = new FormData();
        var listName2Value = $('#form_enquirycost').serializeArray();
        for (var i = 0; i < listName2Value.length; i++) {
            var objName2Value = listName2Value[i];
            listData.append(objName2Value.name, objName2Value.value);
        }

        htmlobj = $.ajax({
            type: "POST",
            url: "<?= $this->createUrl('priceEnquiry/ajaxQuickEnquiryCost'); ?>",
            data: listData,
            async: false,
            contentType: false,
            processData: false,
        });
        obj = JSON.parse(htmlobj.responseText);
        if (obj.isSuccess) {
            $("#divSummarycost").removeClass("display_none");


            if (obj.data.base_TLD > 0) {
                if (obj.data.oversize_TLD != 99999999) {
                    numBaseTld = obj.data.base_TLD != null ? obj.data.base_TLD : 0;
                    numOversizeTLD = obj.data.oversize_TLD != null ? obj.data.oversize_TLD : 0;
                    numManualTLD = obj.data.manual_TLD != null ? obj.data.manual_TLD : 0;
                    numTailgateTLD = obj.data.tailgate_TLD != null ? obj.data.tailgate_TLD : 0;

                    $("#divBaseTldcost").html(funcStrPrice(numBaseTld));
                    $("#divOversizeTldcost").html(funcStrPrice(numOversizeTLD));
                    $("#divTotalTldcost").html(funcStrPrice(numBaseTld + numOversizeTLD));

                    //$("#divTailgateTld").html(funcStrPrice(numTailgateTLD));

                    $("#divTotalTldcost").html(funcStrPrice(numBaseTld + numOversizeTLD + numManualTLD + numTailgateTLD));

                }
            }

            if (obj.data.tailgate_TLD == 0) {
                $("#hExpresscost").html('Express快递服务 (Toll/Allied/TNT)');
                $("#divBaseTollcost").html(funcStrPrice(obj.data.base_Toll));
                $("#divOversizeTollcost").html(funcStrPrice(obj.data.oversize_Toll));
                $("#divTotalTollcost").html(funcStrPrice(obj.data.base_Toll + obj.data.oversize_Toll));

                $("#divTotalExpresscost").html(funcStrPrice(obj.data.base_Toll + obj.data.oversize_Toll));
            }

            $("#spanTotalWeightcost").html(obj.numTotalWeight + " KG");
            $("#spanTotalDimensioncost").html(obj.numTotalDimension + " cbm");
            //$("#spanTotalQtycost").html(obj.numTotalQty + " pcs");

            // if (obj.isCode) {
            //     $("#divCode").removeClass("display_none");
            //     console.log(obj.data.tailgate_TLD > 0);
            //     console.log(obj.paid_by == "Shipper");

            // }

        } else {
            alert(obj.strMessage);
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
            return "";
        }

    }


    function funcCountItem() {

        numWeightAll = 0;
        numCbmAll = 0;
        numMaxCm = 0;
        numMaxKg = 0;
        var i = 1;
        //for (var i = 1; i <= numCountItem; i++) {
        numCurrentWeight = parseFloat($("#weightcost").val());
        numCurrentLength = parseFloat($("#lengthcost").val());
        numCurrentWidth = parseFloat($("#widthcost").val());
        numCurrentHeight = parseFloat($("#heightcost").val());
        //numCurrentQty = parseFloat($("#quantity_" + i).val());

        numWeightAll += numCurrentWeight;
        // numCbmAll += numCurrentLength*numCurrentWidth*numCurrentHeight / 1000000*numCurrentQty;
        if (numCurrentLength > numMaxCm) {
            numMaxCm = numCurrentLength;
        }
        if (numCurrentWidth > numMaxCm) {
            numMaxCm = numCurrentWidth;
        }
        if (numCurrentLength > numMaxCm) {
            numMaxCm = numCurrentLength;
        }
        if (numCurrentHeight > numMaxCm) {
            numMaxCm = numCurrentHeight;
        }
        if (numCurrentWeight > numMaxKg) {
            numMaxKg = numCurrentWeight;
        }
        // }
        console.log(numWeightAll);
        console.log(numMaxKg);
        console.log(numMaxCm);

        if (numMaxKg < 25 && numMaxCm < 100 && numWeightAll < 250) {
            $("#divForklift").addClass("display_none");
            $("#divPaidBy").addClass("display_none");
        } else {
            $("#divForklift").removeClass("display_none");
            $("#divPaidBy").removeClass("display_none");
        }
    }

    var listOldPost = [];

    function funcCheckListPost() {
        var isOk = false;
        var listData = $('#form_enquirycost').serializeArray();

        var objNew = {};
        for (var i = 0; i < listData.length; i++) {
            var objItem = listData[i];
            objNew[objItem.name] = objItem.value;
        }
        var objOld = {};
        for (var i = 0; i < listOldPost.length; i++) {
            var objItem = listOldPost[i];
            objOld[objItem.name] = objItem.value;
        }
        // console.log(objNew);
        // console.log(objOld);
        for (var name in objNew) {
            if (objNew[name] != objOld[name]) {
                isOk = true;
            }
        }

        listOldPost = listData;

        if (!isOk) {
            alert("您已提交过此询价，请勿重复提交相同询价。");
        }
        return isOk;
    }

    function funcCheck() {


        if ($("#postcodecost").val() == '') {
            alert("postcode error.");
            return false;
        }

        if ($("#tel").val() == '') {
            alert("tel error.");
            return false;
        }

        return true;
    }
</script>