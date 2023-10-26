<style type="text/css">
	
	.container {
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto
}


@media (min-width: 1600px) {
    .container {
        width: 1800px;
    }
}



</style>
</br>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/echarts.min.js"></script>
<?php
	echo CHtml::link("goSummary",$this->createUrl('warehouseProcess/getCheckinDash'),['id'=>'goSummary','style'=>'display:none;']);
	echo CHtml::link("goDetail",$this->createUrl('warehouseProcess/getCheckinDashDetail'),['id'=>'goDetail','style'=>'display:none;']);
?>
<div id = "checkin_dash_left" style="width:55em;height:40em;display: inline-block;margin-right:0em;">
</div>
<div id = "checkin_dash_right" style="width:25em;height:40em;display: inline-block;">
</div>


<script type="text/javascript">
	
var chartDom = document.getElementById('checkin_dash_left');
var myChart = echarts.init(chartDom);
var option;
var colors = ['rgb(255,112,112)', 'rgb(159,223,127)', 'rgb(92,123,217)'];
option = {
  title: {
    text: 'Checkin of <?=$provide['depot']?> Today',
    textStyle:{
    	fontSize:'2em'
    },
    subtext: 'Total:<?=$provide['total']?>t    On Hand:<?=$provide['onHand']?>t',
    left: 'center',
    subtextStyle:{
    	fontSize:'2em'
    }
  },
  tooltip: {
    trigger: 'item'
  },
  legend: {
    orient: 'vertical',
    left: 'left'
  },
  series: [
    {
      name: 'Scan Data',
      type: 'pie',
      radius: '65%',
      label: {
      textStyle: {
        fontSize: '2em' // Set the font size for the labels within the pie chart
      }
      },
      data: [
        { value: <?=$provide['left']?>, name: 'Left: <?=$provide['left']?>t(<?=$provide['leftPercent']?>)'},
        { value: <?=$provide['done']?>, name: 'Done: <?=$provide['done']?>t(<?=$provide['scanPercent']?>)' },
        { value: <?=$provide['onRoad']?>, name: 'On Road: <?=$provide['onRoad']?>t' }
      ],
      emphasis: {
        itemStyle: {
          shadowBlur: 10,
          shadowOffsetX: 0,
          shadowColor: 'rgba(0, 0, 0, 0.5)'
        }
      }
    }
  ],
  color: colors
};

option && myChart.setOption(option);





var chartDom2 = document.getElementById('checkin_dash_right');
var myChart2 = echarts.init(chartDom2);
var option2;

option2 = {
	title: {
	    text: 'Done Percent In All Depots',
	    textStyle:{
	    	fontSize:'1.8em'
	    },
	  },
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'shadow'
    }
  },
  dataset: {
    source: [
      ['Percentage', 'Depots'],
      <?php
      foreach ($provide['rightData'] as $key => $value) {
      	echo "[".$value.", '".$key."'],";
      }
      ?>
    ]
  },
  legend: {},
  grid: {
    left: '3%',
    right: '4%',
    bottom: '3%',
    containLabel: true
  },
  grid: { containLabel: true },
  xAxis: {
  	name: '(%)',
  	axisLabel: {
      fontSize: '1.7em' // Set the font size for the yAxis labels
    }
  },
  yAxis: {
  	type: 'category',
  	axisLabel: {
      fontSize: '1.7em' // Set the font size for the yAxis labels
    },
    data:[
    	  <?php
    	  echo '"'.join('","',array_keys($provide['rightData'])).'"';
	      ?>
    	]
  },

  series: [
    {
      type: 'bar',
      encode: {
        // Map the "amount" column to X axis.
        x: 'Percentage',
        // Map the "product" column to Y axis
        y: 'Depots'
      }
    }
  ],
  color:"rgb(159,223,127)"
};



// option2 = {
//   title: {
//     text: 'All Depots'
//   },
//   tooltip: {
//     trigger: 'axis',
//     axisPointer: {
//       type: 'shadow'
//     }
//   },
//   legend: {},
//   grid: {
//     left: '3%',
//     right: '4%',
//     bottom: '3%',
//     containLabel: true
//   },
//   xAxis: {
//     type: 'value',
//     boundaryGap: [0, 0.01]
//   },
//   yAxis: {
//     type: 'category',
//     data: ['Percentage %']
//   },
//   series: [
//     {
//       name: 'SYD',
//       type: 'bar',
//       data: ['80']
//     },
//     {
//       name: 'MEL',
//       type: 'bar',
//       data: ['90']
//     },
//     {
//       name: 'BNE',
//       type: 'bar',
//       data: ['70']
//     }
//   ]
// };



option2 && myChart2.setOption(option2);
myChart2.on('click', function(params){
	$('#goSummary').attr('href','<?=$this->createUrl('warehouseProcess/getCheckinDash')?>'+'?depot='+params.name.split("(")[0]);
	$('#goSummary')[0].click();
});
myChart.on('click', function(params){
	$('#goDetail').attr('href','<?=$this->createUrl('warehouseProcess/getCheckinDashDetail')?>'+'?depot=<?=$provide['checkDepotId']?>');
	$('#goDetail')[0].click();
});
let intervalID = 0;
 function myFunction()
 {
    var links = $('ul li.ui-tabs-active a');
    links.each(function()
    {
      var link = $(this);
      if (link.hasClass('active')) {
         if(link.html()=="Check In Dash")
        {
        $('#goDetail').attr('href','<?=$this->createUrl('warehouseProcess/getCheckinDashDetail')?>'+'?depot=<?=$provide['checkDepotId']?>');
        $('#goDetail')[0].click();
        }else
        {
          clearInterval(intervalID);
        }
      }
    });
  }

  // Schedule the function to run every 5 minutes
  intervalID = setInterval(myFunction, 30 * 1000); // 5 minutes = 5 * 60 seconds * 1000 milliseconds

</script>