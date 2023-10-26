<h2>Customer Service</h2>
<div style="position:absolute; right: 680px;top: 25px">
    <a class="tab_link"  href="<?=$this->createUrl("report/imCourierReport")?>" title="Courier Delivery Report" ><span class="icon"></span>Courier Delivery Report</a>
</div>

<div style="position:absolute; right: 520px;top: 25px">
    <a class="jqm_link"  href="<?=$this->createUrl("customerService/addTicket")?>" title="Add Ticket" ><span class="icon"></span>Add Ticket</a>
</div>

<div style="position:absolute; right: 320px;top: 25px">
    <a class="jqm_link"  href="<?=$this->createUrl("customerService/faqAnswerList")?>" title="Manage Faq Answer List" ><span class="icon"></span>Manage Faq Answer List</a>
</div>
<div style="position:absolute; right: 190px;top: 25px">
    <a class="jqm_link"  href="<?=$this->createUrl("customerService/faqList")?>" title="Manage Faq List" ><span class="icon"></span>Manage Faq List</a>
</div>
<div style="position:absolute; right: 80px;top: 25px">
    <a class="jqm_link"  href="<?=$this->createUrl("customerService/assignUser")?>" title="Assign User" ><span class="icon"></span>Assign User</a>
</div>

<br />
<style>
.cloumn_red_1{
    color:red;
    font-weight: bold;
}   
#custom_summary table.chart1 td{
    text-align:center;
}
</style>
<table>
    <tr>
        <td width="60%">
            <div style="width:100%" id="cs-client-view">
            <?php
            $this->widget('zii.widgets.grid.CGridView', [
                'id' => 'custom-client-list-grid',
                'htmlOptions' => ['style' => 'width: 90%'],
                'afterAjaxUpdate'=>'function(r,s){$("#custom_summary").html($(s).find("#custom_summary").html());}',
                'cssFile' => false,
                'dataProvider' => $dataProvider[0],
                'filter' => $dataProvider[1],
                'columns' => [
                ['name' => 'org_id', 'headerHtmlOptions' => ['style' => 'display:none'], 'filterHtmlOptions' => ['style' => 'display:none'],
                    'htmlOptions' => ['style' => 'display:none'], 'type' => 'raw'],
                ['name' => 'name', 'type' => 'raw'],
                'number',
                ['name' => 'day1', 'header' => '24 hours', 'type' => 'raw'],
                ['name' => 'day2', 'header' => '24-48 hours', 'type' => 'raw'],
                ['name' => 'day3', 'header' => '48+ hours', 'type' => 'raw'],
                ],
            ]);
            ?>
            </div>

        </td>
        <td width="40%" valign="top">
            <div style="width:100%;" id="cs-client-view-detail">
           
            </div> 
        </td>
    </tr>
</table>
<div class="row">
     <div style="clear:both;"></div>
                <div  class="col">
                    <div id="custom_summary">
                    <p>Daily Shipments Questions(Average):<?=$info['totalShipmentQuestion']?></p>
                    <table class="chart chart1">
                        <tr><th>User</th><th>Type</th><th>Total</th><th>Percent of Daily's Shipment Questions</th><th>Percent</th><th>Open Today</th><th>Close Today</th><th>Daily Average Close</th><th>Close(Today/Average)</th></tr>
                        <?php
                            $tot=$totPercentOfTotalShipment=$totPercent=$totOpen=$totClose=$totDaiyClose=$totDailyClosePercent=0;
                            foreach ($info['tableTwoInfo'] as $user_id=> $oneUserInfo) {
                                $username= User::getUserName($user_id);
                                if (empty($username)) {
                                $username=$user_id;
                                }
                                $colums= sizeof($oneUserInfo);
                                if ($colums>1) {
                                $colums+=1;
                                }
                                $subTotal=0;
                                $subPercentOfTotalShipment=$subPercnet=$subOpen=$subClose=$subDaiyClose=$subDailyClosePercent=0;
                                echo "<tr ><td  rowspan='".$colums."'>$username</td>";
                                foreach ($oneUserInfo as $type_id=>$oneTypeInfo) {
                                $typeName= @CsFaq::getFaqList()[$type_id];
                                if(empty($typeName))
                                {
                                    $typeName= @CsFaq::getFaqList(false)[$type_id];
                                }
                                $total=intval(@$oneTypeInfo['total']);
                                $subTotal+=$total;
                                $percentOfTotalShipment=@$oneTypeInfo['totalPercent'];
                                $subPercentOfTotalShipment+= $percentOfTotalShipment;
                                $percent=@$oneTypeInfo['percent'];
                                $subPercnet+=$percent;
                                $open=intval(@$oneTypeInfo['open']);
                                $subOpen+=$open;
                                $close=intval(@$oneTypeInfo['close']);
                                $subClose+=$close;
                                $daily_avg_close=intval(@$oneTypeInfo['close_avg']);
                                $subDaiyClose+=$daily_avg_close;
                                $close_percent=@$oneTypeInfo['close_avg_percent']."%";
                                if ($type_id!=8) {
                                    $tot+=$total;
                                    $totPercentOfTotalShipment+= $percentOfTotalShipment;
                                    $totPercent+=$percent;
                                    $totOpen+=$open;
                                    $totClose+=$close;
                                    $totDaiyClose+=$daily_avg_close;
                                }
                                echo "<td>$typeName</td><td align=center>$total</td><td align=center>$percentOfTotalShipment%</td><td>$percent%</td><td>$open</td><td>$close</td><td><span ".($type_id!=8? "class='cloumn_red_1'":""). ">$daily_avg_close</span></td><td>$close_percent</td></tr>";
                                }
                                $subDailyClosePercent=$subDaiyClose>0?number_format(($subClose/$subDaiyClose*100), 2, '.', ''):0;
                                if ($colums>1) {
                                echo "<td>SubTotal</td><td >$subTotal</td><td>$subPercentOfTotalShipment%</td><td>$subPercnet%</td><td>$subOpen</td><td>$subClose</td><td>$subDaiyClose</td><td>$subDailyClosePercent%</td></tr>";
                                }
                            }
                                $totDailyClosePercent = $totDaiyClose > 0 ? number_format(($totClose / $totDaiyClose * 100), 2, '.', '') : 0;
                                echo "<tr><td></td><th>Total</th><th>$tot</th><th>$totPercentOfTotalShipment%</th><th>$totPercent%</th><th>$totOpen</th><th>$totClose</th><th>$totDaiyClose</th><th>$totDailyClosePercent%</th></tr>";

                                
                        ?>
                    </table>
                </div>
        </div>
</div>
<br/>
<div class="form">
    <div class="row">
    <?php  echo CHtml::button('Show All', ['class' => 'show_all']);?>
    </div>
</div>

<br />
<div class="row">
    <div id="cs-question-view">
            <?php
         $this->renderPartial('_sub_questions', [
            'modelQuestion' => $modelQuestion,
            'orgId'=> $orgId
         ]);
    ?>
      </div>
</div>
<br/>
<br/>
<P>————————————————————————————————————————————————————————————————————————————————————————————————————————————————————————————————————</P>
<script>
$(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel=tab.data('panel');
    tab.unbind('reload_update_tag').bind('reload_update_tag', function(){
        $.ajax({
            url:'<?=Yii::app()->createURL('customProcess/getProcessAmount')?>',
            data:{'status':[<?= ShipmentProcess::STATE_EMPP_CUSTOM?>]},
            method:'POST',
            success:function(msg){
                var reply=JSON.parse(msg)
                for(var p in reply){
                    $('#'+p, tab.data('panel')).html(reply[p]);
                }
            }
        });
        return false;
    });
        
    $('#custom-client-list-grid',panel).on('updated',function(r){
        console.log(1);
    });
    
    $('#cs-client-view',panel).on("click", "table tbody td", function(event){
        // get console id
        var orgId = parseInt($(this).parent().children(':nth-child(1)').html());
        var data = {};
        data['org_id'] = orgId;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("customerService/index", ['tabid'=>$_GET['tabid']]) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#cs-question-view').html(resp);
            },
        });
        tab.trigger('reload_update_tag');
    }); 
    
    $('.show_all',panel).on('click',function(event){
        var data = {};
        data['org_id'] = 0;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl("customerService/index", ['tabid'=>$_GET['tabid']]) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#cs-question-view').html(resp);
            },
        });
        
    });

    $('a.export_search', panel).on('mousedown', function(){
    var id=$('#agent_id',panel).val();
    var q = $('.filters input, .filters select', panel).serialize()+'&agent_id='+id;
    $(this).attr('href', $(this).data('baseurl') + '&' + q);
    });
        
    $('#cs-client-view-detail',panel).on("click", "table tbody td", function(event){
        var columnIndex=$(this).index();
        var type=parseInt($(this).parent().children(':nth-child(1)').html());
        if(parseInt($(this).html())<=0) return false;
        if(columnIndex<2) return false;
            myApp.tabs.CreateTab({
            title: 'Custom Process Detail',
        url: "customProcess/customDetail?type="+type+"&index="+columnIndex,
        bg:  false
    });
                return false;
    });
        
})
    
</script>