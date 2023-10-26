<style>
    #consol_managment .type_row {
        color: rgb(119, 119, 218);
        text-align: center;
        font-size: 20px;
        font-weight: bold;
    }
</style>
<div class="pane" id="consol_managment">
    <?php
        if($type==1){
            $strTitle = "CargoProcess Bidding";
            $strUrl ="cargoProcessBidding/index";
            $strId ="_cargoprocess_bidding";
        }elseif($type==2){
            $strTitle = "...";
            $strUrl ="mcontroller/action";
            $strId ="_task";
        }
    ?>
    <h1><?php echo $strTitle?></h1>
    <div style="position: absolute; right:80px;">
    </div>
    <div class="list-group">
        <div id="cargoprocess_bidding" style="max-width:200px;">
            <?php
            $provide = [];
            $provide[] = ['id' => 106, 'Depot' => 'Sydney'];
            $provide[] = ['id' => 218, 'Depot' => 'Melbourne'];
            $provide[] = ['id' => 530, 'Depot' => 'Brisbane'];
            $dataprovider = new CArrayDataProvider($provide);
            $dataprovider->pagination = false;
            $this->widget('zii.widgets.grid.CGridView', array(
                'id' => 'consol_main_menu' . $_GET['tabid'],
                'cssFile' => false,
                'dataProvider' => $dataprovider,
                'columns' => array(
                    array(
                        'name' => 'id', 'headerHtmlOptions' => array('style' => 'display:none'), 'filterHtmlOptions' => array('style' => 'display:none'),
                        'htmlOptions' => array('style' => 'display:none')
                    ),
                    array('name' => 'Depot', 'cssClassExpression' => '"type_row"', 'headerHtmlOptions' => array('style' => 'text-align: center;
        font-size: 20px;
        font-weight: bold;'))
                ),
            )); ?>
        </div>
    </div>
    <div id='cargoprocess_bidding_type<?php echo $strId?>'>

    </div>
</div>
<script>
    $(function() {
        var tab = $('#<?= $_GET['tabid'] ?>');
        var panel = tab.data('panel');
        $('.summary', panel).html('');
        $("#cargoprocess_bidding", panel).on('click', "table tbody td", function() {
            var delivery_type = parseInt($(this).parent().children(':nth-child(1)').html());
            var data = {};
            data['depot'] = delivery_type;
            console.log(data);
            $.ajax({
                type: 'GET',
                url: '<?php echo Yii::app()->createAbsoluteUrl($strUrl, array('tabid' => $_GET['tabid'])); ?>',
                data: data,
                dataType: 'html',
                success: function(resp) {
                    $('#cargoprocess_bidding_type<?php echo $strId?>').html(resp);
                },
            });
        })


    })
</script>