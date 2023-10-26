<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="language" content="<?=Yii::app()->language;?>" />
<link rel="shortcut icon" href="/client/favicon.ico" >
<link rel="apple-touch-icon" href="/client/images/touch-icon.png">
<link rel="icon" href="/client/images/touch-icon.png">
<link rel="stylesheet" href="/client/css/bootstrap.min.css" type="text/css"/>
<link rel="stylesheet" type="text/css" href="/client/css/pos.css" />
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
<li><a href="<?=$this->createUrl('site/help');?>" class="ajax-link"><span class="glyphicon glyphicon-question-sign"></span> <?=$this->t('Help');?></a></li>
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
<script type="text/javascript" src="/client/js/jquery.min.js"></script>
<script type="text/javascript" src="/client/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/client/js/bootstrap.min.js"></script>
</body>
</html>
