<style type="text/css">
	
	.container {
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto
}


@media (min-width: 1600px) {
    .container {
        width: 1600px;
    }
}



</style>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/echarts.min.js"></script>
<?php
	echo CHtml::link("goSummary",$this->createUrl('warehouseProcess/checkInIndex')."?depot=".$depot,['id'=>'goSummary','style'=>'display:none;']);
?>
<div id = "checkin_dash" style="width:90em;height:45em;display: inline-block;margin-top:2em;">
</div>


<script type="text/javascript">
	
var chartDom = document.getElementById('checkin_dash');
var myChart = echarts.init(chartDom);
var option;
option = {
  title: {
    text: 'Checkin Total Weight of All Scanner In <?=$depot?> Today',
    left: 'center',
    textStyle:{
      fontSize:'2em'
    }
  },
  xAxis: {
    type: 'category',
    data: [
      <?php
      if(!empty($provide))
      {
        echo "'STANDARD',";
      }else
      {
        echo "'STANDARD'";
      }
      ?>
      <?php
      if(!empty($provide))
      {
        echo "'".join("','",array_column($provide,'user'))."'";
      }
      ?>]
  },
  yAxis: {
    type: 'value'
  },
  series: [
    {
      data: [
        <?php
        echo "{value:13600,itemStyle:{color:'rgb(174,245,139)'}}";
        foreach ($provide as $key => $value) {
          echo ",{value:".$value['total_weight'].",itemStyle:{color:'rgb(91,121,214)'}}";
        }
      ?>
      ],
      type: 'bar'
    }
  ]
};

option && myChart.setOption(option);

myChart.on('click', function(params){
	$('#goSummary').attr('href','<?=$this->createUrl('warehouseProcess/checkInIndex')?>'+'?depot=<?=$depot?>');
	$('#goSummary')[0].click();
});

 function myFunction() {
    $('#goSummary').attr('href','<?=$this->createUrl('warehouseProcess/checkInIndex')?>'+'?depot=<?=$depot?>');
    $('#goSummary')[0].click();
  }

  // Schedule the function to run every 5 minutes
  setInterval(myFunction, 30 * 1000); // 5 minutes = 5 * 60 seconds * 1000 milliseconds



</script>