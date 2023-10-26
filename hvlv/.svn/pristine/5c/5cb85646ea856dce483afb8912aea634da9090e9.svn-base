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
	<title>CG Order</title>
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
	  <a href="<?=$this->createUrl('site/index');?>" class="navbar-brand">PCAE HCDG / PCAE耗材订购</a></div>
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
<p align="center"><span id="uinfo"></span><br />&copy; <?=date('Y');?> <a href="http://www.pcaexpress.com.au/" target="_blank">PCA Express</a></p>
</footer>
<!-- Login Modal -->
<div class="modal fade" id="modal-login" tabindex="-1" role="dialog" aria-labelledby="modal-login-label" aria-hidden="true" style="display: block;">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h4 class="modal-title" id="modal-login-label"><?=$this->t('Customer Login');?></h4>
	  </div>
	  <form id="login-form" action="<?=$this->createUrl('site/auth');?>" method="POST">
	  <div class="modal-body">
	<div class="form-group">
		<label><?=$this->t('User Name');?></label>
		<input type="text" class="form-control input-lg" name="LoginForm[user]" />
	</div>
	<div class="form-group">
		<label><?=$this->t('Password');?></label>  <a href="<?=$this->createUrl('accounts/resetpassword')?>" style="text-decoration: none;">忘记密码</a>
		<input type="password" class="form-control input-lg" name="LoginForm[pwd]" />
	</div>
	<div>
		<div class="row">
			<div class="col col-md-6">
				<label><?=$this->t('Captcha');?></label>
				<input type="text" class="form-control input-lg" name="LoginForm[vvc]" autocomplete="off" />
			</div>
			<div class="col col-md-6">
				<img style="cursor:pointer; margin-top:20px;" alt="CAPTCHA" title="Click to reload" id="lf_capcha" src="<?=$this->createUrl('site/captcha').'?'.time();?>" />
			</div>
		</div>
	</div>
	  </div>
	  <div class="modal-footer">
	  	<div style="position:absolute;"><small>初始用户名及密码会代理号</small></div>
		<button type="submit" class="btn btn-primary btn-lg"><?=$this->t('Login');?></button>
		<!-- <a class="btn btn-danger btn-lg" href="<?=$this->createUrl('accounts/resetpassword');?>">Forget</a> -->
	  </div>
	  </form>
	</div>
  </div>
</div>
<div id="notifc" class='notifications bottom-right'></div>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.combo.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/cg.min.js"></script>
</body>
</html>