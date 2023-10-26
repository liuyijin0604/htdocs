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
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/whscan2.css?v=1.2" />
	<?php if(Yii::app()->language != 'en'): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/whscan-<?=Yii::app()->language?>.css" />
	<?php endif; ?>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui.min.js"></script>
<script type='text/javascript' src='<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.combo.js'></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/whscan2.min.js"></script>
	<title>Warehouse Scanner</title>
</head>

<body>
<header class="navbar navbar-static-top bs-docs-nav" id="top" role="banner" style="position:fixed;width: 100%;">
  <div class="container">
	<div class="navbar-header">
	  <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </button>
	  
	<a href="<?=$this->createUrl("site/index")?>" class="navbar-brand ajax-link">TLA SCAN</a></div>
	  <ul class="nav navbar-nav navbar-right">
	  	<li>
	  		 <a href="<?php echo $this->createUrl('message/index').'?tab=inbox'?>" class="message-modal-link" 
               style="float:left;"><span class="glyphicon glyphicon-envelope"></span>&nbsp;<label id = 'messageNumber' style="color: red">0</label>&nbsp;<?= $this->t('My Messages'); ?></a>
	  	</li>

	  	<li>
	  		 <a href="<?php echo $this->createUrl('message/index').'?tab=send'?>" class="message-modal-link" 
               style="float:left;"><span class="glyphicon glyphicon-envelope"></span>&nbsp;<?= $this->t('Send Messages'); ?></a>
	  	</li>

	  	<li>
	  		 <a href="<?php echo $this->createUrl('message/getWarehouseTasks')?>" class="message-modal-link"
               style="float:left;"><span class="glyphicon glyphicon-tasks"></span>&nbsp;<?= $this->t('Tasks'); ?></a>
	  	</li>
	  </ul>


	<nav class="collapse navbar-collapse bs-navbar-collapse">
	  <ul class="nav navbar-nav navbar-right">
		<li class="dropdown">
			<a href="#" class="dropdown-toggle non-ajax" data-toggle="dropdown"><b class="caret"></b><?= $this->t('Menu'); ?>
				</a>
			<ul class="dropdown-menu">
                <li><a href="<?=$this->createUrl('shipment/scan',['op'=>'checkin']);?>" class="ajax-link"><span class="glyphicon glyphicon-wrench"></span>Check In</a></li>
                <li><a href="<?=$this->createUrl('shipment/scan',['op' => 'resort']);?>" class="ajax-link"><span class="glyphicon glyphicon-list"></span> Resorting Stock</a></li>
                <li><a href="<?=$this->createUrl('shipment/scan',['op'=>'status']);?>" class="ajax-link"><span class="glyphicon glyphicon-search"></span> Status Check</a></li>
                <li><a href="<?=$this->createUrl('shipment/scan',['op'=>'change']);?>" class="ajax-link"><span class="glyphicon glyphicon-refresh"></span> Change Label</a></li>
		        <li><a href="<?=$this->createUrl('site/logout');?>"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>
			</ul>
		</li>
	  </ul>
	</nav>
  </div>
	<div class="hredbar"></div>
</header>
<div style="width: 100%;height: 4em;">

</div>
<div id="main" class="container"><?=$content;?></div>

<footer>
<p align="center"><span id="uinfo"></span><br />&copy; <?=date('Y');?> <a href="http://www.toplogistics.com.au/" target="_blank">Top Logistics Australia</a></p>
</footer>
<!-- Login Modal -->
<div class="modal fade" id="modal-login" tabindex="-1" role="dialog" aria-labelledby="modal-login-label" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h4 class="modal-title" id="modal-login-label"><?=$this->t('Scanner Login');?></h4>
	  </div>
	  <form id="login-form" method="post">
	  <div class="modal-body">
	<div class="form-group">
		<label><?=$this->t('User Name');?></label>
		<input type="text" class="form-control input-lg" name="LoginForm[user]" />
	</div>
	<div class="form-group">
		<label><?=$this->t('Password');?></label>
		<input type="password" class="form-control input-lg" name="LoginForm[pwd]" />
	</div>
	<!-- <div>
		<div class="row">
			<div class="col col-md-6">
				<label><?=$this->t('Captcha');?></label>
				<input type="text" class="form-control input-lg" name="LoginForm[vvc]" autocomplete="off" />
			</div>
			<div class="col col-md-6">
				<img style="cursor:pointer;" alt="CAPTCHA" title="Click to reload" id="lf_capcha" src="<?=$this->createUrl('site/captcha').'?'.time();?>" />
			</div>
		</div>
	</div> -->

	<div id="google_Authenicator" style="display:none;">
		<div class="row">
			<div class="col col-md-6">
				<label>Google Authenicator Code</label>
				<input type="text" class="form-control input-lg" id="googel_code_input" name="LoginForm[code]" autocomplete="off" />
			</div>
			<div class="col col-md-6">
				<img style="cursor:pointer;" alt="CODE" id="googel_code" src="" width="80" height="80" />
			</div>
		</div>
		<div id="button_google_login" class="modal-footer">
		<button type="button" id ="google_Authen" onclick="return googleVerifyForm();" class="btn btn-primary btn-lg"><?=$this->t('google authenticator verify');?></button>
	  </div>
	</div>

	  </div>
	  <div id="button_login" class="modal-footer">
		<button type="submit" onclick="return submitForm();" class="btn btn-primary btn-lg"><?=$this->t('Login');?></button>
	  </div>
	  </form>
	</div>
  </div>
</div>

<div class="modal fade" id="modal-message" tabindex="-1" role="dialog" aria-labelledby="modal-message-label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" id='modal_close' class="btn btn-default" data-dismiss="modal"><?=$this->t('Close');?></button>
      </div>
    </div>
  </div>
</div>
<div id="notifc" class='notifications bottom-right'></div>
</body>
</html>




<script type="text/javascript">
		if("<?=Yii::app()->getRequest()->getHostInfo()?>".indexOf("os.toplogistics") !== -1&&"<?=Yii::app()->getRequest()->getHostInfo()?>".indexOf("localhost") == -1&&"<?=empty(User::getCurrentUser())?"":User::getCurrentUser()->dpt_id?>"!="4756")
		{
			window.location.href="https://whscan.toplogistics.com.au";
		}
		
function submitForm()
	{			
		<?php $url1=$this->createUrl('site/auth');?>
		var form = new FormData(document.getElementById("login-form"));
		 $.ajax({
		            url: '<?=$url1?>',
		            type: "post",
		            data: form,
		            processData: false,
		            contentType: false,
		            success: function(r) {
		            	var response = JSON.parse(r);
		                if(response.done)
		                 {
		                 	if(response.qrCodeUrl==0){
		                 		$('#google_Authenicator').show();
			                 	$('#button_login').hide();
		                 		$("#googel_code").hide();
		                 	}
		                 	else{
		                 		let googleAuthSrc = 'https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl='+response.qrCodeUrl;
			                 	$("#googel_code").attr("src",googleAuthSrc);
			                 	$('#google_Authenicator').show();
			                 	$('#button_login').hide();
		                 	}
		                 	
										 }else
										 {
											alert('Invalid Login');   
							       }
							             
							  },
		            error: function(e) {
		                console.log(e);
		            }
		        });
		return false;
	}

	function googleVerifyForm()
	{			
		<?php $url2=$this->createUrl('site/googleAuthen');?>
		var form = new FormData(document.getElementById("login-form"));
		 $.ajax({
		            url: '<?=$url2?>',
		            type: "post",
		            data: form,
		            processData: false,
		            contentType: false,
		            success: function(r) {
		            	var response = JSON.parse(r);
		            	console.log(response);
		                if(response.done)
		                 {
		                 	window.location.replace("<?=$this->createUrl('site/index'); ?>");
										 }else
										 {
											alert('Invalid Login');   
							       }
							             
							  },
		            error: function(e) {
		                console.log(e);
		            }
		        });
		return false;
	}

	$("#google_Authen").on("click", function(e){
	    e.preventDefault();
	    <?php $url2=$this->createUrl('site/googleAuthen');?>
	    $('#login-form').attr('action', "<?= $url2?>").submit();
	});	

	$("#googel_code_input").keyup(function(event) {
    if (event.keyCode === 13) {
        $("#google_Authen").click();
    }
});

// <?php $url=Yii::app()->createUrl('site/logout');?>

// // Set timeout variables.
// var timoutWarning = 3600000; // Display warning in 60 Mins.
// var timoutNow = 60000; // Warning has been shown, give the user 1 minute to interact

// var logoutUrl = "<?=$url;?>";
// // console.log(logoutUrl);

// var warningTimer;
// var timeoutTimer;

// // Start warning timer.
// function StartWarningTimer() {
//     warningTimer = setTimeout("IdleWarning()", timoutWarning);
// }

// // Reset timers.
// function ResetTimeOutTimer() {
//     clearTimeout(timeoutTimer);
//     StartWarningTimer();
//     $("#timeout").dialog('close');
// }

// // Show idle timeout warning dialog.
// function IdleWarning() {    
//     if (confirm("Please confirm, otherwiese your page will redirected to login page.")) {
//       ResetTimeOutTimer();
//     } else {
//         clearTimeout(warningTimer);
//         timeoutTimer = setTimeout("IdleTimeout()", timoutNow);
//         $("#timeout").dialog({
//             modal: true
//         });
//     }
    
// }

// // Logout the user.
// function IdleTimeout() {
//     window.location = logoutUrl;
// }

// StartWarningTimer();
</script>