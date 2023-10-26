<h1><?=$this->t('Task Dashboard')?></h1>
<br/>
<div style="right: 200px;position: absolute;">
      <a class="jqm_link" href="<?=$this->createUrl('tlaTask/createTlaTask');?>" title="Create Task"><div class="icon" style="background-position:-16px -0px"></div>Create Task</a>

    <?php if (Acl::hasAccess("B:importsMail/filterManage")):?>
    <a class="tab_link" href="<?=$this->createUrl('tlaTask/filterManage');?>" title="Filter Management"><div class="icon" style="background-position:-128px -32px"></div>Filter Management</a>
    <?php endif;?>

</div>
<h4 style="float:left">Total Left：</h4>
<div style="width:80%;" id="tla-task-overview<?=$_GET['tabid']?>">
   <?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'dash_tla_task_grid'.$_GET['tabid'],
    'htmlOptions'=>['style'=>'width: 70%'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],
    'filter'=>$dataProvider[1],
    'columns'=>[
        ['name'=>'user_id','headerHtmlOptions' => ['style' => 'display:none'],'filterHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'display:none'],'type'=>'raw'],
        ['name'=>'user','value'=>'$data["name"]'],
        ['name'=>'total_task','value'=>'$data["total_task"]','name'=>'Total'], 
        ['name'=>'today_left','value'=>'$data["today_left"]','name'=>'Unfinished'], 
        ['name'=>'close','value'=>'$data["close"]','name'=>'Finished'],
        ['name'=>'close_percent','value'=>'number_format($data["close_percent"]*100,2,".","")."%"','name'=>'Finished %'],
         ['name'=>'task_need_your_confirm','value'=>'$data["task_need_your_confirm"]','name'=>'Need You Confirm'],
          ['name'=>'closed_task_need_confirm','value'=>'$data["closed_task_need_confirm"]','name'=>'Need Assignor Confirm'], 
           ['name'=>'closed_and_confirmed','value'=>'$data["closed_and_confirmed"]','name'=>'Closed & Confirmed'],
        ['name'=>'month_to_date','value'=>'number_format($data["month_to_date"]*100,2,".","")."%"','name'=>'MTD %']
    ],
   ]); ?>
</div>

<?php
  $requestUrl = "cargoProcess/cargoList";
?>
<br>
<br>
<style>
    .cloumn_red_1{
        color:red;
        font-weight: bold;
    }   
    .cloumn_orange_1{
        color:orange;
        font-weight: bold;
    }  
</style>



<br/>
<div class="form">
    <div class="row">
   <?php  echo CHtml::button('Show All', ['class' => 'show_all']);?>
    </div>
</div>
 <div class="row">
                    <!--<div id="loadingPic"  style="width:20px;height:20px;float:left;"></div>-->
 <div id="my-task-list-view">
      <?php
         $this->renderPartial('_sub_tla_task_dash', [
            'tlaTask' => $tlaTask,
            'name' =>'All'
         ]);
    ?>
  </div>
</div>
<script>
$(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel=tab.data('panel');
    <?php
     $tlaTaskListUrl = $requestUrl;
     ?>
    
    $('.show_all',panel).on('click',function(event){
        var data = {};
        data['status'] = 0;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl($tlaTaskListUrl, ['tabid'=>$_GET['tabid']]) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#cargo-list-view').html(resp);
            },
        });
        
    });

    $('#dash_tla_task_grid<?=$_GET['tabid']?>',panel).on("click", "table tbody td", function(event){
        // get cargoe id
        var userId = parseInt($(this).parent().children(':nth-child(1)').html());
        if(userId> 0){
            myApp.tabs.CreateTab({
                title: userId + ' Task List',
                url: "tlaTask/getTlaTaskList?user_id="+userId,
                bg: false
            });
        }
        return false;

    }); 


     $('a.export_search', panel).on('mousedown', function(){
        var q = $('.filters input, .filters select', panel).serialize();
        $(this).attr('href', $(this).data('baseurl') + '&' + q);
    });
})
  
</script>