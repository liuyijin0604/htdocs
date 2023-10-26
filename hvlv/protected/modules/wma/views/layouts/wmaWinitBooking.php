<!DOCTYPE html>
<html>
<head>
	<title>WMS.App</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="initial-scale=1, maximum-scale=1" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<link type="text/css" rel="stylesheet" href="<?=Yii::app()->request->baseUrl; ?>/ratchet/css/ratchet.min.css" />
	<link type="text/css" rel="stylesheet" href="<?=Yii::app()->request->baseUrl; ?>/ratchet/css/ratchet-theme-ios.min.css" />
	<link type="text/css" rel="stylesheet" href="<?=Yii::app()->request->baseUrl; ?>/js/pickadate/themes/default.css" />
	<link type="text/css" rel="stylesheet" href="<?=Yii::app()->request->baseUrl; ?>/js/pickadate/themes/default.date.css" />
	<link type="text/css" rel="stylesheet" href="<?=Yii::app()->request->baseUrl; ?>/css/wma.css" />
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/ratchet/js/ratchet.min.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/js/jquery.ba-bbq.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/js/jquery.yiigridview.js"></script>
</head>
<body data-baseurl="<?=Yii::app()->request->baseUrl;?>">
<header class="bar bar-nav">
	<a href="javascript:window.history.back();" class="nav-back icon icon-left-nav pull-left"></a>
	<a href="#popover" class="icon icon-bars pull-right"></a>
	<h1 class="title">TLA Delivery Booking</h1>
</header>

<div id="main-content" class="content"><?=$content;?></div>

<div id="ajax-modal" class="modal">
<header class="bar bar-nav"><span class="icon icon-close pull-right"></span><h1 class="title"></h1></header>
<div class="content modal-content"></div>
</div>

</div>
<div id="notifc" class='notifications top-right'></div>
</body>
</html>