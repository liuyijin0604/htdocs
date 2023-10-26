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
	<?php if(Yii::app()->language != 'en'): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/pos-<?=Yii::app()->language?>.css" />
	<?php endif; ?>
	<title>PCA Express - POS</title>
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
	  <a href="<?=$this->createUrl('site/index');?>" class="navbar-brand ajax-link">PCAE POS</a></div>
	<nav class="collapse navbar-collapse bs-navbar-collapse">
	  <ul class="nav navbar-nav navbar-right">
		<li class="dropdown">
			<a href="#" class="dropdown-toggle non-ajax" data-toggle="dropdown">Menu
				<b class="caret"></b></a>
			<ul class="dropdown-menu">
			<li><a href="<?=$this->createUrl('shipment/tracking');?>" class="ajax-link"><span class="glyphicon glyphicon-search"></span> Tracking</a></li>
<li><a href="<?=$this->createUrl('shipment/create');?>" class="ajax-link"><span class="glyphicon glyphicon-barcode"></span> New Shipment</a></li>
<li><a href="<?=$this->createUrl('shipment/manage');?>" class="ajax-link"><span class="glyphicon glyphicon-list"></span> Manage Shipments</a></li>
<?php if(!Yii::app()->user->isGuest && Yii::app()->user->grp == 80): ?>
<li><a href="<?=$this->createUrl('accounts/index');?>" class="ajax-link"><span class="glyphicon glyphicon-usd"></span> Accounts</a></li>
<li><a href="<?=$this->createUrl('reports/index');?>" class="ajax-link"><span class="glyphicon glyphicon-stats"></span> Reports</a></li>
<?php endif; ?>
<li><a href="<?=$this->createUrl('tools/index');?>" class="ajax-link"><span class="glyphicon glyphicon-wrench"></span> Tools</a></li>
<li><a href="<?=$this->createUrl('app/settings');?>" class="ajax-link"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
<li><a href="<?= $this->createUrl('hc/make'); ?>" class="ajax-link"><span
                class="glyphicon glyphicon-gift"></span> Make HC Order</a></li>
<li><a href="<?= $this->createUrl('hc/history'); ?>" class="ajax-link"><span
                class="glyphicon glyphicon-equalizer"></span> HC Order History</a></li>
<li role="presentation" class="divider"></li>
		<li><a href="<?=$this->createUrl('site/logout');?>"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>
			</ul>
		</li>
	  </ul>
	</nav>
  </div>
	<div class="hredbar"></div>
</header>

<div id="main" class="container"><?=$content;?></div>

<footer>
<p align="center"><span id="uinfo"></span><br />&copy; <?=date('Y');?> <a href="http://www.pcaexpress.com.au/" target="_blank">PCA Express</a></p>
</footer>
<!-- Login Modal -->
<div class="modal fade" id="modal-login" tabindex="-1" role="dialog" aria-labelledby="modal-login-label" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h4 class="modal-title" id="modal-login-label"><?=$this->t('PCAE POS Login');?></h4>
	  </div>
	  <form id="login-form" action="<?=$this->createUrl('site/auth');?>" method="POST">
	  <div class="modal-body">
	<div class="form-group">
		<label><?=$this->t('User Name');?></label>
		<input type="text" class="form-control input-lg" name="LoginForm[user]" />
	</div>
	<div class="form-group">
		<label><?=$this->t('Password');?></label>
		<input type="password" class="form-control input-lg" name="LoginForm[pwd]" />
	</div>
	<div>
		<div class="row">
			<div class="col col-md-6">
				<label><?=$this->t('Captcha');?></label>
				<input type="text" class="form-control input-lg" name="LoginForm[vvc]" autocomplete="off" />
			</div>
			<div class="col col-md-6">
				<img style="cursor:pointer;" alt="CAPTCHA" title="Click to reload" id="lf_capcha" src="<?=$this->createUrl('site/captcha').'?'.time();?>" />
			</div>
		</div>
	</div>
	  </div>
	  <div class="modal-footer">
		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t('Login');?></button>
	  </div>
	  </form>
	</div>
  </div>
</div>
<div id="notifc" class='notifications bottom-right'></div>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/pos.min.js"></script>
</body>
</html>
