<h1><?=$this->t('My Task List') . "-" . User::getUserName($user_id);?></h1>
<br/>
<div style="right: 200px;position: absolute;">
      <a class="jqm_link" href="<?=$this->createUrl('tlaTask/createTlaTask');?>" title="Create Task"><div class="icon" style="background-position:-16px -0px"></div>Create Task</a>
</div>
<h4 style="float:left">Total Left：</h4>
<div style="width:50%;" id="tla-task-overview<?=$_GET['tabid']?>">
   <?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'user_tla_task_-grid'.$_GET['tabid'],
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
  $requestUrl = "tlaTask/getTlaTaskList";
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
         $this->renderPartial('_sub_IT_task', [
            'tlaTask' => $tlaTask,
            'name' =>'All',
            'pageSize'=>1000
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
    
    tab.unbind('reload_tla_task_grid').bind('reload_tla_task_grid', function(){
                    $('#<?=$_GET["tabid"];?>_sub_tla_task_grid', tab.data('panel')).yiiGridView('update');
                    return false;
    });

    $('#my_tla_task_-grid<?=$_GET['tabid']?>',panel).on("click", "table tbody td", function(event){
        // get cargoe id
        var status = parseInt($(this).parent().children(':nth-child(1)').html());
        var type = parseInt($(this).parent().children(':nth-child(2)').html());
        var data = {};
        data['TlaTask'] = {};
        data['TlaTask']['status'] = status;
        data['TlaTask']['type'] = type;
        $('#<?=$_GET["tabid"];?>_sub_tla_task_grid', tab.data('panel')).yiiGridView('update', {data: data});
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