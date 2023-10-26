<?php $podName="Driver Jobs";?>

<h2><?=$podName?> Cargo Processing</h2>
<div style="position: absolute; right:80px;">
   
    <?php
        $requestUrl = "cargoProcess/viewDriverJobs";
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
        ['name'=>'agent_id','headerHtmlOptions' => ['style' => 'display:none'],'filterHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'display:none'],'type'=>'raw'],
        ['name'=>'name'],
        'number',
        ['name'=>'day1','header'=>'<=1 days'],
        ['name'=>'day2','header'=>'2 days'],
        ['name'=>'day3','header'=>'>=3 days','cssClassExpression' => '$data>0? "cloumn_red_1" : ""'],
    ],
   ]); ?>
</div>


<br/>
<div class="search-form">
    <div class="row">
   <?php  echo CHtml::button('Show All', ['class' => 'show_all']);?>

    </div>

    <div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route)."?tabid=".$_GET["tabid"],
        'method'=>'get',
    )); ?>
        <?php
            echo "From:",CHtml::textField('CargoProcess[searchFrom]',@$model->searchFrom,["class"=>'date_input  form-control']);
            echo "To:",CHtml::textField('CargoProcess[searchTo]',@$model->searchTo,["class"=>'date_input  form-control']);
            echo CHtml::hiddenField('CargoProcess[export]',0);
            echo "</br>";
            echo CHtml::submitButton($this->t('Search'), array('class' => 'save_btn','id'=>'search','style' => 'width:10em;display:inline;')),'&nbsp;'; 
           // echo CHtml::submitButton($this->t('Export Invoice'), array('class' => 'export','id'=>'export','style' => 'width:10em;display:inline;')); 
        ?>
        <a href="#" class="export_search" target="_blank" data-baseurl="<?=$this->createUrl($this->route, ['typ' => '']);?>"><div style="background-position:-48px -688px" class="icon"></div> Export Current Search Invoice</a>
    <?php $this->endWidget(); ?>
    </div>
</div>

 <div class="row">
  <span> <h2> Available Jobs </h2></span>
 <div id="jobs-list-view-summary"?>
      <?php
         $this->renderPartial('driver_job_list', [
            'cargo' => $cargo,
            'name' =>'All',
         ]);
    ?>
  </div>
</div>
<script>
$(function(){
    var tab = $('#<?=$_GET["tabid"];?>');
    var panel=tab.data('panel');

    <?php $cargoListUrl = "cargoProcess/viewDriverJobs"?>;

     $('#jobs-client-view<?=$_GET['tabid']?>',panel).on("click", "table tbody td", function(event){
        // get agent id
        var agentId = parseInt($(this).parent().children(':nth-child(1)').html());
        var data = {};
        data['agent_id'] = agentId;
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl($cargoListUrl, ['tabid'=>$_GET['tabid']]) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                $('#jobs-list-view-summary',panel).html(resp);
            },
        });
    }); 


    
    $('.show_all',panel).on('click',function(event){
        var data = {};
        $.ajax({
            type : 'GET',
            url : '<?php echo Yii::app()->createAbsoluteUrl($cargoListUrl, ['tabid'=>$_GET['tabid']]) ;?>',
            data: data,
            dataType: 'html',
            success:function(resp){
                panel.html(resp);
            },
        });
        
    });

    $('#search',panel).on('click',function(){
        $('#CargoProcess_export').val(0);
    });

    $('.search-form form', panel).on('submit', function(){
        $('#<?=$_GET["tabid"]?>_cargo_process_grid', panel).yiiGridView('update', {data: $('#CargoProcess_searchFrom, #CargoProcess_searchTo,#CargoProcess_export', panel).serialize() + '&' + $(this).serialize()});
        return false;
    });

    $('a.export_search', panel).on('mousedown', function(){
        $('#CargoProcess_export').val(1);
        var q = $('.filters input, .filters select', panel).serialize()+'&'+$('.search-form form', panel).serialize();
        $(this).attr('href', $(this).data('baseurl') + '&' + q);
    });
})
  
</script>