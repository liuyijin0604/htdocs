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
	echo CHtml::link("goSummary",$this->createUrl('warehouseProcess/getCheckinDash')."?depot=".$depot,['class'=>'tab_link','id'=>'goSummary','style'=>'display:none;']);
?>
<div id = "checkin_dash<?=$_GET['tabid']?>" style="width:110em;height:50em;display: inline-block;">
</div>


<script type="text/javascript">
	
var chartDom = document.getElementById('checkin_dash<?=$_GET['tabid']?>');
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
    data: ['<?php
      echo join("','",array_column($provide,'user'));
      ?>']
  },
  yAxis: {
    type: 'value'
  },
  series: [
    {
      data: [
        <?php
      echo join(",",array_column($provide,'total_weight'));
      ?>
      ],
      type: 'bar'
    }
  ]
};

option && myChart.setOption(option);


</script>