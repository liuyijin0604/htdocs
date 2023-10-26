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
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/js/pcadb.min.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/js/jquery.ba-bbq.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/js/jquery.yiigridview.js"></script>
</head>
<body data-baseurl="<?=Yii::app()->request->baseUrl;?>">
<header class="bar bar-nav">
	<a href="javascript:window.history.back();" class="nav-back icon icon-left-nav pull-left"></a>
	<a href="#popover" class="icon icon-bars pull-right"></a>
	<h1 class="title">TLA Booking</h1>
</header>

<footer class="bar bar-tab">
<nav>
<?php
$nav = [
	['Home', $this->createUrl('booking/deliveryBooking'), 'home'],
	['Booking', $this->createUrl('booking/createBooking'), 'star-filled'],
];
foreach($nav as $n){
	echo '<a class="tab-item', ($n[1] == Yii::app()->request->getUrl()? ' active' : '') ,'" href="',$n[1],'" data-transition="slide-in">
    <span class="icon icon-',$n[2],'"></span>
    <span class="tab-label">',$n[0],'</span>
  </a>';
}
?>
</nav>
</footer>
<div id="popover" class="popover">
  <ul class="table-view">
    <li class="table-view-cell"><a href="<?=$this->createUrl('booking/deliveryBooking');?>" data-transition="slide-in">My Bookings</a></li>
 
  </ul>
</div>

<div id="main-content" class="content"><?=$content;?></div>

<div id="ajax-modal" class="modal">
<header class="bar bar-nav"><span class="icon icon-close pull-right"></span><h1 class="title"></h1></header>
<div class="content modal-content"></div>
</div>
<div id="login-modal" class="modal">
<header class="bar bar-nav"><h1 class="title">TLA Booking Login</h1></header>
<div class="content"><div class="content-padded">
<form id="login-form" action="<?=$this->createUrl('site/auth');?>" method="post">
  <input type="text" placeholder="User Name" name="LoginForm[user]" />
  <input type="password" placeholder="Password" name="LoginForm[pwd]" />
  <input class="pull-left" type="text" name="LoginForm[vvc]" placeholder="CAPTCHA" autocomplete="off" style="width:60%" />
  <img class="media-object pull-right" alt="CAPTCHA" title="Click to reload" id="lf_capcha" src="" data-url="<?=$this->createUrl('site/captcha');?>" style="width:40%; max-width:140px;" />
  <button type="submit" class="btn btn-primary btn-block">Login</button>
</form>
</div></div>
</div>
<div id="notifc" class='notifications top-right'></div>
</body>
</html>