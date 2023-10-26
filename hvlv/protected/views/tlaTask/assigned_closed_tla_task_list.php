<h1><?=$this->t(' Tasks Need Your Confirmation');?></h1>
<br/>


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
 <div class="row">
                    <!--<div id="loadingPic"  style="width:20px;height:20px;float:left;"></div>-->
 <div id="my-task-list-view">
      <?php
         $this->renderPartial('_sub_assigned_closed_tla_task_list', [
            'tlaTask' => $tlaTask,
            'name' =>'All',
            'user_id'=>$user_id,
            'tab'=>$tab
         ]);
    ?>
  </div>
</div>

<script type="text/javascript">
    function initAccttTaskList<?=$user_id?>(){
        var tab = $('#<?=$_GET["tabid"];?>');
        var panel=tab.data('panel');
        tab.unbind('reload_my_assigned_closed_tla_task_grid').bind('reload_my_assigned_closed_tla_task_grid', function(){
                        $('#<?=$_GET["tabid"];?>_my_assigned_closed_sub_tla_task_grid', tab.data('panel')).yiiGridView('update');
                        return false;
        });
        $('.confirm_task',panel).on('click',function(event){
                event.preventDefault();
                if(confirm("Are you Confirm to Confirm The Task?")){
                    $.get($(this).attr('href'),function(r){
                        console.log(r);
                        if(r=='done'||r==' done'){
                            myApp.notice("Confirm!");
                            var tab = $('#<?=$_GET["tabid"];?>');
                            tab.trigger('reload_my_assigned_closed_tla_task_grid');
                        }
                    });
                 }
        });

    }

    $(function(){
         initAccttTaskList<?=$user_id?>();
         $.oTlaTask.clean('#my_tla_task_tab_closed_by_assigned_user',0);
    })

</script>