<style>

div.form select{
    width: 500px;
}

div.form textarea{
    width: 500px;
}

.width_item_label{
    width: 70px;
    display: inline-block;
    margin-left: 10px;
    margin-bottom: 5px;
}


.width_item_input{
    width: 400px;
    display: inline-block;
}

.width_input{
    width: 500px;
}

.width_100{
    width: 80px;
}
.width_400{
    width: 420px;
    margin-bottom: 8px;
}

.rowcol_200{
    display: inline-block;
    width: 200px;
}

.rowcol_500{
    display: inline-block;
    width: 500px;
}

.ml_15{
    margin-left: 15px;
}

.cssPicture{
    width: 500px;
    margin-left: -15px;
}



</style>



<div>
    <h1> Price Enquiry (<?=$model->code?>)</h1>

    <div class="form">
        <div id="divItems" class="row rowcol rowleft">
            <?php
            foreach($model->mdata['listItem'] as $objItem){
                echo '<label class="width_100">Category</label><div>'.$objItem['category'].'</div>';
                echo '<label class="width_100">Quantity</label><div>'.$objItem['quantity'].'</div>';
                echo '<label class="width_100">Length(cm)</label><div>'.$objItem['length'].'</div>';
                echo '<label class="width_100">Width(cm)</label><div>'.$objItem['width'].'</div>';
                echo '<label class="width_100">Height(cm)</label><div>'.$objItem['height'].'</div>';
                echo '<label class="width_100">Weight(KG)</label><div>'.$objItem['weight'].'</div><br/><br/>';
            }
            
            ?>
        </div>
        <br/>
        <br/>

        <div class="row rowcol rowleft">
            <?= CHtml::label('Depot','Depot'); ?>
            <div><?=$model->depot ?></div>
        </div>


        <div class="row rowcol rowleft">
            <?= CHtml::label('Delivery (Address) ','address'); ?>
            <div><?=$model->address?></div>
        </div>
        <div class="row rowcol rowleft">
            <?= CHtml::label('Delivery (Suburb) ','suburb'); ?>
            <div><?=$model->suburb?></div>
        </div>
        <div class="row rowcol rowleft">
            <?= CHtml::label('Delivery (Postcode) ','postcode'); ?>
            <div><?=$model->postcode?></div>
        </div>

        <div class="row rowcol rowleft">
            <label>Forklift</label>
            <div><?=!empty($model->tailgate_unloading)?$model->tailgate_unloading:'No'?></div>
        </div>

        <div class="row rowcol rowleft">
            <label>Notes</label>
            <div><?=$model->note?></div>
        </div>

        <div class="row rowcol rowleft">
            <label>Who pays for Surcharges？</label>
            <div><?=$model->paid_by?></div>
        </div>

        <br/>
        <div class="row rowcol rowleft">
            <label>Enquiry From</label>
            <div><?=$model->name?></div>
        </div>
        <div class="row rowcol rowleft">
            <label>Contact Tel</label>
            <div><?=$model->tel?></div>
        </div>
        <div class="row rowcol rowleft">
            <label>Contact Email</label>
            <div><?=$model->email?></div>
        </div>
    <br/>
    <?php
    if(isset($model->mdata['listPicture'])){
        foreach ($model->mdata['listPicture'] as $strUrl){
            echo '<img src="'.$strUrl.'"  alt="pic" class="cssPicture" /><br/><br/>';
        }
    }
    ?>

    <br/>
        <br/> 
        <br/>
        
        <div class="row rowcol rowleft">
            <h3>TLD 卡派</h3>
            <label>Base Price</label>
            <div><?=$model->mdata['base_TLD']?></div>
        </div>
        <div class="row rowcol rowleft">
            <label>Over Size</label>
            <div><?=$model->mdata['oversize_TLD']?></div>
        </div>

        <div class="row rowcol rowleft">
            <label>Unloading Fee</label>
            <div><?=$model->mdata['tailgate_TLD']<99999999?$model->mdata['tailgate_TLD']:'需人工报价'?></div>
        </div>

        <br/>
        <br/>
        <div class="row rowcol rowleft">
            <h3>Express 快递</h3>
            <label>Base Price</label>
            <div><?=isset($model->mdata['base_Toll'])?$model->mdata['base_Toll']:'null'?></div>
        </div>
        <div class="row rowcol rowleft">
            <label>Over Size</label>
            <div><?=isset($model->mdata['oversize_Toll'])?$model->mdata['oversize_Toll']:'null'?></div>
        </div>
        <br/>

    <div class="row rowcol rowleft width_input">
        <?php if ($model->status != PriceEnquiry::status_canceled): ?>
            <input type="button" onclick="funcCancel()" value="Cancel (删除)">
        <?php endif; ?>
        <?php if ($model->status == PriceEnquiry::status_canceled): ?>
            <h3>Canceled 已删除</h3>
        <?php endif; ?>
    </div>


        
    </div>

</div>


<script>
    function funcCancel(){
        <?php
        $strUrlPrefix = 'https://'.$_SERVER['HTTP_HOST'];
        if ($_SERVER['HTTP_HOST'] == 'localhost:82') {
            $strUrlPrefix = 'http://localhost:82/ims';
        }
        $strUrl = $strUrlPrefix.'/priceEnquiry/cancel';
        ?>

        var listData = new FormData();
        listData.append("id","<?=$model->id?>");
 
        htmlobj = $.ajax({
            type: "POST",
            url: "<?=$strUrl?>",
            data: listData,
            async: false,
            contentType: false,
            processData: false,
        });
        obj = JSON.parse(htmlobj.responseText);
        if (obj.isSuccess) {
            var strUrl = window.location.href;
			window.location.replace(strUrl);
        }
        else{
        }
    }
</script>