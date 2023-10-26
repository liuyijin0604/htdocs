<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="en" />
<meta name="viewport" content="width=320,initial-scale=1, maximum-scale=1" />
<title>PCA Express Scan</title>
<style type="text/css">
* { box-sizing: border-box; }
body {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
	background-color: #000;
	color: #f1f1f1;
	padding: 20px 0 50px 0;
	margin: 0;
}
a{
	color: #BE3426;
}
nav a{
	display: block;
	width: 100%;
	color: #ccc;
	background: #666;
	font-size: 14px;
	font-weight: bold;
	text-align: center;
	margin: 0;
	line-height: 40px;
	text-decoration: none;
	border-bottom: 1px solid #ccc;
}
nav a.hi{
	color: #333;
	background: #ccc;
}
#olay{
	position: absolute;
	top: 0;
	width: 100%;
	height: 100%;
	z-index: 99;
	background: #000;
	text-align: center;
	padding-top: 100px;
	font-size: 20px;
	font-weight: bold;
}
#main, #log{
	padding: 10px;
	margin: 0;
	clear: both;
}
#log{
	height: 350px;
	overflow: auto;
}
#log p{
	margin: 0;
	padding-bottom: 5px;
	lin-height: 15px;
}
#log p.err{
	color: #f22;
}
#log img{
	background: #ccc;
}
h2{
	margin: 0;
	padding-bottom: 10px;
	font-size: 16px;
}
.btn{
	background-color: #666; 
	color: #ccc;
	font-weight: bold; 
	font-size: 16px; 
	text-align: center;
	height: 35px;
	padding: 5px 20px;
	border: #ccc 2px solid;
	border-radius: 15px;
	margin: 5px 0 10px 0;
}
label{
	line-height: 28px;
}
.field{
	background-color: #000;
	color: #f1f1f1;
	font-weight: bold;
	font-size: 16px;
	border: #666 2px solid;
	padding: 4px 8px;
	line-height: 20px;
	margin-bottom: 10px;
}
.clear, .row{
	clear: both;
}
.row .col, .left{
	float: left;
}
.row .col input{
	max-width: 100%;
}
.row .col .field, .c12{
	width: 100%;
}
.c12{
	width: 100%;
}
.c9{
	width: 75%;
}
.c8{
	width: 66.66%;
}
.c6{
	width: 50%;
}
.c4{
	width: 33.33%;
}
.c3{
	width: 25%;
}
button.sound-ctrl {
	position: fixed;
	width: 32px;
	height: 32px;
	border: 2px solid #ccc;
	border-radius: 8px;
	bottom: 5px;
	right: 60px;
	background: #000 url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAABABAMAAACJoGidAAAAA3NCSVQICAjb4U/gAAAALVBMVEX////MzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMzMxkerMOAAAAD3RSTlMAETNEVWZ3iJmqu8zd7v9lNdiyAAAACXBIWXMAAAsSAAALEgHS3X78AAAAHHRFWHRTb2Z0d2FyZQBBZG9iZSBGaXJld29ya3MgQ1M26LyyjAAAABZ0RVh0Q3JlYXRpb24gVGltZQAwMi8yMi8xNTD6qGwAAAC5SURBVDiNY2CgJ2BD4zPtQxOoe4fK136HJMCyatWqd0ABMQYGFbAA+zswYJjXwHRPAVlA94XuCxQVTPfeNaAIMMS9YcCvAsMMuC0Y7sBwKVa/YPoWIzyGNwD5VlsBwQeHx7wDCAFgiGkKyD6H80FhWlfADokpWKjLPmR8ZwASgMUL+zOGeQXIAkwvGeoWIAswvGKwI1EAwwy4LUjuuOeA7NK8Ara3KH4RQ/YLxLd9B9HDw4CkIBx0AAA1Tpx/gqjy9gAAAABJRU5ErkJggg==') center -30px no-repeat;
}
button.sound-ctrl.off{
	background-position: center 0;
}
ul.ac_list { list-style: none; border-top: 1px solid #666; padding: 0;}
ul.ac_list li { padding: 10px; border-bottom: 1px solid #666; line-height: 20px; font-size: 20px; }
</style>
</head>

<body>
<div id="olay">Connecting...</div>
<nav>
<a href="wh_ot">Outturn</a>
<a href="wh_gp">Gate Pass</a>
<a href="wh_es">Exp Sorting</a>
<a href="wh_ep">Exp Picking</a>
<a href="wh_et">Exp Trans Label</a>
<a href="wh_sc">Exp Stock Check</a>
<a href="wh_ec">Exp Pallets</a>
</nav>
<div id="main">
</div>
<div id="log">
</div>
<audio id="sound" src=""></audio>
<button class="sound-ctrl off"></button>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/grabba.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/signature_pad.min.js"></script>
</body>
</html>
