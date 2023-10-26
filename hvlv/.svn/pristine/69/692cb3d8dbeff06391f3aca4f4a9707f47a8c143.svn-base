<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="language" content="<?=Yii::app()->language;?>" />
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.min.css" type="text/css"/>
</head>

<body style="background-color: rgb(238, 238, 238);">
<center>
    <div class="row">
		<div class="col-md-5 col-md-offset-1">
			<table width="100%" align="center">
				<col style="width:50%" span="2">
				<?php
				echo "<tr><td colspan='2'><center><h3><b> Courier Fuel Surcharge:</b></h3></center></td></tr>";
				echo "<tr><td style='border-bottom: double black; padding-bottom:10px; padding-top:10px'><center>Month</center></td><td style='border-bottom: double black; padding-bottom:10px; padding-top:10px'><center>Fuel Surcharge</center></td></tr>";

				$array1 = array();
				$size = count($data);
				foreach ($data as $key => $fuel){
					array_push($array1, "<tr><td style='padding-top:10px'><center>".date('Y-M', strtotime($fuel['from_day'])).($size==$key+1?" to Now":"")."</center></td><td style='padding-top:10px'><center>".($fuel['rate']*100)."%</center></td>");
				};

				for ($i=0; $i <count($array1); $i++){
					echo $array1[$i];
				};

				?>
			</table>
		</div>
		<div class="col-md-5">
			<table width="100%" align="center">
				<col style="width:50%" span="2">
				<?php
				echo "<tr><td colspan='2'><center><h3><b> Container Cartage Fuel Surcharge:</b></h3></center></td><td colspan='2'></tr>";
				echo "<tr><td style='border-bottom: double black; padding-bottom:10px; padding-top:10px'><center>Month</center></td><td style='border-bottom: double black; padding-bottom:10px; padding-top:10px'><center>Fuel Surcharge</center></td></tr>";

				$array2 = array();
				$size = count($data2);
				foreach ($data2 as $key => $fuel1){
					array_push($array2, "<tr><td style='padding-top:10px'><center>".date('Y-M', strtotime($fuel1['from_day'])).($size==$key+1?" to Now":"")."</center></td><td style='padding-top:10px'><center>".($fuel1['rate']*100)."%</center></td></tr>");
				};
				for ($i=0; $i <count($array2); $i++){
					echo $array2[$i];
				};

				?>
			</table>
		</div>
	</div>
</center>
</body>