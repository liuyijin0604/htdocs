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
    <h1> Price Enquiry Excel</h1>

    <div class="form ml_15">
        <h3>Select Excel</h3>
        <?php
        $strUrlPrefix = 'https://os.toplogistics.com.au';
        if ($_SERVER['HTTP_HOST'] == 'localhost:82') {
            $strUrlPrefix = 'http://localhost:82';
        }
        $strUrl = $strUrlPrefix.'/template/price_enquiry_templete.xlsx';
        ?>
        <a href="<?=$strUrl?>" target="_blank">Download Templete (下载模板)</a>
        <br/><br/>
        <input type="file" name="excel" id="excel" value="" placeholder="please select excel template">
        

        <br /><br /><br />
        <div class="row rowcol rowleft">
            <input type="button" value="Show me the price 显示派送价格" onclick="funcShowPrice()" />
        </div>
    </div>


    
    <div id="divSummary" class="display_none">
        <div class="form ml_15">
            <br />
            <br />
            <div class="row">
                <h4>Total Weight 总重量: <span id="spanTotalWeight"></span></h4>
                <h4>Total Dimension 总体积: <span id="spanTotalDimension"></span></h4>
                <h4>Total Quantity 总数量: <span id="spanTotalQty"></span></h4>
            </div>

            <br />
            <br />
            <div class="row">
                <div class="rowcol_200"></div>
                <div class="rowcol_200">
                    <h4>TLD卡派服务</h4>
                </div>
                <div class="rowcol_200">
                    <h4 id="hExpress"></h4>
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
                <div id="divBaseTld" class="rowcol_200"></div>
                <div id="divBaseToll" class="rowcol_200"></div>
            </div>
            <div class="row">
                <div class="rowcol_200">
                    <h5> -Oversize fee 超尺寸费用</h5>
                </div>
                <div id="divOversizeTld" class="rowcol_200"></div>
                <div id="divOversizeToll" class="rowcol_200"></div>
            </div>
            <div class="row">
                <div class="rowcol_200">
                    <h4>Total 小计</h4>
                </div>
                <div id="divTotalTld" class="rowcol_200"></div>
                <div id="divTotalToll" class="rowcol_200"></div>
            </div>
            <div class="row">
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
            </div>


        </div>
        <br/>
        <h5>Price doesn't include GST. 本价格不包含GST</h5>
    </div>


    <br /><br /><br />
    <h2 id="divCode" class="display_none">
        divCode
    </h2>

</div>



<script type="text/javascript">
    var numCountItem = 1;

    function funcAddItem() {
        numCountItem++;
        var strHtmlItem = '<div id="divItem' + numCountItem + '"><br/><label>Item 货物 ' + numCountItem + '</label><br/><label class="width_100">Category 种类:</label><span id="Category_' + numCountItem + '"><input value="carton" id="Category_' + numCountItem + '_0" checked="checked" type="radio" name="Category_' + numCountItem + '"> <label for="Category_' + numCountItem + '_0">carton 纸箱</label>&nbsp;&nbsp;&nbsp;&nbsp;<input value="pallet" id="Category_' + numCountItem + '_1" type="radio" name="Category_' + numCountItem + '"> <label for="Category_' + numCountItem + '_1">pallet 托盘</label>&nbsp;&nbsp;&nbsp;&nbsp;<input value="crate" id="Category_' + numCountItem + '_2" type="radio" name="Category_' + numCountItem + '"> <label for="Category_' + numCountItem + '_2">crate 木箱</label>&nbsp;&nbsp;&nbsp;&nbsp;<input value="others" id="Category_' + numCountItem + '_3" type="radio" name="Category_' + numCountItem + '"> <label for="Category_' + numCountItem + '_3">others 其它</label></span><br/><label class="width_100">quantity 数量:</label><input class="width_400" type="text" name="quantity_' + numCountItem + '" id="quantity_' + numCountItem + '"></input><br/><span class="width_item_label">length(cm)</span><input onchange="funcCountItem()" class="width_item_input" type="text" name="length_' + numCountItem + '" id="length_' + numCountItem + '"></input><br/><span class="width_item_label">width(cm)</span><input onchange="funcCountItem()" class="width_item_input" type="text" name="width_' + numCountItem + '" id="width_' + numCountItem + '"></input><br/><span class="width_item_label">height(cm)</span><input onchange="funcCountItem()" class="width_item_input" type="text" name="height_' + numCountItem + '" id="height_' + numCountItem + '"></input><br/><span class="width_item_label">weight(kg)</span><input onchange="funcCountItem()" class="width_item_input" type="text" name="weight_' + numCountItem + '" id="weight_' + numCountItem + '"></input><br/></div></div>';
        $("#divItems").append(strHtmlItem);
    }

    function funDeleteItem() {
        $("#divItem" + numCountItem).remove();
        if (numCountItem > 1) {
            numCountItem--;
        }
    }

    function funcShowPrice() {
        $("#divSummary").addClass("display_none")
        $("#divCode").addClass("display_none")

        $("#divBaseTld").html('');
        $("#divOversizeTld").html('');
        $("#divTotalTld").html('');
        $("#divManualTld").html('');
        $("#divTailgateTld").html('');

        $("#hExpress").html('');
        $("#divBaseToll").html('');
        $("#divOversizeToll").html('');
        $("#divTotalToll").html('');

        $("#divTotalTLD").html('');
        $("#divTotalExpress").html('');



        // if (!funcCheck()) {
        //     return;
        // }

        var listData = new FormData();
        listData.append("excel", $("#excel")[0].files[0]);
        listData.append("is_excel", "true");

        htmlobj = $.ajax({
            type: "POST",
            url: "<?= $this->createUrl('priceEnquiry/showPrice'); ?>",
            data: listData,
            contentType: false,
            processData: false,
            async: false
        });
        obj = JSON.parse(htmlobj.responseText);
        if (obj.isSuccess) {
            $("#divSummary").removeClass("display_none");


            if (obj.data.base_TLD > 0) {
                if (obj.data.oversize_TLD != 99999999) {
                    numBaseTld = obj.data.base_TLD!=null?obj.data.base_TLD:0;
                    numOversizeTLD = obj.data.oversize_TLD!=null?obj.data.oversize_TLD:0;
                    numManualTLD = obj.data.manual_TLD!=null?obj.data.manual_TLD:0;
                    numTailgateTLD = obj.data.tailgate_TLD!=null?obj.data.tailgate_TLD:0;

                    $("#divBaseTld").html(funcStrPrice(numBaseTld));
                    $("#divOversizeTld").html(funcStrPrice(numOversizeTLD));
                    $("#divTotalTld").html(funcStrPrice(numBaseTld + numOversizeTLD));

                    $("#divTailgateTld").html(funcStrPrice(numTailgateTLD));

                    $("#divTotalTLD").html(funcStrPrice(numBaseTld + numOversizeTLD+numManualTLD+numTailgateTLD));

                }
            }

            if( obj.data.tailgate_TLD == 0){
                $("#hExpress").html('Express快递服务 (Toll/Allied/TNT)');
                $("#divBaseToll").html(funcStrPrice(obj.data.base_Toll));
                $("#divOversizeToll").html(funcStrPrice(obj.data.oversize_Toll));
                $("#divTotalToll").html(funcStrPrice(obj.data.base_Toll + obj.data.oversize_Toll));

                $("#divTotalExpress").html(funcStrPrice(obj.data.base_Toll + obj.data.oversize_Toll));
            }

            $("#spanTotalWeight").html(obj.numTotalWeight + " KG");
            $("#spanTotalDimension").html(obj.numTotalDimension + " cbm");
            $("#spanTotalQty").html(obj.numTotalQty + " pcs");

            if (obj.isCode) {
                $("#divCode").removeClass("display_none");
                console.log(obj.data.tailgate_TLD > 0);
                console.log(obj.paid_by == "Shipper");

                if(obj.enumPeTpey == 40){
                    $("#divCode").html('TLD quotation No. <a target="_blank" href="/ims/priceEnquiry/priceEnquiry.app">' + obj.code + '</a>(高度超过210cm，请等待报价，录入订单时，请务必输入此询单号码)');
                }
                else if (obj.data.base_TLD == null || obj.data.base_TLD == 0) {
                    $("#divCode").html('TLD quotation No. <a target="_blank" href="/ims/priceEnquiry/priceEnquiry.app">' + obj.code + '</a>(postcode不在标准配送范围，请等待报价，录入订单时，请务必输入此询单号码)');
                } else if (obj.data.oversize_TLD != null && obj.data.oversize_TLD == 99999999) {
                    $("#divCode").html('TLD quotation No. <a target="_blank" href="/ims/priceEnquiry/priceEnquiry.app">' + obj.code + '</a>(超过oversize最大长度，录入订单时，请务必输入此询单号码)');
                } else if (obj.data.oversize_TLD != null && obj.data.oversize_TLD > 0) {
                    $("#divCode").html('TLD quotation No. <a target="_blank" href="/ims/priceEnquiry/priceEnquiry.app">' + obj.code + '</a>(存在oversize货物，录入订单时，请务必输入此询单号码)');
                } else if (obj.data.tailgate_TLD > 0 && obj.paid_by == "Shipper") {
                    $("#divCode").html('TLD quotation No. <a target="_blank" href="/ims/priceEnquiry/priceEnquiry.app">' + obj.code + '</a>(需要卸货费，录入订单时，请务必输入此询单号码)');
                }

            }

        } else {
            alert(obj.strMessage);
        }

    }

    function funcStrPrice(numPrice) {
        if (numPrice != null && numPrice > 0) {
            return "$" + numPrice;
        } else {
            return "";
        }

    }
</script>