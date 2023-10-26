<h1 style="text-align: center;">Welcome - <?=Yii::app()->user->name;?> - Driver</h1>

<div class="row dyo_list">
<!-- 	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('job/globalJobList');?>"><span class="glyphicon glyphicon-wrench"></span><br/>Jobs</a></div> -->
	<?php if(User::isNormalDriver()):?>
	<!-- <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('job/myList');?>"><span class="glyphicon glyphicon-list"></span><br/>My Job List</a></div> -->
	<?php endif;?>
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('job/allJobList');?>"><span class="glyphicon glyphicon-list"></span><br/>All Job List</a></div>
	
	<?php if(User::isFBADriver()):?>
		<!-- <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('job/fbaList');?>"><span class="glyphicon glyphicon-list"></span><br/>FBA List</a></div> -->
	<?php endif;?>

	<?php if(User::isB2BDriver()):?>
		<!-- <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('job/b2bList');?>"><span class="glyphicon glyphicon-list"></span><br/>B2B List</a></div> -->
	<?php endif;?>

	<!-- <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('job/cargoJobList');?>"><span class="glyphicon glyphicon-list"></span><br/>Cargo Job List</a></div> -->
	
	<!-- <?php if (User::getCurrentUser()->id == 3595 || User::getCurrentUser()->id==3751): //3237?> -->
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('job/deliveryedCargo');?>"><span class="glyphicon glyphicon-list"></span><br/>Deliveryed Cargo</a></div>
	<!-- <?php endif?> -->
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('job/getBiddingList');?>"><span class="glyphicon glyphicon-list"></span><br/>Bidding List</a></div>
<!-- 	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('invoice/list');?>"><span class="glyphicon glyphicon-list"></span><br/>My Invoices</a></div> -->
	<?php if (User::isShowCostUser()): ?>
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('job/exportCost');?>"><span class="glyphicon glyphicon-list"></span><br/>Export Cost</a></div>
	<?php endif?>
	<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"><a class="dash-item ajax-link" href="<?=$this->createUrl('site/logout');?>"><span class="glyphicon glyphicon-usd"></span><br/>Logout</a></div>
</div>

