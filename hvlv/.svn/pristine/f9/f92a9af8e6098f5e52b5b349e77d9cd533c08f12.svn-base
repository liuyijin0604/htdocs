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
    <h1>Quick Enquiry</h1>

    <div class="form ml_15">
        <form id="form_enquiry" enctype="multipart/form-data">
            <div id="divItems" class="row rowcol rowleft">
                <div>
                    <label>Item 货物</label>

                </div>
                <br />
                <span class="width_item_label">volumn(cbm)</span>
                <input onchange="funcCountItem()" class="width_item_input" type="text" name="length" id="length"></input>
                <br />
                <!-- <span class="width_item_label">width(cm)</span>
                <input onchange="funcCountItem()" class="width_item_input" type="text" name="width" id="width"></input>
                <br />
                <span class="width_item_label">height(cm)</span>
                <input onchange="funcCountItem()" class="width_item_input" type="text" name="height" id="height"></input>
                <br /> -->
                <span class="width_item_label">weight(kg)</span>
                <input onchange="funcCountItem()" class="width_item_input" type="text" name="weight" id="weight"></input>
                <br />
            </div>
            <br />

            <div class="row rowcol rowleft">
                <?= CHtml::label('Depot From  始发地 ', 'Depot'); ?><br />
                <span id="Depot">
                    <input value="Sydney" id="Depot_0" checked="checked" type="radio" name="Depot">
                    <label for="Depot_0">Sydney</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input value="Melbourne" id="Depot_1" type="radio" name="Depot">
                    <label for="Depot_1">Melbourne</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input value="Brisbane" id="Depot_2" type="radio" name="Depot">
                    <label for="Depot_2">Brisbane</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input value="Perth" id="Depot_3" type="radio" name="Depot">
                    <label for="Depot_3">Perth</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input value="Adelaide" id="Depot_4" type="radio" name="Depot">
                    <label for="Depot_4">Adelaide</label>
                </span>
            </div>

            <div id="divForklift" class="row rowcol rowleft">
                <?= CHtml::label('Forklift 是否有叉车', 'have_forklift'); ?><br />
                <span id="have_forklift">
                    <input value="No" id="have_forklift_0" checked="checked" type="radio" name="have_forklift">
                    <label for="have_forklift_0">No</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input value="Yes" id="have_forklift_1" type="radio" name="have_forklift">
                    <label for="have_forklift_1">Yes</label>
                </span>
            </div>

            <div class="row rowcol rowleft">
                <?= CHtml::label('Delivery Postcode 邮编', 'postcode'); ?><br />
                <input class="width_input" type="text" name="postcode" id="postcode" value="<?= $model->postcode ?>"></input><br />
                <span style="color:red;" id="remind"></span>
            </div>
            <br/>
            <div class="row rowcol rowleft">
                <?= CHtml::label('Suburb 区/市', 'suburb'); ?><br />
                <input class="width_input" type="text" name="suburb" id="suburb" value="<?= $model->suburb ?>"></input><br />
            </div>

            <input type="hidden" name="id" value="<?= $model->id ?>" />
        </form>
        <br />


        <br />
        <div class="row rowcol rowleft">
            <input type="button" value="Show me the price 显示派送价格" onclick="funcShowPrice()" />
        </div>
    </div>

    <div id="divSummary" class="display_none">
        <div class="form ml_15">
            <br />
            <br />
            <div class="row">
                <h4>Total Chargeable Weight 总重量: <span id="spanTotalWeight"></span></h4>
                <h4>Total Dimension 总体积: <span id="spanTotalDimension"></span></h4>
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
            <!-- <div class="row">
                <div class="rowcol_200">
                    <h5> -Remote area fee 偏远地区费</h5>
                </div>
                <div id="divOversizeTld" class="rowcol_200"></div>
                <div id="divOversizeToll" class="rowcol_200"></div>
            </div> -->
            <div class="row">
                <div class="rowcol_200">
                    <h4>Total 小计</h4>
                </div>
                <div id="divTotalTld" class="rowcol_200"></div>
                <div id="divTotalToll" class="rowcol_200"></div>
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
        <br/>
        <h5>Price doesn't include GST. 本价格不包含GST</h5>
    </div>



</div>

<script type="text/javascript">
$('input:radio[name="Depot"]')
 .click(function () {
    $("#remind").text("");
    var str = $("#postcode").val();
    let arr = ['Postcode',' ','Sydney','Melbourne','Brisbane','Adelaide','Perth'];
    var strfirst = str.substr(0,1);
    if(str.length>=4){
        var val=$('input:radio[name="Depot"]:checked').val();
        if(jQuery.inArray(str.substr(0,1), ['0','1','2','3','4','5','6']) !== -1){
            if(arr[str.substr(0,1)]!=val){
                alert("Interstate Delivery 跨州派送");
                $("#remind").text("Interstate Delivery 跨州派送");
            }
        }
    }
  })

$("#postcode" )
  .blur(function () {
    $("#remind").text("");
    var str = $("#postcode").val();
    let arr = [' ',' ','Sydney','Melbourne','Brisbane','Adelaide','Perth'];
    var strfirst = str.substr(0,1);
    if(str.length>=4){
        var val=$('input:radio[name="Depot"]:checked').val();
        if(jQuery.inArray(str.substr(0,1), ['0','1','2','3','4','5','6']) !== -1){
            if(arr[str.substr(0,1)]!=val){
                alert("Interstate Delivery 跨州派送");
                $("#remind").text("Interstate Delivery 跨州派送");
            }
        }
    }
  })
</script>

<script type="text/javascript">

    function funcShowPrice() {
        // if (!funcCheckListPost()) {
        //     return;
        // }
        
        $("#divSummary").addClass("display_none")
        $("#divCode").addClass("display_none")

        $("#divBaseTld").html('');
        $("#divOversizeTld").html('');
        $("#divTotalTld").html('');
        $("#divTailgateTld").html('');

        $("#hExpress").html('');
        $("#divBaseToll").html('');
        $("#divOversizeToll").html('');
        $("#divTotalToll").html('');

        $("#divTotalTLD").html('');
        $("#divTotalExpress").html('');



        if (!funcCheck()) {
            return;
        }


        var listData = new FormData();
        var listName2Value = $('#form_enquiry').serializeArray();
        for(var i=0;i<listName2Value.length;i++){
            var objName2Value = listName2Value[i];
            listData.append(objName2Value.name,objName2Value.value);
        }

        htmlobj = $.ajax({
            type: "POST",
            url: "<?= $this->createUrl('priceEnquiry/ajaxQuickEnquiry'); ?>",
            data: listData,
            async: false,
            contentType: false,
            processData: false,
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
                    //$("#divOversizeTld").html(funcStrPrice(numOversizeTLD));
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

            }

        } else {
            alert(obj.strMessage);
        }

    }

    function funcStrPrice(numPrice) {
        if (numPrice != null && numPrice > 0) {
            if(numPrice >= 99999999){
                return "超出标准服务范围，暂无报价";
            }
            numPrice =  Math.round(numPrice*100)/100;
            return "$" + numPrice;
        } else {
            return "";
        }

    }


    function funcCountItem() {

        numWeightAll = 0;
        numCbmAll = 0;
        numMaxCm=0;
        numMaxKg = 0;
        var i =1;
        //for (var i = 1; i <= numCountItem; i++) {
            numCurrentWeight = parseFloat($("#weight").val());
            numCurrentLength = parseFloat($("#length").val());
            numCurrentWidth = parseFloat($("#width").val());
            numCurrentHeight = parseFloat($("#height").val());
            //numCurrentQty = parseFloat($("#quantity_" + i).val());

            numWeightAll += numCurrentWeight;
            // numCbmAll += numCurrentLength*numCurrentWidth*numCurrentHeight / 1000000*numCurrentQty;
            if(numCurrentLength>numMaxCm){
                numMaxCm=numCurrentLength;
            }
            if(numCurrentWidth>numMaxCm){
                numMaxCm=numCurrentWidth;
            }
            if(numCurrentLength>numMaxCm){
                numMaxCm=numCurrentLength;
            }
            if(numCurrentHeight>numMaxCm){
                numMaxCm=numCurrentHeight;
            }
            if(numCurrentWeight>numMaxKg){
                numMaxKg=numCurrentWeight;
            }
       // }
        console.log(numWeightAll);
        console.log(numMaxKg);
        console.log(numMaxCm);

        if(numMaxKg<25&&numMaxCm<100&&numWeightAll<250){
           // $("#divForklift").addClass("display_none");
            $("#divPaidBy").addClass("display_none");
        }
        else{
           // $("#divForklift").removeClass("display_none");
            $("#divPaidBy").removeClass("display_none");
        }
    }

    var listOldPost = [];
    function funcCheckListPost(){
        var isOk = false;
        var listData = $('#form_enquiry').serializeArray();

        var objNew = {};
        for(var i=0; i<listData.length;i++){
            var objItem = listData[i];
            objNew[objItem.name] = objItem.value;
        }
        var objOld = {};
        for(var i=0; i<listOldPost.length;i++){
            var objItem = listOldPost[i];
            objOld[objItem.name] = objItem.value;
        }
        // console.log(objNew);
        // console.log(objOld);
        for(var name in objNew){
            if(objNew[name] != objOld[name]){
                isOk = true;
            }
        }

        listOldPost = listData;

        if(!isOk){
            alert("您已提交过此询价，请勿重复提交相同询价。");
        }
        return isOk;
    }

    function funcCheck() {


        if ($("#postcode").val() == '') {
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