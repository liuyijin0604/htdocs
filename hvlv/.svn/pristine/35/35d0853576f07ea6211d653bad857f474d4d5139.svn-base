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
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/pos.css" />
<title>PCA Express</title>
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
	  <div class="navbar-brand">PCA Express</div></div>
<nav class="collapse navbar-collapse bs-navbar-collapse">
	  <ul class="nav navbar-nav navbar-right">
		<li class="dropdown">
			<a href="#" class="dropdown-toggle non-ajax" data-toggle="dropdown"><?=$this->t('Menu');?>
				<b class="caret"></b></a>
			<ul class="dropdown-menu">
<li><a href="<?=str_replace('.html?hash=', '/', $this->createUrl('qr/cns', ['hash' => Yii::app()->session['hash']]));?>"><span class="glyphicon glyphicon-barcode"></span> <?=$this->t('New Shipment');?></a></li>
<li><a href="<?=$this->createUrl('client/shipments');?>" class="ajax-link"><span class="glyphicon glyphicon-list"></span> <?=$this->t('View Shipments');?></a></li>
<li><a href="<?=$this->createUrl('client/help');?>" class="ajax-link"><span class="glyphicon glyphicon-question-sign"></span> <?=$this->t('Help');?></a></li>
			</ul>
		</li>
	  </ul>
	</nav>
  </div>
	<div class="hredbar"></div>
</header>

<div id="main" class="container"><?=$content;?></div>

<footer>
<p align="center"><br />&copy; <?=date('Y');?> <a href="http://www.pcaexpress.com.au/" target="_blank">PCA Express</a></p>
</footer>
<div id="notifc" class='notifications bottom-right'></div>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/pos.min.js"></script>
</body>
</html>
