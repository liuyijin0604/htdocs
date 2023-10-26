<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="language" content="<?=Yii::app()->language;?>" />
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico" >
  <link rel="apple-touch-icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/touch-icon.png">
  <link rel="icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/touch-icon.png">
  <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.min.css" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/cg.css" />
	<?php if(Yii::app()->language != 'en'): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/app-zh_cn.css" />
	<?php endif; ?>
	<title>买全险</title>
</head>

<body>
<header class="navbar navbar-static-top bs-docs-nav" id="top" role="banner">
  <div class="container">
	<div class="navbar-header">
	  <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </button>
	  <a href="<?=$this->createUrl('site/index');?>" class="navbar-brand ajax-link">PCAE 买全险</a></div>
<!--	<nav class="collapse navbar-collapse bs-navbar-collapse">
	  <ul class="nav navbar-nav navbar-right">
		<li class="dropdown">
			<a href="#" class="dropdown-toggle non-ajax" data-toggle="dropdown">Menu
				<b class="caret"></b></a>
			<ul class="dropdown-menu">

                <?php
                $isDriver = false;
                if ( !empty(Yii::app()->user) ) {
                    $isDriver =Yii::app()->user->getState('driver') ? true : false;
                }
                ?>
                <?php if ( $isDriver ) : ?>

                <li><a href="<?=$this->createUrl('order/make',['op' => 1]);?>" class="ajax-link"><span class="glyphicon glyphicon-wrench"></span> Make Client Order</a></li>
                <li><a href="<?=$this->createUrl('order/viewcorders');?>" class="ajax-link"><span class="glyphicon glyphicon-equalizer"></span> View Orders</a></li>
                    <li><a href="<?=$this->createUrl('order/makedelivery');?>" class="ajax-link"><span class="glyphicon glyphicon-equalizer"></span> Make a Delivery</a></li>
                <?php else : ?>
                    <li><a href="<?=$this->createUrl('order/make');?>" class="ajax-link"><span class="glyphicon glyphicon-wrench"></span> Make Order</a></li>
                    <li><a href="<?=$this->createUrl('order/history');?>" class="ajax-link"><span class="glyphicon glyphicon-equalizer"></span> Order History</a></li>
                <?php endif; ?>
		        <li><a href="<?=$this->createUrl('site/logout');?>"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>
			</ul>
		</li>
	  </ul>
	</nav> -->
  </div>
	<div class="hredbar"></div>
</header>

<div id="main" class="container"><?=$content;?></div>

<footer>
<p align="center">&copy; <?=date('Y');?> <a href="http://www.pcaexpress.com.au/" target="_blank">PCA Express</a></p>
</footer>
</div>
<div id="notifc" class='notifications bottom-right'></div>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.combo.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
<!-- <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/cg.min.js"></script> -->
</body>
</html>
