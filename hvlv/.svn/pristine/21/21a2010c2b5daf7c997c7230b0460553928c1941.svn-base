<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="language" content="<?=Yii::app()->language;?>" />
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=<?=RecaptchaAPI::RECAPTCHA_V3_SITE_KEY?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico" >
  <link rel="apple-touch-icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/touch-icon.png">
  <link rel="icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/touch-icon.png">
  <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.min.css" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ims.css" />
	<?php if(Yii::app()->language != 'en'): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ims-<?=Yii::app()->language?>.css" />
	<?php endif; ?>
	<title>Top Logistics - Customer Service</title>
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
	  <a href="<?=Yii::app()->baseUrl;?>" class="navbar-brand ajax-link">Top Logistics Customer Service</a></div>
	<nav class="collapse navbar-collapse bs-navbar-collapse">
	  <!-- <ul class="nav navbar-nav navbar-right">
		<li class="dropdown">
			<a href="#" class="dropdown-toggle non-ajax" data-toggle="dropdown">Menu
				<b class="caret"></b></a>
			<ul class="dropdown-menu">
                <li><a href="https://www.toplogistics.com.au"><span class="glyphicon glyphicon-home"></span> Top Logistics</a></li>
			</ul>
		</li>
	  </ul> -->
	</nav>
  </div>
	<div class="hredbar"></div>
</header>

<div id="main" class="container"><?=$content;?></div>

<footer>
<!-- <p align="center"><span id="uinfo"></span><br />&copy; <?=date('Y');?> <a href="http://www.toplogistics.com.au/" target="_blank">Top Logistics</a></p> -->
<p align="center"><span id="uinfo"></span><br />&copy; <?=date('Y');?> <span>Top Logistics</span></p>
</footer>

<div id="notifc" class='notifications bottom-right'></div>
</body>
</html>
