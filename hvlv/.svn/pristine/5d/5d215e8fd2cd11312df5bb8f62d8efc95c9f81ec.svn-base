<?php $podName=Org::dptList()[$_GET['pod_id']];?>

<h2><?=$podName?> Cargo Processing</h2>
<div style="position: absolute; right:80px;">
    <?php
    $requestUrl = "topCourierService/cargoList";
    $processTypes =CargoProcess::$processTypes_courier_service;
    $statusStr = 'CargoProcess::$processTypes_courier_service';
    if($cargo_type == CargoProcess::NORMAL_CARGO)
    {
      $processTypes = CargoProcess::$processTypes_normal;
    }

    if($cargo_type == CargoProcess::FBA_CARGO)
    {
      $requestUrl = "topCourierService/fbaList";
    }

    if($cargo_type == CargoProcess::B2B_CARGO)
    {
      $requestUrl = "topCourierService/b2bList";
    }

    if($cargo_type == CargoProcess::INTERSTATE_CARGO)
    {
      $requestUrl = "topCourierService/interstateList";
    }

     if($cargo_type == CargoProcess::PICKUP_CARGO)
    {
      $requestUrl = "topCourierService/pickupList";
      $processTypes = CargoProcess::$processTypes_pickup;
      $statusStr ='CargoProcess::$processTypes_pickup';
    }

    if($cargo_type != CargoProcess::PICKUP_CARGO)
    {
     echo "<a class=\"tab_link\"  href=\"" .$this->createUrl("topCourierService/preAlert", ['cargo_type'=>$cargo_type,'pod_id'=>$_GET['pod_id']])
       ."\" title=\"{$podName} ".CargoProcess::$cargoTypeList[$cargo_type]." pre alert\" ><span style=\"background-position:-48px -688px\" class=\"icon\"></span>Pre Alert</a>";
    }
    foreach ($processTypes as $key => $value) {
        if($key==CargoProcess::DELETED) continue;
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

<div style="width:50%;<?php if(!empty($amazonWeekPlan)):?>float: left<?php endif;?>" id="cargos-client-view<?=$_GET['tabid']?>">
   <?php $this->widget('zii.widgets.grid.CGridView', [
    'id'=>'cargos-client-list-grid'.$_GET['tabid'],
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

<div  style="float: left">
  <h2>Amazon Plan</h2>
<?php if(!empty($amazonWeekPlan)):?>
<div class="grid-view" style="width:100%;">
  <table class="items">
    <thread>
      <tr>
        <?php
        echo "<th>Date</th>";
        foreach ($amazonWeekPlan[0] as $key => $w) {
          echo "<th>".$w[0]."</th>";
        }

        ?>

      </tr>
    </thread>

    <tbody>
      <tr>
        <?php
        echo "<td>Pallet</td>";
        foreach ($amazonWeekPlan[0] as $key => $w) {
          echo "<td>".$w[1]."</td>";
        }

        ?>
      </tr>
    </tbody>

  </table>
</div>

<div class="grid-view" style="width:100%;">
  <table class="items" >
    <thread>
      <tr>
        <?php
        echo "<th>Date</th>";
        foreach ($amazonWeekPlan[1] as $key => $w) {
          echo "<th>".$w[0]."</th>";
        }

        ?>

      </tr>
    </thread>

    <tbody>
      <tr>
        <?php
         echo "<td>Pallet</td>";
        foreach ($amazonWeekPlan[1] as $key => $w) {
          echo "<td>".$w[1]."</td>";
        }

        ?>
      </tr>
    </tbody>

  </table>
</div>
</div>

<?php endif;?>




<br/>
<div class="form">
    <div class="row">
   <?php  echo CHtml::button('Show All', ['class' => 'show_all']);?>
    </div>
</div>
<div style="right: 20px;position: absolute;">
  <?php if($cargo_type==CargoProcess::FBA_CARGO):?>
  <a class="tab_link grid_edit_btn" target="_blank" title="AmazonPlan" href="<?=$this->createUrl('importsAmazon/CreateAmazonPlan',['pod_id'=>$_GET['pod_id']]);?>"> Edit Amazon Plan</a> 
<?php endif;?>
  <a class="tab_link grid_edit_btn" target="_blank" title="CreateJob<?=$cargo_type.$_GET['pod_id']?>" href="<?=$this->createUrl('topCourierService/createCargoJob', ['typ' => $cargo_type, 'pod_id'=>$_GET['pod_id']]);?>"> Create C-Job</a> 
  <a class="jqm_link grid_edit_btn" href="<?=$this->createUrl('topCourierService/splitShipment');?>"> Split Shipment</a>
  <a class="jqm_link grid_edit_btn" href="<?=$this->createUrl('topCourierService/exportCargoProcessReport');?>"> Export Cargo Process Report</a> 
</div>
 <div class="row">
  <span> <h2> Available Cargos </h2></span>
                    <!--<div id="loadingPic"  style="width:20px;height:20px;float:left;"></div>-->
 <div id="cargo-list-view<?=$_GET['pod_id'].$cargo_type?>" style="width:100%;">
      <?php
         $this->renderPartial('_sub_cargos', [
            'cargo' => $cargo,
            'name' =>'All',
            'pod_id' =>$_GET['pod_id'],
            'cargo_type' =>$cargo_type
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

     $('#cargos-client-view<?=$_GET['tabid']?>',panel).on("click", "table tbody td", function(event){
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
                $('#cargo-list-view<?=$_GET['pod_id'].$cargo_type?>',panel).html(resp);
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
                $('#cargo-list-view<?=$_GET['pod_id'].$cargo_type?>').html(resp);
            },
        });
        
    });

})
  
</script>