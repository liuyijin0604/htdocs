<?php $podName="Summary";?>

<h2><?=$podName?> Cargo Processing</h2>

<div style="position: absolute; right:80px;">
   
    <?php
    $requestUrl = "cargoProcess/summary";

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

<div style="width:50%" id="cargos-client-view<?=$_GET['tabid']?>">
   <?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'cargos-client-list-grid'.$_GET['tabid'],
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

<div class="row">
 <div id="pre-alert-cargo-list-view-summary"?>
      <?php
         $this->renderPartial('_sub_cargos_pre_alert_summary', [
            'preAlert' => $preAlert,
            'name' =>'All',
         ]);
    ?>
  </div>
</div>


<br/>
<div class="form">
    <div class="row">
   <?php  echo CHtml::button('Show All', ['class' => 'show_all']);?>
    </div>
</div>

<div class="row">
  <span> <h2> Available Cargos </h2></span>
 <div id="cargo-list-view-summary"?>
      <?php
         $this->renderPartial('_sub_cargos_summary', [
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
    <?php $cargoListUrl = "cargoProcess/summary"?>;

     $('#cargos-client-view<?=$_GET['tabid']?>',panel).on("click", "table tbody td", function(event){
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
                $('#cargo-list-view-summary',panel).html(resp);
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
     $('a.export_search', panel).on('mousedown', function(){
        var q = $('.filters input, .filters select', panel).serialize();
        $(this).attr('href', $(this).data('baseurl') + '&' + q);
    });
})
  
</script>