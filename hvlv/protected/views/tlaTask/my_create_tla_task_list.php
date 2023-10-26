<h1><?=$this->t('My Created Task List') . "-" . User::getUserName($user_id);?></h1>
<br/>
<div style="width:50%;" id="tla-task-overview<?=$_GET['tabid']?>">
   <?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'created_tla_task_grid'.$_GET['tabid'],
    'htmlOptions'=>['style'=>'width: 70%'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],
    'filter'=>$dataProvider[1],
    'columns'=>[
        ['name'=>'status','headerHtmlOptions' => ['style' => 'display:none'],'filterHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'display:none'],'type'=>'raw'],
        ['name'=>'type','headerHtmlOptions' => ['style' => 'display:none'],'filterHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'display:none'],'type'=>'raw'],
        ['name'=>'type','value'=>'TlaTask::$types[$data["type"]]'],
        ['name'=>'status','value'=>'TlaTask::$processTypes[$data["status"]]'],
        'number',
        ['name'=>'day1','header'=>'<=1 days'],
        ['name'=>'day2','header'=>'2 days'],
        ['name'=>'day3','header'=>'>=3 days','cssClassExpression' => '$data>0? "cloumn_red_1" : ""'],
    ],
   ]); ?>
</div>

<?php
  $requestUrl = "tlaTask/getMyCreatedTlaTaskList";
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
         $this->renderPartial('_my_sub_create_tla_task_list', [
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
    
    $('#created_tla_task_grid<?=$_GET['tabid']?>',panel).on("click", "table tbody td", function(event){
        // get cargoe id
        var status = parseInt($(this).parent().children(':nth-child(1)').html());
        var type = parseInt($(this).parent().children(':nth-child(2)').html());
        var data = {};
        data['TlaTask'] = {};
        data['TlaTask']['status'] = status;
        data['TlaTask']['type'] = type;
        $('#<?=$_GET["tabid"];?>_my_create_sub_tla_task_grid', tab.data('panel')).yiiGridView('update', {data: data});
    }); 


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
     $('a.export_search', panel).on('mousedown', function(){
        var q = $('.filters input, .filters select', panel).serialize();
        $(this).attr('href', $(this).data('baseurl') + '&' + q);
    });
})
  
</script>