<?php $podName=Org::dptList()[$_GET['pod_id']];?>

<h2><?=$podName?> Cargo Job Processing</h2>
<div style="position: absolute; right:80px;">
   <?php
   switch($job_type){
    case 1:
        $requestUrl = "topCourierService/cargoJobList";
        break;
    case 2:
        $requestUrl = "topCourierService/fbaJobList";
        break;
    case 4:
        $requestUrl = "topCourierService/b2bJobList";
        break;
    case 5:
        $requestUrl = "topCourierService/palletJobList";
        break;
    case 6:
        $requestUrl = "topCourierService/interstateJobList";
        break;
    default:
        $requestUrl = "topCourierService/cargoJobList";
   }
    
     $statusStr = 'CargoProcessJob::$processTypes';
    foreach (CargoProcessJob::$processTypes as $key => $value) {
        if($key==CargoProcessJob::DELETED) continue;
       echo "<a class=\"tab_link\"  href=\"" .$this->createUrl($requestUrl, ['pod_id'=>$_GET['pod_id'],'status'=>$key])
       ."\" title=\"{$podName} {$value}\" ><span style=\"background-position:-48px -688px\" class=\"icon\"></span>{$value}</a>";
    }
    ?>
</div>
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

<div style="width:50%" id="jobs-client-view<?=$_GET['tabid']?>">
   <?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'jobs-client-list-grid'.$_GET['tabid'],
    'htmlOptions'=>['style'=>'width: 70%'],
    'cssFile' => false,
    'dataProvider'=>$dataProvider[0],
    'filter'=>$dataProvider[1],
    'columns'=>[
        ['name'=>'status','headerHtmlOptions' => ['style' => 'display:none'],'filterHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'display:none'],'type'=>'raw'],
        ['name'=>'status','value'=>$statusStr.'[$data["status"]]'],
        'number',
        ['name'=>'day1','header'=>'<=1 days'],
        ['name'=>'day2','header'=>'2 days'],
        ['name'=>'day3','header'=>'>=3 days','cssClassExpression' => '$data>0? "cloumn_red_1" : ""'],
    ],
   ]); ?>
</div>


<br/>
<div class="form">
    <div class="row">
   <?php  echo CHtml::button('Show All', ['class' => 'show_all']);?>
    </div>
</div>
<div style="right: 450px;position: absolute;">
     <?php  echo CHtml::button('Generate Job', ['class' => 'generate_job']);?>
</div>
<div style="right: 20px;position: absolute;">
<a href="#" class="export_search" target="_blank" data-baseurl="<?=$this->createUrl('topCourierService/export', ['typ' => $job_type, 'pod_id'=>$_GET['pod_id']]);?>"><div style="background-position:-48px -688px" class="icon"></div> Export Current Search Details</a>
<a class="jqm_link grid_edit_btn" href="<?=$this->createUrl('topCourierService/exportCargoProcessJobReport');?>"> Export Cargo Process Job Report</a>  
</div>
 <div class="row">
  <span> <h2> Available Jobs </h2></span>
                    <!--<div id="loadingPic"  style="width:20px;height:20px;float:left;"></div>-->
 <div id="job-list-view<?=$_GET['pod_id'].$job_type?>">
      <?php
         $this->renderPartial('_sub_jobs', [
            'job' => $job,
            'name' =>'All',
            'pod_id' =>$_GET['pod_id'],
            'job_type' => $job_type
         ],false, true);
    ?>
  </div>
</div>
<script>
$(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel=tab.data('panel');
    <?php
     $cargoListUrl = $requestUrl;
     ?>

     $('#jobs-client-view<?=$_GET['tabid']?>',panel).on("click", "table tbody td", function(event){
        // get cargoe id
        var status = parseInt($(this).parent().children(':nth-child(1)').html());
        var data = {};
        data['status'] = status;
        data['pod_id']=<?=$_GET['pod_id']?>;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl($cargoListUrl, ['tabid'=>$_GET['tabid']]) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#job-list-view<?=$_GET['pod_id'].$job_type?>',panel).html(resp);
            },
        });
    }); 
    
    $('.show_all',panel).on('click',function(event){
        var data = {};
        data['status'] = 0;
        data['pod_id']=<?=$_GET['pod_id']?>;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl($cargoListUrl, ['tabid'=>$_GET['tabid']]) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#job-list-view<?=$_GET['pod_id'].$job_type?>').html(resp);
            },
        });
        
    });

    $('.generate_job',panel).on('click',function(event){
        if( confirm('Are you sure to generate jobs')){
        var data = {};
        data['status'] = 0;
        data['pod_id']=<?=$_GET['pod_id']?>;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl('topCourierService/generateJob', ['typ' => $job_type]) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                myApp.notice('Done', 5000);
                $('#job-list-view<?=$_GET['pod_id'].$job_type?>').yiiGridView('update');
            },
        });
    }
    });

     $('a.export_search', panel).on('mousedown', function(){
        var q = $('.filters input, .filters select', panel).serialize();
        $(this).attr('href', $(this).data('baseurl') + '&' + q);
    });
})
  
</script>