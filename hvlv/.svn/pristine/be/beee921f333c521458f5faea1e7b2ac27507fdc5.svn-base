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
	<link type="text/css" rel="stylesheet" href="<?=Yii::app()->request->baseUrl; ?>/../js/pickadate/themes/default.css" />
	<link type="text/css" rel="stylesheet" href="<?=Yii::app()->request->baseUrl; ?>/../js/pickadate/themes/default.date.css" />
	<link type="text/css" rel="stylesheet" href="<?=Yii::app()->request->baseUrl; ?>/../css/wma.css" />
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/../js/jquery.min.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/../js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/ratchet/js/ratchet.min.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/../js/wma-v1.min.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/../js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/../js/jquery.ba-bbq.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/../js/jquery.yiigridview.js"></script>
	<script type="text/javascript" src="<?=Yii::app()->request->baseUrl; ?>/../js/jquery.multifile.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body data-baseurl="<?=Yii::app()->request->baseUrl;?>">
<header class="bar bar-nav">
	<a href="javascript:window.history.back();" class="nav-back icon icon-left-nav pull-left"></a>
	<a href="#popover" class="icon icon-bars pull-right"></a>
	<h1 class="title">WMS.App</h1>
</header>

<footer class="bar bar-tab">
<nav>
<?php
$user = User::model()->findByPk(Yii::app()->user->id);
if (empty($user->extra['wma_settings'])) {
	$nav = [
		// ['Home', Yii::app()->request->baseUrl.'/', 'home'],
		['Home', '/wma', 'home'],
		['Stock', $this->createUrl('stock/list'), 'star-filled'],
		['History', $this->createUrl('site/history'), 'more'],
		['Dashboard', $this->createUrl('site/Dashboard'), 'list'],
	];
	if (in_array(Yii::app()->user->id, WmsTask::$op) || (isset(Yii::app()->user->grp) && Yii::app()->user->grp == 0)) {
		$nav[] = ['Picking', $this->createUrl('job/batchPicking'), 'pages'];
		$nav[] = ['Sorting', $this->createUrl('job/batchSorting'), 'pages'];
		$nav[] = ['Adhoc', $this->createUrl('job/adhocTasks'), 'list'];
		$nav[] = ['RTS Check', $this->createUrl('return/check'), 'pages'];
		$nav[] = ['Quick Label', $this->createUrl('job/quickCourierLabel'), 'pages'];
	}
} else {
	$nav = [['Home', Yii::app()->request->baseUrl.'/', 'home']];
	if (!empty($user->extra['wma_settings']['stock'])) {
		$nav[] = ['Stock', $this->createUrl('stock/list'), 'star-filled'];
	}
	if (!empty($user->extra['wma_settings']['history'])) {
		$nav[] = ['History', $this->createUrl('site/history'), 'more'];
	}
	if (!empty($user->extra['wma_settings']['dashboard'])) {
		$nav[] = ['Dashboard', $this->createUrl('site/Dashboard'), 'list'];
	}
	if (!empty($user->extra['wma_settings']['batch_picking'])) {
		$nav[] = ['Picking', $this->createUrl('job/batchPicking'), 'pages'];
	}
	if (!empty($user->extra['wma_settings']['batch_sorting'])) {
		$nav[] = ['Sorting', $this->createUrl('job/batchSorting'), 'pages'];
	}
	if (!empty($user->extra['wma_settings']['adhoc_tasks'])) {
		$nav[] = ['Adhoc', $this->createUrl('site/adhocTasks'), 'list'];
	}
	if (!empty($user->extra['wma_settings']['rts_check'])) {
		$nav[] = ['RTS Check', $this->createUrl('return/check'), 'pages'];
	}
	if (!empty($user->extra['wma_settings']['quick_label'])) {
		$nav[] = ['Quick Label', $this->createUrl('job/quickCourierLabel'), 'pages'];
	}
}

foreach($nav as $k => $n){
	echo '<a class="tab-item', ($n[1] == Yii::app()->request->getUrl()? ' active' : '') ,'" href="',$n[1],'" data-transition="slide-in">
	<span class="icon icon-',$n[2],'"></span>
	<span class="tab-label">',$n[0],'</span>
	</a>';
}
?>
</nav>
</footer>
<div id="popover" class="popover">
  <ul class="table-view" style="max-height: 500px">
    <li class="table-view-cell"><a href="<?=$this->createUrl('job/list');?>" data-transition="slide-in">Jobs</a></li>
    <li class="table-view-cell"><a href="<?=$this->createUrl('prod/list');?>" data-transition="slide-in">Products</a></li>
	<li class="table-view-cell">
		<a href="<?=$this->createUrl('job/putaway');?>" data-transition="slide-in">Put Away</a>
	</li>
	<li class="table-view-cell">
		<a href="<?=$this->createUrl('job/relocate');?>" data-transition="slide-in">Stock Relocation</a>
	</li>
	<li class="table-view-cell">
		<a href="<?=$this->createUrl('job/storage', array('type' => 'in'));?>" data-transition="slide-in">Storage In</a>
	</li>
	<li class="table-view-cell">
		<a href="<?=$this->createUrl('job/storage', array('type' => 'search'));?>" data-transition="slide-in">Storage Out</a>
	</li> 
	<li class="table-view-cell">
		<a href="<?=$this->createUrl('job/dayBookingSummary');?>" data-transition="slide-in">Booking Record</a>
	</li>
	<li class="table-view-cell">
		<a href="<?=$this->createUrl('job/delivery');?>" data-transition="slide-in">Create Booking Record</a>
	</li>
	<li class="table-view-cell">
		<a href="<?=$this->createUrl('job/setPltInfo');?>" data-transition="slide-in">Pallet Info</a>
	</li>
    <li class="table-view-cell"><a href="<?=$this->createUrl('site/settings');?>" data-transition="slide-in">Settings</a></li>
	
    <li class="table-view-cell"><a href="<?=$this->createUrl('site/logout');?>" data-ignore="push">Log Out</a></li>
  </ul>
</div>

<div id="main-content" class="content"><?=$content;?></div>

<div id="ajax-modal" class="modal">
<header class="bar bar-nav"><span class="icon icon-close pull-right"></span><h1 class="title"></h1></header>
<div class="content modal-content"></div>
</div>

<div id="login-modal" class="modal">
<header class="bar bar-nav"><h1 class="title">WMS.App Login</h1></header>
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